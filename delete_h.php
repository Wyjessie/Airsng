<?php
session_start();
require_once "pdo.php";

// Access Control
if (!isset($_SESSION['user_id'])) { die("Access Denied"); }

// After confirmation, finally processing
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'delete') {
        $stmt = $pdo->prepare('DELETE FROM offerings WHERE offer_id = :oid');
        $stmt->execute([':oid' => $_POST['offer_id']]);
        $_SESSION['success'] = 'Offer successfully removed.';
    } 
    else if ($_POST['action'] == 'match') {
        $stmt = $pdo->prepare('UPDATE offerings SET status = :st WHERE offer_id = :oid');
        $stmt->execute(array(
            ':st' => 'booked',
            ':oid' => $_POST['offer_id']
        ));
        $_SESSION['success'] = 'Offer marked as booked!';
    }
    header('Location: host.php');
    return;
}

// Fetch corresponding data
$stmt = $pdo->prepare('SELECT * FROM offerings WHERE offer_id = :oid');
$stmt->execute([':oid' => $_GET['offer_id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    $_SESSION['error'] = "Offer not found.";
    header('Location: host.php');
    return;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Offer - AirSnG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .manage-card { max-width: 600px; margin: 60px auto; }
        .detail-label { font-size: 0.85rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; }
    </style>
</head>
<body>

<div class="container">
    <div class="card manage-card shadow-sm border-0">
        <div class="card-header bg-dark text-white py-3">
            <h5 class="mb-0 text-center">Manage Your Storage Offer</h5>
        </div>
        <div class="card-body p-4">
            
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <span class="detail-label">Location</span>
                    <p class="fw-bold mb-0"><?= htmlentities($row['postal_code']) ?></p>
                </div>
                <div class="col-6 text-end">
                    <span class="detail-label">Rate</span>
                    <p class="fw-bold mb-0 text-success">$<?= htmlentities($row['charges']) ?>/day</p>
                </div>
                <div class="col-12">
                    <div class="bg-light p-3 rounded d-flex justify-content-between">
                        <div>
                            <span class="detail-label d-block">Available From</span>
                            <span><?= $row['available_from'] ?></span>
                        </div>
                        <div class="text-end">
                            <span class="detail-label d-block">Until</span>
                            <span><?= $row['available_to'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <span class="detail-label">Capacity</span>
                    <p class="mb-0"><?= htmlentities($row['max_num']) ?> Piece(s)</p>
                </div>
            </div>

            <hr class="my-4">

            <form method="post">
                <input type="hidden" value="<?= htmlspecialchars($_GET['offer_id']) ?>" name="offer_id">

                <p class="fw-bold mb-3">Update this offer status:</p>
                
                <div class="form-check p-3 border rounded mb-2">
                    <input class="form-check-input ms-0 me-3" type="radio" name="action" value="match" id="match" required>
                    <label class="form-check-label" for="match">
                        <strong class="d-block">Mark as Booked</strong>
                        <small class="text-muted">I have found a student and my space is no longer available.</small>
                    </label>
                </div>

                <div class="form-check p-3 border rounded mb-4">
                    <input class="form-check-input ms-0 me-3" type="radio" name="action" value="delete" id="delete">
                    <label class="form-check-label" for="delete">
                        <strong class="d-block text-danger">Delete Offer</strong>
                        <small class="text-muted">Remove this listing permanently from the search results.</small>
                    </label>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-dark btn-lg">Confirm Selection</button>
                    <a href="host.php" class="btn btn-link text-secondary">Cancel and Go Back</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
