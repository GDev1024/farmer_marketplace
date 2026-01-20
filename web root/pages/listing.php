<div class="card">
  <div class="card-header">
    <h2 style="margin: 0; display: flex; align-items: center; gap: var(--space-sm);">
      <span>🌾</span> List Your Produce
    </h2>
    <p style="color: var(--text-secondary); margin: var(--space-sm) 0 0 0; font-size: 14px;">
      Share your fresh produce with the community
    </p>
  </div>
  
  <div class="card-body">
    <form method="POST" action="actions.php" enctype="multipart/form-data">
      <div class="form-group">
        <label>Product Name</label>
        <input type="text" name="productName" placeholder="e.g., Fresh Organic Tomatoes" required>
      </div>
      
      <div class="form-group">
        <label>Category</label>
        <select name="category" required>
          <option value="">Select a category</option>
          <option value="vegetables">🥬 Vegetables</option>
          <option value="fruits">🍎 Fruits</option>
          <option value="herbs">🌿 Herbs & Spices</option>
          <option value="grains">🌾 Grains & Legumes</option>
        </select>
      </div>
      
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-md);">
        <div class="form-group">
          <label>Price (EC$)</label>
          <input type="number" name="price" step="0.01" min="0" placeholder="0.00" required>
        </div>
        <div class="form-group">
          <label>Unit Type</label>
          <select name="unit" required>
            <option value="">Select unit</option>
            <option value="lbs">Pounds (lbs)</option>
            <option value="kg">Kilograms (kg)</option>
            <option value="pieces">Pieces</option>
            <option value="bunches">Bunches</option>
          </select>
        </div>
      </div>
      
      <div class="form-group">
        <label>Quantity Available</label>
        <input type="number" name="quantity" min="0" placeholder="How many units do you have?" required>
      </div>
      
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" placeholder="Describe your produce - freshness, growing methods, harvest date, etc." required></textarea>
      </div>
      
      <div class="form-group">
        <label>Product Photo (Optional)</label>
        <div style="position: relative;">
          <input type="file" name="productImage" accept="image/jpeg,image/jpg,image/png" id="productImage" 
                 style="padding: var(--space-lg); border: 2px dashed var(--border-medium); border-radius: var(--radius-md); background: var(--bg-primary); cursor: pointer; transition: var(--transition-fast);">
          <small style="display: block; margin-top: var(--space-sm); color: var(--text-muted);">
            📸 Upload a photo of your produce (JPG or PNG, max 5MB). High-quality images help attract more buyers!
          </small>
        </div>
        <div id="imagePreview" style="margin-top: var(--space-md); display: none; text-align: center;">
          <img id="previewImg" style="max-width: 200px; max-height: 150px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
          <p style="margin-top: var(--space-sm); color: var(--text-secondary); font-size: 14px;">Preview of your uploaded image</p>
        </div>
      </div>
      
      <div style="background: var(--bg-primary); padding: var(--space-md); border-radius: var(--radius-md); margin-bottom: var(--space-lg);">
        <h4 style="margin: 0 0 var(--space-sm) 0; color: var(--text-primary); font-size: 14px; display: flex; align-items: center; gap: var(--space-sm);">
          💡 Tips for Better Sales
        </h4>
        <ul style="margin: 0; padding-left: var(--space-lg); color: var(--text-secondary); font-size: 14px; line-height: 1.5;">
          <li>Use clear, well-lit photos of your produce</li>
          <li>Mention if your produce is organic or pesticide-free</li>
          <li>Include harvest date or freshness information</li>
          <li>Set competitive prices based on quality</li>
        </ul>
      </div>
      
      <button type="submit" name="listProduct" class="btn btn-primary btn-lg" style="width: 100%;">
        ✨ List My Produce
      </button>
    </form>
  </div>
</div>

<script>
document.getElementById('productImage').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const input = e.target;
    
    if (file) {
        // Validate file type
        if (!file.type.match(/^image\/(jpeg|jpg|png)$/)) {
            alert('Please select a JPG or PNG image file.');
            e.target.value = '';
            preview.style.display = 'none';
            input.style.borderColor = 'var(--error)';
            return;
        }
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Image file is too large. Please select a file smaller than 5MB.');
            e.target.value = '';
            preview.style.display = 'none';
            input.style.borderColor = 'var(--error)';
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
            preview.classList.add('fade-in');
        };
        reader.readAsDataURL(file);
        input.style.borderColor = 'var(--success)';
    } else {
        preview.style.display = 'none';
        input.style.borderColor = 'var(--border-medium)';
    }
});

// Enhanced file input styling
document.getElementById('productImage').addEventListener('dragover', function(e) {
    e.preventDefault();
    this.style.borderColor = 'var(--primary)';
    this.style.background = 'var(--bg-tertiary)';
});

document.getElementById('productImage').addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.style.borderColor = 'var(--border-medium)';
    this.style.background = 'var(--bg-primary)';
});

document.getElementById('productImage').addEventListener('drop', function(e) {
    e.preventDefault();
    this.style.borderColor = 'var(--border-medium)';
    this.style.background = 'var(--bg-primary)';
});
</script>
