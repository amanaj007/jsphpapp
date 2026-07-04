<?php
// Database connection - reads from environment variables
// Set these in Vercel dashboard: Settings > Environment Variables
define('DB_HOST',    getenv('DB_HOST')    ?: 'localhost');
define('DB_PORT',    getenv('DB_PORT')    ?: '3306');
define('DB_NAME',    getenv('DB_NAME')    ?: 'misc');
define('DB_USER',    getenv('DB_USER')    ?: 'fred');
define('DB_PASS',    getenv('DB_PASS')    ?: 'zap');
define('DB_CHARSET', 'utf8');

// Salt for password hashing - keep in PHP code, not in DB
define('SALT', 'XyZzy12*_');

function getPDO() {
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }
}
