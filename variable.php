<?php
  /************************
  *
  * last version 17.01.2017
  * 
  *************************/
            
    $path = dirname(__FILE__);
    $path = str_replace("\\", "/", $path);
    // PATH EXAMPLE Z:/home/loc/simpla
    if (!defined('PATH')) { define('PATH', $path); }

    /* include website config file if exists */
    if(file_exists($path.'/config.php')){ include_once($path.'/config.php'); }

    // Admin folder
    if (!defined('ADMIN_FOLDER')) { define('ADMIN_FOLDER', 'adminpro'); }

    // AUTHKEY - for password encryption
    if (!defined('AUTHKEY')) { define('AUTHKEY', 'simpla.es'); }

    // Database settings
    if (!defined('HOSTNAME')) { define('HOSTNAME', 'localhost'); }
    if (!defined('DATABASE')) { define('DATABASE', 'brrrrr'); }
    if (!defined('DBUSER')) { define('DBUSER', 'root'); }
    if (!defined('DBPASS')) { define('DBPASS', ''); }

    // MODE - web or localhost
    if (!defined('MODE')) { define('MODE', 'web'); }

    // Support Agency name
    if (!defined('SUPPORT_AGENCY_NAME')) { define('SUPPORT_AGENCY_NAME', 'Simpla!'); }
    // Support Agency email
    if (!defined('SUPPORT_AGENCY_EMAIL')) { define('SUPPORT_AGENCY_EMAIL', 'support@simpla.es'); }

    // Prefix for database tables
    if (!defined('PREFIX')) { define('PREFIX', ''); }
    
    // TPL_DEFAULT
    if (!defined('TPL_DEFAULT')) { define('TPL_DEFAULT', $path.'/tpl/default/'); }
    
    // MODULE
    if (!defined('MODULE')) { define('MODULE', $path.'/module'); }
    
    // UPLOAD
    if (!defined('UPLOAD')) { define('UPLOAD', $path.'/upload'); }
    
    // days in basket for items
    if (!defined('INBASKET')) { define('INBASKET', 30); }
    
    // ONPAGE
    if (!defined('ONPAGE')) { define('ONPAGE', 20); }
    
    // URL_END end for url: / or .html etc.
    if (!defined('URL_END')) { define('URL_END', '/'); }
    
    // BGCOLOR
    if (!defined('BGCOLOR')) { define('BGCOLOR', '#cccccc'); }

    // PATH FOR AVATARS
    $path_avatars = $path.'/upload/avatars/';
    if (!defined('AVATARS')) { define('AVATARS', $path_avatars); }
    
    // EMAIL_CHARSET utf-8 or koi8-r or cp1251
    if (!defined('EMAIL_CHARSET')) { define('EMAIL_CHARSET', 'utf-8'); }
    
    // UNKNOWN_SITE - if site url not found in database
    // true - will show default site, false - error 503 
    if (!defined('UNKNOWN_SITE')) { define('UNKNOWN_SITE', false); }
    
    // MODE_REWRITE - only true    
    if (!defined('MODE_REWRITE')) { define('MODE_REWRITE', true); }

    /* available get queries */
    if(!isset($get_queries)) $get_queries = array();
    if(!isset($get_queries['outside'])) $get_queries['outside'] = array('utm', 
		'utm_source', 'utm_campaign', 'utm_medium', 'utm_term', 'ymclid',
		'utm_content', 'term', 'type', 'source', 'added', 'block', 
		'pos', 'key', 'campaign', 'retargeting', 'ad', 'phrase', 
		'gbid', 'device', 'region', 'region_name', '_openstat', 
		'ad_id', 'frommarket', 'from', 'MNT_TRANSACTION_ID', 
		'calltouch_tm', 'ZGlyZWN0LnlhbmRleC5ydTs0ODU5NDgyMDs4NDUzMzU5NDk1O3lhbmRleC5ydTpwcmVtaXVt',  'ZGlyZWN0LnlhbmRleC5ydTszMDA1Mzc0Nzs0ODI0MjQxNTE5O3d3dy55YW5kZXgucnU6cHJlbWl1bQ', 'ZGlyZWN0LnlhbmRleC5ydTszMDA1Mzc0NzsxMjM0MjY0NjMzMTt5YW5kZXgucnU6cHJlbWl1bQ', 'ZGlyZWN0LnlhbmRleC5ydTs3NDY4MjAzNjsxMjE5OTYxNTQ5OTt5YW5kZXgucnU6cHJlbWl1bQ', 'ZGlyZWN0LnlhbmRleC5ydTszMDA1Mzc0NzsxMDc1MDI0MzM5MDt5YW5kZXgucnU6cHJlbWl1bQ', 'ZGlyZWN0LnlhbmRleC5ydTszNjU1MzYyMTs4NjQ5NDU2NDA3O3lhbmRleC5ydTpwcmVtaXVt', 'ZGlyZWN0LnlhbmRleC5ydTszMDA1Mzc0Nzs0ODI0MjQxNDU5O3lhbmRleC5ydTpwcmVtaXVt', 'ZGlyZWN0LnlhbmRleC5ydTs0ODU5NDgyMDs4NDUzMzU5NDI5O3d3dy55YW5kZXgucnU6cHJlbWl1bQ', 'ZGlyZWN0LnlhbmRleC5ydTszMDA1Mzc0Nzs0ODI0MjQxNTE5O3d3dy55YW5kZXgucnU6cHJlbWl1bQ', 'ZGlyZWN0LnlhbmRleC5ydTszODM1OTUwMTs2NTQ1MDM2NzI5O3lhbmRleC5ydTpwcmVtaXVt', 'ZGlyZWN0LnlhbmRleC5ydTszNjU1MzYyMTs4NjQ5NDU2NDA2O3lhbmRleC5ydTpwcmVtaXVt', 'ZGlyZWN0LnlhbmRleC5ydTs3NDY4MjAzNjsxMjE5OTYxNTUwMDt5YW5kZXgucnU6cHJlbWl1bQ', 'ZGlyZWN0LnlhbmRleC5ydTszMDA1Mzc0Nzs0ODI0MjQxNTIxO3lhbmRleC5ydTpwcmVtaXVt',
'ZGlyZWN0LnlhbmRleC5ydTs3NDY4MjAzNjsxMjE5OTYxNTQ5ODt5YW5kZXgucnU6cHJlbWl1bQ',
		'position', 'yclid', 'etext', 'yadclid', 'yadordid', 'retargeting_id', 'gclid', 'keyword', 'fbclid', 
		'ust', 'usg', 'pm_source', 'pm_block', 'pm_position', 		
		'utm_referer', 'source_type', 
		'gtm_debug',
		'adposition', 'matchtype', 'placement', 'welcome'
	);

    /* reserved get queries */
    if(!isset($get_queries['inside'])) $get_queries['inside']  = array('page',
        'options','debug', 'orderby', 'all', 'done', 'sent', 
        'set_tpl', 'q', 'where', 'add', 'qty', 'c', 'ids', 
		'skip', 'gskip', 'table', 'print', 'price_from', 
		'price_to', 'referer', 'id', 'edit', 'updated', 
		'added', 'deleted', 'cid');


    $table_vars = array(

		"agents" => PREFIX."agents",
		
		"blocks" => PREFIX."blocks",
		
		"categs" => PREFIX."categs",
		"categ_options" => PREFIX."categ_options",
		"changes" => PREFIX."changes",
		"comments" => PREFIX."comments",
		"connections" => PREFIX."connected",
		"counter" => PREFIX."counter",
		"coupons" => PREFIX."coupons",

		"delivery" => PREFIX."delivery",
		"delivery2pay" => PREFIX."delivery2pay",
		"discounts" => PREFIX."discounts",
		
		"email_event" => PREFIX."email_event",
		"email_event_type" => PREFIX."email_event_type", 

		"fav" => PREFIX."favorites",
		"feedback" => PREFIX."feedback",
		"go_out" => PREFIX."go_out",

		"option_groups" => PREFIX."option_groups",
		"option_values" => PREFIX."option_values",
		"options" => PREFIX."options",
		"orders" => PREFIX."orders",
		"orders_cart" => PREFIX."orders_cart",
		"order_payments" => PREFIX."order_payments",
		"order_status" => PREFIX."order_status",
		"org" => PREFIX."organizations",
      
		"partner_orders" => PREFIX."partner_orders",
		"partner_stat" => PREFIX."partner_stat",
		"partner_tags"=> PREFIX."partner_tags",
		"products" => PREFIX."products",
		"product_to_product" => PREFIX."product_to_product",
		"pub_categs" => PREFIX."pub_categs",
		"pub_to_product" => PREFIX."pub_to_product",
		"publications" => PREFIX."publications",

		"ratings" => PREFIX."ratings",
		"site_categ" => PREFIX."site_categ",
		"site_info" => PREFIX."site_info",
		"site_publications" => PREFIX."site_publications",
		"site_vars" => PREFIX."site_vars",

		"subs_sent" => PREFIX."subs_sent", 
		"subs_subjects" => PREFIX."subs_subjects", 
		"subs_subscription" => PREFIX."subs_subscription", 
		"subs_users" => PREFIX."subs_users", 
    
		"uploaded_files" => PREFIX."uploaded_files",
		"uploaded_pics" => PREFIX."uploaded_pics",
		"users" => PREFIX."users",
		"users_prava" => PREFIX."users_prava",
		"undo" => PREFIX."undo",
    
		"visit_log" => PREFIX."visit_log" 
    );

    $ezsql_core = MODULE."/ezsql/shared/ez_sql_core.php";
    $ezsql_path = MODULE."/ezsql/mysqli/ez_sql_mysqli.php";
    
    if(file_exists($ezsql_core)){
      include_once $ezsql_core;
      if(file_exists($ezsql_path)){
        include_once $ezsql_path;
      }
    }else{
      die('<h1>No database class included!</h1>');
    }

    $db = new ezSQL_mysqli(DBUSER, DBPASS, DATABASE, HOSTNAME);
	
	$db->hide_errors();
	$db->query("SET NAMES utf8");
	if(!empty($db->last_error)){ 
		if (MODE != 'install') {
			die("Database connection error!"); 
		}else{
			$db->no_connection = true;
		}
	}
	$db->query("SET CHARACTER SET utf8");
	$db->query("SET collation_connection = 'utf8_general_ci'");
	$db->query("SET SQL_BIG_SELECTS=1");
    $db->tables = $table_vars;
	
	/* disk cache */
	$db->cache_dir = $path."/".ADMIN_FOLDER."/compiled/mysql";
	if(@file_exists($db->cache_dir)){
		$db->cache_timeout = 24;
		$db->use_disk_cache = true;
		$db->cache_queries = true;
	}
        $get_queries['outside'] = false;
	
    //$db->debug_all = true; // Если открыть, то покажутся все запросы 
//	ini_set('xdebug.max_nesting_level', 200);
?>