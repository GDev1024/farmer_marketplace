<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireLogin();
$user = getCurrentUser();

if ($user['user_type'] !== 'consumer') {
    redirect('index.php');
}

$db = Config::getDB();

// Get cart items
$stmt = $db->prepare("
    SELECT c.*, l.name, l.price, l.unit, l.quantity as stock
    FROM cart c
    JOIN listings l ON c.listing_id = l.id
    WHERE c.user_id = ?
");
$stmt->execute([$user['id']]);
$cartItems = $stmt->fetchAll();

if (empty($cartItems)) {
    redirect('cart.php');
}

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

$totalCents = $total * 100; // Stripe uses cents
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?= Config::getSiteName() ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/marketplace.css">
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://www.paypal.com/sdk/js?client-id=<?= Config::getPaypalClientId() ?>"></script>
</head>
<body class="app-page">
    <header>
        <nav>
            <a href="dashboard.php" class="logo">
                <span class="logo-icon">🌾</span>
                <span><?= Config::getSiteName() ?></span>
            </a>
            <div class="nav-links">
                <a href="cart.php">← Back to Cart</a>
                <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</button>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>Checkout</h1>
                <p>Complete your order</p>
            </div>
        
        <div class="grid grid-2" style="align-items: start;">
            <!-- Order Details -->
            <div>
                <div class="card" style="margin-bottom: 1.5rem;">
                    <h2 style="margin-bottom: 1rem;">Order Details</h2>
                    <?php foreach ($cartItems as $item): ?>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid var(--gray-200);">
                            <div>
                                <strong><?= htmlspecialchars($item['name']) ?></strong>
                                <span style="color: var(--gray-600);"> x <?= $item['quantity'] ?></span>
                            </div>
                            <strong>$<?= number_format($item['price'] * $item['quantity'], 2) ?></strong>
                        </div>
                    <?php endforeach; ?>
                    <div style="display: flex; justify-content: space-between; padding-top: 1rem; font-size: var(--font-size-xl);">
                        <strong>Total:</strong>
                        <strong style="color: var(--primary-green);">$<?= number_format($total, 2) ?></strong>
                    </div>
                </div>
                
                <div class="card">
                    <h2 style="margin-bottom: 1rem;">Delivery Address</h2>
                    <form id="addressForm">
                        <div class="form-group">
                            <label class="form-label">Street Address</label>
                            <input type="text" id="address" class="form-input" required>
                        </div>
                        <div class="grid grid-2">
                            <div class="form-group">
                                <label class="form-label">City</label>
                                <input type="text" id="city" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Parish</label>
                                <select id="parish" class="form-input" required>
                                    <option value="">Select Parish</option>
                                    <option value="St. George">St. George</option>
                                    <option value="St. Andrew">St. Andrew</option>
                                    <option value="St. David">St. David</option>
                                    <option value="St. John">St. John</option>
                                    <option value="St. Mark">St. Mark</option>
                                    <option value="St. Patrick">St. Patrick</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Payment Methods -->
            <div class="card">
                <h2 style="margin-bottom: 1.5rem;">Payment Method</h2>
                
                <div style="margin-bottom: 2rem;">
                    <button onclick="showPaymentMethod('stripe')" class="btn btn-primary btn-block" style="margin-bottom: 1rem;">
                        💳 Pay with Card (Stripe)
                    </button>
                    <button onclick="showPaymentMethod('paypal')" class="btn btn-secondary btn-block">
                        Pay with PayPal
                    </button>
                </div>
                
                <!-- Stripe Payment Form -->
                <div id="stripePayment" style="display: none;">
                    <h3 style="margin-bottom: 1rem;">Card Payment</h3>
                    <form id="payment-form">
                        <div id="card-element" style="padding: 1rem; border: 1px solid var(--gray-200); border-radius: var(--radius-sm); margin-bottom: 1rem;"></div>
                        <div id="card-errors" class="form-error"></div>
                        <button type="submit" class="btn btn-primary btn-block" id="submit-button">
                            Pay $<?= number_format($total, 2) ?>
                        </button>
                    </form>
                </div>
                
                <!-- PayPal Payment -->
                <div id="paypalPayment" style="display: none;">
                    <h3 style="margin-bottom: 1rem;">PayPal Payment</h3>
                    <div id="paypal-button-container"></div>
                </div>
                
                <div id="processing" style="display: none; text-align: center; padding: 2rem;">
                    <div class="spinner"></div>
                    <p style="color: var(--gray-600); margin-top: 1rem;">Processing payment...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Validate address before payment
        function validateAddress() {
            const address = document.getElementById('address').value;
            const city = document.getElementById('city').value;
            const parish = document.getElementById('parish').value;
            
            if (!address || !city || !parish) {
                alert('Please fill in your delivery address');
                return false;
            }
            
            return { address, city, parish };
        }
        
        // Show selected payment method
        function showPaymentMethod(method) {
            document.getElementById('stripePayment').style.display = 'none';
            document.getElementById('paypalPayment').style.display = 'none';
            
            if (method === 'stripe') {
                document.getElementById('stripePayment').style.display = 'block';
                initStripe();
            } else {
                document.getElementById('paypalPayment').style.display = 'block';
                initPayPal();
            }
        }
        
        // Stripe Integration
        let stripe = null;
        let elements = null;
        let cardElement = null;
        
        function initStripe() {
            if (!stripe) {
                stripe = Stripe('<?= Config::getStripePublicKey() ?>');
                elements = stripe.elements();
                cardElement = elements.create('card', {
                    style: {
                        base: {
                            fontSize: '16px',
                            color: '#333',
                            '::placeholder': {
                                color: '#aab7c4',
                            },
                        },
                    },
                });
                cardElement.mount('#card-element');
                
                cardElement.on('change', function(event) {
                    const displayError = document.getElementById('card-errors');
                    if (event.error) {
                        displayError.textContent = event.error.message;
                    } else {
                        displayError.textContent = '';
                    }
                });
                
                document.getElementById('payment-form').addEventListener('submit', handleStripePayment);
            }
        }
        
        async function handleStripePayment(event) {
            event.preventDefault();
            
            const addressData = validateAddress();
            if (!addressData) return;
            
            document.getElementById('submit-button').disabled = true;
            document.getElementById('processing').style.display = 'block';
            
            // Create payment intent
            const response = await fetch('api/payment.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    action: 'create_stripe_intent',
                    amount: <?= $totalCents ?>,
                    address: `${addressData.address}, ${addressData.city}, ${addressData.parish}`
                })
            });
            
            const {clientSecret} = await response.json();
            
            // Confirm payment
            const {error, paymentIntent} = await stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: cardElement,
                }
            });
            
            if (error) {
                document.getElementById('card-errors').textContent = error.message;
                document.getElementById('submit-button').disabled = false;
                document.getElementById('processing').style.display = 'none';
            } else if (paymentIntent.status === 'succeeded') {
                completeOrder('stripe', paymentIntent.id, addressData);
            }
        }
        
        // PayPal Integration
        let paypalInitialized = false;
        
        function initPayPal() {
            if (!paypalInitialized) {
                paypal.Buttons({
                    createOrder: function(data, actions) {
                        const addressData = validateAddress();
                        if (!addressData) {
                            return Promise.reject('Please fill in delivery address');
                        }
                        
                        return actions.order.create({
                            purchase_units: [{
                                amount: {
                                    value: '<?= number_format($total, 2, '.', '') ?>'
                                }
                            }]
                        });
                    },
                    onApprove: function(data, actions) {
                        document.getElementById('processing').style.display = 'block';
                        
                        return actions.order.capture().then(function(details) {
                            const addressData = validateAddress();
                            completeOrder('paypal', details.id, addressData);
                        });
                    },
                    onError: function(err) {
                        alert('PayPal payment failed. Please try again.');
                        console.error(err);
                    }
                }).render('#paypal-button-container');
                
                paypalInitialized = true;
            }
        }
        
        // Complete order with atomic transaction
        async function completeOrder(method, paymentId, addressData) {
            try {
                const response = await fetch('api/payment.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        action: 'complete_order',
                        payment_method: method,
                        payment_intent_id: paymentId,
                        delivery_address: `${addressData.address}, ${addressData.city}, ${addressData.parish}`
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = 'order-success.php?order_id=' + data.order_id;
                } else {
                    alert('Order processing failed: ' + data.message);
                    location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please contact support.');
            }
        }
    </script>

    <footer class="app-footer">
        <div class="footer-content">
            <div class="footer-brand">
                <span class="logo-icon">🌾</span>
                <span><?= Config::getSiteName() ?></span>
            </div>
            <p class="footer-tagline">Supporting local agriculture in Grenada</p>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            document.querySelector('.nav-links').classList.toggle('active');
        }
    </script>
</body>
</html>

<?php