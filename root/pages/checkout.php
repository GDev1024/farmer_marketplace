<?php
// Include payment handler
require_once 'includes/PaymentHandler.php';
require_once 'includes/Config.php';
Config::load();

if (!$_SESSION['isLoggedIn']) {
    redirect('login');
}

if (empty($_SESSION['cart'])) {
    redirect('cart');
}

$paymentHandler = new PaymentHandler();
$availablePaymentMethods = $paymentHandler->getAvailablePaymentMethods();

// Calculate cart totals
$cartItems = [];
$subtotal = 0;

foreach ($_SESSION['cart'] as $listingId => $quantity) {
    $stmt = $pdo->prepare("SELECT l.*, u.name as seller_name FROM listings l JOIN users u ON l.user_id = u.id WHERE l.id = ?");
    $stmt->execute([$listingId]);
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($listing && $listing['quantity'] >= $quantity) {
        $itemTotal = $listing['price'] * $quantity;
        $subtotal += $itemTotal;
        
        $cartItems[] = [
            'listing' => $listing,
            'quantity' => $quantity,
            'total' => $itemTotal
        ];
    }
}

$tax = $subtotal * 0.10; // 10% tax
$total = $subtotal + $tax;

// Display message if set
$msg = getAndClearMessage();
if($msg['message']): ?>
    <div class="alert alert-<?= $msg['type'] ?>">
        <?= ($msg['type'] === 'success' ? '✓' : '⚠') ?> <?= htmlspecialchars($msg['message']) ?>
    </div>
<?php endif; ?>

<main class="page-main checkout-page" id="main-content" role="main">
    <header class="page-header">
        <h1 class="page-title">Secure Checkout</h1>
        <p class="page-subtitle">Review your order and complete your purchase securely</p>
    </header>

    <div class="checkout-content">
        <section class="order-summary-section" aria-labelledby="order-summary-title">
            <div class="card">
                <header class="card-header">
                    <h2 id="order-summary-title" class="checkout-section-title">
                        <span class="section-icon" aria-hidden="true">🛒</span>
                        Order Summary
                    </h2>
                    <p class="section-subtitle">Review your items before payment</p>
                </header>
                
                <div class="card-body">
                    <div class="order-items-list">
                        <?php foreach ($cartItems as $item): ?>
                            <article class="order-item" aria-labelledby="order-item-<?= $item['listing']['id'] ?>-title">
                                <div class="order-item-image">
                                    <?php if (!empty($item['listing']['thumbnail_path']) && file_exists($item['listing']['thumbnail_path'])): ?>
                                        <img src="<?= htmlspecialchars($item['listing']['thumbnail_path']) ?>" 
                                             alt="<?= htmlspecialchars($item['listing']['product_name']) ?>"
                                             class="item-image"
                                             loading="lazy">
                                    <?php else: ?>
                                        <div class="item-placeholder" aria-label="Product image placeholder">
                                            <?php
                                            $emojis = ['vegetables' => '🥬', 'fruits' => '🍎', 'herbs' => '🌿', 'grains' => '🌾'];
                                            echo $emojis[$item['listing']['category']] ?? '🌾';
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="order-item-details">
                                    <h3 id="order-item-<?= $item['listing']['id'] ?>-title" class="item-title">
                                        <?= htmlspecialchars($item['listing']['product_name']) ?>
                                    </h3>
                                    <p class="item-seller">
                                        From <strong><?= htmlspecialchars($item['listing']['seller_name']) ?></strong>
                                    </p>
                                    <div class="item-pricing">
                                        <span class="item-quantity-price" aria-label="<?= $item['quantity'] ?> items at EC$<?= number_format($item['listing']['price'], 2) ?> each">
                                            <?= $item['quantity'] ?> × EC$<?= number_format($item['listing']['price'], 2) ?>
                                        </span>
                                        <span class="item-total" aria-label="Item total: EC$<?= number_format($item['total'], 2) ?>">
                                            EC$<?= number_format($item['total'], 2) ?>
                                        </span>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="order-totals">
                        <div class="total-line">
                            <span class="total-label">Subtotal:</span>
                            <span class="total-value">EC$<?= number_format($subtotal, 2) ?></span>
                        </div>
                        <div class="total-line">
                            <span class="total-label">Tax (10%):</span>
                            <span class="total-value">EC$<?= number_format($tax, 2) ?></span>
                        </div>
                        <div class="total-divider"></div>
                        <div class="total-line total-final">
                            <span class="total-label">Total:</span>
                            <span class="total-value total-amount">EC$<?= number_format($total, 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="payment-section" aria-labelledby="payment-title">
            <div class="card">
                <header class="card-header">
                    <h2 id="payment-title" class="checkout-section-title">
                        <span class="section-icon" aria-hidden="true">💳</span>
                        Payment Method
                    </h2>
                    <p class="section-subtitle">Choose your preferred payment option</p>
                </header>
                
                <div class="card-body">
                    <?php if (empty($availablePaymentMethods)): ?>
                        <div class="alert alert-warning">
                            <span aria-hidden="true">⚠</span> Payment methods are not configured. Please contact support.
                        </div>
                    <?php else: ?>
                        <form class="payment-form" role="form" aria-label="Payment method selection">
                            <fieldset class="payment-methods" aria-labelledby="payment-methods-legend">
                                <legend id="payment-methods-legend" class="sr-only">Select payment method</legend>
                                
                                <?php if (in_array('stripe', $availablePaymentMethods)): ?>
                                    <div class="payment-method" data-method="stripe">
                                        <div class="payment-method-header">
                                            <input type="radio" 
                                                   name="payment_method" 
                                                   value="stripe" 
                                                   id="stripe" 
                                                   class="payment-method-input"
                                                   checked
                                                   aria-describedby="stripe-description">
                                            <label for="stripe" class="payment-method-label">
                                                <div class="payment-method-info">
                                                    <span class="payment-method-name">
                                                        <span class="payment-icon" aria-hidden="true">💳</span>
                                                        Credit/Debit Card
                                                    </span>
                                                    <span id="stripe-description" class="payment-method-desc">
                                                        Secure payment with Stripe
                                                    </span>
                                                </div>
                                                <div class="payment-method-indicator" aria-hidden="true"></div>
                                            </label>
                                        </div>
                                        <div class="payment-method-form" id="stripe-form" aria-labelledby="stripe-form-title">
                                            <h3 id="stripe-form-title" class="sr-only">Credit card information</h3>
                                            <div id="stripe-card-element" 
                                                 class="stripe-card-element"
                                                 aria-label="Credit card information input"></div>
                                            <div id="stripe-card-errors" 
                                                 class="payment-error"
                                                 role="alert"
                                                 aria-live="polite"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (in_array('paypal', $availablePaymentMethods)): ?>
                                    <div class="payment-method" data-method="paypal">
                                        <div class="payment-method-header">
                                            <input type="radio" 
                                                   name="payment_method" 
                                                   value="paypal" 
                                                   id="paypal" 
                                                   class="payment-method-input"
                                                   <?= !in_array('stripe', $availablePaymentMethods) ? 'checked' : '' ?>
                                                   aria-describedby="paypal-description">
                                            <label for="paypal" class="payment-method-label">
                                                <div class="payment-method-info">
                                                    <span class="payment-method-name">
                                                        <span class="payment-icon" aria-hidden="true">🅿️</span>
                                                        PayPal
                                                    </span>
                                                    <span id="paypal-description" class="payment-method-desc">
                                                        Pay with your PayPal account
                                                    </span>
                                                </div>
                                                <div class="payment-method-indicator" aria-hidden="true"></div>
                                            </label>
                                        </div>
                                        <div class="payment-method-form" id="paypal-form" style="display: none;" aria-labelledby="paypal-form-title">
                                            <h3 id="paypal-form-title" class="sr-only">PayPal payment information</h3>
                                            <p class="paypal-notice">
                                                You'll be redirected to PayPal to complete your payment securely.
                                            </p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </fieldset>
                            
                            <div class="checkout-actions">
                                <button type="button" 
                                        id="checkout-btn" 
                                        class="btn btn-primary btn-lg checkout-button" 
                                        disabled
                                        aria-describedby="checkout-btn-help">
                                    <span id="checkout-btn-text" class="btn-content">
                                        <span class="btn-icon" aria-hidden="true">🔒</span>
                                        Complete Order - EC$<?= number_format($total, 2) ?>
                                    </span>
                                    <div id="checkout-spinner" class="btn-spinner" style="display: none;" aria-hidden="true">
                                        <div class="spinner"></div>
                                    </div>
                                </button>
                                <small id="checkout-btn-help" class="checkout-help">
                                    Select a payment method to continue
                                </small>
                            </div>
                        </form>
                        
                        <div class="security-notice">
                            <div class="security-icon" aria-hidden="true">🔒</div>
                            <div class="security-content">
                                <h3 class="security-title">Secure Payment</h3>
                                <p class="security-description">
                                    Your payment information is encrypted and secure. We never store your card details.
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</main>

<!-- Include Stripe.js -->
<?php if (in_array('stripe', $availablePaymentMethods)): ?>
<script src="https://js.stripe.com/v3/"></script>
<?php endif; ?>

<!-- Include PayPal SDK -->
<?php if (in_array('paypal', $availablePaymentMethods)): ?>
<script src="https://www.paypal.com/sdk/js?client-id=<?= $paymentHandler->getPayPalClientId() ?>&currency=USD"></script>
<?php endif; ?>

<script>
// Payment method switching
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        // Hide all forms
        document.querySelectorAll('.payment-method-form').forEach(form => {
            form.style.display = 'none';
        });
        
        // Show selected form
        const selectedForm = document.getElementById(this.value + '-form');
        if (selectedForm) {
            selectedForm.style.display = 'block';
        }
        
        // Enable checkout button
        document.getElementById('checkout-btn').disabled = false;
    });
});

<?php if (in_array('stripe', $availablePaymentMethods)): ?>
// Initialize Stripe
const stripe = Stripe('<?= $paymentHandler->getStripePublishableKey() ?>');
const elements = stripe.elements();

// Create card element
const cardElement = elements.create('card', {
    style: {
        base: {
            fontSize: '16px',
            color: 'var(--text-primary)',
            fontFamily: 'var(--font-family)',
            '::placeholder': {
                color: 'var(--text-muted)',
            },
        },
    },
});

cardElement.mount('#stripe-card-element');

// Handle card errors
cardElement.on('change', function(event) {
    const displayError = document.getElementById('stripe-card-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }
    
    // Enable/disable checkout button based on card validity
    const checkoutBtn = document.getElementById('checkout-btn');
    checkoutBtn.disabled = !event.complete;
});

// Show Stripe form by default if available
document.getElementById('stripe-form').style.display = 'block';
<?php endif; ?>

// Checkout button handler
document.getElementById('checkout-btn').addEventListener('click', async function() {
    const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
    const checkoutBtn = this;
    const btnText = document.getElementById('checkout-btn-text');
    const spinner = document.getElementById('checkout-spinner');
    
    // Disable button and show loading
    checkoutBtn.disabled = true;
    btnText.style.display = 'none';
    spinner.style.display = 'inline-block';
    
    if (selectedMethod === 'stripe') {
        await processStripePayment();
    } else if (selectedMethod === 'paypal') {
        await processPayPalPayment();
    }
});

<?php if (in_array('stripe', $availablePaymentMethods)): ?>
async function processStripePayment() {
    try {
        // Create payment intent
        const response = await fetch('actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'createStripePayment=1&amount=<?= $total ?>'
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.error);
        }
        
        // Confirm payment with Stripe
        const {error} = await stripe.confirmCardPayment(result.client_secret, {
            payment_method: {
                card: cardElement,
                billing_details: {
                    name: '<?= htmlspecialchars($_SESSION['name']) ?>',
                }
            }
        });
        
        if (error) {
            throw new Error(error.message);
        }
        
        // Payment successful, redirect to success page
        window.location.href = 'index.php?page=payment-success&payment_intent=' + result.payment_intent_id;
        
    } catch (error) {
        alert('Payment failed: ' + error.message);
        resetCheckoutButton();
    }
}
<?php endif; ?>

<?php if (in_array('paypal', $availablePaymentMethods)): ?>
async function processPayPalPayment() {
    try {
        // Create PayPal order
        const response = await fetch('actions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'createPayPalPayment=1&amount=<?= $total ?>'
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.error);
        }
        
        // Redirect to PayPal
        window.location.href = result.approval_url;
        
    } catch (error) {
        alert('Payment failed: ' + error.message);
        resetCheckoutButton();
    }
}
<?php endif; ?>

function resetCheckoutButton() {
    const checkoutBtn = document.getElementById('checkout-btn');
    const btnText = document.getElementById('checkout-btn-text');
    const spinner = document.getElementById('checkout-spinner');
    
    checkoutBtn.disabled = false;
    btnText.style.display = 'inline';
    spinner.style.display = 'none';
}
</script>

<style>
.payment-methods {
    display: flex;
    flex-direction: column;
    gap: var(--space-md);
}

.payment-method {
    border: 1px solid var(--border-light);
    border-radius: var(--radius-md);
    overflow: hidden;
    transition: var(--transition-fast);
}

.payment-method:hover {
    border-color: var(--primary);
}

.payment-method-header {
    position: relative;
}

.payment-method input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.payment-method-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-md);
    cursor: pointer;
    transition: var(--transition-fast);
}

.payment-method input[type="radio"]:checked + .payment-method-label {
    background: var(--bg-primary);
    border-left: 4px solid var(--primary);
}

.payment-method-info {
    display: flex;
    flex-direction: column;
    gap: var(--space-xs);
}

.payment-method-name {
    font-weight: var(--font-weight-semibold);
    font-size: 16px;
}

.payment-method-desc {
    color: var(--text-secondary);
    font-size: 14px;
}

.payment-method-form {
    padding: var(--space-md);
    border-top: 1px solid var(--border-light);
    background: var(--bg-primary);
}

@media (max-width: 768px) {
    .container > div {
        grid-template-columns: 1fr;
        gap: var(--space-lg);
    }
    
    .payment-method-label {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--space-sm);
    }
}
</style>