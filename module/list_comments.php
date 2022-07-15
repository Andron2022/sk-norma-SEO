<?php
    /*
        list_comments
        last modified 08.10.2016
        Список комментов к странице        
    */
    
      
    
    function list_comments($id, $page_type, $site, $qty=0, $active=1)
    {
        global $db;
		// $page_type = search - поиск
        $desc = !empty($site->vars['sys_comments']) ? 'desc' : '';
        $limit = !empty($site->vars['sys_comments_limit']) ? intval($site->vars['sys_comments_limit']) : 0;
		if($qty > 0){ $limit = $qty; }
		
		$sql_num = " SELECT COUNT(*) 
						FROM ".$db->tables['comments']." c 
						LEFT JOIN ".$db->tables['users']." u on
                    (u.id = c.userid)  
					";
        $sql = "SELECT c.*, 
                    u.`name` as username, 
					u.login as login, 
                    u2.`name` as sub_username, 
                    c2.id as sub_id, 
                    c2.userid as sub_userid, 
                    c2.comment_text as sub_comment_text, 
                    c2.ddate as sub_ddate,
					c2.unreg_email as sub_title, 
					p.id as pic_id,
					p.width as pic_width,
					p.height as pic_height,
					p.title as pic_title,
					p.ext as pic_ext,
					
				IF(c.record_type = 'pub', 
					(SELECT pp2.`name`
						FROM ".$db->tables['publications']." pp2, 
						".$db->tables['site_publications']." pc2
						WHERE pp2.id = c.record_id 
						AND pp2.active = '1' 
						AND pp2.id = pc2.id_publications 
						AND pc2.id_site = '".$site->vars['id']."'
						GROUP BY pp2.id
					)
					,
					IF(c.record_type = 'categ', 
						(SELECT cc.`title`
							FROM ".$db->tables['categs']." cc,
							".$db->tables['site_categ']." s 
							WHERE cc.id = c.record_id 
							AND cc.active = '1' 
							AND cc.id = s.id_categ 
							AND s.id_site = '".$site->vars['id']."' 
							GROUP BY c.id
						)
						,
						(SELECT pp3.`name`
							FROM ".$db->tables['products']." pp3, 
								".$db->tables['pub_categs']." pc3,
								".$db->tables['categs']." cc3, 
								".$db->tables['site_categ']." s3
							WHERE pp3.id = c.record_id 
								AND pp3.id = pc3.id_pub 
								AND pc3.where_placed = 'product' 
								AND pc3.id_categ = s3.id_categ 
								AND s3.id_site = '".$site->vars['id']."' 
								AND s3.id_categ = cc3.id
							GROUP BY pp3.id
						)		
					)					
				) as page_title,
				
				IF(c.record_type = 'pub', 
					(SELECT `alias`
						FROM ".$db->tables['publications']." 
						WHERE id = c.record_id
					)
					,
					IF(c.record_type = 'categ', 
						(SELECT `alias`
							FROM ".$db->tables['categs']." 
							WHERE id = c.record_id 
						)
						,
						(SELECT `alias`
							FROM ".$db->tables['products']." 
							WHERE id = c.record_id 
						)		
					)					
				) as page_alias
					
                    
                FROM ".$db->tables['comments']." c
                LEFT JOIN ".$db->tables['users']." u on
                    (u.id = c.userid)  
                LEFT JOIN ".$db->tables['comments']." c2 on 
                    (c2.record_type = 'comment' 
                    AND c2.record_id = c.id
                    AND c2.active = '1')
                LEFT JOIN ".$db->tables['users']." u2 on
                    (u2.id = c2.userid)  
				LEFT JOIN ".$db->tables['uploaded_pics']." p on
                    (p.record_id = c.id AND p.record_type = 'comment')  
				";

				
		if(!empty($id) && !empty($page_type)){
			$str = "WHERE c.record_type = '".$page_type."' 
                AND c.record_id = '".$id."' ";
			$sql .= $str;
			$sql_num .= $str;
		}elseif($page_type == 'search' && !empty($site->uri['params']['q'])){
			$q = $db->escape(mb_strtolower($site->uri['params']['q'], 'UTF-8'));
			$str = "WHERE  (
					LOWER(u.name) LIKE '%".$q."%' 
					OR LOWER(u.email) LIKE '%".$q."%' 
					OR LOWER(u.login) LIKE '%".$q."%' 
					OR LOWER(c.comment_text) LIKE '%".$q."%' 
					OR LOWER(c.ip_address) LIKE '%".$q."%' 
					OR LOWER(c.unreg_email) LIKE '%".$q."%' 
					OR LOWER(c.ext_h1) LIKE '%".$q."%' 
				)
				";
			$sql .= $str;
			$sql_num .= $str;
		}else{
			$str = "WHERE c.record_type <> '' 
                AND c.record_id <> '0' ";
			$sql .= $str;
			$sql_num .= $str;
		}
		
		if($active == 1){
			$str = " AND c.`active` = '1'   ";
			$sql .= $str;
			$sql_num .= $str;
		}
            
		
			
		$sql .= " AND c.record_type IN ('pub','product','categ','comment') ";
		$sql_num .= " AND c.record_type IN ('pub','product','categ','comment') ";
		
        if($limit > 0 && $active == 1 && !empty($id)){ 
            $sql .= " ORDER BY RAND() ";
            $sql .= " LIMIT ".$limit; 
			$sql_num = '';
        }else{
            $sql .= " ORDER BY c.`ddate` $desc ";                
        }
		
		if(!empty($sql_num)){
			$all_results = $db->get_var($sql_num);
			$site->page['all_results'] = $all_results;
		}
		
		if(!empty($all_results)){
			$current_page = empty($_GET['page']) ? 0 : intval($_GET['page']);
			$sql .= ' 
		LIMIT '.$current_page*ONPAGE.', '.ONPAGE.' ';
		}
		
 		$old = $db->cache_queries;
		$db->cache_queries = false;
       $rows = $db->get_results($sql, ARRAY_A);		
		$db->cache_queries = $old;
		//$db->debug(); exit;
		
        if(!$rows || $db->num_rows == 0){ return array(); }
        $items = array();
        foreach($rows as $row){
			
			if(!empty($id) || 
				(empty($id) && !empty($row['page_title']))
			){
				
				$nrow = array();
				$nrow['id'] = $row['id'];
				$avatars = array();
				$av_folders = array('mini','small','big');
				foreach($av_folders as $i){
					if(file_exists(AVATARS.$i.'/'.md5($row['userid']).'.jpg')){
					  $avatars[$i] = $site->uri['scheme'].'://'.$site->uri['host'].'/upload/avatars/'.$i.'/'.md5($row['userid']).'.jpg';                
				  }
				}
				
				$nrow['pic'] = array();
				if(!empty($row['pic_id']) && !empty($row['pic_ext'])){
					$nrow['pic'] = array(
						'width' => $row['pic_width'],
						'height' => $row['pic_height'],
						'title' => $row['pic_title'],
						'url' => '/upload/records/'.$row['pic_id'].'.'.$row['pic_ext'],
						'id' => $row['pic_id'],
						'ext' => $row['pic_ext']
					);
				}
				$nrow['avatar'] = $avatars;

				$nrow['page_title'] = $row['page_title'];
				$nrow['page_alias'] = $row['page_alias'];
				$nrow['page_link'] = $site->uri['idn'].$row['page_alias'].URL_END;

				$nrow['name'] = $row['username'];
				$nrow['login'] = $row['login'];
				$nrow['title'] = $row['unreg_email'];
				$nrow['message'] = nl2br($row['comment_text']);
				$nrow['ext1'] = $row['ext_h1'];
				$nrow['ext2'] = $row['ext_desc'];
				$nrow['notify'] = $row['notify'];
				$nrow['active'] = $row['active'];
				$nrow['date_insert'] = $row['ddate']; 
				$nrow['date'] = date($site->vars['site_date_format'], strtotime($row['ddate'])); 
				$nrow['time'] = date($site->vars['site_time_format'], strtotime($row['ddate']));
				
				$nrow['sub_id'] = $row['sub_id'];

				$avatars = array();
				$av_folders = array('mini','small','big');
				foreach($av_folders as $i){
					if(file_exists(AVATARS.$i.'/'.md5($row['sub_userid']).'.jpg')){
					  $avatars[$i] = $site->uri['scheme'].'://'.$site->uri['host'].'/upload/avatars/'.$i.'/'.md5($row['sub_userid']).'.jpg';                
				  }
				}
				$nrow['sub_avatar'] = $avatars;
				
				$nrow['sub_name'] = empty($row['sub_title']) ? $row['sub_username'] : $row['sub_title'];
				$nrow['sub_message'] = nl2br($row['sub_comment_text']);
				$nrow['sub_date'] = date($site->vars['site_date_format'], strtotime($row['sub_ddate']));
				$nrow['sub_time'] = date($site->vars['site_time_format'], strtotime($row['sub_ddate']));
            
				if(isset($items[$row['id']])){
					$items[] = $nrow;				
				}else{
					$items[$row['id']] = $nrow;
				}
			}
        }
        return $items;
    }
	
	
	function list_filter_comments(){		
		global $db;
		$query = "SELECT c.*, 
			  p.`name` as pub_title, p.id as pub_id, 
			  categ.`title` as categ_title, categ.id as categ_id, 
			  product.`name` as product_title, product.id as product_id,
			  
			  (SELECT COUNT(*) 
				FROM ".$db->tables['comments']." 
				WHERE record_type = c.record_type 
				AND record_id = c.record_id
				) as qty
			FROM ".$db->tables['comments']." c 
			LEFT JOIN ".$db->tables['publications']." p on (p.id = c.record_id AND c.record_type = 'pub') 
			LEFT JOIN ".$db->tables['categs']." categ on (categ.id = c.record_id AND c.record_type = 'categ') 
			LEFT JOIN ".$db->tables['products']." product on (product.id = c.record_id AND c.record_type = 'product') 
			WHERE record_type IN ('categ','pub','product')
			GROUP BY c.record_type, c.record_id 
			ORDER BY c.record_type, p.name, categ.title, c.active, c.`ddate` DESC ";
		$rows = $db->get_results($query, ARRAY_A);
		return $rows;
	}

?>