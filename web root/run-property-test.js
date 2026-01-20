// Property Test Runner for Natural Color Palette Compliance
// This script validates the color palette compliance programmatically

const fs = require('fs');
const path = require('path');

// Test colors from the design system
const testColors = [
    { name: 'clay-50', value: '#faf9f7' },
    { name: 'clay-100', value: '#f2f0ec' },
    { name: 'clay-200', value: '#e8e4dd' },
    { name: 'clay-300', value: '#d6cfc4' },
    { name: 'clay-400', value: '#c0b5a7' },
    { name: 'clay-500', value: '#a89688' },
    { name: 'clay-600', value: '#8f7a6b' },
    { name: 'clay-700', value: '#756356' },
    { name: 'clay-800', value: '#5d4f44' },
    { name: 'clay-900', value: '#4a3f36' },
    
    { name: 'cream-50', value: '#fefdfb' },
    { name: 'cream-100', value: '#fdf9f4' },
    { name: 'cream-200', value: '#fbf4ea' },
    { name: 'cream-300', value: '#f7edd8' },
    { name: 'cream-400', value: '#f1e2c1' },
    { name: 'cream-500', value: '#e8d5a6' },
    { name: 'cream-600', value: '#dcc688' },
    { name: 'cream-700', value: '#c9b06d' },
    { name: 'cream-800', value: '#b39856' },
    { name: 'cream-900', value: '#9a8147' },
    
    { name: 'olive-50', value: '#f7f8f4' },
    { name: 'olive-100', value: '#eef0e7' },
    { name: 'olive-200', value: '#dde2d0' },
    { name: 'olive-300', value: '#c4ccb0' },
    { name: 'olive-400', value: '#a8b28a' },
    { name: 'olive-500', value: '#8d9869' },
    { name: 'olive-600', value: '#6f7a52' },
    { name: 'olive-700', value: '#586043' },
    { name: 'olive-800', value: '#474d37' },
    { name: 'olive-900', value: '#3c4030' },
    
    { name: 'slate-50', value: '#f8f9fa' },
    { name: 'slate-100', value: '#f1f3f4' },
    { name: 'slate-200', value: '#e8eaed' },
    { name: 'slate-300', value: '#dadce0' },
    { name: 'slate-400', value: '#bdc1c6' },
    { name: 'slate-500', value: '#9aa0a6' },
    { name: 'slate-600', value: '#80868b' },
    { name: 'slate-700', value: '#5f6368' },
    { name: 'slate-800', value: '#3c4043' },
    { name: 'slate-900', value: '#202124' },
    
    { name: 'success', value: '#2d5a27' },
    { name: 'success-light', value: '#4a7c59' },
    { name: 'success-bg', value: '#f0f7f0' },
    { name: 'warning', value: '#8b5a00' },
    { name: 'warning-light', value: '#b8770a' },
    { name: 'warning-bg', value: '#fef7e6' },
    { name: 'error', value: '#8b2635' },
    { name: 'error-light', value: '#b8364a' },
    { name: 'error-bg', value: '#fef0f2' },
    { name: 'info-bg', value: '#f5f6f7' }
];

function hexToHsl(hex) {
    const r = parseInt(hex.slice(1, 3), 16) / 255;
    const g = parseInt(hex.slice(3, 5), 16) / 255;
    const b = parseInt(hex.slice(5, 7), 16) / 255;
    
    const max = Math.max(r, g, b);
    const min = Math.min(r, g, b);
    let h, s, l = (max + min) / 2;
    
    if (max === min) {
        h = s = 0; // achromatic
    } else {
        const d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        
        switch (max) {
            case r: h = (g - b) / d + (g < b ? 6 : 0); break;
            case g: h = (b - r) / d + 2; break;
            case b: h = (r - g) / d + 4; break;
        }
        h /= 6;
    }
    
    return {
        h: Math.round(h * 360),
        s: Math.round(s * 100),
        l: Math.round(l * 100)
    };
}

function isPureWhite(color) {
    return color.toLowerCase() === '#ffffff' || color.toLowerCase() === '#fff';
}

function isHighSaturation(hsl) {
    return hsl.s > 70;
}

function isNeonColor(hsl) {
    return hsl.s > 80 && hsl.l > 70;
}

function isWithinNaturalRange(hsl) {
    const naturalPalettes = {
        clay: {
            hueRange: [10, 45],
            saturationMax: 50,
            lightnessRange: [15, 95]
        },
        cream: {
            hueRange: [30, 60],
            saturationMax: 40,
            lightnessRange: [60, 98]
        },
        olive: {
            hueRange: [60, 120],
            saturationMax: 50,
            lightnessRange: [20, 90]
        },
        slate: {
            hueRange: [180, 240],
            saturationMax: 25,
            lightnessRange: [10, 95]
        }
    };
    
    for (const [paletteName, palette] of Object.entries(naturalPalettes)) {
        const hueInRange = hsl.h >= palette.hueRange[0] && hsl.h <= palette.hueRange[1];
        const saturationValid = hsl.s <= palette.saturationMax;
        const lightnessInRange = hsl.l >= palette.lightnessRange[0] && hsl.l <= palette.lightnessRange[1];
        
        if (hueInRange && saturationValid && lightnessInRange) {
            return { valid: true, palette: paletteName };
        }
    }
    
    // Special case for neutral grays (very low saturation)
    if (hsl.s <= 15) {
        return { valid: true, palette: 'neutral' };
    }
    
    // Special case for very light colors (near white/cream) with low saturation
    if (hsl.l >= 90 && hsl.s <= 25) {
        return { valid: true, palette: 'light-neutral' };
    }
    
    // Special case for brown/earth tones that might fall outside strict hue ranges
    if ((hsl.h >= 0 && hsl.h <= 60) && hsl.s <= 50 && hsl.l >= 15 && hsl.l <= 85) {
        return { valid: true, palette: 'earth-tone' };
    }
    
    return { valid: false, palette: null };
}

function validateColorCompliance(colorData) {
    const { name, value } = colorData;
    const results = {
        name,
        value,
        tests: {},
        overall: true,
        issues: []
    };
    
    // Test 1: No pure white (#FFFFFF) - Requirement 3.2
    const pureWhiteTest = !isPureWhite(value);
    results.tests.noPureWhite = pureWhiteTest;
    if (!pureWhiteTest) {
        results.overall = false;
        results.issues.push('Uses pure white (#FFFFFF) - violates Requirement 3.2');
    }
    
    // Convert to HSL for further analysis
    const hsl = hexToHsl(value);
    results.hsl = hsl;
    
    // Test 2: No high saturation colors - Requirement 2.2
    const highSaturationTest = !isHighSaturation(hsl);
    results.tests.noHighSaturation = highSaturationTest;
    if (!highSaturationTest) {
        results.overall = false;
        results.issues.push(`High saturation (${hsl.s}%) - violates Requirement 2.2`);
    }
    
    // Test 3: No neon colors - Requirement 3.4
    const neonTest = !isNeonColor(hsl);
    results.tests.noNeonColors = neonTest;
    if (!neonTest) {
        results.overall = false;
        results.issues.push(`Neon color detected (S:${hsl.s}%, L:${hsl.l}%) - violates Requirement 3.4`);
    }
    
    // Test 4: Within natural earth tone ranges - Requirements 3.1, 3.3
    const naturalRangeResult = isWithinNaturalRange(hsl);
    results.tests.withinNaturalRange = naturalRangeResult.valid;
    results.palette = naturalRangeResult.palette;
    if (!naturalRangeResult.valid) {
        results.overall = false;
        results.issues.push(`Outside natural earth tone ranges (H:${hsl.h}°, S:${hsl.s}%, L:${hsl.l}%) - violates Requirements 3.1, 3.3`);
    }
    
    return results;
}

// Run the property test
console.log('🧪 Running Natural Color Palette Compliance Property Test');
console.log('**Validates: Requirements 2.2, 3.1, 3.2, 3.3, 3.4**\n');

const results = testColors.map(validateColorCompliance);
const passed = results.filter(r => r.overall);
const failed = results.filter(r => !r.overall);

console.log(`📊 Test Results: ${passed.length}/${results.length} colors passed`);

if (failed.length === 0) {
    console.log('\n✅ ALL TESTS PASSED!');
    console.log('All colors comply with natural palette requirements.');
    console.log('Requirements 2.2, 3.1, 3.2, 3.3, 3.4 are satisfied.');
} else {
    console.log('\n❌ SOME TESTS FAILED:');
    failed.forEach(failure => {
        console.log(`\n🔴 ${failure.name} (${failure.value})`);
        console.log(`   HSL: H:${failure.hsl.h}°, S:${failure.hsl.s}%, L:${failure.hsl.l}%`);
        console.log(`   Issues: ${failure.issues.join(', ')}`);
    });
}

// Test breakdown
console.log('\n📈 Test Breakdown:');
const testStats = results.reduce((acc, result) => {
    Object.keys(result.tests).forEach(test => {
        if (!acc[test]) acc[test] = { passed: 0, failed: 0 };
        if (result.tests[test]) {
            acc[test].passed++;
        } else {
            acc[test].failed++;
        }
    });
    return acc;
}, {});

Object.entries(testStats).forEach(([test, stats]) => {
    const total = stats.passed + stats.failed;
    const percentage = Math.round((stats.passed / total) * 100);
    console.log(`   ${test}: ${percentage}% (${stats.passed}/${total})`);
});

// Generate additional test cases for property-based testing
console.log('\n🔄 Generating additional property test cases...');
const additionalTests = [];

// Generate variations of existing colors
for (let i = 0; i < 50; i++) {
    const baseColor = testColors[Math.floor(Math.random() * testColors.length)];
    const baseHsl = hexToHsl(baseColor.value);
    
    // Create slight variations
    const variation = {
        h: Math.max(0, Math.min(360, baseHsl.h + (Math.random() - 0.5) * 20)),
        s: Math.max(0, Math.min(100, baseHsl.s + (Math.random() - 0.5) * 10)),
        l: Math.max(0, Math.min(100, baseHsl.l + (Math.random() - 0.5) * 10))
    };
    
    const hexColor = hslToHex(variation);
    additionalTests.push({
        name: `variation-${i}`,
        value: hexColor
    });
}

function hslToHex(hsl) {
    const h = hsl.h / 360;
    const s = hsl.s / 100;
    const l = hsl.l / 100;
    
    const hue2rgb = (p, q, t) => {
        if (t < 0) t += 1;
        if (t > 1) t -= 1;
        if (t < 1/6) return p + (q - p) * 6 * t;
        if (t < 1/2) return q;
        if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
        return p;
    };
    
    let r, g, b;
    
    if (s === 0) {
        r = g = b = l; // achromatic
    } else {
        const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        const p = 2 * l - q;
        r = hue2rgb(p, q, h + 1/3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1/3);
    }
    
    const toHex = (c) => {
        const hex = Math.round(c * 255).toString(16);
        return hex.length === 1 ? '0' + hex : hex;
    };
    
    return `#${toHex(r)}${toHex(g)}${toHex(b)}`;
}

// Test additional cases
const additionalResults = additionalTests.map(validateColorCompliance);
const additionalPassed = additionalResults.filter(r => r.overall);
const additionalFailed = additionalResults.filter(r => !r.overall);

console.log(`📊 Additional Tests: ${additionalPassed.length}/${additionalResults.length} variations passed`);

const totalTests = results.length + additionalResults.length;
const totalPassed = passed.length + additionalPassed.length;
const totalFailed = failed.length + additionalFailed.length;

console.log(`\n🎯 Final Results: ${totalPassed}/${totalTests} total tests passed (${Math.round((totalPassed/totalTests)*100)}%)`);

if (totalFailed === 0) {
    console.log('\n🎉 PROPERTY TEST PASSED!');
    console.log('Natural Color Palette Compliance property holds for all test cases.');
    console.log('Minimum 100 iterations completed successfully.');
} else {
    console.log(`\n⚠️  PROPERTY TEST FAILED: ${totalFailed} test cases failed`);
    if (additionalFailed.length > 0) {
        console.log('\nSome generated variations failed - this may indicate edge cases in the palette definition.');
    }
}

// Exit with appropriate code
process.exit(totalFailed === 0 ? 0 : 1);