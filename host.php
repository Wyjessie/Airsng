<?php
session_start();
require_once "pdo.php";

if (!isset($_SESSION['user_id'])) { die("Access Denied"); }

// Check if they already have an active offering
$stmt = $pdo->prepare('SELECT * FROM offerings WHERE user_id = :uid AND status = "active"');
$stmt->execute([':uid' => $_SESSION['user_id']]);
$hasActive = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Host Dashboard - AirSnG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1">AirSnG Host Portal</span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Your Storage Offerings</h5>
            <?php if (!$hasActive): ?>
                <a href="offer.php" class="btn btn-primary btn-sm">+ Create Offering</a>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Dates</th>
                        <th>Capacity</th>
                        <th>Price/Day</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->prepare('SELECT * FROM offerings WHERE user_id = :uid ORDER BY created_at DESC');
                    $stmt->execute([':uid' => $_SESSION['user_id']]);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $statusClass = ($row['status'] == 'active') ? 'bg-success' : 'bg-secondary';
                        echo "<tr>
                            <td>{$row['available_from']} to {$row['available_to']}</td>
                            <td>{$row['max_num']} pieces</td>
                            <td>{$row['charges']}</td>
                            <td>{$row['location_name']}</td>
                            <td><span class='badge $statusClass'>" . ucfirst($row['status']) . "</span></td>
                            <td><a href='delete_h.php?offer_id={$row['offer_id']}' class='btn btn-sm btn-outline-secondary'>Edit</a></td>
                        </tr>";
                    }?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
