<?php
    /**************************************
	***************************************
	**	list_categs
	**	last modified 17.04.2018
	**	+ link_idn added
	**	+ исправлен путь к картинкам, если много сайтов и урл из подпапки
	**	+ path у картинки
	**	+ count connected blocks
	**	+ count if not empty content in categ
	**	+ count fotos
	**	+ id_parent2 added
	**
	**
	**	Сформируем список страниц, 
	**	в т.ч. возможность вывода только страниц каталога или только не каталога
    **	по умолчанию выводим меню от id-страницы сайта из настроек
    **	отдельно можно указать выводить ли страницы нижнего уровня
	**	added вывод страницы по алиасу
	***************************************
    **************************************/
    
  /* ok */
  function get_categs($tablename, $activeid, $shop=0,$parentid=0, $site, $starred=0) {

    $parents = get_menuparents($tablename, $site, $starred);
	if(empty($parents)){ return; }
    $chain = get_chain($parents, $activeid);
    $level = 1;
    $list = get_hierarchy($parentid, $level, $chain, $parents);
    $menudata = get_menudata($tablename, $list, $activeid, $shop, $site);
    return $menudata;
  }

  /* ok */
  function get_menuparents($tablename, $site, $starred) {
    global $db;
    $parents = array();
    $sql = "SELECT c.id, c.id_parent as parentid
        FROM $tablename c                
        ";
	if($starred == 1){ $sql .= " WHERE f_spec = '1' "; }
    $sql .= " ORDER BY c.`sort`, c.title ";
    $result = $db->get_results($sql);

    if(!$result || $db->num_rows == 0){ return;}
    foreach($result as $row){
        $parents[$row->id] = $row->parentid;
    }

    return $parents;
  }


  /* ok */
  function get_chain($hierachy, $activeid) {
    $chain = array();
    if (!isset($hierachy[$activeid])) {
        return array();
    }

    $current = $activeid;
    do {
        $chain[] = $current;
        $current =@ $hierachy[$current]; 
    }
    while ($current); 

    return $chain;
  }

  /* ok */
  function get_hierarchy($parentid, $level, $chain, $hierarchy) {
    $list = array();
    $brothers = array_keys($hierarchy, $parentid);
	//echo $hierarchy." - ".$parentid;
    //print_r($brothers); exit;
    foreach ($brothers as $value) {
        $list[$value] = $level;
        
        /*
        Это для раскрытия только активного меню
        if (in_array($value, $chain)) {
            $children = get_hierarchy($value, $level+1, $chain, $hierarchy);
            $list = $list + $children;
        }
        */
        
        $children = get_hierarchy($value, $level+1, $chain, $hierarchy);
        $list = $list + $children;
    }
	
    return $list;
  }

  /* ok */
  function get_menudata($tablename, $list, $activeid, $shop=0, $site) {
    $ids = array_keys($list); // вспомним, что id элементов хранятся в ключах массива
    $in = implode(',', $ids);	
    if(empty($in)){ return; }
    global $db;
	
	$sql2 = "SELECT c.id, c.title, 
				IF(c.memo != '', 1, 0) as content,
				c.id_parent, 
				subc.title as parent_title, subc.alias as parent_alias, 
				subc2.id as id_parent2, 
				subc2.title as parent2_title, subc2.alias as parent2_alias, 
				c.icon, c.sortby, 
				c.f_spec AS star, c.active AS visible, 
				c.alias, c.shop, c.sort, c.memo_short as intro,
				c.date_insert, c.date_update, 				
				
				(SELECT COUNT(Distinct id_in_record) as fotos 
					FROM ".$db->tables['uploaded_pics']." up 
					WHERE  up.`record_type` = 'categ' 
					AND up.record_id = c.id 
				) as fotos,
				
				IFNULL(block.blocks,0) AS blocks, 
				IFNULL(pub.pubs,0) AS pubs, 
				IFNULL(prod.products,0) AS products, 
				IFNULL(chld.child_categs,0) AS child_categs
				";
				
	if(isset($site->vars['img_size1']['width']) && isset($site->vars['img_size1']['height'])){
		$sql2 .= ",				
				pic1.id AS pic1_id, 
				pic1.ext AS pic1_ext, 
				pic1.title AS pic1_name,
				pic1.width AS pic1_width,
				pic1.height AS pic1_height 
				";
	}
				
	if(isset($site->vars['img_size2']['width']) && isset($site->vars['img_size2']['height'])){
		$sql2 .= ",				
				pic2.id AS pic2_id,
				pic2.ext AS pic2_ext,
				pic2.title AS pic2_name,
				pic2.width AS pic2_width,
				pic2.height AS pic2_height 
				";
	}
				
	if(isset($site->vars['img_size3']['width']) && isset($site->vars['img_size3']['height'])){
		$sql2 .= ",
				pic3.id AS pic3_id,
				pic3.ext AS pic3_ext,
				pic3.title AS pic3_name,
				pic3.width AS pic3_width,
				pic3.height AS pic3_height 
				";
	}

	if(isset($site->vars['img_size4']['width']) && isset($site->vars['img_size4']['height'])){
		$sql2 .= ",
				pic4.id AS pic4_id,
				pic4.ext AS pic4_ext,
				pic4.title AS pic4_name,
				pic4.width AS pic4_width,
				pic4.height AS pic4_height 
				";
	}
	
	$sql2 .= "FROM ".$db->tables['categs']." c
				JOIN ".$db->tables['site_categ']." sc ON (c.id = sc.id_categ)
			";
	
	$sql2 .= "
		LEFT JOIN ".$db->tables['categs']." subc ON (c.id_parent = subc.id AND subc.active = '1') 
	";

	$sql2 .= "
		LEFT JOIN ".$db->tables['categs']." subc2 ON (subc.id_parent = subc2.id AND subc2.active = '1') 
	";
	
	if(isset($site->vars['img_size1']['width']) && isset($site->vars['img_size1']['height'])){
		$sql2 .= "
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
								
							AND record_type = 'categ'
						
						ORDER by width desc, id_in_record
					) p GROUP BY record_id   
				) pic1 ON c.id = pic1.record_id  
				";
	}
					
	if(isset($site->vars['img_size2']['width']) && isset($site->vars['img_size2']['height'])){
		$sql2 .= "
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
								
							AND record_type = 'categ'
					
						ORDER by width desc, id_in_record
					) p GROUP BY record_id   
				) pic2 ON c.id = pic2.record_id  
				";
	}
					
					
	if(isset($site->vars['img_size3']['width']) && isset($site->vars['img_size3']['height'])){
		$sql2 .= "
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
						
							AND record_type = 'categ'
						
						ORDER by width desc, id_in_record
					) p GROUP BY record_id   
				) pic3 ON c.id = pic3.record_id  
				";
	}

	if(isset($site->vars['img_size4']['width']) && isset($site->vars['img_size4']['height'])){
		$sql2 .= "
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
						
							AND record_type = 'categ'
						
						ORDER by width desc, id_in_record
					) p GROUP BY record_id  
				) pic4 ON c.id = pic4.record_id  
				";
	}
						
	$sql2 .= "LEFT JOIN
					(
						SELECT b.type_id, COUNT(*) blocks 
						FROM ".$db->tables['blocks']." b 
						WHERE  b.`where_placed` = 'manual' 
							AND b.active = '1' 
							AND b.`type` = 'categ'
						GROUP BY b.type_id
					) block ON (block.type_id = c.id)
					";
					
	$sql2 .= "LEFT JOIN
					(
						SELECT p.id_categ, COUNT(*) pubs 
						FROM ".$db->tables['pub_categs']." p
						JOIN ".$db->tables['site_publications']." sp ON (sp.id_publications = p.id_pub)
						JOIN ".$db->tables['publications']." pp ON (pp.id = p.id_pub)
						WHERE p.id_pub > '0' 
							AND p.`where_placed` = 'pub' 
							AND pp.active = '1' 
							AND sp.id_site = '".$site->vars['id']."'
						GROUP BY p.id_categ
					) pub ON (pub.id_categ = c.id)
					";

	$sql2 .= "LEFT JOIN
					(
						SELECT p.id_categ, COUNT(*) products
						FROM ".$db->tables['pub_categs']." p
						JOIN ".$db->tables['products']." pp ON (pp.id = p.id_pub)
						WHERE p.id_pub > '0' AND p.`where_placed` = 'product' AND pp.active = '1'
						GROUP BY p.id_categ
					) prod ON (prod.id_categ = c.id)
					";
	
	$sql2 .= "LEFT JOIN
					(
						SELECT c.id_parent, COUNT(*) child_categs
						FROM ".$db->tables['site_categ']." s
						JOIN ".$db->tables['categs']." c ON (s.id_categ = c.id) 
						WHERE c.active = '1' AND s.id_site = '".$site->vars['id']."'
						GROUP BY c.id_parent
					) chld ON (chld.id_parent=c.id)
					";
				 
	$sql2 .= "
        WHERE c.id IN ($in) 
            AND (child_categs > '0' OR c.active = '1') 
            AND sc.id_site = '".$site->vars['id']."' 
			";
    if($shop == 1) { $sql2 .= " AND c.shop='1' "; } 
	
	if(!isset($_GET['debug'])){	
		$sql2 .= " AND c.date_insert < CONCAT(CURDATE(), ' ',CURTIME()) ";
	}
	
	$sql2 .= " ORDER BY c.sort, c.title ";

    $result = $db->get_results($sql2, ARRAY_A);
	//$db->debug(); exit;
    if(!$result || $db->num_rows == 0){ return;}
	$site->page['all_results'] = $db->num_rows;
    $tempdata = array();
    foreach($result as $row){

			$pic1 = array();
			if(!empty($row['pic1_id'])){				
				$pic1['id'] = $row['pic1_id'];
				$pic1['ext'] = $row['pic1_ext'];            
				$pic1['title'] = $row['pic1_name'];            
				$pic1['width'] = $row['pic1_width'];            
				$pic1['height'] = $row['pic1_height'];
				$pic1['path'] = !empty($row['pic1_id']) && !empty($row['pic1_ext']) 
					? '/upload/records/'.$row['pic1_id'].'.'.$row['pic1_ext']
					: '';            
				$pic1['url'] = !empty($row['pic1_id']) && !empty($row['pic1_ext']) 
					? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic1_id'].'.'.$row['pic1_ext']
					: '';            
			}
            
            $pic2 = array();
			if(!empty($row['pic2_id'])){				
				$pic2['id'] = $row['pic2_id'];           
				$pic2['ext'] = $row['pic2_ext'];            
				$pic2['title'] = $row['pic2_name'];            
				$pic2['width'] = $row['pic2_width'];            
				$pic2['height'] = $row['pic2_height'];
				$pic2['path'] = !empty($row['pic2_id']) && !empty($row['pic2_ext']) 
					? '/upload/records/'.$row['pic2_id'].'.'.$row['pic2_ext']
					: '';            
				$pic2['url'] = !empty($row['pic2_id']) && !empty($row['pic2_ext']) 
					? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic2_id'].'.'.$row['pic2_ext']
					: '';            
			}

            $pic3 = array();
			if(!empty($row['pic3_id'])){				
				$pic3['id'] = $row['pic3_id'];           
				$pic3['ext'] = $row['pic3_ext'];            
				$pic3['title'] = $row['pic3_name'];            
				$pic3['width'] = $row['pic3_width'];            
				$pic3['height'] = $row['pic3_height'];
				$pic3['path'] = !empty($row['pic3_id']) && !empty($row['pic3_ext']) 
					? '/upload/records/'.$row['pic3_id'].'.'.$row['pic3_ext']
					: '';            
				$pic3['url'] = !empty($row['pic3_id']) && !empty($row['pic3_ext']) 
					? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic3_id'].'.'.$row['pic3_ext']
					: '';            
			}

            $pic4 = array();
			if(!empty($row['pic4_id'])){				
				$pic4['id'] = $row['pic4_id'];           
				$pic4['ext'] = $row['pic4_ext'];            
				$pic4['title'] = $row['pic4_name'];            
				$pic4['width'] = $row['pic4_width'];            
				$pic4['height'] = $row['pic4_height'];
				$pic4['path'] = !empty($row['pic4_id']) && !empty($row['pic4_ext']) 
					? '/upload/records/'.$row['pic4_id'].'.'.$row['pic4_ext']
					: '';            
				$pic4['url'] = !empty($row['pic4_id']) && !empty($row['pic4_ext']) 
					? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic4_id'].'.'.$row['pic4_ext']
					: '';            
			}
			
            $pic = array(
                1 => $pic1,
                2 => $pic2, 
                3 => $pic3, 
                4 => $pic4 
            );
			$pic = list_photos('categ', $row['id'], $site);
	
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
	
        $id = $row['id'];
        $star = $row['star'];
        $icon = $row['icon'];
        $link = $site->vars['site_url'].'/'.$row['alias'].URL_END;
        $link_idn = $site->uri['idn'].'/'.$row['alias'].URL_END;
        $level = $list[$id];
        //$active = ($id == $activeid) ? TRUE : FALSE;
		$active = ($row['alias'] == $site->uri['alias']) ? 1 : 0;
		
        $padding = ($level-1)*25; 
        $a = $row + compact('level', 'active', 'padding', 'link', 'link_idn', 'pic');
        $tempdata[$row{'id'}] = $a;
    }
	
    foreach ($list as $key => $value) {
        if(isset($tempdata[$key])) $data[$key] = $tempdata[$key];
    }
	//$site->print_r($data,1);
    return $data;
  }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    function list_categs($id, $site, $ar=array())
    {
        global $db; 
        $ar['mode'] = !isset($ar['bottom']) ? 'top' : 'bottom';	
		// $ar['products'] = 1 -> только разделы содержащие товары
		// $ar['pubs'] = 1 -> только разделы содержащие публикации
		// $ar['categs'] = 1 -> только разделы содержащие подразделы
		// $ar['comments'] = 1 -> только разделы содержащие комменты
		// $ar['fotos'] = 1 -> только разделы содержащие фото
		// $ar['files'] = 1 -> только разделы содержащие файлы
		// $ar['all'] = вывод всех записей
		// $ar['alias'] = вывод страницы по алиасу
		// $ar['id_parent'] = вывод только дочерних страниц
		
        $current_page = isset($site->uri['params']['page']) ? intval($site->uri['params']['page']) : 0;        
        $sql_num_rows = ' SELECT count(DISTINCT p.`id`) ';
                
        $sql = "SELECT 
                p.id, p.title, p.memo_short as intro, 
                p.date_update, p.active, ";

		if(!empty($id) || !empty($ar['alias'])){
			$sql .= " p.memo as content, ";
		}
				
        $sql .= " p.alias, p.date_insert, 
                p.user_id, p.shop, p.in_last, 
                p.icon, p.f_spec,  p.sort, p.id_parent, 
				p.sortby, 
				sub1.id_parent as sub1_id_parent,
				sub1.title as sub1_title,
				sub2.title as sub2_title,               	
              	
				IFNULL(fot.fotos_qty,0) AS fotos_qty,
				IFNULL(fil.files_qty,0) AS files_qty,
				IFNULL(comm.comments_qty,0) AS comments_qty, 
				IFNULL(pub.pubs,0) AS pubs, 
				IFNULL(prod.products,0) AS products, 
				IFNULL(chld.child_categs,0) AS child_categs
		";

		
		if(isset($site->vars['img_size1']['width']) && isset($site->vars['img_size1']['height'])){
			$sql .= ",				
				pic1.id AS pic1_id, 
				pic1.ext AS pic1_ext, 
				pic1.title AS pic1_name,
				pic1.width AS pic1_width,
				pic1.height AS pic1_height 
				";
	}
				
	if(isset($site->vars['img_size2']['width']) && isset($site->vars['img_size2']['height'])){
			$sql .= ",				
				pic2.id AS pic2_id,
				pic2.ext AS pic2_ext,
				pic2.title AS pic2_name,
				pic2.width AS pic2_width,
				pic2.height AS pic2_height 
				";
	}
				
	if(isset($site->vars['img_size3']['width']) && isset($site->vars['img_size3']['height'])){
			$sql .= ",
				pic3.id AS pic3_id,
				pic3.ext AS pic3_ext,
				pic3.title AS pic3_name,
				pic3.width AS pic3_width,
				pic3.height AS pic3_height 
				";
	}

	if(isset($site->vars['img_size4']['width']) && isset($site->vars['img_size4']['height'])){
			$sql .= ",
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
                ".$db->tables['categs']." p, 
                ".$db->tables['site_categ']." sc
                )
                ";
                
        $sql .= "                                            
            FROM 
                (
                ".$db->tables['categs']." p, 
                ".$db->tables['site_categ']." sc
                )
                ";              

        /* find out big picture */
        if(isset($site->vars['img_size1']['width']) && isset($site->vars['img_size1']['height'])){
            $sql .= "
                LEFT JOIN
                    (SELECT pic1.*
                        FROM {$db->tables['uploaded_pics']} pic1
                        WHERE ((pic1.width = '{$site->vars['img_size1']['width']}' 
                        OR pic1.height = '{$site->vars['img_size1']['height']}')
                        OR
                        (pic1.width < '{$site->vars['img_size1']['width']}' 
                    AND pic1.height < '{$site->vars['img_size1']['height']}')
                        )
                        ORDER by pic1.id_in_record) pic1
                    ON p.id = pic1.record_id AND pic1.record_type = 'categ'  
            "; 
        }

        /* find out small picture */
        if(isset($site->vars['img_size2']['width']) && isset($site->vars['img_size2']['height'])){
            $sql .= "
                LEFT JOIN
                    (SELECT pic2.*
                        FROM {$db->tables['uploaded_pics']} pic2
                        WHERE pic2.width = '{$site->vars['img_size2']['width']}' 
                        OR pic2.height = '{$site->vars['img_size2']['height']}'
                        ORDER by pic2.id_in_record) pic2
                    ON p.id = pic2.record_id AND pic2.record_type = 'categ'  
            "; 
        }

        /* find out mini picture */
        if(isset($site->vars['img_size3']['width']) && isset($site->vars['img_size3']['height'])){
            $sql .= "
                LEFT JOIN
                    (SELECT pic3.*
                        FROM {$db->tables['uploaded_pics']} pic3
                        WHERE pic3.width = '{$site->vars['img_size3']['width']}' 
                        OR pic3.height = '{$site->vars['img_size3']['height']}'
                        ORDER by pic3.id_in_record) pic3
                    ON p.id = pic3.record_id AND pic3.record_type = 'categ'  
            "; 
        }
		
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
						
							AND record_type = 'categ'
						
						ORDER by width desc, id_in_record
					) p GROUP BY record_id  
				) pic4 ON p.id = pic4.record_id  
				";
		}
		
			$sql .= "LEFT JOIN
					(
						SELECT fot.record_id, COUNT(DISTINCT `id_in_record`) fotos_qty 
						FROM ".$db->tables['uploaded_pics']." fot 
						WHERE fot.record_type = 'categ' 
						GROUP BY fot.record_id
					) fot ON (fot.record_id = p.id)
					";
					
			$sql .= "LEFT JOIN
					(
						SELECT fil.record_id, COUNT(*) files_qty 
						FROM ".$db->tables['uploaded_files']." fil 
						WHERE fil.record_type = 'categ' 
						GROUP BY fil.record_id
					) fil ON (fil.record_id = p.id)
					";		
			
			$sql .= "LEFT JOIN
					(
						SELECT comm.record_id, COUNT(*) comments_qty 
						FROM ".$db->tables['comments']." comm 
						WHERE comm.record_type = 'comment' 
							AND comm.active = '1' 
						GROUP BY comm.record_id
					) comm ON (comm.record_id = p.id)
					";	  
				  
			$sql .= "LEFT JOIN
					(
						SELECT p.id_categ, COUNT(*) pubs 
						FROM ".$db->tables['pub_categs']." p
						JOIN ".$db->tables['site_publications']." sp ON (sp.id_publications = p.id_pub)
						JOIN ".$db->tables['publications']." pp ON (pp.id = p.id_pub)
						WHERE p.id_pub > '0' 
							AND p.`where_placed` = 'pub' 
							AND pp.active = '1' 
							AND sp.id_site = '".$site->vars['id']."'
						GROUP BY p.id_categ
					) pub ON (pub.id_categ = p.id)
					";

			$sql .= "LEFT JOIN
					(
						SELECT p.id_categ, COUNT(*) products
						FROM ".$db->tables['pub_categs']." p
						JOIN ".$db->tables['products']." pp ON (pp.id = p.id_pub)
						WHERE p.id_pub > '0' AND p.`where_placed` = 'product' AND pp.active = '1'
						GROUP BY p.id_categ
					) prod ON (prod.id_categ = p.id)
					";
	
			$sql .= "LEFT JOIN
					(
						SELECT c.id_parent, COUNT(*) child_categs
						FROM ".$db->tables['site_categ']." s
						JOIN ".$db->tables['categs']." c ON (s.id_categ = c.id) 
						WHERE c.active = '1' AND s.id_site = '".$site->vars['id']."'
						GROUP BY c.id_parent
					) chld ON (chld.id_parent=p.id)
					";		
					
			$sql .= " LEFT JOIN ".$db->tables['categs']." sub1 ON (p.id_parent = 
			sub1.id) ";
			$sql .= " LEFT JOIN ".$db->tables['categs']." sub2 ON (sub1.id_parent =  sub2.id) ";
        
        if($id == 0){
			
			$sql_num_rows .= " 
              WHERE 
                  p.id = sc.id_categ 
                  AND sc.id_categ > '0'
                  AND sc.id_site = '".$site->vars['id']."' 
			";
				  
				  
			$sql .= " 
              WHERE 
                  p.id = sc.id_categ 
                  AND sc.id_categ > '0'
                  AND sc.id_site = '".$site->vars['id']."' 
			";
				  
			if(!empty($ar['alias'])){
				$sql_num_rows .= " AND p.alias LIKE '".$db->escape($ar['alias'])."' ";
				$sql .= " AND p.alias LIKE '".$db->escape($ar['alias'])."' ";
			}  
				  
			if(isset($ar['cid'])){
				  $sql_num_rows .= " 
					  AND p.id_parent = '".intval($ar['cid'])."' ";
		  
				  $sql .= " 
					  AND p.id_parent = '".intval($ar['cid'])."' ";
			}
                  
            if(isset($ar['search']) && isset($site->uri['params']['q'])){
            
              $q = $db->escape(mb_strtolower($site->uri['params']['q'], 'UTF-8'));
              $sql_num_rows .= " AND (LOWER(p.alias) LIKE '%".$q."%' 
                OR LOWER(p.`title`) LIKE '%".$q."%'
                OR LOWER(p.`memo_short`) LIKE '%".$q."%'
                OR LOWER(p.`memo`) LIKE '%".$q."%'                
                ) ";
            
            
              $sql .= " AND (LOWER(p.alias) LIKE '%".$q."%' 
                OR LOWER(p.`title`) LIKE '%".$q."%'
                OR LOWER(p.`memo_short`) LIKE '%".$q."%'
                OR LOWER(p.`memo`) LIKE '%".$q."%'                
                ) ";

            } 


			if(!empty($ar['products'])){
				$sql .= " 
				AND products > '0' ";
			}
			
			if(!empty($ar['pubs'])){
				$sql .= " 
				AND pubs > '0' ";
			}
			
			if(!empty($ar['categs'])){
				$sql .= " 
				AND child_categs > '0' ";
			}
			
			if(!empty($ar['comments'])){
				$sql .= " 
				AND comments_qty > '0' ";
			}
			
			if(!empty($ar['fotos'])){
				$sql .= " 
				AND fotos_qty > '0' ";
			}
			
			if(!empty($ar['files'])){
				$sql .= " 
				AND files_qty > '0' ";
			}
                  
        }else{
          $sql_num_rows .= " 
              WHERE 
                  p.id = sc.id_categ  
                  AND sc.id_categ = '".$id."'
                  AND sc.id_site = '".$site->vars['id']."' ";
  
          $sql .= " 
              WHERE 
                  p.id = sc.id_categ  
                  AND sc.id_categ = '".$id."'
                  AND sc.id_site = '".$site->vars['id']."' ";

        }
                        
        if(!isset($_GET['debug'])){
            $sql .= " AND p.active = '1' ";
            $sql_num_rows .= " AND p.active = '1' ";

			$sql .= " AND p.date_insert < DATE_FORMAT(NOW(), '%Y-%m-%d %H:%m') ";
			
			$sql_num_rows .= " AND p.date_insert < DATE_FORMAT(NOW(), '%Y-%m-%d %H:%m') ";			
		} 
		
		
		if(isset($ar['id_parent'])){
			$sql .= " AND p.id_parent = '".$ar['id_parent']."' AND p.id != '".$site->vars['default_id_categ']."' ";
            $sql_num_rows .= " AND p.id_parent = '".$ar['id_parent']."' AND p.id != '".$site->vars['default_id_categ']."' ";
		}
             
        $sql .= ' GROUP BY p.id ';
        
        /* GET-данные могут переназначить правило сортировки */
        if(!empty($_GET['orderby'])){
            $categ['sortby'] = $db->escape($_GET['orderby']);           
        }
        
        $sql .= ' ORDER BY '; 

        $sql .= ' sub2.sort, sub1.sort, p.sort, p.id_parent desc, p.title ';
              

        if(!isset($_GET['all']) && empty($ar['all'])){
            $sql .= ' LIMIT '.$current_page*ONPAGE.', '.ONPAGE.' ';
        }      
          
        $all_results = $db->get_var($sql_num_rows);
		
        $site->page['all_results'] = $all_results;
        $rows = $db->get_results($sql, ARRAY_A);

        if(!$rows || $db->num_rows == 0){ return array(); }

        if($all_results > $db->num_rows){
            $site->set_pages($all_results);
        }
                
        $items = array();

        foreach($rows as $row){
            $pic1 = array();
			if(!empty($row['pic1_id'])){				
				$pic1['id'] = $row['pic1_id'];
				$pic1['ext'] = $row['pic1_ext'];            
				$pic1['title'] = $row['pic1_name'];            
				$pic1['width'] = $row['pic1_width'];            
				$pic1['height'] = $row['pic1_height'];
				$pic1['path'] = !empty($row['pic1_id']) && !empty($row['pic1_ext']) 
					? '/upload/records/'.$row['pic1_id'].'.'.$row['pic1_ext']
					: '';            
				$pic1['url'] = !empty($row['pic1_id']) && !empty($row['pic1_ext']) 
					? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic1_id'].'.'.$row['pic1_ext']
					: '';            
			}
            
            $pic2 = array();
			if(!empty($row['pic2_id'])){				
				$pic2['id'] = $row['pic2_id'];           
				$pic2['ext'] = $row['pic2_ext'];            
				$pic2['title'] = $row['pic2_name'];            
				$pic2['width'] = $row['pic2_width'];            
				$pic2['height'] = $row['pic2_height'];
				$pic2['path'] = !empty($row['pic2_id']) && !empty($row['pic2_ext']) 
					? '/upload/records/'.$row['pic2_id'].'.'.$row['pic2_ext']
					: '';            
				$pic2['url'] = !empty($row['pic2_id']) && !empty($row['pic2_ext']) 
					? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic2_id'].'.'.$row['pic2_ext']
					: '';            
			}

            $pic3 = array();
			if(!empty($row['pic3_id'])){				
				$pic3['id'] = $row['pic3_id'];           
				$pic3['ext'] = $row['pic3_ext'];            
				$pic3['title'] = $row['pic3_name'];            
				$pic3['width'] = $row['pic3_width'];            
				$pic3['height'] = $row['pic3_height'];
				$pic3['path'] = !empty($row['pic3_id']) && !empty($row['pic3_ext']) 
					? '/upload/records/'.$row['pic3_id'].'.'.$row['pic3_ext']
					: '';            
				$pic3['url'] = !empty($row['pic3_id']) && !empty($row['pic3_ext']) 
					? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic3_id'].'.'.$row['pic3_ext']
					: '';            
			}

            $pic4 = array();
			if(!empty($row['pic4_id'])){				
				$pic4['id'] = $row['pic4_id'];           
				$pic4['ext'] = $row['pic4_ext'];            
				$pic4['title'] = $row['pic4_name'];            
				$pic4['width'] = $row['pic4_width'];            
				$pic4['height'] = $row['pic4_height'];
				$pic4['path'] = !empty($row['pic4_id']) && !empty($row['pic4_ext']) 
					? '/upload/records/'.$row['pic4_id'].'.'.$row['pic4_ext']
					: '';            
				$pic4['url'] = !empty($row['pic4_id']) && !empty($row['pic4_ext']) 
					? $site->uri['scheme'].'://'.$site->uri['host'].'/upload/records/'.$row['pic4_id'].'.'.$row['pic4_ext']
					: '';            
			}
			
            $pic = array(
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
			$row['pic'] = $pic;
            $row['date'] = date($site->vars['site_date_format'], strtotime($row['date_insert'])); 
            $row['time'] = date($site->vars['site_time_format'], strtotime($row['date_insert']));
            $items[] = $row;
        }
        return $items;
    }

?>