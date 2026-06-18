<?php
$config = [
    'host'    => 'localhost',
    'dbname'  => 'mangashelf',
    'user'    => 'root',
    'pass'    => '',
    'charset' => 'utf8mb4',
];

$credentials_path = __DIR__ . '/credentials.php';
if (file_exists($credentials_path)) {
    $config = array_replace($config, require $credentials_path);
}

$dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=%s',
    $config['host'],
    $config['dbname'],
    $config['charset']
);

$pdo = new PDO($dsn, $config['user'], $config['pass'], [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);

function db(): PDO
{
    return $GLOBALS['pdo'];
}

return $config;
