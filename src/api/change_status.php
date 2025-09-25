<?php
header('Content-Type: application/json'); 

require_once __DIR__.'/../auth.php'; 
require_once __DIR__.'/../controllers/tasks.php'; 

$data = json_decode(file_get_contents('php://input'), true);
$user = currentUser();

if(!$user){
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

try {
    changeTaskStatus($data['task_id'], $user['id'], $data['status']);
    echo json_encode(['success' => true]);
} catch(Exception $e){
    echo json_encode(['error' => $e->getMessage()]);
}
