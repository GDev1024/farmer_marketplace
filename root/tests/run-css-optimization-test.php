<?php
/**
 * CSS Optimization Test Runner
 * Runs the property-based test for CSS optimization
 */

// Set up error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the test class
require_once 'css-optimization.test.php';

// Set content type for web output
header('Content-Type: text/plain; charset=utf-8');

echo "CSS Optimization Property-Based Test\n";
echo "====================================\n\n";

try {
    // Create and run the test
    $test = new CSSOptimizationTest();
    $result = $test->runAllTests();
    
    if ($result) {
        echo "\n✅ SUCCESS: All CSS optimization tests passed!\n";
        exit(0);
    } else {
        echo "\n❌ FAILURE: CSS optimization tests failed!\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
?>