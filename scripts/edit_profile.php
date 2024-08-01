<?php
include('../includes/db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../scripts/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Obtenha as informações do usuário
$stmt = $pdo->prepare('SELECT username, real_name, gender, phone, email, profile_pic FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $real_name = $_POST['real_name'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    
    // Verifica se um arquivo foi enviado
    if (!empty($_FILES['profile_pic']['name'])) {
        $profile_pic = $_FILES['profile_pic']['name'];
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($profile_pic);
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file);
    } else {
        $profile_pic = $user['profile_pic'];
    }

    $stmt = $pdo->prepare('UPDATE users SET real_name = ?, gender = ?, phone = ?, email = ?, profile_pic = ? WHERE id = ?');
    $stmt->execute([$real_name, $gender, $phone, $email, $profile_pic, $user_id]);

    header('Location: /fifi/profile.php?id=' . $user_id);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>
    <?php include('../includes/navbar.php'); ?>
    <div class="container mt-4">
        <h1>Edit Profile</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="real_name">Real Name:</label>
                <input type="text" class="form-control" name="real_name" id="real_name" value="<?php echo htmlspecialchars($user['real_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select class="form-control" name="gender" id="gender" required>
                    <option value="Male" <?php if ($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                    <option value="Other" <?php if ($user['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" class="form-control" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="profile_pic">Profile Picture:</label>
                <input type="file" class="form-control-file" name="profile_pic" id="profile_pic">
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
