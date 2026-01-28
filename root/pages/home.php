<main class="page-main dashboard-page" id="main-content" role="main">
  <header class="dashboard-header">
    <div class="welcome-section">
      <h1 class="dashboard-title">Welcome back, <?= htmlspecialchars($name) ?>!</h1>
      <div class="user-status">
        <?php if($farmerVerified): ?>
          <span class="status-badge status-verified" aria-label="Verified farmer account">
            <span aria-hidden="true">✅</span> Verified Farmer
          </span>
        <?php else: ?>
          <span class="status-badge status-unverified" aria-label="Unverified account">
            <span aria-hidden="true">⏳</span> Not Verified
          </span>
          <a href="index.php?page=profile" class="verification-link" aria-label="Complete verification process">
            Complete verification →
          </a>
        <?php endif; ?>
      </div>
    </div>
    <div class="dashboard-actions">
      <a href="index.php?page=listing" class="btn btn-primary" aria-label="Add new product listing">
        <span class="btn-icon" aria-hidden="true">➕</span>
        Add Product
      </a>
    </div>
  </header>

  <section class="dashboard-stats" aria-labelledby="stats-title">
    <h2 id="stats-title" class="sr-only">Dashboard Statistics</h2>
    <div class="stats-grid">
      <article class="stat-card">
        <div class="stat-icon" aria-hidden="true">📦</div>
        <div class="stat-content">
          <div class="stat-number">12</div>
          <div class="stat-label">Active Listings</div>
        </div>
      </article>
      
      <article class="stat-card">
        <div class="stat-icon" aria-hidden="true">🛒</div>
        <div class="stat-content">
          <div class="stat-number">45</div>
          <div class="stat-label">Orders This Month</div>
        </div>
      </article>
      
      <article class="stat-card">
        <div class="stat-icon" aria-hidden="true">💰</div>
        <div class="stat-content">
          <div class="stat-number">EC$1,250</div>
          <div class="stat-label">Monthly Revenue</div>
        </div>
      </article>
      
      <article class="stat-card">
        <div class="stat-icon" aria-hidden="true">⭐</div>
        <div class="stat-content">
          <div class="stat-number">4.8</div>
          <div class="stat-label">Customer Rating</div>
        </div>
      </article>
    </div>
  </section>

  <div class="dashboard-content">
    <section class="quick-actions" aria-labelledby="actions-title">
      <h2 id="actions-title">Quick Actions</h2>
      <div class="actions-grid">
        <article class="action-card">
          <div class="action-icon" aria-hidden="true">🌾</div>
          <div class="action-content">
            <h3>Start Listing</h3>
            <p>Add your fresh produce to the marketplace and reach local customers</p>
          </div>
          <div class="action-footer">
            <a href="index.php?page=listing" class="btn btn-primary" aria-label="Create new product listing">
              Start Listing
            </a>
          </div>
        </article>
        
        <article class="action-card">
          <div class="action-icon" aria-hidden="true">📊</div>
          <div class="action-content">
            <h3>My Listings</h3>
            <p>Manage your active products, update prices, and track inventory</p>
          </div>
          <div class="action-footer">
            <a href="index.php?page=sell" class="btn btn-secondary" aria-label="View and manage product listings">
              View Listings
            </a>
          </div>
        </article>
        
        <article class="action-card">
          <div class="action-icon" aria-hidden="true">📦</div>
          <div class="action-content">
            <h3>Orders</h3>
            <p>Check new orders, manage deliveries, and track your sales</p>
          </div>
          <div class="action-footer">
            <a href="index.php?page=orders" class="btn btn-secondary" aria-label="View order history and manage deliveries">
              View Orders
            </a>
          </div>
        </article>
        
        <article class="action-card">
          <div class="action-icon" aria-hidden="true">💬</div>
          <div class="action-content">
            <h3>Messages</h3>
            <p>Communicate with customers, answer questions, and build relationships</p>
          </div>
          <div class="action-footer">
            <a href="index.php?page=messages" class="btn btn-secondary" aria-label="View and respond to customer messages">
              View Messages
            </a>
          </div>
        </article>
        
        <article class="action-card">
          <div class="action-icon" aria-hidden="true">🛍️</div>
          <div class="action-content">
            <h3>Browse Market</h3>
            <p>Explore what other farmers are selling and discover market trends</p>
          </div>
          <div class="action-footer">
            <a href="index.php?page=browse" class="btn btn-secondary" aria-label="Browse marketplace products">
              Browse Products
            </a>
          </div>
        </article>
        
        <article class="action-card">
          <div class="action-icon" aria-hidden="true">👤</div>
          <div class="action-content">
            <h3>Profile Settings</h3>
            <p>Update your information, manage verification, and customize preferences</p>
          </div>
          <div class="action-footer">
            <a href="index.php?page=profile" class="btn btn-secondary" aria-label="Edit profile and account settings">
              Edit Profile
            </a>
          </div>
        </article>
      </div>
    </section>

    <aside class="dashboard-sidebar" role="complementary" aria-labelledby="tips-title">
      <section class="tips-card">
        <h2 id="tips-title">Farmer Tips</h2>
        <div class="tips-list">
          <article class="tip-item">
            <div class="tip-icon" aria-hidden="true">📸</div>
            <div class="tip-content">
              <h3>Great Photos Sell</h3>
              <p>High-quality photos increase sales by up to 40%. Show your produce in natural lighting.</p>
            </div>
          </article>
          
          <article class="tip-item">
            <div class="tip-icon" aria-hidden="true">💬</div>
            <div class="tip-content">
              <h3>Quick Responses</h3>
              <p>Respond to customer messages within 2 hours to build trust and increase sales.</p>
            </div>
          </article>
          
          <article class="tip-item">
            <div class="tip-icon" aria-hidden="true">🏷️</div>
            <div class="tip-content">
              <h3>Competitive Pricing</h3>
              <p>Check market prices regularly and adjust your listings to stay competitive.</p>
            </div>
          </article>
        </div>
      </section>

      <section class="recent-activity">
        <h2>Recent Activity</h2>
        <div class="activity-list">
          <article class="activity-item">
            <div class="activity-icon" aria-hidden="true">🛒</div>
            <div class="activity-content">
              <p><strong>New order</strong> for Organic Mangoes</p>
              <time datetime="2024-01-15T10:30:00">2 hours ago</time>
            </div>
          </article>
          
          <article class="activity-item">
            <div class="activity-icon" aria-hidden="true">💬</div>
            <div class="activity-content">
              <p><strong>Message</strong> from Sarah about tomatoes</p>
              <time datetime="2024-01-15T09:15:00">3 hours ago</time>
            </div>
          </article>
          
          <article class="activity-item">
            <div class="activity-icon" aria-hidden="true">📦</div>
            <div class="activity-content">
              <p><strong>Listed</strong> Fresh Coconuts</p>
              <time datetime="2024-01-15T08:00:00">5 hours ago</time>
            </div>
          </article>
        </div>
      </section>
    </aside>
  </div>
</main>
