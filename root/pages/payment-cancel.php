<?php
if (!$_SESSION['isLoggedIn']) {
    redirect('login');
}

$msg = getAndClearMessage();
if($msg['message']): ?>
    <div class="alert alert-<?= $msg['type'] ?>">
        <?= ($msg['type'] === 'success' ? '✓' : '⚠') ?> <?= htmlspecialchars($msg['message']) ?>
    </div>
<?php endif; ?>

<div style="max-width: 600px; margin: 0 auto;">
    <div class="card">
        <div class="card-body" style="text-align: center; padding: var(--space-2xl);">
            <!-- Cancel Icon -->
            <div style="width: 80px; height: 80px; background: var(--warning); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-xl) auto;">
                <span style="font-size: 40px; color: white;">⚠</span>
            </div>
            
            <h1 style="color: var(--warning); margin-bottom: var(--space-md); font-size: 28px; font-weight: var(--font-weight-bold);">
                Payment Cancelled
            </h1>
            
            <p style="color: var(--text-secondary); font-size: 16px; margin-bottom: var(--space-xl);">
                Your payment was cancelled and no charges were made to your account. Your cart items are still saved.
            </p>
            
            <!-- Reasons -->
            <div style="background: var(--bg-primary); border-radius: var(--radius-lg); padding: var(--space-lg); margin-bottom: var(--space-xl); text-align: left;">
                <h3 style="margin-bottom: var(--space-md); color: var(--text-primary); text-align: center;">Common reasons for cancellation:</h3>
                
                <div style="display: flex; flex-direction: column; gap: var(--space-sm);">
                    <div style="display: flex; align-items: center; gap: var(--space-sm);">
                        <span style="color: var(--warning);">•</span>
                        <span style="color: var(--text-secondary);">You clicked the back button or closed the payment window</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: var(--space-sm);">
                        <span style="color: var(--warning);">•</span>
                        <span style="color: var(--text-secondary);">Payment method was declined or had insufficient funds</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: var(--space-sm);">
                        <span style="color: var(--warning);">•</span>
                        <span style="color: var(--text-secondary);">You decided to review your order before completing</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: var(--space-sm);">
                        <span style="color: var(--warning);">•</span>
                        <span style="color: var(--text-secondary);">Technical issue occurred during payment processing</span>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div style="display: flex; gap: var(--space-md); justify-content: center; flex-wrap: wrap;">
                <a href="index.php?page=checkout" class="btn btn-primary">
                    💳 Try Payment Again
                </a>
                <a href="index.php?page=cart" class="btn btn-secondary">
                    🛒 Review Cart
                </a>
                <a href="index.php?page=browse" class="btn btn-secondary">
                    🔍 Continue Shopping
                </a>
            </div>
            
            <!-- Help Section -->
            <div style="margin-top: var(--space-2xl); padding-top: var(--space-xl); border-top: 1px solid var(--border-light);">
                <h4 style="margin-bottom: var(--space-md); color: var(--text-primary);">Need help?</h4>
                <p style="color: var(--text-secondary); margin-bottom: var(--space-md);">
                    If you're experiencing issues with payment, try these steps:
                </p>
                
                <div style="text-align: left; max-width: 400px; margin: 0 auto;">
                    <div style="display: flex; align-items: center; gap: var(--space-md); margin-bottom: var(--space-sm);">
                        <span style="color: var(--primary);">✓</span>
                        <span style="color: var(--text-secondary); font-size: 14px;">Check your internet connection</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: var(--space-md); margin-bottom: var(--space-sm);">
                        <span style="color: var(--primary);">✓</span>
                        <span style="color: var(--text-secondary); font-size: 14px;">Verify your payment method details</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: var(--space-md); margin-bottom: var(--space-sm);">
                        <span style="color: var(--primary);">✓</span>
                        <span style="color: var(--text-secondary); font-size: 14px;">Try a different payment method</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: var(--space-md);">
                        <span style="color: var(--primary);">✓</span>
                        <span style="color: var(--text-secondary); font-size: 14px;">Contact us if the problem persists</span>
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
        margin-bottom: var(--space-sm);
    }
}
</style>