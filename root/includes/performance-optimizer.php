<?php
/**
 * Performance Optimizer
 * Handles CSS/JS minification, caching, and performance optimizations
 */

class PerformanceOptimizer {
    
    private $cacheDir;
    private $assetsDir;
    
    public function __construct() {
        $this->cacheDir = __DIR__ . '/../cache/';
        $this->assetsDir = __DIR__ . '/../';
        
        // Create cache directory if it doesn't exist
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    /**
     * Minify CSS content
     */
    public function minifyCSS($css) {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Remove unnecessary whitespace
        $css = str_replace(["\r\n", "\r", "\n", "\t"], '', $css);
        $css = preg_replace('/\s+/', ' ', $css);
        
        // Remove whitespace around specific characters
        $css = str_replace([' {', '{ ', ' }', '} ', '; ', ' ;', ': ', ' :', ', ', ' ,'], 
                          ['{', '{', '}', '}', ';', ';', ':', ':', ',', ','], $css);
        
        // Remove trailing semicolon before closing brace
        $css = str_replace(';}', '}', $css);
        
        return trim($css);
    }
    
    /**
     * Minify JavaScript content
     */
    public function minifyJS($js) {
        // Remove single-line comments (but preserve URLs)
        $js = preg_replace('/(?<!:)\/\/.*$/m', '', $js);
        
        // Remove multi-line comments
        $js = preg_replace('/\/\*[\s\S]*?\*\//', '', $js);
        
        // Remove unnecessary whitespace
        $js = preg_replace('/\s+/', ' ', $js);
        
        // Remove whitespace around operators and punctuation
        $js = preg_replace('/\s*([{}();,:])\s*/', '$1', $js);
        
        return trim($js);
    }
    
    /**
     * Combine and minify CSS files
     */
    public function combineCSS($files, $outputFile = null) {
        $combinedCSS = '';
        $lastModified = 0;
        
        foreach ($files as $file) {
            $fullPath = $this->assetsDir . $file;
            
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
                $combinedCSS .= "/* {$file} */\n" . $content . "\n\n";
                $lastModified = max($lastModified, filemtime($fullPath));
            }
        }
        
        // Minify combined CSS
        $minifiedCSS = $this->minifyCSS($combinedCSS);
        
        // Generate cache filename if not provided
        if (!$outputFile) {
            $hash = md5(implode('|', $files) . $lastModified);
            $outputFile = "combined-{$hash}.css";
        }
        
        $outputPath = $this->cacheDir . $outputFile;
        
        // Write to cache if it doesn't exist or is outdated
        if (!file_exists($outputPath) || filemtime($outputPath) < $lastModified) {
            file_put_contents($outputPath, $minifiedCSS);
        }
        
        return str_replace($this->assetsDir, '', $outputPath);
    }
    
    /**
     * Combine and minify JavaScript files
     */
    public function combineJS($files, $outputFile = null) {
        $combinedJS = '';
        $lastModified = 0;
        
        foreach ($files as $file) {
            $fullPath = $this->assetsDir . $file;
            
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
                $combinedJS .= "/* {$file} */\n" . $content . ";\n\n";
                $lastModified = max($lastModified, filemtime($fullPath));
            }
        }
        
        // Minify combined JavaScript
        $minifiedJS = $this->minifyJS($combinedJS);
        
        // Generate cache filename if not provided
        if (!$outputFile) {
            $hash = md5(implode('|', $files) . $lastModified);
            $outputFile = "combined-{$hash}.js";
        }
        
        $outputPath = $this->cacheDir . $outputFile;
        
        // Write to cache if it doesn't exist or is outdated
        if (!file_exists($outputPath) || filemtime($outputPath) < $lastModified) {
            file_put_contents($outputPath, $minifiedJS);
        }
        
        return str_replace($this->assetsDir, '', $outputPath);
    }
    
    /**
     * Generate critical CSS for above-the-fold content
     */
    public function generateCriticalCSS($cssFiles, $criticalSelectors = []) {
        $criticalCSS = '';
        
        // Default critical selectors
        $defaultCriticalSelectors = [
            'body', 'html', '.page', '.header', '.nav', '.nav-brand', '.nav-links',
            '.hero', '.hero-content', '.hero-title', '.btn', '.btn-primary',
            '.container', '.page-main', '.skip-links', '.sr-only'
        ];
        
        $allCriticalSelectors = array_merge($defaultCriticalSelectors, $criticalSelectors);
        
        foreach ($cssFiles as $file) {
            $fullPath = $this->assetsDir . $file;
            
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
                
                // Extract rules for critical selectors
                foreach ($allCriticalSelectors as $selector) {
                    $pattern = '/(' . preg_quote($selector, '/') . '[^{]*\{[^}]*\})/';
                    if (preg_match_all($pattern, $content, $matches)) {
                        foreach ($matches[1] as $rule) {
                            $criticalCSS .= $rule . "\n";
                        }
                    }
                }
            }
        }
        
        return $this->minifyCSS($criticalCSS);
    }
    
    /**
     * Generate optimized HTML with inlined critical CSS
     */
    public function optimizeHTML($html, $criticalCSS = '') {
        // Inline critical CSS
        if ($criticalCSS) {
            $criticalStyle = "<style>{$criticalCSS}</style>";
            $html = str_replace('</head>', $criticalStyle . '</head>', $html);
        }
        
        // Add preload hints for fonts
        $fontPreloads = '
        <link rel="preload" href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&display=swap" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">
        <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&display=swap"></noscript>';
        
        $html = str_replace('</head>', $fontPreloads . '</head>', $html);
        
        // Add DNS prefetch for external resources
        $dnsPrefetch = '
        <link rel="dns-prefetch" href="//fonts.googleapis.com">
        <link rel="dns-prefetch" href="//fonts.gstatic.com">';
        
        $html = str_replace('</head>', $dnsPrefetch . '</head>', $html);
        
        return $html;
    }
    
    /**
     * Generate service worker for caching
     */
    public function generateServiceWorker($cacheVersion = '1.0.0') {
        $serviceWorker = "
const CACHE_NAME = 'grenada-farmers-v{$cacheVersion}';
const urlsToCache = [
    '/',
    '/assets/css/variables.css',
    '/assets/css/base.css',
    '/css/components.css',
    '/css/layout.css',
    '/css/marketplace.css',
    '/assets/main.js',
    'https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&display=swap'
];

self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                return cache.addAll(urlsToCache);
            })
    );
});

self.addEventListener('fetch', function(event) {
    event.respondWith(
        caches.match(event.request)
            .then(function(response) {
                // Return cached version or fetch from network
                return response || fetch(event.request);
            }
        )
    );
});

self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});";
        
        file_put_contents($this->assetsDir . 'sw.js', $serviceWorker);
        
        return 'sw.js';
    }
    
    /**
     * Generate performance monitoring script
     */
    public function getPerformanceMonitoringScript() {
        return "
        <script>
        // Performance Monitoring
        window.addEventListener('load', function() {
            // Measure page load performance
            if ('performance' in window) {
                const perfData = performance.getEntriesByType('navigation')[0];
                
                if (perfData) {
                    const loadTime = perfData.loadEventEnd - perfData.loadEventStart;
                    const domContentLoaded = perfData.domContentLoadedEventEnd - perfData.domContentLoadedEventStart;
                    const firstPaint = performance.getEntriesByType('paint').find(entry => entry.name === 'first-paint');
                    
                    // Log performance metrics (in production, send to analytics)
                    console.log('Performance Metrics:', {
                        loadTime: loadTime + 'ms',
                        domContentLoaded: domContentLoaded + 'ms',
                        firstPaint: firstPaint ? firstPaint.startTime + 'ms' : 'N/A'
                    });
                    
                    // Announce slow loading to screen readers
                    if (loadTime > 3000) {
                        const announcement = document.getElementById('loading-announcements');
                        if (announcement) {
                            announcement.textContent = 'Page loaded. Connection may be slow.';
                        }
                    }
                }
            }
            
            // Register service worker
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('Service Worker registered successfully');
                    })
                    .catch(function(error) {
                        console.log('Service Worker registration failed');
                    });
            }
        });
        </script>";
    }
    
    /**
     * Clean up old cache files
     */
    public function cleanupCache($maxAge = 604800) { // 7 days
        $files = glob($this->cacheDir . '*');
        $cleaned = 0;
        
        foreach ($files as $file) {
            if (is_file($file) && (time() - filemtime($file)) > $maxAge) {
                unlink($file);
                $cleaned++;
            }
        }
        
        return $cleaned;
    }
    
    /**
     * Get cache statistics
     */
    public function getCacheStats() {
        $files = glob($this->cacheDir . '*');
        $totalSize = 0;
        $fileCount = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $totalSize += filesize($file);
                $fileCount++;
            }
        }
        
        return [
            'file_count' => $fileCount,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize)
        ];
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Generate optimized asset loading HTML
     */
    public function generateOptimizedAssetLoading($cssFiles, $jsFiles = []) {
        $html = '';
        
        // Combine and minify CSS
        $combinedCSS = $this->combineCSS($cssFiles);
        $html .= "<link rel=\"stylesheet\" href=\"{$combinedCSS}\">\n";
        
        // Generate critical CSS
        $criticalCSS = $this->generateCriticalCSS($cssFiles);
        if ($criticalCSS) {
            $html .= "<style>{$criticalCSS}</style>\n";
        }
        
        // Combine and minify JavaScript (load at end of body)
        if (!empty($jsFiles)) {
            $combinedJS = $this->combineJS($jsFiles);
            $html .= "<script src=\"{$combinedJS}\" defer></script>\n";
        }
        
        return $html;
    }
}