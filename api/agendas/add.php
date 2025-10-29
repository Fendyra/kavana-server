<?php

header("Content-Type: application/json");
require '../../connection.php';
require '../response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(405, ["message" => "Method Not Allowed"]);
    exit();
}

$userId = $_POST['user_id'] ?? null;
$title = $_POST['title'] ?? null;
$category = $_POST['category'] ?? null;
$startEvent = $_POST['start_event'] ?? null;
$endEvent = $_POST['end_event'] ?? null;
$description = $_POST['description'] ?? null;
$locationName = (!empty($_POST['location_name'])) ? $_POST['location_name'] : null;
$latitude = (!empty($_POST['latitude'])) ? (float)$_POST['latitude'] : null;
$longitude = (!empty($_POST['longitude'])) ? (float)$_POST['longitude'] : null;

if (empty($userId) || empty($title) || empty($category) || empty($startEvent) || empty($endEvent)) {
    sendResponse(400, ["message" => "User ID, Title, Category, Start Event, and End Event are required"]);
    exit();
}

try {
    $sql = "INSERT INTO agendas
            (user_id, title, category, start_event, end_event, description, location_name, latitude, longitude)
            VALUES
            (:user_id, :title, :category, :start_event, :end_event, :description, :location_name, :latitude, :longitude)";

    $statement = $conn->prepare($sql);

    $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $statement->bindParam(':title', $title, PDO::PARAM_STR);
    $statement->bindParam(':category', $category, PDO::PARAM_STR);
    $statement->bindParam(':start_event', $startEvent);
    $statement->bindParam(':end_event', $endEvent);
    $statement->bindParam(':description', $description, PDO::PARAM_STR);
    $statement->bindParam(':location_name', $locationName, $locationName === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $statement->bindParam(':latitude', $latitude, $latitude === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $statement->bindParam(':longitude', $longitude, $longitude === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

    $statement->execute();

    sendResponse(201, ["message" => "Success Add Agenda"]);
} catch (PDOException $e) {
    sendResponse(500, [
        "message" => "Failed Add Agenda",
        "error" => $e->getMessage(),
    ]);
} finally {
    $conn = null;
}
?>
