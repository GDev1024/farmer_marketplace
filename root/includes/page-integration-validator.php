<?php
/**
 * Page Integration Validator
 * Ensures all pages follow consistent structure and design patterns
 */

class PageIntegrationValidator {
    
    private static $requiredCSSFiles = [
        'assets/css/variables.css',
        'assets/css/base.css', 
        'css/components.css',
        'css/layout.css',
        'css/marketplace.css'
    ];
    
    private static $requiredPageStructure = [
        'semantic_html' => ['header', 'main', 'footer'],
        'accessibility' => ['skip-links', 'aria-labels', 'heading-hierarchy'],
        'responsive' => ['viewport-meta', 'mobile-navigation', 'responsive-grid']
    ];
    
    /**
     * Validate CSS file structure and imports
     */
    public static function validateCSSStructure() {
        $results = [];
        
        foreach (self::$requiredCSSFiles as $file) {
            $fullPath = __DIR__ . '/../' . $file;
            $results[$file] = [
                'exists' => file_exists($fullPath),
                'readable' => file_exists($fullPath) && is_readable($fullPath),
                'size' => file_exists($fullPath) ? filesize($fullPath) : 0
            ];
        }
        
        return $results;
    }
    
    /**
     * Validate page file structure
     */
    public static function validatePageStructure() {
        $pagesDir = __DIR__ . '/../pages/';
        $requiredPages = [
            'landing.php', 'home.php', 'browse.php', 'cart.php', 
            'checkout.php', 'messages.php', 'sell.php', 'listing.php',
            'orders.php', 'profile.php', 'login.php', 'register.php',
            'payment-success.php', 'payment-cancel.php'
        ];
        
        $results = [];
        
        foreach ($requiredPages as $page) {
            $fullPath = $pagesDir . $page;
            $results[$page] = [
                'exists' => file_exists($fullPath),
                'readable' => file_exists($fullPath) && is_readable($fullPath),
                'size' => file_exists($fullPath) ? filesize($fullPath) : 0,
                'has_main_tag' => false,
                'has_semantic_structure' => false
            ];
            
            // Check content structure if file exists
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
                $results[$page]['has_main_tag'] = strpos($content, '<main') !== false;
                $results[$page]['has_semantic_structure'] = 
                    strpos($content, 'class="page-main"') !== false ||
                    strpos($content, 'role="main"') !== false;
            }
        }
        
        return $results;
    }
    
    /**
     * Validate navigation consistency across pages
     */
    public static function validateNavigationConsistency() {
        $headerPath = __DIR__ . '/../header.php';
        $footerPath = __DIR__ . '/../footer.php';
        
        $results = [
            'header' => [
                'exists' => file_exists($headerPath),
                'has_nav_brand' => false,
                'has_nav_toggle' => false,
                'has_skip_links' => false,
                'has_aria_labels' => false
            ],
            'footer' => [
                'exists' => file_exists($footerPath),
                'has_semantic_footer' => false,
                'has_consistent_links' => false
            ]
        ];
        
        // Check header structure
        if (file_exists($headerPath)) {
            $headerContent = file_get_contents($headerPath);
            $results['header']['has_nav_brand'] = strpos($headerContent, 'nav-brand') !== false;
            $results['header']['has_nav_toggle'] = strpos($headerContent, 'nav-toggle') !== false;
            $results['header']['has_skip_links'] = strpos($headerContent, 'skip-links') !== false;
            $results['header']['has_aria_labels'] = strpos($headerContent, 'aria-label') !== false;
        }
        
        // Check footer structure
        if (file_exists($footerPath)) {
            $footerContent = file_get_contents($footerPath);
            $results['footer']['has_semantic_footer'] = strpos($footerContent, '<footer') !== false;
            $results['footer']['has_consistent_links'] = strpos($footerContent, 'index.php?page=') !== false;
        }
        
        return $results;
    }
    
    /**
     * Validate accessibility features across pages
     */
    public static function validateAccessibilityFeatures() {
        $results = [
            'global_features' => [
                'skip_links' => false,
                'aria_live_regions' => false,
                'focus_management' => false
            ],
            'form_accessibility' => [],
            'image_accessibility' => []
        ];
        
        // Check header for global accessibility features
        $headerPath = __DIR__ . '/../header.php';
        if (file_exists($headerPath)) {
            $content = file_get_contents($headerPath);
            $results['global_features']['skip_links'] = strpos($content, 'skip-links') !== false;
            $results['global_features']['aria_live_regions'] = strpos($content, 'aria-live') !== false;
            $results['global_features']['focus_management'] = strpos($content, 'focus') !== false;
        }
        
        return $results;
    }
    
    /**
     * Generate integration report
     */
    public static function generateIntegrationReport() {
        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'css_structure' => self::validateCSSStructure(),
            'page_structure' => self::validatePageStructure(),
            'navigation_consistency' => self::validateNavigationConsistency(),
            'accessibility_features' => self::validateAccessibilityFeatures()
        ];
        
        // Calculate overall scores
        $report['scores'] = [
            'css_score' => self::calculateCSSScore($report['css_structure']),
            'page_score' => self::calculatePageScore($report['page_structure']),
            'navigation_score' => self::calculateNavigationScore($report['navigation_consistency']),
            'accessibility_score' => self::calculateAccessibilityScore($report['accessibility_features'])
        ];
        
        $report['overall_score'] = array_sum($report['scores']) / count($report['scores']);
        
        return $report;
    }
    
    private static function calculateCSSScore($cssResults) {
        $total = count($cssResults);
        $passed = 0;
        
        foreach ($cssResults as $result) {
            if ($result['exists'] && $result['readable'] && $result['size'] > 0) {
                $passed++;
            }
        }
        
        return $total > 0 ? ($passed / $total) * 100 : 0;
    }
    
    private static function calculatePageScore($pageResults) {
        $total = count($pageResults);
        $passed = 0;
        
        foreach ($pageResults as $result) {
            if ($result['exists'] && $result['has_main_tag'] && $result['has_semantic_structure']) {
                $passed++;
            }
        }
        
        return $total > 0 ? ($passed / $total) * 100 : 0;
    }
    
    private static function calculateNavigationScore($navResults) {
        $headerScore = 0;
        $footerScore = 0;
        
        if ($navResults['header']['exists']) {
            $headerFeatures = ['has_nav_brand', 'has_nav_toggle', 'has_skip_links', 'has_aria_labels'];
            $headerPassed = 0;
            foreach ($headerFeatures as $feature) {
                if ($navResults['header'][$feature]) $headerPassed++;
            }
            $headerScore = ($headerPassed / count($headerFeatures)) * 100;
        }
        
        if ($navResults['footer']['exists']) {
            $footerFeatures = ['has_semantic_footer', 'has_consistent_links'];
            $footerPassed = 0;
            foreach ($footerFeatures as $feature) {
                if ($navResults['footer'][$feature]) $footerPassed++;
            }
            $footerScore = ($footerPassed / count($footerFeatures)) * 100;
        }
        
        return ($headerScore + $footerScore) / 2;
    }
    
    private static function calculateAccessibilityScore($accessibilityResults) {
        $globalFeatures = $accessibilityResults['global_features'];
        $passed = 0;
        $total = count($globalFeatures);
        
        foreach ($globalFeatures as $feature => $value) {
            if ($value) $passed++;
        }
        
        return $total > 0 ? ($passed / $total) * 100 : 0;
    }
}