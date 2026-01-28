<main class="auth-page" role="main" aria-labelledby="register-title">
  <div class="auth-container">
    <div class="auth-card">
      <header class="auth-header">
        <h1 id="register-title" class="auth-title">Join Our Community</h1>
        <p class="auth-subtitle">Create your Grenada Farmers account and start connecting with local farmers</p>
      </header>
      
      <form method="POST" action="actions.php" class="auth-form" role="form" aria-labelledby="register-title" novalidate>
        <div class="form-group">
          <label for="name" class="form-label">Full Name</label>
          <input 
            type="text" 
            id="name" 
            name="name" 
            class="form-input" 
            placeholder="Enter your full name"
            required 
            aria-describedby="name-help name-error"
            autocomplete="name"
            minlength="2"
          >
          <small id="name-help" class="form-help">Your name as it will appear to other users</small>
          <div id="name-error" class="form-error" role="alert" aria-live="polite"></div>
        </div>
        
        <div class="form-group">
          <label for="email" class="form-label">Email Address</label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            class="form-input" 
            placeholder="Enter your email address"
            required 
            aria-describedby="email-help email-error"
            autocomplete="email"
            spellcheck="false"
          >
          <small id="email-help" class="form-help">We'll use this to send you order updates and notifications</small>
          <div id="email-error" class="form-error" role="alert" aria-live="polite"></div>
        </div>
        
        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <div class="password-input-wrapper">
            <input 
              type="password" 
              id="password" 
              name="password" 
              class="form-input" 
              placeholder="Create a secure password"
              required 
              aria-describedby="password-help password-error"
              autocomplete="new-password"
              minlength="8"
            >
            <button 
              type="button" 
              class="password-toggle" 
              aria-label="Show password"
              onclick="togglePasswordVisibility('password')"
            >
              <span class="password-toggle-icon" aria-hidden="true">👁️</span>
            </button>
          </div>
          <small id="password-help" class="form-help">Minimum 8 characters with at least one number and special character</small>
          <div id="password-error" class="form-error" role="alert" aria-live="polite"></div>
        </div>
        
        <div class="form-group">
          <label for="confirm" class="form-label">Confirm Password</label>
          <div class="password-input-wrapper">
            <input 
              type="password" 
              id="confirm" 
              name="confirm" 
              class="form-input" 
              placeholder="Confirm your password"
              required 
              aria-describedby="confirm-help confirm-error"
              autocomplete="new-password"
            >
            <button 
              type="button" 
              class="password-toggle" 
              aria-label="Show confirm password"
              onclick="togglePasswordVisibility('confirm')"
            >
              <span class="password-toggle-icon" aria-hidden="true">👁️</span>
            </button>
          </div>
          <small id="confirm-help" class="form-help">Re-enter your password to confirm</small>
          <div id="confirm-error" class="form-error" role="alert" aria-live="polite"></div>
        </div>
        
        <div class="form-group">
          <label for="farmerID" class="form-label">Farmer ID <span class="optional-label">(Optional)</span></label>
          <input 
            type="text" 
            id="farmerID" 
            name="farmerID" 
            class="form-input" 
            placeholder="Enter your Farmer ID for verification"
            aria-describedby="farmerID-help farmerID-error"
            autocomplete="off"
          >
          <small id="farmerID-help" class="form-help">If you're a registered farmer, enter your ID to get verified status and access to seller features</small>
          <div id="farmerID-error" class="form-error" role="alert" aria-live="polite"></div>
        </div>
        
        <div class="form-group">
          <div class="form-checkbox">
            <input type="checkbox" id="terms" name="terms" class="checkbox-input" required aria-describedby="terms-error">
            <label for="terms" class="checkbox-label">
              <span class="checkbox-indicator" aria-hidden="true"></span>
              I agree to the <a href="#" class="auth-link">Terms of Service</a> and <a href="#" class="auth-link">Privacy Policy</a>
            </label>
          </div>
          <div id="terms-error" class="form-error" role="alert" aria-live="polite"></div>
        </div>
        
        <div class="form-group">
          <div class="form-checkbox">
            <input type="checkbox" id="newsletter" name="newsletter" class="checkbox-input">
            <label for="newsletter" class="checkbox-label">
              <span class="checkbox-indicator" aria-hidden="true"></span>
              Send me updates about new farmers, seasonal produce, and special offers
            </label>
          </div>
        </div>
        
        <div class="form-actions">
          <button type="submit" name="register" class="btn btn-primary btn-full" aria-describedby="register-help">
            <span class="btn-icon" aria-hidden="true">🌾</span>
            Create Account
          </button>
          <small id="register-help" class="form-help">By creating an account, you're joining our community of local food enthusiasts</small>
        </div>
      </form>
      
      <footer class="auth-footer">
        <div class="auth-links">
          <a href="index.php?page=login" class="auth-link" aria-label="Sign in to existing account">
            Already have an account? <strong>Sign in</strong>
          </a>
        </div>
        
        <div class="auth-divider">
          <span class="divider-text">or</span>
        </div>
        
        <div class="auth-guest">
          <a href="index.php?page=browse" class="btn btn-secondary btn-full" aria-label="Browse products without creating account">
            <span class="btn-icon" aria-hidden="true">🛍️</span>
            Continue as Guest
          </a>
        </div>
      </footer>
    </div>
    
    <aside class="auth-sidebar" role="complementary" aria-labelledby="farmer-benefits-title">
      <div class="benefits-card">
        <h2 id="farmer-benefits-title" class="benefits-title">For Farmers & Buyers</h2>
        <ul class="benefits-list" role="list">
          <li class="benefit-item" role="listitem">
            <span class="benefit-icon" aria-hidden="true">👨‍🌾</span>
            <div class="benefit-content">
              <h3 class="benefit-title">Sell Your Produce</h3>
              <p class="benefit-description">List your fresh produce and reach local customers directly</p>
            </div>
          </li>
          <li class="benefit-item" role="listitem">
            <span class="benefit-icon" aria-hidden="true">🛒</span>
            <div class="benefit-content">
              <h3 class="benefit-title">Easy Shopping</h3>
              <p class="benefit-description">Browse and order fresh produce from verified local farmers</p>
            </div>
          </li>
          <li class="benefit-item" role="listitem">
            <span class="benefit-icon" aria-hidden="true">💬</span>
            <div class="benefit-content">
              <h3 class="benefit-title">Direct Communication</h3>
              <p class="benefit-description">Message farmers directly about products and availability</p>
            </div>
          </li>
          <li class="benefit-item" role="listitem">
            <span class="benefit-icon" aria-hidden="true">✅</span>
            <div class="benefit-content">
              <h3 class="benefit-title">Verified Quality</h3>
              <p class="benefit-description">All farmers are verified for quality and authenticity</p>
            </div>
          </li>
        </ul>
      </div>
    </aside>
  </div>
</main>

<script>
// Enhanced registration form validation with accessibility
function togglePasswordVisibility(inputId) {
  const input = document.getElementById(inputId);
  const toggle = input.nextElementSibling;
  const icon = toggle.querySelector('.password-toggle-icon');
  
  if (input.type === 'password') {
    input.type = 'text';
    toggle.setAttribute('aria-label', `Hide ${inputId === 'confirm' ? 'confirm ' : ''}password`);
    icon.textContent = '🙈';
  } else {
    input.type = 'password';
    toggle.setAttribute('aria-label', `Show ${inputId === 'confirm' ? 'confirm ' : ''}password`);
    icon.textContent = '👁️';
  }
}

// Registration form validation with accessibility
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('.auth-form');
  const nameInput = document.getElementById('name');
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const confirmInput = document.getElementById('confirm');
  const farmerIDInput = document.getElementById('farmerID');
  const termsInput = document.getElementById('terms');
  
  if (form) {
    // Real-time validation
    nameInput.addEventListener('blur', validateName);
    emailInput.addEventListener('blur', validateEmail);
    passwordInput.addEventListener('blur', validatePassword);
    confirmInput.addEventListener('blur', validateConfirmPassword);
    farmerIDInput.addEventListener('blur', validateFarmerID);
    termsInput.addEventListener('change', validateTerms);
    
    // Password confirmation on input
    confirmInput.addEventListener('input', validateConfirmPassword);
    
    // Form submission
    form.addEventListener('submit', function(e) {
      const isNameValid = validateName();
      const isEmailValid = validateEmail();
      const isPasswordValid = validatePassword();
      const isConfirmValid = validateConfirmPassword();
      const isFarmerIDValid = validateFarmerID();
      const isTermsValid = validateTerms();
      
      if (!isNameValid || !isEmailValid || !isPasswordValid || !isConfirmValid || !isFarmerIDValid || !isTermsValid) {
        e.preventDefault();
        // Focus first invalid field
        const firstError = form.querySelector('.form-input[aria-invalid="true"], .checkbox-input[aria-invalid="true"]');
        if (firstError) {
          firstError.focus();
        }
      }
    });
  }
  
  function validateName() {
    const name = nameInput.value.trim();
    const errorDiv = document.getElementById('name-error');
    
    if (!name) {
      showFieldError(nameInput, errorDiv, 'Full name is required');
      return false;
    } else if (name.length < 2) {
      showFieldError(nameInput, errorDiv, 'Name must be at least 2 characters');
      return false;
    } else if (!/^[a-zA-Z\s'-]+$/.test(name)) {
      showFieldError(nameInput, errorDiv, 'Name can only contain letters, spaces, hyphens, and apostrophes');
      return false;
    } else {
      clearFieldError(nameInput, errorDiv);
      return true;
    }
  }
  
  function validateEmail() {
    const email = emailInput.value.trim();
    const errorDiv = document.getElementById('email-error');
    
    if (!email) {
      showFieldError(emailInput, errorDiv, 'Email address is required');
      return false;
    } else if (!isValidEmail(email)) {
      showFieldError(emailInput, errorDiv, 'Please enter a valid email address');
      return false;
    } else {
      clearFieldError(emailInput, errorDiv);
      return true;
    }
  }
  
  function validatePassword() {
    const password = passwordInput.value;
    const errorDiv = document.getElementById('password-error');
    
    if (!password) {
      showFieldError(passwordInput, errorDiv, 'Password is required');
      return false;
    } else if (password.length < 8) {
      showFieldError(passwordInput, errorDiv, 'Password must be at least 8 characters');
      return false;
    } else if (!/(?=.*[0-9])/.test(password)) {
      showFieldError(passwordInput, errorDiv, 'Password must contain at least one number');
      return false;
    } else if (!/(?=.*[!@#$%^&*])/.test(password)) {
      showFieldError(passwordInput, errorDiv, 'Password must contain at least one special character (!@#$%^&*)');
      return false;
    } else {
      clearFieldError(passwordInput, errorDiv);
      // Re-validate confirm password if it has a value
      if (confirmInput.value) {
        validateConfirmPassword();
      }
      return true;
    }
  }
  
  function validateConfirmPassword() {
    const password = passwordInput.value;
    const confirm = confirmInput.value;
    const errorDiv = document.getElementById('confirm-error');
    
    if (!confirm) {
      showFieldError(confirmInput, errorDiv, 'Please confirm your password');
      return false;
    } else if (password !== confirm) {
      showFieldError(confirmInput, errorDiv, 'Passwords do not match');
      return false;
    } else {
      clearFieldError(confirmInput, errorDiv);
      return true;
    }
  }
  
  function validateFarmerID() {
    const farmerID = farmerIDInput.value.trim();
    const errorDiv = document.getElementById('farmerID-error');
    
    // Farmer ID is optional, so empty is valid
    if (!farmerID) {
      clearFieldError(farmerIDInput, errorDiv);
      return true;
    }
    
    // If provided, validate format (example: alphanumeric, 6-12 characters)
    if (!/^[A-Z0-9]{6,12}$/i.test(farmerID)) {
      showFieldError(farmerIDInput, errorDiv, 'Farmer ID must be 6-12 alphanumeric characters');
      return false;
    } else {
      clearFieldError(farmerIDInput, errorDiv);
      return true;
    }
  }
  
  function validateTerms() {
    const errorDiv = document.getElementById('terms-error');
    
    if (!termsInput.checked) {
      termsInput.setAttribute('aria-invalid', 'true');
      errorDiv.textContent = 'You must agree to the terms of service to create an account';
      errorDiv.style.display = 'block';
      return false;
    } else {
      termsInput.setAttribute('aria-invalid', 'false');
      errorDiv.textContent = '';
      errorDiv.style.display = 'none';
      return true;
    }
  }
  
  function showFieldError(input, errorDiv, message) {
    input.setAttribute('aria-invalid', 'true');
    input.classList.add('form-input--error');
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
  }
  
  function clearFieldError(input, errorDiv) {
    input.setAttribute('aria-invalid', 'false');
    input.classList.remove('form-input--error');
    errorDiv.textContent = '';
    errorDiv.style.display = 'none';
  }
  
  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }
});
</script>
