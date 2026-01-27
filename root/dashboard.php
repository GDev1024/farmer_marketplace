<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/aws-image-handler.php';

requireLogin();
$user = getCurrentUser();

$db = Config::getDB();

// Handle different user types
if ($user['user_type'] === 'farmer') {
    // Get farmer's products
    $stmt = $db->prepare("SELECT * FROM listings WHERE farmer_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user['id']]);
    $products = $stmt->fetchAll();

    // Get stats
    $stmt = $db->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active FROM listings WHERE farmer_id = ?");
    $stmt->execute([$user['id']]);
    $stats = $stmt->fetch();
} else {
    // Customer dashboard - redirect to browse page (to be created)
    // For now, show available products
    $stmt = $db->prepare("SELECT l.*, u.username as farmer_name FROM listings l JOIN users u ON l.farmer_id = u.id WHERE l.status = 'active' ORDER BY l.created_at DESC LIMIT 12");
    $stmt->execute();
    $products = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= Config::getSiteName() ?></title>
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
            <a href="<?= $user['user_type'] === 'farmer' ? 'dashboard.php' : 'dashboard.php' ?>" class="logo">
                <span class="logo-icon">🌾</span>
                <span><?= Config::getSiteName() ?></span>
            </a>
            <div class="nav-links">
                <?php if ($user['user_type'] === 'farmer'): ?>
                    <a href="dashboard.php">My Products</a>
                    <a href="orders.php">Orders</a>
                <?php else: ?>
                    <a href="dashboard.php">Browse</a>
                    <a href="cart.php">Cart</a>
                    <a href="orders.php">My Orders</a>
                <?php endif; ?>
                <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</button>
                <a href="api/auth.php?action=logout" class="btn btn-secondary btn-sm">Logout</a>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <div class="container">
            <?php if ($user['user_type'] === 'farmer'): ?>
                <!-- Farmer Dashboard -->
                <div class="dashboard-header">
                    <div class="welcome-section">
                        <h1>Welcome back, <?= htmlspecialchars($user['username']) ?>!</h1>
                        <p>Manage your product listings and grow your business</p>
                    </div>
                    <button onclick="openAddProductModal()" class="btn btn-primary btn-lg">
                        <span>+ Add Product</span>
                    </button>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?= $stats['total'] ?></div>
                        <div class="stat-label">Total Products</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $stats['active'] ?></div>
                        <div class="stat-label">Active Listings</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">0</div>
                        <div class="stat-label">Orders This Week</div>
                    </div>
                </div>

                <section class="products-section">
                    <h2 class="section-title">My Products</h2>
                    
                    <?php if (empty($products)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">📦</div>
                            <h3>No products yet</h3>
                            <p>Start by adding your first product to the marketplace</p>
                            <button onclick="openAddProductModal()" class="btn btn-primary">Add Your First Product</button>
                        </div>
                    <?php else: ?>
                        <div class="products-grid">
                            <?php foreach ($products as $product): ?>
                                <div class="product-management-card">
                                    <div class="product-image-container">
                                        <img src="<?= $product['image_url'] ?: 'https://via.placeholder.com/300x200/f5f3f0/666?text=No+Image' ?>" 
                                             alt="<?= htmlspecialchars($product['name']) ?>"
                                             class="product-image">
                                        <div class="product-status-badge <?= $product['status'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', $product['status'])) ?>
                                        </div>
                                    </div>
                                    
                                    <div class="product-details">
                                        <div class="product-header">
                                            <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                                            <span class="product-category"><?= ucfirst($product['category']) ?></span>
                                        </div>
                                        
                                        <p class="product-description">
                                            <?= substr(htmlspecialchars($product['description']), 0, 80) ?>...
                                        </p>
                                        
                                        <div class="product-metrics">
                                            <div class="metric">
                                                <span class="metric-value">$<?= number_format($product['price'], 2) ?></span>
                                                <span class="metric-label">per <?= $product['unit'] ?></span>
                                            </div>
                                            <div class="metric">
                                                <span class="metric-value"><?= $product['quantity'] ?></span>
                                                <span class="metric-label"><?= $product['unit'] ?> in stock</span>
                                            </div>
                                        </div>
                                        
                                        <div class="product-actions">
                                            <button onclick="editProduct(<?= $product['id'] ?>)" class="btn btn-outline btn-sm">Edit</button>
                                            <button onclick="toggleStatus(<?= $product['id'] ?>, '<?= $product['status'] ?>')" 
                                                    class="btn btn-secondary btn-sm">
                                                <?= $product['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

            <?php else: ?>
                <!-- Customer Dashboard -->
                <div class="dashboard-header">
                    <div class="welcome-section">
                        <h1>Welcome, <?= htmlspecialchars($user['username']) ?>!</h1>
                        <p>Discover fresh, local produce from Grenadian farmers</p>
                    </div>
                </div>

                <section class="products-section">
                    <h2 class="section-title">Fresh Products Available</h2>
                    
                    <?php if (empty($products)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">🌾</div>
                            <h3>No products available</h3>
                            <p>Check back soon for fresh produce from local farmers</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-auto">
                            <?php foreach ($products as $product): ?>
                                <div class="product-card" onclick="window.location.href='product.php?id=<?= $product['id'] ?>'">
                                    <img src="<?= $product['image_url'] ?: 'https://via.placeholder.com/400x300/f5f3f0/666?text=No+Image' ?>" 
                                         alt="<?= htmlspecialchars($product['name']) ?>" 
                                         class="product-image">
                                    <div class="product-info">
                                        <div class="product-category"><?= ucfirst($product['category']) ?></div>
                                        <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                                        <div class="product-price">$<?= number_format($product['price'], 2) ?> / <?= $product['unit'] ?></div>
                                        <div class="product-farmer">By <?= htmlspecialchars($product['farmer_name']) ?></div>
                                        <div class="product-availability">
                                            <span class="availability-badge"><?= $product['quantity'] ?> <?= $product['unit'] ?> available</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
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

    <?php if ($user['user_type'] === 'farmer'): ?>
        <!-- Add/Edit Product Modal -->
        <div id="productModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="modalTitle">Add New Product</h2>
                    <button class="modal-close" onclick="closeProductModal()">&times;</button>
                </div>
                
                <form id="productForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="id" id="productId">
                    
                    <div class="form-group">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" id="productName" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category" id="productCategory" class="form-input" required>
                            <option value="vegetables">🥬 Vegetables</option>
                            <option value="fruits">🍎 Fruits</option>
                            <option value="herbs">🌿 Herbs</option>
                            <option value="other">🌾 Other</option>
                        </select>
                    </div>
                    
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label">Price</label>
                            <input type="number" name="price" id="productPrice" class="form-input" step="0.01" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Unit</label>
                            <select name="unit" id="productUnit" class="form-input" required>
                                <option value="lb">lb</option>
                                <option value="kg">kg</option>
                                <option value="bunch">bunch</option>
                                <option value="each">each</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Quantity Available</label>
                        <input type="number" name="quantity" id="productQuantity" class="form-input" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="productDescription" class="form-input" rows="4" placeholder="Describe your product..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="image" id="productImage" class="form-input" accept="image/*" onchange="previewImage(event)">
                        <div id="imagePreview" class="image-preview">
                            <span class="image-preview-text">Image preview will appear here</span>
                        </div>
                    </div>
                    
                    <div class="modal-actions">
                        <button type="button" onclick="closeProductModal()" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <script>
        function toggleMobileMenu() {
            document.querySelector('.nav-links').classList.toggle('active');
        }

        <?php if ($user['user_type'] === 'farmer'): ?>
        function openAddProductModal() {
            document.getElementById('modalTitle').textContent = 'Add New Product';
            document.getElementById('formAction').value = 'create';
            document.getElementById('productForm').reset();
            document.getElementById('imagePreview').innerHTML = '<span class="image-preview-text">Image preview will appear here</span>';
            document.getElementById('productModal').classList.add('active');
        }

        function closeProductModal() {
            document.getElementById('productModal').classList.remove('active');
        }

        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').innerHTML = 
                        `<img src="${e.target.result}" alt="Preview">`;
                };
                reader.readAsDataURL(file);
            }
        }

        function editProduct(id) {
            fetch(`api/products.php?action=get&id=${id}`)
                .then(res => res.json())
                .then(product => {
                    document.getElementById('modalTitle').textContent = 'Edit Product';
                    document.getElementById('formAction').value = 'update';
                    document.getElementById('productId').value = product.id;
                    document.getElementById('productName').value = product.name;
                    document.getElementById('productCategory').value = product.category;
                    document.getElementById('productPrice').value = product.price;
                    document.getElementById('productUnit').value = product.unit;
                    document.getElementById('productQuantity').value = product.quantity;
                    document.getElementById('productDescription').value = product.description;
                    
                    if (product.image_url) {
                        document.getElementById('imagePreview').innerHTML = 
                            `<img src="${product.image_url}" alt="Current image">`;
                    }
                    
                    document.getElementById('productModal').classList.add('active');
                });
        }

        function toggleStatus(id, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'out_of_stock' : 'active';
            
            fetch('api/products.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action: 'toggle_status', id, status: newStatus})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating status');
                }
            });
        }

        document.getElementById('productForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('api/products.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error saving product');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving product');
            });
        });
        <?php endif; ?>
    </script>
</body>
</html>
