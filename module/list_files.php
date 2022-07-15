<?php

    // list files 
	// last update 28.06.2019
	
    function list_files($where, $id, $site)
    {
        global $db;
             
        $rows = $db->get_results("
              SELECT * 
              FROM ".$db->tables["uploaded_files"]." 
              WHERE
                record_id = '".$id."' AND record_type = '".$where."' 
              ORDER BY id_in_record, time_added desc
          ", ARRAY_A);
        if(!$rows || $db->num_rows == 0){ return array(); }        
        $images = array();
				
		if(isset($site->uri['scheme']) && isset($site->uri['host'])){
			$site_url = $site->uri['scheme'].'://'.$site->uri['host'];
		}elseif(defined('SI_URL2')){
			$site_url = SI_URL;
		}else{
			$site_url = '';
		}

        foreach($rows as $row){
              $md5_file =  md5($row['id']);
              $file_date = '';
              $file_date = date($site->vars['site_date_format'], $row['time_added']);
			  $file_path = UPLOAD.'/files/'.$md5_file.'.'.$row['ext'];
			  $content = '';
			  
			  if(file_exists($file_path) && !empty($row['allow_download']) && $row['ext'] == 'txt'){
				  $content = file_get_contents($file_path);
			  }	 
			  
			  $direct_url = !empty($row['allow_download']) 
				? $site_url.'/upload/files/'.$md5_file.'.'.$row['ext']
				: '';
			  
              $images[] = array(
                  'id' => $row['id'],
                  'url' => $site->uri['site'].'/getfile/?l='.$md5_file.'&ext='.$row['ext'].'&id='.$row['record_id'],
				  'direct_url' => $direct_url,
				  'content' => $content,
				  'file' => $file_path,
                  'title' => $row['title'],
                  'ext' => $row['ext'],
                  'size' => $row['size'],
                  'id_in_record' => $row['id_in_record'],
                  'allow_download' => $row['allow_download'],
                  'direct_link' => $row['direct_link'],
                  'date' => $file_date 
              );  
        }
        return $images;
    }

?>