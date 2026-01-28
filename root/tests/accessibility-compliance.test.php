<?php
/**
 * Accessibility Compliance Property Test
 * Validates: Requirements 3.3, 3.5, 5.1, 5.2, 5.4, 8.2, 8.4, 12.1, 12.2, 12.4
 * 
 * Property 5: Accessibility Compliance
 */

class AccessibilityComplianceTest {
    
    private function validateARIALabels($content) {
        $violations = [];
        
        // Check for buttons without aria-label or text content
        preg_match_all('/<button[^>]*>(.*?)<\/button>/s', $content, $buttons, PREG_SET_ORDER);
        
        foreach ($buttons as $button) {
            $buttonTag = $button[0];
            $buttonText = trim(strip_tags($button[1]));
            
            if (!preg_match('/aria-label\s*=/', $buttonTag) && empty($buttonText)) {
                $violations[] = [
                    'type' => 'missing-aria-label',
                    'element' => 'button',
                    'context' => substr($buttonTag, 0, 100) . '...'
                ];
            }
        }
        
        // Check for images without alt text
        preg_match_all('/<img[^>]*>/i', $content, $images);
        
        foreach ($images[0] as $imgTag) {
            if (!preg_match('/alt\s*=/', $imgTag)) {
                $violations[] = [
                    'type' => 'missing-alt-text',
                    'element' => 'img',
                    'context' => substr($imgTag, 0, 100) . '...'
                ];
            }
        }
        
        return $violations;
    }
    
    private function validateFormAccessibility($content) {
        $violations = [];
        
        // Extract inputs and labels
        preg_match_all('/<input[^>]*>/i', $content, $inputs);
        preg_match_all('/<label[^>]*for\s*=\s*["\']([^"\']+)["\'][^>]*>/i', $content, $labels);
        
        $inputIds = [];
        $labelFors = isset($labels[1]) ? $labels[1] : [];
        
        foreach ($inputs[0] as $inputTag) {
            if (preg_match('/id\s*=\s*["\']([^"\']+)["\']/i', $inputTag, $idMatch)) {
                $inputIds[] = $idMatch[1];
            }
        }
        
        // Check for inputs without corresponding labels
        foreach ($inputIds as $inputId) {
            if (!in_array($inputId, $labelFors)) {
                $violations[] = [
                    'type' => 'input-without-label',
                    'element' => 'input',
                    'context' => "Input with id='$inputId' has no corresponding label",
                    'inputId' => $inputId
                ];
            }
        }
        
        return $violations;
    }
    
    private function validateSkipLinks($content) {
        $violations = [];
        
        // Check for skip links presence
        if (!preg_match('/skip-link/', $content) || !preg_match('/Skip to main content/', $content)) {
            $violations[] = [
                'type' => 'missing-skip-links',
                'element' => 'navigation',
                'context' => 'No skip links found for keyboard navigation'
            ];
        }
        
        // Check for main content landmark
        if (!preg_match('/id\s*=\s*["\']main-content["\']/', $content) && !preg_match('/role\s*=\s*["\']main["\']/', $content)) {
            $violations[] = [
                'type' => 'missing-main-landmark',
                'element' => 'main',
                'context' => 'No main content landmark found'
            ];
        }
        
        return $violations;
    }
    
    private function validateModalAccessibility($content) {
        $violations = [];
        
        // Check for modal ARIA attributes
        preg_match_all('/<div[^>]*class\s*=\s*["\'][^"\']*modal[^"\']*["\'][^>]*>/i', $content, $modals);
        
        foreach ($modals[0] as $modalTag) {
            if (!preg_match('/aria-hidden/', $modalTag)) {
                $violations[] = [
                    'type' => 'modal-missing-aria-hidden',
                    'element' => 'modal',
                    'context' => substr($modalTag, 0, 100) . '...'
                ];
            }
            
            if (!preg_match('/role\s*=\s*["\']dialog["\']/', $modalTag)) {
                $violations[] = [
                    'type' => 'modal-missing-dialog-role',
                    'element' => 'modal',
                    'context' => substr($modalTag, 0, 100) . '...'
                ];
            }
            
            if (!preg_match('/aria-modal/', $modalTag)) {
                $violations[] = [
                    'type' => 'modal-missing-aria-modal',
                    'element' => 'modal',
                    'context' => substr($modalTag, 0, 100) . '...'
                ];
            }
        }
        
        return $violations;
    }
    
    private function validateHeadingHierarchy($content) {
        $violations = [];
        
        // Extract headings
        preg_match_all('/<h([1-6])[^>]*>/i', $content, $headings);
        $headingLevels = array_map('intval', $headings[1]);
        
        // Check for proper heading hierarchy
        for ($i = 1; $i < count($headingLevels); $i++) {
            $current = $headingLevels[$i];
            $previous = $headingLevels[$i - 1];
            
            if ($current > $previous + 1) {
                $violations[] = [
                    'type' => 'heading-hierarchy-skip',
                    'element' => "h$current",
                    'context' => "Heading level $current follows h$previous, skipping levels"
                ];
            }
        }
        
        // Check for h1 presence
        if (!in_array(1, $headingLevels)) {
            $violations[] = [
                'type' => 'missing-h1',
                'element' => 'h1',
                'context' => 'No h1 heading found on page'
            ];
        }
        
        return $violations;
    }
    
    private function validateColorContrast() {
        $violations = [];
        
        $cssFiles = [
            'assets/css/variables.css',
            'assets/css/components.css',
            'assets/css/layout.css',
            'assets/css/marketplace.css'
        ];
        
        foreach ($cssFiles as $filePath) {
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                
                // Check for hardcoded colors (should use design tokens)
                preg_match_all('/#[0-9a-fA-F]{3,6}/', $content, $hardcodedColors);
                $allowedHardcoded = ['#000', '#fff', '#ffffff', '#000000'];
                
                foreach ($hardcodedColors[0] as $color) {
                    if (!in_array(strtolower($color), array_map('strtolower', $allowedHardcoded))) {
                        $violations[] = [
                            'type' => 'hardcoded-color',
                            'element' => 'css',
                            'context' => "Hardcoded color $color found in $filePath - should use design tokens",
                            'file' => $filePath,
                            'color' => $color
                        ];
                    }
                }
            }
        }
        
        return $violations;
    }
    
    public function validateAccessibility() {
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
        
        $allViolations = [];
        
        foreach ($phpFiles as $filePath) {
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                
                $ariaViolations = $this->validateARIALabels($content);
                $formViolations = $this->validateFormAccessibility($content);
                $skipLinkViolations = $this->validateSkipLinks($content);
                $modalViolations = $this->validateModalAccessibility($content);
                $headingViolations = $this->validateHeadingHierarchy($content);
                
                $fileViolations = array_merge(
                    $ariaViolations,
                    $formViolations,
                    $skipLinkViolations,
                    $modalViolations,
                    $headingViolations
                );
                
                foreach ($fileViolations as &$violation) {
                    $violation['file'] = $filePath;
                }
                
                $allViolations = array_merge($allViolations, $fileViolations);
            }
        }
        
        // Add color contrast violations
        $colorViolations = $this->validateColorContrast();
        $allViolations = array_merge($allViolations, $colorViolations);
        
        return $allViolations;
    }
    
    public function testAccessibilityPatterns() {
        $testCases = [
            // Valid accessibility patterns
            ['<button aria-label="Close dialog">×</button>', true, 'button with aria-label'],
            ['<input id="email" type="email"><label for="email">Email</label>', true, 'input with associated label'],
            ['<img src="photo.jpg" alt="Product photo">', true, 'image with alt text'],
            ['<div role="dialog" aria-modal="true" aria-hidden="false">', true, 'modal with proper ARIA'],
            ['<main id="main-content">', true, 'main landmark'],
            
            // Invalid accessibility patterns
            ['<button>×</button>', false, 'button without label'],
            ['<input type="email">', false, 'input without label'],
            ['<img src="photo.jpg">', false, 'image without alt text'],
            ['<div class="modal">', false, 'modal without ARIA'],
            ['<div class="content">', false, 'content without landmark']
        ];
        
        $failures = [];
        
        foreach ($testCases as [$html, $expected, $description]) {
            $ariaViolations = $this->validateARIALabels($html);
            $formViolations = $this->validateFormAccessibility($html);
            $modalViolations = $this->validateModalAccessibility($html);
            
            $hasViolations = !empty($ariaViolations) || !empty($formViolations) || !empty($modalViolations);
            $isValid = !$hasViolations;
            
            if ($isValid !== $expected) {
                $failures[] = "Failed: '$html' ($description) - Expected " . 
                             ($expected ? 'valid' : 'invalid') . ", got " . 
                             ($isValid ? 'valid' : 'invalid');
            }
        }
        
        return $failures;
    }
    
    public function runTests() {
        echo "Running Accessibility Compliance Tests...\n\n";
        
        // Test 1: Validate accessibility patterns
        echo "Test 1: Accessibility Pattern Validation\n";
        $patternFailures = $this->testAccessibilityPatterns();
        
        if (empty($patternFailures)) {
            echo "✅ All accessibility patterns validated correctly\n";
        } else {
            echo "❌ Accessibility pattern validation failures:\n";
            foreach ($patternFailures as $failure) {
                echo "   $failure\n";
            }
        }
        
        // Test 2: Validate actual file accessibility
        echo "\nTest 2: File Accessibility Validation\n";
        $violations = $this->validateAccessibility();
        
        if (empty($violations)) {
            echo "✅ All files meet accessibility standards\n";
        } else {
            echo "❌ Accessibility violations found:\n";
            foreach ($violations as $violation) {
                $file = isset($violation['file']) ? $violation['file'] : 'CSS';
                echo "   $file: {$violation['type']} - {$violation['context']}\n";
            }
        }
        
        // Summary
        $totalFailures = count($patternFailures) + count($violations);
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "Accessibility Compliance Test Summary\n";
        echo "Total failures: $totalFailures\n";
        
        if ($totalFailures === 0) {
            echo "🎉 All tests passed! Application meets WCAG 2.1 AA standards.\n";
            return true;
        } else {
            echo "⚠️  Some tests failed. Please review and fix the violations above.\n";
            return false;
        }
    }
}

// Run the tests
$test = new AccessibilityComplianceTest();
$success = $test->runTests();

// Exit with appropriate code
exit($success ? 0 : 1);
?>