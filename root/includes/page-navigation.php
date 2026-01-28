<?php
/**
 * Page Navigation Component
 * Provides consistent navigation across all pages
 */
?>

<nav class="page-navigation" style="margin-bottom: var(--space-8); padding: var(--space-4); background: var(--bg-primary); border-radius: var(--radius-lg); border: 1px solid var(--border-primary); box-shadow: var(--shadow-sm);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--space-4);">
        <div style="display: flex; gap: var(--space-4); flex-wrap: wrap;">
            <a href="index.php" class="btn btn-secondary btn-sm">← Home</a>
            <?php if($isLoggedIn): ?>
                <a href="index.php?page=home" class="btn btn-secondary btn-sm">Dashboard</a>
                <a href="index.php?page=browse" class="btn btn-secondary btn-sm">Browse</a>
                <a href="index.php?page=cart" class="btn btn-secondary btn-sm">🛒 Cart</a>
                <a href="index.php?page=sell" class="btn btn-secondary btn-sm">My Listings</a>
                <a href="index.php?page=orders" class="btn btn-secondary btn-sm">Orders</a>
                <a href="index.php?page=messages" class="btn btn-secondary btn-sm">Messages</a>
            <?php else: ?>
                <a href="index.php?page=browse" class="btn btn-secondary btn-sm">Browse Products</a>
                <a href="index.php?page=login" class="btn btn-primary btn-sm">Login</a>
                <a href="index.php?page=register" class="btn btn-secondary btn-sm">Sign Up</a>
            <?php endif; ?>
        </div>
        <?php if($isLoggedIn): ?>
            <div style="display: flex; align-items: center; gap: var(--space-3);">
                <span style="font-size: var(--text-sm); color: var(--text-secondary);">Welcome, <?= htmlspecialchars($name) ?></span>
                <a href="index.php?page=profile" class="btn btn-secondary btn-sm">Profile</a>
                <a href="index.php?page=logout" class="btn btn-danger btn-sm">Logout</a>
            </div>
        <?php endif; ?>
    </div>
</nav>

<style>
/* Responsive navigation */
@media (max-width: 639px) {
    .page-navigation > div {
        flex-direction: column;
        align-items: stretch;
        gap: var(--space-3);
    }
    
    .page-navigation > div > div {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .page-navigation .btn {
        flex: 1;
        min-width: 80px;
        justify-content: center;
    }
}
</style>