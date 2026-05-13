<?php
session_start();
require_once '../php/db.php';

if (isset($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        setcookie('last_login', date('Y-m-d H:i:s'), time() + (86400 * 30), '/');
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, #ff6b35, #ffd166, #ff85a1);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: white;
            padding: 44px;
            border-radius: 24px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.15);
            width: 380px;
            text-align: center;
        }
        .login-box h2 {
            font-family: 'Syne', sans-serif;
            font-size: 1.8rem;
            color: #1a1a2e;
            margin-bottom: 8px;
        }
        .login-box p { color: #aaa; font-size: 0.9rem; margin-bottom: 28px; }
        input {
            width: 100%; padding: 13px 16px; margin: 7px 0;
            border: 2px solid #eee; border-radius: 12px;
            font-size: 0.95rem; font-family: inherit;
            transition: border-color 0.3s;
        }
        input:focus { outline: none; border-color: #ff6b35; }
        button {
            width: 100%; padding: 14px; margin-top: 16px;
            background: linear-gradient(135deg, #ff6b35, #ff85a1);
            color: white; border: none; border-radius: 12px;
            font-size: 1rem; font-weight: 600; cursor: pointer;
            font-family: inherit; transition: transform 0.2s, box-shadow 0.2s;
        }
        button:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(255,107,53,0.4); }
        .error { color: #e74c3c; margin-top: 14px; font-size: 0.88rem; background: #fef0f0; padding: 10px; border-radius: 8px; }
        .hint { color: #ccc; font-size: 0.78rem; margin-top: 16px; }
    </style>
</head>
<body>
<div class="login-box">
    <h2>🔐 Admin Panel</h2>
    <p>Tuğba Caglar Portfolio Management</p>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" value="admin" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <p class="hint">Username: admin / Password: password</p>
</div>
</body>
</html>