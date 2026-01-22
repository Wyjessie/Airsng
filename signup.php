<?php
session_start();
require_once "pdo.php";

if ($_SERVER['REQUEST_METHOD']=='POST') {
    if ( !strlen($_POST['email']) || !strlen($_POST['password']) || !strlen($_POST['phone']) || !isset($_POST['choice']) ) {
        $_SESSION['error'] = 'All fields are required';
        header('Location: signup.php');
        return;
    }

    // storing into database
    $stmt = $pdo->prepare('INSERT INTO users (email, password, phone, user_type) VALUES (:em, :pwd, :phone, :t)');
    $stmt->execute(array(
        ':em' => $_POST['email'],
        ':pwd'=> $_POST['password'],
        ':phone'=>$_POST['phone'],
        ':t' =>$_POST['choice']
    ));
    $_SESSION['success'] = 'Account created';
    header('Location: index.php');
    return;
}
?>

<html>
<head><title>Create an account</title></head>

<body>
<?php
if (isset($_SESSION['error'])) {
    echo('<p style="color:red">'.$_SESSION['error'].'</p>');
    unset($_SESSION['error']);
}
?>
<h1> Create an account </h1>
<form method="post">
    Email: <input type="email" name="email"></br>
    Password: <input type="password" name="password"></br>
    Phone number: <input type="text" name="phone"></br>
    <label><input type="radio" name="choice" value="student"> student</label>
    <label><input type="radio" name="choice" value="host"> host</label></br>
    <input type="submit" value="Create"></br>
    <a href="index.php"> Cancel </a>
</form>
    