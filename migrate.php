<?php

$host = 'localhost';
$username = 'root';
$password = '';
$dbName = 'db_kavana';

try {
    $connUser = new PDO("mysql:host=$host", $username, $password);
    $connUser->exec("DROP DATABASE IF EXISTS $dbName");
    echo "Database Dropped\n";
    $connUser->exec("CREATE DATABASE $dbName");
    echo "Database Created\n";
    $connUser = null;

    $dsn = "mysql:host=$host;dbname=$dbName";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    migrateUsers($conn);
    migrateMoods($conn);
    migrateSolutions($conn);
    migrateAgendas($conn);
    $conn = null;
} catch (PDOException $e) {
    echo json_encode(array(
        "error" => "ConnectionFailed: " . $e->getMessage(),
    ));
    $connUser = null;
    $conn = null;
}


function migrateUsers($conn)
{
    try {
        $sql = "CREATE TABLE users (
                id INT(11) UNSIGNED AUTO_INCREMENT,
                name VARCHAR(30) NOT NULL,
                email VARCHAR(30) NOT NULL,
                password VARCHAR(50) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
                )";
        $conn->exec($sql);
        echo "Table users craeted successfully";
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    echo "\n";
}

function migrateMoods($conn)
{
    try {
        $sql = "CREATE TABLE moods (
                id INT(11) UNSIGNED AUTO_INCREMENT,
                user_id INT(11) UNSIGNED,
                level INT(1) NOT NULL,            
                created_at VARCHAR(30) NOT NULL,
                PRIMARY KEY (id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )";
        $conn->exec($sql);
        echo "Table moods craeted successfully";
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    echo "\n";
}

function migrateSolutions($conn)
{
    try {
        $sql = "CREATE TABLE solutions (
                id INT(11) UNSIGNED AUTO_INCREMENT,
                user_id INT(11) UNSIGNED,
                summary TEXT NOT NULL,
                problem TEXT NOT NULL,
                solution TEXT NOT NULL,
                reference TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )";
        $conn->exec($sql);
        echo "Table solutions created successfully";
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    echo "\n";
}

function migrateAgendas($conn)
{
    try {
        $sql = "CREATE TABLE agendas (
                id INT(11) UNSIGNED AUTO_INCREMENT,
                user_id INT(11) UNSIGNED,
                title VARCHAR(100) NOT NULL,
                category VARCHAR(20) NOT NULL,
                start_event VARCHAR(30) NOT NULL,
                end_event VARCHAR(30) NOT NULL,
                description TEXT NOT NULL,
                PRIMARY KEY (id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )";
        $conn->exec($sql);
        echo "Table agendas created successfully";
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    echo "\n";
}
