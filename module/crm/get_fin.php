<?php

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
    *
    ***********************/ 
       
    $s = new Crm;

    class Crm extends Site {

        function __construct()
        {
        }
                
        static public function get_fin($site)
        {
			if(empty($site->user['id'])){
				$ar['need_auth'] = 1;
				$ar['page'] = 'pages/403.html';
			}elseif(empty($site->user['prava']['crm'])){
				$ar['page'] = 'pages/403.html';
			}else{
				//$ar['page'] = 'crm/index.html';
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