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
</head>
<body class="logreg">
<noscript>
    <h1>Włącz JavaScript aby używać <b>e-buda planner</b></h1>
</noscript>
<main>
    <h1>Weryfikacja</h1>
    <form method="POST">
        <p>Wysłaliśmy ci kod SMS z kodem. Wprowadź go poniżej </p>
        <input type="" name="code" placeholder="kod...">
        <p>
            <input type="submit" value="Zweryfikuj">
        </p>
        <?php
        if ($_GET['code'] != "" && isset($_GET['code'])) {
            require "config.php";
            require "functions.php";
            $conn = new mysqli($dbadress, $dbuser, $dbpass, $dbname);
            $stmt = $conn->prepare("SELECT u.phone as phone, u.id as id FROM users_email_verification v INNER JOIN users u on v.user=u.id WHERE code=UNHEX(?)");
            $code = bin2hex($_GET['code']);
            $stmt->bind_param("s", $code);
            $stmt->execute();
            $result = $stmt->get_result();
            $id = -1;
            $phone = "";
            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $phone = $row['phone'];
            }

            if ($_POST) {
                $stmt = $conn->prepare("SELECT id FROM users_sms_verification WHERE code=UNHEX(?)");
                $code = bin2hex($_POST['code']);
                $stmt->bind_param("s", $code);
                $stmt->execute();
                $result = $stmt->get_result();
                $activationID = -1;
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $activationID = $row['id'];
                    }
                    $conn->query("UPDATE users SET isActive=1 WHERE id=" . $id);
                    $conn->query("DELETE FROM users_email_verification WHERE user=" . $id);
                    $conn->query("DELETE FROM users_sms_verification WHERE user=" . $id);
                    echo "<p class='success'> Konto zostało aktywowane <br><a href='login.php'>Zaloguj się</a></p>";
                } else {
                    echo "<p class='err'>Podaj poprawny kod</p>";
                }

            } else {
                $stmt = $conn->prepare("SELECT code FROM users_sms_verification WHERE user=?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $isCodeSent = $result->num_rows == 0;
                if ($isCodeSent) {
                    $newCode = randString(6);
                    $stmt = $conn->prepare("INSERT INTO users_sms_verification (user, code) VALUES (?,?)");
                    $stmt->bind_param("is", $id, $newCode);
                    $stmt->execute();
                    $ch = curl_init();
                    $smsUrlPrepared = str_replace("SENDTOREPLACE", $phone, $smsUrl);
                    $smsUrlPrepared = str_replace("SMSCONTENTREPLACER", "e-buda planer: Twój kod autoryzacji to: " . $newCode, $smsUrlPrepared);
                    $smsUrlPrepared = str_replace(" ", "+", $smsUrlPrepared);
                    curl_setopt($ch, CURLOPT_URL, $smsUrlPrepared);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $output = curl_exec($ch);
                    $output = explode("|", $output);
                    if ($output['0'] == 'OK') {
                        echo "<p class='success'>Wysłano</p>";
                    } else {
                        echo "<p class='err'>Błąd api sms</p>";
                    }
                    curl_close($ch);
                }
            }
            $conn->close();
        } else {
            die("<p class='err'>Brak tokenu aktywacji!</p>");
        }
        ?>
    </form>
</main>
</body>
</html>


