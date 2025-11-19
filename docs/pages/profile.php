<div class="card" style="max-width:600px;margin:0 auto;">
  <h2>Your Profile</h2>
  <p><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
  <p><strong>Verified Farmer:</strong> <?= $farmerVerified ? '✅ Yes' : '❌ No' ?></p>
  <?php if(!$farmerVerified): ?>
    <p style="color:#666;">Submit your Farmer ID to get verified.</p>
  <?php endif; ?>
  <a href="index.php?page=logout" class="btn btn-danger">Logout</a>
</div>
