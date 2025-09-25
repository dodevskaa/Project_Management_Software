<?php
require_once __DIR__.'/auth.php';
require_once __DIR__.'/db.php';

function userLevel($userId){ 
    global $pdo; 
    $stmt=$pdo->prepare("SELECT level FROM users WHERE id = ?"); 
    $stmt->execute([$userId]); 
    return $stmt->fetchColumn(); }

function isProjectTeamLead($projectId,$userId){ 
    global $pdo; 
    $stmt=$pdo->prepare("SELECT team_lead_id FROM projects WHERE id = ?"); 
    $stmt->execute([$projectId]); 
    $lead=$stmt->fetchColumn(); 
    return $lead && ((int)$lead === (int)$userId || isAdmin()); }

function isProjectMember($projectId,$userId){ 
    global $pdo; 
    $stmt=$pdo->prepare("SELECT COUNT(*) FROM project_members WHERE project_id = ? AND user_id = ?"); 
    $stmt->execute([$projectId,$userId]); 
    return $stmt->fetchColumn() > 0; }
