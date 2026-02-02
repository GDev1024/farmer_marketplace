<section class="auth-section">
  <div class="container">
    <div class="auth-wrapper">
      <div class="auth-card">
        <header class="auth-header">
          <a href="../index.php" class="auth-back-link">← Back to Home</a>
          <h1 class="auth-title">Join Our Community</h1>
          <p class="auth-subtitle">Create your account</p>
        </header>
        
        <form method="POST" action="../actions.php" class="auth-form">
          <div class="form-group">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" id="name" name="name" class="form-input" placeholder="Enter your full name" required>
          </div>
          
          <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email" required>
          </div>
          
          <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-input" placeholder="Create a password" required>
          </div>
          
          <div class="form-group">
            <label for="confirm" class="form-label">Confirm Password</label>
            <input type="password" id="confirm" name="confirm" class="form-input" placeholder="Confirm your password" required>
          </div>
          
          <div class="form-group">
            <label for="farmerID" class="form-label">Farmer ID (Optional)</label>
            <input type="text" id="farmerID" name="farmerID" class="form-input" placeholder="Enter your Farmer ID if you have one">
          </div>
          
          <div class="form-group">
            <label class="checkbox-label">
              <input type="checkbox" name="terms" class="checkbox-input" required>
              I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
            </label>
          </div>
          
          <button type="submit" name="register" class="btn btn-primary btn-full">Create Account</button>
        </form>
        
        <div class="auth-footer">
          <p>Already have an account? <a href="../index.php?page=login">Sign in</a></p>
        </div>
      </div>
    </div>
  </div>
</section>

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
