/**
 * BEM Naming Convention Property Test
 * Validates: Requirements 2.4, 4.3, 7.2
 * 
 * Property 4: BEM Naming Convention
 */

const fc = require('fast-check');
const fs = require('fs');
const path = require('path');

// BEM naming pattern regex
const BEM_PATTERNS = {
    block: /^[a-z][a-z0-9]*(-[a-z0-9]+)*$/,
    element: /^[a-z][a-z0-9]*(-[a-z0-9]+)*__[a-z][a-z0-9]*(-[a-z0-9]+)*$/,
    modifier: /^[a-z][a-z0-9]*(-[a-z0-9]+)*--[a-z][a-z0-9]*(-[a-z0-9]+)*$/,
    elementModifier: /^[a-z][a-z0-9]*(-[a-z0-9]+)*__[a-z][a-z0-9]*(-[a-z0-9]+)*--[a-z][a-z0-9]*(-[a-z0-9]+)*$/
};

function extractClassNames(content) {
    const classRegex = /class\s*=\s*["']([^"']+)["']/g;
    const classes = [];
    let match;
    
    while ((match = classRegex.exec(content)) !== null) {
        const classList = match[1].split(/\s+/).filter(cls => cls.length > 0);
        classes.push(...classList);
    }
    
    return [...new Set(classes)]; // Remove duplicates
}

function isBEMCompliant(className) {
    // Skip utility classes and framework classes
    const skipPatterns = [
        /^(btn|alert|card|form|text|bg|border|space|mt|mb|ml|mr|p|m)-/,
        /^(sr-only|clearfix|mobile-only|desktop-only)$/,
        /^(container|row|col|grid|flex)/,
        /^(empty-state|verified-badge|stat-item|cta-buttons|hero-note)$/,
        /^(search-container|product-grid|product-actions)$/
    ];
    
    if (skipPatterns.some(pattern => pattern.test(className))) {
        return true; // Skip utility classes
    }
    
    // Check if it matches any BEM pattern
    return Object.values(BEM_PATTERNS).some(pattern => pattern.test(className));
}

function validateBEMNaming() {
    const phpFiles = [
        'root/pages/browse.php',
        'root/pages/home.php',
        'root/pages/landing.php',
        'root/pages/cart.php',
        'root/pages/checkout.php',
        'root/pages/orders.php',
        'root/pages/listing.php',
        'root/pages/sell.php',
        'root/pages/profile.php',
        'root/pages/messages.php',
        'root/pages/login.php',
        'root/pages/register.php',
        'root/pages/payment-success.php',
        'root/pages/payment-cancel.php',
        'root/header.php',
        'root/footer.php'
    ];
    
    const violations = [];
    
    phpFiles.forEach(filePath => {
        if (fs.existsSync(filePath)) {
            const content = fs.readFileSync(filePath, 'utf8');
            const classes = extractClassNames(content);
            
            classes.forEach(className => {
                if (!isBEMCompliant(className)) {
                    violations.push({
                        file: filePath,
                        className: className,
                        reason: 'Does not follow BEM naming convention'
                    });
                }
            });
        }
    });
    
    return violations;
}

// Property test
describe('BEM Naming Convention', () => {
    test('All component class names should follow BEM convention', () => {
        const violations = validateBEMNaming();
        
        if (violations.length > 0) {
            const errorMessage = violations.map(v => 
                `${v.file}: "${v.className}" - ${v.reason}`
            ).join('\n');
            
            console.log(`BEM naming violations found:\n${errorMessage}`);
        }
        
        expect(violations).toHaveLength(0);
    });
    
    test('Property: BEM class names are consistently structured', () => {
        fc.assert(fc.property(
            fc.constantFrom(
                'product-card',
                'nav-menu',
                'search-form',
                'user-profile',
                'order-summary'
            ),
            fc.constantFrom(
                'header',
                'content',
                'footer',
                'image',
                'title',
                'description'
            ),
            fc.constantFrom(
                'primary',
                'secondary',
                'active',
                'disabled',
                'featured'
            ),
            (block, element, modifier) => {
                // Test block naming
                const blockClass = block;
                expect(BEM_PATTERNS.block.test(blockClass)).toBe(true);
                
                // Test element naming
                const elementClass = `${block}__${element}`;
                expect(BEM_PATTERNS.element.test(elementClass)).toBe(true);
                
                // Test modifier naming
                const modifierClass = `${block}--${modifier}`;
                expect(BEM_PATTERNS.modifier.test(modifierClass)).toBe(true);
                
                // Test element with modifier
                const elementModifierClass = `${block}__${element}--${modifier}`;
                expect(BEM_PATTERNS.elementModifier.test(elementModifierClass)).toBe(true);
                
                return true;
            }
        ));
    });
});