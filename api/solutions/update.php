<?php

require '../../connection.php';
require '../response.php';

$id = $_POST['id'];
$userId = $_POST['user_id'];
$summary = $_POST['summary'];
$problem = $_POST['problem'];
$solution = $_POST['solution'];
$reference = $_POST['reference'];

try {
    $sql = "UPDATE solutions
            SET
            user_id  = '$userId',
            summary  = '$summary',
            problem  = '$problem',
            solution = '$solution',
            reference= '$reference'
            WHERE id = '$id'
            ";
    $conn->exec($sql);
    $responseBody = array(
        "message" => "Success Update Solution",
    );
    sendResponse(200, $responseBody);
} catch (PDOException $e) {
    $responseBody = array(
        "message" => "Failed Update Solution",
        "error" => $e->getMessage(),
    );
    sendResponse(500, $responseBody);
} finally {
    $conn = null;
}
