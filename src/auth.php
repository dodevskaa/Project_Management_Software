<?php
session_start();
require_once __DIR__.'/db.php';
function registerUser($name,$email,$password,$level='Junior'){
    global $pdo;
    $hash=password_hash($password,PASSWORD_DEFAULT);
    $stmt=$pdo->prepare("INSERT INTO users (name,email,password,level,approved) VALUES (?,?,?,?,0)");
    $stmt->execute([$name,$email,$hash,$level]);
    return $pdo->lastInsertId();
}
function createAdminDirect($name,$email,$password){
    global $pdo;
    $hash=password_hash($password,PASSWORD_DEFAULT);
    $stmt=$pdo->prepare("INSERT INTO users (name,email,password,level,approved) VALUES (?,?,?,'Admin',1)");
    $stmt->execute([$name,$email,$hash]);
}
function login($emailOrName,$password){
    global $pdo;
    $stmt=$pdo->prepare("SELECT * FROM users WHERE email = ? OR name = ?");
    $stmt->execute([$emailOrName,$emailOrName]);
    $user=$stmt->fetch();
    if($user && $user['approved'] && password_verify($password,$user['password'])){
        session_regenerate_id(true);
        $_SESSION['user']=['id'=>$user['id'],'name'=>$user['name'],'level'=>$user['level']];
        return true;
    }
    return false;
}
function logout(){ 
    session_unset(); 
    session_destroy(); }

function currentUser(){ 
    return $_SESSION['user'] ?? null; }

function requireLogin(){ 
    if(!currentUser()){ 
        header('Location: login.php'); 
        exit; } }

function isAdmin(){ 
    $u=currentUser(); 
    return $u && $u['level']==='Admin'; }
