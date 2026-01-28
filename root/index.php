<?php
session_start();

// Load configuration and database connection
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Database Connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=grenada_marketplace", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Helper Functions
function redirect($page, $message = null, $type = 'success') {
    if($message) {
        $_SESSION['message'] = $message;
        $_SESSION['messageType'] = $type;
    }
    header("Location: index.php?page=$page");
    exit;
}

function getAndClearMessage() {
    $message = $_SESSION['message'] ?? null;
    $type = $_SESSION['messageType'] ?? 'success';
    unset($_SESSION['message']);
    unset($_SESSION['messageType']);
    return ['message' => $message, 'type' => $type];
}

// Session info
$isLoggedIn = $_SESSION['isLoggedIn'] ?? false;
$name = $_SESSION['name'] ?? 'Guest';
$userId = $_SESSION['userId'] ?? null;
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
