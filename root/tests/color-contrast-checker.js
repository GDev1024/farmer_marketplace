/**
 * Color Contrast Checker for WCAG Compliance
 * Tests all color combinations in the design system for accessibility compliance
 */

// Color palette from variables.css
const colors = {
  // Primary Colors
  'color-primary': '#3d5a3a',
  'color-primary-light': '#5a7456',
  'color-primary-dark': '#2d4429',
  'color-primary-pale': '#e8ede7',
  
  // Secondary Colors
  'color-secondary': '#8b7355',
  'color-secondary-light': '#a68968',
  'color-secondary-dark': '#6d5940',
  'color-secondary-pale': '#f5f1ec',
  
  // Neutral Colors
  'color-white': '#ffffff',
  'color-cream': '#fefdfb',
  'color-gray-50': '#fafaf9',
  'color-gray-100': '#f5f5f4',
  'color-gray-200': '#e7e5e4',
  'color-gray-300': '#d6d3d1',
  'color-gray-400': '#a8a29e',
  'color-gray-500': '#78716c',
  'color-gray-600': '#57534e',
  'color-gray-700': '#44403c',
  'color-gray-800': '#292524',
  'color-gray-900': '#1c1917',
  'color-black': '#0a0a0a',
  
  // Accent Colors
  'color-accent-green': '#3d5a3a',
  'color-accent-amber': '#d97706',
  'color-accent-red': '#dc2626',
  'color-accent-blue': '#2563eb',
  
  // Background Colors
  'bg-primary': '#ffffff',
  'bg-secondary': '#fefdfb',
  'bg-tertiary': '#fafaf9',
  'bg-muted': '#f5f5f4',
  
  // Text Colors
  'text-primary': '#1c1917',
  'text-secondary': '#57534e',
  'text-muted': '#78716c',
  'text-inverse': '#ffffff',
  
  // Border Colors
  'border-primary': '#e7e5e4',
  'border-secondary': '#f5f5f4',
  'border-light': '#e7e5e4',
  'border-focus': '#3d5a3a'
};

// Common color combinations used in the application
const colorCombinations = [
  // Text on backgrounds
  { foreground: 'text-primary', background: 'bg-primary', context: 'Normal text on white background' },
  { foreground: 'text-primary', background: 'bg-secondary', context: 'Normal text on cream background' },
  { foreground: 'text-primary', background: 'bg-tertiary', context: 'Normal text on light gray background' },
  { foreground: 'text-secondary', background: 'bg-primary', context: 'Secondary text on white background' },
  { foreground: 'text-secondary', background: 'bg-secondary', context: 'Secondary text on cream background' },
  { foreground: 'text-muted', background: 'bg-primary', context: 'Muted text on white background' },
  { foreground: 'text-inverse', background: 'color-primary', context: 'White text on primary green' },
  { foreground: 'text-inverse', background: 'color-primary-dark', context: 'White text on dark green' },
  
  // Button combinations
  { foreground: 'text-inverse', background: 'color-primary', context: 'Primary button text' },
  { foreground: 'color-primary', background: 'bg-primary', context: 'Secondary button text' },
  { foreground: 'text-inverse', background: 'color-accent-red', context: 'Danger button text' },
  { foreground: 'text-inverse', background: 'color-accent-amber', context: 'Warning button text' },
  { foreground: 'text-inverse', background: 'color-accent-blue', context: 'Info button text' },
  
  // Link combinations
  { foreground: 'color-primary', background: 'bg-primary', context: 'Primary links on white' },
  { foreground: 'color-primary', background: 'bg-secondary', context: 'Primary links on cream' },
  { foreground: 'color-primary-light', background: 'bg-primary', context: 'Hovered links on white' },
  
  // Alert/notification combinations
  { foreground: 'color-accent-green', background: 'color-primary-pale', context: 'Success alert text' },
  { foreground: 'color-accent-amber', background: 'color-warning-light', context: 'Warning alert text' },
  { foreground: 'color-accent-red', background: 'bg-primary', context: 'Error text on white' },
  { foreground: 'color-accent-blue', background: 'color-info-light', context: 'Info alert text' },
  
  // Form combinations
  { foreground: 'text-primary', background: 'bg-primary', context: 'Form input text' },
  { foreground: 'text-muted', background: 'bg-primary', context: 'Form placeholder text' },
  { foreground: 'color-accent-red', background: 'bg-primary', context: 'Form error text' },
  
  // Navigation combinations
  { foreground: 'text-primary', background: 'bg-primary', context: 'Navigation text' },
  { foreground: 'color-primary', background: 'bg-primary', context: 'Active navigation links' },
  
  // Card combinations
  { foreground: 'text-primary', background: 'bg-primary', context: 'Card content text' },
  { foreground: 'text-secondary', background: 'bg-primary', context: 'Card secondary text' },
  { foreground: 'color-primary', background: 'bg-primary', context: 'Card links' }
];

/**
 * Convert hex color to RGB
 */
function hexToRgb(hex) {
  const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  return result ? {
    r: parseInt(result[1], 16),
    g: parseInt(result[2], 16),
    b: parseInt(result[3], 16)
  } : null;
}

/**
 * Calculate relative luminance of a color
 * Based on WCAG 2.1 specification
 */
function getLuminance(rgb) {
  const { r, g, b } = rgb;
  
  // Convert to sRGB
  const rsRGB = r / 255;
  const gsRGB = g / 255;
  const bsRGB = b / 255;
  
  // Apply gamma correction
  const rLinear = rsRGB <= 0.03928 ? rsRGB / 12.92 : Math.pow((rsRGB + 0.055) / 1.055, 2.4);
  const gLinear = gsRGB <= 0.03928 ? gsRGB / 12.92 : Math.pow((gsRGB + 0.055) / 1.055, 2.4);
  const bLinear = bsRGB <= 0.03928 ? bsRGB / 12.92 : Math.pow((bsRGB + 0.055) / 1.055, 2.4);
  
  // Calculate luminance
  return 0.2126 * rLinear + 0.7152 * gLinear + 0.0722 * bLinear;
}

/**
 * Calculate contrast ratio between two colors
 * Based on WCAG 2.1 specification
 */
function getContrastRatio(color1, color2) {
  const rgb1 = hexToRgb(color1);
  const rgb2 = hexToRgb(color2);
  
  if (!rgb1 || !rgb2) {
    throw new Error('Invalid color format');
  }
  
  const lum1 = getLuminance(rgb1);
  const lum2 = getLuminance(rgb2);
  
  const lighter = Math.max(lum1, lum2);
  const darker = Math.min(lum1, lum2);
  
  return (lighter + 0.05) / (darker + 0.05);
}

/**
 * Check if contrast ratio meets WCAG standards
 */
function checkWCAGCompliance(ratio, isLargeText = false) {
  const aaThreshold = isLargeText ? 3.0 : 4.5;
  const aaaThreshold = isLargeText ? 4.5 : 7.0;
  
  return {
    ratio: ratio,
    passAA: ratio >= aaThreshold,
    passAAA: ratio >= aaaThreshold,
    level: ratio >= aaaThreshold ? 'AAA' : ratio >= aaThreshold ? 'AA' : 'FAIL'
  };
}

/**
 * Test all color combinations for WCAG compliance
 */
function testColorContrast() {
  const results = [];
  let totalTests = 0;
  let passedAA = 0;
  let passedAAA = 0;
  let failed = 0;
  
  console.log('🎨 Testing Color Contrast Compliance for Grenada Farmer Marketplace');
  console.log('=' .repeat(80));
  
  colorCombinations.forEach(combo => {
    const foregroundColor = colors[combo.foreground];
    const backgroundColor = colors[combo.background];
    
    if (!foregroundColor || !backgroundColor) {
      console.warn(`⚠️  Missing color definition: ${combo.foreground} or ${combo.background}`);
      return;
    }
    
    try {
      const ratio = getContrastRatio(foregroundColor, backgroundColor);
      const normalText = checkWCAGCompliance(ratio, false);
      const largeText = checkWCAGCompliance(ratio, true);
      
      const result = {
        context: combo.context,
        foreground: combo.foreground,
        background: combo.background,
        foregroundHex: foregroundColor,
        backgroundHex: backgroundColor,
        ratio: ratio,
        normalText: normalText,
        largeText: largeText
      };
      
      results.push(result);
      totalTests++;
      
      // Count results
      if (normalText.passAA) passedAA++;
      if (normalText.passAAA) passedAAA++;
      if (!normalText.passAA) failed++;
      
      // Log result
      const status = normalText.passAAA ? '✅ AAA' : normalText.passAA ? '✅ AA' : '❌ FAIL';
      const ratioStr = ratio.toFixed(2);
      
      console.log(`${status} ${ratioStr}:1 - ${combo.context}`);
      console.log(`     ${combo.foreground} (${foregroundColor}) on ${combo.background} (${backgroundColor})`);
      
      if (!normalText.passAA) {
        console.log(`     ⚠️  Normal text fails WCAG AA (needs 4.5:1, got ${ratioStr}:1)`);
      }
      if (!largeText.passAA) {
        console.log(`     ⚠️  Large text fails WCAG AA (needs 3.0:1, got ${ratioStr}:1)`);
      }
      
      console.log('');
      
    } catch (error) {
      console.error(`❌ Error testing ${combo.context}: ${error.message}`);
    }
  });
  
  // Summary
  console.log('=' .repeat(80));
  console.log('📊 SUMMARY');
  console.log(`Total tests: ${totalTests}`);
  console.log(`✅ Passed WCAG AA: ${passedAA} (${((passedAA/totalTests)*100).toFixed(1)}%)`);
  console.log(`✅ Passed WCAG AAA: ${passedAAA} (${((passedAAA/totalTests)*100).toFixed(1)}%)`);
  console.log(`❌ Failed WCAG AA: ${failed} (${((failed/totalTests)*100).toFixed(1)}%)`);
  
  if (failed === 0) {
    console.log('🎉 All color combinations pass WCAG AA standards!');
  } else {
    console.log(`⚠️  ${failed} color combinations need improvement for WCAG AA compliance.`);
  }
  
  return {
    results: results,
    summary: {
      total: totalTests,
      passedAA: passedAA,
      passedAAA: passedAAA,
      failed: failed,
      allPass: failed === 0
    }
  };
}

/**
 * Generate recommendations for failing color combinations
 */
function generateRecommendations(results) {
  const failing = results.filter(r => !r.normalText.passAA);
  
  if (failing.length === 0) {
    console.log('✅ No recommendations needed - all combinations pass WCAG AA!');
    return;
  }
  
  console.log('🔧 RECOMMENDATIONS FOR FAILING COMBINATIONS:');
  console.log('=' .repeat(80));
  
  failing.forEach(result => {
    console.log(`❌ ${result.context}`);
    console.log(`   Current ratio: ${result.ratio.toFixed(2)}:1 (needs 4.5:1)`);
    console.log(`   Colors: ${result.foregroundHex} on ${result.backgroundHex}`);
    
    // Suggest darker foreground or lighter background
    const improvement = 4.5 / result.ratio;
    console.log(`   💡 Suggestion: Increase contrast by ${improvement.toFixed(2)}x`);
    console.log(`      - Make foreground darker, or`);
    console.log(`      - Make background lighter`);
    console.log('');
  });
}

// Export for use in tests
if (typeof module !== 'undefined' && module.exports) {
  module.exports = {
    testColorContrast,
    getContrastRatio,
    checkWCAGCompliance,
    colors,
    colorCombinations
  };
}

// Run tests if called directly
if (typeof window !== 'undefined') {
  // Browser environment
  window.colorContrastChecker = {
    testColorContrast,
    getContrastRatio,
    checkWCAGCompliance,
    colors,
    colorCombinations
  };
} else if (typeof require !== 'undefined' && require.main === module) {
  // Node.js environment - run tests
  const testResults = testColorContrast();
  generateRecommendations(testResults.results);
}