<?php
// header.php - Grenada Farmer Marketplace
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current page info safely
$currentPage = $_GET['page'] ?? 'home';
$isLoggedIn = !empty($_SESSION['user_id']);
$userName = $_SESSION['name'] ?? '';
$userType = $_SESSION['user_type'] ?? 'customer';

/**
 * Helper function to set active class
 */
function isActive($pageName, $currentPage) {
    return $pageName === $currentPage ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?>Grenada Farmer Marketplace</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/marketplace.css">
</head>
<body class="<?= htmlspecialchars($bodyClass ?? 'app-page') ?>">

<header class="app-header">
    <div class="container">
        <nav class="nav">
            <a href="index.php" class="nav-brand">
                <span class="nav-brand-icon">🌾</span>
                <span class="logo-text">Grenada Farmers Marketplace</span>
            </a>
            
            <div class="nav-links" id="navLinks">
                <a href="index.php?page=browse" class="nav-link <?= isActive('browse', $currentPage) ?>">
                    Browse Products
                </a>
                
                <?php if ($isLoggedIn && $userType === 'farmer'): ?>
                    <a href="index.php?page=sell" class="nav-link <?= isActive('sell', $currentPage) ?>">
                        My Products
                    </a>
                <?php else: ?>
                    <a href="index.php?page=cart" class="nav-link <?= isActive('cart', $currentPage) ?>">
                        Cart
                    </a>
                <?php endif; ?>
                
                <a href="index.php?page=orders" class="nav-link <?= isActive('orders', $currentPage) ?>">
                    Orders
                </a>
                
                <a href="index.php?page=messages" class="nav-link <?= isActive('messages', $currentPage) ?>">
                    Messages
                </a>
            </div>
            
            <div class="nav-user">
                <?php if ($isLoggedIn): ?>
                    <div class="nav-user-info">
                        <span class="nav-user-name">Welcome, <?= htmlspecialchars($userName) ?></span>
                    </div>
                    
                    <div class="nav-user-menu" id="userMenu">
                        <a href="index.php?page=profile" class="nav-user-link">Profile Settings</a>
                        <a href="index.php?page=logout" class="nav-user-link nav-user-link--logout">Logout</a>
                    </div>
                <?php else: ?>
                    <div class="nav-auth">
                        <a href="index.php?page=login" class="btn btn-secondary btn-sm">Login</a>
                        <a href="index.php?page=register" class="btn btn-primary btn-sm">Sign Up</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                <span></span><span></span><span></span>
            </button>
        </nav>
    </div>
</header>