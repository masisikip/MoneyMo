<?php
$load_env_path = __DIR__ . '/load-env.php';
include_once($load_env_path);

try {
    $hostname = getenv('DB_HOST');
    $port = getenv('DB_PORT');
    $username = getenv('DB_USER');
    $password = getenv('DB_PASS');
    $defaultSchema = getenv('DB_NAME');
    $charset = getenv('DB_CHARSET');

    $dsn = "mysql:host=$hostname;dbname=$defaultSchema;charset=$charset;port=$port";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}