<?php

    /* 
    list photos ok
    last modified 28.02.2020
	correct first key if not 1
	id_in_record - doesn't matter in sorting
	corrected domain url if in folder
	path added
    */
    function list_photos($where, $id, $site=false)
    {
        global $db;             
        $rows = $db->get_results("
              SELECT * 
              FROM ".$db->tables["uploaded_pics"]." 
              WHERE
                record_id = '".$id."' AND record_type = '".$where."' 
              ORDER BY id_in_record, width, height
          ");
		/*is_default desc, */
        if(!$rows || $db->num_rows == 0){ return array(); }
		
		if(isset($site->uri['scheme']) && isset($site->uri['host'])){
			$site_url = $site->uri['scheme'].'://'.$site->uri['host'];
		}elseif(defined('SI_URL')){
			$site_url = SI_URL;
		}else{
			$site_url = '';
		}		
		
        $img = array();
        foreach($rows as $row){
            if(!isset($current)) { $current = $row->id_in_record; }

            if($row->id_in_record != $current){
                $current = $row->id_in_record; 
            }
			
            $img[$current][] = array(
				'url' => $site_url.'/upload/records/'.$row->id.'.'.$row->ext,
				'path' => '/upload/records/'.$row->id.'.'.$row->ext,
				'title' => $row->title,
				'width' => $row->width,
				'height' => $row->height,
				'id_in_record' => $row->id_in_record,
				'id' => $row->id,
				'ext' => $row->ext,
				'ext_h1' => $row->ext_h1,
				'ext_desc' => $row->ext_desc,
				'ext_link' => $row->ext_link
            );
        }

        $images = array();
        foreach($img as $k=>$v){
			
            foreach($v as $k1 => $v1){
				
				if(isset($site->vars)){
					if(isset($site->vars['img_size1']['width']) && $site->vars['img_size1']['width'] == $v1['width']){
						$images[$k][1] = $v1; 
					}elseif(isset($site->vars['img_size2']['width']) && $site->vars['img_size2']['width'] == $v1['width']){
						$images[$k][2] = $v1; 
					}elseif(isset($site->vars['img_size3']['width']) && $site->vars['img_size3']['width'] == $v1['width']){
						$images[$k][3] = $v1; 
					}elseif(isset($site->vars['img_size4']['width']) && $site->vars['img_size4']['width'] == $v1['width']){
						$images[$k][4] = $v1; 
					}elseif(isset($site->vars['img_size5']['width']) && $site->vars['img_size5']['width'] == $v1['width']){
						$images[$k][5] = $v1; 
					}elseif(isset($site->vars['img_size6']['width']) && $site->vars['img_size6']['width'] == $v1['width']){
						$images[$k][6] = $v1; 
					}elseif(isset($site->vars['img_size7']['width']) && $site->vars['img_size7']['width'] == $v1['width']){
						$images[$k][7] = $v1; 
					}elseif(isset($site->vars['img_size8']['width']) && $site->vars['img_size8']['width'] == $v1['width']){
						$images[$k][8] = $v1; 
					}elseif(isset($site->vars['img_size9']['width']) && $site->vars['img_size9']['width'] == $v1['width']){
						$images[$k][9] = $v1; 
					}elseif(isset($site->vars['img_size10']['width']) && $site->vars['img_size10']['width'] == $v1['width']){
						$images[$k][10] = $v1; 
					}else{					
						$images[$k][11] = $v1; 
					}
					
					if($k1+1 == count($v) && empty($images[$k][11])){
						$images[$k][11] = $v1;
					}
				}else{
					//echo $k.' - '.$k1; echo '<br>';
					$images[$k][$k1+1] = $v1; 
					
					if($k1+1 == count($v) && empty($images[$k][11])){
						$images[$k][11] = $v1;
					}
				}
			
            }
			
			if(!isset($images[$k][4]) && isset($images[$k][11])){
				$images[$k][4] = $images[$k][11];
			}
			
			if(!isset($images[$k][3]) && isset($images[$k][11])){
				$images[$k][3] = $images[$k][11];
			}

			if(!isset($images[$k][2]) && isset($images[$k][11])){
				$images[$k][2] = $images[$k][11];
			}
			
			if(!isset($images[$k][1]) && isset($images[$k][11])){
				$images[$k][1] = $images[$k][11];
			}
			ksort($images[$k]);
			
        }
		
		/* correct first key, if not 1 */		
		if(key($images) != 1){
			$k = key($images);
			$images[1] = $images[$k];
			unset($images[$k]);
		}
		ksort($images);				
        return $images;
    }

?>