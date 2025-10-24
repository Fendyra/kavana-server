<?php

require '../../connection.php';
require '../response.php';

$userId = $_POST['user_id'];

try {
    $sql = "SELECT 
            *
            FROM solutions
            WHERE
            user_id = '$userId'
            ORDER BY problem
            ";
    $statement = $conn->prepare($sql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $responseBody = array(
        "message" => "Success Fetch Data",
        "data" => array(
            "solutions" => $result,
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
