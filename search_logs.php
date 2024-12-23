<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$project_id = $_GET['project_id'] ?? null;
$severity = $_GET['severity_level'] ?? null;
$description = $_GET['description'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'timestamp';
$sort_order = $_GET['sort_order'] ?? 'DESC';

$query = "SELECT logs.*, projects.name AS project_name 
          FROM logs 
          JOIN projects ON logs.project_id = projects.id 
          WHERE logs.user_id = :user_id";
$params = ['user_id' => $_SESSION['user_id']];

if ($project_id) {
    $query .= " AND logs.project_id = :project_id";
    $params['project_id'] = $project_id;
}

if ($severity) {
    $query .= " AND logs.severity = :severity";
    $params['severity'] = $severity;
}

if ($description) {
    $query .= " AND logs.message LIKE :description";
    $params['description'] = '%' . $description . '%';
}

$query .= " ORDER BY $sort_by $sort_order";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($logs);
?>
