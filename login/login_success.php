<?php
session_start();
if(!isset($_SESSION['logged']) or !isset($_SESSION["user"]))
{
      header('Location: http://j-football.cba.pl/index.php');
      exit;     
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Logged</title>
    <link rel="stylesheet" type="text/css" href="login_success.css"/>
</head>

<body>
<div class="intro">
    <div class="inside">
        Zalogowałeś się! </br></br>
    <a class="btn" href="http://j-football.cba.pl/index.php">Strona główna</a>
    </div>
</div>
</body>
</html>
