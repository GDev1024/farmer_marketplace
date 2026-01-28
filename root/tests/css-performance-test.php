<?php
/**
 * CSS Performance Testing
 * Tests CSS loading performance and optimization effectiveness
 */

require_once '../includes/css-optimizer.php';

class CSSPerformanceTest {
    private $cssFiles = [
        'variables.css',
        'base.css',
        'components.css', 
        'layout.css',
        'marketplace.css'
    ];
    
    private $cssPath = '../css/';
    
    public function runPerformanceTests() {
        echo "<h1>CSS Performance Test Results</h1>\n";
        
        $this->testFileSize();
        $this->testLoadTime();
        $this->testCacheEffectiveness();
        $this->testMinificationEffectiveness();
        $this->testCriticalCSSSize();
    }
    
    /**
     * Test total CSS file size
     */
    private function testFileSize() {
        echo "<h2>File Size Analysis</h2>\n";
        
        $totalSize = 0;
        $fileSizes = [];
        
        foreach ($this->cssFiles as $file) {
            $filePath = $this->cssPath . $file;
            if (file_exists($filePath)) {
                $size = filesize($filePath);
                $totalSize += $size;
                $fileSizes[$file] = $size;
            }
        }
        
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>File</th><th>Size (bytes)</th><th>Size (KB)</th></tr>\n";
        
        foreach ($fileSizes as $file => $size) {
            $sizeKB = round($size / 1024, 2);
            echo "<tr><td>{$file}</td><td>{$size}</td><td>{$sizeKB}</td></tr>\n";
        }
        
        $totalKB = round($totalSize / 1024, 2);
        echo "<tr><td><strong>Total</strong></td><td><strong>{$totalSize}</strong></td><td><strong>{$totalKB}</strong></td></tr>\n";
        echo "</table>\n";
        
        // Performance recommendations
        if ($totalKB > 100) {
            echo "<p style='color: orange;'>⚠️ Warning: Total CSS size is {$totalKB}KB. Consider optimization.</p>\n";
        } else {
            echo "<p style='color: green;'>✅ Good: Total CSS size is {$totalKB}KB.</p>\n";
        }
    }
    
    /**
     * Test CSS load time simulation
     */
    private function testLoadTime() {
        echo "<h2>Load Time Simulation</h2>\n";
        
        $startTime = microtime(true);
        
        // Simulate loading all CSS files
        foreach ($this->cssFiles as $file) {
            $filePath = $this->cssPath . $file;
            if (file_exists($filePath)) {
                file_get_contents($filePath);
            }
        }
        
        $loadTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
        
        echo "<p>Simulated load time: " . round($loadTime, 2) . "ms</p>\n";
        
        if ($loadTime > 100) {
            echo "<p style='color: orange;'>⚠️ Warning: Load time is high. Consider optimization.</p>\n";
        } else {
            echo "<p style='color: green;'>✅ Good: Load time is acceptable.</p>\n";
        }
    }
    
    /**
     * Test cache effectiveness
     */
    private function testCacheEffectiveness() {
        echo "<h2>Cache Effectiveness</h2>\n";
        
        $optimizer = new CSSOptimizer($this->cssPath, '../cache/css/');
        
        // Test development mode
        $_ENV['ENVIRONMENT'] = 'development';
        $devCSS = $optimizer->getOptimizedCSS($this->cssFiles);
        $devRequests = substr_count($devCSS, '<link');
        
        // Test production mode
        $_ENV['ENVIRONMENT'] = 'production';
        $prodCSS = $optimizer->getOptimizedCSS($this->cssFiles);
        $prodRequests = substr_count($prodCSS, '<link');
        
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Mode</th><th>HTTP Requests</th><th>Optimization</th></tr>\n";
        echo "<tr><td>Development</td><td>{$devRequests}</td><td>Individual files with cache busting</td></tr>\n";
        echo "<tr><td>Production</td><td>{$prodRequests}</td><td>Minified and concatenated</td></tr>\n";
        echo "</table>\n";
        
        $reduction = (($devRequests - $prodRequests) / $devRequests) * 100;
        echo "<p style='color: green;'>✅ HTTP requests reduced by " . round($reduction, 1) . "% in production.</p>\n";
    }
    
    /**
     * Test minification effectiveness
     */
    private function testMinificationEffectiveness() {
        echo "<h2>Minification Effectiveness</h2>\n";
        
        $originalSize = 0;
        $minifiedSize = 0;
        
        $optimizer = new CSSOptimizer($this->cssPath);
        
        foreach ($this->cssFiles as $file) {
            $filePath = $this->cssPath . $file;
            if (file_exists($filePath)) {
                $originalCSS = file_get_contents($filePath);
                $originalSize += strlen($originalCSS);
                
                // Use reflection to access private method for testing
                $reflection = new ReflectionClass($optimizer);
                $minifyMethod = $reflection->getMethod('minifyCSS');
                $minifyMethod->setAccessible(true);
                
                $minifiedCSS = $minifyMethod->invoke($optimizer, $originalCSS);
                $minifiedSize += strlen($minifiedCSS);
            }
        }
        
        $reduction = (($originalSize - $minifiedSize) / $originalSize) * 100;
        $savedBytes = $originalSize - $minifiedSize;
        $savedKB = round($savedBytes / 1024, 2);
        
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Metric</th><th>Value</th></tr>\n";
        echo "<tr><td>Original Size</td><td>" . round($originalSize / 1024, 2) . " KB</td></tr>\n";
        echo "<tr><td>Minified Size</td><td>" . round($minifiedSize / 1024, 2) . " KB</td></tr>\n";
        echo "<tr><td>Bytes Saved</td><td>{$savedBytes} bytes ({$savedKB} KB)</td></tr>\n";
        echo "<tr><td>Reduction</td><td>" . round($reduction, 1) . "%</td></tr>\n";
        echo "</table>\n";
        
        if ($reduction > 20) {
            echo "<p style='color: green;'>✅ Excellent: Minification saves " . round($reduction, 1) . "% of file size.</p>\n";
        } else {
            echo "<p style='color: orange;'>⚠️ Moderate: Minification saves " . round($reduction, 1) . "% of file size.</p>\n";
        }
    }
    
    /**
     * Test critical CSS size
     */
    private function testCriticalCSSSize() {
        echo "<h2>Critical CSS Analysis</h2>\n";
        
        $optimizer = new CSSOptimizer();
        $criticalCSS = $optimizer->getCriticalCSS();
        $criticalSize = strlen($criticalCSS);
        $criticalKB = round($criticalSize / 1024, 2);
        
        echo "<p>Critical CSS size: {$criticalSize} bytes ({$criticalKB} KB)</p>\n";
        
        if ($criticalKB < 14) {
            echo "<p style='color: green;'>✅ Excellent: Critical CSS is under 14KB (recommended for above-the-fold).</p>\n";
        } else {
            echo "<p style='color: orange;'>⚠️ Warning: Critical CSS is over 14KB. Consider reducing for better performance.</p>\n";
        }
        
        echo "<details><summary>Critical CSS Content</summary><pre>" . htmlspecialchars($criticalCSS) . "</pre></details>\n";
    }
}

// Run the performance tests
$tester = new CSSPerformanceTest();
$tester->runPerformanceTests();
?>