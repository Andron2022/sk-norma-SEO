<?php
/*
  in work
  last updated 15.09.2016
*/
if(!defined('SIMPLA_ADMIN')){ die(); }

	global $admin_vars;
	$id = $admin_vars['uri']['id'];
	$do = $admin_vars['uri']['do'];
	
	if(isset($_GET['id'])){
		$str_content = edit_orgs($id);
	}else{
		$str_content = list_orgs();
	}
	
	
	function edit_orgs($id){
		global $db, $tpl;
		if($id > 0){
			$row = $db->get_row("SELECT o.*, 
			
					(SELECT COUNT(*) 
					FROM ".$db->tables['connections']." cc 
					LEFT JOIN ".$db->tables['orders']." oo ON (cc.id1 = oo.id)
					WHERE cc.name1 = 'order'
					AND cc.id2 = o.id 
					AND cc.name2 = 'org'
					AND oo.id > '0'
					) as qty_orders, 
					
					(SELECT COUNT(*) 
					FROM ".$db->tables['connections']."
					WHERE name1 = 'org'
					AND id1 = o.id 
					AND name2 = 'site' 
					AND o.is_default = '1') as qty_sites, 
					
					f1.id as dir_id, 
					f2.id as buh_id, 
					f3.id as logo_id, 
					f4.id as stamp_id, 
					f1.ext as dir_ext, 
					f2.ext as buh_ext, 
					f3.ext as logo_ext, 
					f4.ext as stamp_ext
					
				FROM ".$db->tables['org']." o 
				LEFT JOIN ".$db->tables['uploaded_files']." f1 on (o.id = f1.record_id AND f1.record_type = 'org_dir') 
				
				LEFT JOIN ".$db->tables['uploaded_files']." f2 on (o.id = f2.record_id AND f2.record_type = 'org_buh') 
				
				LEFT JOIN ".$db->tables['uploaded_files']." f3 on (o.id = f3.record_id AND f3.record_type = 'org_logo') 
				
				LEFT JOIN ".$db->tables['uploaded_files']." f4 on (o.id = f4.record_id AND f4.record_type = 'org_stamp')
				
				WHERE o.id = '".$id."'
			", ARRAY_A);
			
			if(!$row || $db->num_rows == 0){
				return error_not_found();    
			}

		}else{
			$row = array(
				'id' => 0, 'own' => 1, 
				'title' => '', 'postal_code' => '',
				'country' => '', 'city' => '', 
				'address' => '', 'inn' => '',
				'kpp' => '', 'bik' => '', 
				'r_account' => '', 'k_account' => '',
				'bank' => '', 'phone' => '', 
				'post_address' => '', 'fio_dir' => '',
				'fio_buh' => '', 'fio_dir_kratko' => '',
				'fio_buh_kratko' => '', 'ogrn' => '', 
				'website' => '', 'email' => '', 
				'data_reg' => '', 'is_default' => 0
			);
		}
		
		/* save data */
		if(isset($_POST['save'])){
			
			$p_active = !empty($_POST["active"]) ? 1 : 0;
			$p_own = !empty($_POST["own"]) ? 1 : 0;
			$p_default = !empty($_POST["is_default"]) ? 1 : 0;
			$p_title = !empty($_POST["title"]) ? trim($_POST["title"]) : '';
			$p_phone = !empty($_POST["phone"]) ? trim($_POST["phone"]) : '';
			$p_website = !empty($_POST["website"]) ? trim($_POST["website"]) : '';
			$p_email = !empty($_POST["email"]) ? trim($_POST["email"]) : '';
			$p_postal_code = !empty($_POST["postal_code"]) ? trim($_POST["postal_code"]) : '';
			$p_country = !empty($_POST["country"]) ? trim($_POST["country"]) : '';
			$p_city = !empty($_POST["city"]) ? trim($_POST["city"]) : '';
			$p_address = !empty($_POST["address"]) ? trim($_POST["address"]) : '';
			$p_ogrn = !empty($_POST["ogrn"]) ? trim($_POST["ogrn"]) : '';
			
			$p_nds = !empty($_POST["nds"]) ? intval($_POST["nds"]) : 0;
			$p_inn = !empty($_POST["inn"]) ? trim($_POST["inn"]) : '';
			$p_kpp = !empty($_POST["kpp"]) ? trim($_POST["kpp"]) : '';
			$p_bik = !empty($_POST["bik"]) ? trim($_POST["bik"]) : '';
			$p_r_account = !empty($_POST["r_account"]) ? trim($_POST["r_account"]) : '';
			$p_k_account = !empty($_POST["k_account"]) ? trim($_POST["k_account"]) : '';
			$p_bank = !empty($_POST["bank"]) ? trim($_POST["bank"]) : '';
			$p_post_address = !empty($_POST["post_address"]) ? trim($_POST["post_address"]) : '';
			$p_fio_dir = !empty($_POST["fio_dir"]) ? trim($_POST["fio_dir"]) : '';
			$p_fio_buh = !empty($_POST["fio_buh"]) ? trim($_POST["fio_buh"]) : '';
			$p_fio_dir_kratko = !empty($_POST["fio_dir_kratko"]) ? trim($_POST["fio_dir_kratko"]) : '';
			$p_fio_buh_kratko = !empty($_POST["fio_buh_kratko"]) ? trim($_POST["fio_buh_kratko"]) : '';
			$p_Date_Day = !empty($_POST["Date_Day"]) ? trim($_POST["Date_Day"]) : 0;
			$p_Date_Month = !empty($_POST["Date_Month"]) ? trim($_POST["Date_Month"]) : 0;
			$p_Date_Year = !empty($_POST["Date_Year"]) ? trim($_POST["Date_Year"]) : 0;
			
			if(empty($p_Date_Year) || empty($p_Date_Day) || empty($p_Date_Month)){
				$p_data_reg = 'NULL';
			}else{
				$p_data_reg = "'".$p_Date_Year."-".$p_Date_Month."-".$p_Date_Day."'";
			}

			if($id == 0){
					$sql = "INSERT INTO ".$db->tables['org']." (
						`active`, `own`, `title`, 
						`postal_code`, `country`, 
						`city`, `address`, `inn`, 
						`kpp`, `bik`, `r_account`, 
						`k_account`, `bank`, 
						`phone`, `post_address`, 
						`fio_dir`, `fio_buh`, 
						`fio_dir_kratko`, 
						`fio_buh_kratko`, 
						`ogrn`, `website`, `email`, 
						`data_reg`, `is_default` , `nds`) 
						VALUES(
						'".$p_active."', '".$p_own."', 
						'".$db->escape($p_title)."', 
						'".$db->escape($p_postal_code)."', 
						'".$db->escape($p_country)."', 
						'".$db->escape($p_city)."', 
						'".$db->escape($p_address)."', 
						'".$db->escape($p_inn)."', 
						'".$db->escape($p_kpp)."', 
						'".$db->escape($p_bik)."', 
						'".$db->escape($p_r_account)."', 
						'".$db->escape($p_k_account)."', 
						'".$db->escape($p_bank)."', 
						'".$db->escape($p_phone)."', 
						'".$db->escape($p_post_address)."', 
						'".$db->escape($p_fio_dir)."', 
						'".$db->escape($p_fio_buh)."', 
						'".$db->escape($p_fio_dir_kratko)."', 
						'".$db->escape($p_fio_buh_kratko)."', 
						'".$db->escape($p_ogrn)."', 
						'".$db->escape($p_website)."', 
						'".$db->escape($p_email)."', 
						".$p_data_reg.", 
						'".$db->escape($p_default)."', 
						'".$p_nds."' 
						)
					";
					$db->query($sql);
					if(!empty($db->last_error)){ return db_error(basename(__FILE__).": ".__LINE__); }
					
					$id = $db->insert_id;
					register_changes('org', $id, 'add');
					
					save_org_files($id);
					$url = "?action=org&id=".$id."&added=1";
					header("Location: ".$url);
					exit;

			}else{
					$sql = "UPDATE ".$db->tables['org']." SET 
						`active` = '".$p_active."', 
						`own` = '".$p_own."', 
						`title` = '".$db->escape($p_title)."', 
						`postal_code` = '".$db->escape($p_postal_code)."', 
						`country` = '".$db->escape($p_country)."', 
						`city` = '".$db->escape($p_city)."', 
						`address` = '".$db->escape($p_address)."', 
						`inn` = '".$db->escape($p_inn)."', 
						`kpp` = '".$db->escape($p_kpp)."', 
						`bik` = '".$db->escape($p_bik)."', 
						`r_account` = '".$db->escape($p_r_account)."', 
						`k_account` = '".$db->escape($p_k_account)."', 
						`bank` = '".$db->escape($p_bank)."', 
						`phone` = '".$db->escape($p_phone)."', 
						`post_address` = '".$db->escape($p_post_address)."', 
						`fio_dir` = '".$db->escape($p_fio_dir)."', 
						`fio_buh` = '".$db->escape($p_fio_buh)."', 
						`fio_dir_kratko` = '".$db->escape($p_fio_dir_kratko)."', 
						`fio_buh_kratko` = '".$db->escape($p_fio_buh_kratko)."', 
						`ogrn` = '".$db->escape($p_ogrn)."', 
						`website` = '".$db->escape($p_website)."', 
						`email` = '".$db->escape($p_email)."', 
						`data_reg` = ".$p_data_reg.", 
						`is_default` = '".$db->escape($p_default)."',  
						`nds` = '".$p_nds."'
						WHERE id = '".$id."'
					";
					$db->query($sql);
					
					if(!empty($db->last_error)){ return db_error(basename(__FILE__).": ".__LINE__); }
					if($db->rows_affected > 0){
						register_changes('org', $id, 'update');
					}
					
					save_org_files($id);
					$url = "?action=org&id=".$id."&updated=1";
					header("Location: ".$url);
					exit;
			}
			
		}
		
		if(isset($_POST['delete'])){
			$qty = $db->get_var("SELECT COUNT(*) 
				FROM ".$db->tables['connections']." 
				WHERE id2 = '".$id."' AND name2 = 'org' 
				");	
			if($qty > 0){
				$row['error'] = 'Impossible to delete. There are connected orders.';
			}else{
				delete_org_files($id);
				$db->query("DELETE
				FROM ".$db->tables['connections']." 
				WHERE (id2 = '".$id."' AND name2 = 'org') 
					OR (id1 = '".$id."' AND name1 = 'org')
				");	
				
				$db->query("DELETE
				FROM ".$db->tables['org']." 
				WHERE id = '".$id."' ");	
				
				$url = "?action=org&deleted=1";
				header("Location: ".$url);
				exit;
			}
		}
				
		$tpl->assign('row', $row);
		return $tpl->display('settings/edit_org.html');
	}
	
	function delete_org_files($id, $where=''){
		// delete_uploaded_files($record_id, $record_type, $id = false)
		if(empty($where)){
			delete_uploaded_files($id, 'org_logo');
			delete_uploaded_files($id, 'org_stamp');
			delete_uploaded_files($id, 'org_dir');
			delete_uploaded_files($id, 'org_buh');
		}else{
			delete_uploaded_files($id, $where);
		}		
		return;		
	}
			
		
	function save_org_files($id){
		//upload_files_by_path($id,$type,$fullpath,$file);
		// delete_uploaded_files($record_id, $record_type, $id = false)
		global $tpl;
		$ar = array('dir', 'logo', 'buh', 'stamp');
		foreach($ar as $v){
			if(!empty($_FILES['org']['size'][$v]) 
				&& !empty($_FILES['org']['tmp_name'][$v]) 
				&& !empty($_FILES['org']['name'][$v]) 
				&& !empty($_FILES['org']['type'][$v])
			){
				$ar = explode('.', $_FILES['org']['name'][$v]);
				$ext = strtolower(array_pop($ar));
				
				delete_uploaded_files($id, 'org_'.$v);
				
				$file = array(
					'allow_download' > 1,
					'size' => $_FILES['org']['size'][$v],
					'tmp_name' => $_FILES['org']['tmp_name'][$v],
					'name' => $_FILES['org']['name'][$v],
					'ext' => $ext 				
				);
				
				upload_files_by_path($id, 'org_'.$v, $file);
				$size = well_size($_FILES['org']['size'][$v]);
				register_changes('org', $id, 'upload_file', $v.' ('.$size.')');
			}
		}
		return;
	}
	
	
	function list_orgs(){
		global $db, $tpl;
		$sql_num = "SELECT COUNT(*) 
			FROM ".$db->tables['org']."  
		";
		
		$sql = "SELECT * 
			FROM ".$db->tables['org']." 
			ORDER BY `own` desc, `is_default` desc, `title` 
		";
		$all_results = $db->get_var($sql_num);
		
		// PAGE LIMITS		
		$page = isset($_GET["page"]) ? intval($_GET["page"]) : 0;
		$on_Page = 50;
		$limit_Start = $page*$on_Page; // for pages generation
		$limit = "limit $limit_Start, $on_Page";
		$sql = $sql." ".$limit;
		$rows = $db->get_results($sql, ARRAY_A);

		$tpl->assign('all', $all_results);
		$tpl->assign('list', $rows);
		$tpl->assign("pages", _pages($all_results, $page, $on_Page));
		return $tpl->display('settings/list_orgs.html');
	}

	
	
?>