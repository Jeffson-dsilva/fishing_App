<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login</title>
  <link rel="stylesheet" href="assets/css/index.css" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Roboto', sans-serif;
      background-color: #f4f6f9;
      display: flex;
      height: 100vh;
    }

    .login-grid {
      display: grid;
      grid-template-columns: 4fr 6fr;
      width: 100%;
      height: 100vh;
    }

    .login-left {
      background: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px;
    }

    .login-box {
      width: 100%;
      max-width: 400px;
    }

    .logo-icon {
      font-size: 48px;
      color: #007bff;
      display: block;
      text-align: center;
      margin-bottom: 20px;
    }

    .login-title {
      text-align: center;
      font-size: 24px;
      margin-bottom: 30px;
      color: #333;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 6px;
      color: #555;
    }

    .form-group input {
      width: 100%;
      padding: 10px;
      border: 1px solid #007bff;
      border-radius: 6px;
      font-size: 14px;
    }

    .login-btn {
      width: 100%;
      background-color: #007bff;
      color: #fff;
      padding: 12px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .login-btn:hover {
      background-color: #0056b3;
    }

    .error-msg {
      color: red;
      text-align: center;
      margin-bottom: 15px;
    }

    .login-right {
      background: url('assets/images/admin-login.jpeg') no-repeat center center;
      background-size: cover;
    }

  </style>
</head>

<body>
  <div class="login-grid">
    <div class="login-left">
      <div class="login-box">
        <span class="material-icons logo-icon">anchor</span>
        <h2 class="login-title">Admin Login</h2>

        <?php if (isset($_SESSION['error'])): ?>
          <p class="error-msg"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form action="auth.php" method="POST">
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required />
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required />
          </div>

          <button class="login-btn" type="submit">Login</button>
        </form>
      </div>
    </div>

    <div class="login-right">
      <!-- Background image shown here -->
    </div>
  </div>
</body>

</html>
