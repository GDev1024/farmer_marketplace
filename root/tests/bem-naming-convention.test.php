<?php
/**
 * BEM Naming Convention Property Test
 * Validates: Requirements 2.4, 4.3, 7.2
 * 
 * Property 4: BEM Naming Convention
 */

class BEMNamingTest {
    
    private $bemPatterns = [
        'block' => '/^[a-z][a-z0-9]*(-[a-z0-9]+)*$/',
        'element' => '/^[a-z][a-z0-9]*(-[a-z0-9]+)*__[a-z][a-z0-9]*(-[a-z0-9]+)*$/',
        'modifier' => '/^[a-z][a-z0-9]*(-[a-z0-9]+)*--[a-z][a-z0-9]*(-[a-z0-9]+)*$/',
        'elementModifier' => '/^[a-z][a-z0-9]*(-[a-z0-9]+)*__[a-z][a-z0-9]*(-[a-z0-9]+)*--[a-z][a-z0-9]*(-[a-z0-9]+)*$/'
    ];
    
    private function extractClassNames($content) {
        preg_match_all('/class\s*=\s*["\']([^"\']+)["\']/', $content, $matches);
        $classes = [];
        
        foreach ($matches[1] as $classList) {
            $classArray = array_filter(explode(' ', $classList));
            $classes = array_merge($classes, $classArray);
        }
        
        return array_unique($classes);
    }
    
    private function isBEMCompliant($className) {
        // Skip utility classes and framework classes
        $skipPatterns = [
            '/^(btn|alert|card|form|text|bg|border|space|mt|mb|ml|mr|p|m)-/',
            '/^(sr-only|clearfix|mobile-only|desktop-only)$/',
            '/^(container|row|col|grid|flex)/',
            '/^(empty-state|verified-badge|stat-item|cta-buttons|hero-note)$/',
            '/^(search-container|product-grid|product-actions)$/'
        ];
        
        foreach ($skipPatterns as $pattern) {
            if (preg_match($pattern, $className)) {
                return true; // Skip utility classes
            }
        }
        
        // Check if it matches any BEM pattern
        foreach ($this->bemPatterns as $pattern) {
            if (preg_match($pattern, $className)) {
                return true;
            }
        }
        
        return false;
    }
    
    public function validateBEMNaming() {
        $phpFiles = [
            'pages/browse.php',
            'pages/home.php',
            'pages/landing.php',
            'pages/cart.php',
            'pages/checkout.php',
            'pages/orders.php',
            'pages/listing.php',
            'pages/sell.php',
            'pages/profile.php',
            'pages/messages.php',
            'pages/login.php',
            'pages/register.php',
            'pages/payment-success.php',
            'pages/payment-cancel.php',
            'header.php',
            'footer.php'
        ];
        
        $violations = [];
        
        foreach ($phpFiles as $filePath) {
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                $classes = $this->extractClassNames($content);
                
                foreach ($classes as $className) {
                    if (!$this->isBEMCompliant($className)) {
                        $violations[] = [
                            'file' => $filePath,
                            'className' => $className,
                            'reason' => 'Does not follow BEM naming convention'
                        ];
                    }
                }
            }
        }
        
        return $violations;
    }
    
    public function testBEMPatterns() {
        $testCases = [
            // Valid BEM patterns
            ['product-card', true, 'block'],
            ['nav-menu', true, 'block'],
            ['search-form', true, 'block'],
            ['product-card__image', true, 'element'],
            ['nav-menu__item', true, 'element'],
            ['product-card__header', true, 'element'],
            ['btn--primary', true, 'modifier'],
            ['card--featured', true, 'modifier'],
            ['product-card__image--large', true, 'elementModifier'],
            
            // Invalid BEM patterns
            ['ProductCard', false, 'invalid case'],
            ['product_card', false, 'invalid separator'],
            ['product-card___image', false, 'invalid element separator'],
            ['product-card-image', false, 'missing element separator'],
            ['product-card__image__title', false, 'nested elements']
        ];
        
        $failures = [];
        
        foreach ($testCases as [$className, $expected, $description]) {
            $isValid = false;
            foreach ($this->bemPatterns as $pattern) {
                if (preg_match($pattern, $className)) {
                    $isValid = true;
                    break;
                }
            }
            
            if ($isValid !== $expected) {
                $failures[] = "Failed: '$className' ($description) - Expected " . 
                             ($expected ? 'valid' : 'invalid') . ", got " . 
                             ($isValid ? 'valid' : 'invalid');
            }
        }
        
        return $failures;
    }
    
    public function runTests() {
        echo "Running BEM Naming Convention Tests...\n\n";
        
        // Test 1: Validate BEM patterns
        echo "Test 1: BEM Pattern Validation\n";
        $patternFailures = $this->testBEMPatterns();
        
        if (empty($patternFailures)) {
            echo "✅ All BEM patterns validated correctly\n";
        } else {
            echo "❌ BEM pattern validation failures:\n";
            foreach ($patternFailures as $failure) {
                echo "   $failure\n";
            }
        }
        
        // Test 2: Validate actual file class names
        echo "\nTest 2: File Class Name Validation\n";
        $violations = $this->validateBEMNaming();
        
        if (empty($violations)) {
            echo "✅ All component class names follow BEM convention\n";
        } else {
            echo "❌ BEM naming violations found:\n";
            foreach ($violations as $violation) {
                echo "   {$violation['file']}: \"{$violation['className']}\" - {$violation['reason']}\n";
            }
        }
        
        // Summary
        $totalFailures = count($patternFailures) + count($violations);
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "BEM Naming Convention Test Summary\n";
        echo "Total failures: $totalFailures\n";
        
        if ($totalFailures === 0) {
            echo "🎉 All tests passed! BEM naming convention is properly implemented.\n";
            return true;
        } else {
            echo "⚠️  Some tests failed. Please review and fix the violations above.\n";
            return false;
        }
    }
}

// Run the tests
$test = new BEMNamingTest();
$success = $test->runTests();

// Exit with appropriate code
exit($success ? 0 : 1);
?>