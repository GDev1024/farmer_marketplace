<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';
$userType = isset($_GET['type']) ? $_GET['type'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $userType = sanitizeInput($_POST['user_type']);
    $farmerId = isset($_POST['farmer_id']) ? sanitizeInput($_POST['farmer_id']) : null;
    
    // Validation
    if (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters';
    } else {
        $db = Config::getDB();
        
        // Check if email exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email already registered';
        } else {
            // Hash password with bcrypt
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            
            // Insert user
            $stmt = $db->prepare("INSERT INTO users (username, email, password_hash, user_type, farmer_id) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$username, $email, $passwordHash, $userType, $farmerId])) {
                $success = 'Registration successful! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - <?= Config::getSiteName() ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/marketplace.css">
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
                    <h1>Join Our Community</h1>
                    <p>Create your account to get started</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?= $success ?> <a href="login.php">Sign in now</a>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="auth-form" id="registerForm">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="username" class="form-input" required 
                               value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input" required 
                               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-input" minlength="8" required>
                        <small class="form-help">Minimum 8 characters</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Account Type</label>
                        <select name="user_type" class="form-input" id="userType" required>
                            <option value="">Choose your role</option>
                            <option value="consumer" <?= $userType === 'consumer' ? 'selected' : '' ?>>🛒 Customer (Buy fresh produce)</option>
                            <option value="farmer" <?= $userType === 'farmer' ? 'selected' : '' ?>>🌱 Farmer (Sell my produce)</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="farmerIdGroup" style="display: <?= $userType === 'farmer' ? 'block' : 'none' ?>;">
                        <label for="farmer_id" class="form-label">Farmer ID <span class="optional">(Optional)</span></label>
                        <input type="text" name="farmer_id" id="farmer_id" class="form-input" placeholder="Enter your Farmer ID">
                        <small class="form-help">Verified farmers get a trusted badge on their listings</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Create Account</button>
                </form>
                
                <div class="auth-footer">
                    <p>Already have an account? <a href="login.php">Sign in here</a></p>
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

    <script>
        document.getElementById('userType').addEventListener('change', function() {
            const farmerIdGroup = document.getElementById('farmerIdGroup');
            farmerIdGroup.style.display = this.value === 'farmer' ? 'block' : 'none';
        });
    </script>
</body>
</html>
