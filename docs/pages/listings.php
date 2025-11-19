<div class="card" style="max-width:700px;margin:0 auto;">
  <h2>🌾 Start Listing Your Produce</h2>
  <p style="color:#666;margin-bottom:1.5rem;">Fill in the details to add your products</p>
  <form method="POST" action="actions.php">
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
    <button type="submit" name="listProduct" class="btn btn-primary" style="width:100%;">✓ List Product</button>
  </form>
</div>
