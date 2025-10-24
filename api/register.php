<?php

require '../connection.php';
require 'response.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = md5($_POST['password']);

try {
    $sqlCheckEmail = "SELECT * FROM users WHERE email = '$email'";
    $statement = $conn->prepare($sqlCheckEmail);
    $statement->execute();

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $responseBody = array(
            "message" => "Email already registered",
        );
        sendResponse(401, $responseBody);
        return;
    }

    $sqlInsert = "INSERT INTO users
                 (name, email, password)
                 VALUES
                 ('$name', '$email', '$password')
                 ";
    $conn->exec($sqlInsert);
    $responseBody = array(
        "message" => "Register Success",
    );
    sendResponse(201, $responseBody);
} catch (PDOException $e) {
    $responseBody = array(
        "message" => "Register Failed",
        "error" => $e->getMessage(),
    );
    sendResponse(500, $responseBody);
} finally {
    $conn = null;
}
