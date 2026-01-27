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

<?php if(empty($orders)): ?>
    <div class="card">
        <div class="empty-state">
            <p>📭 No orders yet</p>
            <p style="font-size:0.95rem; color:#666;">Start shopping to see your orders here</p>
            <a href="index.php?page=browse" class="btn btn-primary" style="margin-top: 1rem;">Browse Products</a>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <h2>📦 My Orders</h2>
        <p style="color: #666; margin-bottom: 1.5rem;">Track and manage your orders</p>
    </div>

    <?php foreach($orders as $order): ?>
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="margin: 0;">Order #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></h3>
                    <p style="color: #666; margin: 0.5rem 0;">
                        <?= date('M d, Y @ H:i', strtotime($order['created_at'])) ?>
                    </p>
                </div>
                <div style="text-align: right;">
                    <?php
                    $statusColors = [
                        'pending' => '#f39c12',
                        'confirmed' => '#3498db',
                        'shipped' => '#9b59b6',
                        'delivered' => '#27ae60',
                        'cancelled' => '#e74c3c'
                    ];
                    $statusEmojis = [
                        'pending' => '⏳',
                        'confirmed' => '✓',
                        'shipped' => '🚚',
                        'delivered' => '✅',
                        'cancelled' => '❌'
                    ];
                    ?>
                    <span style="background: <?= $statusColors[$order['status']] ?>; color: white; padding: 0.5rem 1rem; border-radius: 20px; font-weight: bold; display: inline-block;">
                        <?= $statusEmojis[$order['status']] ?> <?= ucfirst($order['status']) ?>
                    </span>
                </div>
            </div>

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

            <div style="background: #f9f9f9; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
                <h4 style="margin-bottom: 1rem;">Items in this order:</h4>
                <?php foreach($items as $item): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #ddd;">
                        <div>
                            <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                            <br>
                            <span style="font-size: 0.9rem; color: #666;">
                                <?= htmlspecialchars($item['seller_name']) ?> • <?= $item['quantity'] ?> <?= htmlspecialchars($item['unit']) ?>
                            </span>
                        </div>
                        <span style="font-weight: bold;">EC$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 2px solid #D2DCB6; margin-bottom: 1rem;">
                <span style="font-size: 1.1rem; font-weight: bold;">Total Amount:</span>
                <span style="font-size: 1.3rem; font-weight: bold; color: #A1BC98;">EC$<?= number_format($order['total_price'], 2) ?></span>
            </div>

            <div style="display: flex; gap: 1rem;">
                <a href="index.php?page=messages" class="btn btn-secondary" style="flex: 1;">💬 Contact Seller</a>
                <?php if($order['status'] === 'delivered'): ?>
                    <a href="javascript:void(0)" class="btn btn-warning" style="flex: 1;" onclick="alert('Review feature coming soon!')">⭐ Leave Review</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <style>
        @media (max-width: 768px) {
            .card > div:first-child {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .card > div:first-child > div:last-child {
                text-align: left;
                width: 100%;
            }
            
            .btn {
                font-size: 0.9rem;
                padding: 0.6rem 1rem;
            }
        }
    </style>
<?php endif; ?>