<?php
/**
 * Simpla! print modifier plugin
 *
 * Type:     modifier
 * Name:     print
 * Purpose:  formatted print_r $data in template
 *  
 * Input:<br>
 *         - string: data to print
 */
function tpl_modifier_print($data)
{
	echo "<pre>".print_r($data, true)."</pre>";
	return;
}
?>