<?php
/**
 * Simpla! months modifier plugin
 *
 * Type:     modifier
 * Name:     months
 * Purpose:  converts $data in name of month 
 *  
 * Input:<br>
 *         - string: data to convert in month by word
 * Usage example:
 * {$v.date_insert|date:"m"|months:$site.lang:"ucfirst":3}
 */
function tpl_modifier_months($data, $language="en", $case="", $shorten=0)
{
	if(empty($language)){ return $data; }
	if(strlen($language) > 2){ $language = mb_substr($language, 0, 2, 'UTF-8'); }

	$array = tpl_get_months_array($language);
	if(strlen($data) == 1){ $data = '0'.$data; }
	//$data = date('m', strtotime($data));
	$str = isset($array[$data]) ? $array[$data] : $data;
	
	if($shorten > 0 && $shorten < strlen($str)){
		$str = mb_substr($str, 0, $shorten, 'UTF-8');
	}

	if($case == 'upper'){ return mb_strtoupper($str, 'UTF-8'); }
	if($case == 'lower'){ return mb_strtolower($str, 'UTF-8'); }
	if($case == 'ucfirst'){ 
		$str = mb_strtolower($str, 'UTF-8');
		return mb_strtoupper(mb_substr($str, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr(mb_convert_case($str, MB_CASE_LOWER, 'UTF-8'), 1, mb_strlen($str), 'UTF-8');
	}
	return $str;
}

function tpl_get_months_array($language)
{
	$array = array(
		'en' => array(
			'1' => 'January',
			'01' => 'January',
			'2' => 'February',
			'02' => 'February',
			'3' => 'March',
			'03' => 'March',
			'4' => 'April',
			'04' => 'April',
			'5' => 'May',
			'05' => 'May',
			'6' => 'June',
			'06' => 'June',
			'7' => 'July',
			'07' => 'July',
			'8' => 'August',
			'08' => 'August',
			'9' => 'September',
			'09' => 'September',
			'10' => 'October',
			'11' => 'November',
			'12' => 'December',
		),
		'ru' => array(
			'1' => 'января',
			'01' => 'января',
			'2' => 'февраля',
			'02' => 'февраля',
			'3' => 'марта',
			'03' => 'марта',
			'4' => 'апреля',
			'04' => 'апреля',
			'5' => 'мая',
			'05' => 'мая',
			'6' => 'июня',
			'06' => 'июня',
			'7' => 'июля',
			'07' => 'июля',
			'8' => 'августа',
			'08' => 'августа',
			'9' => 'сентября',
			'09' => 'сентября',
			'10' => 'октября',
			'11' => 'ноября',
			'12' => 'декабря',
		),
		'es' => array(
			'1' => 'Enero',
			'01' => 'Enero',
			'2' => 'Febrero',
			'02' => 'Febrero',
			'3' => 'Marzo',
			'03' => 'Marzo',
			'4' => 'Abril',
			'04' => 'Abril',
			'5' => 'Mayo',
			'05' => 'Mayo',
			'6' => 'Junio',
			'06' => 'Junio',
			'7' => 'Julio',
			'07' => 'Julio',
			'8' => 'Agosto',
			'08' => 'Agosto',
			'9' => 'Septiembre',
			'09' => 'Septiembre',
			'10' => 'Octubre',
			'11' => 'Noviembre',
			'12' => 'Deciembre',
		),
		'fr' => array(
			'1' => 'Janvier',
			'01' => 'Janvier',
			'2' => 'Fevrier',
			'02' => 'Fevrier',
			'3' => 'Mars',
			'03' => 'Mars',
			'4' => 'Avril',
			'04' => 'Avril',
			'5' => 'Mai',
			'05' => 'Mai',
			'6' => 'Juin',
			'06' => 'Juin',
			'7' => 'Juillet',
			'07' => 'Juillet',
			'8' => 'Aout',
			'08' => 'Aout',
			'9' => 'Septembre',
			'09' => 'Septembre',
			'10' => 'Octobre',
			'11' => 'Novembre',
			'12' => 'Decembre',
		),
		'de' => array(
			'1' => 'Januar',
			'01' => 'Januar',
			'2' => 'Februar',
			'02' => 'Februar',
			'3' => 'März',
			'03' => 'März',
			'4' => 'April',
			'04' => 'April',
			'5' => 'Mai',
			'05' => 'Mai',
			'6' => 'Juni',
			'06' => 'Juni',
			'7' => 'Juli',
			'07' => 'Juli',
			'8' => 'August',
			'08' => 'August',
			'9' => 'September',
			'09' => 'September',
			'10' => 'Oktober',
			'11' => 'November',
			'12' => 'Dezember',
		),
		'it' => array(
			'1' => 'Gennaio',
			'01' => 'Gennaio',
			'2' => 'Febbraio',
			'02' => 'Febbraio',
			'3' => 'Marzo',
			'03' => 'Marzo',
			'4' => 'Aprile',
			'04' => 'Aprile',
			'5' => 'Maggio',
			'05' => 'Maggio',
			'6' => 'Giugno',
			'06' => 'Giugno',
			'7' => 'Luglio',
			'07' => 'Luglio',
			'8' => 'Agosto',
			'08' => 'Agosto',
			'9' => 'Settembre',
			'09' => 'Settembre',
			'10' => 'Ottobre',
			'11' => 'Novembre',
			'12' => 'Dicembre',
		),
	);
	
	if(isset($array[$language])){
		return $array[$language];
	}
	return $array['en'];
}
?>