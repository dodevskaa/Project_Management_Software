<?php
require_once __DIR__.'/../auth.php';
require_once __DIR__.'/../db.php';
require_once __DIR__.'/../controllers/tasks.php';

requireLogin();
$user = currentUser();

$data = json_decode(file_get_contents('php://input'), true);
$taskId = $data['taskId'] ?? null;
$title = $data['title'] ?? '';
$description = $data['description'] ?? '';

if(!$taskId){ echo json_encode(['success'=>false,'error'=>'Missing task ID']); exit; }

$stmt = $pdo->prepare("SELECT project_id FROM tasks WHERE id=?");
$stmt->execute([$taskId]);
$task = $stmt->fetch();

if(!$task){ echo json_encode(['success'=>false,'error'=>'Task not found']); exit; }

$project_id = $task['project_id'];

if(!isAdmin() && !isProjectTeamLead($project_id,$user['id'])){
    echo json_encode(['success'=>false,'error'=>'Not authorized']);
    exit;
}

$stmt = $pdo->prepare("UPDATE tasks SET title=?, description=? WHERE id=?");
$res = $stmt->execute([$title,$description,$taskId]);

echo json_encode(['success'=>$res]);
