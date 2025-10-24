<?php

require '../../connection.php';
require '../response.php';

$userId = $_POST['user_id'];
$query = $_POST['query'];

try {
    $sql = "SELECT * FROM solutions
            WHERE
            user_id = '$userId' AND
            (
                summary LIKE '%$query%' OR
                problem LIKE '%$query%' OR
                solution LIKE '%$query%' OR
                reference LIKE '%$query%'
            )
            ";
    $statement = $conn->prepare($sql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    if (!$result) {
        $responseBody = array(
            "message" => "Not Found",
        );
        sendResponse(404, $responseBody);
        return;
    }

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
