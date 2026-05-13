<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
require_once '../php/db.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add_project') {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $technologies = trim($_POST['technologies']);
        $github_url = trim($_POST['github_url']);
        $stmt = $pdo->prepare("INSERT INTO projects (title, description, technologies, github_url) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $description, $technologies, $github_url]);
        $message = 'Project added successfully!';
    }
    if (isset($_POST['action']) && $_POST['action'] === 'delete_project') {
        $id = (int)$_POST['project_id'];
        $pdo->prepare("DELETE FROM projects WHERE id = ?")->execute([$id]);
        $message = 'Project deleted.';
    }
}

$projects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$contacts = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #f5f5f5; }
        .header {
            background: linear-gradient(135deg, #ff6b35, #ff85a1);
            color: white; padding: 20px 40px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .header h1 { font-family: 'Syne', sans-serif; font-size: 1.4rem; }
        .logout {
            color: white; text-decoration: none;
            background: rgba(255,255,255,0.2); padding: 8px 18px;
            border-radius: 20px; font-size: 0.9rem; transition: background 0.2s;
        }
        .logout:hover { background: rgba(255,255,255,0.3); }
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; border-radius: 20px; padding: 30px; margin-bottom: 24px; box-shadow: 0 2px 16px rgba(0,0,0,0.06); }
        h2 { font-family: 'Syne', sans-serif; color: #ff6b35; margin-bottom: 20px; font-size: 1.2rem; }
        input, textarea {
            width: 100%; padding: 11px 14px; margin: 6px 0;
            border: 2px solid #eee; border-radius: 10px;
            font-size: 0.92rem; font-family: inherit; transition: border-color 0.3s;
        }
        input:focus, textarea:focus { outline: none; border-color: #ff6b35; }
        textarea { height: 90px; resize: vertical; }
        .btn { padding: 10px 22px; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 0.9rem; font-family: inherit; }
        .btn-primary { background: linear-gradient(135deg, #ff6b35, #ff85a1); color: white; }
        .btn-danger { background: #e74c3c; color: white; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #f5f5f5; font-size: 0.88rem; }
        th { color: #ff6b35; font-weight: 600; }
        .success { color: #27ae60; background: #eafaf1; padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-weight: 600; }
        .cookie-info { background: #fff3e0; padding: 12px 16px; border-radius: 10px; font-size: 0.84rem; color: #e65100; margin-bottom: 16px; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #ff6b35; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
<div class="header">
    <h1>⚙️ Admin Dashboard — Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?>!</h1>
    <a href="logout.php" class="logout">Logout</a>
</div>
<div class="container">
    <a href="../index.html" class="back-link">← Back to Website</a>

    <?php if (isset($_COOKIE['last_login'])): ?>
    <div class="cookie-info">🍪 Your last login time: <?= htmlspecialchars($_COOKIE['last_login']) ?></div>
    <?php endif; ?>

    <?php if ($message): ?>
    <div class="success">✅ <?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="card">
        <h2>➕ Add New Project</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add_project">
            <input type="text" name="title" placeholder="Project Title" required>
            <textarea name="description" placeholder="Project Description" required></textarea>
            <input type="text" name="technologies" placeholder="Technologies (e.g. Python, Flask)">
            <input type="text" name="github_url" placeholder="GitHub Link">
            <br><br>
            <button type="submit" class="btn btn-primary">Add Project</button>
        </form>
    </div>

    <div class="card">
        <h2>📁 Projects</h2>
        <table>
            <tr><th>Title</th><th>Technologies</th><th>Date</th><th>Delete</th></tr>
            <?php foreach ($projects as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['title']) ?></td>
                <td><?= htmlspecialchars($p['technologies']) ?></td>
                <td><?= $p['created_at'] ?></td>
                <td>
                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this project?')">
                        <input type="hidden" name="action" value="delete_project">
                        <input type="hidden" name="project_id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="card">
        <h2>📬 Incoming Messages</h2>
        <?php if (empty($contacts)): ?>
            <p style="color:#aaa">No messages yet.</p>
        <?php else: ?>
        <table>
            <tr><th>Name</th><th>Email</th><th>Message</th><th>Date</th></tr>
            <?php foreach ($contacts as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= htmlspecialchars(substr($c['message'], 0, 60)) ?>...</td>
                <td><?= $c['created_at'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>