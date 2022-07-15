<?php
/**
 * Simpla! chunk modifier plugin
 *
 * Type:     modifier
 * Name:     chunk
 * Purpose:  format 12345678 as 123 456 78
 *  
 * Input:<br>
 *         - string: data to convert
 */
function tpl_modifier_chunk($str, $length="3", $delimiter=" ")
{
	if(empty($str)){ return $str; }
	$length = intval($length);
	if($length < 1){ return $str; }
	return substr(chunk_split($str, $length, $delimiter), 0, -1); 
}
?>