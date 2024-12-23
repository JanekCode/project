<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized: User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];


// Retrieve logs from the last 24 hours for the given user
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS logs_last_24h
    FROM logs
    WHERE user_id = :user_id AND timestamp >= NOW() - INTERVAL 1 DAY
");
$stmt->execute(['user_id' => $user_id]);
$logs_last_24h = $stmt->fetch(PDO::FETCH_ASSOC)['logs_last_24h'];

// Retrieve logs from the last 1 hours for the given user
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS logs_last_1h
    FROM logs
    WHERE user_id = :user_id AND timestamp >= NOW() - INTERVAL 1 HOUR
");
$stmt->execute(['user_id' => $user_id]);
$logs_last_1h = $stmt->fetch(PDO::FETCH_ASSOC)['logs_last_1h'];

// Group logs by project and level of importance for the given user
$stmt = $pdo->prepare("
    SELECT 
        projects.name AS project_name, 
        logs.severity, 
        COUNT(*) AS log_count 
    FROM logs
    JOIN projects ON logs.project_id = projects.id
    WHERE logs.user_id = :user_id
    GROUP BY logs.project_id, logs.severity
");
$stmt->execute(['user_id' => $user_id]);
$log_breakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return results in jSON format
echo json_encode([
    "logs_last_24h" => $logs_last_24h,
    "logs_last_1h" => $logs_last_1h,
    "log_breakdown" => $log_breakdown
]);
?>