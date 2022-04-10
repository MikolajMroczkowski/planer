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
    <button>
        <i class="material-icons">
            add
        </i>
        <p>Dodaj termin</p>
    </button>
</main>
</body>
</html>

