<?php

require '../../connection.php';
require '../response.php';

$userId = $_POST['user_id'];
$level = $_POST['level'];
$createdAt = $_POST['created_at'];

try {
    $sql = "INSERT INTO moods
            (user_id, level, created_at)
            VALUES
            ('$userId', '$level', '$createdAt')
            ";
    $conn->exec($sql);
    $responseBody = array(
        "message" => "Success Add Mood",
    );
    sendResponse(201, $responseBody);
} catch (PDOException $e) {
    $responseBody = array(
        "message" => "Failed Add Mood",
        "error" => $e->getMessage(),
    );
    sendResponse(500, $responseBody);
} finally {
    $conn = null;
}
