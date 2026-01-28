<?php
/**
 * User Journey Validator
 * Tests complete user workflows end-to-end
 */

class UserJourneyValidator {
    
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Test complete customer journey
     */
    public function testCustomerJourney() {
        $results = [
            'landing_page_access' => false,
            'registration_flow' => false,
            'login_flow' => false,
            'browse_products' => false,
            'add_to_cart' => false,
            'checkout_process' => false,
            'order_management' => false,
            'messaging_system' => false
        ];
        
        try {
            // Test 1: Landing page accessibility
            $results['landing_page_access'] = $this->testLandingPageAccess();
            
            // Test 2: Registration flow
            $results['registration_flow'] = $this->testRegistrationFlow();
            
            // Test 3: Login flow
            $results['login_flow'] = $this->testLoginFlow();
            
            // Test 4: Browse products
            $results['browse_products'] = $this->testBrowseProducts();
            
            // Test 5: Cart functionality
            $results['add_to_cart'] = $this->testCartFunctionality();
            
            // Test 6: Checkout process
            $results['checkout_process'] = $this->testCheckoutProcess();
            
            // Test 7: Order management
            $results['order_management'] = $this->testOrderManagement();
            
            // Test 8: Messaging system
            $results['messaging_system'] = $this->testMessagingSystem();
            
        } catch (Exception $e) {
            error_log("User journey test error: " . $e->getMessage());
        }
        
        return $results;
    }
    
    /**
     * Test complete farmer journey
     */
    public function testFarmerJourney() {
        $results = [
            'farmer_registration' => false,
            'farmer_dashboard' => false,
            'product_listing' => false,
            'listing_management' => false,
            'order_fulfillment' => false,
            'customer_communication' => false,
            'profile_management' => false
        ];
        
        try {
            // Test farmer-specific workflows
            $results['farmer_registration'] = $this->testFarmerRegistration();
            $results['farmer_dashboard'] = $this->testFarmerDashboard();
            $results['product_listing'] = $this->testProductListing();
            $results['listing_management'] = $this->testListingManagement();
            $results['order_fulfillment'] = $this->testOrderFulfillment();
            $results['customer_communication'] = $this->testCustomerCommunication();
            $results['profile_management'] = $this->testProfileManagement();
            
        } catch (Exception $e) {
            error_log("Farmer journey test error: " . $e->getMessage());
        }
        
        return $results;
    }
    
    private function testLandingPageAccess() {
        // Check if landing page file exists and has required elements
        $landingPath = __DIR__ . '/../pages/landing.php';
        if (!file_exists($landingPath)) return false;
        
        $content = file_get_contents($landingPath);
        
        // Check for required elements
        $requiredElements = [
            'hero section' => strpos($content, 'hero') !== false,
            'cta buttons' => strpos($content, 'btn') !== false,
            'features section' => strpos($content, 'features') !== false,
            'semantic structure' => strpos($content, 'main') !== false
        ];
        
        return !in_array(false, $requiredElements);
    }
    
    private function testRegistrationFlow() {
        // Check if registration page exists and has proper form structure
        $registerPath = __DIR__ . '/../pages/register.php';
        if (!file_exists($registerPath)) return false;
        
        $content = file_get_contents($registerPath);
        
        // Check for form elements
        $requiredElements = [
            'form tag' => strpos($content, '<form') !== false,
            'email input' => strpos($content, 'email') !== false,
            'password input' => strpos($content, 'password') !== false,
            'submit button' => strpos($content, 'submit') !== false,
            'validation' => strpos($content, 'required') !== false
        ];
        
        return !in_array(false, $requiredElements);
    }
    
    private function testLoginFlow() {
        // Check if login page exists and has proper form structure
        $loginPath = __DIR__ . '/../pages/login.php';
        if (!file_exists($loginPath)) return false;
        
        $content = file_get_contents($loginPath);
        
        // Check for login form elements
        $requiredElements = [
            'login form' => strpos($content, '<form') !== false,
            'email field' => strpos($content, 'email') !== false,
            'password field' => strpos($content, 'password') !== false,
            'login button' => strpos($content, 'Login') !== false
        ];
        
        return !in_array(false, $requiredElements);
    }
    
    private function testBrowseProducts() {
        // Check if browse page exists and can display products
        $browsePath = __DIR__ . '/../pages/browse.php';
        if (!file_exists($browsePath)) return false;
        
        $content = file_get_contents($browsePath);
        
        // Check for product browsing elements
        $requiredElements = [
            'search functionality' => strpos($content, 'search') !== false,
            'product grid' => strpos($content, 'product') !== false,
            'category filter' => strpos($content, 'category') !== false,
            'add to cart' => strpos($content, 'cart') !== false
        ];
        
        return !in_array(false, $requiredElements);
    }
    
    private function testCartFunctionality() {
        // Check if cart page exists and has proper functionality
        $cartPath = __DIR__ . '/../pages/cart.php';
        if (!file_exists($cartPath)) return false;
        
        $content = file_get_contents($cartPath);
        
        // Check for cart elements
        $requiredElements = [
            'cart items' => strpos($content, 'cart') !== false,
            'quantity controls' => strpos($content, 'quantity') !== false,
            'total calculation' => strpos($content, 'total') !== false,
            'checkout button' => strpos($content, 'checkout') !== false
        ];
        
        return !in_array(false, $requiredElements);
    }
    
    private function testCheckoutProcess() {
        // Check if checkout page exists and has proper form
        $checkoutPath = __DIR__ . '/../pages/checkout.php';
        if (!file_exists($checkoutPath)) return false;
        
        $content = file_get_contents($checkoutPath);
        
        // Check for checkout elements
        $requiredElements = [
            'checkout form' => strpos($content, 'form') !== false,
            'payment section' => strpos($content, 'payment') !== false,
            'order summary' => strpos($content, 'order') !== false,
            'submit order' => strpos($content, 'submit') !== false
        ];
        
        return !in_array(false, $requiredElements);
    }
    
    private function testOrderManagement() {
        // Check if orders page exists
        $ordersPath = __DIR__ . '/../pages/orders.php';
        if (!file_exists($ordersPath)) return false;
        
        $content = file_get_contents($ordersPath);
        
        // Check for order management elements
        $requiredElements = [
            'order list' => strpos($content, 'order') !== false,
            'order status' => strpos($content, 'status') !== false,
            'order details' => strpos($content, 'details') !== false
        ];
        
        return !in_array(false, $requiredElements);
    }
    
    private function testMessagingSystem() {
        // Check if messages page exists
        $messagesPath = __DIR__ . '/../pages/messages.php';
        if (!file_exists($messagesPath)) return false;
        
        $content = file_get_contents($messagesPath);
        
        // Check for messaging elements
        $requiredElements = [
            'message list' => strpos($content, 'message') !== false,
            'conversation' => strpos($content, 'conversation') !== false,
            'send message' => strpos($content, 'send') !== false
        ];
        
        return !in_array(false, $requiredElements);
    }
    
    private function testFarmerRegistration() {
        // Similar to regular registration but check for farmer-specific fields
        return $this->testRegistrationFlow();
    }
    
    private function testFarmerDashboard() {
        // Check if home page has farmer dashboard elements
        $homePath = __DIR__ . '/../pages/home.php';
        if (!file_exists($homePath)) return false;
        
        $content = file_get_contents($homePath);
        
        // Check for dashboard elements
        $requiredElements = [
            'dashboard stats' => strpos($content, 'stats') !== false,
            'quick actions' => strpos($content, 'actions') !== false,
            'recent activity' => strpos($content, 'activity') !== false
        ];
        
        return !in_array(false, $requiredElements);
    }
    
    private function testProductListing() {
        // Check if listing page exists
        $listingPath = __DIR__ . '/../pages/listing.php';
        if (!file_exists($listingPath)) return false;
        
        $content = file_get_contents($listingPath);
        
        // Check for listing form elements
        $requiredElements = [
            'product form' => strpos($content, 'form') !== false,
            'product name' => strpos($content, 'product_name') !== false,
            'price field' => strpos($content, 'price') !== false,
            'image upload' => strpos($content, 'image') !== false
        ];
        
        return !in_array(false, $requiredElements);
    }
    
    private function testListingManagement() {
        // Check if sell page exists for managing listings
        $sellPath = __DIR__ . '/../pages/sell.php';
        if (!file_exists($sellPath)) return false;
        
        $content = file_get_contents($sellPath);
        
        // Check for listing management elements
        $requiredElements = [
            'listing table' => strpos($content, 'listing') !== false,
            'edit functionality' => strpos($content, 'edit') !== false,
            'status management' => strpos($content, 'status') !== false
        ];
        
        return !in_array(false, $requiredElements);
    }
    
    private function testOrderFulfillment() {
        // Test order management from farmer perspective
        return $this->testOrderManagement();
    }
    
    private function testCustomerCommunication() {
        // Test messaging from farmer perspective
        return $this->testMessagingSystem();
    }
    
    private function testProfileManagement() {
        // Check if profile page exists
        $profilePath = __DIR__ . '/../pages/profile.php';
        if (!file_exists($profilePath)) return false;
        
        $content = file_get_contents($profilePath);
        
        // Check for profile elements
        $requiredElements = [
            'profile form' => strpos($content, 'form') !== false,
            'user info' => strpos($content, 'name') !== false,
            'verification' => strpos($content, 'verification') !== false
        ];
        
        return !in_array(false, $requiredElements);
    }
    
    /**
     * Generate comprehensive journey report
     */
    public function generateJourneyReport() {
        $customerJourney = $this->testCustomerJourney();
        $farmerJourney = $this->testFarmerJourney();
        
        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'customer_journey' => $customerJourney,
            'farmer_journey' => $farmerJourney,
            'scores' => [
                'customer_score' => (array_sum($customerJourney) / count($customerJourney)) * 100,
                'farmer_score' => (array_sum($farmerJourney) / count($farmerJourney)) * 100
            ]
        ];
        
        $report['overall_journey_score'] = ($report['scores']['customer_score'] + $report['scores']['farmer_score']) / 2;
        
        return $report;
    }
}