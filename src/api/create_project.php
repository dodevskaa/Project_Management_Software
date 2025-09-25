<?php
header('Content-Type: application/json'); 
require_once __DIR__.'/../auth.php'; 
require_once __DIR__.'/../controllers/projects.php'; 

$user = currentUser(); 
if(!$user || !isAdmin()){ 
    echo json_encode(['error'=>'Only admin']); 
    exit; } $data = json_decode(file_get_contents('php://input'), true); 
    try{ $id = createProject($data); 
        echo json_encode(['success'=>true,'id'=>$id]); } 
        catch(Exception $e){ 
            echo json_encode(['error'=>$e->getMessage()]); }
