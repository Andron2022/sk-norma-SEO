<?php
include_once 'connect.php';
$region_id = @intval($_GET['region_id']);
$country_id = isset($connects[$region_id]) ? $connects[$region_id] : false;
$region_id = 2;
$country_id = 1;

$regs = isset($ar[$country_id]['regions'][$region_id]['cities']) ? $ar[$country_id]['regions'][$region_id]['cities'] : false;
if(!$regs){ return false; }

$regions = array(); 

if(count($regs) > 0){
	foreach($regs as $k => $v){
		if(isset($v['name'])){
			$regions[] = array('name' => $v['name'], 'city_id' => $k );			
			//$regions[$k] = $v['name'];			
		}
	}
	usort($regions, 'mysort');
	$result = array('citys'=>$regions); 	
}else{	
	$result = array('type'=>'error');
}

function mysort($b,$c) { return strcmp($b['name'], $c['name']); }



echo "<pre>";
print_r ($result);
echo "</pre>";
exit;

print json_encode($result); 

?>