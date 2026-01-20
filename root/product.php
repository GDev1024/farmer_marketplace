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
    redirect('index.php');
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
    <title><?= htmlspecialchars($product['name']) ?> - <?= Config::SITE_NAME ?></title>
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/marketplace.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">🌾 <?= Config::SITE_NAME ?></div>
            <div class="nav-links">
                <a href="index.php">Browse</a>
                <?php if ($user): ?>
                    <?php if ($user['user_type'] === 'farmer'): ?>
                        <a href="dashboard.php">My Products</a>
                    <?php else: ?>
                        <a href="cart.php">Cart</a>
                    <?php endif; ?>
                    <a href="api/auth.php?action=logout" class="btn btn-secondary btn-sm">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary btn-sm">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <div class="container" style="margin-top: 3rem;">
        <div class="grid grid-2">
            <div>
                <img src="<?= $product['image_url'] ?: 'https://via.placeholder.com/600x450?text=No+Image' ?>" 
                     alt="<?= htmlspecialchars($product['name']) ?>" 
                     style="width: 100%; border-radius: var(--radius-md);">
            </div>
            
            <div>
                <div class="badge badge-primary" style="margin-bottom: 1rem;">
                    <?= ucfirst($product['category']) ?>
                </div>
                
                <h1 style="font-size: var(--font-size-3xl); color: var(--primary-green); margin-bottom: 1rem;">
                    <?= htmlspecialchars($product['name']) ?>
                </h1>
                
                <div style="margin-bottom: 1rem;">
                    <span style="font-size: var(--font-size-sm); color: var(--gray-600);">
                        Sold by <?= htmlspecialchars($product['farmer_name']) ?>
                        <?php if ($product['verified']): ?>
                            <span class="badge badge-success" style="font-size: 0.7rem;">✓ Verified</span>
                        <?php endif; ?>
                    </span>
                </div>
                
                <?php if (!empty($reviews)): ?>
                    <div style="margin-bottom: 1rem;">
                        <span style="color: #ffc107;">★</span>
                        <strong><?= number_format($avgRating, 1) ?></strong>
                        <span style="color: var(--gray-600);">(<?= count($reviews) ?> reviews)</span>
                    </div>
                <?php endif; ?>
                
                <div style="font-size: var(--font-size-3xl); font-weight: 700; color: var(--primary-green); margin: 1.5rem 0;">
                    $<?= number_format($product['price'], 2) ?> / <?= $product['unit'] ?>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <?php if ($product['quantity'] > 0): ?>
                        <span class="badge badge-success">In Stock: <?= $product['quantity'] ?> <?= $product['unit'] ?> available</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Out of Stock</span>
                    <?php endif; ?>
                </div>
                
                <p style="color: var(--gray-600); line-height: 1.8; margin-bottom: 2rem;">
                    <?= nl2br(htmlspecialchars($product['description'])) ?>
                </p>
                
                <?php if ($user && $user['user_type'] === 'consumer' && $product['quantity'] > 0): ?>
                    <form method="POST" action="api/cart.php" style="display: flex; gap: 1rem; align-items: center;">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="listing_id" value="<?= $product['id'] ?>">
                        <input type="number" name="quantity" value="1" min="1" max="<?= $product['quantity'] ?>" 
                               class="form-input" style="width: 100px;">
                        <button type="submit" class="btn btn-primary">Add to Cart</button>
                    </form>
                <?php elseif (!$user): ?>
                    <a href="login.php" class="btn btn-primary">Login to Purchase</a>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (!empty($reviews)): ?>
            <div style="margin-top: 4rem;">
                <h2 style="color: var(--primary-green); margin-bottom: 2rem;">Customer Reviews</h2>
                <div class="grid">
                    <?php foreach ($reviews as $review): ?>
                        <div class="card">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <strong><?= htmlspecialchars($review['username']) ?></strong>
                                <span style="color: #ffc107;">
                                    <?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?>
                                </span>
                            </div>
                            <p style="color: var(--gray-600);"><?= htmlspecialchars($review['comment']) ?></p>
                            <small style="color: var(--gray-600);">
                                <?= date('M j, Y', strtotime($review['created_at'])) ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>