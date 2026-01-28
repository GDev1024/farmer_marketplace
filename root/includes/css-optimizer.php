<?php
/**
 * CSS Optimization Utility
 * Handles CSS minification and caching for production
 */

class CSSOptimizer {
    private $cssPath;
    private $cacheDir;
    private $isDevelopment;
    
    public function __construct($cssPath = 'css/', $cacheDir = 'cache/css/') {
        $this->cssPath = $cssPath;
        $this->cacheDir = $cacheDir;
        $this->isDevelopment = $_ENV['ENVIRONMENT'] ?? 'development' === 'development';
        
        // Create cache directory if it doesn't exist
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    /**
     * Get optimized CSS files with proper caching
     */
    public function getOptimizedCSS($files) {
        if ($this->isDevelopment) {
            return $this->getDevelopmentCSS($files);
        }
        
        return $this->getProductionCSS($files);
    }
    
    /**
     * Development mode - return individual files with cache busting
     */
    private function getDevelopmentCSS($files) {
        $cssLinks = [];
        foreach ($files as $file) {
            $filePath = $this->cssPath . $file;
            if (file_exists($filePath)) {
                $version = filemtime($filePath);
                $cssLinks[] = "<link rel=\"stylesheet\" href=\"{$filePath}?v={$version}\">";
            }
        }
        return implode("\n", $cssLinks);
    }
    
    /**
     * Production mode - return minified and concatenated CSS
     */
    private function getProductionCSS($files) {
        $cacheKey = md5(implode('|', $files));
        $cacheFile = $this->cacheDir . "optimized-{$cacheKey}.css";
        
        // Check if cache is valid
        if ($this->isCacheValid($cacheFile, $files)) {
            $version = filemtime($cacheFile);
            return "<link rel=\"stylesheet\" href=\"{$cacheFile}?v={$version}\">";
        }
        
        // Generate optimized CSS
        $combinedCSS = $this->combineAndMinifyCSS($files);
        file_put_contents($cacheFile, $combinedCSS);
        
        $version = filemtime($cacheFile);
        return "<link rel=\"stylesheet\" href=\"{$cacheFile}?v={$version}\">";
    }
    
    /**
     * Check if cache file is valid (newer than source files)
     */
    private function isCacheValid($cacheFile, $files) {
        if (!file_exists($cacheFile)) {
            return false;
        }
        
        $cacheTime = filemtime($cacheFile);
        foreach ($files as $file) {
            $filePath = $this->cssPath . $file;
            if (file_exists($filePath) && filemtime($filePath) > $cacheTime) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Combine and minify CSS files
     */
    private function combineAndMinifyCSS($files) {
        $combinedCSS = '';
        
        foreach ($files as $file) {
            $filePath = $this->cssPath . $file;
            if (file_exists($filePath)) {
                $css = file_get_contents($filePath);
                $combinedCSS .= $this->minifyCSS($css) . "\n";
            }
        }
        
        return $combinedCSS;
    }
    
    /**
     * Simple CSS minification
     */
    private function minifyCSS($css) {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Remove unnecessary whitespace
        $css = preg_replace('/\s+/', ' ', $css);
        
        // Remove whitespace around specific characters
        $css = preg_replace('/\s*([{}:;,>+~])\s*/', '$1', $css);
        
        // Remove trailing semicolon before closing brace
        $css = preg_replace('/;(?=\s*})/', '', $css);
        
        // Remove leading/trailing whitespace
        $css = trim($css);
        
        return $css;
    }
    
    /**
     * Set appropriate cache headers for CSS files
     */
    public function setCacheHeaders() {
        if (!$this->isDevelopment) {
            // Cache for 1 year in production
            header('Cache-Control: public, max-age=31536000, immutable');
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        } else {
            // No cache in development
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
        }
    }
    
    /**
     * Get critical CSS for above-the-fold content
     */
    public function getCriticalCSS() {
        $criticalCSS = '
        /* Critical CSS - Above the fold styles */
        :root { 
            --color-primary: #3d5a3a;
            --color-primary-light: #5a7456;
            --bg-primary: #ffffff;
            --bg-secondary: #fefdfb;
            --text-primary: #1c1917;
            --font-primary: "Merriweather", Georgia, serif;
            --font-secondary: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            --space-4: 1rem;
            --space-6: 1.5rem;
            --radius-md: 0.5rem;
            --transition-all: all 200ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        body {
            font-family: var(--font-secondary);
            color: var(--text-primary);
            background-color: var(--bg-secondary);
            margin: 0;
            line-height: 1.5;
        }
        
        .page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            background-color: var(--bg-primary);
            border-bottom: 1px solid #e7e5e4;
            position: sticky;
            top: 0;
            z-index: 20;
        }
        
        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: var(--space-4) 0;
        }
        
        .container {
            width: 100%;
            max-width: 80rem;
            margin: 0 auto;
            padding: 0 var(--space-4);
        }
        ';
        
        return $this->minifyCSS($criticalCSS);
    }
}

/**
 * Helper function to get CSS optimizer instance
 */
function getCSSOptimizer() {
    static $optimizer = null;
    if ($optimizer === null) {
        $optimizer = new CSSOptimizer();
    }
    return $optimizer;
}

/**
 * Helper function to output optimized CSS links
 */
function outputOptimizedCSS($files = null) {
    if ($files === null) {
        $files = [
            'variables.css',
            'base.css', 
            'components.css',
            'layout.css',
            'marketplace.css'
        ];
    }
    
    $optimizer = getCSSOptimizer();
    echo $optimizer->getOptimizedCSS($files);
}

/**
 * Helper function to output critical CSS inline
 */
function outputCriticalCSS() {
    $optimizer = getCSSOptimizer();
    echo '<style>' . $optimizer->getCriticalCSS() . '</style>';
}
?>