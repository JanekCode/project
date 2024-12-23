<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Generate a CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Log Entry</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Submit Log Entry</h1>

    <!-- Form to submit a new log entry -->
    <form action="api/api.php" method="POST">
        <!-- Token hidden field for CSRF -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <!-- Token hidden field for API authorization -->
        <input type="hidden" name="api_token" value="<?= $_SESSION['api_token'] ?>">

        <!-- Project ID selection -->
        <div class="form-group">
            <label for="project_id">Project:</label>
            <select class="form-control" name="project_id" required>
                <option value="">Select a project</option>
                <?php
                // Retrieve projects from the database
                require_once 'db.php';

                // Retrieve user projects
                $stmt = $pdo->prepare("SELECT * FROM projects WHERE user_id = :user_id");
                $stmt->execute(['user_id' => $_SESSION['user_id']]);
                $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($projects as $project) {
                    echo "<option value='{$project['id']}'>{$project['name']}</option>";
                }
                ?>
            </select>
        </div>

        <!-- Severity level selection -->
        <div class="form-group">
            <label for="severity">Severity Level:</label>
            <select class="form-control" name="severity" required>
                <option value="Emergency">Emergency</option>
                <option value="Alert">Alert</option>
                <option value="Critical">Critical</option>
                <option value="Error">Error</option>
                <option value="Warning">Warning</option>
                <option value="Notice">Notice</option>
                <option value="Informational">Informational</option>
                <option value="Debug">Debug</option>
            </select>
        </div>

        <!-- Message textarea -->
        <div class="form-group">
            <label for="message">Log Message:</label>
            <textarea class="form-control" name="message" rows="4" required></textarea>
        </div>

        <!-- Submit button -->
        <button type="submit" class="btn btn-primary">Submit Log</button>
    </form>

    <br>
    <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

<!-- Bootstrap JS (optional, for better functionality) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
