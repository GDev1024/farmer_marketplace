<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Grenada Farmers Marketplace</title>
<link rel="stylesheet" href="assets/css/variables.css">
<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/marketplace.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&display=swap" rel="stylesheet">
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌾</text></svg>">
<meta name="theme-color" content="#3d5a3a">
</head>
<body class="page">
<!-- Skip Links for Accessibility -->
<div class="skip-links">
  <a href="#main-content" class="skip-link">Skip to main content</a>
  <a href="#navigation" class="skip-link">Skip to navigation</a>
  <?php if($isLoggedIn): ?>
    <a href="#user-menu" class="skip-link">Skip to user menu</a>
  <?php endif; ?>
</div>

<header class="header" role="banner">
  <div class="container">
    <nav class="nav" role="navigation" aria-label="Main navigation" id="navigation">
      <a href="index.php" class="nav-brand" aria-label="Grenada Farmers Marketplace - Home">
        <span class="nav-brand-icon" aria-hidden="true">🌾</span>
        <span class="logo-text">Grenada Farmers</span>
      </a>
      
      <!-- Mobile menu button -->
      <button class="nav-toggle" onclick="toggleMobileMenu()" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="navLinks">
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
      </button>
      
      <ul class="nav-links" id="navLinks" role="menubar">
        <?php if($isLoggedIn): ?>
          <li class="nav-section" role="none">
            <a href="index.php?page=browse" class="nav-link" title="Browse Products" role="menuitem" aria-label="Browse fresh produce">
              <span class="nav-icon" aria-hidden="true">🛍️</span>
              <span class="nav-text">Browse</span>
            </a>
          </li>
          <li class="nav-section" role="none">
            <a href="index.php?page=cart" class="nav-link cart-link" title="Shopping Cart" role="menuitem" aria-label="View shopping cart">
              <span class="nav-icon" aria-hidden="true">🛒</span>
              <span class="nav-text">Cart</span>
              <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                <span class="cart-badge" aria-label="<?= count($_SESSION['cart']) ?> items in cart"><?= count($_SESSION['cart']) ?></span>
              <?php endif; ?>
            </a>
          </li>
          <li class="nav-section" role="none">
            <a href="index.php?page=messages" class="nav-link" title="Messages" role="menuitem" aria-label="View messages">
              <span class="nav-icon" aria-hidden="true">💬</span>
              <span class="nav-text">Messages</span>
            </a>
          </li>
          <li class="nav-section" role="none">
            <a href="index.php?page=sell" class="nav-link" title="My Listings" role="menuitem" aria-label="Manage product listings">
              <span class="nav-icon" aria-hidden="true">📊</span>
              <span class="nav-text">My Listings</span>
            </a>
          </li>
          <li class="nav-section" role="none">
            <a href="index.php?page=orders" class="nav-link" title="Orders" role="menuitem" aria-label="View order history">
              <span class="nav-icon" aria-hidden="true">📦</span>
              <span class="nav-text">Orders</span>
            </a>
          </li>
          
          <li class="nav-section user-section" role="none" id="user-menu">
            <div class="user-info" role="group" aria-label="User information">
              <span class="user-name" aria-label="Welcome <?= htmlspecialchars($name) ?>">👋 <?= htmlspecialchars($name) ?></span>
              <?php if($farmerVerified): ?>
                <span class="verified-badge" aria-label="Verified farmer account">✅ Verified Farmer</span>
              <?php endif; ?>
            </div>
            <a href="index.php?page=profile" class="nav-link" title="Profile" role="menuitem" aria-label="View and edit profile">
              <span class="nav-icon" aria-hidden="true">👤</span>
              <span class="nav-text">Profile</span>
            </a>
            <a href="index.php?page=logout" class="btn btn-danger btn-sm" role="menuitem" aria-label="Sign out of account">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-section" role="none">
            <a href="index.php?page=browse" class="nav-link" role="menuitem" aria-label="Browse fresh produce">
              <span class="nav-icon" aria-hidden="true">🛍️</span>
              <span class="nav-text">Browse Products</span>
            </a>
          </li>
          <li class="nav-section auth-section" role="none">
            <a href="index.php?page=login" class="btn btn-primary" role="menuitem" aria-label="Sign in to your account">Login</a>
            <a href="index.php?page=register" class="btn btn-secondary" role="menuitem" aria-label="Create new account">Sign Up</a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>

<!-- Loading overlay -->
<div id="loading-overlay" class="loading-overlay" style="display: none;" role="status" aria-label="Loading">
  <div class="loading-content">
    <div class="loading-spinner spinner-lg">
      <div class="spinner"></div>
    </div>
    <p class="loading-message">Loading...</p>
  </div>
</div>

<main class="page-main" id="main-content" role="main">
<div id="alert-container" aria-live="polite" aria-atomic="true" class="alert-container"></div>

<!-- ARIA Live Regions for Dynamic Content -->
<div id="live-region-polite" aria-live="polite" aria-atomic="true" class="sr-only"></div>
<div id="live-region-assertive" aria-live="assertive" aria-atomic="true" class="sr-only"></div>
<div id="status-region" role="status" aria-live="polite" class="sr-only"></div>
<div id="loading-announcements" aria-live="polite" aria-atomic="true" class="sr-only"></div>