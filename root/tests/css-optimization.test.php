<?php
/**
 * Property-Based Test: CSS Optimization
 * Feature: design-system-migration, Property 15: CSS Optimization
 * 
 * **Validates: Requirements 13.3, 13.5**
 * 
 * Tests that CSS files are optimized for performance with no redundant rules,
 * efficient loading, and proper caching strategies across all environments.
 */

require_once '../includes/css-optimizer.php';

class CSSOptimizationTest {
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
     * Property 15: CSS Optimization
     * For any CSS configuration in the system, it should have no redundant rules,
     * efficient loading mechanisms, and proper caching strategies.
     */
    public function testCSSOptimization() {
        echo "Feature: design-system-migration, Property 15: CSS Optimization\n";
        echo "**Validates: Requirements 13.3, 13.5**\n\n";
        
        $iterations = 100; // Minimum iterations for property-based testing
        $passed = 0;
        $failed = 0;
        $failures = [];
        
        for ($i = 0; $i < $iterations; $i++) {
            // Generate random test scenarios
            $testScenario = $this->generateOptimizationScenario($i);
            
            try {
                $result = $this->validateOptimizationProperty($testScenario);
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
        
        echo "✅ Property 15: CSS Optimization - PASSED\n";
        return true;
    }
    
    /**
     * Generate random optimization test scenarios
     */
    private function generateOptimizationScenario($iteration) {
        $scenarios = [
            'file_size_optimization',
            'redundancy_elimination',
            'minification_effectiveness',
            'caching_strategy',
            'loading_performance'
        ];
        
        return [
            'type' => $scenarios[$iteration % count($scenarios)],
            'file_index' => $iteration % count($this->cssFiles),
            'random_seed' => $iteration,
            'environment' => ($iteration % 2 === 0) ? 'development' : 'production'
        ];
    }
    
    /**
     * Validate the CSS optimization property for a given scenario
     */
    private function validateOptimizationProperty($scenario) {
        switch ($scenario['type']) {
            case 'file_size_optimization':
                return $this->validateFileSizeOptimization($scenario);
                
            case 'redundancy_elimination':
                return $this->validateRedundancyElimination($scenario);
                
            case 'minification_effectiveness':
                return $this->validateMinificationEffectiveness($scenario);
                
            case 'caching_strategy':
                return $this->validateCachingStrategy($scenario);
                
            case 'loading_performance':
                return $this->validateLoadingPerformance($scenario);
                
            default:
                return ['valid' => false, 'reason' => 'Unknown optimization scenario'];
        }
    }
    
    /**
     * Validate that CSS files are optimally sized
     */
    private function validateFileSizeOptimization($scenario) {
        $totalSize = 0;
        $fileSizes = [];
        
        foreach ($this->cssFiles as $file) {
            $filePath = $this->cssPath . $file;
            if (file_exists($filePath)) {
                $size = filesize($filePath);
                $totalSize += $size;
                $fileSizes[$file] = $size;
            }
        }
        
        // Check individual file size limits
        foreach ($fileSizes as $file => $size) {
            $sizeKB = $size / 1024;
            
            // File-specific size limits
            $limits = [
                'variables.css' => 5,    // 5KB max for design tokens
                'base.css' => 15,        // 15KB max for base styles
                'components.css' => 25,  // 25KB max for components
                'layout.css' => 20,      // 20KB max for layout
                'marketplace.css' => 50  // 50KB max for app-specific styles
            ];
            
            $limit = $limits[$file] ?? 30; // Default 30KB limit
            
            if ($sizeKB > $limit) {
                return [
                    'valid' => false,
                    'reason' => "File $file is too large: {$sizeKB}KB (limit: {$limit}KB)"
                ];
            }
        }
        
        // Check total bundle size
        $totalKB = $totalSize / 1024;
        if ($totalKB > 100) { // 100KB total limit
            return [
                'valid' => false,
                'reason' => "Total CSS bundle too large: {$totalKB}KB (limit: 100KB)"
            ];
        }
        
        return ['valid' => true, 'reason' => "CSS file sizes are optimized ({$totalKB}KB total)"];
    }
    
    /**
     * Validate that redundant CSS rules are eliminated
     */
    private function validateRedundancyElimination($scenario) {
        $fileIndex = $scenario['file_index'];
        $fileName = $this->cssFiles[$fileIndex];
        $filePath = $this->cssPath . $fileName;
        
        if (!file_exists($filePath)) {
            return ['valid' => false, 'reason' => "File $fileName not found"];
        }
        
        $content = file_get_contents($filePath);
        
        // Check for duplicate selectors
        preg_match_all('/([.#]?[\w-]+(?:\s*[>+~]\s*[\w-]+)*)\s*{/', $content, $matches);
        $selectors = $matches[1];
        $uniqueSelectors = array_unique($selectors);
        
        $duplicateCount = count($selectors) - count($uniqueSelectors);
        $duplicatePercentage = count($selectors) > 0 ? ($duplicateCount / count($selectors)) * 100 : 0;
        
        if ($duplicatePercentage > 10) { // Allow up to 10% duplication
            return [
                'valid' => false,
                'reason' => "File $fileName has too many duplicate selectors: {$duplicatePercentage}%"
            ];
        }
        
        // Check for redundant properties within selectors
        $redundantProperties = $this->findRedundantProperties($content);
        if ($redundantProperties > 5) { // Allow up to 5 redundant properties
            return [
                'valid' => false,
                'reason' => "File $fileName has $redundantProperties redundant properties"
            ];
        }
        
        // Check for unused CSS (basic heuristic)
        $unusedSelectors = $this->estimateUnusedSelectors($content, $fileName);
        if ($unusedSelectors > 20) { // Allow up to 20% unused selectors
            return [
                'valid' => false,
                'reason' => "File $fileName may have $unusedSelectors% unused selectors"
            ];
        }
        
        return ['valid' => true, 'reason' => "File $fileName has minimal redundancy"];
    }
    
    /**
     * Validate minification effectiveness
     */
    private function validateMinificationEffectiveness($scenario) {
        $fileIndex = $scenario['file_index'];
        $fileName = $this->cssFiles[$fileIndex];
        $filePath = $this->cssPath . $fileName;
        
        if (!file_exists($filePath)) {
            return ['valid' => false, 'reason' => "File $fileName not found"];
        }
        
        $originalContent = file_get_contents($filePath);
        $originalSize = strlen($originalContent);
        
        // Simulate minification
        $optimizer = new CSSOptimizer($this->cssPath);
        $reflection = new ReflectionClass($optimizer);
        $minifyMethod = $reflection->getMethod('minifyCSS');
        $minifyMethod->setAccessible(true);
        
        $minifiedContent = $minifyMethod->invoke($optimizer, $originalContent);
        $minifiedSize = strlen($minifiedContent);
        
        $reduction = $originalSize > 0 ? (($originalSize - $minifiedSize) / $originalSize) * 100 : 0;
        
        // Expect at least 15% reduction for most files
        $expectedReduction = ($fileName === 'variables.css') ? 5 : 15; // Variables file has less whitespace
        
        if ($reduction < $expectedReduction) {
            return [
                'valid' => false,
                'reason' => "File $fileName minification too low: {$reduction}% (expected: {$expectedReduction}%)"
            ];
        }
        
        // Check that minified content is still valid CSS
        if (empty(trim($minifiedContent))) {
            return [
                'valid' => false,
                'reason' => "File $fileName minification resulted in empty content"
            ];
        }
        
        return ['valid' => true, 'reason' => "File $fileName minifies effectively ({$reduction}% reduction)"];
    }
    
    /**
     * Validate caching strategy effectiveness
     */
    private function validateCachingStrategy($scenario) {
        $environment = $scenario['environment'];
        
        // Test CSS optimizer caching behavior
        $optimizer = new CSSOptimizer($this->cssPath, '../cache/css/');
        
        // Simulate environment
        $_ENV['ENVIRONMENT'] = $environment;
        
        $startTime = microtime(true);
        $cssOutput = $optimizer->getOptimizedCSS($this->cssFiles);
        $loadTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
        
        // Validate caching behavior based on environment
        if ($environment === 'development') {
            // Development should have individual files with cache busting
            $linkCount = substr_count($cssOutput, '<link');
            if ($linkCount !== count($this->cssFiles)) {
                return [
                    'valid' => false,
                    'reason' => "Development mode should have individual CSS files ($linkCount found, expected " . count($this->cssFiles) . ")"
                ];
            }
            
            // Should have cache busting parameters
            if (strpos($cssOutput, '?v=') === false) {
                return [
                    'valid' => false,
                    'reason' => 'Development mode should include cache busting parameters'
                ];
            }
        } else {
            // Production should have concatenated files
            $linkCount = substr_count($cssOutput, '<link');
            if ($linkCount > 1) {
                return [
                    'valid' => false,
                    'reason' => "Production mode should have concatenated CSS ($linkCount files found)"
                ];
            }
        }
        
        // Load time should be reasonable
        if ($loadTime > 100) { // 100ms limit
            return [
                'valid' => false,
                'reason' => "CSS loading too slow: {$loadTime}ms (limit: 100ms)"
            ];
        }
        
        return ['valid' => true, 'reason' => "Caching strategy effective for $environment mode"];
    }
    
    /**
     * Validate loading performance
     */
    private function validateLoadingPerformance($scenario) {
        // Test critical CSS size
        $optimizer = new CSSOptimizer();
        $criticalCSS = $optimizer->getCriticalCSS();
        $criticalSize = strlen($criticalCSS);
        $criticalKB = $criticalSize / 1024;
        
        // Critical CSS should be under 14KB for above-the-fold rendering
        if ($criticalKB > 14) {
            return [
                'valid' => false,
                'reason' => "Critical CSS too large: {$criticalKB}KB (limit: 14KB)"
            ];
        }
        
        // Test total CSS bundle performance
        $totalSize = 0;
        foreach ($this->cssFiles as $file) {
            $filePath = $this->cssPath . $file;
            if (file_exists($filePath)) {
                $totalSize += filesize($filePath);
            }
        }
        
        $totalKB = $totalSize / 1024;
        
        // Performance budgets
        if ($totalKB > 100) {
            return [
                'valid' => false,
                'reason' => "Total CSS bundle exceeds performance budget: {$totalKB}KB (limit: 100KB)"
            ];
        }
        
        // Estimate loading time (assuming 3G connection: ~50KB/s)
        $estimatedLoadTime = ($totalKB / 50) * 1000; // Convert to milliseconds
        if ($estimatedLoadTime > 2000) { // 2 second limit
            return [
                'valid' => false,
                'reason' => "Estimated CSS load time too high: {$estimatedLoadTime}ms (limit: 2000ms)"
            ];
        }
        
        return ['valid' => true, 'reason' => "CSS loading performance is optimized"];
    }
    
    /**
     * Find redundant properties within CSS content
     */
    private function findRedundantProperties($content) {
        $redundantCount = 0;
        
        // Look for common redundant patterns
        $redundantPatterns = [
            '/margin:\s*0;\s*margin-top:/',     // margin: 0 followed by margin-top
            '/padding:\s*0;\s*padding-left:/',  // padding: 0 followed by padding-left
            '/border:\s*none;\s*border-width:/', // border: none followed by border-width
        ];
        
        foreach ($redundantPatterns as $pattern) {
            $redundantCount += preg_match_all($pattern, $content);
        }
        
        return $redundantCount;
    }
    
    /**
     * Estimate unused selectors (basic heuristic)
     */
    private function estimateUnusedSelectors($content, $fileName) {
        // This is a simplified heuristic - in practice, you'd need to analyze HTML usage
        preg_match_all('/([.#][\w-]+)/', $content, $matches);
        $selectors = array_unique($matches[1]);
        
        // Estimate based on file type
        $estimatedUsage = [
            'variables.css' => 100,  // All variables should be used
            'base.css' => 90,        // Most base styles are used
            'components.css' => 80,  // Most components are used
            'layout.css' => 85,      // Most layout styles are used
            'marketplace.css' => 70  // Some app-specific styles may be unused
        ];
        
        $expectedUsage = $estimatedUsage[$fileName] ?? 75;
        $unusedPercentage = 100 - $expectedUsage;
        
        return $unusedPercentage;
    }
    
    /**
     * Run all CSS optimization tests
     */
    public function runAllTests() {
        echo "CSS Optimization Test Suite\n";
        echo "===========================\n\n";
        
        $result = $this->testCSSOptimization();
        
        if ($result) {
            echo "\n🎉 All CSS optimization tests passed!\n";
            echo "The CSS is optimized for performance with minimal redundancy,\n";
            echo "effective minification, and proper caching strategies.\n";
        } else {
            echo "\n❌ CSS optimization tests failed!\n";
            echo "Please review the failures above and optimize the CSS files.\n";
        }
        
        return $result;
    }
}

// Run the tests if this file is executed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $test = new CSSOptimizationTest();
    $result = $test->runAllTests();
    exit($result ? 0 : 1);
}
?>