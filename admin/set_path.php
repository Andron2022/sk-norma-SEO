<?php

  $p_action = isset($_GET['action']) ? $_GET['action'] : 'index';
  $p_do = isset($_GET['do']) ? $_GET['do'] : false;
  $p_id = isset($_GET['id']) ? $_GET['id'] : false;
  $q_str = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';

if($p_action == 'info' && $q_str == 'action=info'){
  $ar[''] = GetMessage('admin', 'sidebar', 'main_menu');
}elseif($p_action == 'products' && $q_str == 'action=products'){
  $ar[''] = GetMessage('admin','offers');
}elseif($p_action == 'orders' && $q_str == 'action=orders'){
  $ar[''] = GetMessage('admin', 'sidebar', 'orders');
}elseif($p_action == 'partner' && $q_str == 'action=partner'){
  $ar[''] = 'Affiliate programm';
}elseif($p_action == 'settings' && $q_str == 'action=settings'){
  $ar[''] = GetMessage('admin','sidebar','settings');
}elseif($p_action == 'info'){

  $ar['?action=info'] = GetMessage('admin', 'sidebar', 'main_menu');
  if($p_do == "categories" || $p_do == "tree_categs" || $p_do == "add_category" || $p_do == "edit_categ"){
    if($q_str == 'action=info&do=categories'){ 
		$ar[] = GetMessage('admin', 'block_types', 'listCategs'); 
	}else{ 
		$ar['?action=info&do=categories'] = GetMessage('admin', 'block_types', 'listCategs'); 
	}
	
    if($p_do == "tree_categs"){ 
		$ar[] = GetMessage('admin', 'index', 'structure'); 
	}
	
    if($p_do == "add_category"){ 
		$ar[] = GetMessage('admin','adding'); 
	}
	
    if($p_do == "edit_categ"){ 
		$ar[] = GetMessage('admin','editing'); 
	}
	
	
  }elseif($p_do == "list_publications" || $p_do == "add_publication" || $p_do == "move_publications" || $p_do = "edit_publication"){
    // ?action=info&do=edit_publication&id=11
    if($q_str == 'action=info&do=list_publications'){ 
		$ar[] = GetMessage('admin', 'block_types', 'listPubs'); 
	}else{ 
		$ar['?action=info&do=list_publications'] = GetMessage('admin', 'block_types', 'listPubs'); 
	}
	
    if($p_do == "add_publication"){ 
		$ar[] = GetMessage('admin','adding'); 
	}
	
    if($p_do == "move_publications"){ 
		$ar[] = 'Change page'; 
	}
	
    if($p_do == "edit_publication"){ 
		$ar[] = GetMessage('admin','editing'); 
	}
  }else{}
    
}elseif($p_action == 'mass'){
  $ar['?action=mass'] = GetMessage('admin','prava','mass');
  if(!empty($p_do)){
    $ar[''] = GetMessage('admin','adding');                 
  }

}elseif($p_action == 'banner'){
  $ar['?action=info'] = GetMessage('admin', 'sidebar', 'main_menu');
  if($p_do == "send_message"){
    $ar[''] = 'Sending message';               
  }elseif($p_do == "subscribers"){
    $ar[''] = GetMessage('admin', 'user', 'subscribers');
  }elseif($p_do == "message_stat"){
    $ar[''] = GetMessage('admin', 'prava', 'stat');
  }else{}
}elseif($p_action == "feedback"){
  $ar['?action=info'] = GetMessage('admin', 'sidebar', 'main_menu');
	if($q_str == 'action=feedback'){ 
		$ar[] = GetMessage('admin', 'tpl', 'feedback'); 
	}else{ 
		$ar['?action=feedback'] = GetMessage('admin', 'tpl', 'feedback'); 
	}
	
	if($p_id !== false){
		if($p_id == 0){ 
			$ar[] = 'Send message'; 
		}
		
		if($p_id > 0){ 
			$ar[] = GetMessage('admin', 'fb', 'message'); 
		}
	}
}elseif($p_action == "delivery"){
  $ar['?action=delivery'] = GetMessage('admin', 'prava', 'delivery');
  
  if($p_id !== false){
    if($p_id == 0){ $ar[] = GetMessage('admin','add'); }
    if($p_id > 0){ $ar[] = GetMessage('admin','edit'); }
  }
}elseif($p_action == "users"){

	$ar['?action=info'] = GetMessage('admin', 'sidebar', 'main_menu');

	if($q_str == 'action=users'){ 
		$ar[] = GetMessage('admin', 'sidebar', 'users'); 
	}else{ 
		$ar['?action=users'] = GetMessage('admin', 'sidebar', 'users'); 
	}
	
	if($p_id !== false){
		if($p_id == 0){ 
			$ar[] = GetMessage('admin','adding'); 
		}
		
		if($p_id > 0){ 
			$ar[] = GetMessage('admin', 'user', 'editing'); 
		}
	}
}elseif($p_action == "comments"){
	$ar['?action=info'] = GetMessage('admin', 'sidebar', 'main_menu');
	
	if($q_str == 'action=comments'){ 
		$ar[] = GetMessage('admin','comments'); 
	}else{ 
		$ar['?action=comments'] = GetMessage('admin','comments'); 
	}
	
	if($p_id !== false){
		if($p_id == 0){ 
			$ar[] = GetMessage('admin', 'products', 'add_comment'); 
		}
		
		if($p_id > 0){ 
			$ar[] = GetMessage('admin','editing'); 
		}
	}
}elseif($p_action == 'products'){
  $ar['?action=products'] = GetMessage('admin','offers');
  if($p_do == "list_products"){
    $ar[] = GetMessage('admin', 'block_types', 'listCategs');
  }elseif($p_do == "add"){
    $ar['?action=products&do=list_products'] = GetMessage('admin', 'block_types', 'listProducts');
    $ar[] = GetMessage('admin','adding');
  }elseif($p_do == "move_products"){
    $ar[] = 'Change page';
  }elseif($p_do == "edit"){
    $ar['?action=products&do=list_products'] = GetMessage('admin', 'block_types', 'listProducts');
    $ar[] = GetMessage('admin','editing');
  }elseif($p_do == "all_categs"){
    $ar[] = GetMessage('admin','categs');
  }elseif($p_do == "categ_tree"){
    $ar[] = GetMessage('admin', 'index', 'structure');
  }elseif($p_do == "add_categ"){
    $ar['?action=products&do=all_categs'] = GetMessage('admin','categs');
    $ar[] = GetMessage('admin','adding');
  }elseif($p_do == "show_categ"){
    $ar['?action=products&do=all_categs'] = GetMessage('admin','categs');
    $ar[] = GetMessage('admin','editing');
  }elseif($p_do == "option_group"){
    if($p_id > 0){
      $ar['?action=products&do=option_group'] = GetMessage('admin', 'sidebar', 'opt_groups');
      $ar[] = GetMessage('admin','editing');
    }else{
      $ar[] = GetMessage('admin', 'sidebar', 'opt_groups');
    }
  }elseif($p_do == "add_option_group"){
      $ar['?action=products&do=option_group'] = GetMessage('admin', 'sidebar', 'opt_groups');
      $ar[] = GetMessage('admin','adding');
  }elseif($p_do == "options"){
    if($p_id > 0){
      $ar['?action=products&do=options'] = GetMessage('admin', 'sidebar', 'options');
      $ar[] = GetMessage('admin','editing');
    }else{
      $ar[] = GetMessage('admin', 'sidebar', 'options');
    }
  }elseif($p_do == "add_option"){
      $ar['?action=products&do=options'] = GetMessage('admin', 'sidebar', 'options');
      $ar[] = GetMessage('admin','adding');


  }else{}

}elseif($p_action == 'orders'){
  $ar['?action=orders'] = GetMessage('admin', 'sidebar', 'orders');
  if($p_do == "new_orders"){
    $ar[''] = GetMessage('admin', 'orders', 'new_orders');
  }elseif($p_do == "in_process"){
    $ar[''] = 'In process';
  }elseif($p_do == "list_all"){
    $ar[''] = GetMessage('admin', 'products', 'orders');
  }elseif($p_do == "edit_order"){
    $ar[''] = GetMessage('admin', 'orders', 'order_info');
  }else{}
}elseif($p_action == 'orders_stat'){
  $ar['?action=orders'] = GetMessage('admin', 'sidebar', 'orders');
  if($p_do == "all"){
    $ar[''] = GetMessage('admin', 'prava', 'stat');
  }elseif($p_do == "today"){
    $ar[''] = GetMessage('admin', 'today');
  }elseif($p_do == "yesterday"){
    $ar[''] = GetMessage('admin', 'yesterday');
  }elseif($p_do == "current_month"){
    $ar[''] = 'Current month';
  }elseif($p_do == "last_month"){
    $ar[''] = 'Last month';
  }else{}

}elseif($p_action == 'partner'){
  $ar['?action=partner'] = 'Affiliate programm';
  if($p_do == "edit_tag"){
    $ar[''] = GetMessage('admin','editing');
  }elseif($p_do == "stats"){
    $ar[''] = GetMessage('admin', 'prava', 'stat');
  }elseif($p_do == "summary"){
    $ar[''] = GetMessage('admin', 'prava', 'stat');
  }elseif($p_do == "partner"){
    $ar[''] = 'Partner marks';
  }elseif($p_do == "add_tag"){
    $ar[''] = GetMessage('admin','adding');
  }elseif($p_do == "orders"){
    $ar[''] = GetMessage('admin', 'orders', 'partner_orders');
  }else{}
}elseif($p_action == 'settings'){
  $ar['?action=settings'] = GetMessage('admin','sidebar','settings');
  if($p_do == "site"){
    if($p_id > 0){
      $ar['?action=settings&do=site'] = GetMessage('admin','websites');
      $ar[''] = GetMessage('admin','editing');
    }else{
      $ar[''] = GetMessage('admin','websites');
    }
  }elseif($p_do == "add_site"){
      $ar['?action=settings&do=site'] = GetMessage('admin','websites');
      $ar[''] = GetMessage('admin','adding');
  }elseif($p_do == "payments"){
      $ar['?action=settings&do=payments'] = GetMessage('admin','set','payments');
  }elseif($p_do == "statuses"){
      $ar['?action=settings&do=statuses'] = GetMessage('admin', 'set', 'statuses');

  }elseif($p_do == "tpl"){
      $ar[''] = GetMessage('admin','tpl','templates');
  }elseif($p_do == "site_vars" || $p_do == "mass_vars"){
    if($p_id === false){
	  if(isset($_GET['hint']) || isset($_GET['mode'])){
		$ar['?action=settings&do=site_vars'] = GetMessage('admin','elements','title');		  
	  }else{
		$ar[''] = GetMessage('admin','elements','title');		  
	  }
    }else{
      $ar['?action=settings&do=site_vars'] = GetMessage('admin', 'elements', 'site_vars');
      if($p_id > 0){
        $ar[''] = GetMessage('admin','editing');
      }else{
        $ar[''] = GetMessage('admin','adding');
      }
    }
  }elseif($p_do == "users"){
    $ar[''] = GetMessage('admin', 'user', 'admins');
  }elseif($p_do == "add_user"){
    $ar['?action=settings&do=users'] = GetMessage('admin', 'user', 'admins');
    if($p_id > 0){
      $ar[''] = GetMessage('admin','editing');
    }else{
      $ar[''] = GetMessage('admin','adding');
    }
  }else{}

}elseif($p_action == 'search'){
  $ar[''] = GetMessage('admin','site_search');
}elseif($p_action == 'regions'){
  $ar['?action=settings'] = GetMessage('admin','sidebar','settings');
  if($p_id === false){
    $ar[''] = 'Regions';
  }else{
    $ar['?action=regions'] = 'Regions';
    if($p_id > 0){
      $ar[''] = GetMessage('admin','editing');
    }else{
      $ar[''] = GetMessage('admin','adding');
    }
  }
}elseif($p_action == 'payments'){
  $ar['?action=settings'] = GetMessage('admin','sidebar','settings');
  if($p_id === false){
    $ar[''] = GetMessage('admin','set','payments');
  }else{
    $ar['?action=payments'] = GetMessage('admin','set','payments');
    if($p_id > 0){
      $ar[''] = GetMessage('admin','editing');
    }else{
      $ar[''] = GetMessage('admin','adding');
    }
  }
}elseif($p_action == 'deliv_price'){
  $ar['?action=settings'] = GetMessage('admin','sidebar','settings');
  if($p_id === false){
    $ar[''] = GetMessage('admin', 'orders', 'delivery_price');
  }else{
    $ar['?action=deliv_price'] = GetMessage('admin', 'orders', 'delivery_price');
    if($p_id > 0){
      $ar[''] = GetMessage('admin','editing');
    }else{
      $ar[''] = GetMessage('admin','adding');
    }
  }
}elseif($p_action == 'db'){
  $ar['?action=settings'] = GetMessage('admin','sidebar','settings');
  $ar[''] = GetMessage('admin', 'sidebar', 'database');
}elseif($p_action == 'query'){
  $ar['?action=settings'] = GetMessage('admin','sidebar','settings');
  $ar[''] = GetMessage('admin','sidebar','requests');
}else{
}

?>