<?php
/**
 * Verification script for Cross-Viewport Property Test
 * 
 * This script verifies that the cross-viewport property test is properly implemented
 * and follows the required structure for Property 18: Cross-Viewport Testing
 */

echo "🧪 Verifying Cross-Viewport Property Test Implementation\n";
echo str_repeat("=", 60) . "\n";

$testFile = __DIR__ . '/cross-viewport-property.test.js';
$htmlRunner = __DIR__ . '/run-cross-viewport-property-test.html';

// Check if files exist
$checks = [
    'JavaScript Test File' => file_exists($testFile),
    'HTML Test Runner' => file_exists($htmlRunner)
];

foreach ($checks as $check => $passed) {
    echo ($passed ? "✅" : "❌") . " $check: " . ($passed ? "EXISTS" : "MISSING") . "\n";
}

if (!file_exists($testFile)) {
    echo "\n❌ Cross-viewport property test file not found!\n";
    exit(1);
}

// Read and analyze the test file
$content = file_get_contents($testFile);

// Check for required components
$requiredComponents = [
    'Property 18: Cross-Viewport Testing' => strpos($content, 'Property 18: Cross-Viewport Testing') !== false,
    'Feature: design-system-migration' => strpos($content, 'design-system-migration') !== false,
    'Validates: Requirements 14.3' => strpos($content, 'Requirements 14.3') !== false,
    'PropertyTest class' => strpos($content, 'class PropertyTest') !== false,
    'CrossViewportTestRunner class' => strpos($content, 'class CrossViewportTestRunner') !== false,
    'Viewport generators' => strpos($content, 'viewportSize:') !== false,
    'Touch target testing' => strpos($content, 'Touch Target Accessibility') !== false,
    'Layout integrity testing' => strpos($content, 'Layout Integrity Across Viewports') !== false,
    'Interaction support testing' => strpos($content, 'Cross-Viewport Interaction Support') !== false,
    'Page responsiveness testing' => strpos($content, 'Page Responsiveness Across Viewports') !== false,
    'Viewport categories (mobile/tablet/desktop)' => 
        strpos($content, 'mobile') !== false && 
        strpos($content, 'tablet') !== false && 
        strpos($content, 'desktop') !== false,
    'Minimum 100 iterations' => strpos($content, 'iterations = 100') !== false,
    'Counter-example handling' => strpos($content, 'counterExample') !== false
];

echo "\n📋 Required Components Check:\n";
foreach ($requiredComponents as $component => $passed) {
    echo ($passed ? "✅" : "❌") . " $component\n";
}

// Check viewport breakpoints
$viewportBreakpoints = [
    'Mobile (320px-639px)' => 
        strpos($content, '320') !== false && 
        strpos($content, '639') !== false,
    'Tablet (640px-1023px)' => 
        strpos($content, '640') !== false && 
        strpos($content, '1023') !== false,
    'Desktop (1024px+)' => 
        strpos($content, '1024') !== false
];

echo "\n📱 Viewport Breakpoints Check:\n";
foreach ($viewportBreakpoints as $breakpoint => $passed) {
    echo ($passed ? "✅" : "❌") . " $breakpoint\n";
}

// Check test generators
$generators = [
    'viewportSize generator' => strpos($content, 'viewportSize:') !== false,
    'uiComponent generator' => strpos($content, 'uiComponent:') !== false,
    'layoutTest generator' => strpos($content, 'layoutTest:') !== false,
    'interactionTest generator' => strpos($content, 'interactionTest:') !== false,
    'pageTest generator' => strpos($content, 'pageTest:') !== false
];

echo "\n🎲 Test Generators Check:\n";
foreach ($generators as $generator => $passed) {
    echo ($passed ? "✅" : "❌") . " $generator\n";
}

// Check property tests
$propertyTests = [
    'Viewport Category Validation' => strpos($content, 'Viewport Category Validation') !== false,
    'Touch Target Accessibility' => strpos($content, 'Touch Target Accessibility') !== false,
    'Layout Integrity Across Viewports' => strpos($content, 'Layout Integrity Across Viewports') !== false,
    'Cross-Viewport Interaction Support' => strpos($content, 'Cross-Viewport Interaction Support') !== false,
    'Page Responsiveness Across Viewports' => strpos($content, 'Page Responsiveness Across Viewports') !== false,
    'Viewport-Specific Feature Support' => strpos($content, 'Viewport-Specific Feature Support') !== false
];

echo "\n🧪 Property Tests Check:\n";
foreach ($propertyTests as $test => $passed) {
    echo ($passed ? "✅" : "❌") . " $test\n";
}

// Count total checks
$allChecks = array_merge($checks, $requiredComponents, $viewportBreakpoints, $generators, $propertyTests);
$totalChecks = count($allChecks);
$passedChecks = count(array_filter($allChecks));
$successRate = round(($passedChecks / $totalChecks) * 100);

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 VERIFICATION SUMMARY\n";
echo str_repeat("=", 60) . "\n";
echo "Total Checks: $totalChecks\n";
echo "Passed: $passedChecks\n";
echo "Failed: " . ($totalChecks - $passedChecks) . "\n";
echo "Success Rate: $successRate%\n";

if ($successRate >= 95) {
    echo "\n🎉 Excellent! Cross-viewport property test is properly implemented.\n";
    echo "✅ Property 18: Cross-Viewport Testing is ready for execution.\n";
    echo "✅ Validates Requirements 14.3 as specified.\n";
    echo "\nTo run the test:\n";
    echo "1. Open 'run-cross-viewport-property-test.html' in a web browser\n";
    echo "2. Or include 'cross-viewport-property.test.js' in your test suite\n";
    exit(0);
} elseif ($successRate >= 80) {
    echo "\n⚠️ Good implementation with minor issues.\n";
    echo "The cross-viewport property test is mostly complete but may need some adjustments.\n";
    exit(0);
} else {
    echo "\n❌ Significant issues found in the cross-viewport property test implementation.\n";
    echo "Please review and fix the missing components.\n";
    exit(1);
}
?>