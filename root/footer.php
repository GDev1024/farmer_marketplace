</main>
<footer>
  <div class="footer-content">
    <div class="footer-section">
      <h4>🌾 Grenada Farmers</h4>
      <p>Connecting local farmers with fresh produce lovers across Grenada.</p>
      <div class="footer-social">
        <span>Follow us:</span>
        <a href="#" aria-label="Facebook">📘</a>
        <a href="#" aria-label="Instagram">📷</a>
        <a href="#" aria-label="Twitter">🐦</a>
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
      <div class="footer-stats">
        <div class="stat">
          <strong>500+</strong>
          <span>Products Listed</span>
        </div>
        <div class="stat">
          <strong>100+</strong>
          <span>Local Farmers</span>
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
  const nav = document.getElementById('nav');
  const body = document.body;
  
  nav.classList.toggle('nav-open');
  body.classList.toggle('nav-open');
}

// Close mobile menu when clicking outside
document.addEventListener('click', function(e) {
  const nav = document.getElementById('nav');
  const menuBtn = document.querySelector('.mobile-menu-btn');
  
  if (!nav.contains(e.target) && !menuBtn.contains(e.target)) {
    nav.classList.remove('nav-open');
    document.body.classList.remove('nav-open');
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
