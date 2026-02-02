<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $user_type = $_POST['user_type'] ?? 'customer';
    
    if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $db = Config::getDB();
        
        // Check if email exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = 'Email already exists';
        } else {
            // Create user
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (username, email, password_hash, user_type) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$username, $email, $password_hash, $user_type])) {
                $success = 'Account created successfully! You can now login.';
            } else {
                $error = 'Registration failed';
            }
        }
    }
}

include 'includes/header.php';
?>

<main class="auth-main">
    <div class="container">
        <div class="auth-wrapper">
            <div class="auth-card">
                <div class="auth-header">
                    <a href="index.php" class="auth-back-link">← Back to Home</a>
                    <h1 class="auth-title">Join Our Community</h1>
                    <p class="auth-subtitle">Create your account</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                
                <form method="POST" action="" class="auth-form">
                    <div class="form-group">
                        <label for="username" class="form-label">Full Name</label>
                        <input type="text" id="username" name="username" class="form-input" placeholder="Enter your full name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-input" placeholder="Create a password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="user_type" class="form-label">Account Type</label>
                        <select id="user_type" name="user_type" class="form-input">
                            <option value="customer">Customer</option>
                            <option value="farmer">Farmer</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="terms" class="checkbox-input" required>
                            I agree to the <a href="#" class="auth-link">Terms of Service</a> and <a href="#" class="auth-link">Privacy Policy</a>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-full">Create Account</button>
                </form>
                
                <div class="auth-footer">
                    <p>Already have an account? <a href="login.php" class="auth-link">Sign in</a></p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>