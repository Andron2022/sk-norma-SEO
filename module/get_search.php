<?php
	if (!class_exists('Site')) { return; }

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
    *
    ***********************/ 

    class Search extends Site {

        function __construct()
        {
        }
                
        static public function get_results($site)
        {
			global $db;
            $tpl_page = 'pages/search.html';
            $ar = GetEmptyPageArray();
            $site->uri['page'] = $tpl_page;
            $ar['page'] = $tpl_page;
            $ar['title'] = $site->GetMessage('search','title');
            $ar['metatitle'] = $site->GetMessage('search','metatitle');
            $ar['content'] = '';
			
			$site->uri['params']['q'] = isset($site->uri['params']['q']) ? trim($site->uri['params']['q']) : '';
			
            if(empty($site->uri['params']['q'])){
                return $ar;
            }    
			
            $ar['list_categs'] = list_categs(0, $site, array('search'=>'search')); 
            $ar['list_pubs'] = list_pubs(0, $site, 'search');      
            $ar['list_products'] = list_products(0, 'product', $site, 'search');
 			
            if(empty($site->uri['params']['where']) && !empty($site->uri['params']['q'])){
                $url = $site->uri['site'].$site->uri['path'].'?q='.$site->uri['params']['q'];
                if(count($ar['list_pubs']) == 0 && count($ar['list_categs']) == 0 && count($ar['list_products']) > 0){                    
					$url = $url.'&where=products';
					return $site->redirect($url);
                }

                if(count($ar['list_pubs']) > 0 && count($ar['list_categs']) == 0 && count($ar['list_products']) == 0){
					$url = $url.'&where=pubs';
					return $site->redirect($url);
                    //header("Location: ");
                    //exit;
                }

                if(count($ar['list_pubs']) == 0 && count($ar['list_categs']) > 0 && count($ar['list_products']) == 0){                    
					$url = $url.'&where=categs';
					return $site->redirect($url);
                }
            
            }
            
			/* запишем в лог, что искали */
			$ref_host = 'search';
			$ip_addr = !empty($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '';  
			$sess_current = session_id();
			$ref_query = mb_substr($site->uri['params']['q'], 0, 220, 'UTF-8');
            $partner_key = !empty($site->user['id']) ? $site->user['id'] : 0;
			
			/* дубль записывать не будем */
			$sql = "SELECT COUNT(*) FROM ".$db->tables["visit_log"]." 
				WHERE `ip` = '".$db->escape($ip_addr)."'
				AND `sess` = '".$db->escape($sess_current)."'
				AND `referer` = '".$db->escape($ref_host)."'
				AND `referer_query` = '".$db->escape($ref_query)."'				
			";
			$q = $db->get_var($sql);
			
			if(empty($q)){
				$sql = "INSERT INTO ".$db->tables["visit_log"]." (`time`, `ip`, 
					`sess`, `referer`, `referer_query`, `page`, `partner_key`, 
					`pages_visited`, `qty_pages_visited`, `site_id`) 
					VALUES('".date('Y-m-d H:i:s')."', '".$db->escape($ip_addr)."', 
					'".$db->escape($sess_current)."', 
					'".$db->escape($ref_host)."', '".$db->escape($ref_query)."',                 
					'', '".$db->escape($partner_key)."', 
					'', '1', '".$site->vars['id']."') ";
				$db->query($sql);				
			}
				
            return $ar;           
        }
        
    }

?>