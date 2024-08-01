<?php
include('../includes/db.php');
include('../includes/functions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $real_name = $_POST['real_name'];

    // Verifique se os campos obrigatórios estão preenchidos
    if (empty($username) || empty($password) || empty($email) || empty($gender) || empty($phone) || empty($real_name)) {
        $error = "All fields are required.";
    } else {
        // Verifique se o username ou email já existem no banco de dados
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $error = "Username or email already taken. Please choose another.";
        } else {
            // Insira o novo usuário no banco de dados
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, password, email, gender, phone, real_name) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$username, $hashed_password, $email, $gender, $phone, $real_name]);
            header('Location: ../scripts/login.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>
    <?php include('../includes/navbar.php'); ?>
    <div class="container mt-4">
        <h1>Register</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="form-group">
                <label for="real_name">Real Name</label>
                <input type="text" name="real_name" id="real_name" class="form-control" placeholder="Real Name" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <select name="gender" id="gender" class="form-control" required>
                    <option value="" disabled selected>Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone Number" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
