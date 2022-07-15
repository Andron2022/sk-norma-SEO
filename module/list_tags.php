<?php
    /*
        list_tags
        last modified 11.02.2017

        Сформируем список тегов для сайта
		тег = характеристика с алиасом tag
		
		если задан id категории, то выведем только теги для этой категории, 
		если нет - то все.
		
		++ проверить работу с тегами товаров
    */
    
    function list_tags($site, $categ=0)
    {
		global $db;		
		$sql = "SELECT ov.*, 
				(SELECT COUNT(*) 
					FROM ".$db->tables['option_values']." ov2, 
						".$db->tables['publications']." p2, 
						".$db->tables['site_publications']." sp2
					WHERE ov2.id_option = o.id 
						AND UPPER(ov2.value) = UPPER(ov.value) 
						AND ov2.where_placed = 'pub'
						AND ov2.id_product = p2.id 
						AND p2.id = sp2.id_publications  
						AND sp2.id_site = '".$site->id."' 						
				) as qty
			FROM ".$db->tables['options']." o,  
			".$db->tables['categ_options']." co,
			".$db->tables['site_categ']." sc, 
				".$db->tables['option_values']." ov
			
			WHERE o.alias = 'tag'  
				AND o.group_id = co.id_option 
				AND co.id_categ = sc.id_categ 
				AND sc.id_site = '".$site->id."' 
				AND o.id = ov.id_option 
				AND ov.value <> '' ";
		if(!empty($categ)){
			$sql .= " AND co.id_categ = '".$categ."' ";
		}		
		$sql .= "
			GROUP BY ov.value 
			ORDER BY qty DESC 
		";
		$tags = $db->get_results($sql, ARRAY_A);
		if($categ > 0 && empty($tags)){ return list_tags($site, 0); }
		if(!empty($tags)){
			/* удалим не привязанные к сайту */
			foreach($tags as $k => $v){
				if(empty($v['qty'])){ unset($tags[$k]); }				
			}
		}
		$tags=array_slice($tags,0,12);
        return $tags;
    }
	
	function all_tags($site){
		$tags = list_tags($site);
		$tpl_page = 'blank.html';
		$ar = !empty($site->page) ? $site->page : GetEmptyPageArray();
        $ar['page'] = $tpl_page;
		$ar['list_tags'] = $tags;
			
		if(empty($tags)){
			$ar['error'] = $site->GetMessage('sitemap', 'empty');
		}else{
			$str = '<ul style="-moz-column-count: 3; 
				-moz-column-gap: 2.5em; 
				-webkit-column-count: 3; 
				-webkit-column-gap: 2.5em; 
				column-count: 3; 
				column-gap: 3em;">
			';
			foreach($tags as $v){
				$str .= "<li><a href='".$site->vars['site_url'].URL_END."tag/".$v['value']."/'>".$v['value']." (".$v['qty'].")</a></li>
				";
			}
			$str .= "</ul>";
			$ar['content'] = $str;
		}
		
		$title = $site->GetMessage('words','tags');
			
		$ar['title'] = $title;
        $ar['metatitle'] = $title;
        $site->uri['page'] = $tpl_page;
        $site->page = $ar;  
		
		//$site->print_r($ar,1);
		return $ar;
		
		
	}

?>