<?php
include('../includes/db.php');
include('../includes/functions.php');
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $content = $_POST['content'];
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare('INSERT INTO comments (user_id, post_id, content) VALUES (?, ?, ?)');
    $stmt->execute([$user_id, $post_id, $content]);

    header('Location: ../index.php');
}
?>
