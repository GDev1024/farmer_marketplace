<?php
// ==================== index.php ====================
require_once 'includes/config.php';
require_once 'includes/functions.php';

$user = getCurrentUser();
$db = Config::getDB();

// Fetch products
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';

$sql = "SELECT l.*, u.username as farmer_name 
        FROM listings l 
        JOIN users u ON l.farmer_id = u.id 
        WHERE l.status = 'active'";

$params = [];
if ($search) {
    $sql .= " AND (l.name LIKE ? OR l.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($category) {
    $sql .= " AND l.category = ?";
    $params[] = $category;
}

$sql .= " ORDER BY l.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Config::SITE_NAME ?> - Fresh Local Produce</title>
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
                        <a href="orders.php">Orders</a>
                    <?php endif; ?>
                    <a href="api/auth.php?action=logout" class="btn btn-secondary btn-sm">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-secondary btn-sm">Login</a>
                    <a href="register.php" class="btn btn-primary btn-sm">Sign Up</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <div class="hero">
        <h1>Fresh From Grenada's Farms</h1>
        <p>Connect directly with local farmers for the freshest produce</p>
    </div>

    <div class="search-section">
        <form class="search-container" method="GET" action="">
            <input type="text" name="search" class="search-input form-input" 
                   placeholder="Search for produce..." value="<?= htmlspecialchars($search) ?>">
            <select name="category" class="form-input">
                <option value="">All Categories</option>
                <option value="vegetables" <?= $category === 'vegetables' ? 'selected' : '' ?>>Vegetables</option>
                <option value="fruits" <?= $category === 'fruits' ? 'selected' : '' ?>>Fruits</option>
                <option value="herbs" <?= $category === 'herbs' ? 'selected' : '' ?>>Herbs</option>
                <option value="other" <?= $category === 'other' ? 'selected' : '' ?>>Other</option>
            </select>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>

    <div class="container">
        <h2 class="section-title">Available Products</h2>
        
        <?php if (empty($products)): ?>
            <div class="card" style="text-align: center; padding: 3rem;">
                <p style="font-size: 1.2rem; color: var(--gray-600);">No products found. Try adjusting your search.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-auto">
                <?php foreach ($products as $product): ?>
                    <div class="product-card" onclick="window.location.href='product.php?id=<?= $product['id'] ?>'">
                        <img src="<?= $product['image_url'] ?: 'https://via.placeholder.com/400x300?text=No+Image' ?>" 
                             alt="<?= htmlspecialchars($product['name']) ?>" 
                             class="product-image">
                        <div class="product-info">
                            <div class="product-category"><?= ucfirst($product['category']) ?></div>
                            <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                            <div class="product-price">$<?= number_format($product['price'], 2) ?> / <?= $product['unit'] ?></div>
                            <div class="product-farmer">By <?= htmlspecialchars($product['farmer_name']) ?></div>
                            <div style="margin-top: 1rem;">
                                <span class="badge badge-success"><?= $product['quantity'] ?> <?= $product['unit'] ?> available</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Dynamic search (optional enhancement)
        const searchForm = document.querySelector('.search-container');
        let searchTimeout;
        
        searchForm.querySelector('[name="search"]').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchForm.submit();
            }, 500);
        });
    </script>
</body>
</html>

<?php