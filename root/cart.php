<?php
// ==================== cart.php ====================
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireLogin();
$user = getCurrentUser();

if ($user['user_type'] !== 'consumer') {
    redirect('index.php');
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
    <title>Shopping Cart - <?= Config::SITE_NAME ?></title>
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
        <h1 style="color: var(--primary-green); margin-bottom: 2rem;">Shopping Cart</h1>
        
        <?php if (empty($cartItems)): ?>
            <div class="card" style="text-align: center; padding: 3rem;">
                <h2 style="color: var(--gray-600); margin-bottom: 1rem;">Your cart is empty</h2>
                <p style="color: var(--gray-600); margin-bottom: 2rem;">Browse our marketplace to find fresh local produce</p>
                <a href="index.php" class="btn btn-primary">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="grid grid-2" style="align-items: start;">
                <div>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="card" style="margin-bottom: 1rem;">
                            <div style="display: flex; gap: 1.5rem;">
                                <img src="<?= $item['image_url'] ?: 'https://via.placeholder.com/100?text=No+Image' ?>" 
                                     alt="<?= htmlspecialchars($item['name']) ?>"
                                     style="width: 100px; height: 100px; object-fit: cover; border-radius: var(--radius-sm);">
                                
                                <div style="flex: 1;">
                                    <h3 style="color: var(--primary-green); margin-bottom: 0.5rem;">
                                        <?= htmlspecialchars($item['name']) ?>
                                    </h3>
                                    <p style="color: var(--gray-600); font-size: var(--font-size-sm); margin-bottom: 0.5rem;">
                                        From <?= htmlspecialchars($item['farmer_name']) ?>
                                    </p>
                                    <div style="display: flex; align-items: center; gap: 1rem; margin-top: 1rem;">
                                        <form method="POST" action="api/cart.php" style="display: flex; align-items: center; gap: 0.5rem;">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="listing_id" value="<?= $item['listing_id'] ?>">
                                            <label style="font-size: var(--font-size-sm);">Qty:</label>
                                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" 
                                                   min="1" max="<?= $item['stock'] ?>" 
                                                   class="form-input" style="width: 80px;"
                                                   onchange="this.form.submit()">
                                        </form>
                                        <strong style="color: var(--primary-green);">
                                            $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                        </strong>
                                    </div>
                                </div>
                                
                                <form method="POST" action="api/cart.php">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="listing_id" value="<?= $item['listing_id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="card" style="position: sticky; top: 100px;">
                    <h2 style="color: var(--primary-green); margin-bottom: 1.5rem;">Order Summary</h2>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span>Subtotal:</span>
                        <strong>$<?= number_format($total, 2) ?></strong>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--gray-200);">
                        <span>Shipping:</span>
                        <strong>Free</strong>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2rem; font-size: var(--font-size-xl);">
                        <strong>Total:</strong>
                        <strong style="color: var(--primary-green);">$<?= number_format($total, 2) ?></strong>
                    </div>
                    
                    <a href="checkout.php" class="btn btn-primary btn-block">Proceed to Checkout</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php