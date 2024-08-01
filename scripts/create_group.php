<?php
include('../includes/db.php');
include('../includes/functions.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../scripts/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $group_name = $_POST['group_name'];
    $created_by = $_SESSION['user_id'];
    $friend_ids = $_POST['friends']; // Array de IDs de amigos

    // Insira o grupo na tabela 'groups'
    $stmt = $pdo->prepare('INSERT INTO groups (group_name, created_by) VALUES (?, ?)');
    $stmt->execute([$group_name, $created_by]);
    $group_id = $pdo->lastInsertId();

    // Insira o criador do grupo como membro com o papel de 'creator'
    $stmt = $pdo->prepare('INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, ?)');
    $stmt->execute([$group_id, $created_by, 'creator']);

    // Insira os amigos selecionados como membros do grupo com o papel de 'member'
    foreach ($friend_ids as $friend_id) {
        $stmt = $pdo->prepare('INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, ?)');
        $stmt->execute([$group_id, $friend_id, 'member']);
    }

    // Redirecionar para group_chat.php
    header('Location: ../scripts/group_chat.php?group_id=' . $group_id);
}

// Obtenha a lista de amigos
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('
    SELECT u.id, u.username 
    FROM users u 
    JOIN friend_requests fr 
    ON (u.id = fr.friend_id AND fr.user_id = ? AND fr.status = "accepted") 
    OR (u.id = fr.user_id AND fr.friend_id = ? AND fr.status = "accepted")
');
$stmt->execute([$user_id, $user_id]);
$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Group</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>
    <?php include('../includes/navbar.php'); ?>
    <div class="container mt-4">
        <h1>Create Group</h1>
        <form method="POST" action="../scripts/create_group.php">
            <div class="form-group">
                <label for="group_name">Group Name:</label>
                <input type="text" id="group_name" name="group_name" class="form-control" required>
            </div>
            <div class="form-group">
                <h2>Select Friends to Add:</h2>
                <?php if (count($friends) > 0): ?>
                    <?php foreach ($friends as $friend): ?>
                        <div class="form-check">
                            <input type="checkbox" name="friends[]" value="<?php echo $friend['id']; ?>" class="form-check-input">
                            <label class="form-check-label"><?php echo htmlspecialchars($friend['username']); ?></label>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>You have no friends to add.</p>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Create Group</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
