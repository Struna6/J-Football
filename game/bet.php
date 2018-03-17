<?php
    session_start();
if(!isset($_GET['match']) or !isset($_SESSION['logged']) or !isset($_SESSION["user"]))
{
      header('Location: http://j-football.cba.pl/index.php');
      exit;     
}
else
{
    $choice = $_GET['match'];
    $_SESSION['match'] = $choice;
    $user = $_SESSION["user"];
}
    
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8"/>
    <title>J-Football</title>
    <link rel="stylesheet" type="text/css" href="bet.css"/>
</head>

<body>
<div class="intro">
    <div class="inside">
<?php
       try
        {
            require_once('dbLogin.php');
            $connection = new mysqli($host, $dbUser, $dbPassword, $dbName);
            if($connection -> connect_errno != 0) throw new Exception($connection->error);
            $result = $connection -> query("Select * from MATCHES WHERE match_id='$choice'");
            if(!$result) throw new Exception($connection->error);
            $tab = $result -> fetch_all(MYSQLI_ASSOC);
            date_default_timezone_set('Europe/Warsaw');
            $dateNow = date("Y-m-d H:i:s");
            if($dateNow >= $tab[0]['start'])
            {
                $_SESSION['betError'] = 'Nie możesz obstawiać rozpoczętego meczu!';
                header('Location: matches.php');
                exit;
            }
           if($tab[0]['finished'] == 1)
            {
                $_SESSION['betError'] = 'Nie możesz obstawiać zakończonego meczu!';
                header('Location: matches.php');
                exit;
            }
            if(empty($tab))
            {
                $_SESSION['betError'] = 'Nie znaleziono meczu!';
                header('Location: matches.php');
                exit;
            }
            echo '<table>';
            echo '<tr><th>Liga</th><th>Kolejka</th><th>Gospodarze</th><th>Goście</th><th>W</th><th>W</th><th>1</th><th>X</th><th>2</th><th>Czas</th></tr>';
            foreach($tab as $row)
            {
                echo '<tr><td>'.$row['league'].'</td><td>'.$row['week'].'</td><td>'.$row['home'].'</td><td>'.$row['away'].'</td><td>'.$row['homeScore'].'</td><td>'.$row['awayScore'].'</td><td>'.$row['homeWin'].'</td><td>'.$row['draw'].'</td><td>'.$row['awayWin'].'</td><td>'.$row['start'].'</td>';
            }
            echo '</table>';
            $connection -> close();
        }
        catch(Exception $e)
        {
                echo "Błąd numer:".$e;
        }
?>
<?php
       try
        {
            require_once('dbLogin.php');
            $connectionU = new mysqli($host, $dbUser, $dbPassword, $dbName);
            if($connectionU -> connect_errno != 0) throw new Exception($connectionU->error);
            $resultU = $connectionU -> query("Select * from USERS WHERE login='$user'");
            if(!$resultU) throw new Exception($connectionU->error);
            $tabU = $resultU -> fetch_all(MYSQLI_ASSOC);
            echo '<br/>';
            echo '<span style="color:red"><h1>Twój bilans konta: </h1><b><h2>'.$tabU[0]['money'].'</h2></b></span>';
        }
        catch(Exception $e)
        {
                echo "Błąd numer:".$e;
        }
        $_SESSION['match'] = $choice;
?>

<form method="post" action="betDone.php">

   <h2>Dokonaj zakładu:</h2>
   <h5>(Wpisz jaką kwotę obstawiasz w danej opcji, wygranej, przegranej lub remisie swojej ulubionej drużyny)</h5>
    Home  <input type="text" name="home"/></br></br>
    Draw  <input type="text" name="draw"/></br></br>
    Away  <input type="text" name="away"/>
   <br/></br>
   <input type="submit" value="Zaakceptuj"/>
</form>
<br/>
<a class="btn" href="http://j-football.cba.pl/index.php">Strona główna</a>
<br/><br/>
<a class="btn" href="matches.php">Obstaw inny mecz</a>
</div>
</div>
</body>
</html>


