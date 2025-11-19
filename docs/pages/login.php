<div class="card" style="max-width: 500px; margin: 0 auto;">
  <h2>Login</h2>
  <form method="POST" action="actions.php">
    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" placeholder="you@example.com" required>
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" placeholder="Password" required>
    </div>
    <div class="form-group">
      <label>Account Type</label>
      <select name="userType" required>
        <option value="">Select Account Type</option>
        <option value="farmer">Farmer</option>
        <option value="consumer">Consumer</option>
      </select>
    </div>
    <button type="submit" name="login" class="btn btn-primary" style="width:100%;">Login</button>
  </form>
  <p style="text-align:center; margin-top:1rem;">
    Don't have an account? <a href="index.php?page=register">Register here</a>
  </p>
</div>
