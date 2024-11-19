<?php
// test-database-connection.php
header('Content-Type: application/json');

try {
    $dsn = 'mysql:host=localhost;voluntariadodonaciones;charset=utf8mb4';
    $username = 'root';
    $password = '';
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Connection successful']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
