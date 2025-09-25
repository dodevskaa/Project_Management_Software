<?php
require_once __DIR__.'/../src/db.php';

$message = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $newPassword = $_POST['new_password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if(!$user){
        $message = "No user found with this email.";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password=? WHERE email=?");
        $stmt->execute([$hashedPassword, $email]);

        $message = "Password has been successfully changed!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body { 
            font-family: Arial; 
            background-color: #f5f6fa; 
        }
        main { 
            max-width: 400px; 
            margin: 50px auto; 
            padding: 20px; background: #fff; 
            border-radius: 8px; 
            box-shadow: 0 2px 6px rgba(0,0,0,0.1); 
        }
        input { 
            width: 100%; 
            padding: 8px; 
            margin: 10px 0; 
            border-radius: 4px; 
            border: 1px solid #ccc; 
        }
        button { 
            padding: 6px 12px; 
            border: none; 
            border-radius: 4px; 
            background-color: #28a745; 
            color: white; cursor: pointer; 
            font-weight: bold; 
        }
        button:hover { 
            background-color: #1e7e34; 
        }
        p.message { 
            font-weight: bold; 
            color: green; 
        }
        p.error { 
            font-weight: bold; 
            color: red; 
        }
    </style>
</head>
<body>
<main>
    <h2>Reset Password</h2>
    <?php if($message): ?>
        <p class="<?= strpos($message,'successfully')!==false ? 'message':'error' ?>"><?=htmlspecialchars($message)?></p>
    <?php endif; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Your email" required>
        <input type="password" name="new_password" placeholder="New password" required>
        <button type="submit">Change Password</button>
        <p>Back to <a href='login.php'>Login </a> Page </p>

    </form>
</main>
</body>
</html>
