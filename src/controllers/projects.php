<?php
require_once __DIR__.'/../db.php';
require_once __DIR__.'/../middleware.php';

function createProject($data){ 
    global $pdo; 
    $stmt=$pdo->prepare("INSERT INTO projects (title,description,requirements,estimated_time,team_lead_id,deadline) VALUES (?,?,?,?,?,?)"); 
    $stmt->execute([$data['title'],
    $data['description'],
    $data['requirements'],
    $data['estimated_time'],
    $data['team_lead_id']?:null,
    $data['deadline']?:null]); 
    return $pdo->lastInsertId(); }

function updateProject($id,$data){ 
    global $pdo; 
    $stmt=$pdo->prepare("UPDATE projects SET title=?,description=?,requirements=?,estimated_time=?,team_lead_id=?,deadline=?,status=? WHERE id=?"); 
    $stmt->execute([$data['title'],$data['description'],
    $data['requirements'],$data['estimated_time'],
    $data['team_lead_id']?:null,$data['deadline']?:null,
    $data['status'],
    $id]); }

function addMemberToProject($projectId,$userId,$role=null){ 
    global $pdo; 
    $stmt=$pdo->prepare("INSERT INTO project_members (project_id,user_id,role_in_team) VALUES (?,?,?)"); 
    $stmt->execute([$projectId,$userId,$role]); }

function getProjectById($id){ global $pdo; 
    $stmt=$pdo->prepare("SELECT p.*, u.name as team_lead_name FROM projects p LEFT JOIN users u ON p.team_lead_id = u.id WHERE p.id = ?"); 
    $stmt->execute([$id]); return $stmt->fetch(); }

function getProjectsForUser($userId){ 
    global $pdo; $lvl=userLevel($userId); 
    if($lvl==='Admin'){ 
        $stmt=$pdo->query("SELECT * FROM projects ORDER BY created_at DESC"); 
        return $stmt->fetchAll(); } 
        $stmt=$pdo->prepare("SELECT DISTINCT p.* FROM projects p LEFT JOIN project_members pm ON p.id = pm.project_id WHERE p.team_lead_id = ? OR pm.user_id = ? ORDER BY p.created_at DESC"); 
        $stmt->execute([$userId,$userId]); 
        return $stmt->fetchAll(); }

function deleteProject($id){
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id=?");
    $stmt->execute([$id]);
    if(!$stmt->fetch()) throw new Exception("Project not found");

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("
            DELETE c FROM comments c
            INNER JOIN tasks t ON c.task_id = t.id
            WHERE t.project_id = ?
        ");
        $stmt->execute([$id]);

        $stmt = $pdo->prepare("DELETE FROM tasks WHERE project_id=?");
        $stmt->execute([$id]);

        $stmt = $pdo->prepare("DELETE FROM project_members WHERE project_id=?");
        $stmt->execute([$id]);

        $stmt = $pdo->prepare("DELETE FROM projects WHERE id=?");
        $stmt->execute([$id]);

        $pdo->commit();
    } catch(Exception $e){
        $pdo->rollBack();
        throw $e;
    }
}

