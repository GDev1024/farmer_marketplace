<div class="page page--profile">
  <main class="page__main">
    <div class="page__header">
      <div class="page__title-section">
        <h1 class="page__title">
          <span class="page__title-icon" aria-hidden="true">👤</span>
          Your Profile
        </h1>
        <p class="page__subtitle">
          Manage your account information and settings
        </p>
      </div>
    </div>

    <div class="profile-content">
      <div class="profile-card">
        <header class="profile-card__header">
          <div class="profile-card__avatar" aria-hidden="true">
            <?= strtoupper(substr($name, 0, 1)) ?>
          </div>
          <div class="profile-card__info">
            <h2 class="profile-card__name"><?= htmlspecialchars($name) ?></h2>
            <div class="profile-card__verification">
              <?php if($farmerVerified): ?>
                <span class="verification-badge verification-badge--verified">
                  <span class="verification-badge__icon" aria-hidden="true">✅</span>
                  <span class="verification-badge__text">Verified Farmer</span>
                </span>
              <?php else: ?>
                <span class="verification-badge verification-badge--unverified">
                  <span class="verification-badge__icon" aria-hidden="true">❌</span>
                  <span class="verification-badge__text">Not Verified</span>
                </span>
              <?php endif; ?>
            </div>
          </div>
        </header>

        <div class="profile-card__body">
          <?php if(!$farmerVerified): ?>
            <div class="profile-notice">
              <div class="profile-notice__icon" aria-hidden="true">ℹ️</div>
              <div class="profile-notice__content">
                <h3 class="profile-notice__title">Get Verified</h3>
                <p class="profile-notice__description">
                  Submit your Farmer ID to get verified and gain access to additional features.
                </p>
                <button class="btn btn--primary btn--small" onclick="openVerificationModal()">
                  Submit Farmer ID
                </button>
              </div>
            </div>
          <?php endif; ?>

          <div class="profile-stats">
            <h3 class="profile-stats__title">Account Overview</h3>
            <div class="profile-stats__grid">
              <div class="profile-stats__item">
                <div class="profile-stats__value">
                  <?php
                  // Get user's listing count
                  $stmt = $pdo->prepare("SELECT COUNT(*) FROM listings WHERE user_id = ?");
                  $stmt->execute([$_SESSION['userId']]);
                  echo $stmt->fetchColumn();
                  ?>
                </div>
                <div class="profile-stats__label">Products Listed</div>
              </div>
              <div class="profile-stats__item">
                <div class="profile-stats__value">
                  <?php
                  // Get active listings count
                  $stmt = $pdo->prepare("SELECT COUNT(*) FROM listings WHERE user_id = ? AND is_active = 1");
                  $stmt->execute([$_SESSION['userId']]);
                  echo $stmt->fetchColumn();
                  ?>
                </div>
                <div class="profile-stats__label">Active Listings</div>
              </div>
              <div class="profile-stats__item">
                <div class="profile-stats__value">
                  <?php
                  // Get orders count (as buyer)
                  $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
                  $stmt->execute([$_SESSION['userId']]);
                  echo $stmt->fetchColumn();
                  ?>
                </div>
                <div class="profile-stats__label">Orders Placed</div>
              </div>
            </div>
          </div>

          <div class="profile-actions">
            <h3 class="profile-actions__title">Account Actions</h3>
            <div class="profile-actions__grid">
              <a href="index.php?page=sell" class="profile-action-card">
                <div class="profile-action-card__icon" aria-hidden="true">📦</div>
                <div class="profile-action-card__content">
                  <h4 class="profile-action-card__title">Manage Listings</h4>
                  <p class="profile-action-card__description">View and edit your product listings</p>
                </div>
              </a>
              
              <a href="index.php?page=orders" class="profile-action-card">
                <div class="profile-action-card__icon" aria-hidden="true">📋</div>
                <div class="profile-action-card__content">
                  <h4 class="profile-action-card__title">Order History</h4>
                  <p class="profile-action-card__description">Track your purchases and sales</p>
                </div>
              </a>
              
              <a href="index.php?page=messages" class="profile-action-card">
                <div class="profile-action-card__icon" aria-hidden="true">💬</div>
                <div class="profile-action-card__content">
                  <h4 class="profile-action-card__title">Messages</h4>
                  <p class="profile-action-card__description">Communicate with buyers and sellers</p>
                </div>
              </a>
              
              <button onclick="openEditProfileModal()" class="profile-action-card profile-action-card--button">
                <div class="profile-action-card__icon" aria-hidden="true">✏️</div>
                <div class="profile-action-card__content">
                  <h4 class="profile-action-card__title">Edit Profile</h4>
                  <p class="profile-action-card__description">Update your account information</p>
                </div>
              </button>
            </div>
          </div>
        </div>

        <footer class="profile-card__footer">
          <a href="index.php?page=logout" class="btn btn--danger btn--with-icon">
            <span class="btn__icon" aria-hidden="true">🚪</span>
            <span class="btn__text">Logout</span>
          </a>
        </footer>
      </div>
    </div>
  </main>
</div>

<!-- Verification Modal -->
<div id="verificationModal" class="modal" style="display: none;" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="verificationModalTitle">
  <div class="modal__backdrop"></div>
  <div class="modal__content">
    <header class="modal__header">
      <h2 id="verificationModalTitle" class="modal__title">Submit Farmer ID</h2>
      <button class="modal__close" onclick="closeVerificationModal()" aria-label="Close verification dialog" type="button">
        <span aria-hidden="true">&times;</span>
      </button>
    </header>
    
    <form method="POST" action="actions.php" class="modal__form">
      <input type="hidden" name="submitFarmerVerification" value="1">
      
      <div class="form-group">
        <label for="farmerId" class="form-group__label">Farmer ID Number</label>
        <input type="text" 
               name="farmerId" 
               id="farmerId" 
               class="form-group__input" 
               required 
               aria-describedby="farmerIdHelp"
               placeholder="Enter your official Farmer ID">
        <small id="farmerIdHelp" class="form-group__help">Enter your official Farmer ID number for verification</small>
      </div>
      
      <div class="form-group">
        <label for="farmerName" class="form-group__label">Farm Name (Optional)</label>
        <input type="text" 
               name="farmerName" 
               id="farmerName" 
               class="form-group__input" 
               aria-describedby="farmerNameHelp"
               placeholder="Enter your farm name">
        <small id="farmerNameHelp" class="form-group__help">Optional: Enter your farm or business name</small>
      </div>
      
      <footer class="modal__actions">
        <button type="button" onclick="closeVerificationModal()" class="btn btn--secondary">Cancel</button>
        <button type="submit" class="btn btn--primary">Submit for Verification</button>
      </footer>
    </form>
  </div>
</div>

<!-- Edit Profile Modal -->
<div id="editProfileModal" class="modal" style="display: none;" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="editProfileModalTitle">
  <div class="modal__backdrop"></div>
  <div class="modal__content">
    <header class="modal__header">
      <h2 id="editProfileModalTitle" class="modal__title">Edit Profile</h2>
      <button class="modal__close" onclick="closeEditProfileModal()" aria-label="Close edit profile dialog" type="button">
        <span aria-hidden="true">&times;</span>
      </button>
    </header>
    
    <form method="POST" action="actions.php" class="modal__form">
      <input type="hidden" name="updateProfile" value="1">
      
      <div class="form-group">
        <label for="profileName" class="form-group__label">Full Name</label>
        <input type="text" 
               name="name" 
               id="profileName" 
               class="form-group__input" 
               value="<?= htmlspecialchars($name) ?>"
               required 
               aria-describedby="profileNameHelp">
        <small id="profileNameHelp" class="form-group__help">Enter your full name as you'd like it displayed</small>
      </div>
      
      <div class="form-group">
        <label for="profileEmail" class="form-group__label">Email Address</label>
        <input type="email" 
               name="email" 
               id="profileEmail" 
               class="form-group__input" 
               value="<?= htmlspecialchars($email ?? '') ?>"
               required 
               aria-describedby="profileEmailHelp">
        <small id="profileEmailHelp" class="form-group__help">Your email address for account notifications</small>
      </div>
      
      <footer class="modal__actions">
        <button type="button" onclick="closeEditProfileModal()" class="btn btn--secondary">Cancel</button>
        <button type="submit" class="btn btn--primary">Update Profile</button>
      </footer>
    </form>
  </div>
</div>

<script>
function openVerificationModal() {
  openModal('verificationModal', { focusFirst: true });
}

function closeVerificationModal() {
  closeModal('verificationModal');
}

function openEditProfileModal() {
  openModal('editProfileModal', { focusFirst: true });
}

function closeEditProfileModal() {
  closeModal('editProfileModal');
}

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    closeVerificationModal();
    closeEditProfileModal();
  }
});
</script>
