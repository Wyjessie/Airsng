<?php
session_start();
require_once "pdo.php";

if (isset($_POST['add'])) {
    header('Location: add.php?user_id='.$_SESSION['user_id']);
    return;
}

// Displays any previous requests
$stmt = $pdo->prepare('SELECT * FROM requests WHERE user_id=:uid');
$stmt->execute(array(':uid'=>$_SESSION['user_id']));
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($rows) {

    echo('<table border="1"><th>
            Luggage Amount</th>
            <th>Size</th>
            <th>Drop date</th>
            <th>Pick up date</th>
            <th>Max Price</th>
            <th>Status</th>
            <th>Acceptable areas</th>');
    foreach ($rows as $row) {
        $a = htmlentities($row['luggage_amount']);
        $s = htmlentities($row['total_size']);
        $p = htmlentities($row['acceptable_areas']);
        
        echo('<tr><td>'.$a.'</td><td>'
                .$s.'</td><td>'
                .$row['drop_date'].'</td><td>'
                .$row['leave_date'].'</td><td>'
                .$row['max_price'].'</td><td'
                .$row['acceptable_areas'].'</td><td>'
                .$row['status'].'</td><td>'
                .$p.'</td><td>'
                .$row['created_at'].'</td><td>
                <a href="delete.php?request_id='.$row['request_id'].'">Update</a></tr>');
    }
} 
$stmt = $pdo->prepare('SELECT * FROM requests WHERE user_id=:uid AND status=:st');
$stmt->execute(array(
    ':uid' => $_SESSION['user_id'],
    ':st'  => 'active'
));
$row = $stmt->fetch();
if ($row == false){
    echo('You have no active requests now. <h2>Click to add a new request:</h2> 
        <form method="post"><input type="submit" value="Add" name="add"></form></p>');
}

?>

<html>
    <head><title>Student's page</title></head>
    <body>
        <?php
        if (isset($_SESSION['error'])) {
            echo('<p style="color:red">'.$_SESSION['error'].'</p>');
            unset($_SESSION['error']);
        }else if (isset($_SESSION['success'])) {
            echo('<p style="color:green">'.$_SESSION['success'].'</p>');
            unset($_SESSION['success']);
        }?>
        <form method="post">
            <a href="index.php"> Back to Welcome Page</a></br>
            <a href="match.php"> See hosts that match my preferences</a>
        </form>
