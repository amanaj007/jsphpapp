<?php
session_start();
require_once 'db_pdo.php';

$pdo = getPDO();

$stmt = $pdo->prepare('SELECT Profile.profile_id, Profile.first_name, Profile.last_name,
    Profile.email, Profile.headline, Profile.user_id
    FROM Profile ORDER BY last_name, first_name');
$stmt->execute();
$profiles = $stmt->fetchAll();

$flash = '';
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Database - 4c414f1e</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 30px auto; padding: 0 20px; background: #f5f5f5; }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .flash-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px 15px; border-radius: 4px; margin-bottom: 15px; }
        .flash-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px 15px; border-radius: 4px; margin-bottom: 15px; }
        .nav-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .nav-bar a { text-decoration: none; color: #007bff; font-weight: bold; }
        .nav-bar a:hover { text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.1); border-radius: 6px; overflow: hidden; }
        th { background: #007bff; color: #fff; padding: 12px 15px; text-align: left; }
        td { padding: 10px 15px; border-bottom: 1px solid #eee; vertical-align: top; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f0f7ff; }
        .actions a { margin-right: 8px; font-size: 0.9em; text-decoration: none; }
        .btn-edit { color: #28a745; }
        .btn-delete { color: #dc3545; }
        .btn-view { color: #007bff; }
        .btn-add { background: #007bff; color: #fff; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 0.95em; }
        .btn-add:hover { background: #0056b3; }
        .empty-msg { text-align: center; padding: 30px; color: #888; }
    </style>
</head>
<body>
<h1>Profile Database</h1>

<div class="nav-bar">
    <div>
        <?php if (isset($_SESSION['user_id'])): ?>
            Logged in as <strong><?php echo htmlentities($_SESSION['name']); ?></strong> |
            <a href="/logout">Logout</a>
        <?php else: ?>
            <a href="/login">Please log in</a>
        <?php endif; ?>
    </div>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a class="btn-add" href="/add">Add New Entry</a>
    <?php endif; ?>
</div>

<?php if ($flash): ?>
    <div class="flash-success"><?php echo htmlentities($flash); ?></div>
<?php endif; ?>

<?php if (count($profiles) === 0): ?>
    <p class="empty-msg">No profiles found. <?php if (isset($_SESSION['user_id'])): ?><a href="/add">Add the first one!</a><?php endif; ?></p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Headline</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($profiles as $row): ?>
        <tr>
            <td><?php echo htmlentities($row['first_name'] . ' ' . $row['last_name']); ?></td>
            <td><?php echo htmlentities($row['email']); ?></td>
            <td><?php echo htmlentities($row['headline']); ?></td>
            <td class="actions">
                <a class="btn-view" href="/view?profile_id=<?php echo intval($row['profile_id']); ?>">View</a>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']): ?>
                    <a class="btn-edit" href="/edit?profile_id=<?php echo intval($row['profile_id']); ?>">Edit</a>
                    <a class="btn-delete" href="/delete?profile_id=<?php echo intval($row['profile_id']); ?>">Delete</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
</body>
</html>
