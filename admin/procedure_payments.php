<?php
/*
  ok simpla version
  last updated 09.06.2019 
*/

if(!defined('SIMPLA_ADMIN')){ die(); }

global $tpl;

if(isset($_GET['id'])){
	$id = intval($_GET['id']);
	$str_content = edit_payment($id);
}else{
	$str_content = list_payments();
}


/* list of payment methods */
function list_payments(){
	global $db, $tpl;
	$deleted = 0;
	if(isset($_POST['del'])){
		$del_articles = $_POST['del'];
		if(is_array($del_articles)){
			foreach($del_articles as $del_art){
				$do = $db->query("DELETE FROM ".$db->tables['order_payments']." WHERE id = '".$del_art."'  ");
				if($do){ 
					$deleted++; 
					
					$db->query("DELETE FROM ".$db->tables['option_values']." 
						WHERE id_product = '".$del_art."'  
							AND `where_placed` = 'payments'
					");
				}
			}
		}
	}
	
	if($deleted > 0){
		clear_cache(0);
		header("Location: ?action=settings&do=payments&deleted=".$deleted);
		exit;
	}

	$query = "SELECT p.*, s.site_url as site_url 
			FROM ".$db->tables['order_payments']." p 
			LEFT JOIN ".$db->tables['site_info']." s on (s.id = p.site) ";

	if(!empty($_GET['site_id'])){
		$query .= " WHERE p.site = '".$db->escape($_GET['site_id'])."' OR p.site = '0' ";
	}
	
	$query .= " ORDER BY p.sort, p.`title` ";
	$rows = $db->get_results($query);
	if(!empty($db->last_error)){ return db_error($db->last_error); }
	if($db->num_rows == 0){ 
		$tpl->assign("list_payments",array());
		return $tpl->display("settings/list_payments.html");
	}

	// PAGE LIMITS
	if(isset($_GET["page"])){ $page = $_GET["page"]; } else { $page = 0; }
	$on_Page = 50;
	$limit_Start = $page*$on_Page; // for pages generation
	$limit = "limit $limit_Start, $on_Page";
	$all_results = $db->num_rows;
	$query = $query." ".$limit;

	// COUNT PAGES
	if($page > 0) { $next = $page-1; }
	if($all_results > $limit_Start+$on_Page){ $last = $page+1; }
	if($limit_Start == 0){ $p1 = 1; } else { $p1 = $limit_Start; }
	$strPages = "";
	$pages_all = ceil($all_results/$on_Page);
	if($pages_all < 2){ $strPages = ""; }
	else {
		for($i = 0; $i < $pages_all; $i++){
			$start1 = $i*$on_Page+1;
			$start2 = $i*$on_Page+$on_Page;
			if($start2 > $all_results) { $start2 = $all_results; }
			if($page == $i){ $strPages .= " [$start1-$start2] "; }
			else { $strPages .= " [<a href=?action=settings&do=payments&page=$i>$start1-$start2</a>] "; }
		}
	}
	
	$tpl->assign("pages",$strPages);
	$rows = $db->get_results($query, ARRAY_A);
	$items = array();
	foreach($rows as $k => $row){
		
		$href = "?action=settings&do=payments&id=".$row['id'];
		$description = nl2br(stripslashes($row['description']));
		$description = substr($description, 0, 150);		
		$item = $row;
		$item["href"] = $href;
		$items[] = $item;
	}
  $tpl->assign("list_payments",$items);
  $str = $tpl->display("settings/list_payments.html");
  return $str;
}

function edit_payment($id){
	global $db, $tpl, $lang;

	if(isset($_POST['save'])){
		return update_payment($id);
	}elseif(isset($_POST['delete'])){
		return delete_payment($id);
	}
  
	$sites = $db->get_results("SELECT id, site_url FROM ".$db->tables['site_info']." ORDER BY site_url ", ARRAY_A);
	$tpl->assign("sites",$sites);
	
	if($id == 0){
		$row = array(
			'id' => 0, 'site' => 0, 'title' => '', 'description' => '',
			'price_min' => 0, 'price_max' => 0, 'currency' => 'rur', 
			'sort' => 0, 'active' => 1, 'what_todo' => '', 'encoding' => 'utf-8', 
			'new_window' => 0
		);
	}else{
		$row = $db->get_row("SELECT * FROM ".$db->tables['order_payments']." WHERE id = '".$id."' ", ARRAY_A);		
	}
	
	if(!$row){ 
		return error_not_found();    
	}
	
	$tpl->assign("extra_options",extra_options($id, 'payments'));
	$tpl->assign("row",$row);
	return $tpl->display("settings/edit_payment.html");
}

function update_payment($id){
	if($id == 0){ return add_payment(); }
	global $tpl, $db;
	$v['site'] = empty($_POST["payment"]["site"]) ? 0 : intval($_POST["payment"]["site"]);
	$v['title'] = empty($_POST["payment"]["title"]) ? "" : trim($_POST["payment"]["title"]);
	$v['sort'] = empty($_POST["payment"]["sort"]) ? 0 : intval($_POST["payment"]["sort"]);
	$v['price_min'] = empty($_POST["payment"]["price_min"]) ? 0 : trim($_POST["payment"]["price_min"]);
	$v['price_max'] = empty($_POST["payment"]["price_max"]) ? 0 : trim($_POST["payment"]["price_max"]);
	$v['currency'] = empty($_POST["payment"]["currency"]) ? "rur" : trim($_POST["payment"]["currency"]);
	if($v['currency'] != 'euro' && $v['currency'] != 'usd'){ $v['currency'] = 'rur'; }
	$v['description'] = empty($_POST["payment"]["description"]) ? "" : trim($_POST["payment"]["description"]);
	$v['active'] = empty($_POST["payment"]["active"]) ? 0 : 1;
	$v['new_window'] = empty($_POST["payment"]["new_window"]) ? 0 : 1;

	$v['what_todo'] = empty($_POST["payment"]["what_todo"]) ? "" : trim($_POST["payment"]["what_todo"]);
	$v['encoding'] = empty($_POST["payment"]["encoding"]) ? "" : trim($_POST["payment"]["encoding"]);
	
	$sql = "UPDATE ".$db->tables['order_payments']." SET 
			`site` = '".$v['site']."', 
			`title` = '".$db->escape($v['title'])."', 
			`description` = '".$db->escape($v['description'])."', 
			`price_min` = '".$db->escape($v['price_min'])."', 
			`price_max` = '".$db->escape($v['price_max'])."', 
			`currency` = '".$v['currency']."', 
			`active` = '".$v['active']."', 
			`new_window` = '".$v['new_window']."', 
			`sort` = '".$v['sort']."',
			
			`what_todo` = '".$db->escape($v['what_todo'])."', 
			`encoding` = '".$db->escape($v['encoding'])."' 

			WHERE id = '".$id."' ";
	$db->query($sql);	
	update_extra_options($id, 'payments');
	add_extra_options($id, 'payments');
	clear_cache(0);
	header("Location: ?action=settings&do=payments&id=".$id."&updated=1");
	exit;	
}

function delete_payment($id){
  global $db, $tpl;
	$query = "DELETE FROM ".$db->tables['order_payments']." WHERE id = '".$id."'";
	$db->query($query);
	clear_cache(0);
	delete_extra_options($id, 'payments');
	header("Location: ?action=settings&do=payments&deleted=1");
	exit;
}


function add_payment(){
	global $tpl, $db;
	$v['site'] = empty($_POST["payment"]["site"]) ? 0 : intval($_POST["payment"]["site"]);
	$v['title'] = empty($_POST["payment"]["title"]) ? "" : trim($_POST["payment"]["title"]);
	$v['sort'] = empty($_POST["payment"]["sort"]) ? 0 : intval($_POST["payment"]["sort"]);

	$v['price_min'] = empty($_POST["payment"]["price_min"]) ? 0 : trim($_POST["payment"]["price_min"]);
	$v['price_max'] = empty($_POST["payment"]["price_max"]) ? 0 : trim($_POST["payment"]["price_max"]);
	$v['currency'] = empty($_POST["payment"]["currency"]) ? "rur" : trim($_POST["payment"]["currency"]);
	if($v['currency'] != 'euro' && $v['currency'] != 'usd'){ $v['currency'] = 'rur'; }
	$v['description'] = empty($_POST["payment"]["description"]) ? "" : trim($_POST["payment"]["description"]);
	$v['active'] = empty($_POST["payment"]["active"]) ? 0 : 1;
	$v['new_window'] = empty($_POST["payment"]["new_window"]) ? 0 : 1;

	$v['what_todo'] = empty($_POST["payment"]["what_todo"]) ? "" : trim($_POST["payment"]["what_todo"]);
	$v['encoding'] = empty($_POST["payment"]["encoding"]) ? "" : trim($_POST["payment"]["encoding"]);

	
	$sql = "INSERT INTO ".$db->tables['order_payments']." 
				(`site`, `title`, `description`, `price_min`, `price_max`, 
				`currency`, `active`, `sort`, `new_window`, `what_todo`, `encoding`) 
			VALUES(
			'".$v['site']."', 
			'".$db->escape($v['title'])."', 
			'".$db->escape($v['description'])."', 
			'".$v['price_min']."', 
			'".$v['price_max']."', 
			'".$v['currency']."', 
			'".$v['active']."', 
			'".$v['sort']."',
			
			'".$v['new_window']."',
			'".$db->escape($v['what_todo'])."',
			'".$db->escape($v['encoding'])."'
			) ";
			
	$db->query($sql);
	$id = $db->insert_id;
	add_extra_options($id, 'payments');
	clear_cache(0);
	header("Location: ?action=settings&do=payments&id=".$id."&added=1");
	exit;
}




?>