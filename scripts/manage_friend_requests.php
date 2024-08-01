<?php
include('../includes/db.php');
include('../includes/functions.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../scripts/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    if ($action === 'accept') {
        $stmt = $pdo->prepare('UPDATE friend_requests SET status = ? WHERE id = ?');
        $stmt->execute(['accepted', $request_id]);
    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare('UPDATE friend_requests SET status = ? WHERE id = ?');
        $stmt->execute(['rejected', $request_id]);
    }

    header('Location: ../scripts/notifications.php');
    exit();
}
?>
