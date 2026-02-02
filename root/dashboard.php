<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireLogin();
$user = getCurrentUser();

include 'includes/header.php';
include 'includes/navigation.php';
?>

<main class="main-content">
    <div class="container">
        <h1>Dashboard</h1>
        <p>Welcome, <?= htmlspecialchars($user['username']) ?>!</p>
        
        <?php if ($user['user_type'] === 'farmer'): ?>
            <div class="farmer-dashboard">
                <h2>Farmer Dashboard</h2>
                <a href="index.php?page=sell" class="btn btn-primary">Manage Products</a>
            </div>
        <?php else: ?>
            <div class="customer-dashboard">
                <h2>Customer Dashboard</h2>
                <a href="index.php?page=browse" class="btn btn-primary">Browse Products</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>