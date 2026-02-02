<?php
// Ensure functions are loaded
if (!function_exists('isLoggedIn')) {
    require_once 'functions.php';
}

$isLoggedIn = isLoggedIn();
$user = $isLoggedIn ? getCurrentUser() : null;
$currentPage = $_GET['page'] ?? 'home';
?>

<header class="header">
  <div class="container">
    <nav class="nav">
      <a href="index.php" class="nav-brand">
        <span class="nav-brand-icon">🌾</span>
        <span class="logo-text">Grenada Farmers Marketplace</span>
      </a>
      
      <button class="nav-toggle" onclick="toggleMobileMenu()" aria-label="Menu">
        <span>☰</span>
      </button>
      
      <div class="nav-links" id="navLinks">
        
        <a href="index.php?page=browse" class="nav-link <?= $currentPage === 'browse' ? 'active' : '' ?>">
          Browse Products
        </a>
        
        <?php if ($isLoggedIn): ?>
          <?php if (($user['user_type'] ?? '') === 'farmer'): ?>
            <a href="index.php?page=sell" class="nav-link <?= $currentPage === 'sell' ? 'active' : '' ?>">My Products</a>
          <?php else: ?>
            <a href="index.php?page=cart" class="nav-link <?= $currentPage === 'cart' ? 'active' : '' ?>">Cart</a>
          <?php endif; ?>
          
          <a href="index.php?page=orders" class="nav-link <?= $currentPage === 'orders' ? 'active' : '' ?>">Orders</a>
          <a href="index.php?page=messages" class="nav-link <?= $currentPage === 'messages' ? 'active' : '' ?>">Messages</a>

          <div class="nav-user">
            <button class="nav-user-toggle" onclick="toggleUserMenu()">
              <span class="nav-user-name">Hi, <?= htmlspecialchars($user['username'] ?? 'User') ?></span>
            </button>
            <div class="nav-user-menu" id="userMenu">
              <a href="index.php?page=profile">Profile Settings</a>
              <a href="api/auth.php?action=logout" class="logout-link">Logout</a>
            </div>
          </div>

        <?php else: ?>
          <div class="nav-auth">
            <a href="index.php?page=login" class="btn-login">Login</a>
            <a href="index.php?page=register" class="btn-signup">Sign Up</a>
          </div>
        <?php endif; ?>

      </div>
    </nav>
  </div>
</header>