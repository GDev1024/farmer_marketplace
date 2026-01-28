<?php
/**
 * Form Accessibility and Validation Property Test
 * Validates: Requirements 5.1, 5.2, 5.5
 * 
 * Property 6: Form Accessibility and Validation
 */

class FormAccessibilityValidationTest {
    
    private function validateLabelAssociation($content) {
        $violations = [];
        
        // Extract all inputs and their IDs
        preg_match_all('/<input[^>]*>/i', $content, $inputs);
        preg_match_all('/<label[^>]*for\s*=\s*["\']([^"\']+)["\'][^>]*>/i', $content, $labels);
        
        $inputIds = [];
        $labelFors = isset($labels[1]) ? $labels[1] : [];
        
        foreach ($inputs[0] as $inputTag) {
            if (preg_match('/id\s*=\s*["\']([^"\']+)["\']/i', $inputTag, $idMatch)) {
                $typeMatch = [];
                preg_match('/type\s*=\s*["\']([^"\']+)["\']/i', $inputTag, $typeMatch);
                $type = isset($typeMatch[1]) ? $typeMatch[1] : 'text';
                
                $inputIds[] = [
                    'id' => $idMatch[1],
                    'type' => $type,
                    'tag' => $inputTag
                ];
            }
        }
        
        // Check for inputs without labels (excluding hidden inputs)
        foreach ($inputIds as $input) {
            if ($input['type'] !== 'hidden' && !in_array($input['id'], $labelFors)) {
                $violations[] = [
                    'type' => 'input-without-label',
                    'element' => 'input',
                    'context' => "Input with id='{$input['id']}' and type='{$input['type']}' has no associated label",
                    'inputId' => $input['id'],
                    'inputType' => $input['type']
                ];
            }
        }
        
        return $violations;
    }
    
    private function validateAriaDescribedBy($content) {
        $violations = [];
        
        // Find inputs with aria-describedby
        preg_match_all('/<input[^>]*aria-describedby\s*=\s*["\']([^"\']+)["\'][^>]*>/i', $content, $describedByMatches);
        preg_match_all('/<[^>]*id\s*=\s*["\']([^"\']+)["\'][^>]*>/i', $content, $idMatches);
        
        $describedByIds = [];
        $existingIds = isset($idMatches[1]) ? $idMatches[1] : [];
        
        foreach ($describedByMatches[1] as $describedBy) {
            $ids = preg_split('/\s+/', trim($describedBy));
            $describedByIds = array_merge($describedByIds, $ids);
        }
        
        // Check if all aria-describedby IDs exist
        foreach ($describedByIds as $id) {
            if (!in_array($id, $existingIds)) {
                $violations[] = [
                    'type' => 'missing-describedby-target',
                    'element' => 'aria-describedby',
                    'context' => "aria-describedby references non-existent ID: $id",
                    'missingId' => $id
                ];
            }
        }
        
        return $violations;
    }
    
    private function validateRequiredFields($content) {
        $violations = [];
        
        // Find required inputs
        preg_match_all('/<input[^>]*required[^>]*>/i', $content, $requiredInputs);
        
        foreach ($requiredInputs[0] as $inputTag) {
            if (preg_match('/id\s*=\s*["\']([^"\']+)["\']/i', $inputTag, $idMatch)) {
                $inputId = $idMatch[1];
                
                // Check if there's an associated error element
                if (!preg_match("/id\\s*=\\s*[\"']{$inputId}-error[\"']/i", $content)) {
                    $violations[] = [
                        'type' => 'required-field-without-error-element',
                        'element' => 'input',
                        'context' => "Required input with id='$inputId' has no associated error element",
                        'inputId' => $inputId
                    ];
                }
            }
        }
        
        return $violations;
    }
    
    private function validateFormRoles($content) {
        $violations = [];
        
        // Check for forms without proper roles
        preg_match_all('/<form[^>]*>/i', $content, $forms);
        
        foreach ($forms[0] as $formTag) {
            if (!preg_match('/role\s*=/', $formTag) && !preg_match('/aria-labelledby\s*=/', $formTag)) {
                $violations[] = [
                    'type' => 'form-without-accessibility-attributes',
                    'element' => 'form',
                    'context' => 'Form element lacks role or aria-labelledby attribute',
                    'formTag' => substr($formTag, 0, 100) . '...'
                ];
            }
        }
        
        return $violations;
    }
    
    private function validatePasswordToggles($content) {
        $violations = [];
        
        // Find password toggle buttons
        preg_match_all('/<button[^>]*password-toggle[^>]*>/i', $content, $toggles);
        
        foreach ($toggles[0] as $buttonTag) {
            if (!preg_match('/aria-label\s*=/', $buttonTag)) {
                $violations[] = [
                    'type' => 'password-toggle-without-aria-label',
                    'element' => 'button',
                    'context' => 'Password toggle button lacks aria-label',
                    'buttonTag' => substr($buttonTag, 0, 100) . '...'
                ];
            }
            
            if (!preg_match('/type\s*=\s*["\']button["\']/', $buttonTag)) {
                $violations[] = [
                    'type' => 'password-toggle-wrong-type',
                    'element' => 'button',
                    'context' => 'Password toggle should have type="button"',
                    'buttonTag' => substr($buttonTag, 0, 100) . '...'
                ];
            }
        }
        
        return $violations;
    }
    
    private function validateCheckboxAccessibility($content) {
        $violations = [];
        
        // Find checkbox inputs
        preg_match_all('/<input[^>]*type\s*=\s*["\']checkbox["\'][^>]*>/i', $content, $checkboxes);
        
        foreach ($checkboxes[0] as $inputTag) {
            if (preg_match('/id\s*=\s*["\']([^"\']+)["\']/i', $inputTag, $idMatch)) {
                $inputId = $idMatch[1];
                
                if (!preg_match("/<label[^>]*for\\s*=\\s*[\"']{$inputId}[\"'][^>]*>/i", $content)) {
                    $violations[] = [
                        'type' => 'checkbox-without-label',
                        'element' => 'input[type="checkbox"]',
                        'context' => "Checkbox with id='$inputId' has no associated label",
                        'inputId' => $inputId
                    ];
                }
            }
        }
        
        return $violations;
    }
    
    public function validateFormAccessibility() {
        $formPages = [
            'pages/login.php',
            'pages/register.php',
            'pages/listing.php',
            'pages/checkout.php',
            'pages/profile.php',
            'pages/sell.php'
        ];
        
        $allViolations = [];
        
        foreach ($formPages as $filePath) {
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                
                $labelViolations = $this->validateLabelAssociation($content);
                $ariaViolations = $this->validateAriaDescribedBy($content);
                $requiredViolations = $this->validateRequiredFields($content);
                $roleViolations = $this->validateFormRoles($content);
                $toggleViolations = $this->validatePasswordToggles($content);
                $checkboxViolations = $this->validateCheckboxAccessibility($content);
                
                $fileViolations = array_merge(
                    $labelViolations,
                    $ariaViolations,
                    $requiredViolations,
                    $roleViolations,
                    $toggleViolations,
                    $checkboxViolations
                );
                
                foreach ($fileViolations as &$violation) {
                    $violation['file'] = $filePath;
                }
                
                $allViolations = array_merge($allViolations, $fileViolations);
            }
        }
        
        return $allViolations;
    }
    
    public function testFormAccessibilityPatterns() {
        $testCases = [
            // Valid form accessibility patterns
            ['<label for="email">Email</label><input type="email" id="email" name="email">', true, 'input with associated label'],
            ['<input type="text" id="name" aria-describedby="name-help"><div id="name-help">Help text</div>', true, 'input with aria-describedby'],
            ['<input type="password" required id="password"><div id="password-error" role="alert"></div>', true, 'required field with error element'],
            ['<form role="form" aria-labelledby="form-title"><h2 id="form-title">Login</h2></form>', true, 'form with accessibility attributes'],
            ['<button type="button" class="password-toggle" aria-label="Show password">👁️</button>', true, 'password toggle with aria-label'],
            ['<input type="checkbox" id="terms"><label for="terms">I agree</label>', true, 'checkbox with label'],
            
            // Invalid form accessibility patterns
            ['<input type="email" id="email" name="email">', false, 'input without label'],
            ['<input type="text" aria-describedby="missing-help">', false, 'aria-describedby with missing target'],
            ['<input type="password" required id="password">', false, 'required field without error element'],
            ['<form><input type="text"></form>', false, 'form without accessibility attributes'],
            ['<button class="password-toggle">👁️</button>', false, 'password toggle without aria-label'],
            ['<input type="checkbox" id="terms">I agree', false, 'checkbox without proper label']
        ];
        
        $failures = [];
        
        foreach ($testCases as [$html, $expected, $description]) {
            $labelViolations = $this->validateLabelAssociation($html);
            $ariaViolations = $this->validateAriaDescribedBy($html);
            $requiredViolations = $this->validateRequiredFields($html);
            $roleViolations = $this->validateFormRoles($html);
            $toggleViolations = $this->validatePasswordToggles($html);
            $checkboxViolations = $this->validateCheckboxAccessibility($html);
            
            $hasViolations = !empty($labelViolations) || !empty($ariaViolations) || 
                           !empty($requiredViolations) || !empty($roleViolations) ||
                           !empty($toggleViolations) || !empty($checkboxViolations);
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
        echo "Running Form Accessibility and Validation Tests...\n\n";
        
        // Test 1: Validate form accessibility patterns
        echo "Test 1: Form Accessibility Pattern Validation\n";
        $patternFailures = $this->testFormAccessibilityPatterns();
        
        if (empty($patternFailures)) {
            echo "✅ All form accessibility patterns validated correctly\n";
        } else {
            echo "❌ Form accessibility pattern validation failures:\n";
            foreach ($patternFailures as $failure) {
                echo "   $failure\n";
            }
        }
        
        // Test 2: Validate actual form accessibility
        echo "\nTest 2: Form File Accessibility Validation\n";
        $violations = $this->validateFormAccessibility();
        
        if (empty($violations)) {
            echo "✅ All forms meet accessibility standards\n";
        } else {
            echo "❌ Form accessibility violations found:\n";
            foreach ($violations as $violation) {
                echo "   {$violation['file']}: {$violation['type']} - {$violation['context']}\n";
            }
        }
        
        // Summary
        $totalFailures = count($patternFailures) + count($violations);
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "Form Accessibility and Validation Test Summary\n";
        echo "Total failures: $totalFailures\n";
        
        if ($totalFailures === 0) {
            echo "🎉 All tests passed! Forms meet accessibility and validation standards.\n";
            return true;
        } else {
            echo "⚠️  Some tests failed. Please review and fix the violations above.\n";
            return false;
        }
    }
}

// Run the tests
$test = new FormAccessibilityValidationTest();
$success = $test->runTests();

// Exit with appropriate code
exit($success ? 0 : 1);
?>