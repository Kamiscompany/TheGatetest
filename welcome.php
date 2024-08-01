<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to TheGate</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <style>
        .hero-section {
            position: relative;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-align: center;
            overflow: hidden;
        }
        .hero-video {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -1;
            transform: translate(-50%, -50%);
            background: no-repeat center center fixed;
            background-size: cover;
        }
        .hero-content {
            z-index: 1;
        }
        .hero-content h1 {
            font-size: 4rem;
            font-weight: bold;
        }
        .hero-content p {
            font-size: 1.5rem;
            margin-top: 1rem;
        }
        .cta-buttons a {
            margin: 0 1rem;
            font-size: 1.2rem;
            padding: 0.5rem 2rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #1E90FF;">
        <a class="navbar-brand text-white" href="/fifi/index.php">TheGate</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link text-white" href="/fifi/scripts/login.php">Login</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="/fifi/scripts/register.php">Register</a></li>
            </ul>
        </div>
    </nav>

    <section class="hero-section">
        <video class="hero-video" autoplay muted loop>
        <source src="videos/16968-277526315_tiny.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="hero-content">
            <h1>Welcome to TheGate</h1>
            <p>Your new favorite social network. Connect, share, and communicate effortlessly.</p>
            <div class="cta-buttons">
                <a href="/fifi/scripts/register.php" class="btn btn-primary">Join Now</a>
                <a href="/fifi/scripts/login.php" class="btn btn-light">Login</a>
            </div>
        </div>
    </section>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <h3>Connect with Friends</h3>
                <p>Stay connected with your friends and family, share moments, and chat in real-time.</p>
            </div>
            <div class="col-md-4">
                <h3>Create and Join Groups</h3>
                <p>Find communities that share your interests or create your own groups and invite friends.</p>
            </div>
            <div class="col-md-4">
                <h3>Share Your Moments</h3>
                <p>Post updates, photos, and videos to share what's happening in your life with your friends.</p>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5 py-4" style="background-color: #1E90FF; color: #fff;">
        <p>&copy; 2024 TheGate. All rights reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
