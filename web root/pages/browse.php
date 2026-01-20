<?php
// Get search parameters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sortBy = $_GET['sort'] ?? 'newest';

// Build query
$query = "SELECT l.*, u.name as seller_name, u.farmer_verified FROM listings l JOIN users u ON l.user_id = u.id WHERE l.quantity > 0 AND l.is_active = 1";
$params = [];

if(!empty($search)) {
    $query .= " AND (l.product_name LIKE ? OR l.description LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if(!empty($category)) {
    $query .= " AND l.category = ?";
    $params[] = $category;
}

// Sort
switch($sortBy) {
    case 'price_low':
        $query .= " ORDER BY l.price ASC";
        break;
    case 'price_high':
        $query .= " ORDER BY l.price DESC";
        break;
    case 'newest':
    default:
        $query .= " ORDER BY l.created_at DESC";
        break;
}

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
    $error = 'Error loading products';
}

// Display message if set
$msg = getAndClearMessage();
if($msg['message']): ?>
    <div class="alert alert-<?= $msg['type'] ?>">
        <?= ($msg['type'] === 'success' ? '✓' : '⚠') ?> <?= htmlspecialchars($msg['message']) ?>
    </div>
<?php endif; ?>

<div class="search-container">
    <form method="GET" action="index.php">
        <input type="hidden" name="page" value="browse">
        <div>
            <input type="text" name="search" placeholder="🔍 Search fresh produce..." value="<?= htmlspecialchars($search) ?>">
            <select name="category">
                <option value="">All Categories</option>
                <option value="vegetables" <?= $category === 'vegetables' ? 'selected' : '' ?>>🥬 Vegetables</option>
                <option value="fruits" <?= $category === 'fruits' ? 'selected' : '' ?>>🍎 Fruits</option>
                <option value="herbs" <?= $category === 'herbs' ? 'selected' : '' ?>>🌿 Herbs & Spices</option>
                <option value="grains" <?= $category === 'grains' ? 'selected' : '' ?>>🌾 Grains & Legumes</option>
            </select>
            <select name="sort">
                <option value="newest" <?= $sortBy === 'newest' ? 'selected' : '' ?>>⏰ Newest First</option>
                <option value="price_low" <?= $sortBy === 'price_low' ? 'selected' : '' ?>>💰 Price: Low to High</option>
                <option value="price_high" <?= $sortBy === 'price_high' ? 'selected' : '' ?>>💎 Price: High to Low</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
</div>

<?php if(isset($error)): ?>
    <div class="alert alert-error">⚠ <?= $error ?></div>
<?php endif; ?>

<?php if(empty($products)): ?>
    <div class="card">
        <div class="empty-state">
            <p>🛒</p>
            <p><strong>No products found</strong></p>
            <p style="color: var(--text-secondary);">
                <?= !empty($search) || !empty($category) ? 'Try adjusting your search filters or browse all categories.' : 'Check back soon as farmers add their fresh produce to the marketplace.' ?>
            </p>
            <?php if(!empty($search) || !empty($category)): ?>
                <a href="index.php?page=browse" class="btn btn-secondary mt-md">View All Products</a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="product-grid">
        <?php foreach($products as $product): ?>
            <div class="product-card">
                <div class="product-image">
                    <?php if (!empty($product['thumbnail_path']) && file_exists($product['thumbnail_path'])): ?>
                        <img src="<?= htmlspecialchars($product['thumbnail_path']) ?>" 
                             alt="<?= htmlspecialchars($product['product_name']) ?>"
                             loading="lazy">
                    <?php else: ?>
                        <div style="font-size: 48px; color: rgba(255,255,255,0.8);">
                            <?php
                            // Emoji based on category
                            $emojis = [
                                'vegetables' => '🥬',
                                'fruits' => '🍎',
                                'herbs' => '🌿',
                                'grains' => '🌾'
                            ];
                            echo $emojis[$product['category']] ?? '🌾';
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="product-info">
                    <div class="product-header">
                        <div>
                            <div class="product-name"><?= htmlspecialchars($product['product_name']) ?></div>
                            <div class="product-category"><?= ucfirst($product['category']) ?></div>
                        </div>
                        <div class="product-price">EC$<?= number_format($product['price'], 2) ?></div>
                    </div>
                    
                    <div class="product-seller">
                        <span>👤 <?= htmlspecialchars($product['seller_name']) ?></span>
                        <?php if($product['farmer_verified']): ?>
                            <span class="verified-badge">✅ Verified</span>
                        <?php endif; ?>
                    </div>
                    
                    <p style="color: var(--text-secondary); margin-bottom: var(--space-md); line-height: 1.4;">
                        <?= htmlspecialchars(substr($product['description'], 0, 100)) ?><?= strlen($product['description']) > 100 ? '...' : '' ?>
                    </p>
                    
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--space-md); padding: var(--space-sm); background: var(--bg-tertiary); border-radius: var(--radius-md);">
                        <span style="font-size: 14px; color: var(--text-secondary);">
                            <strong style="color: var(--success);"><?= intval($product['quantity']) ?></strong> <?= htmlspecialchars($product['unit']) ?> available
                        </span>
                        <span style="font-size: 12px; color: var(--text-muted);">
                            per <?= htmlspecialchars($product['unit']) ?>
                        </span>
                    </div>
                    
                    <?php if($_SESSION['isLoggedIn']): ?>
                        <form method="POST" action="actions.php" class="product-actions">
                            <input type="hidden" name="listingId" value="<?= $product['id'] ?>">
                            <input type="hidden" name="addToCart" value="1">
                            <input type="number" name="cartQuantity" value="1" min="1" max="<?= intval($product['quantity']) ?>" 
                                   style="width: 80px; padding: var(--space-sm); border: 1px solid var(--border-light); border-radius: var(--radius-md); text-align: center; font-size: 14px;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">
                                🛒 Add to Cart
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="product-actions">
                            <a href="index.php?page=login" class="btn btn-primary" style="width: 100%;">
                                🔐 Login to Purchase
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <?php if(count($products) > 0): ?>
        <div class="card mt-lg">
            <div class="card-body text-center">
                <p style="color: var(--text-secondary); margin-bottom: var(--space-md);">
                    Showing <?= count($products) ?> product<?= count($products) !== 1 ? 's' : '' ?>
                    <?= !empty($search) ? ' for "' . htmlspecialchars($search) . '"' : '' ?>
                    <?= !empty($category) ? ' in ' . ucfirst($category) : '' ?>
                </p>
                <?php if(!empty($search) || !empty($category)): ?>
                    <a href="index.php?page=browse" class="btn btn-secondary">
                        🔄 Clear Filters
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>