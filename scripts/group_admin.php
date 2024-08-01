<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['group_id'])) {
    header('Location: ../scripts/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$group_id = $_GET['group_id'];

// Verificar se o usuário é o criador ou administrador do grupo
$stmt = $pdo->prepare('SELECT role FROM group_members WHERE group_id = ? AND user_id = ?');
$stmt->execute([$group_id, $user_id]);
$user_role = $stmt->fetchColumn();

if ($user_role !== 'creator' && $user_role !== 'admin') {
    echo "Access denied.";
    exit();
}

// Obter membros do grupo
$stmt = $pdo->prepare('SELECT u.id, u.username, gm.role FROM users u JOIN group_members gm ON u.id = gm.user_id WHERE gm.group_id = ?');
$stmt->execute([$group_id]);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obter lista de amigos para adicionar ao grupo
$stmt = $pdo->prepare('SELECT u.id, u.username FROM users u JOIN friend_requests fr ON (u.id = fr.friend_id AND fr.user_id = ?) OR (u.id = fr.user_id AND fr.friend_id = ?) WHERE fr.status = "accepted"');
$stmt->execute([$user_id, $user_id]);
$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Funções de administração (promover, rebaixar, banir, adicionar)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['promote'])) {
        $stmt = $pdo->prepare('UPDATE group_members SET role = "admin" WHERE group_id = ? AND user_id = ?');
        $stmt->execute([$group_id, $_POST['user_id']]);
    } elseif (isset($_POST['demote'])) {
        $stmt = $pdo->prepare('UPDATE group_members SET role = "member" WHERE group_id = ? AND user_id = ?');
        $stmt->execute([$group_id, $_POST['user_id']]);
    } elseif (isset($_POST['ban'])) {
        $stmt = $pdo->prepare('DELETE FROM group_members WHERE group_id = ? AND user_id = ?');
        $stmt->execute([$group_id, $_POST['user_id']]);
    } elseif (isset($_POST['add_member'])) {
        $stmt = $pdo->prepare('INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, "member")');
        $stmt->execute([$group_id, $_POST['friend_id']]);
    }
    
    header('Location: ../scripts/group_admin.php?group_id=' . $group_id);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Group Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>
    <?php include('../includes/navbar.php'); ?>
    <div class="container mt-4">
        <h1>Group Administration</h1>
        <h3>Members</h3>
        <ul class="list-group mb-3">
            <?php foreach ($members as $member): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?php echo htmlspecialchars($member['username']) . " (" . htmlspecialchars($member['role']) . ")"; ?>
                    <?php if ($user_role === 'creator' || ($user_role === 'admin' && $member['role'] !== 'creator' && $member['role'] !== 'admin')): ?>
                        <div class="btn-group">
                            <?php if ($member['role'] === 'member'): ?>
                                <form method="POST" action="" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?php echo $member['id']; ?>">
                                    <button type="submit" name="promote" class="btn btn-sm btn-warning">Promote to Admin</button>
                                </form>
                            <?php elseif ($member['role'] === 'admin'): ?>
                                <form method="POST" action="" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?php echo $member['id']; ?>">
                                    <button type="submit" name="demote" class="btn btn-sm btn-secondary">Demote to Member</button>
                                </form>
                            <?php endif; ?>
                            <form method="POST" action="" class="d-inline">
                                <input type="hidden" name="user_id" value="<?php echo $member['id']; ?>">
                                <button type="submit" name="ban" class="btn btn-sm btn-danger">Ban</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <h3>Add Member</h3>
        <form method="POST" action="">
            <div class="form-group">
                <select name="friend_id" class="form-control" required>
                    <option value="">Select a friend to add</option>
                    <?php foreach ($friends as $friend): ?>
                        <option value="<?php echo $friend['id']; ?>"><?php echo htmlspecialchars($friend['username']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="add_member" class="btn btn-primary">Add Member</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
