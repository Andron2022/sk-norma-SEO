<?php 
/** 
 * Simpla! array_pop plugin 
 * 
 * Type:     function 
 * Name:     array_pop 
 * Purpose:  Returns a last element in the array 
 */ 
function tpl_function_array_pop($params, &$tpl)
{
	extract($params);
	
	if (is_array($array) && !empty($array))
	{
		$returnvalue = array_pop($array);
	}else{
		$returnvalue = '';
	}
	
	if (!empty($params['assign']))
	{
		$tpl->assign($params['assign'],$returnvalue);
	}
}
?>