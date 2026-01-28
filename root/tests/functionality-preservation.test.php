<?php
/**
 * Functionality Preservation Test
 * 
 * Tests that all existing features work without regression after design system migration.
 * This test validates core user workflows, form submissions, and data processing.
 * 
 * Requirements: 14.1
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

class FunctionalityPreservationTest {
    private $db;
    private $testResults = [];
    private $testUserId = null;
    
    public function __construct() {
        $this->db = Config::getDB();
    }
    
    public function runAllTests() {
        echo "=== Functionality Preservation Test Suite ===\n\n";
        
        $this->testDatabaseConnection();
        $this->testUserRegistration();
        $this->testUserLogin();
        $this->testProductCreation();
        $this->testProductListing();
        $this->testFormValidation();
        $this->testSessionManagement();
        $this->testPageRouting();
        $this->testImageUpload();
        $this->testDataSanitization();
        
        $this->cleanup();
        $this->printResults();
    }
    
    private function testDatabaseConnection() {
        $testName = "Database Connection";
        try {
            $stmt = $this->db->query("SELECT 1");
            $result = $stmt->fetch();
            $this->recordTest($testName, $result !== false, "Database connection successful");
        } catch (Exception $e) {
            $this->recordTest($testName, false, "Database connection failed: " . $e->getMessage());
        }
    }
    
    private function testUserRegistration() {
        $testName = "User Registration";
        try {
            // Test user registration functionality
            $testEmail = 'test_' . time() . '@example.com';
            $testUsername = 'TestUser' . time();
            $passwordHash = password_hash('testpassword123', PASSWORD_BCRYPT);
            
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash, user_type) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([$testUsername, $testEmail, $passwordHash, 'customer']);
            
            if ($result) {
                $this->testUserId = $this->db->lastInsertId();
                $this->recordTest($testName, true, "User registration successful");
            } else {
                $this->recordTest($testName, false, "User registration failed");
            }
        } catch (Exception $e) {
            $this->recordTest($testName, false, "User registration error: " . $e->getMessage());
        }
    }
    
    private function testUserLogin() {
        $testName = "User Login Validation";
        try {
            if (!$this->testUserId) {
                $this->recordTest($testName, false, "No test user available");
                return;
            }
            
            // Fetch the test user
            $stmt = $this->db->prepare("SELECT id, password_hash, user_type FROM users WHERE id = ?");
            $stmt->execute([$this->testUserId]);
            $user = $stmt->fetch();
            
            if ($user && password_verify('testpassword123', $user['password_hash'])) {
                $this->recordTest($testName, true, "User login validation successful");
            } else {
                $this->recordTest($testName, false, "User login validation failed");
            }
        } catch (Exception $e) {
            $this->recordTest($testName, false, "User login error: " . $e->getMessage());
        }
    }
    
    private function testProductCreation() {
        $testName = "Product Creation";
        try {
            if (!$this->testUserId) {
                $this->recordTest($testName, false, "No test user available");
                return;
            }
            
            // Create a test product listing
            $stmt = $this->db->prepare("
                INSERT INTO listings (farmer_id, name, category, price, quantity, unit, description, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'active')
            ");
            $result = $stmt->execute([
                $this->testUserId,
                'Test Product',
                'vegetables',
                10.99,
                5,
                'kg',
                'Test product description',
            ]);
            
            $this->recordTest($testName, $result, $result ? "Product creation successful" : "Product creation failed");
        } catch (Exception $e) {
            $this->recordTest($testName, false, "Product creation error: " . $e->getMessage());
        }
    }
    
    private function testProductListing() {
        $testName = "Product Listing Retrieval";
        try {
            // Test fetching products
            $stmt = $this->db->prepare("SELECT * FROM listings WHERE farmer_id = ? AND status = 'active'");
            $stmt->execute([$this->testUserId]);
            $products = $stmt->fetchAll();
            
            $this->recordTest($testName, count($products) > 0, "Product listing retrieval successful");
        } catch (Exception $e) {
            $this->recordTest($testName, false, "Product listing error: " . $e->getMessage());
        }
    }
    
    private function testFormValidation() {
        $testName = "Form Input Sanitization";
        try {
            // Test sanitizeInput function
            $testInput = "<script>alert('xss')</script>Test Input";
            $sanitized = sanitizeInput($testInput);
            
            $isClean = !strpos($sanitized, '<script>') && strpos($sanitized, 'Test Input') !== false;
            $this->recordTest($testName, $isClean, "Form input sanitization working");
        } catch (Exception $e) {
            $this->recordTest($testName, false, "Form validation error: " . $e->getMessage());
        }
    }
    
    private function testSessionManagement() {
        $testName = "Session Management";
        try {
            // Test session functions
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['test_key'] = 'test_value';
            $sessionWorks = isset($_SESSION['test_key']) && $_SESSION['test_key'] === 'test_value';
            
            unset($_SESSION['test_key']);
            
            $this->recordTest($testName, $sessionWorks, "Session management working");
        } catch (Exception $e) {
            $this->recordTest($testName, false, "Session management error: " . $e->getMessage());
        }
    }
    
    private function testPageRouting() {
        $testName = "Page File Existence";
        try {
            $requiredPages = [
                'pages/landing.php',
                'pages/login.php',
                'pages/register.php',
                'pages/home.php',
                'pages/browse.php',
                'pages/cart.php',
                'pages/checkout.php',
                'pages/orders.php',
                'pages/profile.php',
                'pages/sell.php',
                'pages/listing.php',
                'pages/messages.php',
                'pages/payment-success.php',
                'pages/payment-cancel.php'
            ];
            
            $missingPages = [];
            foreach ($requiredPages as $page) {
                if (!file_exists(__DIR__ . '/../' . $page)) {
                    $missingPages[] = $page;
                }
            }
            
            $allPagesExist = empty($missingPages);
            $message = $allPagesExist ? "All required pages exist" : "Missing pages: " . implode(', ', $missingPages);
            
            $this->recordTest($testName, $allPagesExist, $message);
        } catch (Exception $e) {
            $this->recordTest($testName, false, "Page routing error: " . $e->getMessage());
        }
    }
    
    private function testImageUpload() {
        $testName = "Image Upload Configuration";
        try {
            // Test upload directory exists and is writable
            $uploadDir = __DIR__ . '/../uploads/products';
            $dirExists = is_dir($uploadDir);
            $isWritable = is_writable($uploadDir);
            
            $this->recordTest($testName, $dirExists && $isWritable, 
                $dirExists ? ($isWritable ? "Upload directory ready" : "Upload directory not writable") : "Upload directory missing");
        } catch (Exception $e) {
            $this->recordTest($testName, false, "Image upload test error: " . $e->getMessage());
        }
    }
    
    private function testDataSanitization() {
        $testName = "Data Sanitization Functions";
        try {
            // Test various sanitization scenarios
            $tests = [
                ['input' => '<script>alert("xss")</script>', 'expected_clean' => true],
                ['input' => 'Normal text', 'expected_clean' => true],
                ['input' => 'Text with "quotes"', 'expected_clean' => true],
                ['input' => "Text with 'single quotes'", 'expected_clean' => true],
            ];
            
            $allPassed = true;
            foreach ($tests as $test) {
                $sanitized = sanitizeInput($test['input']);
                if (strpos($sanitized, '<script>') !== false) {
                    $allPassed = false;
                    break;
                }
            }
            
            $this->recordTest($testName, $allPassed, "Data sanitization functions working");
        } catch (Exception $e) {
            $this->recordTest($testName, false, "Data sanitization error: " . $e->getMessage());
        }
    }
    
    private function cleanup() {
        // Clean up test data
        if ($this->testUserId) {
            try {
                // Delete test listings
                $stmt = $this->db->prepare("DELETE FROM listings WHERE farmer_id = ?");
                $stmt->execute([$this->testUserId]);
                
                // Delete test user
                $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$this->testUserId]);
            } catch (Exception $e) {
                echo "Cleanup error: " . $e->getMessage() . "\n";
            }
        }
    }
    
    private function recordTest($testName, $passed, $message) {
        $this->testResults[] = [
            'name' => $testName,
            'passed' => $passed,
            'message' => $message
        ];
        
        $status = $passed ? "✓ PASS" : "✗ FAIL";
        echo sprintf("%-40s %s - %s\n", $testName, $status, $message);
    }
    
    private function printResults() {
        $totalTests = count($this->testResults);
        $passedTests = array_filter($this->testResults, function($test) { return $test['passed']; });
        $passedCount = count($passedTests);
        
        echo "\n=== Test Results ===\n";
        echo "Total Tests: $totalTests\n";
        echo "Passed: $passedCount\n";
        echo "Failed: " . ($totalTests - $passedCount) . "\n";
        echo "Success Rate: " . round(($passedCount / $totalTests) * 100, 2) . "%\n";
        
        if ($passedCount === $totalTests) {
            echo "\n🎉 All functionality preservation tests passed!\n";
        } else {
            echo "\n⚠️  Some tests failed. Review the failures above.\n";
        }
    }
}

// Run the tests
if (php_sapi_name() === 'cli' || isset($_GET['run_test'])) {
    $test = new FunctionalityPreservationTest();
    $test->runAllTests();
}
?>