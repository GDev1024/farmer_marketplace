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
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2>📦 My Listings</h2>
    <a href="index.php?page=listing" class="btn btn-primary">+ Add New Product</a>
  </div>
  
  <?php if (empty($listings)): ?>
    <div class="empty-state">
      <p>📭 You haven't listed any products yet</p>
      <a href="index.php?page=listing" class="btn btn-primary">Start Your First Listing</a>
    </div>
  <?php else: ?>
    <div class="listings-grid">
      <?php foreach($listings as $listing): ?>
        <div class="listing-card">
          <div class="listing-image">
            <?php if (!empty($listing['thumbnail_path']) && file_exists($listing['thumbnail_path'])): ?>
              <img src="<?= htmlspecialchars($listing['thumbnail_path']) ?>" 
                   alt="<?= htmlspecialchars($listing['product_name']) ?>"
                   style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px;">
            <?php else: ?>
              <div style="width: 100%; height: 120px; display: flex; align-items: center; justify-content: center; background: #f5f5f5; border-radius: 8px; font-size: 2rem;">
                <?php
                $emojis = ['vegetables' => '🥬', 'fruits' => '🍎', 'herbs' => '🌿', 'grains' => '🌾'];
                echo $emojis[$listing['category']] ?? '🌾';
                ?>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="listing-info">
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
            
            <div class="listing-stats">
              <span><strong><?= $listing['quantity'] ?></strong> <?= htmlspecialchars($listing['unit']) ?> available</span>
              <span>Listed <?= date('M j, Y', strtotime($listing['created_at'])) ?></span>
            </div>
            
            <div class="listing-actions">
              <button onclick="editListing(<?= $listing['id'] ?>)" class="btn btn-secondary btn-sm">✏️ Edit</button>
              
              <?php if ($listing['is_active']): ?>
                <form method="POST" action="actions.php" style="display: inline;">
                  <input type="hidden" name="listingId" value="<?= $listing['id'] ?>">
                  <input type="hidden" name="deactivateListing" value="1">
                  <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Deactivate this listing?')">⏸️ Deactivate</button>
                </form>
              <?php else: ?>
                <form method="POST" action="actions.php" style="display: inline;">
                  <input type="hidden" name="listingId" value="<?= $listing['id'] ?>">
                  <input type="hidden" name="activateListing" value="1">
                  <button type="submit" class="btn btn-success btn-sm">▶️ Activate</button>
                </form>
              <?php endif; ?>
              
              <?php if ($listing['quantity'] == 0): ?>
                <button onclick="restockListing(<?= $listing['id'] ?>)" class="btn btn-primary btn-sm">📦 Restock</button>
              <?php endif; ?>
              
              <button onclick="deleteListing(<?= $listing['id'] ?>)" class="btn btn-danger btn-sm">🗑️ Delete</button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Edit Listing Modal -->
<div id="editModal" class="modal" style="display: none;">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Edit Listing</h3>
      <span class="close" onclick="closeEditModal()">&times;</span>
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
          <option value="vegetables">Vegetables</option>
          <option value="fruits">Fruits</option>
          <option value="herbs">Herbs & Spices</option>
          <option value="grains">Grains & Legumes</option>
        </select>
      </div>
      
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
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
        <label>Update Image (Optional)</label>
        <input type="file" name="productImage" accept="image/jpeg,image/jpg,image/png">
        <small style="color:#666;display:block;margin-top:0.5rem;">
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
      <span class="close" onclick="closeRestockModal()">&times;</span>
    </div>
    <form method="POST" action="actions.php">
      <input type="hidden" name="restockListing" value="1">
      <input type="hidden" name="listingId" id="restockListingId">
      
      <div class="form-group">
        <label>Add Quantity</label>
        <input type="number" name="addQuantity" min="1" required>
        <small style="color:#666;display:block;margin-top:0.5rem;">
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

<style>
.listings-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.listing-card {
  border: 1px solid #D2DCB6;
  border-radius: 12px;
  padding: 1rem;
  background: white;
}

.listing-info h3 {
  margin: 0.5rem 0;
  color: #2C3E50;
}

.listing-price {
  font-size: 1.2rem;
  font-weight: bold;
  color: #27AE60;
  margin-bottom: 0.5rem;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: bold;
  margin-bottom: 0.5rem;
}

.status-active { background: #D4EDDA; color: #155724; }
.status-out-of-stock { background: #FFF3CD; color: #856404; }
.status-inactive { background: #F8D7DA; color: #721C24; }

.listing-stats {
  font-size: 0.9rem;
  color: #666;
  margin-bottom: 1rem;
}

.listing-stats span {
  display: block;
  margin-bottom: 0.25rem;
}

.listing-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.btn-sm {
  padding: 0.4rem 0.8rem;
  font-size: 0.85rem;
}

.modal {
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.5);
}

.modal-content {
  background-color: white;
  margin: 5% auto;
  padding: 0;
  border-radius: 12px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #eee;
}

.modal-header h3 {
  margin: 0;
}

.close {
  font-size: 1.5rem;
  cursor: pointer;
  color: #999;
}

.close:hover {
  color: #333;
}

.modal form {
  padding: 1.5rem;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1.5rem;
}

@media (max-width: 768px) {
  .listings-grid {
    grid-template-columns: 1fr;
  }
  
  .listing-actions {
    flex-direction: column;
  }
  
  .modal-content {
    width: 95%;
    margin: 2% auto;
  }
}
</style>

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
  }
}

function closeEditModal() {
  document.getElementById('editModal').style.display = 'none';
}

function restockListing(listingId) {
  document.getElementById('restockListingId').value = listingId;
  document.getElementById('restockModal').style.display = 'block';
}

function closeRestockModal() {
  document.getElementById('restockModal').style.display = 'none';
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
</script>
