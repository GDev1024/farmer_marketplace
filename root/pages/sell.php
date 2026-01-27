<?php
// Get user's listings
$stmt = $pdo->prepare("SELECT * FROM listings WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['userId']]);
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Display message if set
$msg = getAndClearMessage();
if($msg['message']): ?>
    <div class="alert alert-<?= $msg['type'] ?>">
        <?= ($msg['type'] === 'success' ? '✓' : '⚠') ?> <?= htmlspecialchars($msg['message']) ?>
    </div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
      <div>
        <h2 style="margin: 0; display: flex; align-items: center; gap: var(--space-sm);">
          <span>📦</span> My Listings
        </h2>
        <p style="color: var(--text-secondary); margin: var(--space-sm) 0 0 0; font-size: 14px;">
          Manage your products and track sales
        </p>
      </div>
      <a href="index.php?page=listing" class="btn btn-primary">
        <span>+</span> Add Product
      </a>
    </div>
  </div>
  
  <div class="card-body">
    <?php if (empty($listings)): ?>
      <div class="empty-state">
        <p>📦</p>
        <p><strong>No products listed yet</strong></p>
        <p style="color: var(--text-secondary); margin-bottom: var(--space-lg);">
          Start selling your fresh produce to the community. It's easy to get started!
        </p>
        <a href="index.php?page=listing" class="btn btn-primary btn-lg">
          🌾 List Your First Product
        </a>
      </div>
    <?php else: ?>
      <div class="listings-grid">
        <?php foreach($listings as $listing): ?>
          <div class="listing-card">
            <div class="listing-header">
              <div style="display: flex; gap: var(--space-md); align-items: flex-start;">
                <div class="listing-image">
                  <?php if (!empty($listing['thumbnail_path']) && file_exists($listing['thumbnail_path'])): ?>
                    <img src="<?= htmlspecialchars($listing['thumbnail_path']) ?>" 
                         alt="<?= htmlspecialchars($listing['product_name']) ?>"
                         loading="lazy">
                  <?php else: ?>
                    <div style="font-size: 32px; color: var(--text-muted);">
                      <?php
                      $emojis = ['vegetables' => '🥬', 'fruits' => '🍎', 'herbs' => '🌿', 'grains' => '🌾'];
                      echo $emojis[$listing['category']] ?? '🌾';
                      ?>
                    </div>
                  <?php endif; ?>
                </div>
                
                <div class="listing-info" style="flex: 1;">
                  <h3><?= htmlspecialchars($listing['product_name']) ?></h3>
                  <div class="listing-price">EC$<?= number_format($listing['price'], 2) ?> / <?= htmlspecialchars($listing['unit']) ?></div>
                  
                  <div class="listing-status">
                    <?php if ($listing['is_active'] && $listing['quantity'] > 0): ?>
                      <span class="status-badge status-active">✅ Active</span>
                    <?php elseif ($listing['is_active'] && $listing['quantity'] == 0): ?>
                      <span class="status-badge status-out-of-stock">📦 Out of Stock</span>
                    <?php else: ?>
                      <span class="status-badge status-inactive">⏸️ Inactive</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="listing-stats">
              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md); margin-bottom: var(--space-md);">
                <div>
                  <strong style="color: var(--success);"><?= $listing['quantity'] ?></strong> 
                  <span style="color: var(--text-secondary);"><?= htmlspecialchars($listing['unit']) ?> available</span>
                </div>
                <div style="text-align: right;">
                  <span style="color: var(--text-muted); font-size: 12px;">
                    Listed <?= date('M j, Y', strtotime($listing['created_at'])) ?>
                  </span>
                </div>
              </div>
              
              <p style="color: var(--text-secondary); font-size: 14px; line-height: 1.4; margin-bottom: var(--space-md);">
                <?= htmlspecialchars(substr($listing['description'], 0, 120)) ?><?= strlen($listing['description']) > 120 ? '...' : '' ?>
              </p>
            </div>
            
            <div class="listing-actions">
              <button onclick="editListing(<?= $listing['id'] ?>)" class="btn btn-secondary btn-sm">
                ✏️ Edit
              </button>
              
              <?php if ($listing['is_active']): ?>
                <form method="POST" action="actions.php" style="display: inline;">
                  <input type="hidden" name="listingId" value="<?= $listing['id'] ?>">
                  <input type="hidden" name="deactivateListing" value="1">
                  <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Deactivate this listing?')">
                    ⏸️ Pause
                  </button>
                </form>
              <?php else: ?>
                <form method="POST" action="actions.php" style="display: inline;">
                  <input type="hidden" name="listingId" value="<?= $listing['id'] ?>">
                  <input type="hidden" name="activateListing" value="1">
                  <button type="submit" class="btn btn-success btn-sm">
                    ▶️ Activate
                  </button>
                </form>
              <?php endif; ?>
              
              <?php if ($listing['quantity'] == 0): ?>
                <button onclick="restockListing(<?= $listing['id'] ?>)" class="btn btn-primary btn-sm">
                  📦 Restock
                </button>
              <?php endif; ?>
              
              <button onclick="deleteListing(<?= $listing['id'] ?>)" class="btn btn-danger btn-sm">
                🗑️ Delete
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      
      <!-- Summary Card -->
      <div style="background: var(--bg-primary); padding: var(--space-lg); border-radius: var(--radius-md); margin-top: var(--space-lg);">
        <h4 style="margin: 0 0 var(--space-md) 0; color: var(--text-primary); display: flex; align-items: center; gap: var(--space-sm);">
          📊 Quick Stats
        </h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: var(--space-md);">
          <div style="text-align: center;">
            <div style="font-size: 20px; font-weight: var(--font-weight-bold); color: var(--primary);">
              <?= count($listings) ?>
            </div>
            <div style="font-size: 12px; color: var(--text-secondary);">Total Products</div>
          </div>
          <div style="text-align: center;">
            <div style="font-size: 20px; font-weight: var(--font-weight-bold); color: var(--success);">
              <?= count(array_filter($listings, function($l) { return $l['is_active'] && $l['quantity'] > 0; })) ?>
            </div>
            <div style="font-size: 12px; color: var(--text-secondary);">Active</div>
          </div>
          <div style="text-align: center;">
            <div style="font-size: 20px; font-weight: var(--font-weight-bold); color: var(--warning);">
              <?= count(array_filter($listings, function($l) { return $l['quantity'] == 0; })) ?>
            </div>
            <div style="font-size: 12px; color: var(--text-secondary);">Out of Stock</div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Edit Listing Modal -->
<div id="editModal" class="modal" style="display: none;">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Edit Listing</h3>
      <button class="close" onclick="closeEditModal()" aria-label="Close">&times;</button>
    </div>
    <form id="editForm" method="POST" action="actions.php" enctype="multipart/form-data">
      <input type="hidden" name="editListing" value="1">
      <input type="hidden" name="listingId" id="editListingId">
      
      <div class="form-group">
        <label>Product Name</label>
        <input type="text" name="productName" id="editProductName" required>
      </div>
      
      <div class="form-group">
        <label>Category</label>
        <select name="category" id="editCategory" required>
          <option value="vegetables">🥬 Vegetables</option>
          <option value="fruits">🍎 Fruits</option>
          <option value="herbs">🌿 Herbs & Spices</option>
          <option value="grains">🌾 Grains & Legumes</option>
        </select>
      </div>
      
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
        <div class="form-group">
          <label>Price (EC$)</label>
          <input type="number" name="price" id="editPrice" step="0.01" min="0" required>
        </div>
        <div class="form-group">
          <label>Unit Type</label>
          <select name="unit" id="editUnit" required>
            <option value="lbs">Pounds (lbs)</option>
            <option value="kg">Kilograms (kg)</option>
            <option value="pieces">Pieces</option>
            <option value="bunches">Bunches</option>
          </select>
        </div>
      </div>
      
      <div class="form-group">
        <label>Quantity Available</label>
        <input type="number" name="quantity" id="editQuantity" min="0" required>
      </div>
      
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" id="editDescription" required></textarea>
      </div>
      
      <div class="form-group">
        <label>Update Photo (Optional)</label>
        <input type="file" name="productImage" accept="image/jpeg,image/jpg,image/png">
        <small style="color: var(--text-muted); display: block; margin-top: var(--space-xs);">
          Leave empty to keep current image. Upload new image to replace.
        </small>
      </div>
      
      <div class="modal-actions">
        <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Listing</button>
      </div>
    </form>
  </div>
</div>

<!-- Restock Modal -->
<div id="restockModal" class="modal" style="display: none;">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Restock Product</h3>
      <button class="close" onclick="closeRestockModal()" aria-label="Close">&times;</button>
    </div>
    <form method="POST" action="actions.php">
      <input type="hidden" name="restockListing" value="1">
      <input type="hidden" name="listingId" id="restockListingId">
      
      <div class="form-group">
        <label>Add Quantity</label>
        <input type="number" name="addQuantity" min="1" placeholder="Enter quantity to add" required>
        <small style="color: var(--text-muted); display: block; margin-top: var(--space-xs);">
          Enter the number of units to add to current stock.
        </small>
      </div>
      
      <div class="modal-actions">
        <button type="button" onclick="closeRestockModal()" class="btn btn-secondary">Cancel</button>
        <button type="submit" class="btn btn-primary">Restock</button>
      </div>
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
    
    document.getElementById('editModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
  }
}

function closeEditModal() {
  document.getElementById('editModal').style.display = 'none';
  document.body.style.overflow = 'auto';
}

function restockListing(listingId) {
  document.getElementById('restockListingId').value = listingId;
  document.getElementById('restockModal').style.display = 'block';
  document.body.style.overflow = 'hidden';
}

function closeRestockModal() {
  document.getElementById('restockModal').style.display = 'none';
  document.body.style.overflow = 'auto';
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

// Close modals when clicking outside
window.onclick = function(event) {
  const editModal = document.getElementById('editModal');
  const restockModal = document.getElementById('restockModal');
  
  if (event.target == editModal) {
    closeEditModal();
  }
  if (event.target == restockModal) {
    closeRestockModal();
  }
}

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    closeEditModal();
    closeRestockModal();
  }
});
</script>
