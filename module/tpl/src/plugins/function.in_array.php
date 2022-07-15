<?php 
/** 
 * template_lite in_array plugin 
 * 
 * Type:     function 
 * Name:     in_array 
 * Purpose:  Checks to see if there is an item in the array that matches and returns the returnvalue if true. 
 */ 
function tpl_function_in_array($params, &$tpl)
{
	extract($params);
	if(!isset($returnvalue)){ $returnvalue = ''; }

	if (is_array($array))
	{
		if (in_array($match, $array))
		{
			if (empty($params['assign']))
			{
				return $returnvalue;
			}			
		}else{
			$returnvalue = '';
		}
		
	}else{
		$returnvalue = '';
	}
	
	if (!empty($params['assign']))
	{
		$tpl->assign($params['assign'],$returnvalue);
	}
}
?>