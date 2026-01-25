<?php
session_start();
require_once "pdo.php";

if (!isset($_SESSION['user_id'])) { die("Access Denied"); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] === "delete") {
        $stmt = $pdo->prepare('DELETE FROM requests WHERE request_id = :rid');
        $stmt->execute([':rid' => $_POST['request_id']]);
        $_SESSION['success'] = 'Request deleted successfully.';
    } else if ($_POST['action'] === 'match') {
        $stmt = $pdo->prepare('UPDATE requests SET status = :status WHERE request_id = :rid');
        $stmt->execute([
            ':rid' => $_POST['request_id'],
            ':status' => 'matched'
        ]);
        $_SESSION['success'] = 'Request marked as matched!';
    }
    header('Location: student.php');
    return; 
}

// Fetch current data
$stmt = $pdo->prepare('SELECT * FROM requests WHERE request_id = :rid');
$stmt->execute([':rid' => $_GET['request_id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    $_SESSION['error'] = "Request not found.";
    header('Location: student.php');
    return;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Request - AirSnG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .manage-card { max-width: 600px; margin: 60px auto; }
    </style>
</head>
<body>

<div class="container">
    <div class="card manage-card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-center text-primary">Manage Your Request</h5>
        </div>
        <div class="card-body p-4">
            <div class="row mb-4">
                <div class="col-6">
                    <small class="text-muted d-block">Items & Size</small>
                    <strong><?= htmlentities($row['luggage_amount']) ?> Pieces</strong>
                </div>
                <div class="col-6 text-end">
                    <small class="text-muted d-block">Budget</small>
                    <strong>$<?= htmlentities($row['max_price']) ?>/day</strong>
                </div>
            </div>

            <div class="bg-light p-3 rounded mb-4">
                <div class="row">
                    <div class="col-6 border-end">
                        <small class="text-muted d-block">Drop-off</small>
                        <span><?= $row['drop_date'] ?></span>
                    </div>
                    <div class="col-6 ps-3">
                        <small class="text-muted d-block">Pick-up</small>
                        <span><?= $row['leave_date'] ?></span>
                    </div>
                </div>
            </div>

            <form method="post">
                <input type="hidden" value="<?= $_GET['request_id'] ?>" name="request_id">
                
                <p class="fw-bold mb-3">What would you like to do?</p>
                
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="action" value="match" id="match" required>
                    <label class="form-check-label" for="match">
                        <strong>Mark as Matched</strong>
                        <small class="d-block text-muted">Select this if you've found a host and the deal is confirmed.</small>
                    </label>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="radio" name="action" value="delete" id="delete">
                    <label class="form-check-label text-danger" for="delete">
                        <strong>Delete Permanently</strong>
                        <small class="d-block text-muted">Select this to remove the request from the platform.</small>
                    </label>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">Confirm Action</button>
                    <a href="student.php" class="btn btn-link text-secondary">Back to Dashboard</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>

