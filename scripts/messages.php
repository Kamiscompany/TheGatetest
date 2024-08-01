<?php
include('../includes/db.php');
include('../includes/functions.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../scripts/login.php');
    exit();
}

$current_user_id = $_SESSION['user_id'];
$chat_user_id = $_GET['user'];

$messages = $pdo->prepare('SELECT * FROM private_messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY timestamp');
$messages->execute([$current_user_id, $chat_user_id, $chat_user_id, $current_user_id]);
$messages = $messages->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Messages</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>
    <?php include('../includes/navbar.php'); ?>
    <h1>Chat with <?php echo htmlspecialchars($chat_user_id); ?></h1>
    <div class="messages">
        <?php foreach ($messages as $message): ?>
            <p><strong><?php echo htmlspecialchars($message['sender_id']); ?>:</strong> <?php echo htmlspecialchars($message['message']); ?></p>
        <?php endforeach; ?>
    </div>
    <form method="POST" action="send_message.php">
        <input type="hidden" name="receiver_id" value="<?php echo $chat_user_id; ?>">
        <textarea name="message" placeholder="Type your message here" required></textarea>
        <button type="submit">Send</button>
    </form>
</body>
</html>
