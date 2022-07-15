<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>PHPMailer - SMTP test</title>
</head>
<body>
<?php

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');

require 'module/phpmailer528/PHPMailerAutoload.php';

$mail = new PHPMailer();
$mail->isSMTP();
$mail->SMTPSecure = 'ssl';
$mail->CharSet = 'UTF-8';
$mail->SMTPDebug = 0;
$mail->Debugoutput = 'html';
$mail->Host = "smtp.yandex.ru";
$mail->Port = 465;
$mail->SMTPAuth = true;
$mail->Username = "robot@363.ru";
$mail->Password = "pbvby6650027";
$mail->setFrom('robot@363.ru', '363.ru');
$mail->addReplyTo('robot@363.ru', '363.ru');
$mail->addAddress('dmvlad19@gmail.com', 'dmvlad19');
$mail->Subject = 'Проверка 363 отправки почты';
$mail->msgHTML("<h1>Ура, заработало!</h1><p>Тест 363 прошел <b>отлично</b>. Сообщение отправлено.</p>");
$mail->AltBody = 'Проверка 363 отправки почты';
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}
?>
</body>
</html>
