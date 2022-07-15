<?php
	if (!class_exists('Site')) { return; }

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
    * $q1 with connected
    ***********************/ 

    class Product extends Site {

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

            $tpl_page = 'category.html';
            $ar = GetEmptyPageArray();
            			
            $sql = "SELECT 
                            p.multitpl as page,  
                            p.meta_title as metatitle, 
                            p.meta_description as description,
                            p.meta_keywords as keywords,
                            p.memo as content, 
                            p.memo_short as intro,
                            p.name as title,
                            p.name_short as title_short, 
                            p.*, 
                            
                            (SELECT count(*) FROM ".$db->tables['counter']." 
                                WHERE `for_page` = 'product' AND id_page = p.id 
                                AND `time` > DATE_ADD(NOW(), INTERVAL -30 DAY)) as monthviews,                                              

                            (SELECT COUNT(DISTINCT `id_in_record`) 
                                FROM ".$db->tables['uploaded_pics']." 
                                WHERE record_id = p.id
                                AND record_type = 'product') as qty_photos,                  

                            (SELECT COUNT(*) 
                                FROM ".$db->tables['uploaded_files']." 
                                WHERE record_id = p.id
                                AND record_type = 'product') as qty_files,                  
                                                                         
                            (SELECT COUNT(*) 
                                FROM ".$db->tables['pub_to_product']." ptp, 
                                ".$db->tables['site_publications']." sp  
                                WHERE ptp.id_product = p.id AND ptp.id_pub = sp.id_publications 
                                AND sp.id_site = '".$site->vars['id']."') as qty_pubs,                  

                            (SELECT COUNT(*) 
                                FROM ".$db->tables['product_to_product']." as ptp2
                                WHERE (ptp2.product1 = p.id AND ptp2.product2 <> p.id) 
                                OR (ptp2.product2 = p.id AND ptp2.product1 <> p.id) 
                                ) as qty_products,                                              

                            (SELECT COUNT(*) 
                                FROM ".$db->tables['comments']." 
                                WHERE record_id = p.id AND record_type = 'product') as qty_comments,                                              

                            (SELECT COUNT(*) 
                                FROM ".$db->tables['option_values']." 
                                WHERE id_product = p.id AND `where_placed` = 'product' 
                                AND `value` <> '') as qty_options,                                              

                            (SELECT COUNT(*) 
                                FROM ".$db->tables['pub_categs']." pc3,
                                    ".$db->tables['site_categ']." sc3,
                                    ".$db->tables['categs']." c3                                   
                                WHERE pc3.id_pub = p.id AND pc3.`where_placed` = 'product'                                 
                                AND sc3.id_categ = pc3.id_categ 
                                AND sc3.id_site = '".$site->vars['id']."' 
                                AND sc3.id_categ = c3.id 
                                AND c3.active = '1'                                 
                            )as qty_topcategs                                                                              

                        FROM ".$db->tables['products']." p
                        WHERE p.alias = '".$site->uri['alias']."' 
                        ";
			if(!isset($_GET['debug'])){
				$sql .= " AND p.date_insert < CONCAT(CURDATE(), ' ',CURTIME()) ";
			}		
			
            $ar = $db->get_row($sql, ARRAY_A);
			//$db->debug(); exit;
			if(!empty($db->last_error)){ return $site->db_error($db->last_error); }			
            if($db->num_rows == 0){ return $site->error_page(404); }
            if($db->num_rows == 1){
				
				$site->set_visited_cookie($ar['id'], 'products');
                $site->register_counter('product', $ar['id']);

                unset($ar['meta_description']);
                unset($ar['meta_keywords']);
                unset($ar['meta_title']);
                unset($ar['memo']);
                unset($ar['memo_short']);
                unset($ar['name']);
                unset($ar['name_short']);
                unset($ar['multitpl']);
            
                if(empty($ar['active']) && !isset($_GET['debug'])){
                    return $site->error_page(404);
                }

                /* если верхний раздел не задан, то страница 404 */
                if(empty($ar['qty_topcategs'])){
                    return $site->error_page(404);
                }else{
                    /* создадим массив привязанных разделов */
                    $ar['categs'] = $db->get_results("SELECT c.id, c.title, 
                                c.id_parent, c.alias, c.shop, 
                                c.f_spec, c.icon, c.memo_short as intro                    
                    
                                FROM ".$db->tables['pub_categs']." pc,
                                    ".$db->tables['site_categ']." sc,
                                    ".$db->tables['categs']." c                                   
                                WHERE pc.id_pub = '".$ar['id']."' 
                                AND pc.`where_placed` = 'product'                                 
                                AND sc.id_categ = pc.id_categ 
                                AND sc.id_site = '".$site->vars['id']."' 
                                AND sc.id_categ = c.id 
                                AND c.active = '1'  
                                ORDER BY c.shop desc, c.f_spec desc, c.sort, c.title
                                ", ARRAY_A);
                    $ar['categs_ar'] = array();
                    foreach($ar['categs'] as $v){
                        $ar['categs_ar'][] = $v['id'];                    
                    } 
                }


                $ar['date'] = date($site->vars['site_date_format'], strtotime($ar['date_insert'])); 
                $ar['date_update'] = date($site->vars['site_date_format'], strtotime($ar['date_update'])); 
                $ar['price_old'] = $ar['price_spec'];
                
                unset($ar['date_insert']);
                unset($ar['date_update']);
                unset($ar['price_spec']);
                
                $ar['price_formatted'] = $site->price_formatted($ar['price'], $ar['currency']);
                $ar['price_old_formatted'] = $ar['price_old'] > 0 ? 
                    $site->price_formatted($ar['price_old'], $ar['currency']) : '';
                                            
                if($ar['qty_photos'] > 0){
                    $ar['list_photos'] = list_photos('product', $ar['id'], $site);
                }
            
                if($ar['qty_files'] > 0){
                    $ar['list_files'] = list_files('product', $ar['id'], $site);
                }
            
                if($ar['qty_comments'] > 0){
                    $ar['list_comments'] = list_comments($ar['id'], 'product', $site);
                }


                /*******************/
                /* ОСТАНОВИЛСЯ ТУТ */
                /*******************/

                if($ar['qty_options'] > 0){
                    $ar['list_options'] = list_product_options($ar['id'], 'product', $site);
                }

                if($ar['qty_pubs'] > 0){
					$q1 = isset($site->vars['sys_qty_connected']) ? intval($site->vars['sys_qty_connected']) : 6;
					$ar['connected_pubs'] = list_pubs($ar, $site, 'connected', $q1);
                }
				
                if($ar['qty_products'] > 0){
					$q1 = isset($site->vars['sys_qty_connected']) ? intval($site->vars['sys_qty_connected']) : 6;
                    $ar['connected_products'] = list_products($ar['id'], 'product', $site, 'connected', $q1);
                }

                if($ar['qty_topcategs'] > 0){
                    $ar['list_topcategs'] = get_categs('categs', $ar['categs_ar'][0], 0, $ar['categs_ar'][0], $site);
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