<?php
include('../includes/db.php');
session_start();

// Verifique se o usuário é um administrador
$stmt = $pdo->prepare('SELECT role FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user_role = $stmt->fetchColumn();

if ($user_role !== 'admin') {
    header('Location: /fifi/index.php');
    exit();
}

// Obtenha todos os relatórios pendentes
$stmt = $pdo->prepare('SELECT r.id, r.reason, r.created_at, u.username AS reporter, p.content AS post_content, c.content AS comment_content 
                       FROM reports r 
                       LEFT JOIN users u ON r.user_id = u.id 
                       LEFT JOIN posts p ON r.post_id = p.id 
                       LEFT JOIN comments c ON r.comment_id = c.id 
                       WHERE r.status = "pending" 
                       ORDER BY r.created_at DESC');
$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Moderation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/fifi/css/styles.css">
</head>
<body>
    <?php include('../includes/navbar.php'); ?>
    <div class="container mt-4">
        <h1>Moderation Panel</h1>
        <ul class="list-group">
            <?php foreach ($reports as $report): ?>
                <li class="list-group-item">
                    <h5>Reported by: <?php echo htmlspecialchars($report['reporter']); ?></h5>
                    <p>Reason: <?php echo htmlspecialchars($report['reason']); ?></p>
                    <p>Reported at: <?php echo htmlspecialchars($report['created_at']); ?></p>
                    <?php if ($report['post_content']): ?>
                        <p>Post Content: <?php echo htmlspecialchars($report['post_content']); ?></p>
                    <?php endif; ?>
                    <?php if ($report['comment_content']): ?>
                        <p>Comment Content: <?php echo htmlspecialchars($report['comment_content']); ?></p>
                    <?php endif; ?>
                    <form method="POST" action="resolve_report.php" class="d-inline">
                        <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                        <button type="submit" name="action" value="resolve" class="btn btn-success btn-sm">Resolve</button>
                        <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm">Delete Content</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
