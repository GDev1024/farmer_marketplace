<div class="card" style="max-width:500px; margin:0 auto;">
  <h2>Register</h2>
  <form method="POST" action="actions.php">
    <div class="form-group">
      <label>Full Name</label>
      <input type="text" name="name" required>
    </div>
    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" required>
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" required>
    </div>
    <div class="form-group">
      <label>Confirm Password</label>
      <input type="password" name="confirm" required>
    </div>
    <div class="form-group">
      <label>Farmer ID (optional, for verification)</label>
      <input type="text" name="farmerID" placeholder="Enter your Farmer ID">
    </div>
    <button type="submit" name="register" class="btn btn-primary" style="width:100%;">Register</button>
  </form>
</div>

<p style="text-align:center; margin-top:1rem;">
  Already have an account? <a href="index.php?page=login">Login here</a>
</p>
