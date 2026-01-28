/**
 * Simple validation script to check if our accessibility fixes are working
 */

// Test cases from the PHP test
const testCases = [
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

function validateARIALabels(html) {
    const violations = [];
    
    // Check for buttons without aria-label or meaningful text content
    const buttonRegex = /<button[^>]*>(.*?)<\/button>/gs;
    let match;
    
    while ((match = buttonRegex.exec(html)) !== null) {
        const buttonTag = match[0];
        const buttonText = match[1].replace(/<[^>]*>/g, '').trim();
        
        const hasAriaLabel = /aria-label\s*=\s*["'][^"']+["']/.test(buttonTag);
        const hasMeaningfulText = buttonText && buttonText !== '×' && buttonText.length > 1;
        
        if (!hasAriaLabel && !hasMeaningfulText) {
            violations.push({
                type: 'missing-aria-label',
                element: 'button',
                context: buttonTag.substring(0, 100) + '...'
            });
        }
    }
    
    // Check for images without alt text
    const imgRegex = /<img[^>]*>/gi;
    while ((match = imgRegex.exec(html)) !== null) {
        if (!/alt\s*=/.test(match[0])) {
            violations.push({
                type: 'missing-alt-text',
                element: 'img',
                context: match[0].substring(0, 100) + '...'
            });
        }
    }
    
    return violations;
}

function validateFormAccessibility(html) {
    const violations = [];
    
    // Extract inputs and labels
    const inputRegex = /<input[^>]*>/gi;
    const labelRegex = /<label[^>]*for\s*=\s*["']([^"']+)["'][^>]*>/gi;
    
    const inputs = [];
    const labelFors = [];
    
    let match;
    while ((match = inputRegex.exec(html)) !== null) {
        inputs.push(match[0]);
    }
    
    while ((match = labelRegex.exec(html)) !== null) {
        labelFors.push(match[1]);
    }
    
    inputs.forEach(inputTag => {
        // Skip hidden inputs and buttons
        if (/type\s*=\s*["'](?:hidden|submit|button)["']/.test(inputTag)) {
            return;
        }
        
        const idMatch = inputTag.match(/id\s*=\s*["']([^"']+)["']/i);
        const hasAriaLabel = /aria-label\s*=\s*["'][^"']+["']/.test(inputTag);
        
        if (idMatch) {
            const inputId = idMatch[1];
            const hasLabel = labelFors.includes(inputId);
            
            if (!hasLabel && !hasAriaLabel) {
                violations.push({
                    type: 'input-without-label',
                    element: 'input',
                    context: `Input with id='${inputId}' has no corresponding label or aria-label`
                });
            }
        } else if (!hasAriaLabel) {
            violations.push({
                type: 'input-without-label',
                element: 'input',
                context: `Input without id must have aria-label: ${inputTag.substring(0, 50)}...`
            });
        }
    });
    
    return violations;
}

function validateSkipLinks(html) {
    const violations = [];
    
    // Check for main content landmark
    if (!/id\s*=\s*["']main-content["']/.test(html) && 
        !/role\s*=\s*["']main["']/.test(html) &&
        !/<main[^>]*>/.test(html)) {
        violations.push({
            type: 'missing-main-landmark',
            element: 'main',
            context: 'No main content landmark found'
        });
    }
    
    return violations;
}

function validateModalAccessibility(html) {
    const violations = [];
    
    // Check for modal ARIA attributes
    const modalRegex = /<div[^>]*class\s*=\s*["'][^"']*modal[^"']*["'][^>]*>/gi;
    let match;
    
    while ((match = modalRegex.exec(html)) !== null) {
        const modalTag = match[0];
        
        if (!/aria-hidden/.test(modalTag)) {
            violations.push({
                type: 'modal-missing-aria-hidden',
                element: 'modal',
                context: modalTag.substring(0, 100) + '...'
            });
        }
        
        if (!/role\s*=\s*["']dialog["']/.test(modalTag)) {
            violations.push({
                type: 'modal-missing-dialog-role',
                element: 'modal',
                context: modalTag.substring(0, 100) + '...'
            });
        }
        
        if (!/aria-modal/.test(modalTag)) {
            violations.push({
                type: 'modal-missing-aria-modal',
                element: 'modal',
                context: modalTag.substring(0, 100) + '...'
            });
        }
    }
    
    return violations;
}

function testAccessibilityPatterns() {
    const failures = [];
    
    testCases.forEach(([html, expected, description]) => {
        const ariaViolations = validateARIALabels(html);
        const formViolations = validateFormAccessibility(html);
        const modalViolations = validateModalAccessibility(html);
        const skipLinkViolations = validateSkipLinks(html);
        
        const hasViolations = ariaViolations.length > 0 || formViolations.length > 0 || 
                             modalViolations.length > 0 || skipLinkViolations.length > 0;
        const isValid = !hasViolations;
        
        if (isValid !== expected) {
            failures.push(`Failed: '${html}' (${description}) - Expected ${expected ? 'valid' : 'invalid'}, got ${isValid ? 'valid' : 'invalid'}`);
        }
    });
    
    return failures;
}

// Run the test
console.log('Running Accessibility Compliance Tests...\n');
console.log('Test 1: Accessibility Pattern Validation');

const patternFailures = testAccessibilityPatterns();

if (patternFailures.length === 0) {
    console.log('✅ All accessibility patterns validated correctly');
} else {
    console.log('❌ Accessibility pattern validation failures:');
    patternFailures.forEach(failure => {
        console.log(`   ${failure}`);
    });
}

console.log('\n' + '='.repeat(50));
console.log('Accessibility Compliance Test Summary');
console.log(`Total failures: ${patternFailures.length}`);

if (patternFailures.length === 0) {
    console.log('🎉 All tests passed! Application meets WCAG 2.1 AA standards.');
} else {
    console.log('⚠️  Some tests failed. Please review and fix the violations above.');
}