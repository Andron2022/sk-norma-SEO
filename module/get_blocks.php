<?php
	if (!class_exists('Site')) { return; }

    /***********************
	*
	*	last update: 13.02.2020
	*	+ added path to pics and fixed url for multi-sites based on folders
	*	+ key in blocks equal sort field in admin
	*	+ delete old picture function
	* 	+ added extra_html for block types
	*	+ blocks[sys] - для настройки служебных типов блоков
	*
    * returns array 
    *
	* Типы блоков
		visitedProducts - Просмотренные товары
		visitedPubs - Просмотренные публикации

		starredPubs - Важные публикации		
		starredCategs - Важные страницы

		specProducts - Спец.товары
		newProducts - Новые товары
		lastProducts - Последние товары
		productProducts - Товары в товаре
		listProducts - Список товаров

		lastPubs - Последние публикации
		pubProducts - Товары в публикации
		productPubs - Публикации в товаре
		listPubs - Список публикаций
		listCategs - Список страниц
		breadcrumbs - Навигационная строка
		free - Свободный блок

		+++
		comparePubs  - отложено для сравнения
		compareProducts - отложено для сравнения
		inBasket - Товары в корзине
		
    ***********************/ 

    class Blocks extends Site {

        function __construct()
        {
        }
                
        static public function get_blocks($site)
        {
			global $db;
			$substr = '*'; // окончание или начало любых символов строки
			$alias = !empty($site->uri['alias']) ? $site->uri['alias'] : '';
			$sql = "SELECT b.*, 
				b_extra.type as type_up, 
				b_extra.type_id as extra_id, 
				b_extra.html as extra_html   
			";
			
			$sql .= ", (SELECT COUNT(DISTINCT id_in_record) 
					FROM `{$db->tables['uploaded_pics']}`  
					WHERE record_type = 'block' 
					AND record_id = b.id 
				) as qty_photos
			";
			
			$sql .= " FROM 	".$db->tables['blocks']." b 					
			";
			
			$sql .= "
				LEFT JOIN ".$db->tables['connections']." c 
				ON (
					b.active = '1' AND 
					b.`where_placed` <> 'manual' AND 
					b.id = c.id2 AND c.name2 = 'block' 
					AND c.id1 = '".$site->id."' 
					AND c.name1 = 'site'
				)
			";
			
			$sql .= "
				LEFT JOIN {$db->tables['blocks']} b_extra 
				ON (b_extra.title = b.where_placed)  
			";
					
			$sql .= " WHERE 
			";
			/*
			if(!empty($site->uri['action']) && !empty($site->page['id'])){
				$type_id = $site->page['id'];
				$type_where = $site->uri['action'];
				if($type_where == 'category'){ $type_where = 'categ'; }
				if($type_where == 'publication'){ $type_where = 'pub'; }
			}
			*/

			$type_id = isset($site->page['id']) ? $site->page['id'] : 0;
			$type_where = isset($site->uri['action']) ? $site->uri['action'] : '';
			if(!empty($site->page['action'])){ $type_where = $site->page['action']; }
			if($type_where == 'category'){ $type_where = 'categ'; }
			if($type_where == 'publication'){ $type_where = 'pub'; }

			$sql .= " (b.active = '1' AND 
					b.`where_placed` <> 'manual' AND 
					b.id = c.id2 AND c.name2 = 'block' 
					AND c.id1 = '".$site->id."' 
					AND c.name1 = 'site') 
			";
			
			if(!empty($type_id) && !empty($type_where)){
				$sql .= " OR (
					b.type = '".$type_where."' 
					AND b.type_id = '".$type_id."' 
					AND b.active = '1' 
				)
				";
			}
			
			$sql .= " 
				GROUP BY b.id 
				ORDER BY b.`sort`, b.`where_placed`
			";	
		
			$rows = $db->get_results($sql);
			//$db->debug(); //exit;

		if(!empty($db->last_error)){ return $site->db_error($db->last_error); }
			if($db->num_rows == 0){ return array(); }
			$ar = array();
			if(empty($site->uri['path'])){ $site->uri['path'] = '/'; }
			foreach($rows as $k_row => $row){

				if(!empty($row->pages) && empty($type_where)){
					
					$row->pages = str_replace(array("\r\n", "\r", "\n"), "<br />", $row->pages);
					$pages = explode('<br />', $row->pages);
					foreach($pages as $k=>$v){
						if(substr($v, -1) == $substr && substr($v, 0, 1) == $substr){
							$v = substr($v, 0, -1);
							$v = substr($v, 1);
							
							if(strlen($site->uri['path']) > strlen($v) 
									&& substr($site->uri['path'], 0-strlen($v)) != $v
									&& substr($site->uri['path'], 0, strlen($v)) != $v 
									&& strpos($site->uri['path'], $v) !== FALSE
								){
								if(!isset($ar[$row->id])){ $ar[$row->id] = $row; }
							}
							
						}elseif(substr($v, 0, 1) == $substr){
							$v = substr($v, 1);
							
							if(strlen($site->uri['path']) > strlen($v) 
									&& substr($site->uri['path'], 0-strlen($v)) == $v){
								if(!isset($ar[$row->id])){ $ar[$row->id] = $row; }
							}
							
						}elseif(substr($v, -1) == $substr){
							$v = substr($v, 0, -1);

							if(strlen($site->uri['path']) > strlen($v) 
									&& substr($site->uri['path'], 0, strlen($v)) == $v){
								if(!isset($ar[$row->id])){ $ar[$row->id] = $row; }
							}
							
						}else{
							if($site->uri['path'] == $v){
								if(!isset($ar[$row->id])){ $ar[$row->id] = $row; }
							}							
						}						
					}					
				}
				if(!isset($ar[$row->id]) && (empty($row->pages) || !empty($type_where))){ 
					$ar[$row->id] = $row; 
				}
				
				if(!empty($row->skip_pages) && empty($type_where)){
					$row->skip_pages = str_replace(array("\r\n", "\r", "\n"), "<br />", $row->skip_pages);
					$pages = explode('<br />', $row->skip_pages);
					foreach($pages as $k=>$v){
						if(substr($v, -1) == $substr && substr($v, 0, 1) == $substr){
							$v = substr($v, 0, -1);
							$v = substr($v, 1);
							
							if(strlen($site->uri['path']) > strlen($v) 
									&& substr($site->uri['path'], 0-strlen($v)) != $v
									&& substr($site->uri['path'], 0, strlen($v)) != $v 
									&& strpos($site->uri['path'], $v) !== FALSE
								){
								if(isset($ar[$row->id])){ unset($ar[$row->id]); }
							}
							
						}elseif(substr($v, 0, 1) == $substr){
							$v = substr($v, 1);
							
							if(strlen($site->uri['path']) > strlen($v) 
									&& substr($site->uri['path'], 0-strlen($v)) == $v){
								if(isset($ar[$row->id])){ unset($ar[$row->id]); }
							}
							
						}elseif(substr($v, -1) == $substr){
							$v = substr($v, 0, -1);

							if(strlen($site->uri['path']) > strlen($v) 
									&& substr($site->uri['path'], 0, strlen($v)) == $v){
								if(isset($ar[$row->id])){ unset($ar[$row->id]); }
							}
							
						}else{
							if($site->uri['path'] == $v){
								if(isset($ar[$row->id])){ unset($ar[$row->id]); }
							}							
						}						
					}					
				}
				
				if($row->qty_photos > 0 && isset($ar[$row->id])){
					$bid = $row->id;
					$arr = list_photos('block', $bid, $site);
					$ar[$bid]->list_photos = $arr;
					$arPhotos[$bid] = $arr;
				}
				
			}
			$rows = $ar;
			$ar = array();
			if($db->num_rows > 0){
				foreach($rows as $row){
					
					$place = $row->where_placed;
					if($row->qty == 12){
						$col = 1;
					}elseif($row->qty == 6){
						$col = 2;
					}elseif($row->qty == 4){
						$col = 3;
					}elseif($row->qty == 3){
						$col = 4;
					}elseif($row->qty == 2){
						$col = 6;
					}else{
						$col = 12;
					}
					/*
					comments - $site->page['list_comments']
					products - $site->page['list_products']['list']
					pubs - $site->page['list_pubs']['list']
					categs - $site->page['list_bottomcategs']
					photos - $site->page['list_photos']
					*/
					$arr_block = array(
						'id' => $row->id,
						'where' => $row->where_placed,
						'gruppa' => $row->where_placed,
						'title' => $row->title,
						'title_admin' => $row->title_admin,
						'qty' => $row->qty,
						'col' => $col,
						'type' => $row->type,
						'type_id' => $row->type_id,
						'type_up' => $row->type_up,
						'html' => $row->html,
						'extra_html' => $row->extra_html,
						'extra_id' => $row->extra_id,
						'pages' => $row->pages,
						'skip_pages' => $row->skip_pages,
						'extra' => $row->pages,
						'comment' => $row->skip_pages,
						'content' => '',
					);
				
					if(!empty($arPhotos[$row->id])){
						$arr_block['list_photos'] = $arPhotos[$row->id];
					}elseif($row->where_placed == 'comments'){						
						$arr_block['list_photos'] = $site->page['list_comments'];
						$ar['sys']['comments'] = $arr_block;
					}elseif($row->where_placed == 'products'){
						if(!empty($site->page['list_products']['list'])){
							$arr_block['list_photos'] = $site->page['list_products']['list'];
						}elseif(!empty($row->pages)){
							$id_where = array('ids'=>explode(',',$row->pages));
							$products = list_products($id_where, 'product', $site, 
								'connected', 12);
							$arr_block['list_products'] = $products['list'];
						}
						$ar['sys']['products'] = $arr_block;
					}elseif($row->where_placed == 'pubs'){
						$ar['sys']['pubs'] = $arr_block;
						//$arr_block['list_photos'] = $site->page['list_pubs']['list'];
					}elseif($row->where_placed == 'categs'){
						$arr_block['list_photos'] = $site->page['list_bottomcategs'];
						$ar['sys']['categs'] = $arr_block;
					}elseif($row->where_placed == 'photos'){
						$arr_block['list_photos'] = $site->page['list_photos'];
						$ar['sys']['photos'] = $arr_block;
					}elseif(!empty($row->list_photos)){
						$arr_block['list_photos'] = $row->list_photos;
					}
					
					if(
						$row->type_up == 'types' || $row->type_up == 'templates' 
						|| $row->type == 'categ' || $row->type == 'pub' 
						|| $row->type == 'product'
					){
						$place = 'manual';	
						$arr_block['where'] = 'manual';					

						if(!empty($row->extra_html) && empty($row->extra_id)){
							$arr_block['type'] = 'free';
							$extra_html = get_blocks_content($arr_block,$site);
							$arr_block['extra_html'] = $extra_html;
						}elseif(!empty($row->extra_id)){
							$arr_block['type'] = 'block_id';
							$extra_html = get_blocks_content($arr_block,$site);
							$arr_block['extra_html'] = $extra_html;
						}
					}

					/* тут переделаем manual чтобы выводились типы и шаблоны блоков */
					
					if(empty($ar[$place][$row->title]) && $place != 'manual'){
						$ar[$place][$row->title] = $arr_block;
					}else{						
						if(isset($ar[$place][$row->sort]) || empty($row->sort)){
							$ar[$place][] = $arr_block;
						}else{
							$ar[$place][$row->sort] = $arr_block;
						}
					}
				}				
			}	
            return $ar;           
        }        
    }
	
	function get_blocks_content($v, $site){
		global $tpl, $db;
		
	/*	
	'block_types' => array(
		'basket' => 'Корзина', OK
		'breadcrumbs' => 'Навигационная строка', OK
++		'comments_pubs' => 'Комментарии публикаций' 
++		'comments_categs' => 'Комментарии страниц' 

		'free' => 'Свободный блок' OK
		'lastProducts' => 'Последние товары', OK
		'lastPubs' => 'Последние публикации', OK
		'listCategs' => 'Список страниц', OK
		'listProducts' => 'Список товаров', OK
		'listPubs' => 'Список публикаций', OK
		'listShopCategs' => 'Список страниц с товарами', OK
		'newProducts' => 'Новые товары', OK
		'options' => 'Характеристики', OK
		'productProducts' => 'Товары в товаре', OK
		'productPubs' => 'Публикации в товаре', OK
		'pubProducts' => 'Товары в публикации', OK
		'pubPubs' => 'Публикации в публикации', OK
		'specProducts' => 'Спец.товары', OK
		'starredCategs' => 'Важные страницы', OK
		'starredPubs' => 'Важные публикации', OK
		'visitedProducts' => 'Просмотренные товары', OK
		'visitedPubs' => 'Просмотренные публикации', OK

	),
*/		
		$qty = $v['qty'] == 0 ? 100 : $v['qty'];
		$id = isset($site->page['categs'][0]['id']) ? $site->page['categs'][0]['id'] : 0;
		if($v['type_id'] > 0){ $id = $v['type_id']; }else{$id = 0;}
		$categ = array('id' => $id, 'sortby' => 'name');

		if($v['type'] == 'listPubs'){
			//&& $id > 0
			$ar = list_pubs($categ, $site, 'all', $qty);
			$tpl->assign("array", $ar);
			return $tpl->fetch_html($v['html']);
			
		}elseif($v['type'] == 'lastPubs'){
			
			$ar = list_pubs($categ, $site, 'last', $qty);
			$tpl->assign("array", $ar);
			return $tpl->fetch_html($v['html']);

		}elseif($v['type'] == 'visitedPubs'){
			
			$ar = list_pubs($categ, $site, 'visited', $qty);
			$tpl->assign("array", $ar);
			return $tpl->fetch_html($v['html']);

		}elseif($v['type'] == 'starredPubs'){
			
			$ar = list_pubs($categ, $site, 'starred', $qty);
			$tpl->assign("array", $ar);
			return $tpl->fetch_html($v['html']);



		}elseif($v['type'] == 'comments_categs'){
			
			$ar = list_comments($id, 'categ', $site, $qty);
			$tpl->assign("array", $ar);
			return $tpl->fetch_html($v['html']);

		}elseif($v['type'] == 'comments_pubs'){
			
			$ar = list_comments($id, 'pub', $site, $qty);
			$tpl->assign("array", $ar);
			return $tpl->fetch_html($v['html']);
			
		}elseif($v['type'] == 'listShopCategs'){
			
			$array = get_categs('categs', 0, 1, $id, $site);
			$tpl->assign("array", $array);
			return $tpl->fetch_html($v['html']);
			
		}elseif($v['type'] == 'listCategs'){
			$array = $id > 0 ? get_categs('categs', 0, 0, $id, $site) : $site->vars['default_menu'];
			$tpl->assign("array", $array);
			return $tpl->fetch_html($v['html']);

		}elseif($v['type'] == 'starredCategs'){
			
			$array = get_categs('categs', 0, 0, $id, $site, 1);
			$tpl->assign("array", $array);
			return $tpl->fetch_html($v['html']);
			
		}elseif($v['type'] == 'block_id'){
			$tpl->assign("uri", $site->uri);
			$tpl->assign("tpl", $site->tpl);
			$tpl->assign("site", $site->vars);
			$tpl->assign("page", $site->page);
			$block = get_block_by_id($v['extra_id'], $site);
			if(!empty($v['title'])){
				$block['title'] = $v['title'];
			}
			$tpl->assign("array", $block);
			
			if(!empty($block['extra_html'])){
				return $tpl->fetch_html($block['extra_html']);
			}else{
				return $tpl->fetch_html($block['html']);
			}			
		}elseif($v['type'] == 'free'){
			$tpl->assign("uri", $site->uri);
			$tpl->assign("tpl", $site->tpl);
			$tpl->assign("site", $site->vars);
			$tpl->assign("page", $site->page);
			$tpl->assign("array", $v);
			if(!empty($v['extra_html'])){
				return $tpl->fetch_html($v['extra_html']);
			}else{
				return $tpl->fetch_html($v['html']);	
			}			

		}elseif($v['type'] == 'breadcrumbs'){
			
			$ar = !empty($site->page['breadcrumbs']) ? $site->page['breadcrumbs'] : array();
			$tpl->assign('breadcrumbs', $ar);
			return $tpl->fetch_html($v['html']);

		}elseif($v['type'] == 'specProducts'){
			
			$array = list_products(0, 'product', $site, 'spec', $qty);
			$tpl->assign("array", $array);
			return $tpl->fetch_html($v['html']);


		}elseif($v['type'] == 'productProducts'){
			
			$array = list_products(0, 'product', $site, 'connected', $qty);
			$tpl->assign("array", $array);
			return $tpl->fetch_html($v['html']);

		}elseif($v['type'] == 'productPubs'){
			
			$array = list_products(0, 'product', $site, 'pub', $qty);
			$tpl->assign("array", $array);
			return $tpl->fetch_html($v['html']);

		}elseif($v['type'] == 'visitedProducts'){
			
			$array = list_products(0, 'product', $site, 'visited', $qty);
			$tpl->assign("array", $array);
			return $tpl->fetch_html($v['html']);
			
		}elseif($v['type'] == 'lastProducts'){
			
			$array = list_products(0, 'product', $site, 'last', $qty);
			$tpl->assign("array", $array);
			return $tpl->fetch_html($v['html']);

		}elseif($v['type'] == 'listProducts'){
			
			$array = list_products(0, 'product', $site, 'all', $qty);
			$tpl->assign("array", $array);
			return $tpl->fetch_html($v['html']);
			
		}elseif($v['type'] == 'newProducts'){
			
			$array = list_products(0, 'product', $site, 'new', $qty);
			$tpl->assign("array", $array);
			return $tpl->fetch_html($v['html']);
			
		}elseif($v['type'] == 'options'){
			
			return $tpl->fetch_html($v['html']);
			
		}elseif($v['type'] == 'basket'){
			
			$basket = array(
				'list' => isset($_SESSION['basket']) ? $_SESSION['basket'] : array(),
				'total' => isset($_SESSION['basket_total']) ? $_SESSION['basket_total'] : array()
			);
			//$basket = isset($site->page['basket']) ? $site->page['basket'] : array();	
			//$site->print_r($basket, 1);
			$tpl->assign("array", $basket);
			return $tpl->fetch_html($v['html']);
			
		}elseif($v['type'] == 'pubProducts'){
			$ar = isset($site->page['connected_products']) ? $site->page['connected_products'] : array();
			$tpl->assign("array", $ar);
			return $tpl->fetch_html($v['html']);
			
		}elseif($v['type'] == 'pubPubs'){
			
			$ar = list_pubs($site->page, $site, 'pub_connected');
			$tpl->assign("array", $ar);
			return $tpl->fetch_html($v['html']);
			
		}
		
	}
	
	function get_blocks_tpl($ar=NULL){
		global $site, $tpl;
		// $ar[where] => top
		// $ar[name] => header
		// $ar[delimiter] => delimiter for blocks
		if(empty($ar["where"])){ return; }
		if($ar["where"] == "manual" && !empty($ar["name"])){
			//$site->print_r($ar);
			// Создадим html блок вручную вызванного блока
		}

		if(empty($ar) || empty($ar['where']) || !isset($site->blocks[$ar['where']])){
			return '';
		}
		
		$str = '';
		foreach($site->blocks[$ar['where']] as $k => $v){
			if(empty($ar['name']) || $v['title'] == $ar['name']){								
				
				/* выводим список всех блоков для этого места */
				$i = !isset($i) ? 0 : $i+1;
				//$row = $tpl->fetch_html($v['html']);
				$row = get_blocks_content($v, $site);
				if($i > 0 && !empty($ar['delimiter'])){ $row .= $ar['delimiter']; }
				$str .= $row;
			}
		}
		if(!empty($ar['assign'])){
			$tpl->assign($ar['assign'], $str);
		}else{
			return $str;
		}
		
	}


	function get_block_by_id($id, $site){
		global $db, $tpl;
		$row = $db->get_row("SELECT b.*, b_extra.html as extra_html 
			FROM ".$db->tables['blocks']." b 
			LEFT JOIN ".$db->tables['blocks']." b_extra ON (b.where_placed = b_extra.title) 
			WHERE b.id = '".$id."' ", ARRAY_A);
		if(empty($row) || $db->num_rows == 0){ return; }
		$arr = list_photos('block', $id, $site);
		$row['list_photos'] = $arr;
		if($row['where_placed'] == 'comments'){
			$arr = list_comments($row['type_id'], $row['type'], $site);
			$row['list_comments'] = $arr;
		}
		return $row;		
	}
?>