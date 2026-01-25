<?php
session_start();
require_once "pdo.php";

if (!isset($_SESSION['user_id'])) {
    die("Access Denied");
}

// Check for active requests to decide if we show the "Add" button
$stmt = $pdo->prepare('SELECT * FROM requests WHERE user_id = :uid AND status = "active"');
$stmt->execute(array(':uid' => $_SESSION['user_id']));
$hasActive = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard - AirSnG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1">AirSnG Student Portal</span>
        <div class="d-flex">
            <a href="match.php" class="btn btn-light btn-sm me-2">Find Matches</a>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <?php
    if (isset($_SESSION['error'])) {
        echo('<div class="alert alert-danger">'.htmlentities($_SESSION['error']).'</div>');
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo('<div class="alert alert-success">'.htmlentities($_SESSION['success']).'</div>');
        unset($_SESSION['success']);
    }
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Your Storage Requests</h5>
                    <?php if (!$hasActive): ?>
                        <a href="add.php" class="btn btn-success btn-sm">+ New Request</a>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Items</th>
                                    <th>Dates</th>
                                    <th>Budget</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $pdo->prepare('SELECT * FROM requests WHERE user_id = :uid ORDER BY created_at DESC');
                                $stmt->execute(array(':uid' => $_SESSION['user_id']));
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $statusBadge = ($row['status'] == 'active') ? 'bg-success' : 'bg-secondary';
                                    echo "<tr>";
                                    echo "<td>" . htmlentities($row['luggage_amount']) . " bags</td>";
                                    echo "<td>" . $row['drop_date'] . " to " . $row['leave_date'] . "</td>";
                                    echo "<td>$" . $row['max_price'] . "</td>";
                                    echo "<td>" . htmlentities($row['location_name']) . "</td>";
                                    echo "<td><span class='badge $statusBadge'>" . ucfirst($row['status']) . "</span></td>";
                                    echo "<td><a href='delete.php?request_id=" . $row['request_id'] . "' class='btn btn-outline-danger btn-sm'>Update</a></td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
