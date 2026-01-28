<?php
/**
 * Visual Continuity Checker
 * Validates design system implementation across all pages
 */

class VisualContinuityChecker {
    
    private $pagesDir;
    private $cssDir;
    private $results = [];
    
    public function __construct() {
        $this->pagesDir = __DIR__ . '/../pages/';
        $this->cssDir = __DIR__ . '/../';
    }
    
    /**
     * Run comprehensive visual continuity check
     */
    public function runCheck() {
        echo "🎨 Starting Visual Continuity Check...\n\n";
        
        // Check 1: CSS File Structure
        echo "1️⃣ Checking CSS File Structure...\n";
        $this->checkCSSStructure();
        
        // Check 2: Design Token Usage
        echo "2️⃣ Checking Design Token Usage...\n";
        $this->checkDesignTokenUsage();
        
        // Check 3: Component Consistency
        echo "3️⃣ Checking Component Consistency...\n";
        $this->checkComponentConsistency();
        
        // Check 4: Navigation Consistency
        echo "4️⃣ Checking Navigation Consistency...\n";
        $this->checkNavigationConsistency();
        
        // Check 5: Typography Consistency
        echo "5️⃣ Checking Typography Consistency...\n";
        $this->checkTypographyConsistency();
        
        // Check 6: Semantic Structure
        echo "6️⃣ Checking Semantic Structure...\n";
        $this->checkSemanticStructure();
        
        // Generate final report
        $this->generateReport();
        
        return $this->results;
    }
    
    /**
     * Check CSS file structure and import order
     */
    private function checkCSSStructure() {
        $requiredFiles = [
            'assets/css/variables.css' => 'Design tokens and CSS custom properties',
            'assets/css/base.css' => 'Base styles and typography',
            'css/components.css' => 'Reusable UI components',
            'css/layout.css' => 'Page structure and navigation',
            'css/marketplace.css' => 'Application-specific styles'
        ];
        
        $cssResults = [];
        
        foreach ($requiredFiles as $file => $description) {
            $fullPath = $this->cssDir . $file;
            $exists = file_exists($fullPath);
            $size = $exists ? filesize($fullPath) : 0;
            $readable = $exists && is_readable($fullPath);
            
            $cssResults[$file] = [
                'exists' => $exists,
                'readable' => $readable,
                'size' => $size,
                'description' => $description,
                'valid' => $exists && $readable && $size > 0
            ];
            
            if ($cssResults[$file]['valid']) {
                echo "✅ {$file} - Valid ({$size} bytes)\n";
            } else {
                echo "❌ {$file} - Issues found\n";
                if (!$exists) echo "   - File missing\n";
                if (!$readable) echo "   - File not readable\n";
                if ($size === 0) echo "   - File empty\n";
            }
        }
        
        // Check import order in header.php
        $headerPath = $this->cssDir . 'header.php';
        if (file_exists($headerPath)) {
            $headerContent = file_get_contents($headerPath);
            $importOrder = $this->checkCSSImportOrder($headerContent);
            $cssResults['import_order'] = $importOrder;
            
            if ($importOrder['correct']) {
                echo "✅ CSS import order is correct\n";
            } else {
                echo "❌ CSS import order issues found\n";
                foreach ($importOrder['issues'] as $issue) {
                    echo "   - {$issue}\n";
                }
            }
        }
        
        $this->results['css_structure'] = $cssResults;
        echo "\n";
    }
    
    /**
     * Check CSS import order
     */
    private function checkCSSImportOrder($content) {
        $expectedOrder = [
            'variables.css',
            'base.css', 
            'components.css',
            'layout.css',
            'marketplace.css'
        ];
        
        $issues = [];
        $correct = true;
        
        // Extract CSS link tags
        preg_match_all('/<link[^>]*href="[^"]*\.css"[^>]*>/i', $content, $matches);
        $cssLinks = $matches[0];
        
        $foundOrder = [];
        foreach ($cssLinks as $link) {
            foreach ($expectedOrder as $file) {
                if (strpos($link, $file) !== false) {
                    $foundOrder[] = $file;
                    break;
                }
            }
        }
        
        // Check if order matches expected
        for ($i = 0; $i < count($foundOrder); $i++) {
            if (!isset($expectedOrder[$i]) || $foundOrder[$i] !== $expectedOrder[$i]) {
                $correct = false;
                $issues[] = "Expected {$expectedOrder[$i]} at position " . ($i + 1) . ", found {$foundOrder[$i]}";
            }
        }
        
        return [
            'correct' => $correct,
            'expected' => $expectedOrder,
            'found' => $foundOrder,
            'issues' => $issues
        ];
    }
    
    /**
     * Check design token usage across CSS files
     */
    private function checkDesignTokenUsage() {
        $variablesFile = $this->cssDir . 'assets/css/variables.css';
        
        if (!file_exists($variablesFile)) {
            echo "❌ Variables file not found\n\n";
            return;
        }
        
        $variablesContent = file_get_contents($variablesFile);
        
        // Extract CSS custom properties
        preg_match_all('/--([a-zA-Z0-9-]+):\s*([^;]+);/', $variablesContent, $matches);
        $definedTokens = array_combine($matches[1], $matches[2]);
        
        $tokenResults = [
            'defined_tokens' => count($definedTokens),
            'color_tokens' => 0,
            'spacing_tokens' => 0,
            'typography_tokens' => 0,
            'usage_validation' => []
        ];
        
        // Categorize tokens
        foreach ($definedTokens as $token => $value) {
            if (strpos($token, 'color') !== false) {
                $tokenResults['color_tokens']++;
            } elseif (strpos($token, 'space') !== false) {
                $tokenResults['spacing_tokens']++;
            } elseif (strpos($token, 'font') !== false || strpos($token, 'text') !== false) {
                $tokenResults['typography_tokens']++;
            }
        }
        
        echo "✅ Found {$tokenResults['defined_tokens']} design tokens\n";
        echo "   - {$tokenResults['color_tokens']} color tokens\n";
        echo "   - {$tokenResults['spacing_tokens']} spacing tokens\n";
        echo "   - {$tokenResults['typography_tokens']} typography tokens\n";
        
        // Check token usage in other CSS files
        $cssFiles = ['css/components.css', 'css/layout.css', 'css/marketplace.css'];
        
        foreach ($cssFiles as $file) {
            $fullPath = $this->cssDir . $file;
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
                $tokenUsage = substr_count($content, 'var(--');
                $tokenResults['usage_validation'][$file] = $tokenUsage;
                
                if ($tokenUsage > 0) {
                    echo "✅ {$file} uses {$tokenUsage} design tokens\n";
                } else {
                    echo "⚠️ {$file} doesn't use design tokens\n";
                }
            }
        }
        
        $this->results['design_tokens'] = $tokenResults;
        echo "\n";
    }
    
    /**
     * Check component consistency across pages
     */
    private function checkComponentConsistency() {
        $componentPatterns = [
            'nav-brand' => 'Navigation brand component',
            'nav-toggle' => 'Mobile navigation toggle',
            'btn' => 'Button components',
            'card' => 'Card components',
            'form-group' => 'Form components',
            'hero' => 'Hero section component',
            'footer' => 'Footer component'
        ];
        
        $componentResults = [];
        
        // Check header.php for global components
        $headerPath = $this->cssDir . 'header.php';
        if (file_exists($headerPath)) {
            $headerContent = file_get_contents($headerPath);
            
            foreach ($componentPatterns as $pattern => $description) {
                $found = strpos($headerContent, $pattern) !== false;
                $componentResults['header'][$pattern] = $found;
                
                if ($found) {
                    echo "✅ Header contains {$description}\n";
                } else {
                    echo "❌ Header missing {$description}\n";
                }
            }
        }
        
        // Check footer.php
        $footerPath = $this->cssDir . 'footer.php';
        if (file_exists($footerPath)) {
            $footerContent = file_get_contents($footerPath);
            
            $footerComponents = ['footer', 'footer-content', 'footer-section'];
            foreach ($footerComponents as $component) {
                $found = strpos($footerContent, $component) !== false;
                $componentResults['footer'][$component] = $found;
                
                if ($found) {
                    echo "✅ Footer contains {$component}\n";
                } else {
                    echo "❌ Footer missing {$component}\n";
                }
            }
        }
        
        // Check sample pages for component usage
        $samplePages = ['landing.php', 'home.php', 'browse.php', 'cart.php'];
        
        foreach ($samplePages as $page) {
            $pagePath = $this->pagesDir . $page;
            if (file_exists($pagePath)) {
                $pageContent = file_get_contents($pagePath);
                $componentResults['pages'][$page] = [];
                
                foreach ($componentPatterns as $pattern => $description) {
                    $found = strpos($pageContent, $pattern) !== false;
                    $componentResults['pages'][$page][$pattern] = $found;
                }
            }
        }
        
        $this->results['component_consistency'] = $componentResults;
        echo "\n";
    }
    
    /**
     * Check navigation consistency
     */
    private function checkNavigationConsistency() {
        $navResults = [
            'header_structure' => false,
            'nav_brand_present' => false,
            'nav_toggle_present' => false,
            'skip_links_present' => false,
            'aria_labels_present' => false,
            'mobile_menu_structure' => false
        ];
        
        $headerPath = $this->cssDir . 'header.php';
        if (file_exists($headerPath)) {
            $headerContent = file_get_contents($headerPath);
            
            // Check navigation structure
            $navResults['header_structure'] = strpos($headerContent, '<header') !== false &&
                                            strpos($headerContent, '<nav') !== false;
            
            $navResults['nav_brand_present'] = strpos($headerContent, 'nav-brand') !== false;
            $navResults['nav_toggle_present'] = strpos($headerContent, 'nav-toggle') !== false;
            $navResults['skip_links_present'] = strpos($headerContent, 'skip-links') !== false;
            $navResults['aria_labels_present'] = strpos($headerContent, 'aria-label') !== false;
            $navResults['mobile_menu_structure'] = strpos($headerContent, 'nav-links') !== false;
            
            foreach ($navResults as $check => $passed) {
                $checkName = ucfirst(str_replace('_', ' ', $check));
                if ($passed) {
                    echo "✅ {$checkName}\n";
                } else {
                    echo "❌ {$checkName}\n";
                }
            }
        } else {
            echo "❌ Header file not found\n";
        }
        
        $this->results['navigation_consistency'] = $navResults;
        echo "\n";
    }
    
    /**
     * Check typography consistency
     */
    private function checkTypographyConsistency() {
        $typographyResults = [
            'merriweather_loaded' => false,
            'font_variables_defined' => false,
            'heading_hierarchy' => false,
            'text_size_variables' => false
        ];
        
        // Check if Merriweather is loaded in header
        $headerPath = $this->cssDir . 'header.php';
        if (file_exists($headerPath)) {
            $headerContent = file_get_contents($headerPath);
            $typographyResults['merriweather_loaded'] = strpos($headerContent, 'Merriweather') !== false;
        }
        
        // Check font variables in variables.css
        $variablesPath = $this->cssDir . 'assets/css/variables.css';
        if (file_exists($variablesPath)) {
            $variablesContent = file_get_contents($variablesPath);
            $typographyResults['font_variables_defined'] = strpos($variablesContent, '--font-primary') !== false;
            $typographyResults['text_size_variables'] = strpos($variablesContent, '--text-') !== false;
        }
        
        // Check heading hierarchy in sample pages
        $samplePages = ['landing.php', 'home.php'];
        $hierarchyCorrect = true;
        
        foreach ($samplePages as $page) {
            $pagePath = $this->pagesDir . $page;
            if (file_exists($pagePath)) {
                $pageContent = file_get_contents($pagePath);
                
                // Simple check for h1, h2, h3 presence
                $hasH1 = strpos($pageContent, '<h1') !== false;
                $hasH2 = strpos($pageContent, '<h2') !== false;
                
                if (!$hasH1) {
                    $hierarchyCorrect = false;
                    break;
                }
            }
        }
        
        $typographyResults['heading_hierarchy'] = $hierarchyCorrect;
        
        foreach ($typographyResults as $check => $passed) {
            $checkName = ucfirst(str_replace('_', ' ', $check));
            if ($passed) {
                echo "✅ {$checkName}\n";
            } else {
                echo "❌ {$checkName}\n";
            }
        }
        
        $this->results['typography_consistency'] = $typographyResults;
        echo "\n";
    }
    
    /**
     * Check semantic structure across pages
     */
    private function checkSemanticStructure() {
        $semanticResults = [];
        
        $requiredPages = [
            'landing.php', 'home.php', 'browse.php', 'cart.php',
            'checkout.php', 'login.php', 'register.php', 'profile.php'
        ];
        
        foreach ($requiredPages as $page) {
            $pagePath = $this->pagesDir . $page;
            
            if (file_exists($pagePath)) {
                $pageContent = file_get_contents($pagePath);
                
                $semanticResults[$page] = [
                    'has_main' => strpos($pageContent, '<main') !== false,
                    'has_page_main_class' => strpos($pageContent, 'page-main') !== false,
                    'has_sections' => strpos($pageContent, '<section') !== false,
                    'has_articles' => strpos($pageContent, '<article') !== false,
                    'has_proper_headings' => strpos($pageContent, '<h1') !== false,
                    'has_aria_labels' => strpos($pageContent, 'aria-label') !== false
                ];
                
                $pageScore = array_sum($semanticResults[$page]);
                $totalChecks = count($semanticResults[$page]);
                $percentage = round(($pageScore / $totalChecks) * 100);
                
                if ($percentage >= 80) {
                    echo "✅ {$page} - Semantic structure good ({$percentage}%)\n";
                } else {
                    echo "⚠️ {$page} - Semantic structure needs improvement ({$percentage}%)\n";
                }
            } else {
                echo "❌ {$page} - File not found\n";
                $semanticResults[$page] = null;
            }
        }
        
        $this->results['semantic_structure'] = $semanticResults;
        echo "\n";
    }
    
    /**
     * Generate comprehensive report
     */
    private function generateReport() {
        echo "📋 VISUAL CONTINUITY REPORT\n";
        echo "===========================\n\n";
        
        $scores = [];
        
        // Calculate CSS structure score
        if (isset($this->results['css_structure'])) {
            $cssFiles = array_filter($this->results['css_structure'], function($key) {
                return $key !== 'import_order';
            }, ARRAY_FILTER_USE_KEY);
            
            $validFiles = array_filter($cssFiles, function($file) {
                return $file['valid'];
            });
            
            $cssScore = (count($validFiles) / count($cssFiles)) * 100;
            $scores['CSS Structure'] = $cssScore;
            echo "🎨 CSS Structure Score: {$cssScore}%\n";
        }
        
        // Calculate design token score
        if (isset($this->results['design_tokens'])) {
            $tokens = $this->results['design_tokens'];
            $tokenScore = 0;
            
            if ($tokens['defined_tokens'] > 0) $tokenScore += 25;
            if ($tokens['color_tokens'] > 0) $tokenScore += 25;
            if ($tokens['spacing_tokens'] > 0) $tokenScore += 25;
            if (!empty($tokens['usage_validation'])) $tokenScore += 25;
            
            $scores['Design Tokens'] = $tokenScore;
            echo "🎯 Design Token Score: {$tokenScore}%\n";
        }
        
        // Calculate navigation score
        if (isset($this->results['navigation_consistency'])) {
            $nav = $this->results['navigation_consistency'];
            $navPassed = array_sum($nav);
            $navTotal = count($nav);
            $navScore = ($navPassed / $navTotal) * 100;
            
            $scores['Navigation'] = $navScore;
            echo "🧭 Navigation Score: {$navScore}%\n";
        }
        
        // Calculate typography score
        if (isset($this->results['typography_consistency'])) {
            $typo = $this->results['typography_consistency'];
            $typoPassed = array_sum($typo);
            $typoTotal = count($typo);
            $typoScore = ($typoPassed / $typoTotal) * 100;
            
            $scores['Typography'] = $typoScore;
            echo "📝 Typography Score: {$typoScore}%\n";
        }
        
        // Calculate semantic structure score
        if (isset($this->results['semantic_structure'])) {
            $semantic = $this->results['semantic_structure'];
            $validPages = array_filter($semantic, function($page) {
                return $page !== null;
            });
            
            $totalScore = 0;
            foreach ($validPages as $page) {
                $pageScore = array_sum($page);
                $pageTotal = count($page);
                $totalScore += ($pageScore / $pageTotal) * 100;
            }
            
            $semanticScore = count($validPages) > 0 ? $totalScore / count($validPages) : 0;
            $scores['Semantic Structure'] = $semanticScore;
            echo "🏗️ Semantic Structure Score: {$semanticScore}%\n";
        }
        
        // Overall score
        if (!empty($scores)) {
            $overallScore = array_sum($scores) / count($scores);
            echo "\n🏆 OVERALL VISUAL CONTINUITY SCORE: {$overallScore}%\n";
            
            if ($overallScore >= 90) {
                echo "🎉 EXCELLENT! Visual continuity is highly consistent.\n";
            } elseif ($overallScore >= 75) {
                echo "✅ GOOD! Visual continuity is mostly consistent with minor issues.\n";
            } elseif ($overallScore >= 60) {
                echo "⚠️ FAIR! Visual continuity has some inconsistencies that need attention.\n";
            } else {
                echo "❌ POOR! Visual continuity needs significant improvements.\n";
            }
        }
        
        echo "\n📅 Check completed at: " . date('Y-m-d H:i:s') . "\n";
    }
}

// Run the check if called directly
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $checker = new VisualContinuityChecker();
    $checker->runCheck();
}