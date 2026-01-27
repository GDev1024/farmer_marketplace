<div class="card" style="max-width: 500px; margin: 0 auto;">
  <h2>Login</h2>
  <form method="POST" action="actions.php">
    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="you@example.com" required>
    </div>
    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Password" required>
    </div>
    <button type="submit" name="login" class="btn btn-primary" style="width:100%;">Login</button>
  </form>
  <p style="text-align:center; margin-top:1rem;">
    Don't have an account? <a href="index.php?page=register">Register here</a>
  </p>
</div>
