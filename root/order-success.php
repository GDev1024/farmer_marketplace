<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireLogin();
$user = getCurrentUser();

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

$db = Config::getDB();
$stmt = $db->prepare("
    SELECT o.*, COUNT(oi.id) as item_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.id = ? AND o.customer_id = ?
    GROUP BY o.id
");
$stmt->execute([$orderId, $user['id']]);
$order = $stmt->fetch();

if (!$order) {
    redirect('dashboard.php');
}

// Get order items
$stmt = $db->prepare("
    SELECT oi.*, l.name, l.unit, l.image_url
    FROM order_items oi
    JOIN listings l ON oi.listing_id = l.id
    WHERE oi.order_id = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - <?= Config::getSiteName() ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/marketplace.css">
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
                <a href="orders.php">My Orders</a>
                <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</button>
                <a href="api/auth.php?action=logout" class="btn btn-secondary btn-sm">Logout</a>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <div class="container container-sm">
            <div class="success-card">
                <div class="success-icon">✓</div>
                <h1>Order Confirmed!</h1>
                <p class="success-message">Thank you for supporting local Grenadian farmers</p>
                
                <div class="order-summary-box">
                    <div class="summary-row">
                        <span>Order Number:</span>
                        <strong>#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></strong>
                    </div>
                    <div class="summary-row">
                        <span>Items:</span>
                        <strong><?= $order['item_count'] ?></strong>
                    </div>
                    <div class="summary-row">
                        <span>Total:</span>
                        <strong class="total-amount">$<?= number_format($order['total_amount'], 2) ?></strong>
                    </div>
                    <div class="summary-row">
                        <span>Payment:</span>
                        <strong><?= ucfirst($order['payment_method']) ?></strong>
                    </div>
                </div>
                
                <div class="order-items-section">
                    <h3>Order Items</h3>
                    <div class="order-items-list">
                        <?php foreach ($items as $item): ?>
                            <div class="order-item">
                                <div class="item-image">
                                    <img src="<?= $item['image_url'] ?: 'https://via.placeholder.com/60x60/f5f3f0/666?text=No+Image' ?>" 
                                         alt="<?= htmlspecialchars($item['name']) ?>">
                                </div>
                                <div class="item-info">
                                    <strong class="item-name"><?= htmlspecialchars($item['name']) ?></strong>
                                    <div class="item-details">
                                        <?= $item['quantity'] ?> <?= $item['unit'] ?> × $<?= number_format($item['price_at_purchase'], 2) ?>
                                    </div>
                                </div>
                                <div class="item-total">
                                    $<?= number_format($item['quantity'] * $item['price_at_purchase'], 2) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="delivery-info">
                    <h4>📍 Delivery Address</h4>
                    <p><?= htmlspecialchars($order['delivery_address']) ?></p>
                </div>
                
                <div class="success-actions">
                    <a href="orders.php" class="btn btn-primary">View All Orders</a>
                    <a href="dashboard.php" class="btn btn-secondary">Continue Shopping</a>
                </div>
            </div>
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
