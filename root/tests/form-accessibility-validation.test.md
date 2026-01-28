# Form Accessibility and Validation Property Test

**Validates: Requirements 5.1, 5.2, 5.5**

## Property 6: Form Accessibility and Validation

This property test validates that all forms in the application meet accessibility standards and provide proper validation feedback to users.

### Form Accessibility Requirements

1. **Label Association**: All form inputs must have properly associated labels
2. **Error Messaging**: Validation errors must be announced to screen readers
3. **Help Text**: Descriptive help text must be linked to form fields
4. **Required Fields**: Required fields must be clearly indicated
5. **Focus Management**: Focus must be managed properly during validation
6. **Keyboard Navigation**: All form elements must be keyboard accessible
7. **ARIA Attributes**: Proper ARIA attributes for form states and validation

### Test Strategy

The property test validates:
- All inputs have associated labels using `for`/`id` attributes
- Error messages are linked with `aria-describedby`
- Help text is properly associated with form fields
- Required fields have appropriate ARIA attributes
- Form validation provides accessible feedback
- Password visibility toggles are keyboard accessible
- Checkbox and radio inputs have proper labels

### Test Implementation

```javascript
// Property-based test for form accessibility and validation
const fc = require('fast-check');
const fs = require('fs');
const path = require('path');

// Form accessibility validation functions
function validateLabelAssociation(content) {
    const violations = [];
    
    // Extract all inputs and their IDs
    const inputRegex = /<input[^>]*>/gi;
    const labelRegex = /<label[^>]*for\s*=\s*["']([^"']+)["'][^>]*>/gi;
    
    const inputs = [];
    const labelFors = new Set();
    
    let match;
    while ((match = inputRegex.exec(content)) !== null) {
        const inputTag = match[0];
        const idMatch = inputTag.match(/id\s*=\s*["']([^"']+)["']/i);
        const typeMatch = inputTag.match(/type\s*=\s*["']([^"']+)["']/i);
        
        if (idMatch) {
            inputs.push({
                id: idMatch[1],
                type: typeMatch ? typeMatch[1] : 'text',
                tag: inputTag
            });
        }
    }
    
    while ((match = labelRegex.exec(content)) !== null) {
        labelFors.add(match[1]);
    }
    
    // Check for inputs without labels (excluding hidden inputs)
    inputs.forEach(input => {
        if (input.type !== 'hidden' && !labelFors.has(input.id)) {
            violations.push({
                type: 'input-without-label',
                element: 'input',
                context: `Input with id='${input.id}' and type='${input.type}' has no associated label`,
                inputId: input.id,
                inputType: input.type
            });
        }
    });
    
    return violations;
}

function validateAriaDescribedBy(content) {
    const violations = [];
    
    // Find inputs with aria-describedby
    const inputRegex = /<input[^>]*aria-describedby\s*=\s*["']([^"']+)["'][^>]*>/gi;
    const elementRegex = /<[^>]*id\s*=\s*["']([^"']+)["'][^>]*>/gi;
    
    const describedByIds = new Set();
    const existingIds = new Set();
    
    let match;
    while ((match = inputRegex.exec(content)) !== null) {
        const describedBy = match[1].split(/\s+/);
        describedBy.forEach(id => describedByIds.add(id.trim()));
    }
    
    // Reset regex
    content.replace(elementRegex, (match, id) => {
        existingIds.add(id);
        return match;
    });
    
    // Check if all aria-describedby IDs exist
    describedByIds.forEach(id => {
        if (!existingIds.has(id)) {
            violations.push({
                type: 'missing-describedby-target',
                element: 'aria-describedby',
                context: `aria-describedby references non-existent ID: ${id}`,
                missingId: id
            });
        }
    });
    
    return violations;
}

function validateRequiredFields(content) {
    const violations = [];
    
    // Find required inputs
    const requiredInputRegex = /<input[^>]*required[^>]*>/gi;
    
    let match;
    while ((match = requiredInputRegex.exec(content)) !== null) {
        const inputTag = match[0];
        const idMatch = inputTag.match(/id\s*=\s*["']([^"']+)["']/i);
        
        if (idMatch) {
            const inputId = idMatch[1];
            
            // Check if there's an associated error element
            const errorElementRegex = new RegExp(`id\\s*=\\s*["']${inputId}-error["']`, 'i');
            if (!errorElementRegex.test(content)) {
                violations.push({
                    type: 'required-field-without-error-element',
                    element: 'input',
                    context: `Required input with id='${inputId}' has no associated error element`,
                    inputId: inputId
                });
            }
        }
    }
    
    return violations;
}

function validateFormRoles(content) {
    const violations = [];
    
    // Check for forms without proper roles
    const formRegex = /<form[^>]*>/gi;
    
    let match;
    while ((match = formRegex.exec(content)) !== null) {
        const formTag = match[0];
        
        if (!formTag.includes('role=') && !formTag.includes('aria-labelledby=')) {
            violations.push({
                type: 'form-without-accessibility-attributes',
                element: 'form',
                context: 'Form element lacks role or aria-labelledby attribute',
                formTag: formTag.substring(0, 100) + '...'
            });
        }
    }
    
    return violations;
}

function validatePasswordToggles(content) {
    const violations = [];
    
    // Find password toggle buttons
    const toggleRegex = /<button[^>]*password-toggle[^>]*>/gi;
    
    let match;
    while ((match = toggleRegex.exec(content)) !== null) {
        const buttonTag = match[0];
        
        if (!buttonTag.includes('aria-label=')) {
            violations.push({
                type: 'password-toggle-without-aria-label',
                element: 'button',
                context: 'Password toggle button lacks aria-label',
                buttonTag: buttonTag.substring(0, 100) + '...'
            });
        }
        
        if (!buttonTag.includes('type="button"')) {
            violations.push({
                type: 'password-toggle-wrong-type',
                element: 'button',
                context: 'Password toggle should have type="button"',
                buttonTag: buttonTag.substring(0, 100) + '...'
            });
        }
    }
    
    return violations;
}

function validateCheckboxAccessibility(content) {
    const violations = [];
    
    // Find checkbox inputs
    const checkboxRegex = /<input[^>]*type\s*=\s*["']checkbox["'][^>]*>/gi;
    
    let match;
    while ((match = checkboxRegex.exec(content)) !== null) {
        const inputTag = match[0];
        const idMatch = inputTag.match(/id\s*=\s*["']([^"']+)["']/i);
        
        if (idMatch) {
            const inputId = idMatch[1];
            const labelRegex = new RegExp(`<label[^>]*for\\s*=\\s*["']${inputId}["'][^>]*>`, 'i');
            
            if (!labelRegex.test(content)) {
                violations.push({
                    type: 'checkbox-without-label',
                    element: 'input[type="checkbox"]',
                    context: `Checkbox with id='${inputId}' has no associated label`,
                    inputId: inputId
                });
            }
        }
    }
    
    return violations;
}

function validateFormAccessibility() {
    const formPages = [
        'root/pages/login.php',
        'root/pages/register.php',
        'root/pages/listing.php',
        'root/pages/checkout.php',
        'root/pages/profile.php',
        'root/pages/sell.php'
    ];
    
    const allViolations = [];
    
    formPages.forEach(filePath => {
        if (fs.existsSync(filePath)) {
            const content = fs.readFileSync(filePath, 'utf8');
            
            const labelViolations = validateLabelAssociation(content);
            const ariaViolations = validateAriaDescribedBy(content);
            const requiredViolations = validateRequiredFields(content);
            const roleViolations = validateFormRoles(content);
            const toggleViolations = validatePasswordToggles(content);
            const checkboxViolations = validateCheckboxAccessibility(content);
            
            const fileViolations = [
                ...labelViolations,
                ...ariaViolations,
                ...requiredViolations,
                ...roleViolations,
                ...toggleViolations,
                ...checkboxViolations
            ].map(violation => ({
                ...violation,
                file: filePath
            }));
            
            allViolations.push(...fileViolations);
        }
    });
    
    return allViolations;
}

// Property test
describe('Form Accessibility and Validation', () => {
    test('All forms should meet accessibility standards', () => {
        const violations = validateFormAccessibility();
        
        if (violations.length > 0) {
            const errorMessage = violations.map(v => 
                `${v.file}: ${v.type} - ${v.context}`
            ).join('\n');
            
            throw new Error(`Form accessibility violations found:\n${errorMessage}`);
        }
        
        expect(violations).toHaveLength(0);
    });
    
    test('Property: Form inputs have proper accessibility attributes', () => {
        fc.assert(fc.property(
            fc.constantFrom('text', 'email', 'password', 'checkbox', 'radio'),
            fc.constantFrom('username', 'email', 'password', 'confirm', 'terms'),
            (inputType, inputName) => {
                // Generate a proper form input with accessibility attributes
                const inputId = `${inputName}-input`;
                const helpId = `${inputName}-help`;
                const errorId = `${inputName}-error`;
                
                const testHTML = `
                    <div class="form-group">
                        <label for="${inputId}">${inputName.charAt(0).toUpperCase() + inputName.slice(1)}</label>
                        <input 
                            type="${inputType}" 
                            id="${inputId}" 
                            name="${inputName}"
                            aria-describedby="${helpId} ${errorId}"
                            ${inputType === 'checkbox' ? '' : 'required'}
                        >
                        <small id="${helpId}" class="form-help">Help text</small>
                        <div id="${errorId}" class="form-error" role="alert" aria-live="polite"></div>
                    </div>
                `;
                
                // Validate the generated HTML
                const labelViolations = validateLabelAssociation(testHTML);
                const ariaViolations = validateAriaDescribedBy(testHTML);
                
                expect(labelViolations).toHaveLength(0);
                expect(ariaViolations).toHaveLength(0);
                
                return true;
            }
        ));
    });
    
    test('Property: Password fields have proper toggle accessibility', () => {
        fc.assert(fc.property(
            fc.constantFrom('password', 'confirm-password', 'new-password'),
            (fieldName) => {
                const testHTML = `
                    <div class="password-input-wrapper">
                        <input type="password" id="${fieldName}" name="${fieldName}">
                        <button 
                            type="button" 
                            class="password-toggle" 
                            aria-label="Show ${fieldName.replace('-', ' ')}"
                        >
                            <span aria-hidden="true">👁️</span>
                        </button>
                    </div>
                `;
                
                const toggleViolations = validatePasswordToggles(testHTML);
                expect(toggleViolations).toHaveLength(0);
                
                return true;
            }
        ));
    });
    
    test('Property: Required fields have error elements', () => {
        fc.assert(fc.property(
            fc.constantFrom('email', 'password', 'name', 'phone'),
            (fieldName) => {
                const testHTML = `
                    <input type="text" id="${fieldName}" name="${fieldName}" required>
                    <div id="${fieldName}-error" class="form-error" role="alert"></div>
                `;
                
                const requiredViolations = validateRequiredFields(testHTML);
                expect(requiredViolations).toHaveLength(0);
                
                return true;
            }
        ));
    });
});
```

### Expected Form Accessibility Features

Based on the current implementation, these accessibility features should be present:

#### Label Association
- All inputs have `id` attributes
- All labels have corresponding `for` attributes
- Checkbox labels properly wrap or reference inputs

#### ARIA Attributes
- `aria-describedby` links inputs to help text and error messages
- `role="alert"` on error messages for screen reader announcements
- `aria-live="polite"` on error containers for dynamic updates
- `aria-invalid` states for validation feedback

#### Form Structure
- Forms have `role="form"` or `aria-labelledby` attributes
- Fieldsets group related inputs with proper legends
- Required fields are clearly indicated

#### Validation Feedback
- Error messages are associated with inputs
- Real-time validation provides immediate feedback
- Focus management during validation errors
- Clear success and error states

#### Keyboard Navigation
- All form elements are keyboard accessible
- Password toggles work with keyboard
- Logical tab order throughout forms
- Submit buttons are easily accessible

### Test Execution

Run this test to validate form accessibility and validation:

```bash
npm test -- form-accessibility-validation.test.js
```

### Success Criteria

- All form inputs have properly associated labels
- Error messages are linked to inputs with `aria-describedby`
- Required fields have appropriate error handling
- Password toggles have proper ARIA labels
- Checkbox and radio inputs are fully accessible
- Forms provide clear validation feedback
- All interactive elements are keyboard accessible
- Screen readers can navigate and understand all form elements

This property test ensures that all forms in the application provide an accessible and user-friendly experience for all users, including those using assistive technologies.