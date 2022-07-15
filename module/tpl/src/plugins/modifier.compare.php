<?php
/**
 * Simpla! compare modifier plugin
 *
 * Type:     modifier
 * Name:     compare
 * Purpose:  Compare a string with var,
 *           returns first value $a if equal 
 *           or second value $b if not.
 */
function tpl_modifier_compare($string, $var = '', $a = '', $b = '')
{
	if (empty($a))
	{
		return $string;
	}

  if($string == $var)
  {
    return $a;
  }else{
    return $b;
  }
}

?>