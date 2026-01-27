<?php
// ==================== index.php ====================
require_once 'includes/config.php';
require_once 'includes/functions.php';

$user = getCurrentUser();

// If user is logged in, redirect to appropriate dashboard
if ($user) {
    if ($user['user_type'] === 'farmer') {
        header('Location: dashboard.php');
    } else {
        header('Location: dashboard.php'); // or browse page when implemented
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Config::getSiteName() ?> - Connect with Local Farmers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/marketplace.css">
</head>
<body class="landing-page">
    <!-- Navigation -->
    <nav class="landing-nav">
        <div class="nav-container">
            <div class="logo">
                <span class="logo-icon">🌾</span>
                <span class="logo-text"><?= Config::getSiteName() ?></span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title"><?= Config::getSiteName() ?></h1>
                    <p class="hero-tagline">Connecting Grenadian farmers directly with consumers for fresh, local produce</p>
                    
                    <div class="hero-actions">
                        <a href="login.php" class="btn btn-primary btn-lg">Log In</a>
                        <a href="register.php" class="btn btn-secondary btn-lg">Sign Up</a>
                    </div>
                </div>
            </div>
            
            <div class="hero-media">
                <div class="video-container">
                    <video autoplay muted loop playsinline poster="https://via.placeholder.com/600x400/2d5016/ffffff?text=Fresh+Local+Produce">
                        <source src="assets/hero-video.mp4" type="video/mp4">
                        <!-- Fallback image if video doesn't load -->
                    </video>
                    <div class="video-overlay">
                        <div class="video-play-btn" onclick="toggleVideo()">
                            <span>▶</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works">
        <div class="container">
            <h2 class="section-title">How It Works</h2>
            
            <div class="user-paths">
                <div class="user-path">
                    <div class="path-icon">
                        <span class="icon">🌱</span>
                    </div>
                    <h3 class="path-title">I Am a Seller</h3>
                    <ul class="path-features">
                        <li>List produce easily</li>
                        <li>Share listings with buyers</li>
                        <li>Earn by selling directly to consumers</li>
                    </ul>
                    <a href="register.php?type=farmer" class="btn btn-outline">Start Selling</a>
                </div>
                
                <div class="user-path">
                    <div class="path-icon">
                        <span class="icon">🛒</span>
                    </div>
                    <h3 class="path-title">I Am a Buyer</h3>
                    <ul class="path-features">
                        <li>Find fresh, local produce</li>
                        <li>Connect directly with farmers</li>
                        <li>Save money with competitive prices</li>
                    </ul>
                    <a href="register.php?type=customer" class="btn btn-outline">Start Shopping</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="landing-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <span class="logo-icon">🌾</span>
                    <span class="logo-text"><?= Config::getSiteName() ?></span>
                </div>
                <p class="footer-tagline">Supporting local agriculture in Grenada</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleVideo() {
            const video = document.querySelector('video');
            const overlay = document.querySelector('.video-overlay');
            
            if (video.paused) {
                video.play();
                overlay.style.opacity = '0';
            } else {
                video.pause();
                overlay.style.opacity = '1';
            }
        }

        // Auto-hide video overlay after video starts
        document.querySelector('video').addEventListener('play', function() {
            setTimeout(() => {
                document.querySelector('.video-overlay').style.opacity = '0';
            }, 1000);
        });
    </script>
</body>
</html>