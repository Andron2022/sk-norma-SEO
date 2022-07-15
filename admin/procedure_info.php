<?php

/**********************************
***********************************
**	
**	need to delete old functions
**	last updated 05.06.2018
**	+ editing pubs with options in list
**	+ updated pubs and categs connections to categs
**	without building new windows and new requests 
**	+ new debug links in categs and pubs
**	+ delete the same option values
**	+ added set_qty_products for categs
**	  
***********************************
**********************************/

if(!defined('SIMPLA_ADMIN')){ die(); }

  global $db, $admin_vars, $tpl, $lang;
  $mode = $admin_vars["uri"]["mode"];
  $do = $admin_vars["uri"]["do"];
  $id = $admin_vars["uri"]["id"];
  $page = $admin_vars["uri"]["page"];

  $str_content = "";
  if($do == "categories"){
    $str_content = list_categories($id, $page);
  }elseif($do == "edit_categ"){
    $str_content = edit_category($id);
  }elseif($do == "save_categ"){
    $str_content = save_category($id);
  }elseif($do == "list_publications"){
    $str_content = list_publications($page);
  }elseif($do == "edit_publication"){
    $str_content = edit_publication($id);
  }elseif($do == "copy"){
    $str_content = copy_page($id);
  }else{
   
	  $query = "SELECT c.id, c.id_parent, c.title, 
		c2.title as title2, c3.title as title3, 
		IFNULL(c2.sort,c.sort) as sort2, 
		IFNULL(c3.sort,IFNULL(c2.sort,c.sort)) as sort3, 
		(SELECT COUNT(*) FROM ".$db->tables['categs']." ch 
         WHERE ch.id_parent = c.id) as childs  
      FROM ".$db->tables["categs"]." c 
	  LEFT JOIN ".$db->tables['categs']." c2 ON (c.id_parent = c2.id) 
	  LEFT JOIN ".$db->tables['categs']." c3 ON (c2.id_parent = c3.id) 
	  WHERE (SELECT COUNT(*) FROM ".$db->tables['categs']." ch 
         WHERE ch.id_parent = c.id) > '0' 
		ORDER BY sort3, sort2, c.sort, c.title  
	"; 
	$topfilter = $db->get_results($query, ARRAY_A);
	//$db->debug();  
	  
    $last_pubs = $db->get_results("SELECT p.*, 
			(SELECT site_url 
					FROM ".$db->tables['site_info']." si2, 
					".$db->tables['site_publications']." sp2
					WHERE sp2.id_publications = p.id 
						AND sp2.id_site = si2.id 
					LIMIT 0, 1
			) as site_url 			
				
		FROM ".$db->tables['publications']." p 
		ORDER BY p.date_insert desc 
		LIMIT 0, 10", ARRAY_A);
		
    $qty_pubs = $db->get_var("SELECT count(*) FROM ".$db->tables['publications']." ");
    $qty_categs = $db->get_var("SELECT count(*) FROM ".$db->tables['categs']." ");

    $menutable = $db->tables['categs'];
    $menuid = 0;
    $parentid = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $categs_tree = getmenu($menutable, $menuid,0,$parentid);
	
	$categ_info = array();
	if(!empty($_GET['id'])){
		$cid = intval($_GET['id']);
		$categ_info = $db->get_row("SELECT * FROM ".$db->tables['categs']." 
			WHERE id = '".$cid."' ", ARRAY_A);
	}	
	
    $tpl->assign("categ_info", $categ_info);
    $tpl->assign("qty_pubs", $qty_pubs);
    $tpl->assign("qty_categs", $qty_categs);
    $tpl->assign("filter", $topfilter);
    $tpl->assign("categs_tree", $categs_tree);
    $tpl->assign("last_pubs", $last_pubs);
    //return $tpl->fetch("categs_tree.html");  
    $str_content = $tpl->display("info/index.html");
  }

/* ok */
function list_categories($id, $page)
{
	global $db, $tpl, $lang;
	$query = "SELECT c.id, c.id_parent, c.title, 
		c2.title as title2, c3.title as title3, 
		IFNULL(c2.sort,c.sort) as sort2, 
		IFNULL(c3.sort,IFNULL(c2.sort,c.sort)) as sort3, 
		(SELECT COUNT(*) FROM ".$db->tables['categs']." ch 
         WHERE ch.id_parent = c.id) as childs  
      FROM ".$db->tables["categs"]." c 
	  LEFT JOIN ".$db->tables['categs']." c2 ON (c.id_parent = c2.id) 
	  LEFT JOIN ".$db->tables['categs']." c3 ON (c2.id_parent = c3.id) 
	  WHERE (SELECT COUNT(*) FROM ".$db->tables['categs']." ch 
         WHERE ch.id_parent = c.id) > '0' 
		ORDER BY sort3, sort2, c.sort, c.title  
	"; 
	$parent_categs = $db->get_results($query, ARRAY_A);
	$tpl->assign("parent_categs", $parent_categs);
	$tpl->assign("id", $id);

  if(isset($_POST["update"]))
  {
  	$active_ar = get_param("active",array());
  	$name_ar = get_param("name",array());
  	$alias_ar = get_param("alias",array());
  	$sort_ar = get_param("sort",array());
  	foreach($name_ar as $k => $v)
  	{
  		$active = (in_array($k,$active_ar)) ? 1 : 0;
  		$name = $name_ar[$k];
  		$alias = $alias_ar[$k];
  		if($alias == '') $alias = build_slug($name,'categ',$k);
  		$sort = $sort_ar[$k];
  		$db->query("UPDATE ".$db->tables["categs"]." 
          SET `title` = '".$db->escape($name)."', alias='".$db->escape($alias)."', 
          active = '$active', `sort`='$sort' WHERE id='$k'");                    
  	}

    $str_id = isset($_GET['id']) ? "&id=".intval($_GET['id']) : "";
    
	if(isset($_POST["delcategs"]))
	{
		$del_ar = get_param("delcategs",array());
		if(count($del_ar) > 0){
			foreach($del_ar as $k=>$v){
				delete_categ_info($v);
				delete_extra_options($v, 'categ');
			}
		}
		clear_cache(0);
		header("Location: ?action=info&do=categories".$str_id."&deleted=".count($del_ar));
		exit;
	}
    
	clear_cache(0);
    header("Location: ?action=info&do=categories".$str_id."&updated=".count($name_ar));
    exit;
  }

  $page_start = ONPAGE*$page;
  $page_stop = ONPAGE;

  $where = "";
  $wheres = array();
  if(isset($_GET["shop"])) $wheres[] = " c.`shop` = '".intval($_GET["shop"])."' ";
  if(isset($_GET["id"])) $wheres[] = " c.`id_parent` = '".$id."' ";
  if(count($wheres) > 0) $where = " WHERE ".implode(" AND ",$wheres);

  $query = "SELECT c.*, 
		IFNULL(c2.sort,c.sort) as sort2, 
		IFNULL(c3.sort,IFNULL(c2.sort,c.sort)) as sort3, 
        (SELECT COUNT(*) FROM ".$db->tables['pub_categs']."  p 
         WHERE p.id_categ = c.id AND p.id_pub >  '0' AND p.where_placed = 'pub') as pubs,
        (SELECT COUNT(*) FROM ".$db->tables['pub_categs']."  p 
         WHERE p.id_categ = c.id AND p.id_pub >  '0' AND p.`where_placed` = 'product') as products, 
		(SELECT COUNT(*) FROM ".$db->tables['categs']." ch 
         WHERE ch.id_parent = c.id) as childs, 
		 
		IF((SELECT count(*) 
			FROM ".$db->tables['site_info']." si2, 
			".$db->tables['site_categ']." sc2
			WHERE sc2.id_categ = c.id 
				AND sc2.id_site = si2.id) > 1, 
			'',
			(SELECT si2.site_url  
				FROM ".$db->tables['site_info']." si2, 
				".$db->tables['site_categ']." sc2
				WHERE sc2.id_categ = c.id 
					AND sc2.id_site = si2.id 
			)
		) as site_url,
		
		IF((SELECT count(*) 
			FROM ".$db->tables['site_info']." si2, 
			".$db->tables['site_categ']." sc2
			WHERE sc2.id_categ = c.id 
				AND sc2.id_site = si2.id) > 1, 
			'',
			(SELECT si2.default_id_categ  
				FROM ".$db->tables['site_info']." si2, 
				".$db->tables['site_categ']." sc2
				WHERE sc2.id_categ = c.id 
					AND sc2.id_site = si2.id 
			)
		) as default_id_categ,
		
		IF((SELECT count(*) 
			FROM ".$db->tables['site_info']." si2, 
			".$db->tables['site_categ']." sc2
			WHERE sc2.id_categ = c.id 
				AND sc2.id_site = si2.id) > 1, 
			'',
			(SELECT si2.id  
				FROM ".$db->tables['site_info']." si2, 
				".$db->tables['site_categ']." sc2
				WHERE sc2.id_categ = c.id 
					AND sc2.id_site = si2.id 
			)
		) as site_id,
		
		IF((SELECT count(*) 
			FROM ".$db->tables['site_info']." si2, 
			".$db->tables['site_categ']." sc2
			WHERE sc2.id_categ = c.id 
				AND sc2.id_site = si2.id) > 1, 
			'',
			(SELECT si2.site_active  
				FROM ".$db->tables['site_info']." si2, 
				".$db->tables['site_categ']." sc2
				WHERE sc2.id_categ = c.id 
					AND sc2.id_site = si2.id 
			)
		) as site_active,
		
		(SELECT count(*) 
			FROM ".$db->tables['site_info']." si2, 
			".$db->tables['site_categ']." sc2
			WHERE sc2.id_categ = c.id 
				AND sc2.id_site = si2.id
		) as site_url_qty
		
      FROM ".$db->tables["categs"]." c 
	  LEFT JOIN ".$db->tables['categs']." c2 ON (c.id_parent = c2.id) 
	  LEFT JOIN ".$db->tables['categs']." c3 ON (c2.id_parent = c3.id) 
	"; 
	//s.site_url, s.id as site_id, s.default_id_categ
	$query .= " $where 
      ORDER BY sort3, sort2, c.sort, c.title "; 
  $results = $db->get_results($query, ARRAY_A);
  //$db->debug(); exit;
  $all_results = $db->num_rows;
  $results = $db->get_results($query." LIMIT $page_start, $page_stop ", ARRAY_A);
  if($db->num_rows == 0){
    if($id > 0){
      //$tpl->assign("categ_name", categ_name_by_id4info($id));
      $categ_info = $db->get_row("SELECT c.*, par_c.title as parent_title,
               (SELECT COUNT(*) FROM ".$db->tables['pub_categs']."  p 
                WHERE p.id_categ = c.id AND p.id_pub >  '0') as pubs
      FROM ".$db->tables["categs"]." c 
      LEFT JOIN ".$db->tables["categs"]." par_c on (par_c.id = c.id_parent) 
      WHERE c.id = '".$id."' ", ARRAY_A);
      $tpl->assign("categ_info", $categ_info);
    }
  }

  
	$categ_info = array();
	if(!empty($_GET['id'])){
		$cid = intval($_GET['id']);
		$categ_info = $db->get_row("SELECT * FROM ".$db->tables['categs']." 
			WHERE id = '".$cid."' ", ARRAY_A);
	}	
	
    $tpl->assign("categ_info", $categ_info);  
  
  $tpl->assign("categs_list", $results);
  $tpl->assign("site_url",SITE_URL);
  $tpl->assign("pages",_pages($all_results, $page, ONPAGE,true));
  return $tpl->display("info/list_categories.html");
}


/* ok, modified 30.12.2017 */
/* added blocks 
modified connected categs window 
*/
function edit_category($id)
{
  global $db, $tpl, $admin_vars, $site_vars, $site;
  $categ_info = $db->get_row("SELECT c.*, 
        b.name as user_name, b.login as user_login,
        c2.title as parent_title, 
        c3.title as parent_title2,  
        c3.id as parent_id2, 
        (SELECT COUNT(DISTINCT id_in_record) 
          FROM ".$db->tables["uploaded_pics"]." 
          WHERE `record_id` = '".$id."' AND record_type = 'categ' ) as fotos_qty,
        (SELECT count(*) 
          FROM ".$db->tables["uploaded_files"]." 
          WHERE `record_id` = '".$id."' AND record_type = 'categ') as files_qty,
		  
		(SELECT count(*) 
          FROM ".$db->tables["pub_categs"]." 
          WHERE `id_categ` = '".$id."' AND where_placed = 'pub') as qty_pubs,
		(SELECT count(*) 
          FROM ".$db->tables["pub_categs"]." 
          WHERE `id_categ` = '".$id."' AND where_placed = 'product') as qty_products, 
		(SELECT id2 FROM ".$db->tables["connections"]." 
			WHERE id1 = c.id AND name1 = 'categ' AND name2 = 'qty_products'
		) as set_qty_products
    FROM ".$db->tables['categs']." c
    LEFT JOIN ".$db->tables['users']." b on (b.id = c.user_id)  
    LEFT JOIN ".$db->tables['categs']." c2 on (c2.id = c.id_parent)  
    LEFT JOIN ".$db->tables['categs']." c3 on (c3.id = c2.id_parent)
    WHERE c.id = '".$id."' ", ARRAY_A);
     
  if($id > 0 && $db->num_rows == 0){ return error_not_found();  }
  if($db->num_rows == 0){
	  
    $prefix = isset($site_vars['sys_prefix_categ']) ? $site_vars['sys_prefix_categ'] : "page-".time();
	
    $categ_info = array(
      'id' => 0,
      'active' => 1,
      'title' => '',
      'id_parent' => 0,
      'meta_description' => '',
      'meta_keywords' => '',
      'meta_title' => '',
      'alias' => $prefix,
      'memo' => '',
      'sort' => 99,
      'sortby' => '',
      'date_insert' => date('Y-m-d H:i:s'),
      'date_update' => '0',
      'user_id' => $admin_vars['bo_user']['id'],
      'multitpl' => 'category.html',
      'icon' => '',
      'f_spec' => 0,
	  'in_last' => 0,
      'shop' => 0,
      'show_filter' => 0,
      'memo_short' => '',
      'parent_title' => 'Главная',
      'fotos_qty'=> 0,
      'files_qty' => 0,
      'comments_qty' => 0,
    );
  }
  
	if(!isset($categ_info['set_qty_products']) 
		|| strlen($categ_info['set_qty_products']) == 0
	){
		$categ_info['set_qty_products'] = $site_vars['onpage'];
	}
  
	$categ_info['date_insert'] = date($site->vars['site_date_format'].' '.$site->vars['site_time_format'], strtotime($categ_info['date_insert']));
	
	if(!empty($categ_info['date_update'])){
		$categ_info['date_update'] = date($site->vars['site_date_format'].' '.$site->vars['site_time_format'], strtotime($categ_info['date_update']));		
	}
  
  
	$categ_info['comments_qty'] = $db->get_var("SELECT count(*) 
		FROM ".$db->tables['comments']." 
		WHERE record_type = 'categ' AND record_id = '".$id."'
		");

	
  $categ_info['all_files_qty'] = $db->get_var("SELECT count(*) FROM ".$db->tables['uploaded_files']."  ");

  if(!empty($site_vars['sys_tpl_pages'])){
    $categ_info['tpls'] = get_array_vars('sys_tpl_pages');
    if($id == 0){
      $categ_info['multitpl'] = $db->get_var("SELECT `value` 
        FROM ".$db->tables['site_vars']." 
        WHERE `name` = 'sys_tpl_pages' ");  
    }  
  }
  
  $ar_fotos = array();
  if($categ_info['fotos_qty'] > 0){
    $ar_fotos = $db->get_results("SELECT * 
      FROM ".$db->tables["uploaded_pics"]." 
      WHERE `record_id` = '".$id."' AND record_type = 'categ'       
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
  }

  $ar_files = array();
  if($categ_info['files_qty'] > 0){
    $ar_files = $db->get_results("SELECT * 
      FROM ".$db->tables["uploaded_files"]." 
      WHERE `record_id` = '".$id."' AND record_type = 'categ'       
      ORDER BY id_in_record, title, id ", ARRAY_A); 
  }
  $categ_info['uploaded_files'] = $ar_files;

  // Options -> параметры
  $rows = $db->get_results("SELECT o.*, o.where_placed as `where`, sco.id_option as selected 
      FROM ".$db->tables['option_groups']." o 
      LEFT JOIN ".$db->tables['categ_options']." sco on (sco.id_categ = '".$id."' 
        AND sco.id_option = o.id AND sco.where_placed = 'categ') 
      ORDER BY o.`sort`, o.`title` ", ARRAY_A);
  $categ_info["option_groups"] = $rows;
  
  $categ_info["option_groups_selected"] = array();
  if(!empty($categ_info["option_groups"])){
	  foreach($categ_info["option_groups"] as $v){
		  if(!empty($v['selected'])){
			  $categ_info["option_groups_selected"][] = $v['id'];
		  }
	  }
  }
  
  if(!empty($categ_info["option_groups"])){
	foreach($categ_info["option_groups"] as $k=>$r){
		$opts = $db->get_results("
			SELECT * 
			FROM ".$db->tables['options']." 
			WHERE group_id = '".$r['id']."'
			ORDER BY `sort`, `title`
		", ARRAY_A);
		$categ_info["option_groups"][$k]['options'] = $opts;
		
		if(!empty($opts)){
			foreach($opts as $k => $v){
				if(!empty($v['show_in_list']) 
					&& in_array($v['group_id'], $categ_info["option_groups_selected"])
				){
					if(!isset($categ_info["option_groups_list"])){
						$categ_info["option_groups_list"] = array();
					}
					
					$categ_info["option_groups_list"][] = $v;
				}
				
				if(!empty($v['show_in_filter']) 
					&& in_array($v['group_id'], $categ_info["option_groups_selected"])
				){
					if(!isset($categ_info["option_groups_filter"])){
						$categ_info["option_groups_filter"] = array();
					}
					
					$categ_info["option_groups_filter"][] = $v;
				}
				
			}
		}			
	}

	if(!empty($categ_info["option_groups_filter"])){
		function cmp_f($a, $b)
		{
			if ($a["sort"] == $b["sort"]){ return 0; } 
			return ($a["sort"] < $b["sort"]) ? -1 : 1; 
		}

		usort($categ_info["option_groups_filter"], "cmp_f");
	}

	if(!empty($categ_info["option_groups_list"])){
		function cmp_l($a, $b)
		{
			if ($a["sort"] == $b["sort"]){ return 0; } 
			return ($a["sort"] < $b["sort"]) ? -1 : 1; 
		}

		usort($categ_info["option_groups_list"], "cmp_l");
	}

	
	
  }
  
  $id_parent = $categ_info["id_parent"];
  $categ_info["parent_categs"] = '';
  $tpl->assign("categ",$categ_info);
  $tpl->assign("record_type","categ");
  $tpl->assign("blocks",get_blocks_info($id,'categ'));
  $tpl->assign("extra_options",extra_options($id, 'categ'));
  if(isset($_POST["save"]) || isset($_POST["del"])){
    return save_category($id);
  }

  return $tpl->display("info/edit_categ.html");
}

/* ok, function to save category info */
function save_category($id)
{
  global $tpl;
  if(isset($_POST['save']) && $id == 0){ // добавляем
    return add_new_categ();  
  }elseif(isset($_POST['save']) && $id > 0){ // обновляем
    return update_categ_info($id);
  }elseif(isset($_POST["del"]) && $id > 0){ // удаляем
    return delete_categ_info($id, true);
  }else{
    $tpl->display("empty.html"); 
    exit;
  }
}

/* ok */
function add_new_categ()
{
  global $db, $admin_vars, $site_vars;
  $posted = $_POST;
  $posted["title"] = trim($posted["title"]);
  if(empty($posted["alias"])){
    $posted["alias"] = stripslashes($posted["title"]);
  }
  $posted["alias"] = build_slug($posted["alias"], "categ", 0);

  if(empty($posted["meta_title"])){
    $posted["meta_title"] = $posted["title"];
  }
  if(empty($posted["meta_description"])){
    $posted["meta_description"] = $posted["title"];
  }
  if(empty($posted["meta_keywords"])){
    $posted["meta_keywords"] = $posted["title"];
  }

  if(empty($posted["multitpl"])){
    $posted["multitpl"] = 'category.html';
  }

  if(!isset($posted["icon"])){
    $posted["icon"] = '';
  }
  
  $posted['active'] = isset($posted['active']) ? intval($posted['active']) : 0;
  $posted["f_spec"] = isset($posted["f_spec"]) ? intval($posted["f_spec"]) : 0; 
  $posted["in_last"] = isset($posted["in_last"]) ? intval($posted["in_last"]) : 0; 

  $posted["shop"] = isset($posted["shop"]) ? intval($posted["shop"]) : 0; 
  $posted["show_filter"] = isset($posted["show_filter"]) ? intval($posted["show_filter"]) : 0; 
  $posted["memo_short"] = isset($posted["memo_short"]) ? trim($posted["memo_short"]) : ""; 

  $memo = $posted["memo"];
  if(isset($_GET['noeditor'])) { $memo = nl2br($memo); }
  $date_update = NULL;
  $date_insert = $_POST["insert_date"]["Year"]."-".$_POST["insert_date"]["Month"]."-".$_POST["insert_date"]["Day"]." 00:00:01"; 

  $user_id = $admin_vars['bo_user']['id'];

  $set_qty_products = isset($posted["set_qty_products"]) ? $posted["set_qty_products"] : $site_vars['onpage'];
  if(strlen($set_qty_products) == 0){ $set_qty_products = $site_vars['onpage']; }
  
  $query = "INSERT INTO ".$db->tables["categs"]." (`title`, `id_parent`,
            `meta_description`, `meta_keywords`, `meta_title`, `alias`,
            `memo`, `active`, `sort`, date_insert, date_update, user_id, 
            multitpl, `icon`, `f_spec`, `shop`, `show_filter`, `memo_short`, `in_last`)
            VALUES(
            '".$db->escape($posted["title"])."',
            '".$db->escape($posted["id_parental"])."',
            '".$db->escape($posted["meta_description"])."',
            '".$db->escape($posted["meta_keywords"])."',
            '".$db->escape($posted["meta_title"])."',
            '".$db->escape($posted["alias"])."',
            '".$db->escape($memo)."',
            '".$db->escape($posted["active"])."',
            '".$db->escape($posted["sort"])."',
            '".$date_insert."',
            NULL,
            '".$admin_vars['bo_user']['id']."',
            '".$db->escape($posted["multitpl"])."',
            '".$db->escape($posted["icon"])."',
            '".$db->escape($posted["f_spec"])."',            
            '".$db->escape($posted["shop"])."',            
            '".$db->escape($posted["show_filter"])."',            
            '".$db->escape($posted["memo_short"])."',
			'".$db->escape($posted["in_last"])."'
            )";
			
  $db->query($query);
  $id = $db->insert_id;
  register_changes('categ', $id, 'add', $posted["title"]);
  
  /* добавим привязку к сайтам */
  if(!empty($posted["site_categ"])){
    add_site_categ($id,$posted["site_categ"]);
  }

  /* добавим новые фото */
  $fotos = add_new_fotos($id, 'categ');

  /* загрузим файлы */
  upload_files($id);
  
  /* привязка к группам характеристик */
  update_opt_groups($id, 'categ');
  
  /* add qty products */
  add_qty_products($id,$set_qty_products,'categ');  
  add_blocks($id, 'categ');  
  add_extra_options($id, 'categ');
  clear_cache(0);
  header("Location: ?action=info&do=edit_categ&id=".$id."&added=1");
  return;
}


/* ok */
function add_qty_products($id, $set_qty_products, $where_set){
	global $db;
	$sql = "DELETE FROM ".$db->tables['connections']." 
		WHERE id1 = '".$id."' 
			AND name1 = '".$where_set."' 
			AND name2 = 'qty_products' 
	";
	$db->query($sql);
	
	$sql = "INSERT INTO ".$db->tables['connections']." 
		(id1, name1, id2, name2) 
		VALUES ('".$id."', '".$db->escape($where_set)."', 
		'".intval($set_qty_products)."', 'qty_products') 	
	";
	$db->query($sql);
	return;
}

/* ok */
function update_categ_info($id)
{
  global $db, $admin_vars, $site_vars;
  $posted = $_POST;
  $posted["title"] = trim($posted["title"]);
  if(empty($posted["alias"])){
    $posted["alias"] = stripslashes($posted["title"]);
  }
  $posted["alias"] = build_slug($posted["alias"], "categ", $id);

  if(empty($posted["multitpl"])){
    $posted["multitpl"] = 'category.html';
  }

  if(!isset($posted["icon"])){
    $posted["icon"] = '';
  }
  
  if(!empty($posted["icon_custom"])){
    $posted["icon"] = trim($posted["icon_custom"]);
  }


  $posted["site_categ"] = isset($posted["site_categ"]) ? $posted["site_categ"] : array();
  $posted['active'] = isset($posted['active']) ? intval($posted['active']) : 0;
  $posted["f_spec"] = isset($posted["f_spec"]) ? intval($posted["f_spec"]) : 0; 
  $posted["in_last"] = isset($posted["in_last"]) ? intval($posted["in_last"]) : 0; 

  $posted["shop"] = isset($posted["shop"]) ? intval($posted["shop"]) : 0; 
  $posted["show_filter"] = isset($posted["show_filter"]) ? intval($posted["show_filter"]) : 0; 
  $posted["memo_short"] = isset($posted["memo_short"]) ? trim($posted["memo_short"]) : ""; 

  $set_qty_products = isset($posted["set_qty_products"]) ? $posted["set_qty_products"] : $site_vars['onpage'];
  if(strlen($set_qty_products) == 0){ $set_qty_products = $site_vars['onpage']; }
  
  $memo = $posted["memo"];
  if(isset($_GET['noeditor'])) { $memo = nl2br($memo); }
  $date_update = date("Y-m-d H:i:s");
  $date_insert = $_POST["insert_date"]["Year"]."-".$_POST["insert_date"]["Month"]."-".$_POST["insert_date"]["Day"]." 00:00:01"; 
  
  $query = "UPDATE ".$db->tables["categs"]." SET
            `title` = '".$db->escape($posted["title"])."',
            `id_parent` = '".$db->escape($posted["id_parental"])."',
            `meta_description` = '".$db->escape($posted["meta_description"])."',
            `meta_keywords` = '".$db->escape($posted["meta_keywords"])."',
            `meta_title` = '".$db->escape($posted["meta_title"])."',
            `alias` = '".$db->escape($posted["alias"])."',
            `memo` = '".$db->escape($memo)."',
            `active` = '".$db->escape($posted["active"])."',
            `sort` = '".$db->escape($posted["sort"])."',
            `sortby` = '".$db->escape($posted["sortby"])."',
            `date_update` = '".$date_update."',
            `user_id` = '".$admin_vars['bo_user']['id']."',
            `multitpl` = '".$db->escape($posted["multitpl"])."',
            `icon` = '".$db->escape($posted["icon"])."', 
            `date_insert` = '".$date_insert."',
            `f_spec` = '".$db->escape($posted["f_spec"])."',
            `in_last` = '".$db->escape($posted["in_last"])."',
            `shop` = '".$db->escape($posted["shop"])."',
            `show_filter` = '".$db->escape($posted["show_filter"])."',
            `memo_short` = '".$db->escape($posted["memo_short"])."'

          WHERE id = '".$id."' ";
  $db->query($query);
  register_changes('categ', $id, 'update');

  /* обновим привязку к сайтам */
  delete_site_categ($id);
  add_site_categ($id,$posted["site_categ"]);

  /* обновим заголовки ранее загруженных картинок */
  update_picture_title_records($id,$posted);
  
  /* добавим новые фото */
  $fotos = add_new_fotos($id, 'categ');

  /* удалим отмеченные фото */
  delete_group_pics($id,$posted);
  
  /* установим фото по умолчанию */
  set_default_picture2($id,$posted);

  /* Обновляем позиции рисунков */
  update_picture_positions($id,$posted);

  /* создадим новые фото из больших */
  create_new_photos($id, $posted);
  
  /* обновим загруженные файлы */
  update_files($id,$posted);
  
  /* загрузим файлы */
  upload_files($id);
  
  /* update qty_products*/
  add_qty_products($id, $set_qty_products, 'categ');

  /* привязка к группам характеристик */
  update_opt_groups($id, 'categ');
  
  add_blocks($id, 'categ');
  update_blocks($id, 'categ');
  update_extra_options($id, 'categ');
  add_extra_options($id, 'categ');
  clear_cache(0);
  header("Location: ?action=info&do=edit_categ&id=".$id."&updated=1");
  return;
}

// ok, newversion correct
function add_site_pubs($id,$ar)
{
  global $db;
  if(empty($ar)){ return; }
  foreach($ar as $k => $v){
    $query = "INSERT INTO ".$db->tables["site_publications"]."  (`id_site`,
              `id_publications`) VALUES('".$v."', '".$id."') ";
    $db->query($query);
  }
  return;
}

// ok, newversion
function add_pub_categs($id,$ar)
{
	global $db;
	if(empty($ar)){ return; }
	//$ar = explode(",",$ar);
	//if(!is_array($ar)){ return; }
	
	foreach($ar as $k => $v){
		if(!empty($v)){
			$query = "INSERT INTO ".$db->tables["pub_categs"]." 
				(`id_pub`, `id_categ`)
				VALUES('".$id."', '".$v."') ";
			$db->query($query);
		}
	}
	return;
}

/* ok */
function add_site_categ($id,$posted_site_categ)
{
  global $db;
  if(!is_array($posted_site_categ)){ return; }
  foreach($posted_site_categ as $k => $v){
    $query = "INSERT INTO ".$db->tables["site_categ"]." (`id_site`, `id_categ`)
              VALUES('".$v."', '".$id."') ";
    $db->query($query);
  }
  return;
}

/* ok */
function delete_site_categ($id)
{
  global $db;
  $query = "DELETE FROM ".$db->tables["site_categ"]." WHERE id_categ = '".$id."' ";
  $db->query($query);
  return;
}

/* ok */
function delete_categ_info($id, $redirect=false)
{
  global $db;

	// Проверим не является ли страница главной для сайта
	$query = "SELECT * FROM ".$db->tables["site_info"]." WHERE default_id_categ = '".$id."' ";
	$rows = $db->get_results($query);
	if($rows && $db->num_rows > 0){
		return error_not_found("<a href='?action=info&do=edit_categ&id=".$id."'>The page</a> can't be deleted - it has connected a site");  
	}
		
	// Проверим нет ли у страницы дочерних страниц
	$query = "SELECT * FROM ".$db->tables["categs"]." WHERE id_parent = '".$id."' ";
	$rows = $db->get_results($query);
	if($rows && $db->num_rows > 0){
		return error_not_found("<a href='?action=info&do=edit_categ&id=".$id."'>The page</a> can't be deleted - it has child pages");  
	}
	
  delete_site_categ($id);
  delete_uploaded_pics($id, 'categ', false, true);
  delete_uploaded_files($id,'categ');
  $query = "DELETE FROM ".$db->tables["categs"]." WHERE id = '".$id."' ";
  $db->query($query);
  
  /* удалим ссылку на страницу у связки с публикациями */
  $query = "DELETE FROM ".$db->tables["pub_categs"]." WHERE id_categ = '".$id."' ";
  $db->query($query);

  /* удалим привязку к группам характеристик */
  $query = "DELETE FROM ".$db->tables["categ_options"]." WHERE id_categ = '".$id."' AND `where_placed` = 'categ' ";
  $db->query($query);

  /* удалим привязку к избранным */
  $query = "DELETE FROM ".$db->tables["fav"]." WHERE where_id = '".$id."' AND `where_placed` = 'categ' ";
  $db->query($query);
  
  register_changes('categ', $id, 'delete');
  
  delete_comments($id, 'categ');
  
  delete_blocks($id, 'categ');
  delete_extra_options($id, 'categ');
  if($redirect){
	clear_cache(0);
    header("Location: ?action=info&do=categories&deleted=1");
    exit;
  }
  return;
  
}


/* ok 18.11.2016 */
function list_pubs_opts($cid, $page)
{
	global $db, $tpl, $lang, $site_vars;
	$page_start = ONPAGE*$page;
	$page_stop = ONPAGE;	
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
							'pub', 
							'".$db->escape(trim($v2['value2']))."', 
							'".$db->escape(trim($v2['value3']))."') ";
						$db->query($sql);
						
					}
				}
				$i++;				
			}			
		}		
		
		if(isset($_POST['pubs_qty'])){ $i = $_POST['pubs_qty']; }
		$url = "?action=info&do=list_publications&cid=".$cid."&options=1&updated=".$i;	
		clear_cache(0);
		header("Location: ".$url);
		exit;		
	}
	
	// вычислим поле для сортировки
	$to_sort = $db->get_var("SELECT `sortby` FROM ".$db->tables['categs']." WHERE id = '".$cid."' ");
	//$db->debug(); exit;
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
			AND (g.`where_placed` = 'pub' OR g.`where_placed` = 'all')
		";
		$sql .= " ORDER BY g.sort, g.title, o.sort, o.title ";
		$rows = $db->get_results($sql, ARRAY_A);
		$tpl->assign("th_options",$rows);
		//$db->debug(); exit;
				
		$sql = "SELECT p.*, 
              (SELECT COUNT(DISTINCT `id_in_record`) 
				FROM ".$db->tables['uploaded_pics']."  u 
                WHERE u.record_id = p.id 
                AND u.record_type = 'pub') as fotos,
				
				(SELECT si2.site_url 
					FROM ".$db->tables['site_info']." si2, 
					".$db->tables['site_publications']." sp2
					WHERE sp2.id_publications = p.id 
						AND sp2.id_site = si2.id 
					LIMIT 0, 1
				) as site_url, 
				
				(SELECT si2.id 
					FROM ".$db->tables['site_info']." si2, 
					".$db->tables['site_publications']." sp2
					WHERE sp2.id_publications = p.id 
						AND sp2.id_site = si2.id 
					LIMIT 0, 1
				) as site_id
				
            FROM ".$db->tables['publications']." as p, ".$db->tables['pub_categs']." as cat
            WHERE cat.id_categ = '".$cid."' AND
            cat.id_pub = p.id AND 
			cat.where_placed = 'pub' 
            ORDER BY ".$to_sort." ";
		$results = $db->get_results($sql, ARRAY_A);
		//$db->debug(); exit;
		
		$all_results = $db->num_rows;		
		$results = $db->get_results($sql." LIMIT $page_start, $page_stop ", ARRAY_A);
		$tpl->assign("pubs_list",$results);
		//$db->debug(); exit;
		
		$u_options = array();
		foreach($results as $k => $v){
			$u_options[$v['id']] = get_options_in_list($v['id'], 'pub');
			
		}
		
		// All pubs
		$tpl->assign("all_pubs_qty",$all_results);
		$tpl->assign("u_options",$u_options);
		
		// All catalogs
		$catalogs = $db->get_results("SELECT c.id, c.title, 
			c.alias, c.id_parent, c.sort, 
			c2.title as parent_title, 
			c3.title as parent2_title, 
			IFNULL(c2.sort,c.sort) as sort2, 
			IFNULL(c3.sort,IFNULL(c2.sort,c.sort)) as sort3 
			
			FROM ".$db->tables['pub_categs']." p 
			LEFT JOIN ".$db->tables['categs']." c on (c.id = p.id_categ) 
			LEFT JOIN ".$db->tables['categs']." c2 on (c2.id = c.id_parent) 
			LEFT JOIN ".$db->tables['categs']." c3 on (c3.id = c2.id_parent) 
			WHERE p.`where_placed` = 'pub' 
			GROUP BY p.id_categ 
			ORDER BY sort3, sort2, c.sort, c.title 
		", ARRAY_A);
		//$db->debug(); exit;
		$tpl->assign("categs",$catalogs);
		
		
		$cid = intval($_GET["cid"]);
		$cid_ar = $db->get_row("SELECT 
				c.id, c.`sortby`, c.`title`, c.alias, c.active, s.site_url
			FROM ".$db->tables['categs']." c 
			LEFT JOIN ".$db->tables['site_categ']." sc ON (c.id = sc.id_categ) 
			LEFT JOIN ".$db->tables['site_info']." s ON (sc.id_site = s.id) 
			WHERE c.id = '".$cid."' ", ARRAY_A);
			
		if(!empty($cid_ar) && !empty($cid_ar['sortby'])){
			$sortby = "pubs.".$cid_ar['sortby'];
			if(!empty($cid_ar['site_url'])){
				$cid_ar['url'] = $cid_ar['site_url'].'/'.$cid_ar['alias'].URL_END;
				if(empty($cid_ar['active'])){
					$cid_ar['url'] .= '?debug';
				}
			}
		}else{
			$sortby = "pubs.`date_insert` desc";
		}
		$tpl->assign('cid',$cid_ar);
		
		
		
		$tpl->assign("pages", _pages($all_results, $page, ONPAGE));
		$str = $tpl->display("info/list_publications.html");
		return $str;
  
}


/* ok */
function list_publications($page)
{
  global $db, $tpl, $lang, $site_vars;

	if(!empty($_GET['options']) && !empty($_GET["cid"])){
		$cid = intval($_GET["cid"]);
		return list_pubs_opts($cid, $page);
	}
  
  

  if(isset($_POST["update"]))
  {
  	$names = get_param("name",array());
  	$aliases = get_param("alias",array());
  	$active_ar = get_param("active",array());
  	foreach($names as $k => $v)
  	{
  		$active = (in_array($k,$active_ar)) ? 1 : 0;
  		$alias = $aliases[$k];
  		if($alias == '') $alias = build_slug($names[$k],'pub',$k);

  		$db->query("UPDATE ".$db->tables["publications"]." 
                		SET `name` = '".$db->escape($names[$k])."', 
                    alias = '".$db->escape($alias)."',
                		active = '$active'
                		where id = '$k' ");
  	}
    $get_plus = isset($_GET['cid']) ? "&cid=".intval($_GET['cid']) : "";
    $get_plus .= isset($_GET['active']) ? "&active=".intval($_GET['active']) : "";
    $get_plus .= isset($_GET['q']) ? "&q=".htmlspecialchars($_GET['q']) : "";
    
    if(isset($_POST["del"]))
    {
    	$del = get_param("del",array());
    	if(count($del) > 0)
    	{
        foreach($del as $k=>$v){
          delete_publication($v);
        }
    	}
		clear_cache(0);
      header("Location: ?action=info&do=list_publications".$get_plus."&deleted=".count($del));
      exit;
    }   
    
    clear_cache(0);
	header("Location: ?action=info&do=list_publications".$get_plus."&updated=".count($names));
    exit;
  }

  $wheres = array();
  $page_start = ONPAGE*$page;
  $page_stop = ONPAGE;
  $rows = $a = "";
  if(isset($_GET["active"])){ 
    $wheres[] = "`active` = '".intval($_GET["active"])."'"; 
  }

  if(isset($_GET["q"])){ // filter by product name
    $q = $db->escape($_GET["q"]);
    $q = str_replace(array($site_vars['site_url'],"/"), "", $q);
    $wheres[] = " (pubs.`name` LIKE '%".$q."%' OR pubs.`alias` LIKE '%".$q."%') ";
  }

  if(isset($_GET["cid"]) && intval($_GET["cid"]) > 0){ // filter by category
    $cid = intval($_GET["cid"]);
	$cid_ar = $db->get_row("SELECT 
				c.id, c.`sortby`, c.`title`, c.alias, c.active, 
				s.site_url, s.id as site_id
			FROM ".$db->tables['categs']." c 
			LEFT JOIN ".$db->tables['site_categ']." sc ON (c.id = sc.id_categ) 
			LEFT JOIN ".$db->tables['site_info']." s ON (sc.id_site = s.id) 
			WHERE c.id = '".$cid."' ", ARRAY_A);

	if(!empty($cid_ar) && !empty($cid_ar['sortby'])){
		$sortby = "pubs.".$cid_ar['sortby'];
		if(!empty($cid_ar['site_url'])){
			$cid_ar['url'] = $cid_ar['site_url'].'/'.$cid_ar['alias'].URL_END;
			if(empty($cid_ar['active'])){
				$cid_ar['url'] .= '?debug';
			}
		}
	}else{
		$sortby = "pubs.`date_insert` desc";
	}
	$tpl->assign('cid',$cid_ar);
	
    $query = "SELECT pubs.*, 
				categ.title as categ_title, 
				(SELECT count(*) 
					FROM ".$db->tables["comments"]." c WHERE c.record_id = pubs.id AND c.record_type = 'pub'
				) as comments_qty,
				(SELECT COUNT(DISTINCT `id_in_record`) 
					FROM ".$db->tables['uploaded_pics']." u 
					WHERE u.record_id = pubs.id 
					AND u.record_type = 'pub'
				) as fotos, 
				
				(SELECT si2.site_url 
					FROM ".$db->tables['site_info']." si2, 
					".$db->tables['site_publications']." sp2
					WHERE sp2.id_publications = pubs.id 
						AND sp2.id_site = si2.id 
					LIMIT 0, 1
				) as site_url,
				
				(SELECT si2.id 
					FROM ".$db->tables['site_info']." si2, 
					".$db->tables['site_publications']." sp2
					WHERE sp2.id_publications = pubs.id 
						AND sp2.id_site = si2.id 
					LIMIT 0, 1
				) as site_id,
				
				c1.id as categ1_id, 
				c1.title as categ1_title,
				
				(SELECT COUNT(*) 
					FROM ".$db->tables["categs"]." c11, 
						".$db->tables["pub_categs"]." pc11
					WHERE 
						pc11.id_pub = pubs.id 
						AND pc11.where_placed = 'pub' 
						AND pc11.id_categ = c11.id
					LIMIT 0,1
				) as categs_qty 
				
            FROM 
            ".$db->tables["pub_categs"]." as cat,
            ".$db->tables["categs"]." as categ, 
			".$db->tables["publications"]." as pubs	
			LEFT JOIN ".$db->tables["categs"]." c1 
				ON (c1.id = 
					(SELECT c11.id 
						FROM ".$db->tables["categs"]." c11, 
							".$db->tables["pub_categs"]." pc11
						WHERE 
							pc11.id_pub = pubs.id 
							AND pc11.where_placed = 'pub' 
							AND pc11.id_categ = c11.id
						LIMIT 0,1
					)
				)
            WHERE cat.id_categ = '".$cid."' AND 
			cat.id_categ = categ.id AND 
            cat.id_pub = pubs.id ".(count($wheres) ? " AND ".implode(" AND ",$wheres) : "")
            ."order by ".$sortby." ";
	//$categ = $db->get_row($query);
    //if(!empty($db->last_error)){ return db_error(basename(__FILE__).": 948"); }
	
  }

  if(!isset($query)){
    $query = "SELECT pubs.*, 
	
				(SELECT count(*) 
					FROM ".$db->tables["comments"]." c 
					WHERE c.record_id = pubs.id AND c.record_type = 'pub'
				) as comments_qty,
				
				(SELECT COUNT(DISTINCT `id_in_record`) 
					FROM ".$db->tables['uploaded_pics']."  u 
					WHERE u.record_id = pubs.id 
					AND u.record_type = 'pub'
				) as fotos, 
				(SELECT si2.site_url 
					FROM ".$db->tables['site_info']." si2, 
					".$db->tables['site_publications']." sp2
					WHERE sp2.id_publications = pubs.id 
						AND sp2.id_site = si2.id 
					LIMIT 0, 1
				) as site_url, 
				(SELECT si2.id 
					FROM ".$db->tables['site_info']." si2, 
					".$db->tables['site_publications']." sp2
					WHERE sp2.id_publications = pubs.id 
						AND sp2.id_site = si2.id 
					LIMIT 0, 1
				) as site_id,
				c1.id as categ1_id, 
				c1.title as categ1_title,
				
				(SELECT COUNT(*) 
					FROM ".$db->tables["categs"]." c11, 
						".$db->tables["pub_categs"]." pc11
					WHERE 
						pc11.id_pub = pubs.id 
						AND pc11.where_placed = 'pub' 
						AND pc11.id_categ = c11.id
					LIMIT 0,1
				) as categs_qty 				
			
			FROM ".$db->tables["publications"]." pubs 
			
			LEFT JOIN ".$db->tables["categs"]." c1 
				ON (c1.id = 
					(SELECT c11.id 
						FROM ".$db->tables["categs"]." c11, 
							".$db->tables["pub_categs"]." pc11
						WHERE 
							pc11.id_pub = pubs.id 
							AND pc11.where_placed = 'pub' 
							AND pc11.id_categ = c11.id
						LIMIT 0,1
					)
				)
				
	";
    if(count($wheres)) $query .= " WHERE ".implode(" AND ",$wheres);
    $query .= " ORDER BY pubs.date_insert desc";
  }
  $results = $db->get_results($query, ARRAY_A);
  if(!empty($db->last_error)){ return db_error(basename(__FILE__).": 972"); }
//$db->debug(); exit;
  $all_results = $db->num_rows;
  $tpl->assign("all_pubs_qty", $all_results);
  $results = $db->get_results($query." LIMIT $page_start, $page_stop ", ARRAY_A);
  $tpl->assign("pubs_list", $results);
  $tpl->assign("pages",_pages($all_results, $page, ONPAGE,true));

  $results = $db->get_results("SELECT p.id_categ as id, c.title, 
			c2.title as parent_title, 
			c3.title as parent2_title, 
			IFNULL(c2.sort,c.sort) as sort2, 
			IFNULL(c3.sort,IFNULL(c2.sort,c.sort)) as sort3 			
        FROM ".$db->tables["pub_categs"]." p  
        LEFT JOIN ".$db->tables["categs"]." c on (c.id = p.id_categ) 
        LEFT JOIN ".$db->tables["categs"]." c2 on (c2.id = c.id_parent)
		LEFT JOIN ".$db->tables["categs"]." c3 on (c3.id = c2.id_parent)
        WHERE p.`where_placed` = 'pub' 
        GROUP by p.id_categ 
		ORDER BY sort3, sort2, c.sort, c.title 
	", ARRAY_A);
		/*
  if(is_array($results))
  foreach($results as $k=>$row){
    if(!empty($row['subtitle'])){
      $row["title"] = $row['subtitle']." &raquo; ".$row['title'];
    }
    $results[$k] = $row;
  }
  */
  $tpl->assign("categs", $results);
  return $tpl->display("info/list_publications.html");
}

/* ok, websited in pubs list */
function _sites4pub($id)
{
  global $db, $tpl;
  if(isset($id['id'])){ $id = $id['id']; }else{ return;}
  $query = "SELECT si.id, si.site_url, 
	p.alias as alias, p.active 
    FROM ".$db->tables["site_publications"]." as sp,
    ".$db->tables["site_info"]." as si
    LEFT JOIN ".$db->tables["publications"]." p on (p.id = '".$id."')  
    WHERE sp.id_publications = '".$id."' AND
    sp.id_site = si.id ";
  $rows = $db->get_results($query, ARRAY_A);
  $tpl->assign("websites", $rows);
  return $tpl->fetch('info/list_websites.html');
}


// ok, newversion
function edit_publication($id)
{
  global $db, $lang, $tpl, $admin_vars, $site_vars, $site;
  $categ_info = $db->get_row("SELECT p.*, 
        b.name as user_name, b.login as user_login, 
		(SELECT COUNT(*) FROM ".$db->tables["counter"]." 
			WHERE for_page = 'pub' AND id_page = p.id 
			AND `time` > NOW() - interval 30 DAY 
		) as views_month, 
        (SELECT COUNT(DISTINCT id_in_record) 
          FROM ".$db->tables["uploaded_pics"]." 
          WHERE `record_id` = '".$id."' AND record_type = 'pub' ) as fotos_qty,
        (SELECT count(*) 
          FROM ".$db->tables["uploaded_files"]." 
          WHERE `record_id` = '".$id."' AND record_type = 'pub') as files_qty
    FROM ".$db->tables["publications"]." p
    LEFT JOIN ".$db->tables['users']." b on (b.id = p.user_id)  
    WHERE p.id = '".$id."' ", ARRAY_A);

  if($id > 0 && $db->num_rows == 0){ return error_not_found();  }
  if($db->num_rows == 0){
    $prefix = isset($site_vars['sys_prefix_pub']) ? $site_vars['sys_prefix_pub'] : "pub-".time();
    $categ_info = array(
      'id' => 0,
      'active' => 1,
      'name' => '',
      'anons' => '',
      'meta_description' => '',
      'meta_keywords' => '',
      'meta_title' => '',
      'alias' => $prefix,
      'memo' => '',
      'date_insert' => date('Y-m-d H:i:s'),
      'date_update' => NULL,
      'user_id' => $admin_vars['bo_user']['id'],
      'multitpl' => 'pub.html',
      'icon' => '',
      'f_spec' => 0,
      'fotos_qty'=> 0,
      'files_qty' => 0,
	  'comments_qty' => 0
    );
  }
  
	$categ_info['comments_qty'] = $db->get_var("SELECT count(*) 
		FROM ".$db->tables['comments']." 
		WHERE record_type = 'pub' AND record_id = '".$id."'
	");

  if($id > 0){
	include_once(MODULE.'/get_blocks.php');
	$site->uri['path'] = '/'.$categ_info['alias'];
	if(defined('__NAMESPACE__')){
		$categ_info['blocks'] = call_user_func(__NAMESPACE__ .'blocks::get_blocks', $site);
	}else{
		$categ_info['blocks'] = call_user_func('blocks::get_blocks', $site);
	}
	
  }
  
  if(isset($_POST["save"]) || isset($_POST["del"])){
    return save_publication($id);
  }

  
  $categ_info['date_insert_f'] = date($site->vars['site_date_format'].' '.$site->vars['site_time_format'], strtotime($categ_info['date_insert']));
  
	$categ_info['ddate_f'] = !empty($categ_info['ddate']) && $categ_info['ddate'] != '0000-00-00 00:00:00' ? date($site->vars['site_date_format'].' '.$site->vars['site_time_format'], strtotime($categ_info['ddate'])) : '';  
  
  if(!empty($site_vars['sys_tpl_pubs'])){
    $categ_info['tpls'] = get_array_vars('sys_tpl_pubs');
    if($id == 0){
      $categ_info['multitpl'] = $db->get_var("SELECT `value` 
        FROM ".$db->tables['site_vars']." 
        WHERE `name` = 'sys_tpl_pubs' ");  
    }  
  }                                
  
  if($id == 0 && !empty($_GET['cid']) && intval($_GET['cid']) > 0){
    $cid = intval($_GET['cid']); 
    $pub_cat_names = $db->get_results("SELECT c.id,c.title,c.active, 
        c2.title as subtitle, c2.id as subid 
      FROM ".$db->tables["categs"]." c  
      LEFT JOIN ".$db->tables["categs"]." c2 on (c2.id = c.id_parent) 
      WHERE c.id = '".$cid."' 
      ORDER BY c.`sort`, c.`title` ", ARRAY_A);
  }else{
    $pub_cat_names = $db->get_results("SELECT c.id,c.title,c.active, 
        c2.title as subtitle, c2.id as subid 
      FROM ".$db->tables["pub_categs"]." pc 
      LEFT JOIN ".$db->tables["categs"]." c on (c.id = pc.id_categ) 
      LEFT JOIN ".$db->tables["categs"]." c2 on (c2.id = c.id_parent) 
      WHERE pc.id_pub = '".$id."' AND pc.id_pub > '0' AND pc.where_placed = 'pub'
      ORDER BY c.`sort`, c.`title` ", ARRAY_A);
  }    
  
	$publication_categories = array();
	if(!empty($pub_cat_names)){
		foreach($pub_cat_names as $k=>$v){
			$pub_cat_names[$v['id']] = $v;
			$publication_categories[] = $v['id'];
			unset($pub_cat_names[$k]);
		}
	}
   
  $tpl->assign('publication_categories',$pub_cat_names);

  $categ_info['all_files_qty'] = $db->get_var("SELECT count(*) FROM ".$db->tables['uploaded_files']."  ");
  $categ_info['all_products'] = $db->get_var("SELECT count(*) FROM ".$db->tables['products']."  ");


  $ar_fotos = array();
  if($categ_info['fotos_qty'] > 0){
    $ar_fotos = $db->get_results("SELECT * 
      FROM ".$db->tables["uploaded_pics"]." 
      WHERE `record_id` = '".$id."' AND record_type = 'pub'       
      ORDER BY is_default desc, id_in_record, width", ARRAY_A); 
  }
  $categ_info['uploaded_fotos'] = $ar_fotos;  

  $ar_files = array();
  if($categ_info['files_qty'] > 0){
    $ar_files = $db->get_results("SELECT * 
      FROM ".$db->tables["uploaded_files"]." 
      WHERE `record_id` = '".$id."' AND record_type = 'pub'       
      ORDER BY id_in_record, title, id ", ARRAY_A); 
  }
  $categ_info['uploaded_files'] = $ar_files;

  $categ_info['options'] = get_options($id, implode(",",$publication_categories), 'categ');

  $categ_info['all_pubs'] = $db->get_var("SELECT count(*) FROM ".$db->tables['publications']." WHERE id <> '".$id."' ");

  $tpl->assign("categ",$categ_info);
  $tpl->assign("record_type","pub");
  $tpl->assign("blocks",get_blocks_info($id,'pub'));
  
  // Получаем названия привязанных к публикации продуктов
  $publication_products = already_pub2p($id);
  $tpl->assign('publication_products_ids',implode(",",$publication_products));
  $pub_prod_names = $db->get_results("SELECT p.id,p.name as title
    FROM ".$db->tables["pub_to_product"]." pp 
    LEFT JOIN ".$db->tables["products"]." p on (p.id = pp.id_product) 
    WHERE pp.id_pub = '".$id."' AND pp.id_pub > '0' 
    ORDER BY p.`name` ", ARRAY_A);
  $tpl->assign('publication_products',$pub_prod_names);
  // --------------------------------------------------------------------

    $array = $db->get_results("SELECT * FROM ".$db->tables['connections']." WHERE (id1 = '".$id."' OR id2 = '".$id."') AND name1 = 'pub' AND name2 = 'pub' ");
	$p2pubs = array();
	if($db->num_rows > 0){
	  foreach($array as $v){
		  $new = $v->id1 == $id ? $v->id2 : $v->id1;
		  $p2pubs[] = $new;		  
	  }
	}
	$p2pubs = implode(",",$p2pubs);
	$name2pubs = array();
	if(!empty($p2pubs)){
		$array1 = $db->get_results("SELECT id, name as title FROM ".$db->tables['publications']." 
			WHERE id IN (".$p2pubs.") ");
		if($db->num_rows > 0){
			foreach($array1 as $v){
				$name2pubs[$v->id] = array(
					'id' => $v->id,
					'title' => $v->title
				);
			}
		}
		
	}
	
	if(!empty($_GET['del_option'])){
	  $del_opt = intval($_GET['del_option']);
	  if($del_opt > 0){
		  $sql = "DELETE FROM ".$db->tables['option_values']." 
				WHERE 
					id = '".$del_opt."' 
					AND id_product = '".$id."'
					AND where_placed = 'pub' 
		  ";
		  $db->query($sql);
		  //$db->debug(); exit;
		  $url = "?action=info&do=edit_publication&id=".$id;
		  header("Location: ".$url);
		  exit;
	  }
	}
	
	
  $tpl->assign('publication_pubs', $name2pubs);
  $tpl->assign('publication_pubs_ids', $p2pubs);

  return $tpl->display("info/edit_publication.html");
}

/* ok, used for function edit_publication */
function already_pub2p($id){
  global $db;
  $query = "SELECT * FROM ".$db->tables['pub_to_product']." WHERE id_pub = '".$id."'  ";
  $rows = $db->get_results($query);
  if($db->num_rows == 0){ return array(); }
  $ar = array();
  foreach($rows as $row){
    $ar[] = $row->id_product;
  }
  return $ar;
}

/* ok */
function pub_to_products($ar){
  global $db;
  $id = !empty($ar['id']) ? intval($ar['id']) : 0;
  $field = !empty($ar['field']) ? $ar['field'] : "";

  $ar = already_pub2p($id);
  $query = "SELECT 
		p.`id`, p.`name`, p.`alias`,
		p.`active`, p.`f_spec`, p.`f_new`,
		c.id as categ_id, 
		c.title as categ_title, 
		c.alias as categ_alias,
		c2.title as categ2_title,
		c2.sort as categ2_sort,
		c3.title as categ3_title,
		c3.sort as categ3_sort
	FROM ".$db->tables['products']." p 
	LEFT JOIN ".$db->tables['pub_categs']." pc on (p.id = pc.id_pub) 
	LEFT JOIN ".$db->tables['categs']." c on (pc.id_categ = c.id) 
	LEFT JOIN ".$db->tables['categs']." c2 on (c.id_parent = c2.id) 
	LEFT JOIN ".$db->tables['categs']." c3 on (c2.id_parent = c3.id) 
	WHERE pc.where_placed = 'product'
	ORDER BY c3.sort, c3.title, c2.sort, c2.title, c.sort, c.title, p.`name` ";
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
		$c_title = !empty($row->categ3_title) 
			? $row->categ3_title.' / ' : '';
			
		if(!empty($row->categ2_title)){
			$c_title .= $row->categ2_title.' / ';
		}	
		
		$c_title .=  $row->categ_title;
		$str .= "<li><h4>".$c_title."</h4></li>";
	}
    $str .= "<li>$form <a href='javascript:' title='".$row->categ_title."'><span id='produkt{$row->id}'>".stripslashes($row->name)."</span></a></li>";
	$title_id = $row->categ_id;
  }
  $str .= "</ul>";
  return $str;
}

/* ok */
function pub_to_pubs($ar){
  global $db;
  $id = !empty($ar['id']) ? intval($ar['id']) : 0;
  $field = !empty($ar['field']) ? $ar['field'] : "";

  $array = $db->get_results("SELECT * FROM ".$db->tables['connections']." WHERE (id1 = '".$id."' OR id2 = '".$id."') AND name1 = 'pub' AND name2 = 'pub' ");
  $ar = array();
  if($db->num_rows > 0){
	  foreach($array as $v){
		  $new = $v->id1 == $id ? $v->id2 : $v->id1;
		  $ar[] = $new;		  
	  }
  }
  
  $query = "SELECT 
		p.id, p.`name`, p.alias, 
		p.active, p.f_spec, 
		c.id as categ_id, c.title as categ_title, 
		c.alias as categ_alias, 
		c2.id as categ2_id, c2.title as categ2_title, 
		c3.id as categ3_id, c3.title as categ3_title   		
	FROM ".$db->tables['publications']." p 
	LEFT JOIN ".$db->tables['pub_categs']." pc on (p.id = pc.id_pub) 
	LEFT JOIN ".$db->tables['categs']." c on (pc.id_categ = c.id) 
	LEFT JOIN ".$db->tables['categs']." c2 on (c.id_parent = c2.id) 
	LEFT JOIN ".$db->tables['categs']." c3 on (c2.id_parent = c3.id) 
	WHERE pc.where_placed = 'pub' AND p.id <> '".$id."' 
	ORDER BY c3.sort, c3.title, c2.sort, c2.title, c.sort, c.title,  p.`name`
	";
  $rows = $db->get_results($query);
  $titles = array();
  //$db->debug();
  //exit;
  
  if($db->num_rows == 0){ return; }
  $title_id = 0;
  $top = '<a name="t"></a>';
  $str = "<ul id='list_pub'>";
  foreach($rows as $row){
    if($field == ''){
      $form = "";
    }else{
      $ch = in_array($row->id, $ar) ? "checked" : "";
      $form = "<input type=checkbox name=".$field."[] value='".$row->id."' $ch>";
    }
    if($title_id != $row->categ_id){
		$categ_title = '';
		if(!empty($row->categ3_title)){
			$categ_title .= $row->categ3_title." / ";
		}
		if(!empty($row->categ2_title)){
			$categ_title .= $row->categ2_title." / ";
		}
		$categ_title .= $row->categ_title;
		
		if($db->num_rows > 100){
			/*			
			$str .= '<a name="t'.$row->categ_id.'"></a>
			';
			*/
			$titles[$row->categ_id] = $categ_title;
			//$categ_title .= ' <a href="#t">#</a>';
		}
		
		if($db->num_rows > 1){
		$str .= "<li><h4>".$categ_title."</h4></li>
		";		
		}
	}
    $str .= "<li>$form <a href='javascript:;' title='".$row->categ_title."'><span id='pub{$row->id}'>".stripslashes($row->name)."</span></a></li>
	";
	$title_id = $row->categ_id;
  }
  $str .= "</ul>";
  /*
  if($db->num_rows > 100 && count($titles) > 1){
	  $top .= '<ul>
	  ';
	  foreach($titles as $k => $v){
		  $top .= '<li><a href="#t'.$k.'">'.$v.'</a></li>
		  ';
	  }
	  $top .= '</ul>
	  ';
  }
  */
  //echo $top;
  //echo $str;
  //exit;
  return $top.$str;
}




// ok, newversion
function update_products2pub($id, $str)
{
  global $db;
  $query = "DELETE FROM ".$db->tables['pub_to_product']." WHERE id_pub = '".$id."'  ";
  $db->query($query);
  if(empty($str)){ return; }
  $produkti = explode(",",$str);

  if(!empty($produkti)){
    if(is_array($produkti) && count($produkti) > 0){
      foreach($produkti as $v){
          $query = "INSERT INTO ".$db->tables['pub_to_product']." (id_pub, id_product)
            VALUES ('".$id."', '".$v."') ";
          $db->query($query);
      }
    }
  }
  return;
}

/* ok, used for function edit_publication */
function pub_categs_ar($id)
{
  global $db;
  $query = "SELECT id_categ FROM ".$db->tables["pub_categs"]."
            WHERE id_pub = '".$id."' AND id_pub > '0' 
			AND `where_placed` = 'pub' ";
  $rows = $db->get_results($query);
  
  if($db->num_rows == 0){
    if($id == 0 && !empty($_GET['cid'])){ return array($_GET['cid']); }   
    return array();     
  }
  $ar = array();
  foreach($rows as $row){
    $ar[] = $row->id_categ;
  }
  return $ar;
}

/* ok */
function save_publication($id)
{
  global $tpl;
  if(isset($_POST['save']) && $id == 0){ // добавляем
    return add_new_publication();  
  }elseif(isset($_POST['save']) && $id > 0){ // обновляем
    return update_publication($id);
  }elseif(isset($_POST["del"]) && $id > 0){ // удаляем
    return delete_publication($id, true);
  }else{
    $tpl->display("empty.html"); 
    exit;
  }
}


// ok, newversion correct
function add_new_publication()
{
  global $db, $admin_vars, $site_vars;
  $posted = $_POST;
  if($posted["alias"] == ""){
    $posted["alias"] = stripslashes($posted["name"]);
  }
  
  if(!empty($site_vars['sys_prefix_pub']) && $site_vars['sys_prefix_pub'] == $posted["alias"]){
	  $posted["alias"] .= trim($posted["name"]);
  }
  $posted["alias"] = build_slug($posted["alias"], "pub", 0);
  
  if($posted["meta_title"] == ''){
    $posted["meta_title"] = $posted["name"];
  }
  if($posted["meta_description"] == ''){
    $posted["meta_description"] = $posted["name"];
  }
  if($posted["meta_keywords"] == ''){
    $posted["meta_keywords"] = $posted["name"];
  }

  if($posted["multitpl"] == ''){
    $posted["multitpl"] = 'pub.html';
  }

  if(!isset($posted["icon"])){
    $posted["icon"] = '';
  }

  if(!isset($posted["f_spec"])){
    $posted["f_spec"] = 0;
  }


  $memo = $posted["memo"];
  if(isset($_GET['noeditor'])) { $memo = nl2br($memo); }
  
  $date_insert = $_POST["insert_date"]["Year"]."-".$_POST["insert_date"]["Month"]."-".$_POST["insert_date"]["Day"]." 00:00:01"; 
  
  $query = "INSERT INTO ".$db->tables["publications"]." (`name`, `anons`,
            `memo`, `active`, `meta_title`, `meta_description`,
            `meta_keywords`, `alias`, `date_insert`, `user_id`, `multitpl`, 
            `icon`, `f_spec`) VALUES(
            '".$db->escape($posted["name"])."',
            '".$db->escape($posted["anons"])."',
            '".$db->escape($memo)."',
            '".$db->escape($posted["active"])."',
            '".$db->escape($posted["meta_title"])."',
            '".$db->escape($posted["meta_description"])."',
            '".$db->escape($posted["meta_keywords"])."',
            '".$db->escape($posted["alias"])."',
            '".$date_insert."',
            '".$admin_vars['bo_user']['id']."',
            '".$db->escape($posted["multitpl"])."',
            '".$db->escape($posted["icon"])."',
            '".$db->escape($posted["f_spec"])."'
            )";
  $db->query($query);
  $id = $db->insert_id;
  register_changes('pub', $id, 'add', $posted["name"]);
  upload_files($id);

  $fotos = add_new_fotos($id, 'pub');

  // Обновляем характеристики
  update_options($id,'pub');
  
  if(isset($posted["site_pubs"])){
    add_site_pubs($id,$posted["site_pubs"]);
  }
  if(isset($posted["id_razdelov"])){
    add_pub_categs($id,$posted["id_razdelov"]);
  }
  
  if(isset($posted["id_produktov"])){
    update_products2pub($id, $posted["id_produktov"]);
  }
  
  if(!isset($posted["id_pubs"])){ $posted["id_pubs"] = ""; }
  update_pubs2pub($id, $posted["id_pubs"]);  
  
  add_blocks($id, 'pub');
  clear_cache(0);
  header("Location: ?action=info&do=edit_publication&id=".$id."&added=1");
  exit;
}

// ok
function delete_pub_categs($id)
{
  global $db;
  $query = "DELETE FROM ".$db->tables["pub_categs"]." 
	WHERE id_pub = '".$id."' AND `where_placed` = 'pub' ";
  $db->query($query);
  return;
}

// ok
function delete_pub_site($id)
{
  global $db;
  $query = "DELETE FROM ".$db->tables["site_publications"]." WHERE id_publications = '".$id."' ";
  $db->query($query);
  return;
}

// ok
function update_publication($id)
{
  global $db, $admin_vars;
  $posted = $_POST;
  if($posted["alias"] == ""){
    $posted["alias"] = stripslashes($posted["name"]);
  }
  $posted["alias"] = build_slug($posted["alias"], "pub", $id);

  $memo = $posted["memo"];
  if(isset($posted['noeditor'])) { $memo = nl2br($memo); }

  if($posted["multitpl"] == ''){
    $posted["multitpl"] = 'pub.html';
  }

  if(!isset($posted["icon"])){
    $posted["icon"] = '';
  }

 if(!empty($posted["icon_custom"])){
    $posted["icon"] = trim($posted["icon_custom"]);
  }

  if(!isset($posted["f_spec"])){
    $posted["f_spec"] = 0;
  }
  
  $memo = $posted["memo"];
  if(isset($_GET['noeditor'])) { $memo = nl2br($memo); }
  $date_insert = $_POST["insert_date"]["Year"]."-".$_POST["insert_date"]["Month"]."-".$_POST["insert_date"]["Day"]." ".$_POST["insert_date"]["Hour"].":".$_POST["insert_date"]["Minute"].":".$_POST["insert_date"]["Second"]; 

  $query = "UPDATE ".$db->tables["publications"]." SET
            `name` = '".$db->escape($posted["name"])."',
            `anons` = '".$db->escape($posted["anons"])."',
            `memo` = '".$db->escape($memo)."',
            `ddate` = '".date("Y-m-d H:i:s")."',
            `active` = '".$db->escape($posted["active"])."',
            `meta_title` = '".$db->escape($posted["meta_title"])."',
            `meta_description` = '".$db->escape($posted["meta_description"])."',
            `meta_keywords` = '".$db->escape($posted["meta_keywords"])."',
            `alias` = '".$db->escape($posted["alias"])."',
            `user_id` = '".$admin_vars["bo_user"]["id"]."',
            `multitpl` = '".$db->escape($posted["multitpl"])."',
            `icon` = '".$db->escape($posted["icon"])."',
            `f_spec` = '".$db->escape($posted["f_spec"])."', 
            `date_insert` = '".$date_insert."'
            WHERE id = '".$id."'
            ";
  $db->query($query);
  register_changes('pub', $id, 'update');
 
  delete_pub_categs($id);
  delete_pub_site($id);

  update_picture_title_records($id,$posted);
  $fotos = add_new_fotos($id, 'pub');

  //add_uploaded_files($id);
  delete_group_pics($id,$posted);
  set_default_picture2($id,$posted);

  // Обновляем позиции рисунков
  update_picture_positions($id,$posted);

  // Обновляем характеристики
  update_options($id,'pub');

  if(isset($posted["site_pubs"])){
    add_site_pubs($id,$posted["site_pubs"]);
  }
  if(isset($posted["id_razdelov"])){
    add_pub_categs($id,$posted["id_razdelov"]);
  }
  if(isset($posted["id_produktov"])){
    update_products2pub($id, $posted["id_produktov"]);
  }

  if(!isset($posted["id_pubs"])){ $posted["id_pubs"] = ""; }
  update_pubs2pub($id, $posted["id_pubs"]);  

  create_new_photos($id, $posted);
  update_files($id,$posted);
  upload_files($id);
  
  add_blocks($id, 'pub');
  update_blocks($id, 'pub');

  clear_cache(0);
  header("Location: ?action=info&do=edit_publication&id=".$id."&updated=1");
  exit;
}

// ok
function delete_publication($id, $redirect=false)
{
  global $db;
  delete_pub_categs($id);
  delete_pub_site($id);
  delete_uploaded_pics($id, 'pub', false, true);
  delete_uploaded_files($id,'pub');
  delete_comments($id, 'pub');
  delete_options($id, 'pub');
  
  delete_blocks($id, 'pub');
  $query = "DELETE FROM ".$db->tables['publications']." WHERE id = '".$id."' ";
  $db->query($query);
  register_changes('pub', $id, 'delete');
  
  /* удалим привязку к избранным */
  $query = "DELETE FROM ".$db->tables["fav"]." WHERE where_id = '".$id."' AND `where_placed` = 'pub' ";
  $db->query($query);
  
  /* удалим запись в COUNTER */
  $query = "DELETE FROM ".$db->tables["counter"]." 
	WHERE id_page = '".$id."' AND `for_page` = 'pub' ";
  $db->query($query);

  /* удалим связь с другой публикацией */
  $query = "DELETE FROM ".$db->tables["connections"]." 
	WHERE (id1 = '".$id."' OR id2 = '".$id."') 
	AND `name1` = 'pub' AND `name2` = 'pub' ";
  $db->query($query);

  /* удалим связь с товаром */
  $query = "DELETE FROM ".$db->tables["pub_to_product"]." 
	WHERE id_pub = '".$id."'  ";
  $db->query($query);

  
  if($redirect){
	clear_cache(0);
    header("Location: ?action=info&do=list_publications&deleted=1");
    exit;
  }
  return;
}



/* OK, top-categs in pubs list */
function pub_in_categs($id)
{
  global $db, $tpl;
  $id = isset($id['id']) ? $id['id'] : 0;
  if( $id == 0){ return; } 
  $query = "SELECT c.id, c.title, c.active
            FROM ".$db->tables["categs"]." c, ".$db->tables["pub_categs"]." pc
            WHERE pc.id_pub = '".$id."' AND pc.where_placed = 'pub' 
            AND pc.id_categ = c.id order by
            c.sort, c.title LIMIT 0,5 ";
  $rows = $db->get_results($query, ARRAY_A);
  if($db->num_rows == 0){ return "-----"; }
  $tpl->assign("categs_in_pubs", $rows);
  return $tpl->fetch('info/categs_in_pubs.html');
}


/* ok */
function copy_page($id)
{
  global $db, $admin_vars, $site_vars;

  $where = isset($_GET['where']) ? trim($_GET['where']) : '';
  if($id == 0 || empty($where)){
    return error_not_found();  
  }

  if($where == 'product'){
    $table = $db->tables['products'];
    $prefix = isset($site_vars['sys_prefix_product']) ? $site_vars['sys_prefix_product'] : "item-";
    $href = "?action=products&do=edit&id=";
  }elseif($where == 'pub'){
    $table = $db->tables['publications'];
    $prefix = isset($site_vars['sys_prefix_pub']) ? $site_vars['sys_prefix_pub'] : "pub-";
    $href= "?action=info&do=edit_publication&id=";
  }elseif($where == 'categ'){
    $table = $db->tables['categs'];
    $prefix = isset($site_vars['sys_prefix_categ']) ? $site_vars['sys_prefix_categ'] : "page-";
    $href= "?action=info&do=edit_categ&id=";
  }else{
    return error_not_found();    
  }

  $row = $db->get_row("SELECT * FROM ".$table." WHERE id = '".$id."' ");
  if($db->num_rows == 0 || !$row){
    return error_not_found();    
  }

  $db->query("INSERT INTO ".$table." (`id`) VALUES('') ");
  $new_id = $db->insert_id;

  foreach($row as $k=>$v){
    if($k == 'active'){ $v = 0; }    
    if($k == 'name'){ $v = "Копия: ".$v; }
    if($k == 'title'){ $v = "Копия: ".$v; }
    if($k == 'views'){ $v = 0; }
    if($k == 'alias'){ $v = $prefix.time(); }
    if($k == 'user_id'){ $v = $admin_vars['bo_user']['id']; }
    if($k == 'date_insert'){ $v = date('Y-m-d H:i:s'); }
    if($k == 'date_update'){ $v = '0000-00-00 00:00:00'; }
    if($k != "id"){
      $db->query("UPDATE $table SET `".$k."` = '".$db->escape($v)."' 
		WHERE `id` = '".$new_id."' ");
    }
  }

  copy_fotos($id, $new_id, $where);
  copy_files($id, $new_id, $where);
  copy_connected_sites($id, $new_id, $where);
  copy_connected_blocks($id, $new_id, $where);
  
  if($where == 'categ'){
    // копируем группы характеристик
    copy_connected_optgroups($id, $new_id, $where);      
  }else{
    // копируем страницы и характеристики
    copy_connected_pages($id, $new_id, $where);
    copy_connected_options($id, $new_id, $where);
  }    
  
  $href = $href.$new_id."&added=1";
  header("Location: ".$href);
  exit;
}

/* ok */
function copy_connected_blocks($id, $new_id, $where)
{
	global $db, $site, $site_vars;  
	$sql = "SELECT * FROM ".$db->tables['blocks']." 
		WHERE `type` = '".$where."' AND `type_id` = '".$id."' 
	";
	$rows = $db->get_results($sql);
	if(!$rows || $db->num_rows == 0){ return; } 
	
	foreach($rows as $row){
		$sql = "INSERT INTO ".$db->tables['blocks']." 
			(`active`, `where_placed`, `title`, 
			`title_admin`, `qty`, `type`, 
			`type_id`, `pages`, `skip_pages`,
			`html`, `sort`) 
			VALUES ('".$row->active."', 
			'".$db->escape($row->where_placed)."',
			
			'".$db->escape($row->title)."',
			'".$db->escape($row->title_admin)."',
			'".$db->escape($row->qty)."',
			'".$db->escape($row->type)."',
			'".$db->escape($new_id)."',
			'".$db->escape($row->pages)."',
			'".$db->escape($row->skip_pages)."',
			'".$db->escape($row->html)."',
			'".$db->escape($row->sort)."') 		
		";
		$db->query($sql);
		$bid = $db->insert_id;
		copy_fotos($row->id, $bid, 'block');
	}	
	return;
}

/* ok */
function copy_fotos($id, $new_id, $where)
{
	global $db, $site, $site_vars;  
  
  $rows = $db->get_results("SELECT * FROM ".$db->tables['uploaded_pics']." 
      WHERE record_id = '".$id."' AND record_type = '".$where."' 
      ORDER BY id_in_record ");
  if(!$rows || $db->num_rows == 0){ return; }      

  foreach($rows as $k=>$v){
        $db->query("INSERT INTO ".$db->tables["uploaded_pics"]." 
          (`id_exists`, `record_id`, `record_type`, `width`, `height`,  
          `title`, `ext`, `id_in_record`, `is_default`) 
          VALUES('0', '".$new_id."', 
          '".$v->record_type."', '".$v->width."', 
          '".$v->height."', '".$v->title."', 
          '".$v->ext."', '".$v->id_in_record."', '".$v->is_default."') ");

        $file_id = $db->insert_id;
        $from = "../upload/records/".$v->id.".".$v->ext;
        $uploaddir = "../upload/records/".$file_id.".".$v->ext;
        
        if(@!copy($from,$uploaddir)){
          die("File can't be written, wrong path in $uploaddir");
        } 
  }
	return;  
}

/* ok */
function copy_files($id, $new_id, $where)
{
	global $db, $site, $site_vars;  
  $rows = $db->get_results("SELECT * FROM ".$db->tables['uploaded_files']." 
      WHERE `record_id` = '".$id."' AND `record_type` = '".$where."' 
      ORDER BY `id_in_record` ");
  if(!$rows || $db->num_rows == 0){ return; }      

  foreach($rows as $k=>$v){
        $db->query("INSERT INTO ".$db->tables["uploaded_files"]." 
          (`id_exists`, `record_id`, `record_type`, `size`, `filename`, 
          `title`, `ext`, `allow_download`, `direct_link`, 
          `id_in_record`, `time_added`) 
          VALUES('0', '".$new_id."', 
          '".$v->record_type."', '".$v->size."', 
          '".$v->filename."', '".$v->title."', 
          '".$v->ext."', '".$v->allow_download."', '".$v->direct_link."',
          '".$v->id_in_record."', '".time()."') ");

        $file_id = $db->insert_id;
        $from = "../upload/files/".md5($v->id).".$v->ext";
        $uploaddir = "../upload/files/".md5($file_id).".$v->ext";
        if(@!copy($from,$uploaddir)){
          die("File can't be written, wrong path in $uploaddir");
        } 
  }
	return;
}

/* ok */
function copy_connected_sites($id, $new_id, $where)
{
  global $db;
  if($where == 'pub'){
    $rows = $db->get_results("SELECT * FROM ".$db->tables['site_publications']." 
        WHERE `id_publications` = '".$id."' ");
    if($rows && $db->num_rows > 0){
      foreach($rows as $row){
        $db->query("INSERT INTO ".$db->tables['site_publications']." 
          (`id_site`, `id_publications`) 
          VALUES('".$row->id_site."', '".$new_id."') ");
      }
    }    
    return;
  }elseif($where == 'categ'){
    $rows = $db->get_results("SELECT * FROM ".$db->tables['site_categ']." 
        WHERE `id_categ` = '".$id."' ");
    if($rows && $db->num_rows > 0){
      foreach($rows as $row){
        $db->query("INSERT INTO ".$db->tables['site_categ']." 
          (`id_site`, `id_categ`) 
          VALUES('".$row->id_site."', '".$new_id."') ");
      }
    }    
    return;
  }else{
    return;
  }
}

/* ok */
function copy_connected_optgroups($id, $new_id, $where)
{
  global $db;
    $rows = $db->get_results("SELECT * FROM ".$db->tables['categ_options']." 
        WHERE `id_categ` = '".$id."' ");
    if($rows && $db->num_rows > 0){
      foreach($rows as $row){
        $db->query("INSERT INTO ".$db->tables['categ_options']." 
          (`id_option`, `id_categ`, `where_placed`) 
          VALUES('".$row->id_option."', '".$new_id."', 'categ') ");
      }
    }    
    return;
}

/* ok */
function copy_connected_pages($id, $new_id, $where)
{
  global $db;
    $rows = $db->get_results("SELECT * FROM ".$db->tables['pub_categs']." 
        WHERE `id_pub` = '".$id."' AND `where_placed` = '".$where."' ");
    if($rows && $db->num_rows > 0){
      foreach($rows as $row){
        $db->query("INSERT INTO ".$db->tables['pub_categs']." 
          (`id_pub`, `id_categ`, `where_placed`) 
          VALUES('".$new_id."', '".$row->id_categ."', '".$where."') ");
      }
    }    
    return;
}

/* ok */
function copy_connected_options($id, $new_id, $where)
{
  global $db;
    $rows = $db->get_results("SELECT * FROM ".$db->tables['option_values']." 
        WHERE id_product = '".$id."' AND `where_placed` = '".$where."' ");
    if($rows && $db->num_rows > 0){
      foreach($rows as $row){     
        $db->query("INSERT INTO ".$db->tables['option_values']." 
          (`id_option`, `id_product`, `value`, `where_placed`) 
          VALUES('".$row->id_option."', '".$new_id."', 
          '".$row->value."', '".$where."') ");
      }
    }    
    return;
}

?>