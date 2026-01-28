<?php
/**
 * Empty States Component
 * Provides consistent empty state displays across the application
 */

class EmptyStates {
    
    /**
     * Generate empty state for no products
     */
    public static function noProducts($userType = 'customer') {
        $icon = '🛍️';
        $title = 'No Products Found';
        $message = 'There are no products available at the moment.';
        $actionText = 'Browse Categories';
        $actionUrl = 'index.php?page=browse';
        
        if ($userType === 'farmer') {
            $icon = '📦';
            $title = 'No Products Listed';
            $message = 'You haven\'t listed any products yet. Start selling your fresh produce!';
            $actionText = 'Add Your First Product';
            $actionUrl = 'index.php?page=listing';
        }
        
        return self::generateEmptyState($icon, $title, $message, $actionText, $actionUrl);
    }
    
    /**
     * Generate empty state for no orders
     */
    public static function noOrders($userType = 'customer') {
        $icon = '📦';
        $title = 'No Orders Yet';
        $message = 'You haven\'t placed any orders yet.';
        $actionText = 'Start Shopping';
        $actionUrl = 'index.php?page=browse';
        
        if ($userType === 'farmer') {
            $title = 'No Orders Received';
            $message = 'You haven\'t received any orders yet. Make sure your products are listed and visible!';
            $actionText = 'Manage Listings';
            $actionUrl = 'index.php?page=sell';
        }
        
        return self::generateEmptyState($icon, $title, $message, $actionText, $actionUrl);
    }
    
    /**
     * Generate empty state for no messages
     */
    public static function noMessages() {
        $icon = '💬';
        $title = 'No Messages';
        $message = 'You don\'t have any messages yet. Start a conversation with farmers or customers!';
        $actionText = 'Browse Products';
        $actionUrl = 'index.php?page=browse';
        
        return self::generateEmptyState($icon, $title, $message, $actionText, $actionUrl);
    }
    
    /**
     * Generate empty state for empty cart
     */
    public static function emptyCart() {
        $icon = '🛒';
        $title = 'Your Cart is Empty';
        $message = 'Add some fresh products from our local farmers to get started!';
        $actionText = 'Browse Products';
        $actionUrl = 'index.php?page=browse';
        
        return self::generateEmptyState($icon, $title, $message, $actionText, $actionUrl);
    }
    
    /**
     * Generate empty state for no search results
     */
    public static function noSearchResults($searchTerm = '') {
        $icon = '🔍';
        $title = 'No Results Found';
        $message = $searchTerm ? 
            "We couldn't find any products matching \"{$searchTerm}\". Try different keywords or browse our categories." :
            "No products match your search criteria. Try adjusting your filters.";
        $actionText = 'Browse All Products';
        $actionUrl = 'index.php?page=browse';
        
        return self::generateEmptyState($icon, $title, $message, $actionText, $actionUrl);
    }
    
    /**
     * Generate empty state for no listings (farmer)
     */
    public static function noListings() {
        $icon = '🌾';
        $title = 'No Active Listings';
        $message = 'You don\'t have any active product listings. Create your first listing to start selling!';
        $actionText = 'Create Listing';
        $actionUrl = 'index.php?page=listing';
        
        return self::generateEmptyState($icon, $title, $message, $actionText, $actionUrl);
    }
    
    /**
     * Generate empty state for no favorites
     */
    public static function noFavorites() {
        $icon = '❤️';
        $title = 'No Favorites Yet';
        $message = 'You haven\'t added any products to your favorites. Heart the products you love!';
        $actionText = 'Discover Products';
        $actionUrl = 'index.php?page=browse';
        
        return self::generateEmptyState($icon, $title, $message, $actionText, $actionUrl);
    }
    
    /**
     * Generate empty state for no reviews
     */
    public static function noReviews($context = 'product') {
        $icon = '⭐';
        $title = 'No Reviews Yet';
        $message = $context === 'product' ? 
            'This product hasn\'t been reviewed yet. Be the first to share your experience!' :
            'You haven\'t written any reviews yet. Share your experience with other customers!';
        $actionText = $context === 'product' ? 'Write a Review' : 'Browse Products';
        $actionUrl = $context === 'product' ? '#write-review' : 'index.php?page=browse';
        
        return self::generateEmptyState($icon, $title, $message, $actionText, $actionUrl);
    }
    
    /**
     * Generate empty state for connection issues
     */
    public static function connectionError() {
        $icon = '📡';
        $title = 'Connection Problem';
        $message = 'We\'re having trouble loading this content. Please check your internet connection and try again.';
        $actionText = 'Try Again';
        $actionUrl = 'javascript:location.reload()';
        
        return self::generateEmptyState($icon, $title, $message, $actionText, $actionUrl, 'error');
    }
    
    /**
     * Generate empty state for maintenance
     */
    public static function maintenance() {
        $icon = '🔧';
        $title = 'Under Maintenance';
        $message = 'This feature is temporarily unavailable while we make improvements. Please check back soon!';
        $actionText = 'Go Home';
        $actionUrl = 'index.php';
        
        return self::generateEmptyState($icon, $title, $message, $actionText, $actionUrl, 'warning');
    }
    
    /**
     * Generate empty state for no data/network issues
     */
    public static function noDataAvailable($context = 'data') {
        $icon = '📊';
        $title = 'No Data Available';
        $message = "We couldn't load any {$context} at this time. This might be due to a network issue or the data is temporarily unavailable.";
        $actionText = 'Refresh Page';
        $actionUrl = 'javascript:location.reload()';
        
        return self::generateEmptyState($icon, $title, $message, $actionText, $actionUrl, 'warning');
    }
    
    /**
     * Generate empty state for slow loading
     */
    public static function slowLoading($context = 'content') {
        $icon = '⏳';
        $title = 'Taking Longer Than Expected';
        $message = "Loading {$context} is taking longer than usual. Your connection might be slow or our servers are busy.";
        $actionText = 'Try Again';
        $actionUrl = 'javascript:location.reload()';
        
        return self::generateEmptyState($icon, $title, $message, $actionText, $actionUrl, 'warning');
    }
    
    /**
     * Generate empty state for offline mode
     */
    public static function offlineMode() {
        $icon = '📡';
        $title = 'You\'re Offline';
        $message = 'It looks like you\'re not connected to the internet. Some features may not be available until you reconnect.';
        $actionText = 'Check Connection';
        $actionUrl = 'javascript:location.reload()';
        
        return self::generateEmptyState($icon, $title, $message, $actionText, $actionUrl, 'error');
    }
    
    /**
     * Generate empty state for feature coming soon
     */
    public static function comingSoon($featureName = 'feature') {
        $icon = '🚀';
        $title = 'Coming Soon';
        $message = "We're working hard to bring you {$featureName}. Stay tuned for updates!";
        $actionText = 'Go Back';
        $actionUrl = 'javascript:history.back()';
        
        return self::generateEmptyState($icon, $title, $message, $actionText, $actionUrl);
    }
    
    /**
     * Generate empty state for seasonal content
     */
    public static function seasonalEmpty($season = 'current season') {
        $icon = '🌱';
        $title = 'Seasonal Products';
        $message = "No products are available for {$season} right now. Check back as farmers update their seasonal offerings!";
        $actionText = 'View All Products';
        $actionUrl = 'index.php?page=browse';
        
        return self::generateEmptyState($icon, $title, $message, $actionText, $actionUrl);
    }
    
    /**
     * Generate empty state for location-based content
     */
    public static function noLocalContent($location = 'your area') {
        $icon = '📍';
        $title = 'No Local Results';
        $message = "We couldn't find any farmers or products in {$location}. Try expanding your search radius or browse all available products.";
        $actionText = 'Browse All';
        $actionUrl = 'index.php?page=browse';
        
        return self::generateEmptyState($icon, $title, $message, $actionText, $actionUrl);
    }
    
    /**
     * Generate generic empty state HTML
     */
    private static function generateEmptyState($icon, $title, $message, $actionText = null, $actionUrl = null, $type = 'default') {
        $typeClass = $type !== 'default' ? "empty-state-{$type}" : '';
        
        $html = "
        <div class=\"empty-state {$typeClass}\" role=\"status\" aria-label=\"{$title}\">
            <div class=\"empty-state-content\">
                <div class=\"empty-state-icon\" aria-hidden=\"true\">{$icon}</div>
                <h3 class=\"empty-state-title\">{$title}</h3>
                <p class=\"empty-state-message\">{$message}</p>";
        
        if ($actionText && $actionUrl) {
            $isJavaScript = strpos($actionUrl, 'javascript:') === 0;
            $linkAttributes = $isJavaScript ? 
                "href=\"{$actionUrl}\" onclick=\"{$actionUrl}; return false;\"" : 
                "href=\"{$actionUrl}\"";
            
            $html .= "
                <div class=\"empty-state-action\">
                    <a {$linkAttributes} class=\"btn btn-primary\" aria-label=\"{$actionText}\">
                        {$actionText}
                    </a>
                </div>";
        }
        
        $html .= "
            </div>
        </div>";
        
        return $html;
    }
    
    /**
     * Generate empty state with custom content
     */
    public static function custom($icon, $title, $message, $actions = []) {
        $html = "
        <div class=\"empty-state empty-state-custom\" role=\"status\" aria-label=\"{$title}\">
            <div class=\"empty-state-content\">
                <div class=\"empty-state-icon\" aria-hidden=\"true\">{$icon}</div>
                <h3 class=\"empty-state-title\">{$title}</h3>
                <p class=\"empty-state-message\">{$message}</p>";
        
        if (!empty($actions)) {
            $html .= "<div class=\"empty-state-actions\">";
            foreach ($actions as $action) {
                $class = $action['class'] ?? 'btn btn-primary';
                $html .= "<a href=\"{$action['url']}\" class=\"{$class}\" aria-label=\"{$action['text']}\">{$action['text']}</a>";
            }
            $html .= "</div>";
        }
        
        $html .= "
            </div>
        </div>";
        
        return $html;
    }
    
    /**
     * Generate CSS for empty states
     */
    public static function getCSS() {
        return "
        <style>
        /* Empty States */
        .empty-state {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 300px;
            padding: var(--space-8);
            text-align: center;
        }
        
        .empty-state-content {
            max-width: 400px;
            margin: 0 auto;
        }
        
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: var(--space-4);
            opacity: 0.8;
        }
        
        .empty-state-title {
            font-family: var(--font-primary);
            font-size: var(--text-xl);
            font-weight: var(--font-bold);
            color: var(--color-gray-800);
            margin-bottom: var(--space-3);
        }
        
        .empty-state-message {
            font-size: var(--text-base);
            color: var(--color-gray-600);
            line-height: var(--leading-relaxed);
            margin-bottom: var(--space-6);
        }
        
        .empty-state-action,
        .empty-state-actions {
            display: flex;
            gap: var(--space-3);
            justify-content: center;
            flex-wrap: wrap;
        }
        
        /* Empty State Variants */
        .empty-state-error {
            background-color: var(--color-red-50);
            border: 1px solid var(--color-red-200);
            border-radius: var(--radius-lg);
        }
        
        .empty-state-error .empty-state-title {
            color: var(--color-red-800);
        }
        
        .empty-state-error .empty-state-message {
            color: var(--color-red-600);
        }
        
        .empty-state-warning {
            background-color: var(--color-yellow-50);
            border: 1px solid var(--color-yellow-200);
            border-radius: var(--radius-lg);
        }
        
        .empty-state-warning .empty-state-title {
            color: var(--color-yellow-800);
        }
        
        .empty-state-warning .empty-state-message {
            color: var(--color-yellow-600);
        }
        
        /* Responsive Design */
        @media (max-width: 640px) {
            .empty-state {
                min-height: 250px;
                padding: var(--space-6);
            }
            
            .empty-state-icon {
                font-size: 3rem;
            }
            
            .empty-state-title {
                font-size: var(--text-lg);
            }
            
            .empty-state-message {
                font-size: var(--text-sm);
            }
        }
        </style>";
    }
}