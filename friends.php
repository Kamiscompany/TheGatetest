<?php
include('includes/db.php');
include('includes/functions.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: scripts/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Obtenha a lista de amigos
$stmt = $pdo->prepare('SELECT u.id, u.username, u.real_name, u.profile_pic FROM users u JOIN friend_requests fr ON u.id = fr.friend_id WHERE fr.user_id = ? AND fr.status = "accepted"');
$stmt->execute([$user_id]);
$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT u.id, u.username, u.real_name, u.profile_pic FROM users u JOIN friend_requests fr ON u.id = fr.user_id WHERE fr.friend_id = ? AND fr.status = "accepted"');
$stmt->execute([$user_id]);
$friends = array_merge($friends, $stmt->fetchAll(PDO::FETCH_ASSOC));
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <?php include('includes/navbar.php'); ?>
    <div class="container mt-4">
        <h1>Friends</h1>
        <ul class="list-group">
            <?php foreach ($friends as $friend): ?>
                <li class="list-group-item d-flex align-items-center">
                    <img src="uploads/<?php echo htmlspecialchars($friend['profile_pic']); ?>" alt="Profile Picture" class="rounded-circle mr-3" width="50" height="50">
                    <div>
                        <h5 class="mb-1"><?php echo htmlspecialchars($friend['username']); ?></h5>
                        <p class="mb-1"><?php echo htmlspecialchars($friend['real_name']); ?></p>
                    </div>
                    <div class="ml-auto">
                        <a href="profile.php?id=<?php echo $friend['id']; ?>" class="btn btn-primary btn-sm">View Profile</a>
                        <a href="/fifi/scripts/private_chat.php?friend_id=<?php echo $friend['id']; ?>" class="btn btn-secondary btn-sm">Chat</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
