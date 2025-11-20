<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Grenada Farmers Marketplace</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header>
  <div class="header-container">
    <a href="index.php" class="logo">🌾 Farmers Market</a>
    <nav id="nav">
      <?php if($isLoggedIn): ?>
        <a href="index.php?page=browse">🛍️ Browse</a>
        <a href="index.php?page=cart" style="position: relative;">
          🛒 Cart
          <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
            <span style="
              position: absolute;
              top: -8px;
              right: -8px;
              background: #D9534F;
              color: white;
              border-radius: 50%;
              width: 22px;
              height: 22px;
              display: flex;
              align-items: center;
              justify-content: center;
              font-size: 0.8rem;
              font-weight: bold;
            "><?= count($_SESSION['cart']) ?></span>
          <?php endif; ?>
        </a>
        <a href="index.php?page=messages">💬 Messages</a>
        <a href="index.php?page=sell">📊 Listings</a>
        <a href="index.php?page=orders">📦 Orders</a>
        <a href="index.php?page=profile">👤 Profile</a>
        <a href="index.php?page=logout" class="btn btn-danger" style="font-size: 0.85rem;">Logout</a>
      <?php else: ?>
        <a href="index.php?page=browse">🛍️ Browse</a>
        <a href="index.php?page=login" class="btn btn-primary">Login</a>
        <a href="index.php?page=register" class="btn btn-secondary">Register</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="container">
<div id="alert-container"></div>