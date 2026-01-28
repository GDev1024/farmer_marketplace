<?php
/**
 * Color Contrast Verification for WCAG 2.1 AA Compliance
 * Tests all color combinations used in the Grenada Farmer Marketplace
 */

class ColorContrastChecker {
    
    // WCAG 2.1 AA Requirements
    const NORMAL_TEXT_RATIO = 4.5;
    const LARGE_TEXT_RATIO = 3.0;
    
    private $colors = [
        // Primary Colors
        'primary' => '#3d5a3a',
        'primary-light' => '#5a7456',
        'primary-dark' => '#2d4429',
        'primary-pale' => '#e8ede7',
        
        // Secondary Colors
        'secondary' => '#8b7355',
        'secondary-light' => '#a68968',
        'secondary-dark' => '#6d5940',
        'secondary-pale' => '#f5f1ec',
        
        // Neutral Colors
        'white' => '#ffffff',
        'cream' => '#fefdfb',
        'gray-50' => '#fafaf9',
        'gray-100' => '#f5f5f4',
        'gray-200' => '#e7e5e4',
        'gray-300' => '#d6d3d1',
        'gray-400' => '#a8a29e',
        'gray-500' => '#78716c',
        'gray-600' => '#57534e',
        'gray-700' => '#44403c',
        'gray-800' => '#292524',
        'gray-900' => '#1c1917',
        'black' => '#0a0a0a',
        
        // Accent Colors
        'success' => '#3d5a3a',
        'warning' => '#d97706',
        'error' => '#dc2626',
        'info' => '#2563eb',
        
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
    
    /**
     * Convert hex color to RGB values
     */
    private function hexToRgb($hex) {
        $hex = ltrim($hex, '#');
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2))
        ];
    }
    
    /**
     * Calculate relative luminance according to WCAG formula
     */
    private function getRelativeLuminance($rgb) {
        $rsRGB = $rgb['r'] / 255;
        $gsRGB = $rgb['g'] / 255;
        $bsRGB = $rgb['b'] / 255;
        
        $r = ($rsRGB <= 0.03928) ? $rsRGB / 12.92 : pow(($rsRGB + 0.055) / 1.055, 2.4);
        $g = ($gsRGB <= 0.03928) ? $gsRGB / 12.92 : pow(($gsRGB + 0.055) / 1.055, 2.4);
        $b = ($bsRGB <= 0.03928) ? $bsRGB / 12.92 : pow(($bsRGB + 0.055) / 1.055, 2.4);
        
        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }
    
    /**
     * Calculate contrast ratio between two colors
     */
    private function getContrastRatio($color1, $color2) {
        $rgb1 = $this->hexToRgb($color1);
        $rgb2 = $this->hexToRgb($color2);
        
        $l1 = $this->getRelativeLuminance($rgb1);
        $l2 = $this->getRelativeLuminance($rgb2);
        
        $lighter = max($l1, $l2);
        $darker = min($l1, $l2);
        
        return ($lighter + 0.05) / ($darker + 0.05);
    }
    
    /**
     * Check if contrast ratio meets WCAG requirements
     */
    private function meetsWCAG($ratio, $isLargeText = false) {
        $requiredRatio = $isLargeText ? self::LARGE_TEXT_RATIO : self::NORMAL_TEXT_RATIO;
        return $ratio >= $requiredRatio;
    }
    
    /**
     * Get WCAG compliance level
     */
    private function getComplianceLevel($ratio) {
        if ($ratio >= 7.0) return 'AAA';
        if ($ratio >= 4.5) return 'AA';
        if ($ratio >= 3.0) return 'AA Large';
        return 'Fail';
    }
    
    /**
     * Test all critical color combinations used in the application
     */
    public function testColorCombinations() {
        $results = [];
        
        // Define critical color combinations used in the application
        $combinations = [
            // Primary text combinations
            ['text-primary', 'bg-primary', 'Primary text on white background', false],
            ['text-primary', 'bg-secondary', 'Primary text on cream background', false],
            ['text-primary', 'bg-tertiary', 'Primary text on light gray background', false],
            
            // Secondary text combinations
            ['text-secondary', 'bg-primary', 'Secondary text on white background', false],
            ['text-secondary', 'bg-secondary', 'Secondary text on cream background', false],
            ['text-muted', 'bg-primary', 'Muted text on white background', false],
            
            // Button combinations
            ['text-inverse', 'primary', 'White text on primary button', false],
            ['text-inverse', 'primary-dark', 'White text on dark primary button', false],
            ['primary', 'bg-primary', 'Primary color text on white background', false],
            ['secondary', 'bg-primary', 'Secondary color text on white background', false],
            
            // Alert/Status combinations
            ['text-inverse', 'success', 'White text on success background', false],
            ['text-inverse', 'warning', 'White text on warning background', false],
            ['text-inverse', 'error', 'White text on error background', false],
            ['text-inverse', 'info', 'White text on info background', false],
            
            // Link combinations
            ['info', 'bg-primary', 'Link color on white background', false],
            ['primary', 'bg-secondary', 'Primary link on cream background', false],
            
            // Form combinations
            ['text-primary', 'white', 'Form text on white input background', false],
            ['text-secondary', 'bg-muted', 'Placeholder text on muted background', false],
            
            // Navigation combinations
            ['text-inverse', 'primary', 'Navigation text on primary background', false],
            ['primary', 'primary-pale', 'Primary text on pale primary background', false],
            
            // Additional critical combinations for comprehensive testing
            ['text-primary', 'bg-muted', 'Primary text on muted background', false],
            ['color-accent-red', 'bg-primary', 'Error text on white background', false],
            ['color-accent-amber', 'bg-primary', 'Warning text on white background', false],
            ['color-accent-blue', 'bg-primary', 'Info text on white background', false],
            ['color-accent-green', 'primary-pale', 'Success text on pale green background', false],
            ['primary-light', 'bg-primary', 'Light primary text on white (hover states)', false],
            ['secondary-light', 'bg-primary', 'Light secondary text on white', false],
            ['gray-600', 'bg-primary', 'Medium gray text on white', false],
            ['gray-700', 'bg-primary', 'Dark gray text on white', false],
            ['text-inverse', 'secondary-dark', 'White text on dark secondary background', false],
            ['primary', 'secondary-pale', 'Primary text on pale secondary background', false],
            
            // Large text combinations (18px+ or 14px+ bold)
            ['text-primary', 'bg-primary', 'Large primary text on white', true],
            ['text-secondary', 'bg-primary', 'Large secondary text on white', true],
            ['text-muted', 'bg-primary', 'Large muted text on white', true],
            ['text-inverse', 'primary', 'Large white text on primary', true],
            ['text-inverse', 'secondary', 'Large white text on secondary', true],
            ['text-inverse', 'primary-light', 'Large white text on light primary', true],
            ['primary', 'bg-secondary', 'Large primary text on cream', true],
            ['secondary', 'bg-primary', 'Large secondary text on white', true]
        ];
        
        foreach ($combinations as [$foreground, $background, $description, $isLargeText]) {
            $fgColor = $this->colors[$foreground] ?? $foreground;
            $bgColor = $this->colors[$background] ?? $background;
            
            $ratio = $this->getContrastRatio($fgColor, $bgColor);
            $passes = $this->meetsWCAG($ratio, $isLargeText);
            $level = $this->getComplianceLevel($ratio);
            
            $results[] = [
                'description' => $description,
                'foreground' => $fgColor,
                'background' => $bgColor,
                'ratio' => round($ratio, 2),
                'passes' => $passes,
                'level' => $level,
                'isLargeText' => $isLargeText,
                'required' => $isLargeText ? self::LARGE_TEXT_RATIO : self::NORMAL_TEXT_RATIO
            ];
        }
        
        return $results;
    }
    
    /**
     * Generate HTML report of color contrast testing
     */
    public function generateReport() {
        $results = $this->testColorCombinations();
        $totalTests = count($results);
        $passedTests = count(array_filter($results, fn($r) => $r['passes']));
        $failedTests = $totalTests - $passedTests;
        
        $html = "<!DOCTYPE html>\n";
        $html .= "<html lang='en'>\n<head>\n";
        $html .= "<meta charset='UTF-8'>\n";
        $html .= "<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
        $html .= "<title>WCAG Color Contrast Report - Grenada Farmer Marketplace</title>\n";
        $html .= "<style>\n";
        $html .= "body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; margin: 2rem; }\n";
        $html .= ".summary { background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; }\n";
        $html .= ".pass { color: #28a745; font-weight: bold; }\n";
        $html .= ".fail { color: #dc3545; font-weight: bold; }\n";
        $html .= ".test-item { border: 1px solid #dee2e6; margin-bottom: 1rem; border-radius: 8px; overflow: hidden; }\n";
        $html .= ".test-header { padding: 1rem; background: #f8f9fa; }\n";
        $html .= ".test-colors { display: flex; height: 60px; }\n";
        $html .= ".color-sample { flex: 1; display: flex; align-items: center; justify-content: center; font-weight: bold; }\n";
        $html .= ".test-details { padding: 1rem; }\n";
        $html .= ".detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }\n";
        $html .= "</style>\n</head>\n<body>\n";
        
        $html .= "<h1>WCAG 2.1 AA Color Contrast Report</h1>\n";
        $html .= "<p>Grenada Farmer Marketplace Design System</p>\n";
        
        $html .= "<div class='summary'>\n";
        $html .= "<h2>Summary</h2>\n";
        $html .= "<p><strong>Total Tests:</strong> {$totalTests}</p>\n";
        $html .= "<p><strong class='pass'>Passed:</strong> {$passedTests}</p>\n";
        $html .= "<p><strong class='fail'>Failed:</strong> {$failedTests}</p>\n";
        $html .= "<p><strong>Success Rate:</strong> " . round(($passedTests / $totalTests) * 100, 1) . "%</p>\n";
        $html .= "</div>\n";
        
        $html .= "<h2>Test Results</h2>\n";
        
        foreach ($results as $result) {
            $statusClass = $result['passes'] ? 'pass' : 'fail';
            $statusText = $result['passes'] ? 'PASS' : 'FAIL';
            
            $html .= "<div class='test-item'>\n";
            $html .= "<div class='test-header'>\n";
            $html .= "<h3>{$result['description']}</h3>\n";
            $html .= "<p class='{$statusClass}'>{$statusText} - {$result['level']}</p>\n";
            $html .= "</div>\n";
            
            $html .= "<div class='test-colors'>\n";
            $html .= "<div class='color-sample' style='background-color: {$result['background']}; color: {$result['foreground']};'>\n";
            $html .= "Sample Text\n";
            $html .= "</div>\n";
            $html .= "</div>\n";
            
            $html .= "<div class='test-details'>\n";
            $html .= "<div class='detail-grid'>\n";
            $html .= "<div><strong>Foreground:</strong> {$result['foreground']}</div>\n";
            $html .= "<div><strong>Background:</strong> {$result['background']}</div>\n";
            $html .= "<div><strong>Contrast Ratio:</strong> {$result['ratio']}:1</div>\n";
            $html .= "<div><strong>Required:</strong> {$result['required']}:1</div>\n";
            $html .= "<div><strong>Text Size:</strong> " . ($result['isLargeText'] ? 'Large (18px+ or 14px+ bold)' : 'Normal') . "</div>\n";
            $html .= "</div>\n";
            $html .= "</div>\n";
            $html .= "</div>\n";
        }
        
        $html .= "</body>\n</html>";
        
        return $html;
    }
    
    /**
     * Run all tests and return summary
     */
    public function runTests() {
        $results = $this->testColorCombinations();
        $totalTests = count($results);
        $passedTests = count(array_filter($results, fn($r) => $r['passes']));
        $failedTests = $totalTests - $passedTests;
        
        return [
            'total' => $totalTests,
            'passed' => $passedTests,
            'failed' => $failedTests,
            'success_rate' => round(($passedTests / $totalTests) * 100, 1),
            'results' => $results
        ];
    }
}

// Run the tests if this file is executed directly
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    $checker = new ColorContrastChecker();
    $summary = $checker->runTests();
    
    echo "WCAG 2.1 AA Color Contrast Test Results\n";
    echo "======================================\n\n";
    echo "Total Tests: {$summary['total']}\n";
    echo "Passed: {$summary['passed']}\n";
    echo "Failed: {$summary['failed']}\n";
    echo "Success Rate: {$summary['success_rate']}%\n\n";
    
    if ($summary['failed'] > 0) {
        echo "Failed Tests:\n";
        echo "-------------\n";
        foreach ($summary['results'] as $result) {
            if (!$result['passes']) {
                echo "❌ {$result['description']}\n";
                echo "   Ratio: {$result['ratio']}:1 (Required: {$result['required']}:1)\n";
                echo "   Colors: {$result['foreground']} on {$result['background']}\n\n";
            }
        }
    }
    
    // Generate HTML report
    $htmlReport = $checker->generateReport();
    file_put_contents('color-contrast-report.html', $htmlReport);
    echo "HTML report generated: color-contrast-report.html\n";
}
?>