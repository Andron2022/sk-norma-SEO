<?php

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
    *
    ***********************/ 
       
    $s = new Cms;

    class Cms extends Site {

        function __construct()
        {
        }
                
        static public function get_comments($site)
        {
			global $db;
			include_once('cms_fns.php');
			$site->tpl = "/tpl/cms/";
			if(empty($site->user['id'])){
				$ar['need_auth'] = 1;
				$ar['page'] = 'pages/403.html';
			}elseif(empty($site->user['prava']['orders'])){
				$ar['page'] = 'pages/403.html';
			}else{
				//$ar['page'] = 'cms/index.html';
				$ar['page'] = 'pages/comments/list.html';
			}
			
			$ar['title'] = $site->GetMessage('cms', 'comments', 'title');
			$ar['metatitle'] = $site->GetMessage('cms', 'comments', 'title');
			
			$link = $site->vars['site_url'].'/cms/comments/';
			if(!empty($site->uri['params']['cid']) && !empty($site->uri['params']['where'])){
				//$link .=  '?cid='.intval($site->uri['params']['cid']).'&where='.$site->uri['params']['where'];
			}
			
			$ar['breadcrumbs'][] = array(
				'title' => $site->GetMessage('cms', 'comments', 'title'),
				'link' => $link
			);
			
			$id = isset($_GET['id']) ? intval($_GET['id']) : NULL;
			$ar['id'] = $id;
			
			if($id === NULL){
				$c = !empty($site->uri['params']['c']) ? $site->uri['params']['c'] : '';
				if($c == 'set'){
					
				}elseif($c == 'export'){
					$fields = array('id', 'record_type', 
						'record_id', 'userid', 'comment_text', 
						'ddate', 'unreg_email', 'active', 
						'ext_h1', 'ext_desc');
										
					$sql = "SELECT * 
							FROM ".$db->tables['comments']."  
					";					
			
					if(!empty($site->uri['params']['cid']) 
						&& !empty($site->uri['params']['where']) 
					){
						$sql .= " WHERE 
							record_id = '".intval($site->uri['params']['cid'])."' 
							AND record_type = '".trim($site->uri['params']['where'])."' 
						";
					}elseif(!empty($site->uri['params']['q']) ){
						$q = $db->escape(mb_strtolower($site->uri['params']['q'], 'UTF-8'));
						$sql .= " WHERE (
							LOWER(comment_text) LIKE '%".$q."%' 
							OR LOWER(ip_address) LIKE '%".$q."%' 
							OR LOWER(unreg_email) LIKE '%".$q."%' 
							OR LOWER(ext_h1) LIKE '%".$q."%' 
							)
						";
					}
					cms_to_csv($sql, $db->tables['comments'], $fields);
				}elseif($c == 'import'){
					return Cms::get_import($site, $ar);
				}
				
				$ar['settings_menu'] = Cms::get_settings_menu($site,$ar);
				return Cms::get_list($site, $ar);
			}elseif($id > 0){
				return Cms::view_comment($site, $ar);
			}else{
				return Cms::add_comment($site, $ar);
			}
			exit;
        }
		
		function get_settings_menu($site,$ar){
			/* зададим меню для настроек */
			/* настройка столбцов для списка */
			/* выгрузка всех строк в формате CSV */
			/* загрузка в формате CSV */
			$arr = array();
			$sub_link = "";
			if(!empty($site->uri['params']['cid'])){
				$sub_link = "&cid=".intval($site->uri['params']['cid']);
			}
			
			if(!empty($site->uri['params']['where'])){
				$sub_link .= "&where=".$site->uri['params']['where'];
			}
			
			$arr[] = array(
				'link' => '/cms/comments/?c=set'.$sub_link,
				'title' => 'Настройка столбцов'
			);
			$arr[] = array(
				'link' => '/cms/comments/?c=export'.$sub_link,
				'title' => 'Скачать в CSV',
				'new' => 1
			);
			$arr[] = array(
				'link' => '/cms/comments/?c=import'.$sub_link,
				'title' => 'Импорт из CSV'
			);
			return $arr;			
		}
		
		function get_list($site, $ar){
			/* выведем список разделов */
			global $db, $tpl;
			/* filter */
			$arr1['categs'] = 1;
			$arr1['all'] = 1;
			//$p = list_comments(0, '', $site, 0, 0);
			$p = list_filter_comments();
			$ar['filter'] = $p;
			//$site->print_r($p);
			//exit;
			
			$cid = !empty($site->uri['params']['cid']) ? intval($site->uri['params']['cid']) : 0;
			$record_type = !empty($site->uri['params']['where']) ? trim($site->uri['params']['where']) : '';
			
			if(!empty($cid) && !empty($record_type)){
				
				if($record_type == 'categ'){
					$arr = array();
					$arr['cid'] = $cid;
					$arr['all'] = 1;
					$p = list_categs($arr['cid'], $site,$arr);	
					$p = isset($p[0]) ? $p[0] : array();
				}elseif($record_type == 'pub'){
					$arr = array();
					$arr['pid'] = $cid;
					$arr['all'] = 1;
					$p = list_pubs($arr, $site, 'all');
					$p = isset($p['list'][0]) ? $p['list'][0] : array();
					$categ_id = !empty($p['categ_id']) ? $p['categ_id'] : 0;
				}else{
					$arr = array();
					$arr['id'] = 0;
					$arr['pid'] = $cid;
					$arr['debug'] = 1;
					//$p = list_categs($arr['cid'], $site,$arr);	
					$p = list_products($arr, 'product', $site, 'all');
					$p = isset($p['list'][0]) ? $p['list'][0] : array();
					$categ_id = !empty($p['id_categ']) ? $p['id_categ'] : 0;
				}
				
/*				
$site->print_r($p);
exit;				
*/

				if(!empty($p)){						
					
					$ar['title'] = $site->GetMessage('cms', 'comments', 'title').': '.$p['title'];
					$ar['metatitle'] = $site->GetMessage('cms', 'comments', 'title').': '.$p['title'];
					$ar['selected'] = $p;
				}


				$categ_id = isset($categ_id) ? $categ_id : $cid;
				$b = $site->get_breadcrumbs($categ_id);
				if(!empty($b)){
					foreach($b as $b1){
						if($b1['id'] != $categ_id || $record_type != 'categ'){
							$ar['breadcrumbs'][] = array(
								'title' => $b1['title'],
								'link' => $site->vars['site_url'].'/cms/categ/?cid='.$b1['id']
							);
						}
					}
				}
			}
			
			
			if(!empty($site->uri['params']['q'])){
				$ar['list'] = list_comments($cid, 'search', $site, 0, 0);
			}else{
				$ar['list'] = list_comments($cid, $record_type, $site, 0, 0);
			}
			
			
			if(!empty($site->page['all_results'])){
				$link = $site->vars['site_url'].'/cms/comments/';
				$onpage = ONPAGE;
				$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
				$start_link = '?';
				$ar['pages'] = array(
					'link' => $link,
					'start_link' => $start_link,
					'pages' => ceil($site->page['all_results']/$onpage),
					'current' => $page
				);
			}
			
			if(isset($site->page['all_results'])){
				$ar['all_results'] = $site->page['all_results'];
			}
			
			
			return $ar;
		}
		
		
		function get_import($site, $ar){
			global $db;
			$ar['page'] = 'pages/comments/import.html';
			$ar['changes'] = 'comment';
			$ar['redirect'] = $site->uri['requested_full'];
			if(isset($_FILES['csv']) || isset($_POST['confirm'])){
				$ar = cms_from_csv($site, $ar, $db->tables['comments']);
			}
			return $ar;
		}
		
        
    }

?>