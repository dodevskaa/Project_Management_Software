<?php
require_once __DIR__.'/../src/auth.php';
require_once __DIR__.'/../src/db.php';

requireLogin();
if(!isAdmin()) { echo 'No access'; exit; }

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if($_SERVER['REQUEST_METHOD']==='POST'){
    if(isset($_POST['approve_user'])){
        $stmt=$pdo->prepare('UPDATE users SET approved=1 WHERE id=?');
        $stmt->execute([$_POST['approve_user']]);
    }
    if(isset($_POST['set_level'])){
        $stmt=$pdo->prepare('UPDATE users SET level=? WHERE id=?');
        $stmt->execute([$_POST['level'],$_POST['set_level']]);
    }
    if(isset($_POST['create_project'])){
        $stmt = $pdo->prepare("INSERT INTO projects 
            (title, description, requirements, estimated_time, team_lead_id, deadline, status, created_at)
            VALUES (?,?,?,?,?,?, 'Active', NOW())");
        $stmt->execute([
            $_POST['title'],
            $_POST['description'],
            $_POST['requirements'],
            $_POST['estimated_time'],
            $_POST['team_lead_id'],
            $_POST['deadline']
        ]);
    }
    if(isset($_POST['create_user'])){
        $randomPassword = bin2hex(random_bytes(4)); 
        $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, level, approved, created_at) 
                               VALUES (?,?,?,?,1,NOW())");
        $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $hashedPassword,
            $_POST['level']
        ]);

        $_SESSION['flash_message'] = "User created! Temporary password: ".$randomPassword;
        header("Location: admin_panel.php");
        exit;
    }
}

$users=$pdo->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();
$projects=$pdo->query('SELECT * FROM projects ORDER BY created_at DESC')->fetchAll();
?>

<!doctype html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Admin Panel</title>
    <link rel='stylesheet' href='css/styles.css'>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f6fa;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h2, h3 { color: #2f3640; }
        main, section {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th { background-color: #00a8ff; color: white; }
        table tr:hover { background-color: #f1f2f6; }
        button {
            padding: 6px 12px;
            background-color: #00a8ff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-left: 5px;
            transition: background-color 0.2s;
        }
        button:hover { background-color: #0097e6; }
        select, input, textarea {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 100%;
            margin-bottom: 10px;
        }
        section div.project-item {
            background-color: #fff;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        section div.project-item .project-info {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        section div.project-item b { font-size: 1rem; }
        section div.project-item span.status {
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .status-Active { background-color: #dff9e3; color: green; }
        .status-Expired { background-color: #ffd6d6; color: red; }
        section div.project-item button {
            padding: 4px 8px;
            font-size: 0.9rem;
            background-color: #00a8ff;
            border-radius: 4px;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        section div.project-item button:hover { background-color: #0097e6; }
        form.inline { display: inline-block; }
    </style>
</head>
<body>
<main>
<h2>Admin Panel</h2>

<!-- Flash message -->
<?php if(isset($_SESSION['flash_message'])): ?>
    <p style="color:green; font-weight:bold;"><?=htmlspecialchars($_SESSION['flash_message'])?></p>
    <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>

<!-- Users Section -->
<section>
    <h3>Users</h3>
    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach($users as $u): ?>
            <tr>
                <td><?=htmlspecialchars($u['name'])?></td>
                <td><?=htmlspecialchars($u['email'])?></td>
                <td><?= $u['approved'] ? 'Approved' : 'Pending' ?></td>
                <td>
                    <form method='post' class="inline">
                        <?php if(!$u['approved']): ?>
                            <button name='approve_user' value='<?=$u['id']?>'>Approve</button>
                        <?php endif; ?>
                    </form>
                    <form method='post' class="inline">
                        <select name='level'>
                            <option <?= $u['level']=='Junior'?'selected':'' ?>>Junior</option>
                            <option <?= $u['level']=='Mid'?'selected':'' ?>>Mid</option>
                            <option <?= $u['level']=='Senior'?'selected':'' ?>>Senior</option>
                            <option <?= $u['level']=='TeamLead'?'selected':'' ?>>TeamLead</option>
                            <option <?= $u['level']=='Admin'?'selected':'' ?>>Admin</option>
                        </select>
                        <button name='set_level' value='<?=$u['id']?>'>Set Level</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>

<!-- Create User Section -->
<section>
    <h3>Create User</h3>
    <form method="post">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <select name="level" required>
            <option value="Junior">Junior</option>
            <option value="Mid">Mid</option>
            <option value="Senior">Senior</option>
            <option value="TeamLead">TeamLead</option>
            <option value="Admin">Admin</option>
        </select>
        <button type="submit" name="create_user">Create User</button>
    </form>
</section>

<!-- Projects Section -->
<section>
    <h3>Projects</h3>
    <?php if(count($projects)>0): ?>
        <?php foreach($projects as $p): ?>
            <div class="project-item">
                <div class="project-info">
                    <b><?=htmlspecialchars($p['title'])?></b>
                    <span class="status status-<?=htmlspecialchars($p['status'])?>">
                        <?=htmlspecialchars($p['status'])?>
                    </span>
                    <span>Deadline: <?=$p['deadline']?></span>
                </div>
                <button onclick="deleteProject(<?=$p['id']?>)">Delete</button>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No projects yet.</p>
    <?php endif; ?>
</section>

<!-- Create Project Section -->
<section>
    <h3>Create Project</h3>
    <form method="post">
        <input type="text" name="title" placeholder="Title" required>
        <textarea name="description" placeholder="Description"></textarea>
        <textarea name="requirements" placeholder="Requirements"></textarea>
        <input type="text" name="estimated_time" placeholder="Estimated time (e.g. 10 days)">
        <input type="date" name="deadline">
        <select name="team_lead_id" required>
            <option value="">-- Assign to Team Lead --</option>
            <?php
            $teamLeads = $pdo->query("SELECT * FROM users WHERE level='TeamLead' AND approved=1")->fetchAll();
            foreach($teamLeads as $tl){
                echo "<option value='{$tl['id']}'>".htmlspecialchars($tl['name'])." ({$tl['email']})</option>";
            }
            ?>
        </select>
        <button type="submit" name="create_project">Create Project</button>
    </form>
</section>

<script>
async function deleteProject(id){
    if(!confirm('Are you sure you want to delete this project?')) return;
    const res = await fetch('../src/api/delete_project.php',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({project_id:id})
    });
    const r = await res.json();
    if(r.success) location.reload();
    else alert(r.error);
}
</script>

</main>
</body>
</html>
