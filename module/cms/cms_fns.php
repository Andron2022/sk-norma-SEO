<?php

    /***********************
    *
    * functions for cms 
    * created 24.09.2016
    *
    ***********************/ 
    
	function cms_upload_file($site, $record_type, $record_id, $file, $changes, $if_replace=false){
		global $db;
		
		if(!empty($file['size']) && !empty($file['tmp_name']) && !empty($file['name'])){
			if(!is_array($file['name'])){
				$nfile['name'] = array();
				$nfile['size'] = array();
				$nfile['tmp_name'] = array();
				$nfile['name'][] = $file['name'];
				$nfile['size'][] = $file['size'];
				$nfile['tmp_name'][] = $file['tmp_name'];
				$file = $nfile;
			}
			
			foreach($file['name'] as $k => $v){
				$f_name = $v;
				$f_tmp_name = $file['tmp_name'][$k];
				$f_size = $file['size'][$k];
				if(empty($f_name) || empty($f_tmp_name) || empty($f_size)){ continue; }
				
				$value = explode('.', $v);
				$ext = strtolower(array_pop($value));
				
				$allow_download = !empty($changes['allow_download']) ? 1 : 0;
				$direct_link = !empty($changes['direct_link']) ? 1 : 0;
				
				/* удалим, если надо */
				if(!empty($if_replace)){
					$changes_del = $changes;
					$changes_del['type'] = 'delete_file';						
					cms_delete_file($site, $record_type, $record_id, $changes_del);
				}
				
				$id_in_record = $db->get_var("SELECT id_in_record 
						FROM ".$db->tables["uploaded_files"]." 
						WHERE `record_id` = '".$record_id."' AND `record_type` = '".$record_type."' 
						ORDER BY id_in_record desc ");
				$id_in_record = !empty($id_in_record) && $id_in_record > 0 
						? $id_in_record+1 : 1;
						
				/* Сохраняем */
				$db->query("INSERT INTO ".$db->tables["uploaded_files"]." 
						(`id_exists`, `record_id`, `record_type`, `size`, 
						`filename`, `title`, `ext`, `allow_download`, 
						`direct_link`, id_in_record, time_added) 
						VALUES('0', '".$record_id."', 
						'".$record_type."', '".$f_size."', 
						'".$db->escape($f_name)."', 
						'".$db->escape($f_name)."', 
						'".$ext."', '".$allow_download."', 
						'".$direct_link."', 
						'".$id_in_record."', '".time()."') ");
				if(!empty($db->last_error)){ return $site->db_error("File ".__FILE__."<br>Line ".__LINE__); }
				$file_id = $db->insert_id; 
				
				$uploaddir = UPLOAD.'/files/'.md5($file_id).'.'.$ext;
				//$uploaddir = realpath(UPLOAD.'/files/'.md5($file_id).'.'.$ext);
				//if(@!move_uploaded_file($file['tmp_name'],$uploaddir)){
				if(@!copy($f_tmp_name,$uploaddir)){
					$db->query("DELETE FROM ".$db->tables["uploaded_files"]." 
							WHERE id = '".$file_id."' 
					");
					die("File can't be written, wrong path in $uploaddir");
				}			
				
				$size = $site->well_size($f_size);
				$comment = $changes['type'] == $record_type ? $f_name.' ('.$size.')' : $changes['comment'].' ('.$size.')';
				$site->register_changes($changes['where'], $changes['id'], 'upload_file', $comment);		
			}	
			
		}
		
		return;
	}	
	
    
	
	function cms_delete_file($site, $record_type, $record_id, $changes){
		global $db;
		$rows = $db->get_results("SELECT * FROM ".$db->tables["uploaded_files"]." 
				WHERE record_id = '".$record_id."' AND record_type = '".$record_type."' 
		");
		if(!empty($db->last_error)){ return $site->db_error("File ".__FILE__."<br>Line ".__LINE__); }
		if($db->num_rows > 0){
			foreach($rows as $row){
				
				$path = realpath(UPLOAD.'/files/'.md5($row->id).'.'.$row->ext);
				if($path){
					if(@unlink($path)){
						$db->query("DELETE FROM ".$db->tables["uploaded_files"]." 
								WHERE id = '".$row->id."' 
						");
						$comment = $changes['where'] == $record_type ? $row->filename : $changes['comment'];
						$site->register_changes($changes['where'], $changes['id'], 'delete_file', $comment);				
					}
				}				
			}
		}
		return;		
	}
	
	



	function cms_to_csv($query, $table='file', $fields=''){
        global $db;
		$delimiter = ';';
		$enclosure = '"';
		$filename = $table.'-'.date('Y-m-d').'.csv';
		if(!empty($fields)){
			$str = '`'.implode('`,`', $fields).'`';
			$query = str_replace(' * ', $str, $query);
		}
        $rows = $db->get_results($query, ARRAY_A);
        if(!$rows || $db->num_rows == 0){ die('Database error: '.$db->last_error); }
		
		setlocale(LC_ALL, 'ru_RU.utf8');
		ini_set("auto_detect_line_endings", true);
		header( 'Content-Type: text/html;charset=UTF-8' );
		header( 'Content-Type: text/csv' );
		header( 'Content-Disposition: attachment;filename='.$filename);
		$fp = fopen('php://output', 'w');
		// add BOM for excel 
		fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
	
		$arr = array_keys($rows[0]);		
		fputcsv($fp, $arr, $delimiter, $enclosure);
		
		foreach($rows as $row){
			fputcsv($fp, $row, $delimiter, $enclosure);
		}
			
		fclose($fp);
		exit;		
    }
	
	
	function correct_sent_data($row, $table, $site){
		global $db;		

		// Проверим $site->required_fields - все ли есть
		// если нет, допишем!
		// ***** ЗДЕСЬ ОСТАНОВИЛСЯ
		$required_fields = isset($site->required_fields) 
			? $site->required_fields : array();
		if(!empty($required_fields)){
			foreach($required_fields as $k => $v){
				if(isset($row[$v])){
					unset($required_fields[$k]);
					echo '1 - '.$v.'<br>';
				}
			}
		}
		/*
		подумать где эти поля выводить
		возможно для колонок тоже надо
		*/

		
		
		echo $site->print_r($required_fields); exit;
		// даты обработаем
		$arr = array('ddate','date_insert','date_update');
		foreach($arr as $ar){
			if(isset($row[$ar]) && (empty($row[$ar]) || $row[$ar] == '0000-00-00 00:00:00')){
				$row[$ar] = date('Y-m-d H:i:s');
			}elseif(isset($row[$ar]) && !empty($row[$ar])){
				$row[$ar] = date('Y-m-d H:i:s', strtotime($row[$ar]));
			}else{ }
		}		
						
		// active, f_spec и подобрые обработаем, чтобы 0 был
		$arr = array('id_parent', 'active', 'sort', 
				'f_spec', 'f_new', 'in_last', 'shop', 
				'show_in_filter', 'record_id', 'notify', 
				'own', 'is_default', 'nds', 
				'accept_orders', 'total_qty', 
				'id_next_model', 'bid_ya', 
				'present_id', 'treba', 
		);
		foreach($arr as $ar){
			if(isset($row[$ar]) && empty($row[$ar])){
				$row[$ar] = 0;
			}elseif(isset($row[$ar]) && !empty($row[$ar])){
				$row[$ar] = 1;
			}else{ }
		}		
						
		// alias - if empty, set it
		if(isset($row['alias']) && empty($row['alias'])){
			$cnt = rand(1,9999);
			$row['alias'] = 'page-'.time().$cnt;
		}
						
		// multitpl
		if(isset($row['multitpl']) && empty($row['multitpl'])){
			$mtpl = '';
			if($table == $db->tables['categs']){
				$mtpl = 'category.html';
			}elseif($table == $db->tables['products']){
				$mtpl = 'product.html';
			}elseif($table == $db->tables['publications']){
				$mtpl = 'pub.html';
			}else{
				$mtpl = 'index.html';
			}
			$row['multitpl'] = $mtpl;
		}
						
		// price_buy, price, price_opt, price_spec, weight_deliver
		$arr = array('price_buy', 'price', 'price_opt', 'price_spec', 'weight_deliver');
		foreach($arr as $ar){
			if(isset($row[$ar]) && empty($row[$ar])){
				$row[$ar] = 0;
			}
		}
				
		// price_period - razovo
		if(isset($row['price_period']) && empty($row['price_period'])){
			$row['price_period'] = 'razovo';
		}
						
		// userid, user_id
		if(isset($row['userid']) && empty($row['userid'])){
			$row['userid'] = isset($site->user['id']) ? $site->user['id'] : 0;
		}

		if(isset($row['user_id']) && empty($row['user_id'])){
			$row['user_id'] = isset($site->user['id']) ? $site->user['id'] : 0;
		}		
		
		// currency
		if(isset($row['currency']) && empty($row['currency'])){
			$row['currency'] = isset($site->vars['sys_currency']) ? $site->user['sys_currency'] : 'rur';
			
		}
						
		// data_reg
		if(isset($row['data_reg'])){
			if(!empty($row['data_reg'])){
				$row['data_reg'] = date('Y-m-d', strtotime($row['data_reg']));
			}
		}						
						
		return $row;
	}
	
	
	
	function cms_from_csv($site, $ar, $table){
		global $db;
		
		// Узнаем все обязательные поля
		$rows = $db->get_row("SELECT * 
				FROM ".$table." 
				LIMIT 0, 1", ARRAY_A);
			
		// Соберем массив обязательных полей
		$fields = array();
		if(empty($db->last_error) && $db->num_rows > 0){
			foreach($rows as $k => $v){
				foreach ( $db->get_col_info($k)  as $name ){
					if ($name->flags & 1) {
						$fields[] = $k;
					}
				}
			}
		}
		$site->required_fields = $fields;
		
		ini_set("auto_detect_line_endings", true);
		$delimiter = ';';
		$enclosure = '"';
		$escape = '"';
		if(empty($ar['changes'])){ $ar['changes'] = $table; }
		if(empty($ar['redirect'])){ $ar['redirect'] = $site->uri['requested_full']; }
		
		$enclist = array( 
			'UCS-4', 'UCS-4BE', 'UCS-4LE', 'UCS-2',
			'UCS-2BE', 'UCS-2LE', 'UTF-32', 
			'UTF-32BE', 'UTF-32LE', 'UTF-16', 
			'UTF-16BE', 'UTF-16LE', 'UTF-7', 
			'UTF-8', 'ASCII', 'ISO-8859-1', 
			'Windows-1251', 'CP1251', 
			'CP866', 'IBM866', 'KOI8-R', 
			'Windows-1252', 'CP1252' 
		);
		
		if(!empty($_POST["import_file"])){
			$f = $_POST["import_file"]; 
			$in_db = 1;
		}else{
			if(empty($_FILES["csv"]["tmp_name"]) || 
				empty($_FILES["csv"]["size"])
			){ return; }

			$f_tmp_name = $_FILES["csv"]["tmp_name"];
			$pre_file = time().rand(11,9999);
			$f = UPLOAD.'/files/'.md5($pre_file).'.csv';
			if(@!copy($f_tmp_name,$f)){
				die("File can't be written, wrong path in ".$f);
			}			
		}

		$text = file_get_contents($f);
		$delimiter = cms_getSplitChar($text);
		@$cp = mb_detect_encoding($text, $enclist, true);
		if(!$cp){ $cp = get_codepage($text);	}
		if($cp == 'CP1251'){ $cp = 'windows-1251'; }
		if($cp == 'IBM866'){ $cp = 'UCS-2BE'; }
		if($cp == 'MAC'){ $cp = 'MAC'; } // x-mac-cyrillic
		if(empty($cp)){ $cp = 'UTF-8'; }
		
		$row = 1;
		$out = array();
		if (($handle = fopen($f, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
				$num = count($data);
				// first row = TH
				if($row == 1){					
					$th = array();
					for ($c=0; $c < $num; $c++) {
						if(!empty($data[$c])){
							$bom = pack('H*','EFBBBF');
							$rth = preg_replace("/^$bom/", '', $data[$c]);
							$th[$c] = strval(trim($rth));
						}						
					}
					
					// check table for obligatory columns
					
					$out[] = $th;					
				}else{
					$arr = array();
					$empty = 0;
					for ($c=0; $c < $num; $c++) {
						$var = $data[$c];
						
						$var = $cp == 'MAC' 
							? iconv('MacCyrillic', 'UTF-8', $var)
							: mb_convert_encoding($var, 'UTF-8', $cp);
						if(isset($th[$c])){							
							$arr[$th[$c]] = $var;
						}
						if(empty($var)){ $empty++; }
					}
					
					if(count($th) != count($arr)){ die('Wrong data'); }					
					$arr = correct_sent_data($arr, $table, $site);
					if($empty < $num){
						$out[] = $arr;
					}
				}
				$row++;
			}
			fclose($handle);
		} 
		
		// delete bom if exists
		$rth = '';
		/*
		if(isset($out[0][0])){
			$bom = pack('H*','EFBBBF');
			$rth = preg_replace("/^$bom/", '', $out[0]);
			unset($rth[0]); 			
		}
		*/
		
		
		foreach ($out as $k => $row) {
			$rth = '';
			if($k > 0 && !empty($in_db)){				
				$id = isset($row['id']) ? intval($row['id']) : 0;
				unset($row['id']);		

				// если есть option-NNN - запишем отдельно
				
				// если есть cid в GET переменной и тип
				// данных товар или публикация - привяжем
				
				// ********** HERE WE ARE 


				
				echo $id; exit;
				if($id > 0){
					// update if exists
					$qty = $db->get_var("SELECT COUNT(*) 
								FROM ".$table." WHERE id = '".$id."' ");
						
					if($qty == 1){
						$i = 0;
						$sql = " UPDATE ".$table." SET ";
						foreach($row as $kv => $v){
							if($i > 0){ $sql .= ", "; }
							$sql .= " `".$kv."` = '".$db->escape(trim($v))."' ";
							$i++;
						}							
						$sql .= " WHERE id = '".$id."' ";
						$db->query($sql);
						$site->register_changes($ar['changes'], $id, 'update', 'CSV');
					}else{
						$sql = "INSERT INTO ".$table." (`id`, `".implode("`,`",$rth)."`)
								VALUES('".$id."', ";
						$i = 0;
						foreach($row as $v){
							if($i > 0){ $sql .= ", "; }
							$v = trim($v);
							$sql .= " '".$db->escape($v)."' ";
							$i++;
						}
						$sql .= "
								)
						";
						$db->query($sql);
						$site->register_changes($ar['changes'], $id, 'add', 'CSV'); 
					}
					
				}else{
					// add
					$sql = "INSERT INTO ".$table." (`".implode("`,`",$rth)."`)
							VALUES(";
					$i = 0;
					foreach($row as $v){
						if($i > 0){ $sql .= ", "; }
						$v = trim($v);
						$sql .= " '".$db->escape($v)."' ";
						$i++;							
					}
					$sql .= "
						)
					";
					$db->query($sql);
					$site->register_changes($ar['changes'], $db->insert_id, 'add', 'CSV');
				}
			}
		}
		
		/* откроем все SQL, сделаем редирект  и проверим работу */
		if(!empty($in_db)){
			echo 'ok';
		exit;
			@unlink($f);
			$qty = count($out)-1;
			$url = $ar['redirect'].'&added='.$qty;
			return $site->redirect($url);			
		}else{
			@unlink($f_tmp_name);
		}
		$ar['results'] = $out;
		$ar['import_file'] = $f;
		return $ar;
	}
	
	
	
	function cms_from_csv_OLD($site, $ar, $table){
		global $db;
		ini_set("auto_detect_line_endings", true);
		$delimiter = ';';
		$enclosure = '"';
		$escape = '"';
		if(empty($ar['changes'])){ $ar['changes'] = $table; }
		if(empty($ar['redirect'])){ $ar['redirect'] = $site->uri['requested_full']; }
		
		$enclist = array( 
			'UCS-4', 'UCS-4BE', 'UCS-4LE', 'UCS-2',
			'UCS-2BE', 'UCS-2LE', 'UTF-32', 'CP866', 
			'UTF-32BE', 'UTF-32LE', 'UTF-16', 'IBM866', 
			'UTF-16BE', 'UTF-16LE', 'UTF-7', 'KOI8-R', 
			'UTF-8', 'ASCII', 'ISO-8859-1', 'CP1252', 
			'Windows-1251', 'CP1251', 'Windows-1252' 
		);
		
		if(!empty($_POST["import_file"])){
			$f = $_POST["import_file"]; 
			$in_db = 1;
		}else{
			if(empty($_FILES["csv"]["tmp_name"]) || 
				empty($_FILES["csv"]["size"])
			){ return; }

			$f_tmp_name = $_FILES["csv"]["tmp_name"];
			$pre_file = time().rand(11,9999);
			$f = UPLOAD.'/files/'.md5($pre_file).'.csv';
			if(@!copy($f_tmp_name,$f)){
				die("File can't be written, wrong path in ".$f);
			}			
		}

		$text = file_get_contents($f);
		@$cp = mb_detect_encoding($text, $enclist, true);
		if(!$cp){ $cp = get_codepage($text);	}
		if($cp == 'CP1251'){ $cp = 'windows-1251'; }
		if($cp == 'IBM866'){ $cp = 'UCS-2BE'; }
		if(empty($cp)){ $cp = 'UTF-8'; }
		//$line = mb_convert_encoding($line, 'UTF-8', $cp);
		
		$row = 1;
		$out = array();
		if (($handle = fopen($f, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 0, ';')) !== FALSE) {
				$num = count($data);
				// first row = TH
				if($row == 1){
					$th = array();
					for ($c=0; $c < $num; $c++) {
						$th[$c] = $data[$c];
					}
					$out[] = $th;
				}else{
					if(count($th) != count($data)){ die('Wrong data'); }					
					$arr = array();
					for ($c=0; $c < $num; $c++) {
						$var = mb_convert_encoding($data[$c], 'UTF-8', $cp);
						$arr[$th[$c]] = $var;
					}
					$arr = correct_sent_data($arr, $table, $site);
					$out[] = $arr;
				}
				$row++;
			}
			fclose($handle);
		} 
		
		// delete bom if exists
		$rth = '';
		if(isset($out[0][0])){
			$bom = pack('H*','EFBBBF');
			$rth = preg_replace("/^$bom/", '', $out[0]);
			unset($rth[0]); 			
		}
		
		foreach ($out as $k => $row) {
			$rth = '';
			if($k > 0 && !empty($in_db)){
				$id = isset($row['id']) ? intval($row['id']) : 0;
				unset($row['id']);				
				
				if($id > 0){
					// update if exists
					$qty = $db->get_var("SELECT COUNT(*) 
								FROM ".$table." WHERE id = '".$id."' ");
						
					if($qty == 1){
						$i = 0;
						$sql = "UPDATE ".$table." SET ";
						foreach($row as $kv => $v){
							if($i > 0){ $sql .= ", "; }
							$sql .= " `".$kv."` = '".$db->escape(trim($v))."' ";
							$i++;
						}							
						$sql .= " WHERE id = '".$id."' ";
						$db->query($sql);
						$site->register_changes($ar['changes'], $id, 'update', 'CSV');
					}else{
						$sql = "INSERT INTO ".$table." (`id`, `".implode("`,`",$rth)."`)
								VALUES('".$id."', ";
						$i = 0;
						foreach($row as $v){
							if($i > 0){ $sql .= ", "; }
							$v = trim($v);
							$sql .= " '".$db->escape($v)."' ";
							$i++;
						}
						$sql .= "
								)
						";
						$db->query($sql);
						$site->register_changes($ar['changes'], $id, 'add', 'CSV'); 
					}
					
				}else{
					// add
					$sql = "INSERT INTO ".$table." (`".implode("`,`",$rth)."`)
							VALUES(";
					$i = 0;
					foreach($row as $v){
						if($i > 0){ $sql .= ", "; }
						$v = trim($v);
						$sql .= " '".$db->escape($v)."' ";
						$i++;							
					}
					$sql .= "
						)
					";
					$db->query($sql);
					$site->register_changes($ar['changes'], $db->insert_id, 'add', 'CSV');
				}
			}
		}
		
		/* откроем все SQL, сделаем редирект  и проверим работу */
		if(!empty($in_db)){
			@unlink($f);
			$qty = count($out)-1;
			$url = $ar['redirect'].'&added='.$qty;
			return $site->redirect($url);			
		}else{
			@unlink($f_tmp_name);
		}
		$ar['results'] = $out;
		$ar['import_file'] = $f;
		return $ar;
	}

	
	function cms_getSplitChar($text) {
		$s = preg_replace('/".+"/isU', '*', $text); 
		$a = array(',',';','|','\t'); //список разделителей
		$r;
		$i = -1;
		foreach($a as $c) {
			if(($n = sizeof(explode($c, $s))) > $i) {
				$i = $n;
				$r = $c;
			}
		}
		return $r;
	}	
	
	function get_codepage($text = '') {
		if (!empty($text)) {
			$utflower  = 7;
			$utfupper  = 5;
			$lowercase = 3;
			$uppercase = 1;
			$last_simb = 0;
			$charsets = array(
				'UTF-8'       => 0,
				'CP1251'      => 0,
				'KOI8-R'      => 0,
				'IBM866'      => 0,
				'ISO-8859-5'  => 0,
				'MAC'         => 0,
			);
			for ($a = 0; $a < strlen($text); $a++) {
				$char = ord($text[$a]);

				// non-russian characters
				if ($char<128 || $char>256)
					continue;

				// UTF-8
				if (($last_simb==208) && (($char>143 && $char<176) || $char==129))
					$charsets['UTF-8'] += ($utfupper * 2);
				if ((($last_simb==208) && (($char>175 && $char<192) || $char==145))
					|| ($last_simb==209 && $char>127 && $char<144))
					$charsets['UTF-8'] += ($utflower * 2);

				// CP1251
				if (($char>223 && $char<256) || $char==184)
					$charsets['CP1251'] += $lowercase;
				if (($char>191 && $char<224) || $char==168)
					$charsets['CP1251'] += $uppercase;

				// KOI8-R
				if (($char>191 && $char<224) || $char==163)
					$charsets['KOI8-R'] += $lowercase;
				if (($char>222 && $char<256) || $char==179)
					$charsets['KOI8-R'] += $uppercase;

				// IBM866
				if (($char>159 && $char<176) || ($char>223 && $char<241))
					$charsets['IBM866'] += $lowercase;
				if (($char>127 && $char<160) || $char==241)
					$charsets['IBM866'] += $uppercase;

				// ISO-8859-5
				if (($char>207 && $char<240) || $char==161)
					$charsets['ISO-8859-5'] += $lowercase;
				if (($char>175 && $char<208) || $char==241)
					$charsets['ISO-8859-5'] += $uppercase;

				// MAC
				if ($char>221 && $char<255)
					$charsets['MAC'] += $lowercase;
				if ($char>127 && $char<160)
					$charsets['MAC'] += $uppercase;

				$last_simb = $char;
			}
			arsort($charsets);
			return key($charsets);
		}
	}

	
	
    
	 
	
?>