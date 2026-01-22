<?php
  session_start();
  require_once "pdo.php";

  if ($_SERVER['REQUEST_METHOD']=='POST') {
    // incomplete
    if (!strlen($_POST['email']) || !strlen($_POST['password']) || empty($_POST['choice'])) {
      $_SESSION['error'] = 'All fields are required';
      header('Location: index.php');
      return;
    }

    // wrong password or email
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :em AND `password` = :pwd AND user_type=:type');
    $stmt->execute(array(
      ':em' => $_POST['email'],
      ':pwd'=> $_POST['password'],
      ':type'=>$_POST['choice']
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
      $_SESSION['error'] = 'Invalid username or password';
      header('Location: index.php');
      return;
    }
    
    // successfully logged in
    $_SESSION['user_id'] = $row['user_id'];
    if (($_POST['choice'])=='student') {
      header('Location: student.php');
    } else {
      header('Location: host.php');
    }
    return;
  }
?>



<html>
<head><title> Welcome to Airsng </title></head>
<body>
  <?php
    if (isset($_SESSION['error'])) {
      echo ('<p style="color:red">'. $_SESSION['error'].'</p>');
      unset($_SESSION['error']);
    }
    else if (isset($_SESSION['success'])) {
      echo ('<p style="color:green">'. $_SESSION['success'].'</p>');
      unset($_SESSION['success']);
    }
  ?>
  <h1>Hello! Welcome to Airsng, </h1>
  <p>whether as a student looking for a luggage storage space,
  or a nearby resident wanting to generate some revenue.</p>

  <?php
    if (!isset($_SESSION['user_id'])) {
      echo('Now, to get started, please indicate whether you are joing us as a student or host:
        <form method="post">
          <label><input type="radio" name="choice" value="student"> Student</label>
          <label><input type="radio" name="choice" value="host"> Host</label></br>
          <h2>Sign in to your account</h2>
          Email:<input type="email" name="email"/></br>
          Password: <input type="password" name="password"></br>
          <input type="submit" value="Log in" name="login"></br>
          <a href="signup.php"> Don\'t have an account yet? </a>
        </form>');
    } else {
      echo('<a href="logout.php">Log out</a>');
    }
  
