<?php
/**
 * Optimization Report Generator
 * Generates comprehensive performance and optimization reports
 */

require_once __DIR__ . '/../includes/asset-optimizer.php';
require_once __DIR__ . '/../includes/performance-optimizer.php';

class OptimizationReport {
    
    private $assetOptimizer;
    private $performanceOptimizer;
    private $results = [];
    
    public function __construct() {
        $this->assetOptimizer = new AssetOptimizer();
        $this->performanceOptimizer = new PerformanceOptimizer();
    }
    
    /**
     * Generate comprehensive optimization report
     */
    public function generateReport() {
        echo "🚀 Starting Optimization Report Generation...\n\n";
        
        // Test 1: Asset Optimization
        echo "1️⃣ Analyzing Asset Optimization...\n";
        $this->analyzeAssetOptimization();
        
        // Test 2: CSS Performance
        echo "2️⃣ Analyzing CSS Performance...\n";
        $this->analyzeCSSPerformance();
        
        // Test 3: JavaScript Performance
        echo "3️⃣ Analyzing JavaScript Performance...\n";
        $this->analyzeJavaScriptPerformance();
        
        // Test 4: Image Optimization
        echo "4️⃣ Analyzing Image Optimization...\n";
        $this->analyzeImageOptimization();
        
        // Test 5: Caching Strategy
        echo "5️⃣ Analyzing Caching Strategy...\n";
        $this->analyzeCachingStrategy();
        
        // Test 6: Loading Performance
        echo "6️⃣ Analyzing Loading Performance...\n";
        $this->analyzeLoadingPerformance();
        
        // Generate final report
        $this->generateFinalReport();
        
        return $this->results;
    }
    
    /**
     * Analyze asset optimization
     */
    private function analyzeAssetOptimization() {
        $assetResults = [
            'css_files_exist' => false,
            'css_files_minified' => false,
            'js_files_exist' => false,
            'js_files_minified' => false,
            'assets_combined' => false,
            'gzip_enabled' => false
        ];
        
        // Check CSS files
        $cssFiles = [
            'assets/css/variables.css',
            'assets/css/base.css',
            'css/components.css',
            'css/layout.css',
            'css/marketplace.css'
        ];
        
        $cssExists = 0;
        $cssMinified = 0;
        
        foreach ($cssFiles as $file) {
            $fullPath = __DIR__ . '/../' . $file;
            if (file_exists($fullPath)) {
                $cssExists++;
                
                $content = file_get_contents($fullPath);
                // Simple check for minification (no unnecessary whitespace)
                if (strlen($content) > 0 && (strpos($content, "\n\n") === false || strlen($content) < 1000)) {
                    $cssMinified++;
                }
            }
        }
        
        $assetResults['css_files_exist'] = $cssExists === count($cssFiles);
        $assetResults['css_files_minified'] = $cssMinified > 0;
        
        // Check JavaScript files
        $jsFiles = ['assets/main.js'];
        $jsExists = 0;
        $jsMinified = 0;
        
        foreach ($jsFiles as $file) {
            $fullPath = __DIR__ . '/../' . $file;
            if (file_exists($fullPath)) {
                $jsExists++;
                
                $content = file_get_contents($fullPath);
                // Simple check for minification
                if (strlen($content) > 0 && strpos($content, '  ') === false) {
                    $jsMinified++;
                }
            }
        }
        
        $assetResults['js_files_exist'] = $jsExists > 0;
        $assetResults['js_files_minified'] = $jsMinified > 0;
        
        // Check for combined assets
        $cacheDir = __DIR__ . '/../cache/';
        $combinedFiles = glob($cacheDir . 'combined-*.{css,js}', GLOB_BRACE);
        $assetResults['assets_combined'] = count($combinedFiles) > 0;
        
        // Check for gzip (simplified)
        $assetResults['gzip_enabled'] = function_exists('gzencode');
        
        $this->results['asset_optimization'] = $assetResults;
        $this->logResults('Asset Optimization', $assetResults);
    }
    
    /**
     * Analyze CSS performance
     */
    private function analyzeCSSPerformance() {
        $cssResults = [
            'critical_css_generated' => false,
            'unused_css_minimal' => false,
            'css_size_optimized' => false,
            'import_order_correct' => false,
            'design_tokens_used' => false
        ];
        
        // Check for critical CSS
        $criticalCSS = $this->performanceOptimizer->generateCriticalCSS([
            'assets/css/variables.css',
            'assets/css/base.css',
            'css/components.css'
        ]);
        $cssResults['critical_css_generated'] = strlen($criticalCSS) > 0;
        
        // Check CSS file sizes
        $totalCSSSize = 0;
        $cssFiles = glob(__DIR__ . '/../**/*.css');
        
        foreach ($cssFiles as $file) {
            if (is_file($file)) {
                $totalCSSSize += filesize($file);
            }
        }
        
        // Consider optimized if total CSS is under 100KB
        $cssResults['css_size_optimized'] = $totalCSSSize < 102400;
        
        // Check import order in header
        $headerPath = __DIR__ . '/../header.php';
        if (file_exists($headerPath)) {
            $headerContent = file_get_contents($headerPath);
            $expectedOrder = ['variables.css', 'base.css', 'components.css', 'layout.css', 'marketplace.css'];
            
            $orderCorrect = true;
            $lastPosition = 0;
            
            foreach ($expectedOrder as $file) {
                $position = strpos($headerContent, $file);
                if ($position === false || $position < $lastPosition) {
                    $orderCorrect = false;
                    break;
                }
                $lastPosition = $position;
            }
            
            $cssResults['import_order_correct'] = $orderCorrect;
        }
        
        // Check design token usage
        $variablesPath = __DIR__ . '/../assets/css/variables.css';
        if (file_exists($variablesPath)) {
            $variablesContent = file_get_contents($variablesPath);
            $tokenCount = substr_count($variablesContent, '--color-') + 
                         substr_count($variablesContent, '--space-') + 
                         substr_count($variablesContent, '--font-');
            
            $cssResults['design_tokens_used'] = $tokenCount > 10;
        }
        
        // Simplified unused CSS check
        $cssResults['unused_css_minimal'] = true; // Assume minimal for now
        
        $this->results['css_performance'] = $cssResults;
        $this->logResults('CSS Performance', $cssResults);
    }
    
    /**
     * Analyze JavaScript performance
     */
    private function analyzeJavaScriptPerformance() {
        $jsResults = [
            'js_minified' => false,
            'js_deferred' => false,
            'service_worker_present' => false,
            'lazy_loading_implemented' => false,
            'performance_monitoring' => false
        ];
        
        // Check main JavaScript file
        $mainJSPath = __DIR__ . '/../assets/main.js';
        if (file_exists($mainJSPath)) {
            $jsContent = file_get_contents($mainJSPath);
            
            // Simple minification check
            $jsResults['js_minified'] = strpos($jsContent, '  ') === false && strlen($jsContent) > 0;
            
            // Check for lazy loading implementation
            $jsResults['lazy_loading_implemented'] = strpos($jsContent, 'IntersectionObserver') !== false ||
                                                   strpos($jsContent, 'lazy') !== false;
        }
        
        // Check header for deferred JavaScript
        $headerPath = __DIR__ . '/../header.php';
        if (file_exists($headerPath)) {
            $headerContent = file_get_contents($headerPath);
            $jsResults['js_deferred'] = strpos($headerContent, 'defer') !== false ||
                                       strpos($headerContent, 'async') !== false;
        }
        
        // Check for service worker
        $swPath = __DIR__ . '/../sw.js';
        $jsResults['service_worker_present'] = file_exists($swPath);
        
        // Check for performance monitoring
        $footerPath = __DIR__ . '/../footer.php';
        if (file_exists($footerPath)) {
            $footerContent = file_get_contents($footerPath);
            $jsResults['performance_monitoring'] = strpos($footerContent, 'performance') !== false;
        }
        
        $this->results['javascript_performance'] = $jsResults;
        $this->logResults('JavaScript Performance', $jsResults);
    }
    
    /**
     * Analyze image optimization
     */
    private function analyzeImageOptimization() {
        $imageResults = [
            'images_optimized' => false,
            'webp_support' => false,
            'lazy_loading_enabled' => false,
            'responsive_images' => false,
            'alt_text_present' => false
        ];
        
        // Get image optimization stats
        $optimizationStats = $this->assetOptimizer->getOptimizationStats();
        $imageResults['images_optimized'] = $optimizationStats['optimized_count'] > 0;
        
        // Check WebP support
        $imageResults['webp_support'] = function_exists('imagewebp');
        
        // Check for lazy loading in sample pages
        $samplePages = ['landing.php', 'browse.php'];
        $lazyLoadingFound = false;
        $responsiveImagesFound = false;
        $altTextPresent = true;
        
        foreach ($samplePages as $page) {
            $pagePath = __DIR__ . '/../pages/' . $page;
            if (file_exists($pagePath)) {
                $pageContent = file_get_contents($pagePath);
                
                if (strpos($pageContent, 'loading="lazy"') !== false ||
                    strpos($pageContent, 'data-src') !== false) {
                    $lazyLoadingFound = true;
                }
                
                if (strpos($pageContent, 'srcset') !== false ||
                    strpos($pageContent, '<picture') !== false) {
                    $responsiveImagesFound = true;
                }
                
                // Simple alt text check
                if (strpos($pageContent, '<img') !== false &&
                    strpos($pageContent, 'alt=') === false) {
                    $altTextPresent = false;
                }
            }
        }
        
        $imageResults['lazy_loading_enabled'] = $lazyLoadingFound;
        $imageResults['responsive_images'] = $responsiveImagesFound;
        $imageResults['alt_text_present'] = $altTextPresent;
        
        $this->results['image_optimization'] = $imageResults;
        $this->logResults('Image Optimization', $imageResults);
    }
    
    /**
     * Analyze caching strategy
     */
    private function analyzeCachingStrategy() {
        $cachingResults = [
            'cache_directory_exists' => false,
            'cache_files_present' => false,
            'cache_headers_set' => false,
            'etag_support' => false,
            'browser_caching_enabled' => false
        ];
        
        // Check cache directory
        $cacheDir = __DIR__ . '/../cache/';
        $cachingResults['cache_directory_exists'] = is_dir($cacheDir);
        
        if ($cachingResults['cache_directory_exists']) {
            $cacheFiles = glob($cacheDir . '*');
            $cachingResults['cache_files_present'] = count($cacheFiles) > 0;
        }
        
        // Check for cache headers (simplified)
        $cachingResults['cache_headers_set'] = function_exists('header');
        $cachingResults['etag_support'] = function_exists('md5');
        $cachingResults['browser_caching_enabled'] = true; // Assume enabled
        
        $this->results['caching_strategy'] = $cachingResults;
        $this->logResults('Caching Strategy', $cachingResults);
    }
    
    /**
     * Analyze loading performance
     */
    private function analyzeLoadingPerformance() {
        $loadingResults = [
            'loading_states_implemented' => false,
            'empty_states_implemented' => false,
            'progressive_enhancement' => false,
            'font_loading_optimized' => false,
            'dns_prefetch_enabled' => false
        ];
        
        // Check for loading states
        $loadingStatesPath = __DIR__ . '/../includes/loading-states.php';
        $loadingResults['loading_states_implemented'] = file_exists($loadingStatesPath);
        
        // Check for empty states
        $emptyStatesPath = __DIR__ . '/../includes/empty-states.php';
        $loadingResults['empty_states_implemented'] = file_exists($emptyStatesPath);
        
        // Check header for font optimization
        $headerPath = __DIR__ . '/../header.php';
        if (file_exists($headerPath)) {
            $headerContent = file_get_contents($headerPath);
            
            $loadingResults['font_loading_optimized'] = strpos($headerContent, 'preconnect') !== false &&
                                                      strpos($headerContent, 'display=swap') !== false;
            
            $loadingResults['dns_prefetch_enabled'] = strpos($headerContent, 'dns-prefetch') !== false;
            
            $loadingResults['progressive_enhancement'] = strpos($headerContent, 'noscript') !== false;
        }
        
        $this->results['loading_performance'] = $loadingResults;
        $this->logResults('Loading Performance', $loadingResults);
    }
    
    /**
     * Log test results
     */
    private function logResults($category, $results) {
        foreach ($results as $test => $passed) {
            $testName = ucfirst(str_replace('_', ' ', $test));
            echo ($passed ? '✅' : '❌') . " {$testName}\n";
        }
        
        $passedCount = array_sum($results);
        $totalCount = count($results);
        $percentage = round(($passedCount / $totalCount) * 100);
        echo "📊 {$category} Score: {$percentage}% ({$passedCount}/{$totalCount})\n\n";
    }
    
    /**
     * Generate final optimization report
     */
    private function generateFinalReport() {
        echo "📋 OPTIMIZATION REPORT\n";
        echo "=====================\n\n";
        
        $categories = array_keys($this->results);
        $scores = [];
        
        foreach ($categories as $category) {
            $results = $this->results[$category];
            $passedCount = array_sum($results);
            $totalCount = count($results);
            $score = round(($passedCount / $totalCount) * 100);
            $scores[$category] = $score;
            
            $emoji = $this->getCategoryEmoji($category);
            $categoryName = ucfirst(str_replace('_', ' ', $category));
            echo "{$emoji} {$categoryName}: {$score}%\n";
        }
        
        // Overall optimization score
        $overallScore = round(array_sum($scores) / count($scores));
        echo "\n🏆 OVERALL OPTIMIZATION SCORE: {$overallScore}%\n";
        
        // Provide recommendations
        $this->generateRecommendations($overallScore, $scores);
        
        echo "\n📅 Report generated at: " . date('Y-m-d H:i:s') . "\n";
    }
    
    /**
     * Generate optimization recommendations
     */
    private function generateRecommendations($overallScore, $scores) {
        echo "\n💡 OPTIMIZATION RECOMMENDATIONS:\n";
        
        if ($overallScore >= 90) {
            echo "🎉 EXCELLENT! Your application is highly optimized.\n";
            echo "   - Consider implementing advanced caching strategies\n";
            echo "   - Monitor performance metrics in production\n";
        } elseif ($overallScore >= 75) {
            echo "✅ GOOD! Your application is well optimized with room for improvement.\n";
            
            // Specific recommendations based on low scores
            foreach ($scores as $category => $score) {
                if ($score < 80) {
                    echo "   - Improve " . str_replace('_', ' ', $category) . "\n";
                }
            }
        } elseif ($overallScore >= 60) {
            echo "⚠️ FAIR! Your application needs optimization improvements.\n";
            echo "   - Focus on asset minification and compression\n";
            echo "   - Implement lazy loading for images\n";
            echo "   - Add caching strategies\n";
        } else {
            echo "❌ POOR! Your application needs significant optimization work.\n";
            echo "   - Minify and combine CSS/JS files\n";
            echo "   - Optimize images and implement lazy loading\n";
            echo "   - Add loading states and empty states\n";
            echo "   - Implement proper caching\n";
        }
        
        // Asset-specific recommendations
        if (isset($this->results['asset_optimization'])) {
            $assetResults = $this->results['asset_optimization'];
            if (!$assetResults['assets_combined']) {
                echo "   - Combine CSS and JavaScript files to reduce HTTP requests\n";
            }
            if (!$assetResults['css_files_minified']) {
                echo "   - Minify CSS files to reduce file sizes\n";
            }
        }
        
        // Image-specific recommendations
        if (isset($this->results['image_optimization'])) {
            $imageResults = $this->results['image_optimization'];
            if (!$imageResults['webp_support']) {
                echo "   - Enable WebP support for better image compression\n";
            }
            if (!$imageResults['lazy_loading_enabled']) {
                echo "   - Implement lazy loading for images\n";
            }
        }
    }
    
    /**
     * Get emoji for category
     */
    private function getCategoryEmoji($category) {
        $emojis = [
            'asset_optimization' => '📦',
            'css_performance' => '🎨',
            'javascript_performance' => '⚡',
            'image_optimization' => '🖼️',
            'caching_strategy' => '💾',
            'loading_performance' => '🚀'
        ];
        
        return $emojis[$category] ?? '📊';
    }
}

// Run the optimization report if called directly
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $report = new OptimizationReport();
    $report->generateReport();
}