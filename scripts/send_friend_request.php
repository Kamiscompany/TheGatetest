<?php
include('../includes/db.php');
include('../includes/functions.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $friend_id = $_POST['friend_id'];
    $user_id = $_SESSION['user_id'];

    // Verifique se já existe uma solicitação de amizade pendente
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM friend_requests WHERE user_id = ? AND friend_id = ? AND status = "pending"');
    $stmt->execute([$user_id, $friend_id]);
    $exists = $stmt->fetchColumn();

    if ($exists == 0) {
        $stmt = $pdo->prepare('INSERT INTO friend_requests (user_id, friend_id, status) VALUES (?, ?, "pending")');
        $stmt->execute([$user_id, $friend_id]);
    }

    header('Location: ../scripts/profile.php?id=' . $friend_id);
}
?>
