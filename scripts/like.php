<?php
include('../includes/db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    // Verifique se o usuário já curtiu o post
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM likes WHERE user_id = ? AND post_id = ?');
    $stmt->execute([$user_id, $post_id]);
    $already_liked = $stmt->fetchColumn();

    if ($already_liked == 0) {
        $stmt = $pdo->prepare('INSERT INTO likes (user_id, post_id) VALUES (?, ?)');
        $stmt->execute([$user_id, $post_id]);
    }

    header('Location: ../index.php');
    exit();
}
?>
