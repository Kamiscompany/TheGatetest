<?php
include('../includes/db.php');
include('../includes/functions.php');
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['group_id']) || !isset($_GET['user_id'])) {
    header('Location: ../scripts/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$group_id = $_GET['group_id'];
$member_id = $_GET['user_id'];

// Verificar se o usuário logado tem permissão para remover membros
$stmt = $pdo->prepare('SELECT role FROM group_members WHERE group_id = ? AND user_id = ?');
$stmt->execute([$group_id, $user_id]);
$role = $stmt->fetchColumn();

if ($role != 'creator' && $role != 'admin') {
    header('Location: ../scripts/group_chat.php?group_id=' . $group_id);
    exit();
}

// Remover o membro do grupo
$stmt = $pdo->prepare('DELETE FROM group_members WHERE group_id = ? AND user_id = ?');
$stmt->execute([$group_id, $member_id]);

header('Location: ../scripts/group_chat.php?group_id=' . $group_id);
exit();
?>
