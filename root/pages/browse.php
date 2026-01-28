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

<main class="page-main browse-page" id="main-content" role="main">
    <header class="page-header">
        <h1 class="page-title">Browse Fresh Produce</h1>
        <p class="page-subtitle">Discover locally grown, fresh produce from verified farmers in Grenada</p>
    </header>

    <?php include 'includes/page-navigation.php'; ?>

    <section class="search-section" aria-labelledby="search-title">
        <h2 id="search-title" class="sr-only">Search and Filter Products</h2>
        <form method="GET" action="index.php" class="search-form" role="search" aria-label="Product search and filters">
            <input type="hidden" name="page" value="browse">
            <div class="search-controls">
                <div class="form-group">
                    <label for="search-input" class="sr-only">Search products</label>
                    <input type="text" 
                           id="search-input"
                           name="search" 
                           class="search-input"
                           placeholder="🔍 Search fresh produce..." 
                           value="<?= htmlspecialchars($search) ?>"
                           aria-describedby="search-help">
                    <small id="search-help" class="form-help">Search by product name or description</small>
                </div>
                
                <div class="form-group">
                    <label for="category-select" class="sr-only">Filter by category</label>
                    <select id="category-select" name="category" class="category-select" aria-describedby="category-help">
                        <option value="">All Categories</option>
                        <option value="vegetables" <?= $category === 'vegetables' ? 'selected' : '' ?>>🥬 Vegetables</option>
                        <option value="fruits" <?= $category === 'fruits' ? 'selected' : '' ?>>🍎 Fruits</option>
                        <option value="herbs" <?= $category === 'herbs' ? 'selected' : '' ?>>🌿 Herbs & Spices</option>
                        <option value="grains" <?= $category === 'grains' ? 'selected' : '' ?>>🌾 Grains & Legumes</option>
                    </select>
                    <small id="category-help" class="form-help">Filter products by category</small>
                </div>
                
                <div class="form-group">
                    <label for="sort-select" class="sr-only">Sort products</label>
                    <select id="sort-select" name="sort" class="sort-select" aria-describedby="sort-help">
                        <option value="newest" <?= $sortBy === 'newest' ? 'selected' : '' ?>>⏰ Newest First</option>
                        <option value="price_low" <?= $sortBy === 'price_low' ? 'selected' : '' ?>>💰 Price: Low to High</option>
                        <option value="price_high" <?= $sortBy === 'price_high' ? 'selected' : '' ?>>💎 Price: High to Low</option>
                    </select>
                    <small id="sort-help" class="form-help">Sort products by date or price</small>
                </div>
            </div>
            <button type="submit" class="btn btn-primary search-button" aria-label="Apply search filters">
                <span class="btn-icon" aria-hidden="true">🔍</span>
                Search
            </button>
        </form>
    </section>

    <?php if(isset($error)): ?>
        <div class="alert alert-error">⚠ <?= $error ?></div>
    <?php endif; ?>

    <section class="products-section" aria-labelledby="products-title">
        <h2 id="products-title" class="sr-only">Product Results</h2>
        
        <?php if(empty($products)): ?>
            <div class="card">
                <div class="empty-state">
                    <div class="empty-icon" aria-hidden="true">🛒</div>
                    <h3 class="empty-title">No products found</h3>
                    <p class="empty-description">
                        <?= !empty($search) || !empty($category) ? 'Try adjusting your search filters or browse all categories.' : 'Check back soon as farmers add their fresh produce to the marketplace.' ?>
                    </p>
                    <?php if(!empty($search) || !empty($category)): ?>
                        <a href="index.php?page=browse" class="btn btn-secondary" aria-label="Clear all filters and view all products">
                            <span class="btn-icon" aria-hidden="true">🔄</span>
                            View All Products
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="products-header">
                <p class="products-count" aria-live="polite">
                    Showing <strong><?= count($products) ?></strong> product<?= count($products) !== 1 ? 's' : '' ?>
                    <?= !empty($search) ? ' for "' . htmlspecialchars($search) . '"' : '' ?>
                    <?= !empty($category) ? ' in ' . ucfirst($category) : '' ?>
                </p>
            </div>
            
            <div class="product-grid" role="grid" aria-label="Product listings">
                <?php foreach($products as $product): ?>
                    <article class="product-card" role="gridcell" aria-labelledby="product-<?= $product['id'] ?>-title">
                        <div class="product-card-image">
                            <?php if (!empty($product['thumbnail_path']) && file_exists($product['thumbnail_path'])): ?>
                                <img src="<?= htmlspecialchars($product['thumbnail_path']) ?>" 
                                     alt="<?= htmlspecialchars($product['product_name']) ?>"
                                     loading="lazy"
                                     class="product-image">
                            <?php else: ?>
                                <div class="product-placeholder" aria-label="Product image placeholder">
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
                        
                        <div class="product-card-content">
                            <header class="product-card-header">
                                <div class="product-info">
                                    <h3 id="product-<?= $product['id'] ?>-title" class="product-card-title">
                                        <?= htmlspecialchars($product['product_name']) ?>
                                    </h3>
                                    <span class="product-card-category" aria-label="Category: <?= ucfirst($product['category']) ?>">
                                        <?= ucfirst($product['category']) ?>
                                    </span>
                                </div>
                                <div class="product-card-price" aria-label="Price: EC$<?= number_format($product['price'], 2) ?>">
                                    EC$<?= number_format($product['price'], 2) ?>
                                </div>
                            </header>
                            
                            <p class="product-card-farmer">
                                by <strong><?= htmlspecialchars($product['seller_name']) ?></strong>
                                <?php if($product['farmer_verified']): ?>
                                    <span class="verified-badge" aria-label="Verified farmer">✅ Verified</span>
                                <?php endif; ?>
                            </p>
                            
                            <p class="product-card-description">
                                <?= htmlspecialchars(substr($product['description'], 0, 100)) ?><?= strlen($product['description']) > 100 ? '...' : '' ?>
                            </p>
                            
                            <footer class="product-card-footer">
                                <div class="product-card-stock" aria-label="Stock: <?= intval($product['quantity']) ?> <?= htmlspecialchars($product['unit']) ?> available">
                                    <strong class="stock-quantity"><?= intval($product['quantity']) ?></strong> 
                                    <span class="product-card-unit"><?= htmlspecialchars($product['unit']) ?> available</span>
                                </div>
                                
                                <?php if(isLoggedIn()): ?>
                                    <form method="POST" action="actions.php" class="product-actions" aria-label="Add <?= htmlspecialchars($product['product_name']) ?> to cart">
                                        <input type="hidden" name="listingId" value="<?= $product['id'] ?>">
                                        <input type="hidden" name="addToCart" value="1">
                                        <div class="quantity-input">
                                            <label for="quantity-<?= $product['id'] ?>" class="sr-only">
                                                Quantity for <?= htmlspecialchars($product['product_name']) ?>
                                            </label>
                                            <input type="number" 
                                                   id="quantity-<?= $product['id'] ?>"
                                                   name="cartQuantity" 
                                                   value="1" 
                                                   min="1" 
                                                   max="<?= intval($product['quantity']) ?>" 
                                                   class="quantity-input-field"
                                                   aria-describedby="quantity-help-<?= $product['id'] ?>">
                                            <small id="quantity-help-<?= $product['id'] ?>" class="sr-only">
                                                Maximum <?= intval($product['quantity']) ?> <?= htmlspecialchars($product['unit']) ?>
                                            </small>
                                        </div>
                                        <button type="submit" class="btn btn-primary add-to-cart-btn">
                                            <span class="btn-icon" aria-hidden="true">🛒</span>
                                            Add to Cart
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="product-actions">
                                        <a href="index.php?page=login" class="btn btn-primary login-to-purchase-btn">
                                            <span class="btn-icon" aria-hidden="true">🔐</span>
                                            Login to Purchase
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </footer>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            
            <?php if(!empty($search) || !empty($category)): ?>
                <div class="card results-footer">
                    <div class="card-body">
                        <div class="results-actions">
                            <a href="index.php?page=browse" class="btn btn-secondary" aria-label="Clear all filters and view all products">
                                <span class="btn-icon" aria-hidden="true">🔄</span>
                                Clear Filters
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</main>