<?php
header('Content-Type: application/json'); 

require_once __DIR__.'/../auth.php'; 
require_once __DIR__.'/../controllers/projects.php'; 

$user=currentUser(); 
if(!$user){ echo json_encode(['error'=>'Not logged in']); 
    exit; } 
    $d=json_decode(file_get_contents('php://input'),true); 
    try{ // only team lead of project or admin can add members
 require_once __DIR__.'/../middleware.php'; 
 if(!isProjectTeamLead($d['project_id'],
 $user['id']) && !isAdmin()) throw new Exception('No permission'); 
 addMemberToProject($d['project_id'],
 $d['user_id'],
 $d['role']); 
 echo json_encode(['success'=>true]); } 
 catch(Exception $e){ 
    echo json_encode(['error'=>$e->getMessage()]); 
}
