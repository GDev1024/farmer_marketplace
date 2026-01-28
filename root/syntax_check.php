<?php
/**
 * Simple PHP Syntax Checker
 * Validates PHP files without using shell commands
 */

function checkPHPSyntax($file) {
    if (!file_exists($file)) {
        return ['status' => 'error', 'message' => 'File not found'];
    }
    
    $content = file_get_contents($file);
    
    // Use PHP's tokenizer to check syntax
    $tokens = @token_get_all($content);
    
    if ($tokens === false) {
        return ['status' => 'error', 'message' => 'Failed to tokenize'];
    }
    
    // Basic checks
    $errors = [];
    
    // Check for common syntax issues
    if (preg_match('/\$[a-zA-Z_][a-zA-Z0-9_]*\s*=\s*;/', $content)) {
        $errors[] = 'Empty assignment';
    }
    
    if (preg_match('/function\s+[a-zA-Z_][a-zA-Z0-9_]*\s*\([^)]*\)\s*;/', $content)) {
        $errors[] = 'Function declaration without body';
    }
    
    // Check for unmatched braces
    $openBraces = substr_count($content, '{');
    $closeBraces = substr_count($content, '}');
    if ($openBraces !== $closeBraces) {
        $errors[] = "Unmatched braces (open: $openBraces, close: $closeBraces)";
    }
    
    // Check for unmatched parentheses
    $openParens = substr_count($content, '(');
    $closeParens = substr_count($content, ')');
    if ($openParens !== $closeParens) {
        $errors[] = "Unmatched parentheses (open: $openParens, close: $closeParens)";
    }
    
    if (empty($errors)) {
        return ['status' => 'success', 'message' => 'Syntax appears valid'];
    } else {
        return ['status' => 'error', 'message' => implode(', ', $errors)];
    }
}

echo "<h1>🔍 PHP Syntax Checker</h1>";

$phpFiles = [
    'index.php', 'login.php', 'register.php', 'dashboard.php', 
    'cart.php', 'checkout.php', 'order-success.php', 'orders.php',
    'includes/config.php', 'includes/functions.php', 'includes/env-loader.php',
    'actions.php'
];

foreach ($phpFiles as $file) {
    $result = checkPHPSyntax($file);
    
    if ($result['status'] === 'success') {
        echo "✅ $file - {$result['message']}<br>";
    } else {
        echo "❌ $file - {$result['message']}<br>";
    }
}

echo "<h2>Summary</h2>";
echo "<p>Basic syntax validation completed. For full validation, run files in PHP environment.</p>";
echo "<p><a href='error_check.php'>Run Full Error Check</a> | <a href='test_connection.php'>Test Connection</a></p>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syntax Check - Grenada Farmer Marketplace</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
        h1, h2 { color: #3d5a3a; }
        a { color: #3d5a3a; text-decoration: none; padding: 8px 12px; background: #f8f9fa; border: 1px solid #3d5a3a; border-radius: 4px; margin: 5px; display: inline-block; }
        a:hover { background: #3d5a3a; color: white; }
    </style>
</head>
<body>
</body>
</html>