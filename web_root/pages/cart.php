<?php
// Get cart items
$cartItems = [];
$total = 0;

if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $listingId => $quantity) {
        $stmt = $pdo->prepare("SELECT l.*, u.name as seller_name FROM listings l JOIN users u ON l.user_id = u.id WHERE l.id = ?");
        $stmt->execute([$listingId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($item) {
            $item['cartQuantity'] = $quantity;
            $item['subtotal'] = $item['price'] * $quantity;
            $total += $item['subtotal'];
            $cartItems[] = $item;
        }
    }
}

// Display message if set
$msg = getAndClearMessage();
if($msg['message']): ?>
    <div class="alert alert-<?= $msg['type'] ?>">
        <?= ($msg['type'] === 'success' ? '✓' : '⚠') ?> <?= htmlspecialchars($msg['message']) ?>
    </div>
<?php endif; ?>

<?php if(empty($cartItems)): ?>
    <div class="card">
        <div class="empty-state">
            <p>🛒 Your cart is empty</p>
            <p style="font-size:0.95rem; color:#666;">Start shopping to add items to your cart</p>
            <a href="index.php?page=browse" class="btn btn-primary" style="margin-top: 1rem;">Browse Products</a>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <h2>🛒 Shopping Cart</h2>
        
        <form method="POST" action="actions.php">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #D2DCB6;">
                            <th style="text-align: left; padding: 1rem;">Product</th>
                            <th style="text-align: center; padding: 1rem;">Price</th>
                            <th style="text-align: center; padding: 1rem;">Quantity</th>
                            <th style="text-align: right; padding: 1rem;">Subtotal</th>
                            <th style="text-align: center; padding: 1rem;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cartItems as $item): ?>
                            <tr style="border-bottom: 1px solid #D2DCB6;">
                                <td style="padding: 1rem;">
                                    <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                    <br>
                                    <span style="font-size: 0.9rem; color: #666;">by <?= htmlspecialchars($item['seller_name']) ?></span>
                                </td>
                                <td style="text-align: center; padding: 1rem;">EC$<?= number_format($item['price'], 2) ?></td>
                                <td style="text-align: center; padding: 1rem;">
                                    <input type="number" name="quantities[<?= $item['id'] ?>]" value="<?= $item['cartQuantity'] ?>" min="1" max="<?= intval($item['quantity']) ?>" style="width: 60px; padding: 0.5rem; border: 1px solid #D2DCB6; border-radius: 5px; text-align: center;">
                                    <br>
                                    <span style="font-size: 0.85rem; color: #666;"><?= htmlspecialchars($item['unit']) ?></span>
                                </td>
                                <td style="text-align: right; padding: 1rem;">
                                    <strong>EC$<?= number_format($item['subtotal'], 2) ?></strong>
                                </td>
                                <td style="text-align: center; padding: 1rem;">
                                    <form method="POST" action="actions.php" style="display: inline;">
                                        <input type="hidden" name="listingId" value="<?= $item['id'] ?>">
                                        <button type="submit" name="removeFromCart" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" name="updateCart" class="btn btn-warning">📝 Update Cart</button>
                <a href="index.php?page=browse" class="btn btn-secondary">Continue Shopping</a>
            </div>
        </form>
    </div>

    <!-- Order Summary -->
    <div class="card" style="max-width: 500px;">
        <h3>Order Summary</h3>
        
        <div style="margin: 1.5rem 0; padding: 1rem 0; border-bottom: 1px solid #D2DCB6;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                <span>Subtotal:</span>
                <span>EC$<?= number_format($total, 2) ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                <span>Delivery Fee:</span>
                <span>EC$0.00</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                <span>Taxes:</span>
                <span>EC$0.00</span>
            </div>
        </div>
        
        <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: bold; margin-bottom: 1.5rem;">
            <span>Total:</span>
            <span style="color: #A1BC98;">EC$<?= number_format($total, 2) ?></span>
        </div>
        
        <?php if($_SESSION['isLoggedIn']): ?>
            <form method="POST" action="actions.php">
                <button type="submit" name="checkout" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">💳 Checkout</button>
            </form>
            <p style="text-align: center; margin-top: 1rem; font-size: 0.9rem; color: #666;">
                ✓ Secure checkout | 🚚 Fresh delivery | 💬 Direct seller contact
            </p>
        <?php else: ?>
            <a href="index.php?page=login" class="btn btn-primary" style="width: 100%; padding: 1rem; text-align: center; display: block;">Login to Checkout</a>
        <?php endif; ?>
    </div>

    <style>
        @media (max-width: 768px) {
            table {
                font-size: 0.9rem;
            }
            
            th, td {
                padding: 0.75rem !important;
            }
        }
    </style>
<?php endif; ?>