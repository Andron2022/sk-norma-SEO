<?php
/*
  in work
  last updated 03.11.2016
*/
if(!defined('SIMPLA_ADMIN')){ die(); }
	global $admin_vars, $db, $tpl;
	$id = isset($admin_vars['uri']['id']) ? $admin_vars['uri']['id'] : '';
	$do = isset($admin_vars['uri']['do']) ? $admin_vars['uri']['do'] : '';
	$q = isset($admin_vars['uri']['q']) ? $admin_vars['uri']['q'] : '';
	$to = isset($admin_vars['uri']['to']) ? $admin_vars['uri']['to'] : '';
	$from = isset($admin_vars['uri']['from']) ? $admin_vars['uri']['from'] : '';
	
	
	$f = 'inc/idna_convert/idna_convert.class.php';
	require_once($f);
	$idn_version = 
		isset($_REQUEST['idn_version']) && $_REQUEST['idn_version'] == 2003 
		? 2003 : 2008;
	$IDN = new idna_convert(array('idn_version' => $idn_version));
	
	
	if(is_array($from)){
		$from = isset($from['Day']) && isset($from['Month']) && isset($from['Year'])
			? $from['Year'].'-'.$from['Month'].'-'.$from['Day']
			: '';
	}
	
	if(is_array($to)){
		$to = isset($to['Day']) && isset($to['Month']) && isset($to['Year'])
			? $to['Year'].'-'.$to['Month'].'-'.$to['Day']
			: '';
	}
	
	$dates = array(
		'yesterday' => date("Y-m-d", strtotime("-1 day")),
		'week' => date("Y-m-d", strtotime("-1 WEEK")),
		'month' => date("Y-m-d", strtotime("-1 MONTH")),
		'from' => $from,
		'to' => $to
	);
	
	
	$sql = "SELECT DISTINCT v.referer as refs, 
			(SELECT COUNT(*) FROM ".$db->tables['visit_log']." 
			WHERE `referer` LIKE v.referer) as qty 
		FROM ".$db->tables['visit_log']." v 
		ORDER BY refs 
	";
	$sql = "SELECT DISTINCT v.referer as refs
		FROM ".$db->tables['visit_log']." v 
		WHERE `referer` <> 'search' AND `referer` <> ''
		ORDER BY refs 
	";
	$refs = $db->get_results($sql, ARRAY_A);
	//$db->debug(); exit;
	//require_once('class.idn.php');
	if($refs && $db->num_rows > 0){
		foreach($refs as $k=>$v){
			if(!empty($v['refs'])){
				if(function_exists('idn_to_utf8')){
					$refs[$k] = idn_to_utf8($v['refs']); 
				}else{
					$refs[$k] = $IDN->decode($v['refs']);					
				}	
			}else{
				//$refs[$k] = ''; 
			}
		}
	}
	sort($refs);
	$dates['refs'] = $refs;
	
	$sql = "SELECT DISTINCT partner_key as partners
		FROM ".$db->tables['visit_log']." 
		WHERE partner_key <> '' AND `referer` <> 'search'
		ORDER BY partner_key 
	";
	$dates['partners'] = $db->get_results($sql, ARRAY_A);

	$back_url = '';
	if(!empty($_SERVER["REQUEST_URI"])){ $back_url = urlencode($_SERVER["REQUEST_URI"]); }
	$dates['back_url'] = $back_url;
		
	$sql = "SELECT DISTINCT v.site_id as id, s.site_url as url, 
			(SELECT MIN(`time`) as min_time FROM ".$db->tables['visit_log']."  
			 LIMIT 0,1) as min_time 
		FROM ".$db->tables['visit_log']." v 
		LEFT JOIN ".$db->tables['site_info']." s ON (v.site_id = s.id) 
		ORDER BY s.site_url
	";
	$sites = $db->get_results($sql, ARRAY_A);

	$s = $sites;
	$foo = array_shift($s);
	$dates['start_year'] = date('Y', strtotime($foo['min_time']));
	
	if(empty($id) && empty($do) && empty($from) && empty($to)){
		/* index stat page */
		
		$sql_today = "SELECT COUNT(*) as qty, SUM(qty_pages_visited) as total  
			FROM ".$db->tables['visit_log']."  
			WHERE `time` >= CURDATE() AND `referer` <> 'search' ";
			
		$sql_yesterday = "SELECT COUNT(*) as qty, SUM(qty_pages_visited) as total  
			FROM ".$db->tables['visit_log']." 
			WHERE `time` >= (CURDATE()-1) AND `time` < CURDATE()  AND `referer` <> 'search' ";
			
		$sql_week = "SELECT COUNT(*) as qty, SUM(qty_pages_visited) as total  
			FROM ".$db->tables['visit_log']." 
			WHERE `time` > NOW() - INTERVAL 7 DAY  AND `referer` <> 'search' ";
		$sql_month = "SELECT COUNT(*) as qty, SUM(qty_pages_visited) as total 
			FROM ".$db->tables['visit_log']." 
			WHERE `time` > NOW() - INTERVAL 30 DAY  AND `referer` <> 'search' ";		
		
		$today = $db->get_row($sql_today, ARRAY_A);
		$yesterday = $db->get_row($sql_yesterday, ARRAY_A);
		$week = $db->get_row($sql_week, ARRAY_A);
		$month = $db->get_row($sql_month, ARRAY_A);
				
		$stat = compact('today', 'yesterday', 'week', 'month', 'dates', 'sites');
		
		$tpl->assign("stat", $stat);
		$str_content = $tpl->display("info/stat_index.html");
		
	}elseif($id > 0){
		/* stat for id and similar sessions */
		$info = $db->get_row("SELECT v.*, s.site_url, 
				(SELECT COUNT(*) FROM ".$db->tables['connections']." 
				WHERE id2 = v.id AND name2 = 'visit_log' 
				AND name1 = 'order') as qty_orders, 
				(SELECT COUNT(*) FROM ".$db->tables['connections']." 
				WHERE id2 = v.id AND name2 = 'visit_log' 
				AND name1 = 'fb') as qty_fb, 
				(SELECT COUNT(*) FROM ".$db->tables['counter']." 
				WHERE for_page = 'pub' 
				AND `sess` = v.sess) as qty_pubs, 
				(SELECT COUNT(*) FROM ".$db->tables['counter']." 
				WHERE for_page = 'product' 
				AND `sess` = v.sess) as qty_products 
		
			FROM ".$db->tables['visit_log']." v  
			LEFT JOIN ".$db->tables['site_info']." s ON (v.site_id = s.id)
			WHERE v.id = '".$id."' ", ARRAY_A);

		if(!empty($info['referer'])){			
			$info['referer'] = $IDN->encode($info['referer']);
		}
		
		if(!empty($info['pages_visited'])){
			$info['pages_visited'] = explode(' | ', $info['pages_visited']);
		}	
		
		if(!empty($info['ip'])){
			$sql = "SELECT COUNT(*) FROM ".$db->tables['visit_log']." 
				WHERE ip = '".$info['ip']."'
			";
			$info['ip_visited'] = $db->get_var($sql);
		}	
		

		$info['site_url_short'] = str_replace('http://www.', '', $info['site_url']);
		$info['site_url_short'] = str_replace('http://', '', $info['site_url_short']);
		$info['site_url_short'] = str_replace('https://', '', $info['site_url_short']);

		if(!empty($info['qty_orders'])){
			$sql = "SELECT c.*, o.id as o_id, 
					o.order_id as o_order_id, 
					o.created as o_created
				FROM ".$db->tables['connections']." c, 
					".$db->tables['orders']." o 
				WHERE c.id2 = '".$id."' AND c.name2 = 'visit_log' 
				AND c.name1 = 'order' 
				AND c.id1 = o.id  
			";
			
			$info['list_orders'] = $db->get_results($sql, ARRAY_A);
		}
		
		if($info['referer'] == 'search'){
			$sql = "SELECT *
				FROM ".$db->tables['visit_log']." 
				WHERE sess = '".$info['sess']."' 
				AND ip = '".$info['ip']."' 
				AND `referer` <> 'search'
			";
			
			$info['original'] = $db->get_row($sql, ARRAY_A);
		}else{
			$sql = "SELECT *
				FROM ".$db->tables['visit_log']." 
				WHERE sess = '".$info['sess']."' 
				AND ip = '".$info['ip']."' 
				AND `referer` = 'search'
			";
			
			$info['list_search'] = $db->get_results($sql, ARRAY_A);
		}
		
		if(!empty($info['qty_fb'])){
			$sql = "SELECT c.*, f.id as f_id, 
					f.ticket as f_ticket, 
					f.sent as f_sent
				FROM ".$db->tables['connections']." c, 
					".$db->tables['feedback']." f 
				WHERE c.id2 = '".$id."' AND c.name2 = 'visit_log' 
				AND c.name1 = 'fb' 
				AND c.id1 = f.id  
			";
			$info['list_fb'] = $db->get_results($sql, ARRAY_A);			
		}
		
		if(!empty($info['qty_pubs'])){
			$sql = "SELECT c.*, p.id as p_id, 
					p.`name` as p_title
				FROM ".$db->tables['counter']." c, 
					".$db->tables['publications']." p 
				WHERE c.sess = '".$info['sess']."'
				AND c.for_page = 'pub'  
				AND c.id_page = p.id 
				ORDER BY p.`name` 
			";
			
			$info['list_pubs'] = $db->get_results($sql, ARRAY_A);
		}
		
		if(!empty($info['qty_products'])){
			$sql = "SELECT c.*, p.id as p_id, 
					p.`name` as p_title
				FROM ".$db->tables['counter']." c, 
					".$db->tables['products']." p 
				WHERE c.sess = '".$info['sess']."'
				AND c.for_page = 'product'  
				AND c.id_page = p.id 
				ORDER BY c.`time`, p.`name` 
			";
			
			$info['list_products'] = $db->get_results($sql, ARRAY_A);
		}
		
		/*
		echo '<pre>';
		print_r($info);
		exit;
		*/
		
		$stat = compact('dates', 'sites', 'info');
		
		$tpl->assign("stat", $stat);
		$str_content = $tpl->display("info/stat_view.html");
		
	}else{
		/* выведем логи */
		/* if $do = search => поиск */

		$sql_num = "SELECT COUNT(*)  
			FROM ".$db->tables['visit_log']." 
			WHERE id > '0' 
		";
		
		$sql = "SELECT v.*, s.site_url as site_url, 
				(SELECT COUNT(*) FROM ".$db->tables['connections']." 
					WHERE v.id = id2 AND name2 = 'visit_log' 
					AND name1 = 'order') as qty_orders, 
				(SELECT COUNT(*) FROM ".$db->tables['connections']." 
					WHERE v.id = id2 AND name2 = 'visit_log' 
					AND name1 = 'fb') as qty_fb
			
		
			FROM ".$db->tables['visit_log']." v
			LEFT JOIN ".$db->tables['site_info']." s ON (v.site_id = s.id) 				
			WHERE v.id > '0' 
		";
		
		if($from != '0-0-0' && !empty($from)){
			
			$sql_num .= " AND `time` > '".$from." 00:00:00' 
			";
			$sql .= " AND v.`time` > '".$from." 00:00:00' 
			";
			
		}
		
		if($to != '0-0-0' && !empty($to)){			
			$sql_num .= " AND `time` < '".$to." 23:59:59' 
			";
			$sql .= " AND v.`time` < '".$to." 23:59:59' 
			";			
		}

		if(!empty($_GET['site_id'])){
			$sql_num .= " AND `site_id` = '".intval($_GET['site_id'])."' ";
			$sql .= " AND v.`site_id` = '".intval($_GET['site_id'])."' ";
		}

		if(!empty($_GET['ip'])){
			
			$sql_num .= " AND `ip` = '".$db->escape($_GET['ip'])."' ";
			$sql .= " AND v.`ip` = '".$db->escape($_GET['ip'])."' ";
		}

		if(!empty($_GET['ref'])){
			
			$encoded = $IDN->encode($_GET['ref']);
			$sql_num .= " AND `referer` = '".$db->escape($encoded)."' ";
			$sql .= " AND v.`referer` = '".$db->escape($encoded)."' ";
		}

		if(!empty($_GET['partners'])){
			
			$sql_num .= " AND `partner_key` = '".$db->escape($_GET['partners'])."' ";
			$sql .= " AND v.`partner_key` = '".$db->escape($_GET['partners'])."' ";
		}
		
		if($do == 'search'){
			$sql_num .= " AND `referer` = 'search' ";
			$sql .= " AND v.`referer` = 'search' ";
		}else{
			$sql_num .= " AND `referer` <> 'search' ";
			$sql .= " AND v.`referer` <> 'search' ";
		}

		$dates['all_results'] = $db->get_var($sql_num);
		
		// PAGE LIMITS
		$on_Page = 50;
		if(isset($_GET["page"])){ $page = $_GET["page"]; } else { $page = 0; }
		$limit_Start = $page*$on_Page; // for pages generation
		$tpl->assign("pages",_pages($dates['all_results'], $page, $on_Page,true));
		
		$sql = $sql." LIMIT ".$limit_Start.", ".$on_Page;		
		$list = $db->get_results($sql, ARRAY_A);
		
		$stat = compact('list', 'dates', 'sites');
		
		$tpl->assign("stat", $stat);
		$str_content = $tpl->display("info/stat_list.html");
		
	}
	
?>