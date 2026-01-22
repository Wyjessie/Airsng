<?php
require 'pdo.php';
session_start();

$student_id = $_SESSION['user_id'] ?? null;
if (!$student_id) {
    header('Location: index.php');
    exit;
}

// get student's latest active/pending request
$stmt = $pdo->prepare("
    SELECT *
    FROM requests
    WHERE user_id = ? AND status IN ('pending','active')
    ORDER BY created_at DESC
    LIMIT 1
");
$stmt->execute([$student_id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    echo "You have no active luggage request.";
    exit;
}

$drop_date       = $request['drop_date'];
$leave_date      = $request['leave_date'];
$max_price       = $request['max_price'];
$luggage_amount  = $request['luggage_amount'];
$total_size      = $request['total_size'];
$acceptable_areas = $request['acceptable_areas'];

// helper: size rank
function size_rank($size) {
    $rank = ['small' => 1, 'medium' => 2, 'large' => 3];
    $size = strtolower(trim($size));
    return $rank[$size] ?? 1;
}

// parse acceptable areas
$acceptedAreas = array_filter(array_map('trim', explode(',', (string)$acceptable_areas)));

// Get all active offerings (no hard filters besides status)
$sql = "
SELECT o.*, u.email, u.phone
FROM offerings o
JOIN users u ON o.user_id = u.user_id
WHERE o.status = 'active'
";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$offers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// precompute some request-based values
$req_start = new DateTime($drop_date);
$req_end   = new DateTime($leave_date);
$requested_days = max(1, $req_start->diff($req_end)->days);
$req_size_rank = size_rank($total_size);

$results = [];

foreach ($offers as $o) {
    $score = 0;

    // ---- 1. Size compatibility (most important) ----
    $offer_size_rank = size_rank($o['max_size']);

    if ($offer_size_rank < $req_size_rank) {
        // cannot really fit → big penalty
        $size_score = -100;
    } else {
        // can fit; bigger than needed gets extra
        $size_score = 50 + 20 * ($offer_size_rank - $req_size_rank);
    }
    $score += $size_score;

    // ---- 2. Time overlap degree (most important) ----
    $host_start = new DateTime($o['available_from']);
    $host_end   = new DateTime($o['available_to']);

    // overlap start/end
    $overlap_start = $req_start > $host_start ? $req_start : $host_start;
    $overlap_end   = $req_end < $host_end ? $req_end : $host_end;

    $overlap_days = 0;
    if ($overlap_end > $overlap_start) {
        $overlap_days = $overlap_start->diff($overlap_end)->days;
    }

    if ($overlap_days <= 0) {
        // no overlap → big penalty
        $time_score = -100;
    } else {
        $overlap_ratio = $overlap_days / $requested_days;
        // full overlap → +60, partial overlap less
        $time_score = 60 * $overlap_ratio;
    }
    $score += $time_score;

    // ---- 3. Price score (medium) ----
    $days = $requested_days;
    $total_cost = $o['charges'] * $days;

    // Cheaper is better; adjust 40 to your typical price range
    $price_score = max(0, 40 - $total_cost);
    $score += $price_score;

    // ---- 4. Location score (medium) ----
    $location_score = 0;
    if (!empty($acceptedAreas)) {
        foreach ($acceptedAreas as $area) {
            if (stripos($o['location'], $area) !== false) {
                $location_score = 30; // any match gives the bonus
                break;
            }
        }
    }
    $score += $location_score;

    // ---- 5. Service level bonus (small) ----
    $services = strtolower((string)$o['services']); // e.g. "help-moving,self-pickup"

    if (strpos($services, 'pickup') !== false || strpos($services, 'delivery') !== false) {
        $service_score = 10; // best service
    } elseif (strpos($services, 'help') !== false) {
        $service_score = 5;  // some help
    } else {
        $service_score = 0;  // basic
    }
    $score += $service_score;

    // collect with score
    $o['match_score'] = $score;
    $results[] = $o;
}

// sort by score desc
usort($results, function($a, $b) {
    return $b['match_score'] <=> $a['match_score'];
});

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recommended Hosts</title>
</head>
<body>
    <h2>My Preferences:</h2>
    <p>
        Drop-off: <?=htmlentities($drop_date)?><br>
        Pick-up: <?=htmlentities($leave_date)?><br>
        Max price/day: $<?=htmlentities($max_price)?><br>
        Areas: <?=htmlentities($acceptable_areas)?>
    </p>

    <h2>Recommended hosts for my request:</h2>

    <?php if (empty($results)): ?>
        <p>No hosts found yet.</p>
    <?php else: ?>
        <?php foreach ($results as $o): ?>
            <p>
                <strong>Location:</strong> <?=htmlspecialchars($o['location'])?><br>
                Score: <?=round($o['match_score'])?><br>
                Available: <?=htmlspecialchars($o['available_from'])?> to <?=htmlspecialchars($o['available_to'])?><br>
                Capacity: up to <?=htmlspecialchars($o['max_num'])?> bags<br>
                Size: <?=htmlspecialchars($o['max_size'])?><br>
                Charges: $<?=htmlspecialchars($o['charges'])?> per day per bag<br>
                Service-level: <?=htmlspecialchars($o['services'])?><br>
                Contact: <?=htmlspecialchars($o['email'])?>, <?=htmlspecialchars($o['phone'])?><br>
            </p>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>

    <p><a href="student.php">Back to My Page</a></p>
</body>
</html>