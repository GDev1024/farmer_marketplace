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

<div class="card">
    <h2>🛒 Browse Fresh Produce</h2>
    <form method="GET" action="index.php" class="search-container">
        <input type="hidden" name="page" value="browse">
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem;">
            <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
            <select name="category">
                <option value="">All Categories</option>
                <option value="vegetables" <?= $category === 'vegetables' ? 'selected' : '' ?>>Vegetables</option>
                <option value="fruits" <?= $category === 'fruits' ? 'selected' : '' ?>>Fruits</option>
                <option value="herbs" <?= $category === 'herbs' ? 'selected' : '' ?>>Herbs & Spices</option>
                <option value="grains" <?= $category === 'grains' ? 'selected' : '' ?>>Grains & Legumes</option>
            </select>
            <select name="sort">
                <option value="newest" <?= $sortBy === 'newest' ? 'selected' : '' ?>>Newest</option>
                <option value="price_low" <?= $sortBy === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                <option value="price_high" <?= $sortBy === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Search</button>
    </form>
</div>

<?php if(isset($error)): ?>
    <div class="alert alert-error">⚠ <?= $error ?></div>
<?php endif; ?>

<?php if(empty($products)): ?>
    <div class="card">
        <div class="empty-state">
            <p>📭 No products found</p>
            <p style="font-size:0.95rem; color:#666;">
                <?= !empty($search) || !empty($category) ? 'Try adjusting your search filters.' : 'Check back soon as farmers add their fresh produce.' ?>
            </p>
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
                             style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
                    <?php else: ?>
                        <div style="width: 100%; height: 200px; display: flex; align-items: center; justify-content: center; background: #f5f5f5; border-radius: 8px; font-size: 3rem;">
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
                    <div class="product-name"><?= htmlspecialchars($product['product_name']) ?></div>
                    <div class="product-price">EC$<?= number_format($product['price'], 2) ?></div>
                    <span class="product-category"><?= ucfirst($product['category']) ?></span>
                    
                    <div class="product-seller">
                        <?= htmlspecialchars($product['seller_name']) ?>
                        <?php if($product['farmer_verified']): ?>
                            <span class="verified-badge">✅ Verified</span>
                        <?php endif; ?>
                    </div>
                    
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 1rem;">
                        <?= htmlspecialchars(substr($product['description'], 0, 80)) ?>...
                    </p>
                    
                    <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 1rem;">
                        <span style="font-size: 0.9rem; color: #666;">
                            <strong><?= intval($product['quantity']) ?></strong> <?= htmlspecialchars($product['unit']) ?> available
                        </span>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                        <?php if($_SESSION['isLoggedIn']): ?>
                            <form method="POST" action="actions.php" style="display: contents;">
                                <input type="hidden" name="listingId" value="<?= $product['id'] ?>">
                                <input type="hidden" name="addToCart" value="1">
                                <input type="number" name="cartQuantity" value="1" min="1" max="<?= intval($product['quantity']) ?>" style="padding: 0.5rem; border: 1px solid #D2DCB6; border-radius: 5px;">
                                <button type="submit" class="btn btn-primary">🛒 Add</button>
                            </form>
                        <?php else: ?>
                            <a href="index.php?page=login" class="btn btn-primary" style="grid-column: 1 / -1;">Login to Buy</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<style>
    .search-container {
        margin-bottom: 2rem;
    }
    
    @media (max-width: 768px) {
        .search-container > div {
            grid-template-columns: 1fr;
        }
    }
</style>