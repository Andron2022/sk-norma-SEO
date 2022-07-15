<?php
    /*
        list_filter
        last modified 6.12.2018
        Список товаров в виде массива с учетом фильтра 
        с разбивкой на страницу или выводом всех записей,
        если так задано get=all    
		$ar['requested_filter']
		+ учет сортировки при выводе с вложенными подразделами
		+ учет кол-ва товаров константой SELECTION_PAGE_ROWS
		+ сортировка с учетом вложенных подразделов
		+ исправлен поиск, учёт позиций да/нет/не выбрано
    */
	
    function list_by_filter($ar, $site, $spec='product', $limit=ONPAGE)
    {
        global $db;
		$id = isset($ar['id']) ? $ar['id'] : 0;
		$categ = $ar;
		if(!empty($ar['ids'])){
			$id = 0;			
		}else{
			$ar['ids'] = $id; 
		}
		
        $current_page = isset($site->uri['params']['page']) 
			? intval($site->uri['params']['page']) : 0;
		
		$sql_num_rows = ' SELECT count(DISTINCT p.`id`) ';
         
        $sql = "SELECT DISTINCT 
                p.id, 
				c3.sort as sort1, c2.sort as sort2, c.sort as sort3, 
				p.name as title, p.name_short as title_short, p.memo_short as intro, 
				c.id as categ_id, 
				c.alias as categ_alias, 
				c2.id as categ2_id, 
				c2.alias as categ2_alias, 
				c3.id as categ3_id, 
				c.title as categ_title, 
				c2.title as categ2_title, 
				c3.title as categ3_title,
				c3.alias as categ3_alias,
                p.comment, p.barcode, p.memo as content,   
                p.currency, p.price, p.price_spec, p.price_period,                  
                p.accept_orders, p.active,   
                p.date_insert, p.date_update, p.total_qty,   
                p.alias, p.id_next_model, p.treba as id_required, p.present_id as id_gift,  
                p.user_id, p.weight_deliver, p.bid_ya, 
                p.f_spec, p.f_new, p.views, 
				".$site->vars['id']." as id_site, pc.id_categ,  
								
				IFNULL(m.monthviews,0) as monthviews,
				IFNULL(u.fotos_qty,0) as fotos_qty,
                IFNULL(f.files_qty,0) as files_qty,
                IFNULL(c.comments_qty,0) as comments_qty  ,
				IFNULL(ptp.pubs_qty,0) as pubs_qty  , 
				IFNULL(pp.products_qty,0) as products_qty  
				  
		";

        if(isset($site->vars['img_size1']['width']) && isset($site->vars['img_size1']['height'])){
            $sql .= " , 
              pic3.id AS pic_big_id,
              pic3.ext AS pic_big_ext,
              pic3.title AS pic_big_name,
              pic3.width AS pic_big_width,
              pic3.height AS pic_big_height
            "; 
        }

        if(isset($site->vars['img_size2']['width']) && isset($site->vars['img_size2']['height'])){
            $sql .= " , 
              pic2.id AS pic_small_id,
              pic2.ext AS pic_small_ext,
              pic2.title AS pic_small_name,
              pic2.width AS pic_small_width,
              pic2.height AS pic_small_height
            "; 
        }

        if(isset($site->vars['img_size3']['width']) && isset($site->vars['img_size3']['height'])){
            $sql .= " , 
              pic1.id AS pic_mini_id,
              pic1.ext AS pic_mini_ext,
              pic1.title AS pic_mini_name,
              pic1.width AS pic_mini_width,
              pic1.height AS pic_mini_height
            ";
        }

        if(isset($site->vars['img_size4']['width']) && isset($site->vars['img_size4']['height'])){
            $sql .= " , 
              pic4.id AS pic_4_id,
              pic4.ext AS pic_4_ext,
              pic4.title AS pic_4_name,
              pic4.width AS pic_4_width,
              pic4.height AS pic_4_height
            ";
        }
			
		if($id == 0 && !empty($ar['ids'])){
			$sql_num_rows .= "                                            
				FROM
					products p 
				INNER JOIN pub_categs pc
					ON  pc.id_pub = p.id 
					AND pc.id_categ IN('".implode("','", $ar['ids'])."') 
					AND pc.`where_placed` = 'product' 
					";
					
			$sql .= "                                            
				FROM
					products p 
				INNER JOIN pub_categs pc
					ON  pc.id_pub = p.id 
					AND pc.id_categ IN ('".implode("','", $ar['ids'])."') 
					AND pc.`where_placed` = 'product'                 
					";              
		}elseif($id == 0){
			$sql_num_rows .= "                                            
				FROM
					products p 
				INNER JOIN pub_categs pc
					ON  pc.id_pub = p.id 
					AND pc.id_categ <> '0'
					AND pc.`where_placed` = 'product' 
					";
					
			$sql .= "                                            
				FROM
					products p 
				INNER JOIN pub_categs pc
					ON  pc.id_pub = p.id 
					AND pc.id_categ <> '0'
					AND pc.`where_placed` = 'product'                 
					";              
		}else{
			
			$sql_num_rows .= "                                            
				FROM
					products p 
				INNER JOIN pub_categs pc
					ON  pc.id_pub = p.id 
					AND pc.id_categ = '".$id."'
					AND pc.`where_placed` = 'product' 
					";
					
			$sql .= "                                            
				FROM
					products p 
				INNER JOIN pub_categs pc
					ON  pc.id_pub = p.id 
					AND pc.id_categ = '".$id."'
					AND pc.`where_placed` = 'product'                 
					";              
		}

		$sql .= "                                            
				INNER JOIN categs c
					ON  pc.id_categ = c.id                
				";
				
		$sql .= "                                            
				LEFT JOIN categs c2
					ON  c.id_parent = c2.id                
				";
				
		$sql .= "                                            
				LEFT JOIN categs c3
					ON  c2.id_parent = c3.id                
				";  
				
		$sql .= "                                            
				LEFT JOIN ( 
					SELECT id, COUNT(*) monthviews 
					FROM counter 
					WHERE `for_page` = 'product' AND `time` > DATE_ADD(NOW(), INTERVAL -30 DAY) 
					GROUP BY id) m on (m.id = p.id)
				";
		
		$sql .= "LEFT JOIN ( 
					SELECT record_id, COUNT(DISTINCT id_in_record) fotos_qty 
					FROM {$db->tables['uploaded_pics']} 
					WHERE record_type = 'product' 
					GROUP BY record_id 
				) u on (p.id = u.record_id)";
				
		$sql .= "LEFT JOIN ( 
					SELECT record_id, COUNT(*) files_qty 
					FROM {$db->tables['uploaded_files']}
					WHERE record_type = 'product' 
					GROUP BY record_id 
				) f on (f.record_id = p.id)";
				
		$sql .= "LEFT JOIN ( 
					SELECT record_id, COUNT(*) comments_qty 
					FROM {$db->tables['comments']}
					WHERE record_type = 'product' 
					GROUP BY record_id 
				) c on (c.record_id = p.id) ";
				
		$sql .= "LEFT JOIN ( 
					SELECT id_product, COUNT(*) pubs_qty 
					FROM pub_to_product 
					GROUP BY id_product 
				) ptp on (ptp.id_product = p.id) ";
				
		$sql .= "LEFT JOIN ( 
					SELECT product1, product2, COUNT(*) products_qty 
					FROM product_to_product  
					GROUP BY product1 
				) pp on (pp.product1 = p.id OR pp.product2 = p.id)";
				
        /* find out big picture */
        if(isset($site->vars['img_size3']['width']) && isset($site->vars['img_size3']['height'])){
            $sql .= "
				LEFT OUTER JOIN uploaded_pics pic3
					ON pic3.record_id = p.id 
					AND pic3.record_type = 'product' 
					AND pic3.id_in_record = '1' 
					AND (pic3.width = '".$site->vars['img_size3']['width']."' 
						OR pic3.height = '".$site->vars['img_size3']['height']."')
                  
            "; 
        }

        /* find out small picture */
        if(isset($site->vars['img_size2']['width']) && isset($site->vars['img_size2']['height'])){
            $sql .= "
				LEFT OUTER JOIN uploaded_pics pic2 
					ON pic2.record_id = p.id 
					AND pic2.record_type = 'product' 
					AND pic2.id_in_record = 1 
					AND (pic2.width = '".$site->vars['img_size2']['width']."' 
						OR pic2.height = '".$site->vars['img_size2']['height']."')                 
            "; 
        }

        /* find out mini picture */
        if(isset($site->vars['img_size1']['width']) && isset($site->vars['img_size1']['height'])){
            $sql .= "
				LEFT OUTER JOIN uploaded_pics pic1
					ON pic1.record_id = p.id 
					AND pic1.record_type = 'product' 
					AND pic1.id_in_record = 1 
					AND (pic1.width = '".$site->vars['img_size1']['width']."' 
						OR pic1.height = '".$site->vars['img_size1']['height']."')                 
            "; 
        }
                
        /* find out picture 4 */
        if(isset($site->vars['img_size4']['width']) && isset($site->vars['img_size4']['height'])){
			$sql .= "
				LEFT JOIN
				(SELECT record_id,id,ext,title,width,height
					FROM
					(
						SELECT *
						FROM {$db->tables['uploaded_pics']} p 
						WHERE ((width = '{$site->vars['img_size4']['width']}' 
							AND height = '{$site->vars['img_size4']['height']}') 
								
							OR (width = '{$site->vars['img_size4']['width']}' 
							AND height >= '{$site->vars['img_size4']['height']}') 
								
							OR (width >= '{$site->vars['img_size4']['width']}' 
							AND height = '{$site->vars['img_size4']['height']}') 
								
							OR (width < '{$site->vars['img_size4']['width']}' 
							AND height < '{$site->vars['img_size4']['height']}')) 
						
							AND record_type = 'product'
						
						ORDER by is_default DESC, id_in_record, width desc 
					) p GROUP BY record_id  
				) pic4 ON p.id = pic4.record_id  
				";
        }
        
        $sql_num_rows .= " 
			WHERE
				EXISTS (SELECT sc.id 
					FROM site_categ sc 
					WHERE sc.id_categ = pc.id_categ 
					AND sc.id_site = '".$site->vars['id']."') 
		";
				
        $sql .= " 
			WHERE
				EXISTS (SELECT sc.id 
					FROM site_categ sc 
					WHERE sc.id_categ = pc.id_categ 
					AND sc.id_site = '".$site->vars['id']."')  
		";

        if(!isset($_GET['debug'])){
            $sql .= " AND p.active = '1' ";
            $sql_num_rows .= " AND p.active = '1' 
			";
        } 
		
		if(!empty($ar['requested_filter']['price']['from'])){
			
			
			if(defined('SI_RATE_USD') && defined('SI_RATE_EURO')){ 				
				$sql .= " AND (
				IF(
					p.currency <> '".SI_CURRENCY."', 
					IF(p.currency = 'usd',
						p.price*".SI_RATE_USD.",
						IF(p.currency = 'euro',
							p.price*".SI_RATE_EURO.", 
							p.price
						)
					), 	
					p.price 	
				) >= '".$ar['requested_filter']['price']['from']."' 
				)
					";			
			
				$sql_num_rows .= " AND (
				IF(
					p.currency <> '".SI_CURRENCY."', 
					IF(p.currency = 'usd',
						p.price*".SI_RATE_USD.",
						IF(p.currency = 'euro',
							p.price*".SI_RATE_EURO.", 
							p.price
						)
					), 	
					p.price 	
				) >= '".$ar['requested_filter']['price']['from']."' 
				)
				";			
			}else{
				
				$sql .= " AND p.price >= '".$ar['requested_filter']['price']['from']."' ";
				$sql_num_rows .= " AND p.price >= '".$ar['requested_filter']['price']['from']."' 
				";
			}
			
		}
				
		if(!empty($ar['requested_filter']['price']['to'])){
			
			if(defined('SI_RATE_USD') && defined('SI_RATE_EURO')){ 				
				$sql .= " AND (
				IF(
					p.currency <> '".SI_CURRENCY."', 
					IF(p.currency = 'usd',
						p.price*".SI_RATE_USD.",
						IF(p.currency = 'euro',
							p.price*".SI_RATE_EURO.", 
							p.price
						)
					), 	
					p.price 	
				) <= '".$ar['requested_filter']['price']['to']."' 
				)
					";			
			
				$sql_num_rows .= " AND ( 
				IF(
					p.currency <> '".SI_CURRENCY."', 
					IF(p.currency = 'usd',
						p.price*".SI_RATE_USD.",
						IF(p.currency = 'euro',
							p.price*".SI_RATE_EURO.", 
							p.price
						)
					), 	
					p.price 	
				) <= '".$ar['requested_filter']['price']['to']."' 
				)
					";			
			}else{
				
				$sql .= " AND p.price <= '".$ar['requested_filter']['price']['to']."' ";
				$sql_num_rows .= " AND p.price <= '".$ar['requested_filter']['price']['to']."' 
				";
			}			
		}
		
		if(!empty($ar['requested_filter'])){
			foreach($ar['requested_filter'] as $k => $v){
				if(
					((is_string($v) && strlen($v) > 0) 
					|| !empty($v))
					/*&& $v != '0' */
					&& $k != 'price'
				){	
				
					if(is_array($v) && isset($v['from']) && isset($v['to'])){
						
						/* 
							если мин и макс совпадают с запрошенными, 
							то уберем это из условий 
						*/
						if(isset($v['min']) && isset($v['max'])
							&& $v['min'] == $v['from'] 
							&& $v['max'] == $v['to'] 
						){
							// equal!
						}else{
							
							$sql .= " AND (
								(EXISTS (SELECT ov.value FROM option_values ov 
									INNER JOIN options o ON o.id = ov.id_option
									WHERE o.alias = '".$k."' 
									AND ov.id_product = p.id 
									AND CAST(ov.value as DECIMAL(10,2)) >= '".$v['from']."'
									AND CAST(ov.value as DECIMAL(10,2)) <= '".$v['to']."'							
									) 
								)
							)";
							  
							$sql_num_rows .= " AND (
								(EXISTS (SELECT ov.value FROM option_values ov 
									INNER JOIN options o ON o.id = ov.id_option
									WHERE o.alias = '".$k."' 
									AND ov.id_product = p.id 
									AND CAST(ov.value as DECIMAL(10,2)) >= '".$v['from']."' 
									AND CAST(ov.value as DECIMAL(10,2)) <= '".$v['to']."' 								
									)								
								)
							)";
						}						
	
					}else{
				
						if(!is_array($v)){ 
							$v = array($v);							
						}
					
						$sql .= ' AND ( 
						';
						$sql_num_rows .= ' AND ( 
						';
						
						foreach($v as $k1 => $v1){
							$v1 = mb_strtoupper(trim($v1), $site->vars['site_charset']); 
							if($k1 > 0){
								$sql .= ' OR 
								';
								$sql_num_rows .= ' OR 
								'; 
							}							
							
							$sql .= " (EXISTS (SELECT ov.value FROM option_values ov 
								INNER JOIN options o ON o.id = ov.id_option
								WHERE o.alias = '".$k."' 
								AND ov.id_product = p.id 
								AND UPPER(ov.value) LIKE '%".$v1."%') 
							)";
						  
							$sql_num_rows .= " (EXISTS (SELECT ov.value FROM option_values ov 
								INNER JOIN options o ON o.id = ov.id_option
								WHERE o.alias = '".$k."' 
								AND ov.id_product = p.id 
								AND UPPER(ov.value) LIKE '%".$v1."%') 
							)";
						}
						
						$sql .= ' 
						) ';
						$sql_num_rows .= ' 
						) '; 						 
					}
				}
			}
		}	
		
        //$sql .= ' GROUP BY p.id ';
        
        /* GET-данные могут переназначить правило сортировки */
        if(!empty($_GET['orderby'])){
            $categ['sortby'] = $db->escape($_GET['orderby']);           
        }
		
        $sql .= ' 
		ORDER BY '; 
		
		if(!empty($ar['ids']) && is_array($ar['ids'])){
			$sql .= " FIELD(categ_id,'".implode("','", $ar['ids'])."'), ";

			$sql .= " 				
				CASE c.sortby WHEN 'price' THEN p.`price` END ASC,
				CASE c.sortby WHEN 'pricedesc' THEN p.`price` END DESC,
				CASE c.sortby WHEN 'date_insert' THEN p.date_insert END ASC,
				CASE c.sortby WHEN 'date_insert desc' THEN p.date_insert END DESC,
				CASE c.sortby WHEN 'views' THEN p.views END DESC,
				CASE c.sortby WHEN 'views desc' THEN p.views END DESC,
				CASE c.sortby WHEN 'monthviews' THEN monthviews END DESC,
				CASE c.sortby WHEN 'monthviews desc' THEN monthviews END DESC,
				CASE c.sortby WHEN 'name' THEN p.`name` END ASC , 	
			";
		}
		
		$sql .= " 
			c3.sort, c2.sort, c.sort, 
			c3.title, c2.title, c.title, 		
		";

        if(!empty($categ['sortby'])){
            if($categ['sortby'] == 'date_insert'){
                $sql .= ' p.date_insert ';
            }elseif($categ['sortby'] == 'name'){
                $sql .= ' p.`name` ';
            }elseif($categ['sortby'] == 'views desc' || $categ['sortby'] == 'views'){
                $sql .= ' p.views desc ';
            }elseif($categ['sortby'] == 'monthviews desc' || $categ['sortby'] == 'monthviews'){
                $sql .= ' monthviews desc ';
            }else{
                $sql .= ' p.date_insert desc ';
            }
        }else{
            $sql .= ' p.date_insert desc ';
        }
              
        if(!isset($_GET['all'])){
            $sql .= ' 
			LIMIT '.$current_page*$limit.', '.$limit.' 
			';
        }
		
		$all_results = $db->get_var($sql_num_rows);
		if($limit < ONPAGE && $all_results > $limit){ $all_results = $limit; }
        $site->page['all_products'] = $all_results;
		if($all_results == 0){ return array(); }

        $rows = $db->get_results($sql, ARRAY_A);		
		//$db->debug(); exit;
        if(!$rows || $db->num_rows == 0){ return array(); }
                        
        $items = $all = array();

        foreach($rows as $row){
            $pic1 = array();
            $pic1['id'] = $row['pic_mini_id'];   
			$pic1['title'] = $row['pic_mini_name'];
            $pic1['ext'] = $row['pic_mini_ext'];            
            $pic1['width'] = $row['pic_mini_width'];            
            $pic1['height'] = $row['pic_mini_height'];
            $pic1['url'] = !empty($row['pic_mini_id']) && !empty($row['pic_mini_ext']) 
                ? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic_mini_id'].'.'.$row['pic_mini_ext']
                : '';            
            
            $pic2 = array();
            $pic2['id'] = $row['pic_small_id'];           
			$pic2['title'] = $row['pic_small_name'];
            $pic2['ext'] = $row['pic_small_ext'];            
            $pic2['width'] = $row['pic_small_height'];            
            $pic2['height'] = $row['pic_small_height'];
            $pic2['url'] = !empty($row['pic_small_id']) && !empty($row['pic_small_ext']) 
                ? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic_small_id'].'.'.$row['pic_small_ext']
                : ''; 
			if(empty($row['pic_small_id'])){
				$pic2 = $pic1;
			}

            $pic3 = array();
            $pic3['id'] = $row['pic_big_id'];           
			$pic3['title'] = $row['pic_big_name'];
            $pic3['ext'] = $row['pic_big_ext'];            
            $pic3['width'] = $row['pic_big_width'];            
            $pic3['height'] = $row['pic_big_height'];
            $pic3['url'] = !empty($row['pic_big_id']) && !empty($row['pic_big_ext']) 
                ? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic_big_id'].'.'.$row['pic_big_ext']
                : '';            
			if(empty($row['pic_big_id'])){
				$pic3 = $pic2;
			}

			$pic4 = array();
			if(!empty($row['pic_4_id']) && !empty($row['pic_4_ext'])){
				
				$pic4['id'] = $row['pic_4_id'];           
				$pic4['title'] = $row['pic_4_name'];
				$pic4['ext'] = $row['pic_4_ext'];            
				$pic4['width'] = $row['pic_4_width'];            
				$pic4['height'] = $row['pic_4_height'];
				$pic4['url'] = !empty($row['pic_4_id']) && !empty($row['pic_4_ext']) 
					? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic_4_id'].'.'.$row['pic_4_ext']
					: '';  
			}
                      
			if(empty($row['pic_4_id'])){
				$pic4 = $pic3;
			}
			
            $row['pic'] = array(
                '1' => $pic1,
                '2' => $pic2, 
                '3' => $pic3,
				'4' => $pic4
            );
            
            unset($row['pic_big_id']);
            unset($row['pic_big_ext']);
            unset($row['pic_big_name']);
            unset($row['pic_big_width']);
            unset($row['pic_big_height']);
            unset($row['pic_small_id']);
            unset($row['pic_small_ext']);
            unset($row['pic_small_name']);
            unset($row['pic_small_width']);
            unset($row['pic_small_height']);
            unset($row['pic_mini_id']);
            unset($row['pic_mini_ext']);
            unset($row['pic_mini_name']);
            unset($row['pic_mini_width']);
            unset($row['pic_mini_height']);			
            unset($row['pic_4_id']);
            unset($row['pic_4_ext']);
            unset($row['pic_4_name']);
            unset($row['pic_4_width']);
            unset($row['pic_4_height']);			
			$price_ar = price_recalculate($row['price'], $row['currency']);
			$price_old_ar = price_recalculate($row['price_spec'], $row['currency']);
			
			$row['price'] = $price_ar['price'];
			$row['currency'] = $price_ar['currency'];
			$row['price_old'] = $price_old_ar['price'];
			
            $row['link'] = $site->vars['site_url'].'/'.$row['alias'].'/';
            $row['link_idn'] = $site->uri['idn'].'/'.$row['alias'].'/';
			
            $row['date'] = date($site->vars['site_date_format'], strtotime($row['date_insert']));
			$row['time'] = date($site->vars['site_time_format'], strtotime($row['date_insert'])); 
			
            //$row['options'] = options_in_list();
            unset($row['price_spec']);
            $all[] = "'".$row['id']."'";
			
            /* отформатируем цену и старую цену */
			$row['price_formatted'] = price_formatted($row['price'], $row['currency']);
			$row['price_old_formatted'] = price_formatted($row['price_old'], $row['currency']);			

			ksort($row);
			//$site->print_r($row,1);
			
            $items[] = $row;
        }

		$options = array();	
		$options = options_in_list($all, 'product', $site); 			
        if($all_results > count($items)){			
            $pages = $site->set_pages($all_results, 'products');
        }else{ $pages = ''; }

        return array(
            'spec' => $spec,
            'list' => $items,
            'all' => $all_results,
            'pages' => $pages,
            'options' => $options
        );
    }

?>