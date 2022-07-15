<?php
if(isset($_GET["name"]) && !empty($_SERVER["PHP_SELF"]) 
	&& $_SERVER["PHP_SELF"] == "/module/captcha/captcha.php"){	

	if (!isset($captcha)) {
		$captcha = new captcha($_GET["name"]);
	}	
	$captcha->draw();
}

function getsize($s,$font,$angle = 0)
{
	$bbox = imagettfbbox(15.0,$angle,$font,$s);
	$w = abs($bbox[2] - $bbox[0]);
	$h = abs($bbox[5]-$bbox[3]);
	return array($w,$h);
}

class captcha
{
	var $name;
	function __construct($name='form')
	{
		$this->name = $name;
	}

    function draw()
    {
    	$value = function_exists("mt_rand") 
			? strval(mt_rand(10000,99999)) 
			: strval(rand(10000,99999));			
		
		if(isset($_SESSION["captcha"][$this->name]['code'])){
			$value = $_SESSION["captcha"][$this->name]['code'];
		}else{
			$_SESSION["captcha"][$this->name] = array(
				"code" => $value,
				"time" => time()
			);
		}

		if(!defined('MODULE')){
			$ddir = str_replace('\\', '/', dirname(__FILE__));
			define('MODULE', $ddir);
			$font = MODULE."/brushtype.ttf";
		}else{
			$font = MODULE."/captcha/brushtype.ttf";
		}
		//$font = "verdana.ttf";

		list($w,$h) = getsize($value,$font);

		list($wm) = getsize("w",$font,0);

		$w = $wm*strlen($value)+5;

		$img = imagecreatetruecolor($w+14,$h+12);

		$textcolor = imagecolorallocate($img,rand(0,50),rand(0,50),rand(0,50));
		$bgcolor = imagecolorallocate($img,224,235,255);

		imagefill($img,1,1,$bgcolor);

		for($i = 0; $i < 150; $i++)
		{
			$noisecolor = imagecolorallocate($img,rand(100,200),rand(100,200),rand(100,200));
			imagesetpixel($img,rand(0,$w+10),rand(0,$h+12),$noisecolor);
		}

		list($wm) = getsize("m",$font,0);

		for($i = 0; $i < strlen($value); $i++)
		{
			$color = imagecolorallocate($img,rand(0,127),rand(0,128),rand(0,128));
			imagettftext($img,15.0,rand(-15,15),imagesx($img)/2-($wm*5+5)/2+(($wm+2)*$i),$h+6,$color,$font,$value{$i});
		}

		header("Content-type: image/png");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		imagepng($img);
	}
	function check($value)
	{
		if(!isset($_SESSION["captcha"][$this->name]["code"])) { return false; }
		if($_SESSION["captcha"][$this->name]["code"] == $value && !empty($value)){
			return true;
		}
		return false;
	}

	function get_src()
	{
		return $_SERVER["REQUEST_URI"]."?name=".$this->name;
	}

}

/*$captcha = new captcha($name);

$captcha->draw();*/

?>