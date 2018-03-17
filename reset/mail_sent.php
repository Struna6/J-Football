<?php
    session_start();
    
    if(isset($_SESSION['logged']))
    {
        header('Location: http://j-football.cba.pl/index.php');
        exit;
    }

    if(isset($_POST['pass']))
    {
        $pass = $_POST['pass'];
        if(!preg_match('`[a-z]`',$pass))
        {
            $_SESSION['blad'] = 'Błąd!';
        }
        else
        {
        try
        {
            require_once("dbLogin.php");
            $connection = new mysqli($host, $dbUser, $dbPassword, $dbName);
            if($connection->connect_errno != 0) throw new Exception($connection->connect_errno);
            $query = $connection->query("SELECT * FROM USERS WHERE confirmation='$pass'");
            $_SESSION['confirmation'] = $pass;
            $queryNum = $query -> num_rows;
            if($queryNum > 0)
            {
                unset($_SESSION['blad']);
                $_SESSION['shallPass'] = true;
                header("Location: new_pass.php");
                exit;
            }
            else
            {
                $_SESSION['blad'] = "Błąd!";
            }
            $connection -> close;
        }
        catch(Exception $e)
        {
            echo 'Błąd numer:'.$e;
        }
        }
    }
    else
    {
            $_SESSION['blad'] = "Podaj tymczasowe hasło wysłane mailem!";
    }
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Reset hasła</title>
    <link rel="stylesheet" type="text/css" href="mail_sent.css"/>
</head>

<body>
   <div class="intro">
   <div class="inside">
    <b>Mail z tymczasowym hasłem został wysłany!</b><br/>
    <form method="post">
           <span style="font-size: 24px;">Podaj tymczasowe hasło wysłane mailem:</span> <br/>
        <input type="text" name="pass"/>
        <input type="submit"/>
    </form>
    <?php
        session_start();
        if(isset($_SESSION['blad'])) echo '<br/><span style="color:red"><b>'.$_SESSION['blad'].'</b></span><br/>';
        unset($_SESSION['blad']);
    ?>
    <br/>
    <a class="btn" href="http://j-football.cba.pl/index.php">Strona główna</a>
    </div>
    </div>
</body>
</html>
