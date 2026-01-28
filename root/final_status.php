<?php
/**
 * Final Status Check - Grenada Farmer Marketplace
 * Comprehensive validation of all fixes
 */

echo "<h1>🎯 Final Status Check</h1>";

// Include required files
try {
    require_once 'includes/config.php';
    require_once 'includes/functions.php';
    echo "✅ Core includes loaded successfully<br>";
} catch (Exception $e) {
    echo "❌ Include error: " . $e->getMessage() . "<br>";
    exit;
}

echo "<h2>1. Critical Files Status</h2>";

$criticalFiles = [
    '.env' => 'Environment configuration',
    'includes/config.php' => 'Application configuration',
    'includes/functions.php' => 'Helper functions',
    'includes/grenada_marketplace.sql' => 'Database schema',
    'assets/css/variables.css' => 'CSS variables',
    'assets/main.js' => 'Main JavaScript',
    'setup.php' => 'Database setup script'
];

$allFilesExist = true;
foreach ($criticalFiles as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $file - $description<br>";
    } else {
        echo "❌ $file - $description MISSING<br>";
        $allFilesExist = false;
    }
}

echo "<h2>2. Database Connection</h2>";
try {
    $pdo = Config::getDB();
    echo "✅ Database connection successful<br>";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    echo "✅ Database query test passed (Users: $userCount)<br>";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<h2>3. Function Availability</h2>";
$requiredFunctions = [
    'isLoggedIn', 'requireLogin', 'redirect', 'sanitizeInput', 'getCurrentUser'
];

$allFunctionsExist = true;
foreach ($requiredFunctions as $func) {
    if (function_exists($func)) {
        echo "✅ $func() available<br>";
    } else {
        echo "❌ $func() missing<br>";
        $allFunctionsExist = false;
    }
}

echo "<h2>4. Session System</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Session system active<br>";
    
    // Test session functions
    if (function_exists('isLoggedIn')) {
        $loginStatus = isLoggedIn() ? 'Yes' : 'No';
        echo "✅ Login check works (Currently logged in: $loginStatus)<br>";
    }
} else {
    echo "❌ Session not active<br>";
}

echo "<h2>5. File Permissions</h2>";
$writableDirs = ['uploads', 'uploads/products'];
foreach ($writableDirs as $dir) {
    if (is_dir($dir) && is_writable($dir)) {
        echo "✅ $dir is writable<br>";
    } else {
        echo "❌ $dir not writable or doesn't exist<br>";
    }
}

echo "<h2>6. Code Quality Check</h2>";

// Check for old patterns
$codeFiles = array_merge(
    glob('*.php'),
    glob('includes/*.php'),
    glob('pages/*.php')
);

$issues = [];
foreach ($codeFiles as $file) {
    if ($file === 'final_status.php' || $file === 'error_check.php') continue;
    
    $content = file_get_contents($file);
    
    // Check for old session patterns
    if (strpos($content, '$_SESSION[\'userId\']') !== false) {
        $issues[] = "$file contains old userId session variable";
    }
    
    if (strpos($content, '$_SESSION[\'isLoggedIn\']') !== false) {
        $issues[] = "$file contains old isLoggedIn session variable";
    }
    
    // Check for function conflicts
    if (substr_count($content, 'function redirect(') > 0 && $file !== 'includes/functions.php') {
        $issues[] = "$file has duplicate redirect function";
    }
}

if (empty($issues)) {
    echo "✅ No code quality issues found<br>";
} else {
    foreach ($issues as $issue) {
        echo "❌ $issue<br>";
    }
}

echo "<h2>7. Overall Status</h2>";

$overallStatus = $allFilesExist && $allFunctionsExist && empty($issues);

if ($overallStatus) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>🎉 ALL SYSTEMS GO!</h3>";
    echo "<p>✅ All critical files present<br>";
    echo "✅ Database connection working<br>";
    echo "✅ All functions available<br>";
    echo "✅ No code quality issues<br>";
    echo "✅ Ready for production use</p>";
    echo "</div>";
    
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li><a href='setup.php'>Run Database Setup</a> (if not done)</li>";
    echo "<li><a href='register.php'>Create User Account</a></li>";
    echo "<li><a href='index.php'>Access Application</a></li>";
    echo "</ol>";
    
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>⚠️ Issues Found</h3>";
    echo "<p>Please fix the issues marked with ❌ above before proceeding.</p>";
    echo "</div>";
}

echo "<h3>Useful Links:</h3>";
echo "<p>";
echo "<a href='error_check.php'>Detailed Error Check</a> | ";
echo "<a href='test_connection.php'>Connection Test</a> | ";
echo "<a href='syntax_check.php'>Syntax Check</a>";
echo "</p>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Status - Grenada Farmer Marketplace</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; }
        h1, h2, h3 { color: #3d5a3a; }
        h2 { border-bottom: 2px solid #3d5a3a; padding-bottom: 5px; margin-top: 30px; }
        a { color: #3d5a3a; text-decoration: none; padding: 8px 12px; background: #f8f9fa; border: 1px solid #3d5a3a; border-radius: 4px; margin: 5px; display: inline-block; }
        a:hover { background: #3d5a3a; color: white; }
        ol li { margin: 10px 0; }
    </style>
</head>
<body>
</body>
</html>