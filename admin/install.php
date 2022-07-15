<?php
	/* 
		This file have to set up Simpla!
		- install database 
		- check folders 
		- create admin account
		ver. 04.05.2018
		- localized version
		
		
		
		
		+++++ TODO correct mysql requests
		
		$create_tables = <<<SQL

			--
			-- Структура таблицы `banner_list`
			--

			DROP TABLE IF EXISTS `banner_list`;
			CREATE TABLE IF NOT EXISTS `banner_list` ()
		SQL;



	*/
	define('SIMPLA_ADMIN', 1);
	define("MODE", "install");
	ini_set("display_errors", 1);
	error_reporting(E_ALL);	
	include "./startup.php";
	$db->use_disk_cache = false;
	$db->cache_queries = false;	
	
	$db_array = array(
		'host' => HOSTNAME,
		'database' => DATABASE,
		'user' => DBUSER,
		'pass' => DBPASS
	);
	$tpl->assign('db', $db_array);	
	
	$tpl->assign('langs', $langs);	
	$tpl->assign('admin_vars', $admin_vars);
	$currentlang = setLanguage('en');
	$tpl->assign('currentlang', $currentlang);	
	
	//echo $currentlang.'<br>'; exit;
	//echo '<pre>';
	//print_r($admin_vars);
	//exit;
	     //     {if $admin_vars.multilang == 1}

	//global $lang, $langs;
	//print_r($langs);
	//echo GetMessage('setup');
	//exit;
	if(isset($_POST["db_host"]) 
		&& isset($_POST["db_database"]) 
		&& isset($_POST["db_user"]) 
		&& isset($_POST["db_password"])
	){
		$db_host = isset($_POST["db_host"]) ? trim($_POST["db_host"]) : '';
		$db_database = isset($_POST["db_database"]) ? trim($_POST["db_database"]) : '';
		$db_user = isset($_POST["db_user"]) ? trim($_POST["db_user"]) : '';
		$db_password = isset($_POST["db_password"]) ? trim($_POST["db_password"]) : '';
			
		if(DBUSER != $db_user
			|| DBPASS != $db_password
			|| DATABASE != $db_database
			|| HOSTNAME != $db_host
		){
				
			/* требуется создать новый файл config.php */
			$file_config = '

  /************************
  *
  * Configuration file
  * for current website
  * 
  *************************/

    // Database settings
    if (!defined("HOSTNAME")) { define("HOSTNAME", "'.$db_host.'"); }
    if (!defined("DATABASE")) { define("DATABASE", "'.$db_database.'"); }
    if (!defined("DBUSER")) { define("DBUSER", "'.$db_user.'"); }
    if (!defined("DBPASS")) { define("DBPASS", "'.$db_password.'"); }

';
			$tpl->assign('file_config', $file_config);			
		}
	}
	
	if(!empty($db->last_error)){ 
	
		$errors = array();
		if(isset($_POST['install']) && empty($user_id)){
			
			$bo_login = isset($_POST["bo_login"]) ? trim($_POST["bo_login"]) : '';
			$bo_password = isset($_POST["bo_password"]) ? trim($_POST["bo_password"]) : '';
			$bo_password2 = isset($_POST["bo_password2"]) ? trim($_POST["bo_password2"]) : '';
			$bo_email = isset($_POST["bo_email"]) ? trim($_POST["bo_email"]) : '';
			$db_host = isset($_POST["db_host"]) ? trim($_POST["db_host"]) : '';
			$db_database = isset($_POST["db_database"]) ? trim($_POST["db_database"]) : '';
			$db_user = isset($_POST["db_user"]) ? trim($_POST["db_user"]) : '';
			$db_password = isset($_POST["db_password"]) ? trim($_POST["db_password"]) : '';

			if(empty($bo_login)){
				$errors[] = GetMessage('admin', 'set', 'login_empty');
			}
			
			if(empty($bo_password)){
				$errors[] = GetMessage('admin', 'set', 'password_empty');
			}
			if(empty($bo_password2)){
				$errors[] = GetMessage('admin', 'set', 'password2_empty');
			}
			if($bo_password != $bo_password2){
				$errors[] = GetMessage('admin', 'set', 'passwords_different');
			}
			if(empty($bo_email)){
				$errors[] = GetMessage('admin', 'set', 'email_empty');
			}
			if(empty($db_host)){
				$errors[] = GetMessage('admin', 'set', 'host_empty');
			}
			if(empty($db_database)){
				$errors[] = GetMessage('admin', 'set', 'db_empty');
			}
			if(empty($db_user)){
				$errors[] = GetMessage('admin', 'set', 'dbuser_empty');
			}
			
			$db->disconnect();
		    $db = new ezSQL_mysqli($db_user, $db_password, $db_database, $db_host);			
			$db->hide_errors();
			$db->use_disk_cache = false;
			$db->cache_queries = false;
			$db->last_error = '';
			$db->tables = $table_vars;

			$sql = 'CREATE DATABASE IF NOT EXISTS '.$db_database.';';
			if(!$db->query($sql)){
				$errors[] = GetMessage('admin', 'set', 'dbtables_not_added');
			}else{
				/* если установлен уже, то вернем ошибку */
				$qty = $db->get_var("SELECT COUNT(*) 
					FROM ".$db->tables['users']." 
				");
				if(!empty($qty)){
					$errors[] = GetMessage('admin', 'set', 'admin_added');
					$tpl->assign('installed', 'yes');
				}
				
				$qty = $db->get_var("SELECT COUNT(*) 
					FROM ".$db->tables['site_info']." 
				");
				if(!empty($qty)){
					$errors[] = GetMessage('admin', 'set', 'site_added');
					$tpl->assign('installed', 'yes');
				}
				$db->last_error = '';
			}

			$db->query("SET NAMES utf8");
			$db->query("SET CHARACTER SET utf8");
			$db->query("SET collation_connection = 'utf8_general_ci'");
			$db->query("SET SQL_BIG_SELECTS=1");
			
			if(!empty($db->last_error)){ 
				$errors[] = GetMessage('admin', 'set', 'mysql_error').': '.$db->last_error;
			}
			
			if(DBUSER != $db_user
				|| DBPASS != $db_password
				|| DATABASE != $db_database
				|| HOSTNAME != $db_host
			){
				/* требуется создать новый файл config.php */
				$file_config = '				
  /************************
  *
  * Configuration file
  * for current website2
  * 
  *************************/

    // Database settings
    if (!defined("HOSTNAME")) { define("HOSTNAME", "'.$db_host.'"); }
    if (!defined("DATABASE")) { define("DATABASE", "'.$db_database.'"); }
    if (!defined("DBUSER")) { define("DBUSER", "'.$db_user.'"); }
    if (!defined("DBPASS")) { define("DBPASS", "'.$db_password.'"); }

';
				$tpl->assign('file_config', $file_config);
				if(!empty($db->last_error)){ 
					$errors[] = GetMessage('admin', 'set', 'correct_file'). ' config.php';
				}
			}
			
			if(empty($errors)){
				/* ошибок нет, создаем базу */
				include_once('procedure_db.php');
				
				foreach($db->tables as $k => $v){
					$str = add_db($k, $v);
					if(!empty($str)){
						$errors[] = $str;
						$tpl->assign('errors', $errors);
						$tpl->display("settings/install.html");
						exit;
					}
				}
				
				/* создадим пользователя */
				$bo_md5 = encode_str($bo_password);
				$sql = "INSERT INTO ".$db->tables['users']." 
					(`name`, `login`, `passwd`, 
					`email`, `phone_mobil`, 
					`icq`, `memo`, `admin`, 
					`date_insert`, `active`, 
					`news`) VALUES(
					'".$db->escape($bo_login)."',
					'".$db->escape($bo_login)."',
					'".$bo_md5."',
					'".$db->escape($bo_email)."',
					'', '', '', '1', 
					'".date('Y-m-d H:i:s')."', 
					'1', '1'
				) ";
				$db->query($sql);
				if(!empty($db->last_error)){ 
					return db_error(basename(__FILE__).": ".__LINE__); 
				}
				$id = $db->insert_id;
				$user_id = $id;
				$db->query("INSERT INTO ".$db->tables['users_prava']." (bo_userid, `orders`, `products`, 
				`orders_stat`, `settings`, `info`, 
				`banner`, `vote`, `db`, `comments`, 
				`feedback`, `search`, `mass`, `fav`, 
				`delivery`, `emails`, `org`, 
				`coupons`, `stat`) VALUES ('".$id."', 
				'1', '1', '1', '1', '1', '1', '1', 
				'1', '1', '1', '1', '1', '1', 
				'1', '1', '1', '1', '1'
				) ");
				if(!empty($db->last_error)){ 
					return db_error(basename(__FILE__).": ".__LINE__); 
				}
				
				/* добавим сайт */
				$site_title = 'http://';
				$site_full = GetMessage('admin', 'sites', 'added');
				$site_short = GetMessage('admin', 'website');
				if(isset($_SERVER['HTTP_HOST'])){
					$site_title .= $_SERVER['HTTP_HOST'];
					$site_full .= ' '.$_SERVER['HTTP_HOST'];
					$site_short .= ' '.$_SERVER['HTTP_HOST'];
				}
				$meta = $site_full;
				$site_url = $site_title;
				$template_path = ''; //
				$site_description = GetMessage('admin', 'set', 'new_site_desc');
				
				$sql = "INSERT INTO ".$db->tables['site_info']." 
					(`name_full`, `name_short`, `site_url`, 
					`email_info`, `phone`, `meta_keywords`, 
					`meta_description`, `meta_title`, 
					`office_ip`, `template_path`, 
					`site_active`, `site_charset`, `lang`, 
					`site_time_zone`, `site_date_format`, 
					`site_time_format`, `site_description`, 
					`gallery`, `mode_basket`, 
					`feedback_in_db`, `default_id_categ`
					) VALUES ('".$db->escape($site_full)."', 
					'".$db->escape($site_short)."', 
					'".$db->escape($site_url)."', 
					'".$db->escape($bo_email)."', 
					'(123) 456-78-90', 
					'".$db->escape($meta)."', 
					'".$db->escape($meta)."', 
					'".$db->escape($meta)."',
					'', '".$db->escape($template_path)."', 
					'1', 'utf-8', 'es_ES', 
					'Europe/Madrid', 'd.m.Y', 
					'H:i', '".$db->escape($site_description)."', 
					'1', '1', '1', '0')
				";
				$db->query($sql);
				if(!empty($db->last_error)){ 
					return db_error(basename(__FILE__).": ".__LINE__); 
				}
				$site_id = $db->insert_id;
				add_default_vars($site_id, $user_id);	

				/* добавим запись о создании сайта */
				in_changes('site', $site_id, 'add', '', $user_id);
				
				/* добавим если задан тестовый контент */
				if(!empty($_POST['add_default'])){
					add_default_content($site_id, $user_id);
				}
								
				
				if(!empty($_POST["send_email"])){
					/* отправим уведомление */
					if (!defined('SI_EMAIL')) { define('SI_EMAIL', $bo_email); }
					if (!defined('SI_TITLE_SHORT')) { define('SI_TITLE_SHORT', $site_title); }
					if (!defined('SI_TIMEZONE')) { define('SI_TIMEZONE', 'Europe/Madrid'); }
					if (!defined('SI_CHARSET')) { define('SI_CHARSET', 'utf-8'); }
					
					$subject = GetMessage('admin', 'sites', 'added');
					$body = '<p>'.GetMessage('admin', 'sites', 'added').'</p>';
					$body .= '<p>'.GetMessage('admin', 'set', 'admin_access_data').':</p>';
					$body .= '<p>'.GetMessage('admin', 'user', 'login').': '.$bo_login.'<br>';
					$body .= GetMessage('admin', 'user', 'password').': '.$bo_password.'<br>';
					$body .= GetMessage('admin', 'set', 'admin_panel').': <a href="'.$site_url.'/'.ADMIN_FOLDER.'/">'.$site_url.'/'.ADMIN_FOLDER.'/</a>
					</p>
					';
					
					send_notification2(
						$subject, 
						$body
					);					
				}
				
				/* Проверим папки на запись */
				$folders_check = array(
					ADMIN_FOLDER.'/compiled/mysql',
					ADMIN_FOLDER.'/compiled', 'upload',
					'upload/files', 'upload/images',
					'upload/records'
				);
				
				$folders = array();
				foreach($folders_check as $f){
					$f = str_replace('../','',$f);
					in_correct_folder($f);
					if(!in_check_folder($f)){
						$folders[] = $f;
					}
				}
				$tpl->assign('folders', $folders);				
				$tpl->assign('installed', 'yes');
				
				if(empty($file_config) && empty($folders)){
					
				}
			}else{
				$tpl->assign('errors', $errors);
			}
			
			
			
		}
		
		
	}else{
		//die('<h1>This site successfully installed!</h1>');
		if(isset($_POST['install'])){			

			/* Проверим папки на запись */
			$folders_check = array(
				ADMIN_FOLDER.'/compiled/mysql',
				ADMIN_FOLDER.'/compiled', 'upload',
				'upload/files', 'upload/images',
				'upload/records'
			);
			
			$folders = array();
			foreach($folders_check as $f){
				$f = str_replace('../','',$f);
				in_correct_folder($f);
				if(!in_check_folder($f)){
					$folders[] = $f;
				}
			}
			$tpl->assign('folders', $folders);
		}
		
		
		$tpl->assign('installed', 'yes');
		$tpl->display("settings/install.html");
		exit;
	}
	
	$tpl->display("settings/install.html");
    exit;

	

function in_correct_folder($f){
	global $path;
	$f = $path.'/'.$f;
	if (@file_exists($f)){
		/* попробуем изменить права на директорию на 0777 */
		@chmod($f, 0777);
	} else {
		mkdir($f, 0777);
	}
	return;
}	

function in_check_folder($f){
	global $path;
	$f = $path.'/'.$f;
	if (@file_exists($f) && is_writable($f)){
		return true;
	} else {
		return false;
	}
}

	
if(!function_exists('encode_str')){
	function encode_str($str){
		if (!defined('AUTHKEY')) { define('AUTHKEY', 'simpla.es'); }
		return md5(md5($str).'-'.md5(AUTHKEY));
	}
}




function add_default_vars($site_id, $user_id){
	global $db;
	$sql = "INSERT INTO `site_vars` (`site_id`, `name`, 
		`value`, `description`, `type`, `autoload`, 
		`if_enum`, `width`, `height`) 
		VALUES
		(0, 'end_url', '/', '', 'text', 1, '', 0, 0),
		(0, 'euro', '<i class=\"fa fa-euro\"></i>', '', 'text', 1, '', 0, 0),
		(0, 'facebook', '#', '".$db->escape(GetMessage('admin', 'set', 'fb_link'))."', 'text', 1, '', 0, 0),
		(0, 'twitter', '#', '".$db->escape(GetMessage('admin', 'set', 'twitter_link'))."', 'text', 1, '', 0, 0),
		(0, 'instagram', '#', '".$db->escape(GetMessage('admin', 'set', 'instagram_link'))."', 'text', 1, '', 0, 0),
		(0, 'form_sent', '".$db->escape(GetMessage('admin', 'set', 'thx_sent'))."', '".$db->escape(GetMessage('admin', 'set', 'thx_sent_desc'))."', 'text', 1, '', 0, 0),
		(0, 'img_comments', '', '', 'text', 1, '', 80, 80),
		(0, 'img_header', '".$db->escape(GetMessage('admin', 'set', 'img_auto_uploads'))."', '".$db->escape(GetMessage('admin', 'set', 'header_img'))."', 'text', 1, '', 1965, 280),
		(0, 'img_logo_big', '', '".$db->escape(GetMessage('admin', 'set', 'logo_big'))."', 'text', 1, '', 155, 155),
		(0, 'img_logo_small', '', '".$db->escape(GetMessage('admin', 'set', 'logo_small'))."', 'text', 1, '', 50, 50),
		(0, 'img_size1', '".$db->escape(GetMessage('admin', 'set', 'used_everywhere'))."', '".$db->escape(GetMessage('admin', 'set', 'image1'))."', 'text', 1, '', 133, 100),
		(0, 'img_size2', '".$db->escape(GetMessage('admin', 'set', 'used_everywhere2'))."', '".$db->escape(GetMessage('admin', 'set', 'image2'))."', 'text', 1, '', 400, 300),
		(0, 'img_size3', '".$db->escape(GetMessage('admin', 'set', 'used_everywhere3'))."', '".$db->escape(GetMessage('admin', 'set', 'image3'))."', 'text', 1, '', 800, 600),
		(0, 'img_size4', '".$db->escape(GetMessage('admin', 'set', 'used_everywhere4'))."', '".$db->escape(GetMessage('admin', 'set', 'image4'))."', 'text', 1, '', 1600, 1200),
		(0, 'rur', ' <i class=\"fa fa-rub\"></i>', '', 'text', 1, '', 0, 0),
		(0, 'sys_admin_intro', '', '".$db->escape(GetMessage('admin', 'set', 'admin_intro_help'))."', 'text', 1, '', 0, 0),
		(0, 'sys_color_scheme', 'violet', '".$db->escape(GetMessage('admin', 'set', 'color_scheme'))."', 'list', 1, 'default\r\nblue\r\ngreen\r\nviolet\r\nturquoise\r\nred\r\n', 0, 0),
		(0, 'sys_comments', '1', '".$db->escape(GetMessage('admin', 'set', 'comments_sort_help'))."', 'text', 1, '', 0, 0),
		(0, 'sys_comments_limit', '0', '".$db->escape(GetMessage('admin', 'set', 'commnets_qty_help'))."', 'text', 1, '', 0, 0),
		(0, 'sys_count_visitors', '0', ' ".$db->escape(GetMessage('admin', 'set', 'stat_on_help'))."', 'text', 1, '', 0, 0),
		(0, 'sys_currency', 'euro', '".$db->escape(GetMessage('admin', 'set', 'default_currency'))."', 'list', 1, 'rur\r\nusd\r\neuro', 0, 0),
		(0, 'sys_docs', 'nakladnaya.html = ".$db->escape(GetMessage('admin', 'set', 'nakladnaya'))."\r\ninvoice.html = ".$db->escape(GetMessage('admin', 'set', 'invoice'))."', '".$db->escape(GetMessage('admin', 'set', 'custom_docs'))."\r\nfile.html = ".$db->escape(GetMessage('admin', 'set', 'filename_help'))."\r\n".$db->escape(GetMessage('admin', 'set', 'docs_in_folder'))."', 'text', 1, '', 0, 0),
		(0, 'sys_fb_subject', '".$db->escape(GetMessage('admin', 'set', 'all_questions'))."', '".$db->escape(GetMessage('admin', 'set', 'subj_variants'))."', 'list', 1, '".$db->escape(GetMessage('admin', 'set', 'all_questions'))."\n".$db->escape(GetMessage('admin', 'set', 'cooperation'))."\n".$db->escape(GetMessage('admin', 'set', 'administrative_q'))."', 0, 0),
		(0, 'sys_licence', '".$db->escape(GetMessage('admin', 'set', 'licence_key'))."', 'Product key', 'text', 1, '', 0, 0),
		(0, 'sys_licence_key', '4285d478ea0f358b2f03a87b2030f977', '".$db->escape(GetMessage('admin', 'set', 'licence_product'))."', 'text', 1, '', 0, 0),
		(0, 'sys_map_contact', '', '".$db->escape(GetMessage('admin', 'set', 'address_help'))."', 'text', 1, '', 0, 0),
		(0, 'sys_map_marker', '', '".$db->escape(GetMessage('admin', 'set', 'mapmarker_default'))."', 'text', 1, '', 0, 0),
		(0, 'sys_menu_view', 'all', '".$db->escape(GetMessage('admin', 'set', 'topmenu_view'))."', 'list', 1, 'icon\r\ntitle\r\nall', 0, 0),
		(0, 'sys_mysql_cache', '1', 'MySQL caching on/off', 'checkbox', 1, '', 0, 0),
		(0, 'sys_prefix_product', 'item-', '".$db->escape(GetMessage('admin', 'set', 'prod_alias_help'))."\r\n', 'text', 1, '', 0, 0),
		(0, 'sys_prefix_pub', '', '".$db->escape(GetMessage('admin', 'set', 'pub_alias_help'))." ', 'text', 1, '', 0, 0),
		(0, 'sys_price_zero', '0', '', 'text', 1, '', 0, 0),
		(0, 'sys_qty_last_pubs', '8', '".$db->escape(GetMessage('admin', 'set', 'pub_qty_index'))."', 'text', 1, '', 0, 0),
		(0, 'sys_resize_method', 'biggest', 'Resize method', 'list', 1, 'square\r\nwidth\r\nheight\r\nbiggest', 0, 0),
		(0, 'sys_robots_txt', '', 'Robots.txt file', 'text', 1, '', 0, 0),
		(0, 'sys_save_original_fotos', '1', '".$db->escape(GetMessage('admin', 'set', 'maxsize_pic_help'))."', 'checkbox', 1, '', 0, 0),
		(0, 'sys_skip_starred_pubs', '', '', 'text', 1, '', 0, 0),
		(0, 'sys_skip_visited_products', '1', '', 'text', 1, '', 0, 0),
		(0, 'sys_skip_visited_pubs', '1', '', 'text', 1, '', 0, 0),
		(0, 'sys_smtp_auth', '1', '".$db->escape(GetMessage('admin', 'set', 'smtp_auth_help'))."', 'checkbox', 1, '', 0, 0),
		(0, 'sys_smtp_host', '', '".$db->escape(GetMessage('admin', 'set', 'smtp_addr_help')).". ".$db->escape(GetMessage('admin', 'icons', 'for_example')).", smtp.gmail.com', 'text', 1, '', 0, 0),
		(0, 'sys_smtp_on', '0', '".$db->escape(GetMessage('admin', 'set', 'smtp_on_help'))."', 'checkbox', 1, '', 0, 0),
		(0, 'sys_smtp_password', '', '".$db->escape(GetMessage('admin', 'set', 'smtp_pass_help'))."', 'text', 1, '', 0, 0),
		(0, 'sys_smtp_port', '', '".$db->escape(GetMessage('admin', 'set', 'smtp_port_help'))."', 'text', 1, '', 0, 0),
		(0, 'sys_smtp_secure', 'ssl', '".$db->escape(GetMessage('admin', 'set', 'smtp_ssl_help'))."', 'list', 1, 'ssl\r\ntls\r\n-', 0, 0),
		(0, 'sys_smtp_username', '', '".$db->escape(GetMessage('admin', 'set', 'smtp_login_help'))."', '', 1, '', 0, 0),
		(0, 'sys_starred_pubs_title', '".$db->escape(GetMessage('admin', 'set', 'imp_help'))."', '".$db->escape(GetMessage('admin', 'set', 'imp_help_desc'))."', 'text', 1, '', 0, 0),
		(0, 'sys_thousands_sep', '.', '".$db->escape(GetMessage('admin', 'set', 'sep_thousends'))."', 'text', 1, '', 0, 0),
		(0, 'sys_tpl_pages', 'category.html', '".$db->escape(GetMessage('admin', 'set', 'tpl_categs_help'))."', 'list', 1, 'category.html\r\nindex.html', 0, 0),
		(0, 'sys_tpl_products', 'product.html', '".$db->escape(GetMessage('admin', 'set', 'tpl_prod_help'))."', 'list', 1, 'product.html', 0, 0),
		(0, 'sys_tpl_pubs', 'pub.html', '".$db->escape(GetMessage('admin', 'set', 'tpl_pub_help'))."', 'list', 1, 'pub.html', 0, 0),
		(0, 'sys_upload_ext_allowed', '*', '".$db->escape(GetMessage('admin', 'set', 'allowed_ext_help'))."', 'text', 1, '', 0, 0),
		(0, 'usd', '<i class=\"fa fa-usd\"></i>', '', 'text', 1, '', 0, 0),
		(0, 'yml_key', 'yml2015', '".$db->escape(GetMessage('admin', 'set', 'yml_key_help'))."', 'text', 1, '', 0, 0);
	";
	$db->query($sql);	
	return;
}

function add_default_content($site_id, $user_id){
	global $db;
	
	/* Добавим 2 страницы и к одной из них публикацию */
	$pages = array();
	$pages[] = array(
		'title' => GetMessage('admin', 'set', 'about_site'),
		'content' => '<p>'.GetMessage('admin', 'set', 'welcome_txt').'</p>
			<p>'.GetMessage('admin', 'set', 'about_test_txt').'</p>',
		'alias' => 'about'		
	);
	
	$pages[] = array(
		'title' => GetMessage('admin', 'prava', 'news'),
		'content' => '<p>'.GetMessage('admin', 'set', 'news_txt').'</p>',
		'alias' => 'news'		
	);
	
	foreach($pages as $k => $v){
		$title = $db->escape($v['title']);
		$content = $db->escape($v['content']);
		$alias = $db->escape($v['alias']);
		
		$sql = "INSERT INTO ".$db->tables["categs"]." 
			(`title`, `id_parent`, `meta_description`, 
			`meta_keywords`, `meta_title`, `alias`,
			`memo`, `active`, `sort`, date_insert, 
			date_update, user_id, multitpl, `icon`, 
			`f_spec`, `shop`, `show_filter`, 
			`memo_short`, `in_last`) VALUES( 
			'".$title."', '0', '".$title."',
			'".$title."', '".$title."', '".$alias."',
			'".$content."', '1', '99',
			'".date('Y-m-d H:i:s')."', NULL, 
			'".$user_id."', 'category.html',
			'', '0', '0', '0', '0', '1')
		";
		$db->query($sql);
		$id = $db->insert_id;
		in_changes('categ', $id, 'add', '', $user_id);
		$sql = "INSERT INTO ".$db->tables["site_categ"]." 
			(`id_site`, `id_categ`) 
			VALUES('".$site_id."', '".$id."') 
		";
		$db->query($sql);	
		if($alias == 'news'){ $news_id = $id; }
	}
	
	if(!empty($news_id)){
		/* добавим новость */
		$posted = array();
		$posted["name"] = GetMessage('admin', 'set', 'one_news_txt').' 1';
		$posted["anons"] = GetMessage('admin', 'set', 'one_news_content');
		$posted["memo"] = '<p>'.GetMessage('admin', 'set', 'one_news1').'</p>
			<p>'.GetMessage('admin', 'set', 'one_news2').'</p>
			<p>'.GetMessage('admin', 'set', 'one_news3').'</p>
		';
		$posted["alias"] = 'post-'.time();
		
		$sql = "INSERT INTO ".$db->tables["publications"]." 
			(`name`, `anons`, `memo`, `active`, 
			`meta_title`, `meta_description`,
            `meta_keywords`, `alias`, `date_insert`, 
			`user_id`, `multitpl`, `icon`, `f_spec`) 
			VALUES(
            '".$db->escape($posted["name"])."',
            '".$db->escape($posted["anons"])."',
            '".$db->escape($posted["memo"])."',
            '1',
            '".$db->escape($posted["name"])."',
            '".$db->escape($posted["name"])."',
            '".$db->escape($posted["name"])."',
            '".$db->escape($posted["alias"])."',
            '".date('Y-m-d H:i:s')."',
            '".$user_id."',
            'pub.html', '', '0')
		";
		$db->query($sql);
		$nid = $db->insert_id;
		in_changes('pub', $nid, 'add', '', $user_id);
		
		$sql = "INSERT INTO ".$db->tables["site_publications"]."  
			(`id_site`, `id_publications`) 
			VALUES('".$site_id."', '".$nid."') ";
		$db->query($sql);
		
		$sql = "INSERT INTO ".$db->tables["pub_categs"]." 
				(`id_pub`, `id_categ`)
				VALUES('".$nid."', '".$news_id."') ";
		$db->query($sql);
  
  
	}
	return;	
}


function in_changes($where, $id, $what, $comment, $who){
	global $db;
	$sql = "INSERT INTO ".$db->tables['changes']." 
			(`where_changed`, `where_id`, `who_changed`, 
			`type_changes`, `date_insert`, `comment`) 
			VALUES('".$where."', '".$id."', 
			'".$who."', '".$what."', 
			'".date("Y-m-d H:i:s")."', 
			'".$db->escape($comment)."')
	";
	$db->query($sql);
	return;
}


?>