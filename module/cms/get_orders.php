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
                
        static public function get_orders($site)
        {
			if(empty($site->user['id'])){
				$ar['need_auth'] = 1;
				$ar['page'] = 'pages/403.html';
			}elseif(empty($site->user['prava']['orders'])){
				$ar['page'] = 'pages/403.html';
			}else{
				//$ar['page'] = 'cms/index.html';
				$ar['page'] = 'pages/coming.html';
			}
			/*
            $tpl_page = 'contact.html';
            $ar = GetEmptyPageArray();
            $ar['page'] = $tpl_page;
            $site->uri['page'] = $tpl_page;
            $site->page = $ar;  
			*/
			return $ar;
        }
        
    }

?>