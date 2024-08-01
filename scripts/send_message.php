<?php
include('../includes/db.php');
include('../includes/functions.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];
    $sender_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare('INSERT INTO private_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)');
    $stmt->execute([$sender_id, $receiver_id, $message]);

    header('Location: ../scripts/massages.php?user=' . $receiver_id);
}
?>
