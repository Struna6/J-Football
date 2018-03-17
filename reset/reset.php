<?php
    session_start();
    
    if(isset($_SESSION['logged']))
    {
        header('Location: http://j-football.cba.pl/index.php');
        exit;
    }

    if(isset($_POST['mail']))
    {
        $mail = $_POST['mail'];
        try
        {
            require_once("dbLogin.php");
            $connection = new mysqli($host, $dbUser, $dbPassword, $dbName);
            if($connection->connect_errno != 0) throw new Exception($connection->connect_errno);
            $query = $connection->query("SELECT * FROM USERS WHERE email='$mail'");
            $queryNum = $query -> num_rows;
            if($queryNum > 0)
            {
                $randomNumber = uniqid();
                if(!$connection -> query("UPDATE USERS SET confirmation = '$randomNumber', active = 0 WHERE email='$mail'")) $_SESSION['blad'] = "Błąd połaczenia!";
                $mailContent = 'Twoje tymczasowe hasło: '.$randomNumber;
                if(!mail($mail, 'Reset hasła', $mailContent, 'From:admin@j-football.cba.pl')) $_SESSION['blad'] = "Błąd wysłania maila!";
                unset($_SESSION['blad']);
                header("Location: mail_sent.php");
                exit;
            }
            else
            {
                $_SESSION['blad'] = "Taki mail nie istnieje!";
            }
            $connection -> close;
        }
        catch(Exception $e)
        {
            echo 'Błąd numer:'.$e;
        }
    }
    else
    {
            $_SESSION['blad'] = "Podaj maila!";
    }
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Reset password</title>
    <link rel="stylesheet" type="text/css" href="reset.css"/>
</head>

<body>
   <div class="intro">
   <div class="inside">
    <form method="post">
           Podaj swojego maila <br/>
        <input type="text" name="mail"/>
        <input type="submit" value="Wyślij!"/>
    </form>
    <?php
        session_start();
        if(isset($_SESSION['blad'])) echo '<span style="color:red"><b>'.$_SESSION['blad'].'</b></span><br/>';
        unset($_SESSION['blad']);
    ?>
    <br/>
    <a class="btn" href="http://j-football.cba.pl/index.php">Strona główna</a>
       </div>
    </div>
</body>
</html>
