<?php

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
    *
    ***********************/ 
       
    $s = new Cms;

    class Cms extends Site {

        function __construct()
        {
        }
                
        static public function get_cms($site)
        {
			global $tpl, $path;
			$site->tpl = "/tpl/cms/";
			if(empty($site->user['id'])){
				$ar['need_auth'] = 1;
				$ar['page'] = 'pages/403.html';
			}elseif(empty($site->user['prava']['orders'])){
				$ar['page'] = 'pages/403.html';
			}else{				
				$ar['page'] = 'pages/coming.html';
				//$ar['page'] = 'pages/coming.html';
			}
			
			return $ar;
        }
        
    }

?>