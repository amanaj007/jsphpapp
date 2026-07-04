<?php
require_once __DIR__ . '/session_handler.php';
start_db_session();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = trim($_POST['pass'] ?? '');

    if (strlen($email) < 1 || strlen($pass) < 1) {
        $_SESSION['flash_error'] = 'Both fields are required';
        ob_end_clean();
        header('Location: /login');
        exit();
    }

    if (strpos($email, '@') === false) {
        $_SESSION['flash_error'] = 'Please enter a valid email address';
        ob_end_clean();
        header('Location: /login');
        exit();
    }

    $pdo   = getPDO();
    $check = hash('md5', SALT . $pass);

    $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
    $stmt->execute([':em' => $email, ':pw' => $check]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row !== false) {
        $_SESSION['name']    = $row['name'];
        $_SESSION['user_id'] = $row['user_id'];
        ob_end_clean();
        header('Location: /');
        exit();
    } else {
        $_SESSION['flash_error'] = 'Incorrect email or password';
        ob_end_clean();
        header('Location: /login');
        exit();
    }
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
    <title>Login - Profile Database - 4c414f1e</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 80px auto; padding: 0 20px; background: #f5f5f5; }
        h1 { color: #333; text-align: center; }
        .card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        input[type=text], input[type=password] { width: 100%; padding: 9px 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 1em; }
        input[type=submit] { width: 100%; background: #007bff; color: #fff; border: none; padding: 10px; border-radius: 4px; font-size: 1em; cursor: pointer; }
        input[type=submit]:hover { background: #0056b3; }
        .flash-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px 15px; border-radius: 4px; margin-bottom: 15px; }
        .back-link { text-align: center; margin-top: 15px; }
        .back-link a { color: #007bff; text-decoration: none; }
    </style>
    <script>
    function doValidate() {
        console.log('Validating...');
        try {
            var em = document.getElementById('id_email').value;
            var pw = document.getElementById('id_pass').value;
            console.log('Validating email=' + em + ' pw=' + pw);

            if (em == null || em == '') {
                alert('Both fields must be filled out');
                return false;
            }
            if (pw == null || pw == '') {
                alert('Both fields must be filled out');
                return false;
            }
            if (em.indexOf('@') < 0) {
                alert('Please enter a valid email address');
                return false;
            }
            return true;
        } catch(e) {
            return false;
        }
        return false;
    }
    </script>
</head>
<body>
<h1>Login</h1>
<div class="card">
    <?php if ($flash_error): ?>
        <div class="flash-error"><?php echo htmlentities($flash_error); ?></div>
    <?php endif; ?>
    <form method="POST" action="/login">
        <label for="id_email">Email Address</label>
        <input type="text" name="email" id="id_email" placeholder="you@example.com">

        <label for="id_pass">Password</label>
        <input type="password" name="pass" id="id_pass">

        <input type="submit" onclick="return doValidate();" value="Log In">
    </form>
</div>
<div class="back-link"><a href="/">&larr; Back to Profiles</a></div>
</body>
</html>
