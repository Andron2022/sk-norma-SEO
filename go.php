<?php
	/* 
		modified 8.06.2020 
		just links from same host can redirect
	*/
	
	$from = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	$from = str_replace('https://','',$from);
	$from = str_replace('http://','',$from);
	$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
	
	if(empty($host) || empty($from)){
		die("mission impossible");
	}
	
	$from = substr($from, 0, strlen($host));
	if($from != $host){
		die("mission impossible");
	}

	$url = isset($_GET['u']) ? $_GET['u'] : '';
	$url = trim($url);
  
	if(empty($url)){
		echo 'mission impossible';
	}else{
		require('variable.php');
		$ip = isset($_SERVER['REMOTE_ADDR']) ? trim($_SERVER['REMOTE_ADDR']) : '';
		$ref_out = isset($_SERVER["HTTP_REFERER"]) ? trim($_SERVER["HTTP_REFERER"]) : ''; 
		$sql = "INSERT INTO `".$db->tables['go_out']."` 
			(`out_url`, `ip`, `time`, `ref_page`) 
			VALUES('".$db->escape($url)."', '".$db->escape($ip)."', 
			'".date('Y-m-d H:i:s')."', '".$db->escape($ref_out)."') ";
		$db->query($sql);
		header('Location: '.urldecode($url));
		exit;
	}

?>