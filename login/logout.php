<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <title>Loggout</title>
    <link rel="stylesheet" type="text/css" href="logout.css"/>
</head>

<body>
  <div class="intro">
  <div class="inside">
   <?php
    session_start();
    if(!isset($_SESSION['logged'])) header('Location: http://j-football.cba.pl/index.php');
    unset($_SESSION['logged']);
    unset($_SESSION['user']);
    echo 'Pomyślnie się wylogowałeś!';
    ?>
    <br/><br/>
    <a class="btn" href="http://j-football.cba.pl/index.php">Strona główna</a>
    </div>
    </div>
</body>
</html>
