<?php

  if(!defined('PATH_UPLOAD_PIC')){ define('PATH_UPLOAD_PIC', '../upload/'); }
	define("UPLOAD_DIR", PATH_UPLOAD_PIC);

	$admin_vars = array(
    'multisite' => 1,
    'test_version' => 1,
    'subscribe' => 1,
    'banners' => 1,
    'users_list' => 1,
    'shop' => 1,
    'reklama' => 1,
    'multitpl' => 1,
    'multilang' => isset($site->vars['sys_multilang']) 
		? $site->vars['sys_multilang'] : 1,
    'deflang' => isset($site->vars['sys_deflang']) 
		? $site->vars['sys_deflang'] :'ru',
    'bgdark' => '#8eaebe',
    'bglight' => '#ebf3ff',
	'bo_user' => isset($site->user) ? $site->user : array(),
    'uri' => array(
      'action' => isset($_GET['action']) ? trim($_GET['action']) : 'main',
      'do' => isset($_GET['do']) ? trim($_GET['do']) : '',
      'mode' => isset($_GET['mode']) ? trim($_GET['mode']) : '',
      'id' => isset($_GET['id']) ? intval($_GET['id']) : '0',
      'page' => isset($_GET['page']) ? intval($_GET['page']) : 0,
      'added' => isset($_GET['added']) ? trim($_GET['added']) : '',
      'deleted' => isset($_GET['deleted']) ? trim($_GET['deleted']) : '',
      'updated' => isset($_GET['updated']) ? trim($_GET['updated']) : '',
      'error' => isset($_GET['error']) ? trim($_GET['error']) : ''
    ),
    'admin_tpl' => 'default',
    'save_original_fotos' => 1, // 0 or 1 save or not original uploaded fotos
    'default_currency' => 'euro' // usd, euro, rur
  );

  /*
  save_original_fotos - эта опция разрешает хранить оригиналы фото на сервере
  если же стоит false - то хранятся только нарезанные изображения  
  */
?>