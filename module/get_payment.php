<?php
/*
last update 21.11.2019

/order/pay/success/
/order/pay/fail/
/order/pay/cancel/

*/

define('SI_CORE', true);
require_once('../variable.php');
if(file_exists(MODULE."/class.site.php")){ 
	require(MODULE."/class.site.php"); 
	$site = new Site();
}

if (!class_exists('Site')) { return; }
//if(empty($site->vars['site_scheme']) || $site->vars['site_scheme'] != 'https'){ echo '00000'; return; }

/*
$source = file_get_contents('php://input ');
$requestBody = json_decode($source, true);
if(isset($requestBody['notification']) && $requestBody['notification'] != '') {
include_once(MODULE."/get _payments/ya_pay.php");
}
*/

include_once(MODULE."/get_payments/ya_pay.php");


?>