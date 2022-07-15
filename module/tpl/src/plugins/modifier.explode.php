<?php
/**
 * Simpla explode modifier plugin
 *
 * Type:     modifier
 * Name:     explode
 * Purpose:  Wrapper for the PHP 'explode' function
 */
function tpl_modifier_explode($string, $delimiter='')
{
    if(empty($delimiter) || empty($string)){ return $string; }
	return explode($delimiter, $string);
}
?>