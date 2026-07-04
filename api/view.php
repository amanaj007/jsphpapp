<?php
require_once __DIR__ . '/session_handler.php';
start_db_session();

$profile_id = intval($_GET['profile_id'] ?? 0);
if ($profile_id < 1) {
    die('Invalid profile ID');
}

$pdo  = getPDO();
$stmt = $pdo->prepare('SELECT Profile.*, users.name AS owner_name
    FROM Profile
    JOIN users ON Profile.user_id = users.user_id
    WHERE profile_id = :pid');
$stmt->execute([':pid' => $profile_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if ($profile === false) {
    die('Profile not found');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile - Profile Database - 4c414f1e</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 40px auto; padding: 0 20px; background: #f5f5f5; }
        h1 { color: #333; }
        .card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .field-label { font-weight: bold; color: #555; margin-bottom: 3px; }
        .field-value { margin-bottom: 18px; color: #333; white-space: pre-wrap; }
        .actions { margin-top: 10px; }
        .actions a { margin-right: 12px; text-decoration: none; }
        .btn-edit { color: #28a745; font-weight: bold; }
        .btn-delete { color: #dc3545; font-weight: bold; }
        .back-link { margin-top: 20px; }
        .back-link a { color: #007bff; text-decoration: none; }
        .owner { color: #888; font-size: 0.9em; margin-bottom: 20px; }
    </style>
</head>
<body>
<h1>Profile Detail</h1>
<div class="card">
    <p class="owner">Added by: <?php echo htmlentities($profile['owner_name']); ?></p>

    <div class="field-label">First Name</div>
    <div class="field-value"><?php echo htmlentities($profile['first_name']); ?></div>

    <div class="field-label">Last Name</div>
    <div class="field-value"><?php echo htmlentities($profile['last_name']); ?></div>

    <div class="field-label">Email</div>
    <div class="field-value"><?php echo htmlentities($profile['email']); ?></div>

    <div class="field-label">Headline</div>
    <div class="field-value"><?php echo htmlentities($profile['headline']); ?></div>

    <div class="field-label">Summary</div>
    <div class="field-value"><?php echo htmlentities($profile['summary']); ?></div>

    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $profile['user_id']): ?>
    <div class="actions">
        <a class="btn-edit" href="/edit.php?profile_id=<?php echo intval($profile['profile_id']); ?>">Edit</a>
        <a class="btn-delete" href="/delete.php?profile_id=<?php echo intval($profile['profile_id']); ?>">Delete</a>
    </div>
    <?php endif; ?>
</div>
<div class="back-link"><a href="/">&larr; Back to Profiles</a></div>
</body>
</html>
