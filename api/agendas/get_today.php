<?php
header("Content-Type: application/json");
require '../../connection.php';
require '../response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(405, ["message" => "Method Not Allowed"]);
    exit();
}

$userId = $_POST['user_id'] ?? null;
$startDate = $_POST['start_date'] ?? null;

if (empty($userId) || empty($startDate)) {
    sendResponse(400, ["message" => "User ID and Start Date are required"]);
    exit();
}

if (strlen($startDate) == 10) {
    $startDate .= ' 00:00:00';
}

$endDate = substr($startDate, 0, 10) . ' 23:59:59';

try {
    $sql = "SELECT
                id, title, category, start_event, end_event,
                location_name, latitude, longitude
            FROM agendas
            WHERE
                user_id = :user_id
                AND start_event >= :start_date AND start_event <= :end_date
            ORDER BY start_event";

    $statement = $conn->prepare($sql);
    $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $statement->bindParam(':start_date', $startDate);
    $statement->bindParam(':end_date', $endDate);
    $statement->execute();

    $agendas = $statement->fetchAll(PDO::FETCH_ASSOC);

    foreach ($agendas as &$row) {
        if (isset($row['latitude'])) $row['latitude'] = (float)$row['latitude'];
        if (isset($row['longitude'])) $row['longitude'] = (float)$row['longitude'];
    }
    unset($row);

    $responseBody = [
        "message" => "Success Fetch Data",
        "data" => ["agendas" => $agendas],
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
