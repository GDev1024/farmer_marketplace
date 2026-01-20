require_once 'includes/config.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('index.php');
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
        
        if ($user['user_type'] === 'farmer') {
            redirect('dashboard.php');
        } else {
            redirect('index.php');
        }
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
    <title>Login - <?= Config::SITE_NAME ?></title>
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
                <a href="register.php" class="btn btn-primary btn-sm">Sign Up</a>
            </div>
        </nav>
    </header>

    <div class="container container-sm" style="margin-top: 4rem;">
        <div class="card">
            <h1 style="text-align: center; color: var(--primary-green); margin-bottom: 2rem;">Login</h1>
            
            <?php if ($error): ?>
                <div style="background: #fee; border: 1px solid var(--error); padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1rem;">
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            
            <p style="text-align: center; margin-top: 1.5rem; color: var(--gray-600);">
                Don't have an account? <a href="register.php" style="color: var(--primary-green); font-weight: 600;">Sign up</a>
            </p>
        </div>
    </div>
</body>
</html>

<?php