<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Include AWS-compatible ImageHandler class
require_once 'includes/AWSImageHandler.php';

// Include Payment Handler
require_once 'includes/PaymentHandler.php';

// Database Connection
try {
    $pdo = Config::getDB();
} catch (Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Helper Functions
function passwordHash($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function passwordVerify($password, $hash) {
    return password_verify($password, $hash);
}

function setMessage($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['messageType'] = $type;
}

// Register Handler
if(isset($_POST['register'])) {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    $farmerID = sanitizeInput($_POST['farmerID'] ?? '');

    $errors = [];

    if(empty($name)) $errors[] = 'Name is required';
    if(empty($email)) $errors[] = 'Email is required';
    if(empty($password)) $errors[] = 'Password is required';
    if($password !== $confirm) $errors[] = 'Passwords do not match';
    if(strlen($password) < 6) $errors[] = 'Password must be at least 6 characters';

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }

    // Check if email exists
    if(empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->rowCount() > 0) {
            $errors[] = 'Email already registered';
        }
    }

    if(!empty($errors)) {
        setMessage(implode(', ', $errors), 'error');
        redirect('register.php');
    }

    // Insert user
    $hashedPassword = passwordHash($password);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, user_type, farmer_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    
    try {
        $userType = !empty($farmerID) ? 'farmer' : 'consumer';
        $stmt->execute([$name, $email, $hashedPassword, $userType, $farmerID]);
        setMessage('Registration successful! Please login.', 'success');
        redirect('login.php');
    } catch (PDOException $e) {
        setMessage('Registration failed. Please try again.', 'error');
        redirect('register.php');
    }
}

// Login Handler
if(isset($_POST['login'])) {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];

    $errors = [];
    if(empty($email)) $errors[] = 'Email is required';
    if(empty($password)) $errors[] = 'Password is required';

    if(!empty($errors)) {
        setMessage(implode(', ', $errors), 'error');
        redirect('login.php');
    }

    $stmt = $pdo->prepare("SELECT id, username, password_hash, user_type FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && passwordVerify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['name'] = $user['username'];
        $_SESSION['email'] = $email;
        
        setMessage('Login successful!', 'success');
        redirect('dashboard.php');
    } else {
        setMessage('Invalid email or password', 'error');
        redirect('login.php');
    }
}

// Default redirect
redirect('index.php');