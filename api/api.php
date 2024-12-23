<?php
require_once '../db.php';

$valid_severities = [
    'Emergency', 'Alert', 'Critical', 'Error', 'Warning', 'Notice', 'Informational', 'Debug'
];

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Only POST requests are allowed"]);
    exit;
}

// Reading data from POST
$request_data = $_POST;

$is_json_request = isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json';

if ($is_json_request) {
    // Retrieving JSON data for external applications
    $json_input = file_get_contents('php://input');
    $request_data = json_decode($json_input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["error" => "Invalid JSON payload"]);
        exit;
    }
}

if (!isset($request_data['project_id'], $request_data['severity'], $request_data['message'])) {
    echo json_encode(["error" => "Missing required fields"]);
    exit;
}

// Function for checking CSRF
function checkCsrf($request_data) {
    if (!isset($request_data['csrf_token']) || $request_data['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(["error" => "Unauthorized: Invalid CSRF token"]);
        exit;
    }
}

// Function for checking token API
function checkApiToken($pdo) {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        echo json_encode(["error" => "Unauthorized: Missing API token"]);
        exit;
    }

    $auth_parts = explode(' ', $headers['Authorization']);
    if (count($auth_parts) !== 2 || $auth_parts[0] !== 'Bearer') {
        echo json_encode(["error" => "Unauthorized: Invalid API token format"]);
        exit;
    }

    $api_token = $auth_parts[1];
    $stmt = $pdo->prepare("SELECT id FROM users WHERE api_token = :api_token");
    $stmt->execute(['api_token' => $api_token]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(["error" => "Unauthorized: Invalid API token"]);
        exit;
    }

    return $user['id'];
}

// Authorization
$user_id = null;

// For the browser, we check the CSRF token
if (isset($request_data['csrf_token'])) {
    checkCsrf($request_data);
    $user_id = $_SESSION['user_id'];
} else {
    // For API, we check API token
    $user_id = checkApiToken($pdo);
}

// Data assignment
$project_id = $request_data['project_id'];
$severity = $request_data['severity'];
$message = trim($request_data['message']);

$severity = filter_var($request_data['severity'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
if (!in_array($severity, $valid_severities)) {
    echo json_encode(["error" => "Invalid severity level."]);
    exit;
}

$message = filter_var($request_data['message'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');     

if (strlen($message) < 10) {
    if ($is_json_request) {
        echo json_encode([
            "Warning" => "Message must be at least 10 characters long."
        ]);
    } else {

        header('Content-Type: text/html; charset=UTF-8');

        ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Validation Error</title>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    <div class="container mt-5">
            <div class="alert alert-danger">
                <h4 class="alert-heading">An Error Occurred!</h4>
                <p>The message must contain at least 10 characters. Please try again.</p>
            </div>
        <a href="submit_log.php" class="btn btn-primary">Add Log Again</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>
    <?php
     return;
    }
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE id = :project_id AND user_id = :user_id");
$stmt->execute(['project_id' => $project_id, 'user_id' => $user_id]);

$projectExists = $stmt->fetchColumn();

if (!$projectExists) {
    // If the project doesn't exist or doesn't belong to the user
    echo json_encode(["error" => "You do not have access to this project."]);
    exit;
}

// Success add the log to the database
$stmt = $pdo->prepare("
    INSERT INTO logs (user_id, project_id, severity, message, timestamp) 
    VALUES (:user_id, :project_id, :severity, :message, NOW())
");

$result = $stmt->execute([
    'user_id' => $user_id,
    'project_id' => $project_id,
    'severity' => $severity,
    'message' => $message
]);

if ($is_json_request) {
    echo json_encode([
        "success" => $result ? "Log added successfully" : "Failed to add log"
    ]);
} else {
    header('Content-Type: text/html; charset=UTF-8');

    ?>
    <!DOCTYPE html>
    <html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Log Added</title>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    <div class="container mt-5">
        <?php if ($result): ?>
            <div class="alert alert-success">
                <h4 class="alert-heading">The log has been successfully added!</h4>
                <p>Your log has been saved in the system. Thank you for adding the log!</p>
                <hr>
                <p class="mb-0">You can now proceed to <a href="/../view_logs.php">viewing the logs</a>.</p>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <h4 class="alert-heading">An error occurred!</h4>
                <p>Failed to add the log. Please try again later.</p>
            </div>
        <?php endif; ?>
        <a href="/../submit_log.php" class="btn btn-primary">Add another log.</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>
    <?php
}
?>
