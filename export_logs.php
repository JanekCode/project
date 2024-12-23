<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Download logs of the user
$stmt = $pdo->prepare("
    SELECT logs.*, projects.name AS project_name 
    FROM logs 
    JOIN projects ON logs.project_id = projects.id 
    WHERE logs.user_id = :user_id
    ORDER BY logs.timestamp DESC
");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="logs.csv"');
header('Content-Encoding: UTF-8'); 

// Open a stream for writing CSV
$output = fopen('php://output', 'w');

// Set the separator to semicolon (for Excel)
$delimiter = ';';

// Add column headers
fputcsv($output, ['Timestamp', 'Project', 'Severity', 'Message'], $delimiter);

foreach ($logs as $log) {
       $log_data = [
        $log['timestamp'],
        $log['project_name'],
        $log['severity'],
        $log['message']
    ];

    fputcsv($output, $log_data, $delimiter);
}

fclose($output);
exit;
