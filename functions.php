<?php

function checkIsPostSet($post)
{
    return isset($post) & $post != "";
}

function checkPhoneNumber($number)
{
    if (strlen($number) == 15) {
        if (is_numeric(toPhoneNumber($number))) {
            return true;
        }
        return false;
    }
    return false;
}

function toPhoneNumber($number)
{
    return str_replace(["+", "-", " "], "", $number);
}

function isAvalible($param, $val, $addIsActive,$dbadress, $dbuser, $dbpass, $dbname)
{
    $val = bin2hex($val);
    $conn = new mysqli($dbadress, $dbuser, $dbpass, $dbname);
    if ($addIsActive) {
        $sql = "SELECT * from users where isActive=1 " . $param . "=unhex(?)";
    } else {
        $sql = "SELECT * from users where " . $param . "=unhex(?)";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $val);
    $stmt->execute();
    $result = $stmt->get_result();
    $isLoginAvailable = $result->num_rows == 0;
    $conn->close();
    return $isLoginAvailable;
}
function randString($length) {
    $char = "abcdefghijklmnopqrstuvwxyz0123456789";
    $char = str_shuffle($char);
    for($i = 0, $rand = '', $l = strlen($char) - 1; $i < $length; $i ++) {
        $rand .= $char[mt_rand(0, $l)];
    }
    return $rand;
}
function sendMail($to, $content,$subject,$mailPort,$mailAdress,$mailUsername,$mailPassword,$mailSender){
    include 'vendor/autoload.php';
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->CharSet = "UTF-8";
        $mail->Host       = $mailAdress;
        $mail->SMTPAuth   = true;
        $mail->Username   = $mailUsername;
        $mail->Password   = $mailPassword;
        $mail->SMTPSecure = "tls";
        $mail->Port       = $mailPort;
        $mail->setFrom($mailSender[0], $mailSender[1]);
        $mail->addAddress($to[0], $to[1]);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $content;
        $mail->send();
        return "ok";
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
