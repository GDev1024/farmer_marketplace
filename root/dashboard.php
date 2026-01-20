<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/aws-image-handler.php';

requireLogin();
$user = getCurrentUser();

if ($user['user_type'] !== 'farmer') {
    redirect('index.php');
}

$db = Config::getDB();

// Get farmer's products
$stmt = $db->prepare("SELECT * FROM listings WHERE farmer_id = ? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$products = $stmt->fetchAll();

// Get stats
$stmt = $db->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active FROM listings WHERE farmer_id = ?");
$stmt->execute([$user['id']]);
$stats = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= Config::SITE_NAME ?></title>
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
                <a href="dashboard.php">My Products</a>
                <a href="api/auth.php?action=logout" class="btn btn-secondary btn-sm">Logout</a>
            </div>
        </nav>
    </header>

    <div class="container" style="margin-top: 3rem;">
        <div class="dashboard-header">
            <div>
                <h1 style="color: var(--primary-green);">Welcome back, <?= htmlspecialchars($user['username']) ?>!</h1>
                <p style="color: var(--gray-600);">Manage your product listings</p>
            </div>
            <button onclick="openAddProductModal()" class="btn btn-primary">+ Add New Product</button>
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
        </div>

        <h2 style="margin-bottom: 1.5rem;">My Products</h2>
        
        <?php if (empty($products)): ?>
            <div class="card" style="text-align: center; padding: 3rem;">
                <p style="font-size: 1.2rem; color: var(--gray-600); margin-bottom: 1rem;">
                    You haven't added any products yet
                </p>
                <button onclick="openAddProductModal()" class="btn btn-primary">Add Your First Product</button>
            </div>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($products as $product): ?>
                    <div class="card">
                        <div style="display: flex; gap: 1.5rem;">
                            <img src="<?= $product['image_url'] ?: 'https://via.placeholder.com/150?text=No+Image' ?>" 
                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                 style="width: 150px; height: 150px; object-fit: cover; border-radius: var(--radius-sm);">
                            
                            <div style="flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                    <div>
                                        <h3 style="color: var(--primary-green); margin-bottom: 0.25rem;">
                                            <?= htmlspecialchars($product['name']) ?>
                                        </h3>
                                        <span class="badge badge-primary"><?= ucfirst($product['category']) ?></span>
                                    </div>
                                    <span class="badge <?= $product['status'] === 'active' ? 'badge-success' : 'badge-warning' ?>">
                                        <?= ucfirst(str_replace('_', ' ', $product['status'])) ?>
                                    </span>
                                </div>
                                
                                <p style="color: var(--gray-600); margin: 0.5rem 0;">
                                    <?= substr(htmlspecialchars($product['description']), 0, 100) ?>...
                                </p>
                                
                                <div style="display: flex; gap: 2rem; margin: 1rem 0;">
                                    <div>
                                        <strong style="color: var(--primary-green);">$<?= number_format($product['price'], 2) ?></strong>
                                        <span style="color: var(--gray-600);">/ <?= $product['unit'] ?></span>
                                    </div>
                                    <div>
                                        <strong><?= $product['quantity'] ?></strong>
                                        <span style="color: var(--gray-600);"><?= $product['unit'] ?> in stock</span>
                                    </div>
                                </div>
                                
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <button onclick="editProduct(<?= $product['id'] ?>)" class="btn btn-secondary btn-sm">Edit</button>
                                    <button onclick="toggleStatus(<?= $product['id'] ?>, '<?= $product['status'] ?>')" 
                                            class="btn btn-secondary btn-sm">
                                        <?= $product['status'] === 'active' ? 'Mark Out of Stock' : 'Mark Available' ?>
                                    </button>
                                    <button onclick="deleteProduct(<?= $product['id'] ?>)" class="btn btn-danger btn-sm">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

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
                        <option value="vegetables">Vegetables</option>
                        <option value="fruits">Fruits</option>
                        <option value="herbs">Herbs</option>
                        <option value="other">Other</option>
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
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" id="productQuantity" class="form-input" min="0" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="productDescription" class="form-input" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Product Image</label>
                    <input type="file" name="image" id="productImage" class="form-input" accept="image/*" onchange="previewImage(event)">
                    <div id="imagePreview" class="image-preview">
                        <span class="image-preview-text">Image preview will appear here</span>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" onclick="closeProductModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
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

        function deleteProduct(id) {
            if (!confirm('Are you sure you want to delete this product?')) return;
            
            fetch('api/products.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action: 'delete', id})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error deleting product');
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
    </script>
</body>
</html>