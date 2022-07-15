<?php
/**
 * template_lite replace modifier plugin
 *
 * Type:     modifier
 * Name:     replace
 * Purpose:  Wrapper for the PHP 'str_replace' function
 * Modified:   by dmvlad 13.02.2015
 * $search can be mix - string or array
 * in template delimiter is ,  
 */
function tpl_modifier_replace($string, $search, $replace="")
{
  $search = explode(",",$search);
	return str_replace($search, $replace, $string);
}
?>