<?php

header("Content-Type: application/json");
require '../../connection.php';
require '../response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(405, ["message" => "Method Not Allowed"]);
    exit();
}

$userId = $_POST['user_id'] ?? null;

if (empty($userId)) {
    sendResponse(400, ["message" => "User ID is required"]);
    exit();
}

try {
    $sql = "SELECT
            id, title, category, start_event, end_event,
            location_name, latitude, longitude
            FROM agendas
            WHERE
            user_id = :user_id
            ORDER BY start_event";

    $statement = $conn->prepare($sql);
    $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $statement->execute();

    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as &$row) {
        if (isset($row['latitude'])) $row['latitude'] = (float)$row['latitude'];
        if (isset($row['longitude'])) $row['longitude'] = (float)$row['longitude'];
    }
    unset($row);

    $responseBody = [
        "message" => "Success Fetch Data",
        "data" => [
            "agendas" => $result,
        ],
    ];
    sendResponse(200, $responseBody);
} catch (PDOException $e) {
    $responseBody = [
        "message" => "Something went wrong",
        "error" => $e->getMessage(),
    ];
    sendResponse(500, $responseBody);
} finally {
    $conn = null;
}
?>
