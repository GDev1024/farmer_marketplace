<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration and database connection
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
$farmerVerified = $_SESSION['farmerVerified'] ?? false;

// Determine page
$page = $_GET['page'] ?? 'landing';
$protectedPages = ['home','browse','sell','listing','orders','messages','profile','cart','checkout','payment-success','payment-cancel'];

// Redirect unauthenticated users
if (!$isLoggedIn && in_array($page, $protectedPages)) {
    $page = 'login';
}

// Redirect logged-in users from login/register
if ($isLoggedIn && in_array($page,['login','register'])) {
    $page = 'home';
}

// Logout handler
if($page === 'logout') {
    session_destroy();
    header('Location: index.php?page=landing');
    exit;
}

// Load the page
$pageFile = "pages/{$page}.php";
if(!file_exists($pageFile)){
    $pageFile = "pages/landing.php";
}

include 'header.php';
include $pageFile;
include 'footer.php';
