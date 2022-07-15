<?php
include_once 'connect.php';
$country_id = @intval($_GET['country_id']);
//$country_id = 1;

$regs = isset($ar[$country_id]['regions']) ? $ar[$country_id]['regions'] : false;
if(!$regs){ return false; }

if(count($regs) > 0){
	foreach($regs as $k => $v){
		if(isset($v['name'])){
			$regions[] = array('name' => $v['name'], 'region_id' => $k );			
			//$regions[$k] = $v['name'];			
		}
	}
	usort($regions, 'mysort');
	$result = array('regions'=>$regions); 
}else{	
	$result = array('type'=>'error');
}

function mysort($b,$c) { return strcmp($b['name'], $c['name']); }


//echo "<pre>";
//print_r ($result);
//echo "</pre>";
//exit;

print json_encode($result); 
?>