<?php 
/** 
 * Simpla! array_shift plugin 
 * 
 * Type:     function 
 * Name:     array_shift 
 * Purpose:  Delete first element of array
 */ 
function tpl_function_array_shift($params, &$tpl)
{
	extract($params);
	
	if(isset($key)){
		unset($array[$key]);
	}elseif(is_array($array) && !empty($array))
	{
		array_shift($array);
	}
	
	if (!empty($params['assign']))
	{
		$tpl->assign($params['assign'],$array);
	}
}
?>