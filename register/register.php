<?php
       
    session_start();
    $proceed = true;
{
    if(isset($_SESSION['logged']))
    {
        header('Location: http://j-football.cba.pl/index.php');
        exit;
    }

    if(isset($_POST['login']))
    {
        $login = $_POST['login'];
        if(ctype_alnum($login))
        {
            if(strlen($login) < 3 or strlen($login) > 12)
            {
                $_SESSION['errorLogin'] = 'Login musi mieć długość 3-12 znaków';
                $proceed = false;
            }
        }
        else
        {
            $_SESSION['errorLogin'] = 'Login może zawierać tylko znaki i cyfry';
            $proceed = false;
        }     
    }
    else
    {
        $_SESSION['errorLogin'] = 'Podaj login';
        $proceed = false;
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
    
    if(isset($_POST['email']))
    {
        $email = $_POST['email'];
        if(filter_var($email, FILTER_VALIDATE_EMAIL) == false)
        {
            $_SESSION['errorMail'] = 'Niewłaściwy adres mail';
            $proceed = false;
        }
    }
    else
    {
        $_SESSION['errorMail'] = 'Podaj maila';
        $proceed = false;
    }
    
    if(!isset($_POST['checked']))
    {
        $_SESSION['errorChecked'] ='Potwierdź regulamin';
        $proceed = false;
    }
    
    $captchaSecret = "6LehSzwUAAAAAJd2sxHnV9rUeexG33gTDkuaKWmh";
    $captchaCheck = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$captchaSecret.'&response='.$_POST['g-recaptcha-response']);
    $captchaAnswer = json_decode($captchaCheck);
    if(!$captchaAnswer->success)
    {
        $_SESSION['errorCaptcha'] = 'Potwierdź, że nie jesteś botem';
        $proceed = false;
    }
    
    try
        {
            require_once('dbLogin.php');
            $connection = new mysqli($host, $dbUser, $dbPassword, $dbName);
            if($connection -> connect_errno != 0) throw new Exception($connection->error);
        
            $result = $connection -> query("Select * from USERS Where login= '$login'");
            if(!$result) throw new Exception($connection->error);
            $resultNumber = $result -> num_rows;
            if($resultNumber > 0)
            {
                $_SESSION['errorUserExists'] = 'Taki użytkownik już istnieje!';
                $proceed = false;        
            }
            $result = $connection -> query("Select * from USERS Where email= '$email'");
            if(!$result) throw new Exception($connection->error);
            $resultNumber = $result -> num_rows;
            if($resultNumber > 0)
            {
                $_SESSION['errorUserExists'] = 'Użytkownik o takim mailu już istnieje!';
                $proceed = false;        
            }
            if($proceed == true)
            {   
                $randomNumber = uniqid();
                if($connection -> query("INSERT INTO USERS VALUES ('$email','$hashedPassword2','$login',0,0,0,'$randomNumber')"))
                {
                    $mailContent = "Witamy na portalu!\n\n Twój unikalny kod do potwierdzenia rejestracji:".$randomNumber."\nProsimy go o wpisanie tutaj: 
                    j-football.cba.pl/register/confirmation.php \n\n";
                    if(mail($email, 'Rejestracja na portalu J-Football', $mailContent, 'From:admin@j-football.cba.pl'))
                    {
                        $_SESSION['registrationCompleted'] = true;
                        unset($_POST['login']);
                        unset($_POST['password1']);
                        unset($_POST['password2']);
                        unset($_POST['email']);
                        unset($_POST['checked']);
                        unset($_POST['g-recaptcha-response']);
                        $connection -> close();
                        header('Location:register_success.php');
                        exit;
                    }
                    else
                    {
                        throw new Exception("Błąd wysyłania maila, spróbuj później!");
                    }
                }
                else
                {
                    throw new Exception($connection->error);
                }
                
            }
            $connection -> close();
        }
        catch(Exception $e)
        {
                echo "Błąd numer:".$e;
        }
    
} //sprawdzenie poprawności danych
    
?>

    <!DOCTYPE html>
    <html lang="pl">

    <head>
        <meta charset="utf-8">
        <title>Register</title>
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <link rel="stylesheet" type="text/css" href="register.css"/>
    </head>

    <body>
       <div class="intro">
        <div class="inside">
        <form method="post">
            Login:<br/>
            <input type="text" name="login" value="<?php if(isset($_POST['login'])) echo $_POST['login'];?>" />
            <?php
            if(isset($_SESSION['errorLogin'])) echo '<br/><span style="color:red"><b>'.$_SESSION['errorLogin'].'</b></span><br/>';
            unset($_SESSION['errorLogin']);
            ?>
            Mail:<br/>
            <input type="email" name="email" value="<?php if(isset($_POST['email'])) echo $_POST['email'];?>" />
            <?php
            if(isset($_SESSION['errorMail'])) echo '<br/><span style="color:red"><b>'.$_SESSION['errorMail'].'</b></span><br/>';
            unset($_SESSION['errorMail']);
            ?>
            Hasło:<br/>
            <input type="password" name="password1" />
            <?php
            if(isset($_SESSION['errorPassword1'])) echo '<br/><span style="color:red"><b>'.$_SESSION['errorPassword1'].'</b></span><br/>';
            unset($_SESSION['errorPassword1']);
            ?>
            Potwierdź hasło:<br/>
            <input type="password" name="password2" />
            <?php
            if(isset($_SESSION['errorPassword2'])) echo '<br/><span style="color:red"><b>'.$_SESSION['errorPassword2'].'</b></span><br/>';
            unset($_SESSION['errorPassword2']);
            ?>
            <br/>
            Czy akceptujesz regulamin?<br/>
            <input type="checkbox" name="checked" /> Tak
            <?php
            if(isset($_SESSION['errorChecked'])) echo '<br/><span style="color:red"><b>'.$_SESSION['errorChecked'].'</b></span><br/>';
            unset($_SESSION['errorChecked']);
            ?>
            <div class="g-recaptcha" data-sitekey="6LehSzwUAAAAANv3CcSEgmHFcVhe7e-aC0VN5Nkm"></div>
            <?php
            if(isset($_SESSION['errorCaptcha'])) echo '<br/><span style="color:red"><b>'.$_SESSION['errorCaptcha'].'</b></span><br/>';
            unset($_SESSION['errorCaptcha']);
            ?>
            <input type="submit" value="Zarejestruj się!"/>
            <?php
            if(isset($_SESSION['errorUserExists'])) echo '<br/><span style="color:red"><b>'.$_SESSION['errorUserExists'].'</b></span><br/>';
            unset($_SESSION['errorUserExists']);
            ?>
        </form><br/>
        <a class="btn" href="http://j-football.cba.pl/index.php">Strona główna</a>
         </div>
        </div>
    </body>

    </html>
