<?php

$host = 'localhost';
$username = 'root';
$password = '';
$dbName = 'db_kavana';

try {
    $dsn = "mysql:host=$host;dbname=$dbName";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(array(
        "error" => "ConnectionFailed: " . $e->getMessage(),
    ));
    $connUser = null;
    $conn = null;
}
