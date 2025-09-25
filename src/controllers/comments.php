<?php
require_once __DIR__.'/../db.php';
require_once __DIR__.'/../middleware.php';

function addComment($taskId, $userId, $content) { 
    global $pdo; 
    $level = userLevel($userId); 

    $stmt = $pdo->prepare("SELECT assigned_to FROM tasks WHERE id = ?"); 
    $stmt->execute([$taskId]); 
    $task = $stmt->fetch(); 

    if (!$task) throw new Exception('Task not found'); 

    $assigned = $task['assigned_to']; 

    if (!in_array($level, ['TeamLead','Senior','Admin'])) {
        if ($level === 'Mid') {
            if ($assigned != $userId && (! $assigned || userLevel($assigned) !== 'Junior')) {
                throw new Exception('Mid cannot comment here');
            }
        } elseif ($level === 'Junior') {
            if ($assigned != $userId) throw new Exception('Junior cannot comment here');
        }
    }

    $stmt = $pdo->prepare("INSERT INTO comments (task_id, user_id, content) VALUES (?, ?, ?)"); 
    $stmt->execute([$taskId, $userId, $content]); 

    return $pdo->lastInsertId(); 
}

function editComment($commentId, $userId, $newContent) { 
    global $pdo; 

    $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = ?"); 
    $stmt->execute([$commentId]); 
    $owner = $stmt->fetchColumn(); 

    if (!$owner) throw new Exception('Comment not found'); 
    if ($owner != $userId && !isAdmin()) throw new Exception('No permission'); 

    $stmt = $pdo->prepare("UPDATE comments SET content = ?, updated_at = NOW(), edited = 1 WHERE id = ?"); 
    $stmt->execute([$newContent, $commentId]); 
}

function deleteComment($commentId, $userId) { 
    global $pdo; 

    $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = ?"); 
    $stmt->execute([$commentId]); 
    $owner = $stmt->fetchColumn(); 

    if (!$owner) throw new Exception('Comment not found'); 
    if ($owner != $userId && !isAdmin()) throw new Exception('No permission'); 

    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?"); 
    $stmt->execute([$commentId]); 
}
