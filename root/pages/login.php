<section class="auth-section">
  <div class="container">
    <div class="auth-wrapper">
      <div class="auth-card">
        <header class="auth-header">
          <a href="../index.php" class="auth-back-link">← Back to Home</a>
          <h1 class="auth-title">Welcome Back</h1>
          <p class="auth-subtitle">Sign in to your account</p>
        </header>
        
        <form method="POST" action="../actions.php" class="auth-form">
          <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email" required>
          </div>
          
          <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
          </div>
          
          <div class="form-group">
            <label class="checkbox-label">
              <input type="checkbox" name="remember" class="checkbox-input">
              Remember me
            </label>
          </div>
          
          <button type="submit" name="login" class="btn btn-primary btn-full">Sign In</button>
        </form>
        
        <div class="auth-footer">
          <p>Don't have an account? <a href="../index.php?page=register" class="auth-link">Sign up</a></p>
          <p><a href="../index.php?page=forgot-password" class="auth-link">Forgot your password?</a></p>
        </div>
      </div>
    </div>
  </div>
</section>

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
