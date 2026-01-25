<?php
session_start();
require_once "pdo.php";

if (!isset($_SESSION['user_id'])) { die("Access Denied"); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Getting the latitude and longitute from our helper function
    $coords = getCoordinates($_POST['postal_code']);

    if (empty($_POST['af']) || empty($_POST['at']) ||
        empty($_POST['charges']) || empty($_POST['postal_code']) || empty($_POST['service'])) {
        $_SESSION['error'] = 'All fields are required';
    } else if (!is_numeric($_POST['num']) || !is_numeric($_POST['charges'])) {
        $_SESSION['error'] = 'Price and Amount must be numbers';
    } else if (!$coords) {
        $_SESSION['error'] = 'Invalid Singapore Postal Code';
    }
    else {
        $stmt = $pdo->prepare('INSERT INTO offerings(user_id, available_from, available_to, 
            max_num, charges, postal_code, lat, lng, services, status, location_name)
            VALUES (:uid, :af, :at, :mn, :ch, :pc, :lt, :lg, :sv, :st, :ln)');
        
        $stmt->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':af' => $_POST['af'],
            ':at' => $_POST['at'],
            ':mn' => $_POST['num'],
            ':ch' => $_POST['charges'],
            ':pc' => $_POST['postal_code'],
            ':lt' => $coords['lat'],
            ':lg' => $coords['lng'],
            ':sv' => $_POST['service'],
            ':st' => 'active',
            ':ln' => $coords['name']
        ));

        $_SESSION['success'] = 'Storage offer created successfully!';
        header('Location: host.php');
        exit;
    }
    header('Location: offer.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Offer - AirSnG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .offer-card { max-width: 700px; margin: 40px auto; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); background: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="offer-card">
        <h2 class="text-center mb-4">Host Your Space</h2>
        
        <?php
        if (isset($_SESSION['error'])) {
            echo('<div class="alert alert-danger">'.htmlspecialchars($_SESSION['error']).'</div>');
            unset($_SESSION['error']);
        }
        ?>

        <form method="post">
            <h5 class="text-primary border-bottom pb-2 mb-3">1. Availability</h5>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Available From</label>
                    <input type="date" name="af" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Available To</label>
                    <input type="date" name="at" class="form-control" required>
                </div>
            </div>

            <h5 class="text-primary border-bottom pb-2 mb-3">2. Storage Capacity</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Max Pieces</label>
                    <input type="number" name="num" class="form-control" placeholder="e.g. 5" required>
                </div>
            </div>

            <h5 class="text-primary border-bottom pb-2 mb-3">3. Pricing & Location</h5>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Daily Rate (SGD/Item)</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="text" name="charges" class="form-control" placeholder="4.00" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-bold">Your Postal Code</label>
                    <input type="text" name="postal_code">
                </div>
            </div>

            <h5 class="text-primary border-bottom pb-2 mb-3">4. Service Level</h5>
            <div class="mb-4">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="service" id="s1" value="1" required>
                    <label class="form-check-label" for="s1">
                        <strong>Level 1:</strong> Standard storage space only.
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="service" id="s2" value="2">
                    <label class="form-check-label" for="s2">
                        <strong>Level 2:</strong> Assistance with moving items.
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="service" id="s3" value="3">
                    <label class="form-check-label" for="s3">
                        <strong>Level 3:</strong> Full pick-up and delivery service.
                    </label>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success btn-lg shadow-sm">Create Storage Offer</button>
                <a href="host.php" class="btn btn-link text-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>

