<?php
session_start();
require_once "pdo.php";

if (!isset($_SESSION['user_id'])) {
    die("Access Denied");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Getting the latitude and longitute from our helper function
    $coords = getCoordinates($_POST['postal_code']);

    if (empty($_POST['amount']) || empty($_POST['price']) || 
        empty($_POST['drop']) || empty($_POST['leave'])) {
        $_SESSION['error'] = 'All fields are required';
        header('Location: add.php');
        return;
    }
    if (!is_numeric($_POST['amount']) || !is_numeric($_POST['price'])) {
        $_SESSION['error'] = 'Amount and price must be numeric';
        header('Location: add.php');
        return;
    }


    $stmt = $pdo->prepare('INSERT INTO requests (user_id, luggage_amount, drop_date, leave_date, max_price, postal_code, location_name, lat, lng, status) 
                           VALUES (:uid, :amount, :drop, :leave, :price, :pc, :ln, :lt, :lg, "active")');
    $stmt->execute([
        ':uid'    => $_SESSION['user_id'],
        ':amount' => $_POST['amount'],
        ':drop'   => $_POST['drop'],
        ':leave'  => $_POST['leave'],
        ':price'  => $_POST['price'],
        ':pc'     => $_POST['postal_code'],
        ':ln'     => $coords['name'],
        ':lt'     => $coords['lat'],
        ':lg'     => $coords['lng']
    ]);
    
    $_SESSION['success'] = 'Request added!';
    header('Location: student.php');
    return;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Request - AirSnG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .form-card { max-width: 600px; margin: 40px auto; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); background: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="form-card">
        <h2 class="mb-4 text-center">Add Storage Request</h2>

        <?php
        if (isset($_SESSION['error'])){
            echo('<div class="alert alert-danger">'.htmlspecialchars($_SESSION['error']).'</div>');
            unset($_SESSION['error']);
        }
        ?>

        <form method="post">
            <div class="mb-4">
                <label class="form-label fw-bold">Number of Luggage Pieces</label>
                <input type="number" name="amount" class="form-control" placeholder="e.g. 2" required>
            </div>
            
            
            <div class="row mb-4">
                <div class="col">
                    <label class="form-label fw-bold">From (Drop-off)</label>
                    <input type="date" name="drop" class="form-control" required>
                </div>
                <div class="col">
                    <label class="form-label fw-bold">To (Pick-up)</label>
                    <input type="date" name="leave" class="form-control" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Max Budget (SGD / Day / Bag)</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="text" name="price" class="form-control" placeholder="5.00" required>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold">Postal Code</label>
                <div class="row ms-1">
                    <input type="text" name="postal_code">
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">Post Request</button>
                <a href="student.php" class="btn btn-link text-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
