<?php
/**
 * Property-Based Test: Color Contrast Accessibility
 * Feature: design-system-migration, Property 12: Color Contrast Accessibility
 * 
 * Validates: Requirements 12.3
 * 
 * Property: For any text or interactive element in the application, color 
 * contrast ratios should meet WCAG 2.1 AA standards (4.5:1 for normal text, 
 * 3:1 for large text)
 */

class ColorContrastAccessibilityTest {
    
    private $colorPalette = [
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
    
    private $textElements = [
        ['type' => 'normal', 'size' => '16px', 'weight' => 'normal'],
        ['type' => 'small', 'size' => '14px', 'weight' => 'normal'],
        ['type' => 'large', 'size' => '18px', 'weight' => 'normal'],
        ['type' => 'heading', 'size' => '24px', 'weight' => 'bold'],
        ['type' => 'button', 'size' => '16px', 'weight' => 'medium'],
        ['type' => 'link', 'size' => '16px', 'weight' => 'normal'],
        ['type' => 'caption', 'size' => '12px', 'weight' => 'normal']
    ];
    
    private $commonCombinations = [
        // Text on backgrounds
        ['foreground' => 'text-primary', 'background' => 'bg-primary', 'context' => 'Normal text on white'],
        ['foreground' => 'text-primary', 'background' => 'bg-secondary', 'context' => 'Normal text on cream'],
        ['foreground' => 'text-secondary', 'background' => 'bg-primary', 'context' => 'Secondary text on white'],
        ['foreground' => 'text-muted', 'background' => 'bg-primary', 'context' => 'Muted text on white'],
        ['foreground' => 'text-inverse', 'background' => 'color-primary', 'context' => 'White text on green'],
        
        // Button combinations
        ['foreground' => 'text-inverse', 'background' => 'color-primary', 'context' => 'Primary button'],
        ['foreground' => 'color-primary', 'background' => 'bg-primary', 'context' => 'Secondary button'],
        ['foreground' => 'text-inverse', 'background' => 'color-accent-red', 'context' => 'Danger button'],
        ['foreground' => 'text-inverse', 'background' => 'color-accent-amber', 'context' => 'Warning button'],
        
        // Link combinations
        ['foreground' => 'color-primary', 'background' => 'bg-primary', 'context' => 'Primary links'],
        ['foreground' => 'color-primary-light', 'background' => 'bg-primary', 'context' => 'Hovered links'],
        
        // Alert combinations
        ['foreground' => 'color-accent-green', 'background' => 'color-primary-pale', 'context' => 'Success alert'],
        ['foreground' => 'color-accent-red', 'background' => 'bg-primary', 'context' => 'Error text'],
        ['foreground' => 'color-accent-amber', 'background' => 'bg-primary', 'context' => 'Warning text']
    ];
    
    public function runPropertyTest() {
        echo "🧪 Running Property-Based Test: Color Contrast Accessibility\n";
        echo "Feature: design-system-migration, Property 12: Color Contrast Accessibility\n";
        echo "Validates: Requirements 12.3\n\n";
        
        $iterations = 100;
        $passed = 0;
        $failures = [];
        
        for ($i = 0; $i < $iterations; $i++) {
            $testCase = $this->generateRandomTestCase();
            
            $result = $this->testColorContrastCompliance($testCase);
            if ($result['passed']) {
                $passed++;
            } else {
                $failures[] = $result;
                echo "❌ Test failed: {$result['context']} - Ratio: {$result['ratio']}:1 (needs {$result['required']}:1)\n";
            }
        }
        
        if (count($failures) === 0) {
            echo "✅ Property passed all $iterations test cases\n";
            echo "✅ Property Test PASSED: Color Contrast Accessibility\n";
            echo "All color combinations meet WCAG 2.1 AA standards\n";
            return true;
        } else {
            echo "\n❌ Property Test FAILED: Color Contrast Accessibility\n";
            echo "Failed " . count($failures) . " out of $iterations test cases\n";
            $this->reportFailures($failures);
            return false;
        }
    }
    
    private function generateRandomTestCase() {
        // Mix of predefined combinations and random combinations
        if (rand(0, 1)) {
            // Use a predefined combination
            $combo = $this->commonCombinations[array_rand($this->commonCombinations)];
            $element = $this->textElements[array_rand($this->textElements)];
            
            return [
                'foreground' => $combo['foreground'],
                'background' => $combo['background'],
                'context' => $combo['context'],
                'element' => $element
            ];
        } else {
            // Generate random combination
            $foregroundKeys = array_keys($this->colorPalette);
            $backgroundKeys = array_keys($this->colorPalette);
            
            $foreground = $foregroundKeys[array_rand($foregroundKeys)];
            $background = $backgroundKeys[array_rand($backgroundKeys)];
            $element = $this->textElements[array_rand($this->textElements)];
            
            return [
                'foreground' => $foreground,
                'background' => $background,
                'context' => "Random: $foreground on $background",
                'element' => $element
            ];
        }
    }
    
    private function testColorContrastCompliance($testCase) {
        $foregroundHex = $this->colorPalette[$testCase['foreground']];
        $backgroundHex = $this->colorPalette[$testCase['background']];
        
        // Skip if same color (no contrast)
        if ($foregroundHex === $backgroundHex) {
            return ['passed' => true, 'context' => $testCase['context'], 'ratio' => 1.0, 'required' => 4.5];
        }
        
        $ratio = $this->calculateContrastRatio($foregroundHex, $backgroundHex);
        $isLargeText = $this->isLargeText($testCase['element']);
        $requiredRatio = $isLargeText ? 3.0 : 4.5;
        
        $passed = $ratio >= $requiredRatio;
        
        return [
            'passed' => $passed,
            'context' => $testCase['context'],
            'foreground' => $testCase['foreground'],
            'background' => $testCase['background'],
            'foregroundHex' => $foregroundHex,
            'backgroundHex' => $backgroundHex,
            'ratio' => $ratio,
            'required' => $requiredRatio,
            'isLargeText' => $isLargeText,
            'element' => $testCase['element']
        ];
    }
    
    private function isLargeText($element) {
        // Large text is 18pt+ (24px+) or 14pt+ (18.5px+) bold
        $size = intval($element['size']);
        $weight = $element['weight'];
        
        return ($size >= 24) || ($size >= 18 && in_array($weight, ['bold', 'black']));
    }
    
    private function calculateContrastRatio($color1, $color2) {
        $rgb1 = $this->hexToRgb($color1);
        $rgb2 = $this->hexToRgb($color2);
        
        $lum1 = $this->getLuminance($rgb1);
        $lum2 = $this->getLuminance($rgb2);
        
        $lighter = max($lum1, $lum2);
        $darker = min($lum1, $lum2);
        
        return ($lighter + 0.05) / ($darker + 0.05);
    }
    
    private function hexToRgb($hex) {
        $hex = ltrim($hex, '#');
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2))
        ];
    }
    
    private function getLuminance($rgb) {
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
    
    private function reportFailures($failures) {
        echo "\n🔧 FAILING COLOR COMBINATIONS:\n";
        echo str_repeat('=', 60) . "\n";
        
        $uniqueFailures = [];
        foreach ($failures as $failure) {
            $key = $failure['foreground'] . '_' . $failure['background'];
            if (!isset($uniqueFailures[$key])) {
                $uniqueFailures[$key] = $failure;
            }
        }
        
        foreach ($uniqueFailures as $failure) {
            echo "❌ {$failure['context']}\n";
            echo "   Foreground: {$failure['foreground']} ({$failure['foregroundHex']})\n";
            echo "   Background: {$failure['background']} ({$failure['backgroundHex']})\n";
            echo "   Ratio: " . number_format($failure['ratio'], 2) . ":1 (needs {$failure['required']}:1)\n";
            echo "   Text Type: " . ($failure['isLargeText'] ? 'Large' : 'Normal') . "\n";
            
            $improvement = $failure['required'] / $failure['ratio'];
            echo "   💡 Needs " . number_format($improvement, 2) . "x more contrast\n";
            echo "\n";
        }
        
        echo "📊 Summary: " . count($uniqueFailures) . " unique failing combinations\n";
        echo "🎯 Recommendation: Review and adjust failing color combinations\n";
    }
}

// Run the test
$test = new ColorContrastAccessibilityTest();
$result = $test->runPropertyTest();

if (!$result) {
    echo "\n❌ Property Test FAILED: Color Contrast Accessibility\n";
    echo "Some color combinations do not meet WCAG 2.1 AA standards\n";
    exit(1);
}

echo "\n🎉 All color contrast accessibility tests passed!\n";
echo "All combinations meet WCAG 2.1 AA standards (4.5:1 for normal text, 3:1 for large text)\n";
?>