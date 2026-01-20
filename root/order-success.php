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
    redirect('index.php');
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
    <title>Order Confirmed - <?= Config::SITE_NAME ?></title>
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
                <a href="orders.php">My Orders</a>
                <a href="api/auth.php?action=logout" class="btn btn-secondary btn-sm">Logout</a>
            </div>
        </nav>
    </header>

    <div class="container container-sm" style="margin-top: 3rem;">
        <div class="card" style="text-align: center; padding: 3rem;">
            <div style="font-size: 4rem; color: var(--success); margin-bottom: 1rem;">✓</div>
            <h1 style="color: var(--primary-green); margin-bottom: 1rem;">Order Confirmed!</h1>
            <p style="color: var(--gray-600); font-size: var(--font-size-lg); margin-bottom: 2rem;">
                Thank you for supporting local Grenadian farmers
            </p>
            
            <div style="background: var(--gray-50); padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: var(--gray-600);">Order Number:</span>
                    <strong>#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: var(--gray-600);">Items:</span>
                    <strong><?= $order['item_count'] ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: var(--gray-600);">Total:</span>
                    <strong style="color: var(--primary-green);">$<?= number_format($order['total_amount'], 2) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--gray-600);">Payment:</span>
                    <strong><?= ucfirst($order['payment_method']) ?></strong>
                </div>
            </div>
            
            <h3 style="color: var(--primary-green); margin-bottom: 1rem; text-align: left;">Order Items</h3>
            <div style="text-align: left; margin-bottom: 2rem;">
                <?php foreach ($items as $item): ?>
                    <div style="display: flex; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid var(--gray-200);">
                        <img src="<?= $item['image_url'] ?: 'https://via.placeholder.com/60?text=No+Image' ?>" 
                             alt="<?= htmlspecialchars($item['name']) ?>"
                             style="width: 60px; height: 60px; object-fit: cover; border-radius: var(--radius-sm);">
                        <div style="flex: 1;">
                            <strong><?= htmlspecialchars($item['name']) ?></strong>
                            <div style="color: var(--gray-600); font-size: var(--font-size-sm);">
                                <?= $item['quantity'] ?> <?= $item['unit'] ?> × $<?= number_format($item['price_at_purchase'], 2) ?>
                            </div>
                        </div>
                        <strong>$<?= number_format($item['quantity'] * $item['price_at_purchase'], 2) ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div style="background: #e7f5ff; border: 1px solid #1971c2; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 2rem; text-align: left;">
                <strong style="color: #1971c2;">📍 Delivery Address:</strong>
                <p style="margin-top: 0.5rem; color: var(--gray-900);"><?= htmlspecialchars($order['delivery_address']) ?></p>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <a href="orders.php" class="btn btn-primary">View All Orders</a>
                <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
            </div>
        </div>
    </div>
</body>
</html>