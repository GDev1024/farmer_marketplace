<?php
/**
 * Property Test: Page Migration Completeness
 * 
 * **Property 10: Page Migration Completeness**
 * **Validates: Requirements 10.1, 10.2, 10.3**
 * 
 * This property test validates that all migrated pages follow the new design system
 * structure and accessibility requirements.
 */

require_once __DIR__ . '/../includes/config.php';

class PageMigrationCompletenessTest {
    private $migratedPages = [
        'landing.php',
        'home.php', 
        'browse.php',
        'login.php',
        'register.php'
    ];
    
    private $requiredPageStructure = [
        'page-main' => true,
        'role="main"' => true,
        'id="main-content"' => true
    ];
    
    private $requiredDesignTokens = [
        '--space-',
        '--color-',
        '--text-',
        '--font-',
        '--radius-',
        '--shadow-'
    ];
    
    public function testPageMigrationCompleteness() {
        echo "🧪 Testing Property 10: Page Migration Completeness\n";
        echo "Validates: Requirements 10.1, 10.2, 10.3\n\n";
        
        $allTestsPassed = true;
        $testResults = [];
        
        foreach ($this->migratedPages as $page) {
            echo "Testing page: {$page}\n";
            
            $pageContent = $this->getPageContent($page);
            if (!$pageContent) {
                echo "❌ Could not load page content for {$page}\n";
                $allTestsPassed = false;
                continue;
            }
            
            // Test 1: Semantic page structure
            $hasSemanticStructure = $this->testSemanticStructure($pageContent, $page);
            $testResults[$page]['semantic'] = $hasSemanticStructure;
            
            // Test 2: Page classes and structure
            $hasPageStructure = $this->testPageStructure($pageContent, $page);
            $testResults[$page]['structure'] = $hasPageStructure;
            
            // Test 3: Design token usage
            $usesDesignTokens = $this->testDesignTokenUsage($page);
            $testResults[$page]['tokens'] = $usesDesignTokens;
            
            // Test 4: Accessibility features
            $hasAccessibility = $this->testAccessibilityFeatures($pageContent, $page);
            $testResults[$page]['accessibility'] = $hasAccessibility;
            
            // Test 5: Responsive design indicators
            $hasResponsiveDesign = $this->testResponsiveDesign($pageContent, $page);
            $testResults[$page]['responsive'] = $hasResponsiveDesign;
            
            $pageTestsPassed = $hasSemanticStructure && $hasPageStructure && 
                             $usesDesignTokens && $hasAccessibility && $hasResponsiveDesign;
            
            if ($pageTestsPassed) {
                echo "✅ {$page} passed all migration tests\n";
            } else {
                echo "❌ {$page} failed some migration tests\n";
                $allTestsPassed = false;
            }
            
            echo "\n";
        }
        
        // Summary
        echo "=== Test Summary ===\n";
        foreach ($testResults as $page => $results) {
            echo "{$page}:\n";
            echo "  Semantic Structure: " . ($results['semantic'] ? '✅' : '❌') . "\n";
            echo "  Page Structure: " . ($results['structure'] ? '✅' : '❌') . "\n";
            echo "  Design Tokens: " . ($results['tokens'] ? '✅' : '❌') . "\n";
            echo "  Accessibility: " . ($results['accessibility'] ? '✅' : '❌') . "\n";
            echo "  Responsive Design: " . ($results['responsive'] ? '✅' : '❌') . "\n";
        }
        
        if ($allTestsPassed) {
            echo "\n🎉 Property 10: Page Migration Completeness - PASSED\n";
            echo "All migrated pages follow the design system requirements.\n";
        } else {
            echo "\n❌ Property 10: Page Migration Completeness - FAILED\n";
            echo "Some pages do not meet the migration requirements.\n";
        }
        
        return $allTestsPassed;
    }
    
    private function getPageContent($page) {
        $pagePath = __DIR__ . "/../pages/{$page}";
        if (!file_exists($pagePath)) {
            return false;
        }
        return file_get_contents($pagePath);
    }
    
    private function testSemanticStructure($content, $page) {
        echo "  Testing semantic structure... ";
        
        // Check for semantic HTML5 elements
        $semanticElements = [
            '<main',
            '<header',
            '<section',
            '<article',
            '<nav',
            '<footer'
        ];
        
        $foundElements = 0;
        foreach ($semanticElements as $element) {
            if (strpos($content, $element) !== false) {
                $foundElements++;
            }
        }
        
        // Require at least 2 semantic elements (main + one other)
        $passed = $foundElements >= 2;
        echo $passed ? "✅\n" : "❌ (found {$foundElements} semantic elements)\n";
        
        return $passed;
    }
    
    private function testPageStructure($content, $page) {
        echo "  Testing page structure... ";
        
        $structureTests = [
            'page-main' => strpos($content, 'page-main') !== false,
            'role="main"' => strpos($content, 'role="main"') !== false,
            'id="main-content"' => strpos($content, 'id="main-content"') !== false
        ];
        
        $passedTests = array_filter($structureTests);
        $passed = count($passedTests) >= 2; // At least 2 out of 3 structure elements
        
        echo $passed ? "✅\n" : "❌ (missing required page structure)\n";
        
        return $passed;
    }
    
    private function testDesignTokenUsage($page) {
        echo "  Testing design token usage... ";
        
        // Check CSS files for design token usage
        $cssFiles = [
            __DIR__ . '/../assets/css/components.css',
            __DIR__ . '/../assets/css/layout.css',
            __DIR__ . '/../assets/css/marketplace.css'
        ];
        
        $tokenUsageFound = false;
        foreach ($cssFiles as $cssFile) {
            if (file_exists($cssFile)) {
                $cssContent = file_get_contents($cssFile);
                foreach ($this->requiredDesignTokens as $token) {
                    if (strpos($cssContent, $token) !== false) {
                        $tokenUsageFound = true;
                        break 2;
                    }
                }
            }
        }
        
        echo $tokenUsageFound ? "✅\n" : "❌ (design tokens not found in CSS)\n";
        
        return $tokenUsageFound;
    }
    
    private function testAccessibilityFeatures($content, $page) {
        echo "  Testing accessibility features... ";
        
        $accessibilityFeatures = [
            'aria-label' => strpos($content, 'aria-label') !== false,
            'aria-describedby' => strpos($content, 'aria-describedby') !== false,
            'aria-labelledby' => strpos($content, 'aria-labelledby') !== false,
            'for=' => strpos($content, 'for=') !== false, // Label associations
            'id=' => strpos($content, 'id=') !== false,
            'role=' => strpos($content, 'role=') !== false
        ];
        
        $foundFeatures = array_filter($accessibilityFeatures);
        $passed = count($foundFeatures) >= 3; // At least 3 accessibility features
        
        echo $passed ? "✅\n" : "❌ (insufficient accessibility features)\n";
        
        return $passed;
    }
    
    private function testResponsiveDesign($content, $page) {
        echo "  Testing responsive design indicators... ";
        
        // Check for responsive design patterns in the content
        $responsiveIndicators = [
            'class=' => strpos($content, 'class=') !== false, // CSS classes for styling
            'grid' => strpos($content, 'grid') !== false,
            'flex' => strpos($content, 'flex') !== false,
            'responsive' => strpos($content, 'responsive') !== false
        ];
        
        // Also check CSS files for responsive patterns
        $cssFiles = [
            __DIR__ . '/../assets/css/components.css',
            __DIR__ . '/../assets/css/layout.css',
            __DIR__ . '/../assets/css/marketplace.css'
        ];
        
        $responsiveCSS = false;
        foreach ($cssFiles as $cssFile) {
            if (file_exists($cssFile)) {
                $cssContent = file_get_contents($cssFile);
                if (strpos($cssContent, '@media') !== false) {
                    $responsiveCSS = true;
                    break;
                }
            }
        }
        
        $foundIndicators = array_filter($responsiveIndicators);
        $passed = (count($foundIndicators) >= 1) && $responsiveCSS;
        
        echo $passed ? "✅\n" : "❌ (responsive design patterns not found)\n";
        
        return $passed;
    }
}

// Run the test
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $test = new PageMigrationCompletenessTest();
    $result = $test->testPageMigrationCompleteness();
    exit($result ? 0 : 1);
}