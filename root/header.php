<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Grenada Farmers Marketplace</title>
<link rel="stylesheet" href="assets/css/variables.css">
<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/style.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&display=swap" rel="stylesheet">
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌾</text></svg>">
<meta name="theme-color" content="#3d5a3a">
</head>
<body class="page">
<header class="header">
  <div class="container">
    <nav class="nav">
      <a href="index.php" class="nav-brand">
        <span class="nav-brand-icon">🌾</span>
        <span class="logo-text">Grenada Farmers</span>
      </a>
      
      <!-- Mobile menu button -->
      <button class="nav-toggle" onclick="toggleMobileMenu()" aria-label="Toggle navigation menu">
        <span></span>
        <span></span>
        <span></span>
      </button>
      
      <ul class="nav-links" id="navLinks">
        <?php if($isLoggedIn): ?>
          <li class="nav-section">
            <a href="index.php?page=browse" class="nav-link" title="Browse Products">
              <span class="nav-icon">🛍️</span>
              <span class="nav-text">Browse</span>
            </a>
          </li>
          <li class="nav-section">
            <a href="index.php?page=cart" class="nav-link cart-link" title="Shopping Cart">
              <span class="nav-icon">🛒</span>
              <span class="nav-text">Cart</span>
              <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                <span class="cart-badge"><?= count($_SESSION['cart']) ?></span>
              <?php endif; ?>
            </a>
          </li>
          <li class="nav-section">
            <a href="index.php?page=messages" class="nav-link" title="Messages">
              <span class="nav-icon">💬</span>
              <span class="nav-text">Messages</span>
            </a>
          </li>
          <li class="nav-section">
            <a href="index.php?page=sell" class="nav-link" title="My Listings">
              <span class="nav-icon">📊</span>
              <span class="nav-text">My Listings</span>
            </a>
          </li>
          <li class="nav-section">
            <a href="index.php?page=orders" class="nav-link" title="Orders">
              <span class="nav-icon">📦</span>
              <span class="nav-text">Orders</span>
            </a>
          </li>
          
          <li class="nav-section user-section">
            <div class="user-info">
              <span class="user-name">👋 <?= htmlspecialchars($name) ?></span>
              <?php if($farmerVerified): ?>
                <span class="verified-badge">✅ Verified Farmer</span>
              <?php endif; ?>
            </div>
            <a href="index.php?page=profile" class="nav-link" title="Profile">
              <span class="nav-icon">👤</span>
              <span class="nav-text">Profile</span>
            </a>
            <a href="index.php?page=logout" class="btn btn-danger btn-sm">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-section">
            <a href="index.php?page=browse" class="nav-link">
              <span class="nav-icon">🛍️</span>
              <span class="nav-text">Browse Products</span>
            </a>
          </li>
          <li class="nav-section auth-section">
            <a href="index.php?page=login" class="btn btn-primary">Login</a>
            <a href="index.php?page=register" class="btn btn-secondary">Sign Up</a>
          </li>
        <?php endif; ?>
      </ul>
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

<main class="page-main">
<div id="alert-container"></div>