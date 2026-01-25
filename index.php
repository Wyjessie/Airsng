<?php
session_start();
require_once "pdo.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    // 1. Validate Input
    if (!strlen($_POST['email']) || !strlen($_POST['password']) || empty($_POST['choice'])) {
        $_SESSION['error'] = 'All fields are required';
        header('Location: index.php');
        return;
    }

    // 2. Fetch user based on email and type
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :em AND user_type = :type');
    $stmt->execute(array(
        ':em' => $_POST['email'],
        ':type' => $_POST['choice']
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. Verify the hashed password
    if ($row !== false && password_verify($_POST['password'], $row['password'])) {
        // Success: Regenerate session ID for security
        session_regenerate_id();
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['user_type'] = $row['user_type'];
        
        if ($_SESSION['user_type'] == 'student') {
            header('Location: student.php');
        } else {
            header('Location: host.php');
        }
        return;
    } else {
        // Failure
        $_SESSION['error'] = 'Invalid email, password, or account type';
        header('Location: index.php');
        return;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to AirSnG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .login-card { max-width: 450px; margin: 60px auto; padding: 30px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); background: white; }
        .hero-section { text-align: center; padding: 40px 20px; }
        .brand-color { color: #0d6efd; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="hero-section">
        <h1 class="display-4">Welcome to <span class="brand-color">AirSnG</span></h1>
        <p class="lead text-muted">A peer-to-peer storage solution for students and residents in Singapore.</p>
    </div>

    <div class="login-card">
        <?php
        // Flash messaging for errors/success
        if (isset($_SESSION['error'])) {
            echo ('<div class="alert alert-danger text-center">'. htmlspecialchars($_SESSION['error']).'</div>');
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo ('<div class="alert alert-success text-center">'. htmlspecialchars($_SESSION['success']).'</div>');
            unset($_SESSION['success']);
        }

        if (!isset($_SESSION['user_id'])) { ?>
            <h4 class="mb-4 text-center">Sign In</h4>
            <form method="post">
                <div class="mb-4">
                    <label class="form-label fw-bold">I am joining as a:</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="choice" id="student" value="student" required>
                        <label class="form-check-label" for="student">Student</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="choice" id="host" value="host">
                        <label class="form-check-label" for="host">Host</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required placeholder="name@example.com">
                </div>

                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" name="login" class="btn btn-primary btn-lg">Log In</button>
                </div>
                
                <div class="mt-4 text-center">
                    <span class="text-muted">Don't have an account?</span> 
                    <a href="signup.php" class="text-decoration-none">Sign up here</a>
                </div>
            </form>
        <?php } else { ?>
            <div class="text-center">
                <p class="mb-4">You are currently logged in.</p>
                <div class="d-grid gap-2">
                    <a href="<?= $_SESSION['user_type'] ?>.php" class="btn btn-primary">Go to Dashboard</a>
                    <a href="logout.php" class="btn btn-outline-danger">Log Out</a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

</body>
</html>
