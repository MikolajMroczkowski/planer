<?php
session_start();
if ($_SESSION['isLoggedIn'] == true) {
    header("Location: index.php");
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
</head>
<body class="logreg">
<noscript>
    <h1>Włącz JavaScript aby używać <b>e-buda planner</b></h1>
</noscript>
<main>
    <h1>Reset hasła</h1>
    <form method="POST">
        <input type="email" name="email" placeholder="email...">
        <hr>
        <hr>
        <p>
            <a href="login.php">Zaloguj się</a>
            <input type="submit" value="Zresetuj hasło">
        </p>
        <?php
        if ($_POST) {
            require "config.php";
            require "functions.php";
            $email = bin2hex($_POST['email']);
            $conn = new mysqli($dbadress, $dbuser, $dbpass, $dbname);
            $stmt = $conn->prepare("SELECT * FROM users WHERE email=UNHEX(?) AND isActive = 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            if ($result->num_rows) {
                while ($row = $result->fetch_assoc()) {
                    $resetCode = randString(200);
                    $stmt = $conn->prepare("INSERT INTO users_lostpass (user,code) VALUES (?,?)");
                    $stmt->bind_param("is", $row['id'], $resetCode);
                    $stmt->execute();
                    $stmt->close();
                    $resetUrl = $appUrl."/resetpass.php?id=".$resetCode;
                    $content = "<h1>Witaj, <b>" . $row['firstname'] . "</b></h1><p>Twój login to <b>" . $row['username'] . "</b>. Aby zresetować hasło wejdź w link poniżej</p><a href='" . $resetUrl . "'>" . $resetUrl . "</a>";
                    $mailTo = [$_POST['email'],$row['firstname']];
                    $mailResponse =  sendMail($mailTo, $content, "Reset hasła", $smtpPort,$smtpServer,$smtpUser,$smtpPass,$smtpSender);
                    if($mailResponse=="ok") {
                        echo "<p class='success'>Wysłano reset hasła<br>Sprawdź skrzynke mailową (spam też)</p>";
                    }
                    else{
                        echo "<p class='err'>".$mailResponse."</p>";
                    }
                }
            } else {
                echo "<p class='err'>Nieznaleziono Użytkownika</p>";
            }
            $conn->close();
        }
        ?>
    </form>
</main>
</body>
</html>

