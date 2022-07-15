<?php
/*
  ok
  last updated 23.02.2020
  added sms in events
*/
if(!defined('SIMPLA_ADMIN')){ die(); }
  global $admin_vars;
  $id = $admin_vars['uri']['id'];
  $do = $admin_vars['uri']['do'];


if(isset($_GET['id']) && $do == 'type'){
	$id = intval($_GET['id']);
	$str_content = edit_emails_type($id);		
}elseif(isset($_GET['id']) && $do != 'type'){
	$id = intval($_GET['id']);
	$str_content = edit_emails($id);		
}else{
	$str_content = view_emails();
}
	
	
	
	function edit_emails($id){
		global $db, $tpl;
		
		$rows = $db->get_results("SELECT * FROM ".$db->tables['email_event_type']." ORDER BY event ", ARRAY_A);
		if(!empty($db->last_error)){ return db_error($db->last_error); }
		if($db->num_rows == 0){
			return error_not_found();
		}

		$tpl->assign("email_types",$rows);
		
		if($id > 0){
			$sql = "SELECT e.*,
				(SELECT `value` 
				FROM ".$db->tables['option_values']." 
				WHERE id_option = '0' 
				AND id_product = '".$id."' 
				AND where_placed = 'email_event_sms'
				) as sms,
				(SELECT `value` 
				FROM ".$db->tables['option_values']." 
				WHERE id_option = '0' 
				AND id_product = '".$id."' 
				AND where_placed = 'email_event_phone'
				) as sms_phone
				FROM ".$db->tables['email_event']." e 
				
				WHERE e.id = '".$id."' 
			";
			$row = $db->get_row($sql, ARRAY_A);
			if($db->num_rows == 0){
				return error_not_found();
			}
			
			
		}else{
			$row = array(
				'event_type_id' => 0,
				'site' => 0,
				'title' => '',
				'content' => '',
				'to_user' => 0,
				'to_admin' => 0,
				'to_extra' => '',
				'subject' => '',
				'body' => '',
				'template' => 0,
				'is_html' => 1,
				'active' => 1,
				'sms' => 0,
				'phone' => ''
			);
		}
		$tpl->assign("row",$row);
		
		if(isset($_POST['save'])){
			update_emails($id);
		}elseif(isset($_POST['delete'])){
			delete_emails($id);			
		}
					
		return $tpl->display("settings/emails_edit.html");
	}
	
	
	
  /* ok */
  function view_emails(){
    global $tpl, $db;

	$sql = "SELECT e.*, et.title as type_title, et.event as type_event  			
		FROM ".$db->tables['email_event']." e 
		LEFT JOIN ".$db->tables['email_event_type']." et ON (e.event_type_id = et.id) ";

	if(isset($_GET['site_id']) && isset($_GET['type'])){
		$sql .= " WHERE `site` = '".intval($_GET['site_id'])."' 
			AND `event_type_id` = '".intval($_GET['type'])."' ";
	}elseif(isset($_GET['site_id'])){
		$sql .= " WHERE `site` = '".intval($_GET['site_id'])."' ";
	}elseif(isset($_GET['type'])){
		$sql .= " WHERE `event_type_id` = '".intval($_GET['type'])."' ";		
	}
	
	$sql .= " ORDER BY et.`event`, et.title ";
	$rows = $db->get_results($sql, ARRAY_A);
	$tpl->assign("emails",$rows);
	
	$rows = $db->get_results("SELECT e.*, 
			(SELECT count(*) FROM ".$db->tables['email_event']." 
			WHERE event_type_id = e.id) as qty 
		FROM ".$db->tables['email_event_type']." e 
		ORDER BY e.`event` ", ARRAY_A);
	$tpl->assign("email_types",$rows);
	
	$href = "?action=emails";
	$href_no_type = $href;
	if(isset($_GET['id'])){ 
		$href .= "&id=".$_GET['id']; 
		$href_no_type = $href;
	}
	if(isset($_GET['type'])){ $href .= "&type=".$_GET['type']; }
	$tpl->assign("href",$href);
	$tpl->assign("href_no_type",$href_no_type);
    return $tpl->display("settings/emails_list.html");
  }
  
  
  
  function delete_emails($id){
	  global $db;
	  $sql = "DELETE FROM ".$db->tables['email_event']." WHERE id = '".$id."' ";
	  $db->query($sql);
	  
	  $sql = "DELETE FROM ".$db->tables['option_values']." 
		WHERE 
			`id_option` = '0' 
			AND `id_product` = '".$id."' 
			AND 
			(`where_placed` = 'email_event_sms' 
			OR `where_placed` = 'email_event_phone')
		";
	  $db->query($sql);
	  
	  clear_cache(0);
	  $url = "?action=emails&deleted=1";
	  header("Location: ".$url);
	  exit;
  }

  /* ok */
  function update_emails($id){
    global $tpl, $db, $site_vars, $admin_vars;

	$event_type_id = !empty($_POST['e']['event_type_id']) ? intval($_POST['e']['event_type_id']) : 0;
	$site = !empty($_POST['e']['site']) ? intval($_POST['e']['site']) : 0;
	$title = !empty($_POST['e']['title']) ? trim($_POST['e']['title']) : '';
	$content = !empty($_POST['e']['content']) ? trim($_POST['e']['content']) : '';
	$to_user =  !empty($_POST['e']['to_user']) ? 1 : 0;
	$to_admin = !empty($_POST['e']['to_admin']) ? 1 : 0; 
	$to_extra = !empty($_POST['e']['to_extra']) ? trim($_POST['e']['to_extra']) : '';
	$subject = !empty($_POST['e']['subject']) ? trim($_POST['e']['subject']) : '';
	$body = !empty($_POST['e']['body']) ? trim($_POST['e']['body']) : '';
	$template = !empty($_POST['e']['template']) ? intval($_POST['e']['template']) : 0;
	$is_html = !empty($_POST['e']['is_html']) ? 1 : 0;
	$active = !empty($_POST['e']['active']) ? 1 : 0;

	$sms = !empty($_POST['e']['sms']) ? 1 : 0;
	$sms_phone = !empty($_POST['e']['sms_phone']) ? trim($_POST['e']['sms_phone']) : '';
	$sms_phone = str_replace(' ', '', $sms_phone);
	
	if($id > 0){
		$sql = "UPDATE ".$db->tables['email_event']." SET 
			event_type_id = '".$event_type_id."', 
			site = '".$site."', 
			title = '".$db->escape($title)."', 
			content = '".$db->escape($content)."', 
			to_user = '".$to_user."', 
			to_admin = '".$to_admin."', 
			to_extra = '".$db->escape($to_extra)."',  
			subject = '".$db->escape($subject)."',  
			body = '".$db->escape($body)."',  
			template = '".$template."',  
			is_html = '".$is_html."',  
			active = '".$active."',
			date_updated = '".date('Y-m-d H:i:s')."' 
			
			WHERE id = '".$id."'
		";
		$db->query($sql);
		$new_id = $id;
		$url = $new_id.'&updated=1';
		
		/* delete old SMS records to add new ones */
		$sql = "DELETE FROM ".$db->tables['option_values']." 
			WHERE 
			`id_option` = '0' 
			AND `id_product` = '".$id."' 
			AND 
			(`where_placed` = 'email_event_sms' 
			OR `where_placed` = 'email_event_phone')
		";
		$db->query($sql);
		
	}else{
		$sql = "INSERT INTO ".$db->tables['email_event']." 
			(event_type_id, site, title, content, to_user, to_admin, 
			to_extra, subject, body, template, is_html, active, date_added) 
			VALUES 
			('".$event_type_id."', 
			'".$site."', 
			'".$db->escape($title)."', 
			'".$db->escape($content)."', 
			'".$to_user."', 
			'".$to_admin."', 
			'".$db->escape($to_extra)."',  
			'".$db->escape($subject)."',  
			'".$db->escape($body)."',  
			'".$template."',  
			'".$is_html."',  
			'".$active."',
			'".date('Y-m-d H:i:s')."' ) 			
		";
		$db->query($sql);
		$new_id = $db->insert_id;
		$url = $new_id.'&added=1';
	}
	
	/* add SMS record for email_event */
	$sql = "INSERT INTO ".$db->tables['option_values']." 
		(id_option, id_product, value, 
		where_placed, value2, value3) VALUES 
		('0', '".$new_id."', '".$sms."', 
		'email_event_sms', '',''
		)
	";
	$db->query($sql);
		
	$sql = "INSERT INTO ".$db->tables['option_values']." 
		(id_option, id_product, value, 
		where_placed, value2, value3) VALUES 
		('0', '".$new_id."', '".$sms_phone."', 
		'email_event_phone', '',''
		)
	";
	$db->query($sql);
	/* end of adding SMS records */
	
	clear_cache(0);	
    header("Location: ?action=emails&id=".$url);
    exit;
  }

  
  
function edit_emails_type($id){
	global $db, $tpl;
	
	if($id > 0){
	  
		$row = $db->get_row("SELECT * FROM ".$db->tables['email_event_type']." WHERE id = '".$id."' ", ARRAY_A);
		if(!empty($db->last_error)){ return db_error($db->last_error); }
		if($db->num_rows == 0){
			return error_not_found();
		}
		
	}else{
		$row = array('id' => 0, 'event' => '', 'active' => 1, 
			'title' => '', 'content' => '');
	}
	
	
	if(isset($_POST["delete"]) && $id > 0){
		$sql = "DELETE FROM ".$db->tables['email_event_type']." WHERE id = '".$id."' ";
		$db->query($sql);
		$url = "?action=emails&deleted=1";
		header("Location: ".$url);
		exit;
	}
	
	if(isset($_POST["save"])){
		$event = isset($_POST["event"]) ? trim($_POST["event"]) : '';
		$title = isset($_POST["title"]) ? trim($_POST["title"]) : '';
		$active = empty($_POST["active"]) ? '0' : '1';
		$content = isset($_POST["content"]) ? trim($_POST["content"]) : '';
		
		if($id > 0){
			$sql = "UPDATE ".$db->tables['email_event_type']." SET 
					`event` = '".$db->escape($event)."',
					`title` = '".$db->escape($title)."',
					`content` = '".$db->escape($content)."',
					`active` = '".$active."'
					
				WHERE id = '".$id."'			
			";
		}else{
			$sql = "INSERT INTO ".$db->tables['email_event_type']." 
				(`event`, `active`, `title`, `content`) VALUES (
					'".$db->escape($event)."', '".$active."', 
					'".$db->escape($title)."', 
					'".$db->escape($content)."')";					
		}
		$db->query($sql);
		$new_id = $id == 0 ? $db->insert_id : $id;
		clear_cache(0);
		$url = "?action=emails&do=type&id=".$new_id;
		if($id > 0){
			$url .= "&updated=1";
		}else{
			$url .= "&added=1";
		}
		
		header("Location: ".$url);
		exit;		
	}

	$tpl->assign("row",$row);	  
    return $tpl->display("settings/emails_edit_type.html");	  
  }
?>