<?php
	if (!class_exists('Site')) { return; }

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
    *
    ***********************/ 
       
    $s = new Option;

    class Option extends Site {

        function __construct()
        {
        }
                
        static public function get_page($site)
        {
            $tpl_page = 'feedback.html';
            $ar = GetEmptyPageArray();
            $ar['page'] = $tpl_page;
            $ar['title'] = $site->GetMessage('words','contact_us');
            $ar['metatitle'] = $site->GetMessage('words','contact_us');
			
            $site->uri['page'] = $tpl_page;
            $site->page = $ar;  
			return $ar;
        }
		
		static public function option_by_tag($site)
        {
			include_once(MODULE.'/list_tags.php');
			if(empty($site->uri['alias'])){ return all_tags($site); }
			if(strlen($site->uri['alias']) < 5){ return all_tags($site); }
			$str = str_replace('tag/','',$site->uri['alias']);
			$tags = explode('/', $str);			
			if(empty($tags)){ return; }
			
			$categ = array(
				'id' => 0,
				'by_option' => array(
					'alias' => 'tag',
					'value' => $tags
				),				
			);
			
            $tpl_page = 'category.html';
            $ar = GetEmptyPageArray();
            $ar['page'] = $tpl_page;
			$ar['list_pubs'] = list_pubs($categ, $site, 'by_option');
			
			if(empty($ar['list_pubs'])){
				$ar['content'] = $site->GetMessage('sitemap', 'empty');
			}
			//$site->print_r($ar['list_pubs'],1);
			$title = count($tags) > 1 
				? $site->GetMessage('words','tags')
				: $site->GetMessage('words','tag');
			
			foreach($tags as $k => $v){
				$title .= $k == 0 ? ': ' : ', ';
				$title .= $v;
			}

            $ar['title'] = $title;
            $ar['metatitle'] = $title;
            $site->uri['page'] = $tpl_page;
            $site->page = $ar;  
			return $ar;
        }
        
    }

?>