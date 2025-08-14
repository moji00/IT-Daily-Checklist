<?php require 'config.php'; if (is_logged_in()) header('Location: ' . (is_admin() ? 'admin_dashboard.php' : 'user_dashboard.php')); ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/style.css" rel="stylesheet">
  <style>
    body {
      background: #f4f8fb;
      min-height: 100vh;
    }
    .card-ghost {
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
      border-radius: 18px;
      border: none;
    }
    .logo {
      width: 80px;
      height: 80px;
      object-fit: contain;
      margin-bottom: 12px;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }
  </style>
</head>
<body>
<div class="container" style="max-width:480px;margin-top:80px">
  <div class="card card-ghost p-4">
    <img src="assets/logo.png" alt="Hospital IT Logo" class="logo">
    <h3 class="text-center mb-1">IT Daily Checklist</h3>
    <p class="text-center text-muted mb-4">Sign in to access your daily checklist</p>
    <?php if(isset($_GET['error'])): ?>
      <div class="alert alert-danger"><?=htmlspecialchars($_GET['error'])?></div>
    <?php endif; ?>
    <form method="post" action="authenticate.php">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input name="email" type="email" class="form-control" placeholder="Enter your username" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input name="password" type="password" class="form-control" placeholder="Enter your password" required>
      </div>
      <button class="btn btn-primary w-100">Sign In</button>
    </form>
  </div>
</div>
</body>
</html>