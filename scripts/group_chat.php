<?php
include('../includes/db.php');
include('../includes/functions.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../scripts/login.php');
    exit();
}

if (!isset($_GET['group_id'])) {
    echo "Group ID is required.";
    exit();
}

$group_id = $_GET['group_id'];
$user_id = $_SESSION['user_id'];

// Verifique o papel do usuário no grupo
$stmt = $pdo->prepare('SELECT role FROM group_members WHERE group_id = ? AND user_id = ?');
$stmt->execute([$group_id, $user_id]);
$user_role = $stmt->fetchColumn();

// Obtenha informações do grupo
$stmt = $pdo->prepare('SELECT group_name FROM groups WHERE id = ?');
$stmt->execute([$group_id]);
$group = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$group) {
    echo "Group not found.";
    exit();
}

// Obtenha as mensagens do grupo
$stmt = $pdo->prepare('SELECT gm.message, gm.sent_at, u.username, u.profile_pic, gm.media FROM group_messages gm JOIN users u ON gm.user_id = u.id WHERE gm.group_id = ? ORDER BY gm.sent_at ASC');
$stmt->execute([$group_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    $stmt = $pdo->prepare('INSERT INTO group_messages (group_id, user_id, message, media) VALUES (?, ?, ?, ?)');
    $stmt->execute([$group_id, $user_id, $message, $media]);

    // Redirecionar para evitar reenvio do formulário
    header('Location: group_chat.php?group_id=' . $group_id);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Group Chat: <?php echo htmlspecialchars($group['group_name']); ?></title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include('../includes/navbar.php'); ?>
    <div class="container mt-4">
        <h1>Group Chat: <?php echo htmlspecialchars($group['group_name']); ?></h1>

        <?php if ($user_role === 'creator' || $user_role === 'admin'): ?>
            <a href="../scripts/group_admin.php?group_id=<?php echo $group_id; ?>" class="btn btn-warning mb-3">Admin Panel</a>
        <?php endif; ?>

        <div class="chat-box border p-3 mb-3">
            <?php foreach ($messages as $msg): ?>
                <div class="media mb-3">
                    <img src="../uploads/<?php echo htmlspecialchars($msg['profile_pic']); ?>" class="mr-3 rounded-circle" alt="Profile Picture" width="50" height="50">
                    <div class="media-body">
                        <h5 class="mt-0"><?php echo htmlspecialchars($msg['username']); ?></h5>
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
                        <small class="text-muted"><?php echo htmlspecialchars($msg['sent_at']); ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="POST" action="group_chat.php?group_id=<?php echo $group_id; ?>" enctype="multipart/form-data" class="mb-3">
            <div class="form-group">
                <textarea name="message" class="form-control" rows="2" placeholder="Type your message..." required></textarea>
            </div>
            <div class="form-group">
                <input type="file" name="media" class="form-control-file">
            </div>
            <button type="submit" class="btn btn-primary">Send</button>
        </form>
        <form method="POST" action="leave_group.php" class="mb-3">
            <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
            <button type="submit" class="btn btn-danger">Leave Group</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
