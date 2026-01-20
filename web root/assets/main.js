// Alert System
function showAlert(message, type='success') {
    const container = document.getElementById('alert-container');
    if(!container) return;
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `${type==='success'?'✓':'⚠'} ${message}`;
    
    // Add close button
    const closeBtn = document.createElement('button');
    closeBtn.textContent = '×';
    closeBtn.style.cssText = 'background: none; border: none; color: inherit; font-size: 1.5rem; cursor: pointer; margin-left: auto;';
    closeBtn.onclick = () => alert.remove();
    alert.appendChild(closeBtn);
    
    container.appendChild(alert);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if(alert.parentElement) alert.remove();
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
    
    inputs.forEach(input => {
        const error = input.nextElementSibling?.classList.contains('form-error');
        
        if(input.type === 'email' && !validateEmail(input.value)) {
            showAlert('Invalid email format', 'error');
            input.focus();
            isValid = false;
        } else if(input.value.trim() === '') {
            showAlert('All fields are required', 'error');
            input.focus();
            isValid = false;
        }
    });
    
    return isValid;
}

// Cart Functions
let cartCount = 0;

function updateCartBadge() {
    const badge = document.querySelector('nav span');
    if(badge) {
        cartCount = parseInt(badge.textContent) || 0;
    }
}

function addToCartLocalValidation(form) {
    const quantity = parseInt(form.querySelector('[name="cartQuantity"]').value);
    const max = parseInt(form.querySelector('[name="cartQuantity"]').max);
    
    if(quantity <= 0) {
        showAlert('Quantity must be at least 1', 'error');
        return false;
    }
    if(quantity > max) {
        showAlert(`Maximum quantity available: ${max}`, 'error');
        return false;
    }
    return true;
}

// Search Functionality
function setupSearch() {
    const searchForm = document.querySelector('form[action*="browse"]');
    if(!searchForm) return;
    
    const searchInput = searchForm.querySelector('input[name="search"]');
    const categorySelect = searchForm.querySelector('select[name="category"]');
    
    if(searchInput) {
        searchInput.addEventListener('input', debounce(() => {
            // Client-side search highlighting (optional)
            console.log('Searching for:', searchInput.value);
        }, 300));
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

// Quantity Updater for Cart
function setupQuantityInputs() {
    const quantityInputs = document.querySelectorAll('input[type="number"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', () => {
            if(parseInt(input.value) <= 0) {
                input.value = 1;
                showAlert('Quantity must be at least 1', 'warning');
            }
        });
    });
}

// Message Auto-scroll
function setupMessaging() {
    const messageContainer = document.querySelector('[style*="flex-direction: column"]');
    if(messageContainer) {
        // Scroll to bottom
        setTimeout(() => {
            messageContainer.scrollTop = messageContainer.scrollHeight;
        }, 100);
        
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
    
    // Accessibility
    setupAccessibility();
    
    console.log('Grenada Farmers Marketplace initialized');
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

// Accessibility Enhancements
function setupAccessibility() {
    // Add aria-labels where needed
    const buttons = document.querySelectorAll('button');
    buttons.forEach((btn, index) => {
        if(!btn.getAttribute('aria-label')) {
            btn.setAttribute('aria-label', btn.textContent.trim() || `Button ${index + 1}`);
        }
    });
    
    // Add roles to empty containers
    const emptyStates = document.querySelectorAll('.empty-state');
    emptyStates.forEach(state => {
        state.setAttribute('role', 'status');
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

// Mobile Menu Toggle
function setupMobileMenu() {
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn-new');
    const mobileNav = document.querySelector('.nav-mobile');
    const body = document.body;
    
    if(mobileMenuBtn && mobileNav) {
        mobileMenuBtn.addEventListener('click', () => {
            const isOpen = mobileNav.classList.contains('show');
            
            if (isOpen) {
                mobileNav.classList.remove('show');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
                body.style.overflow = '';
            } else {
                mobileNav.classList.add('show');
                mobileMenuBtn.setAttribute('aria-expanded', 'true');
                body.style.overflow = 'hidden';
            }
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileMenuBtn.contains(e.target) && !mobileNav.contains(e.target)) {
                mobileNav.classList.remove('show');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
                body.style.overflow = '';
            }
        });
        
        // Close menu on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && mobileNav.classList.contains('show')) {
                mobileNav.classList.remove('show');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
                body.style.overflow = '';
            }
        });
    }
}

// User Dropdown Toggle
function toggleUserMenu() {
    const userMenu = document.getElementById('userMenu');
    if (userMenu) {
        userMenu.classList.toggle('show');
    }
}

// Close user menu when clicking outside
document.addEventListener('click', function(e) {
    const userDropdown = document.querySelector('.user-dropdown');
    const userMenu = document.getElementById('userMenu');
    
    if (userDropdown && userMenu && !userDropdown.contains(e.target)) {
        userMenu.classList.remove('show');
    }
});

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