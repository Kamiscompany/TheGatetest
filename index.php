<?php
include('includes/db.php');
include('includes/functions.php');
session_start();
redirect_if_not_logged_in();

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT p.id, p.content, p.created_at, u.username, u.profile_pic, p.media, p.user_id, (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) AS like_count FROM posts p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC');
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <style>
        .options {
            display: none;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php include('includes/navbar.php'); ?>
    <div class="container mt-4">
        <h1>All Posts</h1>
        <form method="POST" action="/fifi/scripts/create_post.php" enctype="multipart/form-data" class="mb-4">
            <div class="form-group">
                <textarea name="content" class="form-control" rows="3" placeholder="What's on your mind?" required></textarea>
            </div>
            <div class="form-group">
                <input type="file" name="media" class="form-control-file">
            </div>
            <button type="submit" class="btn btn-primary">Post</button>
        </form>
        <ul class="list-group">
            <?php foreach ($posts as $post): ?>
                <li class="list-group-item">
                    <div class="d-flex align-items-center mb-2">
                        <img src="/fifi/uploads/<?php echo htmlspecialchars($post['profile_pic']); ?>" alt="Profile Picture" class="rounded-circle mr-2" width="50" height="50">
                        <strong><?php echo htmlspecialchars($post['username']); ?></strong>
                    </div>
                    <p><?php echo htmlspecialchars($post['content']); ?></p>
                    <?php if ($post['media']): ?>
                        <?php $media_ext = pathinfo($post['media'], PATHINFO_EXTENSION); ?>
                        <?php if (in_array($media_ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                            <img src="/fifi/uploads/<?php echo htmlspecialchars($post['media']); ?>" alt="Post Media" class="img-fluid">
                        <?php elseif (in_array($media_ext, ['mp4', 'webm', 'ogg'])): ?>
                            <video width="320" height="240" controls class="w-100">
                                <source src="/fifi/uploads/<?php echo htmlspecialchars($post['media']); ?>" type="video/<?php echo $media_ext; ?>">
                                Your browser does not support the video tag.
                            </video>
                        <?php endif; ?>
                    <?php endif; ?>
                    <p class="small text-muted">Posted at <?php echo htmlspecialchars($post['created_at']); ?></p>
                    <p class="small text-muted">Likes: <?php echo $post['like_count']; ?></p>
                    <form method="POST" action="/fifi/scripts/like.php" class="d-inline">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <button type="submit" class="btn btn-outline-primary btn-sm">Like</button>
                    </form>
                    <form method="POST" action="/fifi/scripts/comment.php" class="d-inline">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <textarea name="content" class="form-control" rows="1" placeholder="Add a comment..." required></textarea>
                        <button type="submit" class="btn btn-outline-secondary btn-sm">Comment</button>
                    </form>
                    <button class="btn btn-outline-info btn-sm" onclick="toggleOptions('<?php echo $post['id']; ?>')">More Options</button>
                    <div id="options-<?php echo $post['id']; ?>" class="options">
                        <form method="POST" action="/fifi/scripts/report.php" class="d-inline">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <textarea name="reason" class="form-control" rows="1" placeholder="Reason for reporting" required></textarea>
                            <button type="submit" class="btn btn-outline-warning btn-sm">Report Post</button>
                        </form>
                        <?php if ($post['user_id'] == $user_id || $user_role == 'admin'): ?>
                            <form method="POST" action="/fifi/scripts/delete_post.php" class="d-inline">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm">Delete Post</button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <hr>
                    <?php
                    $stmt = $pdo->prepare('SELECT c.id, c.content, c.created_at, u.username, u.profile_pic FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = ? ORDER BY c.created_at ASC');
                    $stmt->execute([$post['id']]);
                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="media mb-2">
                            <img src="/fifi/uploads/<?php echo htmlspecialchars($comment['profile_pic']); ?>" alt="Profile Picture" class="rounded-circle mr-2" width="30" height="30">
                            <div class="media-body">
                                <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                                <p><?php echo htmlspecialchars($comment['content']); ?></p>
                                <p class="small text-muted"><?php echo htmlspecialchars($comment['created_at']); ?></p>
                                <form method="POST" action="/fifi/scripts/report.php" class="d-inline">
                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                    <textarea name="reason" class="form-control" rows="1" placeholder="Reason for reporting" required></textarea>
                                    <button type="submit" class="btn btn-outline-warning btn-sm">Report Comment</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <script>
        function toggleOptions(postId) {
            var options = document.getElementById('options-' + postId);
            if (options.style.display === 'none' || options.style.display === '') {
                options.style.display = 'block';
            } else {
                options.style.display = 'none';
            }
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</body>
</html>
