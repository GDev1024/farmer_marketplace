<?php
/**
 * Loading States Component
 * Provides consistent loading indicators across the application
 */

class LoadingStates {
    
    /**
     * Generate loading spinner HTML
     */
    public static function spinner($size = 'md', $color = 'primary') {
        $sizeClass = "spinner-{$size}";
        $colorClass = "spinner-{$color}";
        
        return "
        <div class=\"loading-spinner {$sizeClass} {$colorClass}\" role=\"status\" aria-label=\"Loading\">
            <div class=\"spinner\"></div>
            <span class=\"sr-only\">Loading...</span>
        </div>";
    }
    
    /**
     * Generate loading overlay for full page
     */
    public static function overlay($message = 'Loading...') {
        return "
        <div class=\"loading-overlay\" id=\"loading-overlay\" style=\"display: none;\">
            <div class=\"loading-content\">
                " . self::spinner('lg') . "
                <p class=\"loading-message\">{$message}</p>
            </div>
        </div>";
    }
    
    /**
     * Generate loading skeleton for content
     */
    public static function skeleton($type = 'text', $lines = 3) {
        $skeletonHtml = '<div class="skeleton-container" role="status" aria-label="Loading content">';
        
        switch ($type) {
            case 'card':
                $skeletonHtml .= '
                <div class="skeleton skeleton-image"></div>
                <div class="skeleton skeleton-title"></div>
                <div class="skeleton skeleton-text"></div>
                <div class="skeleton skeleton-text skeleton-short"></div>';
                break;
                
            case 'list':
                for ($i = 0; $i < $lines; $i++) {
                    $skeletonHtml .= '<div class="skeleton skeleton-list-item"></div>';
                }
                break;
                
            case 'text':
            default:
                for ($i = 0; $i < $lines; $i++) {
                    $width = $i === $lines - 1 ? 'skeleton-short' : '';
                    $skeletonHtml .= "<div class=\"skeleton skeleton-text {$width}\"></div>";
                }
                break;
        }
        
        $skeletonHtml .= '<span class="sr-only">Loading content...</span></div>';
        
        return $skeletonHtml;
    }
    
    /**
     * Generate loading button state
     */
    public static function buttonLoading($text = 'Loading...', $originalText = 'Submit') {
        return "
        <button class=\"btn btn-primary\" disabled>
            " . self::spinner('sm') . "
            <span class=\"btn-text\">{$text}</span>
            <span class=\"sr-only\">Processing request</span>
        </button>";
    }
    
    /**
     * Generate loading state for forms
     */
    public static function formLoading() {
        return "
        <div class=\"form-loading\" role=\"status\" aria-label=\"Processing form\">
            <div class=\"form-loading-overlay\"></div>
            <div class=\"form-loading-content\">
                " . self::spinner('md') . "
                <p>Processing your request...</p>
            </div>
        </div>";
    }
    
    /**
     * Generate loading state for tables/lists
     */
    public static function tableLoading($columns = 4, $rows = 5) {
        $html = '<div class="table-loading" role="status" aria-label="Loading data">';
        
        for ($row = 0; $row < $rows; $row++) {
            $html .= '<div class="table-loading-row">';
            for ($col = 0; $col < $columns; $col++) {
                $html .= '<div class="skeleton skeleton-table-cell"></div>';
            }
            $html .= '</div>';
        }
        
        $html .= '<span class="sr-only">Loading table data...</span></div>';
        
        return $html;
    }
    
    /**
     * Generate loading state for search results
     */
    public static function searchLoading() {
        return "
        <div class=\"search-loading\" role=\"status\" aria-label=\"Searching\">
            <div class=\"search-loading-header\">
                " . self::spinner('sm') . "
                <span>Searching for products...</span>
            </div>
            <div class=\"search-results-skeleton\">
                " . self::skeleton('card') . "
                " . self::skeleton('card') . "
                " . self::skeleton('card') . "
            </div>
        </div>";
    }
    
    /**
     * Generate loading state for image uploads
     */
    public static function imageUploadLoading() {
        return "
        <div class=\"image-upload-loading\" role=\"status\" aria-label=\"Uploading image\">
            <div class=\"upload-progress\">
                <div class=\"upload-icon\">📤</div>
                " . self::spinner('sm') . "
                <p>Uploading image...</p>
                <div class=\"progress-bar\">
                    <div class=\"progress-fill\" style=\"width: 0%\"></div>
                </div>
            </div>
        </div>";
    }
    
    /**
     * Generate loading state for async operations
     */
    public static function asyncOperationLoading($operation = 'Processing') {
        return "
        <div class=\"async-loading\" role=\"status\" aria-label=\"{$operation}\">
            <div class=\"async-loading-content\">
                " . self::spinner('md') . "
                <p class=\"async-loading-text\">{$operation}...</p>
                <div class=\"async-loading-details\">
                    <small>This may take a few moments</small>
                </div>
            </div>
        </div>";
    }
    
    /**
     * Generate loading state for data fetching
     */
    public static function dataFetchLoading($dataType = 'data') {
        return "
        <div class=\"data-fetch-loading\" role=\"status\" aria-label=\"Loading {$dataType}\">
            <div class=\"data-fetch-content\">
                " . self::spinner('lg') . "
                <h3>Loading {$dataType}...</h3>
                <p>Please wait while we fetch the latest information.</p>
            </div>
        </div>";
    }
    
    /**
     * Generate loading state for payment processing
     */
    public static function paymentProcessingLoading() {
        return "
        <div class=\"payment-loading\" role=\"status\" aria-label=\"Processing payment\">
            <div class=\"payment-loading-content\">
                <div class=\"payment-icon\">💳</div>
                " . self::spinner('lg') . "
                <h3>Processing Payment</h3>
                <p>Please do not close this window or refresh the page.</p>
                <div class=\"payment-security-note\">
                    <small>🔒 Your payment is being processed securely</small>
                </div>
            </div>
        </div>";
    }
    
    /**
     * Generate JavaScript for loading state management
     */
    public static function getJavaScript() {
        return "
        <script>
        // Loading State Management
        class LoadingStateManager {
            static show(elementId, type = 'spinner') {
                const element = document.getElementById(elementId);
                if (!element) return;
                
                element.style.display = 'flex';
                element.setAttribute('aria-hidden', 'false');
                
                // Announce to screen readers
                const announcement = document.getElementById('loading-announcements');
                if (announcement) {
                    announcement.textContent = 'Loading content, please wait...';
                }
            }
            
            static hide(elementId) {
                const element = document.getElementById(elementId);
                if (!element) return;
                
                element.style.display = 'none';
                element.setAttribute('aria-hidden', 'true');
                
                // Announce completion to screen readers
                const announcement = document.getElementById('loading-announcements');
                if (announcement) {
                    announcement.textContent = 'Content loaded successfully.';
                }
            }
            
            static showButtonLoading(buttonElement, loadingText = 'Loading...') {
                if (!buttonElement) return;
                
                const originalText = buttonElement.textContent;
                buttonElement.disabled = true;
                buttonElement.innerHTML = `
                    <span class=\"loading-spinner spinner-sm\">
                        <div class=\"spinner\"></div>
                    </span>
                    <span class=\"btn-text\">${loadingText}</span>
                `;
                buttonElement.setAttribute('aria-label', loadingText);
                
                return originalText;
            }
            
            static hideButtonLoading(buttonElement, originalText) {
                if (!buttonElement) return;
                
                buttonElement.disabled = false;
                buttonElement.textContent = originalText;
                buttonElement.removeAttribute('aria-label');
            }
            
            static showFormLoading(formElement) {
                if (!formElement) return;
                
                const loadingOverlay = document.createElement('div');
                loadingOverlay.className = 'form-loading';
                loadingOverlay.innerHTML = `
                    <div class=\"form-loading-overlay\"></div>
                    <div class=\"form-loading-content\">
                        <div class=\"loading-spinner spinner-md\">
                            <div class=\"spinner\"></div>
                        </div>
                        <p>Processing your request...</p>
                    </div>
                `;
                loadingOverlay.setAttribute('role', 'status');
                loadingOverlay.setAttribute('aria-label', 'Processing form');
                
                formElement.style.position = 'relative';
                formElement.appendChild(loadingOverlay);
                
                return loadingOverlay;
            }
            
            static showAsyncLoading(operation = 'Processing') {
                const overlay = document.createElement('div');
                overlay.id = 'async-loading-overlay';
                overlay.className = 'loading-overlay';
                overlay.innerHTML = `
                    <div class="loading-content">
                        <div class="loading-spinner large">
                            <div class="spinner"></div>
                        </div>
                        <h3>${operation}...</h3>
                        <p>This may take a few moments</p>
                    </div>
                `;
                overlay.setAttribute('role', 'status');
                overlay.setAttribute('aria-label', operation);
                
                document.body.appendChild(overlay);
                document.body.style.overflow = 'hidden';
                
                // Auto-hide after 30 seconds (safety)
                setTimeout(() => {
                    this.hideAsyncLoading();
                }, 30000);
            }
            
            static hideAsyncLoading() {
                const overlay = document.getElementById('async-loading-overlay');
                if (overlay) {
                    overlay.remove();
                    document.body.style.overflow = '';
                }
            }
            
            static showDataLoading(container, dataType = 'data') {
                if (typeof container === 'string') {
                    container = document.getElementById(container);
                }
                
                if (!container) return;
                
                container.innerHTML = `
                    <div class="data-fetch-loading" role="status" aria-label="Loading ${dataType}">
                        <div class="data-fetch-content">
                            <div class="loading-spinner large">
                                <div class="spinner"></div>
                            </div>
                            <h3>Loading ${dataType}...</h3>
                            <p>Please wait while we fetch the latest information.</p>
                        </div>
                    </div>
                `;
            }
        }
        
        // Auto-attach loading states to forms
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    // Show loading state
                    const loadingOverlay = LoadingStateManager.showFormLoading(form);
                    
                    // Hide loading after 5 seconds (fallback)
                    setTimeout(() => {
                        LoadingStateManager.hideFormLoading(form, loadingOverlay);
                    }, 5000);
                });
            });
            
            // Auto-attach loading states to buttons with data-loading attribute
            const loadingButtons = document.querySelectorAll('[data-loading]');
            
            loadingButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const loadingText = button.getAttribute('data-loading') || 'Loading...';
                    const originalText = LoadingStateManager.showButtonLoading(button, loadingText);
                    
                    // Hide loading after 3 seconds (fallback)
                    setTimeout(() => {
                        LoadingStateManager.hideButtonLoading(button, originalText);
                    }, 3000);
                });
            });
            
            // Auto-attach loading states to async operations
            const asyncButtons = document.querySelectorAll('[data-async]');
            
            asyncButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const operation = button.getAttribute('data-async') || 'Processing';
                    const form = button.closest('form');
                    
                    if (form) {
                        e.preventDefault();
                        LoadingStateManager.showAsyncLoading(operation);
                        
                        // Submit form after showing loading
                        setTimeout(() => {
                            form.submit();
                        }, 100);
                    }
                });
            });
            
            // Auto-attach loading states to AJAX requests
            if (window.fetch) {
                const originalFetch = window.fetch;
                window.fetch = function(...args) {
                    LoadingStateManager.show('loading-overlay', 'overlay');
                    
                    return originalFetch.apply(this, args)
                        .then(response => {
                            LoadingStateManager.hide('loading-overlay');
                            return response;
                        })
                        .catch(error => {
                            LoadingStateManager.hide('loading-overlay');
                            throw error;
                        });
                };
            }
        });
        
        // Global loading functions
        window.showLoading = LoadingStateManager.show;
        window.hideLoading = LoadingStateManager.hide;
        window.LoadingStateManager = LoadingStateManager;
        </script>";
    }
}