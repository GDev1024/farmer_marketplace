<div class="card">
  <h2>Welcome, <?= htmlspecialchars($name) ?>!</h2>
  <p>
    <?= $farmerVerified ? '✅ Verified Farmer' : 'Not Verified' ?>
  </p>
</div>

<div class="grid">
  <div class="action-card">
    <div class="icon">🌾</div>
    <h3>Start Listing</h3>
    <p>Add your produce to the marketplace</p>
    <a href="index.php?page=listing" class="btn btn-primary">Start Listing</a>
  </div>
  <div class="action-card">
    <div class="icon">📊</div>
    <h3>My Listings</h3>
    <p>Manage your active products</p>
    <a href="index.php?page=sell" class="btn btn-primary">View Listings</a>
  </div>
</div>
