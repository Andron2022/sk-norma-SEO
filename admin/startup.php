<?php
	if(!defined('SIMPLA_ADMIN')){ die(); }

	error_reporting(E_ALL);
	ini_set("memory_limit", "512M");
	ini_set("max_execution_time", 90);

	if(strtolower(mb_internal_encoding()) != 'utf-8'){
		mb_internal_encoding("UTF-8");
		mb_regex_encoding("UTF-8");
	}

  /* This part of the script goes at the top of your page */
  $starttime = explode(' ', microtime());
  $starttime = $starttime[1] + $starttime[0];
  $langs = array('en' => 'EN', 'ru' => 'RU', 'es' => 'ES',
		'fr' => 'FR', 'de' => 'DE', 'it' => 'IT' 
  );

  include "../variable.php";
  $db->use_disk_cache = false;
  
  include_once(MODULE.'/fns.php');
  require("./fns.php");
  $site = new ClassSite();
  $admin_vars['bo_user']['id'] = $site->user['id'];
  load_site_settings();
  if(!empty($site_vars)){ $site->vars = $site_vars; }
  if(!empty($site->langs)){ $langs = $site->langs; }
  include "./admin_vars.php";
  
  /*--------------------------------------------------------*/
  require(MODULE. "/tpl/src/class.template.php");
  $tpl = new Template_Lite;
  $tpl->compile_dir = "compiled/";
  $tpl->cache_dir = "compiled";	// where cache files are stored
  $tpl->force_compile = true;
  $tpl->compile_check = true;
  $tpl->cache = false;
  $tpl->cache_lifetime = 3600;
  $tpl->config_overwrite = false;
  $tpl->template_dir = "tpl/".$admin_vars['admin_tpl'];
  $tpl->assign('tpl', '/'.ADMIN_FOLDER.'/'.$tpl->template_dir.'/');

  $currentlang = setLanguage($admin_vars['deflang']);
  $tpl->assign("currentlang", $currentlang);
  //$lang_default = ($currentlang, 'en');

  include_once(MODULE.'/send_notification.php');
  require(MODULE."/lang.php");

  /* ---- check if FB requests exists OR orders */  
  function check_fb($fb=true)
  {
    global $db;
	if($fb){
		return $db->get_var("SELECT count(*) FROM ".$db->tables["feedback"]." WHERE status = '0' ");		
	}else{
		return $db->get_var("SELECT count(*) FROM ".$db->tables["orders"]." WHERE status = '0' ");		
	}
  }

  //load_site_settings();  
  if(empty($site_vars['site_time_zone'])){ $site_vars['site_time_zone'] = 'utc'; }
  if(empty($site_vars['id'])){ $site_vars['id'] = 0; }
  if(empty($site_vars['template_path'])){ $site_vars['template_path'] = '/tpl/default/'; }
  
  date_default_timezone_set($site_vars['site_time_zone']);
  $db->query("SET time_zone = '".date('P')."' ");
  
  $site_vars['lang_admin'] = $currentlang;
  $site->vars = $site_vars;
  $site->id = $site_vars['id'];
  $site->lang = start_lang_pack($admin_vars['deflang'], $site, 1);  

  //require("./remind.php"); deleted
  if(MODE == "install"){ return; }
  $site_vars['_pages'] = getmenu($db->tables['categs'],0); 
  $tpl->assign("site_vars",$site_vars);
  $tpl->assign("path",set_path());
  $tpl->assign("langs",$langs);
  $tpl->assign("check_fb",check_fb());
  $tpl->assign("check_orders",check_fb(false));
  $tpl->assign("tpl",$tpl->template_dir."/");

  $tpl->assign("admin_vars",$admin_vars);
  require("./auth.php");
  
  /*URI*/
  $action = isset($_GET["action"])  && !is_array($_GET['action']) ? strval(htmlspecialchars($_GET["action"])) : "main";
  $admin_vars['uri']['action'] = $action; 
  if(isset($_GET['do']) && !is_array($_GET['do'])){ $admin_vars['uri']['do'] = htmlspecialchars($_GET["do"]); }
  if(isset($_GET['mode']) && !is_array($_GET['mode'])){ $admin_vars['uri']['mode'] = htmlspecialchars($_GET["mode"]); }
  if(isset($_GET['id']) && !is_array($_GET['id'])){ $admin_vars['uri']['id'] = intval($_GET["id"]); }
  if(isset($_GET['page']) && !is_array($_GET['page'])){ $admin_vars['uri']['page'] = intval($_GET["page"]); }
  if(isset($_GET['added']) && !is_array($_GET['added'])){ $admin_vars['uri']['added'] = htmlspecialchars($_GET["added"]); }
  if(isset($_GET['updated']) && !is_array($_GET['updated'])){ $admin_vars['uri']['updated'] = htmlspecialchars($_GET["updated"]); }
  if(isset($_GET['deleted']) && !is_array($_GET['deleted'])){ $admin_vars['uri']['deleted'] = htmlspecialchars($_GET["deleted"]); }
  if(isset($_GET['error']) && !is_array($_GET['error'])){ $admin_vars['uri']['error'] = htmlspecialchars($_GET["error"]); }

	if(isset($_GET['from'])){
		$admin_vars['uri']['from'] = is_array($_GET['from']) ? $_GET["from"] : htmlspecialchars($_GET["from"]); 
	}
  
	if(isset($_GET['to'])){ 
		$admin_vars['uri']['to'] = is_array($_GET['to']) ? $_GET["to"] : htmlspecialchars($_GET["to"]); 
	} 

	

/*
  $lang = new Admin_Lang();
  $lang->lang = $currentlang;
  $lang->init_Lang();
  */
  $site->user = $admin_vars['bo_user'];

  //$tpl->assign("lang",$lang);

  $tpl->register_function("favorites", "add_favorites_tpl"); // ok
  $tpl->register_function("page_loaded_time", "page_loaded_time"); // ok
  $tpl->register_function("get_option_field", "get_option_field"); // ok
  $tpl->register_function("site_form_for_pubs","site_form_for_pubs_tpl"); // ok
  
  $tpl->register_function("uploaded_pics","uploaded_pics_tpl"); // ok
  $tpl->register_function("pub_to_products","pub_to_products"); // ok
  $tpl->register_function("pub_to_pubs","pub_to_pubs"); // ok
  $tpl->register_function("product_to_pubs","product_to_pubs"); // ok

  $tpl->register_function("show_var_img", "show_var_img_tpl"); // ok

  $tpl->register_function("categ_sites","_sites4categ");   // ok
  $tpl->register_function("pub_sites","_sites4pub");   // ok
  $tpl->register_function("pub_in_categs", "pub_in_categs"); // ok
  
  $sidebar = "";
  if(file_exists($tpl->template_dir."/".$action."/sidebar.html")){
    $sidebar = $tpl->fetch("/".$action."/sidebar.html");
  }elseif($action == "emails" || $action == "mass" 
	|| $action == "db" || $action == "delivery" 
	|| $action == "org" || $action == "coupons" 
	){
    $sidebar = $tpl->fetch("settings/sidebar.html");
  }else{
    $sidebar = $tpl->fetch("info/sidebar.html");
  }
  
  $tpl->assign("sidebar", $sidebar);
  $metatitle = $_SERVER['HTTP_HOST'].' '.GetMessage('admin', 'metatitle');
  $tpl->assign("metatitle", $metatitle);

  if(!isset($admin_vars['bo_user']['prava'][$action]) 
		&& !empty($admin_vars['bo_user']['prava']['settings'])
		&& $action != 'main' 
		&& $action != 'logout' 
  ){
	/* блок генерации права, которого нет, если зашел админ */  
	$sql = "ALTER TABLE `".$db->tables['users_prava']."` 
		ADD `".$action."` TINYINT( 1 ) NULL DEFAULT  '0' ";
	$db->query($sql);
  }
  
	if(isset($admin_vars['bo_user']['prava'][$action]) 
	  && $admin_vars['bo_user']['prava'][$action] == 1){
		  
		// проверим есть ли такой модуль 
		// страницу выводит сам модуль
		$cur_script = "procedure_".$action.".php";
		if(!file_exists($cur_script)){
			$tpl->display("404.html");
			exit;
		}
		require($cur_script);
		if(empty($str_content) && empty($tpl->_printed)){
			$tpl->display("empty.html"); 
			exit;
		}
		
		$tpl->assign("db_queries_qty", $db->num_queries.' ('.round($db->timers[$db->num_queries]-$db->timers[1],3).' sec.)');
		echo $str_content;
		exit;
	}elseif($action == "logout"){
		$str_content = logout();
		exit;
	}elseif($admin_vars['uri']['action'] == "main"){

    $tpl->assign("users_qty",_qty("users"));
    $tpl->assign("products_qty",_qty("products"));
    $tpl->assign("pubs_qty",_qty("pubs"));
    $tpl->assign("site_qty",_qty("site"));
    $tpl->assign("admins_qty",_qty("admins"));

    $tpl->assign("fav",_get_favorites());

    $menutable = $db->tables['categs'];
    $menuid = 0;
    $parentid = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $AdminCategsTree = getmenu($menutable, $menuid,0,$parentid);
    $tpl->assign("AdminCategsTree",$AdminCategsTree);
    $tpl->assign("db_queries_qty", $db->num_queries.' ('.round($db->timers[$db->num_queries]-$db->timers[1],3).' sec.)');
    //$str_content = $tpl->fetch("index.html");
    $tpl->display("index.html");
	exit;
  }else{
    /* нет прав */
    $tpl->display("403.html");
    exit;
  } 

?>