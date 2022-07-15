<?php
/*
  ok
  last updated 04.08.2015
*/
if(!defined('SIMPLA_ADMIN')){ die(); }
  global $admin_vars;
  $id = $admin_vars['uri']['id'];
  $do = $admin_vars['uri']['do'];

    $str_content = view_favorites();


  /* ok */
  function view_favorites(){
    global $tpl, $db;
    if(!empty($_POST['fav'])){ update_favorites(); }
    $tpl->assign("fav",_get_favorites());
	
	if(!empty($_GET['delete']) && intval($_GET['delete']) > 0){
		$sql = "DELETE FROM ".$db->tables['fav']." WHERE id = '".intval($_GET['delete'])."' ";
		$db->query($sql);
		header("Location: ?action=fav");
		exit;
	}

	if(!empty($_GET['all']) && intval($_GET['all']) > 0){
		$id = intval($_GET['all']);
		$sql = "SELECT u.id, f.id as fav_id 
			FROM ".$db->tables['users']." u 
			LEFT JOIN ".$db->tables['fav']." f ON ('".$id."' = f.id AND f.user_id = u.id)
			WHERE u.admin = '1' AND u.active = '1' ";
		$rows = $db->get_results($sql);
	    if(!empty($db->last_error)){ return db_error(__FILE__.'<br>Row: '.__LINE__); }

		if($db->num_rows > 0){
			$to_copy = $db->get_row("SELECT * FROM ".$db->tables['fav']." WHERE id = '".$id."' ");
			if(empty($to_copy)){ return error_not_found("The record not found"); }

			foreach($rows as $row){
				if(empty($row->fav_id)){
					/* adding */
					$sql = "INSERT INTO ".$db->tables['fav']." 
						(`where_placed`, `where_id`, `user_id`, `sort`, 
						`title`, `comment`) VALUES
						('".$to_copy->where_placed."', 
						'".$to_copy->where_id."', 
						'".$row->id."', 
						'".$to_copy->sort."', 
						'".$to_copy->title."', 
						'".$to_copy->comment."') 
						";
					$db->query($sql);					
				}
			}
			
			$url = "?action=fav&added=1";
			header("Location: ".$url);
			exit;
		}
		
	}
	
    return $tpl->display("pages/fav_edit.html");
  }

  /* ok */
  function update_favorites(){
    global $tpl, $db, $site_vars, $admin_vars;
    $userid = $admin_vars['bo_user']['id'];

    foreach($_POST['fav'] as $k => $v){
        $title = isset($v['title']) ? trim($v['title']) : '';
        $comment = isset($v['comment']) ? trim($v['comment']) : '';
        $sort = isset($v['sort']) ? trim($v['sort']) : '';

        $sql = "UPDATE ".$db->tables['fav']." SET 
            `title` = '".$db->escape($title)."',
            `comment` = '".$db->escape($comment)."', 
            `sort` = '".$db->escape($sort)."' 
            WHERE id = '".$k."' AND user_id = '".$userid."'
        ";
        $db->query($sql);
    }
    
    //echo $userid.' - ok'; exit;
    header("Location: ?action=fav&updated=1");
    exit;
  }

?>