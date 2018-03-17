<?php
    session_start();
    $flag = true;
    $_SESSION['numLogging']++;
    if(isset($_SESSION['logged']))
    {
        header('Location: http://j-football.cba.pl/index.php');
        exit;
    }

    if(!isset($_POST['login']))
    {
        $flag = false;
        $_SESSION['errorLogin'] = 'Nie podałeś loginu!';
    }
    else
    {
        $login = $_POST['login'];
    }

    if(!isset($_POST['password']))
    {
        $flag = false;
        $_SESSION['errorPassword'] = 'Nie podałeś hasła!';
    }
    else
    {
        $password = $_POST['password'];
    }

    if($_SESSION['numLogging'] > 3)
    {
        $captchaSecret = "6LehSzwUAAAAAJd2sxHnV9rUeexG33gTDkuaKWmh";
        $captchaCheck = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$captchaSecret.'&response='.$_POST['g-recaptcha-response']);
        $captchaAnswer = json_decode($captchaCheck);
        if(!$captchaAnswer->success)
        {
            $_SESSION['errorCaptcha'] = 'Potwierdź, że nie jesteś botem';
            $flag = false;
        }
    }
    if($_SESSION['numLogging'] > 10)
    {
        header("Location:https://www.google.pl/");
        exit();
    }

    try
    {
        require_once("dbLogin.php");
        $connection = new mysqli($host, $dbUser, $dbPassword, $dbName);
        if($connection->connect_errno != 0) throw new Exception($connection->connect_errno);
        $query = $connection->query("SELECT * FROM USERS WHERE login='$login'");
        if(!$query) throw new Exception($connection->connect_errno);
        $queryNumber = $query -> num_rows;

        if($queryNumber == 0)
        {
            $flag = false;
            $_SESSION['errorData'] = 'Nieprawidłowe dane!';
        }
        else
        {
            $qRow = $query -> fetch_assoc();
            if($qRow['active'] != 1) 
            {
                $flag = false;
                $_SESSION['errorData'] = 'Konto nieaktywne!';
            }

            if(!password_verify($password, $qRow['password']))
            {
                $flag = false;
                $_SESSION['errorData'] = 'Nieprawidłowe dane!';
            }
        }
        if($flag == true)
        {
            $_SESSION['logged'] = 1;
            $_SESSION['numLogging'] = 0;
            $_SESSION['user'] = $login;
            unset($_POST['password']);
            header('Location:login_success.php');
            exit;
        }
        
        $connection -> close;
    }
    catch(Exception $e)
    {
        echo 'Błąd numer:'.$e;
    }
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Login</title>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <link rel="stylesheet" type="text/css" href="login.css"/>
</head>

<body>
   <section class="intro"> 
   <div class="inside">
    <form method="post">
        Login:<br/>
        <input type="text" name="login" value="<?php if(isset($_POST['login'])) echo $_POST['login'];?>" placeholder="Podaj login"/>
        <?php
            if(isset($_SESSION['errorLogin'])) echo '<span style="color:red"><b>'.$_SESSION['errorLogin'].'</b></span><br/>';
            unset($_SESSION['errorLogin']);
        ?>
        <br/>
        Hasło:<br/>
        <input type="password" name="password" placeholder="Podaj hasło"/>
        <?php
            if(isset($_SESSION['errorPassword'])) echo '<span style="color:red"><b>'.$_SESSION['errorPassword'].'</b></span><br/>';
            unset($_SESSION['errorPassword']);
        ?>
        <br/>
        <?php
            if($_SESSION['numLogging'] > 3)
            {
                echo '<br/><div class="g-recaptcha" data-sitekey="6LehSzwUAAAAANv3CcSEgmHFcVhe7e-aC0VN5Nkm"></div><br/><br/>';
            }
            if(isset($_SESSION['errorCaptcha'])) echo '<span style="color:red"><b>'.$_SESSION['errorCaptcha'].'</b></span><br/>';
            unset($_SESSION['errorCaptcha']);
        ?>
        <input type="submit" value="Prześlij"/><br/><br/>
        <?php
            if(isset($_SESSION['errorData'])) echo '<span style="color:red"><b>'.$_SESSION['errorData'].'</b></span><br/><br/>';
            unset($_SESSION['errorData']);
        ?>
        Nie masz konta? <a class="btn" href="http://j-football.cba.pl/register/register.php">Zarejestruj się!</a><br/>
        Zapomniałeś hasła? <a class="btn" href="http://j-football.cba.pl/reset/reset.php">Zresetuj hasło!</a><br/><br/>
        <br/><br/>
        <a class="btn2" href="http://j-football.cba.pl/index.php">Strona główna</a>
    </div>
    </form>
    </section>
</body>
</html>
