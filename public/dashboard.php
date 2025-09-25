<?php
require_once __DIR__.'/../src/auth.php'; 
require_once __DIR__.'/../src/controllers/projects.php'; 
require_once __DIR__.'/../src/middleware.php'; 
require_once __DIR__.'/../src/db.php'; 

requireLogin(); 
$user = currentUser(); 

// Update project status
$pdo->exec("UPDATE projects SET status = 'Expired' WHERE deadline < CURDATE()");
$pdo->exec("UPDATE projects SET status = 'Active' WHERE deadline >= CURDATE()");

$projects = getProjectsForUser($user['id']);
?>

<!doctype html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Dashboard</title>
    <link rel='stylesheet' href='css/styles.css'>
    <style>
        /* --- General Reset & Layout --- */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f6fa;
            color: #333;
            line-height: 1.5;
        }

        header {
            background-color: #2f3640;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 1.5rem;
        }

        header a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
            font-weight: bold;
        }

        header a:hover {
            text-decoration: underline;
        }

        main {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        h2 {
            margin-bottom: 20px;
            color: #2f3640;
            border-bottom: 2px solid #00a8ff;
            padding-bottom: 5px;
        }

        /* --- Project Cards --- */
        .project {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .project:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .project h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .project p {
            margin-bottom: 10px;
        }

        .project a {
            display: inline-block;
            padding: 6px 12px;
            background-color: #00a8ff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: background-color 0.2s;
        }

        .project a:hover {
            background-color: #0097e6;
        }

        /* --- Status Labels --- */
        .status-active {
            color: green;
            font-weight: bold;
        }

        .status-expired {
            color: red;
            font-weight: bold;
        }

        /* --- Responsive --- */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }

            header a {
                margin: 5px 0 0 0;
            }

            .project {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
<header>
    <h1>Welcome, <?=htmlspecialchars($user['name'])?></h1>
    <div>
        <a href='logout.php'>Logout</a>
        <a href='change_password.php'>Change Password</a>
        <?php if(isAdmin()): ?>
            <a href='admin_panel.php'>Admin Panel</a>
        <?php endif; ?>
    </div>
</header>

<main>
<h2>Projects</h2>
<?php if(count($projects) > 0): ?>
    <?php foreach($projects as $p): ?>
        <div class='project'>
            <h3>
                <?=htmlspecialchars($p['title'])?> 
                <span class="<?= $p['status'] === 'Expired' ? 'status-expired' : 'status-active' ?>">
                    <?=htmlspecialchars($p['status'])?>
                </span>
            </h3>
            <p><?=nl2br(htmlspecialchars($p['description'] ?? ''))?></p>
            <a href='project_view.php?id=<?=$p['id']?>'>Open</a>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No projects available.</p>
<?php endif; ?>
</main>
</body>
</html>
