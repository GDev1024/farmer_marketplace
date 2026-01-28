<?php
/**
 * Polish Integration
 * Integrates all final polish features for optimal user experience
 */

require_once 'loading-states.php';
require_once 'empty-states.php';
require_once 'asset-optimizer.php';
require_once 'performance-optimizer.php';

class PolishIntegration {
    
    private $assetOptimizer;
    private $performanceOptimizer;
    
    public function __construct() {
        $this->assetOptimizer = new AssetOptimizer();
        $this->performanceOptimizer = new PerformanceOptimizer();
    }
    
    /**
     * Initialize all polish features for a page
     */
    public function initializePage($pageType = 'default', $options = []) {
        // Set up loading states
        $this->setupLoadingStates($pageType);
        
        // Set up empty states
        $this->setupEmptyStates($pageType);
        
        // Optimize assets
        $this->optimizePageAssets($pageType, $options);
        
        // Add performance enhancements
        $this->addPerformanceEnhancements();
        
        // Add accessibility enhancements
        $this->addAccessibilityEnhancements();
    }
    
    /**
     * Setup loading states for specific page types
     */
    private function setupLoadingStates($pageType) {
        echo '<div id="loading-announcements" aria-live="polite" aria-atomic="true" class="sr-only"></div>';
        
        switch ($pageType) {
            case 'checkout':
                echo '<div id="payment-loading" style="display: none;">';
                echo LoadingStates::paymentProcessingLoading();
                echo '</div>';
                break;
                
            case 'browse':
            case 'search':
                echo '<div id="search-loading" style="display: none;">';
                echo LoadingStates::searchLoading();
                echo '</div>';
                break;
                
            case 'dashboard':
            case 'profile':
                echo '<div id="data-loading" style="display: none;">';
                echo LoadingStates::dataFetchLoading('user data');
                echo '</div>';
                break;
                
            case 'sell':
            case 'listing':
                echo '<div id="upload-loading" style="display: none;">';
                echo LoadingStates::imageUploadLoading();
                echo '</div>';
                break;
        }
        
        // Universal loading overlay
        echo LoadingStates::overlay();
    }
    
    /**
     * Setup empty states for specific page types
     */
    private function setupEmptyStates($pageType) {
        // Add empty state templates to page
        echo '<script type="text/template" id="empty-state-templates">';
        
        switch ($pageType) {
            case 'browse':
                echo json_encode([
                    'no_products' => EmptyStates::noProducts(),
                    'no_search_results' => EmptyStates::noSearchResults(),
                    'seasonal_empty' => EmptyStates::seasonalEmpty(),
                    'no_local_content' => EmptyStates::noLocalContent()
                ]);
                break;
                
            case 'cart':
                echo json_encode([
                    'empty_cart' => EmptyStates::emptyCart()
                ]);
                break;
                
            case 'orders':
                echo json_encode([
                    'no_orders' => EmptyStates::noOrders()
                ]);
                break;
                
            case 'messages':
                echo json_encode([
                    'no_messages' => EmptyStates::noMessages()
                ]);
                break;
                
            case 'dashboard':
                echo json_encode([
                    'no_listings' => EmptyStates::noListings(),
                    'no_orders' => EmptyStates::noOrders('farmer')
                ]);
                break;
                
            default:
                echo json_encode([
                    'connection_error' => EmptyStates::connectionError(),
                    'maintenance' => EmptyStates::maintenance(),
                    'no_data' => EmptyStates::noDataAvailable(),
                    'slow_loading' => EmptyStates::slowLoading(),
                    'offline' => EmptyStates::offlineMode()
                ]);
        }
        
        echo '</script>';
    }
    
    /**
     * Optimize assets for the current page
     */
    private function optimizePageAssets($pageType, $options) {
        // Generate critical CSS for above-the-fold content
        $criticalSelectors = $this->getCriticalSelectorsForPage($pageType);
        $criticalCSS = $this->performanceOptimizer->generateCriticalCSS([
            'assets/css/variables.css',
            'assets/css/base.css',
            'css/layout.css'
        ], $criticalSelectors);
        
        if ($criticalCSS) {
            echo "<style id=\"critical-css\">{$criticalCSS}</style>";
        }
        
        // Preload critical images
        if (isset($options['critical_images'])) {
            echo $this->assetOptimizer->generateCriticalImagePreloads($options['critical_images']);
        }
        
        // Add resource hints
        $this->addResourceHints($pageType);
    }
    
    /**
     * Get critical CSS selectors for specific page types
     */
    private function getCriticalSelectorsForPage($pageType) {
        $baseSelectors = [
            'body', 'html', '.page', '.page-main', '.container',
            '.header', '.nav', '.nav-brand', '.nav-toggle', '.nav-links',
            '.btn', '.btn-primary', '.btn-secondary', '.sr-only'
        ];
        
        $pageSelectors = [];
        
        switch ($pageType) {
            case 'landing':
                $pageSelectors = ['.hero', '.hero-content', '.hero-title', '.hero-actions'];
                break;
                
            case 'browse':
                $pageSelectors = ['.products-grid', '.product-card', '.search-form'];
                break;
                
            case 'cart':
                $pageSelectors = ['.cart-layout', '.cart-items', '.cart-summary'];
                break;
                
            case 'checkout':
                $pageSelectors = ['.checkout-form', '.payment-section'];
                break;
        }
        
        return array_merge($baseSelectors, $pageSelectors);
    }
    
    /**
     * Add resource hints for better performance
     */
    private function addResourceHints($pageType) {
        // DNS prefetch for external resources
        echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">';
        echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">';
        
        // Preconnect to critical origins
        echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>';
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
        
        // Preload critical fonts
        echo '<link rel="preload" href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&display=swap" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
        echo '<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&display=swap"></noscript>';
        
        // Page-specific preloads
        switch ($pageType) {
            case 'browse':
                echo '<link rel="prefetch" href="index.php?page=cart">';
                break;
                
            case 'cart':
                echo '<link rel="prefetch" href="index.php?page=checkout">';
                break;
        }
    }
    
    /**
     * Add performance enhancements
     */
    private function addPerformanceEnhancements() {
        // Service worker registration
        echo '<script>
        if ("serviceWorker" in navigator) {
            window.addEventListener("load", function() {
                navigator.serviceWorker.register("/sw.js")
                    .then(function(registration) {
                        console.log("SW registered: ", registration);
                    })
                    .catch(function(registrationError) {
                        console.log("SW registration failed: ", registrationError);
                    });
            });
        }
        </script>';
        
        // Performance monitoring
        echo $this->performanceOptimizer->getPerformanceMonitoringScript();
        
        // Lazy loading enhancement
        echo AssetOptimizer::getLazyLoadingScript();
    }
    
    /**
     * Add accessibility enhancements
     */
    private function addAccessibilityEnhancements() {
        // Skip links
        echo '<div class="skip-links">';
        echo '<a href="#main-content" class="skip-link">Skip to main content</a>';
        echo '<a href="#navigation" class="skip-link">Skip to navigation</a>';
        echo '</div>';
        
        // Live regions for dynamic content
        echo '<div id="live-region-polite" aria-live="polite" aria-atomic="true" class="sr-only"></div>';
        echo '<div id="live-region-assertive" aria-live="assertive" aria-atomic="true" class="sr-only"></div>';
        
        // Focus management
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            // Ensure main content is focusable
            const mainContent = document.getElementById("main-content") || document.querySelector("main") || document.querySelector(".page-main");
            if (mainContent && !mainContent.hasAttribute("tabindex")) {
                mainContent.setAttribute("tabindex", "-1");
            }
            
            // Add focus indicators
            document.addEventListener("keydown", function(e) {
                if (e.key === "Tab") {
                    document.body.classList.add("keyboard-navigation");
                }
            });
            
            document.addEventListener("mousedown", function() {
                document.body.classList.remove("keyboard-navigation");
            });
        });
        </script>';
    }
    
    /**
     * Generate optimized image HTML
     */
    public function optimizedImage($src, $alt = '', $class = '', $sizes = '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw') {
        return AssetOptimizer::optimizedLazyImage($src, $alt, $class, $sizes);
    }
    
    /**
     * Show loading state for specific operation
     */
    public function showLoadingState($type, $context = '') {
        switch ($type) {
            case 'form':
                return LoadingStates::formLoading();
            case 'button':
                return LoadingStates::buttonLoading();
            case 'search':
                return LoadingStates::searchLoading();
            case 'upload':
                return LoadingStates::imageUploadLoading();
            case 'payment':
                return LoadingStates::paymentProcessingLoading();
            case 'async':
                return LoadingStates::asyncOperationLoading($context);
            case 'data':
                return LoadingStates::dataFetchLoading($context);
            default:
                return LoadingStates::spinner();
        }
    }
    
    /**
     * Show empty state for specific scenario
     */
    public function showEmptyState($type, $context = '', $userType = 'customer') {
        switch ($type) {
            case 'no_products':
                return EmptyStates::noProducts($userType);
            case 'no_orders':
                return EmptyStates::noOrders($userType);
            case 'no_messages':
                return EmptyStates::noMessages();
            case 'empty_cart':
                return EmptyStates::emptyCart();
            case 'no_search_results':
                return EmptyStates::noSearchResults($context);
            case 'no_listings':
                return EmptyStates::noListings();
            case 'no_favorites':
                return EmptyStates::noFavorites();
            case 'no_reviews':
                return EmptyStates::noReviews($context);
            case 'connection_error':
                return EmptyStates::connectionError();
            case 'maintenance':
                return EmptyStates::maintenance();
            case 'access_denied':
                return EmptyStates::accessDenied();
            case 'no_data':
                return EmptyStates::noDataAvailable($context);
            case 'slow_loading':
                return EmptyStates::slowLoading($context);
            case 'offline':
                return EmptyStates::offlineMode();
            case 'coming_soon':
                return EmptyStates::comingSoon($context);
            case 'seasonal':
                return EmptyStates::seasonalEmpty($context);
            case 'no_local':
                return EmptyStates::noLocalContent($context);
            default:
                return EmptyStates::custom('❓', 'No Content', 'No content available at this time.');
        }
    }
    
    /**
     * Get JavaScript for enhanced loading and empty states
     */
    public function getEnhancementScript() {
        return '
        <script>
        // Enhanced Polish Integration
        class PolishIntegration {
            constructor() {
                this.init();
            }
            
            init() {
                this.setupLoadingStates();
                this.setupEmptyStates();
                this.setupImageOptimization();
                this.setupPerformanceMonitoring();
            }
            
            setupLoadingStates() {
                // Auto-show loading for forms
                document.addEventListener("submit", (e) => {
                    const form = e.target;
                    if (form.tagName === "FORM" && !form.hasAttribute("data-no-loading")) {
                        this.showFormLoading(form);
                    }
                });
                
                // Auto-show loading for async buttons
                document.querySelectorAll("[data-async]").forEach(button => {
                    button.addEventListener("click", (e) => {
                        const operation = button.getAttribute("data-async");
                        this.showAsyncLoading(operation);
                    });
                });
            }
            
            setupEmptyStates() {
                // Check for empty containers and show appropriate states
                this.checkEmptyContainers();
                
                // Set up network error detection
                window.addEventListener("offline", () => {
                    this.showOfflineState();
                });
                
                window.addEventListener("online", () => {
                    this.hideOfflineState();
                });
            }
            
            setupImageOptimization() {
                // Enhanced lazy loading with intersection observer
                if ("IntersectionObserver" in window) {
                    const imageObserver = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                const img = entry.target;
                                this.loadOptimizedImage(img);
                                imageObserver.unobserve(img);
                            }
                        });
                    }, { rootMargin: "50px" });
                    
                    document.querySelectorAll("img[data-src]").forEach(img => {
                        imageObserver.observe(img);
                    });
                }
            }
            
            setupPerformanceMonitoring() {
                // Monitor page performance
                window.addEventListener("load", () => {
                    if ("performance" in window) {
                        const perfData = performance.getEntriesByType("navigation")[0];
                        if (perfData && perfData.loadEventEnd - perfData.loadEventStart > 3000) {
                            this.announceSlowLoading();
                        }
                    }
                });
            }
            
            showFormLoading(form) {
                if (window.LoadingStateManager) {
                    window.LoadingStateManager.showFormLoading(form);
                }
            }
            
            showAsyncLoading(operation) {
                if (window.loadingEnhancements) {
                    window.loadingEnhancements.showAsyncOperationLoading(operation);
                }
            }
            
            checkEmptyContainers() {
                const containers = document.querySelectorAll("[data-empty-check]");
                containers.forEach(container => {
                    if (this.isContainerEmpty(container)) {
                        const emptyType = container.getAttribute("data-empty-type") || "no_data";
                        this.showEmptyState(container, emptyType);
                    }
                });
            }
            
            isContainerEmpty(container) {
                const content = container.textContent.trim();
                const hasVisibleChildren = container.querySelectorAll(":not(.sr-only):not([aria-hidden=true])").length > 0;
                return !content && !hasVisibleChildren;
            }
            
            showEmptyState(container, type) {
                if (window.loadingEnhancements) {
                    const config = this.getEmptyStateConfig(type);
                    window.loadingEnhancements.showEnhancedEmptyState(container, config);
                }
            }
            
            getEmptyStateConfig(type) {
                const configs = {
                    no_data: {
                        icon: "📊",
                        title: "No Data Available",
                        description: "We couldn\'t load any data at this time.",
                        action: { text: "Refresh", url: "javascript:location.reload()" }
                    },
                    connection_error: {
                        icon: "📡",
                        title: "Connection Problem",
                        description: "We\'re having trouble connecting to our servers.",
                        type: "error",
                        action: { text: "Try Again", url: "javascript:location.reload()" }
                    }
                };
                
                return configs[type] || configs.no_data;
            }
            
            showOfflineState() {
                const offlineNotice = document.createElement("div");
                offlineNotice.id = "offline-notice";
                offlineNotice.className = "alert alert-warning";
                offlineNotice.innerHTML = "📡 You\'re offline. Some features may not be available.";
                document.body.insertBefore(offlineNotice, document.body.firstChild);
            }
            
            hideOfflineState() {
                const offlineNotice = document.getElementById("offline-notice");
                if (offlineNotice) {
                    offlineNotice.remove();
                }
            }
            
            loadOptimizedImage(img) {
                const src = img.getAttribute("data-src");
                if (src) {
                    img.src = src;
                    img.classList.add("loaded");
                    img.removeAttribute("data-src");
                }
            }
            
            announceSlowLoading() {
                const liveRegion = document.getElementById("live-region-polite");
                if (liveRegion) {
                    liveRegion.textContent = "Page loaded. Connection may be slow.";
                }
            }
        }
        
        // Initialize when DOM is ready
        document.addEventListener("DOMContentLoaded", () => {
            window.polishIntegration = new PolishIntegration();
        });
        </script>';
    }
}