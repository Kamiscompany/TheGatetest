<?php
include('includes/db.php');
include('includes/functions.php');
session_start();
redirect_if_not_logged_in();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $search = trim($_POST['search']);

    // Procure usuários que correspondam ao termo de pesquisa
    $stmt = $pdo->prepare('SELECT id, username, real_name, profile_pic FROM users WHERE username LIKE ? OR real_name LIKE ?');
    $stmt->execute(['%' . $search . '%', '%' . $search . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <?php include('includes/navbar.php'); ?>
    <div class="container mt-4">
        <h1>Search Users</h1>
        <form method="POST" action="search.php" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search for users" required>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>
        <?php if (isset($results)): ?>
            <h2>Results:</h2>
            <ul class="list-group">
                <?php foreach ($results as $user): ?>
                    <li class="list-group-item">
                        <div class="d-flex align-items-center">
                            <img src="uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture" class="rounded-circle mr-2" width="50" height="50">
                            <div>
                                <a href="/fifi/scripts/view_profile.php?id=<?php echo $user['id']; ?>">
                                    <?php echo htmlspecialchars($user['username']) . ' (' . htmlspecialchars($user['real_name']) . ')'; ?>
                                </a>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
