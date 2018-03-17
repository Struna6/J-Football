<?php
session_start();
if(!isset($_SESSION['logged']))
{
        header('Location: http://j-football.cba.pl/index.php');
        exit;
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>J-Football</title>
    <link rel="stylesheet" type="text/css" href="matches.css"/>
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
                $result = $connection -> query("select * from MATCHES where START between DATE_SUB(current_date(), INTERVAL 7 DAY) and DATE_ADD(current_date(), INTERVAL 14 DAY) ORDER BY `league`,`start` ASC ");
                if(!$result) throw new Exception($connection->error);
                $tab = $result -> fetch_all(MYSQLI_ASSOC);
                date_default_timezone_set('Europe/Warsaw');
                $dateNow = date("Y-m-d H:i:s");
                if(empty($tab))
                {
                    echo '<br/><span style="color:red"><b>Aktualnie brak meczy do obstawienia!</b></span><br/>';
                    echo '<a class="btn" href="http://j-football.cba.pl/index.php">Strona główna</a>';
                    exit;
                }
                echo '<table>';
                echo '<tr><th>Liga</th><th>Kolejka</th><th>Gospodarze</th><th>Goście</th><th>W</th><th>W</th><th>1</th><th>X</th><th>2</th><th>Czas</th><th>Status</th></tr>';
                foreach($tab as $row)
                {
                    echo '<tr><td>'.$row['league'].'</td><td>'.$row['week'].'</td><td>'.$row['home'].'</td><td>'.$row['away'].'</td><td>'.$row['homeScore'].'</td><td>'.$row['awayScore'].'</td><td>'.$row['homeWin'].'</td><td>'.$row['draw'].'</td><td>'.$row['awayWin'].'</td><td>'.$row['start'].'</td>';
                    if($dateNow <= $row['start']) echo '<td><a class="btn"  href="bet.php?match='.$row['match_id'].'">Obstaw!</a></td>';
                    elseif($row['finished'] == 1) echo '<td><b>Zakończony!</b></td>';
                    else echo '<td></td>';
                    echo '</tr>';
                }
                echo '</table>';
                $connection -> close();
            }
            catch(Exception $e)
            {
                    echo "Błąd numer:".$e;
            }
    ?>
    <br/>
    <?php
                if(isset($_SESSION['betError'])) echo '<br/><span style="color:red"><b>'.$_SESSION['betError'].'</b></span><br/>';
                unset($_SESSION['betError']);
    ?>
    <br/>
    <a class="btn" href="http://j-football.cba.pl/index.php">Strona główna</a>
    </div>
</div>
</body>
</html>
