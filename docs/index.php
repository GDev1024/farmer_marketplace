<?php
session_start();

// Simple session check
$isLoggedIn = $_SESSION['isLoggedIn'] ?? false;
$userType = $_SESSION['userType'] ?? null;
$name = $_SESSION['name'] ?? 'Guest';

// Determine page
$page = $_GET['page'] ?? 'landing';
$protectedPages = ['home', 'browse', 'sell', 'listing', 'orders', 'messages', 'profile'];

// Redirect unauthenticated users
if (!$isLoggedIn && in_array($page, $protectedPages)) {
    $page = 'login';
}

// Redirect logged-in users away from login/register
if ($isLoggedIn && in_array($page, ['login', 'register'])) {
    $page = 'home';
}

// Load the page
$pageFile = "pages/{$page}.php";
if (!file_exists($pageFile)) {
    $pageFile = "pages/landing.php";
}

include 'header.php'; // Shared header + nav
include $pageFile;    // Page content
include 'footer.php'; // Shared footer
