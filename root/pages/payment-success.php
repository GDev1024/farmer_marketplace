<?php
if (!$_SESSION['isLoggedIn']) {
    redirect('login');
}

// Get the latest order for this user
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$_SESSION['userId']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

$msg = getAndClearMessage();
if($msg['message']): ?>
    <div class="alert alert-<?= $msg['type'] ?>">
        <?= ($msg['type'] === 'success' ? '✓' : '⚠') ?> <?= htmlspecialchars($msg['message']) ?>
    </div>
<?php endif; ?>

<div style="max-width: 600px; margin: 0 auto;">
    <div class="card">
        <div class="card-body" style="text-align: center; padding: var(--space-2xl);">
            <!-- Success Icon -->
            <div style="width: 80px; height: 80px; background: var(--success); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-xl) auto;">
                <span style="font-size: 40px; color: white;">✓</span>
            </div>
            
            <h1 style="color: var(--success); margin-bottom: var(--space-md); font-size: 28px; font-weight: var(--font-weight-bold);">
                Payment Successful!
            </h1>
            
            <p style="color: var(--text-secondary); font-size: 16px; margin-bottom: var(--space-xl);">
                Thank you for your purchase. Your order has been confirmed and is being processed.
            </p>
            
            <?php if ($order): ?>
                <div style="background: var(--bg-primary); border-radius: var(--radius-lg); padding: var(--space-lg); margin-bottom: var(--space-xl);">
                    <h3 style="margin-bottom: var(--space-md); color: var(--text-primary);">Order Details</h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md); text-align: left;">
                        <div>
                            <strong>Order ID:</strong><br>
                            <span style="color: var(--text-secondary);">#<?= $order['id'] ?></span>
                        </div>
                        <div>
                            <strong>Total Amount:</strong><br>
                            <span style="color: var(--success); font-weight: var(--font-weight-semibold);">EC$<?= number_format($order['total_price'], 2) ?></span>
                        </div>
                        <div>
                            <strong>Payment Method:</strong><br>
                            <span style="color: var(--text-secondary); text-transform: capitalize;"><?= htmlspecialchars($order['payment_method'] ?? 'N/A') ?></span>
                        </div>
                        <div>
                            <strong>Order Date:</strong><br>
                            <span style="color: var(--text-secondary);"><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Action Buttons -->
            <div style="display: flex; gap: var(--space-md); justify-content: center; flex-wrap: wrap;">
                <a href="index.php?page=orders" class="btn btn-primary">
                    📋 View My Orders
                </a>
                <a href="index.php?page=browse" class="btn btn-secondary">
                    🛒 Continue Shopping
                </a>
            </div>
            
            <!-- What's Next -->
            <div style="margin-top: var(--space-2xl); padding-top: var(--space-xl); border-top: 1px solid var(--border-light);">
                <h4 style="margin-bottom: var(--space-md); color: var(--text-primary);">What happens next?</h4>
                <div style="text-align: left; max-width: 400px; margin: 0 auto;">
                    <div style="display: flex; align-items: center; gap: var(--space-md); margin-bottom: var(--space-md);">
                        <span style="width: 24px; height: 24px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">1</span>
                        <span style="color: var(--text-secondary);">Sellers will be notified of your order</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: var(--space-md); margin-bottom: var(--space-md);">
                        <span style="width: 24px; height: 24px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">2</span>
                        <span style="color: var(--text-secondary);">They'll prepare your items for pickup/delivery</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: var(--space-md);">
                        <span style="width: 24px; height: 24px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">3</span>
                        <span style="color: var(--text-secondary);">You'll receive updates via messages</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn {
    display: inline-flex;
    align-items: center;
    gap: var(--space-sm);
    padding: var(--space-md) var(--space-lg);
    border: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: var(--font-weight-medium);
    text-decoration: none;
    cursor: pointer;
    transition: var(--transition-fast);
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.btn-secondary {
    background: var(--bg-tertiary);
    color: var(--text-primary);
}

.btn-secondary:hover {
    background: var(--border-medium);
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .card-body {
        padding: var(--space-xl) var(--space-lg) !important;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>