<?php
require_once 'db.php';
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h1>Welcome in dashboard, <?php echo htmlspecialchars($username); ?>!</h1>
    <p><a href="logout.php">Log out</a></p>

    <?php if (isset($_SESSION['account_pending']) && $_SESSION['account_pending']): ?>
        <div class="alert alert-warning">
        Your account requires approval by the administrator. Please wait for the approval.
        </div>
    <?php endif; ?>
    <h3>Log statistics</h3>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Logs from the last 24 hours</h5>
                    <p id="logs-24h">Loading...</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Logs from the last hour</h5>
                    <p id="logs-1h">Loading...</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Log division</h5>
                    <button class="btn btn-primary" id="view-breakdown-btn">View details</button>
                    <div id="log-breakdown" style="display: none;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Project</th>
                                    <th>Importance level</th>
                                    <th>Number of log</th>
                                </tr>
                            </thead>
                            <tbody id="log-breakdown-body">
                                <!-- Ajax insert logs -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mb-4">Options:</h3>
    <div class="list-group">
    <a href="create_project.php" class="list-group-item list-group-item-action d-flex align-items-center">
        <i class="bi bi-plus-circle me-3"></i> <span style="margin-left: 5px;">Create new project</span>
    </a>
    <a href="submit_log.php" class="list-group-item list-group-item-action d-flex align-items-center">
        <i class="bi bi-pencil-square me-3"></i> <span style="margin-left: 5px;">Save new log</span>
    </a>
    <a href="view_logs.php" class="list-group-item list-group-item-action d-flex align-items-center">
        <i class="bi bi-eye me-3"></i> <span style="margin-left: 5px;">View logs</span>
    </a>
    </div>
</div>

<script>
    // Download statistic from the stats.php
    async function fetchStats() {
        const response = await fetch('stats.php');
        const data = await response.json();

        document.getElementById('logs-24h').innerText = data.logs_last_24h;
        document.getElementById('logs-1h').innerText = data.logs_last_1h;

        // Dynamically update the breakdown table
        const breakdownTableBody = document.getElementById('log-breakdown-body');
        breakdownTableBody.innerHTML = '';
        data.log_breakdown.forEach(log => {
            const row = `<tr>
                <td>${log.project_name}</td>
                <td>${log.severity}</td>
                <td>${log.log_count}</td>
            </tr>`;
            breakdownTableBody.innerHTML += row;
        });
    }

    // Initializing statistics loading
    fetchStats();

    // Toggle visibility of breakdown details
    document.getElementById('view-breakdown-btn').addEventListener('click', function() {
        const breakdownDiv = document.getElementById('log-breakdown');
        breakdownDiv.style.display = breakdownDiv.style.display === 'none' ? 'block' : 'none';
    });
</script>
</body>
</html>