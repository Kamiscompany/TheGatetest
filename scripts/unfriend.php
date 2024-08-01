<?php
include('../includes/db.php');
include('../includes/functions.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id']) && isset($_POST['friend_id'])) {
    $user_id = $_SESSION['user_id'];
    $friend_id = $_POST['friend_id'];

    // Remova a amizade da tabela friends
    $stmt = $pdo->prepare('DELETE FROM friend_requests WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)');
    $stmt->execute([$user_id, $friend_id, $friend_id, $user_id]);

    header('Location: ../scripts/view_profile.php?id=' . $friend_id);
}
?>
