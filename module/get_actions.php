<?php
    /***************************
    *  
    *  actions in url 
    *  last modified 11.02.2020
	*
    * 	added urls: crm/ cms/ 
    ****************************/
      
    $actions = array(
        'product' => array(
            'file' => 'get_product.php', // file to include
            'func' => 'Product::get_page', // function name to load
        ),
        'category' => array(
            'file' => 'get_categ.php', // file to include
            'func' => 'Categ::get_page', // function name to load
        ),
        'pub' => array(
            'file' => 'get_pub.php', // file to include
            'func' => 'Pub::get_page', // function name to load
        ),
        'price' => array(
            'file' => 'get_price.php', // file to include
            'func' => 'Price::get_page', // function name to load
        ),
        'catalog' => array(
            'file' => 'get_catalog.php', // file to include
            'func' => 'Catalog::get_page', // function name to load
        ),

        'contact' => array(
            'file' => 'get_contact.php', // file to include
            'func' => 'Contact::get_page', // function name to load
        ),
        'getfile' => array(
            'file' => 'get_file.php', // file to include in folder MODULE
            'func' => 'Getfile::get_file', // function name to load
        ),
        'feedback' => array(
            'file' => 'get_feedback.php', // file to include
            'func' => 'Feedback::get_feedback', // function name to load
        ),
        'search' => array(
            'file' => 'get_search.php', // file to include
            'func' => 'Search::get_results', // function name to load
        ),
        'sitemap' => array(
            'file' => 'get_sitemap.php', // file to include
            'func' => 'Sitemap::get_results', // function name to load
        ),
        'sitemap.xml' => array(
            'file' => 'get_sitemap.php', // file to include
            'func' => 'Sitemap::get_xml', // function name to load
        ),
        'basket' => array(
            'file' => 'get_basket.php', // file to include
            'func' => 'Basket::get_basket', // function name to load
        ),
        'order' => array(
            'file' => 'get_order.php', // file to include
            'func' => 'Order::get_order', // function name to load
        ),
		
		'order/doc' => array(
            'file' => 'get_order.php', // file to include
            'func' => 'Order::get_order_doc', // function name to load
        ),
		
        'order/pay' => array(
            'file' => 'get_order.php', // file to include
            'func' => 'Order::get_order_payment', // function name to load
        ),
        'order/pay/success' => array(
            'file' => 'get_order.php', // file to include
            'func' => 'Order::get_order_success', // function name to load
        ),
        'order/pay/fail' => array(
            'file' => 'get_order.php', // file to include
            'func' => 'Order::get_order_fail', // function name to load
        ),
        'order/pay/cancel' => array(
            'file' => 'get_order.php', // file to include
            'func' => 'Order::get_order_cancel', // function name to load
        ),

        'order/pay/sberbank' => array(
            'file' => 'get_order.php', // file to include
            'func' => 'Order::get_order_sberbank', // function name to load
        ),
		
        'order/pay/invoice' => array(
            'file' => 'get_order.php', // file to include
            'func' => 'Order::get_order_invoice', // function name to load
        ),
		
        'user' => array(
            'file' => 'get_user.php', // file to include
            'func' => 'User::get_userinfo', // function name to load
        ),
        'profile' => array(
            'file' => 'get_user.php', // file to include
            'func' => 'User::get_userinfo', // function name to load
        ),
        'register' => array(
            'file' => 'get_user.php', // file to include
            'func' => 'User::get_register', // function name to load
        ),
        'login' => array(
            'file' => 'get_user.php', // file to include
            'func' => 'User::login', // function name to load
        ),
        'login/edit' => array(
            'file' => 'get_user.php', // file to include
            'func' => 'User::edit', // function name to load
        ),
        'login/orders' => array(
            'file' => 'get_user.php', // file to include
            'func' => 'User::orders', // function name to load
        ),
        'login/fb' => array(
            'file' => 'get_user.php', // file to include
            'func' => 'User::fb', // function name to load
        ),

        'login/add_item' => array(
            'file' => 'get_user.php', // file to include
            'func' => 'User::add_item', // function name to load
        ),
        'login/add_pub' => array(
            'file' => 'get_user.php', // file to include
            'func' => 'User::add_pub', // function name to load
        ),

        'login/changepassword' => array(
            'file' => 'get_user.php', // file to include
            'func' => 'User::changepassword', // function name to load
        ),

        'logout' => array(
            'file' => 'get_user.php', // file to include
            'func' => 'User::logout', // function name to load
        ),
        'forget_password' => array(
            'file' => 'get_user.php', // file to include
            'func' => 'User::forget_password', // function name to load
        ),
        'subscription' => array(
            'file' => 'get_subscribe.php', // file to include
            'func' => 'Subscribe::get_subscription', // function name to load
        ),

        'subscription/added' => array(
            'file' => 'get_subscribe.php', // file to include
            'func' => 'Subscribe::added', // function name to load
        ),
        'subscription/deleted' => array(
            'file' => 'get_subscribe.php', // file to include
            'func' => 'Subscribe::deleted', // function name to load
        ),
        'subscription/toadd' => array(
            'file' => 'get_subscribe.php', // file to include
            'func' => 'Subscribe::toadd', // function name to load
        ),
        'subscription/todelete' => array(
            'file' => 'get_subscribe.php', // file to include
            'func' => 'Subscribe::todelete', // function name to load
        ),

        'compare' => array(
            'file' => 'get_compare.php', // file to include
            'func' => 'Compare::get_compare', // function name to load
        ),
        'cron' => array(
            'file' => 'get_cron.php', // file to include
            'func' => 'Cron::get_cron', // function name to load
        ),
        'get_new' => array(
            'file' => 'get_last.php', // file to include
            'func' => 'Getlast::get_new_products', // function name to load
        ),
        'get_spec' => array(
            'file' => 'get_last.php', // file to include
            'func' => 'Getlast::get_spec_products', // function name to load
        ),
        'get_spec_pubs' => array(
            'file' => 'get_last.php', // file to include
            'func' => 'Getlast::get_spec_pubs', // function name to load
        ),
        'get_last_pubs' => array(
            'file' => 'get_last.php', // file to include
            'func' => 'Getlast::get_pubs', // function name to load
        ),
        'get_last_products' => array(
            'file' => 'get_last.php', // file to include
            'func' => 'Getlast::get_products', // function name to load
        ),
        'get_last_price0' => array(
            'file' => 'get_last.php', // file to include
            'func' => 'Getlast::get_price0', // function name to load
        ),
        'get_last_comments' => array(
            'file' => 'get_last.php', // file to include
            'func' => 'Getlast::get_comments', // function name to load
        ),
        'get_option' => array(
            'file' => 'get_option.php', // file to include
            'func' => 'Getlast::get_option', // function name to load
        ),
		
		'robots.txt' => array(
            'file' => 'get_robots.txt.php', // file to include
            'func' => 'Get::get_robots_txt', // function name to load
        ),
		
		'wishlist' => array(
            'file' => 'get_wishlist.php', // file to include
            'func' => 'GetWishlist::get_list', // function name to load
		),
		
		'tpltest' => array(
            'file' => 'get_tpltest.php', // file to include
            'func' => 'Tpltest::get_test', // function name to load
		),
		'feed' => array(
            'file' => 'get_rss.php', // file to include
            'func' => 'Rss::get_rss', // function name to load
        ),
		'feed-turbo' => array(
            'file' => 'get_rss_turbo.php', // file to include
            'func' => 'RssTurbo::get_rss', // function name to load
        ),
		'feed/comments' => array(
            'file' => 'get_rss.php', // file to include
            'func' => 'Rss::get_comments', // function name to load
        ),
		'tag' => array(
            'file' => 'get_tag.php', // file to include
            'func' => 'Tag::get_tag', // function name to load
        ),
		
		
        'cms' => array(
            'file' => 'cms/get_cms.php', // file to include
            'func' => 'Cms::get_cms', // function name to load
        ),
        'cms/orders' => array(
            'file' => 'cms/get_orders.php', // file to include
            'func' => 'Cms::get_orders', // function name to load
        ),
        'cms/fb' => array(
            'file' => 'cms/get_fb.php', // file to include
            'func' => 'Cms::get_fb', // function name to load
        ),
        'cms/coupons' => array(
            'file' => 'cms/get_coupons.php', // file to include
            'func' => 'Cms::get_coupons', // function name to load
        ),
        'cms/products' => array(
            'file' => 'cms/get_products.php', // file to include
            'func' => 'Cms::get_products', // function name to load
        ),
        'cms/categs' => array(
            'file' => 'cms/get_categs.php', // file to include
            'func' => 'Cms::get_categs', // function name to load
        ),
        'cms/comments' => array(
            'file' => 'cms/get_comments.php', // file to include
            'func' => 'Cms::get_comments', // function name to load
        ),
        'cms/pubs' => array(
            'file' => 'cms/get_pubs.php', // file to include
            'func' => 'Cms::get_pubs', // function name to load
        ),
        'cms/orgs' => array(
            'file' => 'cms/get_orgs.php', // file to include
            'func' => 'Cms::get_orgs', // function name to load
        ),
        'cms/users' => array(
            'file' => 'cms/get_users.php', // file to include
            'func' => 'Cms::get_users', // function name to load
        ),


        'crm' => array(
            'file' => 'crm/get_crm.php', // file to include
            'func' => 'Crm::get_crm', // function name to load
        ),

        'crm/projects' => array(
            'file' => 'crm/get_projects.php', // file to include
            'func' => 'Crm::get_projects', // function name to load
        ),
        'crm/contacts' => array(
            'file' => 'crm/get_contacts.php', // file to include
            'func' => 'Crm::get_contacts', // function name to load
        ),
        'crm/tasks' => array(
            'file' => 'crm/get_tasks.php', // file to include
            'func' => 'Crm::get_tasks', // function name to load
        ),
        'crm/calendar' => array(
            'file' => 'crm/get_calendar.php', // file to include
            'func' => 'Crm::get_calendar', // function name to load
        ),
        'crm/invoices' => array(
            'file' => 'crm/get_invoices.php', // file to include
            'func' => 'Crm::get_invoices', // function name to load
        ),
        'crm/users' => array(
            'file' => 'crm/get_users.php', // file to include
            'func' => 'Crm::get_users', // function name to load
        ),
        'crm/fin' => array(
            'file' => 'crm/get_fin.php', // file to include
            'func' => 'Crm::get_fin', // function name to load
        ),
        'crm/time' => array(
            'file' => 'crm/get_time.php', // file to include
            'func' => 'Crm::get_time', // function name to load
        ),

		
    );


    // function gets array with all empty page keys
    function GetEmptyPageArray()
    {
        return array(
          'page' => '', 
          'keywords' => '',  
          'description' => '', 
          'metatitle' => '', 
          'title' => '', 
          'intro' => '', 
          'content' => '', 
          'options' => array(),  
          'list_pubs' => array(),   
          'list_products' => array(), 
          'photos' => array(), 
          'spec_pubs' => array(),   
          'spec_products' => array(), 
          'new_products' => array(), 
          'last_edit' => '', 
          'added' => '', 
          'id' => 0, 
          'type' => '', 
          'alias' => '', 
          'id_parent' => 0, 
          'user' => array(), // [id],[login],[name]
          'f_new' => 0, 
          'f_spec' => 0, 
          'shop' => 0, 
          'icon' => '', 
          'show_filter' => 0, 
          'views' => 0, 
          'title_short' => '', 
          'price' => 0, 
          'price_spec' => 0, 
          'price_buy' => 0, 
          'price_opt' => 0, 
          'price_period' => '', 
          'comment' => '', 
          'currency' => '', 
          'treba' => 0, 
          'present_id' => 0, 
          'alter_search' => '', 
          'bid_ya' => 0, 
          'id_next_model' => 0, 
          'barcode' => '', 
          'complectation' => '', 
          'weight_deliver' => 0, 
          'accept_orders' => 0       
        );
    }
  


?>