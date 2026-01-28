<?php
// ==================== cart.php ====================
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireLogin();
$user = getCurrentUser();

if ($user['user_type'] !== 'customer') {
    redirect('dashboard.php');
}

$db = Config::getDB();

// Get cart items
$stmt = $db->prepare("
    SELECT c.*, l.name, l.price, l.unit, l.image_url, l.quantity as stock, u.username as farmer_name
    FROM cart c
    JOIN listings l ON c.listing_id = l.id
    JOIN users u ON l.farmer_id = u.id
    WHERE c.user_id = ?
");
$stmt->execute([$user['id']]);
$cartItems = $stmt->fetchAll();

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - <?= Config::getSiteName() ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/marketplace.css">
</head>
<body class="app-page">
    <header>
        <nav>
            <a href="dashboard.php" class="logo">
                <span class="logo-icon">🌾</span>
                <span><?= Config::getSiteName() ?></span>
            </a>
            <div class="nav-links">
                <a href="dashboard.php">Browse</a>
                <a href="cart.php">Cart</a>
                <a href="orders.php">My Orders</a>
                <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</button>
                <a href="api/auth.php?action=logout" class="btn btn-secondary btn-sm">Logout</a>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>Shopping Cart</h1>
                <p>Review your items before checkout</p>
            </div>
            
            <?php if (empty($cartItems)): ?>
                <div class="empty-state">
                    <div class="empty-icon">🛒</div>
                    <h3>Your cart is empty</h3>
                    <p>Browse our marketplace to find fresh local produce</p>
                    <a href="dashboard.php" class="btn btn-primary">Start Shopping</a>
                </div>
            <?php else: ?>
                <div class="cart-layout">
                    <div class="cart-items">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="cart-item-card">
                                <div class="item-image">
                                    <img src="<?= $item['image_url'] ?: 'https://via.placeholder.com/120x120/f5f3f0/666?text=No+Image' ?>" 
                                         alt="<?= htmlspecialchars($item['name']) ?>">
                                </div>
                                
                                <div class="item-details">
                                    <h3 class="item-name"><?= htmlspecialchars($item['name']) ?></h3>
                                    <p class="item-farmer">From <?= htmlspecialchars($item['farmer_name']) ?></p>
                                    
                                    <div class="item-controls">
                                        <form method="POST" action="api/cart.php" class="quantity-form">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="listing_id" value="<?= $item['listing_id'] ?>">
                                            <label class="quantity-label">Quantity:</label>
                                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" 
                                                   min="1" max="<?= $item['stock'] ?>" 
                                                   class="quantity-input"
                                                   onchange="this.form.submit()">
                                            <span class="unit-label"><?= $item['unit'] ?></span>
                                        </form>
                                        
                                        <div class="item-price">
                                            $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="item-actions">
                                    <form method="POST" action="api/cart.php">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="listing_id" value="<?= $item['listing_id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="cart-summary">
                        <div class="summary-card">
                            <h2>Order Summary</h2>
                            
                            <div class="summary-line">
                                <span>Subtotal:</span>
                                <strong>$<?= number_format($total, 2) ?></strong>
                            </div>
                            
                            <div class="summary-line">
                                <span>Shipping:</span>
                                <strong class="free-shipping">Free</strong>
                            </div>
                            
                            <div class="summary-total">
                                <span>Total:</span>
                                <strong>$<?= number_format($total, 2) ?></strong>
                            </div>
                            
                            <a href="checkout.php" class="btn btn-primary btn-lg btn-block">Proceed to Checkout</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="app-footer">
        <div class="footer-content">
            <div class="footer-brand">
                <span class="logo-icon">🌾</span>
                <span><?= Config::getSiteName() ?></span>
            </div>
            <p class="footer-tagline">Supporting local agriculture in Grenada</p>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            document.querySelector('.nav-links').classList.toggle('active');
        }
    </script>
</body>
</html>
