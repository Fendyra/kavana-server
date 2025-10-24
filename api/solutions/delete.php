<?php

require '../../connection.php';
require '../response.php';

$id = $_GET['id'];

try {
    $sql = "DELETE FROM solutions WHERE id = '$id'";
    $result = $conn->exec($sql);
    if (!$result) {
        $responseBody = array(
            "message" => "Not Found",
        );
        sendResponse(404, $responseBody);
        return;
    }

    $responseBody = array(
        "message" => "Success Delete Solution",
    );
    sendResponse(200, $responseBody);
} catch (PDOException $e) {
    $responseBody = array(
        "message" => "Failed Delete Solution",
        "error" => $e->getMessage(),
    );
    sendResponse(500, $responseBody);
} finally {
    $conn = null;
}
