<?php
include('../includes/db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    $user_role = $_SESSION['user_role'];

    // Verifique se o post pertence ao usuário logado ou se o usuário é um administrador
    $stmt = $pdo->prepare('SELECT user_id FROM posts WHERE id = ?');
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post && ($post['user_id'] == $user_id || $user_role == 'admin')) {
        // Excluir relatórios relacionados ao post
        $stmt = $pdo->prepare('DELETE FROM reports WHERE post_id = ?');
        $stmt->execute([$post_id]);

        // Excluir curtidas relacionadas ao post
        $stmt = $pdo->prepare('DELETE FROM likes WHERE post_id = ?');
        $stmt->execute([$post_id]);

        // Excluir comentários relacionados ao post
        $stmt = $pdo->prepare('DELETE FROM comments WHERE post_id = ?');
        $stmt->execute([$post_id]);

        // Excluir o post
        $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
        $stmt->execute([$post_id]);

        // Redirecionar para a página inicial após a exclusão
        header('Location: /fifi/index.php');
        exit();
    } else {
        echo "You do not have permission to delete this post.";
    }
}
?>
