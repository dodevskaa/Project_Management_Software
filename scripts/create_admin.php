<?php
require_once __DIR__.'/../src/db.php';

function create_admin($name,$email,$pass){ 
    global $pdo; 
    $hash = password_hash($pass,PASSWORD_DEFAULT); 
    $stmt=$pdo->prepare("INSERT INTO users (name,email,password,level,approved) VALUES (?,?,?,?,1)"); 
    $stmt->execute([$name,$email,$hash,'Admin']); echo "Admin created\n"; }
create_admin($argv[1] ?? 'Admin', $argv[2] ?? 'admin@example.com', $argv[3] ?? 'password');
