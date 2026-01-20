</div>
</main>
<footer class="footer">
  <div class="footer-content">
    <div class="footer-section">
      <h4>🌾 Grenada Farmers</h4>
      <p>Connecting local farmers with fresh produce lovers across Grenada.</p>
      <div class="flex items-center gap-4 mt-4">
        <span class="text-sm text-muted">Follow us:</span>
        <a href="#" aria-label="Facebook" class="text-lg hover:text-primary transition-colors">📘</a>
        <a href="#" aria-label="Instagram" class="text-lg hover:text-primary transition-colors">📷</a>
        <a href="#" aria-label="Twitter" class="text-lg hover:text-primary transition-colors">🐦</a>
      </div>
    </div>
    
    <div class="footer-section">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="index.php?page=browse">Browse Products</a></li>
        <li><a href="index.php?page=register">Become a Seller</a></li>
        <li><a href="#about">About Us</a></li>
        <li><a href="#contact">Contact</a></li>
      </ul>
    </div>
    
    <div class="footer-section">
      <h4>For Farmers</h4>
      <ul>
        <li><a href="index.php?page=listing">List Your Products</a></li>
        <li><a href="index.php?page=sell">Manage Listings</a></li>
        <li><a href="#verification">Get Verified</a></li>
        <li><a href="#support">Farmer Support</a></li>
      </ul>
    </div>
    
    <div class="footer-section">
      <h4>Support Local</h4>
      <p>🇬🇩 Proudly supporting Grenadian agriculture and sustainable farming practices.</p>
      <div class="flex gap-8 mt-4">
        <div class="text-center">
          <div class="text-xl font-bold text-primary">500+</div>
          <div class="text-xs text-muted">Products Listed</div>
        </div>
        <div class="text-center">
          <div class="text-xl font-bold text-primary">100+</div>
          <div class="text-xs text-muted">Local Farmers</div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="footer-bottom">
    <p>&copy; 2026 Grenada Farmers Marketplace | PMT 226 Capstone Project</p>
    <div class="footer-links">
      <a href="#privacy">Privacy Policy</a>
      <a href="#terms">Terms of Service</a>
      <a href="#help">Help Center</a>
    </div>
  </div>
</footer>

<script src="assets/main.js"></script>

<!-- Mobile menu toggle script -->
<script>
function toggleMobileMenu() {
  const nav = document.getElementById('mobileNav');
  const body = document.body;
  const menuBtn = document.querySelector('.mobile-menu-btn');
  
  nav.classList.toggle('nav-open');
  body.classList.toggle('nav-open');
  
  // Update aria-expanded attribute
  const isOpen = nav.classList.contains('nav-open');
  menuBtn.setAttribute('aria-expanded', isOpen);
  menuBtn.classList.toggle('active', isOpen);
}

// Add click event to mobile menu button
document.addEventListener('DOMContentLoaded', function() {
  const menuBtn = document.querySelector('.mobile-menu-btn');
  if (menuBtn) {
    menuBtn.addEventListener('click', toggleMobileMenu);
  }
});

// Close mobile menu when clicking outside
document.addEventListener('click', function(e) {
  const nav = document.getElementById('mobileNav');
  const menuBtn = document.querySelector('.mobile-menu-btn');
  
  if (nav && menuBtn && !nav.contains(e.target) && !menuBtn.contains(e.target)) {
    nav.classList.remove('nav-open');
    document.body.classList.remove('nav-open');
    menuBtn.setAttribute('aria-expanded', 'false');
    menuBtn.classList.remove('active');
  }
});

// User dropdown toggle
function toggleUserMenu() {
  const userMenu = document.getElementById('userMenu');
  if (userMenu) {
    userMenu.classList.toggle('hidden');
  }
}

// Close user menu when clicking outside
document.addEventListener('click', function(e) {
  const userMenu = document.getElementById('userMenu');
  const userBtn = document.querySelector('.user-btn');
  
  if (userMenu && userBtn && !userMenu.contains(e.target) && !userBtn.contains(e.target)) {
    userMenu.classList.add('hidden');
  }
});

// Loading overlay functions
function showLoading() {
  document.getElementById('loading-overlay').style.display = 'flex';
}

function hideLoading() {
  document.getElementById('loading-overlay').style.display = 'none';
}

// Auto-hide loading after 5 seconds (fallback)
setTimeout(hideLoading, 5000);

// Show loading on form submissions
document.addEventListener('DOMContentLoaded', function() {
  const forms = document.querySelectorAll('form');
  forms.forEach(form => {
    form.addEventListener('submit', function() {
      showLoading();
    });
  });
  
  // Hide loading when page loads
  hideLoading();
});
</script>
</body>
</html>
