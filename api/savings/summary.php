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
    // Get total savings
    $sqlTotal = "SELECT COALESCE(SUM(amount), 0) as total_savings
                 FROM savings
                 WHERE user_id = :user_id";
    $stmtTotal = $conn->prepare($sqlTotal);
    $stmtTotal->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtTotal->execute();
    $totalResult = $stmtTotal->fetch(PDO::FETCH_ASSOC);
    
    // Get monthly total
    $sqlMonthly = "SELECT COALESCE(SUM(amount), 0) as monthly_total
                   FROM savings
                   WHERE user_id = :user_id
                   AND MONTH(created_at) = MONTH(CURRENT_DATE())
                   AND YEAR(created_at) = YEAR(CURRENT_DATE())";
    $stmtMonthly = $conn->prepare($sqlMonthly);
    $stmtMonthly->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtMonthly->execute();
    $monthlyResult = $stmtMonthly->fetch(PDO::FETCH_ASSOC);
    
    // Get savings days (distinct dates)
    $sqlDays = "SELECT COUNT(DISTINCT DATE(created_at)) as savings_days
                FROM savings
                WHERE user_id = :user_id";
    $stmtDays = $conn->prepare($sqlDays);
    $stmtDays->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtDays->execute();
    $daysResult = $stmtDays->fetch(PDO::FETCH_ASSOC);
    
    // Get recent savings
    $sqlRecent = "SELECT * FROM savings
                  WHERE user_id = :user_id
                  ORDER BY created_at DESC
                  LIMIT 5";
    $stmtRecent = $conn->prepare($sqlRecent);
    $stmtRecent->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtRecent->execute();
    $recentSavings = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);
    
    $responseBody = [
        "message" => "Success Fetch Data",
        "data" => [
            "total_savings" => $totalResult['total_savings'],
            "monthly_total" => $monthlyResult['monthly_total'],
            "savings_days" => $daysResult['savings_days'],
            "recent_savings" => $recentSavings,
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