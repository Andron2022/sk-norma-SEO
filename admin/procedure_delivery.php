<?php
/*
  ok simpla version
  last updated 09.06.2019 
*/

if(!defined('SIMPLA_ADMIN')){ die(); }

global $tpl;

if(isset($_GET['id'])){
	$id = intval($_GET['id']);
	$str_content = edit_delivery($id);
}else{
	$str_content = list_delivery();
}

/* list of delivery methods */
function list_delivery(){
	global $db, $tpl;
	$deleted = 0;
	if(isset($_POST['del'])){
		$del_articles = $_POST['del'];
		if(is_array($del_articles)){
			foreach($del_articles as $del_art){
				$do = $db->query("DELETE FROM ".$db->tables['delivery']." WHERE id = '".$del_art."'  ");

				$query = "DELETE FROM ".$db->tables['delivery2pay']." WHERE id = '".$del_art."'";
				$db->query($query);

				if($do){ 
					$deleted++; 
					
					$db->query("DELETE FROM ".$db->tables['option_values']." 
						WHERE id_product = '".$del_art."'  
							AND `where_placed` = 'delivery'
					");
				}				
				
			}
		}
	}
	
	if($deleted > 0){
		clear_cache(0);
		header("Location: ./?action=delivery&deleted=".$deleted);
		exit;
	}

	$query = "SELECT d.*, s.site_url as site_url 
			FROM ".$db->tables['delivery']." d 
			LEFT JOIN ".$db->tables['site_info']." s on (s.id = d.site) ";

	if(!empty($_GET['site_id'])){
		$query .= " WHERE d.site = '".$db->escape($_GET['site_id'])."' OR d.site = '0' ";
	}
	
	$query .= " ORDER BY d.sort, d.`title` ";
	$rows = $db->get_results($query);
	if(!empty($db->last_error)){ return db_error($db->last_error); }
	if($db->num_rows == 0){ 
		$tpl->assign("list_delivery",array());
		return $tpl->display("settings/list_delivery.html");
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
			else { $strPages .= " [<a href=./?action=delivery&page=$i>$start1-$start2</a>] "; }
		}
	}
	
	$tpl->assign("pages",$strPages);
	$rows = $db->get_results($query, ARRAY_A);
	$items = array();
	foreach($rows as $k => $row){
		
		$href = "?action=delivery&id=".$row['id'];
		$description = nl2br(stripslashes($row['description']));
		$description = substr($description, 0, 150);		
		$item = $row;
		$item["href"] = $href;
		$items[] = $item;
	}
  $tpl->assign("list_delivery",$items);
  $str = $tpl->display("settings/list_delivery.html");
  return $str;
}

function edit_delivery($id){
	global $db, $tpl, $lang;

	if(isset($_POST['save'])){
		return update_delivery($id);
	}elseif(isset($_POST['delete'])){
		return delete_delivery($id);
	}
  
	$sites = $db->get_results("SELECT id, site_url FROM ".$db->tables['site_info']." ORDER BY site_url ", ARRAY_A);
	$tpl->assign("sites",$sites);
	
	if($id == 0){
		$row = array(
			'id' => 0, 'site' => 0, 'title' => '', 'description' => '',
			'price' => 0, 'currency' => 'rur', 'sort' => 0, 'noprice' => 0
		);
	}else{
		$row = $db->get_row("SELECT * FROM ".$db->tables['delivery']." WHERE id = '".$id."' ", ARRAY_A);		
	}
	
	if(!$row){ 
		return error_not_found();    
	}
	
	$row['payments'] = $db->get_results("SELECT p.*, dp.payment as selected  
		FROM ".$db->tables['order_payments']." p
		LEFT JOIN ".$db->tables['delivery2pay']." dp on (dp.delivery = '".$row['id']."' AND dp.payment = p.id) 
		WHERE p.active = '1' 
		GROUP BY p.id
		ORDER BY p.`sort` ", ARRAY_A);

	$tpl->assign("extra_options",extra_options($id, 'delivery'));
	$tpl->assign("row",$row);
	return $tpl->display("settings/edit_delivery.html");
}

function update_delivery($id){
	
	if($id == 0){ return add_delivery(); }
	global $tpl, $db;
	$v['site'] = empty($_POST["delivery"]["site"]) ? 0 : intval($_POST["delivery"]["site"]);
	$v['title'] = empty($_POST["delivery"]["title"]) ? "" : trim($_POST["delivery"]["title"]);
	$v['sort'] = empty($_POST["delivery"]["sort"]) ? 0 : intval($_POST["delivery"]["sort"]);
	$v['price'] = empty($_POST["delivery"]["price"]) ? 0 : trim($_POST["delivery"]["price"]);
	$v['currency'] = empty($_POST["delivery"]["currency"]) ? "rur" : trim($_POST["delivery"]["currency"]);
	if($v['currency'] != 'euro' && $v['currency'] != 'usd'){ $v['currency'] = 'rur'; }
	$v['noprice'] = empty($_POST["delivery"]["noprice"]) ? 0 : 1;
	$v['description'] = empty($_POST["delivery"]["description"]) ? "" : trim($_POST["delivery"]["description"]);
	
	$sql = "UPDATE ".$db->tables['delivery']." SET 
			`site` = '".$v['site']."', 
			`title` = '".$db->escape($v['title'])."', 
			`description` = '".$db->escape($v['description'])."', 
			`price` = '".$v['price']."', 
			`currency` = '".$v['currency']."', 
			`noprice` = '".$v['noprice']."', 
			`sort` = '".$v['sort']."' 
			WHERE id = '".$id."' ";
	$db->query($sql);	
	if(!empty($db->last_error)){ return db_error($db->last_error); }
	
	$db->query("DELETE FROM ".$db->tables['delivery2pay']." WHERE delivery = '".$id."' ");
	if(!empty($_POST["delivery"]["payments"])){
		foreach($_POST["delivery"]["payments"] as $v){
			$sql = "INSERT INTO ".$db->tables['delivery2pay']." 
				(delivery, payment) 
				VALUES ('".$id."', '".$v."')
				";
			$db->query($sql);
		}
	}
	
	update_extra_options($id, 'delivery');
	add_extra_options($id, 'delivery');
	
	clear_cache(0);
	header("Location: ./?action=delivery&id=".$id."&updated=1");
	exit;	
}

function delete_delivery($id){
  global $db, $tpl;
	$query = "DELETE FROM ".$db->tables['delivery']." WHERE id = '".$id."'";
	$db->query($query);

	$query = "DELETE FROM ".$db->tables['delivery2pay']." WHERE id = '".$id."'";
	$db->query($query);
	delete_extra_options($id, 'delivery');
	clear_cache(0);
	header("Location: ?action=delivery&deleted=1");
	exit;
}


function add_delivery(){
	global $tpl, $db;
	$v['site'] = empty($_POST["delivery"]["site"]) ? 0 : intval($_POST["delivery"]["site"]);
	$v['title'] = empty($_POST["delivery"]["title"]) ? "" : trim($_POST["delivery"]["title"]);
	$v['sort'] = empty($_POST["delivery"]["sort"]) ? 0 : intval($_POST["delivery"]["sort"]);
	$v['price'] = empty($_POST["delivery"]["price"]) ? 0 : trim($_POST["delivery"]["price"]);
	$v['currency'] = empty($_POST["delivery"]["currency"]) ? "rur" : trim($_POST["delivery"]["currency"]);
	if($v['currency'] != 'euro' && $v['currency'] != 'usd'){ $v['currency'] = 'rur'; }
	$v['noprice'] = empty($_POST["delivery"]["noprice"]) ? 0 : 1;
	$v['description'] = empty($_POST["delivery"]["description"]) ? "" : trim($_POST["delivery"]["description"]);
	
	$sql = "INSERT INTO ".$db->tables['delivery']." 
				(`site`, `title`, `description`, `price`, `currency`, `noprice`, `sort`) 
			VALUES(
			'".$v['site']."', 
			'".$db->escape($v['title'])."', 
			'".$db->escape($v['description'])."', 
			'".$v['price']."', 
			'".$v['currency']."', 
			'".$v['noprice']."', 
			'".$v['sort']."'
			) ";
			
	$db->query($sql);
	if(!empty($db->last_error)){ return db_error($db->last_error); }
	$id = $db->insert_id;
	
	if(!empty($_POST["delivery"]["payments"])){
		foreach($_POST["delivery"]["payments"] as $v){
			$sql = "INSERT INTO ".$db->tables['delivery2pay']." 
				(delivery, payment) 
				VALUES ('".$id."', '".$v."')
				";
			$db->query($sql);
		}
	}	
	add_extra_options($id, 'delivery');
	clear_cache(0);	
	header("Location: ?action=delivery&id=".$id."&added=1");
	exit;
}




?>