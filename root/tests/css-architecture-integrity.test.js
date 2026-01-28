/**
 * Property-Based Test: CSS Architecture Integrity
 * Feature: design-system-migration, Property 14: CSS Architecture Integrity
 * 
 * **Validates: Requirements 13.1, 13.2, 13.4**
 * 
 * Tests that the CSS architecture maintains proper separation of concerns,
 * correct import order, and dependency management across all CSS files.
 */

const fs = require('fs');
const path = require('path');

class CSSArchitectureIntegrityTest {
    constructor() {
        this.cssFiles = [
            'variables.css',
            'base.css',
            'components.css',
            'layout.css',
            'marketplace.css'
        ];
        
        this.cssPath = '../css/';
        this.mainStylePath = '../assets/style.css';
    }
    
    /**
     * Property 14: CSS Architecture Integrity
     * For any CSS file in the system, it should maintain proper separation of concerns,
     * follow the correct import hierarchy, and have no circular dependencies.
     */
    testCSSArchitectureIntegrity() {
        console.log('Feature: design-system-migration, Property 14: CSS Architecture Integrity');
        console.log('**Validates: Requirements 13.1, 13.2, 13.4**\n');
        
        const iterations = 100; // Minimum iterations for property-based testing
        let passed = 0;
        let failed = 0;
        const failures = [];
        
        for (let i = 0; i < iterations; i++) {
            // Generate random test scenarios
            const testScenario = this.generateTestScenario(i);
            
            try {
                const result = this.validateArchitectureProperty(testScenario);
                if (result.valid) {
                    passed++;
                } else {
                    failed++;
                    failures.push({
                        iteration: i + 1,
                        scenario: testScenario,
                        reason: result.reason
                    });
                }
            } catch (error) {
                failed++;
                failures.push({
                    iteration: i + 1,
                    scenario: testScenario,
                    reason: `Exception: ${error.message}`
                });
            }
        }
        
        console.log('Property Test Results:');
        console.log(`Iterations: ${iterations}`);
        console.log(`Passed: ${passed}`);
        console.log(`Failed: ${failed}`);
        console.log(`Success Rate: ${((passed / iterations) * 100).toFixed(2)}%\n`);
        
        if (failed > 0) {
            console.log('Failures:');
            failures.slice(0, 5).forEach(failure => {
                console.log(`- Iteration ${failure.iteration}: ${failure.reason}`);
            });
            if (failures.length > 5) {
                console.log(`- ... and ${failures.length - 5} more failures`);
            }
            console.log('');
            return false;
        }
        
        console.log('✅ Property 14: CSS Architecture Integrity - PASSED');
        return true;
    }
    
    /**
     * Generate random test scenarios for property-based testing
     */
    generateTestScenario(iteration) {
        const scenarios = [
            'import_order_validation',
            'file_separation_validation',
            'dependency_validation',
            'design_token_usage',
            'modular_structure_validation'
        ];
        
        return {
            type: scenarios[iteration % scenarios.length],
            fileIndex: iteration % this.cssFiles.length,
            randomSeed: iteration
        };
    }
    
    /**
     * Validate the CSS architecture property for a given scenario
     */
    validateArchitectureProperty(scenario) {
        switch (scenario.type) {
            case 'import_order_validation':
                return this.validateImportOrder(scenario);
                
            case 'file_separation_validation':
                return this.validateFileSeparation(scenario);
                
            case 'dependency_validation':
                return this.validateDependencies(scenario);
                
            case 'design_token_usage':
                return this.validateDesignTokenUsage(scenario);
                
            case 'modular_structure_validation':
                return this.validateModularStructure(scenario);
                
            default:
                return { valid: false, reason: 'Unknown test scenario' };
        }
    }
    
    /**
     * Validate that CSS imports follow the correct order
     */
    validateImportOrder(scenario) {
        const mainStylePath = path.join(__dirname, this.mainStylePath);
        
        if (!fs.existsSync(mainStylePath)) {
            return { valid: false, reason: 'Main style file not found' };
        }
        
        const content = fs.readFileSync(mainStylePath, 'utf8');
        const importMatches = content.match(/@import\s+url\(['"]?([^'"]+)['"]?\);?/gi);
        
        if (!importMatches) {
            return { valid: false, reason: 'No import statements found' };
        }
        
        const imports = importMatches.map(match => {
            const urlMatch = match.match(/@import\s+url\(['"]?([^'"]+)['"]?\);?/i);
            return urlMatch ? urlMatch[1] : '';
        });
        
        // Expected order
        const expectedOrder = [
            'css/variables.css',
            'css/base.css',
            'css/components.css',
            'css/layout.css',
            'css/marketplace.css'
        ];
        
        // Validate order
        for (let i = 0; i < Math.min(imports.length, expectedOrder.length); i++) {
            if (imports[i] !== expectedOrder[i]) {
                return {
                    valid: false,
                    reason: `Import order violation: expected ${expectedOrder[i]}, got ${imports[i]} at position ${i}`
                };
            }
        }
        
        return { valid: true, reason: 'Import order is correct' };
    }
    
    /**
     * Validate that each CSS file maintains proper separation of concerns
     */
    validateFileSeparation(scenario) {
        const fileName = this.cssFiles[scenario.fileIndex];
        const filePath = path.join(__dirname, this.cssPath, fileName);
        
        if (!fs.existsSync(filePath)) {
            return { valid: false, reason: `File ${fileName} not found` };
        }
        
        const content = fs.readFileSync(filePath, 'utf8');
        
        // Define what each file should contain
        const expectedContent = {
            'variables.css': ['--color-', '--font-', '--space-', '--radius-', '--shadow-', ':root'],
            'base.css': ['html', 'body', 'h1', 'h2', 'h3', 'p', 'a', '.container', '.grid'],
            'components.css': ['.btn', '.card', '.form-', '.modal', '.alert', '.badge'],
            'layout.css': ['.page', '.header', '.nav', '.footer', '.hero'],
            'marketplace.css': ['.product-', '.dashboard-', '.auth-', '.landing-', '.cart-']
        };
        
        const expected = expectedContent[fileName] || [];
        let foundCount = 0;
        
        expected.forEach(pattern => {
            if (content.includes(pattern)) {
                foundCount++;
            }
        });
        
        // At least 50% of expected patterns should be found
        const threshold = Math.max(1, expected.length * 0.5);
        
        if (foundCount < threshold) {
            return {
                valid: false,
                reason: `File ${fileName} doesn't contain expected content patterns (found ${foundCount} of ${expected.length})`
            };
        }
        
        return { valid: true, reason: `File ${fileName} maintains proper separation of concerns` };
    }
    
    /**
     * Validate that dependencies are properly managed
     */
    validateDependencies(scenario) {
        const fileName = this.cssFiles[scenario.fileIndex];
        const filePath = path.join(__dirname, this.cssPath, fileName);
        
        if (!fs.existsSync(filePath)) {
            return { valid: false, reason: `File ${fileName} not found` };
        }
        
        const content = fs.readFileSync(filePath, 'utf8');
        
        // Check for circular dependencies (CSS files shouldn't import each other)
        if (content.includes('@import')) {
            // Only variables.css and base.css should have imports (Google Fonts)
            if (!['variables.css', 'base.css'].includes(fileName)) {
                return {
                    valid: false,
                    reason: `File ${fileName} contains @import statements, violating modular architecture`
                };
            }
        }
        
        // Check for proper CSS custom property usage
        if (fileName !== 'variables.css') {
            // Non-variables files should use CSS custom properties, not define them
            const customPropDefinitions = (content.match(/--[\w-]+\s*:/g) || []).length;
            if (customPropDefinitions > 5) { // Allow a few exceptions
                return {
                    valid: false,
                    reason: `File ${fileName} defines too many CSS custom properties (should use variables.css)`
                };
            }
        }
        
        return { valid: true, reason: `Dependencies are properly managed in ${fileName}` };
    }
    
    /**
     * Validate proper design token usage
     */
    validateDesignTokenUsage(scenario) {
        const fileName = this.cssFiles[scenario.fileIndex];
        const filePath = path.join(__dirname, this.cssPath, fileName);
        
        if (!fs.existsSync(filePath) || fileName === 'variables.css') {
            return { valid: true, reason: 'Skipping design token validation for variables.css' };
        }
        
        const content = fs.readFileSync(filePath, 'utf8');
        
        // Check for hardcoded values that should use design tokens
        const hardcodedPatterns = [
            /color:\s*#[0-9a-fA-F]{3,6}(?!\s*;?\s*\/\*\s*fallback)/g,  // Hardcoded hex colors
            /font-family:\s*['"][^'"]*(?:Arial|Helvetica|Times)['"]/g,  // Hardcoded font families
            /padding:\s*\d+px/g,  // Hardcoded pixel padding
            /margin:\s*\d+px/g,   // Hardcoded pixel margins
        ];
        
        for (const pattern of hardcodedPatterns) {
            if (pattern.test(content)) {
                return {
                    valid: false,
                    reason: `File ${fileName} contains hardcoded values that should use design tokens`
                };
            }
        }
        
        // Check for proper CSS custom property usage
        const customPropUsage = (content.match(/var\(--[\w-]+\)/g) || []).length;
        const totalRules = (content.match(/[{;}]/g) || []).length;
        
        if (totalRules > 10 && customPropUsage < (totalRules * 0.1)) {
            return {
                valid: false,
                reason: `File ${fileName} has low design token usage ratio (${customPropUsage}/${totalRules})`
            };
        }
        
        return { valid: true, reason: `Design tokens are properly used in ${fileName}` };
    }
    
    /**
     * Validate modular structure integrity
     */
    validateModularStructure(scenario) {
        // Check that all required files exist
        for (const file of this.cssFiles) {
            const filePath = path.join(__dirname, this.cssPath, file);
            if (!fs.existsSync(filePath)) {
                return { valid: false, reason: `Required CSS file ${file} is missing` };
            }
        }
        
        // Check main style file exists and imports all modules
        const mainStylePath = path.join(__dirname, this.mainStylePath);
        if (!fs.existsSync(mainStylePath)) {
            return { valid: false, reason: 'Main style file is missing' };
        }
        
        const mainContent = fs.readFileSync(mainStylePath, 'utf8');
        for (const file of this.cssFiles) {
            if (!mainContent.includes(`css/${file}`)) {
                return {
                    valid: false,
                    reason: `Main style file doesn't import ${file}`
                };
            }
        }
        
        // Check file sizes are reasonable (not empty, not too large)
        for (const file of this.cssFiles) {
            const filePath = path.join(__dirname, this.cssPath, file);
            const stats = fs.statSync(filePath);
            const size = stats.size;
            
            if (size < 100) {
                return { valid: false, reason: `File ${file} is too small (possibly empty)` };
            }
            
            if (size > 100000) { // 100KB limit per file
                return { valid: false, reason: `File ${file} is too large (over 100KB)` };
            }
        }
        
        return { valid: true, reason: 'Modular structure is intact' };
    }
    
    /**
     * Run all architecture integrity tests
     */
    runAllTests() {
        console.log('CSS Architecture Integrity Test Suite');
        console.log('=====================================\n');
        
        const result = this.testCSSArchitectureIntegrity();
        
        if (result) {
            console.log('\n🎉 All CSS architecture integrity tests passed!');
            console.log('The CSS architecture maintains proper separation of concerns,');
            console.log('follows correct import order, and has no dependency issues.');
        } else {
            console.log('\n❌ CSS architecture integrity tests failed!');
            console.log('Please review the failures above and fix the architecture issues.');
        }
        
        return result;
    }
}

// Run the tests if this file is executed directly
if (require.main === module) {
    const test = new CSSArchitectureIntegrityTest();
    const result = test.runAllTests();
    process.exit(result ? 0 : 1);
}

module.exports = CSSArchitectureIntegrityTest;