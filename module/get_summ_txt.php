<?php

/*функции для перевода дат и сумм в буквы*/
define('M2S_KOPS_DIGITS', 0x01);	// digital copecks 1 - 0x01
define('M2S_KOPS_MANDATORY', 0x02);	// mandatory copecks 2 - 0x02
define('M2S_KOPS_SHORT', 0x04);	// shorten copecks 4 - 0x04
 
function money2str_ru($money, $options = 3) { 
	if(!extension_loaded('bcmath')) return $money;
    $money = preg_replace('/[\,\-\=]/', '.', $money);
    $numbers_m = array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь',
        'восемь', 'девять', 'десять', 'одиннадцать', 'двенадцать', 'тринадцать',
        'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать',
        'девятнадцать', 'двадцать', 30 => 'тридцать', 40 => 'сорок', 50 => 'пятьдесят',
        60 => 'шестьдесят', 70 => 'семьдесят', 80 => 'восемьдесят', 90 => 'девяносто',
        100 => 'сто', 200 => 'двести', 300 => 'триста', 400 => 'четыреста',
        500 => 'пятьсот', 600 => 'шестьсот', 700 => 'семьсот', 800 => 'восемьсот',
        900 => 'девятьсот');
 
    $numbers_f = array('', 'одна', 'две');
 
    $units_ru = array(
        (($options & M2S_KOPS_SHORT)
            ? array('коп.', 'коп.', 'коп.')
            : array('копейка', 'копейки', 'копеек')),
        array('рубль', 'рубля', 'рублей'),
        array('тысяча', 'тысячи', 'тысяч'),
        array('миллион', 'миллиона', 'миллионов'),
        array('миллиард', 'миллиарда', 'миллиардов'),
        array('триллион', 'триллиона', 'триллионов'),
    );
 
    $ret = '';
 
    // enumerating digit groups from left to right, from trillions to copecks
    // $i == 0 means we deal with copecks, $i == 1 for roubles,
    // $i == 2 for thousands etc.
    for ($i = sizeof($units_ru) - 1; $i >= 0; $i--) {
 
        // each group contais 3 digits, except copecks, containing of 2 digits
        $grp = ($i != 0) ? dec_digits_group($money, $i - 1, 3) :
            dec_digits_group($money, -1, 2);
 
        // process the group if not empty
        if ($grp != 0) {
 
            // digital copecks
            if ($i == 0 && ($options & M2S_KOPS_DIGITS)) {
                $ret .= sprintf('%02d', $grp). ' ';
                $dig = $grp;
 
            // the main case
            } else for ($j = 2; $j >= 0; $j--) {
                $dig = dec_digits_group($grp, $j);
                if ($dig != 0) {
 
                    // 10 to 19 is a special case
                    if ($j == 1 && $dig == 1) {
                        $dig = dec_digits_group($grp, 0, 2);
                        $ret .= $numbers_m[$dig]. ' ';
                        break;
                    }
 
                    // thousands and copecks are Feminine gender in Russian
                    elseif (($i == 2 || $i == 0) && $j == 0 && ($dig == 1 || $dig == 2))
                        $ret .= $numbers_f[$dig]. ' ';
 
                    // the main case
                    else $ret .= $numbers_m[(int) ($dig * pow(10, $j))]. ' ';
                }
            }
            $ret .= $units_ru[$i][sk_plural_form($dig)]. ' ';
        }
 
        // roubles should be named in case of empty roubles group too
        elseif ($i == 1 && $ret != '')
            $ret .= $units_ru[1][2]. ' ';
 
        // mandatory copecks
        elseif ($i == 0 && ($options & M2S_KOPS_MANDATORY))
            $ret .= (($options & M2S_KOPS_DIGITS) ? '00' : 'ноль').
                ' '. $units_ru[0][2];
    }
 
    return trim($ret);
}
 
// service function to select the group of digits
function dec_digits_group($number, $power, $digits = 1) {
    return (int) bcmod(bcdiv($number, bcpow(10, $power * $digits, 8)),
        bcpow(10, $digits, 8));
}
 
// service function to get plural form for the number
function sk_plural_form($d) {
    $d = $d % 100;
    if ($d > 20) $d = $d % 10;
    if ($d == 1) return 0;
    elseif ($d > 0 && $d < 5) return 1;
    else return 2;
}










if(!function_exists('getFormatData')){

	//Отдает отформатированную дату
	function getFormatData($time) {
		$day = date ( "d", $time );
		$mon = date ( "m", $time );
		$year = date ( "Y", $time );
		switch ($mon) {
			case "01" :
				$mon = 'января';
				break;
			case "02" :
				$mon = 'февраля';
				break;
			case "03" :
				$mon = 'марта';
				break;
			case "04" :
				$mon = 'апреля';
				break;
			case "05" :
				$mon = 'мая';
				break;
			case "06" :
				$mon = 'июня';
				break;
			case "07" :
				$mon = 'июля';
				break;
			case "08" :
				$mon = 'августа';
				break;
			case "09" :
				$mon = 'сентября';
				break;
			case "10" :
				$mon = 'октября';
				break;
			case "11" :
				$mon = 'ноября';
				break;
			case "12" :
				$mon = 'декабря';
				break;
		}
		return $day . ' ' . $mon . ' ' . $year;
	}
}

if(!function_exists('mb_ucfirst')){
	function mb_ucfirst($string, $enc = 'UTF-8'){
		return mb_strtoupper(mb_substr($string, 0, 1, $enc), $enc) . 
			mb_substr($string, 1, mb_strlen($string, $enc), $enc);
	}
}


?>