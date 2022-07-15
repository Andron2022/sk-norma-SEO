<?php
if(!defined('SIMPLA_ADMIN')){ die(); }

/**********************************
***********************************
**	
**	Функции для вывода данных о сайтах
**	updated 21.03.2019
**	
**	  
***********************************
**********************************/


/* ok, find all site settings */
if(!function_exists('load_site_settings')){
  function load_site_settings()
  {
	  global $db;
	  if(!empty($db->no_connection)){
		define("SITE_ID", 0);  
		return;
	  }
	
    if(!defined('SITE_ID')){ load_siteid_by_url(); }
    if(!defined('SITE_ID')){ load_siteid_by_default(); }
    if(!defined('SITE_ID')){ return false; }
    global $db;
  		
	$query = "SELECT s.* FROM ".$db->tables["site_info"]." s  
		WHERE s.id = '".SITE_ID."' ";
		
    $result = $db->get_row($query, ARRAY_A);
	if(!empty($db->last_error)){ return die('Database error: '.basename(__FILE__).': '.__LINE__); }

	
    if($db->num_rows == 1) {
		/* 
			создадим таблицу организаций, 
			если ее нет, чтобы не приводило к ошибке
		*/
		$db->query("CREATE TABLE IF NOT EXISTS ".$db->tables['org']." 
			(`id` int(12) NOT NULL AUTO_INCREMENT,
			`active` tinyint(1) DEFAULT '0',
			`own` tinyint(1) DEFAULT '0',
			`title` varchar(250) NOT NULL,
			`postal_code` varchar(20) DEFAULT NULL,
			`country` varchar(250) DEFAULT NULL,
			`city` varchar(250) DEFAULT NULL,
			`address` varchar(250) DEFAULT NULL,
			`inn` varchar(20) DEFAULT NULL,
			`kpp` varchar(20) DEFAULT NULL,
			`bik` varchar(20) DEFAULT NULL,
			`r_account` varchar(50) DEFAULT NULL,
			`k_account` varchar(50) DEFAULT NULL,
			`bank` varchar(250) DEFAULT NULL,
			`phone` varchar(250) DEFAULT NULL,
			`post_address` varchar(250) DEFAULT NULL,
			`fio_dir` varchar(250) DEFAULT NULL,
			`fio_buh` varchar(250) DEFAULT NULL,
			`fio_dir_kratko` varchar(250) DEFAULT NULL,
			`fio_buh_kratko` varchar(250) DEFAULT NULL,
			`ogrn` varchar(20) DEFAULT NULL,
			`website` varchar(250) DEFAULT NULL,
			`email` varchar(250) DEFAULT NULL,
			`data_reg` date DEFAULT NULL,
			`is_default` tinyint(1) DEFAULT '0',
			`nds` tinyint(2) DEFAULT '0',
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 
			AUTO_INCREMENT=100 ;");
		
		$orgs = $db->get_var("SELECT c.id1 
				FROM ".$db->tables['connections']." c 
				LEFT JOIN ".$db->tables['org']." o ON (c.id1 = o.id)
				WHERE c.id2 = '".SITE_ID."' 
					AND c.name2 = 'site' 
					AND c.name1 = 'org' 
					AND o.is_default = '1'
					AND o.active = '1'
				GROUP BY o.id 
				ORDER BY o.title
				LIMIT 0, 1");
		$result['orgs'] = !empty($orgs) ? $orgs : 0;
		
      foreach($result as $k => $v){
        $todefine = strtoupper($k);
        if(!defined($todefine)){
          $v = stripslashes($v);
        //if(ctype_digit($v)){
          if(is_numeric($v)){
            if($v == 0){ $v = false; }
            elseif($v == 1){ $v = true; }
          }
          define($todefine, $v);
        }
      }
    }else{ return false; }
    
    global $site_vars, $admin_vars;	
    $site_vars = $result;
    $query = "SELECT * FROM ".$db->tables['site_vars']." 
        WHERE (site_id='".SITE_ID."' OR site_id = '0') 
			AND autoload = '1' 
		ORDER BY `name` ";
    $results = $db->get_results($query);

	if(!empty($db->last_error)){
		return db_error(
				'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
				<br>Function: '.__FUNCTION__
		); 			
	}
    if(is_array($results))
    {
		
		$img = $db->get_results("SELECT * 
                    FROM ".$db->tables['uploaded_pics']." 
                    WHERE record_type = 'var' 
                    ORDER BY id_in_record
                    ", ARRAY_A);
					
        $img_ar = array();
        if($db->num_rows > 0){
            foreach($img as $v){
                $ar = array(
                            'url' => '/upload/records/'.$v['id'].'.'.$v['ext'], 
                            'width' => $v['width'], 
                            'height' => $v['height'], 
                            'title' => $v['title'],
                            'ext' => $v['ext'],
							'ext_h1' => isset($v['ext_h1']) ? $v['ext_h1'] : '',
							'ext_desc1' => isset($v['ext_desc']) ? $v['ext_desc'] : '',
							'ext_desc2' => isset($v['ext_link']) ? $v['ext_link'] : ''
                        );
                $img_ar[$v['record_id']][] = $ar;
            }
        }		
		
		
      foreach($results as $row)
      {                        
        if(strlen($row->name) > 5 && substr($row->name, 0, 4) == 'img_'){
			$imgs = array();
            if(isset($img_ar[$row->id])){
				$imgs = $img_ar[$row->id]; 
            }
        
        	$site_vars[$row->name] = array(
            'name' => $row->description, 
            'value' => $row->value, 
            'width' => $row->width, 
            'height' => $row->height,
			'img' => $imgs 			
          );

        }elseif($row->name == "sys_save_original_fotos"){
          $admin_vars['save_original_fotos'] = $row->value;
        }elseif($row->name == "sys_currency"){
          $admin_vars['default_currency'] = $row->value;
        }else{
        	$site_vars[$row->name] = $row->value;
			
			if($row->name == 'sys_licence'){
				$str3 = $row->if_enum;
				$ar3 = explode('@@@',$str3);
				$time3 = !empty($ar3[1]) ? $ar3[1] : time()-600;
				//$time3 = time()-600;
				$site_vars['sys_licence_time'] = $time3;
				$now3 = time();
				
				if($now3 > ($time3-60*60*24*30)){
					$site_vars['sys_licence_prolong'] = 1;
				}
			}
        }
      }
    }
	
	$rows = $db->get_results("SELECT s.*, c.title as categ_title  
		FROM ".$db->tables['site_info']." s 
		LEFT JOIN ".$db->tables['categs']." c ON (s.default_id_categ = c.id) 
		ORDER BY c.sort 
	", ARRAY_A);
	if($rows && $db->num_rows > 0){
		foreach($rows as $k=>$v){
			$ar = array();
			$vars = $db->get_results("SELECT * FROM ".$db->tables['site_vars']." 
				WHERE site_id = '".$v['id']."' ", ARRAY_A);
			if($vars && $db->num_rows > 0){
				foreach($vars as $v1 => $v2){
					
					if(strlen($v2['name']) > 5 && substr($v2['name'], 0, 4) == 'img_'){
						$v[$v2['name']] = array(
							'name' => $v2['description'], 
							'width' => $v2['width'], 
							'height' => $v2['height'] 
						);
					}else{
							$v[$v2['name']] = $v2['value'];
					}					
				}
			}
			$sites[$v['id']] = $v;			
		}
	}
	$site_vars['list_sites'] = $sites;
	
	define('SI_ID', $site_vars['id']);
	define('SI_URL', $site_vars['site_url']);
	define('SI_EMAIL', $site_vars['email_info']);
	define('SI_PHONE', $site_vars['phone']);
	define('SI_TPL', $site_vars['template_path']);
	define('SI_LANG', $site_vars['lang']);
	define('SI_CHARSET', $site_vars['site_charset']);
	define('SI_TIMEZONE', $site_vars['site_time_zone']);
	define('SI_DATEFORMAT', $site_vars['site_date_format']);
	define('SI_TIMEFORMAT', $site_vars['site_time_format']);
	define('SI_DATETIMEFORMAT', $site_vars['site_date_format'].' '.$site_vars['site_time_format']);
	define('SI_TITLE', $site_vars['name_full']);
	define('SI_TITLE_SHORT', $site_vars['name_short']);
	
	if(!isset($site_vars['sys_decimals'])){ $site_vars['sys_decimals'] = 0; }
	if(!isset($site_vars['sys_dec_point'])){ $site_vars['sys_dec_point'] = ','; }
	if(!isset($site_vars['sys_thousands_sep'])){ $site_vars['sys_thousands_sep'] = ' '; }
	if(!isset($site_vars['usd'])){ $site_vars['usd'] = 'usd'; }
	if(!isset($site_vars['euro'])){ $site_vars['euro'] = 'euro'; }
	if(!isset($site_vars['rur'])){ $site_vars['rur'] = 'rub'; }
	if(!isset($site_vars['rub'])){ $site_vars['rub'] = $site_vars['rur']; }

	if(!isset($site_vars['sys_smtp_host'])){ $site_vars['sys_smtp_host'] = ''; }
	if(!isset($site_vars['sys_smtp_port'])){ $site_vars['sys_smtp_port'] = ''; }
	if(!isset($site_vars['sys_smtp_auth'])){ $site_vars['sys_smtp_auth'] = ''; }
	if(!isset($site_vars['sys_smtp_username'])){ $site_vars['sys_smtp_username'] = ''; }
	if(!isset($site_vars['sys_smtp_password'])){ $site_vars['sys_smtp_password'] = ''; }
	if(!isset($site_vars['sys_smtp_on'])){ $site_vars['sys_smtp_on'] = 0; }
	if(!isset($site_vars['sys_smtp_secure'])){ $site_vars['sys_smtp_secure'] = '-'; }
	
	
	define('SI_DECIMALS', $site_vars['sys_decimals']);
	define('SI_DEC_POINT', $site_vars['sys_dec_point']);
	define('SI_THOUSANDS_SEP', $site_vars['sys_thousands_sep']);
	define('SI_USD', $site_vars['usd']);
	define('SI_RUB', $site_vars['rur']);
	define('SI_EURO', $site_vars['euro']);
	define('SI_PRICE_ZERO', $site_vars['sys_price_zero']);
	
	define('SI_SMTP_HOST', $site_vars['sys_smtp_host']);
	define('SI_SMTP_PORT', $site_vars['sys_smtp_port']);
	define('SI_SMTP_AUTH', $site_vars['sys_smtp_auth']);
	define('SI_SMTP_USERNAME', $site_vars['sys_smtp_username']);
	define('SI_SMTP_PASSWORD', $site_vars['sys_smtp_password']);
	define('SI_SMTP_SECURE', $site_vars['sys_smtp_secure']);
	
	if(!empty($site_vars['sys_smtp_on']) && !empty($site_vars['sys_smtp_host'])){
		define('SI_SMTP_ON', 1);
	}
	
	if(isset($ar['kurs_rur']) && !defined('SI_RATE_RUR')){
		define('SI_RATE_RUR', $ar['kurs_rur']);
	}
		
	if(isset($ar['kurs_usd']) && !defined('SI_RATE_USD')){
		define('SI_RATE_USD', $ar['kurs_usd']);
	}
		
	if(isset($ar['kurs_euro']) && !defined('SI_RATE_EURO')){
		define('SI_RATE_EURO', $ar['kurs_euro']);
	}	

	$site_vars = load_currencies($site_vars);
	$site_vars['site_datetime_format'] = $site_vars['site_date_format'].' '.$site_vars['site_time_format'];
		
	/* on_page */
	if(!isset($site_vars['onpage'])){
		if(defined('ONPAGE')){
			$site_vars['onpage'] = ONPAGE;
		}else{
			$site_vars['onpage'] = 20;
		}
	}


	/* найдем доступные шаблоны и типы контен-блоков */
	$sql = "SELECT * 
        FROM ".$db->tables['blocks']." 
        WHERE `type` = 'types' OR `type` = 'templates' 
        ORDER BY `sort`,`title_admin`
    ";
    $arTemplates = $db->get_results($sql, ARRAY_A);
	//$db->debug(); exit;
    $array = array(
    	'types' => array(),
    	'templates' => array(),
    );

    if(!empty($arTemplates)){
    	foreach($arTemplates as $k => $v){
    		$array[$v['type']]['id'.$v['id']] = $v;
    	}
    	

    }
    $site_vars['spec_blocks'] = $array;
	

/*	
	Допишем избранные страницы и публикации
*/	

	$site_vars['fav'] = array();
	$sql = "SELECT c.id, f.`title` as u_title, 
		c.title, c2.title as title2, c3.title as title3, 
		c.shop 
		FROM 
			".$db->tables['fav']." f, 
			".$db->tables['categs']." c 
		LEFT JOIN ".$db->tables['categs']." c2 ON (c.id_parent = c2.id) 
		LEFT JOIN ".$db->tables['categs']." c3 ON (c2.id_parent = c3.id) 
		
		WHERE f.where_placed = 'categ' 
		AND f.user_id = '".$admin_vars['bo_user']['id']."' 
		AND f.where_id = c.id
		ORDER BY f.sort, c3.sort, c2.sort, c.sort, c.title
	";
	$site_vars['fav']['categs'] = $db->get_results($sql, ARRAY_A);
	
	$sql = "SELECT p.id, f.`title` as u_title, 
		p.`name` as title
		FROM ".$db->tables['fav']." f, ".$db->tables['publications']." p 
		WHERE f.where_placed = 'pub' 
		AND f.user_id = '".$admin_vars['bo_user']['id']."' 
		AND f.where_id = p.id
		ORDER BY f.sort, title
	";
	$site_vars['fav']['pubs'] = $db->get_results($sql, ARRAY_A);
	
	$sql = "SELECT p.id, f.`title` as u_title, 
		p.`name` as title
		FROM ".$db->tables['fav']." f, ".$db->tables['products']." p 
		WHERE f.where_placed = 'product' 
		AND f.user_id = '".$admin_vars['bo_user']['id']."' 
		AND f.where_id = p.id
		ORDER BY f.sort, title
	";
	$site_vars['fav']['products'] = $db->get_results($sql, ARRAY_A);
	//$db->debug(); exit;
	
	$sql = "SELECT o.id, o.order_id, o.site_id
		FROM ".$db->tables['fav']." f, ".$db->tables['orders']." o 
		WHERE f.where_placed = 'order' 
		AND f.user_id = '".$admin_vars['bo_user']['id']."' 
		AND f.where_id = o.id
		ORDER BY f.sort, o.order_id
	";
	$site_vars['fav']['orders'] = $db->get_results($sql, ARRAY_A);
	//$db->debug(); exit;
	
	$sql = "SELECT fb.id, fb.ticket
		FROM ".$db->tables['fav']." f, ".$db->tables['feedback']." fb 
		WHERE f.where_placed = 'fb' 
		AND f.user_id = '".$admin_vars['bo_user']['id']."' 
		AND f.where_id = fb.id
		ORDER BY f.sort, fb.ticket
	";
	$site_vars['fav']['fb'] = $db->get_results($sql, ARRAY_A);
	//$db->debug(); exit;
	
	$sql = "SELECT v.id, v.`name`, si.site_url as site 
		FROM ".$db->tables['fav']." f, 
			".$db->tables['site_vars']." v 
		LEFT JOIN ".$db->tables['site_info']." si 
			ON (v.site_id = si.id)
		WHERE f.where_placed = 'var' 
		AND f.user_id = '".$admin_vars['bo_user']['id']."' 
		AND f.where_id = v.id
		ORDER BY f.sort, `name`
	";
	$site_vars['fav']['vars'] = $db->get_results($sql, ARRAY_A);
	//$db->debug(); exit;
	
	$sql = "SELECT v.id, v.`title_admin`, v.title
		FROM ".$db->tables['fav']." f, ".$db->tables['blocks']." v 
		WHERE f.where_placed = 'block' 
		AND f.user_id = '".$admin_vars['bo_user']['id']."' 
		AND f.where_id = v.id
		ORDER BY f.sort, title
	";
	$site_vars['fav']['blocks'] = $db->get_results($sql, ARRAY_A);
	//$db->debug(); exit;
	
	$sql = "SELECT COUNT(*)
		FROM ".$db->tables['comments']." c 
		WHERE c.active = '0' AND (c.record_type = 'product' 
		OR c.record_type = 'pub' 
		OR c.record_type = 'categ' )
	";
	$site_vars['moderate_comments'] = $db->get_var($sql);

	if(isset($site_vars['site_time_zone'])){
		date_default_timezone_set($site_vars['site_time_zone']); 
		$db->query("SET time_zone = '".date('P')."' ");
	}
	
	if(!empty($_GET['update_rate'])){
		load_currency_rate($site_vars);
		header('Location: ?updated=1');
		exit;
	}
    return true;
  }
}

  

?>