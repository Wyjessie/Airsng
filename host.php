<?php
session_start();
require_once "pdo.php";

$stmt = $pdo->prepare('SELECT * FROM offerings WHERE user_id = :uid AND status = :st');
$stmt->execute(array(
    ':uid'=>$_SESSION['user_id'],
    ':st' =>'active'
));
$row = $stmt->fetch();

if ($row == false)
    echo('You have no active offerings yet, 
        <a href="offer.php"> go ahead and make one!</a></p>');
else {
    echo('<table border="1">
                <caption>Here is a list of all your past offerings. Note that you can have only up to one active offering.<br></caption>
                <th> From </th>
                <th> To </th>
                <th> Max Pieces </th>
                <th> Max Size </th>
                <th> Charges </th>
                <th> Location </th>
                <th> Service </th>
                <th> Status </th>
                <th> Created at </th></tr>');

    $stmt = $pdo->prepare('SELECT * FROM offerings WHERE user_id = :uid');
    $stmt->execute([':uid'=>$_SESSION['user_id']]);
    $rows = $stmt->fetchall(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $af = htmlentities($row['available_from']);
        $at = htmlentities($row['available_to']);
        $mn = htmlentities($row['max_num']);
        $ms = htmlentities($row['max_size']);
        $cg = htmlentities($row['charges']);
        $lo = htmlentities($row['location']);
        $sv = htmlentities($row['services']);
        $st = htmlentities($row['status']);
        $ca = htmlentities($row['created_at']);
        echo('
                <td>'.$af.'</td>
                <td>'.$at.'</td>
                <td>'.$mn.'</td>
                <td>'.$ms.'</td>
                <td>'.$cg.'</td>
                <td>'.$lo.'</td>
                <td>'.$sv.'</td>
                <td>'.$st.'</td>
                <td>'.$ca.'</td>
                <td><a href="delete_h.php?offer_id='.$row['offer_id'].'">Update</a></tr>');        
    }
}
?>

<html>
    <head><title>Host's page</title></head>
    <body> <a href="logout.php"> Logout</a>
        
        