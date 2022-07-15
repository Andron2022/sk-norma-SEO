<?php
	if (!class_exists('Site')) { return; }

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
    *
    ***********************/ 

    class Subscribe extends Site {

        function __construct()
        {
        }
                
        static public function get_subscription($site)
        {
            $site->uri['page'] = 'pages/subscription.html';
			$ar = empty($site->page) ? GetEmptyPageArray() : $site->page;
            $ar['page'] = $site->uri['page'];
            $ar['title'] = $site->GetMessage('subscription','title');
            $ar['metatitle'] = $site->GetMessage('subscription','metatitle');
            $ar['content'] = '';
            return $ar;           
        }
        
        static public function added($site){
            $site->uri['page'] = 'blank.html';
			$ar = empty($site->page) ? GetEmptyPageArray() : $site->page;
            $ar['page'] = $site->uri['page'];
            $ar['title'] = $site->GetMessage('subscription','title');
            $ar['metatitle'] = $site->GetMessage('subscription','metatitle');
            $ar['content'] = $site->GetMessage('subscription','added');
			return $ar;
        }


        static public function toadd($site){
			global $db;
			if(!empty($site->uri['params']['key'])){
				$try_add = $site->uri['params']['key'];
				// md5 id + email
				global $db;
				$sql = "UPDATE ".$db->tables['users']." SET 
					`news` = '1', `active` = '1' 
					WHERE MD5(CONCAT(id,email)) = '".$db->escape($try_add)."' ";
				if($db->query($sql)){
					$user_id = $db->get_var("SELECT id FROM ".$db->tables['users']." 
						WHERE MD5(CONCAT(id,email)) = '".$db->escape($try_add)."' 
					");
					$ar = prepare_user_info($user_id);
					send_email_event('subscriber_new', $ar);					
				}
			}
			
			return $site->redirect('/subscription/added/');
        }

        static public function deleted($site){
            $site->uri['page'] = 'blank.html';
			$ar = empty($site->page) ? GetEmptyPageArray() : $site->page;
            $ar['page'] = $site->uri['page'];
            $ar['title'] = $site->GetMessage('subscription','title');
            $ar['metatitle'] = $site->GetMessage('subscription','metatitle');
            $ar['content'] = $site->GetMessage('subscription','deleted');
			return $ar;
        }

        static public function todelete($site){
			if(!empty($site->uri['params']['key'])){
				$try_add = $site->uri['params']['key'];
				// md5 id + email
				global $db;
				$sql = "UPDATE ".$db->tables['users']." SET `news` = '0' 
					WHERE MD5(CONCAT(id,email)) = '".$db->escape($try_add)."' ";
				$db->query($sql);
			}
			return $site->redirect('/subscription/deleted/');
        }
		        
    }

?>