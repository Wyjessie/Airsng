<?php
session_start();
require_once "pdo.php";

// After confirmation, finally deleting
if ($_SERVER['REQUEST_METHOD']=='POST') {
    if ($_POST['action']=='delete') {
        $stmt = $pdo->prepare('DELETE FROM offerings WHERE offer_id=:oid');
        $stmt->execute([':oid'=>$_POST['offer_id']]);
        $_SESSION['success'] = 'Offer deleted';
    } 
    else if ($_POST['action']=='match') {
        $stmt = $pdo->prepare('UPDATE offerings SET status=:st WHERE offer_id=:oid');
        $stmt->execute(array(
            ':st'=>'booked',
            ':oid'=>$_POST['offer_id']
        ));
        $_SESSION['success'] = 'Offer marked as booked';
    }
    header('Location: host.php');
    return;
}

// Grabbing the corresponding data
$stmt = $pdo->prepare('SELECT * FROM offerings WHERE offer_id=:oid');
$stmt->execute([':oid'=>$_GET['offer_id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$af = htmlentities($row['available_from']);
$at = htmlentities($row['available_to']);
$mn = htmlentities($row['max_num']);
$ms = htmlentities($row['max_size']);
$ch = htmlentities($row['charges']);
$lc = htmlentities($row['location']);
$sv = htmlentities($row['services']);
$st = htmlentities($row['status']);
$ca = htmlentities($row['created_at']);

?>
<html>
    <head><title>Delete a Offer</title></head>
    <body>
        <table border="1">
            <th>Availability</th>
            <th>Max Amount</th>
            <th>Max Size</th>
            <th>Charges</th>
            <th>Services</th>
            <th>Location</th>
            <th>Status</th>
            <th>Created at</th></tr>
            <td><?=$af, $at?></td>
            <td><?=$mn?></td>
            <td><?=$ms?></td>
            <td><?=$ch?></td>
            <td><?=$sv?></td>
            <td><?=$lc?></td>
            <td><?=$st?></td>
            <td><?=$ca?></td>
</table></p>
<form method="post">
    <input type="hidden" value=<?=$_GET['offer_id']?> name="offer_id">

    Please indicate you desired behavior toward this offer:<br>
    <label><input type="radio" name="action" value="delete">Delete</label><br>
    <label><input type="radio" name="action" value="match"> Mark as Booked</label><br></p>

    <input type="submit" value="Confirm">
</form>