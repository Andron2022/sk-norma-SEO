<?php
/*
  ok version
  last updated 20.02.2020
  added: connections site-org
  smsc settings - hint 
  other vars with the same name
  03.02.2017 - added list of files and fotos
*/
if(!defined('SIMPLA_ADMIN')){ die(); }

  global $db, $admin_vars, $tpl, $lang, $path;
  $mode = $admin_vars["uri"]["mode"];
  $do = $admin_vars["uri"]["do"];
  $id = $admin_vars["uri"]["id"];
  $page = $admin_vars["uri"]["page"];
  $hint = !empty($_GET['hint']) ? trim($_GET['hint']) : '';
  
  if($do == 'payments'){
	  include('procedure_payments.php');
	  return;
  }
  
  if($do == 'statuses'){
	  include('procedure_statuses.php');
	  return;
  }

  $str_content = "";
  if($do == "site"){
    $str_content = show_settings($mode, $id);
  }elseif($do == "email"){
    $str_content = test_email();
  }elseif($do == "users"){
    $str_content = list_users($page);
  }elseif($do == "tpl"){
    $str_content = show_tpl();
  }elseif($do == "files"){
    $str_content = list_files($do, $page);
  }elseif($do == "fotos"){
    $str_content = list_files($do, $page);
  }elseif($do == "add_user"){
    $str_content = edit_user($id,$mode);
  }elseif($do == "add_site"){
    if($admin_vars["multisite"] == 1){
      $str_content = edit_site(0);
    }else{
      $str_content = "<p>".GetMessage('admin', 'supports_only_one_website')."</p>";
      return error_not_found($str_content);
    }
  }elseif($do == "licence"){
  	$str_content .= get_licence_info();
  }elseif($do == "blocks"){
  	$str_content .= get_blocks();
  }elseif($do == "site_vars"){
  	$str_content .= site_vars();
  }elseif($do == "mass_vars"){
  	$str_content .= mass_vars($hint);
  }elseif($do == "add_site_var"){
  	$str_content .= add_site_var();
  }elseif($do == "edit_site_var"){
  	$id = get_param("id",0);
  	if($id == 0) $str_content .= add_site_var();
  	else $str_content .= edit_site_var($id);
  }else{
	
    $folders_check = array(
          ADMIN_FOLDER.'/compiled/mysql',
		  ADMIN_FOLDER.'/compiled', 'upload',
          'upload/files', 'upload/images',
          'upload/records');
		  
	$try_folder = !empty($_GET['try']) ? $_GET['try'] : '';
	$try_folder = str_replace('../','',$try_folder);
	if(!empty($try_folder) && in_array($try_folder, $folders_check)){
		correct_folder($try_folder);
	}elseif(!empty($try_folder)){
		// wrong folder
	}
	
	$f = MODULE.'/smsc_api.php';
	if(file_exists($f)){
		$sms1 = isset($site->vars['sys_smsc_login']) 
			? $site->vars['sys_smsc_login'] : ''; 
		$sms2 = isset($site->vars['sys_smsc_password'])
			? $site->vars['sys_smsc_password'] : '';
		if (!defined('SMSC_LOGIN')) { define('SMSC_LOGIN', $sms1); }
		if (!defined('SMSC_PASSWORD')) { define('SMSC_PASSWORD', $sms2); } 
		// пароль или MD5-хеш пароля в нижнем регистре
		include_once($f);
		$smsc_balance = get_balance();
		$tpl->assign("smsc_balance", $smsc_balance);
	}	
	
	/* окружение выведем в переменные */	
	$ext = get_loaded_extensions();
	natcasesort($ext);
	$settings = array(
		'php' => phpversion(),
		'os' => php_uname(),
		'os2' => PHP_OS,
		'type' => php_sapi_name(),
		'memory' => memory_get_usage(),
		'memory_peak' => memory_get_peak_usage(true),
		
		'mbstring' => mb_get_info(),
		'open_basedir' => ini_get('open_basedir'),
		'default_charset' => ini_get('default_charset'),
		'post_max_size' => ini_get('post_max_size'),
		'register_globals' => ini_get('register_globals'),
		'realpath_cache_size' => ini_get('realpath_cache_size'),
		'realpath_cache_ttl' => ini_get('realpath_cache_ttl'),
		'memory_limit' => ini_get('memory_limit'),
		
		'file_uploads' => ini_get('file_uploads'),
		'upload_tmp_dir' => ini_get('upload_tmp_dir'),
		'upload_max_filesize' => ini_get('upload_max_filesize'),
		'max_file_uploads' => ini_get('max_file_uploads'),
		'ext' => $ext

	);
	$tpl->assign("settings", $settings);
    
	$allsites = $db->get_results("SELECT v.value as yml, s.*, c.title as categ_title  
    FROM ".$db->tables['site_info']." s 
    LEFT JOIN ".$db->tables['site_vars']." v ON (v.`name` = 'yml_key' AND (s.id = v.site_id OR v.site_id = '0'))  
	LEFT JOIN ".$db->tables['categs']." c ON (s.`default_id_categ` = c.id)  
    ORDER BY c.sort, s.id
    ", ARRAY_A);
	
    $allsites1 = $db->get_results("SELECT v.value as yml, s.*  
    FROM ".$db->tables['site_info']." s 
    LEFT JOIN ".$db->tables['site_vars']." v ON (v.`name` = 'yml_key' AND (s.id = v.site_id OR v.site_id = '0'))  
    GROUP BY s.id ORDER BY s.id ", ARRAY_A);
	  
    $check_folders = scan_dir($folders_check,'../');
	
	/* подсчет размера папок с файлами */
	/* если менее недели назад подсчитывали, то выведем их */
	$size_r = $db->get_row("SELECT 
			date_insert as date,
			comment as size 
		FROM ".$db->tables['changes']." 
		WHERE `where_changed` = 'records' 
		AND type_changes = 'filesize' 
		ORDER BY date_insert DESC 
		LIMIT 0,1
	", ARRAY_A);
	
	$size_f = $db->get_row("SELECT 
			date_insert as date,
			comment as size 
		FROM ".$db->tables['changes']." 
		WHERE `where_changed` = 'files' 
		AND type_changes = 'filesize' 
		ORDER BY date_insert DESC 
		LIMIT 0,1
	", ARRAY_A);
	
	if(empty($size_r) || (!empty($_GET['update']) && $_GET['update'] == 'records')){
		$size_r = array(
			'size' => show_size('../upload/records/'),
			'date' => date('Y-m-d H:i:s')
		);
		/* удалим предыдущие записи */
		$db->query("DELETE FROM ".$db->tables['changes']." 
			WHERE `where_changed` = 'records' AND type_changes = 'filesize'");
		/* запишем новые */
		register_changes('records', '1000', 'filesize', $size_r['size']);		
	}
	
	if(empty($size_f) || (!empty($_GET['update']) && $_GET['update'] == 'files')){
		$size_f = array(
			'size' => show_size('../upload/files'),
			'date' => date('Y-m-d H:i:s')
		);
		/* удалим предыдущие записи */
		$db->query("DELETE FROM ".$db->tables['changes']." 
			WHERE `where_changed` = 'files' AND type_changes = 'filesize'");
		/* запишем новые */
		register_changes('files', '1000', 'filesize', $size_f['size']);	
	}
	$tpl->assign("sizerecords", $size_r);
    $tpl->assign("sizefiles", $size_f);
    
    $tpl->assign("allsites", $allsites);
    $tpl->assign("check_folders", $check_folders);
    //$tpl->assign("last_pubs", $last_pubs);
    $str_content = $tpl->display("settings/index.html");
	return 'dfghj';
  }
  
	/* ок */  
	function correct_folder($f){
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
  
  /* OK */
  /* функция тестирует отправку email-уведомлений */
  function test_email()
  {
    global $tpl, $site_vars;
    
    if(!empty($_POST["emails_to_send"]) 
        && !empty($_POST["text_to_send"]) 
        && !empty($_POST["send"])){

      // Тестируем отправку сообщений
    
      $body = nl2br(trim($_POST["text_to_send"])); 
      $from_name = $site_vars['name_short'];  
      $from = $site_vars['email_info']; 
      $subject = $site_vars['name_short'].": test notification";
      
      $str_all = nl2br($_POST["emails_to_send"]);
      $ar_all = explode("<br />",$str_all);
      foreach($ar_all as $k => $v){
        $v = trim($v);
        send_notification2($subject, $body, $v, $v);
      } 
      header("Location: ?action=settings&do=email&sent=1");
      exit;    
    }      
    
    return $tpl->display("settings/test_email.html");  
  }

  // проверяем в массиве все папки на открытость на запись
  // возвращаем также массив тех папок, которые закрыты
  //Функция сканирует от указанной папки, либо от каталога, в котором лежит данный файл
  function scan_dir($dirnames, $root = './')  
  {
  	$arr = array();
  	foreach ($dirnames as $dir)
  	{
  		$dir = $root . $dir;
  		if (file_exists($dir) && $dir != '.' && $dir != '..')
  		{
            $fileperms = substr ( decoct ( fileperms ( $dir ) ), 2, 6 );
            if ( strlen ( $fileperms ) == '3' )
            {
                $fileperms = '0' . $fileperms;
            }

            if ($fileperms != '0777')
            {
                $dir = str_replace($root, '', $dir);
                $arr[] = $dir;
            }
  		}else{
			$arr[] = $dir;
		}
  	}
    return $arr;
  }



// Показывает текущие настройки и управляет их изменением, добавлением и удалением
function show_settings($mode,$id)
{
  if($mode == ""){ // Show main site settings
    return show_default_site_settings();
  }elseif($mode == "add"){
    return edit_site(0);
  }elseif($mode == "edit"){
    return edit_site($id);
  }elseif($mode == "save"){
    return save_site($id);
  }else{
    return "<p><b>Неизвестная операция</b></p>";
  }
}

// Показывает список пользователей
function list_users($page)
{
	global $tpl, $db;
	$query_num = "SELECT COUNT(*) 
          FROM ".$db->tables['users']." as u ";
	$query = "SELECT u.*,p.*
          FROM ".$db->tables['users']." as u
		  LEFT JOIN ".$db->tables['users_prava']." p ON (p.bo_userid = u.id) ";
		  
	$href = "?action=settings&do=users";
	if(!empty($_GET['admin'])){
		$href .= "&admin=1";
		$query_num .= " WHERE u.`admin` = '1' ";
		$query .= " WHERE u.`admin` = '1' ";
	}elseif(!empty($_GET['news'])){
		$href .= "&news=1";
		$query_num .= " WHERE u.`news` = '1' ";
		$query .= " WHERE u.`news` = '1' ";
	}elseif(!empty($_GET['all'])){
		$href .= "&all=1";
		$query_num .= " WHERE u.`admin` = '0' AND u.news = '0' ";
		$query .= " WHERE u.`admin` = '0' AND u.news = '0' ";
	}else{
		$query_num .= " WHERE u.`admin` = '1' OR u.news = '1' ";		
		$query .= " WHERE u.`admin` = '1' OR u.news = '1' ";
	}
	
	$all_results = $db->get_var($query_num);
	$query .= " order by u.admin desc, u.`name`, u.login
          ";
	$onpage = ONPAGE;
	$start = $page*$onpage;
	$query .= " LIMIT ".$start.", ".$onpage;
	$pages = ceil($all_results/$onpage);
	$href .= "&page=";
	if($pages > 1){
		$tpl->assign("page", $page);
		$tpl->assign("npages", $pages);
		$tpl->assign("href", $href);
	}
	
		  
  $rows = $db->get_results($query, ARRAY_A);
  $ar = array();
  if($db->num_rows >0){
    foreach($rows as $row){
        if(!file_exists(UPLOAD."/avatars/mini/".md5($row['id']).".jpg")){
          $row['avatar'] = "";
        }else{
          $row['avatar'] = "/upload/avatars/mini/".md5($row['id']).".jpg";
        } 
        $ar[] = $row;
    }
  }
  
  $tpl->assign("list_users", $ar);
  return $tpl->display("settings/list_users.html");
}

// Добавление, изменение и удаление пользователей системы
function edit_user($id,$mode)
{
  global $tpl, $db, $admin_vars, $site;
  $query = "SELECT *
          FROM ".$db->tables['users']." as u, ".$db->tables['users_prava']." as p
          WHERE u.id = p.bo_userid AND u.id = '".$id."' 
          ";

  $query = "SELECT u.*, p.*, s.site_url
          FROM ".$db->tables['users']." as u
		  LEFT JOIN ".$db->tables['users_prava']." p ON (p.bo_userid = u.id)
		  LEFT JOIN ".$db->tables['site_info']." s ON (u.site_id = s.id)
          WHERE u.id = '".$id."' 
          ";
		  
		  
  $ar_user = $db->get_row($query, ARRAY_A);
  //$db->debug(); exit;
  
  if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }   
  if($id > 0 && $db->num_rows == 0){
    return error_404(GetMessage('admin','record_not_found')); 
  }

  if($id > 0){
	  if(!empty($ar_user['last_login']) && $ar_user['last_login'] != '0000-00-00 00:00:00'){
		$ar_user['last_login'] = date($site->vars['site_date_format'].' '.$site->vars['site_time_format'], strtotime($ar_user['last_login']));		  
	  }else{
		$ar_user['last_login'] = '';  
	  }
	
	$ar_user['date_insert'] = date($site->vars['site_date_format'].' '.$site->vars['site_time_format'], strtotime($ar_user['date_insert']));
	
	
    $query = "SELECT * FROM ".$db->tables['users_prava']." 
            WHERE bo_userid = '".$id."' ";
    $ar_user['prava'] = $db->get_row($query, ARRAY_A);
	if($db->num_rows == 0){
		// не создана запись с правами
		$db->query("INSERT INTO ".$db->tables['users_prava']." 
			(`bo_userid`) VALUES('".$id."') ");
		$query = "SELECT * FROM ".$db->tables['users_prava']." 
            WHERE bo_userid = '".$id."' ";
		$ar_user['prava'] = $db->get_row($query, ARRAY_A);			
	}
  }else{
    $ar_user = array('name' => '', 'login' => '',
        'passwd' => '', 'email' => '', 'phone_mobil' => '',
        'icq' => '', 'memo' => '', 'id' => 0, 'news' => 1, 
		'admin' => 0, 'site_url' => '', 'site_id' => 0, 
		'country' => '', 'city' => '',
		'birth_day' => '0000-00-00', 
		'user_interes' => '',
		'user_about' => '', 
		'url' => '', 'pers_hi' => '', 
		'user_title' => '', 
		'active' => 0, 'gender' => '-', 
		'ref1' => 0, 'ref2' => 0, 

		
	);
		
    $ar = array();
    $rows = $db->get_results("SHOW FIELDS FROM ".$db->tables['users_prava']." WHERE Field <> 'bo_userid' ");
    foreach($rows as $row){
      $ar[$row->Field] = 0;
    }
    $ar_user['prava'] = $ar;
  }
  if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }   

  $a = "bgcolor=".$admin_vars['bglight'];
  $tpl->assign("pravo", GetMessage('admin','prava'));
  $tpl->assign("a", $a);

  if(isset($_POST["save"]) || isset($_POST["del"])){
    return save_user($id);
  }

    if(!file_exists(UPLOAD."/avatars/small/".md5($id).".jpg")){
        $ar_user['avatar'] = "";
    }else{
        $ar_user['avatar'] = "/upload/avatars/small/".md5($id).".jpg";
    }                     

  $tpl->assign("site", $site->vars);
  $tpl->assign("user", $ar_user);
  
  return $tpl->display("settings/edit_user.html");
}


/* ok */
function create_new_avatar($id)
{
    global $db, $site_vars, $admin_vars;
	if(isset($_POST["del_avatar"])){        
		@unlink(UPLOAD."/avatars/mini/".md5($id).".jpg");
		@unlink(UPLOAD."/avatars/small/".md5($id).".jpg");
		@unlink(UPLOAD."/avatars/big/".md5($id).".jpg");
	}

    if(empty($_FILES["add_avatar"]["size"])){ return; }
    $sizes = array();
    if(isset($site_vars['img_size1'])){ $sizes['mini'] = $site_vars['img_size1']; }
    if(isset($site_vars['img_size2'])){ $sizes['small'] = $site_vars['img_size2']; }
    if(isset($site_vars['img_size3'])){ $sizes['big'] = $site_vars['img_size3']; }
    
    $size =@ GetImageSize($_FILES["add_avatar"]["tmp_name"]);
    if(!$size){ return; }
              
	// пройдемся по массиву заданных размеров
    foreach($sizes as $k => $v){
        $new_file_name = "../upload/avatars/".$k."/".md5($id).".jpg";
        if($size[0] >= $v['width'] || $size[1] >= $v['height']){
            // посчитаем какие размеры у файлов будут
            if($v['width'] > 0 && $v['height'] > 0){
                $width = $v['width'];
                $height = $v['height'];
            }else{
                if($v['width'] > 0){
                  $width = $v['width'];
                  $height = $width*$size[1]/$size[0];
                }else{
                  $height = $v['height'];
                  $width = $height*$size[0]/$size[1];
                }
            }
            
            admin_crop_image($_FILES["add_avatar"]['tmp_name'],
                $new_file_name,
                intval($width), intval($height),
                $size); 
        }else{
            admin_crop_image($_FILES["add_avatar"]['tmp_name'],
                $new_file_name,
                intval($size[0]), intval($size[1]),
                $size); 
        
        }
    }
            
	return;
}


/* ok */
function save_user($id)
{
  global $db;
  $posted = $_POST;
  if(isset($_POST["save"]) || isset($_POST["del"])){
    if($id == 0 && isset($posted["save"])){ return add_new_user($posted); }
    elseif(isset($posted["save"])){ return update_user_info($id, $posted); }
    elseif(isset($posted["del"]) && $id > 0){
    
        @unlink(UPLOAD."/avatars/mini/".md5($id).".jpg");
		@unlink(UPLOAD."/avatars/small/".md5($id).".jpg");
		@unlink(UPLOAD."/avatars/big/".md5($id).".jpg");
      $db->query("DELETE FROM ".$db->tables['users_prava']." WHERE bo_userid = '".$id."' ");
      $db->query("DELETE FROM ".$db->tables['users']." WHERE id = '".$id."' ");
      if(!empty($db->last_error)){ return db_error(basename(__FILE__).": 153"); }
	  clear_cache(0);
      header("Location: ?action=settings&do=users&deleted=1");
      exit; 
    }
  }else{
    return "<p>Информация успешно обработана!</p>";
  }
}

/* ok */
function add_new_user($posted)
{
  global $db;
  $passwd = trim($posted["passwd"]);
  $passwd = encode_str($passwd);
  $admin = !empty($posted['admin']) ? intval($posted['admin']) : 0;
  $active = !empty($posted['active']) ? intval($posted['active']) : 0;
  $news = !empty($posted['news']) ? intval($posted['news']) : 0;
  
  $r_country = !empty($posted['country']) ? trim($posted['country']) : '';
  $r_city = !empty($posted['city']) ? trim($posted['city']) : '';
  
  if(is_array($posted['birth_day'])){
	  
	$r_birth_day = !empty($posted['birth_day']['Year']) 
		? $posted['birth_day']['Year'] : '0000';
		
	if(!empty($posted['birth_day']['Month'])){
		$r_birth_day .= '-'.$posted['birth_day']['Month'];
	}else{ $r_birth_day .= '-00'; }
	
	if(!empty($posted['birth_day']['Day'])){
		$r_birth_day .= '-'.$posted['birth_day']['Day'];
	}else{ $r_birth_day .= '-00'; }
	
  }else{
		$r_birth_day = !empty($posted['birth_day']) 
			? trim($posted['birth_day']) : '0000-00-00';  
  }
  
  $r_user_interes = !empty($posted['user_interes']) ? trim($posted['user_interes']) : '';
  $r_user_about = !empty($posted['user_about']) ? trim($posted['user_about']) : '';
  $r_url = !empty($posted['url']) ? trim($posted['url']) : '';
  $r_pers_hi = !empty($posted['pers_hi']) ? trim($posted['pers_hi']) : '';
  $r_gender = !empty($posted['gender']) ? strtolower(trim($posted['gender'])) : '-';
  $r_user_title = !empty($posted['user_title']) ? trim($posted['user_title']) : '';
  $r_site_id = !empty($posted['site_id']) ? intval($posted['site_id']) : '0';
  $r_ref1 = !empty($posted['ref1']) ? intval($posted['ref1']) : '0';
  $r_ref2 = !empty($posted['ref2']) ? intval($posted['ref2']) : '0';
  
  $query = "INSERT INTO ".$db->tables['users']." (`name`, `login`, `passwd`,
            `email`, `phone_mobil`, `icq`, `memo`, `admin`, `date_insert`, 
			`country`, `city`, `birth_day`, `user_interes`, `user_about`, 
			`url`, `pers_hi`, `gender`, `user_title`, `site_id`, 
			`ref1`, `ref2`, `active`, `news`
			) VALUES(
            '".$db->escape($posted["name"])."',
            '".$db->escape($posted["login"])."',
            '".$passwd."',
            '".$db->escape($posted["email"])."',
            '".$db->escape($posted["phone_mobil"])."',
            '".$db->escape($posted["icq"])."',
            '".$db->escape($posted["memo"])."',
            '".$admin."', '".date('Y-m-d H:i:s')."', 			
			'".$db->escape($r_country)."',
			'".$db->escape($r_city)."',
			'".$db->escape($r_birth_day)."',
			'".$db->escape($r_user_interes)."',
			'".$db->escape($r_user_about)."',
			'".$db->escape($r_url)."',
			'".$db->escape($r_pers_hi)."',
			'".$db->escape($r_gender)."',
			'".$db->escape($r_user_title)."',
			'".$db->escape($r_site_id)."',
			'".$db->escape($r_ref1)."',
			'".$db->escape($r_ref2)."',
			'".$active."', '".$news."'
            ) ";
  $db->query($query);
  if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }
  $id = $db->insert_id;
  $db->query("INSERT INTO ".$db->tables['users_prava']." (bo_userid) VALUES ('".$id."') ");
  if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }

  if(isset($posted["pravo"])){
    foreach($posted['pravo'] as $k=>$v){
      $var = $v == 1 ? 1 : 0;
      $db->query("UPDATE ".$db->tables['users_prava']." SET `".$k."` = '".$var."' WHERE bo_userid = '".$id."' ");
      if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }
    }
  }
    create_new_avatar($id);
  header("Location: ?action=settings&do=add_user&id=".$id."&added=1");
  exit;
}

/* ok */
function update_user_info($id, $posted)
{
  global $db;
  $passwd = trim($posted["passwd"]);
  $admin = !empty($posted['admin']) ? intval($posted['admin']) : 0;
  $active = !empty($posted['active']) ? intval($posted['active']) : 0;
  $news = !empty($posted['news']) ? intval($posted['news']) : 0;
  if($id == 0){ return add_new_user($posted); }
  
  $r_country = !empty($posted['country']) ? trim($posted['country']) : '';
  $r_city = !empty($posted['city']) ? trim($posted['city']) : '';
  
  if(is_array($posted['birth_day'])){
	  
	$r_birth_day = !empty($posted['birth_day']['Year']) 
		? $posted['birth_day']['Year'] : '0000';
		
	if(!empty($posted['birth_day']['Month'])){
		$r_birth_day .= '-'.$posted['birth_day']['Month'];
	}else{ $r_birth_day .= '-00'; }
	
	if(!empty($posted['birth_day']['Day'])){
		$r_birth_day .= '-'.$posted['birth_day']['Day'];
	}else{ $r_birth_day .= '-00'; }
	
  }else{
		$r_birth_day = !empty($posted['birth_day']) 
			? trim($posted['birth_day']) : '0000-00-00';  
  }
  
  $r_user_interes = !empty($posted['user_interes']) ? trim($posted['user_interes']) : '';
  $r_user_about = !empty($posted['user_about']) ? trim($posted['user_about']) : '';
  $r_url = !empty($posted['url']) ? trim($posted['url']) : '';
  $r_pers_hi = !empty($posted['pers_hi']) ? trim($posted['pers_hi']) : '';
  $r_gender = !empty($posted['gender']) ? strtolower(trim($posted['gender'])) : '-';
  $r_user_title = !empty($posted['user_title']) ? trim($posted['user_title']) : '';
  $r_site_id = !empty($posted['site_id']) ? intval($posted['site_id']) : '0';  
  
  $query = "UPDATE ".$db->tables['users']." SET
            `name` = '".$db->escape($posted["name"])."',
            `login` = '".$db->escape($posted["login"])."',
            `email` = '".$db->escape($posted["email"])."',
            `phone_mobil` = '".$db->escape($posted["phone_mobil"])."',
            `admin` = '".$posted["admin"]."',
            `icq` = '".$db->escape($posted["icq"])."',
            `memo` = '".$db->escape($posted["memo"])."', 
			
			`country` = '".$db->escape($r_country)."',
			`city` = '".$db->escape($r_city)."',
			`birth_day` = '".$db->escape($r_birth_day)."',
			`user_interes` = '".$db->escape($r_user_interes)."',
			`user_about` = '".$db->escape($r_user_about)."',
			`url` = '".$db->escape($r_url)."',
			`pers_hi` = '".$db->escape($r_pers_hi)."',
			`gender` = '".$db->escape($r_gender)."',
			`user_title` = '".$db->escape($r_user_title)."',
			`site_id` = '".$db->escape($r_site_id)."',
			
			`active` = '".$active."',
			`news` =  '".$news."'
            WHERE id = '".$id."'
            ";
  $db->query($query);
  if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }
  
  if(!empty($passwd)){
    $passwd = encode_str($passwd);
    $db->query("UPDATE ".$db->tables['users']."  
        SET `passwd` = '".$passwd."' 
        WHERE id = '".$id."' ");    
  }

  if(isset($posted["pravo"])){
    foreach($posted['pravo'] as $k=>$v){
      $var = $v == 1 ? 1 : 0;
      $db->query("UPDATE ".$db->tables['users_prava']." SET `".$k."` = '".$var."' WHERE bo_userid = '".$id."' ");
      if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }
    }
  }
  
  create_new_avatar($id);
  clear_cache(0);
  header("Location: ?action=settings&do=add_user&id=".$id."&updated=1");
  exit;
}

/* ok */
function show_default_site_settings()
{
  global $db, $tpl;
  $rows = $db->get_results("SELECT * FROM ".$db->tables['site_info']." ", ARRAY_A);
  if(!empty($db->last_error)){ return db_error(basename(__FILE__).": ".__LINE__); }
  if($db->num_rows == 0){
    return "Ваш сайт пока не сконфигурирован.<br/>
    Сделайте это <a href=?action=settings&do=site&mode=add>сейчас</a>!";
  }

  $tpl->assign("sites_working", $db->num_rows);
  return list_sites();
}

/* ok */
function edit_site($id)
{
  global $tpl, $db, $lang, $admin_vars;
  require('inc/lang.php');
  require('inc/timezone.php');
  require('inc/dateformat.php');
  require('inc/timeformat.php');
  $admin_vars['timeformat'] = $array_timeformat_settings;
  $admin_vars['dateformat'] = $array_dateformat_settings;
  $admin_vars['timezone'] = $array_timezone_settings;
  $admin_vars['sitelangs'] = $array_lang_settings;    
  $tpl->assign("admin_vars", $admin_vars);
  
  $tpl->assign("id", $id);
  $ar_site = $db->get_row("SELECT * FROM ".$db->tables['site_info']." WHERE id = '".$id."' ", ARRAY_A);
  if(!empty($db->last_error)){ return db_error(basename(__FILE__).": ".__LINE__); }
  $tpl->assign("site", $ar_site);
  if($id > 0 && $db->num_rows == 0){
    return error_404(GetMessage('admin','record_not_found')); 
  }
  
  $a = "bgcolor=".$admin_vars['bglight'];
  $tpl->assign("a", $a);
  
  /* site->org */
	$sql = "SELECT o.id, o.title, o.active, o.is_default, o.inn, c.id as connected  
		FROM ".$db->tables['org']." o 
		LEFT JOIN ".$db->tables['connections']." c ON (o.id = c.id1 
			AND c.name1 = 'org' AND id2 = '".$id."' AND name2 = 'site')
		WHERE o.`own` = '1' 
		ORDER BY o.`active` desc, o.`is_default` desc, o.`title`
	";
	$o_rows = $db->get_results($sql, ARRAY_A);	
	$tpl->assign("orgs", $o_rows);	

  $list_categs = $db->get_results("SELECT c.id, c.title as name, c.active, c2.title as subname 
      FROM ".$db->tables['categs']." c 
      LEFT JOIN ".$db->tables['categs']." c2 on (c2.id = c.id_parent) 
      ORDER BY c.id_parent, c.sort ", ARRAY_A);
  
  $tpl->assign("list_categs", $list_categs);
  $tpls = array();

    if ($handle = opendir("../tpl/")) {
       while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && substr($file, 0, 1) != "_") {
                //Проверяем, файл или директория и заносим в разные мыссивы
                $check = '../tpl/' . $file;
                $check = str_replace('../tpl/../tpl/','../tpl/',$check);
                if (is_dir($check)) {
                    $tpls[] = $file;
                }
            }
        }
        natcasesort($tpls);
        closedir($handle);
    }

  $tpl->assign("tpls", $tpls);

  if(isset($_POST["save"]) && $id == 0){
    $str = add_new_site();
  }elseif(isset($_POST["save"]) && $id > 0){
    $str = update_site_info($id);
  }elseif(isset($_POST["del"])){
    $str = delete_site_info($id);
  }

  $str = $tpl->display("settings/edit_site.html");
  return $str;

}


/* ok */
function add_new_site()
{
  $posted = $_POST;  
  if(!isset($posted["gallery"])){ $posted["gallery"] = 0; }
  if(!isset($posted["mode_basket"])){ $posted["mode_basket"] = 0; }
  if(!isset($posted["feedback_in_db"])){ $posted["feedback_in_db"] = 0; }
  if(!isset($posted["default_id_categ"])){ $posted["default_id_categ"] = 0; }
  if(!isset($posted["site_active"])){ $posted["site_active"] = 0; }
           
  global $db;
  $query = "INSERT INTO ".$db->tables['site_info']."
	         (`name_full`, `name_short`, `site_url`, `email_info`,
           `phone`, `meta_keywords`, `meta_description`, 
           `meta_title`, `office_ip`, `template_path`, 
           site_active, site_charset, lang, site_time_zone,
           site_date_format, site_time_format, site_description,
           gallery, mode_basket, feedback_in_db,
           default_id_categ)
           VALUES( '".$db->escape($posted["name_full"])."',
           '".$db->escape($posted["name_short"])."',
           '".$db->escape($posted["site_url"])."',
           '".$db->escape($posted["email_info"])."',
           '".$db->escape($posted["phone"])."',
           '".$db->escape($posted["meta_keywords"])."',
           '".$db->escape($posted["meta_description"])."',
           '".$db->escape($posted["meta_title"])."',
           '".$db->escape($posted["office_ip"])."',
           '".$db->escape($posted["template_path"])."',
           '".$db->escape($posted["site_active"])."',
           '".$db->escape($posted["site_charset"])."',
           '".$db->escape($posted["lang"])."',
           '".$db->escape($posted["site_time_zone"])."',
           '".$db->escape($posted["site_date_format"])."',
           '".$db->escape($posted["site_time_format"])."',
           '".$db->escape($posted["site_description"])."',
           '".$db->escape($posted["gallery"])."',
           '".$db->escape($posted["mode_basket"])."',
           '".$db->escape($posted["feedback_in_db"])."',
           '".$db->escape($posted["default_id_categ"])."'
           ) ";

  $db->query($query);
  if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }
	$id = $db->insert_id;
	if(!empty($_POST["orgs"])){
		foreach($_POST["orgs"] as $v){
			$sql = "INSERT INTO ".$db->tables['connections']." 
				(id1, name1, id2, name2) 
				VALUES ('".$v."', 'org', '".$id."', 'site')
			";
			$db->query($sql);
		}
	}  
  
  $href = "?action=settings&do=site&mode=edit&id=".$id."&added=1";
  header("Location: ".$href);
  exit;
}


/* ok */
function update_site_info($id)
{
  global $db;
  $posted = $_POST;
  if(!isset($posted["gallery"])){ $posted["gallery"] = 0; }
  if(!isset($posted["mode_basket"])){ $posted["mode_basket"] = 0; }
  if(!isset($posted["feedback_in_db"])){ $posted["feedback_in_db"] = 0; }
  if(!isset($posted["default_id_categ"])){ $posted["default_id_categ"] = 0; }
  if(!isset($posted["site_active"])){ $posted["site_active"] = 0; }

	if(!empty($_POST["orgs"])){
		$db->query("DELETE FROM ".$db->tables['connections']." 
			WHERE `name1` = 'org' 
				AND `id2` = '".$id."' 
				AND `name2` = 'site'
		");
		
		foreach($_POST["orgs"] as $v){
			$sql = "INSERT INTO ".$db->tables['connections']." 
				(id1, name1, id2, name2) 
				VALUES ('".$v."', 'org', '".$id."', 'site')
			";
			$db->query($sql);
			if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }
		}
	}else{
		$db->query("DELETE FROM ".$db->tables['connections']." 
			WHERE `name1` = 'org' 
				AND `id2` = '".$id."' 
				AND `name2` = 'site'
		");
	}
			

  $query = "UPDATE ".$db->tables['site_info']." SET
	         `name_full` = '".$db->escape($posted["name_full"])."',
           `name_short` = '".$db->escape($posted["name_short"])."',
           `site_url` = '".$db->escape($posted["site_url"])."',
           `email_info` = '".$db->escape($posted["email_info"])."',
           `phone` = '".$db->escape($posted["phone"])."',
           `meta_keywords` = '".$db->escape($posted["meta_keywords"])."',
           `meta_description` = '".$db->escape($posted["meta_description"])."',
           `meta_title` = '".$db->escape($posted["meta_title"])."',
           `office_ip` = '".$db->escape($posted["office_ip"])."',
           `template_path` = '".$db->escape($posted["template_path"])."',

           site_active = '".$db->escape($posted["site_active"])."',
           site_charset = '".$db->escape($posted["site_charset"])."',
           lang = '".$db->escape($posted["lang"])."',
           site_time_zone = '".$db->escape($posted["site_time_zone"])."',
           site_date_format = '".$db->escape($posted["site_date_format"])."',
           site_time_format = '".$db->escape($posted["site_time_format"])."',
           site_description = '".$db->escape($posted["site_description"])."',
           gallery = '".$db->escape($posted["gallery"])."',
           mode_basket = '".$db->escape($posted["mode_basket"])."',
           feedback_in_db = '".$db->escape($posted["feedback_in_db"])."',
           default_id_categ = '".$db->escape($posted["default_id_categ"])."'

            WHERE id = '".$id."'
           ";
  $db->query($query);
  if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }

  clear_cache(0);
  $href = "./?action=settings&do=site&mode=edit&id=".$id."&updated=1";
  header("Location: $href");
  exit;
}

/* ok */
function delete_site_info($id)
{
  global $db;
  $query = "DELETE FROM ".$db->tables['site_info']." WHERE id = '".$id."' ";
  $db->query($query);

  $query = "DELETE FROM ".$db->tables['site_publications']." WHERE id_site = '".$id."' ";
  $db->query($query);

  $query = "DELETE FROM ".$db->tables['site_categ']." WHERE id_site = '".$id."' ";
  $db->query($query);

  $query = "DELETE FROM ".$db->tables['fav']." WHERE `where_placed` = 'site' AND where_id = '".$id."' ";
  $db->query($query);
  
	$db->query("DELETE FROM ".$db->tables['connections']." 
		WHERE `name1` = 'org' 
			AND `id2` = '".$id."' 
			AND `name2` = 'site'
	");
  
  
  if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }
  clear_cache(0);
  $href = "?action=settings&do=site&deleted=1";
  header("Location: $href");
  exit;
}

/* ok */
function list_sites()
{
  global $db, $tpl, $admin_vars;
  $a = "bgcolor=".$admin_vars['bgdark'];
  $tpl->assign("a", $a);
  
  $rows = $db->get_results("SELECT v.value as yml, s.*, c.title as categ_title  
    FROM ".$db->tables['site_info']." s 
    LEFT JOIN ".$db->tables['site_vars']." v ON (v.`name` = 'yml_key' AND (s.id = v.site_id OR v.site_id = '0'))  
	LEFT JOIN ".$db->tables['categs']." c ON (s.`default_id_categ` = c.id)  
    ORDER BY c.sort, s.id
    ", ARRAY_A);
	//$db->debug(); exit;
  $tpl->assign("list_sites", $rows);
  $str = $tpl->display("settings/list_sites.html");
  return $str;
}

/* ok */
function show_tpl()
{
    global $tpl;
    if(!empty($_GET["folder"]) && empty($_GET["doc"])){
        $tpl->assign("folder", $_GET["folder"]);
        $url = $_SERVER["QUERY_STRING"];
        if ($handle =@ opendir("../tpl/".$_GET["folder"]."/")) {
            $files = array();
            $folders = array();
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && substr($file, 0, 1) != "_") {
                    //Проверяем, файл или директория и заносим в разные мыссивы
                    $check = '../tpl/' . $_GET["folder"]."/" . $file;
                    $check = str_replace('//','/',$check);
                    $check = str_replace('../tpl/../tpl/','../tpl/',$check);
                    if (is_dir($check)) {
                        $folders[$file] = "?action=settings&do=tpl&folder=$check";
                    } else {
                    	if (check_perm($check)) {
                            $files[$file] = "?$url&doc=$file";
                    	} else {
                            $files[$file] = "?$url&doc=$file&edit=false";  
							// нельзя изменить
                    	}
                    }
                }
            }
            natcasesort($files);
            natcasesort($folders);
            $topfolders = explode('/', $_GET["folder"]);
            $newpath = array(); $path = '';

            foreach($topfolders as $k=>$v){
              if ($v == '..'){
                $path = '..';
              }else{
				if(!empty($path)){
					$path = $path.'/';
				}
				$path .= $v;
              }
			  if(!empty($v)) { $newpath[$v] = $path; }
            }

            //$result = array_merge($folders ,$files);
            $result = $folders + $files;
            $items = array();
            //Приводим к варианту для шаблона
            foreach ($result as $key => $value) {
                if (is_dir("../tpl/".$_GET["folder"]."/" . $key)) {
                    $items[] = array(
                        'file' => $key, 
                        'link' => $value, 
                        'img' => 'images/icon/folder.png',
                        'ext_link' => ''
                    );
                } else {
                    //if(stristr($value, 'edit=false') === FALSE) {
                    //} else{ $value = ''; }
                    $ext_link = "";
                    $ext = explode(".", $key);   
                    if(is_array($ext) && count($ext) > 1){
                      $real_ext = $ext[count($ext)-1];
                      $ar_editable = array('txt', 'htm', 'html', 'css', 'js', 'php');
                      if(!in_array($real_ext, $ar_editable)){
                        $ext_link = "../tpl/".$_GET["folder"]."/".$key;
                      }
                    }
					
					$ff = explode('.', $key);
					$ext = count($ff) > 1 ? $ff[count($ff)-1] : '';
					
                    $items[] = array(
						'file' => $key, 
						'ext' => $ext,
						'link' => $value, 
						'img' => '', 
						'ext_link' => $ext_link
					);
                }
            }
            closedir($handle);
            $tpl->assign("files", $items);
            $tpl->assign("newpath", $newpath);
            $str = $tpl->display("settings/template_files.html");
        }else{
          $tpl->assign("title", "Folder not found");
          $tpl->assign("content", "Folder ".$_GET["folder"]." not found");
          $tpl->display("empty.html");
          exit;       
        }
    }elseif(!empty($_GET["folder"]) && !empty($_GET["doc"])){
        $doc = htmlspecialchars($_GET["doc"]);
        $folder = htmlspecialchars($_GET["folder"]);
        $fname = "../tpl/".$folder."/".$doc;
        $fname = str_replace('../tpl/../tpl/', '../tpl/', $fname);
        $tpl->assign("folder", $folder);

        // check for filetype
        $ext = explode(".", $doc);   

        if(is_array($ext) && count($ext) > 1){
          $real_ext = $ext[count($ext)-1];
          $ar_editable = array('txt', 'htm', 'html', 'css', 'js', 'php');
          if(!in_array($real_ext, $ar_editable)){
            $str = "Only files ";
            foreach($ar_editable as $k => $v){
              if($k > 0){ $str .= ", ".$v; } else { $str .= $v; }              
            }
            $str .= " can be edited!";
            $tpl->assign("content", $str);
            $tpl->display("empty.html");
            exit;            
          }
        }else{
            $str = "File .".$doc." cant be edited!";
            $tpl->assign("content", $str);
            $tpl->display("empty.html");
            exit;            
        }
		
        if(isset($_POST["update_file"])){
            $tpl->assign("fname", $fname);
            $tpl->assign("folder", $folder);
            $str = update_tpl($fname);
        }elseif(is_file($fname)){
            $tpl->assign("fname", $fname);
            $tpl->assign("folder", $folder);
            $str = edit_tpl($fname);
        }else{
          $nfolder = $folder."/".$doc;
          $url = $_SERVER["QUERY_STRING"];
          $items = array();
		  if(!file_exists('../tpl/'.$nfolder)){
			$tpl->assign("title", "The file not found");
			$tpl->assign("content", "The file <b>".$doc."</b> in folder <a href='?action=settings&do=tpl&folder=".$folder."'><b>".$folder."</b></a> not found");
			$tpl->display("empty.html");
			exit;       
		  }

          if ($handle = opendir('../tpl/'.$nfolder)) {
            while (false !== ($file = readdir($handle))) {
                //$items = array();
                if ($file != "." && $file != ".." && substr($file, 0, 1) != "_") {
                    $newdoc = "$doc/$file";
                    $path1 = "../tpl/".$nfolder."/".$file."<br>";
                    $item = array();
                    $item["file"] = $file;
                    $item["link"] = "?action=settings&do=tpl&folder=$folder&doc=$newdoc";
                    if(is_file($file)){
                }else{
                    $item["img"] = '';
                }
                $items[] = $item;
                }
            }
            closedir($handle);
            $tpl->assign("templates", $items);
            return $tpl->display("settings/templates.html");
       }else{
        $tpl->assign("title", "The file not found");
        $tpl->assign("content", "The file <b>".$doc."</b> in folder <a href='?action=settings&do=tpl&folder=".$folder."'><b>".$folder."</b></a> not found");
        $tpl->display("empty.html");
        exit;       
       }
    }
    }else{
        $url = $_SERVER["QUERY_STRING"];
        $files = array();
        $folders = array();
        if ($handle = opendir('../tpl')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." 
					&& substr($file, 0, 1) != "_" 
					&& $file != "crm" 
					&& $file != "cms" 
					) {
                	//Проверяем, файл или директория и заносим в разные мыссивы
                    if (is_dir('../tpl/' . $file)) {
                    	//$folders[strval($file)] = "./?$url&folder=$file";
                    	$folders[$file] = "./?$url&folder=$file";
                    } else {
                        if (check_perm('../tpl/' . $file)) {
                            $files[$file] = "./?$url&doc=$file";
                        } else {
                            $files[$file] = "";
                        }
                    }

                }
            }
            closedir($handle);
            //Сортируем
            natcasesort($files);
            natcasesort($folders);
            $result = $folders + $files;
            
            $items = array();
            //Приводим к варианту для шаблона
            foreach ($result as $key => $value) {
            	if (is_dir('../tpl/' . $key)) {
            		$items[] = array('file' => $key, 'link' => $value, 'img' => 'images/icon/folder.png');
            	} else {

            	   $items[] = array('file' => $key, 'link' => $value, 'img' => '');
            	}
            }
            $tpl->assign("templates", $items);
        }
        $str = $tpl->display("settings/templates.html");
    }
    return $str;
}

/* ok */
function check_perm($dir)
{
      if (file_exists($dir) && $dir != '.' && $dir != '..')
      {
          $fileperms = substr ( decoct ( fileperms ( $dir ) ), 2, 6 );
          if ( strlen ( $fileperms ) == '3' )
          {
              $fileperms = '0' . $fileperms;
          }
          if ($fileperms != '0777')
          {
              return false;
          } else {
          	return true;
          }
      } else {
      	return false;
      }
}

/* ok */
function edit_tpl($fname, $updated="", $error="")
{
	global $tpl;
	if(!file_exists($fname)){ $error = "<p>Файл $fname не найден на сервере!</p>"; }
	if(!is_writable($fname)){ $error = "<p>Файл $fname не доступен для изменений!</p>"; }
	$tpl->assign("messages",$error);
	$tpl->assign("updated",$updated);
	$fcontent = htmlspecialchars(stripslashes(file_get_contents($fname)));
	$tpl->assign("fcontent", $fcontent);
	return $tpl->display("settings/edit_template.html");
}

/* ok */
function update_tpl($fname)
{
	if(!empty($_GET["fcopy"]) && !empty($_POST["new_filename"])
		&& !empty($_POST["folder_filename"]) && isset($_POST["tpl"])
	){
		$nfile  = preg_replace('/[^a-z0-9_.]+/is', '', trim($_POST["new_filename"]));
		$nfile = strtolower($nfile);
		$nfolder = trim($_POST["folder_filename"]);
		$ff = explode('.',$nfile);
		if(count($ff) < 2 || $ff[count($ff)-1] != 'html'){
			return edit_tpl($fname, 
				GetMessage('admin','tpl','wrong_filename'), 
			"");
		}
		if(!empty($nfile) && !empty($nfolder)){
			$file = $nfolder.'/'.$nfile;
			//если файла нету... тогда
			if (!file_exists($file)) {
				$fp = fopen($file, "w"); 
				fwrite($fp, stripslashes($_POST["tpl"]));
				fclose($fp);				
				$url = '?action=settings&do=tpl&folder='.$nfolder.'&doc='.$nfile.'&added=1';
				header('Location: '.$url);
				exit;
			}else{
				return edit_tpl($fname, 
					GetMessage('admin','tpl','filename_exists'), 
				"");
			}			
		}
	}

	$fcontent = stripslashes($_POST["tpl"]);
	if (!$handle = fopen($fname, 'w+')) {
    $str = "<b><font color=red>Невозможно найти файл ($fname)</font></b>";
    return edit_tpl($fname, "", $str);
  }

  // Write $somecontent to our opened file.
  if (!fwrite($handle, $fcontent)) {
    $str = "<b><font color=red>Невозможно изменить файл ($fname)</font></b>";
    return edit_tpl($fname, "", $str);
  }

  $str = _updated("Файл $fname успешно обновлен!");
  fclose($handle);
  return edit_tpl($fname, $str);
}

/* ok */
function site_vars()
{
	$str = "";
	if(isset($_GET["id"]))
	{
		$id = intval($_GET["id"]);
		if($id == 0) $str .= add_site_var();
		else $str .= edit_site_var($id);
	}
	else $str .= list_site_vars();
	return $str;
}


/* ok */
function list_site_vars()
{
	global $db, $tpl;
	$str = $str1 = "";
	$sites = $db->get_results("SELECT * FROM ".$db->tables['site_info']." ORDER BY `site_url`", ARRAY_A);
	$tpl->assign('sites', $sites);
	if(get_param("delete","") != "")
	{
		$del_ar = get_param("del",array());
		if(count($del_ar))
		{
			$ids = implode4mysql($del_ar);
			$db->query("DELETE FROM ".$db->tables['site_vars']." WHERE id in ($ids)");
      if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }
			$href = ".?action=settings&do=site_vars&deleted=".$db->rows_affected;
			_redirect($href);
		}
	}
	$str_sid = '';
	if(isset($_GET['site_id']) && $_GET['site_id'] != "-1"){
	   $site_id = intval($_GET['site_id']);
	   $str_sid = " AND v.site_id = '".$site_id."' ";
  }
  $tpl->assign("messages", $str1);
  
	$page = !empty($_GET['page']) ? intval($_GET['page']) : 0;
	$onpage = ONPAGE;
	$start = $page*$onpage;
	$href = "?action=settings&do=site_vars";
	
  if(isset($_GET['q']) && !empty($_GET['q'])){ 
		if(isset($_GET['site_id'])){
			$href .= "&site_id=".$_GET['site_id'];
		}
		$href .= "&q=".$_GET['q'];
		
		$all_results = $db->get_var("SELECT COUNT(*)
			FROM ".$db->tables['site_vars']." v 
			LEFT JOIN ".$db->tables['site_info']." s on (s.id = v.site_id) 
			WHERE 
				(v.`name` NOT LIKE 'img_cmslogo'
					AND
				(v.`name` LIKE '%".$db->escape(trim($_GET['q']))."%' 
				OR v.`value` LIKE '%".$db->escape(trim($_GET['q']))."%' 
				OR v.`description` LIKE '%".$db->escape(trim($_GET['q']))."%')
				) OR (v.`name` LIKE '".$db->escape(trim($_GET['q']))."') 
			");
		
    	$rows = $db->get_results("SELECT v.*, s.site_url as website FROM ".$db->tables['site_vars']." v 
        LEFT JOIN ".$db->tables['site_info']." s on (s.id = v.site_id) 
        WHERE 
          (v.`name` NOT LIKE 'img_cmslogo'
            AND
            (v.`name` LIKE '%".$db->escape(trim($_GET['q']))."%' 
            OR v.`value` LIKE '%".$db->escape(trim($_GET['q']))."%' 
            OR v.`description` LIKE '%".$db->escape(trim($_GET['q']))."%')
           ) OR (v.`name` LIKE '".$db->escape(trim($_GET['q']))."') 
        ORDER BY v.`name` LIMIT ".$start.", ".$onpage, ARRAY_A);
        
		
		
      if($db->num_rows == 1 && isset($_GET['redirect'])){
        header("Location: ?action=settings&do=site_vars&id=".$rows[0]['id']);
        exit;
      }
  }elseif(isset($_GET['mode']) && $_GET['mode'] == 'img'){
		if(isset($_GET['site_id'])){
			$href .= "&site_id=".$_GET['site_id'];
		}
		$href .= "&mode=".$_GET['mode'];
		$all_results = $db->get_var("SELECT COUNT(*) 
			FROM ".$db->tables['site_vars']." v 
			LEFT JOIN ".$db->tables['site_info']." s on (s.id = v.site_id) 
			WHERE (v.`name` LIKE 'img_%' 
				AND v.`name` NOT LIKE 'img_cmslogo') $str_sid 
			");
		
    	$rows = $db->get_results("SELECT v.*, s.site_url as website FROM ".$db->tables['site_vars']." v 
        LEFT JOIN ".$db->tables['site_info']." s on (s.id = v.site_id) 
        WHERE (v.`name` LIKE 'img_%' AND v.`name` NOT LIKE 'img_cmslogo') $str_sid 
        ORDER BY v.`name` LIMIT ".$start.", ".$onpage, ARRAY_A);
  }elseif(isset($_GET['mode']) && $_GET['mode'] == 'sys'){
		if(isset($_GET['site_id'])){
			$href .= "&site_id=".$_GET['site_id'];
		}
		$href .= "&mode=".$_GET['mode'];
		$all_results = $db->get_var("SELECT COUNT(*) 
			FROM ".$db->tables['site_vars']." v 
			LEFT JOIN ".$db->tables['site_info']." s on (s.id = v.site_id) 
			WHERE (v.`name` LIKE 'sys_%' 
				OR v.`name` LIKE 'smtp%') $str_sid 
		");
		
    	$rows = $db->get_results("SELECT v.*, s.site_url as website FROM ".$db->tables['site_vars']." v 
        LEFT JOIN ".$db->tables['site_info']." s on (s.id = v.site_id) 
        WHERE (v.`name` LIKE 'sys_%' OR v.`name` LIKE 'smtp%') $str_sid 
        ORDER BY v.`name` LIMIT ".$start.", ".$onpage, ARRAY_A);
  }else{
		if(isset($_GET['site_id'])){
			$href .= "&site_id=".$_GET['site_id'];
		}
		$all_results = $db->get_var("SELECT COUNT(*) 
			FROM ".$db->tables['site_vars']." v 
			LEFT JOIN ".$db->tables['site_info']." s on (s.id = v.site_id) 
			WHERE v.`name` NOT LIKE 'sys_%' 
				AND v.`name` NOT LIKE 'smtp%' 
				AND v.`name` NOT LIKE 'img_%' $str_sid 
		");
		
    	$rows = $db->get_results("SELECT v.*, s.site_url as website FROM ".$db->tables['site_vars']." v 
        LEFT JOIN ".$db->tables['site_info']." s on (s.id = v.site_id) 
        WHERE v.`name` NOT LIKE 'sys_%' AND v.`name` NOT LIKE 'smtp%' AND v.`name` NOT LIKE 'img_%' $str_sid 
        ORDER BY v.`name` LIMIT ".$start.", ".$onpage, ARRAY_A);
  }

		
	$pages = ceil($all_results/$onpage);
	$href .= "&page=";
	if($pages > 1){
		$tpl->assign("page", $page);
		$tpl->assign("npages", $pages);
		$tpl->assign("href", $href);
	}
	
	$tpl->assign("list_vars", $rows);
	return $tpl->display("settings/site_vars.html");
}

/* ok */
function add_site_var()
{
	global $db, $tpl;
	
	if(!empty($_GET['hint_id'])){
		$sql = "SELECT * 
			FROM ".$db->tables['site_vars']." 
			WHERE id = '".intval($_GET['hint_id'])."'
		";
		$hint = $db->get_row($sql, ARRAY_A);
		$tpl->assign('hint', $hint);
	}
	
	$sites = $db->get_results("SELECT id, site_url FROM ".$db->tables['site_info']." ORDER BY id ", ARRAY_A);
	$tpl->assign('sites', $sites);
	$str = "";
	$posted = $_POST;
	if(!isset($posted['forsite'])) { $posted['forsite'] = 0; }
	if(!isset($posted['autoload'])) { $posted['autoload'] = 0; }  
	if(!isset($posted['width'])) { $posted['width'] = 0; }  
	if(!isset($posted['height'])) { $posted['height'] = 0; }  

	if(isset($posted["add"]))
	{
      $db->query("INSERT INTO ".$db->tables['site_vars']." (site_id, `name`, 
          description,  `autoload`, if_enum, `type`, 
          `value`, width, height) VALUES('".$db->escape($posted['forsite'])."', 
          '".$db->escape($posted['varname'])."', 
          '".$db->escape($posted['description'])."', 
          '".$db->escape($posted['autoload'])."', 
          '".$db->escape($posted['if_enum'])."', 
          '".$db->escape($posted['type'])."', 
          '".$db->escape($posted['value'])."', 
          '".$db->escape($posted['width'])."', 
          '".$db->escape($posted['height'])."')");
      if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }
	  $id = $db->insert_id;
      $href = "?action=settings&do=site_vars&id=".$id."&added=1";
	  clear_cache(0); 
      _redirect($href);
	  
	}	
	$str .= $tpl->display("settings/add_site_var.html");
	return $str;
}

/* ok */
function edit_site_var($id)
{
	global $db, $tpl, $admin_vars;
	$sites = $db->get_results("SELECT id, site_url 
		FROM ".$db->tables['site_info']." 
		ORDER BY id ", ARRAY_A);
	$tpl->assign('sites', $sites);
	$str = "";
	$a = "bgcolor=".$admin_vars['bglight'];
	$posted = isset($_POST) ? $_POST : array();
	if(!isset($posted['forsite'])) { $posted['forsite'] = 0; }
	if(!isset($posted['autoload'])) { $posted['autoload'] = 0; }  
	if(!isset($posted['width'])) { $posted['width'] = 0; }  
	if(!isset($posted['height'])) { $posted['height'] = 0; }  
	if(!isset($posted["type"])) { $posted["type"] = "text"; }  
	
	if(isset($posted["add_file"]) && isset($_FILES['foto']['size']) &&  $_FILES['foto']['size'] > 0)
	{
		$str = add_vars_foto($id, 'var', $posted['width'], $posted['height']);
		if(empty($str)){ $str = "ok"; }
		$href = "?action=settings&do=edit_site_var&id=".$id."&error=".$str."#upload";
		header("Location: ".$href);
		exit;
	}


	if(isset($posted["update"]))
	{
		/* если ключ обновлен, то сбросим данные */
		if($posted['varname'] == 'sys_licence'){
			$posted['if_enum'] = '';
		} 
		
		$posted['value'] = trim($posted['value']);
		$db->query("UPDATE ".$db->tables['site_vars']." SET 
        site_id='{$posted['forsite']}',
        `name`='".$db->escape($posted['varname'])."', 
        `description` = '".$db->escape($posted["description"])."',
        `value` = '".$db->escape($posted['value'])."', 
        `type` = '".$db->escape($posted["type"])."',
        autoload = '".$db->escape($posted['autoload'])."', 
        if_enum = '".$db->escape($posted['if_enum'])."',
        `width` = '".$db->escape($posted['width'])."', 
        `height` = '".$db->escape($posted['height'])."' 
       WHERE id='$id'");
	   
    if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }
    
    if($db->escape($posted['varname']) == "sys_smtp_on"){
      $href = "?action=settings&do=email";
    }else{
  		$href = "?action=settings&do=edit_site_var&id=$id&updated=1";
    }
    clear_cache(0);
    header("Location: ".$href);
    exit;

	}elseif(isset($posted["update_fotos"])){
      if(isset($_POST["imgdefault"]) && intval($_POST["imgdefault"]) > 0){
        // поставим новое значение для картинки по умолчанию
        $db->query("UPDATE ".$db->tables["uploaded_pics"]." SET is_default = '1' WHERE id = '".intval($_POST["imgdefault"])."' ");
        if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }

        $db->query("UPDATE ".$db->tables["uploaded_pics"]." SET is_default = '0' WHERE id <> '".intval($_POST["imgdefault"])."' AND `record_type` = 'var' AND record_id = '".$id."' ");
        if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }
      }

      $qty = 0;
      if(isset($_POST["imgtitle"]) && count($_POST["imgtitle"]) > 0){
        $qty = count($_POST["imgtitle"]);
        foreach($_POST["imgtitle"] as $k => $v){
          $db->query("UPDATE ".$db->tables["uploaded_pics"]." 
              SET `title` = '".$db->escape($v)."' 
              WHERE id = '".intval($k)."' ");
          if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }      
        }
      }
	  
	  
	  /* обновим доп.поля описания */
	  if(!empty($_POST["img_ext_h1"])){
        foreach($_POST["img_ext_h1"] as $k => $v){
          $db->query("UPDATE ".$db->tables["uploaded_pics"]." 
              SET `ext_h1` = '".$db->escape(trim($v))."' 
              WHERE id = '".intval($k)."' ");
          //if(!empty($db->last_error)){ return db_error(basename(__FILE__).": 834"); }      
        }
      }
	  if(!empty($_POST["img_ext_desc"])){
        foreach($_POST["img_ext_desc"] as $k => $v){
          $db->query("UPDATE ".$db->tables["uploaded_pics"]." 
              SET `ext_desc` = '".$db->escape(trim($v))."' 
              WHERE id = '".intval($k)."' ");
          //if(!empty($db->last_error)){ return db_error(basename(__FILE__).": 834"); }      
        }
      }
	  if(!empty($_POST["img_ext_link"])){
        foreach($_POST["img_ext_link"] as $k => $v){
          $db->query("UPDATE ".$db->tables["uploaded_pics"]." 
              SET `ext_link` = '".$db->escape(trim($v))."' 
              WHERE id = '".intval($k)."' ");
          //if(!empty($db->last_error)){ return db_error(basename(__FILE__).": 834"); }      
        }
      }
	  
	  
	  
	  
	  

      if(isset($_POST["imgdel"]) && count($_POST["imgdel"]) > 0){
        $deleted = count($_POST["imgdel"]);
        foreach($_POST["imgdel"] as $k => $v){
          $is_default = $db->get_var("SELECT is_default 
                FROM ".$db->tables["uploaded_pics"]." 
                WHERE id = '".$k."' ");
          if($is_default == 1){
            $correct_default = "yes";
          }
          delete_uploaded_pics($id, 'var', intval($k), true);
        }

        if(isset($correct_default) && $qty > $deleted){
          // зададим новую картинку по умолчанию
          $rows = $db->get_results("SELECT * FROM ".$db->tables["uploaded_pics"]." 
              WHERE record_type = 'var' AND record_id = '".$id."' ORDER BY id_in_record");
          if($db->num_rows > 0){
            $i = 0;
            foreach($rows as $row){
              $i = $i+1;
              if($i == 1){
                $db->query("UPDATE ".$db->tables["uploaded_pics"]." 
                  SET is_default = '1', id_in_record = '".$i."' 
                  WHERE id = '".$row->id."' "); 
                if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }
              }else{
                $db->query("UPDATE ".$db->tables["uploaded_pics"]." 
                  SET is_default = '0', id_in_record = '".$i."' 
                  WHERE id = '".$row->id."' ");
                if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }
              }              
            }
          }              
        }
      }
			$href = "?action=settings&do=edit_site_var&id=$id&fotosupdated=1#upload";
		clear_cache(0);
		header("Location: ".$href);
		exit;
	}elseif(isset($posted["delete"])){
		/* удалим привязку к избранным */
		$query = "DELETE FROM ".$db->tables["fav"]." WHERE where_id = '".$id."' AND `where_placed` = 'var' ";
		$db->query($query);
		
	  $db->query("DELETE FROM ".$db->tables["site_vars"]." WHERE id = '".$id."' ");
	  if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }

		$pics_to_delete = $db->get_results("SELECT * FROM ".$db->tables["uploaded_pics"]." 
			WHERE record_type = 'var' AND record_id = '".$id."' ");
		if($pics_to_delete && $db->num_rows > 0){
			foreach($pics_to_delete as $v){
				delete_uploaded_pics($id, 'var', $v->id, true);
			}
		}
	  
	  clear_cache(0);
	  $href = "?action=settings&do=site_vars&deleted=1";
      header("Location: ".$href);
      exit;
  }else{
		$var = $db->get_row("SELECT * 
			FROM ".$db->tables["site_vars"]." 
			WHERE id='$id'", ARRAY_A);
		if($var && $db->num_rows == 1){

			$sql = "SELECT v.id, v.site_id, v.`name`, 
					s.site_url, 
					s.name_short as site
				FROM ".$db->tables["site_vars"]." v 				
				LEFT JOIN ".$db->tables['site_info']." s ON (v.site_id = s.id)
				WHERE v.`name` = '".$db->escape($var['name'])."' 
				AND v.id <> '".$id."' 
			";
			$var["other_vars"] = $db->get_results($sql, ARRAY_A);
			//$db->debug(); exit;
		
			$var["values_ar"] = get_array_vars($var['name']);
			$tpl->assign("site_var",$var);

			if(substr($var['name'], 0, 4) == 'sys_' && !isset($_GET['edit']) && !isset($_GET['editor'])){
				$tpl_page = 'settings/edit_site_var_sys.html';				
			}elseif(substr($var['name'], 0, 4) == 'img_' && !isset($_GET['edit']) && !isset($_GET['editor'])){
				$tpl_page = 'settings/edit_site_var_img.html';				
			}else{
				$tpl_page = 'settings/edit_site_var.html';								
			}
	  
		}else{
			return error_404(GetMessage('admin','record_not_found'));
		}
	}
	if(!isset($tpl_page)){ $tpl_page = 'settings/edit_site_var.html'; }
	return $tpl->display($tpl_page);
}

/* ок, считает объем файлов в папке */
function show_size($f,$format=true) 
{ 
        if($format) 
        { 
                $size=show_size($f,false); 
                return $size;
                if($size<=1024) return $size.' bytes'; 
                else if($size<=1024*1024) return round($size/(1024),2).' Kb'; 
                else if($size<=1024*1024*1024) return round($size/(1024*1024),2).' Mb'; 
                else if($size<=1024*1024*1024*1024) return round($size/(1024*1024*1024),2).' Gb'; 
                else if($size<=1024*1024*1024*1024*1024) return round($size/(1024*1024*1024*1024),2).' Tb'; //:))) 
                else return round($size/(1024*1024*1024*1024*1024),2).' Pb'; // ;-) 
        }else 
        { 
                if(is_file($f)) return filesize($f); 
                $size=0; 
                $dh=opendir($f); 
                while(($file=readdir($dh))!==false) 
                { 
                        if($file=='.' || $file=='..') continue; 
                        if(is_file($f.'/'.$file)) $size+=filesize($f.'/'.$file); 
                        else $size+=show_size($f.'/'.$file,false); 
                } 
                closedir($dh); 
                return $size+filesize($f); // +filesize($f) for *nix directories 
        } 
} 

function mass_vars($hint){
    global $tpl, $db;
    $tpl->assign('hint', $hint);
    
    if(empty($hint)){
        /* вывести возможные страницы массовых правок */
        return $tpl->display("settings/mass_vars_index.html");
    }
    
    
    if($hint == 'smtp'){
        $ar = array('sys_smtp_auth', 'sys_smtp_host', 'sys_smtp_on', 'sys_smtp_password', 'sys_smtp_port', 'sys_smtp_username', 'sys_smtp_secure');
    }elseif($hint == 'currencies'){
        $ar = array('rur', 'usd', 'euro', 'sys_currency');
	}elseif($hint == 'sms'){
        $ar = array('sys_smsc_login', 'sys_smsc_password', 'sys_smsc_debug', 'sys_smsc_url');	
    }elseif($hint == 'images'){
        $ar = array('sys_resize_method', 'img_size1', 'img_size2', 'img_size3', 'img_size4', 'img_size5', 'img_size6', 'sys_save_original_fotos');
    }elseif($hint == 'extra'){
        $ar = array('sys_count_visitors', 'sys_upload_ext_allowed', 'sys_skip_visited_products', 'sys_skip_visited_pubs', 'sys_skip_spec_products', 'sys_skip_last_products', 'sys_skip_last_pubs', 'sys_skip_starred_pubs', 'sys_extra_page');
    }elseif($hint == 'yml'){
        $ar = array('sys_yml', 'yml_key', 'company_name', 'sys_yml_min_bid', 'sys_yml_book_categs', 'sys_yml_only_in_stock');
    }elseif($hint == 'gmc'){
        $ar = array('sys_gmc', 'sys_gmc_categs', 'sys_gmc_key', 'sys_gmc_title', 'sys_gmc_description', 'sys_gmc_min_bid');
    }elseif($hint == 'social'){
        $ar = array('facebook', 'twitter', 'vk', 'youtube', 'instagram');
    }else{
        $tpl->assign('error', 'Unknown hint sent');        
        return $tpl->fetch("settings/mass_vars_index.html");
    }
    
    $ids = '';
    foreach($ar as $k=>$v){
        if($k > 0){ $ids .= ',';}
        $ids .= "'".$v."'";
    }
    $newar = array();

    if(!empty($ids)){
        $rows = $db->get_results('SELECT v.*, s.site_url 
            FROM '.$db->tables['site_vars'].' v
            LEFT JOIN '.$db->tables['site_info'].' s on (v.site_id = s.id) 
            WHERE v.`name` IN ('.$ids.') ORDER BY v.`name`, v.site_id ', ARRAY_A);
        if($rows && $db->num_rows > 0){
            foreach($rows as $row){
                if(!isset($newar[$row['name']])){ $newar[$row['name']] = array(); }
				if($row['type'] == 'list'){
					$values = str_replace("\r\n","\n",$row['if_enum']);
					$row['value_ar'] = explode("\n",$values);
				}
				if($row['name'] == 'sys_yml_book_categs'){
					$ids = explode(',', $row['value']);
					$row['ids_selected'] = $ids;
				}

				
				if($row['name'] == 'sys_gmc_categs'){
					$gmc_ar = explode(',', $row['value']);
					if(!empty($gmc_ar)){
						$gmc_row = array();
						foreach($gmc_ar as $gmc){
							$id_array = explode('-',$gmc);
							if(isset($id_array[0]) && isset($id_array[1])){
								$gmc_row[$id_array[0]] = $id_array[1];								
							}							
						}
					}
					$tpl->assign('gmc_categs', $gmc_row);
				}

                $newar[$row['name']][] = $row;
            }        
        }        
    }
    
    foreach($ar as $v){
        if(!isset($newar[$v])){
            $newar[$v] = array();
        }
    }
    $tpl->assign('vars', $newar);
	$categs = $db->get_results("SELECT 
		c.id, c.title, c.alias, 
		c.active, c.shop, 
		(SELECT COUNT(*) 
			FROM ".$db->tables['pub_categs']."
			WHERE where_placed = 'product' 
				AND id_categ = c.id 
		) as qty_products
		FROM ".$db->tables['categs']." c 
		ORDER BY c.`sort`, c.`id_parent`, c.`title` 
	", ARRAY_A);
    $tpl->assign('categs', $categs);
	
	if(isset($_POST['update']) && !empty($_POST['vars'])){
		/* save sent data */
		foreach($_POST['vars'] as $k => $v){
			$value = empty($v['value']) ? '' : trim($v['value']);
			if($v['type'] == 'checkbox'){ $value = intval($value); }
			$width = empty($v['width']) ? '0' : intval($v['width']);
			$height = empty($v['height']) ? '0' : intval($v['height']);
			
			if(!empty($v['ids'])){
				foreach($v['ids'] as $i=>$j){
					if($i > 0){
						$value .= ','.$j;
					}else{
						$value = $j;
					}
					
				}
			}elseif(isset($v['gmc'])){
				unset($gmc);
				foreach($v['gmc'] as $g1 => $g2){
					$g2 = intval($g2);
					if(!empty($g2)){
						$gmc = !empty($gmc) ? $gmc.','.$g1.'-'.$g2 : $g1.'-'.$g2;
					}					
				}
				if(empty($gmc)){ $value = ''; }else{ $value = $gmc; }
			}else{
				if(isset($v['ids'])){ $value = ''; }
			}
			$sql = "UPDATE ".$db->tables['site_vars']." SET 
				`value` = '".$db->escape($value)."',
				`width` = '".$width."',
				`height` = '".$height."'
				WHERE id = '".$k."'
			";
			$db->query($sql);
		}
		$url = "?".$_SERVER["QUERY_STRING"];
		$url = str_replace('&updated=1','',$url);
		$url .= "&updated=1";
		clear_cache(0);
		header("Location: ".$url);
		exit;
	}
	
	
	if($hint == 'yml'){
		return $tpl->display("settings/mass_vars_yml.html");   		
	}elseif($hint == 'gmc'){
		return $tpl->display("settings/mass_vars_yml.html");   		
	}else{
		return $tpl->display("settings/mass_vars_yml.html");   
	}    

}

function get_blocks(){
	global $db, $tpl;	
	if(isset($_GET['id'])){
		$id = intval($_GET['id']);
		return edit_block($id);
	}
		
	if(!empty($_GET['site'])){
		$site_id = intval($_GET['site']);
		$sql = "SELECT b.*, b.where_placed as `where`,
				si.id as site_id,
				si.name_short as site,
				si.site_url as site_url 
			FROM ".$db->tables['blocks']." b 
			LEFT JOIN ".$db->tables['connections']." c on 
				(c.id1 = '".$site_id."' AND c.name1 = 'site' AND c.id2 = b.id AND c.name2 = 'block') 
			LEFT JOIN ".$db->tables['site_info']." si ON ('".$site_id."' = si.id)
			WHERE c.id2 = b.id 
				AND `type` NOT IN ('categ','pub','produt')
			ORDER BY b.`where_placed`, b.`sort`, b.title_admin";
	}else{
		$sql = "SELECT b.*, `where_placed` as `where`,
				si.id as site_id,
				si.name_short as site,
				si.site_url as site_url 
			FROM ".$db->tables['blocks']." b 
			LEFT JOIN ".$db->tables['connections']." c on 
				(c.name1 = 'site' AND c.id2 = b.id AND c.name2 = 'block') 
			LEFT JOIN ".$db->tables['site_info']." si ON (c.id1 = si.id)
			WHERE b.`type` NOT IN ('categ','pub','product')
			ORDER BY b.`where_placed`, b.`sort`, b.title_admin";
	}
	
	$blocks = $db->get_results($sql, ARRAY_A);
	//$db->debug(); 	exit;
	$tpl->assign("list_blocks", $blocks);

	$filter_sites = $db->get_results("SELECT s.id, s.name_short, s.site_url
		FROM ".$db->tables['connections']." c 
		LEFT JOIN ".$db->tables['site_info']." s on (c.id1 = s.id)
		WHERE c.`name1` = 'site' AND c.`name2` = 'block' 
		GROUP BY c.`id1`
		", ARRAY_A);
	$tpl->assign("filter_sites", $filter_sites);
    return $tpl->display("settings/list_blocks.html");   	
}

function save_block($id){
//	phpinfo();
	//echo 'ok'; exit;
	global $db;
	$b = array();
	$b['active'] = !empty($_POST['block']['active']) ? 1 : 0;
	$b['where'] = !empty($_POST['block']['where']) ? trim($_POST['block']['where']) : '';
	$b['title'] = !empty($_POST['block']['title']) ? trim($_POST['block']['title']) : '';
	$b['title_admin'] = !empty($_POST['block']['title_admin']) ? trim($_POST['block']['title_admin']) : '';
	$b['qty'] = !empty($_POST['block']['qty']) ? intval($_POST['block']['qty']) : '0';
	$b['type'] = !empty($_POST['block']['type']) ? trim($_POST['block']['type']) : '';
	$b['type_id'] = !empty($_POST['block']['type_id']) ? intval($_POST['block']['type_id']) : 0;
	$b['pages'] = !empty($_POST['block']['pages']) ? trim($_POST['block']['pages']) : '';
	$b['skip_pages'] = !empty($_POST['block']['skip_pages']) ? trim($_POST['block']['skip_pages']) : '';
	$b['html'] = !empty($_POST['block']['html']) ? trim($_POST['block']['html']) : '';
	$b['sites'] = !empty($_POST['block']['sites']) ? $_POST['block']['sites'] : array();
	$b['sort'] = !empty($_POST['block']['sort']) ? intval($_POST['block']['sort']) : '99';

	if($id > 0){
		/* удалим старую привязку к сайтам */
		$sql = " DELETE FROM ".$db->tables['connections']." 
			WHERE id2 = '".$id."' AND name1 = 'site' AND name2 = 'block' ";
		$db->query($sql);		
		$new_id = $id;
		
		if(isset($_POST['del'])){
			/* удалим привязку к избранным */
			$query = "DELETE FROM ".$db->tables["fav"]." WHERE where_id = '".$id."' AND `where_placed` = 'block' ";
			$db->query($query);
			
			
			$sql = " DELETE FROM ".$db->tables['blocks']." WHERE id = '".$id."' ";
			$db->query($sql);	
			clear_cache(0);			
			header("Location: ?action=settings&do=blocks&deleted=1");
			exit;
		}
		
		$sql = " UPDATE ".$db->tables['blocks']." SET 
				`active` = '".$db->escape($b['active'])."', 
				`where_placed` = '".$db->escape($b['where'])."', 
				`title` = '".$db->escape($b['title'])."', 
				`title_admin` = '".$db->escape($b['title_admin'])."', 
				`qty` = '".$db->escape($b['qty'])."', 
				`type` = '".$db->escape($b['type'])."', 
				`type_id` = '".$db->escape($b['type_id'])."', 			
				`pages` = '".$db->escape($b['pages'])."', 
				`skip_pages` = '".$db->escape($b['skip_pages'])."', 
				`html` = '".$db->escape($b['html'])."',
				`sort` = '".$db->escape($b['sort'])."'
			WHERE id = '".$id."'  ";
		$db->query($sql);		
		if(!empty($db->last_error)){ 
			return db_error(basename(__FILE__).' LINE: '.__LINE__.'<br>Function: '.__FUNCTION__
			); 
		}  
		
		
	}else{
		$sql = "INSERT INTO ".$db->tables['blocks']." (`active`, `where_placed`, 
			`title`, `title_admin`, `qty`, `type`, `type_id`, 
			`pages`, `skip_pages`, `html`, `sort`) 
				VALUES ('".$db->escape($b['active'])."', 
			'".$db->escape($b['where'])."', 
			'".$db->escape($b['title'])."', 
			'".$db->escape($b['title_admin'])."', 
			'".$db->escape($b['qty'])."', 
			'".$db->escape($b['type'])."', 
			'".$db->escape($b['type_id'])."', 			
			'".$db->escape($b['pages'])."', 
			'".$db->escape($b['skip_pages'])."', 
			'".$db->escape($b['html'])."',
			'".$db->escape($b['sort'])."'
			) ";
		$db->query($sql);
		if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }  
		$new_id = $db->insert_id;
	}
	
	if(!empty($b['sites'])){
		/* сохраним привязанные сайты */
		foreach($b['sites'] as $v){
			$sql = " INSERT INTO ".$db->tables['connections']." 
				(id1, name1, id2, name2) 
				VALUES ('".$v."', 'site', '".$new_id."', 'block') ";
			$db->query($sql);			
		}
		
	}
	
	$url = $id > 0 ? '&updated='.$id : '&added='.$new_id;
	clear_cache(0);
	header("Location: ?action=settings&do=blocks".$url);
	exit;
}

function edit_block($id){
	global $db, $tpl;	
	$where_ar = GetMessage('admin','block_wheres');
	asort($where_ar);
	$type_ar = GetMessage('admin','block_types');
	asort($type_ar);

	if($id == 0){
		$ar = array('id' => 0, 'active' => 1, 'title' => '', 'title_admin' => '', 
			'qty' => '0', 'where' => 'manual', 'type_id' => 0,
			'type' => '', 'pages' => '', 'skip_pages' => '', 'html' => '', 
			'sort' => '99', 'alias' => ''
		);
	}else{
		$ar = $db->get_row("SELECT *, where_placed as `where` FROM ".$db->tables['blocks']." WHERE id = '".$id."' ", ARRAY_A);
		if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }   
		if($id > 0 && $db->num_rows == 0){
			return error_404(GetMessage('admin','record_not_found')); 
		}
	}

	if(!empty($_POST['block'])){
		return save_block($id);
	}
  
	$ar['where_ar'] = $where_ar;
	$ar['type_ar'] = $type_ar;
	$ar['sites_ar'] = $db->get_results("SELECT s.id, s.name_short, s.site_url, c.id as connected  
		FROM ".$db->tables['site_info']." s 
		LEFT JOIN ".$db->tables['connections']." c on (s.id = c.id1 AND c.name1 = 'site' AND c.id2 = '".$id."' AND c.name2 = 'block')
		", ARRAY_A);


    $tpl->assign('block', $ar);
	// вместо файла можно распарсить html-блок
	// $tpl->assign("bbb", $tpl->fetch_html($html));   		
    return $tpl->display("settings/edit_block.html");   		
}


function get_licence_info(){
	global $db, $tpl, $site;
	return $tpl->display("settings/licence.html");   		
}


function list_files($do,$page){
	// $do - files or fotos
	global $db, $tpl;
	
	/* если задано удаление - удалим! */
	if(!empty($_GET['record_id']) && !empty($_GET['id']) && !empty($_GET['type'])){
		$record_id = intval($_GET['record_id']);
		$record_type = $_GET['type'];
		$id = intval($_GET['id']);
		
		if($do == 'fotos'){
			delete_uploaded_pics($record_id, $record_type, false, true);
		}else{
			delete_uploaded_files($record_id, $record_type, $id);
		}
		
		$url = "?action=settings&do=".$do;
		if(!empty($page)){ $url .= "&page=".$page; }
		$url .= "&deleted=1";
		header("Location: ".$url);
		exit;
	}	
	
	if($do == 'files'){
		
		$sql = "SELECT uf.*, 
			(SELECT COUNT(*) 
				FROM ".$db->tables['uploaded_files']." 
				WHERE record_id = uf.record_id 
				AND record_type = uf.record_type 
			) as qty,
		
		
			CASE uf.record_type 
				WHEN 'categ' 
					THEN 
						(SELECT `title`
						FROM ".$db->tables['categs']." 
						WHERE id = uf.record_id
						) 
				WHEN 'pub' 
					THEN (SELECT `name`
						FROM ".$db->tables['publications']." 
						WHERE id = uf.record_id
						)  
				WHEN 'product' 
					THEN (SELECT `name`
						FROM ".$db->tables['products']." 
						WHERE id = uf.record_id
						)  
				WHEN 'fb_comment' 
					THEN (SELECT f.`ticket`
						FROM ".$db->tables['feedback']." f, 
						".$db->tables['comments']." fc  
						WHERE fc.id = uf.record_id
							AND fc.record_type = 'fb' 
							AND fc.record_id = f.id
						) 
				WHEN 'order_comment' 
					THEN (SELECT o.`order_id`
						FROM ".$db->tables['orders']." o, 
						".$db->tables['comments']." oc  
						WHERE oc.id = uf.record_id
							AND oc.record_type = 'order' 
							AND oc.record_id = o.id
						)
				
				WHEN 'org_stamp' 
					THEN (SELECT `title`
						FROM ".$db->tables['org']." 
						WHERE id = uf.record_id
						)				
				WHEN 'org_dir'  
					THEN (SELECT `title`
						FROM ".$db->tables['org']." 
						WHERE id = uf.record_id
						)			
				WHEN 'org_logo' 
					THEN (SELECT `title`
						FROM ".$db->tables['org']." 
						WHERE id = uf.record_id
						)				
				WHEN 'org_buh' 
					THEN (SELECT `title`
						FROM ".$db->tables['org']." 
						WHERE id = uf.record_id
						)				
					
				ELSE NULL 
			END AS where_record, 
			
			CASE uf.record_type 

				WHEN 'categ' 
					THEN 
						CONCAT('?action=info&do=edit_categ&id=', uf.record_id)
				WHEN 'pub' 
					THEN 
						CONCAT('?action=info&do=edit_publication&id=', uf.record_id) 
				WHEN 'product' 
					THEN 
						CONCAT('?action=products&do=edit&id=', uf.record_id)

				WHEN 'fb_comment' 
					THEN CONCAT('?action=feedback&id=', 
						(SELECT f.`id`
						FROM ".$db->tables['feedback']." f, 
						".$db->tables['comments']." fc  
						WHERE fc.id = uf.record_id
							AND fc.record_type = 'fb' 
							AND fc.record_id = f.id
						)) 
				WHEN 'order_comment' 
					THEN CONCAT('?action=orders&id=', (SELECT o.`id`
						FROM ".$db->tables['orders']." o, 
						".$db->tables['comments']." oc  
						WHERE oc.id = uf.record_id
							AND oc.record_type = 'order' 
							AND oc.record_id = o.id
						))
				
				WHEN 'org_stamp' 
					THEN CONCAT('?action=org&id=', 
						(SELECT `id`
						FROM ".$db->tables['org']." 
						WHERE id = uf.record_id
						))				
				WHEN 'org_dir'  
					THEN CONCAT('?action=org&id=', 
						(SELECT `id`
						FROM ".$db->tables['org']." 
						WHERE id = uf.record_id
						))			
				WHEN 'org_logo' 
					THEN CONCAT('?action=org&id=', 
						(SELECT `id`
						FROM ".$db->tables['org']." 
						WHERE id = uf.record_id
						))				
				WHEN 'org_buh' 
					THEN CONCAT('?action=org&id=', 
						(SELECT `id`
						FROM ".$db->tables['org']." 
						WHERE id = uf.record_id
						))
					
				ELSE NULL 
			END AS link
			
			FROM ".$db->tables['uploaded_files']." uf 
			GROUP BY link 
			ORDER BY uf.record_type, uf.record_id
		";
	}else{
		$sql = "SELECT uf.*, 
			IF(uf.record_type = 'block', 
				(SELECT COUNT(*) 
					FROM ".$db->tables['blocks']." b1
					LEFT JOIN ".$db->tables['blocks']." b2 
						ON (b1.`type` = b2.`type` 
							AND b1.`type_id` = b2.`type_id`
						)
					WHERE b1.id = uf.record_id
				)
				,
				(SELECT COUNT(DISTINCT id_in_record) 
				FROM ".$db->tables['uploaded_pics']." 
				WHERE record_id = uf.record_id 
				AND record_type = uf.record_type 
				)
			) as qty,
			
			CASE uf.record_type 
				WHEN 'categ' 
					THEN 
						(SELECT `title`
						FROM ".$db->tables['categs']." 
						WHERE id = uf.record_id
						) 
				WHEN 'pub' 
					THEN (SELECT `name`
						FROM ".$db->tables['publications']." 
						WHERE id = uf.record_id
						)  
				WHEN 'product' 
					THEN (SELECT `name`
						FROM ".$db->tables['products']." 
						WHERE id = uf.record_id
						)  
				WHEN 'comment' 
					THEN 
					
						(SELECT 
							IF(record_type = 'product',
							(SELECT `name`
								FROM ".$db->tables['products']."
								WHERE id = record_id
							)
							,
								IF(record_type = 'pub',
								(SELECT `name`
								FROM ".$db->tables['publications']."
								WHERE id = record_id
								)
								,
									IF(record_type = 'categ',
									(SELECT `title`
									FROM ".$db->tables['categs']."
									WHERE id = record_id
									)
									,
									record_type
									)
								)
							)
						FROM ".$db->tables['comments']." 
						WHERE id = uf.record_id
						)



				WHEN 'fb_comment' 
					THEN 'fb' 
				WHEN 'order_comment' 
					THEN 'order'
				
				WHEN 'org_stamp' 
					THEN (SELECT `title`
						FROM ".$db->tables['org']." 
						WHERE id = uf.record_id
						)				
				WHEN 'org_dir'  
					THEN (SELECT `title`
						FROM ".$db->tables['org']." 
						WHERE id = uf.record_id
						)				
				WHEN 'org_logo' 
					THEN (SELECT `title`
						FROM ".$db->tables['org']." 
						WHERE id = uf.record_id
						)				
				WHEN 'org_buh' 
					THEN (SELECT `title`
						FROM ".$db->tables['org']." 
						WHERE id = uf.record_id
						)	
				WHEN 'var' 
					THEN (SELECT `name`
						FROM ".$db->tables['site_vars']." 
						WHERE id = uf.record_id
						) 	
				WHEN 'block' 
					THEN CONCAT(
						(SELECT  
						IF(`type` = 'categ', 
							(SELECT `title`
								FROM ".$db->tables['categs']." 
								WHERE id = type_id
							),
							IF(`type` = 'pub', 
								(SELECT `name`
								FROM ".$db->tables['publications']." 
								WHERE id = type_id
								), 
								IF(`type` = 'product', 
									(SELECT `name`
									FROM ".$db->tables['products']." 
									WHERE id = type_id
									),
									'*** unknown ***'
								)
								
							)
						)
						
						FROM ".$db->tables['blocks']." 
						
						WHERE id = uf.record_id
						)
					,' (block)')	
					
				ELSE NULL 
			END AS where_record, 
			
			CASE uf.record_type 
				WHEN 'categ' 
					THEN 
						CONCAT('?action=info&do=edit_categ&id=', uf.record_id)
				WHEN 'pub' 
					THEN 
						CONCAT('?action=info&do=edit_publication&id=', uf.record_id) 
				WHEN 'product' 
					THEN 
						CONCAT('?action=products&do=edit&id=', uf.record_id)


				WHEN 'comment' 
					THEN 
					
						(SELECT 
							IF(record_type = 'product',
							CONCAT('?action=products&do=edit&id=', record_id)
							,
								IF(record_type = 'pub',
								CONCAT('?action=info&do=edit_publication&id=', record_id)
								,
									IF(record_type = 'categ',
									CONCAT('?action=info&do=edit_categ&id=', record_id)
									,
									'#'
									)
								)
							)
						FROM ".$db->tables['comments']." 
						WHERE id = uf.record_id
						)						
						
						
				WHEN 'fb_comment' 
					THEN 'fb' 
				WHEN 'order_comment' 
					THEN 'order'
				
				WHEN 'org_stamp' 
					THEN 'org'				
				WHEN 'org_dir'  
					THEN 'org'				
				WHEN 'org_logo' 
					THEN 'org'				
				WHEN 'org_buh' 
					THEN 'org'	
				WHEN 'var' 
					THEN 
						CONCAT('?action=settings&do=site_vars&id=', uf.record_id)
					
				WHEN 'block' 
					THEN 
						(SELECT  
						IF(`type` = 'categ', 
							CONCAT('?action=info&do=edit_categ&id=', type_id),
							IF(`type` = 'pub', 
								CONCAT('?action=info&do=edit_publication&id=', type_id), 
							
								IF(`type` = 'product', 
									CONCAT('?action=products&do=edit&id=', type_id),
									'#'
								)								
							)
						)
						
						FROM ".$db->tables['blocks']." 
						WHERE id = uf.record_id
						)
				ELSE NULL 
			END AS link
			
			
			FROM ".$db->tables['uploaded_pics']." uf 
			GROUP BY link
			ORDER BY uf.record_type, where_record
		";
	}
	
	/* этот запрос быстрее, но он не доработан
	$sql2 = "SELECT 
            CONCAT(record_type, record_id, id_in_record) as link, id
			
			FROM ".$db->tables['uploaded_pics']." 
			GROUP BY link
			order by link

	";
	*/
	$onpage = ONPAGE*2;
	$page_start = $onpage*$page;
	$results = $db->get_results($sql);
	$all_results = $db->num_rows;

	$rows = $db->get_results($sql." LIMIT $page_start, $onpage ", ARRAY_A);
	
	/* подсчет размера папок с файлами */
	/* если менее недели назад подсчитывали, то выведем их */
	if($do == 'files'){
		$size = $db->get_row("SELECT 
				date_insert as date,
				comment as size 
			FROM ".$db->tables['changes']." 
			WHERE `where_changed` = 'files' 
			AND type_changes = 'filesize' 
			ORDER BY date_insert DESC 
			LIMIT 0,1
		", ARRAY_A);
	}else{
		$size = $db->get_row("SELECT 
				date_insert as date,
				comment as size 
			FROM ".$db->tables['changes']." 
			WHERE `where_changed` = 'records' 
			AND type_changes = 'filesize' 
			ORDER BY date_insert DESC 
			LIMIT 0,1
		", ARRAY_A);
	}

    $tpl->assign("size", $size);
	$tpl->assign('list', $rows);
	$tpl->assign('all', $all_results);
	$tpl->assign("pages",_pages($all_results, $page, $onpage, true));
	return $tpl->display("settings/files.html");
}


?>