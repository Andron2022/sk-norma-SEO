<?php
/*
  in work
  last updated 16.11.2016
*/
if(!defined('SIMPLA_ADMIN')){ die(); }

	global $admin_vars;
	$mode = $admin_vars["uri"]["mode"];
	$do = $admin_vars["uri"]["do"];
	$id = $admin_vars["uri"]["id"];
	$page = $admin_vars["uri"]["page"];
	$hint = !empty($_GET['hint']) ? trim($_GET['hint']) : '';

	$str_content = '';
	
	if(!empty($id)){
		/* show coupon by ID */
		$str_content = show_coupon($id);
	}elseif(isset($_GET['id'])){
		/* add new coupon */
		$str_content = edit_coupon(0);
	}else{
		/* list of coupons */
		$str_content = list_coupons();
	}


	
	function list_coupons(){
		global $db, $admin_vars, $tpl, $site;		
		$sql = "SELECT c.*, 
		
			IF(
				IFNULL(`date_start`,'1990-11-11 11:11:11')
				 < DATE(NOW()) 
				AND 
				IFNULL(`date_stop`,'2199-11-11 11:11:11')
				 > DATE(NOW())
				AND 
				(c.onetime = '0' OR (SELECT COUNT(*) 
					FROM ".$db->tables['discounts']." 
					WHERE is_coupon = c.id
				) = 0 AND c.onetime = '1')
				AND c.active = '1' 
				, '1', '0'
			) as active_date, 
		
			(SELECT `date_insert` FROM ".$db->tables['changes']." 
				WHERE c.id = where_id AND where_changed = 'coupon' 
				ORDER BY `type_changes` DESC, `date_insert` DESC 
				LIMIT 0,1 
			) as last_updated, 
			(SELECT COUNT(*) FROM ".$db->tables['discounts']." 
				WHERE is_coupon = c.id
			) as qty_discounts  
			FROM ".$db->tables['coupons']." c 
			ORDER BY last_updated DESC, c.id DESC ";
		$rows = $db->get_results($sql, ARRAY_A);
		//db->debug(); exit;
		if(!empty($db->last_error)){ 
			return db_error(basename(__FILE__).": LINE ".__LINE__); 
		}   

		$tpl->assign("all", $db->num_rows);
		$tpl->assign("site", $site->vars);
		$tpl->assign("rows", $rows);
		return $tpl->fetch("settings/list_coupons.html", null, 1);
	}
	
	function edit_coupon($id){
		global $db, $admin_vars, $tpl, $site;
		if($id == 0){
			$row = array();
		}else{
			$sql = "SELECT c.* FROM ".$db->tables['coupons']." c 
				WHERE id = '".$id."' ";
			$row = $db->get_row($sql, ARRAY_A);
			if(!empty($db->last_error)){ 
				return db_error(basename(__FILE__).": LINE ".__LINE__); 
			}
		}
		
		$sql = "SELECT c.*, 
				u.name as who_added_name,
				u.login as who_added_login
				FROM ".$db->tables['changes']." c 
				LEFT JOIN ".$db->tables['users']." u ON (c.who_changed = u.id)
				WHERE where_id = '".$id."' 
				AND where_changed = 'coupon' 
				AND type_changes = 'add'
		";
		$row1 = $db->get_row($sql);
		if(!empty($row1)){
			$row['who_added'] = !empty($row1->who_added_name) 
				? $row1->who_added_name : $row1->who_added_login;
			$row['who_added_id'] = $row1->who_changed;
			$row['when_added'] = $row1->date_insert;
		}
		
		$sql = "SELECT c.*, 
				u.name as who_updated_name,
				u.login as who_updated_login
				FROM ".$db->tables['changes']." c 
				LEFT JOIN ".$db->tables['users']." u ON (c.who_changed = u.id)
				WHERE where_id = '".$id."' 
				AND where_changed = 'coupon' 
				AND type_changes = 'update' 
				ORDER BY c.id DESC 
				LIMIT 0,1
		";
		$row1 = $db->get_row($sql);
		if(!empty($row1)){
			$row['who_updated'] = !empty($row1->who_updated_name) 
				? $row1->who_updated_name : $row1->who_updated_login;
			$row['who_updated_id'] = $row1->who_changed;
			$row['last_updated'] = $row1->date_insert;
		}
	
		if(!empty($_POST["save"])){ update_coupon($id); }
		if(!empty($_POST["delete"])){ delete_coupon($id); }
		$tpl->assign("site", $site->vars);
		$tpl->assign("row", $row);
		return $tpl->fetch("settings/edit_coupon.html", null, 1);
	}
	
	function delete_coupon($id){
		/* if no added records for partners - delete */
		global $db;
		$qty = $db->get_var("SELECT COUNT(*)
			FROM ".$db->tables['discounts']." 
			WHERE is_coupon = '".$id."'
		");
		
		if($qty == 0){
			$sql = "DELETE FROM ".$db->tables['coupons']." 
				WHERE id = '".$id."'
			";
			$db->query($sql);
			
			$sql = "DELETE FROM ".$db->tables['changes']." 
				WHERE where_changed = 'coupon' 
				AND where_id = '".$id."'
			";
			$db->query($sql);			
			$url = "?action=coupons&deleted=1";			
		}else{
			$url = "?action=coupons&do=edit&id=".$id."&error=1";
		}
		
		header("Location: ".$url);
		exit;		
	}

	function update_coupon($id){		
		global $db;
		$ar = array();
		$ar['active'] = !empty($_POST['active']) ? 1 : 0;
		$ar['onetime'] = !empty($_POST['onetime']) ? 1 : 0;		
		$ar['site_id'] = !empty($_POST['site_id']) ? intval($_POST['site_id']) : 0;
		$ar['for_userid'] = !empty($_POST['for_userid']) ? intval($_POST['for_userid']) : 0;
		
		$ar['discount_procent'] = !empty($_POST['discount_procent']) ? 1 : 0;
		
		$ar['title'] = !empty($_POST['title']) ? trim($_POST['title']) : '';
		$ar['content'] = !empty($_POST['content']) ? trim($_POST['content']) : '';
		
		$ar['id_product'] = !empty($_POST['id_product']) ? intval($_POST['id_product']) : 0;
		$ar['id_categ'] = !empty($_POST['id_categ']) ? intval($_POST['id_categ']) : 0;
		$ar['discount_summ'] = !empty($_POST['discount_summ']) ? intval($_POST['discount_summ']) : 0;
		$ar['partner_summ'] = !empty($_POST['partner_summ']) ? intval($_POST['partner_summ']) : 0;
		
		$dstart = NULL;
		if(
			isset($_POST['date_start']['Day']) && 
			isset($_POST['date_start']['Month']) &&
			isset($_POST['date_start']['Year']) && 
			isset($_POST['date_start']['Hour']) && 
			isset($_POST['date_start']['Minute']) && 
			isset($_POST['date_start']['Second']) 			
		){
			$dstart = $_POST['date_start']['Year'].'-'.$_POST['date_start']['Month'].'-'.$_POST['date_start']['Day'].' '.$_POST['date_start']['Hour'].':'.$_POST['date_start']['Minute'].':'.$_POST['date_start']['Second'];
			
			if($dstart == '0-0-0 00:00:00'){
				$dstart = 'NULL';
			}else{ $dstart = "'".$dstart."'"; }
		}
		
		$dstop = NULL;
		if(
			isset($_POST['date_stop']['Day']) && 
			isset($_POST['date_stop']['Month']) &&
			isset($_POST['date_stop']['Year']) && 
			isset($_POST['date_stop']['Hour']) && 
			isset($_POST['date_stop']['Minute']) && 
			isset($_POST['date_stop']['Second']) 			
		){
			$dstop = $_POST['date_stop']['Year'].'-'.$_POST['date_stop']['Month'].'-'.$_POST['date_stop']['Day'].' '.$_POST['date_stop']['Hour'].':'.$_POST['date_stop']['Minute'].':'.$_POST['date_stop']['Second'];
			
			if($dstop == '0-0-0 00:00:00'){
				$dstop = 'NULL';
			}else{ $dstop = "'".$dstop."'"; }
		}
		
		
		if($id > 0){
			/* update */
			$sql = "UPDATE ".$db->tables['coupons']." SET 
				`title` = '".$db->escape($ar['title'])."', 
				`date_start` = ".$dstart.", 
				`date_stop` = ".$dstop.", 
				`id_product` = '".$ar['id_product']."', 
				`id_categ` = '".$ar['id_categ']."', 
				`content` = '".$db->escape($ar['content'])."', 
				`discount_summ` = '".$ar['discount_summ']."', 
				`discount_procent` = '".$ar['discount_procent']."', 
				`partner_summ` = '".$ar['partner_summ']."', 
				`for_userid` = '".$ar['for_userid']."', 
				`active` = '".$ar['active']."', 
				`onetime` = '".$ar['onetime']."', 
				`site_id` = '".$ar['site_id']."'
				WHERE id = '".$id."' ";
			$db->query($sql);
			if(!empty($db->last_error)){ 
				return db_error(basename(__FILE__).": LINE ".__LINE__); 
			} 
			register_changes('coupon', $id, 'update');
			clear_cache(0);
			$url = '?action=coupons&do=edit&id='.$id.'&updated=1';
			header('Location: '.$url);
			exit;
			
		}else{
			/* add */
			$sql = "INSERT INTO ".$db->tables['coupons']." 
				(`title`, `date_start`, `date_stop`, `id_product`, 
				`id_categ`, `content`, `discount_summ`, `discount_procent`, `partner_summ`, `for_userid`, `active`, `onetime`, `site_id`) 
				VALUES(
				'".$db->escape($ar['title'])."', ".$dstart.", ".$dstop.", 
				'".$ar['id_product']."', '".$ar['id_categ']."', 
				'".$db->escape($ar['content'])."', '".$ar['discount_summ']."', 
				'".$ar['discount_procent']."', '".$ar['partner_summ']."', 				
				'".$ar['for_userid']."', '".$ar['active']."', 
				'".$ar['onetime']."', '".$ar['site_id']."' 
				) ";
			$db->query($sql);
			if(!empty($db->last_error)){ 
				return db_error(basename(__FILE__).": LINE ".__LINE__); 
			} 
			$id = $db->insert_id;
			clear_cache(0);
			register_changes('coupon', $id, 'add', $ar['title']);
			
			$url = '?action=coupons&do=edit&id='.$id.'&added=1';
			header('Location: '.$url);
			exit;
		}
	}
	
	function show_coupon($id){
		global $db, $admin_vars, $tpl, $site;
		$do = $admin_vars["uri"]["do"];
		if($do == 'edit'){ return edit_coupon($id); }
		$sql = "SELECT c.*, 
					(SELECT COUNT(*) 
					FROM ".$db->tables['discounts']." 
					WHERE is_coupon = c.id 
					) as qty_discounts, 
					si.site_url as site_url, 
					ca.title as categ_title, 
					p.`name` as product_title, 
					u.login as user_login,
					u.`name` as user_name
				FROM ".$db->tables['coupons']." c 
				LEFT JOIN ".$db->tables['site_info']." si ON (c.site_id = si.id) 
				LEFT JOIN ".$db->tables['categs']." ca ON (c.id_categ = ca.id) 
				LEFT JOIN ".$db->tables['products']." p ON (c.id_product = p.id) 
				LEFT JOIN ".$db->tables['users']." u ON (c.for_userid = u.id) 
				WHERE c.id = '".$id."' ";
		$row = $db->get_row($sql, ARRAY_A);
		//$db->debug(); exit;
		if(!empty($db->last_error)){ 
			return db_error(basename(__FILE__).": LINE ".__LINE__); 
		}
		
		if(isset($site->vars['site_date_format']) && isset($site->vars['site_time_format'])){			
			if(!empty($row["date_start"])){				
				$row["date_start"] = date($site->vars['site_date_format']." ".$site->vars['site_time_format'], strtotime($row["date_start"]));
			}
			
			if(!empty($row["date_stop"])){				
				$row["date_stop"] = date($site->vars['site_date_format']." ".$site->vars['site_time_format'], strtotime($row["date_stop"]));
			}
		}
		
		$now = time();
		if(
			(!empty($row["date_start"]) || !empty($row["date_stop"]))
			&& 
			(
		$now < strtotime($row["date_start"]) || $now > strtotime($row["date_stop"]))){
			$row["active"] = 0;
		}

		if(!empty($row['qty_discounts'])){
			$sql_num = "SELECT COUNT(*) 
					FROM ".$db->tables['discounts']." d 
					WHERE d.is_coupon = '".$row['id']."'
			";
			$sql = "SELECT d.*, o.order_id as order_id_full, 
						o.created as order_date, 
						o.payd_status as payd_status, 
						o.site_id
					FROM ".$db->tables['discounts']." d 
					LEFT JOIN ".$db->tables['orders']." o ON (d.order_id = o.id)
					WHERE d.is_coupon = '".$row['id']."' 
					ORDER BY d.id DESC
			";			
			
			// PAGE LIMITS
			if(isset($_GET["page"])){ $page = intval($_GET["page"]); } else { $page = 0; }
			$on_Page = ONPAGE;
			$limit_Start = $page*$on_Page; // for pages generation
			$limit = "limit $limit_Start, $on_Page";
			$all_results = $db->get_var($sql_num);			
			$sql = $sql." ".$limit;
			$row['list_discounts'] = $db->get_results($sql, ARRAY_A);
			$row['all_discounts'] = $all_results;
			$tpl->assign("pages",_pages($all_results, $page, $on_Page,true));
			
			//$db->debug(); exit;
		}		  
		  
		$tpl->assign("row", $row);
		return $tpl->fetch("settings/show_coupon.html", null, 1);
	}
	
?>