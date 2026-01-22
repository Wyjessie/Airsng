<?php
session_start();
require_once "pdo.php";

// After confirmation, finally deleting
if ($_SERVER['REQUEST_METHOD']=='POST') {
    if ($_POST['action']==="delete") {
        $stmt = $pdo->prepare('DELETE FROM requests WHERE request_id=:rid');
        $stmt->execute([':rid'=>$_POST['request_id']]);
        $_SESSION['success'] = 'Request deleted';
    } else if ($_POST['action']==='match') {
        $stmt = $pdo->prepare('UPDATE requests SET status = :status WHERE request_id=:rid');
        $stmt->execute(array(
            ':rid'=>$_POST['request_id'],
            ':status'=>'matched'));
        $_SESSION['success'] = 'Request marked as matched';
    }
    header('Location: student.php');
    return; 
}

// Grabbing the corresponding data
$stmt = $pdo->prepare('SELECT * FROM requests WHERE request_id=:rid');
$stmt->execute([':rid'=>$_GET['request_id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$la = htmlentities($row['luggage_amount']);
$s  = htmlentities($row['total_size']);
$dd = htmlentities($row['drop_date']);
$ld = htmlentities($row['leave_date']);
$mp = htmlentities($row['max_price']);
$aa = htmlentities($row['acceptable_areas']);
$st = htmlentities($row['status']);
$ca = htmlentities($row['created_at']);

?>
<html>
    <head><title>Delete a Request</title></head>
    <body>
        <p>Indicate your desired action to this request:</p>
        <p><table border="1">
            <th>Luggage amount</th>
            <th>Size</th>
            <th>Drop date</th>
            <th>Leave date</th>
            <th>Max price</th>
            <th> Acceptable areas</th>
            <th>Status</th>
            <th>Created at</th></tr>
            <td><?=$la?></td>
            <td><?=$s ?></td>
            <td><?=$dd?></td>
            <td><?=$ld?></td>
            <td><?=$mp?></td>
            <td><?=$aa?></td>
            <td><?=$st?></td>
            <td><?=$ca?></td>
</table></p>
<form method="post">
    <input type="hidden" value=<?=$_GET['request_id']?> name="request_id">

    <label><input type="radio" name="action" value="delete"> Delete </label><br>
    <label><input type="radio" name="action" value="match"> Mark as Matched </label><br>
    
    <input type="submit" value="Confirm">
</form>

