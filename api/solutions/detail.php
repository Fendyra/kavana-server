<?php

require '../../connection.php';
require '../response.php';

$id = $_GET['id'];

try {
    $sql = "SELECT * FROM solutions WHERE id = '$id'";
    $statement = $conn->prepare($sql);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        $responseBody = array(
            "message" => "Not Found",
        );
        sendResponse(404, $responseBody);
        return;
    }

    $responseBody = array(
        "message" => "Found",
        "data" => array(
            "solution" => $result,
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
