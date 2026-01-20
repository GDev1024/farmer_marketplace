<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Grenada Farmers Marketplace</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌾</text></svg>">
<meta name="theme-color" content="#6f7a52">
</head>
<body>
<header class="header">
  <div class="header-container">
    <a href="index.php" class="logo">
      <span class="logo-icon">🌾</span>
      <span class="logo-text">Farmer M</span>
    </a>
    
    <!-- Desktop Navigation -->
    <nav class="nav hidden md:flex">
      <a href="index.php?page=browse" class="nav-link">Shop</a>
      <a href="index.php?page=sell" class="nav-link">Farmers</a>
      <a href="#about" class="nav-link">About</a>
    </nav>
    
    <!-- Mobile menu button -->
    <button class="mobile-menu-btn md:hidden" aria-label="Toggle menu" aria-expanded="false">
      <span></span>
      <span></span>
      <span></span>
    </button>
    
    <!-- Auth Section -->
    <div class="flex items-center gap-4">
      <?php if($isLoggedIn): ?>
        <a href="index.php?page=cart" class="relative nav-link" title="Shopping Cart">
          🛒
          <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
            <span class="absolute -top-2 -right-2 bg-error text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"><?= count($_SESSION['cart']) ?></span>
          <?php endif; ?>
        </a>
        <div class="relative">
          <button class="btn btn-ghost" onclick="toggleUserMenu()">
            👤 <?= htmlspecialchars(explode(' ', $name)[0]) ?>
          </button>
          <div class="absolute right-0 top-full mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg hidden" id="userMenu">
            <a href="index.php?page=profile" class="block px-4 py-2 text-sm hover:bg-gray-50">Profile</a>
            <a href="index.php?page=orders" class="block px-4 py-2 text-sm hover:bg-gray-50">Orders</a>
            <a href="index.php?page=messages" class="block px-4 py-2 text-sm hover:bg-gray-50">Messages</a>
            <a href="index.php?page=sell" class="block px-4 py-2 text-sm hover:bg-gray-50">My Listings</a>
            <hr class="my-1">
            <a href="index.php?page=logout" class="block px-4 py-2 text-sm text-error hover:bg-gray-50">Logout</a>
          </div>
        </div>
      <?php else: ?>
        <a href="index.php?page=login" class="btn btn-secondary">Login</a>
        <a href="index.php?page=register" class="btn btn-primary">Get Started</a>
      <?php endif; ?>
    </div>
    
    <!-- Mobile Navigation -->
    <nav class="nav-menu md:hidden" id="mobileNav">
      <a href="index.php?page=browse" class="nav-link">🛍️ Shop</a>
      <a href="index.php?page=sell" class="nav-link">🌾 Farmers</a>
      <a href="#about" class="nav-link">ℹ️ About</a>
      
      <?php if($isLoggedIn): ?>
        <div class="border-t border-gray-200 pt-4 mt-4">
          <a href="index.php?page=cart" class="nav-link">
            🛒 Cart
            <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
              <span class="badge badge-error ml-2"><?= count($_SESSION['cart']) ?></span>
            <?php endif; ?>
          </a>
          <a href="index.php?page=profile" class="nav-link">👤 Profile</a>
          <a href="index.php?page=orders" class="nav-link">📦 Orders</a>
          <a href="index.php?page=messages" class="nav-link">💬 Messages</a>
          <a href="index.php?page=sell" class="nav-link">📊 My Listings</a>
          <a href="index.php?page=logout" class="nav-link text-error">Logout</a>
        </div>
      <?php else: ?>
        <div class="border-t border-gray-200 pt-4 mt-4">
          <a href="index.php?page=login" class="nav-link">Login</a>
          <a href="index.php?page=register" class="nav-link text-primary font-semibold">Get Started</a>
        </div>
      <?php endif; ?>
    </nav>
  </div>
</header>

<!-- Loading overlay -->
<div id="loading-overlay" class="loading-overlay" style="display: none;">
  <div class="loading-spinner">
    <div class="spinner"></div>
    <p class="loading-text">Loading...</p>
  </div>
</div>

<main class="main-content">
<div class="container">
<div id="alert-container"></div>