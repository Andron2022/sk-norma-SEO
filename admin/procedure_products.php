<?php

/**********************************
***********************************
**	
**	test version
**	last updated 17.04.2018
**	+ updated tree of categs connected to option group
**	+ updated product to categs connection without new window and new requests
**	+ added mass changing YML bid
**	  
***********************************
**********************************/

if(!defined('SIMPLA_ADMIN')){ die(); }

  global $db, $admin_vars, $tpl, $lang;
  $mode = $admin_vars["uri"]["mode"];
  $do = $admin_vars["uri"]["do"];
  $id = $admin_vars["uri"]["id"];
  $page = $admin_vars["uri"]["page"];

if($do == "add"){
  $str_content = edit_products($id);
}elseif($do == "list_products"){
  $str_content = list_products($page);
}elseif($do == "edit"){
  $str_content = edit_products($id);
}elseif($do == "option_group"){
  $str_content = option_group($id);
}elseif($do == "add_option_group"){
  $str_content = edit_option_group(0);
}elseif($do == "options"){
  $str_content = $id > 0 ? edit_option($id) : list_options($page);
}elseif($do == "add_option"){
  $str_content = edit_option(0);
}else{
  $qty_catalogs = $db->get_var("SELECT count(*) FROM ".$db->tables["categs"]."  WHERE shop = '1' ");
  $tpl->assign("qty_catalogs", $qty_catalogs);
  $qty_products = $db->get_var("SELECT count(*) FROM ".$db->tables["products"]."  ");
  $tpl->assign("qty_products", $qty_products);
  $ar = $db->get_results("SELECT * FROM ".$db->tables["products"]." ORDER BY id desc limit 0, 10 ", ARRAY_A);
  $tpl->assign("last_products", $ar);
  //$tpl->assign("allcatalogs", build_catalogs_tree(0));

  $menutable = $db->tables['categs'];
  $menuid = 0;
  $parentid = isset($_GET['id']) ? intval($_GET['id']) : 0;
  $categs_tree = getmenu($menutable, $menuid,1,$parentid);
  $tpl->assign("allcatalogs", $categs_tree);

  $str_content = $tpl->display("products/index.html");
}

  /* ok for new version for product page */
  function _productcatalogs($ar, $current=0){
    global $db, $admin_vars, $lang;
    $query = "SELECT c.id, c.title, c.alias, c.id_parent, c.active, c.shop
          FROM ".$db->tables['categs']." c
          WHERE c.id_parent =  '".$current."' 
          ORDER BY c.sort, c.title";   
          // AND c.shop = '1'      
    $rows = $db->get_results($query);
    $str = "";

    if($db->num_rows > 0){
      $str = "<ul>";
      foreach($rows as $row){
        $str .= '<li>';
        if($row->shop == 1){
              if(in_array($row->id, $ar)){
                $str .= "<input type='checkbox' name='categs[]' value='".$row->id."' checked='checked'> ";
              }else{
                $str .= "<input type='checkbox' name='categs[]' value='".$row->id."'> ";
              }
              
        }
        $str .= "<span id='categ".$row->id."'>";
        $str .= $row->active == 1 ? '' : '<s>';
        $str .= $row->shop == 1 ? $row->title : '<small>'.$row->title.'</small>'; 
        $str .= $row->active == 1 ? '' : '</s>';
        $str .= "</span>";
        $current = $row->id;
        $str .= _productcatalogs($ar, $current);
        $str .= "</li>";
      }                                  
      $str .= "</ul>";
    }

    if(!isset($str)){ $str = ""; }
    return $str;
  }

  
  /* new version builds catalogstree */
  function build_catalogs_tree($id)
  {
    global $db, $admin_vars;
    static $i = 0;
        
    $query = "SELECT c.id, c.title, c.alias, c.id_parent, c.active,
              (SELECT COUNT(*) FROM ".$db->tables['pub_categs']."  p 
                WHERE p.id_categ = c.id AND p.id_pub >  '0' AND p.`where_placed` = 'product') as products
          FROM ".$db->tables['categs']." c
          WHERE c.id_parent =  '".$id."' AND c.shop = '1' 
          ORDER BY c.sort, c.title";        
    $rows = $db->get_results($query);

    if($db->num_rows > 0){
      $str = "<ul>";
      foreach($rows as $row){
        $i = $i == 0 && $id > 0 ? 1 : 0;
        $str .= $i == 1 ? '<li style="list-style-type: none; background-color:'.$admin_vars['bglight'].';">' : '<li style="list-style-type: none; background-color:#ffffff;">';
        
        $str .= $row->active == 1 ? '<i class="fa fa-check"></i> ' : '<i class="fa fa-minus"></i> ';
        $str .= '<a href="?action=info&do=edit_categ&id='.$row->id.'">';
        $str .= $row->title;
        $str .= ' <i class="fa fa-pencil"></i></a>';
        $str .= ' <span style="padding-left:3px;margin-left:3px;"></span><small><a href="../'.$row->alias.'/"><i class="fa fa-external-link"></i></small></a>';
        $str .= $row->products > 0 ? ' <a href="?action=products&do=list_products&cid='.$row->id.'"><i class="fa fa-shopping-cart"></i> ('.$row->products.')</a>' : '';
        $str .= build_catalogs_tree($row->id);
        $str .= "</li>";
      }                                  
      $str .= "</ul>";
    }
    if(!isset($str)){ $str = ""; }
    return $str;
  }

/* ok */
function option_save2db($id, $what) // what: add, delete, update
{
  global $db;
  $title = isset($_POST["title"]) ? trim($_POST["title"]) : "";
  $alias = isset($_POST["alias"]) ? trim($_POST["alias"]) : "";
  $sort = isset($_POST["sort"]) ? intval($_POST["sort"]) : 0;
  $group = isset($_POST["group"]) ? intval($_POST["group"]) : 0;
  $type = isset($_POST["type"]) ? trim($_POST["type"]) : "val";
  $if_select = isset($_POST["if_select"]) ? trim($_POST["if_select"]) : "";
  $show_in_list = isset($_POST["show_in_list"]) ? trim($_POST["show_in_list"]) : 0;
  $show_in_filter = isset($_POST["show_in_filter"]) ? trim($_POST["show_in_filter"]) : 0;
  $filter_type = isset($_POST["filter_type"]) ? trim($_POST["filter_type"]) : "";
  $filter_description = isset($_POST["filter_description"]) ? trim($_POST["filter_description"]) : "";
  $after = isset($_POST["after"]) ? trim($_POST["after"]) : "";
  $icon = isset($_POST["icon"]) ? trim($_POST["icon"]) : "";

  if($what == "add"){
    $db->query("INSERT INTO ".$db->tables['options']." (`title`, `alias`, `sort`, 
        `group_id`, `type`, `if_select`,`filter_type`,`show_in_filter`,
        `show_in_list`,`filter_description`, `after`, `icon`) VALUES(
          '".$db->escape($title)."', 
          '".$db->escape($alias)."', 
          '".$sort."', 
          '".$group."', 
          '".$db->escape($type)."', 
          '".$db->escape($if_select)."',
          '".$db->escape($filter_type)."',
          '".$show_in_filter."',
          '".$show_in_list."',
          '".$db->escape($filter_description)."', 
          '".$db->escape($after)."',
          '".$db->escape($icon)."'
        )");
    if(!empty($db->last_error)){ return db_error(basename(__FILE__).": 999"); }
    $id = $db->insert_id;
	register_changes('option', $id, 'add', $title);
    
    if(empty($alias)){
      $db->query("UPDATE ".$db->tables['options']." 
          SET `alias` = '".$id."' WHERE id = '".$id."' ");
    }
    $href = "?action=products&do=options&id=".$id."&added=1";
    header("Location: ".$href);
    exit;
  }elseif($what == "update"){
    if(empty($alias)){ $alias = $id; }
    $query = "UPDATE ".$db->tables['options']." SET
            `title` = '".$db->escape($title)."',
            `alias` = '".$db->escape($alias)."',
            `sort` = '".$sort."',
            `group_id` = '".$group."',
            `type` = '".$db->escape($type)."',
            `if_select` = '".$db->escape($if_select)."',
            `filter_type` = '".$db->escape($filter_type)."',
            `show_in_filter` = '".$show_in_filter."',
            `show_in_list` = '".$show_in_list."',
            `filter_description` = '".$db->escape($filter_description)."',
            `after` = '".$db->escape($after)."',
            `icon` = '".$db->escape($icon)."'
            WHERE `id` = '".$id."' ";
    $db->query($query);
    if(!empty($db->last_error)){ return db_error(basename(__FILE__).": 1018"); }
	register_changes('option', $id, 'update');
    $href = "?action=products&do=options&id=".$id."&updated=1";
    header("Location: ".$href);
    exit;
  }elseif($what == "delete"){
    $db->query("DELETE FROM ".$db->tables['options']." WHERE `id` = '".$id."' ");
    $db->query("DELETE FROM ".$db->tables['option_values']." WHERE `id_option` = '".$id."' ");
	register_changes('option', $id, 'delete');
    $href = "?action=products&do=options&deleted=1";
    header("Location: ".$href);
    exit;
  }else{
    return error_not_found("1028");
  }

}

/* ok */
function edit_option($id)
{
  global $tpl, $db;

  if(isset($_POST["add"])){
    return option_save2db($id, "add");
  }elseif(isset($_POST["edit"])){
    return option_save2db($id, "update");
  }elseif(isset($_POST["delete"])){
    return option_save2db($id, "delete");
  }

  if($id == 0){
    $group = array(
      'id' => 0,
      'title' => '',
      'alias' => '',
      'sort' => '',
      'type' => 'val',
      'group_id' => 0,
      'filter_type' => 'text',
      'if_select' => '',
      'show_in_list' => '',
      'show_in_filter' => '',
      'filter_description' => '',
      'after' => '', 
      'icon' => '',
    );
  }else{
    $query = "SELECT *, group_id as `group` FROM ".$db->tables['options']." WHERE id = '".$id."' ";
    $group = $db->get_row($query, ARRAY_A);
    if($id > 0 && $db->num_rows == 0){
      return error_not_found();    
    }            
  }

  $query = "SELECT id, title FROM ".$db->tables['option_groups']." ORDER BY `title` ";
  $group["option_group"] = $db->get_results($query, ARRAY_A);
  $tpl->assign("option", $group);
  return $tpl->display("products/edit_option.html");
}


/* ok */
function list_options($page)
{
  global $tpl, $db;
  if(isset($_POST['update']) && isset($_POST["sort"])){
    $i = 0;
    foreach($_POST["sort"] as $k=>$v){
      $db->query("UPDATE ".$db->tables['options']." SET `sort` = '".intval($v)."' WHERE id = '".intval($k)."' ");
      $i++;
    }
    $href = "?action=products&do=options";
    if(isset($_GET['categ'])){ $href .= "&categ=".intval($_GET['categ']); }
    if(isset($_GET['where'])){ $href .= "&where=".$_GET['where']; }
    if(isset($_GET['group'])){ $href .= "&group=".intval($_GET['group']); }

    if(!empty($_POST["delopt"])){
      $i = 0;
      foreach($_POST["delopt"] as $k=>$v){
        $db->query("DELETE FROM ".$db->tables['options']." WHERE id = '".intval($v)."' ");
        $db->query("DELETE FROM ".$db->tables['option_values']." WHERE id_option = '".intval($v)."' ");
        $i++;
      }
      $href .= "&deleted=".$i;
      header("Location: ".$href);
      exit;
    }

    $href .= "&updated=".$i;
    header("Location: ".$href);
    exit;
  }
  
  $group = get_param("group",0);
  $categ = get_param("categ",0);
  $where = get_param("where","");
  $opt_filter = group_filter();
  
  if(!empty($opt_filter['categs'])){
	  foreach($opt_filter['categs'] as $v){
		  if(isset($tpl->_vars['site_vars']['_pages'][$v['id']])){
			  $tpl->_vars['site_vars']['_pages'][$v['id']]['options'] = '1';
		  }
	  }
  }    
  $tpl->assign("filter", $opt_filter);

  $wheres = array();
  
	//if($categ != 0) $categ_sql = " AND sco.id_categ='$categ' ";

	$sql_num = "SELECT COUNT(DISTINCT o.id) 
			FROM ".$db->tables['options']." o 
	";
	
	$query = "SELECT o.*, o.group_id as `group`, 
				og.title as group_title 
			FROM ".$db->tables['options']." o  
	";
		
	if($categ != 0){
		//$sql_num .= ", ".$db->tables['option_groups']." og , ".$db->tables['categ_options']." sco ";
		/*
		$query .= ", ".$db->tables['option_groups']." og , ".$db->tables['categ_options']." sco 
		";
		*/
		$query .= "LEFT JOIN ".$db->tables['option_groups']." og 
			ON (o.group_id=og.id)
		";
		$query .= "LEFT JOIN ".$db->tables['categ_options']." sco  
			ON (og.id=sco.id_option)
		";

		$sql_num .= "LEFT JOIN ".$db->tables['option_groups']." og 
			ON (o.group_id=og.id)
		";
		$sql_num .= "LEFT JOIN ".$db->tables['categ_options']." sco  
			ON (og.id=sco.id_option)
		";
	}else{
		$query .= "LEFT JOIN ".$db->tables['option_groups']." og 
			ON (o.group_id = og.id)
		";
		$query .= "LEFT JOIN ".$db->tables['categ_options']." sco  
			ON (og.id=sco.id_option)
		";
		$sql_num .= "LEFT JOIN ".$db->tables['option_groups']." og 
			ON (o.group_id=og.id)
		";
		$sql_num .= "LEFT JOIN ".$db->tables['categ_options']." sco  
			ON (og.id=sco.id_option)
		";	}
	
	if($group != 0) $wheres[] = " o.`group_id`='$group' ";
	if($categ != 0){ 
		//$wheres[] = " o.group_id=og.id " ;
		//$wheres[] = " og.id=sco.id_option "; 
		$wheres[] = "sco.id_categ = '$categ'";
	}
	
	if(!empty($where)){
		$wheres[] = "sco.where_placed = '$where'";
	} 
  
	if(count($wheres)){
		$query .= " WHERE ".implode(" AND ",$wheres);		
		$sql_num .= " WHERE ".implode(" AND ",$wheres);		
	} 
	
	$query .= " 
		GROUP BY o.id 
		ORDER BY og.`sort`, og.`title`, o.`sort`, o.`title` 
	";
	

	// PAGE LIMITS
	$page = isset($_GET["page"]) ? intval($_GET["page"]) : 0;
	$on_Page = ONPAGE*2;
	$limit_Start = $page*$on_Page; // for pages generation
	$query .= " LIMIT $limit_Start, $on_Page ";
	$all_results = $db->get_var($sql_num);
	//$db->debug(); exit;
	$tpl->assign("all_results",$all_results);
	$tpl->assign("pages",_pages($all_results, $page, $on_Page,true));
	$result = $db->get_results($query, ARRAY_A);
//$db->debug(); exit;
	if(!empty($db->last_error)){ 
		return db_error(basename(__FILE__).": ".__LINE__); 
	}
	
	$tpl->assign("list_options",$result);
	return $tpl->display("products/list_options.html");
}


/* ok */
function edit_option_group($id)
{
  global $tpl, $lang, $db;

  if(isset($_POST["add_group"])){
    return option_group_save2db($id, "add");
  }elseif(isset($_POST["edit_group"])){
    return option_group_save2db($id, "update");
  }elseif(isset($_POST["delete_group"])){
    return option_group_save2db($id, "delete");
  }
  $query = "SELECT *, where_placed as `where` FROM ".$db->tables['option_groups']." WHERE id = '".$id."' ";
  $group = $db->get_row($query, ARRAY_A);
  if($id > 0 && $db->num_rows == 0){ return error_not_found();  }
  if($id == 0){
    $group = array('description' => '', 'id' => 0, 'sort' => 99, 'title' => '', 'where' => 'product', 'opt_title' => '', 'value1' => '', 'value2' => '', 'value3' => '');
  }
  
	/*	
  $query = "(SELECT c.id, c.title, 
			c3.sort as sort1, c2.sort as sort2, 
			c.sort, c.id_parent, c.shop,  
            c2.title as subtitle, c2.id_parent as sub_parent, 
			c3.title as subtitle1, c3.id_parent as sub_parent1, 
            co.id_option as group_id 
        FROM ".$db->tables['categs']." c 
        LEFT JOIN ".$db->tables['categs']." c2 on (c.id_parent = c2.id) 
		LEFT JOIN ".$db->tables['categs']." c3 on (c2.id_parent = c3.id) 
        LEFT JOIN ".$db->tables['categ_options']." co on (co.id_categ = c.id AND co.id_option = '".$id."') 
        GROUP BY c.id
        ORDER BY c3.sort) ORDER BY sort2, sort, title ";
	*/	
		
// (SELECT ... ORDER BY `date` DESC LIMIT 15) ORDER BY `score` DESC;
	$query = "SELECT id_categ   
		FROM ".$db->tables['categ_options']." 
		WHERE id_option = '".$id."'
        GROUP BY id_categ
         ";
	$checked_categs = $db->get_results($query, ARRAY_A);
	$group['checked_categs'] = $checked_categs;

	$group['categs'] = getmenu($db->tables['categs'], 0,0,0);
		
	foreach($checked_categs as $v){
		$group['categs'][$v['id_categ']]['group_id'] = $id;
	}	
  
  $sql = "SELECT * 
	FROM ".$db->tables['options']." 
	WHERE group_id = '".$id."' 
	ORDER BY `sort`, `title`
  ";
  $group['options'] = $db->get_results($sql, ARRAY_A);
  $tpl->assign("group",$group);
  return $tpl->display("products/edit_option_group.html");
}

/* ok */
function option_group_save2db($id, $what) // what - add, delete, update
{
  global $db;
  $title = isset($_POST["title"]) ? trim($_POST["title"]) : "";
  $to_show = isset($_POST["to_show"]) ? trim($_POST["to_show"]) : "all";
  $sort = isset($_POST["sort"]) ? intval($_POST["sort"]) : 0;
  $description = isset($_POST["description"]) ? trim($_POST["description"]) : "";
  $where = isset($_POST["where"]) ? trim($_POST["where"]) : "all";
  $opt_title = isset($_POST['opt_title']) ? trim($_POST['opt_title']) : '';
  $hide_title = isset($_POST["hide"]) ? intval($_POST["hide"]) : 0;
  $value1 = isset($_POST['value1']) ? trim($_POST['value1']) : '';
  $value2 = isset($_POST['value2']) ? trim($_POST['value2']) : '';
  $value3 = isset($_POST['value3']) ? trim($_POST['value3']) : '';

  if($what == "add"){
    $db->query("INSERT INTO ".$db->tables['option_groups']." 
        (`title`, `to_show`, `hide_title`, `sort`, `description`, `where_placed`, `opt_title`, 
		`value1`, `value2`, `value3`) VALUES(
          '".$db->escape($title)."', 
          '".$db->escape($to_show)."', 
          '".$db->escape($hide_title)."', 
          '".$sort."',
          '".$db->escape($description)."',
          '".$db->escape($where)."',
          '".$db->escape($opt_title)."',
          '".$db->escape($value1)."',
          '".$db->escape($value2)."',
          '".$db->escape($value3)."'
        )");
    $id = $db->insert_id;
	register_changes('option_group', $id, 'add', $title);
    
    if(!empty($_POST["categs"])){
      foreach($_POST["categs"] as $v){
        $db->query("INSERT INTO ".$db->tables['categ_options']." 
            (`id_option`, `id_categ`, `where_placed`) 
            VALUES('".$id."', '".$v."', 'categ') ");        
      }
    }
    clear_cache(0);
    $href = "?action=products&do=option_group&id=".$id."&added=1";
    header("Location: ".$href);
    exit;
  }elseif($what == "update"){
    $query = "UPDATE ".$db->tables['option_groups']." SET
            `title` = '".$db->escape($title)."',
            `to_show` = '".$db->escape($to_show)."',
            `hide_title` = '".$db->escape($hide_title)."',
            `sort` = '".$sort."',
            `description` = '".$db->escape($description)."',            
            `where_placed` = '".$db->escape($where)."', 
            `opt_title` = '".$db->escape($opt_title)."', 
            `value1` = '".$db->escape($value1)."', 
            `value2` = '".$db->escape($value2)."', 
            `value3` = '".$db->escape($value3)."' 
            WHERE `id` = '".$id."' ";
    $db->query($query);
	register_changes('option_group', $id, 'update');

    $db->query("DELETE FROM ".$db->tables['categ_options']." WHERE `id_option` = '".$id."' ");
    if(!empty($_POST["categs"])){
      foreach($_POST["categs"] as $v){
        $db->query("INSERT INTO ".$db->tables['categ_options']." 
            (`id_option`, `id_categ`, `where_placed`) 
            VALUES('".$id."', '".$v."', 'categ') ");        
      }
    }
	clear_cache(0);
    $href = "?action=products&do=option_group&id=".$id."&updated=1";
    header("Location: ".$href);
    exit;
  }elseif($what == "delete"){
    $db->query("DELETE FROM ".$db->tables['option_groups']." WHERE `id` = '".$id."' ");
    $db->query("DELETE FROM ".$db->tables['categ_options']." WHERE `id_option` = '".$id."' ");
	register_changes('option_group', $id, 'delete');
	clear_cache(0);
    $href = "?action=products&do=option_group&deleted=1";
    header("Location: ".$href);
    exit;
  }else{
    return error_not_found("The page not found");
  }

}


/* ok */
function option_group($id)
{
  if($id > 0){ return edit_option_group($id); }
  return list_option_groups();
}


/* ok */
function list_option_groups()
{
  global $tpl, $db;
  $query = "SELECT *, where_placed as `where` FROM ".$db->tables['option_groups']." ORDER BY `sort`, `title`";
  $rows = $db->get_results($query);
	if(!empty($db->last_error)){
		return db_error(basename(__FILE__).' LINE: '.__LINE__); 			
	}
  $ar = array();
  if($db->num_rows > 0){
    foreach($rows as $row){
      $pages = $db->get_results("SELECT sco.id_categ, sco.where_placed as `where`, 
          cat3.title as cat_title, cat3.alias as cat_alias, cat3.active as cat_active, 
		  cat2.title as cat2_title,
		  cat1.title as cat1_title 
        FROM ".$db->tables['categ_options']." sco 
        LEFT JOIN ".$db->tables['categs']." cat3 on (cat3.id = sco.id_categ) 
		LEFT JOIN ".$db->tables['categs']." cat2 on (cat3.id_parent = cat2.id) 
		LEFT JOIN ".$db->tables['categs']." cat1 on (cat2.id_parent = cat1.id) 
        WHERE sco.id_option = '".$row->id."' 
        ORDER BY cat1.sort, cat2.sort, cat3.sort, sco.where_placed, sco.id_categ ");
      $pages_ar = array();

      if($pages && $db->num_rows > 0){
        foreach($pages as $page){          
          $link = "?action=info&do=edit_categ&id=".$page->id_categ;
          $ext_link = $page->cat_alias;
          $title = '';
		  if(!empty($page->cat1_title)){ $title .= $page->cat1_title.' &raquo; '; }
		  if(!empty($page->cat2_title)){ $title .= $page->cat2_title.' &raquo; '; }
		  if(!empty($page->cat_title)){ $title .= $page->cat_title; }
		  
          $active = $page->cat_active;
          $pages_ar[] = array(
            'link' => $link,
            'ext_link' => $ext_link,
            'title' => $title,
            'active' => $active 
          );          
        }
      }
       //$db->debug(); exit;     
      $ar[] = array(
        'id' => $row->id,
        'title' => $row->title,
        'sort' => $row->sort,
        'to_show' => $row->to_show,
        'where' => $row->where,
        'description' => $row->description,
        'pages' => $pages_ar
      ); 
    }
  }
  $tpl->assign("list_groups",$ar);  
  return $tpl->display("products/list_option_groups.html");
}


/* 09.08.2016 */
function list_products_opts($cid, $page)
{
	global $db, $tpl;
	$page_start = ONPAGE*$page;
	$page_stop = ONPAGE;	
	
	$cid_ar = array();
	  if(!empty($_GET["cid"])){ // filter by category
		$cid = intval($_GET["cid"]);
		
		$cid_ar = $db->get_row("SELECT c.id, c.title, c.alias, c.active, s.site_url 	
			FROM ".$db->tables['categs']." c 		
			LEFT JOIN ".$db->tables['site_categ']." sc ON (c.id = sc.id_categ) 
			LEFT JOIN ".$db->tables['site_info']." s ON (sc.id_site = s.id)
			WHERE c.id = '".$cid."' ", ARRAY_A);
		if(!empty($cid_ar['site_url'])){
			$cid_ar['url'] = $cid_ar['site_url'].'/'.$cid_ar['alias'].URL_END;
			if(empty($cid_ar['active'])){
				$cid_ar['url'] .= '?debug';
			}
		}
	  }
	  $tpl->assign("cid", $cid_ar);
	
	
	if(isset($_POST['update'])){
		$i = 0;
		
		
		if(!empty($_POST["option_u"])){
			foreach($_POST["option_u"] as $k => $v){
				if(!isset($v['value'])){ $v['value'] = ''; }
				if(!isset($v['value2'])){ $v['value2'] = ''; }
				if(!isset($v['value3'])){ $v['value3'] = ''; }
				
				if(is_array($v['value'])){
					$str = '';
					foreach($v['value'] as $v21 => $v22){
						if($v21 > 0){ $str .= "\n"; }
						$str .= $v22;
					}
					$v['value'] = $str;
				}

				if(is_array($v['value2'])){
					$str = '';
					foreach($v['value2'] as $v21 => $v22){
						if($v21 > 0){ $str .= "\n"; }
						$str .= $v22;
					}
					$v['value2'] = $str;
				}

						
				if(is_array($v['value3'])){
					$str = '';
					foreach($v['value3'] as $v21 => $v22){
						if($v21 > 0){ $str .= "\n"; }
						$str .= $v22;
					}
					$v['value3'] = $str;
				}
				
				$sql = "UPDATE ".$db->tables['option_values']." SET 
					`value` = '".$db->escape(trim($v['value']))."', 
					`value2` = '".$db->escape(trim($v['value2']))."', 
					`value3` = '".$db->escape(trim($v['value3']))."' 
					WHERE 
						id = '".$k."'
				";
				$db->query($sql);
				$i++;
			}
			
		}
		
		if(!empty($_POST["option"])){
			foreach($_POST["option"] as $k => $v){
				
				if(!empty($v)){
					foreach($v as $v1 => $v2){
						if(!isset($v2['value'])){ $v2['value'] = ''; }
						if(!isset($v2['value2'])){ $v2['value2'] = ''; }
						if(!isset($v2['value3'])){ $v2['value3'] = ''; }
						
						if(is_array($v2['value'])){
							$str = '';
							foreach($v2['value'] as $v21 => $v22){
								if($v21 > 0){ $str .= "\n"; }
								$str .= $v22;
							}
							$v2['value'] = $str;
						}


						if(is_array($v2['value2'])){
							$str = '';
							foreach($v2['value2'] as $v21 => $v22){
								if($v21 > 0){ $str .= "\n"; }
								$str .= $v22;
							}
							$v2['value2'] = $str;
						}

						
						if(is_array($v2['value3'])){
							$str = '';
							foreach($v2['value3'] as $v21 => $v22){
								if($v21 > 0){ $str .= "\n"; }
								$str .= $v22;
							}
							$v2['value3'] = $str;
						}
						
						$sql = "INSERT INTO 
							".$db->tables['option_values']." 
							(id_option, id_product, 
							`value`, `where_placed`, 
							`value2`, `value3`) 
							VALUES ('".$v1."', '".$k."', 
							'".$db->escape(trim($v2['value']))."', 
							'product', 
							'".$db->escape(trim($v2['value2']))."', 
							'".$db->escape(trim($v2['value3']))."') ";
						$db->query($sql);
						
					}
				}
				$i++;
				
			}
			
		}		
		
		if(isset($_POST['products_qty'])){ $i = $_POST['products_qty']; }
		$url = "?action=products&do=list_products&cid=".$cid."&updated=".$i."&options=1";
		header("Location: ".$url);
		exit;		
		
	}
	
	// вычислим поле для сортировки
	$to_sort = $db->get_var("SELECT `sortby` FROM ".$db->tables['categs']." WHERE id = '".$cid."' ");
	//exit;
	if($to_sort == 'views desc' || $to_sort == 'monthviews desc' || empty($to_sort)){
		$to_sort = 'date_insert desc';
	}	
	
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
	$sql .= " ORDER BY g.sort, g.title, o.sort, o.title ";
	$rows = $db->get_results($sql, ARRAY_A);
	$tpl->assign("th_options",$rows);
		
	$sql = "SELECT product.*, cat.id_categ as categ1, 
              (SELECT COUNT(DISTINCT `id_in_record`) FROM ".$db->tables['uploaded_pics']."  u 
                WHERE u.record_id = product.id 
                AND u.record_type = 'product') as fotos, 
				s.site_url, s.site_active, s.id as site_id
            FROM ".$db->tables['products']." as product, 
				".$db->tables['pub_categs']." as cat 
			LEFT JOIN ".$db->tables['site_categ']." sc ON (cat.id_categ = sc.id_categ) 
			LEFT JOIN ".$db->tables['site_info']." s ON (sc.id_site = s.id)
            WHERE cat.id_categ = '".$cid."' AND
            cat.id_pub = product.id AND 
			cat.where_placed = 'product' 
			GROUP BY cat.id 
            ORDER BY ".$to_sort." ";
	$results = $db->get_results($sql, ARRAY_A);
	
	$all_results = $db->num_rows;
	$results = $db->get_results($sql." LIMIT $page_start, $page_stop ", ARRAY_A);
	$tpl->assign("products_list",$results);
	
	$u_options = array();
	foreach($results as $k => $v){
		$u_options[$v['id']] = get_options_in_list($v['id']);
	}
	
	// All products
	$tpl->assign("products_qty",$all_results);
	$tpl->assign("u_options",$u_options);
	
	// All catalogs
	$catalogs = $db->get_results("SELECT c.id, c.title, c.alias, 
			c.id_parent, c.sort, 
			c2.title as parent_title, 
			c3.title as parent2_title,  
			IFNULL(c2.sort,c.sort) as sort2, 
			IFNULL(c3.sort,IFNULL(c2.sort,c.sort)) as sort3 
			
		FROM ".$db->tables['pub_categs']." p 
		LEFT JOIN ".$db->tables['categs']." c on (c.id = p.id_categ) 
		LEFT JOIN ".$db->tables['categs']." c2 on (c2.id = c.id_parent) 
		LEFT JOIN ".$db->tables['categs']." c3 on (c3.id = c2.id_parent) 
		WHERE p.`where_placed` = 'product' 
		GROUP BY p.id_categ
		ORDER BY sort3, sort2, c.sort, c.title
		", ARRAY_A);
	$tpl->assign("catalogs",$catalogs);
	
	$tpl->assign("pages", _pages($all_results, $page, ONPAGE));
	$str = $tpl->display("products/list_products.html");
	return $str;
}




/* ok */
function list_products($page, $active_only = false)
{
  global $db, $tpl;
  $where = "";
  
  if(!empty($_GET['options']) && !empty($_GET["cid"])){
	  $cid = intval($_GET["cid"]);
	  return list_products_opts($cid, $page);
  }

  if(isset($_POST["update"]))
  {
    $r_str = "";
    if(!empty($_GET["cid"])){
      $r_str .= "&cid=".intval($_GET["cid"]);
    }
    if(!empty($_GET["q"])){
      $r_str .= "&q=".urlencode($_GET["q"]);
    }         

  	$goods = get_param("goods",array());
  	$active_ar = get_param("active",array());
    $accept_orders_ar = get_param("accept_orders",array()); 
  	$f_new_ar = get_param("f_new",array());
  	$f_spec_ar = get_param("f_spec",array());

  	foreach($goods as $k => $v)
  	{
  		$active = (in_array($k,$active_ar)) ? 1 : 0;
  		$accept_orders = (in_array($k,$accept_orders_ar)) ? 1 : 0;
  		$f_new = (in_array($k,$f_new_ar)) ? 1 : 0;
  		$f_spec = (in_array($k,$f_spec_ar)) ? 1 : 0;

      $db->query("UPDATE ".$db->tables['products']." SET 
          `name` = '".$db->escape(trim($v['name']))."',
          `alias` = '".$db->escape(trim($v['alias']))."',
          `price` = '".$db->escape(trim($v['price']))."',
          `bid_ya` = '".$db->escape(trim($v['bid_ya']))."',
          `active` = '".$active."',
          `accept_orders` = '".$accept_orders."',
          `f_new` = '".$f_new."',
          `f_spec` = '".$f_spec."',
          `date_update` = '".date("Y-m-d H:i:s")."'
        WHERE id = '".$k."'
      ");
  	}

    if(!empty($_POST["del"])){
      foreach($_POST["del"] as $v){
        // удаляем товар $v
        delete_products($v);
      }
	  clear_cache(0);
      header("Location: ?action=products&do=list_products".$r_str."&deleted=".count($_POST["del"]));
      exit;        
    }
	clear_cache(0);
    header("Location: ?action=products&do=list_products".$r_str."&updated=".count($goods));
    exit;            
  }
  
  $page_start = ONPAGE*$page;
  $page_stop = ONPAGE;
  if(isset($_GET["active"])){ $active_only = true; }
  if($active_only){
    $q_str = "WHERE `active` = '1' ";
    $q_str_1 = "AND `active` = '1' ";
  } else { $q_str = $q_str_1 = ""; }

  $cid_ar = array();
  if(!empty($_GET["cid"])){ // filter by category
    $cid = intval($_GET["cid"]);
	
	// вычислим поле для сортировки
	$to_sort = $db->get_var("SELECT `sortby` FROM ".$db->tables['categs']." WHERE id = '".$cid."' ");
	//exit;
	if($to_sort == 'views desc' || $to_sort == 'monthviews desc' || empty($to_sort)){
		$to_sort = 'date_insert desc';
	}
	
    $query = "SELECT product.*, 
              (SELECT COUNT(DISTINCT `id_in_record`) FROM ".$db->tables['uploaded_pics']."  u 
                WHERE u.record_id = product.id 
                AND u.record_type = 'product') as fotos, 
				
					(SELECT count(*) 
						FROM ".$db->tables['site_categ']." sc  
						WHERE cat.id_categ = sc.id_categ 
					) as site_url_qty, 
					
					(SELECT s.site_url 
						FROM ".$db->tables['site_categ']." sc  
						LEFT JOIN ".$db->tables['site_info']." s ON (sc.id_site = s.id)
						WHERE cat.id_categ = sc.id_categ 
						LIMIT 0,1
					) as site_url, 
					
					(SELECT s.id 
						FROM ".$db->tables['site_categ']." sc  
						LEFT JOIN ".$db->tables['site_info']." s ON (sc.id_site = s.id)
						WHERE cat.id_categ = sc.id_categ 
						LIMIT 0,1
					) as site_id,
					(SELECT s.site_active  
						FROM ".$db->tables['site_categ']." sc  
						LEFT JOIN ".$db->tables['site_info']." s ON (sc.id_site = s.id)
						WHERE cat.id_categ = sc.id_categ 
						LIMIT 0,1
					) as site_active
				
				
            FROM ".$db->tables['products']." as product, ".$db->tables['pub_categs']." as cat
            WHERE cat.id_categ = '".$cid."' AND
            cat.id_pub = product.id AND 
			cat.where_placed = 'product' 
	";
	
	if(!empty($_GET["q"])){
		$q = $db->escape($_GET["q"]);
		$query .= " AND 
			( product.`name` LIKE '%".$q."%' 
			OR product.`alias` LIKE '%".$q."%' 
			OR product.`name_short` LIKE '%".$q."%' 
			OR product.`barcode` LIKE '%".$q."%') 
		";		
	}
		
	$query .= " $q_str_1 ORDER BY ".$to_sort." 
	";
	$cid_ar = $db->get_row("SELECT c.id, c.title, c.alias, c.active, s.site_url 	
		FROM ".$db->tables['categs']." c 		
		LEFT JOIN ".$db->tables['site_categ']." sc ON (c.id = sc.id_categ) 
		LEFT JOIN ".$db->tables['site_info']." s ON (sc.id_site = s.id)
		WHERE c.id = '".$cid."' ", ARRAY_A);
	if(!empty($cid_ar['site_url'])){
		$cid_ar['url'] = $cid_ar['site_url'].'/'.$cid_ar['alias'].URL_END;
		if(empty($cid_ar['active'])){
			$cid_ar['url'] .= '?debug';
		}
	}
  }
  $tpl->assign("cid", $cid_ar);

  if(isset($_GET["q"])){ // filter by product name
    if($q_str != ""){ $q_str .= " AND "; } else { $q_str .= " WHERE "; }
    $q = $db->escape($_GET["q"]);
    $q_str .= " ( `name` LIKE '%".$q."%' OR `alias` LIKE '%".$q."%' 
        OR `name_short` LIKE '%".$q."%' OR `barcode` LIKE '%".$q."%')";
  }

  
  if(!isset($query)){
    $query = "SELECT 
					product.*, 
					c.id_categ as categ1, 
					c2.id_categ as categ2,
					(SELECT COUNT(DISTINCT `id_in_record`) 
						FROM ".$db->tables['uploaded_pics']."  u 
						WHERE u.record_id = product.id 
						AND u.record_type = 'product'
					) as fotos, 
					(SELECT count(*) 
						FROM ".$db->tables['site_categ']." sc  
						WHERE c.id_categ = sc.id_categ 
					) as site_url_qty, 
					
					(SELECT s.site_url 
						FROM ".$db->tables['site_categ']." sc  
						LEFT JOIN ".$db->tables['site_info']." s ON (sc.id_site = s.id)
						WHERE c.id_categ = sc.id_categ 
						LIMIT 0,1
					) as site_url, 
					
					(SELECT s.id 
						FROM ".$db->tables['site_categ']." sc  
						LEFT JOIN ".$db->tables['site_info']." s ON (sc.id_site = s.id)
						WHERE c.id_categ = sc.id_categ 
						LIMIT 0,1
					) as site_id,
					(SELECT s.site_active  
						FROM ".$db->tables['site_categ']." sc  
						LEFT JOIN ".$db->tables['site_info']." s ON (sc.id_site = s.id)
						WHERE c.id_categ = sc.id_categ 
						LIMIT 0,1
					) as site_active
					
			FROM ".$db->tables['products']." product  
			LEFT JOIN ".$db->tables['pub_categs']." c on 
				(product.id = c.id_pub AND c.where_placed = 'product') 
			LEFT JOIN ".$db->tables['pub_categs']." c2 on (product.id = c2.id_pub AND c2.where_placed = 'product' AND c2.id <> c.id ) 
			
			$where $q_str group by product.id 
			ORDER BY product.`date_insert` desc 
	";
		
  }
  
  if(!empty($_GET['lider'])){
				
		$query = "SELECT p.id, p.`name`, 
				p.price, p.active, p.accept_orders, 
				p.bid_ya, p.f_new, p.f_spec,  
				p.currency, p.alias, p.multitpl, 
					cat.id_categ as categ1, 
				(SELECT COUNT(DISTINCT orderid) 
					FROM ".$db->tables['orders_cart']." 
					WHERE product_id = c.product_id 
				) as cnt,
				
					(SELECT count(*) 
						FROM ".$db->tables['site_categ']." sc  
						WHERE cat.id_categ = sc.id_categ 
					) as site_url_qty, 
					
					(SELECT s.site_url 
						FROM ".$db->tables['site_categ']." sc  
						LEFT JOIN ".$db->tables['site_info']." s ON (sc.id_site = s.id)
						WHERE cat.id_categ = sc.id_categ 
						LIMIT 0,1
					) as site_url, 
					
					(SELECT s.id 
						FROM ".$db->tables['site_categ']." sc  
						LEFT JOIN ".$db->tables['site_info']." s ON (sc.id_site = s.id)
						WHERE cat.id_categ = sc.id_categ 
						LIMIT 0,1
					) as site_id,
					(SELECT s.site_active  
						FROM ".$db->tables['site_categ']." sc  
						LEFT JOIN ".$db->tables['site_info']." s ON (sc.id_site = s.id)
						WHERE cat.id_categ = sc.id_categ 
						LIMIT 0,1
					) as site_active
				
				FROM ".$db->tables['orders_cart']." c 
				LEFT JOIN ".$db->tables['products']." p 
					ON (c.product_id = p.id) 
				LEFT JOIN ".$db->tables['pub_categs']." cat on 
				(p.id = cat.id_pub AND cat.where_placed = 'product') 
				
			WHERE (SELECT COUNT(DISTINCT orderid) 
					FROM ".$db->tables['orders_cart']." 
					WHERE product_id = c.product_id 
					
				) > '0' 
			GROUP BY c.product_id 
			ORDER BY cnt DESC, p.name 
		";
		
		
  }
  
  
  $results = $db->get_results($query, ARRAY_A);
  $all_results = $db->num_rows;
  $results = $db->get_results($query." LIMIT $page_start, $page_stop ", ARRAY_A);
  //$db->debug(); exit;
  $tpl->assign("products_list",$results);
  
//$db->debug(); exit;
  // All products
  $tpl->assign("products_qty",$all_results);

  // All catalogs
  $catalogs = $db->get_results("SELECT c.id, c.title, c.alias, 
			c.id_parent, c.sort, 
			c2.title as parent_title, 
			c3.title as parent2_title,  
			IFNULL(c2.sort,c.sort) as sort2, 
			IFNULL(c3.sort,IFNULL(c2.sort,c.sort)) as sort3 
			
		FROM ".$db->tables['pub_categs']." p 
		LEFT JOIN ".$db->tables['categs']." c on (c.id = p.id_categ) 
		LEFT JOIN ".$db->tables['categs']." c2 on (c2.id = c.id_parent) 
		LEFT JOIN ".$db->tables['categs']." c3 on (c3.id = c2.id_parent) 
		WHERE p.`where_placed` = 'product' 
		GROUP BY p.id_categ
		ORDER BY sort3, sort2, c.sort, c.title
		", ARRAY_A);
	$tpl->assign("catalogs",$catalogs);
  $tpl->assign("pages", _pages($all_results, $page, ONPAGE));
  $str = $tpl->display("products/list_products.html");
  return $str;
}
        
/* ok */
function group_filter()
{
	global $db, $lang;
	$group = get_param("group",0);
	$categ = get_param("categ",0);
	$str = "<form>\n";
	$str .= "<input name=\"action\" type=\"hidden\" value=\"products\">\n";
	$str .= "<input name=\"do\" type=\"hidden\" value=\"options\">\n";
	$str .= "".GetMessage("admin", "categ").": <select name=\"categ\">";
	
	$rows2 = $db->get_results("SELECT 
			c.id, c.title as title, c2.title as title2 , 
			c3.title as title3, c.sort, 
			IFNULL(c2.sort,c.sort) as sort2,
			IFNULL(c3.sort,IFNULL(c2.sort,c.sort)) as sort3
		FROM ".$db->tables['categ_options']." sco, 
			".$db->tables['categs']." c 
		LEFT JOIN ".$db->tables['categs']." c2 ON (c.id_parent = c2.id) 
		LEFT JOIN ".$db->tables['categs']." c3 ON (c2.id_parent = c3.id)
		WHERE sco.id_categ = c.id  AND sco.where_placed = 'categ' 
		GROUP BY c.id  
		ORDER BY sort3, sort2, c.sort, c.title
	", ARRAY_A);

	$rows3 = $db->get_results("SELECT *, where_placed as `where` 
		FROM ".$db->tables['option_groups']." 
		ORDER BY `sort` 
	", ARRAY_A);

  $ar = array(
    'categs' => $rows2,
    'groups' => $rows3
  );
  return $ar;
}

/* ok */
function edit_products($id)
{
  global $tpl, $db, $site_vars, $admin_vars, $site;
  if($id == 0 && isset($_GET['slug'])){
    $slug = $db->escape(trim($_GET['slug']));
    $id = $db->get_var("SELECT id FROM ".$db->tables['products']." WHERE alias = '".$slug."' ");
    if($id > 0){
      header("Location: ?action=products&do=edit&id=$id");
      exit;
    }else{
      return error_not_found(); 
    }
  }

  $query = "SELECT p.*, 
				u.login as user_login, 
				u.name as user_name, 
				(SELECT COUNT(*) FROM ".$db->tables["counter"]." 
					WHERE for_page = 'product' AND id_page = p.id 
					AND `time` > NOW() - interval 30 DAY 
				) as views_month, 
				
				(SELECT COUNT(DISTINCT id_in_record) 
					FROM ".$db->tables["uploaded_pics"]." 
					WHERE `record_id` = '".$id."' 
						AND record_type = 'product' 
				) as fotos_qty,
				
				(SELECT count(*) 
					FROM ".$db->tables["uploaded_files"]." 
					WHERE `record_id` = '".$id."' 
						AND record_type = 'product'
				) as files_qty 
				
		FROM ".$db->tables["products"]." p
		LEFT JOIN ".$db->tables['users']." u on (u.id = p.user_id) 
		WHERE p.id = '".$id."' ";
  $categ_info = $db->get_row($query, ARRAY_A);

  if($id > 0 && $db->num_rows == 0){ return error_not_found();  }

  if($id == 0){
    $prefix = isset($site_vars['sys_prefix_product']) ? $site_vars['sys_prefix_product'] : "item-";
    $categ_info = array(
      'id' => 0,
      'fotos_qty'=> 0,
      'files_qty' => 0,
      'date_insert' => date('Y-m-d H:i:s'),
      'date_update' => '0000-00-00 00:00:00',
      'user_name' => $admin_vars['bo_user']['name'],
      'user_id' => $admin_vars['bo_user']['id'],

      'name' => '',
      'name_short' => '',
      'meta_title' => '',
      'meta_description' => '',
      'meta_keywords' => '',
      'memo' => '',
      'memo_short' => '',
      'price_buy' => 0,
      'price' => 0,
      'total_qty' => 0,
      'price_opt' => 0,
      'price_spec' => 0,
      'price_period' => 'razovo',
      'comment' => '', 
      'active' => 1,
      'accept_orders' => 1,
      'weight_deliver' => 0,
      'complectation' => '',
      'barcode' => '',
      'id_next_model' => 0,
      'bid_ya' => 0,
      'alter_search' => '',
      'f_new' => 0,
      'f_spec' => 0,
      'present_id' => 0,
      'currency' => $admin_vars['default_currency'],
      'treba' => '0', 
      'alias' => $prefix.time(),
      'views' => 0,
      'brand' => 0,	
      'multitpl' => 'product.html'    
    
    );
  }

  if(!empty($site_vars['sys_tpl_products'])){
    $categ_info['tpls'] = get_array_vars('sys_tpl_products');
    if($id == 0){
      $categ_info['multitpl'] = $db->get_var("SELECT `value` 
        FROM ".$db->tables['site_vars']." 
        WHERE `name` = 'sys_tpl_products' ");  
    }  
  }
  
  $categ_info['comments_qty'] = $db->get_var("SELECT count(*) FROM ".$db->tables['comments']." 
	WHERE record_type = 'product' AND record_id = '".$id."'
	"); 
  
  $ar_fotos = array();
  if($categ_info['fotos_qty'] > 0){
    $ar_fotos = $db->get_results("SELECT * 
      FROM ".$db->tables["uploaded_pics"]." 
      WHERE `record_id` = '".$id."' AND record_type = 'product'       
      ORDER BY is_default desc, id_in_record, width", ARRAY_A); 
  }
  $categ_info['uploaded_fotos'] = $ar_fotos;  
  
  if($id > 0){
	include_once(MODULE.'/get_blocks.php');
	$site->uri['path'] = '/'.$categ_info['alias'];
	if(defined('__NAMESPACE__')){
		$categ_info['blocks'] = call_user_func(__NAMESPACE__ .'blocks::get_blocks', $site);
	}else{
		$categ_info['blocks'] = call_user_func('blocks::get_blocks', $site);
	}	
	
	/* найдем связанные заказы */
	$sql = "SELECT COUNT(DISTINCT `orderid`) as qty
		FROM ".$db->tables['orders_cart']." 
		WHERE product_id = '".$id."' 
	";
	$categ_info['orders_qty'] = $db->get_var($sql);
	
	if(!empty($categ_info['orders_qty']) 
		&& !empty($_GET['orders'])
	){
		$sql = "SELECT o.id, o.order_id, 
				o.created, o.fio, o.email, 
				o.phone, o.status, 
				o.payd_status, o.site_id, 
				os.title as status_title, 
				si.site_url as site 
			FROM ".$db->tables['orders_cart']." oc, 
				".$db->tables['orders']." o 
			LEFT JOIN ".$db->tables['site_info']." si ON (o.site_id = si.id)
			LEFT JOIN ".$db->tables['order_status']." os ON (o.status = os.id)
			WHERE o.id = oc.orderid 
				AND oc.product_id = '".$id."' 				
			GROUP BY o.id 
			ORDER BY o.created DESC 
		";
		
		// PAGE LIMITS		
		$page = isset($_GET["page"]) ? intval($_GET["page"]) : 0;
		$on_Page = ONPAGE*2;
		$limit_Start = $page*$on_Page; 
		$limit = "LIMIT $limit_Start, $on_Page";
		$sql = $sql." ".$limit;
		
		$categ_info['orders'] = $db->get_results($sql, ARRAY_A);
		
		$tpl->assign("pages", _pages($categ_info['orders_qty'], $page, $on_Page));
		/*$db->debug();
		exit;*/
	}
  }

  $ar_files = array();
  if($categ_info['files_qty'] > 0){
    $ar_files = $db->get_results("SELECT f.*, 
		(SELECT count(*) FROM ".$db->tables["counter"]." 
			WHERE `for_page` = 'file' 
			AND `id_page` = f.id) as downloaded, 
        (SELECT count(*) FROM ".$db->tables['counter']." 
            WHERE `for_page` = 'file' 
			AND id_page = f.id 
			AND `time` > DATE_ADD(NOW(), INTERVAL -30 DAY)) as dwnl30
		FROM ".$db->tables['uploaded_files']." f
      WHERE f.`record_id` = '".$id."' AND f.record_type = 'product'       
      ORDER BY f.id_in_record, f.title, f.id ", ARRAY_A); 
	  
  }
  $categ_info['uploaded_files'] = $ar_files;

  $categ_info['date_insert_f'] = date($site_vars['site_date_format']." ".$site_vars['site_time_format'], strtotime($categ_info['date_insert']));
  $categ_info['date_update_f'] = !empty($categ_info['date_update']) && $categ_info['date_update'] != '0000-00-00 00:00:00' ? date($site_vars['site_date_format']." ".$site_vars['site_time_format'], strtotime($categ_info['date_update'])) : '';

  $ar = array();
  $rows = $db->get_results("SELECT id_categ FROM ".$db->tables['pub_categs']." 
      WHERE id_pub = '".$id."' AND `where_placed` = 'product' ");
	if(!empty($db->last_error)){
		return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
	}
	  
  if($db->num_rows > 0){
    foreach($rows as $v){
      $ar[] = $v->id_categ;
    }
  }
  
  if($id == 0 && !empty($_GET['cid']) && intval($_GET['cid']) > 0){
    $ar[] = intval($_GET['cid']); 
  }

  $ids = Array2Str($ar, ',', '\'' );
  // Находим названия категорий, прицепленных к продукту
  $str2 = "";

  $categ_info['links'] = array();
  
	if(!empty($ids)){
        $query = "SELECT c.`id` as id_categ, c.title, c2.id as subid, c2.title as subtitle 
            FROM ".$db->tables["categs"]." c 
            LEFT JOIN ".$db->tables["categs"]." c2 on (c2.id = c.id_parent) 
            WHERE c.`id` IN (".$ids.")  ";             
        $product_categories = $db->get_results($query, ARRAY_A);
        if($db->num_rows > 0){  
            foreach($product_categories as $k=>$row){
                if($k == 0){ $str2 .= $row['id_categ']; }else{ $str2 .= ",".$row['id_categ'];}
				$product_categories[$row['id_categ']] = $row;
				unset($product_categories[$k]);
            }
        }

		
		/* links */
		$sql = "SELECT s.* 
			FROM ".$db->tables['site_info']." s 
			LEFT JOIN ".$db->tables['site_categ']." sc ON (sc.id_site = s.id) 
			WHERE sc.`id_categ` IN (".$ids.")  
		";
		$categ_info['links'] = $db->get_results($sql, ARRAY_A);
		if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }
		
    }else{
        $product_categories = array();
    }
  
  $tpl->assign("product_categories",$product_categories);
  $tpl->assign("product_categories_ids",$str2);

  
  // Находим названия товаров, связанных с данным товаром
  $categ_info['other_products'] = other_products_list($id, 'products');

  $connected_products = already_p2p($id);
  $ar1 = $ar2 = array();
  foreach($connected_products as $conn_ar){
    $ar1[] = $conn_ar['id']; 
    $ar2[$conn_ar['id']] = $conn_ar['title']; 
  }

  $tpl->assign("connected_products",$ar2);
  $tpl->assign("connected_products_ids",implode(",",$ar1));
  
// Получаем названия привязанных к публикации продуктов
  $publication_products = already_pub2p($id);
  $tpl->assign('publication_pubs_ids',implode(",",$publication_products));
  $pub_prod_names = $db->get_results("SELECT p.id,p.name as title
    FROM ".$db->tables["pub_to_product"]." pp 
    LEFT JOIN ".$db->tables["publications"]." p on (p.id = pp.id_pub) 
    WHERE pp.id_product = '".$id."' AND pp.id_pub > '0' 
    ORDER BY p.`name` ", ARRAY_A);	
  $tpl->assign('publication_pubs',$pub_prod_names);
  // --------------------------------------------------------------------
  
  $tpl->assign("blocks",get_blocks_info($id,'product'));

  if(isset($_POST["save"]) || isset($_POST["del"])){
    return save_products($id);
  }

  $categ_info['all_pubs'] = $db->get_var("SELECT count(*) FROM ".$db->tables['publications']." WHERE id <> '0' ");
  
  if(!empty($_GET['del_option'])){
	  $del_opt = intval($_GET['del_option']);
	  if($del_opt > 0){
		  $sql = "DELETE FROM ".$db->tables['option_values']." 
				WHERE 
					id = '".$del_opt."' 
					AND id_product = '".$id."'
					AND where_placed = 'product' 
		  ";
		  $db->query($sql);
		  //$db->debug(); exit;
		  $url = "?action=products&do=edit&id=".$id;
		  header("Location: ".$url);
		  exit;
	  }
  }
  
  $categ_info['options'] = get_options($id, $str2, 'catalog');
  $categ_info['record_type'] = 'product';
  $tpl->assign("record_type", "product");
  $tpl->assign("categ",$categ_info);
  return $tpl->display("products/edit_product.html");
}

/* ok, used for function edit_product */
function already_pub2p($id){
  global $db;
  $query = "SELECT * FROM ".$db->tables['pub_to_product']." WHERE id_product = '".$id."'  ";
  $rows = $db->get_results($query);  
  if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }
  if($db->num_rows == 0){ return array(); }
  $ar = array();
  foreach($rows as $row){
    $ar[] = $row->id_product;
  }
  return $ar;
}

/* ok */
function product_to_pubs($ar){
  global $db;
  $id = !empty($ar['id']) ? intval($ar['id']) : 0;
  $field = !empty($ar['field']) ? $ar['field'] : "";
  
  $query = "SELECT 
		p.id, p.`name`, p.alias, 
		p.active, p.f_spec, 
		c3.id as categ3_id, c3.title as categ3_title, 
		c2.id as categ2_id, c2.title as categ2_title, 
		c.id as categ_id, c.title as categ_title, 
		c.alias as categ_alias, 
		ptp.id_pub as pub_connected
	FROM ".$db->tables['publications']." p 
	LEFT JOIN ".$db->tables['pub_categs']." pc on (p.id = pc.id_pub) 
	LEFT JOIN ".$db->tables['categs']." c on (pc.id_categ = c.id) 
	LEFT JOIN ".$db->tables['categs']." c2 on (c.id_parent = c2.id) 
	LEFT JOIN ".$db->tables['categs']." c3 on (c2.id_parent = c3.id) 
	LEFT JOIN ".$db->tables['pub_to_product']." ptp on (p.id = ptp.id_pub AND ptp.id_product = '".$id."') 
	
	WHERE pc.where_placed = 'pub' AND p.id <> '0' 
	ORDER BY c3.sort, c3.title, c2.sort, c2.title, c.sort, c.title,  p.`name`
	";
  $rows = $db->get_results($query);  
  if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }
  if($db->num_rows == 0){ return; }
  $title_id = 0;
  $str = "<ul id='list_pub'>";
  foreach($rows as $row){
    if($field == ''){
      $form = "";
    }else{
      $ch = $row->pub_connected == $row->id ? "checked" : "";
      $form = "<input type=checkbox name=".$field."[] value='".$row->id."' $ch>";
    }
    if($title_id != $row->categ_id){
		$title = '';
		if(!empty($row->categ3_title)){
			$title .= $row->categ3_title.' / ';
		}
		
		if(!empty($row->categ2_title)){
			$title .= $row->categ2_title.' / ';
		}
		
		$title .= $row->categ_title;
		$str .= "<li><h4>".$title."</h4></li>";
	}
    $str .= "<li>$form <a href='javascript:' title='".$row->categ_title."'><span id='pub{$row->id}'>".stripslashes($row->name)."</span></a></li>";
	$title_id = $row->categ_id;
  }
  $str .= "</ul>";
  return $str;
}

/* ok */
function already_p2p($id){
  global $db;

  $query = "SELECT ptp.*, p1.`name` as title1, p2.`name` as title2 
    FROM `".$db->tables['product_to_product']."` ptp 
    LEFT JOIN ".$db->tables['products']." p1 on (p1.id = ptp.product1)
    LEFT JOIN ".$db->tables['products']." p2 on (p2.id = ptp.product2)
    WHERE 
        (product1 = '".$id."' AND product2 <> '".$id."' AND product2 > '0') 
        OR  
        (product2 = '".$id."' AND product1 <> '".$id."' AND product1 > '0') ";
  $rows = $db->get_results($query);
  if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }
  
  if($db->num_rows == 0){ return array(); }
  $ar = array();
  foreach($rows as $row){
    if($row->product1 != $id){
      $ar[] = array(
        'id' => $row->product1,
        'title' => $row->title1
      );
    }else{
      $ar[] = array(
        'id' => $row->product2,
        'title' => $row->title2
      );
    }
  }
  return $ar;
}

/* ok */
function other_products_list($id, $field=''){
  global $db;
  $ar1 = already_p2p($id);
  $ar = array();
  foreach($ar1 as $v){
    $ar[] = $v['id'];
  }
  $query = "SELECT * FROM ".$db->tables['products']." WHERE id <> '".$id."' order by `name` ";
  
  $query = "SELECT 
		p.`id`, p.`name`, p.`alias`,
		p.`active`, p.`f_spec`, p.`f_new`,
		c3.id as categ3_id, c3.title as categ3_title, 
		c2.id as categ2_id, c2.title as categ2_title, 
		c.id as categ_id, c.title as categ_title, 
		c.alias as categ_alias 
	FROM ".$db->tables['products']." p 
	LEFT JOIN ".$db->tables['pub_categs']." pc on (p.id = pc.id_pub) 
	LEFT JOIN ".$db->tables['categs']." c on (pc.id_categ = c.id) 
	LEFT JOIN ".$db->tables['categs']." c2 on (c.id_parent = c2.id) 
	LEFT JOIN ".$db->tables['categs']." c3 on (c2.id_parent = c3.id) 
	WHERE pc.where_placed = 'product' AND p.id <> '".$id."' 
	ORDER BY c3.sort, c3.title, c2.sort, c2.title, c.sort, c.title,  p.`name` ";

  $rows = $db->get_results($query);
  
  if($db->num_rows == 0){ return; }
  $title_id = 0;
  $str = "<ul id='list'>";
  foreach($rows as $row){
    if($field == ''){
      $form = "";
    }else{
      $ch = in_array($row->id, $ar) ? "checked" : "";
      $form = "<input type=checkbox name=products[] value='".$row->id."' $ch>";
    }
    if($title_id != $row->categ_id){
		$title = '';
		if(!empty($row->categ3_title)){
			$title .= $row->categ3_title.' / ';
		}
		
		if(!empty($row->categ2_title)){
			$title .= $row->categ2_title.' / ';
		}
		$title .= $row->categ_title;
		$str .= "<li><h4>".$title."</h4></li>";
	}
    $str .= "<li>$form <a href='javascript:;' title='".$row->categ_title."'><span id='produkt{$row->id}'>".stripslashes($row->name)."</span></a></li>";
	$title_id = $row->categ_id;
  }
  $str .= "</ul>";
  return $str;
}

/* ok */
function update_product_pubs($id){
	global $db;
	$sql = "DELETE FROM ".$db->tables['pub_to_product']." 
			WHERE id_product = '".$id."' ";
	$db->query($sql);
	if(!empty($_POST["id_pubs"])){
		$ar = explode(',', $_POST["id_pubs"]);
		if(!empty($ar)){
			foreach($ar as $v){
				$v = intval(trim($v));
				$sql = "INSERT INTO ".$db->tables['pub_to_product']." 
					(id_pub, id_product) VALUES ('".$v."', '".$id."')
				";
				$db->query($sql);
			}
		}
	}
	return;
}

/* ok */
/* last updated 07.08.2015 добавлено кол-во товара */
/* сохраняем данные о товаре */
function save_products($id)
{
  global $tpl, $db, $admin_vars;
  $posted = isset($_POST) ? $_POST : array();

  $posted["name"] = empty($posted["name"]) ? time() : trim($posted["name"]);
  if(empty($posted["meta_title"]) && $id == 0){ $posted["meta_title"] = $posted["name"]; }
  if(empty($posted["meta_description"]) && $id == 0){ $posted["meta_description"] = $posted["name"]; }
  if(empty($posted["meta_keywords"]) && $id == 0){ $posted["meta_keywords"] = $posted["name"]; }
  $ddate = date("Y-m-d H:i:s");
  if($posted["alias"] == ''){ $posted["alias"] = $posted["name"]; }
  $posted["alias"] = build_slug($posted["alias"], "product", $id);
  $memo = trim($posted["memo"]);         
  if(isset($_GET['noeditor'])) { $memo = nl2br($memo); }        

  if(empty($posted["f_new"])){ $posted["f_new"] = 0; }
  if(empty($posted["f_spec"])){ $posted["f_spec"] = 0; }
  if(empty($posted["active"])){ $posted["active"] = 0; }
  if(empty($posted["accept_orders"])){ $posted["accept_orders"] = 0; }  
  if(empty($posted["id_next_model"])){ $posted["id_next_model"] = 0; }  
  if(empty($posted["present_id"])){ $posted["present_id"] = 0; }  
  if(empty($posted["treba"])){ $posted["treba"] = 0; }          
  if(empty($posted["total_qty"])){ $posted["total_qty"] = 0; }   
  $posted["total_qty"] = intval($posted["total_qty"]);  
	  
  if(!empty($_POST["save"]) && $id > 0){ // update

    $ins_date = "";
    if(!empty($_POST["insert_date"])){
      $ins_date = ", date_insert = '".$_POST["insert_date"]["Year"]."-".$_POST["insert_date"]["Month"]."-".$_POST["insert_date"]["Day"]." ".$_POST["insert_date"]["Hour"].":".$_POST["insert_date"]["Minute"].":".$_POST["insert_date"]["Second"]."' ";
    }

    $db->query("UPDATE ".$db->tables["products"]." SET
            `name` = '".$db->escape($posted["name"])."',
            `name_short` = '".$db->escape($posted["name_short"])."',
            `memo` = '".$db->escape($memo)."',
            `memo_short` = '".$db->escape($posted["memo_short"])."',
            `price_period` = '".$db->escape($posted["price_period"])."',
            `price_buy` = '".$db->escape($posted["price_buy"])."',
            `price` = '".$db->escape($posted["price"])."',
            `total_qty` = '".$db->escape($posted["total_qty"])."',
            `price_opt` = '".$db->escape($posted["price_opt"])."',
            `price_spec` = '".$db->escape($posted["price_spec"])."',
            `comment` = '".$db->escape($posted["comment"])."',
            `active` = '".$db->escape($posted["active"])."',
            `accept_orders` = '".$db->escape($posted["accept_orders"])."',
            `weight_deliver` = '".$db->escape($posted["weight_deliver"])."',
            `complectation` = '".$db->escape($posted["complectation"])."',
            `barcode` = '".$db->escape($posted["barcode"])."',
            `id_next_model` = '".$db->escape($posted["id_next_model"])."',
            `bid_ya` = '".$db->escape($posted["bid_ya"])."',
            `alter_search` = '".$db->escape($posted["alter_search"])."',
            `f_new` = '".$db->escape($posted["f_new"])."',
            `f_spec` = '".$db->escape($posted["f_spec"])."',
            `present_id` = '".$db->escape($posted["present_id"])."',
            `currency` = '".$db->escape($posted["currency"])."',
            `date_update` = '".$ddate."',
            `treba` = '".$db->escape($posted["treba"])."',
            `meta_title` = '".$db->escape($posted["meta_title"])."',
            `meta_description` = '".$db->escape($posted["meta_description"])."',
            `meta_keywords` = '".$db->escape($posted["meta_keywords"])."',
            `alias` = '".$db->escape($posted["alias"])."',
            `price_period` = '".$db->escape($posted["price_period"])."',
            `multitpl` = '".$db->escape($posted["multitpl"])."',
            `user_id` = '".$admin_vars['bo_user']['id']."'
            $ins_date
          WHERE id = '".$id."'
          "); 
    if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }    

	register_changes('product', $id, 'update');

    // Обновляем характеристики
    update_options($id,'product');
    update_picture_title_records($id,$posted);
    $fotos = add_new_fotos($id, 'product');

    delete_group_pics($id,$posted);
    set_default_picture2($id,$posted);
  
    // Обновляем позиции рисунков
    update_picture_positions($id,$posted);
    update_categs_products($id,$posted);
    update_products2p($id, $posted);

    create_new_photos($id, $posted);
    update_files($id,$posted);
    upload_files($id);
	
	update_product_pubs($id);
  
	add_blocks($id, 'product');
	update_blocks($id, 'product');
	clear_cache(0);  
    $href = "?action=products&do=edit&id=".$id."&updated=1";
    header("Location: ".$href);
    exit;

  }elseif(!empty($_POST["save"]) && $id == 0){ // add
  
    $db->query("INSERT INTO ".$db->tables["products"]." (
            `name`, `name_short`, `memo`,
            `memo_short`, `price_buy`,
            `price`, `price_opt`,
            `price_spec`, `comment`,
            `active`, `accept_orders`, `weight_deliver`,
            `complectation`, `barcode`,
            `id_next_model`, `bid_ya`, `alter_search`,
            `f_new`, `f_spec`, `present_id`, `currency`,
            `date_insert`, `date_update`, `treba`,
            `meta_title`, `meta_description`,
            `meta_keywords`, `alias`, `views`, 
            `user_id`, `price_period`, `multitpl`, `total_qty`
            ) VALUES(
            '".$db->escape($posted["name"])."',
            '".$db->escape($posted["name_short"])."',
            '".$db->escape($memo)."',
            '".$db->escape($posted["memo_short"])."',
            '".$db->escape($posted["price_buy"])."',
            '".$db->escape($posted["price"])."',
            '".$db->escape($posted["price_opt"])."',
            '".$db->escape($posted["price_spec"])."',
            '".$db->escape($posted["comment"])."',
            '".$db->escape($posted["active"])."',
            '".$db->escape($posted["accept_orders"])."',
            '".$db->escape($posted["weight_deliver"])."',
            '".$db->escape($posted["complectation"])."',
            '".$db->escape($posted["barcode"])."',
            '".$db->escape($posted["id_next_model"])."',
            '".$db->escape($posted["bid_ya"])."',
            '".$db->escape($posted["alter_search"])."',
            '".$db->escape($posted["f_new"])."',
            '".$db->escape($posted["f_spec"])."',
            '".$db->escape($posted["present_id"])."',
            '".$db->escape($posted["currency"])."',
            '".$ddate."',
            '0000-00-00 00:00:00',
            '".$db->escape($posted["treba"])."',
            '".$db->escape($posted["meta_title"])."',
            '".$db->escape($posted["meta_description"])."',
            '".$db->escape($posted["meta_keywords"])."',
            '".$db->escape($posted["alias"])."', 
            '0',
            '".$admin_vars['bo_user']['id']."',
            '".$db->escape($posted["price_period"])."',
            '".$db->escape($posted["multitpl"])."', 
            '".$db->escape($posted["total_qty"])."'
            )");
    $id = $db->insert_id;
	register_changes('product', $id, 'add', $posted["name"]);
  
    update_options($id,'product');
    upload_files($id);
    $fotos = add_new_fotos($id, 'product');
    update_categs_products($id,$posted);
    update_products2p($id, $posted);
	
	update_product_pubs($id);
	add_blocks($id, 'product');
	clear_cache(0);  
    $href = "?action=products&do=edit&id=".$id."&added=1";
    header("Location: ".$href);
    exit;
    
  }elseif(!empty($_POST["del"]) && $id > 0){ // delete    
    delete_products($id);
    $href = "?action=products&do=list_products&deleted=1";
	clear_cache(0);
    header("Location: ".$href);
    exit;
  
  }else{ // unknown
    return error_not_found("The page not found");
  }
}

/* ok */
/* удалим привязку товара и раздела */
function delete_categs_products($id){
  global $db;
  $db->query("DELETE FROM ".$db->tables['pub_categs']." WHERE id_pub = '".$id."' AND `where_placed` = 'product' ");
  return;
} 

/* ok */
function update_categs_products($id,$posted)
{
  delete_categs_products($id);
  global $db;

  if(empty($posted["categs"])){ return;}
  //$categs = explode(',',$posted['id_razdelov']);
  if(!is_array($posted["categs"]) || empty($posted["categs"])){ return;}
  foreach($posted["categs"] as $v){
    $db->query("INSERT INTO ".$db->tables['pub_categs']." (`id_categ`, `id_pub`, `where_placed`)
            VALUES('".$v."', '".$id."', 'product') ");
  }
  return;
}


function update_product_options($id, $posted)
{
  global $db;
  $db->query("DELETE FROM ".$db->tables['option_values']." WHERE id_product = '".$id."' ");
  if(isset($posted['option'])){
    foreach($posted['option'] as $k => $v){
      $db->query("INSERT INTO ".$db->tables['option_values']."
        (id_option, id_product, value)
        VALUES ('".$k."', '".$id."', '".$v."') ");
    }
  }
  return;
}

/* ok */
/* удалим связку товаров друг с другом */
function delete_products2p($id){
  global $db;
  $db->query("DELETE FROM ".$db->tables['product_to_product']." 
      WHERE product1 = '".$id."' OR product2 = '".$id."' ");
  return;
}

/* ok */
/* обновим связь товаров друг с другом */
function update_products2p($id, $posted)
{
    global $db;
  delete_products2p($id);
  if(!empty($posted['id_produktov'])){
    $prods = explode(",",$posted['id_produktov']);
    if(!empty($prods)){
      foreach($prods as $v){
        if($v > 0){
          $query = "INSERT INTO ".$db->tables['product_to_product']." 
            (product1, product2) VALUES ('".$id."', '".$v."') ";
          $db->query($query);
        }
      }
    }
  }
  return;
}


/* ok */
function delete_products($id)
{
  global $db;
  $db->query("DELETE FROM ".$db->tables["products"]." WHERE id = '".$id."' ");
  $db->query("DELETE FROM ".$db->tables["pub_categs"]." WHERE id_pub = '".$id."' AND `where_placed` = 'product' ");
  delete_options($id, 'product');
  delete_uploaded_pics($id, 'product', false, true);
  delete_uploaded_files($id,'product');
  delete_comments($id, 'product');
  delete_products2p($id);
  
  delete_blocks($id, 'product');
  register_changes('product', $id, 'delete');
  
	/* удалим привязку к избранным */
	$query = "DELETE FROM ".$db->tables["fav"]." WHERE where_id = '".$id."' AND `where_placed` = 'product' ";
	$db->query($query);
	
	/* удалим записи в COUNTER */
	$query = "DELETE FROM ".$db->tables["counter"]." WHERE for_page = 'product' 
		AND id_page = '".$id."' ";
	$db->query($query);

	/* удалим связь с публикацией */
	$query = "DELETE FROM ".$db->tables["pub_to_product"]." 
		WHERE id_product = '".$id."' ";
	$db->query($query);

	
  return;
}

?>