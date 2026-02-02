<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireLogin();
$user = getCurrentUser();

if ($user['user_type'] !== 'customer') {
    redirect('dashboard.php');
}

include 'includes/header.php';
include 'includes/navigation.php';
?>

<main class="main-content">
    <div class="container">
        <h1>Shopping Cart</h1>
        
        <div class="cart-items">
            <p>Your cart is empty.</p>
        </div>
        
        <div class="cart-actions">
            <a href="index.php?page=browse" class="btn btn-secondary">Continue Shopping</a>
            <a href="checkout.php" class="btn btn-primary">Checkout</a>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>