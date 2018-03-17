<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Register success!</title>
    <link rel="stylesheet" type="text/css" href="register_success.css"/>
</head>

<body>
<div class="intro">
<div class="inside">
<?php
    session_start();
    if(!isset($_SESSION['registrationCompleted']))
    {
        header('Location:register.php');
    }
?>
    <b>Rejestracja udana!</b></br>
    Sprawdź swojego maila, a następnie przejdź   <a class="btn2" href="confirmation.php">Tutaj</a><br/><br/><br/>
    <a class="btn" href="http://j-football.cba.pl/index.php">Strona główna</a>
    </div>
    </div>
</body>
</html>
