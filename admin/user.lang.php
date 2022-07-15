<?php
	#############################################
	#############################################
	##
	##	ver. 16.04.2018
	##	test языковых файлов
	##	http://simpla.es/admin/user.lang.php
	##	или
	##	http://simpla.es/admin/user.lang.php?admin=1
	##	
	#############################################
	#############################################
?>
<head>
	<meta charset="utf-8">
	<title>Translate words for Simpla.es</title>
	
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta content="" name="description">
	<meta content="simpla.es" name="author">
	<link rel="shortcut icon" href="/tpl/cb/favicon.ico">
	
	<!-- Fonts START -->
	<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|PT+Sans+Narrow|Source+Sans+Pro:200,300,400,600,700,900&amp;subset=all" rel="stylesheet" type="text/css">
	<!-- Fonts END -->

	<link href="css/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link rel="icon" href="tpl/default/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="tpl/default/favicon.ico" type="image/x-icon">
	<link href="tpl/default/css/style.css" rel="stylesheet">
</head>
<!-- Head END -->

<!-- Body BEGIN -->
<body class="">

<?php
	require_once('../variable.php');
	$set_lang = array();
	$folder = MODULE.'/lang';
	if(!empty($_GET['admin'])){
		$admin = 1;
		$folder .= '/admin';
	}
	
	echo '<h3 style="text-align: center;">Translate words for Simpla.es</h3>';
	echo '<p style="text-align: center;">';
	if(empty($admin)){
		echo '<b>site</b> | ';
		echo '<a href="user.lang.php?admin=1">admin</a>';
	}else{
		echo '<a href="user.lang.php">site</a> | ';
		echo '<b>admin</b>';
	}
	echo '</p>';
	
	$dir = new DirectoryIterator($folder);
	foreach ($dir as $fileinfo)
	{   
		if(!$fileinfo->isDot() && 
			!is_dir($folder.'/'.$fileinfo->getFilename())
		){
			$f = explode('.', $fileinfo->getFilename());
			include_once($folder.'/'.$fileinfo->getFilename());
			$set_lang[$f[0]] = isset($lang) ? $lang : array();
			unset($lang);
		}
	}
	
	$lang = $set_lang;	
	get_lang_table($lang);
	
	function get_lang_table($lang){
		// кол-во языков и массив с ними
		$langs = array_keys($lang);			
		echo '<table border=1 cellpadding=4 cellspacing=0 class="table bordered">';
		echo '<tr>';
		echo '<th width="20%">key</th>';
		
		foreach($lang as $k => $v){
			$qty = count($v,COUNT_RECURSIVE);
			echo '<th width="'.ceil(80/count($langs)).'%">';
			echo $k;
			echo '<br><small>('.$qty.')</small>';
			echo '</th>';

			$max = !isset($max) ? $k :
				count($lang[$max]) < count($lang[$k],COUNT_RECURSIVE) 
					? $k : $max;			
		}
		echo '</tr>';
		/* скорректируем массивы 
			пройдемся по меньшим и добавим, 
			если таких элементов нет в большом
		*/
		foreach($langs as $t1){
			if($t1 != $max){
				foreach($lang[$t1] as $k2 => $v2){
					if(!isset($lang[$max][$k2])){
						$lang[$max][$k2] = '';
					}
				}
			}
		}
		
		$ar = $lang[$max];
		//ksort($ar);
		//$array = array_diff($ar, array(''));
		$array = $ar;
		$i = 0;
		
		foreach($array as $k => $v){
			if(empty($k)){ continue; }
			
			if(is_array($v)){
				$i++;
				echo get_row($lang, $i, $k);
				foreach($v as $v11 => $v12){
					if(is_array($v12)){
						$i++;
						echo get_row($lang, $i, $k, $v11);
						foreach($v12 as $v21 => $v22){
							$i++;
							echo get_row($lang, $i, $k, $v11, $v21);
						}
					}else{
						$i++;
						echo get_row($lang, $i, $k, $v11);
					}
				}
			}else{
				$i++;
				echo get_row($lang, $i, $k);
			}

	/*
	echo count($arr['string'],COUNT_RECURSIVE);//0

	if(($i % 2) == 0){
        // если четное, то делаем отступ 
        echo "<span style='padding-left: 20px;'>$value</span><br/>";
    }else{
        // если не четное, то просто выводим
        echo "<span>$value</span><br/>";
    }
	*/
	
	
	
	
			
		}
		
		
		
		
		
		echo '</table>';
		
		
	}
	
	function get_row($lang, $i, $k1, $k2=NULL, $k3=NULL){
		
		$langs = array_keys($lang);
		if(($i % 2) == 0){
			echo '<tr class="odd">';
		}else{
			echo '<tr>';
		}
		
		echo '<td>';
		if(!empty($k3)){
			echo '.. .. '.$k3;
		}

		if(!empty($k2) && empty($k3)){
			echo '.. '.$k2;
		}

		if(!empty($k1) && empty($k2)){
			echo $k1;
		}
		echo '</td>';
		/*
		echo '<td>'.$k1;
		if(!empty($k2)){ echo '|'.$k2; }
		if(!empty($k3)){ echo '|'.$k2; }
		echo '</td>';
		*/
		foreach($langs as $lng){

				if(!empty($k1) && !empty($k2) && !empty($k3)){
					$str = !empty($lang[$lng][$k1][$k2][$k3]) 
						? $lang[$lng][$k1][$k2][$k3]
						: '?';
				}elseif(!empty($k1) && !empty($k2)){
					$str = !empty($lang[$lng][$k1][$k2]) 
						? $lang[$lng][$k1][$k2]
						: '?';
				}else{
					$str = !empty($lang[$lng][$k1]) 
						? $lang[$lng][$k1]
						: '?';
				}
				
				if($str == '?' || empty($str)){
					//echo '<td bgcolor="#D3EDF6" align="center">?</td>';
					echo '<td align="center"><span style="font-weight:bold; color:#cfcfcf;">?</span></td>';
				}elseif(is_array($str)){
					echo '<td align="center"><span style="font-weight:bold; color:#e60000;">array</span></td>';
				}else{
					echo '<td>'.$str.'</td>';
				}
		}
		echo '</tr>';
		return;
	}
	
	
?>

</body>
<!-- END BODY -->
</html>