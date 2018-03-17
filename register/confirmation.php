<?php
    session_start();
    if(isset($_POST['confirmationCode']))
    {
        $confirmationCode = $_POST['confirmationCode'];
        if(!preg_match('`[a-z]`',$confirmationCode))
        {
            $_SESSION['error'] = 'Błąd!';
        }
        else
        {
            try
            {
                require_once("dbLogin.php");
                $connection = new mysqli($host, $dbUser, $dbPassword, $dbName);
                if($connection -> connect_errno != 0) throw new Exception($connection->error);

                $result = $connection -> query("Select * from USERS Where confirmation= '$confirmationCode'");
                if(!$result) throw new Exception($connection->error);
                $resultNumber = $result -> num_rows;
                if($resultNumber == 1)
                {
                    $connection -> query("UPDATE USERS SET active = 1, money = 50, confirmation = NULL WHERE confirmation='$confirmationCode'");
                    $_SESSION['error'] = 'Twoje konto zostało pomyślnie aktywowane!';
                    session_destroy();
                    unset($_POST['confirmationCode']);
                    //WSTAW INDEX.PHP
                }
                else
                {
                    $_SESSION['error'] = 'Twoje konto jest już aktywowane lub nie istnieje!';
                }
                $connection -> close();
            }
            catch(Exception $e)
            {
                    echo "Błąd numer:".$e;
            }
        }
    }
    else
    {
        $_SESSION['error'] = 'Podaj kod wysłany mailem';
    }
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Account Confirmation</title>
    <link rel="stylesheet" type="text/css" href="confirmation.css"/>
</head>

<body>
   <div class="intro">
   <div class="inside">
    <form method="post">
        <input type="text" name="confirmationCode" value="<?php echo $_POST['confirmationCode'];?>"/><br/>
        <input type="submit"/><br/>
    </form>
    <?php
    
    if(isset($_SESSION['logged']) or isset($_COOKIE['logged']))
    {
        header('Location: http://j-football.cba.pl/index.php');
        exit;
    }
    if(isset($_SESSION['error'])) echo '<span style="color:red"><b>'.$_SESSION['error'].'</b></span><br/>';
    echo '<br/><a class="btn" href="http://j-football.cba.pl/login/login.php">Zaloguj się</a>';
    ?>
    </div>
    </div>
</body>
</html>
