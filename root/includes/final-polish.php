<?php
/**
 * Final Polish
 * Applies final optimizations and enhancements to the application
 */

require_once 'loading-states.php';
require_once 'empty-states.php';
require_once 'asset-optimizer.php';
require_once 'performance-optimizer.php';

class FinalPolish {
    
    /**
     * Apply all final polish optimizations
     */
    public static function applyAll() {
        self::optimizeAssets();
        self::setupPerformanceHeaders();
        self::createOptimizedAssets();
        self::generateServiceWorker();
        self::setupErrorHandling();
        self::createMaintenanceMode();
        self::optimizeImages();
        self::setupAsyncLoadingStates();
    }
    
    /**
     * Optimize images in uploads directory
     */
    public static function optimizeImages() {
        $assetOptimizer = new AssetOptimizer();
        $uploadsDir = __DIR__ . '/../uploads/';
        
        if (is_dir($uploadsDir)) {
            $results = $assetOptimizer->optimizeDirectory($uploadsDir);
            error_log("Image optimization completed: {$results['optimized_count']} images optimized, {$results['total_savings_formatted']} saved");
        }
    }
    
    /**
     * Setup async loading states for better UX
     */
    public static function setupAsyncLoadingStates() {
        // Create loading state CSS if it doesn't exist
        $loadingCSS = __DIR__ . '/../assets/css/loading-states.css';
        
        if (!file_exists($loadingCSS)) {
            $css = '
/* Loading States CSS */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(2px);
}

.loading-content {
    text-align: center;
    color: white;
    max-width: 300px;
    padding: 2rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    backdrop-filter: blur(10px);
}

.spinner {
    width: 40px;
    height: 40px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-top: 3px solid #3d5a3a;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.async-operation .loading-content {
    background: white;
    color: #333;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.async-operation .spinner {
    border-color: rgba(0,0,0,0.1);
    border-top-color: #3d5a3a;
}

.loading-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #3d5a3a;
}

.loading-security-note {
    margin-top: 1rem;
    padding: 0.75rem;
    background: rgba(61, 90, 58, 0.1);
    border-radius: 4px;
    font-size: 0.875rem;
    color: #2d4429;
}
';
            
            file_put_contents($loadingCSS, $css);
        }
    }
    
    /**
     * Optimize all assets
     */
    public static function optimizeAssets() {
        // Combine and minify CSS files
        $cssFiles = [
            __DIR__ . '/../assets/css/variables.css',
            __DIR__ . '/../assets/css/base.css',
            __DIR__ . '/../css/components.css',
            __DIR__ . '/../css/layout.css',
            __DIR__ . '/../css/marketplace.css'
        ];
        
        $combinedCSSPath = __DIR__ . '/../assets/css/combined.min.css';
        PerformanceOptimizer::combineCSSFiles($cssFiles, $combinedCSSPath);
        
        // Combine and minify JavaScript files
        $jsFiles = [
            __DIR__ . '/../assets/main.js',
            __DIR__ . '/../assets/loading-enhancements.js'
        ];
        
        $combinedJSPath = __DIR__ . '/../assets/js/combined.min.js';
        PerformanceOptimizer::combineJSFiles($jsFiles, $combinedJSPath);
        
        // Optimize images in uploads directory
        $uploadsDir = __DIR__ . '/../uploads/';
        if (is_dir($uploadsDir)) {
            PerformanceOptimizer::optimizeImagesInDirectory($uploadsDir);
        }
    }
    
    /**
     * Setup performance headers
     */
    public static function setupPerformanceHeaders() {
        // Enable compression
        PerformanceOptimizer::enableCompression();
        
        // Set appropriate cache headers
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|webp|woff|woff2)$/i', $requestUri)) {
            PerformanceOptimizer::setCacheHeaders('static');
        } else {
            PerformanceOptimizer::setCacheHeaders('dynamic');
        }
        
        // Preload critical resources
        $criticalResources = [
            ['url' => 'assets/css/variables.css', 'as' => 'style'],
            ['url' => 'assets/css/base.css', 'as' => 'style'],
            ['url' => 'https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&display=swap', 'as' => 'style']
        ];
        
        PerformanceOptimizer::generatePreloadHeaders($criticalResources);
    }
    
    /**
     * Create optimized asset versions
     */
    public static function createOptimizedAssets() {
        // Generate critical CSS
        $cssFiles = [
            __DIR__ . '/../assets/css/variables.css',
            __DIR__ . '/../assets/css/base.css',
            __DIR__ . '/../css/layout.css'
        ];
        
        $criticalSelectors = [
            'body', 'header', 'nav', 'main', '.page', '.page-main',
            '.container', '.btn', '.btn-primary', '.btn-secondary',
            '.nav-brand', '.nav-toggle', '.nav-links', '.hero'
        ];
        
        $criticalCSS = PerformanceOptimizer::generateCriticalCSS($cssFiles, $criticalSelectors);
        
        // Save critical CSS
        $criticalCSSPath = __DIR__ . '/../assets/css/critical.min.css';
        file_put_contents($criticalCSSPath, $criticalCSS);
        
        // Generate .htaccess rules
        $htaccessRules = PerformanceOptimizer::generateHtaccessRules();
        $htaccessPath = __DIR__ . '/../.htaccess';
        
        // Append rules if .htaccess exists, otherwise create it
        if (file_exists($htaccessPath)) {
            $existingContent = file_get_contents($htaccessPath);
            if (strpos($existingContent, 'Enable compression') === false) {
                file_put_contents($htaccessPath, $existingContent . "\n" . $htaccessRules, LOCK_EX);
            }
        } else {
            file_put_contents($htaccessPath, $htaccessRules);
        }
    }
    
    /**
     * Generate service worker for offline functionality
     */
    public static function generateServiceWorker() {
        $serviceWorkerContent = "
const CACHE_NAME = 'grenada-farmers-v1';
const urlsToCache = [
    '/',
    '/assets/css/variables.css',
    '/assets/css/base.css',
    '/css/components.css',
    '/css/layout.css',
    '/css/marketplace.css',
    '/assets/main.js',
    '/assets/loading-enhancements.js',
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

// Clean up old caches
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
});
";
        
        $serviceWorkerPath = __DIR__ . '/../sw.js';
        file_put_contents($serviceWorkerPath, $serviceWorkerContent);
    }
    
    /**
     * Setup enhanced error handling
     */
    public static function setupErrorHandling() {
        // Create error handler
        set_error_handler(function($severity, $message, $file, $line) {
            if (!(error_reporting() & $severity)) {
                return false;
            }
            
            $errorLog = [
                'timestamp' => date('Y-m-d H:i:s'),
                'severity' => $severity,
                'message' => $message,
                'file' => $file,
                'line' => $line,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
            ];
            
            // Log to file
            $logFile = __DIR__ . '/../logs/error.log';
            $logDir = dirname($logFile);
            
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            
            file_put_contents($logFile, json_encode($errorLog) . "\n", FILE_APPEND | LOCK_EX);
            
            return true;
        });
        
        // Create exception handler
        set_exception_handler(function($exception) {
            $errorLog = [
                'timestamp' => date('Y-m-d H:i:s'),
                'type' => 'exception',
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
            ];
            
            // Log to file
            $logFile = __DIR__ . '/../logs/error.log';
            $logDir = dirname($logFile);
            
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            
            file_put_contents($logFile, json_encode($errorLog) . "\n", FILE_APPEND | LOCK_EX);
            
            // Show user-friendly error page
            if (!headers_sent()) {
                http_response_code(500);
                include __DIR__ . '/../pages/error.php';
            }
        });
    }
    
    /**
     * Create maintenance mode functionality
     */
    public static function createMaintenanceMode() {
        $maintenanceFile = __DIR__ . '/../maintenance.php';
        
        $maintenanceContent = '<?php
/**
 * Maintenance Mode
 * Display maintenance page when site is under maintenance
 */

// Check if maintenance mode is enabled
$maintenanceFlag = __DIR__ . "/maintenance.flag";

if (file_exists($maintenanceFlag)) {
    http_response_code(503);
    header("Retry-After: 3600"); // 1 hour
    
    $maintenanceMessage = file_get_contents($maintenanceFlag);
    if (empty($maintenanceMessage)) {
        $maintenanceMessage = "We are currently performing scheduled maintenance. Please check back soon.";
    }
    
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Maintenance - Grenada Farmers Marketplace</title>
        <link rel="stylesheet" href="assets/css/variables.css">
        <link rel="stylesheet" href="assets/css/base.css">
        <link rel="stylesheet" href="css/components.css">
        <style>
            body {
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                margin: 0;
                background: var(--color-gray-50);
            }
            .maintenance-container {
                text-align: center;
                max-width: 500px;
                padding: var(--space-8);
                background: white;
                border-radius: var(--border-radius-lg);
                box-shadow: var(--shadow-lg);
            }
            .maintenance-icon {
                font-size: 4rem;
                margin-bottom: var(--space-4);
            }
            .maintenance-title {
                font-size: 2rem;
                font-weight: 700;
                color: var(--color-text);
                margin-bottom: var(--space-4);
            }
            .maintenance-message {
                font-size: 1.125rem;
                color: var(--color-text-muted);
                line-height: 1.6;
                margin-bottom: var(--space-6);
            }
            .maintenance-eta {
                font-size: 0.875rem;
                color: var(--color-primary);
                font-weight: 500;
            }
        </style>
    </head>
    <body>
        <div class="maintenance-container">
            <div class="maintenance-icon">🔧</div>
            <h1 class="maintenance-title">Under Maintenance</h1>
            <p class="maintenance-message"><?= htmlspecialchars($maintenanceMessage) ?></p>
            <p class="maintenance-eta">Expected completion: Within 1 hour</p>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>';
        
        file_put_contents($maintenanceFile, $maintenanceContent);
    }
    
    /**
     * Generate performance report
     */
    public static function generateReport() {
        $report = PerformanceOptimizer::generatePerformanceReport();
        
        // Add polish-specific metrics
        $report['polish_status'] = [
            'loading_states' => class_exists('LoadingStates'),
            'empty_states' => class_exists('EmptyStates'),
            'asset_optimizer' => class_exists('AssetOptimizer'),
            'performance_optimizer' => class_exists('PerformanceOptimizer'),
            'service_worker' => file_exists(__DIR__ . '/../sw.js'),
            'htaccess_rules' => file_exists(__DIR__ . '/../.htaccess'),
            'error_handling' => is_dir(__DIR__ . '/../logs'),
            'maintenance_mode' => file_exists(__DIR__ . '/../maintenance.php')
        ];
        
        // Calculate polish score
        $polishFeatures = $report['polish_status'];
        $polishScore = (array_sum($polishFeatures) / count($polishFeatures)) * 100;
        $report['polish_score'] = $polishScore;
        
        return $report;
    }
    
    /**
     * Clean up temporary files and optimize storage
     */
    public static function cleanup() {
        // Clear expired cache
        $deletedCache = PerformanceOptimizer::clearExpiredCache();
        
        // Clean up old log files (keep last 30 days)
        $logDir = __DIR__ . '/../logs/';
        $deletedLogs = 0;
        
        if (is_dir($logDir)) {
            $logFiles = glob($logDir . '*.log');
            $cutoffTime = time() - (30 * 24 * 60 * 60); // 30 days
            
            foreach ($logFiles as $logFile) {
                if (filemtime($logFile) < $cutoffTime) {
                    unlink($logFile);
                    $deletedLogs++;
                }
            }
        }
        
        // Clean up old image backups
        $uploadsDir = __DIR__ . '/../uploads/';
        $deletedBackups = 0;
        
        if (is_dir($uploadsDir)) {
            $backupFiles = glob($uploadsDir . '**/*.backup', GLOB_BRACE);
            
            foreach ($backupFiles as $backupFile) {
                if (filemtime($backupFile) < $cutoffTime) {
                    unlink($backupFile);
                    $deletedBackups++;
                }
            }
        }
        
        return [
            'deleted_cache' => $deletedCache,
            'deleted_logs' => $deletedLogs,
            'deleted_backups' => $deletedBackups
        ];
    }
}

// Auto-apply optimizations if called directly
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    FinalPolish::applyAll();
    echo "Final polish optimizations applied successfully!\n";
    
    $report = FinalPolish::generateReport();
    echo "Polish Score: " . $report['polish_score'] . "%\n";
}