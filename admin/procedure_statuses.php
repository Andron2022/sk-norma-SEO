<?php
/*
  ok simpla version
  last updated 08.11.2019 
  08.11.2019 - fixed adding of status (alias)
*/
if(!defined('SIMPLA_ADMIN')){ die(); }


global $tpl;

if(isset($_GET['id'])){
	$id = intval($_GET['id']);
	$str_content = edit_status($id);
}else{
	$str_content = list_statuses();
}


/* list of statuses */
function list_statuses(){
	global $db, $tpl;
	$deleted = 0;
	if(isset($_POST['del'])){
		$del_articles = $_POST['del'];
		if(is_array($del_articles)){
			foreach($del_articles as $del_art){
				$do = $db->query("DELETE FROM ".$db->tables['order_status']." WHERE id = '".$del_art."'  ");
				if($do){ $deleted++; }
			}
		}
	}
	
	if($deleted > 0){
		clear_cache(0);
		header("Location: ?action=settings&do=statuses&deleted=".$deleted);
		exit;
	}

	$query = "SELECT d.*, s.site_url as site_url 
			FROM ".$db->tables['order_status']." d 
			LEFT JOIN ".$db->tables['site_info']." s on (s.id = d.site) ";

	if(!empty($_GET['site_id'])){
		$query .= " WHERE d.site = '".$db->escape($_GET['site_id'])."' OR d.site = '0' ";
	}
	
	$query .= " ORDER BY d.`sort`, d.`title` ";
	$rows = $db->get_results($query);
	if(!empty($db->last_error)){ return db_error($db->last_error); }
	if($db->num_rows == 0){ 
		$tpl->assign("list_statuses",array());
		return $tpl->display("settings/list_statuses.html");
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
			else { $strPages .= " [<a href=?action=settings&do=statuses&page=$i>$start1-$start2</a>] "; }
		}
	}
	
	$tpl->assign("pages",$strPages);
	$rows = $db->get_results($query, ARRAY_A);
	$items = array();
	foreach($rows as $k => $row){
		
		$href = "?action=settings&do=statuses&id=".$row['id'];
		$description = nl2br(stripslashes($row['description']));
		$description = substr($description, 0, 150);		
		$item = $row;
		$item["href"] = $href;
		$items[] = $item;
	}
  $tpl->assign("list_statuses",$items);
  $str = $tpl->display("settings/list_statuses.html");
  return $str;
}

function edit_status($id){
	global $db, $tpl, $lang;

	if(isset($_POST['save'])){
		return update_status($id);
	}elseif(isset($_POST['delete'])){
		return delete_status($id);
	}
  
	$sites = $db->get_results("SELECT id, site_url FROM ".$db->tables['site_info']." 
		ORDER BY site_url ", ARRAY_A);
	$tpl->assign("sites",$sites);
	
	if($id == 0){
		$row = array(
			'id' => 0, 'site' => 0, 'title' => '', 'description' => '',
			'active' => 1, 'title_client' => '', 'sort' => 0, 'show_client' => 1,
			'edit' => 1, 'alias' => ''
		);
	}else{
		$row = $db->get_row("SELECT * FROM ".$db->tables['order_status']." WHERE id = '".$id."' ", ARRAY_A);		
	}
	
	if(!$row){ 
		return error_not_found();    
	}
	$tpl->assign("row",$row);
	return $tpl->display("settings/edit_status.html");
}

function update_status($id){
	if($id == 0){ return add_status(); }
	global $tpl, $db;
	$v['site'] = empty($_POST["status"]["site"]) ? 0 : intval($_POST["status"]["site"]);
	$v['title'] = empty($_POST["status"]["title"]) ? "" : trim($_POST["status"]["title"]);
	$v['title_client'] = empty($_POST["status"]["title_client"]) ? "" : trim($_POST["status"]["title_client"]);
	$v['description'] = empty($_POST["status"]["description"]) ? "" : trim($_POST["status"]["description"]);
	$v['sort'] = empty($_POST["status"]["sort"]) ? 0 : intval($_POST["status"]["sort"]);
	$v['active'] = empty($_POST["status"]["active"]) ? 0 : 1;
	$v['show_client'] = empty($_POST["status"]["show_client"]) ? 0 : 1;
	$v['edit'] = empty($_POST["status"]["edit"]) ? 0 : 1;
	$v['alias'] = empty($_POST["status"]["alias"]) ? '' : trim($_POST["status"]["alias"]);
	
	$sql = "UPDATE ".$db->tables['order_status']." SET 
			`site` = '".$v['site']."', 
			`title` = '".$db->escape($v['title'])."', 
			`title_client` = '".$db->escape($v['title_client'])."', 
			`description` = '".$db->escape($v['description'])."', 
			`sort` = '".$v['sort']."', 
			`active` = '".$v['active']."', 
			`show_client` = '".$v['show_client']."', 
			`edit` = '".$v['edit']."',
			`alias` = '".$db->escape($v['alias'])."' 
			WHERE id = '".$id."' ";
	$db->query($sql);
	clear_cache(0);
	header("Location: ?action=settings&do=statuses&id=".$id."&updated=1");
	exit;	
}

function delete_status($id){
  global $db, $tpl;
	$query = "DELETE FROM ".$db->tables['order_status']." WHERE id = '".$id."'";
	$db->query($query);
	clear_cache(0);
	header("Location: ?action=settings&do=statuses&deleted=1");
	exit;
}


function add_status(){
	global $tpl, $db;
	$v['site'] = empty($_POST["status"]["site"]) ? 0 : intval($_POST["status"]["site"]);
	$v['title'] = empty($_POST["status"]["title"]) ? "" : trim($_POST["status"]["title"]);
	$v['title_client'] = empty($_POST["status"]["title_client"]) ? "" : trim($_POST["status"]["title_client"]);
	$v['description'] = empty($_POST["status"]["description"]) ? "" : trim($_POST["status"]["description"]);
	$v['sort'] = empty($_POST["status"]["sort"]) ? 0 : intval($_POST["status"]["sort"]);
	$v['active'] = empty($_POST["status"]["active"]) ? 0 : 1;
	$v['show_client'] = empty($_POST["status"]["show_client"]) ? 0 : 1;
	$v['edit'] = empty($_POST["status"]["edit"]) ? 0 : 1;
	$v['alias'] = empty($_POST["status"]["alias"]) ? '' : trim($_POST["status"]["alias"]);
	
	$sql = "INSERT INTO ".$db->tables['order_status']." 
				(`site`, `title`, `title_client`, `description`, `sort`, 
				`active`, `show_client`, `edit`, `alias`) 
			VALUES(
				'".$v['site']."', 
				'".$db->escape($v['title'])."', 
				'".$db->escape($v['title_client'])."', 
				'".$db->escape($v['description'])."', 
				'".$v['sort']."',
				'".$v['active']."',
				'".$v['show_client']."',
				'".$v['edit']."',
				'".$db->escape($v['alias'])."'
			) ";
			
	$db->query($sql);
	$id = $db->insert_id;
	header("Location: ?action=settings&do=statuses&id=".$id."&added=1");
	exit;
}




?>