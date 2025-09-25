<?php
require_once __DIR__.'/../src/db.php'; 

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_repeat = $_POST['password_repeat'];
    $level = $_POST['level'] ?? 'Junior';

    if($password !== $password_repeat){
        $error = "Passwords do not match!";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
        $stmt->execute([$email]);
        if($stmt->fetch()){
            $error = "Email already registered!";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, level, approved, created_at) VALUES (?,?,?,?,0,NOW())");
            $stmt->execute([$name, $email, $hashedPassword, $level]);
            $success = "Registration successful! Wait for admin approval.";
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Register</title>
  <style>
    * { box-sizing: border-box; font-family: Arial, sans-serif; }
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      /* background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); */
      font-family: Arial; 
            background-color: #f5f6fa; 
    }
    .card {
      background: #fff;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      width: 360px;
      text-align: center;
      animation: fadeIn 0.5s ease-in-out;
    }
    h2 {
      margin-bottom: 25px;
      color: #333;
      font-size: 24px;
    }
    .error, .success {
      padding: 12px;
      border-radius: 6px;
      margin-bottom: 15px;
      font-weight: bold;
      font-size: 14px;
    }
    .error { background: #ffdddd; color: #a33; }
    .success { background: #ddffdd; color: #3a3; }
    form input, form select {
      width: 100%;
      padding: 12px;
      margin: 8px 0;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
      transition: border 0.3s;
    }
    form input:focus, form select:focus {
      border-color: #2575fc;
      outline: none;
    }
    form button {
      width: 100%;
      padding: 14px;
      margin-top: 12px;
      border: none;
      border-radius: 6px;
      background-color: #2575fc;
      color: white;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }
    form button:hover { background-color: #1a5edb; }
    .login-link {
      display: inline-block;
      margin-top: 18px;
      text-decoration: none;
      color: #2575fc;
      font-weight: bold;
      transition: color 0.3s;
    }
    .login-link:hover { color: #1a5edb; }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px);}
      to { opacity: 1; transform: translateY(0);}
    }
  </style>
</head>
<body>
  <div class='card'>
    <h2>Registration</h2>

    <?php 
      if(!empty($error)) echo "<div class='error'> $error</div>"; 
      if(!empty($success)) echo "<div class='success'>$success</div>"; 
    ?>

    <form method='post'>
      <input name='name' required placeholder='Full Name'>
      <input name='email' type='email' required placeholder='Email'>
      <input name='password' type='password' required placeholder='Password'>
      <input name='password_repeat' type='password' required placeholder='Repeat Password'>
      <select name='level'>
        <option>Junior</option>
        <option>Mid</option>
        <option>Senior</option>
      </select>
      <button type='submit'>Signup</button>
    </form>

    <a class="login-link" href="login.php">Back to Login</a>
  </div>
</body>
</html>
