<?php

session_start();
if(!isset($_SESSION['logged']) or !isset($_SESSION["user"]) or !isset($_SESSION['match']))
{
      header('Location:http://j-football.cba.pl/index.php');
      exit;     
}
else
{
    $user = $_SESSION["user"];
    $match = $_SESSION['match'];
}


if(isset($_POST['home'])) $what['home'] = $_POST['home'];
else $what['home'] = 0;

if(isset($_POST['draw'])) $what['draw'] = $_POST['draw'];
else $what['draw'] = 0;
    
if(isset($_POST['away'])) $what['away'] = $_POST['away'];
else $what['away'] = 0;

$i = 0;
if($what['home'] != 0) $i++;
if($what['draw'] != 0) $i++;
if($what['away'] != 0) $i++;

if($i > 1)
{
    $_SESSION['betError'] = 'Możesz dokonać zakładu tylko na 1 opcję!';
    header('Location:matches.php');
    exit; 
}
elseif($i == 0)
{
    $_SESSION['betError'] = 'Podaj kwote zakładu!';
    header('Location:matches.php');
    exit;   
}
if($what['home'] != 0) $choice = $what['home'];
elseif($what['draw'] != 0) $choice = $what['draw'];
elseif($what['away'] != 0) $choice = $what['away'];

if(!ctype_digit($choice))
{
    $_SESSION['betError'] = 'Podaj liczbę!';
    header('Location:matches.php');
    exit; 
}

try
{
    require_once('dbLogin.php');
    $connection = new mysqli($host, $dbUser, $dbPassword, $dbName);
    if($connection -> connect_errno != 0) throw new Exception($connection->error);
    $resultUser = $connection -> query("Select * from USERS WHERE login='$user'");
    if(!$resultUser) throw new Exception($connection->error);
    $tabUser = $resultUser -> fetch_all(MYSQLI_ASSOC);
    if(empty($tabUser))
    {
        $_SESSION['betError'] = 'Nie znaleziono użytkownika!';
        header('Location:matches.php');
        exit;   
    }
    $cashOwn = $tabUser[0]['money'];
    if($choice > $cashOwn)
    {
        $_SESSION['betError'] = 'Nie masz tyle pieniędzy!';
        header('Location:matches.php');
        exit; 
    } 
    $resultMatch = $connection -> query("Select * from MATCHES WHERE match_id='$match'");
    if(!$resultMatch) throw new Exception($connection->error);
    $tabMatch = $resultMatch -> fetch_all(MYSQLI_ASSOC);
    if(empty($tabMatch))
    {
        $_SESSION['betError'] = 'Nie znaleziono meczu!';
        header('Location:matches.php');
        exit;   
    }
    date_default_timezone_set('Europe/Warsaw');
    $dateNow = date("Y-m-d H:i:s");
    if($dateNow >= $tabMatch[0]['start'])
    {
        $_SESSION['betError'] = 'Nie możesz obstawiać rozpoczego meczu!';
        header('Location:matches.php');
        exit;
    }
    if($what['home'] != 0) $win = $choice * $tabMatch[0]['homeWin'];
    elseif($what['draw'] != 0) $win = $choice * $tabMatch[0]['draw'];
    elseif($what['away'] != 0) $win = $choice * $tabMatch[0]['awayWin'];
    
    $checkBet = $connection -> query("SELECT * FROM BOOK WHERE login='$user' AND match_id='$match'");
    if(!$checkBet) throw new Exception($connection->error);
    if($checkBet -> num_rows > 0)
    {
        $_SESSION['betError'] = 'Obstawiłeś już ten mecz!';
        header('Location:matches.php');
        exit;
    }
    if($what['home'] != 0) $whichBet = 'homeWin';
    elseif($what['draw'] != 0) $whichBet = 'draw';
    elseif($what['away'] != 0) $whichBet = 'awayWin';
    
    $resultInsert = $connection -> query("INSERT INTO BOOK (match_id,login,bet,toWin,$whichBet) VALUES('$match','$user','$choice','$win',1)");
    if(!$resultInsert) throw new Exception($connection->error);
    
    $newCash = $cashOwn - $choice;
    $resultInsertUser = $connection -> query("UPDATE USERS SET money='$newCash' WHERE login='$user'");
    $connection -> close();
}
catch(Exception $e)
{
        echo "Błąd numer:".$e;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>J-Football</title>
    <link rel="stylesheet" type="text/css" href="bet.css"/>
</head>

<body>
  <div class="intro">
  <div class="inside">
      <h1>Obstawiłeś mecz, powodzenia!</h1>
   <br/>
   <br/>
    <a class="btn" href="matches.php">Obstaw inny mecz</a>
  </div>
</div>
</body>
</html>
