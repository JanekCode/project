<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// By default, sort by date (timestamp)
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'timestamp';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'DESC';

// We check if the selected criteria are valid to prevent SQL Injection
$allowed_sort_columns = ['timestamp', 'severity', 'project_name'];
$allowed_sort_orders = ['ASC', 'DESC'];

if (!in_array($sort_by, $allowed_sort_columns)) {
    $sort_by = 'timestamp'; // Set default if invalid column
}

if (!in_array($sort_order, $allowed_sort_orders)) {
    $sort_order = 'DESC'; // Set default if invalid order
}

// Defining the number of logs per page
$logs_per_page = 10;

// Retrieving the current page from GET parameters (default is 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1; // Default to the first page if the page number is invalid
}

// Calculate how many records to skip before starting to fetch the data
$offset = ($page - 1) * $logs_per_page;

$query = "
    SELECT logs.*, projects.name AS project_name 
    FROM logs 
    JOIN projects ON logs.project_id = projects.id 
    WHERE logs.user_id = :user_id
    ORDER BY $sort_by $sort_order
    LIMIT $logs_per_page OFFSET $offset
";

$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $_SESSION['user_id']]);

$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT COUNT(*) AS total_logs FROM logs WHERE user_id = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$total_logs = $stmt->fetch(PDO::FETCH_ASSOC)['total_logs'];

$total_pages = ceil($total_logs / $logs_per_page);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Logs</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 20px;
        }
        .container {
            max-width: 1200px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Your Logs</h1>

    <form method="GET" action="view_logs.php">
        <div class="form-row">
            <div class="col">
                <label for="sort_by">Sort by:</label>
                <select class="form-control" id="sort_by" name="sort_by">
                    <option value="timestamp" <?= ($sort_by == 'timestamp') ? 'selected' : '' ?>>Data</option>
                    <option value="severity" <?= ($sort_by == 'severity') ? 'selected' : '' ?>>Level of importance</option>
                    <option value="project_name" <?= ($sort_by == 'project_name') ? 'selected' : '' ?>>Project Name</option>
                </select>
            </div>
            <div class="col">
                <label for="sort_order">Sort Order:</label>
                <select class="form-control" id="sort_order" name="sort_order">
                    <option value="ASC" <?= ($sort_order == 'ASC') ? 'selected' : '' ?>>Ascending</option>
                    <option value="DESC" <?= ($sort_order == 'DESC') ? 'selected' : '' ?>>Descending</option>
                </select>
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary mt-4">Sort</button>
            </div>
        </div>
    </form>

    <!-- Excel Export logs (CSV) -->
    <a href="export_logs.php" class="btn btn-success mb-4">Export Logs to Excel</a>

    <table class="table table-striped table-bordered mt-4">
        <thead>
            <tr>
                <th scope="col">Timestamp</th>
                <th scope="col">Project</th>
                <th scope="col">Severity</th>
                <th scope="col">Message</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['timestamp']) ?></td>
                    <td><?= htmlspecialchars($log['project_name']) ?></td>
                    <td>
                        <?php
                        switch ($log['severity']) {
                            case 'Emergency':
                                echo '<span class="badge badge-danger">' . htmlspecialchars($log['severity']) . '</span>';
                                break;
                            case 'Alert':
                                echo '<span class="badge badge-warning">' . htmlspecialchars($log['severity']) . '</span>';
                                break;
                            case 'Critical':
                                echo '<span class="badge badge-danger">' . htmlspecialchars($log['severity']) . '</span>';
                                break;
                            case 'Error':
                                echo '<span class="badge badge-danger">' . htmlspecialchars($log['severity']) . '</span>';
                                break;
                            case 'Warning':
                                echo '<span class="badge badge-warning">' . htmlspecialchars($log['severity']) . '</span>';
                                break;
                            case 'Notice':
                                echo '<span class="badge badge-info">' . htmlspecialchars($log['severity']) . '</span>';
                                break;
                            case 'Informational':
                                echo '<span class="badge badge-secondary">' . htmlspecialchars($log['severity']) . '</span>';
                                break;
                            case 'Debug':
                                echo '<span class="badge badge-light">' . htmlspecialchars($log['severity']) . '</span>';
                                break;
                        }
                        ?>
                    </td>
                    <td><?= htmlspecialchars($log['message']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <!-- Button Previous Page -->
            <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page - 1 ?>&sort_by=<?= $sort_by ?>&sort_order=<?= $sort_order ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <!-- Number of Page -->
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&sort_by=<?= $sort_by ?>&sort_order=<?= $sort_order ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <!-- Button Next Page -->
            <li class="page-item <?= ($page == $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page + 1 ?>&sort_by=<?= $sort_by ?>&sort_order=<?= $sort_order ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>

    <br>
    <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
