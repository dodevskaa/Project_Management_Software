<?php
header('Content-Type: application/json'); 
require_once __DIR__.'/../auth.php'; 
require_once __DIR__.'/../controllers/tasks.php'; 

$user=currentUser(); 
if(!$user){ 
    echo json_encode(['error'=>'Not logged in']); exit; } 
    $data=json_decode(file_get_contents('php://input'),true); 
    try{ $id = createTask($data['project_id'],$data['title'],$data['description'],$user['id']); 
        echo json_encode(['success'=>true,'id'=>$id]); } 
        catch(Exception $e){ 
            echo json_encode(['error'=>$e->getMessage()]); }
