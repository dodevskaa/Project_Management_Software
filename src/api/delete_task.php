<?php
header('Content-Type: application/json'); 
require_once __DIR__.'/../auth.php';
require_once __DIR__.'/../controllers/tasks.php';
require_once __DIR__.'/../middleware.php';

$user = currentUser();
$data = json_decode(file_get_contents('php://input'), true);
$taskId = $data['task_id'] ?? null;

if(!$taskId){
    echo json_encode(['error'=>'Task ID missing']);
    exit;
}

// only teamLead or admin can delete
if(!isAdmin() && !isTaskProjectTeamLead($taskId, $user['id'])){
    echo json_encode(['error'=>'No permission']);
    exit;
}

try {
    deleteTask($taskId);
    echo json_encode(['success'=>true]);
} catch(Exception $e){
    echo json_encode(['error'=>$e->getMessage()]);
}
