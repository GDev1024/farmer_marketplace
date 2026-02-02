<?php
session_start();

require_once 'includes/config.php';
require_once 'includes/functions.php';

// Database Connection
try {
    $pdo = Config::getDB();
} catch (Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Session info
$isLoggedIn = $_SESSION['user_id'] ?? false;
$name = $_SESSION['name'] ?? 'Guest';
$userId = $_SESSION['user_id'] ?? null;

// If no page specified and user is logged in, go to home
// If no page specified and user is not logged in, show landing
if (!isset($_GET['page'])) {
    if ($isLoggedIn) {
        header('Location: dashboard.php');
        exit;
    } else {
        // Show landing page directly
        include 'includes/header.php';
        include 'pages/landing.php';
        include 'includes/footer.php';
        exit;
    }
}

// Determine page
$page = $_GET['page'];
$protectedPages = ['home', 'browse', 'sell', 'listing', 'orders', 'messages', 'profile', 'cart', 'checkout', 'payment-success', 'payment-cancel'];

// Redirect unauthenticated users
if (!$isLoggedIn && in_array($page, $protectedPages)) {
    $page = 'login';
}

// Redirect logged-in users from login/register
if ($isLoggedIn && in_array($page, ['login', 'register'])) {
    header('Location: dashboard.php');
    exit;
}

// Logout handler
if ($page === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Load the page
$pageFile = "pages/{$page}.php";
if (!file_exists($pageFile)) {
    $pageFile = "pages/landing.php";
}

include 'includes/header.php';
include $pageFile;
include 'includes/footer.php';
