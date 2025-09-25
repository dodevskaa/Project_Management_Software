<?php
require_once __DIR__.'/../src/auth.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
  $ok = login($_POST['email_or_name'], $_POST['password']);
  if($ok) header('Location: dashboard.php');
  else $error = 'Invalid user or you are not approved.';
}
?>

<!doctype html>
<html>
  <head>
    <meta charset='utf-8'>
    <title>Login</title>
    <link rel='stylesheet' href='css/styles.css'>
  </head>
  <body>
<div class='card'>
  <h2>Login</h2>
  <?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>

<form method='post' class="login-form">
  <input name='email_or_name' placeholder='Email or username' required>
  <input name='password' type='password' placeholder='Password' required>
  <button type='submit' class="btn-login">Log in</button>
</form>
<p>Don't have an account? <a href='register.php'>Signup</a></p>
<p>Forgot password? <a href='forgot_password.php' class="btn-change-password">Reset Password</a></p>


<style>
.login-form {
  display: flex;
  flex-direction: column;
  gap: 10px;
  max-width: 300px;
  margin: auto;
}

.login-form input {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}

.btn-login {
  background-color: #007BFF;
  color: white;
  padding: 10px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
  font-weight: bold;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-login:hover {
  background-color: #0056b3;
  transform: translateY(-2px);
}
</style>


</body>
</html>
