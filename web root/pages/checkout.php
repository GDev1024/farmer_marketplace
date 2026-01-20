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

<div class="checkout-container">
    <!-- Order Summary -->
    <div class="card">
        <div class="card-header">
            <h2 style="margin: 0; display: flex; align-items: center; gap: var(--space-sm);">
                <span>🛒</span> Order Summary
            </h2>
            <p style="color: var(--text-secondary); margin: var(--space-sm) 0 0 0; font-size: 14px;">
                Review your items before payment
            </p>
        </div>
        
        <div class="card-body">
            <?php foreach ($cartItems as $item): ?>
                <div style="display: flex; gap: var(--space-md); padding: var(--space-md) 0; border-bottom: 1px solid var(--border-light);">
                    <div style="width: 60px; height: 60px; border-radius: var(--radius-md); overflow: hidden; background: var(--bg-tertiary); display: flex; align-items: center; justify-content: center;">
                        <?php if (!empty($item['listing']['thumbnail_path']) && file_exists($item['listing']['thumbnail_path'])): ?>
                            <img src="<?= htmlspecialchars($item['listing']['thumbnail_path']) ?>" 
                                 alt="<?= htmlspecialchars($item['listing']['product_name']) ?>"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <span style="font-size: 24px;">
                                <?php
                                $emojis = ['vegetables' => '🥬', 'fruits' => '🍎', 'herbs' => '🌿', 'grains' => '🌾'];
                                echo $emojis[$item['listing']['category']] ?? '🌾';
                                ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div style="flex: 1;">
                        <h4 style="margin: 0 0 var(--space-xs) 0; font-size: 16px; font-weight: var(--font-weight-semibold);">
                            <?= htmlspecialchars($item['listing']['product_name']) ?>
                        </h4>
                        <p style="margin: 0; color: var(--text-secondary); font-size: 14px;">
                            From <?= htmlspecialchars($item['listing']['seller_name']) ?>
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: var(--space-sm);">
                            <span style="color: var(--text-secondary); font-size: 14px;">
                                <?= $item['quantity'] ?> × EC$<?= number_format($item['listing']['price'], 2) ?>
                            </span>
                            <span style="font-weight: var(--font-weight-semibold); color: var(--success);">
                                EC$<?= number_format($item['total'], 2) ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <!-- Order Totals -->
            <div style="margin-top: var(--space-lg);">
                <div style="display: flex; justify-content: space-between; margin-bottom: var(--space-sm);">
                    <span style="color: var(--text-secondary);">Subtotal:</span>
                    <span>EC$<?= number_format($subtotal, 2) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: var(--space-sm);">
                    <span style="color: var(--text-secondary);">Tax (10%):</span>
                    <span>EC$<?= number_format($tax, 2) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; padding-top: var(--space-sm); border-top: 2px solid var(--border-medium); font-size: 18px; font-weight: var(--font-weight-bold);">
                    <span>Total:</span>
                    <span style="color: var(--success);">EC$<?= number_format($total, 2) ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payment Methods -->
    <div class="card">
        <div class="card-header">
            <h3 style="margin: 0; display: flex; align-items: center; gap: var(--space-sm);">
                <span>💳</span> Payment Method
            </h3>
            <p style="color: var(--text-secondary); margin: var(--space-sm) 0 0 0; font-size: 14px;">
                Choose your preferred payment option
            </p>
        </div>
        
        <div class="card-body">
            <?php if (empty($availablePaymentMethods)): ?>
                <div class="alert alert-warning">
                    ⚠ Payment methods are not configured. Please contact support.
                </div>
            <?php else: ?>
                <div class="payment-methods">
                    <?php if (in_array('stripe', $availablePaymentMethods)): ?>
                        <div class="payment-method" data-method="stripe">
                            <div class="payment-method-header">
                                <input type="radio" name="payment_method" value="stripe" id="stripe" checked>
                                <label for="stripe" class="payment-method-label">
                                    <div class="payment-method-info">
                                        <span class="payment-method-name">💳 Credit/Debit Card</span>
                                        <span class="payment-method-desc">Secure payment with Stripe</span>
                                    </div>
                                    <div class="payment-method-logos">
                                        <span style="font-size: 20px;">💳</span>
                                    </div>
                                </label>
                            </div>
                            <div class="payment-method-form" id="stripe-form">
                                <div id="stripe-card-element" style="padding: var(--space-md); border: 1px solid var(--border-light); border-radius: var(--radius-md); background: var(--bg-secondary);"></div>
                                <div id="stripe-card-errors" style="color: var(--error); font-size: 14px; margin-top: var(--space-sm);"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (in_array('paypal', $availablePaymentMethods)): ?>
                        <div class="payment-method" data-method="paypal">
                            <div class="payment-method-header">
                                <input type="radio" name="payment_method" value="paypal" id="paypal" <?= !in_array('stripe', $availablePaymentMethods) ? 'checked' : '' ?>>
                                <label for="paypal" class="payment-method-label">
                                    <div class="payment-method-info">
                                        <span class="payment-method-name">🅿️ PayPal</span>
                                        <span class="payment-method-desc">Pay with your PayPal account</span>
                                    </div>
                                    <div class="payment-method-logos">
                                        <span style="font-size: 20px; color: #0070ba;">🅿️</span>
                                    </div>
                                </label>
                            </div>
                            <div class="payment-method-form" id="paypal-form" style="display: none;">
                                <p style="color: var(--text-secondary); font-size: 14px; margin: var(--space-md) 0;">
                                    You'll be redirected to PayPal to complete your payment securely.
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Checkout Button -->
                <button id="checkout-btn" class="btn btn-primary btn-lg" style="width: 100%; margin-top: var(--space-lg);" disabled>
                    <span id="checkout-btn-text">🔒 Complete Order - EC$<?= number_format($total, 2) ?></span>
                    <div id="checkout-spinner" style="display: none;" class="spinner"></div>
                </button>
                
                <!-- Security Notice -->
                <div style="display: flex; align-items: center; gap: var(--space-sm); margin-top: var(--space-md); padding: var(--space-md); background: var(--bg-primary); border-radius: var(--radius-md);">
                    <span style="font-size: 16px;">🔒</span>
                    <div>
                        <p style="margin: 0; font-size: 12px; color: var(--text-secondary);">
                            <strong>Secure Payment</strong><br>
                            Your payment information is encrypted and secure. We never store your card details.
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

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