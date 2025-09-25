<?php
require_once __DIR__.'/../db.php';
require_once __DIR__.'/../middleware.php';

function createTask($projectId,$title,$description,$creatorId){ 
   global $pdo; 
   if(!isProjectTeamLead($projectId,$creatorId) && !isAdmin()) 
       throw new Exception('No permission to create task for this project'); 

   $stmt=$pdo->prepare("INSERT INTO tasks (project_id,title,description,created_by,status) VALUES (?,?,?,?, 'Unassigned')"); 
   $stmt->execute([$projectId,$title,$description,$creatorId]); 
   return $pdo->lastInsertId(); 
}

function assignTask($taskId, $assignerId, $assigneeId){ 
   global $pdo; 
   $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?"); 
   $stmt->execute([$taskId]); 
   $task = $stmt->fetch(); 
   if (!$task) throw new Exception('Task not found'); 

   $projectId = $task['project_id']; 
   $assignerLevel = userLevel($assignerId);
   $assigneeLevel = userLevel($assigneeId);

   if (isProjectTeamLead($projectId, $assignerId) || isAdmin()) {
   } elseif ($assignerLevel === 'Senior') {
       if ($assigneeId != $assignerId && !in_array($assigneeLevel, ['Mid','Junior'])) {
           throw new Exception('Senior can assign only to themselves, Mid, or Junior');
       }
   } elseif ($assignerLevel === 'Mid') {
       if ($assigneeId != $assignerId && $assigneeLevel !== 'Junior') {
           throw new Exception('Mid can assign only to themselves or Junior');
       }
   } elseif ($assignerLevel === 'Junior') {
       throw new Exception('Junior cannot assign tasks');
   } else {
       throw new Exception('No permission to assign');
   }
   $stmt = $pdo->prepare("UPDATE tasks SET assigned_to = ?, status = ?, updated_at = NOW() WHERE id = ?");
   $newStatus = $assigneeId ? 'To Do' : $task['status']; 
   $stmt->execute([$assigneeId, $newStatus, $taskId]);
}



function changeTaskStatus($taskId,$userId,$newStatus){ 
   global $pdo; 
   $stmt=$pdo->prepare("SELECT * FROM tasks WHERE id = ?"); 
   $stmt->execute([$taskId]); 
   $task=$stmt->fetch(); 
   if(!$task) throw new Exception('Task not found'); 

   $projectId=$task['project_id']; 
   $userLvl=userLevel($userId);

   if(isProjectTeamLead($projectId,$userId) || isAdmin()){
       // full access
   } elseif($userLvl==='Senior'){
       if(!isProjectMember($projectId,$userId)) 
           throw new Exception('Senior must be project member to change status');
   } elseif($userLvl==='Mid'){
       if($task['assigned_to']==$userId){
       } elseif($task['assigned_to'] && userLevel($task['assigned_to'])==='Junior'){
       } else {
           throw new Exception('Mid can only change status of own tasks or tasks assigned to Junior');
       }
   } elseif($userLvl==='Junior'){
       if($task['assigned_to'] != $userId) 
           throw new Exception('Junior can only change status of their own tasks');
   } else {
       throw new Exception('No permission');
   }

   // --- Update status ---
   $stmt=$pdo->prepare("UPDATE tasks SET status = ?, updated_at = NOW() WHERE id = ?"); 
   $stmt->execute([$newStatus,$taskId]);

   // --- Auto comment ---
   $stmt=$pdo->prepare("INSERT INTO comments (task_id,user_id,content) VALUES (?,?,?)");
   $user=currentUser(); 
   $uname=$user['name'] ?? 'System'; 
   $text = "$uname changed the status to $newStatus";
   $stmt->execute([$taskId,$user['id'],$text]);

   // --- Check if all tasks Done ---
   if($newStatus==='Done'){
       $stmt=$pdo->prepare("SELECT COUNT(*) FROM tasks WHERE project_id = ? AND status <> 'Done'"); 
       $stmt->execute([$projectId]); 
       $remaining=$stmt->fetchColumn(); 
       if($remaining==0){
           $stmt=$pdo->prepare("UPDATE projects SET status='Done' WHERE id = ?"); 
           $stmt->execute([$projectId]); 
       }
   }
}

function deleteTask($id){
    global $pdo;

    $stmt = $pdo->prepare("DELETE FROM comments WHERE task_id=?");
    $stmt->execute([$id]);

    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id=?");
    $stmt->execute([$id]);
}

function isTaskProjectTeamLead($taskId, $userId){
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT 1 FROM tasks t
        JOIN projects p ON t.project_id = p.id
        JOIN users u ON p.team_lead_id = u.id
        WHERE t.id=? AND u.id=?");
    $stmt->execute([$taskId, $userId]);
    return (bool)$stmt->fetchColumn();
}
