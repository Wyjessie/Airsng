<?php
session_start();
require_once "pdo.php";

if ($_SERVER['REQUEST_METHOD']=='POST') {
    if ( empty($_POST['af']) || empty($_POST['at']) || empty($_POST['size']) ||
        empty($_POST['charges']) || empty($_POST['location']) || empty($_POST['service'])) {
            $_SESSION['error'] = 'All fields are required';
        } else if (!is_numeric($_POST['num']) || !is_numeric($_POST['charges'])) {
            $_SESSION['error'] = 'Price and Amount must be numbers';
        } else {
            $stmt = $pdo->prepare('INSERT INTO offerings(user_id, available_from, available_to, 
                max_num, max_size, charges, location, services, status)
                VALUES (:uid, :af, :at, :mn, :ms, :ch, :lc, :sv, "active")');
            
            $stmt->execute(array(
                ':uid'=> $_SESSION['user_id'],
                ':af' => $_POST['af'],
                ':at' => $_POST['at'],
                ':mn' => $_POST['num'],
                ':ms' => $_POST['size'],
                ':ch' => $_POST['charges'],
                ':lc' => $_POST['location'],
                ':sv' => $_POST['service']
            ));

            $_SESSION['success'] = 'Offer created';
            header('Location: host.php');
            exit;
        }
        header('Location: offer.php');
        exit;
}


?>

<html>
    <head><title> Add a new offer</title></head>
    <body>
        <?php
        if (isset($_SESSION['error'])) {
            echo('<p style="color:red">'.$_SESSION['error'].'</p>');
            unset($_SESSION['error']);
        }else if (isset($_SESSION['success'])) {
            echo('<p style="color:green">'.$_SESSION['success'].'</p>');
            unset($_SESSION['success']);
        }?>
        <h1> My Offer </h1>
        <form method="post">
            Available from <input type="date" name="af"> to <input type="date" name="at"></p>
            Maximum pieces of luggage I can take: <input type="text" name="num"></p>
            Maximum size I can take:<br>
            <label><input type="radio" name="size" value="large"> Large: Over 28 inch </label></br>
            <label><input type="radio" name="size" value="medium"> Medium: 24~28 inch </label></br>
            <label><input type="radio" name="size" value="small"> Small: Below 24 inch </label></p>

            I charge <input type="text" name="charges"> SGD per day per item</p>
            
            Which of the following places best describes my location:<br>
            <label><input type="radio" name="location" value="NTU"> NTU </label><br>
            <label><input type="radio" name="location" value="Boonlay"> Boonlay </label><br>
            <label><input type="radio" name="location" value="Jurong Point"> Jurong Point </label><br>
            <label><input type="radio" name="location" value="Jurong West"> Jurong West </label><br>
            <label><input type="radio" name="location" value="Clementi"> Clementi </label><p>

            I will provide assistance to which level:<br>
            <label><input type="radio" name="service" value="1"> 1 - Just a storage space </label><br>
            <label><input type="radio" name="service" value="2"> 2 - I will assist you move your stuff </label><br>
            <label><input type="radio" name="service" value="3"> 3 - I will be in pick up and deliver your stuff </label></p>

            <input type="submit" value="Create">
</form>

