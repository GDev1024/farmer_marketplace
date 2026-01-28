<?php
// Get user's listings
$stmt = $pdo->prepare("SELECT * FROM listings WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Display message if set
$msg = getAndClearMessage();
if($msg['message']): ?>
    <div class="alert alert-<?= $msg['type'] ?>" role="alert" aria-live="polite">
        <?= ($msg['type'] === 'success' ? '✓' : '⚠') ?> <?= htmlspecialchars($msg['message']) ?>
    </div>
<?php endif; ?>

<main class="page-main" id="main-content" role="main">
    <?php include 'includes/page-navigation.php'; ?>
    
    <header class="page-header">
        <div class="page-header-content">
            <div class="page-title-section">
                <h1 class="page-title">
                    <span class="section-icon" aria-hidden="true">📦</span>
                    My Listings
                </h1>
                <p class="page-subtitle">
                    Manage your products and track sales
                </p>
            </div>
            <div class="page-actions">
                <a href="index.php?page=listing" class="btn btn-primary">
                    <span class="btn-icon" aria-hidden="true">+</span>
                    <span class="btn-text">Add Product</span>
                </a>
            </div>
        </div>
    </header>

    <section class="sell-dashboard">
        <?php if (empty($listings)): ?>
            <div class="empty-state">
                <div class="empty-icon" aria-hidden="true">📦</div>
                <h2 class="empty-title">No products listed yet</h2>
                <p class="empty-description">
                    Start selling your fresh produce to the community. It's easy to get started!
                </p>
                <a href="index.php?page=listing" class="btn btn-primary btn-lg">
                    <span class="btn-icon" aria-hidden="true">🌾</span>
                    <span class="btn-text">List Your First Product</span>
                </a>
            </div>
        <?php else: ?>
            <div class="listings-grid">
                <?php foreach($listings as $listing): ?>
                    <article class="listing-card">
                        <header class="listing-card__header">
                            <div class="listing-card__media">
                                <?php if (!empty($listing['thumbnail_path']) && file_exists($listing['thumbnail_path'])): ?>
                                    <img src="<?= htmlspecialchars($listing['thumbnail_path']) ?>" 
                                         alt="<?= htmlspecialchars($listing['product_name']) ?>"
                                         class="listing-card__image"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="listing-card__placeholder" aria-hidden="true">
                                        <?php
                                        $emojis = ['vegetables' => '🥬', 'fruits' => '🍎', 'herbs' => '🌿', 'grains' => '🌾'];
                                        echo $emojis[$listing['category']] ?? '🌾';
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="listing-card__content">
                                <h3 class="listing-card__title"><?= htmlspecialchars($listing['product_name']) ?></h3>
                                <div class="listing-card__price">EC$<?= number_format($listing['price'], 2) ?> / <?= htmlspecialchars($listing['unit']) ?></div>
                                
                                <div class="listing-card__status">
                                    <?php if ($listing['is_active'] && $listing['quantity'] > 0): ?>
                                        <span class="status-badge status-badge--active">
                                            <span class="status-badge__icon" aria-hidden="true">✅</span>
                                            <span class="status-badge__text">Active</span>
                                        </span>
                                    <?php elseif ($listing['is_active'] && $listing['quantity'] == 0): ?>
                                        <span class="status-badge status-badge--out-of-stock">
                                            <span class="status-badge__icon" aria-hidden="true">📦</span>
                                            <span class="status-badge__text">Out of Stock</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="status-badge status-badge--inactive">
                                            <span class="status-badge__icon" aria-hidden="true">⏸️</span>
                                            <span class="status-badge__text">Inactive</span>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </header>
                        
                        <div class="listing-card__body">
                            <div class="listing-card__stats">
                                <div class="listing-card__stat">
                                    <span class="listing-card__stat-value"><?= $listing['quantity'] ?></span> 
                                    <span class="listing-card__stat-label"><?= htmlspecialchars($listing['unit']) ?> available</span>
                                </div>
                                <div class="listing-card__meta">
                                    <time class="listing-card__date" datetime="<?= $listing['created_at'] ?>">
                                        Listed <?= date('M j, Y', strtotime($listing['created_at'])) ?>
                                    </time>
                                </div>
                            </div>
                            
                            <p class="listing-card__description">
                                <?= htmlspecialchars(substr($listing['description'], 0, 120)) ?><?= strlen($listing['description']) > 120 ? '...' : '' ?>
                            </p>
                        </div>
                        
                        <footer class="listing-card__actions">
                            <button onclick="editListing(<?= $listing['id'] ?>)" 
                                    class="btn btn-secondary btn-sm"
                                    aria-label="Edit <?= htmlspecialchars($listing['product_name']) ?>">
                                <span class="btn-icon" aria-hidden="true">✏️</span>
                                <span class="btn-text">Edit</span>
                            </button>
                            
                            <?php if ($listing['is_active']): ?>
                                <form method="POST" action="actions.php" class="listing-card__action-form">
                                    <input type="hidden" name="listingId" value="<?= $listing['id'] ?>">
                                    <input type="hidden" name="deactivateListing" value="1">
                                    <button type="submit" 
                                            class="btn btn-warning btn-sm" 
                                            onclick="return confirm('Deactivate this listing?')"
                                            aria-label="Pause <?= htmlspecialchars($listing['product_name']) ?>">
                                        <span class="btn-icon" aria-hidden="true">⏸️</span>
                                        <span class="btn-text">Pause</span>
                                    </button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="actions.php" class="listing-card__action-form">
                                    <input type="hidden" name="listingId" value="<?= $listing['id'] ?>">
                                    <input type="hidden" name="activateListing" value="1">
                                    <button type="submit" 
                                            class="btn btn-success btn-sm"
                                            aria-label="Activate <?= htmlspecialchars($listing['product_name']) ?>">
                                        <span class="btn-icon" aria-hidden="true">▶️</span>
                                        <span class="btn-text">Activate</span>
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if ($listing['quantity'] == 0): ?>
                                <button onclick="restockListing(<?= $listing['id'] ?>)" 
                                        class="btn btn-primary btn-sm"
                                        aria-label="Restock <?= htmlspecialchars($listing['product_name']) ?>">
                                    <span class="btn-icon" aria-hidden="true">📦</span>
                                    <span class="btn-text">Restock</span>
                                </button>
                            <?php endif; ?>
                            
                            <button onclick="deleteListing(<?= $listing['id'] ?>)" 
                                    class="btn btn-danger btn-sm"
                                    aria-label="Delete <?= htmlspecialchars($listing['product_name']) ?>">
                                <span class="btn-icon" aria-hidden="true">🗑️</span>
                                <span class="btn-text">Delete</span>
                            </button>
                        </footer>
                    </article>
                <?php endforeach; ?>
            </div>
            <!-- Summary Card -->
            <aside class="sell-stats" aria-labelledby="sell-stats-title">
                <h2 id="sell-stats-title" class="sell-stats__title">
                    <span class="sell-stats__icon" aria-hidden="true">📊</span>
                    Quick Stats
                </h2>
                <div class="sell-stats__grid">
                    <div class="sell-stats__item">
                        <div class="sell-stats__value">
                            <?= count($listings) ?>
                        </div>
                        <div class="sell-stats__label">Total Products</div>
                    </div>
                    <div class="sell-stats__item">
                        <div class="sell-stats__value sell-stats__value--success">
                            <?= count(array_filter($listings, function($l) { return $l['is_active'] && $l['quantity'] > 0; })) ?>
                        </div>
                        <div class="sell-stats__label">Active</div>
                    </div>
                    <div class="sell-stats__item">
                        <div class="sell-stats__value sell-stats__value--warning">
                            <?= count(array_filter($listings, function($l) { return $l['quantity'] == 0; })) ?>
                        </div>
                        <div class="sell-stats__label">Out of Stock</div>
                    </div>
                </div>
            </aside>
        <?php endif; ?>
    </section>
</main>

<!-- Edit Listing Modal -->
<div id="editModal" class="modal" style="display: none;" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
  <div class="modal-backdrop"></div>
  <div class="modal-content">
    <header class="modal-header">
      <h2 id="editModalTitle" class="modal-title">Edit Listing</h2>
      <button class="modal-close" onclick="closeEditModal()" aria-label="Close edit listing dialog" type="button">
        <span aria-hidden="true">&times;</span>
      </button>
    </header>
    
    <form id="editForm" method="POST" action="actions.php" enctype="multipart/form-data" class="modal-form">
      <input type="hidden" name="editListing" value="1">
      <input type="hidden" name="listingId" id="editListingId">
      
      <div class="form-group">
        <label for="editProductName" class="form-label">Product Name</label>
        <input type="text" 
               name="productName" 
               id="editProductName" 
               class="form-input" 
               required 
               aria-describedby="editProductNameHelp">
        <small id="editProductNameHelp" class="form-help">Enter the name of your product</small>
      </div>
      
      <div class="form-group">
        <label for="editCategory" class="form-label">Category</label>
        <select name="category" 
                id="editCategory" 
                class="form-input" 
                required 
                aria-describedby="editCategoryHelp">
          <option value="vegetables">🥬 Vegetables</option>
          <option value="fruits">🍎 Fruits</option>
          <option value="herbs">🌿 Herbs & Spices</option>
          <option value="grains">🌾 Grains & Legumes</option>
        </select>
        <small id="editCategoryHelp" class="form-help">Select the product category</small>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label for="editPrice" class="form-label">Price (EC$)</label>
          <input type="number" 
                 name="price" 
                 id="editPrice" 
                 class="form-input" 
                 step="0.01" 
                 min="0" 
                 required 
                 aria-describedby="editPriceHelp">
          <small id="editPriceHelp" class="form-help">Price per unit in EC dollars</small>
        </div>
        <div class="form-group">
          <label for="editUnit" class="form-label">Unit Type</label>
          <select name="unit" 
                  id="editUnit" 
                  class="form-input" 
                  required 
                  aria-describedby="editUnitHelp">
            <option value="lbs">Pounds (lbs)</option>
            <option value="kg">Kilograms (kg)</option>
            <option value="pieces">Pieces</option>
            <option value="bunches">Bunches</option>
          </select>
          <small id="editUnitHelp" class="form-help">How the product is sold</small>
        </div>
      </div>
      
      <div class="form-group">
        <label for="editQuantity" class="form-label">Quantity Available</label>
        <input type="number" 
               name="quantity" 
               id="editQuantity" 
               class="form-input" 
               min="0" 
               required 
               aria-describedby="editQuantityHelp">
        <small id="editQuantityHelp" class="form-help">How many units are available</small>
      </div>
      
      <div class="form-group">
        <label for="editDescription" class="form-label">Description</label>
        <textarea name="description" 
                  id="editDescription" 
                  class="form-input" 
                  required 
                  aria-describedby="editDescriptionHelp"></textarea>
        <small id="editDescriptionHelp" class="form-help">Describe your product to attract buyers</small>
      </div>
      
      <div class="form-group">
        <label for="editProductImage" class="form-label">Update Photo <span class="optional-label">(Optional)</span></label>
        <div class="image-upload-wrapper">
          <input type="file" 
                 name="productImage" 
                 id="editProductImage" 
                 class="image-upload-input" 
                 accept="image/jpeg,image/jpg,image/png" 
                 aria-describedby="editImageHelp">
          <div class="image-upload-content">
            <div class="upload-icon" aria-hidden="true">📸</div>
            <div class="upload-text">
              <span class="upload-primary">Click to upload or drag and drop</span>
              <span class="upload-secondary">JPG or PNG, max 5MB</span>
            </div>
          </div>
        </div>
        <small id="editImageHelp" class="form-help">Leave empty to keep current image. Upload new image to replace.</small>
        
        <div id="editImagePreview" class="image-preview" style="display: none;" aria-live="polite">
          <img id="editPreviewImg" class="preview-image" alt="Product image preview">
          <p class="preview-caption">Preview of your uploaded image</p>
          <button type="button" class="btn btn-sm btn-secondary remove-image-btn" onclick="removeEditImage()">
            <span class="btn-icon" aria-hidden="true">🗑️</span>
            Remove Image
          </button>
        </div>
      </div>
      
      <footer class="modal-actions">
        <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Listing</button>
      </footer>
    </form>
  </div>
</div>

<!-- Restock Modal -->
<div id="restockModal" class="modal" style="display: none;" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="restockModalTitle">
  <div class="modal-backdrop"></div>
  <div class="modal-content">
    <header class="modal-header">
      <h2 id="restockModalTitle" class="modal-title">Restock Product</h2>
      <button class="modal-close" onclick="closeRestockModal()" aria-label="Close restock dialog" type="button">
        <span aria-hidden="true">&times;</span>
      </button>
    </header>
    
    <form method="POST" action="actions.php" class="modal-form">
      <input type="hidden" name="restockListing" value="1">
      <input type="hidden" name="listingId" id="restockListingId">
      
      <div class="form-group">
        <label for="addQuantity" class="form-label">Add Quantity</label>
        <input type="number" 
               name="addQuantity" 
               id="addQuantity" 
               class="form-input" 
               min="1" 
               placeholder="Enter quantity to add" 
               required 
               aria-describedby="addQuantityHelp">
        <small id="addQuantityHelp" class="form-help">Enter the number of units to add to current stock.</small>
      </div>
      
      <footer class="modal-actions">
        <button type="button" onclick="closeRestockModal()" class="btn btn-secondary">Cancel</button>
        <button type="submit" class="btn btn-primary">Restock</button>
      </footer>
    </form>
  </div>
</div>

<script>
function editListing(listingId) {
  // Find the listing data
  const listings = <?= json_encode($listings) ?>;
  const listing = listings.find(l => l.id == listingId);
  
  if (listing) {
    document.getElementById('editListingId').value = listing.id;
    document.getElementById('editProductName').value = listing.product_name;
    document.getElementById('editCategory').value = listing.category;
    document.getElementById('editPrice').value = listing.price;
    document.getElementById('editUnit').value = listing.unit;
    document.getElementById('editQuantity').value = listing.quantity;
    document.getElementById('editDescription').value = listing.description;
    
    // Reset image preview
    resetEditImageUpload();
    
    // Use the new modal system
    openModal('editModal', { focusFirst: true });
  }
}

function closeEditModal() {
  closeModal('editModal');
  resetEditImageUpload();
}

function restockListing(listingId) {
  document.getElementById('restockListingId').value = listingId;
  openModal('restockModal', { focusFirst: true });
}

function closeRestockModal() {
  closeModal('restockModal');
}

function deleteListing(listingId) {
  if (confirm('Are you sure you want to delete this listing? This action cannot be undone.')) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'actions.php';
    
    const input1 = document.createElement('input');
    input1.type = 'hidden';
    input1.name = 'deleteListing';
    input1.value = '1';
    
    const input2 = document.createElement('input');
    input2.type = 'hidden';
    input2.name = 'listingId';
    input2.value = listingId;
    
    form.appendChild(input1);
    form.appendChild(input2);
    document.body.appendChild(form);
    form.submit();
  }
}

// Enhanced image upload functionality for edit modal
function initializeEditImageUpload() {
    const fileInput = document.getElementById('editProductImage');
    const uploadWrapper = fileInput?.closest('.image-upload-wrapper');
    const preview = document.getElementById('editImagePreview');
    const previewImg = document.getElementById('editPreviewImg');
    
    if (!fileInput || !uploadWrapper || !preview || !previewImg) return;
    
    // File input change handler
    fileInput.addEventListener('change', function(e) {
        handleEditFileSelection(e.target.files[0]);
    });
    
    // Drag and drop handlers
    uploadWrapper.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadWrapper.classList.add('drag-over');
    });
    
    uploadWrapper.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadWrapper.classList.remove('drag-over');
    });
    
    uploadWrapper.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadWrapper.classList.remove('drag-over');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleEditFileSelection(files[0]);
        }
    });
    
    // Click handler for upload area
    uploadWrapper.addEventListener('click', function() {
        fileInput.click();
    });
}

function handleEditFileSelection(file) {
    const uploadWrapper = document.querySelector('#editModal .image-upload-wrapper');
    const preview = document.getElementById('editImagePreview');
    const previewImg = document.getElementById('editPreviewImg');
    
    if (!file) {
        resetEditImageUpload();
        return;
    }
    
    // Validate file type
    if (!file.type.match(/^image\/(jpeg|jpg|png)$/)) {
        showEditImageError('Please select a JPG or PNG image file.');
        return;
    }
    
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        showEditImageError('Image file is too large. Please select a file smaller than 5MB.');
        return;
    }
    
    // Show preview
    const reader = new FileReader();
    reader.onload = function(e) {
        previewImg.src = e.target.result;
        previewImg.alt = `Preview of ${file.name}`;
        preview.style.display = 'block';
        uploadWrapper.classList.add('has-file');
        
        // Announce to screen readers
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'sr-only';
        announcement.textContent = `Image ${file.name} uploaded successfully`;
        document.body.appendChild(announcement);
        setTimeout(() => document.body.removeChild(announcement), 1000);
    };
    reader.readAsDataURL(file);
}

function showEditImageError(message) {
    const fileInput = document.getElementById('editProductImage');
    const uploadWrapper = document.querySelector('#editModal .image-upload-wrapper');
    
    alert(message);
    fileInput.value = '';
    uploadWrapper.classList.add('error');
    uploadWrapper.classList.remove('has-file');
    
    setTimeout(() => {
        uploadWrapper.classList.remove('error');
    }, 3000);
    
    resetEditImageUpload();
}

function removeEditImage() {
    const fileInput = document.getElementById('editProductImage');
    fileInput.value = '';
    resetEditImageUpload();
    
    // Announce to screen readers
    const announcement = document.createElement('div');
    announcement.setAttribute('aria-live', 'polite');
    announcement.setAttribute('aria-atomic', 'true');
    announcement.className = 'sr-only';
    announcement.textContent = 'Image removed';
    document.body.appendChild(announcement);
    setTimeout(() => document.body.removeChild(announcement), 1000);
}

function resetEditImageUpload() {
    const uploadWrapper = document.querySelector('#editModal .image-upload-wrapper');
    const preview = document.getElementById('editImagePreview');
    
    if (uploadWrapper) {
        uploadWrapper.classList.remove('has-file', 'error', 'drag-over');
    }
    
    if (preview) {
        preview.style.display = 'none';
    }
}

// Form validation enhancement
function initializeFormValidation() {
    const editForm = document.getElementById('editForm');
    if (!editForm) return;
    
    editForm.addEventListener('submit', function(e) {
        const requiredFields = editForm.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('error');
                isValid = false;
            } else {
                field.classList.remove('error');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            const firstError = editForm.querySelector('.error');
            if (firstError) {
                firstError.focus();
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
    
    // Real-time validation
    const inputs = editForm.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('error');
            } else {
                this.classList.remove('error');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('error') && this.value.trim()) {
                this.classList.remove('error');
            }
        });
    });
}

// Enhanced modal system is now handled by ModalManager in main.js
// No need for manual click handlers as the ModalManager handles backdrop clicks

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    closeEditModal();
    closeRestockModal();
  }
});

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeEditImageUpload();
    initializeFormValidation();
});
</script>
