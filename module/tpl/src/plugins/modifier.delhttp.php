<?php
/**
 * Simpla plugin to delete http:// from URL
 */


/**
 * Type:     modifier<br>
 * Name:     delhttp<br>
 * Purpose:  delete http: //www AND https: //www.
 * @param string if 1 then deletes without www
 * @return string
**/
 
function tpl_modifier_delhttp($string, $param = '0')
{
	if(empty($param)){
		$string = str_replace('https://www.', '', $string);
		$string = str_replace('http://www.', '', $string);
	}

	$string = str_replace('https://', '', $string);	
	$string = str_replace('http://', '', $string);
    return $string;
}

?>
