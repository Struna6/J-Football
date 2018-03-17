<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Summary</title>
    <link rel="stylesheet" type="text/css" href="summary.css"/>
</head>
<body>
<div class="intro">
<div class="inside">
<?php
session_start();
if(!isset($_SESSION['logged']) or !isset($_SESSION["user"]))
{
    header('Location: http://j-football.cba.pl/index.php');
    exit;
}
$user = $_SESSION["user"];
try
{
    require_once('dbLogin.php');
    $connection = new mysqli($host, $dbUser, $dbPassword, $dbName);
    if($connection -> connect_errno != 0) throw new Exception($connection->error);

    $resultBet = $connection -> query("SELECT * FROM BOOK WHERE login='$user' ORDER BY match_id ASC");
    if(!$resultBet) throw new Exception($connection->error);
    $tabBet = $resultBet -> fetch_all(MYSQLI_ASSOC);
    $i=0;
    foreach($tabBet as $key)
    {
        if($i==0) $bets = 'match_id='.$key['match_id'];
        $bets .= ' OR match_id='.$key['match_id'];
        $i++;
    }
    $resultMatch = $connection -> query("SELECT * FROM MATCHES WHERE $bets ORDER BY match_id ASC");
    if(!$resultMatch) throw new Exception($connection->error);
    $tabMatch = $resultMatch -> fetch_all(MYSQLI_ASSOC);

    if(empty($tabMatch))
    {
        header('Location: http://j-football.cba.pl/index.php');
        exit;
    }
    $howMuchBets = count($tabBet, COUNT_RECURSIVE) - count($tabBet);
    echo '<table>';
    echo '<h1>Twoje zakłady:</h1>';
    echo '<tr><th>Liga</th><th>Home</th><th>Away</th><th>Start</th><th>Obstawiłeś wygraną</th><th>Kwota</th><th>Możesz wygrać</th><th>Rozliczone</th><th>Wygrałeś</th><th>Balans</th></tr>';
    for($i=0;$i<$howMuchBets/10;$i++)
    {
        if($tabBet[$i]['match_id'] != $tabMatch[$i]['match_id'])
        {
            header('Location: http://j-football.cba.pl/index.php');
            exit;
        }
        $betNum = $tabBet[$i]['bet_id'];
        $acMoney = $connection -> query("SELECT money from USERS where login='$user'");
        if(!$acMoney) throw new Exception($connection->error);
        $tabMoney = $acMoney -> fetch_all(MYSQLI_ASSOC);
        $actualMoney = $tabMoney[0]['money'];
        if($acMoney->num_rows < 1)
        {
            header('Location: http://j-football.cba.pl/index.php');
            exit;
        }
        if($tabBet[$i]['calculated'] == 0 and $tabMatch[$i]['finished'] == 1)
        {
            if($tabMatch[$i]['homeScore'] == $tabMatch[$i]['awayScore'])
                if($tabBet[$i]['draw'] == 1)
                {
                    $balance = $tabBet[$i]['toWin'];
                    $newMoney = $actualMoney+$balance;
                    $editBet = $connection -> query("UPDATE BOOK SET balance = $balance, calculated=1 WHERE bet_id=$betNum");
                    if(!$editBet) throw new Exception($connection->error);
                    $editUser = $connection -> query("UPDATE USERS SET money=$newMoney WHERE login='$user'");
                    if(!$editUser) throw new Exception($connection->error);
                }
                else
                {
                    $balance = -($tabBet[$i]['bet']);
                    $editBet = $connection -> query("UPDATE BOOK SET balance = $balance, calculated=1 WHERE bet_id=$betNum");
                    if(!$editBet) throw new Exception($connection->error);
                }
            if($tabMatch[$i]['homeScore'] > $tabMatch[$i]['awayScore'])
                if($tabBet[$i]['homeWin'] == 1)
                {
                    $balance = $tabBet[$i]['toWin'];
                    $newMoney = $actualMoney+$balance;
                    $editBet = $connection -> query("UPDATE BOOK SET balance = $balance, calculated=1 WHERE bet_id=$betNum");
                    if(!$editBet) throw new Exception($connection->error);
                    $editUser = $connection -> query("UPDATE USERS SET money=$newMoney WHERE login='$user'");
                    if(!$editUser) throw new Exception($connection->error);
                }
                else
                {
                    $balance = -($tabBet[$i]['bet']);
                    $editBet = $connection -> query("UPDATE BOOK SET balance = $balance, calculated=1 WHERE bet_id=$betNum");
                    if(!$editBet) throw new Exception($connection->error);
                }

            if($tabMatch[$i]['homeScore'] < $tabMatch[$i]['awayScore'])
                if($tabBet[$i]['awayWin'] == 1)
                {
                    $balance = $tabBet[$i]['toWin'];
                    $newMoney = $actualMoney+$balance;
                    $editBet = $connection -> query("UPDATE BOOK SET balance = $balance, calculated=1 WHERE bet_id=$betNum");
                    if(!$editBet) throw new Exception($connection->error);
                    $editUser = $connection -> query("UPDATE USERS SET money=$newMoney WHERE login='$user'");
                    if(!$editUser) throw new Exception($connection->error);
                }
                else
                {
                    $balance = -($tabBet[$i]['bet']);
                    $editBet = $connection -> query("UPDATE BOOK SET balance = $balance, calculated=1 WHERE bet_id=$betNum");
                    if(!$editBet) throw new Exception($connection->error);
                }    
        }
            echo '<tr><td>'.$tabMatch[$i]['league'].'</td><td>'.$tabMatch[$i]['home'].'</td><td>'.$tabMatch[$i]['away'].'</td><td>'.$tabMatch[$i]['start'].'</td>';
            if($tabBet[$i]['homeWin']==1) echo '<td>'.$tabMatch[$i]['home'].'</td>';
            elseif($tabBet[$i]['draw']==1) echo '<td>Remis</td>';
            elseif($tabBet[$i]['awayWin']==1) echo '<td>'.$tabMatch[$i]['away'].'</td>';
            echo '<td>'.$tabBet[$i]['bet'].'</td>';
            echo '<td>'.$tabBet[$i]['toWin'].'</td>';
            if($tabBet[$i]['calculated'] == 1) echo '<td>Tak</td>';
            else echo '<td>Nie</td>';  
            if($tabBet[$i]['balance'] > 0 ) echo '<td>Tak</td>';
            else echo '<td>Nie</td>';
            if(isset($tabBet[$i]['balance'])) echo '<td>'.$tabBet[$i]['balance'].'</td>';
            else echo '<td>'.-($tabBet[$i]['bet']).'</td>';
            echo '</tr>';
    }
    
    //DODAJ BALANS ZAKŁADU KOLUMNE!!!
    
    echo '</table>';
    $acMoney2 = $connection -> query("SELECT money from USERS where login='$user'");
    if(!$acMoney2) throw new Exception($connection->error);
    $tabMoney = $acMoney2 -> fetch_all(MYSQLI_ASSOC);
    $actualMoney2 = $tabMoney[0]['money'];
    echo '<br/><span style="color:red; font-size: 60"><b>Masz:'.$actualMoney2.' zł</b></span><br/><br/>';
    $connection -> close();
    
}
catch(Exception $e)
{
        echo "Błąd numer:".$e;
}


?>
<br/><a class="btn" href="http://j-football.cba.pl/game/matches.php">Postaw i wygraj!</a><br/>
<br/><a class="btn" href="http://j-football.cba.pl/index.php">Strona główna</a>
</div>
</div>
</body>
</html>
