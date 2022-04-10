<?php
session_start();
if ($_SESSION['isLoggedIn'] == true) {
    header("Location: index.php");
}
if (isset($_COOKIE['rememberMe_cookie'])) {
    require "config.php";
    $code = bin2hex($_COOKIE['rememberMe_cookie']);
    $conn = new mysqli($dbadress, $dbuser, $dbpass, $dbname);
    $stmt = $conn->prepare("SELECT u.id as id,u.username as username, u.firstname as firstname, u.lastname as lastname, u.phone as phone, u.email as email FROM users_sessions s INNER JOIN users u on u.id=s.user WHERE s.code=UNHEX(?)");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows) {
        while ($row = $result->fetch_assoc()) {
            $_SESSION['id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['firstname'] = $row['firstname'];
            $_SESSION['lastname'] = $row['lastname'];
            $_SESSION['phone'] = $row['phone'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['isLoggedIn'] = true;
            header("Location: index.php");
        }
    }
    $conn->close();
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
    <h1>Zaloguj się</h1>
    <form method="POST">
        <input type="" name="login" placeholder="login...">
        <input type="password" name="password" placeholder="hasło...">
        <hr>
        <p class="left"><input type="checkbox" name="rememberMe"> Zapamiętaj mnie</p>
        <hr>
        <p>
            <a href="forgotpass.php">Nie pamiętam hasła</a>
            <a href="register.php">Utwórz konto</a>
            <input type="submit" value="Zaloguj się">
        </p>
        <?php
        if ($_POST) {
            if ($_POST['login'] != "" && $_POST['password'] != "") {
                require "config.php";
                require "functions.php";
                $login = bin2hex($_POST['login']);
                $conn = new mysqli($dbadress, $dbuser, $dbpass, $dbname);
                $stmt = $conn->prepare("SELECT * FROM users WHERE username=UNHEX(?)");
                $stmt->bind_param("s", $login);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();

                if ($result->num_rows) {
                    while ($row = $result->fetch_assoc()) {
                        if (password_verify($_POST['password'], $row['password'])) {
                            if ($row['isActive'] == 1) {
                                if ($_POST['rememberMe']) {
                                    $rememberMeCode = randString(200);
                                    setcookie("rememberMe_cookie", $rememberMeCode,time()+10368000 );
                                    $stmt = $conn->prepare("INSERT INTO users_sessions (user,code) VALUES (?,?)");
                                    $stmt->bind_param("is", $row['id'], $rememberMeCode);
                                    $stmt->execute();
                                    $stmt->close();
                                }
                                $_SESSION['id'] = $row['id'];
                                $_SESSION['username'] = $row['username'];
                                $_SESSION['firstname'] = $row['firstname'];
                                $_SESSION['lastname'] = $row['lastname'];
                                $_SESSION['phone'] = $row['phone'];
                                $_SESSION['email'] = $row['email'];
                                $_SESSION['isLoggedIn'] = true;
                                header("Location: index.php");
                                exit;
                            } else {
                                echo "<p class='err'>Aktywuj konto poprzez e-mail</p>";
                            }
                        } else {
                            echo "<p class='err'>Błędna nazwa użytkownika lub/i hasło</p>";
                        }
                    }
                } else {
                    echo "<p class='err'>Błędna nazwa użytkownika lub/i hasło</p>";
                }
                $conn->close();
            }
        }
        ?>
    </form>
</main>
</body>
</html>

