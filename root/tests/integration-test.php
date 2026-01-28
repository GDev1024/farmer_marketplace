<?php
/**
 * Comprehensive Integration Test
 * Tests all components and pages work together consistently
 */

require_once __DIR__ . '/../includes/navigation-integration.php';
require_once __DIR__ . '/../includes/page-integration-validator.php';
require_once __DIR__ . '/../includes/user-journey-validator.php';

class IntegrationTest {
    
    private $results = [];
    private $pdo;
    
    public function __construct() {
        // Initialize database connection for testing
        try {
            $this->pdo = new PDO("mysql:host=localhost;dbname=grenada_marketplace", "root", "");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Database connection failed: " . $e->getMessage() . "\n";
            $this->pdo = null;
        }
    }
    
    /**
     * Run all integration tests
     */
    public function runAllTests() {
        echo "🧪 Starting Comprehensive Integration Tests...\n\n";
        
        // Test 1: Navigation Consistency
        echo "1️⃣ Testing Navigation Consistency...\n";
        $this->testNavigationConsistency();
        
        // Test 2: Page Structure Validation
        echo "2️⃣ Testing Page Structure...\n";
        $this->testPageStructure();
        
        // Test 3: CSS Architecture Integrity
        echo "3️⃣ Testing CSS Architecture...\n";
        $this->testCSSArchitecture();
        
        // Test 4: User Journey Validation
        echo "4️⃣ Testing User Journeys...\n";
        $this->testUserJourneys();
        
        // Test 5: Visual Continuity
        echo "5️⃣ Testing Visual Continuity...\n";
        $this->testVisualContinuity();
        
        // Test 6: Accessibility Integration
        echo "6️⃣ Testing Accessibility Integration...\n";
        $this->testAccessibilityIntegration();
        
        // Generate final report
        $this->generateFinalReport();
    }
    
    private function testNavigationConsistency() {
        $validator = new PageIntegrationValidator();
        $navResults = $validator->validateNavigationConsistency();
        
        $this->results['navigation'] = $navResults;
        
        // Test navigation items consistency
        $testPages = ['landing', 'home', 'browse', 'cart'];
        $navConsistency = true;
        
        foreach ($testPages as $page) {
            $navItems = NavigationIntegration::getNavigationItems(true, $page);
            if (empty($navItems)) {
                $navConsistency = false;
                break;
            }
        }
        
        $this->results['navigation']['items_consistency'] = $navConsistency;
        
        echo $navResults['header']['exists'] ? "✅ Header exists\n" : "❌ Header missing\n";
        echo $navResults['header']['has_nav_brand'] ? "✅ Nav brand present\n" : "❌ Nav brand missing\n";
        echo $navResults['header']['has_nav_toggle'] ? "✅ Mobile toggle present\n" : "❌ Mobile toggle missing\n";
        echo $navResults['footer']['exists'] ? "✅ Footer exists\n" : "❌ Footer missing\n";
        echo $navConsistency ? "✅ Navigation items consistent\n" : "❌ Navigation items inconsistent\n";
        echo "\n";
    }
    
    private function testPageStructure() {
        $validator = new PageIntegrationValidator();
        $pageResults = $validator->validatePageStructure();
        
        $this->results['pages'] = $pageResults;
        
        $totalPages = count($pageResults);
        $validPages = 0;
        
        foreach ($pageResults as $page => $result) {
            if ($result['exists'] && $result['has_main_tag'] && $result['has_semantic_structure']) {
                $validPages++;
                echo "✅ {$page} - Valid structure\n";
            } else {
                echo "❌ {$page} - Issues found\n";
                if (!$result['exists']) echo "   - File missing\n";
                if (!$result['has_main_tag']) echo "   - Missing main tag\n";
                if (!$result['has_semantic_structure']) echo "   - Missing semantic structure\n";
            }
        }
        
        $pageScore = ($validPages / $totalPages) * 100;
        echo "\n📊 Page Structure Score: {$pageScore}%\n\n";
    }
    
    private function testCSSArchitecture() {
        $validator = new PageIntegrationValidator();
        $cssResults = $validator->validateCSSStructure();
        
        $this->results['css'] = $cssResults;
        
        $totalFiles = count($cssResults);
        $validFiles = 0;
        
        foreach ($cssResults as $file => $result) {
            if ($result['exists'] && $result['readable'] && $result['size'] > 0) {
                $validFiles++;
                echo "✅ {$file} - Valid ({$result['size']} bytes)\n";
            } else {
                echo "❌ {$file} - Issues found\n";
                if (!$result['exists']) echo "   - File missing\n";
                if (!$result['readable']) echo "   - File not readable\n";
                if ($result['size'] === 0) echo "   - File empty\n";
            }
        }
        
        $cssScore = ($validFiles / $totalFiles) * 100;
        echo "\n📊 CSS Architecture Score: {$cssScore}%\n\n";
    }
    
    private function testUserJourneys() {
        if (!$this->pdo) {
            echo "❌ Database connection required for journey testing\n\n";
            return;
        }
        
        $journeyValidator = new UserJourneyValidator($this->pdo);
        $journeyResults = $journeyValidator->generateJourneyReport();
        
        $this->results['journeys'] = $journeyResults;
        
        // Display customer journey results
        echo "👤 Customer Journey:\n";
        foreach ($journeyResults['customer_journey'] as $step => $passed) {
            echo ($passed ? "✅" : "❌") . " " . ucfirst(str_replace('_', ' ', $step)) . "\n";
        }
        
        // Display farmer journey results
        echo "\n🌾 Farmer Journey:\n";
        foreach ($journeyResults['farmer_journey'] as $step => $passed) {
            echo ($passed ? "✅" : "❌") . " " . ucfirst(str_replace('_', ' ', $step)) . "\n";
        }
        
        echo "\n📊 Customer Journey Score: {$journeyResults['scores']['customer_score']}%\n";
        echo "📊 Farmer Journey Score: {$journeyResults['scores']['farmer_score']}%\n\n";
    }
    
    private function testVisualContinuity() {
        // Test visual consistency across pages
        $visualTests = [
            'consistent_branding' => $this->testConsistentBranding(),
            'color_palette_usage' => $this->testColorPaletteUsage(),
            'typography_consistency' => $this->testTypographyConsistency(),
            'spacing_system' => $this->testSpacingSystem(),
            'component_consistency' => $this->testComponentConsistency()
        ];
        
        $this->results['visual'] = $visualTests;
        
        foreach ($visualTests as $test => $passed) {
            echo ($passed ? "✅" : "❌") . " " . ucfirst(str_replace('_', ' ', $test)) . "\n";
        }
        
        $visualScore = (array_sum($visualTests) / count($visualTests)) * 100;
        echo "\n📊 Visual Continuity Score: {$visualScore}%\n\n";
    }
    
    private function testAccessibilityIntegration() {
        $validator = new PageIntegrationValidator();
        $accessibilityResults = $validator->validateAccessibilityFeatures();
        
        $this->results['accessibility'] = $accessibilityResults;
        
        // Test global accessibility features
        foreach ($accessibilityResults['global_features'] as $feature => $present) {
            echo ($present ? "✅" : "❌") . " " . ucfirst(str_replace('_', ' ', $feature)) . "\n";
        }
        
        $accessibilityScore = (array_sum($accessibilityResults['global_features']) / count($accessibilityResults['global_features'])) * 100;
        echo "\n📊 Accessibility Integration Score: {$accessibilityScore}%\n\n";
    }
    
    private function testConsistentBranding() {
        // Check if header and footer have consistent branding
        $headerPath = __DIR__ . '/../header.php';
        $footerPath = __DIR__ . '/../footer.php';
        
        if (!file_exists($headerPath) || !file_exists($footerPath)) return false;
        
        $headerContent = file_get_contents($headerPath);
        $footerContent = file_get_contents($footerPath);
        
        // Check for consistent branding elements
        $brandingElements = [
            'nav-brand' => strpos($headerContent, 'nav-brand') !== false,
            'logo_emoji' => strpos($headerContent, '🌾') !== false,
            'footer_brand' => strpos($footerContent, 'Grenada') !== false
        ];
        
        return !in_array(false, $brandingElements);
    }
    
    private function testColorPaletteUsage() {
        // Check if CSS variables are properly used
        $variablesPath = __DIR__ . '/../assets/css/variables.css';
        if (!file_exists($variablesPath)) return false;
        
        $content = file_get_contents($variablesPath);
        
        // Check for design system colors
        $colorVariables = [
            '--color-primary' => strpos($content, '--color-primary') !== false,
            '--color-secondary' => strpos($content, '--color-secondary') !== false,
            '--font-primary' => strpos($content, '--font-primary') !== false
        ];
        
        return !in_array(false, $colorVariables);
    }
    
    private function testTypographyConsistency() {
        // Check if Merriweather font is properly loaded
        $headerPath = __DIR__ . '/../header.php';
        if (!file_exists($headerPath)) return false;
        
        $content = file_get_contents($headerPath);
        
        return strpos($content, 'Merriweather') !== false;
    }
    
    private function testSpacingSystem() {
        // Check if spacing variables are defined
        $variablesPath = __DIR__ . '/../assets/css/variables.css';
        if (!file_exists($variablesPath)) return false;
        
        $content = file_get_contents($variablesPath);
        
        return strpos($content, '--space-') !== false;
    }
    
    private function testComponentConsistency() {
        // Check if component CSS file exists and has content
        $componentsPath = __DIR__ . '/../css/components.css';
        if (!file_exists($componentsPath)) return false;
        
        $content = file_get_contents($componentsPath);
        
        // Check for key component classes
        $components = [
            '.btn' => strpos($content, '.btn') !== false,
            '.card' => strpos($content, '.card') !== false,
            '.nav' => strpos($content, '.nav') !== false
        ];
        
        return !in_array(false, $components);
    }
    
    private function generateFinalReport() {
        echo "📋 FINAL INTEGRATION REPORT\n";
        echo "==========================\n\n";
        
        // Calculate overall scores
        $scores = [];
        
        if (isset($this->results['navigation'])) {
            $navScore = $this->calculateNavigationScore();
            $scores['Navigation'] = $navScore;
            echo "🧭 Navigation Score: {$navScore}%\n";
        }
        
        if (isset($this->results['pages'])) {
            $pageScore = $this->calculatePageScore();
            $scores['Pages'] = $pageScore;
            echo "📄 Page Structure Score: {$pageScore}%\n";
        }
        
        if (isset($this->results['css'])) {
            $cssScore = $this->calculateCSSScore();
            $scores['CSS'] = $cssScore;
            echo "🎨 CSS Architecture Score: {$cssScore}%\n";
        }
        
        if (isset($this->results['journeys'])) {
            $journeyScore = $this->results['journeys']['overall_journey_score'];
            $scores['User Journeys'] = $journeyScore;
            echo "🚶 User Journey Score: {$journeyScore}%\n";
        }
        
        if (isset($this->results['visual'])) {
            $visualScore = (array_sum($this->results['visual']) / count($this->results['visual'])) * 100;
            $scores['Visual Continuity'] = $visualScore;
            echo "👁️ Visual Continuity Score: {$visualScore}%\n";
        }
        
        if (isset($this->results['accessibility'])) {
            $accessibilityScore = (array_sum($this->results['accessibility']['global_features']) / count($this->results['accessibility']['global_features'])) * 100;
            $scores['Accessibility'] = $accessibilityScore;
            echo "♿ Accessibility Score: {$accessibilityScore}%\n";
        }
        
        // Overall integration score
        if (!empty($scores)) {
            $overallScore = array_sum($scores) / count($scores);
            echo "\n🏆 OVERALL INTEGRATION SCORE: {$overallScore}%\n";
            
            if ($overallScore >= 90) {
                echo "🎉 EXCELLENT! Integration is highly successful.\n";
            } elseif ($overallScore >= 75) {
                echo "✅ GOOD! Integration is mostly successful with minor issues.\n";
            } elseif ($overallScore >= 60) {
                echo "⚠️ FAIR! Integration has some issues that need attention.\n";
            } else {
                echo "❌ POOR! Integration needs significant improvements.\n";
            }
        }
        
        echo "\n📅 Test completed at: " . date('Y-m-d H:i:s') . "\n";
    }
    
    private function calculateNavigationScore() {
        $nav = $this->results['navigation'];
        $score = 0;
        $total = 0;
        
        // Header score
        if ($nav['header']['exists']) {
            $headerFeatures = ['has_nav_brand', 'has_nav_toggle', 'has_skip_links', 'has_aria_labels'];
            foreach ($headerFeatures as $feature) {
                $total++;
                if ($nav['header'][$feature]) $score++;
            }
        }
        
        // Footer score
        if ($nav['footer']['exists']) {
            $footerFeatures = ['has_semantic_footer', 'has_consistent_links'];
            foreach ($footerFeatures as $feature) {
                $total++;
                if ($nav['footer'][$feature]) $score++;
            }
        }
        
        // Navigation items consistency
        $total++;
        if ($nav['items_consistency']) $score++;
        
        return $total > 0 ? ($score / $total) * 100 : 0;
    }
    
    private function calculatePageScore() {
        $pages = $this->results['pages'];
        $score = 0;
        $total = count($pages);
        
        foreach ($pages as $result) {
            if ($result['exists'] && $result['has_main_tag'] && $result['has_semantic_structure']) {
                $score++;
            }
        }
        
        return $total > 0 ? ($score / $total) * 100 : 0;
    }
    
    private function calculateCSSScore() {
        $css = $this->results['css'];
        $score = 0;
        $total = count($css);
        
        foreach ($css as $result) {
            if ($result['exists'] && $result['readable'] && $result['size'] > 0) {
                $score++;
            }
        }
        
        return $total > 0 ? ($score / $total) * 100 : 0;
    }
}

// Run the integration test if called directly
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    $test = new IntegrationTest();
    $test->runAllTests();
}