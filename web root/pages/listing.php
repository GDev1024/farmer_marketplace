<div class="card" style="max-width:700px;margin:0 auto;">
  <h2>🌾 Start Listing Your Produce</h2>
  <p style="color:#666;margin-bottom:1.5rem;">Fill in the details to add your products</p>
  <form method="POST" action="actions.php" enctype="multipart/form-data">
    <div class="form-group">
      <label>Product Name</label>
      <input type="text" name="productName" required>
    </div>
    <div class="form-group">
      <label>Category</label>
      <select name="category" required>
        <option value="">Select category</option>
        <option value="vegetables">Vegetables</option>
        <option value="fruits">Fruits</option>
        <option value="herbs">Herbs & Spices</option>
        <option value="grains">Grains & Legumes</option>
      </select>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr; gap:1rem;">
      <div class="form-group">
        <label>Price (EC$)</label>
        <input type="number" name="price" step="0.01" min="0" required>
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
      <input type="number" name="quantity" min="0" required>
    </div>
    <div class="form-group">
      <label>Description</label>
      <textarea name="description" required></textarea>
    </div>
    <div class="form-group">
      <label>Product Image (Optional)</label>
      <input type="file" name="productImage" accept="image/jpeg,image/jpg,image/png" id="productImage">
      <small style="color:#666;display:block;margin-top:0.5rem;">
        Upload a photo of your produce (JPG or PNG, max 5MB). Images will be resized to 800x600px.
      </small>
      <div id="imagePreview" style="margin-top:1rem;display:none;">
        <img id="previewImg" style="max-width:200px;max-height:150px;border-radius:8px;border:2px solid #D2DCB6;">
      </div>
    </div>
    <button type="submit" name="listProduct" class="btn btn-primary" style="width:100%;">✓ List Product</button>
  </form>
</div>

<script>
document.getElementById('productImage').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (file) {
        // Validate file type
        if (!file.type.match(/^image\/(jpeg|jpg|png)$/)) {
            alert('Please select a JPG or PNG image file.');
            e.target.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Image file is too large. Please select a file smaller than 5MB.');
            e.target.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});
</script>
