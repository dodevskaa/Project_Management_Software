<?php
header('Content-Type: application/json'); 
require_once __DIR__.'/../auth.php'; 
require_once __DIR__.'/../db.php';

$user = currentUser();
if(!$user){ echo json_encode(['error'=>'Not logged in']); exit; }

$data = json_decode(file_get_contents('php://input'), true);
$projectId = $data['project_id'] ?? null;
$memberId = $data['member_id'] ?? null;

if(!$projectId || !$memberId){ 
    echo json_encode(['error'=>'Missing parameters']); 
    exit; 
}

// Check permissions
if(!isAdmin() && !isProjectTeamLead($projectId, $user['id'])){
    echo json_encode(['error'=>'No permission']);
    exit;
}

// Check member level
$stmt = $pdo->prepare("SELECT level FROM users WHERE id=?");
$stmt->execute([$memberId]);
$level = $stmt->fetchColumn();

// TeamLead can only remove non-TeamLead members
if(!isAdmin() && $level === 'TeamLead'){
    echo json_encode(['error'=>'Cannot remove another Team Lead']);
    exit;
}

// Perform deletion
$stmt = $pdo->prepare("DELETE FROM project_members WHERE project_id=? AND user_id=?");
$stmt->execute([$projectId, $memberId]);

echo json_encode(['success'=>true]);
