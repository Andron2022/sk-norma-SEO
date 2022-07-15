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
                
        static public function get_categs($site)
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
				$ar['page'] = 'pages/categs/list.html';
			}
			
			$ar['title'] = $site->GetMessage('cms', 'categs', 'title');
			$ar['metatitle'] = $site->GetMessage('cms', 'categs', 'title');
			
			$link = !empty($site->uri['params']['c']) 
				? $site->vars['site_url'].'/cms/categs/?c='.$site->uri['params']['c']
				: $site->vars['site_url'].'/cms/categs/';
			$ar['breadcrumbs'][] = array(
				'title' => $site->GetMessage('cms', 'categs', 'title'),
				'link' => $link
			);
			
			$id = isset($_GET['id']) ? intval($_GET['id']) : NULL;
			$ar['id'] = $id;
			
			if($id === NULL){
				$c = !empty($site->uri['params']['c']) ? $site->uri['params']['c'] : '';
				if($c == 'set'){
					
				}elseif($c == 'export'){
					$sql = "SELECT * FROM ".$db->tables['categs']." ";
					if(!empty($site->uri['params']['cid'])){
						$sql .= " WHERE id_parent = '".intval($site->uri['params']['cid'])."' ";
					}
					$fields = array('id','title', 'inn');
					cms_to_csv($sql, $db->tables['categs'], $fields);
				}elseif($c == 'import'){
					return Cms::get_import($site, $ar);
				}
				
				$ar['settings_menu'] = Cms::get_settings_menu($site,$ar);
				return Cms::get_list($site, $ar);
			}elseif($id > 0){
				return Cms::view_categ($site, $ar);
			}else{
				return Cms::add_categ($site, $ar);
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
			
			$arr[] = array(
				'link' => '/cms/categs/?c=set'.$sub_link,
				'title' => 'Настройка столбцов'
			);
			$arr[] = array(
				'link' => '/cms/categs/?c=export'.$sub_link,
				'title' => 'Скачать в CSV',
				'new' => 1
			);
			$arr[] = array(
				'link' => '/cms/categs/?c=import'.$sub_link,
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
			$p = list_categs(0, $site, $arr1);
			$ar['filter'] = $p;
			
			if(!empty($site->uri['params']['c'])  
				&& $site->uri['params']['c'] == 'list'){
				$arr = array();
				if(isset($site->uri['params']['cid'])){
					$arr['cid'] = $site->uri['params']['cid'];
					$p = list_categs($arr['cid'], $site);
					if(!empty($p[0])){						
						$p = $p[0];
						$ar['title'] = $site->GetMessage('cms', 'categs', 'view').': '.$p['title'];
						$ar['metatitle'] = $site->GetMessage('cms', 'categs', 'view').': '.$p['title'];
						$ar['selected'] = $p;
					}
					
					$b = $site->get_breadcrumbs($arr['cid']);
					if(!empty($b)){
						foreach($b as $b1){
							if($b1['id'] != $arr['cid']){
								$ar['breadcrumbs'][] = array(
									'title' => $b1['title'],
									'link' => $site->vars['site_url'].'/cms/categs/?c=list&cid='.$b1['id']
								);
							}
						}
					}
					//$site->print_r($ar['breadcrumbs']);
					//exit;
					
					
				}
				
				$ar['list'] = list_categs(0, $site, $arr);
			}elseif(!empty($site->uri['params']['q'])){
				$arr['search'] = $site->uri['params']['q'];
				$ar['list'] = list_categs(0, $site, $arr);
			}else{
				$ar['list'] = get_categs($db->tables['categs'], 0, 0, 0, $site, 0);
				$ar['page'] = 'pages/categs/tree.html';
				$ar['scripts'] = array();
				$ar['scripts']['jstree'] = '1';
			}
			
			if(!empty($site->page['all_results'])){
				$link = $site->vars['site_url'].'/cms/categs/?c=list';
				$onpage = ONPAGE;
				$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
				$start_link = '&';
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
        
    }

?>