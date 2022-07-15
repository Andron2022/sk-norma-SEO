<?php
/**
 * SIMPLA! numformat modifier plugin 
 *
 * Type:     modifier
 * Name:     numformat
 * Purpose:  Wrapper for the PHP 'numberformat' function
 * decimals - Sets the number of decimal points.
 * dec_point - Sets the separator for the decimal point.
 * thousands_sep - Sets the thousands separator.
 */

function tpl_modifier_numformat($string, $decimals='0', $dec_point='.', $thousands_sep=' ')
{
	$string = trim($string);
    $string = str_replace('&nbsp;','',$string);
    $string = str_replace(' ','',$string);
	if(empty($string)){ return $string; }
	if(substr_count($string, '.') > 1){ return $string; }
	if (!is_numeric($string)) { return $string; }
    return number_format($string, $decimals, $dec_point, $thousands_sep);
}
?>