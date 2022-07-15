<?php
//session_start();
include("captcha.php");

$name = isset($_GET["name"]) ? $_GET["name"] : "mycaptcha";

$captcha = new captcha($name);

$captcha->draw();

/*function rgb($r,$g,$g)
{
	$color = $r+$g*0xff00;
}*/
/*
$font = "verdana.ttf";

function getsize($s,$font,$angle = 0)
{
	$bbox = imagettfbbox(11.0,$angle,$font,$s);
	$w = abs($bbox[2] - $bbox[0]);
	$h = abs($bbox[5]-$bbox[3]);
	return array($w,$h);
}

$time = time();
$str = md5($time);

$code = substr($str,rand(0,26),5);
$angle = rand(-5,5);

$_SESSION['captcha_code'] = $code;
$_SESSION['captcha_time'] = $time;

list($w,$h) = getsize($code,$font);

list($wm) = getsize("m",$font,0);

$w = $wm*5;

$img = imagecreatetruecolor($w+10,$h+8);

$textcolor = imagecolorallocate($img,rand(0,50),rand(0,50),rand(0,100));
$bgcolor = imagecolorallocate($img,224,235,255);

imagefill($img,1,1,$bgcolor);

for($i = 0; $i < 150; $i++)
{
	$noisecolor = imagecolorallocate($img,rand(100,200),rand(100,200),rand(100,200));
	imagesetpixel($img,rand(0,$w+10),rand(0,$h+10),$noisecolor);
}

list($wm) = getsize("m",$font,0);

for($i = 0; $i < strlen($code); $i++)
{
	$color = imagecolorallocate($img,rand(0,255),rand(0,128),rand(0,255));
	imagettftext($img,11.0,rand(-30,30),4+($wm+2)*$i,$h+4,$color,$font,$code{$i});
}
*/
/*header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

header("Content-type: image/png");
imagepng($img);*/

?>