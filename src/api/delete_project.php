<?php
header('Content-Type: application/json'); 
require_once __DIR__.'/../auth.php';
require_once __DIR__.'/../controllers/projects.php';

$user = currentUser();
if(!$user || !isAdmin()){
    echo json_encode(['error'=>'Only admin can delete projects']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$projectId = $data['project_id'] ?? null;

if(!$projectId){
    echo json_encode(['error'=>'Project ID missing']);
    exit;
}

try {
    deleteProject($projectId); 
    echo json_encode(['success'=>true]);
} catch(Exception $e){
    echo json_encode(['error'=>$e->getMessage()]);
}
