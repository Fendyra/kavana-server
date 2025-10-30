<?php
header("Content-Type: application/json");
require '../../connection.php';
require '../response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(405, ["message" => "Method Not Allowed"]);
    exit();
}

$userId = $_POST['user_id'] ?? null;
$amount = $_POST['amount'] ?? null;
$note = $_POST['note'] ?? '';
$createdAt = $_POST['created_at'] ?? null;

if (empty($userId) || empty($amount) || empty($createdAt)) {
    sendResponse(400, ["message" => "User ID, Amount, and Created At are required"]);
    exit();
}

try {
    $sql = "INSERT INTO savings (user_id, amount, note, created_at)
            VALUES (:user_id, :amount, :note, :created_at)";
    
    $statement = $conn->prepare($sql);
    $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $statement->bindParam(':amount', $amount);
    $statement->bindParam(':note', $note, PDO::PARAM_STR);
    $statement->bindParam(':created_at', $createdAt);
    
    $statement->execute();
    
    sendResponse(201, ["message" => "Success Add Savings"]);
} catch (PDOException $e) {
    sendResponse(500, [
        "message" => "Failed Add Savings",
        "error" => $e->getMessage(),
    ]);
} finally {
    $conn = null;
}
?>