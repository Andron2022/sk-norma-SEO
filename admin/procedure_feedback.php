<?php
/*
  ok simple version
  last updated 20.02.2020 
*/
if(!defined('SIMPLA_ADMIN')){ die(); }

global $tpl, $admin_vars;

if(isset($_GET['id'])){
  if($admin_vars['uri']['id'] == 0){
    $str_content = clear_email();
  }else{
    $str_content = edit_fb($admin_vars['uri']['id']);
  }
}else{
  $str_content = list_fb();
}

/* ok */
function edit_fb($id){
	global $db, $tpl, $lang, $site_vars, $site;

	$row = $db->get_row("SELECT 
				f.*, 
				s.name_short as site, 
				s.site_url, 
				c.id2 as visit_log_id,
				m.`id` as manager_id, 
				m.`name` as manager_name, 
				m.`login` as manager_login, 
				m.`email` as manager_email
			FROM ".$db->tables['feedback']." f 
			LEFT JOIN ".$db->tables['site_info']." s ON (f.site_id = s.id) 
			LEFT JOIN ".$db->tables['connections']." c ON (f.id = c.id1 AND c.name1 = 'fb' AND c.name2 = 'visit_log')
			LEFT JOIN ".$db->tables['connections']." cm ON (f.id = cm.id1 AND cm.name1 = 'fb' AND cm.name2 = 'manager')
			LEFT JOIN ".$db->tables['users']." m ON (cm.id2 = m.id)
			WHERE f.id = '".$id."' 
    ", ARRAY_A);

	if(!$row || $db->num_rows == 0){
		return error_not_found();    
	}
	
	if(!empty($row['site_url'])){
		$metka = mb_strtolower(substr($row['ticket'],-4), 'utf-8').$row['id'];
		$row['tinyurl'] = $row['site_url'].'/f'.URL_END.$metka;
	}
	
	$sql = "SELECT u.id, u.login, u.`name`, u.email  
		FROM ".$db->tables['users']." u 
		LEFT JOIN ".$db->tables['users_prava']." up ON (u.id = up.bo_userid)
		WHERE u.admin = '1' 
			AND u.active = '1' 
			AND up.feedback = '1'
	";
	$row['managers'] = $db->get_results($sql, ARRAY_A);
	//$db->debug();
	//exit;
		
	/* если есть менеджер, проинформируем */
	if(!empty($row['manager_id']) && $row['manager_id'] != $site->user['id']){
		
	}
	
  if(!empty($_GET['del_comment']) && !empty($site->user['prava']['settings'])){
		$sql = "DELETE FROM ".$db->tables['comments']." 
			WHERE record_type = 'fb' 
			AND record_id = '".$id."' 
			AND id = '".intval($_GET['del_comment'])."' ";
		$db->query($sql);
		delete_uploaded_files(intval($_GET['del_comment']), 'fb_comment');
		register_changes('fb', $id, 'update', 'comment deleted');
		
		
		if(!empty($row['manager_id']) && $row['manager_id'] != $site->user['id']){
			$mes = "Удален комментарий <a href='".$row['tinyurl']."'>по запросу ".$row['ticket']."</a>, где Вы являетесь исполнителем. ";
			$r = prepare_fb_info($id, $mes);
			$r['customer'] = array();
			send_email_event('fb_comment', $r);			
		}
  }

  if(isset($_POST['update']) && isset($_POST['fb_id']) && intval($_POST['fb_id']) > 0 ){
    // меняем статус
	$old = array();
	$old['f_phone'] = isset($row['phone']) ? $row['phone'] : '';
	$old['f_email'] = isset($row['email']) ? $row['email'] : '';
	$old['f_subject'] = isset($row['subject']) ? $row['subject'] : '';
	$old['f_status'] = isset($row['status']) ? intval($row['status']) : 0;

	$new = array();	
	if(isset($_POST["f_phone"])){ $new['f_phone'] = trim($_POST["f_phone"]); }
	if(isset($_POST["f_email"])){ $new['f_email'] = trim($_POST["f_email"]); }
	if(isset($_POST["f_subject"])){ $new['f_subject'] = trim($_POST["f_subject"]); }
	
    $new_status = isset($_POST['f_status']) ? intval($_POST['f_status']) : 0;
	
	$updates = '';
	if($new_status != $old['f_status']){
		$db->query("UPDATE ".$db->tables['feedback']." 
			SET status = '".$new_status."' 
			WHERE id = '".intval($_POST['fb_id'])."' ");
		$updates = 'new status - '.$new_status;
	}
	
	if(!empty($new)){
		foreach($new as $k=>$v){
			if($v != $old[$k]){
				$s = str_replace('f_','',$k);
				$sql = "UPDATE ".$db->tables['feedback']."  
					SET `".$s."` = '".$db->escape($v)."' 
					WHERE id = '".intval($_POST['fb_id'])."' 
				";
				$db->query($sql);
				if(!empty($updates)){ $updates .= '<br>'; }
				$updates .= $s.' changed - '.$v.' [old value: '.$old[$k].']';	
				
				if($s == 'phone'){
					$row['phone'] = $v;
				}				
			}			
		}
	}
    
	if(!empty($updates)){
		if(!empty($row['manager_id']) && $row['manager_id'] != $site->user['id']){
			$mes = "Изменения <a href='".$row['tinyurl']."'>по запросу ".$row['ticket']."</a>, где Вы являетесь исполнителем. <p>".$updates."</p>";
			$r = prepare_fb_info($id, $mes);
			$r['customer'] = array();
			send_email_event('fb_comment', $r);			
		}
		
		register_changes('fb', intval($_POST['fb_id']), 'update', $updates);
		
		/* добавим запись об изменениях в коммент */
		$ip_address = !empty($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : ''; 
		$sql = "INSERT INTO ".$db->tables['comments']." (record_type, 
				record_id, userid, comment_text, ddate, ip_address, 
				unreg_email, active, ext_h1, ext_desc, notify) 
				VALUES('fb', '".$id."', '".$site->user['id']."', 
				'".$db->escape($updates)."', '".date('Y-m-d H:i:s')."', 
				'".$ip_address."', '', '0', '', '', 
				'0')
		";
		$db->query($sql);
		
	}	
        
    if(!empty($_POST['f_update_link']) AND !empty($_POST['f_sent'])){
        $new_sent = date('Y-m-d H:i:s', strtotime($_POST['f_sent'])+10);
        $db->query("UPDATE ".$db->tables['feedback']." SET sent = '".$new_sent."'
            WHERE id = '".intval($_POST['fb_id'])."' ");
		register_changes('fb', intval($_POST['fb_id']), 'update', 'open link changed');

    }        
	
	/* new manager */
	$new_manager = isset($_POST['manager']) 
		? intval($_POST['manager']) : intval($row['manager_id']);
	
	if($new_manager != intval($row['manager_id'])){
		/* менеджер изменился */
		/*запишем данные, удалив старые */
		$sql = "DELETE FROM ".$db->tables['connections']." 
			WHERE id1 = '".$row['id']."' 
			AND name1 = 'fb' 
			AND name2 = 'manager'
		";
		$db->query($sql);
		
		$sql = "INSERT INTO ".$db->tables['connections']." 
			(id1, name1, name2, id2) VALUES 
			('".$row['id']."', 'fb', 'manager', '".$new_manager."')
		";
		$db->query($sql);

		/* запишем коммент про нового исполнителя!!! */
		$str = 'Новый исполнитель: ';
		if(empty($new_manager)){
			$str .= ' - все (не выбран)';
		}else{
			$sql = "SELECT id, login, `name`, email 
				FROM ".$db->tables['users']." 
				WHERE id = '".$new_manager."'
			";
			$u1 = $db->get_row($sql, ARRAY_A);
			if(!$u1 || $db->num_rows == 0){
				$str .= ' unknown (ID = '.$new_manager.')';
			}else{
				$str .= $u1['name'].' ('.$u1['login'].')';
			}
		}
		
		$ip_address = !empty($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : ''; 
		$sql = "INSERT INTO ".$db->tables['comments']." (record_type, 
				record_id, userid, comment_text, ddate, ip_address, 
				unreg_email, active, ext_h1, ext_desc, notify) 
				VALUES('fb', '".$id."', '".$site->user['id']."', 
				'".$db->escape($str)."', '".date('Y-m-d H:i:s')."', 
				'".$ip_address."', '', '0', '', '', 
				'0')
		";
		$db->query($sql);
		
		/* отправим уведомление новому исполнителю */
		if($new_manager > 0 && $new_manager != $site->user['id']){
			$mes = "Вы назначены новым исполнителем <a href='".$row['tinyurl']."'>по запросу ".$row['ticket']."</a>. ";
			$r = prepare_fb_info($id, $mes);
			$r['customer'] = array();
			send_email_event('fb_comment', $r);			
		}
		
		/* отправим уведомление старому исполнителю */
		if(!empty($row['manager_id']) && $row['manager_id'] != $site->user['id']){
			$mes = "Назначен новый исполнитель <a href='".$row['tinyurl']."'>по запросу ".$row['ticket']."</a>, где Вы были исполнителем.";
			$r = prepare_fb_info($id, $mes);
			$r['manager']['name'] = $row['manager_name'];
			$r['manager']['login'] = $row['manager_login'];
			$r['manager']['email'] = $row['manager_email'];
			$r['customer'] = array();
			send_email_event('fb_comment', $r);						
		}
	}
	

	if(!empty($_POST["send_comment"]["message"])){
		
		$o_phone = isset($row['phone']) ? trim($row['phone']) : '';
		$row['spec_id'] = '';
		if(!empty($row['ticket'])){
			$row['spec_id'] = substr($row['ticket'], -4).$row['id'];
			$row['spec_id'] = mb_strtolower($row['spec_id'], 'UTF-8');	
		}
		
		$msg = trim($_POST["send_comment"]["message"]);
		$ip_address = !empty($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '';  
		if(!empty($msg)){
			$active = !empty($_POST["send_comment"]["active"]) ? 1 : 0;
			$notify = !empty($_POST["send_comment"]["notify"]) ? 1 : 0;
			$sms = empty($_POST["send_comment"]["sms"]) ? 0 : 1;
			
			/*
			$sql = "UPDATE ".$db->tables['feedback']." SET 
				status = '1' WHERE id = '".$id."' ";
			$db->query($sql);
			*/
			
			$sql = "INSERT INTO ".$db->tables['comments']." (record_type, 
				record_id, userid, comment_text, ddate, ip_address, 
				unreg_email, active, ext_h1, ext_desc, notify) 
				VALUES('fb', '".$id."', '".$site->user['id']."', 
				'".$db->escape($msg)."', '".date('Y-m-d H:i:s')."', 
				'".$ip_address."', '', '".$active."', '', '', 
				'".$notify."')";
			$db->query($sql);
			if(!empty($db->last_error)){
				return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__);
			}			
			
			$comment_id = $db->insert_id;
			register_changes('fb', $id, 'update', 'added_comment #'.$comment_id);
			upload_files($comment_id, 'fb_comment');

			
			if(!empty($notify)){
				$ar = prepare_fb_info($id, $msg);
				send_email_event('fb_comment', $ar);
			}else{
				$ar = prepare_fb_info($id, $msg);
				$ar['customer'] = array();	
				send_email_event('fb_comment', $ar);
			}
			
			/* если отправка SMS - то обработаем через smsc.ru */
			$o_phone = trim($o_phone);
			$o_phone = preg_replace('~[^0-9]+~','',$o_phone); 			
			
			if(!empty($sms) && !empty($o_phone)){
				/* зададим логин и пароль */
				$sms1 = isset($site->vars['sys_smsc_login']) 
					? $site->vars['sys_smsc_login'] : ''; 
				$sms2 = isset($site->vars['sys_smsc_password'])
					? $site->vars['sys_smsc_password'] : '';
				if (!defined('SMSC_LOGIN')) { define('SMSC_LOGIN', $sms1); }
				if (!defined('SMSC_PASSWORD')) { define('SMSC_PASSWORD', $sms2); } 
				// пароль или MD5-хеш пароля в нижнем регистре
				if(!empty($site->vars['sys_smsc_debug'])
					&& !defined('SMSC_DEBUG')
				){
					define('SMSC_DEBUG', 1);
				}
				
				if($o_phone[0] == 8){
					$o_phone = '7'.substr($o_phone, 1);
				}
								
				$f = MODULE.'/smsc_api.php';
				if(file_exists($f)){
					include_once($f);
					//$msg = str_replace('\n','%0A',$msg);
					$sms_message = $msg.' 
					'.$row['site'];
					
					if(!empty($site->vars['sys_smsc_url'])){
						$sms_message .= '  
						'.$row['site_url'].'/f/'.$row['spec_id'];
					}
					
					$sms_ar = send_sms($o_phone, $sms_message, 0);
					if(!empty($sms_ar) && count($sms_ar) > 2){
						/* отправлено! запишем в историю */
						register_changes('comment', $comment_id, 'sms');
					}
				}				
			}
			
			
			
			header("Location: ?action=feedback&id=".$id."#comment".$comment_id);
			exit;		
		}		
	}
	        
    header("Location: ?action=feedback&id=".intval($_POST['fb_id'])."&updated=1");
    exit;
  }elseif(isset($_POST['del_question']) && isset($_POST['fb_id']) && intval($_POST['fb_id']) > 0 ){
	$id = intval($_POST['fb_id']);
	/* удалим менеджера и отправим сообщение, если он был */
	if(!empty($row['manager_id']) && $row['manager_id'] != $site->user['id']){
		$msg = "Запрос <a href='".$row['tinyurl']."'>".$row['ticket']."</a>, где Вы были исполнителем, удален.";
		$ar = prepare_fb_info($id, $msg);
		$ar['customer'] = array();	
		send_email_event('fb_comment', $ar);
	}
	
	$f_ticket_number = $db->get_var("SELECT * FROM ".$db->tables['feedback']." WHERE id = '".intval($_POST['fb_id'])."' ");
    if($f_ticket_number){
    /*
      $db->query("DELETE FROM ".$db->tables['feedback_answers']." 
          WHERE `ticket` = '".$f_ticket_number."' ");
          */      
    }
	
	/* удалим привязку к избранным */
	$query = "DELETE FROM ".$db->tables["fav"]." WHERE where_id = '".$id."' AND `where_placed` = 'fb' ";
	$db->query($query);	

	/* удалим комменты */
	$query = "DELETE FROM ".$db->tables["comments"]." WHERE record_id = '".$id."' AND `record_type` = 'fb' ";
	$db->query($query);
	delete_uploaded_files($id, 'fb_comment');

	/* удалим записи о визитах и менеджере */
	$query = "DELETE FROM ".$db->tables["connections"]." WHERE name1 = 'fb' 
		AND id1 = '".$id."' ";
	$db->query($query);
	
    $db->query("DELETE FROM ".$db->tables['feedback']." WHERE id = '".$id."' ");
	register_changes('fb', $id, 'delete');
	
    header("Location: ?action=feedback&deleted=1");
    exit;
  }

  $row["date"] = date($site_vars['site_date_format']." ".$site_vars['site_time_format'], strtotime($row["sent"]));
  //feedback/?done=e6978ba8bd494e56805b24f585f9dca1
  $row["open_link"] = $row['site_url'].'/feedback/?done='.md5($row['id'].$row['sent']);
  
	$row['comments'] = $db->get_results("SELECT c.*, 
		u.login as user_login, 
		u.`name` as user_name, 
		
		f.id as file_id, 
		f.size as file_size, 
		f.filename as file_name, 
		f.title as file_title, 
		f.ext as file_ext,
		sms.id as sms
		
	FROM ".$db->tables['comments']." c
	LEFT JOIN ".$db->tables['users']." u on (c.userid = u.id)
	LEFT JOIN ".$db->tables['uploaded_files']." f on (c.id = f.record_id 
		AND f.record_type = 'fb_comment')
	LEFT JOIN ".$db->tables['changes']." sms on (c.id = sms.where_id 
		AND sms.where_changed = 'comment' AND `type_changes` = 'sms')
	WHERE c.record_type = 'fb' AND c.record_id = '".$id."' 
	ORDER BY c.ddate ", ARRAY_A);
	
	//$db->debug();  exit;
	
	if(!empty($row['visit_log_id'])){
		
		$sql = "SELECT * FROM ".$db->tables['visit_log']." WHERE id = '".$row['visit_log_id']."' ";
		$row['visit_log'] = $db->get_row($sql, ARRAY_A);
		
		if(!empty($row['visit_log']['time'])){
			$row['visit_log']['time'] = date($site->vars['site_date_format'].' '.$site->vars['site_time_format'], strtotime($row['visit_log']['time']));			
		}
		
		if(!empty($row['visit_log']['pages_visited'])){
			$row['visit_log']['pages_visited'] = explode(' | ', $row['visit_log']['pages_visited']);
		}
	}
	
	$tpl->assign("ticket", $row);
	return $tpl->display("info/feedback_edit.html");
}

/* ок */
function list_fb(){
  global $db, $tpl, $site, $admin_vars;
  $deleted = 0;
    $get_plus = isset($_GET['cid']) ? "&cid=".intval($_GET['cid']) : "";
    $get_plus .= isset($_GET['q']) ? "&q=".htmlspecialchars($_GET['q']) : "";
  
  
  if(isset($_POST['id']) && count($_POST['id'])>0){
    $i = 0; $url = "";
    if(isset($_POST['set_read'])){
      foreach($_POST['id'] as $_id){
        $db->query("UPDATE ".$db->tables['feedback']." SET `status` = '1' WHERE id = '".$_id."' LIMIT 1;");
      }
      header("Location: ?action=feedback".$get_plus."&updated=1");
      exit;
    }
    if(isset($_POST['delete'])){
      foreach($_POST['id'] as $_id){
		  
			/* удалим менеджера и отправим сообщение, если он был */
			$ar = prepare_fb_info($_id);
			if(!empty($ar['manager_id']) && $ar['manager_id'] != $site->user['id']){
				$ar['message'] = "Удален запрос <a href='".$ar['tinyurl']."'>".$ar['ticket']."</a>, где Вы были исполнителем.";
				$ar['customer'] = array();	
				send_email_event('fb_comment', $ar);
			}
		  
		    $do = $db->query("DELETE 
				FROM ".$db->tables['feedback']." 
				WHERE id = '".$_id."' ");
			
			/* удалим комменты */
			$sql = "DELETE FROM ".$db->tables['comments']." 
				WHERE record_type = 'fb' AND record_id = '".$_id."' ";
			$db->query($sql);
			delete_uploaded_files($_id, 'fb_comment');
			
			/* удалим привязку к избранным */
			$query = "DELETE FROM ".$db->tables["fav"]." 
				WHERE where_id = '".$_id."' AND `where_placed` = 'fb' ";
			$db->query($query);
			
			/* удалим записи о визитах */
			$query = "DELETE FROM ".$db->tables["connections"]." 
				WHERE name1 = 'fb' AND id1 = '".$_id."' ";
			$db->query($query);
	
		    $i++;
      }
      header("Location: ?action=feedback".$get_plus."&deleted=".$i);
      exit;
    }
  }

    $all_sites = $db->get_results("SELECT id, name_short as title, site_url as url 
        FROM ".$db->tables['site_info']." ORDER BY name_short, id ", ARRAY_A); 
    $tpl->assign('all_sites', $all_sites);


    $str_where = !empty($_GET['site_id']) ? " WHERE `site_id` = '".intval($_GET['site_id'])."' " : "";
	$query = "SELECT 
			f.*, s.site_url, 
			c.id2 as visit_log_id, 
			IFNULL(m.`id`,0) as manager_id, 
			m.`name` as manager_name, 
			m.`login` as manager_login, 
			m.`email` as manager_email, 
				
			IF(
				
					(SELECT `sort` 
					FROM ".$db->tables['fav']." 
					WHERE `where_placed` = 'fb' 
					AND `where_id` = f.id 
					AND `user_id` = '".$admin_vars['bo_user']['id']."') 
				> '0', 1, 0) as fav,
				
			(SELECT COUNT(*) 
				FROM ".$db->tables['comments']." 
				WHERE `record_type` = 'fb' 
				AND `record_id` = f.id
			) as comments	
			
		FROM ".$db->tables['feedback']." f
		LEFT JOIN ".$db->tables['site_info']." s ON (f.site_id = s.id) 
		LEFT JOIN ".$db->tables['connections']." c ON 
			(f.id = c.id1 
			AND c.name1 = 'fb' 
			AND c.name2 = 'visit_log') 
		LEFT JOIN ".$db->tables['connections']." cm ON (f.id = cm.id1 AND cm.name1 = 'fb' AND cm.name2 = 'manager')
		LEFT JOIN ".$db->tables['users']." m ON (cm.id2 = m.id)
		WHERE f.id > '0' 
	";
	
	if(!empty($_GET['site_id'])){
		$query .= " AND f.site_id = '".intval($_GET['site_id'])."' 
		";
	}
	
	if(!empty($_GET['q'])){
		$q = mb_strtoupper($_GET['q'], 'UTF-8');
		$query .= " AND (
			UPPER(f.hidden) LIKE '%".$db->escape($q)."%' OR
			UPPER(f.message) LIKE '%".$db->escape($q)."%' OR
			UPPER(f.phone) LIKE '%".$db->escape($q)."%' OR
			UPPER(f.email) LIKE '%".$db->escape($q)."%' OR
			UPPER(f.ticket) LIKE '%".$db->escape($q)."%' OR
			UPPER(f.name) LIKE '%".$db->escape($q)."%' OR
			UPPER(f.sent) LIKE '%".$db->escape($q)."%' 
		)
	";
	}else{	
	
		$query .= " AND (m.id = '0' 
			OR m.id = '".$site->user['id']."' 
			OR m.id IS NULL 
			OR (SELECT `user_id` 
					FROM ".$db->tables['fav']." 
					WHERE `where_placed` = 'fb' 
					AND `where_id` = f.id 
					AND `user_id` = '".$admin_vars['bo_user']['id']."') = '".$admin_vars['bo_user']['id']."'
		)
		";		
	}
	
	$query .= " 
		ORDER BY fav DESC, f.`status`, f.`sent` DESC 
	";
	$rows = $db->get_results($query);
	//$db->debug(); exit;
	// PAGE LIMITS
	if(isset($_GET["page"])){ $page = $_GET["page"]; } else { $page = 0; }
	$on_Page = ONPAGE;
	$limit_Start = $page*$on_Page; // for pages generation
	$limit = "limit $limit_Start, $on_Page";

	$all_results = $db->num_rows;
	$tpl->assign("pages",_pages($all_results, $page, $on_Page,true));

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
			else { $strPages .= " [<a href=./?action=feedback&page=$i>$start1-$start2</a>] "; }
		}
	}

	$rows = $db->get_results($query, ARRAY_A);
	if($db->num_rows == 0){
  	$tpl->assign("site",$site->vars);
  	$tpl->assign("feedback_list",array());
  	return $tpl->display("info/feedback_list.html");
	}

  	$tpl->assign("site",$site->vars);
	$tpl->assign("pages", $strPages);
	$tpl->assign("feedback_list",$rows);
	return $tpl->display("info/feedback_list.html");
}

?>