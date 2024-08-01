<?php
include('../includes/db.php');
include('../includes/functions.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../scripts/login.php');
    exit();
}

$message_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Obtenha a mensagem
$stmt = $pdo->prepare('SELECT m.id, u.username, m.message, m.sent_at FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.id = ? AND m.receiver_id = ?');
$stmt->execute([$message_id, $user_id]);
$message = $stmt->fetch(PDO::FETCH_ASSOC);

if ($message) {
    // Marque a mensagem como lida
    $stmt = $pdo->prepare('UPDATE messages SET status = "read" WHERE id = ?');
    $stmt->execute([$message_id]);
} else {
    echo "Message not found.";
    exit();
}
?>

<!DOCTYPE html>
<html>
head>
    <title>View Message</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>
    <?php include('../includes/navbar.php'); ?>
    <h1>Message from <?php echo htmlspecialchars($message['username']); ?></h1>
    <p><?php echo htmlspecialchars($message['message']); ?></p>
    <p>Sent at: <?php echo htmlspecialchars($message['sent_at']); ?></p>
    <a href="notifications.php">Back to Notifications</a>
</body>
</html>
