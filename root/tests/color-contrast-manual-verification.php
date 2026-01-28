<?php
/**
 * Manual Color Contrast Verification
 * Validates key color combinations for WCAG 2.1 AA compliance
 * Can be run without external dependencies
 */

echo "🎨 WCAG 2.1 AA Color Contrast Verification\n";
echo "Grenada Farmer Marketplace Design System\n";
echo str_repeat('=', 60) . "\n\n";

// Color definitions from variables.css
$colors = [
    'text-primary' => '#1c1917',
    'text-secondary' => '#57534e', 
    'text-muted' => '#78716c',
    'text-inverse' => '#ffffff',
    'bg-primary' => '#ffffff',
    'bg-secondary' => '#fefdfb',
    'bg-tertiary' => '#fafaf9',
    'bg-muted' => '#f5f5f4',
    'primary' => '#3d5a3a',
    'primary-light' => '#5a7456',
    'primary-dark' => '#2d4429',
    'primary-pale' => '#e8ede7',
    'secondary' => '#8b7355',
    'accent-red' => '#dc2626',
    'accent-amber' => '#d97706',
    'accent-blue' => '#2563eb'
];

// Critical color combinations to test
$tests = [
    // High priority combinations
    ['text-primary', 'bg-primary', 'Primary text on white', false, 'CRITICAL'],
    ['text-secondary', 'bg-primary', 'Secondary text on white', false, 'CRITICAL'],
    ['text-muted', 'bg-primary', 'Muted text on white', false, 'CRITICAL'],
    ['text-inverse', 'primary', 'White text on primary button', false, 'CRITICAL'],
    ['primary', 'bg-primary', 'Primary links on white', false, 'CRITICAL'],
    
    // Button combinations
    ['text-inverse', 'primary-dark', 'White on dark primary', false, 'BUTTON'],
    ['text-inverse', 'accent-red', 'White on error button', false, 'BUTTON'],
    ['text-inverse', 'accent-amber', 'White on warning button', false, 'BUTTON'],
    ['text-inverse', 'accent-blue', 'White on info button', false, 'BUTTON'],
    
    // Form and content combinations
    ['text-primary', 'bg-secondary', 'Primary text on cream', false, 'CONTENT'],
    ['text-secondary', 'bg-secondary', 'Secondary text on cream', false, 'CONTENT'],
    ['accent-red', 'bg-primary', 'Error text on white', false, 'FORM'],
    ['primary', 'primary-pale', 'Primary on pale green', false, 'NAVIGATION'],
    
    // Large text combinations (18px+ or 14px+ bold)
    ['text-muted', 'bg-primary', 'Large muted text on white', true, 'LARGE_TEXT'],
    ['text-inverse', 'primary-light', 'Large white on light primary', true, 'LARGE_TEXT']
];

/**
 * Convert hex to RGB
 */
function hexToRgb($hex) {
    $hex = ltrim($hex, '#');
    return [
        'r' => hexdec(substr($hex, 0, 2)),
        'g' => hexdec(substr($hex, 2, 2)),
        'b' => hexdec(substr($hex, 4, 2))
    ];
}

/**
 * Calculate relative luminance
 */
function getLuminance($rgb) {
    $rsRGB = $rgb['r'] / 255;
    $gsRGB = $rgb['g'] / 255;
    $bsRGB = $rgb['b'] / 255;
    
    $r = ($rsRGB <= 0.03928) ? $rsRGB / 12.92 : pow(($rsRGB + 0.055) / 1.055, 2.4);
    $g = ($gsRGB <= 0.03928) ? $gsRGB / 12.92 : pow(($gsRGB + 0.055) / 1.055, 2.4);
    $b = ($bsRGB <= 0.03928) ? $bsRGB / 12.92 : pow(($bsRGB + 0.055) / 1.055, 2.4);
    
    return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
}

/**
 * Calculate contrast ratio
 */
function getContrastRatio($color1, $color2) {
    $rgb1 = hexToRgb($color1);
    $rgb2 = hexToRgb($color2);
    
    $l1 = getLuminance($rgb1);
    $l2 = getLuminance($rgb2);
    
    $lighter = max($l1, $l2);
    $darker = min($l1, $l2);
    
    return ($lighter + 0.05) / ($darker + 0.05);
}

/**
 * Check WCAG compliance
 */
function checkCompliance($ratio, $isLargeText = false) {
    $threshold = $isLargeText ? 3.0 : 4.5;
    $aaaThreshold = $isLargeText ? 4.5 : 7.0;
    
    if ($ratio >= $aaaThreshold) return 'AAA';
    if ($ratio >= $threshold) return 'AA';
    return 'FAIL';
}

// Run tests
$totalTests = 0;
$passedAA = 0;
$passedAAA = 0;
$failed = 0;
$results = [];

foreach ($tests as $test) {
    [$fg, $bg, $description, $isLarge, $category] = $test;
    
    $fgColor = $colors[$fg] ?? $fg;
    $bgColor = $colors[$bg] ?? $bg;
    
    $ratio = getContrastRatio($fgColor, $bgColor);
    $compliance = checkCompliance($ratio, $isLarge);
    $required = $isLarge ? 3.0 : 4.5;
    
    $totalTests++;
    if ($compliance === 'AAA') $passedAAA++;
    if ($compliance === 'AA' || $compliance === 'AAA') $passedAA++;
    if ($compliance === 'FAIL') $failed++;
    
    $status = $compliance === 'AAA' ? '✅ AAA' : 
              ($compliance === 'AA' ? '✅ AA' : '❌ FAIL');
    
    $results[] = [
        'description' => $description,
        'category' => $category,
        'ratio' => $ratio,
        'compliance' => $compliance,
        'required' => $required,
        'isLarge' => $isLarge,
        'fgColor' => $fgColor,
        'bgColor' => $bgColor
    ];
    
    printf("%-8s %5.2f:1 - %s\n", $status, $ratio, $description);
    printf("         %s (%s) on %s (%s)\n", $fg, $fgColor, $bg, $bgColor);
    
    if ($compliance === 'FAIL') {
        $improvement = $required / $ratio;
        printf("         ⚠️  Needs %.2fx more contrast\n", $improvement);
    }
    
    echo "\n";
}

// Summary
echo str_repeat('=', 60) . "\n";
echo "📊 SUMMARY\n";
echo str_repeat('-', 60) . "\n";
printf("Total Tests:     %d\n", $totalTests);
printf("✅ WCAG AA:      %d (%.1f%%)\n", $passedAA, ($passedAA/$totalTests)*100);
printf("✅ WCAG AAA:     %d (%.1f%%)\n", $passedAAA, ($passedAAA/$totalTests)*100);
printf("❌ Failed:       %d (%.1f%%)\n", $failed, ($failed/$totalTests)*100);

echo "\n";

if ($failed === 0) {
    echo "🎉 EXCELLENT! All color combinations pass WCAG AA standards!\n";
    echo "The design system provides strong accessibility foundations.\n";
} else {
    echo "⚠️  Some combinations need improvement for WCAG AA compliance.\n";
}

// Category breakdown
echo "\n📋 BREAKDOWN BY CATEGORY\n";
echo str_repeat('-', 60) . "\n";

$categories = [];
foreach ($results as $result) {
    $cat = $result['category'];
    if (!isset($categories[$cat])) {
        $categories[$cat] = ['total' => 0, 'passed' => 0, 'failed' => 0];
    }
    $categories[$cat]['total']++;
    if ($result['compliance'] !== 'FAIL') {
        $categories[$cat]['passed']++;
    } else {
        $categories[$cat]['failed']++;
    }
}

foreach ($categories as $category => $stats) {
    $rate = ($stats['passed'] / $stats['total']) * 100;
    printf("%-12s: %d/%d passed (%.1f%%)\n", 
           $category, $stats['passed'], $stats['total'], $rate);
}

// Specific recommendations
echo "\n🔧 RECOMMENDATIONS\n";
echo str_repeat('-', 60) . "\n";

$failedResults = array_filter($results, fn($r) => $r['compliance'] === 'FAIL');
if (empty($failedResults)) {
    echo "✅ No immediate changes needed!\n";
    echo "All tested combinations meet WCAG AA standards.\n";
} else {
    foreach ($failedResults as $result) {
        echo "❌ {$result['description']}\n";
        printf("   Current: %.2f:1 (needs %.1f:1)\n", 
               $result['ratio'], $result['required']);
        printf("   Colors: %s on %s\n", 
               $result['fgColor'], $result['bgColor']);
        
        $improvement = $result['required'] / $result['ratio'];
        printf("   💡 Increase contrast by %.2fx\n", $improvement);
        echo "\n";
    }
}

// Final validation message
echo "\n✨ VALIDATION COMPLETE\n";
echo str_repeat('=', 60) . "\n";

if ($passedAA === $totalTests) {
    echo "🎯 WCAG 2.1 AA COMPLIANCE: ACHIEVED\n";
    echo "The color system is ready for production use.\n";
} else {
    echo "🔄 WCAG 2.1 AA COMPLIANCE: NEEDS ATTENTION\n";
    echo "Please address the failing combinations above.\n";
}

echo "\nFor detailed analysis, see: wcag-color-contrast-report.md\n";
?>