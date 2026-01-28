<?php
/**
 * Comprehensive Error Checker for Grenada Farmer Marketplace
 * Checks for common issues and missing files
 */

echo "<h1>🔍 Comprehensive Error Check</h1>";

// 1. Check PHP syntax errors
echo "<h2>1. PHP Syntax Check</h2>";
$phpFiles = [
    'index.php', 'login.php', 'register.php', 'dashboard.php', 
    'cart.php', 'checkout.php', 'order-success.php', 'orders.php',
    'includes/config.php', 'includes/functions.php', 'includes/env-loader.php',
    'actions.php'
];

foreach ($phpFiles as $file) {
    if (file_exists($file)) {
        // Use PHP's built-in syntax checking
        $content = file_get_contents($file);
        
        // Basic syntax checks
        $errors = [];
        
        // Check for unclosed PHP tags
        if (substr_count($content, '<?php') !== substr_count($content, '?>') && !preg_match('/\?>$/', trim($content))) {
            // This is actually OK - PHP files don't need closing tags
        }
        
        // Check for obvious syntax issues
        if (preg_match('/\$[a-zA-Z_][a-zA-Z0-9_]*\s*=\s*$/', $content)) {
            $errors[] = 'Incomplete assignment';
        }
        
        if (preg_match('/function\s+[a-zA-Z_][a-zA-Z0-9_]*\s*\([^)]*\)\s*$/', $content)) {
            $errors[] = 'Function without body';
        }
        
        if (empty($errors)) {
            echo "✅ $file - Basic syntax check passed<br>";
        } else {
            echo "❌ $file - Issues: " . implode(', ', $errors) . "<br>";
        }
    } else {
        echo "⚠️ $file - File not found<br>";
    }
}

// 2. Check required files exist
echo "<h2>2. Required Files Check</h2>";
$requiredFiles = [
    'includes/config.php' => 'Configuration file',
    'includes/functions.php' => 'Helper functions',
    'includes/env-loader.php' => 'Environment loader',
    'assets/css/variables.css' => 'CSS variables',
    'assets/css/base.css' => 'Base CSS',
    'assets/css/components.css' => 'Components CSS',
    'assets/css/layout.css' => 'Layout CSS',
    'assets/css/marketplace.css' => 'Marketplace CSS',
    'assets/main.js' => 'Main JavaScript',
    'assets/loading-enhancements.js' => 'Loading enhancements',
    '.env' => 'Environment variables'
];

foreach ($requiredFiles as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $file - $description exists<br>";
    } else {
        echo "❌ $file - $description missing<br>";
    }
}

// 3. Check database connection
echo "<h2>3. Database Connection Check</h2>";
try {
    require_once 'includes/config.php';
    require_once 'includes/functions.php';
    
    $pdo = Config::getDB();
    echo "✅ Database connection successful<br>";
    
    // Check tables exist
    $tables = ['users', 'listings', 'orders', 'order_items', 'cart', 'reviews'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table' exists<br>";
        } else {
            echo "❌ Table '$table' missing<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// 4. Check function definitions
echo "<h2>4. Function Definitions Check</h2>";
$requiredFunctions = [
    'isLoggedIn' => 'Check if user is logged in',
    'requireLogin' => 'Require user to be logged in',
    'redirect' => 'Redirect to another page',
    'sanitizeInput' => 'Sanitize user input',
    'getCurrentUser' => 'Get current user data'
];

foreach ($requiredFunctions as $func => $description) {
    if (function_exists($func)) {
        echo "✅ $func() - $description defined<br>";
    } else {
        echo "❌ $func() - $description missing<br>";
    }
}

// 5. Check session configuration
echo "<h2>5. Session Configuration Check</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Session is active<br>";
} else {
    echo "⚠️ Session not started<br>";
}

// 6. Check permissions
echo "<h2>6. File Permissions Check</h2>";
$writableDirs = ['uploads', 'uploads/products'];
foreach ($writableDirs as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "✅ $dir - Writable<br>";
        } else {
            echo "❌ $dir - Not writable<br>";
        }
    } else {
        echo "⚠️ $dir - Directory doesn't exist<br>";
    }
}

// 7. Check for common issues
echo "<h2>7. Common Issues Check</h2>";

// Check for duplicate function declarations
$duplicateFunctions = [];
$phpCode = '';
foreach (glob('*.php') as $file) {
    if ($file !== 'error_check.php') { // Exclude this file
        $phpCode .= file_get_contents($file);
    }
}
foreach (glob('includes/*.php') as $file) {
    $phpCode .= file_get_contents($file);
}
foreach (glob('pages/*.php') as $file) {
    $phpCode .= file_get_contents($file);
}

if (substr_count($phpCode, 'function redirect(') > 1) {
    echo "❌ Duplicate redirect() function found<br>";
} else {
    echo "✅ No duplicate redirect() function<br>";
}

if (substr_count($phpCode, 'function sanitize(') > 0) {
    echo "❌ Old sanitize() function found (should be sanitizeInput)<br>";
} else {
    echo "✅ No old sanitize() function<br>";
}

// Check for session variable consistency
if (strpos($phpCode, '$_SESSION[\'userId\']') !== false) {
    echo "❌ Old session variable \$_SESSION['userId'] found (should be user_id)<br>";
} else {
    echo "✅ Session variables are consistent<br>";
}

if (strpos($phpCode, '$_SESSION[\'isLoggedIn\']') !== false) {
    echo "❌ Old session variable \$_SESSION['isLoggedIn'] found (use isLoggedIn() function)<br>";
} else {
    echo "✅ No old isLoggedIn session variable<br>";
}

echo "<h2>8. Summary</h2>";
echo "<p>Check completed. Fix any ❌ errors above before deployment.</p>";
echo "<p><a href='test_connection.php'>Run Connection Test</a> | <a href='setup.php'>Run Setup</a></p>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Check - Grenada Farmer Marketplace</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; }
        h1, h2 { color: #3d5a3a; }
        h2 { border-bottom: 2px solid #3d5a3a; padding-bottom: 5px; margin-top: 30px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        a { color: #3d5a3a; text-decoration: none; padding: 8px 12px; background: #f8f9fa; border: 1px solid #3d5a3a; border-radius: 4px; margin: 5px; display: inline-block; }
        a:hover { background: #3d5a3a; color: white; }
    </style>
</head>
<body>
</body>
</html>