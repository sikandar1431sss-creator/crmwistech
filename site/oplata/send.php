<?php
file_put_contents('file.txt', json_encode($_POST), FILE_APPEND);
$headers = "MIME-Version: 1.0" . "\r\n" .
    "Content-type: text/plain; charset=UTF-8" . "\r\n";

$message = "Заявка с сайта \r\n";
$message .= "user_id: ".$_POST['user_id']."\r\n";
$message .= "paylink_id: ".$_POST['paylink_id']."\r\n";
$message .= "price: ".$_POST['price']."\r\n";
$message .= "vin: ".$_POST['vin']."\r\n";
$message .= "name_avto: ".$_POST['name_avto']."\r\n";
$message .= "REG: ".$_POST['REG']."\r\n";
$message .= "year: ".$_POST['year']."\r\n";
$message .= "country: ".$_POST['country']."\r\n";

if (mail("topfirm85@mail.ru", "Заявка с сайта", $message ,$headers))
{
    echo "сообщение успешно отправлено";
} else {
    echo "при отправке сообщения возникли ошибки";
}
?>