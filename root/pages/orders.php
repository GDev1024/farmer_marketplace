<?php
// Get user's orders
$stmt = $pdo->prepare("SELECT o.*, COUNT(oi.id) as item_count FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = ? GROUP BY o.id ORDER BY o.created_at DESC");
$stmt->execute([$_SESSION['userId']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Display message if set
$msg = getAndClearMessage();
if($msg['message']): ?>
    <div class="alert alert-<?= $msg['type'] ?>">
        <?= ($msg['type'] === 'success' ? '✓' : '⚠') ?> <?= htmlspecialchars($msg['message']) ?>
    </div>
<?php endif; ?>

<main class="page-main orders-page" id="main-content" role="main">
    <header class="page-header">
        <h1 class="page-title">My Orders</h1>
        <p class="page-subtitle">Track and manage your order history</p>
    </header>

    <?php if(empty($orders)): ?>
        <section class="empty-orders-section" aria-labelledby="empty-orders-title">
            <div class="card">
                <div class="empty-state">
                    <div class="empty-icon" aria-hidden="true">📭</div>
                    <h2 id="empty-orders-title" class="empty-title">No orders yet</h2>
                    <p class="empty-description">Start shopping to see your orders here</p>
                    <a href="index.php?page=browse" class="btn btn-primary" aria-label="Browse products to start shopping">
                        <span class="btn-icon" aria-hidden="true">🌾</span>
                        Browse Products
                    </a>
                </div>
            </div>
        </section>
    <?php else: ?>
        <section class="orders-section" aria-labelledby="orders-list-title">
            <h2 id="orders-list-title" class="sr-only">Order History</h2>
            
            <div class="orders-list">
                <?php foreach($orders as $order): ?>
                    <article class="order-card" aria-labelledby="order-<?= $order['id'] ?>-title">
                        <header class="order-header">
                            <div class="order-info">
                                <h3 id="order-<?= $order['id'] ?>-title" class="order-title">
                                    Order #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?>
                                </h3>
                                <time class="order-date" datetime="<?= date('c', strtotime($order['created_at'])) ?>">
                                    <?= date('M d, Y @ H:i', strtotime($order['created_at'])) ?>
                                </time>
                            </div>
                            <div class="order-status-wrapper">
                                <?php
                                $statusConfig = [
                                    'pending' => ['color' => 'warning', 'icon' => '⏳', 'label' => 'Pending'],
                                    'confirmed' => ['color' => 'info', 'icon' => '✓', 'label' => 'Confirmed'],
                                    'shipped' => ['color' => 'primary', 'icon' => '🚚', 'label' => 'Shipped'],
                                    'delivered' => ['color' => 'success', 'icon' => '✅', 'label' => 'Delivered'],
                                    'cancelled' => ['color' => 'error', 'icon' => '❌', 'label' => 'Cancelled']
                                ];
                                $status = $statusConfig[$order['status']] ?? $statusConfig['pending'];
                                ?>
                                <span class="order-status status-<?= $status['color'] ?>" 
                                      aria-label="Order status: <?= $status['label'] ?>">
                                    <span class="status-icon" aria-hidden="true"><?= $status['icon'] ?></span>
                                    <?= $status['label'] ?>
                                </span>
                            </div>
                        </header>

                        <?php
                        // Get order items
                        $stmt = $pdo->prepare("
                            SELECT oi.quantity, l.product_name, l.price, l.unit, u.name as seller_name 
                            FROM order_items oi 
                            JOIN listings l ON oi.listing_id = l.id 
                            JOIN users u ON l.user_id = u.id 
                            WHERE oi.order_id = ?
                        ");
                        $stmt->execute([$order['id']]);
                        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>

                        <div class="order-items-section" aria-labelledby="order-<?= $order['id'] ?>-items-title">
                            <h4 id="order-<?= $order['id'] ?>-items-title" class="order-items-title">
                                Items in this order (<?= count($items) ?>):
                            </h4>
                            <div class="order-items-list">
                                <?php foreach($items as $item): ?>
                                    <div class="order-item">
                                        <div class="order-item-details">
                                            <h5 class="order-item-name">
                                                <?= htmlspecialchars($item['product_name']) ?>
                                            </h5>
                                            <p class="order-item-meta">
                                                <span class="item-seller">
                                                    <?= htmlspecialchars($item['seller_name']) ?>
                                                </span>
                                                <span class="item-separator" aria-hidden="true">•</span>
                                                <span class="item-quantity" aria-label="Quantity: <?= $item['quantity'] ?> <?= htmlspecialchars($item['unit']) ?>">
                                                    <?= $item['quantity'] ?> <?= htmlspecialchars($item['unit']) ?>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="order-item-price" aria-label="Item total: EC$<?= number_format($item['price'] * $item['quantity'], 2) ?>">
                                            EC$<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="order-total-section">
                            <div class="order-total">
                                <span class="total-label">Total Amount:</span>
                                <span class="total-amount" aria-label="Order total: EC$<?= number_format($order['total_price'], 2) ?>">
                                    EC$<?= number_format($order['total_price'], 2) ?>
                                </span>
                            </div>
                        </div>

                        <footer class="order-actions">
                            <a href="index.php?page=messages" 
                               class="btn btn-secondary order-action-btn"
                               aria-label="Contact seller about order #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?>">
                                <span class="btn-icon" aria-hidden="true">💬</span>
                                Contact Seller
                            </a>
                            <?php if($order['status'] === 'delivered'): ?>
                                <button type="button" 
                                        class="btn btn-warning order-action-btn"
                                        onclick="alert('Review feature coming soon!')"
                                        aria-label="Leave review for order #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?>">
                                    <span class="btn-icon" aria-hidden="true">⭐</span>
                                    Leave Review
                                </button>
                            <?php endif; ?>
                            <?php if(in_array($order['status'], ['pending', 'confirmed'])): ?>
                                <button type="button" 
                                        class="btn btn-danger order-action-btn"
                                        onclick="if(confirm('Are you sure you want to cancel this order?')) { alert('Cancel feature coming soon!'); }"
                                        aria-label="Cancel order #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?>">
                                    <span class="btn-icon" aria-hidden="true">❌</span>
                                    Cancel Order
                                </button>
                            <?php endif; ?>
                        </footer>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</main>