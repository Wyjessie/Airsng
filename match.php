<?php
require 'pdo.php';
session_start();

$student_id = $_SESSION['user_id'] ?? null;
if (!$student_id) {
    header('Location: index.php');
    exit;
}

// 1. Get student's active request
$stmt = $pdo->prepare("SELECT * FROM requests WHERE user_id = ? AND status = 'active' ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$student_id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    $_SESSION['error'] = "Please create an active storage request first.";
    header('Location: student.php');
    exit;
}

// 2. Assign variables
$drop_date      = $request['drop_date'];
$leave_date     = $request['leave_date'];
$max_price      = (float)$request['max_price'];
$req_amount     = (int)$request['luggage_amount'];
$studentLat     = $request['lat'];
$studentLng     = $request['lng'];
$location_name  = $request['location_name'];


// Helper to convert size to numeric for comparison
function sizeToNum($s) {
    $map = ['small' => 1, 'medium' => 2, 'large' => 3];
    return $map[strtolower($s)] ?? 1;
}

// 3. Fetch offerings
$stmt = $pdo->prepare("SELECT o.*, u.email FROM offerings o JOIN users u ON o.user_id = u.user_id WHERE o.status = 'active'");
$stmt->execute();
$offerings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$results = [];
foreach ($offerings as $o) {
    $score = 0;

    // Priority 1: Time (50 pts)
    $student_start = strtotime($drop_date);
    $student_end = strtotime($leave_date);
    $host_start = strtotime($o['available_from']);
    $host_end = strtotime($o['available_to']);

    $overlap_start = max($student_start, $host_start);
    $overlap_end = min($student_end, $host_end);

    $total_needed_days = $student_end - $student_start;
    $overlap_days = max(0, ($overlap_end - $overlap_start));
    $o['ratio'] = $overlap_days / $total_needed_days;

    if ($o['ratio'] >= 1) {
        $score += 50;
    } else if ($overlap_days > 0) {
        // Partial fit: deduct 20 marks and then weight
        $score +=  $o['ratio'] * 30;
    }

    // Priority 2: Amount compatibility (25 pts)
    if ($o['max_num'] >= $req_amount) $score += 25;

    // Priority 3: Price (15 pts)
    if ($o['charges'] <= $max_price) $score += 15;

    
    // Priority 4: Distance (10 pts)
    $o['distance'] = calculateDistance($studentLat, $studentLng, $o['lat'], $o['lng']);

    if ($o['distance'] < 1.0) {
        $score += 10;
    } else if ($o['distance'] < 3.0) {
        $score += 7;
    } else if ($o['distance'] < 7.0) {
        $score += 3;
    }

    // Bonus: Service (5 pts)
    $score += ($o['services'] * 1.5);

    $o['match_score'] = $score;
    $results[] = $o;
}

// Implementing the Haversine Formula
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earth_radius * $c;
}



usort($results, function($a, $b) { return $b['match_score'] <=> $a['match_score']; });
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Top Matches - AirSnG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Top Recommended Hosts</h2>
        <a href="student.php" class="btn btn-outline-primary">Back to Dashboard</a>
    </div>

<div class="alert alert-info shadow-sm">
    <strong>Your Preferences:</strong> <br>
    <?= htmlentities($drop_date) ?> to <?= htmlentities($leave_date) ?><br>
    Budget: $<?= htmlentities($max_price) ?>/day<br>
    Location: <?= htmlentities($location_name) ?>
</div>

    <div class="row">
        <?php if (empty($results)): ?>
            <div class="col-12 text-center mt-5"><h5>No hosts match your criteria yet.</h5></div>
        <?php else: ?>
            <?php foreach ($results as $o): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title text-primary"><?= htmlspecialchars($o['location_name']) ?></h5>
                                <span class="badge bg-warning text-dark">Match Score: <?= round($o['match_score']) ?></span>
                            </div>
                            <hr>
                            <p class="card-text small">
                                <strong>📅</strong> <?= $o['available_from'] ?> to <?= $o['available_to'] ?>, Overlap=<?=number_format(100 * $o['ratio'],0)?>%<br>
                                <strong>🎒</strong> Up to <?= $o['max_num'] ?><br>
                                <strong>💰</strong> $<?= $o['charges'] ?> / bag / day<br>
                                <strong>🚇</strong> <?=number_format($o['distance'], 2) ?>km

                            </p>
                            <div class="bg-light p-2 rounded mb-3 small">
                                <strong>🛠 Services:</strong> <?= htmlspecialchars($o['services']) ?>
                            </div>
                            <a href="mailto:<?= $o['email'] ?>" class="btn btn-success w-100">Contact Host</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
