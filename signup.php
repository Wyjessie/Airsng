<?php
session_start();
require_once "pdo.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!strlen($_POST['email']) || !strlen($_POST['password']) || !strlen($_POST['phone']) || !isset($_POST['choice'])) {
        $_SESSION['error'] = 'All fields are required';
        header('Location: signup.php');
        return;
    }

    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('INSERT INTO users (email, password, phone, user_type) VALUES (:em, :pwd, :phone, :t)');
    $stmt->execute(array(
        ':em' => $_POST['email'],
        ':pwd' => $hashed_password, // Store the hash, not the plain text
        ':phone' => $_POST['phone'],
        ':t' => $_POST['choice']
    ));

    $_SESSION['success'] = 'Account created! Please log in.';
    header('Location: index.php');
    return;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account - AirSnG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .signup-card { max-width: 500px; margin: 50px auto; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); background: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="signup-card">
        <h2 class="text-center mb-4">Join AirSnG</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" required placeholder="+65 ...">
            </div>
            <div class="mb-3">
                <label class="form-label d-block">Register as a:</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="choice" id="st" value="student" required>
                    <label class="form-check-label" for="st">Student</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="choice" id="ht" value="host">
                    <label class="form-check-label" for="ht">Host</label>
                </div>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success btn-lg">Create Account</button>
                <a href="index.php" class="btn btn-link">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
    
