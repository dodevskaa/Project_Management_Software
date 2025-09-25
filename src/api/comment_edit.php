<?php
header('Content-Type: application/json');

require_once __DIR__.'/../auth.php'; 
require_once __DIR__.'/../controllers/comments.php'; 

$user = currentUser(); 
if (!$user) {
    echo json_encode(['error' => 'Not logged in']); 
    exit; 
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    editComment($data['comment_id'], $user['id'], $data['content']);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
