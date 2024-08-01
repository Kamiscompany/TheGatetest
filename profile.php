<?php
include('includes/db.php');
include('includes/functions.php');
session_start();
redirect_if_not_logged_in();

$profile_user_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id'];

// Obtenha as informações do usuário
$stmt = $pdo->prepare('SELECT username, real_name, gender, phone, email, profile_pic FROM users WHERE id = ?');
$stmt->execute([$profile_user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit();
}

// Obtenha todas as publicações do usuário
$stmt = $pdo->prepare('SELECT id, content, created_at, media FROM posts WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$profile_user_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtenha a lista de amigos
$stmt = $pdo->prepare('SELECT u.id, u.username, u.real_name, u.profile_pic FROM users u JOIN friend_requests fr ON u.id = fr.friend_id WHERE fr.user_id = ? AND fr.status = "accepted"');
$stmt->execute([$profile_user_id]);
$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT u.id, u.username, u.real_name, u.profile_pic FROM users u JOIN friend_requests fr ON u.id = fr.user_id WHERE fr.friend_id = ? AND fr.status = "accepted"');
$stmt->execute([$profile_user_id]);
$friends = array_merge($friends, $stmt->fetchAll(PDO::FETCH_ASSOC));

// Verificar se o usuário logado é amigo do perfil visitado
$is_friend = false;
foreach ($friends as $friend) {
    if ($friend['id'] == $_SESSION['user_id']) {
        $is_friend = true;
        break;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <?php include('includes/navbar.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <img src="uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture" class="img-fluid rounded-circle mb-4">
                <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                <p><strong>Real Name:</strong> <?php echo htmlspecialchars($user['real_name']); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <?php if ($profile_user_id == $_SESSION['user_id']): ?>
                    <a href="/fifi/scripts/edit_profile.php" class="btn btn-primary">Edit Profile</a>
                <?php elseif ($is_friend): ?>
                    <p>You are friends with this user.</p>
                <?php else: ?>
                    <form method="POST" action="/fifi/scripts/send_friend_request.php">
                        <input type="hidden" name="friend_id" value="<?php echo $profile_user_id; ?>">
                        <button type="submit" class="btn btn-primary">Send Friend Request</button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="col-md-8">
                <h3>Friends (<?php echo count($friends); ?>)</h3>
                <ul class="list-group mb-4">
                    <?php foreach ($friends as $friend): ?>
                        <li class="list-group-item">
                            <div class="d-flex align-items-center">
                                <img src="uploads/<?php echo htmlspecialchars($friend['profile_pic']); ?>" alt="Profile Picture" class="rounded-circle mr-2" width="50" height="50">
                                <div>
                                    <strong><?php echo htmlspecialchars($friend['username']); ?></strong> (<?php echo htmlspecialchars($friend['real_name']); ?>)
                                    <a href="profile.php?id=<?php echo $friend['id']; ?>" class="btn btn-link">View Profile</a>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <h3>Posts</h3>
                <ul class="list-group">
                    <?php foreach ($posts as $post): ?>
                        <li class="list-group-item">
                            <p><?php echo htmlspecialchars($post['content']); ?></p>
                            <?php if ($post['media']): ?>
                                <?php $media_ext = pathinfo($post['media'], PATHINFO_EXTENSION); ?>
                                <?php if (in_array($media_ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($post['media']); ?>" alt="Post Media" class="img-fluid">
                                <?php elseif (in_array($media_ext, ['mp4', 'webm', 'ogg'])): ?>
                                    <video width="320" height="240" controls class="w-100">
                                        <source src="uploads/<?php echo htmlspecialchars($post['media']); ?>" type="video/<?php echo $media_ext; ?>">
                                        Your browser does not support the video tag.
                                    </video>
                                <?php endif; ?>
                            <?php endif; ?>
                            <p class="small text-muted">Posted on <?php echo htmlspecialchars($post['created_at']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
