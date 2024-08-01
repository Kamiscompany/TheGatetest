<?php
include('includes/db.php');
include('includes/functions.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: scripts/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Obtenha solicitações de amizade pendentes
$friend_requests_stmt = $pdo->prepare('SELECT fr.id, u.username, u.real_name FROM friend_requests fr JOIN users u ON fr.user_id = u.id WHERE fr.friend_id = ? AND fr.status = "pending"');
$friend_requests_stmt->execute([$user_id]);
$friend_requests = $friend_requests_stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtenha mensagens não lidas
$messages_stmt = $pdo->prepare('SELECT m.id, u.username, m.message FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.receiver_id = ? AND m.status = "unread"');
$messages_stmt->execute([$user_id]);
$messages = $messages_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <?php include('includes/navbar.php'); ?>
    <h1>Notifications</h1>
    
    <h2>Friend Requests</h2>
    <ul>
        <?php foreach ($friend_requests as $request): ?>
            <li>
                <?php echo htmlspecialchars($request['username']) . ' (' . htmlspecialchars($request['real_name']) . ')'; ?>
                <form method="POST" action="/fifi/scripts/manage_friend_requests.php" style="display:inline;">
                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                    <button type="submit" name="action" value="accept">Accept</button>
                    <button type="submit" name="action" value="reject">Reject</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>Messages</h2>
    <ul>
        <?php foreach ($messages as $message): ?>
            <li>
                <?php echo htmlspecialchars($message['username']); ?>: <?php echo htmlspecialchars($message['message']); ?>
                <a href="/fifi/scripts/view_message.php?id=<?php echo $message['id']; ?>">View Message</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
