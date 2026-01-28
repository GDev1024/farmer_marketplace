<main class="auth-page" role="main" aria-labelledby="login-title">
  <div class="auth-container">
    <div class="auth-card">
      <header class="auth-header">
        <h1 id="login-title" class="auth-title">Welcome Back</h1>
        <p class="auth-subtitle">Sign in to your Grenada Farmers account</p>
      </header>
      
      <form method="POST" action="actions.php" class="auth-form" role="form" aria-labelledby="login-title" novalidate>
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
          <small id="email-help" class="form-help">We'll never share your email with anyone else</small>
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
              placeholder="Enter your password"
              required 
              aria-describedby="password-help password-error"
              autocomplete="current-password"
              minlength="6"
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
          <small id="password-help" class="form-help">Minimum 6 characters required</small>
          <div id="password-error" class="form-error" role="alert" aria-live="polite"></div>
        </div>
        
        <div class="form-group">
          <div class="form-checkbox">
            <input type="checkbox" id="remember" name="remember" class="checkbox-input">
            <label for="remember" class="checkbox-label">
              <span class="checkbox-indicator" aria-hidden="true"></span>
              Remember me for 30 days
            </label>
          </div>
        </div>
        
        <div class="form-actions">
          <button type="submit" name="login" class="btn btn-primary btn-full" aria-describedby="login-help">
            <span class="btn-icon" aria-hidden="true">🔐</span>
            Sign In
          </button>
          <small id="login-help" class="form-help">By signing in, you agree to our terms of service</small>
        </div>
      </form>
      
      <footer class="auth-footer">
        <div class="auth-links">
          <a href="index.php?page=register" class="auth-link" aria-label="Create a new account">
            Don't have an account? <strong>Sign up</strong>
          </a>
          <a href="index.php?page=forgot-password" class="auth-link" aria-label="Reset your password">
            Forgot your password?
          </a>
        </div>
        
        <div class="auth-divider">
          <span class="divider-text">or</span>
        </div>
        
        <div class="auth-guest">
          <a href="index.php?page=browse" class="btn btn-secondary btn-full" aria-label="Browse products without signing in">
            <span class="btn-icon" aria-hidden="true">🛍️</span>
            Continue as Guest
          </a>
        </div>
      </footer>
    </div>
    
    <aside class="auth-sidebar" role="complementary" aria-labelledby="benefits-title">
      <div class="benefits-card">
        <h2 id="benefits-title" class="benefits-title">Why Join Our Community?</h2>
        <ul class="benefits-list" role="list">
          <li class="benefit-item" role="listitem">
            <span class="benefit-icon" aria-hidden="true">🌾</span>
            <div class="benefit-content">
              <h3 class="benefit-title">Fresh Local Produce</h3>
              <p class="benefit-description">Direct from Grenadian farmers to your table</p>
            </div>
          </li>
          <li class="benefit-item" role="listitem">
            <span class="benefit-icon" aria-hidden="true">🤝</span>
            <div class="benefit-content">
              <h3 class="benefit-title">Support Local Farmers</h3>
              <p class="benefit-description">Help strengthen our agricultural community</p>
            </div>
          </li>
          <li class="benefit-item" role="listitem">
            <span class="benefit-icon" aria-hidden="true">📦</span>
            <div class="benefit-content">
              <h3 class="benefit-title">Easy Ordering</h3>
              <p class="benefit-description">Simple checkout and delivery options</p>
            </div>
          </li>
        </ul>
      </div>
    </aside>
  </div>
</main>

<script>
// Enhanced form validation and accessibility
function togglePasswordVisibility(inputId) {
  const input = document.getElementById(inputId);
  const toggle = input.nextElementSibling;
  const icon = toggle.querySelector('.password-toggle-icon');
  
  if (input.type === 'password') {
    input.type = 'text';
    toggle.setAttribute('aria-label', 'Hide password');
    icon.textContent = '🙈';
  } else {
    input.type = 'password';
    toggle.setAttribute('aria-label', 'Show password');
    icon.textContent = '👁️';
  }
}

// Form validation with accessibility
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('.auth-form');
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  
  if (form) {
    // Real-time validation
    emailInput.addEventListener('blur', validateEmail);
    passwordInput.addEventListener('blur', validatePassword);
    
    // Form submission
    form.addEventListener('submit', function(e) {
      const isEmailValid = validateEmail();
      const isPasswordValid = validatePassword();
      
      if (!isEmailValid || !isPasswordValid) {
        e.preventDefault();
        // Focus first invalid field
        const firstError = form.querySelector('.form-input[aria-invalid="true"]');
        if (firstError) {
          firstError.focus();
        }
      }
    });
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
    } else if (password.length < 6) {
      showFieldError(passwordInput, errorDiv, 'Password must be at least 6 characters');
      return false;
    } else {
      clearFieldError(passwordInput, errorDiv);
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
