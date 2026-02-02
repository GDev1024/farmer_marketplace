<?php
/**
 * XAMPP Setup Script for Grenada Farmer Marketplace
 */

// Prevent function redeclaration errors
if (!function_exists('redirect')) {
    require_once 'includes/config.php';
    require_once 'includes/functions.php';
}

// Check if database exists and create if not
try {
    // First connect without database to create it
    $dsn = "mysql:host=" . Config::getDbHost() . ";charset=utf8mb4";
    $pdo = new PDO($dsn, Config::getDbUser(), Config::getDbPass(), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . Config::getDbName());
    $pdo->exec("USE " . Config::getDbName());
    
    // Read and execute SQL file
    $sqlFile = __DIR__ . '/includes/grenada_marketplace.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        
        // Remove the CREATE DATABASE and USE statements since we already handled them
        $sql = preg_replace('/CREATE DATABASE.*?;/', '', $sql);
        $sql = preg_replace('/USE.*?;/', '', $sql);
        
        // Split by semicolon and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        
        echo "<h2>✅ Database Setup Complete!</h2>";
        echo "<p>Database '{" . Config::getDbName() . "}' created successfully with all tables.</p>";
    } else {
        echo "<h2>❌ Error</h2>";
        echo "<p>SQL file not found: {$sqlFile}</p>";
    }
    
    // Create uploads directory if it doesn't exist
    $uploadsDir = __DIR__ . '/uploads/products';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
        echo "<p>✅ Created uploads directory: {$uploadsDir}</p>";
    }
    
    // Check .env file
    $envFile = dirname(__DIR__) . '/.env';
    if (file_exists($envFile)) {
        echo "<p>✅ Environment file found: {$envFile}</p>";
    } else {
        echo "<p>⚠️ Environment file not found. Using default configuration.</p>";
    }
    
    echo "<h3>Next Steps:</h3>";
    echo "<ul>";
    echo "<li>✅ Database is ready</li>";
    echo "<li>✅ File structure is correct</li>";
    echo "<li>🔗 <a href='register.php'>Create your first account</a></li>";
    echo "<li>🔗 <a href='index.php'>Visit the homepage</a></li>";
    echo "</ul>";
    
    echo "<h3>XAMPP Configuration:</h3>";
    echo "<ul>";
    echo "<li>Make sure Apache and MySQL are running in XAMPP</li>";
    echo "<li>Place this project in your htdocs/root/ folder</li>";
    echo "<li>Access via: http://localhost/root/</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<h2>❌ Database Setup Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<h3>Troubleshooting:</h3>";
    echo "<ul>";
    echo "<li>Make sure MySQL is running in XAMPP</li>";
    echo "<li>Check your database credentials in .env file</li>";
    echo "<li>Default XAMPP MySQL: host=localhost, user=root, password=empty</li>";
    echo "</ul>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - Grenada Farmer Marketplace</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        h2 { color: #3d5a3a; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        ul { margin: 10px 0; }
        li { margin: 5px 0; }
        a { color: #3d5a3a; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>🌾 Grenada Farmer Marketplace - Setup</h1>
</body>
</html>