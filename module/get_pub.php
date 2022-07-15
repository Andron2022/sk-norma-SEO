<?php
	if (!class_exists('Site')) { return; }

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
    * updated 27.10.2016 users datas added
    ***********************/ 

    class Pub extends Site {

        function __construct()
        {
        }
                
        static public function get_page($site)
        {
            global $db;
            include_once('list_photos.php');
            include_once('list_files.php');
            include_once('list_pubs.php');
            include_once('list_products.php');
            include_once('list_comments.php');
            include_once('list_options.php');

            $tpl_page = 'pub.html';
            $ar = GetEmptyPageArray();

            $sql = "SELECT 
                            p.multitpl as page,  
                            p.meta_title as metatitle, 
                            p.meta_description as description,
                            p.meta_keywords as keywords,
                            p.memo as content, 
                            p.anons as intro, 
                            p.name as title, 
                            p.*, 
							u.`name` as user_name,
							u.login as user_login,
							u.gender as user_gender, 
                            
                            (SELECT COUNT(DISTINCT `id_in_record`) 
                                FROM ".$db->tables['uploaded_pics']." 
                                WHERE record_id = p.id
                                AND record_type = 'pub') as qty_photos,                  

                            (SELECT COUNT(*) 
                                FROM ".$db->tables['uploaded_files']." 
                                WHERE record_id = p.id
                                AND record_type = 'pub') as qty_files,                  
                                                                         
                            (SELECT COUNT(*) 
                                FROM ".$db->tables['connections']." 
                                WHERE name1 = 'pub' AND name2 = 'pub' 
								AND (id1 = p.id OR id2 = p.id)) as qty_pubs_connected,                  

								(SELECT COUNT(*) 
                                FROM ".$db->tables['pub_to_product']." 
                                WHERE id_pub = p.id ) as qty_products,                                              

                            (SELECT COUNT(*) 
                                FROM ".$db->tables['comments']." 
                                WHERE record_id = p.id AND record_type = 'pub') as qty_comments,                                              

                            (SELECT COUNT(*) 
                                FROM ".$db->tables['option_values']." 
                                WHERE id_product = p.id AND `where_placed` = 'pub') as qty_options                                              

                        FROM 
                            ".$db->tables['site_publications']." sp, 
							".$db->tables['publications']." p 
						LEFT JOIN ".$db->tables['users']." u ON (p.user_id = u.id)  
                        WHERE p.alias = '".$site->uri['alias']."' 
                        AND p.id = sp.id_publications 
                        AND sp.id_site = '".$site->vars['id']."'
                        ";
						
			if(!isset($_GET['debug'])){
				$sql .= " AND p.date_insert < CONCAT(CURDATE(), ' ',CURTIME()) ";
				$sql .= " AND p.active = '1' ";
			}

            $ar = $db->get_row($sql, ARRAY_A);

            if(!empty($db->last_error)){ return $site->db_error($db->last_error); }
            if($db->num_rows == 0){ return $site->error_page(404); }
            if($db->num_rows == 1){
                
                $site->set_visited_cookie($ar['id'], 'pubs');
                $site->register_counter('pub', $ar['id']);
            
                unset($ar['meta_description']);
                unset($ar['meta_keywords']);
                unset($ar['meta_title']);
                unset($ar['name']);
                unset($ar['anons']);
                unset($ar['memo']);
            
                if(empty($ar['active']) && !isset($_GET['debug'])){
                    return $site->error_page(404);
                }                             

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

                $ar['date'] = date($site->vars['site_date_format'], strtotime($ar['date_insert'])); 
                $ar['date_update'] = date($site->vars['site_date_format'], strtotime($ar['ddate']));
                
                /* создадим массив привязанных разделов */
                $ar['categs'] = $db->get_results("SELECT c.id, c.title, 
                                c.id_parent, c.alias, c.shop, 
                                c.f_spec, c.icon, c.sortby, 
								o.to_show as access 
                    
                                FROM ".$db->tables['pub_categs']." pc,
                                    ".$db->tables['site_categ']." sc,
                                    ".$db->tables['categs']." c     
								LEFT JOIN `".$db->tables['categ_options']."` co 
									ON (c.id = co.id_categ AND co.where_placed = 'categ') 
								LEFT JOIN `".$db->tables['option_groups']."` o 
									ON (co.id_option = o.id 
									AND o.where_placed = 'pub'
									AND o.to_show != 'all') 	
									
                                WHERE pc.id_pub = '".$ar['id']."' 
                                AND pc.`where_placed` = 'pub'                                 
                                AND sc.id_categ = pc.id_categ 
                                AND sc.id_site = '".$site->vars['id']."' 
                                AND sc.id_categ = c.id 
                                AND c.active = '1' 
								
								GROUP BY c.id
                                ORDER BY c.shop desc, c.f_spec desc, c.sort, c.title 
								
                                ", ARRAY_A);
                $ar['categs_ar'] = array();
                foreach($ar['categs'] as $v){
                    $ar['categs_ar'][] = $v['id'];                    
                }                
                
                unset($ar['multitpl']);
                unset($ar['ddate']);
                unset($ar['date_insert']);

                if($ar['qty_photos'] > 0){
                    $ar['list_photos'] = list_photos('pub', $ar['id'], $site);
                }
            
                if($ar['qty_files'] > 0){
                    $ar['list_files'] = list_files('pub', $ar['id'], $site);
                }

                if($ar['qty_products'] > 0 ){
                    $ar['connected_products'] = list_products($ar['id'], 'pub', $site, 'connected');
                }

                if($ar['qty_pubs_connected'] > 0 ){
                    $ar['connected_pubs'] = list_pubs($ar, $site, 'pub_connected');
					//$ar['connected_pubs'] $ar['list_pubs']
                }
				
                if($ar['qty_comments'] > 0){
                    $ar['list_comments'] = list_comments($ar['id'], 'pub', $site);
                }

                if($ar['qty_options'] > 0){
                    $ar['list_options'] = list_product_options($ar['id'], 'pub', $site);
                }

                $ar['breadcrumbs'] = array();
                $breadcrumbs_ar = array();
                $breadcrumbs_ar[] = array(
                    'id' => $ar['id'],
                    'title' => $ar['title'],
                    'link' => $site->vars['site_url'].'/'.$ar['alias'].URL_END,
                    'alias' => $ar['alias']
                );
                foreach($ar['categs'] as $v){
                    $ar['breadcrumbs'][] = $site->get_breadcrumbs($v['id'], $breadcrumbs_ar);
                }

                return $ar;               
            }
            
        }
        
    }

?>