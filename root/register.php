require_once 'includes/config.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

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
    <title>Register - <?= Config::SITE_NAME ?></title>
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/layout.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">🌾 <?= Config::SITE_NAME ?></div>
            <div class="nav-links">
                <a href="index.php">Browse</a>
                <a href="login.php" class="btn btn-secondary btn-sm">Login</a>
            </div>
        </nav>
    </header>

    <div class="container container-sm" style="margin-top: 4rem;">
        <div class="card">
            <h1 style="text-align: center; color: var(--primary-green); margin-bottom: 2rem;">Create Account</h1>
            
            <?php if ($error): ?>
                <div style="background: #fee; border: 1px solid var(--error); padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem;">
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div style="background: #efe; border: 1px solid var(--success); padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem;">
                    <?= $success ?> <a href="login.php" style="color: var(--primary-green); font-weight: 600;">Login now</a>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="registerForm">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password (min 8 characters)</label>
                    <input type="password" name="password" class="form-input" minlength="8" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">I am a...</label>
                    <select name="user_type" class="form-input" id="userType" required>
                        <option value="">Select type</option>
                        <option value="consumer">Consumer (Buy produce)</option>
                        <option value="farmer">Farmer (Sell produce)</option>
                    </select>
                </div>
                
                <div class="form-group" id="farmerIdGroup" style="display: none;">
                    <label class="form-label">Farmer ID (Optional - for verification)</label>
                    <input type="text" name="farmer_id" class="form-input" placeholder="Enter your Farmer ID">
                    <small style="color: var(--gray-600);">Verified farmers get a badge on their listings</small>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Create Account</button>
            </form>
            
            <p style="text-align: center; margin-top: 1.5rem; color: var(--gray-600);">
                Already have an account? <a href="login.php" style="color: var(--primary-green); font-weight: 600;">Login</a>
            </p>
        </div>
    </div>

    <script>
        document.getElementById('userType').addEventListener('change', function() {
            const farmerIdGroup = document.getElementById('farmerIdGroup');
            farmerIdGroup.style.display = this.value === 'farmer' ? 'block' : 'none';
        });
    </script>
</body>
</html>

<?php