<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    $db = Config::getDB();
    $stmt = $db->prepare("SELECT id, password_hash, user_type FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type'];
        
        redirect('dashboard.php');
    } else {
        $error = 'Invalid email or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= Config::getSiteName() ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/marketplace.css">
</head>
<body class="auth-page">
    <header>
        <nav>
            <a href="index.php" class="logo">
                <span class="logo-icon">🌾</span>
                <span><?= Config::getSiteName() ?></span>
            </a>
            <div class="nav-links">
                <a href="index.php">Home</a>
            </div>
        </nav>
    </header>

    <main class="auth-main">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <h1>Welcome Back</h1>
                    <p>Sign in to your account</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="auth-form">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input" required 
                               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-input" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Sign In</button>
                </form>
                
                <div class="auth-footer">
                    <p>Don't have an account? <a href="register.php">Create one here</a></p>
                </div>
            </div>
        </div>
    </main>

    <footer class="app-footer">
        <div class="footer-content">
            <div class="footer-brand">
                <span class="logo-icon">🌾</span>
                <span><?= Config::getSiteName() ?></span>
            </div>
            <p class="footer-tagline">Supporting local agriculture in Grenada</p>
        </div>
    </footer>
</body>
</html>
