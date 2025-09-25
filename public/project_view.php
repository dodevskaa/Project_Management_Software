<?php
require_once __DIR__.'/../src/auth.php';
require_once __DIR__.'/../src/controllers/projects.php';
require_once __DIR__.'/../src/controllers/tasks.php';
require_once __DIR__.'/../src/controllers/comments.php';
require_once __DIR__.'/../src/db.php';

requireLogin();
$user=currentUser();
$pid = $_GET['id'] ?? null;
if(!$pid) { header('Location: dashboard.php'); exit; }

$project = getProjectById($pid);

// all tasks
$stmt=$pdo->prepare('
    SELECT t.*, 
           u.name as assignee_name, 
           c.name as creator_name
    FROM tasks t
    LEFT JOIN users u ON t.assigned_to = u.id
    LEFT JOIN users c ON t.created_by = c.id
    WHERE t.project_id = ?
    ORDER BY t.created_at DESC
');
$stmt->execute([$pid]);
$tasks=$stmt->fetchAll();

// members
$stmt=$pdo->prepare('SELECT pm.*, u.name, u.level
                     FROM project_members pm 
                     JOIN users u ON pm.user_id = u.id 
                     WHERE pm.project_id = ?');
$stmt->execute([$pid]);
$members=$stmt->fetchAll();

// comments
$stmt=$pdo->prepare('SELECT c.*, u.name 
                     FROM comments c 
                     JOIN users u ON c.user_id = u.id 
                     WHERE c.task_id IN (SELECT id FROM tasks WHERE project_id = ?) 
                     ORDER BY c.created_at DESC');
$stmt->execute([$pid]);
$comments=$stmt->fetchAll();

$allUsers = $pdo->query('SELECT id,name,level FROM users WHERE approved=1')->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Project</title>
    <link rel='stylesheet' href='css/styles.css'>
    <script src='js/app.js'></script>
</head>
<body>
<header><a href='dashboard.php'>Back</a></header>
<main>

<!-- Project Info -->
<h2 id="project_title"><?=htmlspecialchars($project['title'])?></h2>
<p id="project_description"><?=nl2br(htmlspecialchars($project['description'] ?? ''))?></p>

<?php if(isAdmin()): ?>
<section>
    <h4>Edit Project</h4>
    <input type="text" id="edit_title" value="<?=htmlspecialchars($project['title'])?>"><br><br>
    <textarea id="edit_description"><?=htmlspecialchars($project['description'])?></textarea><br><br>
    <textarea id="edit_requirements"><?=htmlspecialchars($project['requirements'])?></textarea><br><br>
    <input type="text" id="edit_estimated_time" value="<?=htmlspecialchars($project['estimated_time'])?>"><br><br>
    <input type="date" id="edit_deadline" value="<?=htmlspecialchars($project['deadline'])?>"><br><br>
    <select id="edit_team_lead">
      <option value="">-- Assign to Team Lead --</option>
      <?php
      $teamLeads = $pdo->query("SELECT * FROM users WHERE level='TeamLead' AND approved=1")->fetchAll();
      foreach($teamLeads as $tl){
          $sel = ($tl['id']==$project['team_lead_id'])?'selected':'';
          echo "<option value='{$tl['id']}' $sel>".htmlspecialchars($tl['name'])." ({$tl['email']})</option>";
      }
      ?>
    </select><br><br>
    <button onclick="editProject(<?=$pid?>)">Save Changes</button>
    <button onclick="deleteProject(<?=$pid?>)">Delete Project</button>
</section>
<?php endif; ?>

<!-- Members -->
<h3>Members</h3>
<ul>
<?php foreach($members as $m): ?>
    <li>
      <?=htmlspecialchars($m['name'])?> (<?=htmlspecialchars($m['level'])?>)
      <?php 
      // Show remove button only if current user is Admin OR TeamLead trying to remove non-TeamLead
      if(isAdmin() || (isProjectTeamLead($pid,$user['id']) && $m['level'] !== 'TeamLead')): ?>
    <button onclick="removeMember(<?=$pid?>, <?=$m['user_id']?>)">Remove</button>
    <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>



<!-- Add Member & Create Task -->
<?php if(isProjectTeamLead($pid,$user['id']) || isAdmin()): ?>
<section>
<h4>Add Member</h4>
<select id='add_user'>
  <?php foreach($allUsers as $au): ?>
    <option value='<?=$au['id']?>'><?=htmlspecialchars($au['name'].' - '.$au['level'])?></option>
  <?php endforeach; ?>
</select>
<select id='add_role'>
  <option>Senior</option><option>Mid</option><option>Junior</option>
</select>
<button onclick="addMember(<?=$pid?>)">Add Member</button>
</section>

<section>
<h4>Create Task</h4>
<input id='task_title' placeholder='Title'><br><br>
<textarea id='task_desc' placeholder='Description'></textarea><br><br>
<button onclick="createTask(<?=$pid?>)">Create Task</button>
</section>
<?php endif; ?>


<!-- Tasks -->
<h3>Tasks</h3>
<?php 
$userLevel = $user['level']; 
foreach($tasks as $t): 
?>
<div class='task' id="task_<?=$t['id']?>">
  <h4><?=htmlspecialchars($t['title'])?> - <?=$t['status']?></h4>
  <p><?=nl2br(htmlspecialchars($t['description']))?></p>
  <p>Assignee: <?=htmlspecialchars($t['assignee_name'] ?? 'Unassigned')?></p>
  <p><i>Created by: <?=htmlspecialchars($t['creator_name'] ?? 'Unknown')?></i></p>

  <div>
    <!-- Buttons to change status (if assigned) -->
    <?php if($t['assigned_to'] || in_array($userLevel, ['TeamLead','Admin','Senior','Mid'])): ?>
      <button onclick="changeStatus(<?=$t['id']?>,'To Do')">To Do</button>
      <button onclick="changeStatus(<?=$t['id']?>,'In Progress')">In Progress</button>
      <button onclick="changeStatus(<?=$t['id']?>,'QA')">QA</button>
      <button onclick="changeStatus(<?=$t['id']?>,'Done')">Done</button>
    <?php endif; ?>

    <!-- Assign dropdown -->
    <?php if(in_array($userLevel,['TeamLead','Admin','Senior','Mid'])): ?>
      <select id='assign_<?=$t['id']?>'>
        <option value=''>Unassigned</option>
        <?php foreach($members as $m): ?>
          <?php 
            $mLevel = $m['level'];
            if($userLevel==='Senior' && !in_array($mLevel,['Mid','Junior']) && $m['user_id'] != $user['id']) continue;
            if($userLevel==='Mid' && $mLevel !== 'Junior' && $m['user_id'] != $user['id']) continue;
          ?>
          <option value='<?=$m['user_id']?>' <?=($t['assigned_to']==$m['user_id'])?'selected':''?>><?=htmlspecialchars($m['name'].' ('.$mLevel.')')?></option>
        <?php endforeach; ?>
      </select>
      <button onclick="assignTask(<?=$t['id']?>, document.getElementById('assign_<?=$t['id']?>').value)">Assign</button>
    <?php endif; ?>

    <!-- Admin extra buttons -->
    <?php if(isAdmin()): ?>
      <button onclick="editTaskPrompt(<?=$t['id']?>)">Edit Task</button>
      <button onclick="deleteTask(<?=$t['id']?>)">Delete Task</button>
    <?php endif; ?>
  </div>

  <!-- Comments -->
  <div class='comments'>
    <h5>Comments</h5>
    <?php foreach($comments as $c): ?>
      <?php if($c['task_id']==$t['id']): ?>
        <div class='comment' id='comment_<?=$c['id']?>'>
          <b><?=htmlspecialchars($c['name'])?></b>: <?=nl2br(htmlspecialchars($c['content']))?>
          <?php if($c['user_id']==$user['id'] || isAdmin()): ?>
            <button onclick="deleteComment(<?=$c['id']?>)">Delete</button>
            <button onclick="editCommentPrompt(<?=$c['id']?>)">Edit</button>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
    <textarea id='comment_text_<?=$t['id']?>' placeholder='Add comment'></textarea>
    <button onclick="addComment(<?=$t['id']?>)">Add Comment</button>
  </div>
</div>
<?php endforeach; ?>


</main>

<script>
async function editProject(pid){
  const title = document.getElementById('edit_title').value;
  const description = document.getElementById('edit_description').value;
  const requirements = document.getElementById('edit_requirements').value;
  const estimated_time = document.getElementById('edit_estimated_time').value;
  const deadline = document.getElementById('edit_deadline').value;
  const team_lead_id = document.getElementById('edit_team_lead').value;

  const res = await fetch('../src/api/project_edit.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify({pid,title,description,requirements,estimated_time,deadline,team_lead_id})
  });
  const r = await res.json();
  if(r.success) location.reload();
  else alert(r.error);
}

async function deleteProject(pid){
  if(!confirm('Delete this project?')) return;
  const res = await fetch('../src/api/delete_project.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body: JSON.stringify({ project_id: pid }) 
  });
  const r = await res.json();
  if(r.success) window.location='dashboard.php';
  else alert(r.error);
}


async function editTaskPrompt(taskId){
  const newTitle = prompt('New title:');
  const newDesc = prompt('New description:');
  if(newTitle!==null && newDesc!==null){
    const res = await fetch('../src/api/task_edit.php',{
      method:'POST', headers:{'Content-Type':'application/json'},
      body:JSON.stringify({taskId, title:newTitle, description:newDesc})
    });
    const r = await res.json();
    if(r.success) location.reload(); else alert(r.error);
  }
}

async function deleteTask(taskId){
  if(!confirm('Are you sure you want to delete this task?')) return;
  const res = await fetch('../src/api/task_delete.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify({taskId})
  });
  const r = await res.json();
  if(r.success) location.reload(); else alert(r.error);
}


async function addMember(pid){ 
  const uid = document.getElementById('add_user').value;
  const role = document.getElementById('add_role').value;
  const res = await fetch('../src/api/add_member.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify({project_id:pid,user_id:uid,role})
  });
  const r=await res.json();
  if(r.success) location.reload(); else alert(r.error);
}

async function removeMember(pid, userId){
    if(!confirm('Are you sure you want to remove this member?')) return;
    if(!pid || !userId){
        alert('Missing parameters!');
        return;
    }

    const res = await fetch('../src/api/remove_member.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({project_id: pid, member_id: userId})
    });

    const r = await res.json();
    if(r.success) location.reload();
    else alert(r.error);
}


async function createTask(pid){
  const title=document.getElementById('task_title').value;
  const desc=document.getElementById('task_desc').value;
  const res=await fetch('../src/api/create_task.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify({project_id:pid,title,description:desc})
  });
  const r=await res.json();
  if(r.success) location.reload(); else alert(r.error);
}
async function assignTask(taskId,userId){
  if(!userId){ alert("Избери член за задачата!"); return; }
  const res=await fetch('../src/api/assign_task.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify({taskId:taskId, assigneeId:userId})
  });
  const r=await res.json();
  if(r.success) location.reload(); else alert(r.error);
}
async function addComment(taskId){
  const text = document.getElementById('comment_text_'+taskId).value;
  const res=await fetch('../src/api/comment_create.php',{
    method:'POST',headers:{'Content-Type':'application/json'},
    body:JSON.stringify({task_id:taskId,content:text})
  });
  const r=await res.json();
  if(r.success) location.reload(); else alert(r.error);
}
async function deleteComment(id){
  if(!confirm('Delete?')) return;
  const res=await fetch('../src/api/comment_delete.php',{
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify({comment_id:id})
  });
  const r=await res.json();
  if(r.success) location.reload(); else alert(r.error);
}
function editCommentPrompt(id){
  const newc = prompt('Edit comment:');
  if(newc!==null){
    fetch('../src/api/comment_edit.php',{
      method:'POST',headers:{'Content-Type':'application/json'},
      body:JSON.stringify({comment_id:id,content:newc})
    }).then(r=>r.json()).then(j=>{
      if(j.success) location.reload(); else alert(j.error);
    });
  }
}
async function changeStatus(taskId, newStatus){
  const res = await fetch('../src/api/change_status.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ task_id: taskId, status: newStatus })
  });
  const r = await res.json();
  if(r.success) location.reload();
  else alert(r.error);
}
</script>

<style>
  /* --- Basic Reset & Fonts --- */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f5f6fa;
    color: #333;
}

header {
    background-color: #2f3640;
    color: #fff;
    padding: 15px 20px;
}

header a {
    color: #fff;
    text-decoration: none;
    font-weight: bold;
}

main {
    padding: 20px;
    max-width: 1200px;
    margin: auto;
}

/* --- Project Info --- */
#project_title {
    font-size: 2rem;
    margin-bottom: 10px;
    color: #2f3640;
}

#project_description {
    font-size: 1.1rem;
    margin-bottom: 20px;
}

/* --- Sections --- */
section {
    background-color: #fff;
    padding: 15px 20px;
    margin-bottom: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* --- Buttons --- */
button {
    background-color: #00a8ff;
    border: none;
    color: white;
    padding: 7px 14px;
    margin: 5px 0;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background-color 0.2s;
}

button:hover {
    background-color: #0097e6;
}

/* --- Inputs & Textareas --- */
input[type="text"], input[type="date"], textarea, select {
    width: 100%;
    padding: 8px 10px;
    margin-bottom: 10px;
    border-radius: 4px;
    border: 1px solid #dcdde1;
    font-size: 0.95rem;
}

/* --- Members List --- */
ul {
    list-style: none;
    padding-left: 0;
}

ul li {
    background-color: #dcdde1;
    padding: 8px 12px;
    border-radius: 4px;
    margin-bottom: 5px;
}

/* --- Tasks --- */
.task {
    background-color: #fff;
    border-left: 4px solid #00a8ff;
    padding: 15px 20px;
    margin-bottom: 20px;
    border-radius: 6px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.task h4 {
    margin-top: 0;
}

.task p {
    margin: 5px 0;
}

/* --- Comments --- */
.comments {
    background-color: #f1f2f6;
    padding: 10px 15px;
    border-radius: 6px;
    margin-top: 10px;
}

.comment {
    background-color: #fff;
    padding: 8px 12px;
    margin-bottom: 8px;
    border-radius: 4px;
    border-left: 3px solid #00a8ff;
}

.comment b {
    color: #2f3640;
}

/* --- Textareas in Comments --- */
textarea[id^="comment_text_"] {
    width: calc(100% - 20px);
    padding: 8px 10px;
    margin-bottom: 5px;
    border-radius: 4px;
    border: 1px solid #dcdde1;
}

/* --- Small Responsive --- */
@media (max-width: 768px) {
    main {
        padding: 15px;
    }

    button, input, select, textarea {
        font-size: 0.9rem;
    }
}

</style>
</body>
</html>
