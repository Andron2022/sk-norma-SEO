<?php
/*
  ok
  last updated 01.02.2015
*/
if(!defined('SIMPLA_ADMIN')){ die(); }

  global $admin_vars;
  $id = $admin_vars['uri']['id'];

  if(isset($_GET['id']) && $id == 0){
    $str_content = admin_add_comment();
  }elseif(isset($_GET['id']) && $id > 0){
    $str_content = admin_edit_comment($id);  
  }else{
    $str_content = admin_list_comments();
  }

/* ok - updated 23.07.2016 */
function admin_list_comments(){
	// record_type = 'pub'  record_id   userid
	$str = "";
	global $db, $tpl, $admin_vars, $site_vars;
	$deleted = 0;
		
	if(isset($_POST['del'])){
		$todo = isset($_POST["confirm_comments"]) ? 'confirm' : 'delete';			
		$del_articles = $_POST['del'];
			
		if(is_array($del_articles)){
			foreach($del_articles as $del_art){				
				if($todo == 'confirm'){
					$sql = "UPDATE ".$db->tables['comments']." 
						SET `active` = '1' 
						WHERE id = '".$del_art."' ";
						clear_cache(0);
					$do = $db->query($sql);
				}else{
					delete_comment($del_art);
					$do = 1;
				}
				if($do){ $deleted++; }
			}
		}
		
		if($deleted > 0){
			if($todo == 'confirm'){
				$redirect = "?action=comments&confirmed=".$deleted;
			}else{
				$redirect = "?action=comments&deleted=".$deleted;
			}
			header("Location: ".$redirect);
			exit;
		}
	}


    $query = "SELECT c.*, 
          p.`name` as pub_title, p.id as pub_id, 
          categ.`title` as categ_title, categ.id as categ_id, 
          product.`name` as product_title, product.id as product_id 
        FROM ".$db->tables['comments']." c 
        LEFT JOIN ".$db->tables['publications']." p on (p.id = c.record_id AND c.record_type = 'pub') 
        LEFT JOIN ".$db->tables['categs']." categ on (categ.id = c.record_id AND c.record_type = 'categ') 
        LEFT JOIN ".$db->tables['products']." product on (product.id = c.record_id AND c.record_type = 'product') 
		WHERE record_type IN ('categ','pub','product','comment')
        GROUP BY c.record_type, c.record_id 
        ORDER BY c.record_type, p.name, categ.title, c.active, c.`ddate` DESC ";
    $rows = $db->get_results($query, ARRAY_A);
    $tpl->assign("filter_comments",$rows);

    if(!empty($_GET['record_type']) && isset($_GET['record_id'])){
      $record_id_str = intval($_GET['record_id']) == 0 ? "" : " AND record_id = '".intval($_GET['record_id'])."' "; 
      $query2 = "SELECT * FROM ".$db->tables['comments']."
          WHERE record_type = '".$db->escape($_GET['record_type'])."' 
            $record_id_str  
          ORDER BY active, `ddate` DESC ";      

	  $query = "SELECT c.id, c.record_type, c.record_id, c.userid,  
				c.ddate, c.ip_address, c.active, 
				IF(c.active = '0',2,1) as sort_active, 
				IF(c.userid > '0',u.name,c.ext_h1) as u_name,
				IF(c.userid > '0',u.email,c.unreg_email) as u_email,
				c.notify, c.comment_text, 
				(SELECT COUNT(*)
					FROM ".$db->tables['uploaded_pics']." 
					WHERE record_id = c.id 
					AND record_type = 'comment'
				) as fotos_qty,
				(SELECT COUNT(*)
					FROM ".$db->tables['uploaded_files']." 
					WHERE record_id = c.id 
					AND record_type = 'comment'
				) as files_qty
					
				
		FROM ".$db->tables['comments']." c 
		LEFT JOIN ".$db->tables['users']." u ON (c.userid = u.id) 
		WHERE c.record_type IN ('categ','pub','product','comment') 
			AND record_type = '".$db->escape($_GET['record_type'])."' 
            $record_id_str 
		ORDER BY sort_active DESC, c.active DESC, c.`ddate` DESC ";

    }else{
      $query = "SELECT c.id, c.record_type, c.record_id, c.userid,  
				c.ddate, c.ip_address, c.active, 
				IF(c.active = '0',2,1) as sort_active, 
				IF(c.userid > '0',u.name,c.ext_h1) as u_name,
				IF(c.userid > '0',u.email,c.unreg_email) as u_email,
				c.notify, c.comment_text, 
				(SELECT COUNT(*)
					FROM ".$db->tables['uploaded_pics']." 
					WHERE record_id = c.id 
					AND record_type = 'comment'
				) as fotos_qty,
				(SELECT COUNT(*)
					FROM ".$db->tables['uploaded_files']." 
					WHERE record_id = c.id 
					AND record_type = 'comment'
				) as files_qty 			
									
		FROM ".$db->tables['comments']." c 
		LEFT JOIN ".$db->tables['users']." u ON (c.userid = u.id) 
		WHERE c.record_type IN ('categ','pub','product','comment')
		ORDER BY sort_active DESC, c.active DESC, c.`ddate` DESC ";
    }
    $rows = $db->get_results($query);
    if(!$rows || $db->num_rows == 0){
      $tpl->assign("list_comments",array());
      return $tpl->display("info/list_comments.html");
    }

  	// PAGE LIMITS
  	if(isset($_GET["page"])){ $page = $_GET["page"]; } else { $page = 0; }

  	$on_Page = ONPAGE;
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
  			else { $strPages .= " [<a href=./?action=comments&page=$i>$start1-$start2</a>] "; }
  		}
  	}
  
  	$rows = $db->get_results($query);
    if($db->num_rows == 0){
      $tpl->assign("list_comments",array());
      return $tpl->display("info/list_comments.html");
    }
  
    //$tpl->assign("pages", $strPages);
    $tpl->assign("pages",_pages($all_results, $page, ONPAGE,true));

    $items = array();
    if(is_array($rows))
    foreach($rows as $row){  
      $href = "?action=comments&id=".$row->id;
      $inserted = $row->ddate == 0 ? '-' : date($site_vars['site_date_format'], strtotime($row->ddate));
  
      $item = get_object_vars($row);
      $item["href"] = $href;
      $item["inserted"] = $inserted;
  
      $items[] = array(
        'id' => $row->id,
        'userid' => $row->userid,
        'ip_address' => $row->ip_address,
        'u_name' => $row->u_name,
        'u_email' => $row->u_email,
        'notify' => $row->notify,
        'files_qty' => $row->files_qty,
        'fotos_qty' => $row->fotos_qty,

        'active' => $row->active,
        'rowhref' => $href,
        'inserted' => $inserted,
        'record_type' => $row->record_type,
        'record_id' => $row->record_id,
        'comment_text' => $row->comment_text,
      );
    }
  
    $tpl->assign("list_comments",$items);
    return $tpl->display("info/list_comments.html");
  }

/* ok */
function update_admin_comment($id){
  global $lang, $db;

  $record_type = isset($_POST["record_type"]) ? trim($_POST["record_type"]) : '';
  $record_id = isset($_POST["record_id"]) ? intval($_POST["record_id"]) : 0;
  $unreg_email = isset($_POST["unreg_email"]) ? trim($_POST["unreg_email"]) : '';
  $comment_text = isset($_POST['comment_text']) ? trim($_POST['comment_text']) : '';

  $ext_h1 = !empty($_POST['ext_h1']) ? trim($_POST['ext_h1']) : '';
  $ext_desc = !empty($_POST['ext_desc']) ? trim($_POST['ext_desc']) : '';

  $active = isset($_POST['active']) ? intval($_POST['active']) : '0';
  
  $ddate = isset($_POST['insert_date']['Year']) ? $_POST['insert_date']['Year'] : date('Y');
    $ddate .= '-';
  $ddate .= isset($_POST['insert_date']['Month']) ? $_POST['insert_date']['Month'] : date('m');
    $ddate .= '-';
  $ddate .= isset($_POST['insert_date']['Day']) ? $_POST['insert_date']['Day'] : date('d');
    $ddate .= ' ';
  
  $ddate .= isset($_POST['insert_time']['Time_Hour']) ? $_POST['insert_time']['Time_Hour'] : date('H');
    $ddate .= ':';
  $ddate .= isset($_POST['insert_time']['Time_Minute']) ? $_POST['insert_time']['Time_Minute'] : date('i');
    $ddate .= ':';
  $ddate .= isset($_POST['insert_time']['Time_Second']) ? $_POST['insert_time']['Time_Second'] : date('s');

  $query = "UPDATE ".$db->tables['comments']." SET 
      record_type = '".$db->escape($record_type)."', 
      record_id = '".$db->escape($record_id)."', 
      unreg_email = '".$db->escape($unreg_email)."', 
      comment_text = '".$db->escape($comment_text)."',
      active = '".$active."',
      ddate = '".$ddate."',
	  `ext_h1` = '".$db->escape($ext_h1)."',
	  `ext_desc` = '".$db->escape($ext_desc)."'
    WHERE id = '".$id."' ";
  $db->query($query);
  /* add foto */
  
  if(!empty($_POST["width"]) && !empty($_POST["height"])){
	  add_vars_foto($id, 'comment', intval($_POST["width"]), intval($_POST["height"]));
  }

  if(!empty($_POST["img"])){
	  foreach($_POST["img"] as $k => $v){
			$query = "UPDATE ".$db->tables['uploaded_pics']." SET 
				`title` = '".$db->escape($v['title'])."'
				WHERE id = '".$k."' ";
			$db->query($query);
	  }
  }
  clear_cache(0);  
  header("Location: ?action=comments&id=".$id."&updated=1");
  exit;
}

/* ok */
function delete_admin_comment($id){
  global $db, $lang;
  delete_comment($id);
  
  header("Location: ?action=comments&deleted=1");
  exit;
}

/* ok */
function admin_edit_comment($id){
  global $db, $tpl, $lang, $admin_vars, $site_vars;
  $str = "";

  
	if(!empty($_GET['delimg']) && !empty($_GET['delext'])){
	  $f = '../upload/records/'.$_GET['delimg'].'.'.$_GET['delext'];
	  if(file_exists($f)){
		  @unlink($f);
		  
		  $db->query("DELETE FROM ".$db->tables['uploaded_pics']." 
			WHERE id = '".intval($_GET['delimg'])."' 
				AND ext = '".$db->escape($_GET['delext'])."' ");
			clear_cache(0);
		  header("Location: ?action=comments&id=".$id);
		  exit;
	  }	  
	}  
  
  if(isset($_POST['update'])){
    return update_admin_comment($id);
  }elseif(isset($_POST['delete'])){
    return delete_admin_comment($id);
  }elseif(isset($_POST['remark'])){
    $q = trim($_POST['remark']);
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    $db->query("INSERT INTO ".$db->tables['comments']."
      (record_type, record_id, userid, comment_text,
      ddate, ip_address, active) VALUES ('comment',
      '".$id."', '".$admin_vars['bo_user']['id']."', 
      '".$db->escape($q)."',
      '".date('Y-m-d H:i:s')."',
      '".$db->escape($ip)."', '1') ");
    $nid = $db->insert_id;
	clear_cache(0);
    header("Location: ?action=comments&id=".$nid."&added=1");
    exit;
  }

  $row = $db->get_row("SELECT c.*, u.login as user_login, u.name as user_name 
        FROM ".$db->tables['comments']." c 
        LEFT JOIN ".$db->tables['users']." u on (u.id = c.userid) 
        WHERE c.id = '".$id."' ", ARRAY_A);
  if($id > 0 && $db->num_rows == 0){
    return error_not_found();    
  }

  $row['inserted'] = date($site_vars['site_date_format']." ".$site_vars['site_time_format'], strtotime($row['ddate']));
  $row['reply_for'] = reply_for($row['record_type'], $row['record_id']);
  $tpl->assign("comment", $row);
  
  $imgs = $db->get_results("SELECT * FROM ".$db->tables['uploaded_pics']." 
	WHERE record_id = '".$id."' AND record_type = 'comment' 
	ORDER BY `id_in_record` ", ARRAY_A);
  $tpl->assign("comment_images", $imgs);
  return $tpl->display("info/edit_comment.html");
}


/* ok */
function admin_add_comment(){
  global $tpl, $db, $lang;
  if(isset($_POST['add'])){
    return add_admin_comment();
  }

  $ar = $db->get_results("SELECT id, login, email FROM ".$db->tables['users']." WHERE admin = '1' order by login ",ARRAY_A);  
  $tpl->assign("publishers", $ar);
  return $tpl->display("info/add_comment.html");
}

/* ok */
function add_admin_comment(){
  global $db, $lang;
  $record_type = isset($_POST["record_type"]) ? trim($_POST["record_type"]) : '';
  $record_id = isset($_POST["record_id"]) ? intval($_POST["record_id"]) : 0;
  $userid = isset($_POST["userid"]) ? intval($_POST["userid"]) : 0;
  $unreg_email = isset($_POST["unreg_email"]) ? trim($_POST["unreg_email"]) : '';
  $comment_text = isset($_POST["comment_text"]) ? trim($_POST["comment_text"]) : 0;
  $ip_address = isset($_SERVER["REMOTE_ADDR"]) ? trim($_SERVER["REMOTE_ADDR"]) : '';
  $ddate = date('Y-m-d H:i:s');

  $ext_h1 = isset($_POST["ext_h1"]) ? trim($_POST["ext_h1"]) : '';
  $ext_desc = isset($_POST["ext_desc"]) ? trim($_POST["ext_desc"]) : '';

  $query = "INSERT INTO ".$db->tables['comments']." (`record_type`, `record_id`, `userid`,
      `unreg_email`, `comment_text`, `ip_address`, `ddate`, `ext_h1`, `ext_desc`) VALUES (
      '".$db->escape($record_type)."',
      '".$db->escape($record_id)."',
      '".$db->escape($userid)."',
      '".$db->escape($unreg_email)."',
      '".$db->escape($comment_text)."',
      '".$db->escape($ip_address)."',
      '".$db->escape($ddate)."',
      '".$db->escape($ext_h1)."',
      '".$db->escape($ext_desc)."'
      ) ";
  $db->query($query);
  if(!empty($db->last_error)){ return db_error(basename(__FILE__).": 118"); }   
  $id = $db->insert_id;
  
  /* add foto */
  if(!empty($_POST["width"]) && !empty($_POST["height"])){
	  add_vars_foto($id, 'comment', intval($_POST["width"]), intval($_POST["height"]));
  }
  
  clear_cache(0);
  header("Location: ?action=comments&id=".$id."&added=1");
  exit;
}

/* ok */
function reply_for($type, $id){
  global $db, $lang;
  if($type == 'pub'){
    $dbn = $db->tables['publications'];
    $value = 'name as title, alias';
  }elseif($type == 'categ'){
    $dbn = $db->tables['categs'];
    $value = '`title`, alias';
  }elseif($type == 'product'){
    $dbn = $db->tables['products'];
    $value = 'name as title, alias';
  }elseif($type == 'comment'){
    $dbn = $db->tables['comments'];
    $value = 'id, ddate as title';
  }else{ return error_not_found(); }

  if(isset($value)){
    $row = $db->get_row("SELECT $value FROM $dbn WHERE id = '".$id."' ", ARRAY_A);
    if(!$row || $db->num_rows == 0){ return array(); }
    return $row;
  }

  return array();
}
?>