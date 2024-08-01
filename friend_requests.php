<?php
include('includes/db.php');
include('includes/functions.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: scripts/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Obtenha as solicitações de amizade pendentes
$stmt = $pdo->prepare('SELECT fr.id, u.username, u.real_name FROM friend_requests fr JOIN users u ON fr.user_id = u.id WHERE fr.friend_id = ? AND fr.status = "pending"');
$stmt->execute([$user_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Friend Requests</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <?php include('includes/navbar.php'); ?>
    <div class="container mt-4">
        <h1>Friend Requests</h1>
        <ul class="list-group">
            <?php foreach ($requests as $request): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?php echo htmlspecialchars($request['username']) . ' (' . htmlspecialchars($request['real_name']) . ')'; ?>
                    <div class="btn-group">
                        <form method="POST" action="/fifi/scripts/manage_friend_requests.php" class="d-inline">
                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                            <button type="submit" name="action" value="accept" class="btn btn-success btn-sm">Accept</button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
