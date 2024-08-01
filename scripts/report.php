<?php
include('../includes/db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $post_id = $_POST['post_id'] ?? null;
    $comment_id = $_POST['comment_id'] ?? null;
    $reason = $_POST['reason'];

    $stmt = $pdo->prepare('INSERT INTO reports (user_id, post_id, comment_id, reason) VALUES (?, ?, ?, ?)');
    $stmt->execute([$user_id, $post_id, $comment_id, $reason]);

    header('Location: /fifi/index.php');
    exit();
}
?>
