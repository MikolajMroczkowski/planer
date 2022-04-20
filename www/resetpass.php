<?php
session_start();
if ($_SESSION['isLoggedIn'] == true) {
    header("Loaction: index.php");
}
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>planer - Zaloguj się</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
          rel="stylesheet">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/passValidator.js"></script>
</head>
<body class="logreg">
<noscript>
    <h1>Włącz JavaScript aby używać <b>e-buda planner</b></h1>
</noscript>
<div class="lock" id="lock">
    <h1>Kod sprawy nieznany</h1>
</div>
<main>
    <h1>Zmiana Hasła</h1>
    <form method="POST">
        <input type="password" oninput="checkPassPower(this)" name="password" placeholder="Nowe Hasło...">
        <div id="passPowerBox">
            <p id="passPowerInf">Hasło jest słabe</p>
        </div>
        <input type="password" name="rePassword" placeholder="Powtórz nowe hasło...">
        <p>
            <a href="login.php" >Zaloguj się</a>
            <input type="submit" value="Zmień">
        </p>
        <?php
        if($_POST) {
            if ($_POST['password'] != "") {
                if ($_POST['password'] == $_POST['rePassword']) {
                    $codeHex = bin2hex($_GET['id']);
                    require "config.php";
                    require "functions.php";
                    $conn = new mysqli($dbadress, $dbuser, $dbpass, $dbname);
                    $stmt = $conn->prepare("SELECT * FROM users_lostpass WHERE code=UNHEX(?)");
                    $stmt->bind_param("s", $codeHex);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    if ($result->num_rows) {
                        while ($row = $result->fetch_assoc()) {
                            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
                            $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
                            $stmt->bind_param("si", $password, $row['user']);
                            $stmt->execute();
                            $stmt->close();
                            $stmt = $conn->prepare("DELETE FROM users_lostpass WHERE code=UNHEX(?)");
                            $stmt->bind_param("s", $codeHex);
                            $stmt->execute();
                            $stmt->close();
                            echo "<script>document.getElementById('lock').style.display='none'</script>"."\n"."<p class='success'>Hasło zostało zmienione</p>";
                        }
                    }
                    $conn->close();
                } else {
                    echo "<p class='err'>Hasła są niezgodne</p>";
                }
            } else {
                echo "<p class='err'>Hasło niemoże być puste</p>";
            }
        }
        ?>
    </form>
</main>
</body>
</html>
<?php
if ($_GET) {
    $codeHex = bin2hex($_GET['id']);
    require "config.php";
    require "functions.php";
    $conn = new mysqli($dbadress, $dbuser, $dbpass, $dbname);
    $stmt = $conn->prepare("SELECT * FROM users_lostpass WHERE code=UNHEX(?)");
    $stmt->bind_param("s", $codeHex);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows) {
        echo "<script>document.getElementById('lock').style.display='none'</script>";
    }
    $conn->close();
}
?>


