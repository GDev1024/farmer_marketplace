// Alert System with ARIA Live Region Support
function showAlert(message, type='success') {
    const container = document.getElementById('alert-container');
    if(!container) return;
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.setAttribute('role', 'alert');
    alert.setAttribute('aria-live', 'assertive');
    alert.setAttribute('aria-atomic', 'true');
    alert.innerHTML = `${type==='success'?'✓':'⚠'} ${message}`;
    
    // Add close button
    const closeBtn = document.createElement('button');
    closeBtn.textContent = '×';
    closeBtn.setAttribute('aria-label', 'Close alert');
    closeBtn.style.cssText = 'background: none; border: none; color: inherit; font-size: 1.5rem; cursor: pointer; margin-left: auto;';
    closeBtn.onclick = () => {
        alert.remove();
        // Announce dismissal to screen readers
        announceToScreenReader(`Alert dismissed: ${message}`, 'polite');
    };
    alert.appendChild(closeBtn);
    
    container.appendChild(alert);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if(alert.parentElement) {
            alert.remove();
            announceToScreenReader(`Alert automatically dismissed: ${message}`, 'polite');
        }
    }, 5000);
}

// Form Validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validateForm(formElement) {
    const inputs = formElement.querySelectorAll('[required]');
    let isValid = true;
    let firstErrorField = null;
    
    inputs.forEach(input => {
        const error = input.nextElementSibling?.classList.contains('form-error');
        
        if(input.type === 'email' && !validateEmail(input.value)) {
            showAlert('Invalid email format', 'error');
            announceFormError(input.name || 'Email field', 'Invalid email format');
            if (!firstErrorField) firstErrorField = input;
            input.focus();
            isValid = false;
        } else if(input.value.trim() === '') {
            showAlert('All fields are required', 'error');
            const fieldLabel = input.previousElementSibling?.textContent || input.name || 'Field';
            announceFormError(fieldLabel, 'This field is required');
            if (!firstErrorField) firstErrorField = input;
            input.focus();
            isValid = false;
        }
    });
    
    if (!isValid && firstErrorField) {
        // Focus the first error field and announce validation summary
        firstErrorField.focus();
        announceToScreenReader('Form has validation errors. Please correct the highlighted fields.', 'assertive');
    } else if (isValid) {
        announceToScreenReader('Form validation passed', 'polite');
    }
    
    return isValid;
}

// Cart Functions with ARIA Live Region Support
let cartCount = 0;

function updateCartBadge() {
    const badge = document.querySelector('nav span');
    if(badge) {
        const oldCount = cartCount;
        cartCount = parseInt(badge.textContent) || 0;
        
        // Announce cart changes to screen readers
        if (oldCount !== cartCount) {
            const message = cartCount > oldCount 
                ? `Item added to cart. Cart now has ${cartCount} items.`
                : `Item removed from cart. Cart now has ${cartCount} items.`;
            announceToScreenReader(message, 'polite');
        }
    }
}

function addToCartLocalValidation(form) {
    const quantity = parseInt(form.querySelector('[name="cartQuantity"]').value);
    const max = parseInt(form.querySelector('[name="cartQuantity"]').max);
    
    if(quantity <= 0) {
        showAlert('Quantity must be at least 1', 'error');
        announceToScreenReader('Error: Quantity must be at least 1', 'assertive');
        return false;
    }
    if(quantity > max) {
        const message = `Maximum quantity available: ${max}`;
        showAlert(message, 'error');
        announceToScreenReader(`Error: ${message}`, 'assertive');
        return false;
    }
    return true;
}

// Search Functionality with Live Region Support
function setupSearch() {
    const searchForm = document.querySelector('form[action*="browse"]');
    if(!searchForm) return;
    
    const searchInput = searchForm.querySelector('input[name="search"]');
    const categorySelect = searchForm.querySelector('select[name="category"]');
    
    if(searchInput) {
        searchInput.addEventListener('input', debounce(() => {
            const query = searchInput.value.trim();
            if (query.length > 2) {
                announceToScreenReader(`Searching for: ${query}`, 'polite');
            }
            console.log('Searching for:', searchInput.value);
        }, 300));
    }
    
    if(categorySelect) {
        categorySelect.addEventListener('change', () => {
            const selectedCategory = categorySelect.options[categorySelect.selectedIndex].text;
            announceToScreenReader(`Category filter changed to: ${selectedCategory}`, 'polite');
        });
    }
}

// Debounce Helper
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Quantity Updater for Cart with Live Region Support
function setupQuantityInputs() {
    const quantityInputs = document.querySelectorAll('input[type="number"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', () => {
            const oldValue = input.getAttribute('data-previous-value') || input.defaultValue;
            const newValue = parseInt(input.value);
            
            if (newValue <= 0) {
                input.value = 1;
                showAlert('Quantity must be at least 1', 'warning');
                announceToScreenReader('Quantity changed to minimum value of 1', 'polite');
            } else if (oldValue !== input.value) {
                const productName = input.closest('.cart-item, .order-item')?.querySelector('.item-title')?.textContent || 'item';
                announceToScreenReader(`Quantity for ${productName} changed to ${newValue}`, 'polite');
            }
            
            input.setAttribute('data-previous-value', input.value);
        });
    });
}

// Message Auto-scroll with Live Region Support
function setupMessaging() {
    const messageContainer = document.querySelector('[style*="flex-direction: column"]');
    if(messageContainer) {
        // Scroll to bottom
        setTimeout(() => {
            messageContainer.scrollTop = messageContainer.scrollHeight;
        }, 100);
        
        // Set up mutation observer for new messages
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    // Check if new message was added
                    const newMessages = Array.from(mutation.addedNodes).filter(node => 
                        node.nodeType === Node.ELEMENT_NODE && node.classList.contains('message')
                    );
                    
                    if (newMessages.length > 0) {
                        // Scroll to bottom
                        messageContainer.scrollTop = messageContainer.scrollHeight;
                        
                        // Announce new message to screen readers
                        const lastMessage = newMessages[newMessages.length - 1];
                        const messageText = lastMessage.querySelector('.message__text')?.textContent;
                        const isOwnMessage = lastMessage.classList.contains('message--sent');
                        
                        if (messageText && !isOwnMessage) {
                            announceToScreenReader(`New message received: ${messageText}`, 'polite');
                        }
                    }
                }
            });
        });
        
        observer.observe(messageContainer, { childList: true });
        
        // Auto-refresh messages (optional)
        // setInterval(() => location.reload(), 5000);
    }
}

// Format Currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-GD', {
        style: 'currency',
        currency: 'XCD'
    }).format(amount);
}

// Product Filter with Local Storage
function setupProductFilters() {
    const filterForm = document.querySelector('form[action*="browse"]');
    if(!filterForm) return;
    
    // Save filters to localStorage
    filterForm.addEventListener('submit', function(e) {
        const formData = new FormData(this);
        const filters = Object.fromEntries(formData);
        // Note: Using localStorage for demo only - won't work in Claude artifacts
        // localStorage.setItem('productFilters', JSON.stringify(filters));
    });
}

// Confirm Actions
function confirmAction(message = 'Are you sure?') {
    return confirm(message);
}

// Modal for Product Details (placeholder)
function showProductModal(productId) {
    console.log('Showing product details for ID:', productId);
    // TODO: Implement modal with product details
}

// Enhanced Modal System with Accessibility
class ModalManager {
    constructor() {
        this.activeModal = null;
        this.previousFocus = null;
        this.focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
        this.scrollbarWidth = this.getScrollbarWidth();
        
        // Bind methods
        this.handleKeydown = this.handleKeydown.bind(this);
        this.handleBackdropClick = this.handleBackdropClick.bind(this);
        
        this.init();
    }
    
    init() {
        // Add backdrop elements to existing modals
        document.querySelectorAll('.modal').forEach(modal => {
            if (!modal.querySelector('.modal-backdrop')) {
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop';
                modal.insertBefore(backdrop, modal.firstChild);
            }
        });
        
        // Set up event listeners
        document.addEventListener('keydown', this.handleKeydown);
        document.addEventListener('click', this.handleBackdropClick);
    }
    
    getScrollbarWidth() {
        const outer = document.createElement('div');
        outer.style.visibility = 'hidden';
        outer.style.overflow = 'scroll';
        outer.style.msOverflowStyle = 'scrollbar';
        document.body.appendChild(outer);
        
        const inner = document.createElement('div');
        outer.appendChild(inner);
        
        const scrollbarWidth = outer.offsetWidth - inner.offsetWidth;
        outer.parentNode.removeChild(outer);
        
        return scrollbarWidth;
    }
    
    openModal(modalId, options = {}) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        
        // Store previous focus
        this.previousFocus = document.activeElement;
        
        // Prevent body scroll and compensate for scrollbar
        document.body.classList.add('modal-open');
        document.body.style.setProperty('--scrollbar-width', `${this.scrollbarWidth}px`);
        
        // Show modal
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        
        // Set focus to first focusable element or close button
        setTimeout(() => {
            const focusableElements = modal.querySelectorAll(this.focusableElements);
            const firstFocusable = focusableElements[0];
            const closeButton = modal.querySelector('.modal-close, .close');
            
            if (options.focusFirst && firstFocusable) {
                firstFocusable.focus();
            } else if (closeButton) {
                closeButton.focus();
            }
        }, 100);
        
        this.activeModal = modal;
        
        // Announce to screen readers
        this.announceModal(modal);
    }
    
    closeModal(modalId) {
        const modal = modalId ? document.getElementById(modalId) : this.activeModal;
        if (!modal) return;
        
        // Hide modal
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
        modal.removeAttribute('role');
        modal.removeAttribute('aria-modal');
        
        // Restore body scroll
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('--scrollbar-width');
        
        // Restore focus
        if (this.previousFocus) {
            this.previousFocus.focus();
            this.previousFocus = null;
        }
        
        // Announce modal closure
        announceModalState(false);
        
        this.activeModal = null;
    }
    
    handleKeydown(e) {
        if (!this.activeModal) return;
        
        // Close on Escape
        if (e.key === 'Escape') {
            e.preventDefault();
            this.closeModal();
            return;
        }
        
        // Focus trapping with Tab
        if (e.key === 'Tab') {
            this.trapFocus(e);
        }
    }
    
    trapFocus(e) {
        const focusableElements = this.activeModal.querySelectorAll(this.focusableElements);
        const firstFocusable = focusableElements[0];
        const lastFocusable = focusableElements[focusableElements.length - 1];
        
        if (e.shiftKey) {
            // Shift + Tab
            if (document.activeElement === firstFocusable) {
                e.preventDefault();
                lastFocusable.focus();
            }
        } else {
            // Tab
            if (document.activeElement === lastFocusable) {
                e.preventDefault();
                firstFocusable.focus();
            }
        }
    }
    
    handleBackdropClick(e) {
        if (!this.activeModal) return;
        
        if (e.target.classList.contains('modal-backdrop') || 
            (e.target.classList.contains('modal') && !e.target.querySelector('.modal-content').contains(e.target))) {
            this.closeModal();
        }
    }
    
    announceModal(modal) {
        const title = modal.querySelector('.modal-header h2, .modal-header h3, .modal-title');
        const titleText = title ? title.textContent.trim() : 'Dialog';
        announceModalState(true, titleText);
    }
}

// Initialize modal manager
let modalManager;

// Legacy modal functions for backward compatibility
function openModal(modalId, options = {}) {
    if (!modalManager) modalManager = new ModalManager();
    modalManager.openModal(modalId, options);
}

function closeModal(modalId) {
    if (!modalManager) modalManager = new ModalManager();
    modalManager.closeModal(modalId);
}

// Specific modal functions
function openAddProductModal() {
    document.getElementById('modalTitle').textContent = 'Add New Product';
    document.getElementById('formAction').value = 'create';
    document.getElementById('productForm').reset();
    document.getElementById('imagePreview').innerHTML = '<span class="image-preview-text">Image preview will appear here</span>';
    openModal('productModal', { focusFirst: true });
}

function closeProductModal() {
    closeModal('productModal');
}

function openEditModal() {
    openModal('editModal', { focusFirst: true });
}

function closeEditModal() {
    closeModal('editModal');
}

function openRestockModal() {
    openModal('restockModal', { focusFirst: true });
}

function closeRestockModal() {
    closeModal('restockModal');
}

// Form Submission Handlers
function setupForms() {
    // Login form
    const loginForm = document.querySelector('form[action*="actions.php"]');
    if(loginForm) {
        const emailInput = loginForm.querySelector('input[name="email"]');
        const passwordInput = loginForm.querySelector('input[name="password"]');
        
        if(emailInput && passwordInput) {
            loginForm.addEventListener('submit', function(e) {
                if(!validateEmail(emailInput.value)) {
                    e.preventDefault();
                    showAlert('Please enter a valid email', 'error');
                }
            });
        }
    }
    
    // Password match validation on register
    const registerForm = document.querySelector('form');
    const confirmPasswordInput = registerForm?.querySelector('input[name="confirm"]');
    const passwordInput = registerForm?.querySelector('input[name="password"]');
    
    if(confirmPasswordInput && passwordInput) {
        confirmPasswordInput.addEventListener('blur', function() {
            if(this.value !== passwordInput.value) {
                showAlert('Passwords do not match', 'error');
                this.focus();
            }
        });
    }
}

// Initialize all
document.addEventListener('DOMContentLoaded', function() {
    // Initialize modal manager
    modalManager = new ModalManager();
    
    // Setup all features
    updateCartBadge();
    setupSearch();
    setupQuantityInputs();
    setupMessaging();
    setupProductFilters();
    setupForms();
    setupMobileMenu();
    
    // Keyboard shortcuts
    setupKeyboardShortcuts();
    
    // Accessibility enhancements
    setupAccessibility();
    manageFocus();
    
    console.log('Grenada Farmers Marketplace initialized with accessibility enhancements');
});

// Keyboard Shortcuts
function setupKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ctrl+K for search
        if((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('input[name="search"]');
            if(searchInput) searchInput.focus();
        }
        
        // Ctrl+Enter to submit form
        if((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            const activeForm = document.activeElement?.closest('form');
            if(activeForm) {
                const submitBtn = activeForm.querySelector('button[type="submit"]');
                if(submitBtn) submitBtn.click();
            }
        }
    });
}

// Accessibility Enhancements - Comprehensive WCAG Compliance
function setupAccessibility() {
    // Add aria-labels where needed
    const buttons = document.querySelectorAll('button:not([aria-label]):not([aria-labelledby])');
    buttons.forEach((btn, index) => {
        const text = btn.textContent.trim() || btn.title || btn.getAttribute('data-tooltip');
        if (text) {
            btn.setAttribute('aria-label', text);
        } else {
            btn.setAttribute('aria-label', `Button ${index + 1}`);
        }
    });
    
    // Add roles to empty containers
    const emptyStates = document.querySelectorAll('.empty-state');
    emptyStates.forEach(state => {
        state.setAttribute('role', 'status');
        state.setAttribute('aria-live', 'polite');
    });
    
    // Enhance form accessibility
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        // Add form role if not present
        if (!form.getAttribute('role')) {
            form.setAttribute('role', 'form');
        }
        
        // Enhance form groups
        const formGroups = form.querySelectorAll('.form-group');
        formGroups.forEach(group => {
            const label = group.querySelector('label');
            const input = group.querySelector('input, select, textarea');
            const helpText = group.querySelector('small, .form-text');
            
            if (label && input) {
                // Ensure label is associated with input
                if (!input.id) {
                    input.id = `input-${Math.random().toString(36).substr(2, 9)}`;
                }
                if (!label.getAttribute('for')) {
                    label.setAttribute('for', input.id);
                }
                
                // Associate help text with input
                if (helpText && !helpText.id) {
                    helpText.id = `help-${input.id}`;
                    input.setAttribute('aria-describedby', helpText.id);
                }
            }
        });
    });
    
    // Enhance interactive elements
    const interactiveElements = document.querySelectorAll('a, button, input, select, textarea');
    interactiveElements.forEach(element => {
        // Ensure minimum touch target size (44x44px)
        const rect = element.getBoundingClientRect();
        if (rect.width < 44 || rect.height < 44) {
            element.style.minWidth = '44px';
            element.style.minHeight = '44px';
        }
        
        // Add focus indicators
        if (!element.classList.contains('skip-link')) {
            element.addEventListener('focus', function() {
                this.setAttribute('data-focus-visible', 'true');
            });
            
            element.addEventListener('blur', function() {
                this.removeAttribute('data-focus-visible');
            });
        }
    });
    
    // Enhance product cards with proper semantics
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach((card, index) => {
        if (!card.getAttribute('role')) {
            card.setAttribute('role', 'article');
        }
        
        const title = card.querySelector('.product-card-title, h3');
        if (title && !title.id) {
            title.id = `product-title-${index}`;
            card.setAttribute('aria-labelledby', title.id);
        }
    });
    
    // Enhance navigation with keyboard support
    const navLinks = document.querySelectorAll('.nav-links a');
    navLinks.forEach((link, index) => {
        link.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowDown' || e.key === 'ArrowRight') {
                e.preventDefault();
                const nextLink = navLinks[index + 1] || navLinks[0];
                nextLink.focus();
            } else if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
                e.preventDefault();
                const prevLink = navLinks[index - 1] || navLinks[navLinks.length - 1];
                prevLink.focus();
            }
        });
    });
    
    // Add live region for dynamic content updates
    if (!document.getElementById('live-region')) {
        const liveRegion = document.createElement('div');
        liveRegion.id = 'live-region';
        liveRegion.setAttribute('aria-live', 'polite');
        liveRegion.setAttribute('aria-atomic', 'true');
        liveRegion.className = 'sr-only';
        document.body.appendChild(liveRegion);
    }
    
    // Announce page changes for single-page app behavior
    const currentPage = new URLSearchParams(window.location.search).get('page') || 'home';
    announcePageChange(currentPage);
}

// Announce page changes to screen readers
function announcePageChange(pageName) {
    const liveRegion = document.getElementById('live-region');
    if (liveRegion) {
        const pageNames = {
            'home': 'Home page',
            'browse': 'Browse products page',
            'cart': 'Shopping cart page',
            'checkout': 'Checkout page',
            'orders': 'Order history page',
            'profile': 'User profile page',
            'sell': 'Manage listings page',
            'messages': 'Messages page',
            'login': 'Login page',
            'register': 'Registration page'
        };
        
        const announcement = pageNames[pageName] || `${pageName} page`;
        liveRegion.textContent = `Navigated to ${announcement}`;
        
        // Clear the announcement after a short delay
        setTimeout(() => {
            liveRegion.textContent = '';
        }, 1000);
    }
}

// Core function to announce messages to screen readers
function announceToScreenReader(message, priority = 'polite') {
    // Create or get the appropriate live region
    let liveRegion = document.getElementById(`live-region-${priority}`);
    
    if (!liveRegion) {
        liveRegion = document.createElement('div');
        liveRegion.id = `live-region-${priority}`;
        liveRegion.setAttribute('aria-live', priority);
        liveRegion.setAttribute('aria-atomic', 'true');
        liveRegion.className = 'sr-only';
        document.body.appendChild(liveRegion);
    }
    
    // Set the message
    liveRegion.textContent = message;
    
    // Clear the message after announcement
    setTimeout(() => {
        if (liveRegion.textContent === message) {
            liveRegion.textContent = '';
        }
    }, priority === 'assertive' ? 2000 : 1000);
}

// Enhanced function to announce form validation errors
function announceFormError(fieldName, errorMessage) {
    const message = `${fieldName}: ${errorMessage}`;
    announceToScreenReader(message, 'assertive');
}

// Function to announce loading states
function announceLoadingState(isLoading, context = '') {
    const message = isLoading 
        ? `Loading ${context}...`.trim()
        : `${context} loaded`.trim();
    announceToScreenReader(message, 'polite');
}

// Function to announce modal state changes
function announceModalState(isOpen, modalTitle = '') {
    const message = isOpen 
        ? `Dialog opened: ${modalTitle}`.trim()
        : 'Dialog closed';
    announceToScreenReader(message, 'polite');
}

// Enhanced focus management
function manageFocus() {
    // Trap focus within modals (handled by ModalManager)
    // Restore focus after navigation
    const focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
    
    // Skip to main content functionality
    const skipLinks = document.querySelectorAll('.skip-link');
    skipLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.focus();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}

// Utility: Clone Cart from Session
function getCartItemCount() {
    const badge = document.querySelector('nav span');
    return badge ? parseInt(badge.textContent) : 0;
}

// Utility: Format Date
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString('en-GD', options);
}

// Notification System
class Notification {
    static show(message, type = 'success', duration = 5000) {
        showAlert(message, type);
    }
    
    static success(message) {
        this.show(message, 'success');
    }
    
    static error(message) {
        this.show(message, 'error');
    }
    
    static warning(message) {
        this.show(message, 'warning');
    }
    
    static info(message) {
        this.show(message, 'info');
    }
}

// Export for use in other scripts
if(typeof module !== 'undefined' && module.exports) {
    module.exports = {
        showAlert,
        validateEmail,
        validateForm,
        formatCurrency,
        formatDate,
        confirmAction,
        Notification
    };
}

// Mobile Menu Toggle - Enhanced with Accessibility
function setupMobileMenu() {
    const mobileMenuBtn = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-links');
    const body = document.body;
    
    if(mobileMenuBtn && navMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            const isOpen = navMenu.classList.contains('is-active');
            
            if (isOpen) {
                navMenu.classList.remove('is-active');
                body.classList.remove('nav-open');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
            } else {
                navMenu.classList.add('is-active');
                body.classList.add('nav-open');
                mobileMenuBtn.setAttribute('aria-expanded', 'true');
            }
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileMenuBtn.contains(e.target) && !navMenu.contains(e.target)) {
                navMenu.classList.remove('is-active');
                body.classList.remove('nav-open');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
            }
        });
        
        // Close menu on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && navMenu.classList.contains('is-active')) {
                navMenu.classList.remove('is-active');
                body.classList.remove('nav-open');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
                mobileMenuBtn.focus(); // Return focus to toggle button
            }
        });
    }
}

// Legacy function for backward compatibility
function toggleMobileMenu() {
    const mobileMenuBtn = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-links');
    const body = document.body;
    
    if(mobileMenuBtn && navMenu) {
        const isOpen = navMenu.classList.contains('is-active');
        
        if (isOpen) {
            navMenu.classList.remove('is-active');
            body.classList.remove('nav-open');
            mobileMenuBtn.setAttribute('aria-expanded', 'false');
        } else {
            navMenu.classList.add('is-active');
            body.classList.add('nav-open');
            mobileMenuBtn.setAttribute('aria-expanded', 'true');
        }
    }
}

// Smooth Scrolling
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if(target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

console.log('✓ Grenada Farmers Marketplace JS loaded successfully');