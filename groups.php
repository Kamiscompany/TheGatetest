<?php
include('includes/db.php');
include('includes/functions.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: scripts/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Obtenha os grupos que o usuário pertence
$stmt = $pdo->prepare('
    SELECT g.id, g.group_name, g.created_by 
    FROM groups g 
    JOIN group_members gm ON g.id = gm.group_id 
    WHERE gm.user_id = ?
');
$stmt->execute([$user_id]);
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groups</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <?php include('includes/navbar.php'); ?>
    <div class="container mt-4">
        <h1>Your Groups</h1>
        <a href="/fifi/scripts/create_group.php" class="btn btn-primary mb-4">Create New Group</a>
        <ul class="list-group">
            <?php foreach ($groups as $group): ?>
                <li class="list-group-item">
                    <a href="/fifi/scripts/group_chat.php?group_id=<?php echo $group['id']; ?>"><?php echo htmlspecialchars($group['group_name']); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
