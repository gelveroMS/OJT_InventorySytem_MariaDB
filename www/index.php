<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = hash('sha256', $_POST['password']);

  $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
  $result = $conn->query($sql);

  if ($result->num_rows === 1) {
    $_SESSION['username'] = $username;
    header("Location: dashboard.php");
    exit();
  } else {
    $error = "Invalid username or password!";
  }
}
?>

<h2>Login</h2>
<form method="POST">
  <input type="text" name="username" placeholder="Username" required><br><br>
  <input type="password" name="password" placeholder="Password" required><br><br>
  <input type="submit" value="Login">
</form>

<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
