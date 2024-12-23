<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Random token CSRF
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "Unauthorized: Invalid CSRF token";
        exit;
    }

    
    $name = $_POST['project_name'];

    // The user can't perform any actions. They need to be an admin in the database to have the required permissions.
    if (isset($_SESSION['account_pending']) && $_SESSION['account_pending'])
    {
        $error_message = "You do not have permission to create a project.";
    }
    else if (empty($name)) {
        $error_message = "The project name cannot be empty!";
    }
    else if (strlen($name) > 35) {
        $error_message = "The project name cannot exceed 40 characters!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO projects (name, user_id) VALUES (:name, :user_id)");
        if ($stmt->execute(['name' => $name, 'user_id' => $_SESSION['user_id']])) {
            $success_message = "Project created successfully!";
        } else {
            $error_message = "Project creation error!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create a project</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Create a new project</h2>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php elseif (isset($success_message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <form action="create_project.php" method="POST">
            <!-- Token CSRF -->
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="form-group">
                <label for="project_name">Project Name:</label>
                <input type="text" class="form-control" id="project_name" name="project_name" maxlength="40" required 
                class="form-control" placeholder="Enter the project name (max. 40 characters)">
            </div>
            <button type="submit" class="btn btn-primary">Create a project</button>
        </form>
        
        <br>
        <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</body>
</html>