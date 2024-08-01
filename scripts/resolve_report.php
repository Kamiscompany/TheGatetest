<?php
include('../includes/db.php');
session_start();

// Verifique se o usuário é um administrador
if ($_SESSION['user_role'] !== 'admin') {
    header('Location: /fifi/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $report_id = $_POST['report_id'];
    $action = $_POST['action'];

    // Obtenha os detalhes do relatório
    $stmt = $pdo->prepare('SELECT post_id, comment_id FROM reports WHERE id = ?');
    $stmt->execute([$report_id]);
    $report = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($report) {
        if ($action === 'resolve') {
            // Marcar o relatório como revisado
            $stmt = $pdo->prepare('UPDATE reports SET status = "reviewed" WHERE id = ?');
            $stmt->execute([$report_id]);
        } elseif ($action === 'delete') {
            if ($report['post_id']) {
                // Excluir relatórios relacionados ao post
                $stmt = $pdo->prepare('DELETE FROM reports WHERE post_id = ?');
                $stmt->execute([$report['post_id']]);

                // Excluir curtidas relacionadas ao post
                $stmt = $pdo->prepare('DELETE FROM likes WHERE post_id = ?');
                $stmt->execute([$report['post_id']]);

                // Excluir comentários relacionados ao post
                $stmt = $pdo->prepare('DELETE FROM comments WHERE post_id = ?');
                $stmt->execute([$report['post_id']]);

                // Excluir o post
                $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ?');
                $stmt->execute([$report['post_id']]);
            } elseif ($report['comment_id']) {
                // Excluir relatórios relacionados ao comentário
                $stmt = $pdo->prepare('DELETE FROM reports WHERE comment_id = ?');
                $stmt->execute([$report['comment_id']]);

                // Excluir o comentário
                $stmt = $pdo->prepare('DELETE FROM comments WHERE id = ?');
                $stmt->execute([$report['comment_id']]);
            }

            // Marcar o relatório como revisado
            $stmt = $pdo->prepare('UPDATE reports SET status = "reviewed" WHERE id = ?');
            $stmt->execute([$report_id]);
        }
    }

    header('Location: /fifi/scripts/moderation.php');
    exit();
}
?>
