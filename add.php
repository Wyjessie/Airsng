<?php
session_start();
require_once "pdo.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['amount']) || empty($_POST['choice']) || empty($_POST['price']) || 
        empty($_POST['drop']) || empty($_POST['leave'])) {
        $_SESSION['error'] = 'All fields are required';
        header('Location: add.php');
        return;
    }
    if (!is_numeric($_POST['amount']) || !is_numeric($_POST['price'])) {
        $_SESSION['error'] = 'Amount and price must be numeric';
        header('Location: add.php');
        return;
    }

    $areas = isset($_POST['areas']) && is_array($_POST['areas']) ? implode(',', $_POST['areas']) : '';

    $stmt = $pdo->prepare('INSERT INTO requests (user_id, luggage_amount, total_size, drop_date, leave_date, max_price, acceptable_areas, status) 
                           VALUES (:uid, :amount, :size, :drop, :leave, :price, :areas, "active")');
    $stmt->execute([
        ':uid' => $_SESSION['user_id'],
        ':amount' => $_POST['amount'],
        ':size' => $_POST['choice'],
        ':drop' => $_POST['drop'],
        ':leave' => $_POST['leave'],
        ':price' => $_POST['price'],
        ':areas' => $areas
    ]);
    
    $_SESSION['success'] = 'Request added!';
    header('Location: student.php');
    return;
}
?>


<html>
    <?php
    if (isset($_SESSION['error'])){
        echo('<p style="color:red">'.$_SESSION['error'].'</p>');
        unset($_SESSION['error']);
    }
    ?>
    <head><title> Add a new request </title></head>
    <body>
        <h1> Add a new request</h1>
        <form method="post">
            How many pieces of luggages do you have? <input type="text" name="amount"></p>
            
            What is the average size of them?
            <label><input type="radio" name="choice" value="large"> 28-inch or bigger</label>
            <label><input type="radio" name="choice" value="medium"> 20-inch to 28-inch</label>
            <label><input type="radio" name="choice" value="small"> 20-inch or smaller</label></p>
            
            Your intended date of storage: From <input type="date" name="drop"> TO <input type="date" name="leave"></p>
            The maximum price you accept (/day in SGD) <input type="text" name="price"></p>
            
            Choose areas:<br>
            <input type="checkbox" name="areas[]" value="NTU"> NTU<br>
            <input type="checkbox" name="areas[]" value="Boonlay"> Boonlay<br>
            <input type="checkbox" name="areas[]" value="Jurong Point"> Jurong Point<br>
            <input type="checkbox" name="areas[]" value="Jurong West"> Jurong West<br>
            <input type="checkbox" name="areas[]" value="Clementi"> Clementi<br>

            
            <input type="submit" value="Add"></p>
            <a href="student.php"> Cancel
        </form>