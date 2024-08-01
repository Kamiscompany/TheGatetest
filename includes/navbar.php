<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$user_logged_in = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';
?>
<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #1E90FF;">
    <a class="navbar-brand text-white" href="/fifi/index.php">TheGate</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <?php if ($user_logged_in): ?>
                <li class="nav-item"><a class="nav-link text-white" href="/fifi/index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="/fifi/search.php">Search</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="/fifi/profile.php?id=<?php echo $_SESSION['user_id']; ?>">Profile</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="/fifi/friends.php">Friends</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="/fifi/groups.php">Groups</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link text-white" href="/fifi/scripts/login.php">Login</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="/fifi/scripts/register.php">Register</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="/fifi/forgot_password.php">Forgot Password</a></li>
            <?php endif; ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    More
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <?php if ($user_logged_in): ?>
                        <a class="dropdown-item" href="/fifi/friend_requests.php">Friend Requests</a>
                        <?php if ($user_role === 'admin'): ?>
                            <a class="dropdown-item" href="/fifi/scripts/moderation.php">Moderation</a>
                        <?php endif; ?>
                        <a class="dropdown-item" href="/fifi/scripts/logout.php">Logout</a>
                    <?php else: ?>
                        <a class="dropdown-item" href="/fifi/scripts/login.php">Login</a>
                        <a class="dropdown-item" href="/fifi/scripts/register.php">Register</a>
                        <a class="dropdown-item" href="/fifi/forgot_password.php">Forgot Password</a>
                    <?php endif; ?>
                </div>
            </li>
        </ul>
    </div>
</nav>


