<?php
/**
 * Template_Lite filesize modifier plugin
 *
 * Type:     modifier
 * Name:     filesize
 * Purpose:  converts $data in well view
 * for example, 12000 -> 12Kb
 *  
 * Input:<br>
 *         - string: data to convert
 *		   - $round and $lang added 14.10.2016
 */
function tpl_modifier_filesize($data, $round=2, $lang='en')
{
	$size = intval($data);
	$ar = array(
		'en' => array(
			'bytes' => 'bytes',
			'kb' => 'Kb',
			'mb' => 'Mb',
			'gb' => 'Gb',
			'tb' => 'Tb',
			'pb' => 'Pb'
		),
		'ru' => array(
			'bytes' => 'байт',
			'kb' => 'Кб',
			'mb' => 'Мб',
			'gb' => 'Гб',
			'tb' => 'Тб',
			'pb' => 'Пб'
		)
	);
	$ar = $lang == 'ru' ? $ar['ru'] : $ar['en'];

  if($size > 0 && $size<=1024) $data = $size.' '.$ar['bytes'];
  else if($size == 0) $data = '0';
  else if($size<=1024*1024) $data = round($size/(1024),$round).' '.$ar['kb'];
  else if($size<=1024*1024*1024) $data = round($size/(1024*1024),$round).' '.$ar['mb'];
  else if($size<=1024*1024*1024*1024) $data = round($size/(1024*1024*1024),$round).' '.$ar['gb'];
  else if($size<=1024*1024*1024*1024*1024) $data = round($size/(1024*1024*1024*1024),$round).' '.$ar['tb'];
  else $data = round($size/(1024*1024*1024*1024*1024),$round).' '.$ar['Pb']; // ;-) 
	return $data;
}
?>