<?php
require_once __DIR__ . '/session_handler.php';
start_db_session();
unset($_SESSION['name']);
unset($_SESSION['user_id']);
ob_end_clean();
header('Location: /');
exit();
