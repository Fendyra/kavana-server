<?php

require '../../connection.php';
require '../response.php';

$userId = $_POST['user_id'];
$createdAt = $_POST['created_at'];

try {
    $sql = "SELECT level, created_at FROM moods
            WHERE
            user_id = '$userId' AND created_at >= '$createdAt'
            ORDER BY created_at
            ";
    $statement = $conn->prepare($sql);
    $statement->execute();
    $moods = $statement->fetchAll(PDO::FETCH_ASSOC);

    $sqlGroup = "SELECT level, COUNT(level) AS total FROM moods
            WHERE
            user_id = '$userId' AND created_at >= '$createdAt'
            GROUP BY level
            ORDER BY level
            ";
    $statementGroup = $conn->prepare($sqlGroup);
    $statementGroup->execute();
    $group = $statementGroup->fetchAll(PDO::FETCH_ASSOC);

    $responseBody = array(
        "message" => "Success Fetch Data",
        "data" => array(
            "moods" => $moods,
            "group" => $group,
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
