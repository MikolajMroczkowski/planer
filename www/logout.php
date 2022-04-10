<?php
session_start();
session_destroy();
if ($_SESSION['isLoggedIn'] == true) {
    if (isset($_COOKIE['rememberMe_cookie'])) {
        require "config.php";
        $code = bin2hex($_COOKIE['rememberMe_cookie']);
        $conn = new mysqli($dbadress, $dbuser, $dbpass, $dbname);
        $stmt = $conn->prepare("DELETE FROM users_sessions WHERE code=UNHEX(?)");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $conn->close();
        setcookie('rememberMe_cookie', null, -1, '/');
    }
}

header("Location: login.php");
?>