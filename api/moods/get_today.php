<?php

require '../../connection.php';
require '../response.php';

$userId = $_POST['user_id'];
$createdAt = $_POST['created_at'];

try {
    $sql = "SELECT level, created_at FROM moods
            WHERE
            user_id = '$userId' AND created_at >= '$createdAt'
            ";
    $statement = $conn->prepare($sql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $responseBody = array(
        "message" => "Success Fetch Data",
        "data" => array(
            "moods" => $result,
        ),
    );
    sendResponse(200, $responseBody);
} catch (PDOException $e) {
    $responseBody = array(
        "message" => "Something went wrong",
        "error" => $e->getMessage(),
    );
    sendResponse(500, $responseBody);
} finally {
    $conn = null;
}
