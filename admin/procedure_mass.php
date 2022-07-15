<?php
/*
  ok
  last updated 10.07.2015
*/
if(!defined('SIMPLA_ADMIN')){ die(); }
  global $admin_vars;
  $id = $admin_vars['uri']['id'];
  $do = $admin_vars['uri']['do'];

  if($do == "products"){
    $str_content = mass_add_products();
  }elseif($do == "pubs"){
    $str_content = mass_add_pubs();
  }elseif($do == "categs"){
    $str_content = mass_add_categs();
  }else{
    $str_content = mass_choose();
  }


  /* ok */
  function mass_choose(){
    global $tpl;
    return $tpl->display("mass/index.html");
  }

  /* ok */
  function mass_add_products(){
    global $tpl, $db, $site_vars, $admin_vars;
    $menutable = $db->tables['categs'];
    $menuid = 0;
    $ar = getmenu($menutable, $menuid, $shop=1);
    $tpl->assign("top_pages", $ar);
    
    $multitpl = $db->get_var("SELECT `value`
          FROM ".$db->tables['site_vars']."
          WHERE `name` = 'sys_tpl_products' ");
    if(!$multitpl || $db->num_rows == 0){ $multitpl = "product.html "; }
    
    if(!empty($site_vars['sys_tpl_products'])){
      $tpls = get_array_vars('sys_tpl_products');
    }else{
      $tpls = array($multitpl);
    }
    $tpl->assign("tpls", $tpls);
    $tpl->assign("multitpl", $multitpl);
	
	
	// if get &cid - receive properties for products list mode
	$cid = !empty($_GET['cid']) ? intval($_GET['cid']) : 0;
	$rows = array();
	if($cid > 0){
				
		$sql = "SELECT 
					g.title as g_title, 
					g.description as g_description, 
					g.opt_title as g_o_title,
					g.value1 as g_value1, 
					g.value2 as g_value2, 
					g.value3 as g_value3, 
                    o.*
					
                FROM ".$db->tables['categ_options']." co, 
					".$db->tables['options']." o 
				LEFT JOIN ".$db->tables['option_groups']." g ON (o.group_id = g.id)
				
				
                WHERE 
					co.id_categ = '".$cid."' 
					AND co.id_option = o.group_id  
					AND o.show_in_list = '1' 
					AND (g.`where_placed` = 'product' OR g.`where_placed` = 'all')
				";			
		$sql .= " ORDER BY o.sort, o.title ";
		$rows = $db->get_results($sql, ARRAY_A);
	}
    $tpl->assign("categ_options", $rows);
	
	

    if(!empty($_POST["products"]) && !empty($_POST["add"])){

        /* to save new products */
        $active = isset($_POST["active"]) ? intval($_POST["active"]) : 0;
        $top_page_ex = isset($_POST["top_page"]) ? $_POST["top_page"] : '';
		$top_page = 0;
		if(!empty($top_page_ex)){
			$t = explode('cid=',$top_page_ex);
			if(isset($t[1])){ $top_page = intval($t[1]); }
		}
		
        $multitpl = isset($_POST["multitpl"]) ? trim($_POST["multitpl"]) : "product.html";
        $do_alias = empty($_POST["alias"]) ? "time" : trim($_POST["alias"]);
        $ar = explode("<br />", nl2br($_POST["products"]) );
        $prices = explode("<br />", nl2br($_POST["prices"]) );
				
		$options = isset($_POST["options"]) ? $_POST["options"] : array();
		$opts = array();
		if(!empty($options)){
			foreach($options as $k=>$v){
				if(!empty($v['value1'])){
					$opts[$k]['value1'] = explode("<br />", nl2br($v["value1"]) );
				}
				
				if(!empty($v['value2'])){
					$opts[$k]['value2'] = explode("<br />", nl2br($v["value2"]) );
				}

				if(!empty($v['value3'])){
					$opts[$k]['value3'] = explode("<br />", nl2br($v["value3"]) );
				}
				
			}
		}

        $barcodes = isset($_POST["barcode"]) 
            ? explode("<br />", nl2br($_POST["barcode"]) ) 
            : array();

        $pre_alias = isset($site_vars['sys_prefix_product']) 
            ? $site_vars['sys_prefix_product'] 
            : "item-"; 
        $i=0;
        $date_insert = date("Y-m-d H:i:s");
        $currency = isset($_POST['currency']) 
            ? trim($_POST['currency']) 
            : $admin_vars['default_currency'];
        if($currency != 'rur' && $currency != 'euro' && $currency != 'usd'){
          $currency = $admin_vars['default_currency'];
        }
            

        foreach($ar as $k => $v){
			$price = isset($prices[$k]) ? floatval($prices[$k]) : 0;
			$price = round($price, 2);
			$barcode = isset($barcodes[$k]) ? trim($barcodes[$k]) : "";

			if(empty($v)){ break 1; }
			$v = trim($v);
			if(!empty($v)){

				if($do_alias == "time"){
					$alias =  $pre_alias.time().$i;
				}elseif($do_alias == "code"){		
					$alias = build_slug($barcode,'product',0);
				}else{
					$alias = build_slug($v,'product',0);
				}

				$sql = "INSERT INTO ".$db->tables['products']." (`name`, meta_title, 
				  meta_description, meta_keywords, price, price_period, active, 
				  accept_orders, barcode, currency, date_insert, date_update, 
				  user_id, alias, views, multitpl) 
				  VALUES ('".$db->escape($v)."', '".$db->escape($v)."', 
				  '".$db->escape($v)."', '".$db->escape($v)."', '".$price."', 
				  'razovo', '".$active."', '1', '".$db->escape($barcode)."', '".$currency."', 
				  '".$date_insert."', '0000-00-00 00:00:00', 
				  '".$admin_vars['bo_user']['id']."', '".$db->escape($alias)."', 
				  '0', '".$db->escape($multitpl)."') ";
				$db->query($sql);
				$id = $db->insert_id;
				$i++;

				// Обновим barcode - код товара
				if(empty($barcode)){
					$sql = "UPDATE ".$db->tables['products']." 
					  SET `barcode` = '".$id."' WHERE id = '".$id."' ";
					$db->query($sql);            
				}          
									
				// привяжем к верхней странице
				if($top_page > 0){
					$sql = "INSERT INTO ".$db->tables['pub_categs']." 
						(id_pub, id_categ, `where_placed`) 
						VALUES('".$id."', '".intval($top_page)."', 'product') ";
					$db->query($sql);            
				}    

				/* добавим опции, если есть */
				if(!empty($opts)){
					foreach($opts as $o1 => $o2){
						$opt_row1 = isset($o2['value1'][$k]) ? trim($o2['value1'][$k]) : "";
						$opt_row2 = isset($o2['value2'][$k]) ? trim($o2['value2'][$k]) : "";
						$opt_row3 = isset($o2['value3'][$k]) ? trim($o2['value3'][$k]) : "";
						
						if(isset($o2['value1'][$k])){
							$sql = "INSERT INTO ".$db->tables['option_values']." 
								(id_option, id_product, value, where_placed, value2, value3) 
								VALUES ('".$o1."', '".$id."', '".$db->escape($opt_row1)."', 
								'product', '".$db->escape($opt_row2)."', 
								'".$db->escape($opt_row3)."') ";
							$db->query($sql);
						}
					}
				}
				
				
			}			
        }

		$url = "?action=mass&do=products&added=".$i;
		if(!empty($top_page)){ $url .= "&cid=".$top_page; }
		clear_cache(0);
        header("Location: ".$url);
        exit;       
        
    }

    return $tpl->display("mass/add_products.html");
  }

  /* ok */
  function mass_add_pubs(){
    global $tpl, $db, $site_vars, $admin_vars;
    $menutable = $db->tables['categs'];
    $menuid = 0;
    $ar = getmenu($menutable, $menuid);
    $tpl->assign("top_pages", $ar);

    $sites = $db->get_results("SELECT id, site_url, site_active, name_short as title FROM ".$db->tables['site_info']." order by `name_short` ", ARRAY_A);
    $tpl->assign("sites", $sites);
    
    $multitpl = $db->get_var("SELECT `value`
          FROM ".$db->tables['site_vars']."
          WHERE `name` = 'sys_tpl_pubs' ");
    if(!$multitpl || $db->num_rows == 0){ $multitpl = "pub.html "; }
    
    if(!empty($site_vars['sys_tpl_pubs'])){
      $tpls = get_array_vars('sys_tpl_pubs');
    }else{
      $tpls = array($multitpl);
    }
    $tpl->assign("tpls", $tpls);
    $tpl->assign("multitpl", $multitpl);


    if(!empty($_POST["pubs"]) && !empty($_POST["add"])){

        /* to save new pubs */
        $active = isset($_POST["active"]) ? intval($_POST["active"]) : 0;
        $top_page = isset($_POST["top_page"]) ? intval($_POST["top_page"]) : 0;
        $multitpl = isset($_POST["multitpl"]) ? trim($_POST["multitpl"]) : "pub.html";
        $do_alias = empty($_POST["alias"]) ? "time" : trim($_POST["alias"]);
        $ar = explode("<br />", nl2br($_POST["pubs"]) );
        $pre_alias = isset($site_vars['sys_prefix_pub']) 
            ? $site_vars['sys_prefix_pub'] 
            : "pub-"; 
        $i=0;
        $date_insert = date("Y-m-d H:i:s");
		
		$barcodes = isset($_POST["barcode"]) 
            ? explode("<br />", nl2br($_POST["barcode"]) ) 
            : array();

        foreach($ar as $k => $v){
			$barcode = isset($barcodes[$k]) ? trim($barcodes[$k]) : "";
			$v = trim($v);
			
			if($do_alias == "time"){
				$alias =  $pre_alias.time().$i;
			}elseif($do_alias == "code"){		
				$alias = build_slug($barcode,'pub',0);
			}else{
				$alias = build_slug($v,'pub',0);
			}
			
			if(!empty($v)){

				$sql = "INSERT INTO ".$db->tables['publications']." (`name`, `ddate`, 
					  active, meta_title, meta_description, meta_keywords, alias, 
					  date_insert, views, user_id, multitpl) 
					  VALUES ('".$db->escape($v)."', '0000-00-00 00:00:00', 
					  '".$active."', '".$db->escape($v)."', '".$db->escape($v)."', 
					  '".$db->escape($v)."', '".$db->escape($alias)."', 
					  '".$date_insert."', '0', '".$admin_vars['bo_user']['id']."', 
					  '".$db->escape($multitpl)."') ";
				$db->query($sql);
				$id = $db->insert_id;
				$i++;
				
				// привяжем к сайтам
				if(!empty($_POST["sites"])){  
					foreach($_POST["sites"] as $site_id){
					  $sql = "INSERT INTO ".$db->tables['site_publications']." 
						(id_site, id_publications) VALUES('".intval($site_id)."', '".$id."') ";
					  $db->query($sql);
					}
				}
					

				// привяжем к верхней странице
				if($top_page > 0){
					  $sql = "INSERT INTO ".$db->tables['pub_categs']." 
						(id_pub, id_categ, `where_placed`) 
						VALUES('".$id."', '".intval($top_page)."', 'pub') ";
					  $db->query($sql);            
				}          
			}
		  
        }

		$url = "?action=mass&do=pubs&added=".$i;
		if(!empty($top_page)){ $url .= '&cid='.$top_page; }
		clear_cache(0);
        header("Location: ".$url);
        exit;       
        
    }

    
    
    return $tpl->display("mass/add_pubs.html");
  }

  /* ok */
  function mass_add_categs(){
    global $tpl, $db, $site_vars, $admin_vars;
    $menutable = $db->tables['categs'];
    $menuid = 0;
    $ar = getmenu($menutable, $menuid);
    $tpl->assign("top_pages", $ar);
    
    $option_groups = $db->get_results("SELECT o.*  
      FROM ".$db->tables['option_groups']." o        
      ORDER BY o.`sort`, o.`title` ", ARRAY_A);
    $tpl->assign("option_groups", $option_groups);

    $sites = $db->get_results("SELECT id, site_url, site_active, name_short as title FROM ".$db->tables['site_info']." order by `name_short` ", ARRAY_A);
    $tpl->assign("sites", $sites);

    $multitpl = $db->get_var("SELECT `value`
          FROM ".$db->tables['site_vars']."
          WHERE `name` = 'sys_tpl_pages' ");
    if(!$multitpl || $db->num_rows == 0){ $multitpl = "category.html "; }
    
    if(!empty($site_vars['sys_tpl_pages'])){
      $tpls = get_array_vars('sys_tpl_pages');
    }else{
      $tpls = array($multitpl);
    }
    $tpl->assign("tpls", $tpls);
    $tpl->assign("multitpl", $multitpl);
	
    if(!empty($_POST["categs"]) && !empty($_POST["add"])){
        /* to save new categs */
        $shop = isset($_POST["shop"]) ? intval($_POST["shop"]) : 0;
        $active = isset($_POST["active"]) ? intval($_POST["active"]) : 0;
        $top_page = isset($_POST["top_page"]) ? intval($_POST["top_page"]) : 0;
        $multitpl = isset($_POST["multitpl"]) ? trim($_POST["multitpl"]) : "category.html";
        $do_alias = empty($_POST["alias"]) ? "time" : trim($_POST["alias"]);
        $ar = explode("<br />", nl2br($_POST["categs"]) );
        $pre_alias = isset($site_vars['sys_prefix_categ']) 
            ? $site_vars['sys_prefix_categ'] 
            : "page-"; 
        $i=0;
        $date_insert = date("Y-m-d H:i:s");
		
		$barcodes = isset($_POST["barcode"]) 
            ? explode("<br />", nl2br($_POST["barcode"]) ) 
            : array();
		

        foreach($ar as $k => $v){
			$barcode = isset($barcodes[$k]) ? trim($barcodes[$k]) : "";
			$v = trim($v);
			
			if($do_alias == "time"){
				$alias =  $pre_alias.time().$i;
			}elseif($do_alias == "code"){		
				$alias = build_slug($barcode,'categ',0);
			}else{
				$alias = build_slug($v,'categ',0);
			}
			
			if(!empty($v)){
			
				$sql = "INSERT INTO ".$db->tables['categs']." (title, id_parent, 
				  meta_description, meta_keywords, meta_title, alias, active, 
				  sort, date_insert, date_update, user_id, multitpl, shop) 
				  VALUES ('".$db->escape($v)."', '".$top_page."', 
				  '".$db->escape($v)."', '".$db->escape($v)."', 
				  '".$db->escape($v)."', '".$db->escape($alias)."', 
				  '".$active."', '99', '".$date_insert."', 
				  '0000-00-00 00:00:00', 
				  '".$admin_vars['bo_user']['id']."', 
				  '".$db->escape($multitpl)."', '".$shop."') ";
				$db->query($sql);
				$id = $db->insert_id;
				$i++;
          
				// привяжем к верхним страницам, если есть
				if(!empty($_POST["sites"])){
					foreach($_POST["sites"] as $site_id){
					  $sql = "INSERT INTO ".$db->tables['site_categ']." 
						(id_site, id_categ) VALUES('".intval($site_id)."', '".$id."') ";
					  $db->query($sql);
					}
				}
				  
				// привяжем к группам характеристик, если указаны
				if(!empty($_POST["option_groups"])){
					foreach($_POST["option_groups"] as $v){
					  $sql = "INSERT INTO ".$db->tables['categ_options']." 
						(id_option, id_categ, `where_placed`) 
						VALUES('".intval($v)."', '".$id."', 'categ') ";
					  $db->query($sql);            
					}
				}          
			}
        }

		$url = "?action=mass&do=categs&added=".$i;
		if(!empty($top_page)){ $url .= '&cid='.$top_page; }
		clear_cache(0);
        header("Location: ".$url);
        exit;       
        
    }
    return $tpl->display("mass/add_categs.html");
  }
  
?>