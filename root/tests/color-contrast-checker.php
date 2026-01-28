<?php
/**
 * Color Contrast Checker for WCAG Compliance
 * Tests all color combinations in the design system for accessibility compliance
 */

// Color palette from variables.css
$colors = [
    // Primary Colors
    'color-primary' => '#3d5a3a',
    'color-primary-light' => '#5a7456',
    'color-primary-dark' => '#2d4429',
    'color-primary-pale' => '#e8ede7',
    
    // Secondary Colors
    'color-secondary' => '#8b7355',
    'color-secondary-light' => '#a68968',
    'color-secondary-dark' => '#6d5940',
    'color-secondary-pale' => '#f5f1ec',
    
    // Neutral Colors
    'color-white' => '#ffffff',
    'color-cream' => '#fefdfb',
    'color-gray-50' => '#fafaf9',
    'color-gray-100' => '#f5f5f4',
    'color-gray-200' => '#e7e5e4',
    'color-gray-300' => '#d6d3d1',
    'color-gray-400' => '#a8a29e',
    'color-gray-500' => '#78716c',
    'color-gray-600' => '#57534e',
    'color-gray-700' => '#44403c',
    'color-gray-800' => '#292524',
    'color-gray-900' => '#1c1917',
    'color-black' => '#0a0a0a',
    
    // Accent Colors
    'color-accent-green' => '#3d5a3a',
    'color-accent-amber' => '#d97706',
    'color-accent-red' => '#dc2626',
    'color-accent-blue' => '#2563eb',
    
    // Background Colors
    'bg-primary' => '#ffffff',
    'bg-secondary' => '#fefdfb',
    'bg-tertiary' => '#fafaf9',
    'bg-muted' => '#f5f5f4',
    
    // Text Colors
    'text-primary' => '#1c1917',
    'text-secondary' => '#57534e',
    'text-muted' => '#78716c',
    'text-inverse' => '#ffffff',
    
    // Border Colors
    'border-primary' => '#e7e5e4',
    'border-secondary' => '#f5f5f4',
    'border-light' => '#e7e5e4',
    'border-focus' => '#3d5a3a'
];

// Common color combinations used in the application
$colorCombinations = [
    // Text on backgrounds
    ['foreground' => 'text-primary', 'background' => 'bg-primary', 'context' => 'Normal text on white background'],
    ['foreground' => 'text-primary', 'background' => 'bg-secondary', 'context' => 'Normal text on cream background'],
    ['foreground' => 'text-primary', 'background' => 'bg-tertiary', 'context' => 'Normal text on light gray background'],
    ['foreground' => 'text-secondary', 'background' => 'bg-primary', 'context' => 'Secondary text on white background'],
    ['foreground' => 'text-secondary', 'background' => 'bg-secondary', 'context' => 'Secondary text on cream background'],
    ['foreground' => 'text-muted', 'background' => 'bg-primary', 'context' => 'Muted text on white background'],
    ['foreground' => 'text-inverse', 'background' => 'color-primary', 'context' => 'White text on primary green'],
    ['foreground' => 'text-inverse', 'background' => 'color-primary-dark', 'context' => 'White text on dark green'],
    
    // Button combinations
    ['foreground' => 'text-inverse', 'background' => 'color-primary', 'context' => 'Primary button text'],
    ['foreground' => 'color-primary', 'background' => 'bg-primary', 'context' => 'Secondary button text'],
    ['foreground' => 'text-inverse', 'background' => 'color-accent-red', 'context' => 'Danger button text'],
    ['foreground' => 'text-inverse', 'background' => 'color-accent-amber', 'context' => 'Warning button text'],
    ['foreground' => 'text-inverse', 'background' => 'color-accent-blue', 'context' => 'Info button text'],
    
    // Link combinations
    ['foreground' => 'color-primary', 'background' => 'bg-primary', 'context' => 'Primary links on white'],
    ['foreground' => 'color-primary', 'background' => 'bg-secondary', 'context' => 'Primary links on cream'],
    ['foreground' => 'color-primary-light', 'background' => 'bg-primary', 'context' => 'Hovered links on white'],
    
    // Alert/notification combinations
    ['foreground' => 'color-accent-green', 'background' => 'color-primary-pale', 'context' => 'Success alert text'],
    ['foreground' => 'color-accent-amber', 'background' => 'bg-primary', 'context' => 'Warning alert text'],
    ['foreground' => 'color-accent-red', 'background' => 'bg-primary', 'context' => 'Error text on white'],
    ['foreground' => 'color-accent-blue', 'background' => 'bg-primary', 'context' => 'Info alert text'],
    
    // Form combinations
    ['foreground' => 'text-primary', 'background' => 'bg-primary', 'context' => 'Form input text'],
    ['foreground' => 'text-muted', 'background' => 'bg-primary', 'context' => 'Form placeholder text'],
    ['foreground' => 'color-accent-red', 'background' => 'bg-primary', 'context' => 'Form error text'],
    
    // Navigation combinations
    ['foreground' => 'text-primary', 'background' => 'bg-primary', 'context' => 'Navigation text'],
    ['foreground' => 'color-primary', 'background' => 'bg-primary', 'context' => 'Active navigation links'],
    
    // Card combinations
    ['foreground' => 'text-primary', 'background' => 'bg-primary', 'context' => 'Card content text'],
    ['foreground' => 'text-secondary', 'background' => 'bg-primary', 'context' => 'Card secondary text'],
    ['foreground' => 'color-primary', 'background' => 'bg-primary', 'context' => 'Card links']
];

/**
 * Convert hex color to RGB
 */
function hexToRgb($hex) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) !== 6) {
        return null;
    }
    
    return [
        'r' => hexdec(substr($hex, 0, 2)),
        'g' => hexdec(substr($hex, 2, 2)),
        'b' => hexdec(substr($hex, 4, 2))
    ];
}

/**
 * Calculate relative luminance of a color
 * Based on WCAG 2.1 specification
 */
function getLuminance($rgb) {
    // Convert to sRGB
    $rsRGB = $rgb['r'] / 255;
    $gsRGB = $rgb['g'] / 255;
    $bsRGB = $rgb['b'] / 255;
    
    // Apply gamma correction
    $rLinear = $rsRGB <= 0.03928 ? $rsRGB / 12.92 : pow(($rsRGB + 0.055) / 1.055, 2.4);
    $gLinear = $gsRGB <= 0.03928 ? $gsRGB / 12.92 : pow(($gsRGB + 0.055) / 1.055, 2.4);
    $bLinear = $bsRGB <= 0.03928 ? $bsRGB / 12.92 : pow(($bsRGB + 0.055) / 1.055, 2.4);
    
    // Calculate luminance
    return 0.2126 * $rLinear + 0.7152 * $gLinear + 0.0722 * $bLinear;
}

/**
 * Calculate contrast ratio between two colors
 * Based on WCAG 2.1 specification
 */
function getContrastRatio($color1, $color2) {
    $rgb1 = hexToRgb($color1);
    $rgb2 = hexToRgb($color2);
    
    if (!$rgb1 || !$rgb2) {
        throw new Exception('Invalid color format');
    }
    
    $lum1 = getLuminance($rgb1);
    $lum2 = getLuminance($rgb2);
    
    $lighter = max($lum1, $lum2);
    $darker = min($lum1, $lum2);
    
    return ($lighter + 0.05) / ($darker + 0.05);
}

/**
 * Check if contrast ratio meets WCAG standards
 */
function checkWCAGCompliance($ratio, $isLargeText = false) {
    $aaThreshold = $isLargeText ? 3.0 : 4.5;
    $aaaThreshold = $isLargeText ? 4.5 : 7.0;
    
    return [
        'ratio' => $ratio,
        'passAA' => $ratio >= $aaThreshold,
        'passAAA' => $ratio >= $aaaThreshold,
        'level' => $ratio >= $aaaThreshold ? 'AAA' : ($ratio >= $aaThreshold ? 'AA' : 'FAIL')
    ];
}

/**
 * Test all color combinations for WCAG compliance
 */
function testColorContrast() {
    global $colors, $colorCombinations;
    
    $results = [];
    $totalTests = 0;
    $passedAA = 0;
    $passedAAA = 0;
    $failed = 0;
    
    echo "🎨 Testing Color Contrast Compliance for Grenada Farmer Marketplace\n";
    echo str_repeat('=', 80) . "\n";
    
    foreach ($colorCombinations as $combo) {
        $foregroundColor = $colors[$combo['foreground']] ?? null;
        $backgroundColor = $colors[$combo['background']] ?? null;
        
        if (!$foregroundColor || !$backgroundColor) {
            echo "⚠️  Missing color definition: {$combo['foreground']} or {$combo['background']}\n";
            continue;
        }
        
        try {
            $ratio = getContrastRatio($foregroundColor, $backgroundColor);
            $normalText = checkWCAGCompliance($ratio, false);
            $largeText = checkWCAGCompliance($ratio, true);
            
            $result = [
                'context' => $combo['context'],
                'foreground' => $combo['foreground'],
                'background' => $combo['background'],
                'foregroundHex' => $foregroundColor,
                'backgroundHex' => $backgroundColor,
                'ratio' => $ratio,
                'normalText' => $normalText,
                'largeText' => $largeText
            ];
            
            $results[] = $result;
            $totalTests++;
            
            // Count results
            if ($normalText['passAA']) $passedAA++;
            if ($normalText['passAAA']) $passedAAA++;
            if (!$normalText['passAA']) $failed++;
            
            // Log result
            $status = $normalText['passAAA'] ? '✅ AAA' : ($normalText['passAA'] ? '✅ AA' : '❌ FAIL');
            $ratioStr = number_format($ratio, 2);
            
            echo "{$status} {$ratioStr}:1 - {$combo['context']}\n";
            echo "     {$combo['foreground']} ({$foregroundColor}) on {$combo['background']} ({$backgroundColor})\n";
            
            if (!$normalText['passAA']) {
                echo "     ⚠️  Normal text fails WCAG AA (needs 4.5:1, got {$ratioStr}:1)\n";
            }
            if (!$largeText['passAA']) {
                echo "     ⚠️  Large text fails WCAG AA (needs 3.0:1, got {$ratioStr}:1)\n";
            }
            
            echo "\n";
            
        } catch (Exception $e) {
            echo "❌ Error testing {$combo['context']}: {$e->getMessage()}\n";
        }
    }
    
    // Summary
    echo str_repeat('=', 80) . "\n";
    echo "📊 SUMMARY\n";
    echo "Total tests: {$totalTests}\n";
    echo "✅ Passed WCAG AA: {$passedAA} (" . number_format(($passedAA/$totalTests)*100, 1) . "%)\n";
    echo "✅ Passed WCAG AAA: {$passedAAA} (" . number_format(($passedAAA/$totalTests)*100, 1) . "%)\n";
    echo "❌ Failed WCAG AA: {$failed} (" . number_format(($failed/$totalTests)*100, 1) . "%)\n";
    
    if ($failed === 0) {
        echo "🎉 All color combinations pass WCAG AA standards!\n";
    } else {
        echo "⚠️  {$failed} color combinations need improvement for WCAG AA compliance.\n";
    }
    
    return [
        'results' => $results,
        'summary' => [
            'total' => $totalTests,
            'passedAA' => $passedAA,
            'passedAAA' => $passedAAA,
            'failed' => $failed,
            'allPass' => $failed === 0
        ]
    ];
}

/**
 * Generate recommendations for failing color combinations
 */
function generateRecommendations($results) {
    $failing = array_filter($results, function($r) {
        return !$r['normalText']['passAA'];
    });
    
    if (empty($failing)) {
        echo "✅ No recommendations needed - all combinations pass WCAG AA!\n";
        return;
    }
    
    echo "🔧 RECOMMENDATIONS FOR FAILING COMBINATIONS:\n";
    echo str_repeat('=', 80) . "\n";
    
    foreach ($failing as $result) {
        echo "❌ {$result['context']}\n";
        echo "   Current ratio: " . number_format($result['ratio'], 2) . ":1 (needs 4.5:1)\n";
        echo "   Colors: {$result['foregroundHex']} on {$result['backgroundHex']}\n";
        
        // Suggest darker foreground or lighter background
        $improvement = 4.5 / $result['ratio'];
        echo "   💡 Suggestion: Increase contrast by " . number_format($improvement, 2) . "x\n";
        echo "      - Make foreground darker, or\n";
        echo "      - Make background lighter\n";
        echo "\n";
    }
}

// Run tests
$testResults = testColorContrast();
generateRecommendations($testResults['results']);
?>