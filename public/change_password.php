<?php
require_once __DIR__.'/../src/auth.php';
require_once __DIR__.'/../src/db.php';

requireLogin(); 
$user = currentUser();
if(!$user){
    header("Location: login.php");
    exit;
}

$message = '';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $oldPassword = $_POST['old_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id=?");
    $stmt->execute([$user['id']]);
    $dbUser = $stmt->fetch();

    if(!$dbUser || !password_verify($oldPassword, $dbUser['password'])){
        $message = "Old password is incorrect.";
    } else {
        
        if(strlen($newPassword) < 6){
            $message = "New password must be at least 6 characters long.";
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->execute([$hashedPassword, $user['id']]);
            $message = "Password changed successfully!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <style>
        main {
            max-width: 420px;
            margin: 40px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            font-family: Arial, sans-serif;
        }
        h2 { margin-bottom: 16px; }
        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        button {
            padding: 10px 14px;
            background: #007BFF;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background: #0056b3;
        }
        .msg { color: green; margin-bottom: 10px; }
        .err { color: red; margin-bottom: 10px; }
    </style>
</head>
<body>
<main>
    <h2>Change Password</h2>
    <?php if($message): ?>
        <p class="<?= strpos($message,'successfully')!==false ? 'msg':'err' ?>">
            <?=htmlspecialchars($message)?>
        </p>
    <?php endif; ?>

    <form method="post">
        <input type="password" name="old_password" placeholder="Old password" required>
        <input type="password" name="new_password" placeholder="New password" required>
        <button type="submit">Change Password</button>
    </form>

    <p style="margin-top:12px"><a href="dashboard.php">Back to dashboard</a></p>
</main>
</body>
</html>
