<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>J-Football</title>
    <link rel="stylesheet" type="text/css" href="index.css"/>
</head>

<body>
    <section class="intro">
       <div class="menu">
        <?php
          session_start();
          if(isset($_SESSION['logged']))
          {
              echo '<div class="button">';
              echo '<a class="btn" href="login/logout.php">Wyloguj się</a><br/>';
              echo '</div>';
              echo '<div class="button">';
              echo '<a class="btn" href="summary/summary.php">Moje konto</a><br/>';
              echo '</div>';
          }
          else
          {   echo '<div class="button">';
              echo '<a class="btn" href="login/login.php">Zaloguj się</a><br/>';
              echo '</div>';
              echo '<div class="button">';
              echo '<a class="btn"
              href="register/register.php">Zarejestruj się</a>';
              echo '</div>';
          }
        ?>
        <span style="clear:both;"></span>
       </div>
        <div class="inner">
            <div class="inner-content">
                <h1>J-Football</h1>
                <?php
                if(isset($_SESSION['logged']))
                echo '<a class="btn" href="game/matches.php">Postaw i wygraj!</a><br/>';
                ?>
            </div>
        </div>
    </section>
</body>
</html>
