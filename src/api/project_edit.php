<?php
require_once __DIR__.'/../auth.php';
require_once __DIR__.'/../db.php';
require_once __DIR__.'/../controllers/projects.php';

requireLogin();
if(!isAdmin()){ echo json_encode(['success'=>false,'error'=>'Not authorized']); exit; }

$data = json_decode(file_get_contents('php://input'), true);
$pid = $data['pid'] ?? null;
$title = $data['title'] ?? '';
$description = $data['description'] ?? '';
$requirements = $data['requirements'] ?? '';
$estimated_time = $data['estimated_time'] ?? '';
$deadline = $data['deadline'] ?? null;
$team_lead_id = $data['team_lead_id'] ?? null;

if(!$pid){ echo json_encode(['success'=>false,'error'=>'Missing project ID']); exit; }

$stmt = $pdo->prepare("UPDATE projects SET title=?, description=?, requirements=?, estimated_time=?, deadline=?, team_lead_id=? WHERE id=?");
$res = $stmt->execute([$title,$description,$requirements,$estimated_time,$deadline,$team_lead_id,$pid]);

echo json_encode(['success'=>$res]);
