<?php
/**
 * Simpla array modifier plugin
 *
 * Type:     modifier
 * Name:     array
 * Purpose:  Wrapper for the PHP 'explode' function
 */
function tpl_modifier_array($string, $delimiter='')
{
    if(empty($delimiter) || empty($string)){ return $string; }
	return explode($delimiter, $string);
}
?>