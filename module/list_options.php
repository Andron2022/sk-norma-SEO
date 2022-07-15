<?php

    /* list options ok */
    function list_product_options($id, $where, $site)
    {
        global $db;
        if($where != 'product'){ $where = 'pub'; }
		
        $sql = "SELECT  
                    g.id as group_id,
					g.to_show as to_show,
                    g.title as group_title, 
                    g.hide_title as group_hide, 
                    g.description as group_intro, 
                    g.opt_title as opt_title, 
                    g.value1 as opt_value1, 
                    g.value2 as opt_value2, 
                    g.value3 as opt_value3, 
                    v.value, v.value2, v.value3, 
                    o.id, o.title,
					o.alias, o.sort, o.type,
					o.if_select, o.show_in_filter, o.filter_type,
					o.show_in_list, o.filter_description, 
					o.after, o.icon					
					
                FROM ".$db->tables['option_values']." v
                LEFT JOIN ".$db->tables['options']." o ON (v.id_option = o.id)
                LEFT JOIN ".$db->tables['option_groups']." g ON (o.group_id = g.id 
                    AND (g.`where_placed` = '".$where."' OR g.`where_placed` = 'all'))
                WHERE 
                    v.id_product = '".$id."' 
                    AND v.`where_placed` = '".$where."' 
					AND v.`value` <> ''
				";

		if(empty($site->user['id']) || empty($site->user['active'])){
			/* только all */
			$sql .= " AND g.to_show = 'all' ";
		}elseif(!empty($site->user['prava']['info']) && !empty($site->user['prava']['orders'])){
			/* all + user + info + manager */
			$sql .= " AND g.to_show IN ('all','user','manager','info') ";
		}elseif(!empty($site->user['prava']['info'])){
			/* all + user + info */
			$sql .= " AND g.to_show IN ('all','user','info') ";
		}elseif(!empty($site->user['prava']['orders'])){
			/* all + user + manager */
			$sql .= " AND g.to_show IN ('all','user','manager') ";
		}else{
			/* all + user */
			$sql .= " AND g.to_show IN ('all','user') ";
		}
					
		$sql .= " ORDER BY g.sort, o.sort, o.title ";
        $rows = $db->get_results($sql, ARRAY_A);
        if(!$rows || $db->num_rows == 0){ return array(); }
        $ar = array();
        $in_list = array();
        foreach($rows as $k => $row){
            if($row['type'] == 'connected'){
                $type_rows = explode('|', $row['title']);
                $row['title'] = $type_rows[count($type_rows)-1]; 
            }
			
			if($row['type'] == 'categ' && !empty($row['value'])){
				$c = $db->get_row("SELECT id, `name`, `alias`, active  
						FROM ".$db->tables['publications']." 
						WHERE id = '".intval($row['value'])."'
					");
				if(!$c || $db->num_rows == 0){
					$row['value'] = '';
				}else{
					$row['value2'] = $row['value'];
					$row['value'] = !empty($c->active) 
						? '<a href="'.SI_URL.'/'.$c->alias.URL_END.'">'.$c->name.'</a>'
						: $c->name;
				}
			}

			if($row['type'] == 'products' && !empty($row['value'])){
				$c = $db->get_row("SELECT id, `name`, `alias`, active 
						FROM ".$db->tables['products']." 
						WHERE id = '".intval($row['value'])."'
					");
				if(!$c || $db->num_rows == 0){
					$row['value'] = '';
				}else{
					$row['value2'] = $row['value'];
					$row['value'] = !empty($c->active) 
						? '<a href="'.SI_URL.'/'.$c->alias.URL_END.'">'.$c->name.'</a>'
						: $c->name;
				}
			}
			
            if($row['show_in_list'] == 1){
				if(!is_numeric($row['alias']) && !isset($in_list[$row['alias']])){
					$in_list[$row['alias']] = $row;
				}else{
					$in_list[] = $row;
				}
            }
            
			if(!empty($row['alias']) 
				&& !isset($ar[$row['group_id']][$row['alias']]) 
				&& ($k > 0 || count($rows) == 1) && !is_numeric($row['alias'])){
				$ar[$row['group_id']][$row['alias']] = $row;								
			}else{
				$ar[$row['group_id']][] = $row;				
			} 
			
        }		
		
        $ar['in_list'] = $in_list;
		return $ar;
    }
    
    /* характеристики для списка товаров */
    function options_in_list($ids, $where, $site){
        $all = implode(', ',$ids);
        global $db;
        if($where != 'product'){ $where = 'pub'; }
		
        $sql = "SELECT 
                    v.id_product,
                    o.title,  
                    v.value, 
                    o.after, o.`icon`, o.type, o.group_id as `group`, o.alias,
                    g.opt_title as opt_title, 
					g.to_show, 
                    g.value1 as opt_value1, 
                    g.value2 as opt_value2, 
                    g.value3 as opt_value3, 
                    v.value2, v.value3, g.title as group_title 

				FROM ".$db->tables['option_values']." v
                LEFT JOIN ".$db->tables['options']." o ON (v.id_option = o.id)
                LEFT JOIN ".$db->tables['option_groups']." g ON (o.group_id = g.id 
                    AND (g.`where_placed` = '".$where."' OR g.`where_placed` = 'all'))
                WHERE 
                    v.id_product IN (".$all.") 
                    AND v.`where_placed` = '".$where."' ";
					
		if(!empty($site->is_filter)){
			$sql .= "
                    AND o.show_in_filter = '1' 					
					";
		}else{
			$sql .= "
                    AND o.show_in_list = '1' 					
					";
		}
					
		if(empty($site->user['id']) || empty($site->user['active'])){
			/* только all */
			$sql .= " AND g.to_show = 'all' ";
		}elseif(!empty($site->user['prava']['info']) && !empty($site->user['prava']['orders'])){
			/* all + user + info + manager */
			$sql .= " AND g.to_show IN ('all','user','manager','info') ";
		}elseif(!empty($site->user['prava']['info'])){
			/* all + user + info */
			$sql .= " AND g.to_show IN ('all','user','info') ";
		}elseif(!empty($site->user['prava']['orders'])){
			/* all + user + manager */
			$sql .= " AND g.to_show IN ('all','user','manager') ";
		}else{
			/* all + user */
			$sql .= " AND g.to_show IN ('all','user') ";
		}
					
        $sql .= " ORDER BY v.id_product, g.sort, o.sort, o.title ";		
				
        $rows = $db->get_results($sql, ARRAY_A);
        if(!$rows || $db->num_rows == 0){ return array(); }
        $ar = array();
        foreach($rows as $row){
                    
            if($row['type'] == 'connected'){ 
                $type_rows = explode('|', $row['title']);
                $row['title'] = $type_rows[count($type_rows)-1];
            }

            if($row['alias'] == 'city' && !empty($row['value'])){ 
                $ar[$row['id_product']]['city'] = $row['value']; 
            }

            if($row['alias'] == 'vendor' && !empty($row['value'])){ 
                $ar[$row['id_product']]['vendor'] = $row['value']; 
            }

            if($row['alias'] == 'vendorCode' && !empty($row['value'])){ 
                $ar[$row['id_product']]['vendorCode'] = $row['value']; 
            }
			
			if(!empty($row['alias']) && !isset($ar[$row['id_product']]['list'][$row['alias']])){
				$ar[$row['id_product']]['list'][$row['alias']] = $row;								
			}else{
				$ar[$row['id_product']]['list'][] = $row;				
			}            
            
        }
		
        return $ar;
    }

?>