<?php
	if (!class_exists('Site')) { return; }

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
	* last updated 07.12.2018
	* (добавлена возможность вручную задать характеристики
	* если они не установлены у товаров)
    *
    ***********************/ 

    class Categ extends Site {

        function __construct()
        {
        }
                
        static public function get_page($site, $id=0, $list_ar=array())
        {
            global $db;
            include_once('list_photos.php');
            include_once('list_files.php');
            include_once('list_pubs.php');
            include_once('list_products.php');
            include_once('list_comments.php');
            include_once('list_filter.php');

            $tpl_page = 'category.html';
			$ar = empty($site->page) ? GetEmptyPageArray() : $site->page;

            $sql = "SELECT 
							c.id, c.title, 
							c.id_parent, 
							c2.title as parent_title, 
							c2.alias as parent_alias,
							c2.id_parent as id_parent2, 
							c3.title as parent2_title, 
							c3.alias as parent2_alias,
							
							c.multitpl as page,  
							c.meta_description as description, 
							c.meta_keywords as keywords, 
							c.meta_title as metatitle, 
							c.alias, 
							c.memo as content, 
							c.memo_short as intro, 
							c.active, c.sort, c.sortby, 
							c.date_insert, c.date_update, 
							c.user_id, c.icon, c.f_spec, 
							c.in_last, c.shop, c.show_filter, 
							
							IFNULL((SELECT id2 
								FROM ".$db->tables['connections']." 
								WHERE id1 = c.id 
								AND name1 = 'categ' 
								AND name2 = 'qty_products' 
							),".ONPAGE.") as set_qty_products,
							
							IFNULL(comm.qty_comments,0) as qty_comments, 
							IFNULL(ptp.qty_pubs,0) as qty_pubs, 
							IFNULL(u.qty_photos,0) as qty_photos, 
							IFNULL(f.qty_files,0) as qty_files, 
							IFNULL(qp.qty_products,0) as qty_products, 
							
							IFNULL(qo.qty_options,0) as qty_options, 
							IFNULL(qb.qty_bottomcategs,0) as qty_bottomcategs, 
							IFNULL(qt.qty_topcategs,0) as qty_topcategs

                        FROM ".$db->tables['site_categ']." sc, ".$db->tables['categs']." c 
						
						LEFT JOIN ( 
							SELECT record_id, COUNT(*) qty_comments 
							FROM ".$db->tables['comments']."
							WHERE record_type = 'categ' 
							GROUP BY record_id 
						) comm on (comm.record_id = c.id) 
				
						LEFT JOIN ( 
							SELECT id_categ, `where_placed`, COUNT(*) qty_pubs 
							FROM ".$db->tables['pub_categs']." 
							WHERE where_placed = 'pub' 
							GROUP BY id_categ  
						) ptp on (ptp.id_categ = c.id)
						
						LEFT JOIN ( 
							SELECT record_id, COUNT(DISTINCT id_in_record) qty_photos  
							FROM ".$db->tables['uploaded_pics']."   
							WHERE record_type = 'categ' 
							GROUP BY record_id 
						) u on (u.record_id = c.id)
						
						LEFT JOIN ( 
							SELECT record_id, COUNT(*) qty_files 
							FROM ".$db->tables['uploaded_files']."  
							WHERE record_type = 'categ' 
							GROUP BY record_id 
						) f on (f.record_id = c.id)
				
						LEFT JOIN ( 
							SELECT id_categ, `where_placed`, COUNT(*) qty_products 
							FROM ".$db->tables['pub_categs']."  
							WHERE where_placed = 'product' 
							GROUP BY id_categ  
						) qp on (qp.id_categ = c.id) 				
				
						LEFT JOIN ( 
							SELECT id_categ, `where_placed`, COUNT(*) qty_options 
							FROM ".$db->tables['categ_options']."  
							GROUP BY id_categ  
						) qo on (qo.id_categ = c.id AND qo.where_placed = 'categ') 
			
						LEFT JOIN ( 
							SELECT id_parent, COUNT(*) qty_bottomcategs 
							FROM ".$db->tables['categs']."   
							GROUP BY id_parent  
						) qb on (qb.id_parent = c.id)
				
						LEFT JOIN ( 
							SELECT id_parent, COUNT(*) qty_topcategs 
							FROM ".$db->tables['categs']."   
							GROUP BY id_parent  
						) qt on (qt.id_parent = c.id_parent) 
						
						LEFT JOIN ".$db->tables['categs']." c2 
							ON (c.id_parent = c2.id AND c2.active = '1') 
						
						LEFT JOIN ".$db->tables['categs']." c3 
							ON (c2.id_parent = c3.id AND c3.active = '1')
						";

			if($id > 0){
               $sql .= " 
			   WHERE c.id = '".$id."' ";
           }else{
               $sql .= " 
			   WHERE c.alias = '".$site->uri['alias']."' ";
           } 
           
		    if(!isset($site->vars['id'])){ $site->vars['id'] = 0; }
			$sql .= "                          
                        AND c.id = sc.id_categ 
                        AND sc.id_site = '".$site->vars['id']."'
                        ";

			if(!isset($_GET['debug'])){	
				$sql .= " AND c.date_insert < CONCAT(CURDATE(), ' ',CURTIME()) ";
			}
						
			$row = $db->get_row($sql, ARRAY_A);
			//$db->debug(); exit;
			if($row && $db->num_rows > 0){
				$ar = array_merge ($ar,$row);
			}
			
            if(!empty($db->last_error)){ return $site->db_error($db->last_error); }			
            if($db->num_rows == 0){ return $site->error_page(404); }
            if($db->num_rows == 1){                
            
				if($id == 0 && $row['id'] > 0){ $id = $row['id']; }
				
				if(!isset($row['set_qty_products'])){
					if(isset($site->vars['onpage'])){
						$row['set_qty_products'] = $site->vars['onpage'];
					}elseif(defined('ONPAGE')){
						$row['set_qty_products'] = ONPAGE;
						$site->vars['onpage'] = ONPAGE;
					}else{
						$row['set_qty_products'] = ONPAGE;
						$site->vars['onpage'] = ONPAGE;
					}
				}
				
				if(!isset($site->vars['onpage'])){
					$site->vars['onpage'] = ONPAGE;
				}

				if(!isset($row['set_qty_products'])){
					$row['set_qty_products'] = ONPAGE;
				}
				
                if(empty($ar['active']) && !isset($_GET['debug'])){
					
					if(!empty($site->vars['default_id_categ']) && $site->vars['default_id_categ'] != $id){
						/* тут неясно какие скрытые страницы выводить 
							сейчас выводим если скрытая, но главная страница сайта
						*/
						return $site->error_page(404);
					}					
                }
                      
                /* 
                * Если запрошена дефолтная страница сайта, 
                * то переадресуем на главную 
                */
                if(!empty($site->uri['alias']) && ($id == 0 || $id == $ar['id']) && $site->vars['default_id_categ'] == $ar['id']){
					return $site->redirect($site->vars['site_url']);
                }
				
				/* 
					Если запрошена главная страница сайта по умолчанию -
					корректируем мета-теги, ставим из настроек сайта 
					и дефолтное меню от него создаем				
				*/
				if($site->vars['default_id_categ'] == $id){
					$ar['title'] = $site->vars['name_full'];
					$ar['metatitle'] = $site->vars['meta_title'];
                    $ar['description'] = $site->vars['meta_description'];
                    $ar['keywords'] = $site->vars['meta_keywords'];			
					$last_qty = !empty($site->vars['sys_qty_last_pubs']) 
						? intval($site->vars['sys_qty_last_pubs']) 
						: ONPAGE;
					$ar['last_pubs'] = list_pubs($ar, $site, 'last', $last_qty);
					$ar['action'] = 'category';
					$ar['id'] = $id;
				}
				
				/* URL */
				$ar['link'] = $site->uri['site'].'/'.$ar['alias'].URL_END;
				$ar['idn'] = $site->uri['idn'].'/'.$ar['alias'].URL_END;
                
                /*
                Если страница более 0, то правим метатеги
                */
                if(!empty($site->uri['params']['page'])){
                    if(intval($site->uri['params']['page']) > 0){
                        $pp = intval($site->uri['params']['page'])+1;
                        $ar['metatitle'] = 'Страница '.$pp.' '.$ar['metatitle'];
                        $ar['description'] = 'Страница '.$pp.' '.$ar['description'];
                        $ar['keywords'] = 'Страница '.$pp.' '.$ar['keywords'];
                    }
                }
            
                if($ar['qty_pubs'] > 0 && in_array('list_pubs', $list_ar)){
                    $ar['list_pubs'] = list_pubs($ar, $site);
                }

                if($ar['qty_photos'] > 0 && in_array('list_photos', $list_ar)){
                    $ar['list_photos'] = list_photos('categ', $ar['id'], $site);
                }
								
                if($ar['qty_files'] > 0 && in_array('list_files', $list_ar)){
                    $ar['list_files'] = list_files('categ', $ar['id'], $site);
                }

                if($ar['qty_comments'] > 0 && in_array('list_comments', $list_ar)){
                    $ar['list_comments'] = list_comments($ar['id'], 'categ', $site);
                }

                if($ar['qty_options'] > 0 && in_array('list_options', $list_ar)){
                    $ar['list_options'] = array();
                }

				if($ar['qty_bottomcategs'] > 0 
				){
					$ar = bottomcategs_array($ar, $site);					
				}
				
                if($ar['qty_topcategs'] > 0 
					&& in_array('list_topcategs', $list_ar)
				){
                    if(isset($site->vars['default_menu'])){
						$ar['list_topcategs'] = $site->vars['default_menu'];
					}else{
						$ar['list_topcategs'] = get_categs('categs', 0, 0, $ar['id_parent'], $site);
					}
                }
				
				if(!empty($ar['id_parent']) && 
					!empty($site->vars['default_menu'][$ar['id_parent']]['title'])
				){
					$ar['title_parent'] = $site->vars['default_menu'][$ar['id_parent']]['title'];
				}
					
                if(in_array('breadcrumbs', $list_ar)){
                    $ar['breadcrumbs'][] = $site->get_breadcrumbs($ar['id']);
                }
				
				$ar['is_filter'] = 1;
				$ar = bottomcategs_filter($ar,$site);
				unset($ar['is_filter']);
				//$site->print_r($ar,1);
				
				/* filter requested */
				$requested = !empty($site->uri['params']['options']) 
					? $site->uri['params']['options'] : '';
				$site->page['requested_filter'] = $requested;
				$ar['requested_filter'] = $requested;
	
				/* filters loaded */
				$site->page['filters'] = isset($ar['filters']) 
					? $ar['filters'] : array();
					
				if(empty($site->page['requested_filter'])
					&& !empty($site->uri['params']['options'])
				){
					$site->page['requested_filter'] = $site->uri['params']['options'];
					$ar['requested_filter'] = $site->uri['params']['options'];
				}
				
				if(!empty($ar['requested_filter'])){
					foreach($ar['requested_filter'] as $k => $f){
						if(is_array($f) 
							&& isset($ar['filters'][$k]['value_min']) 
							&& isset($ar['filters'][$k]['value_max']) 
							&& ((isset($ar['filters'][$k]['type']) 
							&& $ar['filters'][$k]['type'] == 'int') 
							|| $k == 'price')
						){
							$ar['requested_filter'][$k]['min'] = $ar['filters'][$k]['value_min'];
							$ar['requested_filter'][$k]['max'] = $ar['filters'][$k]['value_max'];
						}
						
						if(!isset($ar['filters'][$k]) && $k != 'price'){
							unset($ar['requested_filter'][$k]);
						}
					}
					$requested = $ar['requested_filter'];
					$site->page['requested_filter'] = $requested;
					$list_ar[] = 'list_products';
				}
				
				if($ar['qty_products'] > 0 && in_array('list_products', $list_ar)){
					
					if(!empty($ar['requested_filter'])){
						$qq = empty($row['set_qty_products']) ? $site->vars['onpage'] : $row['set_qty_products'];
						$ar['list_products'] = list_by_filter($ar, $site, 'product', $qq);
						if(isset($ar['list_products']['pages'])){ $ar['pages'] = $ar['list_products']['pages']; }
						if(isset($ar['list_products']['all'])){ $ar['all_results'] = $ar['list_products']['all']; }
					}elseif(!isset($ar['list_products'])){
						$ar['list_products'] = list_products($ar, 'categ', $site);
					}
					
					if(!empty($ar['requested_filter'])){
						$ar['is_filtered'] = '1';
						$ar['qty_products'] = !empty($ar['list_products']['list']) 
							? count($ar['list_products']['list']) : 0;
					}					
                }
				
				if(!empty($ar['list_products']['options'])){
					$ar2 = $ar['list_products']['options'];
					$ar['title_options'] = array_shift($ar2);
				}
				
				if(!isset($ar['value_min']) && isset($ar['filters']['price']['value_min'])){
					$ar['value_min'] = $ar['filters']['price']['value_min'];
				}
				
				if(!isset($ar['value_max']) && isset($ar['filters']['price']['value_max'])){
					$ar['value_max'] = $ar['filters']['price']['value_max'];
				}
					
				unset($ar['list_bottomcategs_new']);
				unset($ar['list_pubs2']);
				ksort($ar);
				//$site->print_r($ar,1);
				
                return $ar;
            }            
        }        
    }
	
	
	// пересоберем массив нижних разделов, 
	// с подключением, если надо, 
	// фильтров и товаров
	function bottomcategs_array($ar, $site){
		global $db;
		if(empty($ar['id'])){ return; }
		$start = microtime(true);
		
		if(isset($site->vars['default_menu'][$ar['id']])){			
			$b = find_bottom_categs($ar['id'], $site);
			$ar['list_bottomcategs'] = $b;
		}else{			
			$ar['list_bottomcategs'] = get_categs('categs', $ar['id'], 0, $ar['id'], $site);
		}
		
		$ar['bottom_categs_ids'] = array();
		if(!empty($ar['list_bottomcategs'])){
			$ar['bottom_categs_ids'] = array_keys($ar['list_bottomcategs']);
		}
		$ar['ids'] = $ar['bottom_categs_ids'];

		$qty_bottom = !empty($ar['is_filter']) 
			? 900 :  $ar['set_qty_products'];
		
		$bottom_products = !empty($site->vars['sys_bottom_products']) 
			? 1 : 0;
			
		if(!empty($ar['settings']['list_products'])){
			$qty_bottom = $ar['settings']['list_products'];
		}		
		
		$sort_in_categs = 1;
		
		if(
			(!empty($ar['page']) 
				&& $ar['page'] == 'category.html' 
				&& empty($ar['is_filter'])
			) 
		){
			$sort_in_categs = 0;
		}

		// это чтобы искало в большом кол-ве товаров			
		if( !empty($ar['list_bottomcategs']) 
			&& !empty($qty_bottom) 
		){
			// показ фильтра включен, 
			// готовим вывод всех данных для этого
			/*
			$site->is_filter = 1;
			$r = list_products(
					array('id'=>0, 
						'ids'=>$ar['bottom_categs_ids'],
						'sortby' => $ar['sortby'], 
						'sort_in_categs' => $sort_in_categs
					), 'categ', $site, 'all', $qty_bottom);
			$ar['list_products'] = $r;
*/
			if(!empty($r)){
	
				if(isset($r['price_min'])){ 
					$ar['value_min'] = $r['price_min']; 
					$ar['price_min'] = $r['price_min']; 
				}
				if(isset($r['price_max'])){ 
					$ar['value_max'] = $r['price_max']; 
					$ar['price_max'] = $r['price_max']; 
				}
				if(isset($r['ids'])){ $ar['products_ids'] = $r['ids']; }
				
				$ar['list_bottomcategs_new'] = $ar['list_bottomcategs'];
				foreach($ar['list_bottomcategs_new'] as $k => $v){
					if($v['shop'] == 1 && $v['products'] > 0){
						$ar['list_bottomcategs_new'][$k]['list_products'] = array();
						$ar['qty_products'] += $v['products'];
						
						$j = 0;
						foreach($r['list'] as $k1 => $rid){
										
							if($v['id'] == $rid['id_categ']){
								if($j == 0){
									$ar['list_bottomcategs_new'][$k]['list_products']['spec'] = 'all';
								}
											
								$ar['list_bottomcategs_new'][$k]['list_products']['list'][] = $rid;
											
								$row_opts = isset($r['options'][$rid['id']]) ? $r['options'][$rid['id']] : array();
								$ar['list_bottomcategs_new'][$k]['list_products']['options'][$rid['id']] = $row_opts;
								if($j == 0){
									$ar2 = $row_opts;
									$ar['list_bottomcategs_new'][$k]['title_options'] = array_shift($ar2);
								}
								$j++;
							}
						}
					}
				}
							
				unset($site->is_filter);
				$ar['list_bottomcategs'] = $ar['list_bottomcategs_new'];
			}					
		}
					
		return $ar;
	}

	
	// характеристики, доступные для фильтра
	function bottomcategs_filter($ar,$site){
		global $db;
		
		if(!empty($ar['list_bottomcategs'])){
			$array = array_keys($ar['list_bottomcategs']);
			$categs_str = implode("', '", $array);
			$categs_ids = " AND id_categ IN ('".$categs_str."') ";
			
			$sql = "SELECT id_pub as id 
					FROM ".$db->tables['pub_categs']."  
					WHERE 
						`where_placed` = 'product' 
						".$categs_ids." 
					GROUP BY id_pub 
			";
			$ar['products_ids_all'] = $db->get_results($sql, ARRAY_A);
			$array = array();
			if(!empty($ar['products_ids_all'])){
				foreach($ar['products_ids_all'] as $v){
					$array[] = $v['id'];
				}
			}
			$ar['products_ids_all'] = $array;
		}	
	
		if(empty($ar['qty_bottomcategs'])){ return $ar; }
		
		if(!empty($ar['show_filter'])){
					
			$str_ids = '';
			if(!empty($ar['products_ids_all'])){
				$sub_str = implode("', '", $ar['products_ids_all']);
				$str_ids = " AND id_product IN ('".$sub_str."') ";
			}

			$sql = "SELECT o.id, o.title, o.alias, o.sort, o.group_id, 
							o.type, o.show_in_filter, o.filter_type, 
							o.show_in_list, o.filter_description, 
							o.after, o.icon, 
							og.to_show, 
							IF(o.type = 'int' ,
								(
								SELECT MIN(CAST(value as DECIMAL(10,2))) as min_value 
								FROM option_values 
								WHERE id_option = o.id  
								".$str_ids." 
								AND value !=  ''
								), 0
							) as value_min, 
							IF(o.type = 'int',
								(SELECT MAX(CAST(value as DECIMAL(10,2))) as max_value 
								FROM option_values 
								WHERE id_option = o.id  
								".$str_ids." 
								AND value !=  ''
								), 0
							) as value_max
						FROM ".$db->tables['options']." o, 
							".$db->tables['option_groups']." og, 							
							".$db->tables['categ_options']." co 
							
						WHERE ";

			if(!empty($ar['bottom_categs_ids'])){
				$s = implode("','", $ar['bottom_categs_ids']);
				$sql .= "
					co.id_categ IN ('".$s."')  
				";
			}else{
				$sql .= "
					co.id_categ = '".$ar['id']."' 	
				";
			}			
													
			$sql .= "
							AND co.`where_placed` = 'categ' 
							AND co.id_option = og.id 
							AND og.id = o.group_id 
							AND o.show_in_filter = '1' 
							AND og.to_show = 'all'
						GROUP BY o.id 
						ORDER BY o.sort, og.sort, og.title, o.title 
			";
			
			/* select, text, radio, multicheckbox */
			$rows = $db->get_results($sql, ARRAY_A);
			//$db->debug(); exit;
			/*
				сделать фильтр с учетом мин. и макс. значения
				для цифровых полей и с учетом вложенных категорий.
			*/
			
			$new_rows = array();
			if($rows && $db->num_rows > 0){						
				foreach($rows as $row){
					
					if($row['filter_type'] != 'connected' && $row['filter_type'] != 'text'){ 
						/* 1 */
						$sql = "SELECT oval.*, 
									qtty.innercount as qty 
								FROM (
									SELECT o.* 
										FROM option_values o 
									WHERE o.id_option = '".$row['id']."' 
										".$str_ids." 
										AND o.value <> '' 
						";
						if(!empty($ar['shop']) 
							|| !empty($site->vars['sys_bottom_products'])
						){ 
							$sql .= " AND o.`where_placed` = 'product' "; 
						}else{ $sql .= " AND o.`where_placed` = 'pub' "; }	
								
						$sql .= "
								GROUP BY o.`value`
								) oval 
							LEFT JOIN (
								SELECT ov.value as innervalue, COUNT(*) as innercount 
									FROM option_values ov 
									GROUP BY innervalue) qtty 
										ON
										qtty.innervalue <> '' 
										AND UPPER(qtty.innervalue) = UPPER(oval.value) 
										GROUP BY oval.`value` 
						";
										
						if($row['type'] == 'int'){
							$sql .= "ORDER BY CAST(oval.`value` as DECIMAL(10,2)) ASC";
						}else{
							$sql .= "ORDER BY oval.`value` ASC";
						}
						$row['values'] = $db->get_results($sql, ARRAY_A);
						//$db->debug(); exit;
					}elseif($row['filter_type'] == 'text'){
						//$site->print_r($row,1);
					}elseif($row['filter_type'] == 'connected'){
					}else{
					}
							
					if(!empty($row['values'])){
						$subrow = $row['values'];
						$row['values'] = array();
						$test_ar = array();
						foreach($subrow as $v){									
							$values = str_replace("\r\n","\n",$v['value']);
							$values = explode("\n",$values);
							if(!empty($values)){
								foreach($values as $v1 => $v2){
									$v2 = trim($v2);
									$v['value'] = $v2;
									$ch = isset($site->uri['params']['options'][$row['alias']]) 
										? $site->uri['params']['options'][$row['alias']] 
										: '';
									
									if(is_array($ch)){
										if($site->in_arrayi(
											$v['value'], $ch
										)){
											$ch = 'checked';
										}else{ $ch = ''; }
									}else{
										$ch = mb_strtolower($ch, 'UTF-8') == mb_strtolower($v['value'], 'UTF-8') ? 'checked' : '';
									}
											
									$v['checked'] = $ch;	
									if(!in_array($v2, $test_ar)){
										$test_ar[] = $v2;
										$row['values'][] = $v;
									}
								}
							}
						}
					}
							
					//$site->print_r($row,1);
					$new_rows[$row['alias']] = $row;
				}
			}
			
			if(isset($ar['value_min']) && isset($ar['value_max'])
			){
				$new_rows['price'] = array(
					'value_min' => $ar['value_min'],
					'value_max' => $ar['value_max'],
				);
			}else{
				//Найдем здесь макс и мин цену!!!!
				if(!empty($ar['bottom_categs_ids'])){
					$s = implode("','", $ar['bottom_categs_ids']);
					$subsql = " IN ('".$s."')  ";
				}else{
					$subsql = " = '".$ar['id']."' 	";
				}		
					
				$sql = "SELECT COUNT(*) as qty, 
						(
							SELECT MAX(p2.price) 
							FROM ".$db->tables['products']." p2 
							LEFT JOIN ".$db->tables['pub_categs']." pc2 
								ON (p2.id = pc2.id_pub)
							WHERE pc2.where_placed = 'product' 
								AND pc2.id_categ ".$subsql."  
								AND p2.active = '1' 
								AND p2.currency = 'rur' 						
						) as max_price_rub,

						(
							SELECT MAX(p3.price) 
							FROM ".$db->tables['products']." p3 
							LEFT JOIN ".$db->tables['pub_categs']." pc3 
								ON (p3.id = pc3.id_pub)
							WHERE pc3.where_placed = 'product' 
								AND pc3.id_categ ".$subsql." 
								AND p3.active = '1' 
								AND p3.currency = 'euro' 						
						) as max_price_euro,

						(
							SELECT MAX(p4.price) 
							FROM ".$db->tables['products']." p4 
							LEFT JOIN ".$db->tables['pub_categs']." pc4 
								ON (p4.id = pc4.id_pub)
							WHERE pc4.where_placed = 'product' 
								AND pc4.id_categ ".$subsql." 
								AND p4.active = '1' 
								AND p4.currency = 'usd' 						
						) as max_price_usd, 

						(
							SELECT MIN(p12.price) 
							FROM ".$db->tables['products']." p12 
							LEFT JOIN ".$db->tables['pub_categs']." pc12 
								ON (p12.id = pc12.id_pub)
							WHERE pc12.where_placed = 'product' 
								AND pc12.id_categ ".$subsql." 
								AND p12.active = '1' 
								AND p12.currency = 'rur' 						
						) as min_price_rub,

						(
							SELECT MIN(p13.price) 
							FROM ".$db->tables['products']." p13 
							LEFT JOIN ".$db->tables['pub_categs']." pc13 
								ON (p13.id = pc13.id_pub)
							WHERE pc13.where_placed = 'product' 
								AND pc13.id_categ ".$subsql." 
								AND p13.active = '1' 
								AND p13.currency = 'euro' 						
						) as min_price_euro,

						(
							SELECT MIN(p14.price) 
							FROM ".$db->tables['products']." p14 
							LEFT JOIN ".$db->tables['pub_categs']." pc14 
								ON (p14.id = pc14.id_pub)
							WHERE pc14.where_placed = 'product' 
								AND pc14.id_categ ".$subsql." 
								AND p14.active = '1' 
								AND p14.currency = 'usd' 						
						) as min_price_usd						
						
						
					FROM ".$db->tables['products']." p 
					LEFT JOIN ".$db->tables['pub_categs']." pc 
						ON (p.id = pc.id_pub)
					WHERE pc.where_placed = 'product' 
					AND pc.id_categ ".$subsql." 
					AND p.active = '1' 
				";
				$p = $db->get_row($sql);
				//$db->debug(); exit;
				if(!empty($p)){					
					if(defined('SI_RATE_USD') && defined('SI_RATE_EURO')){ 
					
						$pmin = array();
						if($p->min_price_euro != ''){
							$p_euro = $p->min_price_euro;
							if(SI_CURRENCY == 'rur'){
								$pmin[] = $p_euro*SI_RATE_EURO;
							}else{ $pmin[] = $p_euro; }
						}
						
						if($p->min_price_usd != ''){
							$p_usd = $p->min_price_usd;
							if(SI_CURRENCY == 'rur'){
								$pmin[] = $p_usd*SI_RATE_USD;
							}else{ $pmin[] = $p_usd; }
						}
						
						if($p->min_price_rub != ''){
							$pmin[] = $p->min_price_rub;
						}
					
						$pmax = array();
						if($p->max_price_euro != ''){
							$pmax_euro = $p->max_price_euro;
							if(SI_CURRENCY == 'rur'){
								$pmax[] = $pmax_euro*SI_RATE_EURO;
							}else{ $pmax[] = $pmax_euro; }
						}
						
						if($p->max_price_usd != ''){
							$pmax_usd = $p->max_price_usd;
							if(SI_CURRENCY == 'rur'){
								$pmax[] = $pmax_usd*SI_RATE_USD;
							}else{ $pmax[] = $pmax_usd; }
						}
						
						if($p->max_price_rub != ''){
							$pmax_rub = $p->max_price_rub;
							$pmax[] = $pmax_rub;
						}
						
						$value_max = empty($pmax) ? 0 : max($pmax);
						$value_min = empty($pmin) ? 0 : min($pmin);
					}else{
						if(SI_CURRENCY == 'euro'){
							$value_min = $p->min_price_euro;
							$value_max = $p->max_price_euro;
						}elseif(SI_CURRENCY == 'usd'){
							$value_min = $p->min_price_usd;
							$value_max = $p->max_price_usd;
						}else{
							$value_min = $p->min_price_rub;
							$value_max = $p->max_price_rub;
						}						
					}
					
					$new_rows['price'] = array(
						'value_min' => $value_min,
						'value_max' => $value_max,
						'qty' => $p->qty
					);
					$ar['qty_products'] = $p->qty;
				}
			}
			
			$ar['filters'] = $new_rows;
		} 
		//$site->print_r($new_rows,1);
		return $ar;			
	}
	
	/* тут выведем список товаров, либо все, если фильтр не установлен */
	function filtered_products($ar, $site){
	}
	
	function find_bottom_categs($id, $site, $array=array(), $level=0){		
		if(isset($site->vars['default_menu'][$id])){
			
			foreach($site->vars['default_menu'] as $k => $v){
				if($v['id_parent'] == $id){
					$v['level'] = $level+1;
					$array[$v['id']] = $v;
					if(!empty($v['child_categs'])){
						$array = find_bottom_categs($v['id'], $site, $array, $level);
					}
				}
			}
		}
		return $array;
	}	
	

	
	
	
	
	
	/*
		Установка вручную не достающих значений характеристики внутри страницы
		
		добавить вывод на странице
		и установить нужные данные характеристики в массив $set_option_value
		
				if(!empty($site->user['login']) && $site->user['login'] == 'admin' && !empty($site->user['admin'])){
					
					$set_option_value = array(
						'alias' => 'inverter',
						'value' => 0,
						'id' => 33,
						'where_placed' => 'product'
					);
					// $ar['id']
					
					if(!empty($ar['list_bottomcategs'])){
						//$site->print_r($ar['list_bottomcategs'],1);
						foreach($ar['list_bottomcategs'] as $k => $v){
							if(!empty($v['products']) && manual_check_option($k, $set_option_value)){
								$id = array('id' => $k);
								$pp = list_products($id, 'product', $site, 'all', 999);
								foreach($pp['list'] as $p1 => $p2){
									manual_update_value($p2['id'], 'product', $set_option_value, $site);
								}
							}
						}
					}elseif(!empty($ar['list_products']) && manual_check_option($ar['id'], $set_option_value)){
						foreach($ar['list_products']['list'] as $p1 => $p2){
							manual_update_value($p2['id'], 'product', $set_option_value, $site);
						}
					}
				}		
	
	*/
	
	function manual_check_option($id, $ar_option_value){
		global $db;
		$gr = $db->get_var("SELECT `group_id` 
			FROM `".$db->tables['options']."` 
			WHERE `alias` = '".$ar_option_value['alias']."' 
		");
		if(empty($gr)){ return false; }
		
		$sql = "SELECT COUNT(*)  
			FROM `".$db->tables['categ_options']."` 
			WHERE id_categ = '".$id."' AND id_option = '".$gr."'
		";
		$rows = $db->get_var($sql);
		if(empty($rows)){ return false; }
		return true;
	}
	 
	function manual_update_value($id, $where_is='product', $ar_option_value, $site){
		global $db;
		$sql = "SELECT * 
			FROM `".$db->tables['option_values']."` 
			WHERE id_option = '".$ar_option_value['id']."'
			AND id_product = '".$id."' ";
		$row = $db->get_row($sql);
		if(empty($row)){ // add new record
			$sql = "INSERT INTO `".$db->tables['option_values']."` 
				(`id_option`, `id_product`, `value`, `where_placed`, `value2`, `value3`)
				VALUES ('".$ar_option_value['id']."', '".$id."', '".$ar_option_value['value']."', 
				'".$ar_option_value['where_placed']."', '', '')
			";
			$db->query($sql);
		}
		return;
	}	
	
?>