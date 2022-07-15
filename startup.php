<?php
  /************************
  *
  * last version 14.10.2019
  * updated: coupons and partners
  * + open site for auth users only 
  * + fixed errors  
  * + list_products_tpl added
  * + list_pubs_tpl added
  * + strval( + strlen( rows 92/93
  *************************/

    $timeparts = explode(" ",microtime());
    $starttime = $timeparts[1].substr($timeparts[0],1);
    
    // Turn register globals off
	if (!function_exists('unregister_GLOBALS')) {
		function unregister_GLOBALS()
		{
			if ( !ini_get('register_globals') ){ return; }
			if ( isset($_REQUEST['GLOBALS']) ){ die('GLOBALS overwrite attempt detected'); }
		
			// Variables that shouldn't be unset
			$noUnset = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST',
				'_SERVER', '_ENV', '_FILES', 'table_prefix');
			$input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV,
				$_FILES, isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
				
			foreach ( $input as $k => $v ){
				if ( !in_array($k, $noUnset) && isset($GLOBALS[$k]) ){ 
					unset($GLOBALS[$k]); 
				}
			}
		}
	}

    function fix_server_vars()
    {
        // Fix for IIS, which doesn't set REQUEST_URI
        if ( empty( $_SERVER['REQUEST_URI'] ) ) {
            $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME']; // Does this work under CGI?

            // Append the query string if it exists and isn't null
            if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
                $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
            }
        }
        
        // Fix for PHP as CGI hosts that set SCRIPT_FILENAME to something 
        // ending in php.cgi for all requests
        if ( isset($_SERVER['SCRIPT_FILENAME']) 
            && ( strpos($_SERVER['SCRIPT_FILENAME'], 'php.cgi') 
                == strlen($_SERVER['SCRIPT_FILENAME']) - 7 ) ){
            $_SERVER['SCRIPT_FILENAME'] = $_SERVER['PATH_TRANSLATED'];
        }
        
        // Fix for Dreamhost and other PHP as CGI hosts
        if ( strstr( $_SERVER['SCRIPT_NAME'], 'php.cgi' ) ){
            unset($_SERVER['PATH_INFO']);        
        }
        
        // Fix empty PHP_SELF
        $PHP_SELF = $_SERVER['PHP_SELF'];
        if ( empty($PHP_SELF) ){
            $_SERVER['PHP_SELF'] = $PHP_SELF = preg_replace("/(\?.*)?$/",'',$_SERVER["REQUEST_URI"]);
        }

        if ( !(phpversion() >= '5.1') ){
            die( 'Your server is running PHP version ' . phpversion() . ' but the site requires at least 5.1' );        
        }
        
        if ( !extension_loaded('mysql') && !extension_loaded('mysqli') ){
            die( 'Your PHP installation appears to be missing the MySQL which is required for the site.' );        
        }
    }

    if(!function_exists("bcsub")){
        function bcsub($a, $b, $c=0){
            return round($a-$b,$c);
        }
    }
    
    unregister_GLOBALS();
    fix_server_vars();
	
	ini_set('session.gc_maxlifetime', 10800);
	ini_set('session.cookie_lifetime', 86400);
	session_start(); 
	//session_start(['cookie_lifetime' => 10800]); // 3 часа php 7.0
	
	
	if(!empty($_GET['welcome'])){
		$wel = strval($_GET['welcome']);
		if(strlen($wel) > 0){
			$_SESSION['welcome'] = $wel;
			// _SERVER["REQUEST_URI"]	/hbjhbhjb/?id=7&welcome=1
			$str_r = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '';
			$str_r = str_replace('?welcome='.$wel, '', $str_r);
			$str_r = str_replace('&welcome='.$wel, '', $str_r);
			header("Location: ".$str_r);
			exit;	
		}	
	}
	
	$_SESSION['qty_visited'] = isset($_SESSION['qty_visited']) ? $_SESSION['qty_visited']+1 : 1;
    // Запускаем шаблон
    require(MODULE."/tpl/src/class.template.php");
    $tpl = new Template_Lite;
    $tpl->compile_dir = ADMIN_FOLDER."/compiled/";
    $tpl->cache_dir = ADMIN_FOLDER."/compiled";	// where cache files are stored

    $tpl->force_compile = true;
    $tpl->compile_check = true;
    $tpl->cache = true;
    $tpl->cache_lifetime = 3600;
    $tpl->config_overwrite = false;
    $tpl->register_function("blocks", "get_blocks_tpl"); // ok
	$tpl->register_function("list_products", "list_products_tpl");
	$tpl->register_function("list_pubs", "list_pubs_tpl");

    if(file_exists(MODULE."/class.site.php")){ require(MODULE."/class.site.php"); }
    $site = new Site();
			
	if(!empty($_GET['coupon'])){
		$site->set_coupon($_GET['coupon']);
	}else{
		if(isset($_SESSION['coupon'])){
			$site->set_coupon($_SESSION['coupon']);
		}
	}
	
    if($site->id == 0){
		if (!headers_sent()) {
			header("HTTP/1.1 503 Service Unavailable");
		}
        echo '<h1>'.$site->GetMessage('error','503').'</h1>';
		if(!empty($site->user['admin']) && !empty($site->user['active'])){
			if(!empty($site->vars['all_sites'])){
				echo '<h3>'.count($site->vars['all_sites']).' web site';
				if(count($site->vars['all_sites']) > 1){ echo 's'; }
				echo ' added</h3><ul>';
				foreach($site->vars['all_sites'] as $v){ 					
					echo '<li>'.$v['site_url'].'</li>'; 
				}
				echo '</ul>';
			}else{
				echo '<p>No added web sites!</p>';
			}
		}
		$db->disconnect();
        exit;
    }

    $tpl->assign("tpl", $site->tpl);    
    $tpl->assign("tpl_url", $site->uri['scheme'].'://'.$site->uri['host'].$site->tpl);

    if(empty($site->page['page'])){ $site->page['page'] = 'blank.html'; }
    $tpl->assign("site", $site->vars);
    $tpl->assign("uri", $site->uri);
    $site->correct_meta_data();
    $tpl->assign("page", $site->page);
    $tpl->assign("user", $site->user);	
    $site->send_headers();
	$tpl->template_dir = $path.$site->tpl;

	if(file_exists($path.$site->tpl.$site->page['page'])){
		$tpl->display($site->page['page']);    
    }else{
        if(!empty($site->page['error'])){
            echo '<p>'.$site->page['error'].'</p>';
        }    

        echo '<p>Template page <b>'.$site->tpl.$site->page['page'].'</b> not found.</p>';
    }
                                 
    $timeparts = explode(" ",microtime());
    $endtime = $timeparts[1].substr($timeparts[0],1);
	if(!empty($site->user['id']) && (isset($site->uri["debug"]) || !empty($site->vars['sys_debug']) )){		
		echo '<div class="hidden-print">';
		echo "<br>";
		echo '<center><small>Template: '.$site->page['page'].'.';
		echo ' Generated in '.bcsub($endtime, $starttime, 4).' sec. Queries: ';
		echo $db->num_queries-3; // porque 3 queries para set codepage in database
		echo ' ('.round($db->timers[$db->num_queries]-$db->timers[1],3).';';
		if(empty($db->use_disk_cache)){ echo 'cache OFF'; }else{ echo 'cache ON'; }
		echo ')';
		echo '</small></center>';
		echo '</div>';
	}
	$db->disconnect();
    exit;
?>