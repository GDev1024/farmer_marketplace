<?php
/**
 * Simple connection test for XAMPP deployment
 */

// Test includes
echo "<h2>Testing File Includes...</h2>";

try {
    require_once 'includes/config.php';
    echo "✅ Config loaded successfully<br>";
    
    require_once 'includes/functions.php';
    echo "✅ Functions loaded successfully<br>";
    
    require_once 'includes/env-loader.php';
    echo "✅ Environment loader loaded successfully<br>";
    
} catch (Exception $e) {
    echo "❌ Include error: " . $e->getMessage() . "<br>";
}

// Test database connection
echo "<h2>Testing Database Connection...</h2>";

try {
    $pdo = Config::getDB();
    echo "✅ Database connection successful<br>";
    
    // Test if tables exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "✅ Found " . count($tables) . " tables: " . implode(', ', $tables) . "<br>";
    } else {
        echo "⚠️ No tables found. Run setup.php to create tables.<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
    echo "💡 Make sure MySQL is running in XAMPP<br>";
}

// Test functions
echo "<h2>Testing Functions...</h2>";

try {
    if (function_exists('sanitizeInput')) {
        $test = sanitizeInput('<script>alert("test")</script>');
        echo "✅ sanitizeInput() works: " . htmlspecialchars($test) . "<br>";
    } else {
        echo "❌ sanitizeInput() function not found<br>";
    }
    
    if (function_exists('isLoggedIn')) {
        echo "✅ isLoggedIn() function exists<br>";
    } else {
        echo "❌ isLoggedIn() function not found<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Function test error: " . $e->getMessage() . "<br>";
}

// Test CSS files
echo "<h2>Testing CSS Files...</h2>";

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

echo "<h2>Summary</h2>";
echo "<p>If all tests pass, your XAMPP deployment is ready!</p>";
echo "<p><a href='index.php'>Go to Homepage</a> | <a href='register.php'>Register</a> | <a href='login.php'>Login</a></p>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connection Test - Grenada Farmer Marketplace</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        h2 { color: #3d5a3a; border-bottom: 2px solid #3d5a3a; padding-bottom: 5px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        a { color: #3d5a3a; text-decoration: none; padding: 10px 15px; background: #f8f9fa; border: 1px solid #3d5a3a; border-radius: 5px; margin: 5px; display: inline-block; }
        a:hover { background: #3d5a3a; color: white; }
    </style>
</head>
<body>
    <h1>🌾 XAMPP Connection Test</h1>
</body>
</html>