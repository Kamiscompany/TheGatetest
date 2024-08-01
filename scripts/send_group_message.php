<?php
include('../includes/db.php');
include('../includes/functions.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $group_id = $_POST['group_id'];
    $message = $_POST['message'];
    $sender_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare('INSERT INTO group_messages (group_id, user_id, message) VALUES (?, ?, ?)');
    $stmt->execute([$group_id, $sender_id, $message]);

    header('Location: ../scripts/group.php?id=' . $group_id);
}
?>
