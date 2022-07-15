<?php
	if (!class_exists('Site')) { return; }

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
    *
    ***********************/ 

    class Compare extends Site {

        function __construct()
        {
        }
                
        static public function get_compare($site)
        {
			global $db;
            $tpl_page = 'pages/compare.html';
            $ar = array();
            $site->uri['page'] = $tpl_page;
            $ar['page'] = $tpl_page;
            $ar['content'] = '';
			$ar['skip_sidebar'] = '1';
			$ar['in_row'] = 6; // кол-во публикаций в строку для сравнения

			$str = '';
			if(!empty($_GET['table'])){				
				$ar['title'] = $site->GetMessage('compare','title_table');
				$ar['metatitle'] = $site->GetMessage('compare','metatitle_table');
				$qty = 50;
				$t = intval($_GET['table']);
				$row = $db->get_row("SELECT * 
					FROM ".$db->tables['option_groups']." 
					WHERE id = '".$t."' ");
				//$ar['content'] = $row->title.'<br>';
				if(!empty($row->title) && empty($row->hide_title)) { 
					$ar['title'] = $row->title; 
				}elseif(!empty($row->opt_title)){
					$ar['title'] = $row->opt_title; 
				}
				//$ar['metatitle'] = $ar['metatitle'];
				//$str = $ar['title'];
				$ar['content'] = !empty($row->description) ? $row->description : $str;
			}else{
				$ar['title'] = $site->GetMessage('compare','title');
				$ar['metatitle'] = $site->GetMessage('compare','metatitle');				
				$qty = 8;
				$t = 0; 
			}

			// $t - номер группы опций, которые оставить
			// 0 - собрать все
			$site->get_group_options = $t;
			
			$categ = !empty($_GET['c']) ? intval($_GET['c']) : '';
			$categ_ar = array('id' => $categ, 'sortby' => 'name');

			if(!empty($categ)){
				
				$categ_ar1 = list_categs($categ, $site);
				if(!empty($categ_ar1[0])){
					$categ_ar = $categ_ar1[0];
					$ar['categ'] = $categ_ar;
				}else{
					$site->page['skip_sidebar'] = '1';
					return $site->error_page(404);
				}
		
				/* создаем массив данных товаров и публикаций */
				$ar['list_pubs'] = list_pubs($categ_ar, $site, 'compare', $qty);

				$ar['list_products'] = list_products($categ, 'product', $site, 'compare', $qty);
				
				if(!isset($_GET['print'])){
					$ar['select_pubs'] = list_pubs($categ_ar, $site, 'all', 500);
					$ar['select_products'] = list_products($categ, 'product', $site, 'all', 500);
				}				
				
				if(empty($ar['list_pubs']['list']) && empty($ar['list_products']['list'])){
					return $ar;
				}elseif(!empty($ar['list_pubs']['list'])){
					$_arr = $ar['list_pubs']['list'];
				}elseif(!empty($ar['list_products']['list'])){
					$_arr = $ar['list_products']['list'];
				}else{
					return $ar;
				}
				$arr = array_chunk($_arr, $ar['in_row'], true);
				
				$ar['list_in_row'] = $arr;
				
				function mySort($f1,$f2){
					if(count($f1) < count($f2)) return 1;
					elseif(count($f1) > count($f2)) return -1;
					else return 0;
				}
				
				// uasort – сортирует массив, используя пользовательскую функцию mySort
				if(!empty($ar['list_pubs']['options'])){
					uasort($ar['list_pubs']['options'],"mySort");
				}
				
				// uasort – сортирует массив, используя пользовательскую функцию mySort
				if(!empty($ar['list_products']['options'])){
					uasort($ar['list_products']['options'],"mySort");
				}
			}
			$full = $site->uri['requested_full'];
			$site->uri['requested_no_skip'] = $full;
			
			if(!empty($site->uri['params']) && !empty($site->uri['params']['skip'])){
				$no_skip = $site->uri['params'];
				unset($no_skip['skip']);
				$no_skip = $site->uri['site'].$site->uri['path'].'?'.http_build_query($no_skip);
				$site->uri['requested_no_skip'] = $no_skip;
			}
			
			/* delete table */
			$tbl = isset($_GET['table']) ? $_GET['table'] : 0;
			$vertical = str_replace('&table='.$tbl, '', $site->uri['requested_no_skip']);
			$site->uri['requested_vertical'] = $vertical;
			
			$ar['ids'] = !empty($_GET['ids']) ? $_GET['ids'] : array(); 
			
			if(!empty($ar['list_pubs']['list'])){
				foreach($ar['list_pubs']['list'] as $k=>$v){
					if($k == 0){
						$str .= ' '.$v['categ_title'];
						//$ar['content'] .= '<br>'.$v['categ_title'].':';
					}
					//if($k > 0){ $ar['content'] .=  ','; }
					//$ar['content'] .= ' '.$v['title'];
					$str .= ' '.$v['title'];					
				}
			}
			
			if(!empty($ar['list_products']['list'])){
				foreach($ar['list_products']['list'] as $k=>$v){
					if($k == 0 && !empty($v['categ_title'])){
						$str .= ' '.$v['categ_title'];	
						if(!empty($ar['content'])){ $ar['content'] .= '<br>'; }
						$ar['content'] .= $v['categ_title'].':';						
					}
					$ar['content'] .= ' '.$v['title'];
					$str .= ' '.$v['title'];					
				}
			}
							
			$ar['metatitle'] = $ar['title'].' '.$str;
			$ar['description'] = $ar['title'].' '.$str;
			
			/* check if set elements */
			/*
			if(!isset($ar['select_pubs']['list'])){ $ar['select_pubs']['list'] = array(); }
			if(!isset($ar['select_products']['list'])){ $ar['select_products']['list'] = array(); }
			if(!isset($ar['list_pubs'])){ $ar['list_pubs'] = array(); }
			if(!isset($ar['list_products'])){ $ar['list_products'] = array(); }
			*/
			return $ar;
        }
        
    }

?>