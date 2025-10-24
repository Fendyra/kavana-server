<?php

require '../connection.php';
require 'response.php';

$email = $_POST['email'];
$password = md5($_POST['password']);

try {
    $sql = "SELECT * FROM users 
        WHERE 
        email = '$email' AND password = '$password'
        ";
    $statement = $conn->prepare($sql);
    $statement->execute();

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        $responseBody = array(
            "message" => "Login Failed",
        );
        sendResponse(401, $responseBody);
        return;
    }

    $responseBody = array(
        "message" => "Login Success",
        "data" => array(
            "user" => $result,
        ),
    );
    sendResponse(201, $responseBody);
} catch (PDOException $e) {
    $responseBody = array(
        "message" => "Login Failed",
        "error" => $e->getMessage(),
    );
    sendResponse(500, $responseBody);
} finally {
    $conn = null;
}
