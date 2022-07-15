<?php
/**
 * SIMPLA! lastkey modifier plugin
 *
 * Type:     modifier
 * Name:     lastkey
 * Purpose:  send last key for array
 */
function tpl_modifier_lastkey($array)
{
	if(!is_array($array)){ return; }
	$array = array_keys($array);
	$q = count($array)-1;
	return $q;
}
?>