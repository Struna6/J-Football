<?php
session_start();
$proceed = true;


if(isset($_SESSION['logged']))
{
        header('Location: http://j-football.cba.pl/index.php');
        exit;
}

if(!$_SESSION['shallPass'])
{
   header('Location: http://j-football.cba.pl/index.php');
}

 if(isset($_POST['password1']))
    {
        $password1 = $_POST['password1'];
        if(strlen($password1) < 8 or strlen($password1) > 30)
        {
            $_SESSION['errorPassword1'] = 'Hasło musi zawierać między 8-30 znaków';
            $proceed = false;
        }
        
        if(!preg_match('`[A-Z]`',$password1))
        {
            $_SESSION['errorPassword1'] = 'Hasło musi zawierać minimum 1 wielką literę';
            $proceed = false;
        }
        if(!preg_match('`[a-z]`',$password1))
        {
            $_SESSION['errorPassword1'] = 'Hasło musi zawierać minimum 1 małą literę';
            $proceed = false;
        }
        if(!preg_match('`[0-9]`',$password1))
        {
            $_SESSION['errorPassword1'] = 'Hasło musi zawierać minimum 1 cyfrę';
            $proceed = false;
        }
        
        if(isset($_POST['password2']))
        {
            $password2 = $_POST['password2'];
            $hashedPassword1 = password_hash($password1, PASSWORD_DEFAULT);
            $hashedPassword2 = password_hash($password2, PASSWORD_DEFAULT);
            if($hashedPassword1 == $hashedPassword2)
            {    
                $_SESSION['errorPassword2'] = 'Podane hasła nie są identyczne';
                $proceed = false;
            }
        }
        else
        {
            $_SESSION['errorPassword2'] = 'Potwierdź hasło';
            $proceed = false;
        }
    }
    else
    {
        $_SESSION['errorPassword1'] = 'Podaj hasło';
        $proceed = false;
    }
    if(isset($_SESSION['confirmation']))
    {
        $conf = $_SESSION['confirmation'];
    }
    else
    {
        $_SESSION['errorPassword1'] = 'Błąd!';
        $proceed = false;
    }

    if($proceed == true)
    {
        try
        {
            require_once("dbLogin.php");
            $connection = new mysqli($host, $dbUser, $dbPassword, $dbName);
            if($connection->connect_errno != 0) throw new Exception($connection->connect_errno);
            if($query = $connection->query("UPDATE USERS SET confirmation = NULL, active = 1, password = '$hashedPassword1' WHERE confirmation='$conf'")) header("Location:http://j-football.cba.pl/index.php");
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

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Nowe hasło</title>
    <link rel="stylesheet" type="text/css" href="new_pass.css"/>
</head>

<body>
   <div class="intro">
   <div class="inside">
    <form method="post">
           Podaj nowe hasło: <br/>
        <input type="password" name="password1"/><br/>
        <?php
            if(isset($_SESSION['errorPassword1'])) echo '<span style="color:red"><b>'.$_SESSION['errorPassword1'].'</b></span><br/>';
            unset($_SESSION['errorPassword1']);
        ?>
           Powtórz nowe hasło: <br/>
        <input type="password" name="password2"/><br/>
        <?php
            if(isset($_SESSION['errorPassword2'])) echo '<span style="color:red"><b>'.$_SESSION['errorPassword2'].'</b></span><br/>';
            unset($_SESSION['errorPassword2']);
            if(isset($_SESSION['blad'])) echo '<span style="color:red"><b>'.$_SESSION['blad'].'</b></span><br/>';
            unset($_SESSION['blad']);
        ?>
        <input type="submit" value="Prześlij!"/>
    </form>
    <br/>
    </div>
    </div>
</body>
</html>
