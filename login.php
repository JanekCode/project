<?php
require_once 'db.php';

session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        session_start();

        $api_token = bin2hex(random_bytes(32));
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['api_token'] = $api_token;
        $_SESSION['role'] = $user['role']; // We save the user role in the session

        if ($user['role'] == 'user') {
            $_SESSION['account_pending'] = true; // We set the account as pending approval
        } else {
            $_SESSION['account_pending'] = false;
        }

        // Updating the token in the database
        $stmt = $pdo->prepare("UPDATE users SET api_token = :api_token WHERE id = :id");
        $stmt->execute(['api_token' => $api_token, 'id' => $user['id']]);

        header("Location: index.php"); 
        exit;
    } else {
        $error_message = "Invalid login credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Login</h2>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Login: </label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Log in</button>
            <br><br>
            <a href="register.php" class="btn btn-link">Sign up</a>
        </form>
    </div>
</body>
</html>
