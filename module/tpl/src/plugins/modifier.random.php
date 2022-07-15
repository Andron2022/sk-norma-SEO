<?php
/**
 * Simpla! random modifier plugin
 *
 * Type:     modifier
 * Name:     random
 * Purpose:  returns random rows from array $data
 *  
 */
function tpl_modifier_random($data, $number=1)
{
	$number = count($data) < $number ? count($data) : $number;
	$pre_array = array_rand($data, $number);
	if(is_array($pre_array)){
		$array = array();
		foreach($pre_array as $v){
			$array[] = $data[$v]['url'];		
		}			
	}else{
		$array = $data[$pre_array]['url'];
	}
	return $array;
}
?>