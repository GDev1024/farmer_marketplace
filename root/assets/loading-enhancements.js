/**
 * Loading Enhancements
 * Provides enhanced loading states and user feedback
 */

class LoadingEnhancements {
    constructor() {
        this.init();
    }
    
    init() {
        this.setupFormLoadingStates();
        this.setupImageLazyLoading();
        this.setupProgressiveEnhancement();
        this.setupLoadingTimeouts();
    }
    
    /**
     * Setup form loading states
     */
    setupFormLoadingStates() {
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.tagName === 'FORM') {
                this.showFormLoading(form);
            }
        });
    }
    
    /**
     * Show loading state for form
     */
    showFormLoading(form) {
        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
        if (submitBtn) {
            this.showButtonLoading(submitBtn);
        }
        
        // Disable form inputs
        const inputs = form.querySelectorAll('input, select, textarea, button');
        inputs.forEach(input => {
            input.disabled = true;
        });
        
        // Show loading overlay if form has data-loading-overlay
        if (form.hasAttribute('data-loading-overlay')) {
            this.showLoadingOverlay(form.getAttribute('data-loading-message') || 'Processing...');
        }
    }
    
    /**
     * Show button loading state
     */
    showButtonLoading(button) {
        if (button.classList.contains('loading')) return;
        
        button.classList.add('loading');
        button.disabled = true;
        
        const originalText = button.textContent;
        const loadingText = button.getAttribute('data-loading-text') || 'Processing...';
        
        // Store original text
        button.setAttribute('data-original-text', originalText);
        
        // Create loading content
        const loadingContent = document.createElement('span');
        loadingContent.className = 'btn-loading';
        loadingContent.innerHTML = `
            <span class="spinner-small"></span>
            ${loadingText}
        `;
        
        // Hide original text and show loading
        const textSpan = button.querySelector('.btn-text') || button;
        if (textSpan === button) {
            button.innerHTML = '';
            button.appendChild(loadingContent);
        } else {
            textSpan.style.display = 'none';
            button.appendChild(loadingContent);
        }
    }
    
    /**
     * Hide button loading state
     */
    hideButtonLoading(button) {
        button.classList.remove('loading');
        button.disabled = false;
        
        const originalText = button.getAttribute('data-original-text');
        const loadingSpan = button.querySelector('.btn-loading');
        
        if (loadingSpan) {
            loadingSpan.remove();
        }
        
        const textSpan = button.querySelector('.btn-text');
        if (textSpan) {
            textSpan.style.display = '';
        } else if (originalText) {
            button.textContent = originalText;
        }
        
        button.removeAttribute('data-original-text');
    }
    
    /**
     * Show loading overlay
     */
    showLoadingOverlay(message = 'Loading...', showProgress = false) {
        // Remove existing overlay
        this.hideLoadingOverlay();
        
        const overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.className = 'loading-overlay';
        
        const progressBar = showProgress ? `
            <div class="loading-progress">
                <div class="progress-bar">
                    <div class="progress-fill" id="loading-progress-fill"></div>
                </div>
                <div class="progress-text" id="loading-progress-text">0%</div>
            </div>
        ` : '';
        
        overlay.innerHTML = `
            <div class="loading-content">
                <div class="loading-spinner large">
                    <div class="spinner"></div>
                </div>
                <p class="loading-message">${message}</p>
                ${progressBar}
            </div>
        `;
        
        document.body.appendChild(overlay);
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        
        // Auto-hide after 30 seconds (safety)
        setTimeout(() => {
            this.hideLoadingOverlay();
        }, 30000);
    }
    
    /**
     * Hide loading overlay
     */
    hideLoadingOverlay() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.remove();
            document.body.style.overflow = '';
        }
    }
    
    /**
     * Show loading state for async operations
     */
    showAsyncOperationLoading(operation = 'Processing', showProgress = false) {
        // Remove existing overlay
        this.hideLoadingOverlay();
        
        const overlay = document.createElement('div');
        overlay.id = 'async-loading-overlay';
        overlay.className = 'loading-overlay async-operation';
        
        const progressBar = showProgress ? `
            <div class="loading-progress">
                <div class="progress-bar">
                    <div class="progress-fill" id="async-progress-fill"></div>
                </div>
                <div class="progress-text" id="async-progress-text">0%</div>
            </div>
        ` : '';
        
        overlay.innerHTML = `
            <div class="loading-content">
                <div class="loading-spinner large">
                    <div class="spinner"></div>
                </div>
                <h3 class="loading-title">${operation}</h3>
                <p class="loading-message">This may take a few moments. Please don't close this window.</p>
                ${progressBar}
                <div class="loading-security-note">
                    <small>🔒 Your data is being processed securely</small>
                </div>
            </div>
        `;
        
        overlay.setAttribute('role', 'status');
        overlay.setAttribute('aria-label', operation);
        
        document.body.appendChild(overlay);
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        
        // Auto-hide after 60 seconds (safety for long operations)
        setTimeout(() => {
            this.hideLoadingOverlay();
        }, 60000);
    }
    
    /**
     * Show loading state for payment processing
     */
    showPaymentProcessing() {
        this.showAsyncOperationLoading('Processing Payment', true);
        
        // Simulate payment progress
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90; // Don't complete automatically
            
            this.updateLoadingProgress(progress);
            
            if (progress >= 90) {
                clearInterval(progressInterval);
            }
        }, 500);
        
        return progressInterval;
    }
    
    /**
     * Show loading state for file uploads
     */
    showFileUploadLoading(fileName = '') {
        const message = fileName ? `Uploading ${fileName}` : 'Uploading file';
        this.showAsyncOperationLoading(message, true);
    }
    
    /**
     * Show loading state for data synchronization
     */
    showDataSyncLoading() {
        this.showAsyncOperationLoading('Synchronizing Data', false);
    }
    
    /**
     * Setup image lazy loading
     */
    setupImageLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        this.loadImage(img);
                        observer.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[loading="lazy"]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }
    
    /**
     * Load image with fade-in effect
     */
    loadImage(img) {
        img.style.opacity = '0';
        
        const tempImg = new Image();
        tempImg.onload = () => {
            img.src = tempImg.src;
            img.style.opacity = '1';
        };
        
        tempImg.onerror = () => {
            // Show placeholder on error
            img.style.opacity = '1';
            img.alt = 'Image failed to load';
        };
        
        tempImg.src = img.src;
    }
    
    /**
     * Setup progressive enhancement
     */
    setupProgressiveEnhancement() {
        // Add loading states to AJAX requests
        if (window.fetch) {
            const originalFetch = window.fetch;
            window.fetch = (...args) => {
                // Show loading for longer requests
                const loadingTimeout = setTimeout(() => {
                    this.showLoadingOverlay('Loading...');
                }, 500);
                
                return originalFetch(...args)
                    .then(response => {
                        clearTimeout(loadingTimeout);
                        this.hideLoadingOverlay();
                        return response;
                    })
                    .catch(error => {
                        clearTimeout(loadingTimeout);
                        this.hideLoadingOverlay();
                        throw error;
                    });
            };
        }
        
        // Add loading states to XMLHttpRequest
        if (window.XMLHttpRequest) {
            const originalOpen = XMLHttpRequest.prototype.open;
            const originalSend = XMLHttpRequest.prototype.send;
            
            XMLHttpRequest.prototype.open = function(...args) {
                this._startTime = Date.now();
                return originalOpen.apply(this, args);
            };
            
            XMLHttpRequest.prototype.send = function(...args) {
                const loadingTimeout = setTimeout(() => {
                    window.loadingEnhancements.showLoadingOverlay('Loading...');
                }, 500);
                
                this.addEventListener('loadend', () => {
                    clearTimeout(loadingTimeout);
                    window.loadingEnhancements.hideLoadingOverlay();
                });
                
                return originalSend.apply(this, args);
            };
        }
        
        // Add loading states to form submissions
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.tagName === 'FORM' && !form.hasAttribute('data-no-loading')) {
                const operation = form.getAttribute('data-operation') || 'Processing';
                this.showAsyncOperationLoading(operation);
            }
        });
    }
    
    /**
     * Setup loading timeouts
     */
    setupLoadingTimeouts() {
        // Auto-hide loading states after reasonable time
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                this.hideLoadingOverlay();
                
                // Reset any stuck loading buttons
                document.querySelectorAll('.btn.loading').forEach(btn => {
                    this.hideButtonLoading(btn);
                });
            }, 10000); // 10 seconds
        });
    }
    
    /**
     * Show skeleton loader
     */
    showSkeletonLoader(container, type = 'card', count = 3) {
        if (typeof container === 'string') {
            container = document.querySelector(container);
        }
        
        if (!container) return;
        
        let skeletonHTML = '';
        
        switch (type) {
            case 'card':
                skeletonHTML = this.generateCardSkeleton(count);
                break;
            case 'table':
                skeletonHTML = this.generateTableSkeleton(count);
                break;
            case 'form':
                skeletonHTML = this.generateFormSkeleton();
                break;
            default:
                skeletonHTML = this.generateCardSkeleton(count);
        }
        
        container.innerHTML = skeletonHTML;
    }
    
    /**
     * Generate card skeleton HTML
     */
    generateCardSkeleton(count) {
        let html = '<div class="skeleton-container">';
        
        for (let i = 0; i < count; i++) {
            html += `
                <div class="skeleton-card">
                    <div class="skeleton-image"></div>
                    <div class="skeleton-content">
                        <div class="skeleton-line skeleton-title"></div>
                        <div class="skeleton-line skeleton-text"></div>
                        <div class="skeleton-line skeleton-text short"></div>
                        <div class="skeleton-actions">
                            <div class="skeleton-button"></div>
                            <div class="skeleton-button small"></div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        html += '</div>';
        return html;
    }
    
    /**
     * Generate table skeleton HTML
     */
    generateTableSkeleton(rows = 5) {
        let html = '<div class="skeleton-table">';
        
        // Header
        html += `
            <div class="skeleton-table-header">
                <div class="skeleton-line skeleton-header"></div>
                <div class="skeleton-line skeleton-header"></div>
                <div class="skeleton-line skeleton-header"></div>
                <div class="skeleton-line skeleton-header"></div>
            </div>
        `;
        
        // Rows
        for (let i = 0; i < rows; i++) {
            html += `
                <div class="skeleton-table-row">
                    <div class="skeleton-line skeleton-cell"></div>
                    <div class="skeleton-line skeleton-cell"></div>
                    <div class="skeleton-line skeleton-cell"></div>
                    <div class="skeleton-line skeleton-cell"></div>
                </div>
            `;
        }
        
        html += '</div>';
        return html;
    }
    
    /**
     * Generate form skeleton HTML
     */
    generateFormSkeleton() {
        return `
            <div class="skeleton-form">
                <div class="skeleton-line skeleton-title"></div>
                <div class="skeleton-field">
                    <div class="skeleton-line skeleton-label"></div>
                    <div class="skeleton-line skeleton-input"></div>
                </div>
                <div class="skeleton-field">
                    <div class="skeleton-line skeleton-label"></div>
                    <div class="skeleton-line skeleton-input"></div>
                </div>
                <div class="skeleton-field">
                    <div class="skeleton-line skeleton-label"></div>
                    <div class="skeleton-line skeleton-textarea"></div>
                </div>
                <div class="skeleton-actions">
                    <div class="skeleton-button"></div>
                    <div class="skeleton-button secondary"></div>
                </div>
            </div>
        `;
    }
    
    /**
     * Show inline loading state
     */
    showInlineLoading(container, message = 'Loading...') {
        if (typeof container === 'string') {
            container = document.querySelector(container);
        }
        
        if (!container) return;
        
        container.innerHTML = `
            <div class="loading-inline">
                <div class="spinner-small"></div>
                <span class="loading-text">${message}</span>
            </div>
        `;
    }
    
    /**
     * Update loading progress
     */
    updateLoadingProgress(percentage) {
        const progressFill = document.getElementById('loading-progress-fill') || document.getElementById('async-progress-fill');
        const progressText = document.getElementById('loading-progress-text') || document.getElementById('async-progress-text');
        
        if (progressFill && progressText) {
            progressFill.style.width = `${percentage}%`;
            progressText.textContent = `${Math.round(percentage)}%`;
        }
    }
    
    /**
     * Show empty state with enhanced options
     */
    showEnhancedEmptyState(container, config) {
        if (typeof container === 'string') {
            container = document.querySelector(container);
        }
        
        if (!container) return;
        
        const actionHTML = config.actions ? 
            config.actions.map(action => `
                <a href="${action.url}" class="${action.class || 'btn btn-secondary'}" ${action.target ? `target="${action.target}"` : ''}>
                    ${action.icon ? `<span class="btn-icon">${action.icon}</span>` : ''}
                    ${action.text}
                </a>
            `).join('') : 
            (config.action ? `
                <a href="${config.action.url}" class="${config.action.class || 'btn btn-secondary'}">
                    ${config.action.text}
                </a>
            ` : '');
        
        const typeClass = config.type ? `empty-state-${config.type}` : '';
        
        container.innerHTML = `
            <div class="empty-state ${typeClass}" role="status" aria-label="${config.title}">
                <div class="empty-icon" aria-hidden="true">${config.icon}</div>
                <h3 class="empty-title">${config.title}</h3>
                <p class="empty-description">${config.description}</p>
                ${config.details ? `<div class="empty-details"><small>${config.details}</small></div>` : ''}
                ${actionHTML ? `<div class="empty-actions">${actionHTML}</div>` : ''}
            </div>
        `;
    }
    
    /**
     * Show network error state
     */
    showNetworkError(container) {
        this.showEnhancedEmptyState(container, {
            icon: '📡',
            title: 'Connection Problem',
            description: 'We\'re having trouble connecting to our servers. Please check your internet connection and try again.',
            details: 'If the problem persists, our servers might be temporarily unavailable.',
            type: 'error',
            actions: [
                { text: 'Try Again', url: 'javascript:location.reload()', class: 'btn btn-primary', icon: '🔄' },
                { text: 'Go Home', url: 'index.php', class: 'btn btn-secondary' }
            ]
        });
    }
    
    /**
     * Show maintenance mode state
     */
    showMaintenanceMode(container) {
        this.showEnhancedEmptyState(container, {
            icon: '🔧',
            title: 'Under Maintenance',
            description: 'We\'re currently performing scheduled maintenance to improve your experience.',
            details: 'Expected completion: Within 1 hour',
            type: 'warning',
            action: { text: 'Check Status', url: 'javascript:location.reload()', class: 'btn btn-primary' }
        });
    }
}

// Global functions for backward compatibility
window.showLoading = function(message) {
    window.loadingEnhancements.showLoadingOverlay(message);
};

window.hideLoading = function() {
    window.loadingEnhancements.hideLoadingOverlay();
};

window.showButtonLoading = function(button) {
    window.loadingEnhancements.showButtonLoading(button);
};

window.hideButtonLoading = function(button) {
    window.loadingEnhancements.hideButtonLoading(button);
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.loadingEnhancements = new LoadingEnhancements();
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LoadingEnhancements;
}