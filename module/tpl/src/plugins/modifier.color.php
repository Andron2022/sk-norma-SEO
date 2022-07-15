<?php
/**
 * Simpla! color modifier plugin
 *
 * Type:     modifier
 * Name:     color
 * Purpose:  converts $data in color
 *  
 * Input:<br>
 *         - string: data to convert
 */
function tpl_modifier_color($str, $color="")
{
  if(empty($color)){ return $str; }
  $color = "#".$color;
  $color = str_replace("##", "#", $color);  
	return "<span style='color:".$color.";'>".$str."</span>";
}
?>