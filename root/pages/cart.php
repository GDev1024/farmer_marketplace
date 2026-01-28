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

<main class="page-main cart-page" id="main-content" role="main">
    <?php include 'includes/page-navigation.php'; ?>
    
    <header class="page-header">
        <h1 class="page-title">Shopping Cart</h1>
        <p class="page-subtitle">Review your selected items and proceed to checkout</p>
    </header>

    <?php if(empty($cartItems)): ?>
        <section class="empty-cart-section" aria-labelledby="empty-cart-title">
            <div class="card">
                <div class="empty-state">
                    <div class="empty-icon" aria-hidden="true">🛒</div>
                    <h2 id="empty-cart-title" class="empty-title">Your cart is empty</h2>
                    <p class="empty-description">Start shopping to add fresh produce to your cart</p>
                    <a href="index.php?page=browse" class="btn btn-primary" aria-label="Browse products to add to cart">
                        <span class="btn-icon" aria-hidden="true">🌾</span>
                        Browse Products
                    </a>
                </div>
            </div>
        </section>
    <?php else: ?>
        <div class="cart-content">
            <section class="cart-items-section" aria-labelledby="cart-items-title">
                <div class="card">
                    <header class="card-header">
                        <h2 id="cart-items-title" class="cart-section-title">
                            <span class="section-icon" aria-hidden="true">🛒</span>
                            Cart Items (<?= count($cartItems) ?>)
                        </h2>
                    </header>
                    
                    <div class="card-body">
                        <form method="POST" action="actions.php" class="cart-form" aria-label="Update cart quantities">
                            <div class="cart-items-list">
                                <?php foreach($cartItems as $item): ?>
                                    <article class="cart-item" aria-labelledby="item-<?= $item['id'] ?>-title">
                                        <div class="cart-item-image">
                                            <?php if (!empty($item['thumbnail_path']) && file_exists($item['thumbnail_path'])): ?>
                                                <img src="<?= htmlspecialchars($item['thumbnail_path']) ?>" 
                                                     alt="<?= htmlspecialchars($item['product_name']) ?>"
                                                     class="item-image"
                                                     loading="lazy">
                                            <?php else: ?>
                                                <div class="item-placeholder" aria-label="Product image placeholder">
                                                    <?php
                                                    $emojis = [
                                                        'vegetables' => '🥬',
                                                        'fruits' => '🍎',
                                                        'herbs' => '🌿',
                                                        'grains' => '🌾'
                                                    ];
                                                    echo $emojis[$item['category']] ?? '🌾';
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="cart-item-details">
                                            <header class="item-header">
                                                <h3 id="item-<?= $item['id'] ?>-title" class="item-title">
                                                    <?= htmlspecialchars($item['product_name']) ?>
                                                </h3>
                                                <div class="item-price">EC$<?= number_format($item['price'], 2) ?></div>
                                            </header>
                                            
                                            <p class="item-seller">
                                                by <strong><?= htmlspecialchars($item['seller_name']) ?></strong>
                                            </p>
                                            
                                            <div class="item-controls">
                                                <div class="quantity-control">
                                                    <label for="quantity-<?= $item['id'] ?>" class="quantity-label">
                                                        Quantity
                                                    </label>
                                                    <input type="number" 
                                                           id="quantity-<?= $item['id'] ?>"
                                                           name="quantities[<?= $item['id'] ?>]" 
                                                           value="<?= $item['cartQuantity'] ?>" 
                                                           min="1" 
                                                           max="<?= intval($item['quantity']) ?>" 
                                                           class="quantity-input"
                                                           aria-describedby="unit-<?= $item['id'] ?>">
                                                    <small id="unit-<?= $item['id'] ?>" class="quantity-unit">
                                                        <?= htmlspecialchars($item['unit']) ?>
                                                    </small>
                                                </div>
                                                
                                                <div class="item-subtotal">
                                                    <span class="subtotal-label">Subtotal:</span>
                                                    <span class="subtotal-amount">EC$<?= number_format($item['subtotal'], 2) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="cart-item-actions">
                                            <form method="POST" action="actions.php" class="remove-form" aria-label="Remove <?= htmlspecialchars($item['product_name']) ?> from cart">
                                                <input type="hidden" name="listingId" value="<?= $item['id'] ?>">
                                                <button type="submit" name="removeFromCart" class="btn btn-danger btn-sm remove-btn">
                                                    <span class="btn-icon" aria-hidden="true">🗑️</span>
                                                    <span class="sr-only">Remove <?= htmlspecialchars($item['product_name']) ?></span>
                                                    Remove
                                                </button>
                                            </form>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                            
                            <footer class="cart-actions">
                                <button type="submit" name="updateCart" class="btn btn-warning">
                                    <span class="btn-icon" aria-hidden="true">📝</span>
                                    Update Cart
                                </button>
                                <a href="index.php?page=browse" class="btn btn-secondary">
                                    <span class="btn-icon" aria-hidden="true">🛍️</span>
                                    Continue Shopping
                                </a>
                            </footer>
                        </form>
                    </div>
                </div>
            </section>

            <aside class="order-summary-section" role="complementary" aria-labelledby="summary-title">
                <div class="card order-summary-card">
                    <header class="card-header">
                        <h2 id="summary-title" class="cart-section-title">
                            <span class="section-icon" aria-hidden="true">📋</span>
                            Order Summary
                        </h2>
                    </header>
                    
                    <div class="card-body">
                        <div class="summary-details">
                            <div class="summary-line">
                                <span class="summary-label">Subtotal:</span>
                                <span class="summary-value">EC$<?= number_format($total, 2) ?></span>
                            </div>
                            <div class="summary-line">
                                <span class="summary-label">Tax (10%):</span>
                                <span class="summary-value">EC$<?= number_format($total * 0.10, 2) ?></span>
                            </div>
                            <div class="summary-divider"></div>
                            <div class="summary-line summary-total">
                                <span class="summary-label">Total:</span>
                                <span class="summary-value total-amount">EC$<?= number_format($total + ($total * 0.10), 2) ?></span>
                            </div>
                        </div>
                        
                        <div class="checkout-section">
                            <?php if(isLoggedIn()): ?>
                                <a href="index.php?page=checkout" class="btn btn-primary btn-full checkout-btn">
                                    <span class="btn-icon" aria-hidden="true">💳</span>
                                    Proceed to Checkout
                                </a>
                                <div class="checkout-features">
                                    <div class="feature-item">
                                        <span class="feature-icon" aria-hidden="true">✓</span>
                                        <span class="feature-text">Secure payment</span>
                                    </div>
                                    <div class="feature-item">
                                        <span class="feature-icon" aria-hidden="true">🚚</span>
                                        <span class="feature-text">Fresh delivery</span>
                                    </div>
                                    <div class="feature-item">
                                        <span class="feature-icon" aria-hidden="true">💬</span>
                                        <span class="feature-text">Direct seller contact</span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <a href="index.php?page=login" class="btn btn-primary btn-full">
                                    <span class="btn-icon" aria-hidden="true">🔐</span>
                                    Login to Checkout
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    <?php endif; ?>
</main>