<?php
/**
 * Simpla! red modifier plugin
 *
 * Type:     modifier
 * Name:     red
 * Purpose:  converts $data in red color
 *  
 * Input:<br>
 *         - string: data to convert
 */
function tpl_modifier_red($data)
{
	return "<span style='color:red;'>".$data."</span>";
}
?>