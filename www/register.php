<?php
include_once "functions.php";
include_once "config.php";
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
    <title>planer - Utwórz konto</title>
    <script src='https://www.hCaptcha.com/1/api.js' async defer></script>
    <script src="js/registerValidator.js"></script>
    <script src="js/passValidator.js"></script>
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
    <h1>Utwórz konto</h1>
    <form method="POST">
        <input type="" value="<?php echo $_POST['firstname'] ?>" name="firstname" placeholder="Imię...">
        <input type="" value="<?php echo $_POST['lastname'] ?>" name="lastname" placeholder="Nazwisko...">
        <input type="" value="<?php echo $_POST['login'] ?>" name="login" placeholder="Login...">
        <input type="email" value="<?php echo $_POST['mail'] ?>" onfocusout="checkMailFormat(this)" name="mail" placeholder="E-mail...">
        <p class="informer" id="mailInformer"></p>
        <input type="tel" value="<?php echo $_POST['phone'] ?>" onfocusout="checkPhoneFormat(this)" name="phone" placeholder="Numer Telefonu...">
        <p class="informer" id="phoneInformer"></p>
        <input type="password" oninput="checkPassPower(this)" name="password" placeholder="Hasło...">
        <div id="passPowerBox">
            <p id="passPowerInf">Hasło jest słabe</p>
        </div>
        <input type="password" name="repassword" placeholder="Powtórz Hasło...">
        <hr>
        <div class="h-captcha" data-sitekey="<?php echo $hCaptchaSiteKey ?>"></div>
        <hr>
        <p>
            <a href="login.php">Zaloguj się</a>
            <input type="submit" value="Utwórz konto">
        </p>

        <?php
        if ($_POST) {
            if (checkIsPostSet($_POST['firstname'])&&checkIsPostSet($_POST['lastname'])&&checkIsPostSet($_POST['login'])&&checkIsPostSet($_POST['mail'])&&checkIsPostSet($_POST['phone'])&&checkIsPostSet($_POST['password'])&&checkIsPostSet($_POST['repassword'])) {
                $data = array(
                    'secret' => $hCaptchaSecret,
                    'response' => $_POST['h-captcha-response']
                );
                $verify = curl_init();
                curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
                curl_setopt($verify, CURLOPT_POST, true);
                curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($verify);
                $responseData = json_decode($response);
                if ($responseData->success) {
                    if ($_POST['password'] == $_POST['repassword']) {
                        if (filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
                            if (checkPhoneNumber($_POST['phone'])) {
                                if (isAvalible("username", $_POST['login'], false, $dbadress, $dbuser, $dbpass, $dbname)) {
                                    if (isAvalible("email", $_POST['mail'], false, $dbadress, $dbuser, $dbpass, $dbname)) {
                                        if (isAvalible("phone", $_POST['phone'], false, $dbadress, $dbuser, $dbpass, $dbname)) {
                                            $conn = new mysqli($dbadress, $dbuser, $dbpass, $dbname);
                                            $stmt = $conn->prepare("INSERT INTO users(username,firstname,lastname,password,phone,email,isActive) VALUES (unhex(?),unhex(?),unhex(?),?,unhex(?),unhex(?),0)");
                                            $login = bin2hex($_POST['login']);
                                            $firstname = bin2hex($_POST['firstname']);
                                            $lastname = bin2hex($_POST['lastname']);
                                            $password = password_hash($_POST['password'],PASSWORD_BCRYPT);
                                            $phone = bin2hex(toPhoneNumber($_POST['phone']));
                                            $mail = bin2hex($_POST['mail']);
                                            $stmt->bind_param("ssssss", $login,$firstname,$lastname,$password,$phone,$mail);
                                            $stmt->execute();

                                            $stmt = $conn->prepare("SELECT id FROM users WHERE username=UNHEX(?)");
                                            $stmt->bind_param("s", $login);
                                            $stmt->execute();
                                            $result = $stmt->get_result();
                                            $stmt->close();
                                            $id=-1;
                                            while($row = $result->fetch_assoc()) {
                                                $id = $row['id'];
                                            }
                                            $activateToken = randString(120);
                                            $stmt = $conn->prepare("INSERT INTO users_email_verification (code,user) VALUES (?, ?)");
                                            $stmt->bind_param("si", $activateToken,$id);
                                            $stmt->execute();
                                            $url = $appUrl."/activate.php?code=".$activateToken;
                                            $mailContent = "<h1>Witaj ".$_POST['firstname']."</h1> Aby zweryfikować rejstracje wejdź w link <a href='".$url."'>".$url."</a>";
                                            $mailResponse =  sendMail([$_POST['mail'],$_POST['firstname']." ".$_POST['lastname']],$mailContent,"Weryfikacja Konta e-buda planer",$smtpPort,$smtpServer,$smtpUser,$smtpPass,$smtpSender);
                                            if($mailResponse=="ok") {
                                                echo "<p class='success'>Konto zostało utworzone<br>Sprawdź skrzynke mailową (spam też)</p>";
                                            }
                                            else{
                                                echo "<p class='err'>".$mailResponse."</p>";
                                            }
                                        } else {
                                            echo "<p class='err'>Ten numer telefonu jest niedostępny</p>";
                                        }
                                    } else {
                                        echo "<p class='err'>Ten e-mail jest niedostępny</p>";
                                    }
                                } else {
                                    echo "<p class='err'>Ten login jest niedostępny</p>";
                                }
                            } else {
                                echo "<p class='err'>format numeru telefonu jest nieznany</p>";
                            }
                        } else {
                            echo "<p class='err'>format adresu e-mail jest nieznany</p>";
                        }

                    }
                    else{
                        echo "<p class='err'>Hasła nie są zgodne</p>";
                    }
                }
                else {
                    echo "<p class='err'>Jeśli jseteś człowiekiem poinformuj nas o tym<br>Aplikacja przeznaczona jest dla ludzi<br>Pracujemy nad wersją dla botów</p>";
                }

            }
            else{
                echo "<p class='err'>W formularzu tworzenia konta podaj wszystkie dane</p>";
            }
        }
        ?>

    </form>

</main>
</body>
</html>

