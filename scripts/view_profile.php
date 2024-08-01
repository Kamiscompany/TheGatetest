<?php
include('../includes/db.php');
include('../includes/functions.php');
session_start();

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Obtenha as informações do usuário com base no ID
    $stmt = $pdo->prepare('SELECT username, real_name, gender, phone, email, profile_pic FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found.";
        exit();
    }

    // Obtenha todas as publicações do usuário
    $stmt = $pdo->prepare('SELECT content, created_at FROM posts WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$user_id]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verifique se o usuário logado já enviou uma solicitação de amizade
    $friend_request_sent = false;
    $is_friend = false;
    if (isset($_SESSION['user_id'])) {
        $logged_in_user_id = $_SESSION['user_id'];
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM friend_requests WHERE user_id = ? AND friend_id = ? AND status = "pending"');
        $stmt->execute([$logged_in_user_id, $user_id]);
        $friend_request_sent = $stmt->fetchColumn() > 0;

        // Verificar se o usuário logado é amigo do perfil visitado
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM friend_requests WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?) AND status = "accepted"');
        $stmt->execute([$logged_in_user_id, $user_id, $user_id, $logged_in_user_id]);
        $is_friend = $stmt->fetchColumn() > 0;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>
    <?php include('../includes/navbar.php'); ?>
    <div class="container mt-4">
        <?php if ($user): ?>
            <h1><?php echo htmlspecialchars($user['username']) . "'s Profile"; ?></h1>
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="../uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture" class="rounded-circle mr-3" width="100" height="100">
                        <div>
                            <h4><?php echo htmlspecialchars($user['real_name']); ?></h4>
                            <p><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                    </div>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                </div>
            </div>
            
            <?php if (isset($logged_in_user_id) && $logged_in_user_id != $user_id && !$friend_request_sent && !$is_friend): ?>
                <form method="POST" action="send_friend_request.php">
                    <input type="hidden" name="friend_id" value="<?php echo $user_id; ?>">
                    <button type="submit" class="btn btn-primary">Send Friend Request</button>
                </form>
            <?php elseif (isset($friend_request_sent) && $friend_request_sent): ?>
                <p class="text-warning">Friend request already sent.</p>
            <?php elseif ($is_friend): ?>
                <p class="text-success">You are friends with this user.</p>
                <form method="POST" action="unfriend.php">
                    <input type="hidden" name="friend_id" value="<?php echo $user_id; ?>">
                    <button type="submit" class="btn btn-danger">Unfriend</button>
                </form>
            <?php endif; ?>

            <h2 class="mt-4"><?php echo htmlspecialchars($user['username']) . '\'s Posts'; ?></h2>
            <ul class="list-group">
                <?php foreach ($posts as $post): ?>
                    <li class="list-group-item">
                        <p><?php echo htmlspecialchars($post['content']); ?></p>
                        <p class="text-muted"><small>Posted at <?php echo htmlspecialchars($post['created_at']); ?></small></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-danger">User not found.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
