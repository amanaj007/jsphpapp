<?php
require_once __DIR__ . '/session_handler.php';
start_db_session();

if (!isset($_SESSION['user_id'])) {
    die('Not logged in');
}

$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profile_id = intval($_POST['profile_id'] ?? 0);

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

    $stmt = $pdo->prepare('DELETE FROM Profile WHERE profile_id = :pid AND user_id = :uid');
    $stmt->execute([':pid' => $profile_id, ':uid' => $_SESSION['user_id']]);

    $_SESSION['flash'] = 'Profile deleted successfully';
    safe_redirect('/');
}

// GET request - show confirmation screen
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Profile - Profile Database - Aman Kumar Jaiswal</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 0 20px; background: #f5f5f5; }
        h1 { color: #dc3545; }
        .card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .warning { background: #fff3cd; border: 1px solid #ffc107; color: #856404; padding: 12px 15px; border-radius: 4px; margin-bottom: 20px; }
        .profile-info { margin-bottom: 20px; }
        .profile-info p { margin: 6px 0; }
        .field-label { font-weight: bold; color: #555; }
        .btn-delete { background: #dc3545; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; font-size: 1em; cursor: pointer; }
        .btn-delete:hover { background: #b02a37; }
        .btn-cancel { display: inline-block; margin-left: 12px; color: #007bff; text-decoration: none; font-size: 1em; }
        .btn-cancel:hover { text-decoration: underline; }
    </style>
</head>
<body>
<h1>Delete Profile</h1>
<div class="card">
    <div class="warning">
        <strong>Are you sure?</strong> This action cannot be undone.
    </div>
    <div class="profile-info">
        <p><span class="field-label">Name:</span>
            <?php echo htmlentities($profile['first_name'] . ' ' . $profile['last_name']); ?></p>
        <p><span class="field-label">Email:</span>
            <?php echo htmlentities($profile['email']); ?></p>
        <p><span class="field-label">Headline:</span>
            <?php echo htmlentities($profile['headline']); ?></p>
    </div>
    <form method="POST" action="/delete.php">
        <input type="hidden" name="profile_id" value="<?php echo intval($profile['profile_id']); ?>">
        <button type="submit" class="btn-delete">Yes, Delete This Profile</button>
        <a class="btn-cancel" href="/">Cancel</a>
    </form>
</div>
</body>
</html>
