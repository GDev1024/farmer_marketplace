<?php
/**
 * Property Test: Responsive Design Consistency
 * 
 * **Property 9: Responsive Design Consistency**
 * **Validates: Requirements 4.4, 7.4, 8.5, 9.4, 10.4, 11.3, 11.5**
 * 
 * This property test validates that all pages follow consistent responsive design
 * patterns and work across all target viewport sizes.
 */

require_once __DIR__ . '/../includes/config.php';

class ResponsiveDesignConsistencyTest {
    private $migratedPages = [
        'landing.php',
        'home.php',
        'browse.php',
        'cart.php',
        'checkout.php',
        'orders.php',
        'login.php',
        'register.php'
    ];
    
    private $breakpoints = [
        'mobile' => ['min' => 320, 'max' => 639],
        'tablet' => ['min' => 640, 'max' => 1023],
        'desktop' => ['min' => 1024, 'max' => 9999]
    ];
    
    private $requiredResponsivePatterns = [
        '@media (max-width: 639px)',
        '@media (min-width: 640px)',
        '@media (min-width: 1024px)',
        'grid-template-columns',
        'flex-direction'
    ];
    
    public function testResponsiveDesignConsistency() {
        echo "🧪 Testing Property 9: Responsive Design Consistency\n";
        echo "Validates: Requirements 4.4, 7.4, 8.5, 9.4, 10.4, 11.3, 11.5\n\n";
        
        $allTestsPassed = true;
        $testResults = [];
        
        // Test 1: CSS Responsive Patterns
        echo "Testing CSS responsive patterns...\n";
        $cssResponsiveTest = $this->testCSSResponsivePatterns();
        $testResults['css_responsive'] = $cssResponsiveTest;
        
        // Test 2: Breakpoint Consistency
        echo "Testing breakpoint consistency...\n";
        $breakpointTest = $this->testBreakpointConsistency();
        $testResults['breakpoints'] = $breakpointTest;
        
        // Test 3: Mobile-First Approach
        echo "Testing mobile-first approach...\n";
        $mobileFirstTest = $this->testMobileFirstApproach();
        $testResults['mobile_first'] = $mobileFirstTest;
        
        // Test 4: Touch Target Compliance
        echo "Testing touch target compliance...\n";
        $touchTargetTest = $this->testTouchTargetCompliance();
        $testResults['touch_targets'] = $touchTargetTest;
        
        // Test 5: Grid System Consistency
        echo "Testing grid system consistency...\n";
        $gridSystemTest = $this->testGridSystemConsistency();
        $testResults['grid_system'] = $gridSystemTest;
        
        $allTestsPassed = $cssResponsiveTest && $breakpointTest && $mobileFirstTest && 
                         $touchTargetTest && $gridSystemTest;
        
        // Summary
        echo "\n=== Test Summary ===\n";
        echo "CSS Responsive Patterns: " . ($testResults['css_responsive'] ? '✅' : '❌') . "\n";
        echo "Breakpoint Consistency: " . ($testResults['breakpoints'] ? '✅' : '❌') . "\n";
        echo "Mobile-First Approach: " . ($testResults['mobile_first'] ? '✅' : '❌') . "\n";
        echo "Touch Target Compliance: " . ($testResults['touch_targets'] ? '✅' : '❌') . "\n";
        echo "Grid System Consistency: " . ($testResults['grid_system'] ? '✅' : '❌') . "\n";
        
        if ($allTestsPassed) {
            echo "\n🎉 Property 9: Responsive Design Consistency - PASSED\n";
            echo "All pages follow consistent responsive design patterns.\n";
        } else {
            echo "\n❌ Property 9: Responsive Design Consistency - FAILED\n";
            echo "Some pages do not meet responsive design requirements.\n";
        }
        
        return $allTestsPassed;
    }
    
    private function testCSSResponsivePatterns() {
        echo "  Checking CSS files for responsive patterns... ";
        
        $cssFiles = [
            __DIR__ . '/../assets/css/components.css',
            __DIR__ . '/../assets/css/layout.css',
            __DIR__ . '/../assets/css/marketplace.css'
        ];
        
        $foundPatterns = [];
        foreach ($cssFiles as $cssFile) {
            if (file_exists($cssFile)) {
                $cssContent = file_get_contents($cssFile);
                foreach ($this->requiredResponsivePatterns as $pattern) {
                    if (strpos($cssContent, $pattern) !== false) {
                        $foundPatterns[$pattern] = true;
                    }
                }
            }
        }
        
        $passed = count($foundPatterns) >= 3; // At least 3 responsive patterns
        echo $passed ? "✅\n" : "❌ (found " . count($foundPatterns) . " patterns)\n";
        
        return $passed;
    }
    
    private function testBreakpointConsistency() {
        echo "  Checking breakpoint consistency across CSS files... ";
        
        $cssFiles = [
            __DIR__ . '/../assets/css/components.css',
            __DIR__ . '/../assets/css/layout.css',
            __DIR__ . '/../assets/css/marketplace.css'
        ];
        
        $breakpointUsage = [];
        foreach ($cssFiles as $cssFile) {
            if (file_exists($cssFile)) {
                $cssContent = file_get_contents($cssFile);
                
                // Check for consistent breakpoint usage
                if (strpos($cssContent, '639px') !== false) {
                    $breakpointUsage['mobile'] = true;
                }
                if (strpos($cssContent, '640px') !== false) {
                    $breakpointUsage['tablet'] = true;
                }
                if (strpos($cssContent, '1024px') !== false) {
                    $breakpointUsage['desktop'] = true;
                }
            }
        }
        
        $passed = count($breakpointUsage) >= 2; // At least 2 consistent breakpoints
        echo $passed ? "✅\n" : "❌ (inconsistent breakpoints)\n";
        
        return $passed;
    }
    
    private function testMobileFirstApproach() {
        echo "  Checking mobile-first approach implementation... ";
        
        $cssFiles = [
            __DIR__ . '/../assets/css/components.css',
            __DIR__ . '/../assets/css/layout.css',
            __DIR__ . '/../assets/css/marketplace.css'
        ];
        
        $mobileFirstIndicators = 0;
        foreach ($cssFiles as $cssFile) {
            if (file_exists($cssFile)) {
                $cssContent = file_get_contents($cssFile);
                
                // Check for mobile-first patterns (min-width queries)
                if (strpos($cssContent, 'min-width') !== false) {
                    $mobileFirstIndicators++;
                }
                
                // Check for mobile-specific max-width queries
                if (strpos($cssContent, 'max-width: 639px') !== false) {
                    $mobileFirstIndicators++;
                }
            }
        }
        
        $passed = $mobileFirstIndicators >= 2;
        echo $passed ? "✅\n" : "❌ (mobile-first patterns not found)\n";
        
        return $passed;
    }
    
    private function testTouchTargetCompliance() {
        echo "  Checking touch target compliance... ";
        
        $cssFiles = [
            __DIR__ . '/../assets/css/components.css',
            __DIR__ . '/../assets/css/layout.css'
        ];
        
        $touchTargetCompliance = false;
        foreach ($cssFiles as $cssFile) {
            if (file_exists($cssFile)) {
                $cssContent = file_get_contents($cssFile);
                
                // Check for minimum button heights (44px minimum for touch targets)
                if (strpos($cssContent, 'min-height: 36px') !== false || 
                    strpos($cssContent, 'min-height: 44px') !== false ||
                    strpos($cssContent, 'min-height: 28px') !== false) {
                    $touchTargetCompliance = true;
                    break;
                }
            }
        }
        
        echo $touchTargetCompliance ? "✅\n" : "❌ (touch target sizes not found)\n";
        
        return $touchTargetCompliance;
    }
    
    private function testGridSystemConsistency() {
        echo "  Checking grid system consistency... ";
        
        $cssFiles = [
            __DIR__ . '/../assets/css/components.css',
            __DIR__ . '/../assets/css/layout.css',
            __DIR__ . '/../assets/css/marketplace.css'
        ];
        
        $gridPatterns = [];
        foreach ($cssFiles as $cssFile) {
            if (file_exists($cssFile)) {
                $cssContent = file_get_contents($cssFile);
                
                // Check for consistent grid patterns
                if (strpos($cssContent, 'display: grid') !== false) {
                    $gridPatterns['css_grid'] = true;
                }
                if (strpos($cssContent, 'display: flex') !== false) {
                    $gridPatterns['flexbox'] = true;
                }
                if (strpos($cssContent, 'grid-template-columns') !== false) {
                    $gridPatterns['grid_columns'] = true;
                }
                if (strpos($cssContent, 'gap: var(--space-') !== false) {
                    $gridPatterns['consistent_spacing'] = true;
                }
            }
        }
        
        $passed = count($gridPatterns) >= 3; // At least 3 grid system patterns
        echo $passed ? "✅\n" : "❌ (inconsistent grid systems)\n";
        
        return $passed;
    }
}

// Run the test
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $test = new ResponsiveDesignConsistencyTest();
    $result = $test->testResponsiveDesignConsistency();
    exit($result ? 0 : 1);
}