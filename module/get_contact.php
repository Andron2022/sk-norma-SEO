<?php
	if (!class_exists('Site')) { return; }

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
    *
    ***********************/ 
       
    $s = new Contact;

    class Contact extends Site {

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
        
    }

?>