<div class="card" style="max-width:500px; margin:0 auto;">
  <h2>Register</h2>
  <form method="POST" action="actions.php">
    <div class="form-group">
      <label for="name">Full Name</label>
      <input type="text" id="name" name="name" required>
    </div>
    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>
    </div>
    <div class="form-group">
      <label for="confirm">Confirm Password</label>
      <input type="password" id="confirm" name="confirm" required>
    </div>
    <div class="form-group">
      <label for="farmerID">Farmer ID (optional, for verification)</label>
      <input type="text" id="farmerID" name="farmerID" placeholder="Enter your Farmer ID">
    </div>
    <button type="submit" name="register" class="btn btn-primary" style="width:100%;">Register</button>
  </form>
</div>

<p style="text-align:center; margin-top:1rem;">
  Already have an account? <a href="index.php?page=login">Login here</a>
</p>
