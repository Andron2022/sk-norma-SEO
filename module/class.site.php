<?php
/*
    SIMPLA! ver 3.01 23.02.2020 
	updated: coupon
	updated: orgs in site variables
	updated: rate_currency auto load
	updated: fixed error with sys_price_zero
	updated: count basket total summ
	updated: bugfix in function check_forms_in_content
	updated: cbr курс исправлен, из-за кэша запрашивался постоянно
	updated: function db_error исправлена
	updated: bug line 214 
	updated: bugs meta tags if site undefinied 29.11.2018
	updated: bugs smtp fixed 08.12.2018
	updated: site_vars may be array 
	updated: http => https => http redirect
	updated: fixed load_siteid_by_url http => https
	updated: SMSC constants 
*/         

class Site {
	var $url;
	var $id        = 0;
	var $page      = array('page'=>'', 'title' => '', 
						'content' => '', 
						'list_pubs2' => array());
	var $uri       = array();
	var $vars      = array();
	var $blocks    = array();
	var $u_vars    = array();
	var $tpl       = TPL_DEFAULT;
    var $lang      = array();
    var $user      = array('id' => 0);
    var $actions   = array();
	var $header_status = 200;
    var $formkeys = array(); // array of keys to check form key in
	var $all_sites = array(); // all sites in admin panel

    // Запущен класс
    // Найдем наш сайт и запишем все переменные окружения 
    // запрошенный урл и данные о посетителе
    function __construct(){
        global $path, $tpl, $db;
        if(!is_null($tpl)){ $this->ver = $tpl->_version; }
        $this->url = $this->request_url();
		$url = $this->url; 
        $uri = parse_url($url);
		
		$uri['requested'] = $uri['scheme'].'://'.$uri['host'].$uri['path'];
		$uri['requested_full'] = $uri['scheme'].'://'.$uri['host'].$uri['path'];

		if(!empty($uri['query'])){
			$uri['requested_full'] .= '?'.$uri['query'];
		}
        
        include_once('list_categs.php');
        include_once('list_options.php');
        include_once('list_pubs.php');
        include_once('list_photos.php');
        include_once('list_files.php');
        include_once('list_products.php');
        include_once('list_comments.php');
        include_once('get_categ.php');
        include_once('get_user.php');
        include_once('get_feedback.php');
		include_once('get_basket.php');
        include_once('send_notification.php');
		

        // если последний символ не равен /
        // переадресация на правильный урл
        if(!empty($uri['path']) 
			&& $uri['path'] != '/' 
			&& strlen(URL_END) > 0 
			&& !defined('SI_CORE')
		){
			if($uri['path'] == '/index.php' ||
				$uri['path'] == '/startup.php' ||
				$uri['path'] == '/config.php' ||
				$uri['path'] == '/variable.php'
			){
				return $this->redirect('/');
			}
			
			if(substr($uri['path'], -4) != '.xml' 
					&& (substr($uri['path'], -4, 1) == '.' 
					|| substr($uri['path'], -5, 1) == '.') 
					&& file_exists($path.$uri['path'])){
						echo $uri['path']; exit;
				$tpl->display($path.$uri['path']);			
			}
			
            $i = 0-strlen(URL_END);
            if(substr($uri['path'], $i) != URL_END 
				&& substr($uri['path'], -4) 
				!= '.xml' && substr($uri['path'], -4) 
				!= '.txt'
			){
				$url = str_replace($uri['path'], $uri['path'].URL_END, $url);
				return $this->redirect($url);
            }
        }

        $this->uri = $uri;       
        $this->correct_path();
		
        if(isset($this->uri['query'])){
			$this->uri['query'] = urldecode($this->uri['query']);
			parse_str($this->uri['query'], $params);
			
			$this->uri['params'] = $params;
			$this->uri['clear_params'] = $params;
			$this->clear_params();
        }
	
		$this->user = call_user_func('User::get_user', $this);
        $this->load_site_settings();

        $this->check_tpl();
		if (class_exists('Template_Lite')) {
			$tpl->template_dir = $path.$this->tpl;
		}
        $this->load_u_settings();

		include_once('get_feedback.php');
		check_sent_feedback($this);

		require_once(MODULE."/get_blocks.php");
		if(defined('__NAMESPACE__')){
			$this->blocks = call_user_func(__NAMESPACE__ .'blocks::get_blocks', $this);
		}else{
			$this->blocks = call_user_func('blocks::get_blocks', $this);
		}		
		$this->page['blocks'] = $this->blocks;
		$this->vars['all_sites'] = $this->all_sites;

        // найдем запрошенную страницу и загрузим данные
		if(!defined('SI_CORE')){
			$this->find_page();
			$this->count_visitor();
		}
        
		if(empty($this->page['basket']) && !empty($_SESSION['basket'])){
			$this->page['basket'] = list_products(0, 'product', $this, 'basket', 99);
			$total_summ = 0;
			$total_qty = 0;
			
			if(!empty($this->page['basket']['list'])){
				foreach($this->page['basket']['list'] as $k => $v){
					$_SESSION['basket'][$v['id']]['info'] = $v;
					$_SESSION['basket'][$v['id']]['title'] = $v['title'];
					$_SESSION['basket'][$v['id']]['price'] = $v['price'];
					
					$_SESSION['basket'][$v['id']]['price_formatted'] = $v['price_formatted'];
					$_SESSION['basket'][$v['id']]['summ'] = $v['price']*$_SESSION['basket'][$v['id']]['qty'];
					$_SESSION['basket'][$v['id']]['summ_formatted'] = $this->price_formatted($_SESSION['basket'][$v['id']]['summ'], $v['currency']);
					$total_summ += $_SESSION['basket'][$v['id']]['summ'];
					$total_qty += $_SESSION['basket'][$v['id']]['qty'];
				}

				$currency = isset($v['currency']) ? $v['currency'] : '';
				$ar_total = array($currency => $total_summ);
				
				$_SESSION['basket_total'] = $this->correct_total_summ($ar_total);
				$_SESSION['basket_total']['qty'] = $total_qty;
				$this->page['basket']['list'] = $_SESSION['basket'];
				$this->page['basket']['total'] = $_SESSION['basket_total'];				
			}
		}
		
    } 
	    
    // ok
    function load_u_settings()
    {  
        global $tpl;
		if(!is_null($tpl)){ 
			if(file_exists($tpl->template_dir."local.php")){
				include_once($tpl->template_dir."local.php");
			}
		}
    
        if(!empty($u_actions)){ $this->actions['local'] = $u_actions; }
        if(!empty($u_vars)){ $this->u_vars = $u_vars; }
        
        include_once(MODULE.'/get_actions.php');
        if(!empty($actions)){ $this->actions['cms'] = $actions; }               
    }
    
    // ok
    function correct_path()
    {
		if(!empty($this->uri['alias_corrected'])){
			//$this->print_r($this->uri,1);
		}
		
		$try_url = '';
        $this->uri['path'] = urldecode($this->uri['path']);
        if($this->uri['path'] == '/'){ $this->uri['path'] = ''; }
		
		/*correct path if contents pseudo request_url
		example /alias/@/power/1.8;10/area/18;100/shum_min/19;40/shum_max/29;50/price/52000;195878/
			instead of 
		/alias/?options[range][power]=1.8;10&options[range][area]=18;100&options[range][shum_min]=19;40&options[range][shum_max]=29;50&options[range][price]=33194;195878
		*/
		$pseudo_url = explode('@', $this->uri['path']);
		if(isset($pseudo_url[0])){
			$this->uri['path'] = $pseudo_url[0];
		}
				
		if(!empty($pseudo_url[1])){
			if(substr($pseudo_url[1], 0, 1) == "/"){
                $pseudo_url[1] = substr($pseudo_url[1], 1);
            }  
			
			if(substr($pseudo_url[1], -1) == "/"){
                $pseudo_url[1] = substr($pseudo_url[1], 0, -1);
            }  
			
			$str = explode('/',$pseudo_url[1]);			
			$sub_array = array();

			if(is_array($str) && count($str) > 1){ 
			
				for($i = 0, $size = count($str); $i < $size; ++$i) {
					
					if(($i % 2) == 0){
						$j = $i+1;
						$k1 = $str[$i];
						$v1 = isset($str[$i+1]) ? $str[$i+1] : '';
						
						if(stristr($v1, ';')){
							$p = explode(';',$v1);
							$v1 = array();
							if(isset($p[0])){ $v1['from'] = $p[0]; }
							if(isset($p[1])){ $v1['to'] = $p[1]; }
						}elseif(stristr($v1, '|')){
							$p = explode('|',$v1);
							$v1 = array();
							if(!empty($p)){
								foreach($p as $p1){
									$v1[] = $p1;
								}
							}
						}
						
						if(!empty($k1)){
							$sub_array[$k1] = $v1;
						}
					}
				}
			}
			$this->uri['params']['options'] = $sub_array;
		}

        $correct_path = isset($this->uri['path']) ? $this->uri['path'] : '';
        if(!empty($correct_path) && $correct_path != '/'){ 
            if(substr($correct_path, 0-strlen(URL_END)) == URL_END){
                $correct_path = substr($correct_path, 0, 0-strlen(URL_END));
				$r = explode('/',$correct_path);
                if(isset($r[1])){ $try_url = '/'.$r[1]; }     
            }
            
			if(substr($correct_path, 0, 1) == "/"){
                $correct_path = substr($correct_path, 1);
            }        
        }
        
        $this->uri['try_url'] = $try_url;
		$this->uri['alias'] = $correct_path;
		
    }

    // ok  clear reserved get params
	// update 18.11.2015 clear all after 'from' and 'frommarket'
	// update 12.02.2018 clear 'clear_query' if empty 'clear_params'
	// update 04.03.2018 possible add alias as GET-data for forms
    function clear_params()
    {
        global $get_queries;			
			
		// correct 'frommarket' key - delete all after get-datas
		$s = explode('frommarket=', $this->uri['query']);
		if(isset($s[0])){ 
			if(substr($s[0], -1) == '&' || substr($s[0], -1) == '?'){
				$s[0] = substr($s[0], 0, -1);
			}
			$this->uri['query'] = $s[0]; 
			parse_str($this->uri['query'], $params);			
			$this->uri['params'] = $params;
		}
		
		// correct 'from' key - delete all after get-datas
		if(!strstr($this->uri['query'], '?from=')){
			$substr1 = '?from=';
		}else{ $substr1 = '&from='; }
		$s = explode($substr1, $this->uri['query']);
		if(isset($s[0])){ 
			if(substr($s[0], -1) == '&' || substr($s[0], -1) == '?'){
				$s[0] = substr($s[0], 0, -1);
			}
			$this->uri['query'] = $s[0]; 
			parse_str($this->uri['query'], $params);			
			$this->uri['params'] = $params;
		}
			
        $this->uri['clear_params'] = $this->uri['params'];
        $this->uri['clear_query'] = $this->uri['query'];

        /* foreach($this->uri['params'] as $k=>$v){
            if(in_array($k, $get_queries['inside']) || in_array($k, $get_queries['outside'])){ 
                unset($this->uri['clear_params'][$k]);
            }  
        }  */
        foreach($this->uri['params'] as $k=>$v){
            if(in_array($k, $get_queries['inside']) || (is_array($get_queries['outside']) && in_array($k, $get_queries['outside']))){ 
                unset($this->uri['clear_params'][$k]);
            }elseif(!$get_queries['outside']){
                unset($this->uri['clear_params'][$k]);
            }
}
		
		/* if isset options[range] we will do this */
		if(!empty($this->uri['params']['options']['range']) 
			&& is_array($this->uri['params']['options']['range'])
		){
			foreach($this->uri['params']['options']['range'] as $q1 => $q2){
				if(is_array($q2)){
					$q_from = 0;
					$q_to = 0;
					if(isset($q2['from'])){ $q_from = $q2['from']; }
					if(isset($q2['to'])){ $q_to = $q2['to']; }
				}else{
					$q3 = explode(';', $q2);
					$q_from = isset($q3[0]) ? $q3[0] : 0;
					$q_to = isset($q3[1]) ? $q3[1] : 0;
				}
				$this->uri['params']['options'][$q1] = array(
					'from' => $q_from,
					'to' => $q_to
				);
			}
			unset($this->uri['params']['options']['range']);
		}
		
		if(isset($this->uri['params']['options']['price_from']) 
			&& !empty($this->uri['params']['options']) 
			&& is_array($this->uri['params']['options'])
		){
			$this->uri['params']['options']['price']['from'] = $this->uri['params']['options']['price_from'];
			unset($this->uri['params']['options']['price_from']);
		}
		if(isset($this->uri['params']['options']['price_to'])
			&& !empty($this->uri['params']['options'])
			&& is_array($this->uri['params']['options'])
		){
			$this->uri['params']['options']['price']['to'] = $this->uri['params']['options']['price_to'];
			unset($this->uri['params']['options']['price_to']);
		}
		
		/* check for alias as get-data */
		if(empty($this->uri['alias']) 
			&& empty($this->uri['path'])
			&& !empty($this->uri['clear_params']['alias'])){
			$this->uri['alias'] = $this->uri['clear_params']['alias'];
			$this->uri['path'] = '/'.$this->uri['clear_params']['alias'].URL_END;
			unset($this->uri['clear_params']['alias']);
			$this->uri['alias_corrected'] = 1;
			$this->uri['requested'] .= $this->uri['alias'].URL_END;
		}
				
        if(empty($this->uri['clear_query'])){ unset($this->uri['clear_query']); }
        if(empty($this->uri['clear_params'])){ 
			unset($this->uri['clear_query']);
			unset($this->uri['clear_params']); 
		}
		//$this->print_r($this->uri,1);
    }
    
    // find url requested # 12.05.2015
    function request_url(){
      $result = ''; // Пока результат пуст
      $default_port = 80; // Порт по-умолчанию
		
      // А не в защищенном-ли мы соединении?
      if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=='on')) {
        $result .= 'https://';
        $default_port = 443;
      } else {
        $result .= 'http://';
      }
      $result .= $_SERVER['HTTP_HOST'];
     
      if ($_SERVER['SERVER_PORT'] != $default_port) {
        $result .= ':'.$_SERVER['SERVER_PORT'];
      }
      $result .= $_SERVER['REQUEST_URI'];
      return $result;
    }

    // Loading site settings
    function load_site_settings(){
      global $db, $tpl, $path;
	  $sql = "SELECT s.*, 
			(SELECT c.id1 
				FROM ".$db->tables['connections']." c 
				LEFT JOIN ".$db->tables['org']." o ON (c.id1 = o.id)
				WHERE c.id2 = s.id 
					AND c.name2 = 'site' 
					AND c.name1 = 'org' 
					AND o.is_default = '1'
					AND o.active = '1'
				GROUP BY o.id 
				ORDER BY o.title
				LIMIT 0, 1 				
			) as orgs";
			
		$ip_addr = !empty($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '';  
        $sess_current = session_id();			
		
		if(!empty($sess_current)){
			$sql .= ",
				(SELECT COUNT(*) FROM ".$db->tables["visit_log"]." 
				WHERE `ip` = '".$db->escape($ip_addr)."'
				AND `sess` = '".$db->escape($sess_current)."'
				AND `referer` <> 'search') as qty_visit_log
			";
			
		}
			
		$sql .= " 	
		FROM ".$db->tables["site_info"]." s 
		";

		if(empty($this->user['id'])){
			$sql .= " WHERE site_active = '1' ";
		}
		
		$sql .= "
		ORDER BY s.site_active DESC, CHAR_LENGTH(s.site_url) DESC, s.id 
		LIMIT 0,30 ";
		
		$this->vars['all_sites'] = $db->get_results($sql, ARRAY_A);	
		if(!empty($this->vars['all_sites'])){
			foreach($this->vars['all_sites'] as $k =>$v){
				$scheme = explode('://', $v['site_url']);
				$this->vars['all_sites'][$k]['site_scheme'] = $scheme[0];
				$this->vars['all_sites'][$k]['site_domain'] = isset($scheme[1]) ? $scheme[1] : 'n\a';
			}
		}
		$this->all_sites = $this->vars['all_sites'];		
		if($this->id == 0){ $row = $this->load_siteid_by_url(); }
		if($this->id == 0 && UNKNOWN_SITE){ 
			$row = $this->load_siteid_by_default(); 
		}
	  
		/* если сайт выключен, то только если 
		есть доступ в админку его можно увидеть */
		if(empty($row['site_active']) 
			&& empty($this->user['id']) 
			&& empty($this->user['active']) 
			&& empty($this->user['admin']) 
		){
			$this->id = 0;
			$lang_default = 'en';
			require_once(MODULE."/lang.php");		
			start_lang_pack($lang_default, $this);
		}
	  
		if($this->id == 0 || !$row){ 
			$this->id = 0;
			$lang_default = 'en';
			require_once(MODULE."/lang.php");		
			start_lang_pack($lang_default, $this);
			return false; 
		}
		
		$this->vars = $row;
		$this->tpl = $row['template_path'];
		$lang_default = 'en';
		require_once(MODULE."/lang.php");		
		start_lang_pack($lang_default, $this);

		$this->uri['site'] = $row['site_url'];
		$f = $path.'/'.ADMIN_FOLDER.'/inc/idna_convert/idna_convert.class.php';
		require_once($f);
		$idn_version = isset($_REQUEST['idn_version']) 
			&& $_REQUEST['idn_version'] == 2003 ? 2003 : 2008;
		$IDN = new idna_convert(array('idn_version' => $idn_version));
		$this->uri['idn'] = $IDN->encode($this->uri['site']);
		$this->uri['host'] = $IDN->decode($this->uri['host']);
		$this->vars['idn_url'] = $this->uri['idn'];		
		
		if(!empty($row['qty_visit_log'])){
			$_SESSION['qty_visited'] = $row['qty_visit_log']+1;
		}
	  
		foreach($row as $k => $v){
			if(!isset($this->vars[$k])){
				$this->vars[$k] = stripslashes($v);
				if($k == 'template_path'){ $this->tpl = $v; }
			}
		}	  
		
		$query = "SELECT `id`, `site_id`, `name`, 
				`value`, `description`, `type`, 
				`autoload`, `if_enum`, `width`, `height` 
			FROM ".$db->tables['site_vars']." 
			WHERE autoload = '1' AND (site_id='".$this->id."' OR site_id='0') LIMIT 0,300 ";
		$results = $db->get_results($query);
		//$db->debug(); exit;
      if(is_array($results)){
      
        $img = $db->get_results("SELECT * 
                    FROM ".$db->tables['uploaded_pics']." 
                    WHERE record_type = 'var' 
                    ORDER BY record_id, is_default DESC, id_in_record
                    ", ARRAY_A);
					
        $img_ar = array();
        if($db->num_rows > 0){
            foreach($img as $v){
                $ar = array(
                            'url' => $this->uri['scheme'].'://'.$this->uri['host'].'/upload/records/'.$v['id'].'.'.$v['ext'], 
                            'width' => $v['width'], 
                            'height' => $v['height'], 
                            'title' => $v['title'],
                            'ext' => $v['ext'],
							'ext_h1' => $v['ext_h1'],
							'ext_desc1' => $v['ext_desc'],
							'ext_desc2' => $v['ext_link']
                        );
                $img_ar[$v['record_id']][] = $ar;
            }
        }
      
        foreach($results as $row)
        {
            if(substr($row->name, 0,4) == 'img_') {
                $imgs = array();
                if(isset($img_ar[$row->id])){
                    $imgs = $img_ar[$row->id]; 
                }
                
                $this->vars[$row->name] = array(
                    'width' => $row->width, 
                    'height' => $row->height,
					'value' => $row->value, 
                    'img' => $imgs 
                );
                
            }else{
				if(isset($this->vars[$row->name])){ 
					if(!is_array($this->vars[$row->name])){
						$prev_value = $this->vars[$row->name];
						$this->vars[$row->name] = array();
						$this->vars[$row->name][] = $prev_value; 
					}
					$this->vars[$row->name][] = $row->value; 
				}else{
					$this->vars[$row->name] = $row->value;
				}        	   
            } 
        }
      }
      
		if(!empty($this->uri['site']) && !empty($this->uri['alias'])){
			$full_url = $this->uri['requested'];
            $new_path = str_replace($this->uri['site'], '', $full_url);
			if($new_path == $full_url){
				$new_path = str_replace('http://'.$this->uri['host'], '', $full_url);
			}
            if($this->uri['path'] != $new_path){
                $this->uri['path'] = $new_path;				
                $this->correct_path();
            }
        }
		
		if(!isset($this->vars['url_end'])){ $this->vars['url_end'] = URL_END; }

		
		/* если запрошен фильтр, то проверим урл 
			усли задано, что красивый урл, а введен с переменными,
			то составим красивый и отправим туда 
			sys_filter_friendly
		*/
		
		if(!empty($this->vars['sys_filter_friendly']) 
			&& !empty($this->uri['params']['options'])
		){
			$u = $this->uri['requested_full'];
			if(!stristr($this->uri['requested_full'], '@')){
				$u = $this->uri['requested'].'@/';
				
				if(!empty($this->uri['params']['options'])){
					
					foreach($this->uri['params']['options'] as $u1 => $u2){
						$u .= $u1.'/';
						if(is_array($u2) && isset($u2['from']) && isset($u2['to'])){
							$u .= $u2['from'].';'.$u2['to'].'/';
						}elseif(is_array($u2)){
							$u .= implode('|',$u2).'/';
						}else{
							$u .= $u2.'/';
						}
					}
				}
								
				if(isset($this->uri['params']['foto'])){
					$u .= '?foto='.$this->uri['params']['foto'];
				}
				$this->redirect($u);				
			}			
		}		
		
        /* add formkey for stop bot messages */
        $this->vars['formkey'] = time().'-'.md5(AUTHKEY.date('Y-m-d'));
        $this->formkeys = array(
             md5(AUTHKEY.date('Y-m-d', strtotime("-1 day"))),
             md5(AUTHKEY.date('Y-m-d')),
             md5(AUTHKEY.date('Y-m-d', strtotime("+1 day")))        
        );
		
		define('SI_ID', $this->vars['id']);
		define('SI_URL', $this->vars['site_url']);
		define('SI_SCHEME', $this->vars['site_scheme']);
		define('SI_DOMAIN', $this->vars['site_domain']);
		define('SI_EMAIL', $this->vars['email_info']);
		define('SI_PHONE', ''); //$this->vars['phone']
		define('SI_TPL', $this->vars['template_path']);
		define('SI_LANG', $this->vars['lang']);
		define('SI_CHARSET', $this->vars['site_charset']);
		define('SI_TIMEZONE', $this->vars['site_time_zone']);
		define('SI_DATEFORMAT', $this->vars['site_date_format']);
		define('SI_TIMEFORMAT', $this->vars['site_time_format']);
		define('SI_TITLE', $this->vars['name_full']);
		define('SI_TITLE_SHORT', $this->vars['name_short']);
		
		if(!isset($this->vars['sys_decimals'])){ $this->vars['sys_decimals'] = 0; }
		if(!isset($this->vars['sys_dec_point'])){ $this->vars['sys_dec_point'] = ','; }
		if(!isset($this->vars['sys_thousands_sep'])){ $this->vars['sys_thousands_sep'] = ' '; }
		if(!isset($this->vars['usd'])){ $this->vars['usd'] = 'usd'; }
		if(!isset($this->vars['euro'])){ $this->vars['euro'] = 'euro'; }
		if(!isset($this->vars['rur'])){ $this->vars['rur'] = 'rub'; }
		if(!isset($this->vars['rub'])){ $this->vars['rub'] = $this->vars['rur']; }
		
		if(!isset($this->vars['sys_smtp_host'])){ $this->vars['sys_smtp_host'] = ''; }
		if(!isset($this->vars['sys_smtp_port'])){ $this->vars['sys_smtp_port'] = ''; }
		if(!isset($this->vars['sys_smtp_auth'])){ $this->vars['sys_smtp_auth'] = ''; }
		if(!isset($this->vars['sys_smtp_username'])){ $this->vars['sys_smtp_username'] = ''; }
		if(!isset($this->vars['sys_smtp_password'])){ $this->vars['sys_smtp_password'] = ''; }
		if(!isset($this->vars['sys_smtp_on'])){ $this->vars['sys_smtp_on'] = 0; }
		if(!isset($this->vars['sys_smtp_secure'])){ $this->vars['sys_smtp_secure'] = '-'; }
		
		define('SI_DECIMALS', $this->vars['sys_decimals']);
		define('SI_DEC_POINT', $this->vars['sys_dec_point']);
		define('SI_THOUSANDS_SEP', $this->vars['sys_thousands_sep']);
		define('SI_USD', $this->vars['usd']);
		define('SI_RUB', $this->vars['rur']);
		define('SI_EURO', $this->vars['euro']);
		if(!isset($this->vars['sys_price_zero'])){ 
			$this->vars['sys_price_zero'] = 0; 
		}
		define('SI_PRICE_ZERO', $this->vars['sys_price_zero']);
		
		if(isset($this->vars['sys_currency'])){
			define('SI_CURRENCY', $this->vars['sys_currency']);
		}
		
		define('SI_SMTP_HOST', $this->vars['sys_smtp_host']);
		define('SI_SMTP_PORT', $this->vars['sys_smtp_port']);
		define('SI_SMTP_AUTH', $this->vars['sys_smtp_auth']);
		define('SI_SMTP_USERNAME', $this->vars['sys_smtp_username']);
		define('SI_SMTP_PASSWORD', $this->vars['sys_smtp_password']);
		define('SI_SMTP_SECURE', $this->vars['sys_smtp_secure']);
		
		if(!empty($this->vars['sys_smtp_on']) && !empty($this->vars['sys_smtp_host'])){
			define('SI_SMTP_ON', 1);
		}

		/* SMSC constants */
		if(empty($this->vars['sys_smsc_login'])){
			$this->vars['sys_smsc_login'] = '';
		}
		define('SMSC_LOGIN', $this->vars['sys_smsc_login']);
		
		if(empty($this->vars['sys_smsc_password'])){
			$this->vars['sys_smsc_password'] = '';
		}
		define('SMSC_PASSWORD', $this->vars['sys_smsc_password']);
		
		if(empty($this->vars['sys_smsc_debug'])){
			$this->vars['sys_smsc_debug'] = 0;
		}
		define('SMSC_DEBUG', $this->vars['sys_smsc_debug']);
		
		if(empty($this->vars['sys_smsc_url'])){
			$this->vars['sys_smsc_url'] = 0;
		}
		define('SMSC_URL', $this->vars['sys_smsc_url']);
		/* end of SMSC constants */		
		
		if(isset($this->vars['sys_mysql_cache']) 
			&& empty($this->vars['sys_mysql_cache'])
		){ 
			$db->use_disk_cache = false;
		}
				
		if(isset($this->vars['site_time_zone'])){
			date_default_timezone_set($this->vars['site_time_zone']); 
			$db->query("SET time_zone = '".date('P')."' ");
		}
		
		$this->check_currency();
        $this->vars['default_menu'] = get_categs('categs', 0, 0, $this->vars['default_id_categ'], $this);
        return;
    }

    // by url
    function load_siteid_by_url(){
		$url_full = $this->uri['requested_full'];	
		$url = $this->uri['requested'];	
		
        if(stristr($url, $this->uri['scheme'].'://www.') === FALSE){
			$url2 = str_replace($this->uri['scheme'].'://', $this->uri['scheme'].'://www.', $url_full);	
		}else{
			$url2 = str_replace($this->uri['scheme'].'://www.', $this->uri['scheme'].'://', $url_full);				
		}
		
        if(count($this->vars['all_sites']) > 0){
			$array = $this->vars['all_sites'];			

			foreach($array as $v){
				if(substr($v['site_url'], -1) != '/'){ $v['site_url'] .= '/'; }
				$pos1 = stripos($url, $v['site_url']);				
				$pos2 = stripos($url2, $v['site_url']);
				
				if(substr($v['site_url'], -1) == '/'){ 
					$v['site_url'] = substr($v['site_url'], 0, -1);
				}
				
				if ($pos1 !== false) {
					$this->uri['path'] = str_replace($v['site_url'], '', $url);
					$this->correct_path();
					$this->id = $v['id'];
                    return $v;
				}
				
				if($this->uri['host'] == $v['site_domain'] 
					&& $this->uri['scheme'] != $v['site_scheme']
				){
					$url3 = str_replace($this->uri['scheme'].'://', $v['site_scheme'].'://', $url_full);
					header('HTTP/1.1 301 Moved Permanently');
                    header("Location: ".$url3);
                    exit;
				}
				
				if('www.'.$this->uri['host'] == $v['site_domain'] 
					&& $this->uri['scheme'] != $v['site_scheme']
				){
					$url3 = str_replace($this->uri['scheme'].'://', $v['site_scheme'].'://www.', $url_full);
					header('HTTP/1.1 301 Moved Permanently');
                    header("Location: ".$url3);
                    exit;
				}
				
				if($this->uri['host'] == 'www.'.$v['site_domain'] 
					&& $this->uri['scheme'] != $v['site_scheme']
				){
					$url3 = str_replace($this->uri['scheme'].'://www.', $v['site_scheme'].'://', $url_full);
					header('HTTP/1.1 301 Moved Permanently');
                    header("Location: ".$url3);
                    exit;
				}
				
				if ($pos2 !== false) {
                    header('HTTP/1.1 301 Moved Permanently');
                    header("Location: ".$url2);
                    exit;
				}
			}
		}
		
        return false;
    }

    // get default website 
    function load_siteid_by_default(){
        global $db;
        $query = "SELECT * 
			FROM ".$db->tables["site_info"]." 
		";
		if(empty($this->user['id'])){
			$query .= " WHERE site_active = '1' 
			";
		}
			 
		$query .= " ORDER BY id limit 0,1 ";
        $row = $db->get_row($query, ARRAY_A);
        if(!$row || $db->num_rows == 0) { return false; }
		if($row['site_url'] != 'http://'.$this->uri['host']){
			$row['site_url'] = 'http://'.$this->uri['host'];
		}
        $this->id = $row['id'];
        return $row;
    }
    
    // correct template
    function check_tpl(){
        if(!empty($_GET['set_tpl'])){
            $this->tpl = '/tpl/'.$_GET['set_tpl'].'/';
            $_SESSION['tpl'] = $this->tpl; 
        }else{
            if(!empty($_SESSION['tpl'])){
                $this->tpl = $_SESSION['tpl'];
            }
        }
        $this->uri['tpl'] = $this->tpl;      
    }
    
    // ok
    function print_r($ar, $exit=false)
    {
        echo '<pre>';
        if(!is_array($ar)) { 
            print($ar);
        }else{
            print_r($ar);
        }
        echo '</pre>';
        if($exit){ exit; }
    }
    
    // ok
    function error_page($error='404', $str='')
    {
        global $tpl;
        $this->header_status = $error;
        if($error == 404){
            //$tpl->display('pages/404.html');
            $this->page['page'] = 'pages/404.html'; 
            $this->page['title'] = $this->GetMessage('error', 'error_404'); 
            $this->page['metatitle'] = $this->GetMessage('error', 'error_404'); 
        }elseif($error == 403 || $error == 429){
            //$tpl->display('pages/404.html');
            $this->page['page'] = 'pages/403.html'; 
            $this->page['title'] = $error; 
            $this->page['metatitle'] = $this->GetMessage('error', $error);
			$this->page['content'] = $this->GetMessage('error', $error); 
        }else{
            //$tpl->display('pages/404.html');
            $this->page['title'] = $this->GetMessage('error', 'error').' '.$error;
            $this->page['error'] = $this->GetMessage('error', 'error').' '.$error;
            $this->page['page'] = 'blank.html'; 
        }
		if(!empty($str)){ $this->page['content'] = $str; }
        return $this->page;
    }
    
    // ok
    function find_page()
    {	
		$_SESSION["captcha"] = array();
		/* captcha */
		$captcha_size = !empty($this->vars['sys_captcha']) ? intval($this->vars['sys_captcha']) : 5;
		if(!empty($captcha_size)){
			include_once(MODULE."/captcha/captcha.php");
			$this->vars['captcha'] = array();
			$this->vars['captcha']['size'] = $captcha_size;
			$this->vars['captcha']['name'] = random_string(5);
			$start = 1;
			$stop = 9;
			for($i = 1; $i < $captcha_size; $i++){
				$start .= 0;
				$stop .= 9;
			}
			$value = function_exists("mt_rand") 
				? strval(mt_rand($start,$stop)) 
				: strval(rand($start,$stop));			
			$this->vars['captcha']['rnd'] = $value;	
			// $this->vars['captcha']['rnd'] = '';	
			$_SESSION["captcha"][$this->vars['captcha']['name']] = array(
				"code" => $value,
				"time" => time()
			);
		}

        if(!empty($this->uri['alias'])){
          $this->get_page_by_action();
          //if(empty($this->page['page']) && empty($this->page['error'])){
			if(empty($this->page['page'])){
				$this->get_page_by_alias(); 
			}
        }

        if(!empty($this->uri['clear_params']) 
			&& $this->header_status == 200)
		{
            $this->header_status = 404;
            $this->page['page'] = 'pages/404.html';
        }elseif(empty($this->page['page'])){
            if(!empty($this->uri['alias'])){
                $this->header_status = 404;
                $this->page['page'] = 'pages/404.html';
            }else{
                //$this->page['list_topcategs'] = get_categs('categs', $this->vars['default_id_categ'], 0, 0, $this);
                $this->page['page'] = 'index.html'; 
                if(!empty($this->vars['default_id_categ'])){
                    $list_ar = array('list_photos','list_files','list_comments');
					if(defined('__NAMESPACE__')){
						$this->page = call_user_func(__NAMESPACE__ .'categ::get_page', $this, $this->vars['default_id_categ'],$list_ar);
					}else{
						$this->page = call_user_func('categ::get_page', $this, $this->vars['default_id_categ'],$list_ar);					
					}                    
                }                               
            }
        }

        if(empty($this->vars['sys_skip_visited_products'])){
			$qty = isset($this->vars['sys_qty_visited_products']) ? $this->vars['sys_qty_visited_products'] : 6;
            $this->page['visited_products'] = list_products(0, 'product', $this, 'visited', $qty);
        }

        if(empty($this->vars['sys_skip_visited_pubs'])){
			$qty = isset($this->vars['sys_qty_visited_pubs']) ? $this->vars['sys_qty_visited_pubs'] : 6;
            $this->page['visited_pubs'] = list_pubs(0, $this, 'visited', $qty);
        }
        
        if(empty($this->vars['sys_skip_spec_products'])){
			$qty = isset($this->vars['sys_qty_spec_products']) ? $this->vars['sys_qty_spec_products'] : 6;	
			if(!isset($this->page['spec_products'])){
				$this->page['spec_products'] = list_products(0, 'product', $this, 'spec', $qty);
			}
        }

	
        if(empty($this->vars['sys_skip_last_products'])){
			$qty = isset($this->vars['sys_qty_last_products']) ? $this->vars['sys_qty_last_products'] : 6;
			if(!isset($this->page['last_products'])){
				$this->page['last_products'] = list_products(0, 'product', $this, 'last', $qty);
			}
        }
	
		if(!empty($this->vars['sys_qty_last_pubs']) && empty($this->vars['sys_skip_last_pubs'])){
			$arr = list_pubs(0, $this, 'last', $this->vars['sys_qty_last_pubs']);
			$this->page['last_pubs'] = $arr;
		}		

        if(empty($this->vars['sys_skip_starred_pubs'])){
			$qty = isset($this->vars['sys_qty_starred_pubs']) ? $this->vars['sys_qty_starred_pubs'] : 6;
            if(!isset($this->page['starred_pubs'])){
				$this->page['starred_pubs'] = list_pubs(0, $this, 'starred', $qty);
			}
        }

        if(!empty($this->vars['sys_extra_page'])){
            $list_ar = array('list_photos');
            $this->page['extra_page'] = call_user_func('categ::get_page', $this, 
                $this->vars['sys_extra_page'], $list_ar);
        }
		
		/* list_tags найдем */
		if(empty($this->page['id'])){ $this->page['id'] = 0; }
		$cat2 = !empty($this->page['action']) 
			&& $this->page['action'] == 'category' 
			&& empty($this->page['list_products'])
			? $this->page['id'] : 0;	
		if(!empty($this->vars['default_id_categ']) && 
			$cat2 == $this->vars['default_id_categ']
		){ 		
			$cat2 = 0; 
		}
        require_once(MODULE."/list_tags.php");
		$this->page['list_tags'] = list_tags($this, $cat2);
		
		$this->page = $this->check_forms_in_content($this->page);
		//$this->page = $this->check_forms_in_content();
		
		require_once(MODULE."/get_blocks.php");
		if(defined('__NAMESPACE__')){
			$this->blocks = call_user_func(__NAMESPACE__ .'blocks::get_blocks', $this);
		}else{
			$this->blocks = call_user_func('blocks::get_blocks', $this);
		}		
		$this->page['blocks'] = $this->blocks;
		
		if(function_exists('modify_page_data')){
			$this->page = modify_page_data($this); 
		}
		
    }


	function check_forms_in_content($page) 
	{
		global $tpl;
		//$page = $this->page;
		/*
		$tpl->assign("site", $this->vars);
		$tpl->assign("page", $page);
		$tpl->assign("uri", $this->uri);
		*/
		if(empty($tpl->_vars['site'])){ $tpl->assign('site', $this->vars); }
		if(empty($tpl->_vars['uri'])){ $tpl->assign('uri', $this->uri); }
		
		if(!empty($page['content'])){
			if(!empty($this->blocks['form'])){
				$ar = array();
				foreach($this->blocks['form'] as $k=>$v){
					$str = empty($page['clear']) ? $tpl->fetch_html($v['html']) : $tpl->fetch_html($v['html']);
					//$str = empty($page['clear']) ? $v['html'] : '';
					$page['content'] = str_replace('%'.$k.'%', $str, $page['content']);
				}
			}
		}
		return $page;
	}


    function get_page_by_action()
    {
        global $path;	
		$this->uri['alias'] = mb_strtolower($this->uri['alias'], 'utf-8');	
		
		/* check for tinyurls */
		if(strlen($this->uri['alias']) > 2 
			&& substr($this->uri['alias'], 0, 2) == 'o/'
		){
			include_once(MODULE.'/get_order.php');
			$this->page = call_user_func('Order::get_order', $this);
			//$o = new $order;
			//$this->page = $o->get_order($this, $this->uri['alias']);
			return;
		}elseif(strlen($this->uri['alias']) > 2 
			&& substr($this->uri['alias'], 0, 2) == 'f/'){
			include_once(MODULE.'/get_feedback.php');
			$this->page = call_user_func('Feedback::get_feedback', $this);
			return;
		}elseif(
			(strlen($this->uri['alias']) > 3 
			&& substr($this->uri['alias'], 0, 4) == 'tag/')
			||
			(strlen($this->uri['alias']) > 2 
			&& substr($this->uri['alias'], 0, 3) == 'tag') 				
		){
			include_once(MODULE.'/get_option.php');
			$this->page = call_user_func('Option::option_by_tag', $this);
			return;
		}		
		
        $u_alias = isset($this->actions['local'][$this->uri['alias']]) ? 
                $this->actions['local'][$this->uri['alias']] : ''; 
        $u_func = isset($this->actions['local'][$this->uri['alias']]['func']) ? 
                $this->actions['local'][$this->uri['alias']]['func'] : '';
        $u_file = isset($this->actions['local'][$this->uri['alias']]['file']) ? 
                $this->actions['local'][$this->uri['alias']]['file'] : '';
			
		if(!empty($u_alias)){
        
          if(!empty($u_file) && file_exists($path.$this->tpl.$u_file)){
              include_once($path.$this->tpl.$u_file);
          }elseif(!empty($u_file)){
              $this->page['error'] = $u_file.' not found';
              return;
          } 
  
          if(!empty($u_alias) && !empty($u_func) && function_exists($u_func)){
              return call_user_func($u_func);
          }elseif(!empty($u_func)){
              $this->page['error'] = 'The function <b>'.$u_func.'</b> not found';
              return;
          } 
        } 

        $u_alias = isset($this->actions['cms'][$this->uri['alias']]) ? 
                $this->actions['cms'][$this->uri['alias']] : ''; 
        $u_func = isset($this->actions['cms'][$this->uri['alias']]['func']) ? 
                $this->actions['cms'][$this->uri['alias']]['func'] : ''; 
        $u_file = isset($this->actions['cms'][$this->uri['alias']]['file']) ? 
                $this->actions['cms'][$this->uri['alias']]['file'] : '';
	
	
        if(!empty($u_alias)){
                     
          if(!empty($u_file) && file_exists(MODULE.'/'.$u_file)){
              include_once(MODULE.'/'.$u_file);
          }elseif(!empty($u_file)){
              $this->page['error'] = $u_file.' not found';
          } 

          $f = explode('::', $u_func);
          if(!isset($f[0])){ $f[0] = ''; }
          if(!isset($f[1])){ $f[1] = ''; }
		  
          if(!empty($f[0]) && !empty($f[1])){
              if(!empty($u_alias) && !empty($u_func) && method_exists($f[0], $f[1])){
				$this->page = call_user_func($u_func, $this);
				///$this->print_r($this->page,1);
              }else{
                  $this->page['error'] = 'The method <b>'.$f[1].'</b> in class <b>'.$f[0].'</b> not found';
                  return;
              } 
          }elseif(!empty($f[0])){
              if(!empty($u_alias) && !empty($u_func) && function_exists($u_func)){
			    $this->page = call_user_func($u_func);
              }else{
                  $this->page['error'] = 'The function <b>'.$u_func.'</b> not found';
                  return;
              } 
          }
        }
    }
    
    // find page by alias $this->uri['alias']
    function get_page_by_alias()
    {
        global $db;		
		//echo $this->uri['alias']; exit;
        $slug = $this->uri['alias'];
		$this->uri['requested'] = '/'.$slug.URL_END;
		/* проверка на фильтр в строке */
		$ar = explode('/@/', $slug);
		if(isset($ar[1])){ 
			$slug = $ar[0]; 
			$this->uri['alias'] = $ar[0];
			$this->uri['path'] = '/'.$ar[0].URL_END;
			//$this->uri['requested'] = '/'.$ar[0].URL_END;
			$ar = explode('/', $ar[1]);
			if(count($ar) > 1){
				$f = array();
				for($i=0, $j=1; $i < count($ar); $i=$i+2, $j=$i+1){
					if(isset($ar[$i]) && isset($ar[$j])){
						$f_ar = explode('|', $ar[$j]);
						if(count($f_ar) > 1){
							$f[$ar[$i]] = $f_ar;
						}else{
							$f[$ar[$i]] = $ar[$j];
						}
						
						
					}
				}				
				$this->page['requested_filter'] = $f;
			}
		}

        if(!$this->special_urls($slug)){
          return;
        }
        
        $query = array(
            "category" => "SELECT alias, 'category' as action 
                FROM ".$db->tables['categs']." WHERE alias = '".$db->escape($slug)."' ",
            "pub" => "SELECT alias, 'pub' as action 
                FROM ".$db->tables['publications']." WHERE alias = '".$db->escape($slug)."' ",
            "product" => "SELECT alias, 'product' as action 
                FROM ".$db->tables['products']." WHERE alias = '".$db->escape($slug)."' "
        );
        
        $queries = $query;
        foreach($queries as $k=>$v){
          	$queries[$k] = $v;
        }
        $query1 = join(" \nUNION ",array_values($queries));        
        $row = $db->get_row($query1);
		    
        if($row && $db->num_rows > 0){
            $u_alias = $row->action;
            $this->uri['action'] = $row->action; 
            $this->page['action'] = $row->action; 
            $this->page['page'] = $row->action.'.html'; 
            $u_func = isset($this->actions['cms'][$row->action]['func']) ? 
                $this->actions['cms'][$row->action]['func'] : ''; 
            $u_file = isset($this->actions['cms'][$row->action]['file']) ? 
                $this->actions['cms'][$row->action]['file'] : '';

            if(!empty($u_file) && file_exists(MODULE.'/'.$u_file)){
                include_once(MODULE.'/'.$u_file);
            }elseif(!empty($u_file)){
                $this->page['error'] = $u_file.' not found';
            } 
            
            $f = explode('::', $u_func);
            if(!isset($f[0])){ $f[0] = ''; }
            if(!isset($f[1])){ $f[1] = ''; }
            
            if(!empty($f[0]) && !empty($f[1])){
                if(!empty($u_alias) && !empty($u_func) && method_exists($f[0], $f[1])){   
                    if($f[0] == 'Pub'){			
                      $this->page = call_user_func($u_func, $this);
                    }else{
                      $list_ar = array('list_pubs','list_products',
                          'list_photos','list_comments','list_files', 
                          'list_options','breadcrumbs',
						  'list_topcategs','list_bottomcategs');                            
                      $this->page = call_user_func($u_func, $this, 0, $list_ar);
                    }
                }else{
					$this->page['error'] = 'The method <b>'.$f[1].'</b> in class <b>'.$f[0].'</b> not found';
                    return;
                }
            }elseif(!empty($f[0])){
                if(!empty($u_alias) && !empty($u_func) && function_exists($u_func)){
                    $this->page = call_user_func($u_func);
                }else{
                    $this->page['error'] = 'The function <b>'.$u_func.'</b> not found';
                    return;
                }
            }
        }else{
            $this->page['error'] = 'Alias \'<b>'.$this->uri['alias'].'</b>\' not found';
            $this->header_status = 404;
        }

/*		
		if(!empty($this->page['filters'])){
			foreach($this->page['filters'] as $k => $v){
				if(isset($this->uri['clear_params'][$v['alias']])){
					unset($this->uri['clear_params'][$v['alias']]);
				}
				if(isset($_GET[$v['alias']])){
					$this->page['filters'][$k]['requested'] = $_GET[$v['alias']];
				}
			}
		}		
*/		
    }
    

    function error_message($str='')
    {
        global $db, $tpl;
        $this->page['page'] = 'blank.html';
        $this->page['title'] = empty($this->page['title']) ? $this->GetMessage('error', 'error') : $this->page['title'];
        $this->page['intro'] = empty($this->page['intro']) ? '' : $this->page['intro'];
        $this->page['content'] = empty($this->page['content']) ? '' : $this->page['content'];
        $this->page['error'] = $str;
        $this->header_status = 400;
        return $this->page; 
    }
    
    function db_error($str=NULL)
    {
        global $db, $tpl, $path;
        $this->page['page'] = 'blank.html';
        $this->page['title'] = $this->GetMessage('error', 'error');
		if(!empty($db->captured_errors) && !empty($this->user['admin'])){
			$this->page['title'] = $this->GetMessage('error', 'db');
			foreach($db->captured_errors as $v){
				$str .= '<h3><b>'.$this->GetMessage('error','error').'</b></h3>';
				$str .= $v['error_str'];
				$str .= '<h3><b>Query</b></h3>';
				$str .= $v['query'];
			}				
		}else{
			$this->page['content'] = $this->GetMessage('error', 'db');
			$str = '';
		}
        $this->page['error'] = $str;
        $this->header_status = 400;
		$this->tpl = str_replace($path, '', $this->tpl);
		$tpl->assign("tpl", $this->tpl);    
		$tpl->assign("tpl_url", $this->uri['scheme'].'://'.$this->uri['host'].$this->tpl);
		
		$tpl->assign("site", $this->vars);
		$tpl->assign("uri", $this->uri);
		$this->correct_meta_data();
		$tpl->assign("page", $this->page);
		$tpl->assign("user", $this->user);	
		$this->send_headers();
		$tpl->template_dir = $path.$this->tpl;
		if(!file_exists($tpl->template_dir.$this->page['page'])){ die($this->page['content']); }
		$tpl->display($this->page['page']);
		die();
        return $this->page; 
    }
    
  // ok
  function correct_meta_data(){
    if(empty($this->vars['meta_title'])){ $this->vars['meta_title'] = $_SERVER['HTTP_HOST']; }
    if(empty($this->vars['meta_keywords'])){ $this->vars['meta_keywords'] = $_SERVER['HTTP_HOST']; }
    if(empty($this->vars['meta_description'])){ $this->vars['meta_description'] = $_SERVER['HTTP_HOST']; }
    if(empty($this->page['metatitle'])){ $this->page['metatitle'] = $this->vars['meta_title']; }
    if(empty($this->page['keywords'])){ $this->page['keywords'] = $this->vars['meta_keywords']; }
    if(empty($this->page['description'])){ $this->page['description'] = $this->vars['meta_description']; }
  }
  
  /* set pagination */
  function set_pages($results, $where='pubs', $limit=ONPAGE){
    $pages = ceil($results/$limit);
    $page = isset($this->uri['params']['page']) ? intval($this->uri['params']['page']) : 0;
    $no_page_params = isset($this->uri['params']) ? $this->uri['params'] : array();
    if(isset($no_page_params['page'])){ unset($no_page_params['page']); }

	$current = empty($this->uri['requested_full']) 
		? $this->uri['path'] : $this->uri['requested_full'];
    
    if(!empty($no_page_params) && !stristr($current, '@')){
        foreach($no_page_params as $k => $v){
            $q = isset($q) ? $q.'&' : '?';
            $q .= $k.'='.$v; 
        }
        $sep = '&';
    }
    /* OLD
	$current = empty($this->uri['requested']) ? $this->uri['path'] : $this->uri['requested'];
	*/
	
	if(!empty($this->uri['params']['page'])){
		$p = "page=".$this->uri['params']['page'];
		$current = str_replace('?'.$p, '', $current);
		$current = str_replace('&'.$p, '', $current);
	}
	
    if(!isset($q)){ $sep = '?'; $q = ''; }
    $p_next = $page+1;
    $p_prev = $page-1;
    if($p_prev < 1){ 
        $prev_link = $p_prev < 0 ? '' : $current.$q; 
    }else{
        $prev_link = $current.$q.$sep.'page='.$p_prev; 
    }
    $next_link = $p_next < $pages ? $current.$q.$sep.'page='.$p_next : '';
    
    $skip_num = isset($this->vars['sys_skip_num_pages']) ? $this->vars['sys_skip_num_pages'] : 3;
    $skip_left = $page-$skip_num-1; 
    $skip_right = $page+1+$skip_num; 
    
    $ar['pages'] = array();
    $ar['prev'] = $prev_link;
    if(!empty($skip_left) && ($page-$skip_num) > 0){ $ar['skip_left'] = 1; }
    if(!empty($skip_right) && ($page+2+$skip_num) < $pages){ $ar['skip_right'] = 1; }
    
    for($i=0; $i<$pages; $i++){
        $start = $i == 0 ? 1 : 0;
        $stop = ($pages-$i) == 1 ? 1 : 0; 
        $active = $page == $i ? 1 : 0;
        $link = $i == 0 ? $current.$q : $current.$q.$sep.'page='.$i;

        if($start > 0 || $stop > 0 || $active > 0 || ($i>$skip_left && $i < $skip_right)){
        
          $ar['pages'][] = array(
              'id' => $i,
              'page' => $i+1,
              'start' => $start,
              'stop' => $stop,
              'link' => $link,
              'active' => $active
          );
        }
    }
	
    $ar['next'] = $next_link;
    $ar['all_pages'] = $pages;
    //$this->uri['pagination'][$where] = $ar;
    global $tpl;
    $tpl->assign('pages_ar', $ar);
    return $tpl->fetch('pages/pages.html');    
  }

  
  // Получение ссылок на верхние страницы, путь до запрошенной страницы
  function get_breadcrumbs($id, $ar=array() ){
    // find parent categs for id
	$ar = array();
	if(isset($this->vars['default_menu'][$id])){		
		$ar[] = $this->vars['default_menu'][$id];
		$j = $this->vars['default_menu'][$id]['id_parent'];
		for ($i = 1; $i <= 10; $i++) {
			if($j == 0){ 
				$i = 11; 
			}elseif(isset($this->vars['default_menu'][$j])){
				$ar[] = $this->vars['default_menu'][$j];
				$n = $this->vars['default_menu'][$j]['id_parent'];
				$j = $n;
			}
		}
	}else{
	
		global $db;  
		$sql = "SELECT c.id, c.title, c.alias, c.active, c.`sort`, c.id_parent 
			FROM ".$db->tables['categs']." c, 
				".$db->tables['site_categ']." sc             
			WHERE c.id = '".$id."'
				AND c.active > '0' 
				AND c.id = sc.id_categ
				AND sc.id_site = '".$this->vars['id']."' 
			ORDER BY c.`sort`, c.`title` ";
		$rows = $db->get_results($sql);
		if($db->num_rows > 0){
			foreach($rows as $row){
			  $link = $this->vars['site_url'].'/'.$row->alias.URL_END;		
			  $ar[] = array(
				'id' => $row->id,
				'title' => stripslashes($row->title),
				'link' => $link,
				'link_idn' => $this->uri['idn'].'/'.$row->alias.URL_END, 
				'alias' => $row->alias
			  );
			  if($row->id_parent > 0 && $row->id_parent != $this->vars['default_id_categ']){
				return $this->get_breadcrumbs($row->id_parent, $ar);
			  }
			}
		}
	}
	
    if(count($ar) > 0){
      $ar = array_reverse($ar);
    }
    
    return $ar;
  }
  
  
    function price_formatted($price, $currency, $decimals=NULL){
		return price_formatted($price, $currency, $decimals);        
    }
	
	function correct_total_summ($total_summ, $delivery=0, $discount=0, $coupon=0, $nds=0){
		return correct_total_summ($total_summ, $delivery, $discount, $coupon, $nds);
	}
    

    function GetMessage($var1='',$var2='',$var3=''){
        if(!empty($var1) && !empty($var2) && !empty($var3)){
            if(!isset($this->lang[$var1][$var2][$var3])){
                return $var1.'|'.$var2.'|'.$var3;
            }else{
                return $this->lang[$var1][$var2][$var3];
            }
        }elseif(!empty($var1) && !empty($var2)){
            if(!isset($this->lang[$var1][$var2])){
                return $var1.'|'.$var2;
            }else{
                return $this->lang[$var1][$var2];
            }
        }elseif(!empty($var1)){
            if(!isset($this->lang[$var1])){
                return $var1;
            }else{
                return $this->lang[$var1];
            }
        }else{
            return;
        }
    }
    
    function check_form_key($when)
    {
        // 1437665023-df6b11abf9c82730e96022e959e4793b
        if(empty($when)){ return false; }
        $ar = explode('-', $when);
        if(count($ar) == 2 && !empty($ar[0]) && !empty($ar[1])){       
            $diff = time()-$ar[0];
            if($diff < (60*60*24) && in_array($ar[1], $this->formkeys)){
                return true;
            }
        }
        return false;
    }
    
    function set_visited_cookie($id, $where)
    {
        /* cookie приводит к ошибкам, пока сделано через сессию */
        if(isset($_SESSION['visited'][$where])){
            if(!in_array($id, $_SESSION['visited'][$where])){
                $_SESSION['visited'][$where][] = $id;
            }
        }else{
            $_SESSION['visited'][$where][] = $id;
        }                
        return;
    }
    
    /* проверяем есть ли в сессии, если нет, 
    то прибавляем к счетчику страницы */
    function register_counter($where, $id)
    {
        if($where != 'pub' && $where != 'product'){ return; }
		if(empty($id)){ return; }
        if(!isset($_SESSION['counter'][$where])){ $_SESSION['counter'][$where] = array(); }
        $ip_addr = !empty($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '';  
        $sess_current = session_id();
        global $db;            

        if(!in_array($id, $_SESSION['counter'][$where])){
            $table = $where == 'pub' ? $db->tables['publications'] : $db->tables['products'];
            $_SESSION['counter'][$where][] = $id;
            $db->query("UPDATE ".$table." SET `views` = `views`+1 WHERE id = '".$id."' ");
                                    
            $db->query("INSERT INTO ".$db->tables['counter']." 
                (`id_page`, `for_page`, `ip`, `time`, `hits`, `sess`) 
                VALUES ('".$id."', '".$where."', '".$db->escape($ip_addr)."', 
                '".date('Y-m-d H:i:s')."', '1', 
                '".$db->escape($sess_current)."') ");
            
        }else{
            if(!empty($sess_current)){
                $db->query("UPDATE ".$db->tables['counter']." 
                    SET `hits` = `hits`+1 
                    WHERE `sess` = '".$db->escape($sess_current)."'
                        AND `for_page` = '".$where."'
                        AND `id_page` = '".$id."'
                    ");
            }
        }
        return;
    }
    
    function count_visitor()
    {
		$this->user_agent(); // find user_agent
		if(empty($this->vars['sys_count_visitors'])){ return; }
        $ip_addr = !empty($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '';  
        $sess_current = session_id();
        global $db;

		$page_path = !empty($this->uri['path']) ? urldecode($this->uri['path']) : '/';
		if($page_path == '/favicon.ico/' 
			|| $page_path == $this->vars['site_url'].'/favicon.ico/' 
			|| stristr($page_path, $this->uri['tpl'])){
			return;
		}
		
        if($_SESSION['qty_visited'] == 1){        
            // вычислим реферер
            $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            $ref_ar = parse_url($ref); 
            $ref_host = isset($ref_ar['host']) ? urldecode($ref_ar['host']) : ''; 
            $ref_query = isset($ref_ar['path']) ? urldecode($ref_ar['path']) : '';
			$ref_query .= isset($ref_ar['query']) ? '?'.urldecode($ref_ar['query']) : '';			
            
            /* если сайт совпадает со своим, то реф не пишем */
            if($this->uri['host'] == $ref_host){
                $ref_host = '';
                $ref_query = ''; 				
            }
             
			$ref_query = mb_substr($ref_query, 0, 220, 'UTF-8');
            $partner_key = isset($this->uri['params']['from']) 
				? urldecode($this->uri['params']['from']) : '';  

			if(empty($ref_host) && empty($ref_query) && empty($partner_key)){
				// return; 
				// выключим, если нет предмета реф-перехода
				// то не будем писать в базу
			}
            
            $sql = "INSERT INTO ".$db->tables["visit_log"]." (`time`, `ip`, 
                `sess`, `referer`, `referer_query`, `page`, `partner_key`, 
                `pages_visited`, `qty_pages_visited`, `site_id`) 
                VALUES('".date('Y-m-d H:i:s')."', '".$db->escape($ip_addr)."', 
                '".$db->escape($sess_current)."', 
                '".$db->escape($ref_host)."', '".$db->escape($ref_query)."',                 
                '".$db->escape($page_path)."', '".$db->escape($partner_key)."', 
                '".$db->escape($page_path)."', '1', '".$this->vars['id']."') ";
            $db->query($sql);
			$uid = $db->insert_id;
			/* add in DB about user */
			if(!empty($this->user['about'])){
				$u_about = serialize($this->user['about']);
				$u_type = !empty($this->user['about']['is_browser']) 
					? 'browser' : '';
				if(!empty($this->user['about']['is_mobile'])){
					$u_type = 'mobile';
				}
				
				if(!empty($this->user['about']['is_robot'])){
					$u_type = 'robot';
				}
				
				/* all */
				$sql = "INSERT INTO option_values (`id_option`, `id_product`, 
					`value`, `where_placed`, `value2`, 
					`value3`) VALUES ('".$uid."', '0', 
					'".$db->escape($u_about)."', 'visit', 'all', '') ";
				$db->query($sql);

				/* user_type */
				$sql = "INSERT INTO option_values (`id_option`, `id_product`, 
					`value`, `where_placed`, `value2`, 
					`value3`) VALUES ('".$uid."', '0', 
					'".$db->escape($u_type)."', 
					'visit', 'type', '') ";
				$db->query($sql);

				/* user_agent*/
				$sql = "INSERT INTO option_values (`id_option`, `id_product`, 
					`value`, `where_placed`, `value2`, 
					`value3`) VALUES ('".$uid."', '0', 
					'".$db->escape($this->user['about']['agent'])."', 
					'visit', 'agent', '') ";
				$db->query($sql);				
			}
			
        }else{
            // найдем по сессии и допишем кол-во просмотренных страниц и какая страница просмотрена
            
            if($page_path != '/favicon.ico/' && !stristr($page_path, $this->uri['tpl'])){
                $sql = "UPDATE ".$db->tables["visit_log"]." SET  
                        `pages_visited` = CONCAT(`pages_visited`,' | ".$db->escape($page_path)."'), 
                        `qty_pages_visited` = `qty_pages_visited` + 1 
                    WHERE 
                        `sess` = '".$db->escape($sess_current)."' 
						AND `referer` <> 'search' 
						AND `ip` = '".$db->escape($ip_addr)."' 
						AND `site_id` = '".$this->vars['id']."' 
				";
                $db->query($sql); 
            }             
        }
        return;    
    }
  
    function special_urls($slug){
        if($slug == 'last_products'){
            $this->page['list_products'] = list_products(0, 'categ', $this, 'last');
			
			if(empty($this->page['list_products'])){
				$this->page['content'] = $this->GetMessage('sitemap', 'empty');
			}
        }elseif($slug == 'new_products'){
            $this->page['list_products'] = list_products(0, 'categ', $this, 'new');
			if(empty($this->page['list_products'])){
				$this->page['content'] = $this->GetMessage('sitemap', 'empty');
			}
        }elseif($slug == 'spec_products'){
            $this->page['list_products'] = list_products(0, 'categ', $this, 'spec');
			if(empty($this->page['list_products'])){
				$this->page['content'] = $this->GetMessage('sitemap', 'empty');
			}
        }elseif($slug == 'last_pubs'){
            $this->page['list_pubs'] = list_pubs(0, $this, 'all');
			if(empty($this->page['list_pubs'])){
				$this->page['content'] = $this->GetMessage('sitemap', 'empty');
			}
        }else{
            return true;
        }

        $this->page['title'] = $this->GetMessage('spec_pages', $slug, 'title');
        $this->page['metatitle'] = $this->GetMessage('spec_pages', $slug, 'metatitle');
        $this->page['intro'] = $this->GetMessage('spec_pages', $slug, 'intro');
        $this->page['page'] = 'category.html';
        return false;
    }
        
		
	function send_headers(){
		global $tpl;
		if(isset($this->page['last_modified'])){
			$last_mod = date('D, d M Y H:i:s', strtotime($this->page['last_modified']));
		}else{
			$last_mod = gmdate('D, d M Y H:i:s');
		}
		
		$ar = array(
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',    
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => '(Unused)',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			429 => 'Too Many Requests', 
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported'
		);

		if(!isset($ar[$this->header_status])) { $this->header_status = 200; }
		$text = $ar[$this->header_status];
		if(empty($this->page['title']) 
			&& $this->header_status != 200
		){
			if(!empty($this->lang['error'][$this->header_status])){
				$this->page['title'] = $this->lang['error'][$this->header_status];
				$tpl->_vars['page']['title'] = $this->lang['error'][$this->header_status];
			}else{
				$this->page['title'] = $text.' ('.$this->header_status.')';
				$tpl->_vars['page']['title'] = $text.' ('.$this->header_status.')';
			}
		}
		
		if (headers_sent()) { return; }
		if($this->header_status != 200) header("HTTP/1.1 ".$this->header_status." $text");
		header('HTTP/1.0 '.$this->header_status.' '.$text);
		
		if($this->user['id'] > 0 || in_array('basket',$this->uri) || in_array('order',$this->uri)){
			$this->nocache_headers();
		}elseif(empty($this->vars['site_time_zone'])){
			header('Last-Modified: '.$last_mod.' Europe/Madrid ');
		}else{
			header('Last-Modified: '.$last_mod.' '.$this->vars['site_time_zone'].' ');
		}           
		return;
	}

	function nocache_headers() {
		header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-cache, must-revalidate, max-age=0');
		header('Pragma: no-cache');
	}

	/* ok */
	function encode_str($str){
		if (!defined('AUTHKEY')) { define('AUTHKEY', 'simpla.es'); }
		return md5(md5($str).'-'.md5(AUTHKEY));
	}
	
	/* ok */
	function create_new_avatar($id)
	{
		global $db;
		$site_vars = $this->vars;
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
			//$new_file_name = "../upload/avatars/".$k."/".md5($id).".jpg";
			$new_file_name = UPLOAD."/avatars/".$k."/".md5($id).".jpg";
			
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
				
				$this->crop_image($_FILES["add_avatar"]['tmp_name'],
					$new_file_name,
					intval($width), intval($height),
					$size); 
			}else{
				$this->crop_image($_FILES["add_avatar"]['tmp_name'],
					$new_file_name,
					intval($size[0]), intval($size[1]),
					$size); 			
			}
		}
				
		return;
	}
	
	
	// ok new version
	function crop_image($filePath, $savePath, $max_width, $max_height, $size=false)
	{
		$method = "width";
		require_once MODULE.'/resize/AcImage.php';
		$image = AcImage::createImage($filePath);
		AcImage::setRewrite(true); //разрешить перезапись при конфликте имён
		
		if($max_height == 0){
		 // Если 0 по высоте, то она не имеет значения и вырезаем только по ширине
			  $image
				->resizeByWidth($max_width)
				->save($savePath);
			  return 'ok';
		}

		if($max_width == 0){
		 // Если 0 по ширине, то она не имеет значения и вырезаем только по высоте
			  $image
				->resizeByHeight($max_height)
				->save($savePath);
			  return 'ok';
		}


		if(!$size) { $size = GetImageSize($filePath); }
		$t_width = $size[0];
		$t_height = $size[1];
		// рассчитаем размеры изображения для ресайза
		$x_ratio = $max_width / $t_width;
		$y_ratio = $max_height / $t_height;
		
		if($t_width/$t_height < $max_width/$max_height){
		  $resize_method = 'width';
		}else{
		  $resize_method = 'height';
		} 
		
		if($resize_method == 'height'){
		  $image
			->resizeByHeight($max_height)
			->cropCenter($max_width.'px', '100%')
			->save($savePath);
		}else{
		  $image
			->resizeByWidth($max_width)
			->cropCenter('100%', $max_height.'px')
			->save($savePath);
		}
			
		return 'ok';              
	}
	

	function redirect($filename, $sec=0) {
		$filename = urldecode($filename);
		if (!headers_sent()){
			header('Location: '.$filename);
			//header('Content-Type: text/html; charset=utf-8');
			//header('Refresh: '.$sec.'; url='.$filename);
		}else {			
			$msec = $sec * 1000;
			echo '<script type="text/javascript">';
			if($msec > 0){
				echo 'window.location.href="'.$filename.'";';
			}else{
				echo 'setTimeout(function(){window.top.location="'.$filename.'"} , '.$msec.');';
			}
			
			echo '</script>';
			echo '<noscript>';
			echo '<meta http-equiv="refresh" content="'.$sec.';url='.$filename.'" />';
			echo '</noscript>';
		}
		exit;
	}
	
	function get_email_event($event){		
		return get_email_event($event);
	}	

	function send_email_event($event, $ar=NULL){
		return send_email_event($event, $ar);
	}
	
	
	function set_coupon($str){
		global $db;
		$str = trim($str);
		if(empty($str)){
			unset($this->page['coupon']);
			unset($_SESSION['coupon']);
		}
		
		$str = mb_strtoupper($str, 'UTF-8');
		$old = $db->cache_queries;
		$db->cache_queries = false;
		$sql = "SELECT * 
					FROM ".$db->tables['coupons']."  
					WHERE UPPER(`title`) LIKE '".$str."' 					
						AND `active` = '1' 						
						AND (date_start < NOW() 
							OR date_start  IS NULL ) 						
						AND (date_stop > NOW() 
							OR date_stop IS NULL )
				";		
		$row = $db->get_row($sql, ARRAY_A);
		$db->cache_queries = $old;
		if(!$row || $db->num_rows == 0){ 
			unset($this->page['coupon']);
			unset($_SESSION['coupon']);
			return; 
		}
		$this->page['coupon'] = $row;
		$_SESSION['coupon'] =  $row['title'];
		return;
	}


	function strip_data($text)
	{
		$quotes = array ("\x27", "\x22", "\x60", "\t", "\n", "\r", "*", "%", "<", ">", "?", "!" );
		$goodquotes = array ("-", "+", "#" );
		$repquotes = array ("\-", "\+", "\#" );
		$text = trim( strip_tags( $text ) );
		$text = str_replace( $quotes, '', $text );
		$text = str_replace( $goodquotes, $repquotes, $text );
		$text = str_replace(" +", " ", $text);
				
		return $text;
	}
		
	/* функция фиксации активности пользователей */
	function register_changes($where, $id, $type, $comment=''){
		/* register db changes
			where / id /
			type - add/update/delete/login/logout
			comment - optional		
		*/
		global $db, $site;
		$who_changed = isset($this->user['id']) ? $this->user['id'] : 0;
		$where_changed = isset($where) ? $where : '';
		$where_id = isset($id) ? $id : '';
		$type_changes = isset($type) ? $type : '';
		$date_insert = date("Y-m-d H:i:s");
		$comment = isset($comment) ? $comment : '';
		
		if(!empty($where_changed)  
			&& !empty($where_id)  && !empty($type_changes)){
			$sql = "INSERT INTO ".$db->tables['changes']." 
				(`where_changed`, `where_id`, `who_changed`, 
				`type_changes`, `date_insert`, `comment`
				) VALUES('".$db->escape($where_changed)."', 
				'".$where_id."', '".$who_changed."', 
				'".$db->escape($type_changes)."', 
				'".$date_insert."', 
				'".$db->escape($comment)."'
				)
			";
			$db->query($sql);
		}
		return;
	}


	/* ok, filesize for well-view datas */
	function well_size($data)
	{
		$size = intval($data);
		if($size > 0 && $size<=1024) $data = $size.' bytes';
		else if($size == 0) $data = '0';
		else if($size<=1024*1024) $data = round($size/(1024),2).' Kb';
		else if($size<=1024*1024*1024) $data = round($size/(1024*1024),2).' Mb';
		else if($size<=1024*1024*1024*1024) $data = round($size/(1024*1024*1024),2).' Gb';
		else if($size<=1024*1024*1024*1024*1024) $data = round($size/(1024*1024*1024*1024),2).' Tb';
		else $data = round($size/(1024*1024*1024*1024*1024),2).' Pb'; // ;-) 
		return $data;
	}	
	
	function start_db_cache(){
		global $db;
		if(isset($this->vars['sys_mysql_cache']) && !$this->vars['sys_mysql_cache']){
			return ;
		}
		$db->cache_queries = true;
	}
	
	function stop_db_cache(){
		global $db;
		$db->cache_queries = false;		
	}
	
	// регистронезависимый поиск в массиве
	function in_arrayi($needle, $haystack) {
        mb_internal_encoding('UTF-8');
        return in_array(mb_strtolower($needle, 'UTF-8'), array_map('mb_strtolower', $haystack));
    }
	
	function check_currency(){
		$this->vars['kurs_rur'] = 1;
		if(!empty($this->vars['sys_autoload_rate'])){
			global $db;
			$sql = "SELECT 
					`type_changes` as usd_rate,
					`date_insert` as usd_date, 
					(SELECT `type_changes` 
					FROM ".$db->tables['changes']." 
					WHERE `where_changed` = 'kurs_euro' 
					ORDER BY date_insert DESC 
					LIMIT 0, 1)  as euro_rate,
					(SELECT  `date_insert`  
					FROM ".$db->tables['changes']." 
					WHERE `where_changed` = 'kurs_euro' 
					ORDER BY date_insert DESC 
					LIMIT 0, 1
					) as euro_date
				FROM ".$db->tables['changes']." 
				WHERE `where_changed` = 'kurs_usd' 
				ORDER BY date_insert DESC 
				LIMIT 0, 1
			";
			$old = $db->cache_queries;
			$db->cache_queries = false;
			$row = $db->get_row($sql, ARRAY_A);
			$db->cache_queries = $old;

			if($row && $db->num_rows > 0 
				&& !empty($row['usd_date'])
			){
				/* найден, проверим дату */
				/* если дата позже 13-00 след.дня
				то закажем новый курс
				*/	
				$t = date_diff(new DateTime(date('Y-m-d 23:59:59')), new DateTime($row['usd_date']))->days;
				if($t > 1 || ($t == 1 && date('H') > 14)){
					$d = $this->load_currency_rate($this->vars['sys_autoload_rate']);
				}else{
					/* используем полученный курс */
					$d = array(
						'USD' => $row['usd_rate'],
						'EUR' => $row['euro_rate']
					);
				}				
			}else{
				$d = $this->load_currency_rate($this->vars['sys_autoload_rate']);
			}
			
			$this->vars['kurs_usd'] = $d['USD'];
			$this->vars['kurs_euro'] = $d['EUR'];
		}
		
		if(isset($this->vars['kurs_rur']) && !defined('SI_RATE_RUR')){
			define('SI_RATE_RUR', $this->vars['kurs_rur']);
		}
		
		if(isset($this->vars['kurs_usd']) && !defined('SI_RATE_USD')){
			define('SI_RATE_USD', $this->vars['kurs_usd']);
		}
		
		if(isset($this->vars['kurs_euro']) && !defined('SI_RATE_EURO')){
			define('SI_RATE_EURO', $this->vars['kurs_euro']);
		}
		return;
	}
	
	/* load currency rate from cbrf */
	function load_currency_rate($d=1){
		global $db;
		$d = $d == 1 ? strtotime("+1 day") : time();
		$date = date("d/m/Y", $d); // формат даты 
		$link = "http://www.cbr.ru/scripts/XML_daily.asp?date_req=$date"; // Ссылка на XML-файл с курсами валют
		
		@$all = simplexml_load_file($link);
		if($all === FALSE){ return; }
		$data = array();

		foreach($all->Valute as $c){
			if($c->CharCode == 'USD' || $c->CharCode == 'EUR'){
				$v = array(
					'NumCode' => $c->NumCode,
					'CharCode' => $c->CharCode,
					'Nominal' => $c->Nominal,
					'Name' => $c->Name,
					'Value' => $c->Value,
					'currency' => $c->NumCode,
					'kurs' => $c->Value
				);
				$k = str_replace(',','.',strval($c->Value));
				$data[strval($c->CharCode)] = $k;
				$str = strval($c->CharCode) == 'USD' ? 'kurs_usd' : 'kurs_euro';
				$this->register_changes($str, $this->id, $k);
			}
		}
		
		return $data;
	}

	function user_agent()
	{
		$f = MODULE.'/userdetect/userdetect.php';
		if(file_exists($f)){
			include_once($f);
		}else{ return; }
		$about = new DetectGuest;
		$pages_visited = !empty($_SESSION['qty_visited']) 
			? $_SESSION['qty_visited'] : 1;
		
		$this->user['about'] = array(
			'is_browser' => $about->is_browser,
			'is_mobile' => $about->is_mobile,
			'is_robot' => $about->is_robot,
			'ip' => $about->ip,
			'version' => $about->version,
			'browser' => $about->browser,
			'browser_full_name' => $about->browser_full_name,
			'operating_system' => $about->operating_system,
			'os_version' => $about->os_version,
			'robot' => $about->robot,
			'mobile' => $about->mobile,
			'agent' => $about->agent,
			'pages_visited' => $pages_visited
		);
		
		return;
	}
	
	
}


?>