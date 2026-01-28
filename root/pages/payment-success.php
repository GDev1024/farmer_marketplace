<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

// Get the latest order for this user
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

$msg = getAndClearMessage();
if($msg['message']): ?>
    <div class="alert alert-<?= $msg['type'] ?>">
        <?= ($msg['type'] === 'success' ? '✓' : '⚠') ?> <?= htmlspecialchars($msg['message']) ?>
    </div>
<?php endif; ?>

<section class="payment-success">
    <div class="container">
        <?php include 'includes/page-navigation.php'; ?>
        
        <article class="payment-success__card">
            <header class="payment-success__header">
                <div class="payment-success__icon">
                    <span class="payment-success__checkmark" aria-hidden="true">✓</span>
                </div>
                
                <h1 class="payment-success__title">Payment Successful!</h1>
                
                <p class="payment-success__message">
                    Thank you for your purchase. Your order has been confirmed and is being processed.
                </p>
            </header>
            
            <?php if ($order): ?>
                <section class="payment-success__order-details">
                    <h2 class="payment-success__details-title">Order Details</h2>
                    
                    <dl class="payment-success__details-grid">
                        <div class="payment-success__detail">
                            <dt class="payment-success__detail-label">Order ID:</dt>
                            <dd class="payment-success__detail-value">#<?= $order['id'] ?></dd>
                        </div>
                        <div class="payment-success__detail">
                            <dt class="payment-success__detail-label">Total Amount:</dt>
                            <dd class="payment-success__detail-value payment-success__detail-value--amount">EC$<?= number_format($order['total_price'], 2) ?></dd>
                        </div>
                        <div class="payment-success__detail">
                            <dt class="payment-success__detail-label">Payment Method:</dt>
                            <dd class="payment-success__detail-value"><?= htmlspecialchars($order['payment_method'] ?? 'N/A') ?></dd>
                        </div>
                        <div class="payment-success__detail">
                            <dt class="payment-success__detail-label">Order Date:</dt>
                            <dd class="payment-success__detail-value"><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></dd>
                        </div>
                    </dl>
                </section>
            <?php endif; ?>
            
            <nav class="payment-success__actions" aria-label="Next steps">
                <a href="index.php?page=orders" class="btn btn-primary">
                    <span aria-hidden="true">📋</span> View My Orders
                </a>
                <a href="index.php?page=browse" class="btn btn-secondary">
                    <span aria-hidden="true">🛒</span> Continue Shopping
                </a>
            </nav>
            
            <footer class="payment-success__next-steps">
                <h3 class="payment-success__next-steps-title">What happens next?</h3>
                <ol class="payment-success__steps-list">
                    <li class="payment-success__step">
                        <span class="payment-success__step-number" aria-hidden="true">1</span>
                        <span class="payment-success__step-text">Sellers will be notified of your order</span>
                    </li>
                    <li class="payment-success__step">
                        <span class="payment-success__step-number" aria-hidden="true">2</span>
                        <span class="payment-success__step-text">They'll prepare your items for pickup/delivery</span>
                    </li>
                    <li class="payment-success__step">
                        <span class="payment-success__step-number" aria-hidden="true">3</span>
                        <span class="payment-success__step-text">You'll receive updates via messages</span>
                    </li>
                </ol>
            </footer>
        </article>
    </div>
</section>

