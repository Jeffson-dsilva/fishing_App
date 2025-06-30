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
      margin: 0;
      padding: 0;
    }

    body {
      margin: 0;
      font-family: 'Roboto', sans-serif;
      background-color: #f4f6f9;
      height: 100vh;
      overflow: hidden;
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
      color: #3674B5;
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
      border: 1px solid #3674B5;
      border-radius: 6px;
      font-size: 14px;
    }

    .login-btn {
      width: 100%;
      background-color: #3674B5;
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

    /* Preloader Styles (Cosmic Style) */
    .preloader {
      width: 100vw;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      background: #3674B5;
      z-index: 9999;
    }

    .site-name {
      width: 100%;
      height: 100%;
      text-align: center;
      align-content: center;
      position: absolute;
      top: 0;
      left: 0;
      z-index: 1;
      background-color: #3674B5;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .site-name span {
      font-size: 60px;
      font-weight: 800;
      color: #fff;
      text-transform: uppercase;
    }

    .preloader-gutters {
      background-color: transparent;
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 2;
      display: flex;
    }

    .preloader-gutters .bar {
      width: 12.5%;
      height: 100%;
      background: transparent;
      position: relative;
    }

    .preloader-gutters .inner-bar {
      position: absolute;
      top: 0;
      left: 0;
      width: 0%;
      height: 100%;
      background-color:rgb(9, 66, 127);
    }

    .preloader-overlay {
      background-color: #3674B5;
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 3;
      transform: translateX(-100%);
    }
  </style>
</head>

<body>

<!-- Preloader -->
<div class="preloader">
  <div class="site-name">
    <span>FISHERNET</span>
  </div>
  <div class="preloader-gutters">
    <div class="bar"><div class="inner-bar"></div></div>
    <div class="bar"><div class="inner-bar"></div></div>
    <div class="bar"><div class="inner-bar"></div></div>
    <div class="bar"><div class="inner-bar"></div></div>
    <div class="bar"><div class="inner-bar"></div></div>
    <div class="bar"><div class="inner-bar"></div></div>
    <div class="bar"><div class="inner-bar"></div></div>
    <div class="bar"><div class="inner-bar"></div></div>
  </div>
  <div class="preloader-overlay"></div>
</div>

<!-- Login Content -->
<div class="login-grid" style="opacity: 0; transform: translateY(20px);">
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

  <div class="login-right"></div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
// Preloader Animation Logic
const innerBars = document.querySelectorAll(".inner-bar");
let increment = 0;

function animateBars() {
  for (let i = 0; i < 2; i++) {
    let randomWidth = Math.floor(Math.random() * 101);
    gsap.to(innerBars[i + increment], {
      width: `${randomWidth}%`,
      duration: 0.2,
      ease: "none"
    });
  }

  setTimeout(() => {
    for (let i = 0; i < 2; i++) {
      gsap.to(innerBars[i + increment], {
        width: "100%",
        duration: 0.2,
        ease: "none"
      });
    }

    increment += 2;
    if (increment < innerBars.length) {
      animateBars();
    } else {
      const preloaderTl = gsap.timeline();
      preloaderTl.to(".preloader-overlay", {
        transform: "translateX(0)",
        duration: 0.5,
        delay: 0.4
      });
      preloaderTl.to(".preloader", {
        display: "none",
        duration: 0
      });
      preloaderTl.to(".login-grid", {
        opacity: 1,
        y: 0,
        duration: 0.5,
        ease: "power2.out"
      });
      document.body.style.overflow = "auto";
    }
  }, 200);
}

window.onload = () => {
  setTimeout(animateBars, 1000);
};
</script>

</body>
</html>
