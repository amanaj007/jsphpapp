<?php
session_start();
require_once 'db_pdo.php';

if (!isset($_SESSION['user_id'])) {
    die('Not logged in');
}

$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profile_id = intval($_POST['profile_id'] ?? 0);
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name']  ?? '');
    $email      = trim($_POST['email']      ?? '');
    $headline   = trim($_POST['headline']   ?? '');
    $summary    = trim($_POST['summary']    ?? '');

    // Data validation
    if (strlen($first_name) < 1 || strlen($last_name) < 1 || strlen($email) < 1
        || strlen($headline) < 1 || strlen($summary) < 1) {
        $_SESSION['flash_error'] = 'All fields are required';
        header('Location: /edit?profile_id=' . $profile_id);
        exit();
    }

    if (strpos($email, '@') === false) {
        $_SESSION['flash_error'] = 'Email address must contain @';
        header('Location: /edit?profile_id=' . $profile_id);
        exit();
    }

    // Verify profile exists and belongs to logged-in user
    $stmt = $pdo->prepare('SELECT user_id FROM Profile WHERE profile_id = :pid');
    $stmt->execute([':pid' => $profile_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row === false) {
        die('Profile not found');
    }
    if ($row['user_id'] != $_SESSION['user_id']) {
        die('You do not own this profile');
    }

    $stmt = $pdo->prepare('UPDATE Profile SET
        first_name = :fn,
        last_name  = :ln,
        email      = :em,
        headline   = :he,
        summary    = :su
        WHERE profile_id = :pid AND user_id = :uid');
    $stmt->execute([
        ':fn'  => $first_name,
        ':ln'  => $last_name,
        ':em'  => $email,
        ':he'  => $headline,
        ':su'  => $summary,
        ':pid' => $profile_id,
        ':uid' => $_SESSION['user_id'],
    ]);

    $_SESSION['flash'] = 'Profile updated successfully';
    header('Location: /');
    exit();
}

// GET request - load profile for editing
$profile_id = intval($_GET['profile_id'] ?? 0);
if ($profile_id < 1) {
    die('Invalid profile ID');
}

$stmt = $pdo->prepare('SELECT * FROM Profile WHERE profile_id = :pid');
$stmt->execute([':pid' => $profile_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if ($profile === false) {
    die('Profile not found');
}
if ($profile['user_id'] != $_SESSION['user_id']) {
    die('You do not own this profile');
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
    <title>Edit Profile - Profile Database - AJ007</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 0 20px; background: #f5f5f5; }
        h1 { color: #333; }
        .card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input[type=text], textarea { width: 100%; padding: 9px 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 1em; }
        textarea { height: 100px; resize: vertical; }
        input[type=submit] { background: #007bff; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; font-size: 1em; cursor: pointer; }
        input[type=submit]:hover { background: #0056b3; }
        .flash-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px 15px; border-radius: 4px; margin-bottom: 15px; }
        .back-link { margin-top: 15px; }
        .back-link a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
<h1>Edit Profile</h1>
<div class="card">
    <?php if ($flash_error): ?>
        <div class="flash-error"><?php echo htmlentities($flash_error); ?></div>
    <?php endif; ?>
    <form method="POST" action="/edit">
        <input type="hidden" name="profile_id" value="<?php echo intval($profile['profile_id']); ?>">

        <label for="first_name">First Name</label>
        <input type="text" name="first_name" id="first_name" value="<?php echo htmlentities($profile['first_name']); ?>">

        <label for="last_name">Last Name</label>
        <input type="text" name="last_name" id="last_name" value="<?php echo htmlentities($profile['last_name']); ?>">

        <label for="email">Email Address</label>
        <input type="text" name="email" id="email" value="<?php echo htmlentities($profile['email']); ?>">

        <label for="headline">Headline</label>
        <input type="text" name="headline" id="headline" value="<?php echo htmlentities($profile['headline']); ?>">

        <label for="summary">Summary</label>
        <textarea name="summary" id="summary"><?php echo htmlentities($profile['summary']); ?></textarea>

        <input type="submit" value="Save Changes">
    </form>
</div>
<div class="back-link"><a href="/">&larr; Back to Profiles</a></div>
</body>
</html>
