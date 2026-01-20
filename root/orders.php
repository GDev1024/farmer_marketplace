<?php
// ==================== orders.php ====================
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireLogin();
$user = getCurrentUser();

if ($user['user_type'] !== 'consumer') {
    redirect('index.php');
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
    <title>My Orders - <?= Config::SITE_NAME ?></title>
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/marketplace.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">🌾 <?= Config::SITE_NAME ?></div>
            <div class="nav-links">
                <a href="index.php">Browse</a>
                <a href="cart.php">Cart</a>
                <a href="orders.php">Orders</a>
                <a href="api/auth.php?action=logout" class="btn btn-secondary btn-sm">Logout</a>
            </div>
        </nav>
    </header>

    <div class="container" style="margin-top: 3rem;">
        <h1 style="color: var(--primary-green); margin-bottom: 2rem;">My Orders</h1>
        
        <?php if (empty($orders)): ?>
            <div class="card" style="text-align: center; padding: 3rem;">
                <h2 style="color: var(--gray-600); margin-bottom: 1rem;">No orders yet</h2>
                <p style="color: var(--gray-600); margin-bottom: 2rem;">Start shopping for fresh local produce</p>
                <a href="index.php" class="btn btn-primary">Browse Products</a>
            </div>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($orders as $order): ?>
                    <div class="card">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                            <div>
                                <h3 style="color: var(--primary-green);">Order #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></h3>
                                <p style="color: var(--gray-600); font-size: var(--font-size-sm);">
                                    <?= date('F j, Y', strtotime($order['created_at'])) ?>
                                </p>
                            </div>
                            <span class="badge badge-success"><?= ucfirst($order['status']) ?></span>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1rem; padding: 1rem; background: var(--gray-50); border-radius: var(--radius-sm);">
                            <div>
                                <div style="color: var(--gray-600); font-size: var(--font-size-sm);">Items</div>
                                <strong><?= $order['item_count'] ?></strong>
                            </div>
                            <div>
                                <div style="color: var(--gray-600); font-size: var(--font-size-sm);">Total</div>
                                <strong style="color: var(--primary-green);">$<?= number_format($order['total_amount'], 2) ?></strong>
                            </div>
                            <div>
                                <div style="color: var(--gray-600); font-size: var(--font-size-sm);">Payment</div>
                                <strong><?= ucfirst($order['payment_method']) ?></strong>
                            </div>
                        </div>
                        
                        <a href="order-success.php?order_id=<?= $order['id'] ?>" class="btn btn-secondary btn-sm btn-block">
                            View Details
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>