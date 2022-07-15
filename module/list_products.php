<?php
    /*
        list_products
        last modified 05.08.2019
        Список товаров в виде массива
        с разбивкой на страницы или выводом всех записей,
        если так задано get=all  
		$id['ids'] = id's of categs * then $id['id'] = 0
		29.01.2018
		$id['sort_in_categs'] = при запросе товаров из подразделов -
		сортировать внутри разделов или все вместе
		18.03.2018
		+ учет сортировки при выводе с вложенными подразделами
		+ учет кол-ва товаров константой SELECTION_PAGE_ROWS
		+ сортировка при выборке с вложенными разделами
		+ 04.05.2018 correct product to product connections
		+ 11.04.2019 added orderby in tpl
		+ 16.04.2019 fixes
		+ 07.05.2019 $from_tpl added
		+ 20.05.2019 qty from template or 1
		+ 27.06.2019 list_files, list_photos + alias in tpl
		+ 05.08.2019 https
		+ 11.10.2019 added complectation as real_title
    */
    
    function list_products($id, $where, $site, $spec='all', $limit=ONPAGE, $orderby='', $from_tpl='')
    {
        global $db, $tpl;        
		if(!empty($id['pid']) && $where != 'pub'){ $pid = $id['pid']; }
		if(!empty($id['ids']) && is_array($id['ids'])){ 
			$ids = implode("','", $id['ids']);		
			$idsArray = $id['ids'];
		}
		
		if(!empty($id['qtys']) && is_array($id['qtys'])){ 
			$qtys = implode("','", $id['qtys']);
			$qtysArray = $id['qtys'];
		}
		
		if(!empty($idsArray) AND !empty($qtysArray)){
			$idsArray = array_flip($idsArray);
			foreach($idsArray as $k => $v){
				$id_qty = isset($qtysArray[$v]) ? $qtysArray[$v] : 1;
				$idsArray[$k] = $id_qty;
			}
		}
		
		if(!empty($id['list_files_on'])){ 
			$list_files_on = 1; 
		}elseif(!empty($site->vars['sys_list_files_on'])){
			$list_files_on = 1; 
		}
		
		if(!empty($id['list_photos_on'])){ 
			$list_photos_on = 1; 
		}elseif(!empty($site->vars['sys_list_photos_on'])){
			$list_photos_on = 1; 
		}
				
		if(!empty($id['debug'])){ $debug = 1; }
		if(empty($site->vars['id'])){ 
			if(!empty($tpl->_vars['site'])){
				$site = new \stdClass();
				$site->vars = $tpl->_vars['site'];
				$site->uri = array();
				$site->uri['scheme'] = $tpl->_vars['site']['site_scheme'];
				$site->uri['host'] = $tpl->_vars['site']['site_domain'];
			}else{				
				return ''; 
			}			
		}
		
		if($spec == 'connected' && !empty($site->vars['sys_qty_connected'])){
			$limit = $site->vars['sys_qty_connected'];
		}
		
		if(is_array($id)){
			$categ = $id;
			$id = isset($categ['id']) ? intval($categ['id']) : 0;
			$alias = isset($categ['alias']) ? $categ['alias'] : '';
		}else{
			$categ = $id;
		}
		
        /* if $spec == 'connected' */
        /* составим массив связанных товаров и выведем их */
        if($spec == 'connected' && $where == 'product' && empty($ids)){
            $query = "SELECT 
						ptp.*, 
						p1.name as title1, 
						p2.name as title2 
                FROM ".$db->tables['product_to_product']." ptp 
                    LEFT JOIN ".$db->tables['products']." p1 on (p1.id = ptp.product1)
                    LEFT JOIN ".$db->tables['products']." p2 on (p2.id = ptp.product2)
                WHERE
                    (ptp.product1 = '".$id."' AND ptp.product2 <> '".$id."' 
					AND ptp.product2 > '0' AND p2.active = '1')
                     OR  
                    (ptp.product2 = '".$id."' AND ptp.product1 <> '".$id."' 
					AND ptp.product1 > '0' AND p1.active = '1') ";
            $rows = $db->get_results($query);
			if(!empty($db->last_error)){
				$str = '<p><b>File:</b> '.__FILE__.'<br><b>Function:</b> '.__METHOD__.'<br><b>Line:</b> '.__LINE__.'</p>';
				return $site->db_error($str);
			}
            if($db->num_rows == 0){ return ''; }
            $ar = array();
            foreach($rows as $row){
            
              if($row->product1 != $id){
                $ar[] = $row->product1;
              }else{
                $ar[] = $row->product2;
              }
            }
            $ids = implode("','", $ar);
            $id = 0;

        }elseif($spec == 'connected' && $where == 'pub' && empty($ids)){
            $query = "SELECT p.*  
                FROM 
                    ".$db->tables['pub_to_product']." ptp,  
                    ".$db->tables['products']." p  
                WHERE
                    ptp.id_pub = '".$id."' 
                    AND ptp.id_product = p.id 
					";
            $rows = $db->get_results($query);
			if(!empty($db->last_error)){
				$str = '<p><b>File:</b> '.__FILE__.'<br><b>Function:</b> '.__METHOD__.'<br><b>Line:</b> '.__LINE__.'</p>';
				return $site->db_error($str);
			}
            if($db->num_rows == 0){ return ''; }
            $ar = array();
            foreach($rows as $row){            
                $ar[] = $row->id;
            }			
            $ids = implode("','", $ar);
            $id = 0;
        }elseif($spec == 'visited'){
            $v_ar = isset($_SESSION['visited']['products']) ? $_SESSION['visited']['products'] : array(); 
            $ar = array();
            $i = count($v_ar) > $limit ? count($v_ar)-$limit : 0;
            if($i > 0){
                foreach($v_ar as $k=>$v){
                    if($k < $i){
                        unset($v_ar[$k]);
                    }
                }               
            }  

            foreach($v_ar as $row){            
                $ar[] = "'".$row."'";
            }
            $ids = implode("','", $ar);
            $id = 0;
			
        }elseif($spec == 'wishlist'){
			
			if(!empty($_SESSION['wishlist']['products'])){
				$ids = implode("','", $_SESSION['wishlist']['products']);
				$id = 0;
			}else{
				return '';
			}
			
		}elseif($spec == 'compare'){
			$compare_ar = !empty($_GET['ids']) ? $_GET['ids'] : '';
			if(empty($compare_ar)){
				$compare_ar = !empty($_POST['ids']) ? $_POST['ids'] : '';
			}
			if(empty($compare_ar)){ return ''; }
            $ids = implode("','", $compare_ar);
            $id = 0;
        }elseif($spec == 'basket'){
			if(!empty($_SESSION['basket'])){
				$ids = array();
				foreach($_SESSION['basket'] as $k => $v){
					$ids[] = $k;					
				}
				$ids = implode("','", $ids);
			}else{ return ''; }
			if(empty($ids)){ return ''; }		
        }
		
        $current_page = isset($site->uri['params']['page']) ? intval($site->uri['params']['page']) : 0;        
        $sql_num_rows = ' SELECT count(DISTINCT p.`id`) ';
         
        $sql = "SELECT 
				p.id, p.name as title, p.name_short as title_short, p.memo_short as intro, 
                p.comment, p.barcode, p.memo as content,  
				p.complectation as real_title, 
                p.currency, p.price, p.price_spec, p.price_period,                  
                p.accept_orders, p.active,   
                p.date_insert, p.date_update, p.total_qty,   
                p.alias, p.id_next_model, p.treba as id_required, p.present_id as id_gift,  
                p.user_id, p.weight_deliver, p.bid_ya, 
                p.f_spec, p.f_new, p.views, 
				sc.id_site, pc.id_categ, 
				cat.id as categ_id, 
				cat.title as categ_title, 
				c2.title as categ2_title, 
				c2.id as categ2_id, 
				c3.title as categ3_title, 
				c3.id as categ3_id, 
				c3.alias as categ3_alias, 
				cat.alias as categ_alias, 
				c2.alias as categ2_alias, 
                
                IFNULL(m.monthviews,0) as monthviews,
                IFNULL(u.fotos_qty,0) as fotos_qty,
                IFNULL(f.files_qty,0) as files_qty,
                IFNULL(c.comments_qty,0) as comments_qty  , 
				IFNULL(ptp.pubs_qty,0) as pubs_qty  , 
				IFNULL(pp.products_qty,0) as products_qty  ";
						  
        if(isset($site->vars['img_size1']['width']) && isset($site->vars['img_size1']['height'])){
            $sql .= " , 
              pic1.id AS pic1_id,
              pic1.ext AS pic1_ext,
              pic1.title AS pic1_name,
              pic1.width AS pic1_width,
              pic1.height AS pic1_height
            "; 
        }

        if(isset($site->vars['img_size2']['width']) && isset($site->vars['img_size2']['height'])){
            $sql .= " , 
              pic2.id AS pic2_id,
              pic2.ext AS pic2_ext,
              pic2.title AS pic2_name,
              pic2.width AS pic2_width,
              pic2.height AS pic2_height
            "; 
        }

        if(isset($site->vars['img_size3']['width']) && isset($site->vars['img_size3']['height'])){
            $sql .= " , 
              pic3.id AS pic3_id,
              pic3.ext AS pic3_ext,
              pic3.title AS pic3_name,
              pic3.width AS pic3_width,
              pic3.height AS pic3_height
            ";
        }

        if(isset($site->vars['img_size4']['width']) && isset($site->vars['img_size4']['height'])){
            $sql .= " , 
              pic4.id AS pic4_id,
              pic4.ext AS pic4_ext,
              pic4.title AS pic4_name,
              pic4.width AS pic4_width,
              pic4.height AS pic4_height
            ";
        }
		
        $sql_num_rows .= "                                            
            FROM 
                (
                ".$db->tables['products']." p, 
                ".$db->tables['pub_categs']." pc, 
                ".$db->tables['site_categ']." sc,
                ".$db->tables['categs']." cat 
				)
                ";
                
        $sql .= "                                            
            FROM 
                (
                ".$db->tables['products']." p, 
                ".$db->tables['pub_categs']." pc, 
                ".$db->tables['site_categ']." sc, 
                ".$db->tables['categs']." cat 
                )
                ";              
						  
        /* find out picture 1 */
        if(isset($site->vars['img_size1']['width']) && isset($site->vars['img_size1']['height'])){
            $sql .= "
				LEFT JOIN
				(SELECT record_id,id,ext,title,width,height
					FROM
					(
						SELECT *
						FROM {$db->tables['uploaded_pics']} p 
						WHERE ((width = '{$site->vars['img_size1']['width']}' 
							AND height = '{$site->vars['img_size1']['height']}') 
							
							OR (width = '{$site->vars['img_size1']['width']}' 
							AND height >= '{$site->vars['img_size1']['height']}') 
							
							OR (width >= '{$site->vars['img_size1']['width']}' 
							AND height = '{$site->vars['img_size1']['height']}')) 
								
							AND record_type = 'product'
						
						ORDER by is_default DESC, id_in_record, width desc
					) p GROUP BY record_id   
				) pic1 ON p.id = pic1.record_id  ";
        }

        /* find out picture 2 */
        if(isset($site->vars['img_size2']['width']) && isset($site->vars['img_size2']['height'])){
			$sql .= "
				LEFT JOIN
				(SELECT record_id,id,ext,title,width,height
					FROM
					(
						SELECT *
						FROM {$db->tables['uploaded_pics']} p 
						WHERE ((width = '{$site->vars['img_size2']['width']}' 
							AND height = '{$site->vars['img_size2']['height']}') 
							
							OR (width = '{$site->vars['img_size2']['width']}' 
							AND height >= '{$site->vars['img_size2']['height']}') 
								
							OR (width >= '{$site->vars['img_size2']['width']}' 
							AND height = '{$site->vars['img_size2']['height']}')) 
								
							AND record_type = 'product'
					
						ORDER by is_default DESC, id_in_record, width desc 
					) p GROUP BY record_id   
				) pic2 ON p.id = pic2.record_id  ";

        }

        /* find out picture 3 */
        if(isset($site->vars['img_size3']['width']) && isset($site->vars['img_size3']['height'])){
			$sql .= "
				LEFT JOIN
				(SELECT record_id,id,ext,title,width,height
					FROM
					(
						SELECT *
						FROM {$db->tables['uploaded_pics']} p 
						WHERE ((width = '{$site->vars['img_size3']['width']}' 
							AND height = '{$site->vars['img_size3']['height']}') 
								
							OR (width = '{$site->vars['img_size3']['width']}' 
							AND height >= '{$site->vars['img_size3']['height']}') 
								
							OR (width >= '{$site->vars['img_size3']['width']}' 
							AND height = '{$site->vars['img_size3']['height']}') 
								
							OR (width < '{$site->vars['img_size3']['width']}' 
							AND height < '{$site->vars['img_size3']['height']}')) 
						
							AND record_type = 'product'
						
						ORDER by is_default DESC, id_in_record, width desc 
					) p GROUP BY record_id   
				) pic3 ON p.id = pic3.record_id  ";        }
				
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
				
		$sql .= "LEFT JOIN ( 
					SELECT id, COUNT(*) monthviews 
					FROM counter 
					WHERE `for_page` = 'product' AND `time` > DATE_ADD(NOW(), INTERVAL -30 DAY) 
					GROUP BY id) m on (m.id = p.id)";

		$sql .= "LEFT JOIN ".$db->tables['categs']." c2 ON (cat.id_parent = c2.id) ";
		$sql .= "LEFT JOIN ".$db->tables['categs']." c3 ON (c2.id_parent = c3.id) ";
		
			
		if(!empty($alias)){
			
		  $sql_num_rows .= " 
              WHERE 
                  p.id = pc.id_pub 
                  AND pc.id_categ = cat.id 
				  AND cat.alias = '".$alias."'
                  AND pc.`where_placed` = 'product' 
                  AND pc.id_categ = sc.id_categ
				  AND cat.`id` = sc.id_categ 
                  AND sc.id_site = '".$site->vars['id']."'
				  ";
  
          $sql .= " 
              WHERE 
                  p.id = pc.id_pub 
                  AND pc.id_categ = cat.id 
				  AND cat.alias = '".$alias."'
                  AND pc.`where_placed` = 'product' 
                  AND pc.id_categ = sc.id_categ
				  AND cat.`id` = sc.id_categ 
                  AND sc.id_site = '".$site->vars['id']."'
				  ";
		}elseif($id == 0){
          $sql_num_rows .= " 
              WHERE 
                  p.`id` = pc.`id_pub` 
                  AND pc.`id_categ` <> '0'
                  AND pc.`where_placed` = 'product' 
                  AND pc.`id_categ` = sc.`id_categ`
                  AND cat.`id` = sc.`id_categ` ";
				  
			if(empty($debug)){
				$sql_num_rows .= " 
				  AND sc.`id_site` = '".$site->vars['id']."' 				  
				  ";
			}
				  
			if(!empty($pid) && $spec != 'connected'){
				$sql_num_rows .= " 
				  AND p.id = '".$pid."' 				  
				  ";
			}
                
          $sql .= " 
              WHERE 
                  p.id = pc.id_pub 
                  AND pc.id_categ <> '0'
                  AND pc.`where_placed` = 'product' 
                  AND pc.id_categ = sc.id_categ
				  AND cat.`id` = sc.id_categ 
				  ";
                              
			if(empty($debug)){
				$sql .= " 
				  AND sc.`id_site` = '".$site->vars['id']."' 				  
				  ";
			}
				  
			if(!empty($pid) && $spec != 'connected'){
				$sql .= " 
				  AND p.id = '".$pid."' 				  
				  ";
			}
				  
          if($spec == 'spec'){
              $sql_num_rows .= " AND p.f_spec = '1' ";
              $sql .= " AND p.f_spec = '1' ";  
              if(!empty($site->uri['alias'])){
                $sql_num_rows .= " AND p.alias != '".$site->uri['alias']."' ";
                $sql .= " AND p.alias != '".$site->uri['alias']."' ";  
              }			  
		  }elseif($spec == 'new'){
              $sql_num_rows .= " AND p.f_new = '1' ";
              $sql .= " AND p.f_new = '1' ";  
          }elseif(($spec == 'connected' || $spec == 'visited' 
				|| $spec == 'wishlist' || $spec == 'basket' 
				|| $spec == 'compare') && !empty($ids)
		  ){
			  $sql_num_rows .= " AND p.id IN ('".$ids."')  ";
              $sql .= " AND p.id IN ('".$ids."') ";
          }elseif(!empty($ids)){
			  $sql_num_rows .= " AND cat.id IN ('".$ids."') ";
              $sql .= " AND cat.id IN ('".$ids."') ";  
              if(!empty($site->uri['alias'])){
                $sql_num_rows .= " AND p.alias != '".$site->uri['alias']."' ";
                $sql .= " AND p.alias != '".$site->uri['alias']."' ";  
              }		
          }elseif($spec == 'search' && !empty($site->uri['params']['q'])){

			  $q = $db->escape(mb_strtolower($site->uri['params']['q'], 'UTF-8'));
              $sql_num_rows .= " AND (LOWER(p.alias) LIKE '%".$q."%' 
                OR LOWER(p.`name`) LIKE '%".$q."%'
                OR LOWER(p.`name_short`) LIKE '%".$q."%'
                OR LOWER(p.`memo`) LIKE '%".$q."%'                
                OR LOWER(p.`memo_short`) LIKE '%".$q."%'                
                OR LOWER(p.`barcode`) LIKE '%".$q."%'                
                OR LOWER(p.`alias`) LIKE '%".$q."%'                
                OR LOWER(p.`alter_search`) LIKE '%".$q."%'                
                ) ";
                        
              $sql .= " AND (LOWER(p.alias) LIKE '%".$q."%' 
                OR LOWER(p.`name`) LIKE '%".$q."%'
                OR LOWER(p.`name_short`) LIKE '%".$q."%'
                OR LOWER(p.`memo`) LIKE '%".$q."%'                
                OR LOWER(p.`memo_short`) LIKE '%".$q."%'                
                OR LOWER(p.`barcode`) LIKE '%".$q."%'                
                OR LOWER(p.`alias`) LIKE '%".$q."%'                
                OR LOWER(p.`alter_search`) LIKE '%".$q."%'                
                ) ";
          } 

        }else{
        
          $sql_num_rows .= " 
              WHERE 
                  p.id = pc.id_pub 
                  AND pc.id_categ = '".$id."'
                  AND pc.`where_placed` = 'product' 
                  AND pc.id_categ = sc.id_categ
				  AND cat.`id` = sc.id_categ 
                  AND sc.id_site = '".$site->vars['id']."'
				  ";
  
          $sql .= " 
              WHERE 
                  p.id = pc.id_pub 
                  AND pc.id_categ = '".$id."'
                  AND pc.`where_placed` = 'product' 
                  AND pc.id_categ = sc.id_categ
				  AND cat.`id` = sc.id_categ 
                  AND sc.id_site = '".$site->vars['id']."'
				  ";
        }

        if(!isset($_GET['debug']) && empty($categ['debug'])){
            $sql .= " AND p.active = '1' 
			";
            $sql_num_rows .= " AND p.active = '1' 
			";
			
			$sql .= " AND p.date_insert < CONCAT(CURDATE(), ' ',CURTIME()) ";
			
			$sql_num_rows .= " AND p.date_insert < CONCAT(CURDATE(), ' ',CURTIME()) ";				
        } 
             
        $sql .= " GROUP BY p.id 
		";
        
        /* GET-данные могут переназначить правило сортировки */
        if(!empty($_GET['orderby'])){
            $categ['sortby'] = $db->escape($_GET['orderby']);           
        }
        
        $sql .= ' 
			ORDER BY 			
		'; 
				
		if(!empty($categ['sort_in_categs']) && !empty($ids)){
			$sql .= " FIELD(categ_id, '".$ids."'),  ";			
		}
	
		if(!empty($categ['sort_in_categs'])){
			//$sql .= ' c3.sort, c2.sort, cat.sort,
			//	c3.title, c2.title, cat.title, ';
			$sql .= " 				
				CASE cat.sortby WHEN 'price' THEN p.`price` END ASC,
				CASE cat.sortby WHEN 'pricedesc' THEN p.`price` END DESC,
				CASE cat.sortby WHEN 'date_insert' THEN p.date_insert END ASC,
				CASE cat.sortby WHEN 'date_insert desc' THEN p.date_insert END DESC,
				CASE cat.sortby WHEN 'views' THEN p.views END DESC,
				CASE cat.sortby WHEN 'views desc' THEN p.views END DESC,
				CASE cat.sortby WHEN 'monthviews' THEN monthviews END DESC,
				CASE cat.sortby WHEN 'monthviews desc' THEN monthviews END DESC,
				CASE cat.sortby WHEN 'name' THEN p.`name` END ASC , 	
			";
		}
		
		if($spec == 'connected'){
			$sql .= ' c3.sort, c3.title, c2.sort, c2.title, cat.sort, cat.title, p.`name` ';
		}elseif(!empty($categ['sortby'])){
            if($categ['sortby'] == 'date_insert'){
                $sql .= ' p.date_insert ';

            }elseif($categ['sortby'] == 'price'){
                $sql .= ' p.`price` ';
            }elseif($categ['sortby'] == 'pricedesc'){
                $sql .= ' p.`price` DESC '; 
			}elseif($categ['sortby'] == 'name'){
                $sql .= ' p.`name` ';
            }elseif($categ['sortby'] == 'views desc' || $categ['sortby'] == 'views'){
                $sql .= ' p.views desc ';
            }elseif($categ['sortby'] == 'monthviews desc' || $categ['sortby'] == 'monthviews'){
                $sql .= ' monthviews desc ';
            }else{
                $sql .= ' p.date_insert desc ';
            }
		}elseif(!empty($orderby) 
			&& in_array($orderby, array('bid_ya','price','title', 'bid_ya_desc','price_desc','title_desc'))
		){
			$asc = strpos($orderby, '_desc') !== false ? 'desc' : 'asc';
			$orderby = str_replace('_desc','',$orderby);
			$sql .= ' p.'.$orderby.' '.$asc.' ';
        }else{
            $sql .= ' p.date_insert desc ';
        }
        $sql .= '
		';  

        if(!isset($_GET['all']) && empty($from_tpl)){
            $sql .= ' LIMIT '.$current_page*$limit.', '.$limit;
        }elseif(!empty($from_tpl)){
			$sql .= ' LIMIT 0, '.$limit;
		}      
          
		if($spec == 'basket' || $spec == 'wishlist'){
			$site->stop_db_cache();
		}
        
		$all_results = $db->get_var($sql_num_rows);		
		//$db->debug(); exit;
		
		if(!empty($db->last_error)){
			$str = '<p><b>File:</b> '.__FILE__.'<br><b>Function:</b> '.__METHOD__.'<br><b>Line:</b> '.__LINE__.'</p>';
			return $site->db_error($str);
		}
			
		if($all_results == 0){ return ''; }
        //if($limit < ONPAGE && $all_results > $limit){ $all_results = $limit; }
        $rows = $db->get_results($sql, ARRAY_A);
		//$db->debug(); exit;
		
		if(!empty($db->last_error)){
			$str = '<p><b>File:</b> '.__FILE__.'<br><b>Function:</b> '.__METHOD__.'<br><b>Line:</b> '.__LINE__.'</p>';
			return $site->db_error($str);
		}
		
		if($spec != 'last' && $spec != 'spec'){
			$site->page['all_products'] = $all_results;
			$site->page['all_results'] = $all_results;
		}

		if($spec == 'basket' || $spec == 'wishlist'){
			$site->start_db_cache();
		}
		
		if($spec == 'connected'){
		}

		if($spec == 'compare'){
		}
        if(!$rows || $db->num_rows == 0){ return ''; }
		
        if($all_results > $db->num_rows && $spec == 'all'){
            $pages = $site->set_pages($all_results, 'products');
        }else{ $pages = ''; }
                
        $items = $all = array();
		$price_min = 0;
		$price_max = 0;
		$ids = array();

        foreach($rows as $krow => $row){
            $pic1 = array();
			if(!empty($row['pic1_id']) && !empty($row['pic1_ext'])){				
				$pic1['id'] = $row['pic1_id'];           
				$pic1['ext'] = $row['pic1_ext'];            
				$pic1['width'] = $row['pic1_width'];            
				$pic1['title'] = $row['pic1_name'];            
				$pic1['height'] = $row['pic1_height'];
				$pic1['url'] = !empty($row['pic1_id']) && !empty($row['pic1_ext']) 
					? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic1_id'].'.'.$row['pic1_ext']
					: '';            
			}
            
            $pic2 = array();
			if(!empty($row['pic2_id']) && !empty($row['pic2_ext'])){				
				$pic2['id'] = $row['pic2_id'];           
				$pic2['ext'] = $row['pic2_ext'];            
				$pic2['width'] = $row['pic2_width'];            
				$pic2['title'] = $row['pic2_name'];            
				$pic2['height'] = $row['pic2_height'];
				$pic2['url'] = !empty($row['pic2_id']) && !empty($row['pic2_ext']) 
					? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic2_id'].'.'.$row['pic2_ext']
					: '';            
			}else{ $pic2 = $pic1; }

            $pic3 = array();
			if(!empty($row['pic3_id']) && !empty($row['pic3_ext'])){
				$pic3['id'] = $row['pic3_id'];           
				$pic3['ext'] = $row['pic3_ext'];            
				$pic3['title'] = $row['pic3_name'];            
				$pic3['width'] = $row['pic3_width'];            
				$pic3['height'] = $row['pic3_height'];
				$pic3['url'] = !empty($row['pic3_id']) && !empty($row['pic3_ext']) 
					? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic3_id'].'.'.$row['pic3_ext']
					: '';
			}else{ $pic3 = $pic2; }

            $pic4 = array();
			if(!empty($row['pic4_id']) && !empty($row['pic4_ext'])){
				$pic4['id'] = $row['pic4_id'];           
				$pic4['ext'] = $row['pic4_ext'];            
				$pic4['title'] = $row['pic4_name'];            
				$pic4['width'] = $row['pic4_width'];            
				$pic4['height'] = $row['pic4_height'];
				$pic4['url'] = !empty($row['pic4_id']) && !empty($row['pic4_ext']) 
					? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic4_id'].'.'.$row['pic4_ext']
					: '';  
			}else{ $pic4 = $pic3; }

            $row['pic'] = array(
                '1' => $pic1,
                '2' => $pic2, 
                '3' => $pic3, 
                '4' => $pic4 
            );
            
            unset($row['pic1_id']);
            unset($row['pic1_ext']);
            unset($row['pic1_name']);
            unset($row['pic1_width']);
            unset($row['pic1_height']);
            unset($row['pic2_id']);
            unset($row['pic2_ext']);
            unset($row['pic2_name']);
            unset($row['pic2_width']);
            unset($row['pic2_height']);
            unset($row['pic3_id']);
            unset($row['pic3_ext']);
            unset($row['pic3_name']);
            unset($row['pic3_width']);
            unset($row['pic3_height']);
			
            unset($row['pic4_id']);
            unset($row['pic4_ext']);
            unset($row['pic4_name']);
            unset($row['pic4_width']);
            unset($row['pic4_height']);

			$price_ar = price_recalculate($row['price'], $row['currency']);
			$price_old_ar = price_recalculate($row['price_spec'], $row['currency']);

			$row['price'] = $price_ar['price'];
			$row['currency'] = $price_ar['currency'];
			$row['price_old'] = $price_old_ar['price'];
			
			// qty
			if(!empty($idsArray[$row['id']])){
				$row['qty'] = $idsArray[$row['id']];
			}else{
				$row['qty'] = 1;
			}
			
			// files > 0
			if($row['files_qty'] > 0 && !empty($list_files_on)){
				$row['list_files'] = list_files('product', $row['id'], $site);
			}
			
			// pics > 1 and list_products_pics_qty 
			if($row['fotos_qty'] > 1 && !empty($list_photos_on)){
				$row['list_photos'] = list_photos('product', $row['id'], $site);
			}
            
            $row['link'] = $site->vars['site_url'].'/'.$row['alias'].'/';
			if(!isset($site->uri['idn'])){
				$row['link_idn'] = $row['link'];
			}else{
				$row['link_idn'] = $site->uri['idn'].'/'.$row['alias'].'/';
			}
			
			$row['link_basket'] = $site->vars['site_url'].'/basket/?add='.$row['id'].'&qty='.$row['qty'];
            
			$row['date'] = date($site->vars['site_date_format'], strtotime($row['date_insert'])); 
			$row['time'] = date($site->vars['site_time_format'], strtotime($row['date_insert'])); 
            //$row['options'] = options_in_list();
			
            unset($row['price_spec']);
            $all[] = "'".$row['id']."'";
			
			$row['price_formatted'] = price_formatted($row['price'], $row['currency']);
			$row['price_old_formatted'] = price_formatted($row['price_old'], $row['currency']);
			
            $row['clear'] = 1;
			if(isset($site->check_forms_in_content)){
				$row = $site->check_forms_in_content($row);
			}
			
			ksort($row);
            $items[] = $row;
			
			if($krow == 0){
				$price_min = $row['price'];
			}
			
			if($row['price'] > $price_max){
				$price_max = $row['price'];
			}
			
			if($row['price'] < $price_min){
				$price_min = $row['price'];
			}
			
			$ids[] = $row['id'];
			
        }
		
        //$options = options_in_list($all, 'product', $site);
		$options = array();	
		if($spec == 'compare'){
			if(!empty($compare_ar)){
				foreach($compare_ar as $v){
					$options[$v] = list_product_options($v, 'product', $site);
					unset($options[$v]['in_list']);
				}
			}
		}else{
			$options = options_in_list($all, 'product', $site); 			
		}
		
		foreach($items as $k => $v){
			if(isset($options[$v['id']]['list'])){
				$items[$k]['options'] = $options[$v['id']]['list'];
			}
		}
		
        return array(
            'spec' => $spec,
            'list' => $items,
			'price_min' => $price_min,
			'price_max' => $price_max,
			'ids' => $ids,
            'all' => $all_results,
            'pages' => $pages,
            'options' => $options
        );
    }
	
	
	function list_products_tpl($ar=NULL){
		global $site, $tpl;
		
		if(empty($ar["where"])){ $ar['where'] = 'product'; }
		if(empty($ar["id"])){ $ar['id'] = 0; }
		if(empty($ar["qtys"])){ $ar['qtys'] = ''; }
		if(empty($ar["spec"])){ $ar['spec'] = 'all'; }
		if(empty($ar["limit"])){ $ar['limit'] = ONPAGE; }
		if(empty($ar["orderby"])){ $ar['orderby'] = ''; }
		if(empty($ar["ids"])){ $ar['ids'] = ''; }
		if(empty($ar["list_photos_on"])){ $ar['list_photos_on'] = ''; }
		if(empty($ar["list_files_on"])){ $ar['list_files_on'] = ''; }
		
		$id_where = !empty($ar['ids']) 
			? array(
				'ids'=>explode(',',$ar['ids']),
				'qtys'=>explode(',',$ar['qtys']),
				'id' => 0
			) 
			: $ar;
		
		if(!empty($ar['ids'])){
			$ar["spec"] = 'connected';
		}
		
		$str = list_products($id_where, 
			$ar["where"], 
			$site, 
			$ar["spec"], 
			$ar["limit"],
			$ar["orderby"],
			1 // from_tpl			
		);
		
		if(!empty($ar['assign'])){
			$tpl->assign($ar['assign'], $str);
		}else{
			return $str;
		}
	}

?>