<?php
    /*
        list_pubs
        last modified 18.09.2019
		+ link_idn added
		+ random pubs if set in admin
		+ list_pubs_tpl added
		
        Сформируем список публикаций в виде массива
        с разбивкой на страницу или выводом всех записей,
        если так задано get=all   

		$spec=all / starred / not_starred / connected / 
			visited / last / by_option / search / 
			wishlist / show / compare / pub_connected
		+ $from_tpl
		+ fixed sortby setting in categ
		+ added list_pubs in template by alias
		+ fixed pub_connected for pub page 
    */
    
    
    function list_pubs($categ, $site, $spec='all', $limit=ONPAGE, $from_tpl=false)
    {
        global $db, $user;
		if(empty($site->vars['id'])){ return array(); }
		if(!is_array($categ)){ 
			$categ = array('id' => $categ);
		}

		if(empty($categ['sortby']) && !empty($site->vars['default_menu'][$categ['id']]['sortby'])){
			$categ['sortby'] = $site->vars['default_menu'][$categ['id']]['sortby'];
		}
		
		if(!empty($categ['list_files_on'])){ 
			$list_files_on = 1; 
		}elseif(!empty($site->vars['sys_list_files_on'])){
			$list_files_on = 1; 
		}
		
		if(!empty($categ['list_photos_on'])){ 
			$list_photos_on = 1; 
		}elseif(!empty($site->vars['sys_list_photos_on'])){
			$list_photos_on = 1; 
		}
		
        if($spec == 'connected'){
            $query = "SELECT p.*  
                FROM 
                    ".$db->tables['pub_to_product']." ptp,  
                    ".$db->tables['publications']." p  
                WHERE
                    ptp.id_product = '".$categ['id']."' 
                    AND ptp.id_pub = p.id 
                    AND p.active = '1' ";
            $rows = $db->get_results($query);
            if($db->num_rows == 0){ return array(); }
            $ar = array();
            foreach($rows as $row){            
                $ar[] = "'".$row->id."'";
            }
            $categ['id'] = 0;
			$ids = implode(',', $ar);
            $id = 0;
            
        }elseif($spec == 'visited'){
            $v_ar = isset($_SESSION['visited']['pubs']) ? $_SESSION['visited']['pubs'] : array(); 
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
            $ids = implode(',', $ar);
            $id = 0;

			
        }elseif($spec == 'last'){
			$categ['id'] = 0;
        }elseif($spec == 'by_option'){
			//if(empty($categ['by_option']) || empty($categ['id'])){ return;}
			if(empty($categ['by_option']['id']) && empty($categ['by_option']['alias'])){ return;}
			if(empty($categ['by_option']['value'])){ return;}
			
			$query = "SELECT p.id, p.name 
			
                FROM 
                    ".$db->tables['publications']." p,
					".$db->tables['options']." o,  
					".$db->tables['option_values']." opt,  
                    ".$db->tables['pub_categs']." c,  
                    ".$db->tables['site_publications']." s
					
                WHERE
                    p.active = '1' 
					AND p.id = s.id_publications
					AND s.id_site = '".$site->id."'
					AND c.id_pub = p.id 
					AND c.where_placed = 'pub' ";
					
			if(is_array($categ['by_option']['value'])){
				/* если передан массив - не работает
				 надо доделать! */
				foreach($categ['by_option']['value'] as $v){
					$query .= "
						AND UPPER(opt.value) LIKE '".mb_strtoupper($v, 'UTF-8')."' 
					";
				}
				
			}else{
				
				$query .= "
                    AND UPPER(opt.value) LIKE '".mb_strtoupper($categ['by_option']['value'], 'UTF-8')."' 
				";
			}
			//AND c.id_categ = '".$categ['id']."'
			if(!empty($categ['by_option']['id'])){
				$query .= "	AND o.id = '".$categ['by_option']['id']."' 
				";            	
			}else{
				$query .= "	AND o.alias = '".$categ['by_option']['alias']."' 
				";
			}
			$query .= "	AND o.id = opt.id_option 
					AND opt.id_product = p.id
					AND opt.where_placed = 'pub'
					GROUP BY p.id
			";
            $rows = $db->get_results($query);		
			//$db->debug(); exit;			
            if($db->num_rows == 0){ return array(); }
            $ar = array();
            foreach($rows as $row){            
                $ar[] = "'".$row->id."'";
            }
            $ids = implode(',', $ar);
            $categ['id'] = 0;				
			
		}elseif($spec == 'search' && !empty($categ['search']) && empty($categ['id'])){			
			if(empty($categ['search']) || empty($categ['id'])){ return;}			
			$q = $db->escape(mb_strtolower($categ['search'], 'UTF-8'));	
			$query = "SELECT p.id, p.name 
                FROM 
                    ".$db->tables['pub_categs']." c,  
                    ".$db->tables['site_publications']." s,  
                    ".$db->tables['publications']." p  
                WHERE
                    UPPER(p.name) LIKE '%".$q."%'
                    AND p.active = '1' 
					AND p.id = s.id_publications
					AND s.id_site = '".$site->id."'
					AND c.id_pub = p.id AND c.where = 'pub' AND c.id_categ = '".$categ['id']."'
			";
            $rows = $db->get_results($query);
			
            if($db->num_rows == 0){ return array(); }
            $ar = array();
            foreach($rows as $row){            
                $ar[] = "'".$row->id."'";
            }
            $ids = implode(',', $ar);
            $categ['id'] = 0;			
        }elseif($spec == 'wishlist'){
			if(!empty($_SESSION['wishlist']['pubs'])){
				$ids = implode(',', $_SESSION['wishlist']['pubs']);
				$categ['id'] = 0;	
			}else{
				return array();
			}            
        }elseif($spec == 'show'){
            $ids = implode(',', $categ);
            $categ['id'] = 0;
        }elseif($spec == 'compare'){
			$compare_ar = !empty($_GET['ids']) ? $_GET['ids'] : '';
			if(empty($compare_ar)){
				$compare_ar = !empty($_POST['ids']) ? $_POST['ids'] : '';
			}
			if(empty($compare_ar)){ return array(); }
            $ids = implode(',', $compare_ar);
            $categ['id'] = 0;
        }elseif($spec == 'pub_connected'){
            $query = "SELECT p.id, p.name 
                FROM 
                    ".$db->tables['connections']." c,  
                    ".$db->tables['publications']." p  
                WHERE
                    (c.id1 = '".$categ['id']."' 
					AND c.name1 = 'pub' AND c.name2 = 'pub' 
                    AND c.id2 = p.id) OR
					(c.id2 = '".$categ['id']."' 
					AND c.name1 = 'pub' AND c.name2 = 'pub' 
                    AND c.id1 = p.id) 
                    AND p.active = '1' 
			";
            $rows = $db->get_results($query);
			//$db->debug();
			//exit;
            if($db->num_rows == 0){ return array(); }
            $ar = array();
            foreach($rows as $row){            
                $ar[] = "'".$row->id."'";
            }
            $ids = implode(',', $ar);
            $categ['id'] = 0;
        }

        $current_page = isset($site->uri['params']['page']) ? intval($site->uri['params']['page']) : 0;        
        $sql_num_rows = ' SELECT count(DISTINCT p.`id`) ';

        $sql = "SELECT 
                p.id, p.name as title, p.anons as intro, 
                p.active, 
				p.memo as content,  
                p.alias, p.views, 
				p.user_id, 
				u.login as user_login, 
				u.`name` as user_name, 
				u.gender as user_gender, 
                p.icon, p.f_spec,
				p.date_insert, p.ddate as date_update,                  
				c.title as categ_title, 
				c.id as categ_id, 
				c.alias as categ_alias, 
				
				cat2.title as categ2_title, 
				cat2.id as categ2_id, 
				cat2.alias as categ2_alias, 
				
				cat3.title as categ3_title, 
				cat3.id as categ3_id, 
				cat3.alias as categ3_alias, 
								
                IFNULL(m.monthviews,0) as monthviews,
                IFNULL(u.fotos_qty,0) as fotos_qty,
                IFNULL(f.files_qty,0) as files_qty,
                IFNULL(comm.comments_qty,0) as comments_qty   
		";				
				
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
                ".$db->tables['categs']." c, 
                ".$db->tables['publications']." p, 
                ".$db->tables['pub_categs']." pc, 
                ".$db->tables['site_publications']." sp
                )
        ";
                
        $sql .= "                                            
            FROM 
                (
                
                ".$db->tables['publications']." p, 
                ".$db->tables['site_publications']." sp, 
				".$db->tables['pub_categs']." pc, 
				".$db->tables['categs']." c  
                
                )
        ";

        /* find out big picture */
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
								
							AND record_type = 'pub'
						
						ORDER by width desc, id_in_record
					) p GROUP BY record_id   
				) pic1 ON p.id = pic1.record_id  
			";

        }

        /* find out small picture */
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
								
							AND record_type = 'pub'
					
						ORDER by width desc, id_in_record
					) p GROUP BY record_id   
				) pic2 ON p.id = pic2.record_id  
			";			
        }

        /* find out mini picture */
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
						
							AND record_type = 'pub'
						
						ORDER by width desc, id_in_record
					) p GROUP BY record_id   
				) pic3 ON p.id = pic3.record_id  
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
						
							AND record_type = 'pub'
						
						ORDER by width desc, id_in_record
					) p GROUP BY record_id  
				) pic4 ON p.id = pic4.record_id  
			";
        }
		
		$sql .= "
			LEFT JOIN ".$db->tables['users']." u ON (p.user_id = u.id)
		";
		$sql .= "
			LEFT JOIN ".$db->tables['categs']." cat2 ON (c.id_parent = cat2.id)
		";
		$sql .= "
			LEFT JOIN ".$db->tables['categs']." cat3 ON (cat2.id_parent = cat3.id)
		";
        
		$sql .= "
				LEFT JOIN ( 
					SELECT record_id, COUNT(DISTINCT id_in_record) fotos_qty 
					FROM uploaded_pics 
					WHERE record_type = 'pub' 
					GROUP BY record_id 
				) u on (p.id = u.record_id) 
		";
				
		$sql .= "
				LEFT JOIN ( 
					SELECT record_id, COUNT(*) files_qty 
					FROM uploaded_files 
					WHERE record_type = 'pub' 
					GROUP BY record_id 
				) f on (f.record_id = p.id) 
		";
				
		$sql .= "
				LEFT JOIN ( 
					SELECT record_id, COUNT(*) comments_qty 
					FROM comments 
					WHERE record_type = 'pub' 
					GROUP BY record_id 
				) comm on (comm.record_id = p.id) 
		";
						
		$sql .= "
				LEFT JOIN ( 
					SELECT id, COUNT(*) monthviews 
					FROM counter 
					WHERE `for_page` = 'pub' AND `time` > DATE_ADD(NOW(), INTERVAL -30 DAY) 
					GROUP BY id) m on (m.id = p.id)
		";		
		      
        if(!empty($categ['id'])){
            $sql_num_rows .= " 
                WHERE 
                    p.id = pc.id_pub                     
                    AND pc.`where_placed` = 'pub' 
					AND pc.id_categ = '".$categ['id']."' 
                    AND c.id = '".$categ['id']."'
                    AND p.id = sp.id_publications
                    AND sp.id_site = '".$site->vars['id']."' 
			";
    
            $sql .= " 
                WHERE 
                    p.id = pc.id_pub                     
                    AND pc.`where_placed` = 'pub' 
					AND pc.id_categ = '".$categ['id']."' 
                    AND c.id = '".$categ['id']."'
                    AND p.id = sp.id_publications
                    AND sp.id_site = '".$site->vars['id']."' 
			";
		}elseif(!empty($categ['pid'])){
            $sql_num_rows .= " 
                WHERE 
                    p.id = pc.id_pub                     
                    AND pc.`where_placed` = 'pub' 
					AND pc.id_categ = c.id 
                    AND p.id = sp.id_publications 
					AND p.id = '".intval($categ['pid'])."'
                    AND sp.id_site = '".$site->vars['id']."' 
			";
    
            $sql .= " 
                WHERE 
                    p.id = pc.id_pub                     
                    AND pc.`where_placed` = 'pub' 
					AND pc.id_categ = c.id 
                    AND p.id = sp.id_publications 
					AND p.id = '".intval($categ['pid'])."'
                    AND sp.id_site = '".$site->vars['id']."' 
			";
		}elseif(!empty($categ['alias']) && empty($ids)){
			$sql_num_rows .= " 
                WHERE 
                    p.id = pc.id_pub                     
                    AND pc.`where_placed` = 'pub' 
					AND pc.id_categ = c.id  
                    AND c.alias = '".$categ['alias']."'
                    AND p.id = sp.id_publications
                    AND sp.id_site = '".$site->vars['id']."' 
			";
    
            $sql .= " 
                WHERE 
                    p.id = pc.id_pub                     
                    AND pc.`where_placed` = 'pub' 
					AND pc.id_categ = c.id  
                    AND c.alias = '".$categ['alias']."'
                    AND p.id = sp.id_publications
                    AND sp.id_site = '".$site->vars['id']."' 
			";
        }else{
            $sql_num_rows .= " 
                WHERE 
                    p.id = pc.id_pub 
                    AND pc.`where_placed` = 'pub' 
					AND pc.id_categ > '0'                     
                    AND pc.id_categ = c.id
                    AND p.id = sp.id_publications
                    AND sp.id_site = '".$site->vars['id']."' 
			";
    
            $sql .= " 
                WHERE 
                    p.id = pc.id_pub 					
                    AND pc.`where_placed` = 'pub' 
                    AND pc.id_categ > '0'
                    AND pc.id_categ = c.id
                    AND p.id = sp.id_publications
                    AND sp.id_site = '".$site->vars['id']."' 
			";
                    
            if(!empty($ids)){
                $sql_num_rows .= " AND p.id IN (".$ids.") ";
                $sql .= " AND p.id IN (".$ids.") ";                
            }
            
        }

        if($spec == 'last'){
            $sql_num_rows .= "
				AND c.in_last = '1' 
			";
            $sql .= " 
				AND c.in_last = '1' 
			";  
        }        
        
        if(($spec == 'last' || $spec == 'starred') && !empty($site->uri['alias'])){
            $sql_num_rows .= "
				AND p.alias != '".$site->uri['alias']."' 
			";
            $sql .= "
				AND p.alias != '".$site->uri['alias']."' 
			";
        }        

        if($spec == 'starred'){
            $sql_num_rows .= " AND p.f_spec = '1' ";
            $sql .= " AND p.f_spec = '1' ";  
        }        

        if($spec == 'not_starred'){
            $sql_num_rows .= " AND p.f_spec = '0' ";
            $sql .= " AND p.f_spec = '0' ";  
        }        
		

        if(($spec == 'search') && !empty($site->uri['params']['q'])){
			$q = $db->escape(mb_strtolower($site->uri['params']['q'], 'UTF-8'));
            $sql_num_rows .= " AND (LOWER(p.alias) LIKE '%".$q."%' 
                OR LOWER(p.`name`) LIKE '%".$q."%'
                OR LOWER(p.`anons`) LIKE '%".$q."%'
                OR LOWER(p.`memo`) LIKE '%".$q."%'                
                ) 
			";
            
            
            $sql .= " AND (LOWER(p.alias) LIKE '%".$q."%' 
                OR LOWER(p.`name`) LIKE '%".$q."%'
                OR LOWER(p.`anons`) LIKE '%".$q."%'
                OR LOWER(p.`memo`) LIKE '%".$q."%'                
                ) 
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
		
		$sql .= ' 
			GROUP BY p.id 
		';
        
        /* GET-данные могут переназначить правило сортировки */
        if(!empty($_GET['orderby'])){
            $categ['sortby'] = $db->escape($_GET['orderby']);           
        }
        
        //$sql .= ' ORDER BY c.sort, '; 
		$sql .= ' 
			ORDER BY  
		'; 
        
        if($spec == 'starred'){
            $sql .= ' p.f_spec desc, ';
        }

        if(!empty($categ['sortby'])){
            if($categ['sortby'] == 'date_insert'){
                $sql .= ' p.date_insert ';
            }elseif($categ['sortby'] == 'name'){
                //$sql .= ' p.name ';
				$sql .= ' p.`name` ';
            }elseif($categ['sortby'] == 'random'){
                //$sql .= ' p.name ';
				$sql .= ' RAND() ';
            }elseif($categ['sortby'] == 'views desc' || $categ['sortby'] == 'views'){
                $sql .= ' p.views desc ';
            }elseif($categ['sortby'] == 'monthviews desc' || $categ['sortby'] == 'monthviews'){
                $sql .= ' p.monthviews desc ';
            }else{
                $sql .= ' p.date_insert desc ';
            }
        }else{
            $sql .= ' p.date_insert desc ';
        }
		
		if(!isset($_GET['all']) && empty($from_tpl)){
            $sql .= ' LIMIT '.$current_page*$limit.', '.$limit;
        }elseif(!empty($from_tpl)){
			$sql .= ' LIMIT 0, '.$limit;
		}
          
        $all_results = $db->get_var($sql_num_rows);
        if($limit < ONPAGE && $all_results > $limit){ $all_results = $limit; }
        //if($limit < ONPAGE){ $all_results = $limit; }
        $site->page['all_pubs'] = $all_results;		
		
		if($spec == 'all'){
			$site->page['all_results'] = $all_results;
		}
		
        $rows = $db->get_results($sql, ARRAY_A);
	
		if($spec == 'by_option'){			
			//$db->debug();
			//exit;			
		}
		
		if($spec == 'pub_connected'){
						
		}
		
		if($spec == 'connected'){
			//$db->debug();
			//exit;			
		}
		
        if(!$rows || $db->num_rows == 0){ return array(); }
        if($spec == 'last'){
			//$db->debug();
			//exit;
        }
        
        if($all_results > $db->num_rows && $limit >= ONPAGE){
            $pages = $site->set_pages($all_results, 'pubs');
        }else{ $pages = ''; }
        
        $items = $all = array();

        foreach($rows as $row){
            $pic1 = array();
			if(isset($row['pic1_id'])){				
				$pic1['id'] = $row['pic1_id'];           
				$pic1['ext'] = $row['pic1_ext'];            
				$pic1['width'] = $row['pic1_width'];            
				$pic1['height'] = $row['pic1_height'];
				$pic1['title'] = $row['pic1_name']; 
				$pic1['url'] = !empty($row['pic1_id']) && !empty($row['pic1_ext']) 
					? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic1_id'].'.'.$row['pic1_ext']
					: '';
			}
            
            $pic2 = array();
			if(isset($row['pic2_id'])){				
				$pic2['id'] = $row['pic2_id'];           
				$pic2['ext'] = $row['pic2_ext'];            
				$pic2['width'] = $row['pic2_width'];            
				$pic2['height'] = $row['pic2_height'];
				$pic2['title'] = $row['pic2_name']; 
				$pic2['url'] = !empty($row['pic2_id']) && !empty($row['pic2_ext']) 
					? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic2_id'].'.'.$row['pic2_ext']
					: '';            
			}

            $pic3 = array();
			if(isset($row['pic3_id'])){				
				$pic3['id'] = $row['pic3_id'];           
				$pic3['ext'] = $row['pic3_ext'];            
				$pic3['width'] = $row['pic3_width'];            
				$pic3['height'] = $row['pic3_height'];
				$pic3['title'] = $row['pic3_name']; 
				$pic3['url'] = !empty($row['pic3_id']) && !empty($row['pic3_ext']) 
					? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic3_id'].'.'.$row['pic3_ext']
					: ''; 
			}
				
			$pic4 = array();
			if(isset($row['pic4_id'])){				
				$pic4['id'] = $row['pic4_id'];           
				$pic4['ext'] = $row['pic4_ext'];            
				$pic4['width'] = $row['pic4_width'];            
				$pic4['height'] = $row['pic4_height'];
				$pic4['title'] = $row['pic4_name']; 
				$pic4['url'] = !empty($row['pic4_id']) && !empty($row['pic4_ext']) 
					? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic4_id'].'.'.$row['pic4_ext']
					: ''; 				
			}


            $row['pic'] = array(
                1 => $pic1,
                2 => $pic2, 
                3 => $pic3, 
				4 => $pic4 				
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
			
			
            $row['link'] = $site->vars['site_url'].'/'.$row['alias'].'/';
            $row['link_idn'] = $site->uri['idn'].'/'.$row['alias'].'/';
            $row['date'] = date($site->vars['site_date_format'], strtotime($row['date_insert'])); 
            $row['time'] = date($site->vars['site_time_format'], strtotime($row['date_insert']));
            $all[] = $row['id'];
			
			// files > 0
			if($row['files_qty'] > 0 && !empty($list_files_on)){
				$row['list_files'] = list_files('pub', $row['id'], $site);
			}
			
			// pics > 1 and list_products_pics_qty 
			if($row['fotos_qty'] > 1 && !empty($list_photos_on)){
				$row['list_photos'] = list_photos('pub', $row['id'], $site);
			}

			$row['clear'] = 1;
			$row = $site->check_forms_in_content($row);
            $items[] = $row;
        }
        
		$options = array();	
		if($spec == 'compare'){
			if(!empty($compare_ar)){
				foreach($compare_ar as $v){
					$options[$v] = list_product_options($v, 'pub', $site);
					unset($options[$v]['in_list']);
				}
			}			
		}else{
			$options = options_in_list($all, 'pub', $site); 			
		}
		
		if(!empty($options) && !empty($items)){			
			foreach($items as $k => $item){				
				if(isset($options[$item['id']]['list'])){
					$items[$k]['options'] = $options[$item['id']]['list'];
				}elseif(isset($options[$item['id']])){
					
					//if(!empty($site->get_group_options)){
						//if(isset($options[$item['id']][$site->get_group_options])){
							//$items[$k]['options'] = $options[$item['id']][$site->get_group_options];
						//}						
					//}else{
						$items[$k]['options'] = $options[$item['id']];
					//}
					
				}
				
			}
		}
        return array(
            'spec' => $spec,
            'list' => $items,
            'all' => $all_results,
            'pages' => $pages,
            'options' => $options
        );
    }
	
	function list_pubs_tpl($ar=NULL){
		global $site, $tpl;
		if(empty($ar["id"])){ $ar['id'] = 0; }
		if(empty($ar["alias"])){ $ar['alias'] = ''; }
		if(empty($ar["spec"])){ $ar['spec'] = 'all'; }
		if(empty($ar["limit"])){ $ar['limit'] = ONPAGE; }

		$str = list_pubs($ar, 
			$site, 
			$ar["spec"], 
			$ar["limit"],
			1 // from_tpl
		);

		if(!empty($ar['assign'])){
			$tpl->assign($ar['assign'], $str);
		}else{
			return $str;
		}
		
	}

?>