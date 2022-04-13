<?php
session_start();
if ($_SESSION['isLoggedIn'] != true) {
    header("Location: login.php");
}
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>planer e-buda</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
          rel="stylesheet">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="body">
<?php
require_once "sidebar.php";
renderNav(0);
?>
<main class="main">
    <h1>Witaj, <b><?php echo $_SESSION['firstname']; ?></b></h1>
    <h2>Twój plan tygodnia</h2>
    <button>
        <i class="material-icons">
            arrow_back_ios
        </i>
        <p>Poprzedni tydzień</p>
    </button>
    <button>
        <i class="material-icons">
            add
        </i>
        <p>Dodaj termin</p>
    </button>
    <button>
        <i class="material-icons">
            arrow_forward_ios
        </i>
        <p>Następny tydzień</p>
    </button>
    <div class="mobilePlanBox">
        <p>Plan grid nie jest dostępny w trybie mobilnym</p>
    </div>
    <div class="planBox">
        <div class="planData">
            <?php
            require "functions.php";
            for ($x = 0; $x < 10; $x++) {
                $day = rand(2, 7);
                $hour = rand(0, 23);
                $text = randString(128);
                echo '<div class="activity" style="background-color: rgba(0,255,255,0.4); grid-column: ' . $day . '; grid-row: ' . ((($hour) * 12) + 2) . ' / ' . ((($hour + 2.5) * 12) + 2) . ';"><p>' . $text . ': </p></div>';
            }
            ?>
        </div>
        <div class="overlayLinesH">
            <div></div>
            <div class="line"></div>
            <div></div>
            <div class="line"></div>
            <div></div>
            <div class="line"></div>
            <div></div>
            <div class="line"></div>
            <div></div>
            <div class="line"></div>
            <div></div>
            <div class="line"></div>
            <div></div>
            <div class="line"></div>
            <div></div>
            <div class="line"></div>
        </div>
        <div class="overlayLinesW">
            <?php
            for ($x = 0; $x < 24; $x++) {
                echo "<div></div>";
                echo "<div class='line'></div>";
            }
            ?>
        </div>
        <div class="planBase">

            <div class=""></div>
            <div class="dayInf">Poniedziałek</div>

            <div class="dayInf">Wtorek</div>

            <div class="dayInf">Środa</div>

            <div class="dayInf">Czwartek</div>

            <div class="dayInf">Piątek</div>

            <div class="dayInf">Sobota</div>

            <div class="dayInf">Niedziela</div>
            <?php
            for ($x = 0; $x < 24; $x++) {
                echo '<div class="hourInf">' . ($x) . ':00</div>';
            }
            ?>
        </div>
    </div>
</main>
</body>
</html>

