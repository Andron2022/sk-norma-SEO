<?php
/*
 * template_lite plugin
 *
 * Type:     function
 * Name:     phpinfo
 * Version:  1.0
 * Date:     Nov 12, 2016
 * Author:   Simpla <info@simpla.es>
 * Examples: {phpinfo} or {phpinfo INFO_GENERAL="1"}
 */
function tpl_function_phpinfo($params=NULL, &$tpl)
{
	if(isset($params['INFO_GENERAL'])) { return phpinfo(INFO_GENERAL); }
	if(isset($params['INFO_CONFIGURATION'])) { return phpinfo(INFO_CONFIGURATION); }
	if(isset($params['INFO_MODULES'])) { return phpinfo(INFO_MODULES); }
	if(isset($params['INFO_ENVIRONMENT'])) { return phpinfo(INFO_ENVIRONMENT); }
	if(isset($params['INFO_VARIABLES'])) { return phpinfo(INFO_VARIABLES); }
	return phpinfo();
}
?>