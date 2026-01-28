<?php
/**
 * Navigation Integration Helper
 * Ensures consistent navigation across all pages
 */

class NavigationIntegration {
    
    /**
     * Get navigation items based on user authentication status
     */
    public static function getNavigationItems($isLoggedIn, $currentPage = '') {
        $items = [];
        
        if ($isLoggedIn) {
            $items = [
                'browse' => [
                    'url' => 'index.php?page=browse',
                    'title' => 'Browse Products',
                    'icon' => '🛍️',
                    'label' => 'Browse fresh produce',
                    'active' => $currentPage === 'browse'
                ],
                'cart' => [
                    'url' => 'index.php?page=cart',
                    'title' => 'Shopping Cart',
                    'icon' => '🛒',
                    'label' => 'View shopping cart',
                    'active' => $currentPage === 'cart',
                    'badge' => isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0
                ],
                'messages' => [
                    'url' => 'index.php?page=messages',
                    'title' => 'Messages',
                    'icon' => '💬',
                    'label' => 'View messages',
                    'active' => $currentPage === 'messages'
                ],
                'sell' => [
                    'url' => 'index.php?page=sell',
                    'title' => 'My Listings',
                    'icon' => '📊',
                    'label' => 'Manage product listings',
                    'active' => $currentPage === 'sell'
                ],
                'orders' => [
                    'url' => 'index.php?page=orders',
                    'title' => 'Orders',
                    'icon' => '📦',
                    'label' => 'View order history',
                    'active' => $currentPage === 'orders'
                ],
                'profile' => [
                    'url' => 'index.php?page=profile',
                    'title' => 'Profile',
                    'icon' => '👤',
                    'label' => 'View and edit profile',
                    'active' => $currentPage === 'profile'
                ]
            ];
        } else {
            $items = [
                'browse' => [
                    'url' => 'index.php?page=browse',
                    'title' => 'Browse Products',
                    'icon' => '🛍️',
                    'label' => 'Browse fresh produce',
                    'active' => $currentPage === 'browse'
                ],
                'login' => [
                    'url' => 'index.php?page=login',
                    'title' => 'Login',
                    'icon' => '',
                    'label' => 'Sign in to your account',
                    'active' => $currentPage === 'login',
                    'class' => 'btn btn-primary'
                ],
                'register' => [
                    'url' => 'index.php?page=register',
                    'title' => 'Sign Up',
                    'icon' => '',
                    'label' => 'Create new account',
                    'active' => $currentPage === 'register',
                    'class' => 'btn btn-secondary'
                ]
            ];
        }
        
        return $items;
    }
    
    /**
     * Get breadcrumb navigation for current page
     */
    public static function getBreadcrumbs($currentPage, $isLoggedIn) {
        $breadcrumbs = [
            ['title' => 'Home', 'url' => $isLoggedIn ? 'index.php?page=home' : 'index.php?page=landing']
        ];
        
        $pageMap = [
            'browse' => 'Browse Products',
            'cart' => 'Shopping Cart',
            'checkout' => 'Checkout',
            'messages' => 'Messages',
            'sell' => 'My Listings',
            'listing' => 'Add Product',
            'orders' => 'Orders',
            'profile' => 'Profile',
            'login' => 'Login',
            'register' => 'Sign Up',
            'payment-success' => 'Payment Successful',
            'payment-cancel' => 'Payment Cancelled'
        ];
        
        if (isset($pageMap[$currentPage])) {
            $breadcrumbs[] = ['title' => $pageMap[$currentPage], 'url' => null];
        }
        
        return $breadcrumbs;
    }
    
    /**
     * Validate page access permissions
     */
    public static function validatePageAccess($page, $isLoggedIn) {
        $protectedPages = [
            'home', 'browse', 'sell', 'listing', 'orders', 
            'messages', 'profile', 'cart', 'checkout', 
            'payment-success', 'payment-cancel'
        ];
        
        $publicPages = ['landing', 'login', 'register'];
        
        // Redirect unauthenticated users from protected pages
        if (!$isLoggedIn && in_array($page, $protectedPages)) {
            return 'login';
        }
        
        // Redirect authenticated users from auth pages
        if ($isLoggedIn && in_array($page, ['login', 'register'])) {
            return 'home';
        }
        
        return $page;
    }
    
    /**
     * Get page metadata for consistent titles and descriptions
     */
    public static function getPageMetadata($page) {
        $metadata = [
            'landing' => [
                'title' => 'Grenada Farmers Marketplace - Fresh Local Produce',
                'description' => 'Connect with local farmers in Grenada. Buy fresh, locally grown produce directly from verified farmers.',
                'keywords' => 'grenada, farmers, marketplace, fresh produce, local food'
            ],
            'home' => [
                'title' => 'Dashboard - Grenada Farmers Marketplace',
                'description' => 'Manage your listings, orders, and connect with customers on Grenada Farmers Marketplace.',
                'keywords' => 'dashboard, farmer, listings, orders'
            ],
            'browse' => [
                'title' => 'Browse Fresh Produce - Grenada Farmers Marketplace',
                'description' => 'Discover fresh, locally grown produce from verified farmers across Grenada.',
                'keywords' => 'browse, fresh produce, vegetables, fruits, local farmers'
            ],
            'cart' => [
                'title' => 'Shopping Cart - Grenada Farmers Marketplace',
                'description' => 'Review your selected fresh produce and proceed to checkout.',
                'keywords' => 'shopping cart, checkout, fresh produce'
            ],
            'login' => [
                'title' => 'Login - Grenada Farmers Marketplace',
                'description' => 'Sign in to your account to buy fresh produce or manage your farm listings.',
                'keywords' => 'login, sign in, account'
            ],
            'register' => [
                'title' => 'Sign Up - Grenada Farmers Marketplace',
                'description' => 'Create your account to start buying fresh produce or selling your farm products.',
                'keywords' => 'register, sign up, create account, farmer, customer'
            ]
        ];
        
        return $metadata[$page] ?? [
            'title' => 'Grenada Farmers Marketplace',
            'description' => 'Fresh local produce from Grenada farmers',
            'keywords' => 'grenada, farmers, marketplace'
        ];
    }
}