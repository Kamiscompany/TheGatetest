<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('../includes/db.php');
    
    $token = $_POST['token'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    
    // Verificar se o token é válido
    $stmt = $pdo->prepare('SELECT user_id FROM password_resets WHERE token = ?');
    $stmt->execute([$token]);
    $user_id = $stmt->fetchColumn();

    if ($user_id) {
        // Atualizar a senha do usuário
        $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->execute([$new_password, $user_id]);

        // Remover o token usado
        $stmt = $pdo->prepare('DELETE FROM password_resets WHERE token = ?');
        $stmt->execute([$token]);

        echo "Password reset successfully. You can now <a href='/fifi/scripts/login.php'>login</a>.";
    } else {
        echo "Invalid token.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/fifi/css/styles.css">
</head>
<body>
    <?php include('../includes/navbar.php'); ?>
    <div class="container mt-4">
        <h1>Reset Password</h1>
        <form method="POST" action="" class="mt-3">
            <div class="form-group">
                <label for="token">Enter your reset token:</label>
                <input type="text" id="token" name="token" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="new_password">Enter your new password:</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Reset Password</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
