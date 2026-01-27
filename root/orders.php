<?php
// ==================== orders.php ====================
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireLogin();
$user = getCurrentUser();

if ($user['user_type'] !== 'customer') {
    redirect('dashboard.php');
}

$db = Config::getDB();

// Get user's orders
$stmt = $db->prepare("
    SELECT o.*, COUNT(oi.id) as item_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.customer_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->execute([$user['id']]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - <?= Config::getSiteName() ?></title>
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
                <h1>My Orders</h1>
                <p>Track your purchases and order history</p>
            </div>
            
            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📦</div>
                    <h3>No orders yet</h3>
                    <p>Start shopping for fresh local produce</p>
                    <a href="dashboard.php" class="btn btn-primary">Browse Products</a>
                </div>
            <?php else: ?>
                <div class="orders-grid">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-info">
                                    <h3>Order #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></h3>
                                    <p class="order-date"><?= date('F j, Y', strtotime($order['created_at'])) ?></p>
                                </div>
                                <span class="order-status <?= strtolower($order['status']) ?>"><?= ucfirst($order['status']) ?></span>
                            </div>
                            
                            <div class="order-metrics">
                                <div class="metric">
                                    <span class="metric-label">Items</span>
                                    <span class="metric-value"><?= $order['item_count'] ?></span>
                                </div>
                                <div class="metric">
                                    <span class="metric-label">Total</span>
                                    <span class="metric-value">$<?= number_format($order['total_amount'], 2) ?></span>
                                </div>
                                <div class="metric">
                                    <span class="metric-label">Payment</span>
                                    <span class="metric-value"><?= ucfirst($order['payment_method']) ?></span>
                                </div>
                            </div>
                            
                            <div class="order-actions">
                                <a href="order-success.php?order_id=<?= $order['id'] ?>" class="btn btn-outline btn-sm">
                                    View Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
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
