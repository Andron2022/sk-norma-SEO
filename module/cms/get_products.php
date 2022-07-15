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
                
        static public function get_products($site)
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
				$ar['page'] = 'pages/products/list.html';
			}
			
			$ar['title'] = $site->GetMessage('cms', 'products', 'title');
			$ar['metatitle'] = $site->GetMessage('cms', 'products', 'title');
			
			$ar['breadcrumbs'][] = array(
				'title' => $site->GetMessage('cms', 'products', 'title'),
				'link' => $site->vars['site_url'].'/cms/products/'
			);
			
			$id = isset($_GET['id']) ? intval($_GET['id']) : NULL;
			$ar['id'] = $id;
			
			if($id === NULL){
				$c = !empty($site->uri['params']['c']) ? $site->uri['params']['c'] : '';
				if($c == 'set'){
					
				}elseif($c == 'export'){
					$sql = "SELECT * FROM ".$db->tables['products']." ";
					if(!empty($site->uri['params']['cid'])){
						$sql .= " WHERE id_parent = '".intval($site->uri['params']['cid'])."' ";
					}
					$fields = array();
					cms_to_csv($sql, $db->tables['products'], $fields);
				}elseif($c == 'import'){
					return Cms::get_import($site, $ar);
				}
				
				$ar['settings_menu'] = Cms::get_settings_menu($site,$ar);
				return Cms::get_list($site, $ar);
			}elseif($id > 0){
				return Cms::view_product($site, $ar);
			}else{
				return Cms::add_product($site, $ar);
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
				'link' => '/cms/products/?c=set'.$sub_link,
				'title' => 'Настройка столбцов'
			);
			$arr[] = array(
				'link' => '/cms/products/?c=export'.$sub_link,
				'title' => 'Скачать в CSV',
				'new' => 1
			);
			$arr[] = array(
				'link' => '/cms/products/?c=import'.$sub_link,
				'title' => 'Импорт из CSV'
			);
			return $arr;			
		}
		
		
		function get_list($site, $ar){
			/* выведем список разделов */
			global $db, $tpl;
			
			/* filter */
			$arr1['products'] = 1;
			$arr1['all'] = 1;
			$p = list_categs(0, $site, $arr1);
			$ar['filter'] = $p;
			
			if(!empty($site->uri['params']['cid'])){
				$arr = array();
				$arr['cid'] = $site->uri['params']['cid'];
				
				$p = list_categs($arr['cid'], $site);
				if(!empty($p[0])){						
					$p = $p[0];
					$ar['title'] = $site->GetMessage('cms', 'products', 'title').': '.$p['title'];
					$ar['metatitle'] = $site->GetMessage('cms', 'products', 'title').': '.$p['title'];
					$ar['selected'] = $p;
				}
					
				$b = $site->get_breadcrumbs($arr['cid']);
				if(!empty($b)){
					foreach($b as $b1){
						if($b1['id'] != $arr['cid']){
							$ar['breadcrumbs'][] = array(
								'title' => $b1['title'],
								'link' => $site->vars['site_url'].'/cms/products/?cid='.$b1['id']
							);
						}
					}
				}
				
				$cat = array(
					'id' => $arr['cid'],
					'debug' => 1
				);
				$ar['list'] = list_products($cat, 'product', $site, 'all');
				if(!empty($ar['list']['all'])){
					$ar['all_results'] = $ar['list']['all'];
					$site->page['all_results'] = $ar['all_results'];
				}else{
					$ar['all_results'] = 0;
					$site->page['all_results'] = 0;
				}
				
			}elseif(!empty($site->uri['params']['q'])){
				$arr['search'] = $site->uri['params']['q'];
				$cat = array(
					'id' => 0,
					'debug' => 1
				);
				$ar['list'] = list_products($cat, 'product', $site, 'search');
			}else{
				$cat = array(
					'id' => 0,
					'debug' => 1
				);
				$ar['list'] = list_products($cat, 'product', $site, 'all');
			}
			
			if(!empty($site->page['all_results'])){
				$link = $site->vars['site_url'].'/cms/products/';
				$onpage = ONPAGE;
				$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
				if(isset($site->uri['params']['cid'])){
					$link .= "?cid=".intval($site->uri['params']['cid']);
					$start_link = '&';
				}elseif(isset($site->uri['params']['q'])){
					$link .= "?q=".$site->uri['params']['q'];
					$start_link = '&';				
				}else{
					$start_link = '?';
				}
				
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