<?php
/**
 * Property-Based Test: CSS Architecture Integrity
 * Feature: design-system-migration, Property 14: CSS Architecture Integrity
 * 
 * **Validates: Requirements 13.1, 13.2, 13.4**
 * 
 * Tests that the CSS architecture maintains proper separation of concerns,
 * correct import order, and dependency management across all CSS files.
 */

require_once '../includes/css-optimizer.php';

class CSSArchitectureIntegrityTest {
    private $cssFiles = [
        'variables.css',
        'base.css',
        'components.css',
        'layout.css',
        'marketplace.css'
    ];
    
    private $cssPath = '../css/';
    private $mainStylePath = '../assets/style.css';
    private $testResults = [];
    
    /**
     * Property 14: CSS Architecture Integrity
     * For any CSS file in the system, it should maintain proper separation of concerns,
     * follow the correct import hierarchy, and have no circular dependencies.
     */
    public function testCSSArchitectureIntegrity() {
        echo "Feature: design-system-migration, Property 14: CSS Architecture Integrity\n";
        echo "**Validates: Requirements 13.1, 13.2, 13.4**\n\n";
        
        $iterations = 100; // Minimum iterations for property-based testing
        $passed = 0;
        $failed = 0;
        $failures = [];
        
        for ($i = 0; $i < $iterations; $i++) {
            // Generate random test scenarios
            $testScenario = $this->generateTestScenario($i);
            
            try {
                $result = $this->validateArchitectureProperty($testScenario);
                if ($result['valid']) {
                    $passed++;
                } else {
                    $failed++;
                    $failures[] = [
                        'iteration' => $i + 1,
                        'scenario' => $testScenario,
                        'reason' => $result['reason']
                    ];
                }
            } catch (Exception $e) {
                $failed++;
                $failures[] = [
                    'iteration' => $i + 1,
                    'scenario' => $testScenario,
                    'reason' => 'Exception: ' . $e->getMessage()
                ];
            }
        }
        
        echo "Property Test Results:\n";
        echo "Iterations: $iterations\n";
        echo "Passed: $passed\n";
        echo "Failed: $failed\n";
        echo "Success Rate: " . round(($passed / $iterations) * 100, 2) . "%\n\n";
        
        if ($failed > 0) {
            echo "Failures:\n";
            foreach (array_slice($failures, 0, 5) as $failure) { // Show first 5 failures
                echo "- Iteration {$failure['iteration']}: {$failure['reason']}\n";
            }
            if (count($failures) > 5) {
                echo "- ... and " . (count($failures) - 5) . " more failures\n";
            }
            echo "\n";
            return false;
        }
        
        echo "✅ Property 14: CSS Architecture Integrity - PASSED\n";
        return true;
    }
    
    /**
     * Generate random test scenarios for property-based testing
     */
    private function generateTestScenario($iteration) {
        $scenarios = [
            'import_order_validation',
            'file_separation_validation',
            'dependency_validation',
            'design_token_usage',
            'modular_structure_validation'
        ];
        
        return [
            'type' => $scenarios[$iteration % count($scenarios)],
            'file_index' => $iteration % count($this->cssFiles),
            'random_seed' => $iteration
        ];
    }
    
    /**
     * Validate the CSS architecture property for a given scenario
     */
    private function validateArchitectureProperty($scenario) {
        switch ($scenario['type']) {
            case 'import_order_validation':
                return $this->validateImportOrder($scenario);
                
            case 'file_separation_validation':
                return $this->validateFileSeparation($scenario);
                
            case 'dependency_validation':
                return $this->validateDependencies($scenario);
                
            case 'design_token_usage':
                return $this->validateDesignTokenUsage($scenario);
                
            case 'modular_structure_validation':
                return $this->validateModularStructure($scenario);
                
            default:
                return ['valid' => false, 'reason' => 'Unknown test scenario'];
        }
    }
    
    /**
     * Validate that CSS imports follow the correct order
     */
    private function validateImportOrder($scenario) {
        if (!file_exists($this->mainStylePath)) {
            return ['valid' => false, 'reason' => 'Main style file not found'];
        }
        
        $content = file_get_contents($this->mainStylePath);
        $imports = [];
        
        // Extract import statements
        if (preg_match_all('/@import\s+url\([\'"]?([^\'"]+)[\'"]?\);?/i', $content, $matches)) {
            $imports = $matches[1];
        }
        
        // Expected order
        $expectedOrder = [
            'css/variables.css',
            'css/base.css',
            'css/components.css',
            'css/layout.css',
            'css/marketplace.css'
        ];
        
        // Validate order
        for ($i = 0; $i < count($imports) && $i < count($expectedOrder); $i++) {
            if ($imports[$i] !== $expectedOrder[$i]) {
                return [
                    'valid' => false,
                    'reason' => "Import order violation: expected {$expectedOrder[$i]}, got {$imports[$i]} at position $i"
                ];
            }
        }
        
        return ['valid' => true, 'reason' => 'Import order is correct'];
    }
    
    /**
     * Validate that each CSS file maintains proper separation of concerns
     */
    private function validateFileSeparation($scenario) {
        $fileIndex = $scenario['file_index'];
        $fileName = $this->cssFiles[$fileIndex];
        $filePath = $this->cssPath . $fileName;
        
        if (!file_exists($filePath)) {
            return ['valid' => false, 'reason' => "File $fileName not found"];
        }
        
        $content = file_get_contents($filePath);
        
        // Define what each file should contain
        $expectedContent = [
            'variables.css' => ['--color-', '--font-', '--space-', '--radius-', '--shadow-', ':root'],
            'base.css' => ['html', 'body', 'h1', 'h2', 'h3', 'p', 'a', '.container', '.grid'],
            'components.css' => ['.btn', '.card', '.form-', '.modal', '.alert', '.badge'],
            'layout.css' => ['.page', '.header', '.nav', '.footer', '.hero'],
            'marketplace.css' => ['.product-', '.dashboard-', '.auth-', '.landing-', '.cart-']
        ];
        
        $expected = $expectedContent[$fileName] ?? [];
        $foundCount = 0;
        
        foreach ($expected as $pattern) {
            if (strpos($content, $pattern) !== false) {
                $foundCount++;
            }
        }
        
        // At least 50% of expected patterns should be found
        $threshold = max(1, count($expected) * 0.5);
        
        if ($foundCount < $threshold) {
            return [
                'valid' => false,
                'reason' => "File $fileName doesn't contain expected content patterns (found $foundCount of " . count($expected) . ")"
            ];
        }
        
        return ['valid' => true, 'reason' => "File $fileName maintains proper separation of concerns"];
    }
    
    /**
     * Validate that dependencies are properly managed
     */
    private function validateDependencies($scenario) {
        $fileIndex = $scenario['file_index'];
        $fileName = $this->cssFiles[$fileIndex];
        $filePath = $this->cssPath . $fileName;
        
        if (!file_exists($filePath)) {
            return ['valid' => false, 'reason' => "File $fileName not found"];
        }
        
        $content = file_get_contents($filePath);
        
        // Check for circular dependencies (CSS files shouldn't import each other)
        if (preg_match('/@import/', $content)) {
            // Only variables.css and base.css should have imports (Google Fonts)
            if (!in_array($fileName, ['variables.css', 'base.css'])) {
                return [
                    'valid' => false,
                    'reason' => "File $fileName contains @import statements, violating modular architecture"
                ];
            }
        }
        
        // Check for proper CSS custom property usage
        if ($fileName !== 'variables.css') {
            // Non-variables files should use CSS custom properties, not define them
            $customPropDefinitions = preg_match_all('/--[\w-]+\s*:/', $content);
            if ($customPropDefinitions > 5) { // Allow a few exceptions
                return [
                    'valid' => false,
                    'reason' => "File $fileName defines too many CSS custom properties (should use variables.css)"
                ];
            }
        }
        
        return ['valid' => true, 'reason' => "Dependencies are properly managed in $fileName"];
    }
    
    /**
     * Validate proper design token usage
     */
    private function validateDesignTokenUsage($scenario) {
        $fileIndex = $scenario['file_index'];
        $fileName = $this->cssFiles[$fileIndex];
        $filePath = $this->cssPath . $fileName;
        
        if (!file_exists($filePath) || $fileName === 'variables.css') {
            return ['valid' => true, 'reason' => 'Skipping design token validation for variables.css'];
        }
        
        $content = file_get_contents($filePath);
        
        // Check for hardcoded values that should use design tokens
        $hardcodedPatterns = [
            '/color:\s*#[0-9a-fA-F]{3,6}(?!\s*;?\s*\/\*\s*fallback)/',  // Hardcoded hex colors
            '/font-family:\s*[\'"][^\'"]*(Arial|Helvetica|Times)[\'"]/',  // Hardcoded font families
            '/padding:\s*\d+px/',  // Hardcoded pixel padding
            '/margin:\s*\d+px/',   // Hardcoded pixel margins
        ];
        
        foreach ($hardcodedPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return [
                    'valid' => false,
                    'reason' => "File $fileName contains hardcoded values that should use design tokens"
                ];
            }
        }
        
        // Check for proper CSS custom property usage
        $customPropUsage = preg_match_all('/var\(--[\w-]+\)/', $content);
        $totalRules = preg_match_all('/[{;}]/', $content);
        
        if ($totalRules > 10 && $customPropUsage < ($totalRules * 0.1)) {
            return [
                'valid' => false,
                'reason' => "File $fileName has low design token usage ratio ($customPropUsage/$totalRules)"
            ];
        }
        
        return ['valid' => true, 'reason' => "Design tokens are properly used in $fileName"];
    }
    
    /**
     * Validate modular structure integrity
     */
    private function validateModularStructure($scenario) {
        // Check that all required files exist
        foreach ($this->cssFiles as $file) {
            $filePath = $this->cssPath . $file;
            if (!file_exists($filePath)) {
                return ['valid' => false, 'reason' => "Required CSS file $file is missing"];
            }
        }
        
        // Check main style file exists and imports all modules
        if (!file_exists($this->mainStylePath)) {
            return ['valid' => false, 'reason' => 'Main style file is missing'];
        }
        
        $mainContent = file_get_contents($this->mainStylePath);
        foreach ($this->cssFiles as $file) {
            if (strpos($mainContent, "css/$file") === false) {
                return [
                    'valid' => false,
                    'reason' => "Main style file doesn't import $file"
                ];
            }
        }
        
        // Check file sizes are reasonable (not empty, not too large)
        foreach ($this->cssFiles as $file) {
            $filePath = $this->cssPath . $file;
            $size = filesize($filePath);
            
            if ($size < 100) {
                return ['valid' => false, 'reason' => "File $file is too small (possibly empty)"];
            }
            
            if ($size > 100000) { // 100KB limit per file
                return ['valid' => false, 'reason' => "File $file is too large (over 100KB)"];
            }
        }
        
        return ['valid' => true, 'reason' => 'Modular structure is intact'];
    }
    
    /**
     * Run all architecture integrity tests
     */
    public function runAllTests() {
        echo "CSS Architecture Integrity Test Suite\n";
        echo "=====================================\n\n";
        
        $result = $this->testCSSArchitectureIntegrity();
        
        if ($result) {
            echo "\n🎉 All CSS architecture integrity tests passed!\n";
            echo "The CSS architecture maintains proper separation of concerns,\n";
            echo "follows correct import order, and has no dependency issues.\n";
        } else {
            echo "\n❌ CSS architecture integrity tests failed!\n";
            echo "Please review the failures above and fix the architecture issues.\n";
        }
        
        return $result;
    }
}

// Run the tests if this file is executed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $test = new CSSArchitectureIntegrityTest();
    $result = $test->runAllTests();
    exit($result ? 0 : 1);
}
?>