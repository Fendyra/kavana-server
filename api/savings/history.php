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
    $sql = "SELECT * FROM savings
            WHERE user_id = :user_id
            ORDER BY created_at DESC";
    
    $statement = $conn->prepare($sql);
    $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $statement->execute();
    
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    $responseBody = [
        "message" => "Success Fetch Data",
        "data" => [
            "savings" => $result,
        ],
    ];
    sendResponse(200, $responseBody);
} catch (PDOException $e) {
    sendResponse(500, [
        "message" => "Something went wrong",
        "error" => $e->getMessage(),
    ]);
} finally {
    $conn = null;
}
?>