<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$msg = getAndClearMessage();
if($msg['message']): ?>
    <div class="alert alert-<?= $msg['type'] ?>">
        <?= ($msg['type'] === 'success' ? '✓' : '⚠') ?> <?= htmlspecialchars($msg['message']) ?>
    </div>
<?php endif; ?>

<section class="payment-cancel">
    <div class="container">
        <?php include 'includes/page-navigation.php'; ?>
        
        <article class="payment-cancel__card">
            <header class="payment-cancel__header">
                <div class="payment-cancel__icon">
                    <span class="payment-cancel__warning-icon" aria-hidden="true">⚠</span>
                </div>
                
                <h1 class="payment-cancel__title">Payment Cancelled</h1>
                
                <p class="payment-cancel__message">
                    Your payment was cancelled and no charges were made to your account. Your cart items are still saved.
                </p>
            </header>
            
            <section class="payment-cancel__reasons">
                <h2 class="payment-cancel__reasons-title">Common reasons for cancellation:</h2>
                
                <ul class="payment-cancel__reasons-list">
                    <li class="payment-cancel__reason">
                        <span class="payment-cancel__reason-bullet" aria-hidden="true">•</span>
                        <span class="payment-cancel__reason-text">You clicked the back button or closed the payment window</span>
                    </li>
                    <li class="payment-cancel__reason">
                        <span class="payment-cancel__reason-bullet" aria-hidden="true">•</span>
                        <span class="payment-cancel__reason-text">Payment method was declined or had insufficient funds</span>
                    </li>
                    <li class="payment-cancel__reason">
                        <span class="payment-cancel__reason-bullet" aria-hidden="true">•</span>
                        <span class="payment-cancel__reason-text">You decided to review your order before completing</span>
                    </li>
                    <li class="payment-cancel__reason">
                        <span class="payment-cancel__reason-bullet" aria-hidden="true">•</span>
                        <span class="payment-cancel__reason-text">Technical issue occurred during payment processing</span>
                    </li>
                </ul>
            </section>
            
            <nav class="payment-cancel__actions" aria-label="Recovery options">
                <a href="index.php?page=checkout" class="btn btn-primary">
                    <span aria-hidden="true">💳</span> Try Payment Again
                </a>
                <a href="index.php?page=cart" class="btn btn-secondary">
                    <span aria-hidden="true">🛒</span> Review Cart
                </a>
                <a href="index.php?page=browse" class="btn btn-secondary">
                    <span aria-hidden="true">🔍</span> Continue Shopping
                </a>
            </nav>
            
            <footer class="payment-cancel__help">
                <h3 class="payment-cancel__help-title">Need help?</h3>
                <p class="payment-cancel__help-description">
                    If you're experiencing issues with payment, try these steps:
                </p>
                
                <ul class="payment-cancel__help-list">
                    <li class="payment-cancel__help-item">
                        <span class="payment-cancel__help-check" aria-hidden="true">✓</span>
                        <span class="payment-cancel__help-text">Check your internet connection</span>
                    </li>
                    <li class="payment-cancel__help-item">
                        <span class="payment-cancel__help-check" aria-hidden="true">✓</span>
                        <span class="payment-cancel__help-text">Verify your payment method details</span>
                    </li>
                    <li class="payment-cancel__help-item">
                        <span class="payment-cancel__help-check" aria-hidden="true">✓</span>
                        <span class="payment-cancel__help-text">Try a different payment method</span>
                    </li>
                    <li class="payment-cancel__help-item">
                        <span class="payment-cancel__help-check" aria-hidden="true">✓</span>
                        <span class="payment-cancel__help-text">Contact us if the problem persists</span>
                    </li>
                </ul>
            </footer>
        </article>
    </div>
</section>

