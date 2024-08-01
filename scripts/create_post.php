<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../scripts/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    
    // Verifica se um arquivo foi enviado
    if (!empty($_FILES['media']['name'])) {
        $media = $_FILES['media']['name'];
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($media);
        move_uploaded_file($_FILES['media']['tmp_name'], $target_file);
    } else {
        $media = NULL;
    }

    $stmt = $pdo->prepare('INSERT INTO posts (user_id, content, created_at, media) VALUES (?, ?, NOW(), ?)');
    $stmt->execute([$user_id, $content, $media]);

    header('Location: ../index.php');
    exit();
}
?>
