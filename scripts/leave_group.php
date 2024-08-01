<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['group_id'])) {
    header('Location: ../scripts/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$group_id = $_POST['group_id'];

// Verificar se o usuário é o criador do grupo
$stmt = $pdo->prepare('SELECT role FROM group_members WHERE group_id = ? AND user_id = ?');
$stmt->execute([$group_id, $user_id]);
$user_role = $stmt->fetchColumn();

if ($user_role === 'creator') {
    echo "Creators cannot leave their own groups.";
    exit();
}

// Remover usuário do grupo
$stmt = $pdo->prepare('DELETE FROM group_members WHERE group_id = ? AND user_id = ?');
$stmt->execute([$group_id, $user_id]);

header('Location: /fifi/groups.php');
exit();
?>
