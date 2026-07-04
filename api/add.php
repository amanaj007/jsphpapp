<?php
require_once __DIR__ . '/session_handler.php';
start_db_session();

if (!isset($_SESSION['user_id'])) {
    die('Not logged in');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name']  ?? '');
    $email      = trim($_POST['email']      ?? '');
    $headline   = trim($_POST['headline']   ?? '');
    $summary    = trim($_POST['summary']    ?? '');

    // Data validation
    if (strlen($first_name) < 1 || strlen($last_name) < 1 || strlen($email) < 1
        || strlen($headline) < 1 || strlen($summary) < 1) {
        $_SESSION['flash_error'] = 'All fields are required';
        safe_redirect('/add');
    }

    if (strpos($email, '@') === false) {
        $_SESSION['flash_error'] = 'Email address must contain @';
        safe_redirect('/add');
    }

    $pdo  = getPDO();
    $stmt = $pdo->prepare('INSERT INTO Profile
        (user_id, first_name, last_name, email, headline, summary)
        VALUES (:uid, :fn, :ln, :em, :he, :su)');
    $stmt->execute([
        ':uid' => $_SESSION['user_id'],
        ':fn'  => $first_name,
        ':ln'  => $last_name,
        ':em'  => $email,
        ':he'  => $headline,
        ':su'  => $summary,
    ]);

    $_SESSION['flash'] = 'Profile added successfully';
    safe_redirect('/');
}

$flash_error = '';
if (isset($_SESSION['flash_error'])) {
    $flash_error = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Profile - Profile Database - Aman Kumar Jaiswal</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 0 20px; background: #f5f5f5; }
        h1 { color: #333; }
        .card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input[type=text], textarea { width: 100%; padding: 9px 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 1em; }
        textarea { height: 100px; resize: vertical; }
        input[type=submit] { background: #28a745; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; font-size: 1em; cursor: pointer; }
        input[type=submit]:hover { background: #1e7e34; }
        .flash-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px 15px; border-radius: 4px; margin-bottom: 15px; }
        .back-link { margin-top: 15px; }
        .back-link a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
<h1>Add New Profile</h1>
<div class="card">
    <?php if ($flash_error): ?>
        <div class="flash-error"><?php echo htmlentities($flash_error); ?></div>
    <?php endif; ?>
    <form method="POST" action="/add.php">
        <label for="first_name">First Name</label>
        <input type="text" name="first_name" id="first_name">

        <label for="last_name">Last Name</label>
        <input type="text" name="last_name" id="last_name">

        <label for="email">Email Address</label>
        <input type="text" name="email" id="email">

        <label for="headline">Headline</label>
        <input type="text" name="headline" id="headline">

        <label for="summary">Summary</label>
        <textarea name="summary" id="summary"></textarea>

        <input type="submit" value="Add">
    </form>
</div>
<div class="back-link"><a href="/">&larr; Back to Profiles</a></div>
</body>
</html>
