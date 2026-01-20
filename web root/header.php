<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Grenada Farmers Marketplace</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌾</text></svg>">
</head>
<body>
<header>
  <div class="header-container">
    <a href="index.php" class="logo">
      <span class="logo-icon">🌾</span>
      <span class="logo-text">Grenada Farmers</span>
    </a>
    
    <!-- Mobile menu button -->
    <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
      <span></span>
      <span></span>
      <span></span>
    </button>
    
    <nav id="nav" class="nav-menu">
      <?php if($isLoggedIn): ?>
        <div class="nav-section">
          <a href="index.php?page=browse" class="nav-link">
            <span class="nav-icon">🛍️</span>
            <span class="nav-text">Browse</span>
          </a>
          <a href="index.php?page=cart" class="nav-link cart-link">
            <span class="nav-icon">🛒</span>
            <span class="nav-text">Cart</span>
            <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
              <span class="cart-badge"><?= count($_SESSION['cart']) ?></span>
            <?php endif; ?>
          </a>
          <a href="index.php?page=messages" class="nav-link">
            <span class="nav-icon">💬</span>
            <span class="nav-text">Messages</span>
          </a>
        </div>
        
        <div class="nav-section">
          <a href="index.php?page=sell" class="nav-link">
            <span class="nav-icon">📊</span>
            <span class="nav-text">My Listings</span>
          </a>
          <a href="index.php?page=orders" class="nav-link">
            <span class="nav-icon">📦</span>
            <span class="nav-text">Orders</span>
          </a>
        </div>
        
        <div class="nav-section user-section">
          <div class="user-info">
            <span class="user-name">👋 <?= htmlspecialchars($name) ?></span>
            <?php if($farmerVerified): ?>
              <span class="verified-badge">✅ Verified Farmer</span>
            <?php endif; ?>
          </div>
          <a href="index.php?page=profile" class="nav-link">
            <span class="nav-icon">👤</span>
            <span class="nav-text">Profile</span>
          </a>
          <a href="index.php?page=logout" class="btn btn-danger btn-sm">Logout</a>
        </div>
      <?php else: ?>
        <div class="nav-section">
          <a href="index.php?page=browse" class="nav-link">
            <span class="nav-icon">🛍️</span>
            <span class="nav-text">Browse Products</span>
          </a>
        </div>
        <div class="nav-section auth-section">
          <a href="index.php?page=login" class="btn btn-primary">Login</a>
          <a href="index.php?page=register" class="btn btn-secondary">Sign Up</a>
        </div>
      <?php endif; ?>
    </nav>
  </div>
</header>

<!-- Loading overlay -->
<div id="loading-overlay" class="loading-overlay" style="display: none;">
  <div class="loading-spinner">
    <div class="spinner"></div>
    <p>Loading...</p>
  </div>
</div>

<main class="container">
<div id="alert-container"></div>