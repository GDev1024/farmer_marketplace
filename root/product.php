<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$user = getCurrentUser();
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$db = Config::getDB();
$stmt = $db->prepare("
    SELECT l.*, u.username as farmer_name, u.verified 
    FROM listings l 
    JOIN users u ON l.farmer_id = u.id 
    WHERE l.id = ?
");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    redirect('dashboard.php');
}

// Get reviews
$stmt = $db->prepare("
    SELECT r.*, u.username 
    FROM reviews r 
    JOIN users u ON r.customer_id = u.id 
    WHERE r.listing_id = ? 
    ORDER BY r.created_at DESC
");
$stmt->execute([$productId]);
$reviews = $stmt->fetchAll();

$avgRating = 0;
if (!empty($reviews)) {
    $avgRating = array_sum(array_column($reviews, 'rating')) / count($reviews);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - <?= Config::getSiteName() ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/marketplace.css">
</head>
<body class="app-page">
    <header>
        <nav>
            <a href="dashboard.php" class="logo">
                <span class="logo-icon">🌾</span>
                <span><?= Config::getSiteName() ?></span>
            </a>
            <div class="nav-links">
                <a href="dashboard.php">Browse</a>
                <?php if ($user): ?>
                    <?php if ($user['user_type'] === 'farmer'): ?>
                        <a href="dashboard.php">My Products</a>
                    <?php else: ?>
                        <a href="cart.php">Cart</a>
                        <a href="orders.php">My Orders</a>
                    <?php endif; ?>
                    <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</button>
                    <a href="api/auth.php?action=logout" class="btn btn-secondary btn-sm">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary btn-sm">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="product-detail-layout">
                <div class="product-image-section">
                    <img src="<?= $product['image_url'] ?: 'https://via.placeholder.com/600x450/f5f3f0/666?text=No+Image' ?>" 
                         alt="<?= htmlspecialchars($product['name']) ?>" 
                         class="product-detail-image">
                </div>
                
                <div class="product-info-section">
                    <div class="product-category"><?= ucfirst($product['category']) ?></div>
                    
                    <h1 class="product-detail-title"><?= htmlspecialchars($product['name']) ?></h1>
                    
                    <div class="farmer-info">
                        <span class="farmer-label">Sold by <?= htmlspecialchars($product['farmer_name']) ?></span>
                        <?php if ($product['verified']): ?>
                            <span class="verified-badge">✓ Verified Farmer</span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($reviews)): ?>
                        <div class="rating-section">
                            <span class="rating-stars">★</span>
                            <strong class="rating-value"><?= number_format($avgRating, 1) ?></strong>
                            <span class="rating-count">(<?= count($reviews) ?> reviews)</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="product-price">
                        $<?= number_format($product['price'], 2) ?> / <?= $product['unit'] ?>
                    </div>
                    
                    <div class="stock-status">
                        <?php if ($product['quantity'] > 0): ?>
                            <span class="availability-badge in-stock">In Stock: <?= $product['quantity'] ?> <?= $product['unit'] ?> available</span>
                        <?php else: ?>
                            <span class="availability-badge out-of-stock">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-description">
                        <?= nl2br(htmlspecialchars($product['description'])) ?>
                    </div>
                    
                    <?php if ($user && $user['user_type'] === 'customer' && $product['quantity'] > 0): ?>
                        <form method="POST" action="api/cart.php" class="add-to-cart-form">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="listing_id" value="<?= $product['id'] ?>">
                            <div class="quantity-selector">
                                <label class="quantity-label">Quantity:</label>
                                <input type="number" name="quantity" value="1" min="1" max="<?= $product['quantity'] ?>" 
                                       class="quantity-input">
                                <span class="unit-label"><?= $product['unit'] ?></span>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg">Add to Cart</button>
                        </form>
                    <?php elseif (!$user): ?>
                        <div class="login-prompt">
                            <a href="login.php" class="btn btn-primary btn-lg">Login to Purchase</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (!empty($reviews)): ?>
                <section class="reviews-section">
                    <h2 class="section-title">Customer Reviews</h2>
                    <div class="reviews-grid">
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-card">
                                <div class="review-header">
                                    <strong class="reviewer-name"><?= htmlspecialchars($review['username']) ?></strong>
                                    <span class="review-rating">
                                        <?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?>
                                    </span>
                                </div>
                                <p class="review-comment"><?= htmlspecialchars($review['comment']) ?></p>
                                <small class="review-date">
                                    <?= date('M j, Y', strtotime($review['created_at'])) ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </main>

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