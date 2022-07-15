<?php
	if (!class_exists('Site')) { return; }

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
    *
    ***********************/ 

    class Feedback extends Site {

        function __construct()
        {
        }
                
        static public function get_feedback($site)
        {
            global $db, $tpl;
            $tpl_page = 'pages/feedback.html';
            $ar = GetEmptyPageArray();
            $ar['page'] = $tpl_page;
            $site->uri['page'] = $tpl_page;

            if(!empty($_POST['fb'])){
				
				$str = check_captcha($site);
				if($str == 'skip'){
					/* подозрение на спам, симулируем отправку */
			        $redirect = isset($_POST['fb']['redirect']) ? $_POST['fb']['redirect']: '';
					$url = $site->vars['site_url'].'/feedback'.URL_END.'?done='.md5(101);
					$url_redirect = !empty($redirect) ? $redirect : $url.'&sent=1';
					return $site->redirect($url_redirect);
			
				}elseif(!empty($str)){
					$ar['error'] = $str;
				}else{
					return register_feedback($site);    
				}
            }
            
            $details = !empty($site->uri['params']['done']) 
                ? '<a href="'.$site->uri['site'].$site->uri['path'].'?done='.$site->uri['params']['done'].'">'.$site->GetMessage('feedback', 'message').'</a>'
                : ''; 

            /*
            $site->page['content'] = sprintf($site->GetMessage('order', 'content'), '<b>'.$row['id'].'</b>');
            */
			$tpl->assign("site", $site->vars);
			$tpl->assign("user", $site->user);
			
			/* if NO GET - check for uri with o/ */
			if(empty($_GET['done']) && !empty($site->uri['alias']) 
				&& strlen($site->uri['alias']) > 6 
				&& substr($site->uri['alias'], 0, 2) == 'f/'
			){
				$done1 = str_replace('f/', '', $site->uri['alias']);
			}
			
            if(!empty($site->uri['params']['sent'])){
				if(!empty($site->uri['params']['done'])){
					$old = $db->cache_queries;
					$db->cache_queries = false;
					$sql = "SELECT f.* 
						FROM ".$db->tables['feedback']." f
                        WHERE   
					";
					
					if(!empty($done1)){
						$ticket_part = substr($done1, 0, 4);
						$ticket_id = str_replace($ticket_part, '', $done1);
                        $sql .= " f.id = '".$db->escape($ticket_id)."' 
							AND f.ticket LIKE '%".$db->escape($ticket_part)."' 
						";
					}else{
                        $sql .= " MD5(concat(f.id,f.sent)) = '".$db->escape($site->uri['params']['done'])."' 
						";
					}

					$ar['feedback'] = $db->get_row($sql, ARRAY_A);
					$db->cache_queries = $old;
                    if(!empty($db->last_error)){ 
						return $site->db_error(basename(__FILE__).": ".__LINE__); 
					}
					//$db->debug(); exit;
				}
				
                $ar['title'] = $site->GetMessage('feedback', 'sent', 'title');
                $ar['metatitle'] = $site->GetMessage('feedback', 'sent', 'metatitle');
                $ar['content'] = sprintf($site->GetMessage('feedback', 'sent', 'content'), $details);
                $ar['skip_sidebar'] = '1';
				
            }else{				
                $ar['title'] = $site->GetMessage('feedback', 'new', 'title');
                $ar['metatitle'] = $site->GetMessage('feedback', 'new', 'metatitle');
                $ar['content'] = $site->GetMessage('feedback', 'new', 'content');
                
                if(!empty($site->uri['params']['done']) || !empty($done1)){
                    $ar['skip_sidebar'] = '1';
					$old = $db->cache_queries;
					$db->cache_queries = false;
                    $sql = " SELECT f.* FROM ".$db->tables['feedback']." f 
								WHERE  
					";
							
					if(!empty($done1)){
						$ticket_part = substr($done1, 0, 4);
						$ticket_id = str_replace($ticket_part, '', $done1);
                        $sql .= " f.id = '".$db->escape($ticket_id)."' 
							AND f.ticket LIKE '%".$db->escape($ticket_part)."' 
						";
					}else{
                        $sql .= " MD5(concat(f.id,f.sent)) = '".$db->escape($site->uri['params']['done'])."' 
						";
					}
					$sql .= " AND f.status > '-1' ";
					$row = $db->get_row($sql, ARRAY_A);		
					//$db->debug(); exit;		
					$db->cache_queries = $old;
                    if(!empty($db->last_error)){ return $site->db_error(basename(__FILE__).": 53"); }
                    if($db->num_rows == 0){
                        $site->page['title'] = $site->GetMessage('feedback', 'ticket_not_found_title');
                        return $site->error_message($site->GetMessage('feedback', 'ticket_not_found'));
                    }
                    
                    $row['date'] = date($site->vars['site_date_format'], strtotime($row['sent'])); 
                    $row['date'] .= ' '.date($site->vars['site_time_format'], strtotime($row['sent'])); 

                    $ar['feedback'] = $row;
                    
                    $ar['title'] = sprintf($site->GetMessage('feedback', 'view', 'title'), '<b>'.$row['ticket'].'</b>');
                    $ar['metatitle'] = sprintf($site->GetMessage('feedback', 'view', 'metatitle'), $row['ticket']);
                    $ar['content'] = nl2br($row['message']);
					$ar['ticket'] = $row['ticket'];
					
					$ar['feedback']['extra_fields'] = array();
					if(!empty($row['extras'])){
						$str4 = $row['extras'];
						$str4 = str_replace('<ul>','',$str4);
						$str4 = str_replace('</ul>','',$str4);
						$ar4 = explode('<li>',$str4);
						if(!empty($ar4)){
							foreach($ar4 as $v){
								$v = strip_tags($v);
								$v2 = explode(':',$v);
								if(!empty($v2[0]) 
									&& !empty($v2[1])
								){
									$f1 = trim($v2[0]);
									$f2 = trim($v2[1]);
									if(!isset($ar['feedback']['extra_fields'][$f1])){
										$ar['feedback']['extra_fields'][$f1] = $f2;
									}									
								}
							}
						}						
					}
					
					$old = $db->cache_queries;
					$db->cache_queries = false;
					
					$ar['comments'] = $db->get_results("SELECT c.*, 
							u.name as user_name, 
							u.login as user_login, 
							f.id as file_id, 
							md5(f.id) as file_md5, 
							f.size as file_size, 
							f.filename as file_name, 
							f.title as file_title, 
							f.ext as file_ext 
						FROM ".$db->tables['comments']." c 
						LEFT JOIN ".$db->tables['users']." u ON (c.userid = u.id) 
						LEFT JOIN ".$db->tables['uploaded_files']." f on (c.id = f.record_id AND f.record_type = 'fb_comment')
						WHERE c.record_type = 'fb' 
						AND c.record_id = '".$row['id']."' 
						AND c.active = '1' 
						ORDER BY c.ddate
						", ARRAY_A);
										
					$db->cache_queries = $old;
					//$db->debug(); exit;	
                }                
            }
            
            return $ar;        
        }
        
    }
    
    function register_feedback($site)
    {		
        global $db, $tpl;
        $when = isset($_POST['fb']['when']) ? $_POST['fb']['when'] : '';
        if(!$site->check_form_key($when)){
            return $site->error_page(404, 'Sent wrong form parameters');
        }
		
		/* сохраним отправленную форму */
        $message = !empty($_POST['fb']['message']) ? nl2br(trim($_POST['fb']['message'])) : '';
        $name = !empty($_POST['fb']['name']) ? trim($_POST['fb']['name']) : '';
        $email = !empty($_POST['fb']['email']) ? trim($_POST['fb']['email']) : '';
        $phone = !empty($_POST['fb']['phone']) ? trim($_POST['fb']['phone']) : '';
		$ph_search = array('+', '(', ')' ,'-', ' ');
		$phone = str_replace($ph_search, '', $phone);
        $type = !empty($_POST['fb']['type']) ? trim($_POST['fb']['type']) : '';
        $subject = !empty($_POST['fb']['subject']) ? trim($_POST['fb']['subject']) : '';
        $from_page = isset($_POST['fb']['from_page']) ? $_POST['fb']['from_page']: '';
        $record_type = isset($_POST['fb']['record_type']) ? $_POST['fb']['record_type']: '';
        $record_id = isset($_POST['fb']['record_id']) ? $_POST['fb']['record_id']: 0;
        $redirect = isset($_POST['fb']['redirect']) ? $_POST['fb']['redirect']: '';
		$post_active = isset($_POST['fb']['active']) ? intval($_POST['fb']['active']): '0';
		
        $ticket = create_ticket();
        $hidden = "";
		$ip_address = "";
        if(!empty($_SERVER["REMOTE_ADDR"])){ 
			$hidden .= "IP - ".$_SERVER["REMOTE_ADDR"]; 
			$ip_address = $_SERVER["REMOTE_ADDR"];
		}
        if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) { 
			$hidden .= "<br>PROXY - ".$_SERVER["HTTP_X_FORWARDED_FOR"]; 
			$ip_address .= '/'.$_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		
        $ar = $_POST['fb'];
	
        unset($ar['when']);
        unset($ar['name']);
        unset($ar['email']);
        unset($ar['message']);
        unset($ar['type']);
		unset($ar['active']);
        unset($ar['phone']);
        unset($ar['subject']);
        unset($ar['from_page']);
        unset($ar['ticket']);
        unset($ar['feedback']);
        unset($ar['redirect']);
        unset($ar['record_type']);
        unset($ar['record_id']);
        $html = '';
        if(!empty($ar)){
            $html .= '<ul>';
            foreach($ar as $k => $v){
				if(is_array($v)){
					$v = implode(' / ', $v);
				}
                $html .= '<li><b>'.$k.'</b>: '.$v.'</li>';
            }
            $html .= '</ul>';
        }
        $created = date('Y-m-d H:i:s');    
		$tpl->assign('site', $site->vars);

		if($type == 'comment'){
			
			if(empty($record_type) OR empty($record_id)){
				//return 'недостаточно данных';
				return $site->error_page(404, 'Sent wrong form parameters');
			}
			$r = array();	
			$userid = empty($site->user['id']) ? 0 : $site->user['id'];
			if($userid > 0){
				$old = $db->cache_queries;
				$db->cache_queries = false;
				$u = $db->get_row("SELECT * 
					FROM ".$db->tables['users']." 
					WHERE id = '".$userid."' ", ARRAY_A);
				$db->cache_queries = $old;
				if($db->num_rows == 0){ 
					$userid = 0; 
				}else{
					$name = $u['name'];
					$email = $u['email']; 
				}
			}
			$message .= $html;
			
			$sql = "INSERT INTO ".$db->tables['comments']." (record_type, 
				record_id, userid, comment_text, ddate, ip_address, 
				unreg_email, active, ext_h1) 
				VALUES('".$db->escape($record_type)."', 
				'".$db->escape($record_id)."', '".$userid."', 
				'".$db->escape($message)."', '".$created."', 
				'".$db->escape($ip_address)."', '".$db->escape($email)."',
				'".$post_active."', '".$db->escape($name)."') ";

			$db->query($sql);
			if(!empty($db->last_error)){ return $site->db_error(basename(__FILE__).": 143"); }
			$id = $db->insert_id;
			$ticket = $id;
			//$db->debug(); exit;
			if($record_type == 'fb'){				
				$txt = $site->GetMessage('words', 'added_fb_comment');				
				$r = prepare_fb_info($record_id, $message);
				$event = 'fb_comment';
			}elseif($record_type == 'order'){
				$txt = $site->GetMessage('words', 'added_order_comment');				
				$r = prepare_order_info($record_id, $message);
				$event = 'order_comment';
			}elseif($record_type == 'categ'){
				$txt = $site->GetMessage('words', 'added_categ_comment');				
				$r = prepare_categ_info($record_id, $message);
				$event = 'page_comment';
			}elseif($record_type == 'pub'){
				$txt = $site->GetMessage('words', 'added_pub_comment');				
				$r = prepare_pub_info($record_id, $message);
				$event = 'page_comment';
			}elseif($record_type == 'product'){
				$txt = $site->GetMessage('words', 'added_product_comment');				
				$r = prepare_product_info($record_id, $message);
				$event = 'page_comment';
			}elseif($record_type == 'comment'){
				$txt = $site->GetMessage('words', 'added_comment_comment');				
				$r = prepare_comment_info($record_id, $message);
				$event = 'page_comment';
			}else{
				/* unknown data */
				$txt = $site->GetMessage('words', 'added_fb_comment');
				$event = 'page_comment';
			}
		
			$r['info'] = $txt;
			if(empty($r['customer'])){
				$r['customer'] = array(
					'name' => $name,
					'email' => $email
				);
			}			
			
			/* отправим уведомление админу */
			if(!isset($site->vars['site_date_format'])){ $site->vars['site_date_format'] = 'd/m/Y'; }
			if(!isset($site->vars['site_time_format'])){ $site->vars['site_time_format'] = 'H:i:s'; }			
			$r['date'] = date($site->vars['site_date_format'].' '.$site->vars['site_time_format']); 
			$site->send_email_event($event, $r);
			
			$url = $site->uri['requested_full'];
			$url_redirect = empty($site->uri['params']) ? $url.'?sent=1#comment' : $url.'&sent=1#comment';
			
			if(!empty($redirect)){
				$url_redirect = $redirect;
			}
			//$url_redirect = urldecode();
			$mode_to = 'comment';
			
		}else{
			$sql = "INSERT INTO ".$db->tables['feedback']." (`ticket`, `name`, 
				`email`, `phone`, `subject`, `message`, `sent`, `hidden`, 
				`status`, `type`, `site_id`, `from_page`, `extras`) VALUES('".$db->escape($ticket)."', 
				'".$db->escape($name)."', '".$db->escape($email)."', 
				'".$db->escape($phone)."', '".$db->escape($subject)."', 
				'".$db->escape($message)."', '".$created."', 
				'".$db->escape($hidden)."', '0',
				'".$db->escape($type)."', '".$site->vars['id']."', 
				'".$db->escape($from_page)."', '".$db->escape($html)."') ";
			$db->query($sql);
			if(!empty($db->last_error)){ 
				return $site->db_error(basename(__FILE__).": ".__LINE__); 
			}
			$id = $db->insert_id;	
			
			/* отправим уведомление админу */
			$ar = prepare_fb_info($id);
			if(!isset($site->vars['site_date_format'])){ $site->vars['site_date_format'] = 'd/m/Y'; }
			if(!isset($site->vars['site_time_format'])){ $site->vars['site_time_format'] = 'H:i:s'; }
			$ar['date'] = date($site->vars['site_date_format'].' '.$site->vars['site_time_format']); 
			
			$site->send_email_event('fb_new', $ar);
			
			$url = $site->vars['site_url'].'/feedback'.URL_END.'?done='.md5($id.$created);
			if(!empty($redirect)){
				$url_redirect = $redirect;
			}elseif(isset($_POST['fb']['redirect'])){
				$url_redirect = $url;
			}else{
				$url_redirect = $url.'&sent=1';
			}
			
			$mode_to = 'fb';
		}
		
		/* Запишем инфо с сессией */
		$sess_current = session_id();
		$old = $db->cache_queries;
		$db->cache_queries = false;
		$sql = "SELECT id FROM ".$db->tables['visit_log']." 
			WHERE `sess` = '".$db->escape($sess_current)."' AND `referer` <> 'search' ";
		$visit_id = $db->get_var($sql);
		$db->cache_queries = $old;
		if(!empty($visit_id)){
			$sql = "INSERT INTO ".$db->tables['connections']."
				(id1, name1, id2, name2) 
				VALUES ('".$id."', '".$mode_to."', '".$visit_id."', 'visit_log')
			";
			$db->query($sql);
		}
        /* переадресация */
		return $site->redirect($url_redirect);
    }
    
    function create_ticket()
    {
        $letters = 
        $str = date('Y').'-'.date('m').'-';
        $str .= generate_str().'-';
        $str .= generate_str().'-';
        $str .= generate_str();
        return $str;
    }
    
    function generate_str($length = 4){
        $chars = 'ABCDEFGHJKLMNQPRSTUVXYZ';
        $numChars = strlen($chars);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($chars, rand(1, $numChars) - 1, 1);
        }
        return $string;
    }
	
	function check_sent_feedback($site){
		if(!empty($_POST['fb'])){
			$site->page = call_user_func('feedback::get_feedback', $site);
			return $site->page;
		}
	}
	
	/* check captcha */
	function check_captcha($site){
		/* Если отправлен код фидбэка и пользователь авторизован, 
		то без капчи обходимся */
		if(!empty($site->user['id']) && isset($_POST['fb']['ticket'])){
			return ;
		}
		
		/* капча не задана в настройках или сброшена */
		
		if(empty($site->vars['sys_captcha']) || (!empty($site->vars['sys_captcha']) && !empty($_POST['skip_captcha']))){
			$ttime = time();
			if (!empty($_POST['skip_captcha']) && is_numeric($_POST['skip_captcha'])) {
				$diff = $ttime - $_POST['skip_captcha'];
				if($diff > 20 && $diff < 21600){
					/* time metka OK */
					return;
				}else{
					return 'skip';
				}
			} else {
				return 'skip';
			}
			return;
		}
		
		//if(!isset($_POST['captcha_name']) || !isset($_POST['keystring'])){ return; }		
		$name = isset($_POST['captcha_name']) ? $_POST['captcha_name'] : '';
		if(empty($_POST['keystring'])){
			unset($_SESSION['captcha'][$name]);
			return $site->GetMessage('feedback', 'empty_captcha');
		}
		
		if (!isset($captcha)) {
			include_once(MODULE.'/captcha/captcha.php');
			$captcha = new captcha($name);
		}
		
		if($captcha->check($_POST['keystring'])){
			unset($_SESSION['captcha'][$name]);
			$_SESSION['captcha'] = array();
		}else{
			unset($_SESSION['captcha'][$name]);
			$_SESSION['captcha'] = array();
			return $site->GetMessage('feedback', 'error_captcha');
		}
		return;
	}
	
	function random_string($len){
		$chars = "";
		$str = "";
		for($i = ord('a'); $i <= ord('z'); $i++) $chars .= chr($i);
		for($i = ord('A'); $i <= ord('Z'); $i++) $chars .= chr($i);
		for($i = ord('0'); $i <= ord('9'); $i++) $chars .= chr($i);		

		for($i = 0; $i < $len; $i++)
		{
			if(function_exists("mt_rand")) $index = mt_rand(0,strlen($chars)-1);
			else $index = rand(0,strlen($chars)-1);
			$str .= $chars{$index};
		}
		return $str;
	}
	

?>