<?php
	if (!class_exists('Site')) { return; }

    /***********************
    *
    * returns array 
    * with page datas
    * $_SESSION['wishlist']['pubs'] => array; 
    * $_SESSION['wishlist']['products'] => array; 
    *
    ***********************/ 

    class GetWishlist extends Site {

        function __construct()
        {
        }
                
        static public function get_list($site)
        {
            $tpl_page = 'pages/wishlist.html';
            $ar = GetEmptyPageArray();
            $site->uri['page'] = $tpl_page;
            $ar['page'] = $tpl_page;
            $ar['title'] = $site->GetMessage('wishlist','title');
            $ar['metatitle'] = $site->GetMessage('wishlist','metatitle');
            $ar['content'] = '';
                        
            $ar['list_pubs'] = list_pubs(0, $site, 'wishlist');      
            $ar['list_products'] = list_products(0, 'product', $site, 'wishlist');
            
			if(empty($ar['list_pubs']['list']) && empty($ar['list_products']['list'])){
				$ar['content'] = $site->GetMessage('wishlist','empty');
				return $ar;
			}

			if(!empty($ar['list_pubs']['list']) && !empty($ar['list_products']['list'])){
				$ar['content'] = sprintf($site->GetMessage('wishlist','exists_all'), '/wishlist/?where=products', '/wishlist/?where=pubs');
				return $ar;
			}
			
            if(empty($site->uri['params']['where'])){
                $url = $site->uri['site'].$site->uri['path'];
                if(count($ar['list_pubs']) == 0 && count($ar['list_products']) > 0){
                    header("Location: ".$url.'?where=products');
                    exit;
                }

                if(count($ar['list_pubs']) > 0 && count($ar['list_products']) == 0){
                    header("Location: ".$url.'?where=pubs');
                    exit;
                }

            }
            

            return $ar;           
        }
        
    }

?>