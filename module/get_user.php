<?php
	if (!class_exists('Site')) { return; }

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
    *
    ***********************/ 

    class User extends Site {

        function __construct()
        {
        }
                
        static public function get_user($site)
        {
			if(defined('SI_CORE'))
				return;
			
            global $db, $tpl;
			// [SI_USERID] => md5
			// [SI_LOGIN] => md5
			// [SI_PASSWORD] => md5
			if(!isset($_SESSION['SI_USERID']) && isset($_SESSION['BO_USERID'])){
				$_SESSION['SI_USERID'] = md5($_SESSION['BO_USERID']);
			}
			
			if(!isset($_SESSION['SI_LOGIN']) && isset($_SESSION['BO_LOGIN'])){
				$_SESSION['SI_LOGIN'] = md5($_SESSION['BO_LOGIN']);
			}
			
			if(!isset($_SESSION['SI_PASSWORD']) && isset($_SESSION['BO_PASSWORD'])){
				$_SESSION['SI_PASSWORD'] = $_SESSION['BO_PASSWORD'];
			}
			
			
			$r = array('id' => 0); 
			if(!empty($_SESSION['SI_USERID']) && !empty($_SESSION['SI_LOGIN']) && !empty($_SESSION["SI_PASSWORD"])){
				$u_id = $_SESSION['SI_USERID'];
				$u_login = $_SESSION['SI_LOGIN'];
				$u_password = $_SESSION["SI_PASSWORD"];
				
				$old = $db->cache_queries;
				$db->cache_queries = false;
				$r = $db->get_row("SELECT * 
					FROM ".$db->tables['users']." 
					WHERE md5(id) = '".$u_id."' 
					AND md5(login) = '".$u_login."' 
					AND md5(passwd) = '".$u_password."' 
					AND active = '1' 
					", ARRAY_A);
				$db->cache_queries = $old;
				//$db->debug(); exit;	
				if(!$r || $db->num_rows == 0){
					return array('id' => 0);
				}
				
				if($db->num_rows == 1){
					//unset($r['passwd']);
					$r['to_add'] = array(
						'pub' => array(),
						'product' => array()
					);
					
					/* OK */
					$sql = "SELECT c.*, o.where_placed 
						FROM ".$db->tables['option_groups']." o
						LEFT JOIN ".$db->tables['categ_options']." co ON (o.id = co.id_option AND co.`where_placed` = 'categ')
						LEFT JOIN ".$db->tables['categs']." c ON (co.id_categ = c.id)
						WHERE o.`to_show` = 'info' 
							AND c.active = '1' 
						ORDER BY o.`sort`, c.`sort`
					";
					$rows = $db->get_results($sql, ARRAY_A);
					
					if($db->num_rows > 0){
						foreach($rows as $row){
							if($row['shop'] == 1 && $row['where_placed'] == 'product'){
								$r['to_add']['product'][] = $row;
							}else{
								$r['to_add']['pub'][] = $row;
							}
						}
					}
					
				}
				
								
				/* Если новый и старый IP - разные, то реконнектим и предложим ввести пароль */
				$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
				if($r['last_ip'] != $ip){
					return User::skip_login($site, 
						$r['id'], 
						'IP changed (last '.$r['last_ip'].'; now '.$ip.')');
				}
				
				/* Обновим last_login - это время последней активности */
				$r['last_login'] = date('Y-m-d H:i:s');
				$db->query("UPDATE ".$db->tables['users']."  SET 
					last_login = '".$r['last_login']."' 
					WHERE id = '".$r['id']."'
				");				
				
				if(isset($site->vars['site_date_format'])){ $r['date_insert'] = date($site->vars['site_date_format'], strtotime($r['date_insert'])); }
				if(isset($site->vars['site_date_format'])){ $r['last_login'] = date($site->vars['site_date_format'].' '.$site->vars['site_time_format'], strtotime($r['last_login'])); }

				//$r['last_login'] = "2016-09-20 11:01";
				$d1 = time() - strtotime($r['last_login']);
				
				if(function_exists('date_create')){
					$datetime1 = date_create(date('Y-m-d H:i', time()));
					$datetime2 = date_create(date('Y-m-d H:i', strtotime($r['last_login'])));
				}
				
				if(function_exists('date_diff')){
					$interval = date_diff($datetime1, $datetime2);
					$d = array(
						'diff' => $d1,
						'year' => $interval->format('%y'),
						'month' => $interval->format('%m'),
						'day' => $interval->format('%d'),
						'hour' => $interval->format('%h'),
						'min' => $interval->format('%i')					
					);					
				}else{
					$d = array(
						'diff' => $d1,
						'year' => 0,
						'month' => 0,
						'day' => 0,
						'hour' => 0,
						'min' => 0
					);					
				}

				$r['last_login_ago'] = $d; 
				$r['avatar'] = array('mini'=>'', 'small' => '', 'big' => '');
				$md5 = md5($r['id']);
				foreach($r['avatar'] as $k => $v){
					$f = UPLOAD."/avatars/".$k."/".$md5.".jpg";
					$u = "http://".$site->uri['host']."/upload/avatars/".$k."/".$md5.".jpg"; 
					if(file_exists($f)){
						$r['avatar'][$k] = $u;
					}					
				}
				$db->cache_queries = false;
				$r['prava'] = $db->get_results("SELECT * 
					FROM ".$db->tables['users_prava']." 
					WHERE bo_userid = '".$r['id']."' ", ARRAY_A);	
				$db->cache_queries = true;
			}
			
			$tpl->assign('site',$site->vars);
			return $r;
        }
		
		
		static public function forget_password($site)
		{
            global $db, $tpl;
			$ar = array();
			$ar['title'] = $site->GetMessage('user', 'forget_password');
			$ar['page'] = 'user/forget_password.html';
			$ar['metatitle'] = $site->GetMessage('user', 'meta', 'forget_password');
			$ar['description'] = $site->GetMessage('user', 'meta', 'description');
			$ar['keywords'] = $site->GetMessage('user', 'meta', 'keywords');
			$ar['error'] = '';
			
			if(!empty($_POST['login']['email'])){
				$email = trim($_POST['login']['email']);
				$sql = "SELECT u.id, u.`name`, 
							u.`login`, u.`email`, 
							c.id2 as sent_time,
							md5(CONCAT(c.id,c.id1, c.id2)) as md5_key
					FROM ".$db->tables['users']." u
					LEFT JOIN ".$db->tables['connections']." c 
						ON (u.id = c.id1 AND c.name1 = 'user' 
						AND c.name2 = 'new_password')
					WHERE email = '".$db->escape($email)."' 
					AND active = '1' 
					
					";
				$row = $db->get_row($sql);
				if(!$row || $db->num_rows == 0){ 
					$ar['error'] = $site->GetMessage('user', 'errors', 'user_not_found'); // unknown user
					return $ar; 
				}
				
				$diff = 60*60*48;
				/* если не ставились метки, то создадим их */				
				if(empty($row->sent_time) && empty($row->md5_key)){
					$sql = "INSERT INTO ".$db->tables['connections']." 
						(`id1`, `name1`, `id2`, `name2`) 
						VALUES ('".$row->id."', 'user', 
						'".time()."', 'new_password') ";
					$db->query($sql);
				}else{
					/* метка есть, проверим не устарела ли */
					$diff_real = ($diff + $row->sent_time) - time();
					if($diff_real < 0){
						/* метка устарела, ставим новую */
						$sql = "UPDATE ".$db->tables['connections']." SET 
							id2 = '".time()."' 
							WHERE id = '".$row->id."' 
						";
						$db->query($sql);
					}
				}
				$message = '';
				$site->register_changes('user', $row->id, 'newpass_request');
				$ar = prepare_user_info($row->id, $message);
				send_email_event('user_lost_password', $ar);
				$url = $site->vars['site_url'].'/forget_password/?sent=1';
				return $site->redirect($url);
				exit;				
			}else{
				///forget_password/
				//?add=5c8f01c5ae7f7c387186e737cd33f228
				//&key=d929c1c32901c3726932087777b71cdf
				/* проверим ок ли ссылка */
				
				$error = '';
				if(!empty($site->uri['params']['add']) 
					&& !empty($site->uri['params']['key'])){
					
					$add1 = $site->uri['params']['add'];
					$key1 = $site->uri['params']['key'];
					
					$sql = "SELECT u.*, 
							MD5(CONCAT(c.id,c.id1, c.id2)) as md5_key 
						FROM ".$db->tables['users']." u 
						LEFT JOIN ".$db->tables['connections']." c 
							ON (u.id = c.id1 AND c.name1 = 'user' 
							AND c.name2 = 'new_password')
						WHERE 
							MD5(CONCAT(u.id,u.email)) = '".$db->escape($key1)."' 
							AND MD5(CONCAT(c.id,c.id1, c.id2)) = '".$db->escape($add1)."' 
						LIMIT 0,1					
					";
					$row = $db->get_row($sql);
					if(!$row || $db->num_rows == 0){
						$ar['error'] = $site->GetMessage('user', 'errors', 'wrong_params');
					}
					
				}else{
					//$ar['error'] = $site->GetMessage('user', 'errors', 'wrong_params');
				}
				
				
				if(isset($_POST['login']['pass1']) && isset($_POST['login']['pass1'])){
					$pass1 = trim($_POST['login']['pass1']);
					$pass2 = trim($_POST['login']['pass2']);
					
					if(empty($pass1) || strlen($pass1) < 4 || strlen($pass1) > 20){
						$ar['error'] = $site->GetMessage('user', 'errors', 'passw_length');
					}elseif($pass1 != $pass2){
						$ar['error'] = $site->GetMessage('user', 'errors', 'different_passw');
					}elseif(!empty($error)){
						return $ar;
					}else{
						/* все ок, меняем пароль */
						$message = '';
						if(!empty($_POST['login']['send_password'])){
							$message = $site->GetMessage('user', 'password_new');
							$message .= ': '.$pass1;
						}
						
						/* изменим пароль */
						$pass1_crypt = $site->encode_str($pass1);
						$sql = "UPDATE ".$db->tables['users']." SET 
									`passwd` = '".$db->escape($pass1_crypt)."'
								WHERE id = '".$row->id."'
						";
						$db->query($sql);						
						$ar = prepare_user_info($row->id, $message);
						send_email_event('user_password_changed', $ar);	
						$site->register_changes('user', $row->id, 'newpass');

						/* удалим запись о смене пароля */
						$sql = "DELETE FROM ".$db->tables['connections']." 
							WHERE id1 = '".$row->id."' 
								AND name1 = 'user' 
								AND name2 = 'new_password' ";
						$db->query($sql);
						$url = $site->vars['site_url'].'/forget_password/?done=1';
						return $site->redirect($url);
						exit;
					}					
				}
			}
			return $ar;			
		}

		static public function changepassword($site)
		{
            global $db, $tpl;
			$ar = array();
			$ar['title'] = $site->GetMessage('user', 'changepassword');
			$ar['page'] = 'user/changepassword.html';
			$ar['metatitle'] = $site->GetMessage('user', 'meta', 'changepassword');
			$ar['description'] = $site->GetMessage('user', 'meta', 'description');
			$ar['keywords'] = $site->GetMessage('user', 'meta', 'keywords');
			$ar['error'] = '';
			
			$old = !empty($_POST["login"]["passwdOLD"]) ? trim($_POST["login"]["passwdOLD"]) : '';
			$new1 = !empty($_POST["login"]["passwd1"]) ? trim($_POST["login"]["passwd1"]) : '';
			$new2 = !empty($_POST["login"]["passwd2"]) ? trim($_POST["login"]["passwd2"]) : '';

			if(!empty($old) && !empty($new1) && !empty($new2) && !empty($site->user['passwd'])){
				if($new1 == $new2){
					 if(strlen($new1) > 3 && strlen($new1) < 21){
						 $try_passwd = $site->encode_str($old);

						 if($try_passwd == $site->user['passwd']){
							$new_passwd = $site->encode_str($new1);
							$u_id = $_SESSION['SI_USERID'];
							$u_login = $_SESSION['SI_LOGIN'];
							$u_password = $_SESSION["SI_PASSWORD"];	
				
							$sql = "UPDATE ".$db->tables['users']." SET 
								`passwd` = '".$db->escape($new_passwd)."' 
								WHERE md5(id) = '".$u_id."' 
									AND md5(login) = '".$u_login."' 
									AND md5(passwd) = '".$u_password."' 
									AND active = '1'
							";
							if($db->query($sql)){
								$_SESSION["SI_PASSWORD"] = md5($new_passwd);
								header("Location: /login/changepassword/?done=1");
								exit;
							}else{
								
							}
						 }else{
							 $ar['error'] = $site->GetMessage('user', 'errors', 'passw_wrong');
						 }
					 }else{
						 $ar['error'] = $site->GetMessage('user', 'errors', 'passw_length');
					 }
					
					
				}else{
					$ar['error'] = $site->GetMessage('user', 'errors', 'different_passw');
				}
				
			}
			
			return $ar;
		}
		
        static public function get_register($site)
        {
            global $db, $tpl;
			$ar = array();
			$ar['title'] = $site->GetMessage('user', 'register');
			$ar['page'] = 'user/register.html';
			$ar['metatitle'] = $site->GetMessage('user', 'meta', 'register');
			$ar['description'] = $site->GetMessage('user', 'meta', 'description');
			$ar['keywords'] = $site->GetMessage('user', 'meta', 'keywords');
			$ar['error'] = '';
			//$ar['content'] = '';
			$reg = array('login' => '', 'email' => '', 
				'passw1' => '', 'passw2' => '' );
			
			if(!empty($_POST['reg'])){
				
				$when = isset($_POST['reg']['when']) ? $_POST['reg']['when'] : '';
				if(!$site->check_form_key($when)){
					$site->page['title'] = $ar['title'];
					$site->page['content'] = '<p>'.$site->GetMessage('user', 'errors', 'title').'</p>';
					$site->page['metatitle'] = $ar['metatitle'];
					$site->page['description'] = $ar['description'];
					$site->page['keywords'] = $ar['keywords'];					
					return $site->error_message($site->GetMessage('user', 'errors', 'form_closed'));
				}
				
				$sent_reg = $_POST["reg"];
				$errors = array();

				if(empty($sent_reg['login'])){
					if(empty($sent_reg['subscribe'])){
						$errors[] = $site->GetMessage('user', 'errors', 'login_empty');
					}					
				}else{
					if(!preg_match("/^[a-zа-я0-9_]{4,20}$/",$sent_reg['login'])){
						$errors[] = $site->GetMessage('user', 'errors', 'login_incorrect');
					}else{
						$reg['login'] = $sent_reg['login'];
					}
				}

				$sent_reg['email'] = !empty($sent_reg['email']) ? trim($sent_reg['email']) : '';
				if(empty($sent_reg['email'])){
					$errors[] = $site->GetMessage('user', 'errors', 'email_empty');
				}else{
					if(!preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)*\.([a-zA-Z]{2,6})$/", $sent_reg['email'])){
						$errors[] = $site->GetMessage('user', 'errors', 'email_incorrect');
					}else{
						$reg['email'] = $sent_reg['email'];
					}
				}
				
				if(empty($errors) && !empty($sent_reg['subscribe'])){
					/* add subscriber */
					return User::add_subscriber($reg['email'], $site);
				}

				if(empty($sent_reg['passw1'])){
					$errors[] = $site->GetMessage('user', 'errors', 'passw1_empty');
				}else{
					if(!preg_match("/^[a-zа-я0-9_]{4,20}$/",$sent_reg['passw1'])){
						$errors[] = $site->GetMessage('user', 'errors', 'passw1_incorrect');
					}else{
						$reg['passw1'] = $sent_reg['passw1'];
					}					
				}

				if(empty($sent_reg['passw2'])){
					$errors[] = $site->GetMessage('user', 'errors', 'passw2_empty');
				}
				
				if(!empty($sent_reg['passw1']) && !empty($sent_reg['passw2']) && 
					$sent_reg['passw2'] != $sent_reg['passw1']){
					$errors[] = $site->GetMessage('user', 'errors', 'different_passw');
				}else{
					$reg['passw2'] = $reg['passw1'];
				}

				if(empty($errors)){
					/* проверим есть ли такой логин */
					$r = $db->get_row("SELECT * 
						FROM ".$db->tables['users']." 
						WHERE login LIKE '".$db->escape($sent_reg['login'])."' ");
						
					if(!empty($db->last_error)){ return $site->db_error($db->last_error); }
					if($db->num_rows > 0){ 
						$errors[] = $site->GetMessage('user', 'errors', 'login_taken'); 
					}
						
					/* проверим есть ли такой email */
					$r = $db->get_row("SELECT * 
						FROM ".$db->tables['users']." 
						WHERE email LIKE '".$db->escape($sent_reg['email'])."' ");
						
					if(!empty($db->last_error)){ return $site->db_error($db->last_error); }
					if($db->num_rows > 0){ 
						$errors[] = $site->GetMessage('user', 'errors', 'email_taken'); 
					}
					
					if(empty($errors)){
						$r_date = date('Y-m-d H:i:s');
						$try_passwd = $site->encode_str($sent_reg['passw1']);
						$sql = "INSERT INTO ".$db->tables['users']." 
							(`login`, `passwd`, `email`, `date_insert`) 
							VALUES (
							'".$db->escape($sent_reg['login'])."',
							'".$db->escape($try_passwd)."',
							'".$db->escape($sent_reg['email'])."',
							'".$r_date."' 							
							) ";
						$db->query($sql);
						$id = $db->insert_id;
						$site->register_changes('user', $id, 'add');

						$sql = "INSERT INTO (bo_userid) VALUES ('".$id."')";
						$db->query($sql);
						
						$arr = prepare_user_info($id);
						send_email_event('user_invitation', $arr);
						
						$url = $site->uri['requested_full'].'?done=1';
						header('Location: '.$url);
						exit;						
					}
				}
				
				
				if(!empty($errors)){
					$ar['error'] .= '<ul>';
					foreach($errors as $v){
						$ar['error'] .= '<li>'.$v.'</li>';						
					}
					$ar['error'] .= '</ul>';
				}
				
			}else{
				if(!empty($_GET['key']) && !empty($_GET['add'])){
					$sql = "UPDATE ".$db->tables['users']." 
						SET `active` = '1' 
						WHERE 
						MD5(id) = '".$db->escape($_GET['key'])."' AND 
						MD5(date_insert) = '".$db->escape($_GET['add'])."' AND 
						active = '0' 						
					";
					$db->query($sql);
					
					$row = $db->get_row("SELECT * FROM ".$db->tables['users']." 
						WHERE 
						MD5(id) = '".$db->escape($_GET['key'])."' AND 
						MD5(date_insert) = '".$db->escape($_GET['add'])."' AND 
						active = '1' 						
					", ARRAY_A);
					
					if($row && $db->num_rows == 1){
						$arr = prepare_user_info($row['id']);
						send_email_event('user_new', $arr);	
						$site->register_changes('user', $row['id'], 'activate');
						
					}					
					
					$url = $site->vars['site_url'].'/login/?done=1';
					header('Location: '.$url);
					exit;
				}
			}
			$tpl->assign('reg', $reg);
			return $ar;            
        }
		
		static public function add_subscriber($email, $site)
		{
			global $db, $tpl;
			$sql = "SELECT * FROM ".$db->tables['users']." 
						WHERE email LIKE '".$db->escape($email)."' ";
			$row = $db->get_row($sql);			

			if($db->num_rows == 1){
				/* Пользователь есть, отправим 2 версии письма */
				/* 1 - вы подписаны, хотите отказаться от рассылки? */
				/* 2 - для подписки нажмите на ссылку */
				
				if(!empty($row->news)){
					/* подписан, напомним и предложим ссылку об отмене подписки */
					$ar = prepare_user_info($row->id);
					send_email_event('subscriber_cancel', $ar);
				}else{
					/* не подписан, предложим ссылку на подписку */
					$ar = prepare_user_info($row->id);
					send_email_event('subscriber_invitation', $ar);
				}
			}else{
				/*
					новые ящик - зарегим юзера и 
					отправим письмо для активации подписки
				*/
				$arr = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','r','q','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','R','Q','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9');
				// Генерируем пароль
				$passwd = "";
				for($i = 0; $i < 8; $i++){
					// Вычисляем случайный индекс массива
					$index = rand(0, count($arr) - 1);
					$passwd .= $arr[$index];
				}
								
				$try_passwd = $site->encode_str($passwd);
				$new_login = 's'.date('j').'-'.date('ny').'-';
				for($i = 0; $i < 4; $i++){
					// Вычисляем случайный индекс массива
					$index = rand(26, count($arr) - 10);
					$new_login .= $arr[$index];
				}

				$r_date = date('Y-m-d H:i:s');
				$sql = "INSERT INTO ".$db->tables['users']." 
					(`name`, `login`, `passwd`, 
					`email`, `news`, `date_insert`, `active`) 
					VALUES ('Subscriber', '".$db->escape($new_login)."', 
					'".$try_passwd."', '".$db->escape($email)."', 
					'0', '".$r_date."', '0') ";
				$db->query($sql);
				$user_id = $db->insert_id;
				
				$ar = prepare_user_info($user_id);
				send_email_event('subscriber_invitation', $ar);
			}
			
			$url = $site->vars['site_url'].'/register/?done=subscribe';
			return $site->redirect($url);			
		}
		
		static public function edit($site)
		{
            global $db, $tpl;
			
			if(!empty($_POST["user"])){
				$ar = array();
				$ar['name'] = !empty($_POST["user"]["name"]) ? trim($_POST["user"]["name"]) : '';
				
				$ar['phone_mobil'] = !empty($_POST["user"]["phone_mobil"]) ? trim($_POST["user"]["phone_mobil"]) : '';
				$ar['icq'] = !empty($_POST["user"]["icq"]) ? trim($_POST["user"]["icq"]) : '';
				$ar['country'] = !empty($_POST["user"]["country"]) ? trim($_POST["user"]["country"]) : '';
				$ar['city'] = !empty($_POST["user"]["city"]) ? trim($_POST["user"]["city"]) : '';
				$ar['user_interes'] = !empty($_POST["user"]["user_interes"]) ? trim($_POST["user"]["user_interes"]) : '';
				$ar['user_about'] = !empty($_POST["user"]["user_about"]) ? trim($_POST["user"]["user_about"]) : '';
				$ar['pers_hi'] = !empty($_POST["user"]["pers_hi"]) ? trim($_POST["user"]["pers_hi"]) : '';
				$ar['url'] = !empty($_POST["user"]["url"]) ? trim($_POST["user"]["url"]) : '';
				$ar['birth_day'] = !empty($_POST["user"]["birth_day"]) ? $_POST["user"]["birth_day"] : '';
				
				$str = isset($ar['birth_day']['Year']) ? $ar['birth_day']['Year'] : '0000';
				$str = isset($ar['birth_day']['Month']) ? $str.'-'.$ar['birth_day']['Month'] : '0000-00';
				$str = isset($ar['birth_day']['Day']) ? $str.'-'.$ar['birth_day']['Day'] : '0000-00-00';
				
				if($str == date('Y-m-d')){ $str = '0000-00-00'; }
				$ar['gender'] = !empty($_POST["user"]["gender"]) ? trim($_POST["user"]["gender"]) : '-';
				$ar['news'] = !empty($_POST["user"]["news"]) ? intval($_POST["user"]["news"]) : '0';
				
				$sql = "UPDATE ".$db->tables['users']." SET
					`name` = '".$db->escape($ar['name'])."',
					`phone_mobil` = '".$db->escape($ar['phone_mobil'])."',
					`icq` = '".$db->escape($ar['icq'])."',
					`country` = '".$db->escape($ar['country'])."',
					`city` = '".$db->escape($ar['city'])."',
					`birth_day` = '".$db->escape($str)."',
					`user_interes` = '".$db->escape($ar['user_interes'])."',
					`user_about` = '".$db->escape($ar['user_about'])."',
					`url` = '".$db->escape($ar['url'])."',
					`pers_hi` = '".$db->escape($ar['pers_hi'])."',
					`news` = '".$db->escape($ar['news'])."',
					`gender` = '".$db->escape($ar['gender'])."'
					WHERE id = '".$site->user['id']."' 
				";
				$db->query($sql);
				//$db->debug(); exit;
				$site->register_changes('user', $site->user['id'], 'update');

				$site->create_new_avatar($site->user['id']);
				header("Location: /login/edit/?done=updated");
				exit;

			}
			
			$rows = array();
			$ar = array();
			$ar['title'] = $site->GetMessage('user', 'edit');
			$ar['page'] = 'user/edit.html';
			$ar['metatitle'] = $site->GetMessage('user', 'meta', 'edit');
			$ar['description'] = $site->GetMessage('user', 'meta', 'edit');
			$ar['keywords'] = $site->GetMessage('user', 'meta', 'edit');
			$tpl->assign('orders', $rows);
			
			$ar['breadcrumbs'][0][] = array(
				'link' => '/login/',
				'alias' => 'login',
				'link_idn' => $site->uri['idn'].'/login/',
				'title' => $site->GetMessage('user', 'cabinet')
			);
			return $ar;
		}

		static public function fb($site)
		{
            global $db, $tpl;
			$rows = array();
			
			if(!empty($site->user['email'])){
				$sql = "SELECT * FROM ".$db->tables['feedback']." 
					WHERE `email` = '".$site->user['email']."' 
					AND site_id = '".$site->vars['id']."'
					ORDER BY id desc ";
				$rows = $db->get_results($sql, ARRAY_A);
				
				if($db->num_rows > 0){
					$n_rows = array();
					foreach($rows as $row){
						$row['created'] = date($site->vars['site_date_format'], strtotime($row['sent'])); 

						$row['link'] = $site->vars['site_url'].'/feedback'.URL_END.'?done='.md5($row['id'].$row['sent']);

						unset($row['sent']);
						$n_rows[] = $row;
					}
					$rows = $n_rows;
				}
			}

			$ar = array();
			$ar['title'] = $site->GetMessage('user', 'fb');
			$ar['page'] = 'user/fb.html';
			$ar['metatitle'] = $site->GetMessage('user', 'meta', 'fb');
			$ar['description'] = $site->GetMessage('user', 'meta', 'fb');
			$ar['keywords'] = $site->GetMessage('user', 'meta', 'fb');
			
			$tpl->assign('rows', $rows);
			return $ar;
		}

		
		static public function orders($site)
        {
            global $db, $tpl;
			$rows = array();
			
			if(!empty($site->user['email'])){
				
				$rows = $db->get_results("SELECT o.*, 
						s.show_client as show_client, 
						s.title as status_admin, 
						s.title_client as status_user
					FROM ".$db->tables['orders']." o 
					LEFT JOIN ".$db->tables['order_status']." s on (s.id = o.status)
					WHERE o.email = '".$site->user['email']."' 
					AND o.site_id = '".$site->vars['id']."'
					ORDER BY o.id desc 
				", ARRAY_A);
				
				$n_rows = array();
				if($db->num_rows > 0){
					foreach($rows as $row){
						$url = $site->vars['site_url'].'/order'.URL_END.'?done='.md5($row['id'].$row['created']);
						
						$row['created'] = date($site->vars['site_date_format'], strtotime($row['created'])); 
						$row['last_edit'] = $row['last_edit'] == '0000-00-00 00:00:00' ? '' : date($site->vars['site_date_format'], strtotime($row['last_edit'])); 
						$status = !empty($row['status_user']) ? $row['status_user'] : $row['status_admin'];
						if($row['status'] == 0){ $status = $site->GetMessage('order', 'status_new'); }
						
						unset($row['status_admin']);
						unset($row['status_user']);
						unset($row['manager_id']);
						unset($row['region']);
						unset($row['delivery_price']);
						unset($row['delivery_index']);
						unset($row['ur_type']);
						unset($row['fio']);
						unset($row['name_company']);
						unset($row['email']);
						unset($row['city']);
						unset($row['metro']);
						unset($row['phone']);
						unset($row['address']);
						unset($row['address_memo']);
						unset($row['memo']);
						
						$row['status_title'] = $status;
						$row['link'] = !empty($row['show_client']) ? $url : '';
						$n_rows[] = $row;
					}
				}
				$rows = $n_rows;
			}
			$ar = array();
			$ar['title'] = $site->GetMessage('user', 'list_orders');
			$ar['page'] = 'user/orders.html';
			$ar['metatitle'] = $site->GetMessage('user', 'meta', 'list_orders');
			$ar['description'] = $site->GetMessage('user', 'meta', 'list_orders');
			$ar['keywords'] = $site->GetMessage('user', 'meta', 'list_orders');
			
			$tpl->assign('orders', $rows);
			return $ar;
		}

        static public function logout($site)
        {
            global $db, $tpl;			
			$site->register_changes('user', $site->user['id'], 'logout');
			unset($_SESSION["SI_USERID"]);
			unset($_SESSION["SI_LOGIN"]);
			unset($_SESSION["SI_PASSWORD"]);
			
			unset($_SESSION["BO_USERID"]);
			unset($_SESSION["BO_LOGIN"]);
			unset($_SESSION["BO_PASSWORD"]);
			unset($_SESSION["BO_REGISTERED"]);
			unset($_SESSION["BO_NAME"]);
			unset($_SESSION["BO_EMAIL"]);
			$_SESSION = array();
					
			$url = $site->uri['site'];
			header("Location: ".$url);
			exit;
		}

		static public function login($site)
        {
            global $db, $tpl;
			$ar = array();
			
			if(!empty($site->user['id'])){
				$ar['title'] = $site->GetMessage('user', 'cabinet');
				$ar['page'] = 'user/login.html';
				$ar['metatitle'] = $site->GetMessage('user', 'cabinet');
				$ar['description'] = $site->GetMessage('user', 'cabinet');
				$ar['keywords'] = $site->GetMessage('user', 'cabinet');
			}else{
				$ar['title'] = $site->GetMessage('user', 'title');
				$ar['page'] = 'user/login.html';
				$ar['metatitle'] = $site->GetMessage('user', 'meta', 'title');
				$ar['description'] = $site->GetMessage('user', 'meta', 'description');
				$ar['keywords'] = $site->GetMessage('user', 'meta', 'keywords');
			}
			
			//$ar['content'] = '';

			$try_email = !empty($_POST['login']['email']) ? trim($_POST['login']['email']) : '';
			$try_passwd = !empty($_POST['login']['passwd']) ? trim($_POST['login']['passwd']) : '';
			
	
			if(!empty($try_email) && !empty($try_passwd)){
				/* проверяем пользователя */
				$try_passwd = $site->encode_str($try_passwd);
				
				/* если кол-во попыток за час больше 3-х - выйдем с ошибкой */
				$qty = $db->get_var("SELECT COUNT(*)  
						FROM ".$db->tables['changes']." c 
						LEFT JOIN ".$db->tables['users']." u 
							ON (c.where_id = u.id 
							AND c.where_changed = 'user')
					WHERE (u.login = '".$db->escape($try_email)."' 
					OR u.email = '".$db->escape($try_email)."') 
					AND c.date_insert > NOW() - INTERVAL 1 HOUR 
					AND c.type_changes = 'login_wrong'
				");
				
				
				if(!empty($qty) && $qty > 3){
					return $site->error_page(429);
				}
					
				$u = $db->get_row("SELECT * FROM ".$db->tables['users']." 
					WHERE  `passwd` = '".$db->escape($try_passwd)."' 
					AND (login = '".$db->escape($try_email)."' 
					OR email = '".$db->escape($try_email)."')
				", ARRAY_A);
				if($db->num_rows == 1){					
					/* успешно, авторизуемся, раз пароль подошел */
					/* обновим IP и дату логина */
					$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
					$ddate = date('Y-m-d H:i:s');
					$sql = "UPDATE ".$db->tables['users']." SET 
						last_login = '".$ddate."', 
						last_ip = '".$db->escape($ip)."' 
						WHERE id = '".$u['id']."' ";
					$db->query($sql);

					$site->register_changes('user', $u['id'], 'login', $ip);
					$site->user = $u;
					$_SESSION["SI_USERID"] = md5($u['id']);					
					$_SESSION["SI_LOGIN"] = md5($u['login']);
					$_SESSION["SI_PASSWORD"] = md5($u['passwd']);
					
					$url = $site->uri['requested_full'];					
					$t_url = !empty($_POST["referer"]) ? trim($_POST["referer"]) : '';
					if(!empty($t_url)){
						$t_url = str_replace($site->vars['site_url'], '', $t_url);
						$t_url = htmlspecialchars($t_url);
						$t_url = mysql_escape_string($t_url);
					
						if(stristr($t_url, '://') === FALSE) {
							$url = $t_url;
						}					
					}
										
					header("Location: ".$url);
					exit;
				}else{
					/* запишем попытку */
					$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
					$u = $db->get_row("SELECT * 
						FROM ".$db->tables['users']." 
						WHERE email = '".$db->escape($try_email)."' 
					", ARRAY_A);
					if(!$u || $db->num_rows == 0){
						$u = $db->get_row("SELECT * 
							FROM ".$db->tables['users']." 
							WHERE login = '".$db->escape($try_email)."'  
						", ARRAY_A);
						
						if($u && $db->num_rows == 1){
							$site->register_changes('user', $u['id'], 'login_wrong', $ip);
						}
						
					}else{
						$site->register_changes('user', $u['id'], 'login_wrong', $ip);
					}
					$ar['error'] = $site->GetMessage('user', 'errors', 'user_not_found');
				}
			}
			return $ar;
        }

		static public function skip_login($site, $id, $str=''){
			global $db, $tpl;
			$site->register_changes('user', $id, 'skip', $str);
			unset($_SESSION["SI_USERID"]);
			unset($_SESSION["SI_LOGIN"]);
			unset($_SESSION["SI_PASSWORD"]);
			
			unset($_SESSION["BO_USERID"]);
			unset($_SESSION["BO_LOGIN"]);
			unset($_SESSION["BO_PASSWORD"]);
			unset($_SESSION["BO_REGISTERED"]);
			unset($_SESSION["BO_NAME"]);
			unset($_SESSION["BO_EMAIL"]);
			$_SESSION = array();
			$url = !empty($site->uri['site']) ? $site->uri['site'] : '';
			$url .= '/login/?skip=ip';
			header("Location: ".$url);
			exit;
		}
		
		
		static public function add_item($site)
        {
            global $db, $tpl;
			$ar = array();
			$ar['title'] = $site->GetMessage('user', 'cabinet');
			$ar['page'] = 'user/add_item.html';
			$ar['metatitle'] = $site->GetMessage('user', 'cabinet');
			$ar['description'] = $site->GetMessage('user', 'cabinet');
			$ar['keywords'] = $site->GetMessage('user', 'cabinet');
			$ar['error'] = '';
			
			$ar['breadcrumbs'][0][] = array(
				'link' => '/login/',
				'alias' => 'login',
				'link_idn' => $site->uri['idn'].'/login/',
				'title' => $site->GetMessage('user', 'cabinet')
			);
			
			if(empty($site->user['to_add']['product'])){
				return $ar;
			}
			
			return $ar;
		}
		
		static public function add_pub($site)
        {
            global $db, $tpl;
			$ar = array();
			$ar['title'] = $site->GetMessage('user', 'cabinet');
			$ar['page'] = 'user/add_pub.html';
			$ar['metatitle'] = $site->GetMessage('user', 'cabinet');
			$ar['description'] = $site->GetMessage('user', 'cabinet');
			$ar['keywords'] = $site->GetMessage('user', 'cabinet');
			$ar['error'] = '';
			
			$ar['breadcrumbs'][0][] = array(
				'link' => '/login/',
				'alias' => 'login',
				'link_idn' => $site->uri['idn'].'/login/',
				'title' => $site->GetMessage('user', 'cabinet')
			);
			
			if(empty($site->user['to_add']['pub'])){
				return $ar;
			}
			
			
			if(!empty($_POST['add']['for_page']) 
				&& !empty($_POST['add']['title']) 
				&& !empty($_POST['add']['text'])
			){
				
				$add_id = intval($_POST['add']['for_page']);
				$add_title = trim($_POST['add']['title']);
				$add_title = strip_tags($add_title);
				$add_text = trim($_POST['add']['text']);
				$add_text = strip_tags($add_text);
				
				/* to add post and inform admin */
				//$site->print_r($site->vars, 1);
				$alias = !empty($site->vars['sys_prefix_pub']) 
					? $site->vars['sys_prefix_pub']
					: 'post-'; 
				$alias .= time();
				
				$multitpl = !empty($site->vars['sys_tpl_pubs']) 
					? $site->vars['sys_tpl_pubs'] 
					: 'pub.html';
				
				//$site->print_r($site->vars['all_sites'], 1);
				$date_insert = date('Y-m-d H:i:s'); 
  
				$query = "INSERT INTO ".$db->tables["publications"]." 
					(`name`, `anons`,
						`memo`, `active`, `meta_title`, `meta_description`,
						`meta_keywords`, `alias`, `date_insert`, `user_id`, `multitpl`, 
						`icon`, `f_spec`
					) VALUES(
						'".$db->escape($add_title)."',
						'',
						'".$db->escape($add_text)."',
						'1',
						'".$db->escape($add_title)."',
						'".$db->escape($add_title)."',
						'".$db->escape($add_title)."',
						'".$db->escape($alias)."',
						'".$date_insert."',
						'".$site->user['id']."',
						'".$db->escape($multitpl)."',
						'',
						'0'
					)
				";
				$db->query($query);
				$id = $db->insert_id;
				$site->register_changes('pub', $id, 'add', $add_title);
				
				/* привязка к сайтам - всем что есть в системе */
				foreach($site->vars['all_sites'] as $k => $v){
					$query = "INSERT INTO ".$db->tables["site_publications"]."  
							(`id_site`, `id_publications`) 
							VALUES('".$v['id']."', '".$id."') ";
					$db->query($query);
				}				
				
				/* привязка к странице */
				$query = "INSERT INTO ".$db->tables["pub_categs"]." 
						(`id_pub`, `id_categ`) 
						VALUES('".$id."', '".$add_id."') ";
				$db->query($query);
				
				$url = $site->vars['idn_url'].'/'.$alias.URL_END;
				return $site->redirect($url);
			}
			return $ar;
		}
        
    }

?>