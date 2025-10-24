<?php

require '../../connection.php';
require '../response.php';

$userId = $_POST['user_id'];

try {
    $sql = "SELECT id, title, category, start_event, end_event FROM agendas 
        WHERE 
        user_id = '$userId' 
        ";
    $statement = $conn->prepare($sql);
    $statement->execute();

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    $responseBody = array(
        "message" => "Success Fetch Data",
        "data" => array(
            "agendas" => $result,
        ),
    );
    sendResponse(200, $responseBody);
} catch (PDOException $e) {
    $responseBody = array(
        "message" => "Something Went Wrong",
        "error" => $e->getMessage(),
    );
    sendResponse(500, $responseBody);
} finally {
    $conn = null;
}
