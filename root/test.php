<?php
// Simple test file to check if everything is working
session_start();

echo "<h1>Grenada Farmer Marketplace - System Test</h1>";

// Test 1: Check if includes work
echo "<h2>Test 1: Include Files</h2>";
try {
    require_once 'includes/config.php';
    require_once 'includes/functions.php';
    echo "✅ Include files loaded successfully<br>";
} catch (Exception $e) {
    echo "❌ Include files failed: " . $e->getMessage() . "<br>";
}

// Test 2: Check database connection
echo "<h2>Test 2: Database Connection</h2>";
try {
    $pdo = Config::getDB();
    echo "✅ Database connection successful<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
}

// Test 3: Check CSS files
echo "<h2>Test 3: CSS Files</h2>";
$cssFiles = [
    'assets/css/variables.css',
    'assets/css/base.css',
    'assets/css/components.css',
    'assets/css/layout.css',
    'assets/css/marketplace.css'
];

foreach ($cssFiles as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

// Test 4: Check JavaScript files
echo "<h2>Test 4: JavaScript Files</h2>";
if (file_exists('assets/main.js')) {
    echo "✅ assets/main.js exists<br>";
} else {
    echo "❌ assets/main.js missing<br>";
}

// Test 5: Check page files
echo "<h2>Test 5: Page Files</h2>";
$pageFiles = [
    'pages/landing.php',
    'pages/login.php',
    'pages/register.php',
    'pages/browse.php',
    'pages/cart.php'
];

foreach ($pageFiles as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

echo "<h2>Test 6: Session Info</h2>";
echo "Session ID: " . session_id() . "<br>";
echo "User logged in: " . (isset($_SESSION['user_id']) ? 'Yes' : 'No') . "<br>";

echo "<h2>Test 7: Links</h2>";
echo '<a href="index.php">Home</a> | ';
echo '<a href="index.php?page=browse">Browse</a> | ';
echo '<a href="index.php?page=login">Login</a> | ';
echo '<a href="index.php?page=register">Register</a><br>';

?>
<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { color: #2c5530; }
h2 { color: #4a7c59; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
</style>