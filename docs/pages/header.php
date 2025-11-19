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
        <a href="index.php?page=browse">Browse</a>
        <a href="index.php?page=messages">Messages</a>
        <a href="index.php?page=sell">My Listings</a>
        <a href="index.php?page=profile">Profile</a>
        <a href="index.php?page=logout" class="btn btn-danger">Logout</a>
      <?php else: ?>
        <a href="index.php?page=login" class="btn btn-primary">Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="container">
<div id="alert-container"></div>
