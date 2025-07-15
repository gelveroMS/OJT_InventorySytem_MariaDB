<?php
session_start();
include 'config.php';

$username = '';
$username_error = '';
$password_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    if ($password === $row['password']) {
      $_SESSION['user'] = $row['username'];
      header("Location: dashboard.php");
      exit;
    } else {
      $password_error = "❌ Incorrect password.";
    }
  } else {
    $username_error = "❌ Username not found.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Inventory System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #fff;
    }

    .login-wrapper {
      display: flex;
      height: 100vh;
    }

    .login-left {
      width: 35%;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #E8F2F9;
    }

    .login-box {
      background-color: #FFFFFF;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      width: 100%;
      max-width: 500px;
    }

    .logo-img {
      display: block;
      margin: 0 auto 25px;
      max-width: 100%;
      height: auto;
    }

    .form-control {
      height: 44px;
      border-radius: 8px;
    }

    .btn-primary {
      width: 100%;
      padding: 10px;
      background-color: #002366;
      border: none;
      border-radius: 8px;
      transition: 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #0135a5;
    }

    .form-label {
      font-weight: 600;
      color: #222;
    }

    .login-right {
      width: 65%;
      background: url('pp.jpg') no-repeat center center;
      background-size: cover;
      position: relative;
    }

    .login-right::before {
      content: "";
      position: absolute;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 35, 102, 0.75); /* translucent navy overlay */
    }

    @media (max-width: 768px) {
      .login-wrapper {
        flex-direction: column;
      }

      .login-left,
      .login-right {
        width: 100%;
        height: 50vh;
      }

      .login-box {
        padding: 30px;
      }
    }
  </style>
</head>
<body>

<div class="login-wrapper">
  <div class="login-left">
    <div class="login-box">
      <img src="shindengen_logo.png" alt="ShinDengen Corp Philippines" class="logo-img">

      <form method="POST" novalidate>
        <div class="mb-3">
          <label class="form-label">User:</label>
          <input type="text" name="username" class="form-control <?= $username_error ? 'is-invalid' : '' ?>" 
                 value="<?= htmlspecialchars($username) ?>" required>
          <?php if ($username_error): ?>
            <div class="invalid-feedback"><?= $username_error ?></div>
          <?php endif; ?>
        </div>

        <div class="mb-3">
          <label class="form-label">Password:</label>
          <input type="password" name="password" class="form-control <?= $password_error ? 'is-invalid' : '' ?>" required>
          <?php if ($password_error): ?>
            <div class="invalid-feedback"><?= $password_error ?></div>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
      </form>
    </div>
  </div>

  <div class="login-right"></div>
</div>

</body>
</html>
