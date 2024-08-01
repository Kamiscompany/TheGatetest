<?php
include('../includes/db.php');
include('../includes/functions.php');
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['friend_id'])) {
    header('Location: ../scripts/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$friend_id = $_GET['friend_id'];

// Obter informações do amigo
$stmt = $pdo->prepare('SELECT username FROM users WHERE id = ?');
$stmt->execute([$friend_id]);
$friend = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$friend) {
    echo "Friend not found.";
    exit();
}

$friend_username = $friend['username'];

// Enviar mensagem
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];
    
    // Verifica se um arquivo foi enviado
    if (!empty($_FILES['media']['name'])) {
        $media = $_FILES['media']['name'];
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($media);
        move_uploaded_file($_FILES['media']['tmp_name'], $target_file);
    } else {
        $media = NULL;
    }

    $stmt = $pdo->prepare('INSERT INTO private_messages (sender_id, receiver_id, message, media, timestamp) VALUES (?, ?, ?, ?, NOW())');
    $stmt->execute([$user_id, $friend_id, $message, $media]);
}

// Obter mensagens
$stmt = $pdo->prepare('
    SELECT pm.message, pm.timestamp, u.username, u.profile_pic, pm.media, pm.sender_id
    FROM private_messages pm
    JOIN users u ON pm.sender_id = u.id
    WHERE (pm.sender_id = ? AND pm.receiver_id = ?) OR (pm.sender_id = ? AND pm.receiver_id = ?)
    ORDER BY pm.timestamp
');
$stmt->execute([$user_id, $friend_id, $friend_id, $user_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo htmlspecialchars($friend_username); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>
    <?php include('../includes/navbar.php'); ?>
    <div class="container mt-4">
        <h1>Chat with <?php echo htmlspecialchars($friend_username); ?></h1>
        <div class="chat-box border rounded p-3 mb-4" style="height: 400px; overflow-y: scroll;">
            <?php foreach ($messages as $msg): ?>
                <div class="media mb-3">
                    <img src="../uploads/<?php echo htmlspecialchars($msg['profile_pic']); ?>" class="mr-3 rounded-circle" alt="Profile Picture" width="30" height="30">
                    <div class="media-body">
                        <h5 class="mt-0"><?php echo $msg['sender_id'] == $user_id ? 'You' : htmlspecialchars($msg['username']); ?></h5>
                        <p><?php echo htmlspecialchars($msg['message']); ?></p>
                        <?php if ($msg['media']): ?>
                            <?php $media_ext = pathinfo($msg['media'], PATHINFO_EXTENSION); ?>
                            <?php if (in_array($media_ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($msg['media']); ?>" class="img-fluid" alt="Media">
                            <?php elseif (in_array($media_ext, ['mp4', 'webm', 'ogg'])): ?>
                                <video width="320" height="240" controls class="w-100">
                                    <source src="../uploads/<?php echo htmlspecialchars($msg['media']); ?>" type="video/<?php echo $media_ext; ?>">
                                    Your browser does not support the video tag.
                                </video>
                            <?php endif; ?>
                        <?php endif; ?>
                        <small class="text-muted">(<?php echo htmlspecialchars($msg['timestamp']); ?>)</small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <form method="POST" action="" enctype="multipart/form-data" class="d-flex">
            <textarea name="message" class="form-control mr-2" rows="1" placeholder="Type a message..." required></textarea>
            <input type="file" name="media" class="form-control-file mr-2">
            <button type="submit" class="btn btn-primary">Send</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
