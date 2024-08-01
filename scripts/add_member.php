<?php
include('../includes/db.php');
include('../includes/functions.php');
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['group_id']) || !isset($_POST['friend_id'])) {
    header('Location: ../scripts/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$group_id = $_POST['group_id'];
$friend_id = $_POST['friend_id'];

// Verificar se o usuário logado tem permissão para adicionar membros
$stmt = $pdo->prepare('SELECT role FROM group_members WHERE group_id = ? AND user_id = ?');
$stmt->execute([$group_id, $user_id]);
$role = $stmt->fetchColumn();

if ($role != 'creator' && $role != 'admin') {
    header('Location: ../scripts/group_chat.php?group_id=' . $group_id);
    exit();
}

// Adicionar o amigo como membro do grupo
$stmt = $pdo->prepare('INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, ?)');
$stmt->execute([$group_id, $friend_id, 'member']);

header('Location: ../scripts/group_chat.php?group_id=' . $group_id);
exit();
?>
