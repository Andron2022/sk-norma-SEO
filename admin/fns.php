<?php
if(!defined('SIMPLA_ADMIN')){ die(); }

/**********************************
***********************************
**	
**	Тут мы держим все необходимые функции
**	updated 28.06.2019
**	
**	+ added function register_changes() 16.09.2016
**	+ moved fns clear_cache 21.01.2017
**	+ ClassSite moved here 15.02.2017
**	+ delete_uploaded_files 13.02.2018 
**	не удалялся файл, если физически его не было
**	+ debug=2 site id added for pubs and categs
**	+ дополнен главный класс - там сразу грузятся все данные пользователя
**	+ теперь для языковых файлов админки достаточно 
**	загрузить файлы, ссылки на эти языки появятся автоматом
**	+ исправлено создание одинаковой характеристики 
**	(теперь как надо - только в группе, а не во всех)
**	+ можно убрать установку названия фото при загрузке
**  + установка характеристики типа чекбокс = 0, если не выбрано
**  (ранее не создавалась запись о характеристрике)
**	+ extra_options  
**  + direct_link for files
***********************************
**********************************/

require_once('fns.blocks.php');
require_once('fns.site.php');
require_once('fns.options.php');

	/* создадим класс site */
	class ClassSite  
	{
		public $bar = 'property';
		public $uri = array();
		public $user = array(
			'id' => 0,
			'name' => '',
			'email' => '',
			'login' => '',
			'prava' => array(),
		);
		
		function __construct(){
			$this->url = $this->request_url();
			$this->uri = parse_url($this->url);
			$this->langs = check_lang_files();
			$session_id = session_id();
			if(empty($session_id)) session_start();
			
			$this->user['id'] = isset($_SESSION['BO_USERID']) 
				? intval($_SESSION['BO_USERID']) : 0;
			$this->user['name'] = isset($_SESSION['BO_NAME']) 
				? $_SESSION['BO_NAME'] : '';
			$this->user['email'] = isset($_SESSION['BO_EMAIL']) 
				? $_SESSION['BO_EMAIL'] : '';
			$this->user['login'] = isset($_SESSION['BO_LOGIN']) 
				? $_SESSION['BO_LOGIN'] : '';
				
			global $db;	
			$sql = "SELECT * FROM ".$db->tables['users_prava']."  
				WHERE bo_userid = '".$this->user['id']."' ";
			$row = $db->get_row($sql, ARRAY_A);
			$this->user['prava'] = $row;
		}
		
		public function db_error($str='') {
			return error_404($str);	
		}
		
		public function request_url(){
		  $result = ''; // Пока результат пуст
		  $default_port = 80; // Порт по-умолчанию
		 
		  // А не в защищенном-ли мы соединении?
		  if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=='on')) {
			$result .= 'https://';
			$default_port = 443;
		  } else {
			$result .= 'http://';
		  }
		  $result .= $_SERVER['HTTP_HOST'];
		 
		  if ($_SERVER['SERVER_PORT'] != $default_port) {
			$result .= ':'.$_SERVER['SERVER_PORT'];
		  }
		  $result .= $_SERVER['REQUEST_URI'];
		  return $result;
		}
			
	}

// ok, check lang files for admin
function check_lang_files(){
	$folder = MODULE.'/lang/admin/';
	$langs = array();
	$dir = new DirectoryIterator($folder);
	foreach ($dir as $fileinfo)
	{   
		if(!$fileinfo->isDot() && 
			!is_dir($folder.'/'.$fileinfo->getFilename())
		){
			$f = explode('.', $fileinfo->getFilename());
			$langs[$f[0]] = strtoupper($f[0]);
			
		}
	}
	return $langs;
}	

// запись не найдена	
function error_not_found($str=''){ 
	global $tpl;
	if(empty($str)){ $str = GetMessage('admin','record_not_found'); }
	$tpl->assign("title", GetMessage('error','error'));
	$tpl->assign("metatitle", GetMessage('error','error'));
	$tpl->assign("content", "<pre><p>".$str."</p></pre>");
	$tpl->display("empty.html");   
	exit;
}

// in admin
function db_error($str=NULL){
	global $site, $tpl, $db;
	$site->header_status = 400;

	if(!empty($db->last_error)){ 
		$str .= "<p>MySQL said: ".$db->last_error."</p>
		<p>".$db->last_query."</p>"; 
	}
	
	$tpl->assign("content", "<pre><p>".$str."</p></pre>");
	$tpl->assign("title", GetMessage('admin','elements','db_error'));
	$tpl->assign("metatitle", GetMessage('admin','elements','db_error'));
	$tpl->display("empty.html");
	exit;
}	

	function register_changes($where, $id, $type, $comment=''){
		/* register db changes
			where / id /
			type - add/update/delete
			comment - optional		
		*/
		global $db, $site;
		$who_changed = isset($site->user['id']) ? $site->user['id'] : 1000;
		$where_changed = isset($where) ? $where : '';
		$where_id = isset($id) ? $id : '';
		$type_changes = isset($type) ? $type : '';
		$date_insert = date("Y-m-d H:i:s");
		$comment = isset($comment) ? $comment : '';
		
		if(!empty($who_changed) && !empty($where_changed)  
			&& !empty($where_id)  && !empty($type_changes)){
			$sql = "INSERT INTO ".$db->tables['changes']." 
				(`where_changed`, `where_id`, `who_changed`, 
				`type_changes`, `date_insert`, `comment`
				) VALUES('".$db->escape($where_changed)."', 
				'".$where_id."', '".$who_changed."', 
				'".$db->escape($type_changes)."', 
				'".$date_insert."', 
				'".$db->escape($comment)."'
				)
			";
			$db->query($sql);
		}
		return;
	}
	
	
	/* clear mysql cache folder */
	function clear_cache($r='1'){
		global $db, $path;
		$f = $path.'/'.ADMIN_FOLDER.'/compiled/mysql/';
		
		if($handle = opendir($f)){
			while(false !== ($file = readdir($handle))){
				if($file != "." 
					&& $file != ".." 
					&& $file != "index.html" 
					&& $file != "index.php" 
				) @unlink($f.$file);
			}
                
			closedir($handle);
		}
	  
		register_changes('clear_cache', '1000', 'mysql');
		if($r == 1){
			header("Location: ?action=db&clearcached=1");
			exit;
		}
		return;		
	}	


  /* ok */
  function drawmenu($menudata) {
    // упрощенный вариант без URL для пунктов меню
    $html = '';
    foreach ($menudata as $a) {
        extract($a);
        $html .= '<div style="padding-left: '.(($level-1)*20).'px;">';
        if ($active) $html.= "<strong>$title</strong>";
        else $html .= $title;
        $html .= '</div>';
    }
    return $html;
  }
  
  /* ok */
  function getmenu($tablename, $activeid, $shop=0,$parentid=0) {
    $parents = menuparents($tablename);
	if(empty($parents)){ return; }
    $chain = makechain($parents, $activeid);
    $level = 1;
    $list = build_hierarchy($parentid, $level, $chain, $parents);

	// Проверим есть ли выпавшие из привязки страницы
	$new_arr = array_flip($parents)+$parents;
	$arr = array_diff_key($new_arr, $list);
	if(isset($arr[0])){ unset($arr[0]);}

    $menudata = menudata($tablename, $list, $activeid, $shop);

	if(!empty($arr) && !isset($_GET['id'])){
		$new_arr = array();
		foreach($arr as $k => $v){
			if(!isset($new_arr[$k])){ $new_arr[$k] = -1; }
		}
		
		if(!empty($new_arr)){
			//$menudata += menudata($tablename, $new_arr, $activeid, $shop);
			if(empty($menudata)){ $menudata = array(); }
			$new_menu = menudata($tablename, $new_arr, $activeid, $shop);
			array_push($menudata, $new_menu);
		}
	}
    return $menudata;
  }

  /* ok */
  function menuparents($tablename) {
    global $db;
    $parents = array();
    $sql = "SELECT id, id_parent as parentid FROM $tablename ";
    $sql .= " ORDER BY `sort`, `title` ";
    $result = $db->get_results($sql);
    if(!$result || $db->num_rows == 0){ return;}
    foreach($result as $row){
        $parents[$row->id] = $row->parentid;
    }
    return $parents;
  }


  /* ok */
  function makechain($hierachy, $activeid) {
    $chain = array();
    if (!isset($hierachy[$activeid])) {
        return array();
    }

    $current = $activeid;
    do {
        $chain[] = $current;
        $current = $hierachy[$current]; 
    }
    while ($current); 
    
    return $chain;
  }

  /* ok */
  function build_hierarchy($parentid, $level, $chain, $hierarchy) {
    $list = array();
    $brothers = array_keys($hierarchy, $parentid);
    
    foreach ($brothers as $value) {
        $list[$value] = $level;
        
        /*
        Это для раскрытия только активного меню
        if (in_array($value, $chain)) {
            $children = build_hierarchy($value, $level+1, $chain, $hierarchy);
            $list = $list + $children;
        }
        */

        $children = build_hierarchy($value, $level+1, $chain, $hierarchy);
        $list = $list + $children;
    }
    return $list;
  }

  /* ok */
  function menudata($tablename, $list, $activeid, $shop=0) {
    $ids = array_keys($list); // вспомним, что id элементов хранятся в ключах массива
    $in = implode(',', $ids);
    global $db;
    $sql = "SELECT 
				c.id, c.title, c.id_parent, 
				c.active as visible, c.date_insert, 
                c.alias, c.shop, c.f_spec as starred, 
				
				(SELECT COUNT(*) 
					FROM ".$db->tables['pub_categs']."  p 
					WHERE p.id_categ = c.id AND p.id_pub >  '0' 
					AND p.`where_placed` = 'pub'
				) as pubs, 
				(SELECT COUNT(*) 
					FROM ".$db->tables['pub_categs']."  p2 
					WHERE p2.id_categ = c.id AND p2.id_pub >  '0' 
					AND p2.`where_placed` = 'product'
				) as products, 
				(SELECT COUNT(*) 
					FROM ".$db->tables['categs']."  
					WHERE id_parent = c.id
				) as subcategs,
				
				(SELECT count(*)  
					FROM ".$db->tables['site_info']." si2, 
					".$db->tables['site_categ']." sc2
					WHERE sc2.id_categ = c.id 
						AND sc2.id_site = si2.id 
				) as site_url_qty, 
				
				s.id as site_id, 
				
				(SELECT si2.site_url 
					FROM ".$db->tables['site_info']." si2, 
					".$db->tables['site_categ']." sc2
					WHERE sc2.id_categ = c.id 
						AND sc2.id_site = si2.id 
					LIMIT 0, 1
				) as site_url,
				
				(SELECT si2.id 
					FROM ".$db->tables['site_info']." si2, 
					".$db->tables['site_categ']." sc2
					WHERE sc2.id_categ = c.id 
						AND sc2.id_site = si2.id 
					LIMIT 0, 1
				) as site_idd,
				
				(SELECT si2.default_id_categ 
					FROM ".$db->tables['site_info']." si2, 
					".$db->tables['site_categ']." sc2
					WHERE sc2.id_categ = c.id 
						AND sc2.id_site = si2.id 
					LIMIT 0, 1
				) as default_id_categ
			
			FROM $tablename c 
			LEFT JOIN ".$db->tables['site_info']." s 
				on (s.default_id_categ = c.id) 
			WHERE c.id IN ($in) ";
    if($shop == 1) { $sql .= " AND c.shop='1' "; } 
    $result = $db->get_results($sql, ARRAY_A);
	//$db->debug();
	//exit;
    if(!$result || $db->num_rows == 0){ return;}
    $tempdata = array();
    foreach($result as $row){
        $id = $row['id'];
		$date_insert = $row['date_insert'];
        $level = $list[$id];
        $active = ($id == $activeid) ? TRUE : FALSE;
        $padding = ($level-1)*25; 
		$url = '';
		if(!empty($row['site_url'])){
			$url = $row['site_url'].'/'.$row['alias'].'/';
			if(empty($row['visible'])){ $url .= '?debug='.$row['site_idd']; }
		}
		
        $a = $row + compact('level', 'active', 'padding','date_insert','url');
        $tempdata[$row{'id'}] = $a;
    }
    foreach ($list as $key => $value) {
        if(isset($tempdata[$key])) $data[$key] = $tempdata[$key];
    }
	
    return $data;
  }


/* favourites */
/*
Типа записи - categ, pub, product, site, var, order, fb, block
?action=info&do=edit_categ&id=94
?action=info&do=edit_publication&id=125
?action=products&do=edit&id=52
?action=settings&do=site&mode=edit&id=5
?action=settings&do=site_vars&id=18
?action=orders&id=135
?action=feedback&id=198
?action=settings&do=blocks&id=5
*/





function _get_favorites(){
    global $db, $admin_vars;
    $userid = $admin_vars['bo_user']['id'];
    if(empty($userid)){ return; }
    $sql = "SELECT * FROM ".$db->tables['fav']." WHERE user_id = '".$userid."' ORDER BY `sort` ";

    $link_arr = array(
        'categ' => '?action=info&do=edit_categ&id=',
        'pub' => '?action=info&do=edit_publication&id=',
        'product' => '?action=products&do=edit&id=',
        'site' => '?action=settings&do=site&mode=edit&id=',
        'var' => '?action=settings&do=site_vars&id=',
        'order' => '?action=orders&id=',
        'fb' => '?action=feedback&id=', 
        'block' => '?action=settings&do=blocks&id=' 
    );

	$sql = "SELECT f.id, f.`sort`, f.where_placed as `where`, 
			f.title, f.where_id, f.comment, 
		
		(
		CASE
		WHEN f.where_placed = 'categ' 
			THEN (
					SELECT `title`  
					FROM ".$db->tables['categs']." 
					WHERE id = f.where_id
				)
		WHEN f.where_placed = 'pub'  
			THEN (
					SELECT `name` 
					FROM ".$db->tables['publications']." 
					WHERE id = f.where_id
				) 
		WHEN f.where_placed = 'product'  
			THEN (
					SELECT `name`  
					FROM ".$db->tables['products']." 
					WHERE id = f.where_id
				)  
		WHEN f.where_placed = 'site'  
			THEN (
					SELECT `name_short`   
					FROM ".$db->tables['site_info']." 
					WHERE id = f.where_id
				)  
		WHEN f.where_placed = 'var'  
			THEN (
					SELECT `name` 
					FROM ".$db->tables['site_vars']." 
					WHERE id = f.where_id
				)  
		WHEN f.where_placed = 'fb'  
			THEN (
					SELECT `ticket`  
					FROM ".$db->tables['feedback']." 
					WHERE id = f.where_id
				) 
		WHEN f.where_placed = 'block'  
			THEN (
					SELECT `title`  
					FROM ".$db->tables['blocks']." 
					WHERE id = f.where_id
				) 
		WHEN f.where_placed = 'order'  
			THEN (
					SELECT CONCAT(`site_id`,'-',`order_id`)
					FROM ".$db->tables['orders']." 
					WHERE id = f.where_id
				) 
		ELSE 'unknown' 
		END) as row_title, 

		(
		CASE
		WHEN f.where_placed = 'categ' 
			THEN (
					SELECT `id`  
					FROM ".$db->tables['categs']." 
					WHERE id = f.where_id
				)
		WHEN f.where_placed = 'pub'  
			THEN (
					SELECT `id` 
					FROM ".$db->tables['publications']." 
					WHERE id = f.where_id
				) 
		WHEN f.where_placed = 'product'  
			THEN (
					SELECT `id`  
					FROM ".$db->tables['products']." 
					WHERE id = f.where_id
				)  
		WHEN f.where_placed = 'site'  
			THEN (
					SELECT `id`   
					FROM ".$db->tables['site_info']." 
					WHERE id = f.where_id
				)  
		WHEN f.where_placed = 'var'  
			THEN (
					SELECT `id` 
					FROM ".$db->tables['site_vars']." 
					WHERE id = f.where_id
				)  
		WHEN f.where_placed = 'fb'  
			THEN (
					SELECT `id`  
					FROM ".$db->tables['feedback']." 
					WHERE id = f.where_id
				) 
		WHEN f.where_placed = 'block'  
			THEN (
					SELECT `id`  
					FROM ".$db->tables['blocks']." 
					WHERE id = f.where_id
				) 
		WHEN f.where_placed = 'order'  
			THEN (
					SELECT `id` 
					FROM ".$db->tables['orders']." 
					WHERE id = f.where_id
				) 
		ELSE '0' 
		END) as row_id, 

		(
		CASE
		WHEN f.where_placed = 'categ' 
			THEN (
					SELECT CONCAT('?action=info&do=edit_categ&id=',`id`)  
					FROM ".$db->tables['categs']." 
					WHERE id = f.where_id
				)
		WHEN f.where_placed = 'pub'  
			THEN (
					SELECT CONCAT('?action=info&do=edit_publication&id=',`id`) 
					FROM ".$db->tables['publications']." 
					WHERE id = f.where_id
				) 
		WHEN f.where_placed = 'product'  
			THEN (
					SELECT CONCAT('?action=products&do=edit&id=',`id`)  
					FROM ".$db->tables['products']." 
					WHERE id = f.where_id
				)  
		WHEN f.where_placed = 'site'  
			THEN (
					SELECT CONCAT('?action=settings&do=site&mode=edit&id=',`id`)   
					FROM ".$db->tables['site_info']." 
					WHERE id = f.where_id
				)  
		WHEN f.where_placed = 'var'  
			THEN (
					SELECT CONCAT('?action=settings&do=site_vars&id=',`id`) 
					FROM ".$db->tables['site_vars']." 
					WHERE id = f.where_id
				)  
		WHEN f.where_placed = 'fb'  
			THEN (
					SELECT CONCAT('?action=feedback&id=',`id`)  
					FROM ".$db->tables['feedback']." 
					WHERE id = f.where_id
				) 
		WHEN f.where_placed = 'block'  
			THEN (
					SELECT CONCAT('?action=settings&do=blocks&id=',`id`)  
					FROM ".$db->tables['blocks']." 
					WHERE id = f.where_id
				) 
		WHEN f.where_placed = 'order'  
			THEN (
					SELECT CONCAT('?action=orders&id=',`id`) 
					FROM ".$db->tables['orders']." 
					WHERE id = f.where_id
				) 
		ELSE '0' 
		END) as link, 

		(
		CASE
		WHEN f.where_placed = 'categ' 
			THEN (
					IF(
						(SELECT COUNT(*) 
							FROM ".$db->tables['site_categ']." 
							WHERE id_categ = f.where_id
						) = '1',
						(SELECT si.site_url 
							FROM ".$db->tables['site_categ']." sc, 
								".$db->tables['site_info']." si
							WHERE sc.id_categ = f.where_id AND  
								sc.id_site = si.id 
						), 
						''
					)					
				)
		WHEN f.where_placed = 'pub'  
			THEN (
					IF(
						(SELECT COUNT(*) 
							FROM ".$db->tables['site_publications']." 
							WHERE id_publications = f.where_id
						) = '1',
						(SELECT si.site_url 
							FROM ".$db->tables['site_publications']." sc, 
								".$db->tables['site_info']." si
							WHERE sc.id_publications = f.where_id AND  
								sc.id_site = si.id 
						), 
						''
					)
				) 
		WHEN f.where_placed = 'product'  
			THEN ('')  
		WHEN f.where_placed = 'site'  
			THEN (
				SELECT site_url 
				FROM ".$db->tables['site_info']." 
				WHERE id = f.where_id
			)  
		WHEN f.where_placed = 'var'  
			THEN (
					IF(
						(SELECT site_id 
							FROM ".$db->tables['site_vars']." 
							WHERE id = f.where_id
						) > '0',
						(SELECT si.site_url 
							FROM ".$db->tables['site_vars']." sc, 
								".$db->tables['site_info']." si
							WHERE sc.id = f.where_id AND  
								sc.site_id = si.id 
						), 
						''
					)
				)  
		WHEN f.where_placed = 'fb'  
			THEN (
					IF(
						(SELECT site_id 
							FROM ".$db->tables['feedback']." 
							WHERE id = f.where_id
						) > '0',
						(SELECT si.site_url 
							FROM ".$db->tables['feedback']." sc, 
								".$db->tables['site_info']." si
							WHERE sc.id = f.where_id AND  
								sc.site_id = si.id 
						), 
						''
					)
				) 
		WHEN f.where_placed = 'block'  
			THEN (
					IF(
						(SELECT COUNT(*) 
							FROM ".$db->tables['connections']." 
							WHERE id2 = f.where_id 
								AND name2 = 'block'
								AND name1 = 'site'
						) = '1',
						(SELECT si.site_url 
							FROM ".$db->tables['connections']." sc, 
								".$db->tables['site_info']." si
							WHERE sc.id2 = f.where_id 
								AND sc.name2 = 'block'
								AND sc.name1 = 'site'
								AND sc.id1 = si.id 
						), 
						''
					)	
				) 
		WHEN f.where_placed = 'order'  
			THEN (
					IF(
						(SELECT site_id 
							FROM ".$db->tables['orders']." 
							WHERE id = f.where_id
						) > '0',
						(SELECT si.site_url 
							FROM ".$db->tables['orders']." sc, 
								".$db->tables['site_info']." si
							WHERE sc.id = f.where_id AND  
								sc.site_id = si.id 
						), 
						''
					)
				) 
		ELSE '0' 
		END) as site, 
		
		(
		CASE
		WHEN f.where_placed = 'order' 
			THEN 
				(SELECT created 
					FROM ".$db->tables['orders']." 
					WHERE id = f.where_id
				)
		WHEN f.where_placed = 'fb' 
			THEN
				(SELECT `sent`  
					FROM ".$db->tables['feedback']." 
					WHERE id = f.where_id
				)
		ELSE '' 
		END) as date,
		
		(SELECT count(*) FROM ".$db->tables['users']." 
			WHERE `admin` = '1' AND `active` = '1') 
			as qty_admins,
		(SELECT count(*) 
			FROM ".$db->tables['fav']." f1, 
				".$db->tables['users']." u1
			WHERE f1.`where_placed` = f.where_placed 
				AND f1.`where_id` = f.where_id
				AND u1.id = f1.user_id 
				AND u1.admin = '1' 
				AND u1.active = '1'
		) as qty_added


		
        FROM ".$db->tables['fav']." f 
        WHERE f.user_id = '".$userid."' 
		ORDER BY f.`sort`, 
			f.`where_placed`, 
			row_title, f.title
	";

	
    $ar = $db->get_results($sql, ARRAY_A);
	//$db->debug(); exit;
	if(!$ar || $db->num_rows == 0){ return array(); }
    return $ar;
}


/* ok, newversion */
function update_pubs2pub($id, $str)
{
  global $db;
  $query = "DELETE FROM ".$db->tables['connections']." 
		WHERE name1 = 'pub' AND name2 = 'pub' AND (id1 = '".$id."' OR id2 = '".$id."')  ";
  $db->query($query);
  if(empty($str)){ return; }
  $produkti = explode(",",$str);

  if(!empty($produkti)){
    if(is_array($produkti) && count($produkti) > 0){
      foreach($produkti as $v){
          $query = "INSERT INTO ".$db->tables['connections']." (id1, id2, name1, name2)
            VALUES ('".$id."', '".$v."', 'pub', 'pub') ";
          $db->query($query);
      }
    }
  }
  return;
}



function add_favorites_tpl($ar, &$tpl){
  $where = isset($ar['where']) ? $ar['where'] : '';
  $where_id = isset($ar['id']) ? intval($ar['id']) : 0;

    /* массив с переадресацией */
    $arr = array(
        'categ' => '?action=info&do=edit_categ&id='.$where_id,
        'pub' => '?action=info&do=edit_publication&id='.$where_id,
        'product' => '?action=products&do=edit&id='.$where_id,
        'site' => '?action=settings&do=site&mode=edit&id='.$where_id,
        'var' => '?action=settings&do=site_vars&id='.$where_id,
        'order' => '?action=orders&id='.$where_id,
        'fb' => '?action=feedback&id='.$where_id,    
        'block' => '?action=settings&do=blocks&id='.$where_id,    
    );

  if(empty($where) || empty($where_id) || !isset($arr[$where])){ return; }
  $go = check_favourites($where, $where_id, $arr[$where]);
  $tpl->assign('fav_url', $arr[$where]);
  if(!empty($go)){
    $tpl->assign('fav', $go);
    return $tpl->fetch('pages/del_favorites.html');
  }else{
    return $tpl->fetch('pages/add_favorites.html');
  }
}

function check_favourites($where, $where_id, $redirect){
    global $db, $admin_vars;
    $userid = $admin_vars['bo_user']['id'];

    $del = isset($_GET['del_fav']) ? 1 : 0;
    if($del == 1){
        $db->query("DELETE FROM ".$db->tables['fav']." 
            WHERE `where_placed` = '".$where."' 
            AND `where_id` = '".$where_id."' 
            AND `user_id` = '".$userid."'
            ");
        header("Location: ".$redirect);
        exit;
    }
    
    $add = isset($_GET['add_fav']) ? 1 : 0;
    if($add == 1){
        $db->query("DELETE FROM ".$db->tables['fav']." 
            WHERE `where_placed` = '".$where."' 
            AND `where_id` = '".$where_id."' 
            AND `user_id` = '".$userid."'
            ");

        $db->query("INSERT INTO ".$db->tables['fav']." 
            (`where_placed`, `where_id`, `user_id`, `sort`) 
            VALUES ('".$where."', '".$where_id."', '".$userid."', '99') 
            ");
        header("Location: ".$redirect);
        exit;
    }
    $row = $db->get_row("SELECT *, where_placed as `where` FROM ".$db->tables['fav']." WHERE 
         `where_placed` = '".$where."' 
         AND `where_id` = '".$where_id."' 
         AND `user_id` = '".$userid."'
    ", ARRAY_A);
    if($db->num_rows == 0 || !$row){ return 0; }
    return $row;
}

// ok
function _really($mes = '')
{
  if(empty($mes)) $mes = GetMessage('admin', 'delete');
    return "\"onclick=\"if(confirm('".sprintf(GetMessage('admin', 'really'), $mes)."')) return true; else return false;\"";
}

// ok
function _updated($str) // formatting str
{
  $str = '<div style="color:red;"><p>'.$str.'</p></div>';
  return $str;
}

/* ok */
function _qty($where)
{
  global $db, $lang;
  if($where == "products"){
    $query = "SELECT count(*) FROM ".$db->tables['products']." ";
  }elseif($where == "categs"){
    $query = "SELECT count(*) FROM ".$db->tables['categs']." ";
  }elseif($where == "pubs"){
    $query = "SELECT count(*) FROM ".$db->tables['publications']." ";
  }elseif($where == "users"){
    $query = "SELECT count(*) FROM ".$db->tables['users']." ";
  }elseif($where == "admins"){
    $query = "SELECT count(*) FROM ".$db->tables['users']." WHERE admin = '1' ";
  }elseif($where == "site"){
    $query = "SELECT count(*) FROM ".$db->tables['site_info']." ";
  }else{ return; }
  return $db->get_var($query);
}


// ok
function check_ip($of)
{
    if(empty($of)){ return true; }
    $current_ip = $_SERVER["REMOTE_ADDR"];
    $newof = explode(",",$of);
    if(!is_array($newof)){ $of = array($newof); } else { $of = $newof; }
    if(count($of) > 0){
        if(in_array($current_ip, $of)){ return true; } else { return false; }
    }else{ return true; }
}

// ok last modified 04/04/2018 sort by connected category
function all_sites()
{
	global $db;
	$query = "SELECT s.id, s.name_short, s.site_url, 
				s.site_active, s.default_id_categ 
			FROM ".$db->tables['site_info']." s 
			LEFT JOIN ".$db->tables['categs']." c ON (s.default_id_categ = c.id) 
			ORDER BY c.sort 			
	"; 
	$rows = $db->get_results($query, ARRAY_A);
	if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }

	if(!$rows || $db->num_rows == 0){ return array(); }
	return $rows;
}       

/* ok */
function slug_exists($str, $where, $id)
{
  global $db;
  $str1 = "";
  if(function_exists('reserved_slugs')){
    $ar_reserved = reserved_slugs();
  }else{
    $ar_reserved = array( 'feedback', 'contact', 'category', 
                        'product', 'pub', 'sitemap', 'map', 
                        'search', 'user', 'basket', 'order' );
  }
  
  if(in_array($str, $ar_reserved)){ return true; }

  if($where == "categ"){ 
    $str1 = " AND id <> '".$id."' "; 
  }  
  $result = $db->get_row("SELECT * FROM ".$db->tables["categs"]." 
    WHERE alias = '".$str."' $str1 ", ARRAY_N);
  if($db->num_rows > 0){ return true; }
  
  if($where == "pub"){ 
    $str1 = " AND id <> '".$id."' "; 
  }
  $result = $db->get_row("SELECT * FROM ".$db->tables["publications"]." 
    WHERE alias = '".$str."' $str1 ", ARRAY_N);
  if($db->num_rows > 0){ return true; }
      
  if($where == "product"){ 
    $str1 = "AND id <> '".$id."' "; 
  }
  $result = $db->get_row("SELECT * FROM ".$db->tables["products"]." 
    WHERE alias = '".$str."' $str1 ", ARRAY_N);
  if($db->num_rows > 0){
    return true; 
  }
  return false;

}


/* ok */
/* получим массив с доступными вариантами для переменной */
function get_array_vars($var_name){
  global $db, $site_vars;
  $values = $db->get_var("SELECT `if_enum` FROM ".$db->tables['site_vars']." WHERE `name` = '".$var_name."' ");      
	$values = str_replace("\r\n","\n",$values);
	$values = explode("\n",$values);
	return $values;
}


// ok, builds slug, supports en/text-of-slug  
if(!function_exists('build_slug')){
	function build_slug($str, $where, $id)
	{	
		$str = trim(mb_strtolower($str, 'UTF-8'));
		if(empty($str)){ return time(); }
		$str = preg_replace('/[^a-zа-яёA-ZА-ЯЁ0-9ÓóÑñÍíÉéÚúÁáüÜ\/\-\_\s]+/iu', '', $str);
		$str = preg_replace('/ {2,}/',' ',$str);
		$str = str_replace(' ','-',$str);
		$str = strtolower(rus2trans($str));

		if($str{0} == '/'){ $str = substr($str, 1); }
		if(substr($str, -1) == '/'){ $str = substr($str, 0, -1); }
		//$str = str_replace('/','',$str);

		if(slug_exists($str, $where, $id)){
			return $str.'-'.time();
		}else{ return $str; }

		return;
	}
}

############## CLASS ADMIN_LANG ###############################
class Admin_Lang {
	var $txt;
	var $default = 'en';

	function init_lang(){
     global $site, $currentlang;
	   $lang_default = $this->file($this->default);
	   $fn = $currentlang;
	   $lang_current = $this->file($fn);
	   if(file_exists($lang_current)){
	     require($lang_current);
	     $this->txt = $lang;
     }else{
       require($lang_default);
	     $this->txt = $lang;
     }
	}

	function file($fn){
    global $tpl;
    $fn = $tpl->template_dir. "/lang.php";
    //$fn = "tpl/" . $fn . "/lang.php";
    return $fn;
  }

  function txt($str){

    if(!isset($this->txt[$str])){
       return $str;
	     $this->txt = $lang;
    }else{
       return $this->txt[$str];
    }

    if(!isset($this->txt[$str])){
       return $str;
    }else{
      return $this->txt[$str];
    }
  }
}

// ok
function non_auth_footer()
{
  global $site, $tpl;
  $str = "";
  //$str = $tpl->fetch("footer_menu.html");
  return $str;
}


// ok, newversion updates title for uploaded fotos
// обновляет id записи, тип записи и id_in_record
function update_picture_title_records($id,$posted)
{                                                 
  global $db;                      
 
  if(empty($posted["update_pics_title"])){ return; }
  if(empty($posted["record_type"])){ return; }
  foreach($posted["update_pics_title"] as $k => $v){
    $db->query("UPDATE ".$db->tables["uploaded_pics"]." 
        SET 
          `title` = '".$db->escape($v)."' 
        WHERE id_in_record = '".$k."' 
          AND record_id = '".$id."' 
          AND record_type = '".$db->escape($posted["record_type"])."' 
    ");                                             

  }     
  
  /* обновим доп.поля описания */
	  if(!empty($_POST["img_ext_h1"])){
        foreach($_POST["img_ext_h1"] as $k => $v){
          $db->query("UPDATE ".$db->tables["uploaded_pics"]." 
              SET `ext_h1` = '".$db->escape(trim($v))."' 
              WHERE id_in_record = '".intval($k)."' 
			  AND record_id = '".$id."' 
			  AND record_type = '".$db->escape($posted["record_type"])."' ");
          //if(!empty($db->last_error)){ return db_error(basename(__FILE__).": 834"); }      
        }
      }
	  if(!empty($_POST["img_ext_desc"])){
        foreach($_POST["img_ext_desc"] as $k => $v){
          $db->query("UPDATE ".$db->tables["uploaded_pics"]." 
              SET `ext_desc` = '".$db->escape(trim($v))."' 
              WHERE id_in_record = '".intval($k)."' 
			  AND record_id = '".$id."' 
			  AND record_type = '".$db->escape($posted["record_type"])."' ");
          //if(!empty($db->last_error)){ return db_error(basename(__FILE__).": 834"); }      
        }
      }
	  if(!empty($_POST["img_ext_link"])){
        foreach($_POST["img_ext_link"] as $k => $v){
          $db->query("UPDATE ".$db->tables["uploaded_pics"]." 
              SET `ext_link` = '".$db->escape(trim($v))."' 
              WHERE id_in_record = '".intval($k)."' 
			  AND record_id = '".$id."' 
			  AND record_type = '".$db->escape($posted["record_type"])."' ");
          //if(!empty($db->last_error)){ return db_error(basename(__FILE__).": 834"); }      
        }
      }
  
  
  return;
}



/* ok for new version */
/* updates exists files, re-sorting, re-naming, deleting it */
function update_files($id,$posted)
{
  global $db;
  if(empty($posted["record_type"])) { return; }
  $record_type = $posted["record_type"];
  $update_titles = !empty($posted["update_files_title"]) ? $posted["update_files_title"] : array();
  $delete_files = !empty($posted["delete_files"]) ? $posted["delete_files"] : array();
  $allow_download = !empty($posted["update_allow_download"]) ? $posted["update_allow_download"] : array();
  $direct_link = !empty($posted["update_direct_link"]) ? $posted["update_direct_link"] : array();
  
  if(count($delete_files) > 0){
    foreach($delete_files as $v){
      if(isset($update_titles[$v])){ unset($update_titles[$v]); }
      delete_uploaded_files($id, $record_type, $v);   
    }
  }

  $i = 1;
  if(count($update_titles) > 0){
    foreach($update_titles as $k=>$v){
		$d = !empty($allow_download[$k]) ? 1 : 0;
		$d_link = !empty($direct_link[$k]) ? 1 : 0;
      $db->query("UPDATE ".$db->tables["uploaded_files"]." 
          SET 
            `id_in_record` = '".$i."',
			`allow_download` = '".$d."',
			`direct_link` = '".$d_link."',
            `title` = '".$db->escape($v)."'
          WHERE
            `id` = '".$k."' ");      
      $i++;
    }
  }
  return;
}

// used in new version
function delete_group_pics($id, $posted)
{
  global $db;
  if(!empty($posted["delete_pics"]) && !empty($posted["record_type"])){
    foreach($posted["delete_pics"] as $v){
      // найдем все записи с очередью $v
      $rows = $db->get_results("SELECT * FROM ".$db->tables["uploaded_pics"]." 
        WHERE record_id = '".$id."' AND record_type = '".$posted["record_type"]."' 
          AND id_in_record = '".$v."' ");
      if($db->num_rows >0){
        foreach($rows as $row){
          delete_uploaded_pics($id, $posted["record_type"], $row->id, true);  
        }                           
      }
    }
    
        // Изменим порядок сортировки изображений
        $rows = $db->get_results("SELECT * FROM ".$db->tables["uploaded_pics"]." 
            WHERE record_id = '".$id."' AND record_type = '".$posted["record_type"]."'       
            ORDER BY is_default DESC, id_in_record, width DESC ");
        if($db->num_rows == 0){return;}
      
        foreach($rows as $key=>$row){
          if($key == 0){ $i = 1; }
          if(isset($rows[$key-1]->id_in_record)){
            if($row->id_in_record != $rows[$key-1]->id_in_record){ $i = $i+1; }
          }
          $query = "UPDATE ".$db->tables["uploaded_pics"]." 
            SET id_in_record = '".$i."' WHERE id = '".$row->id."' ";
          $db->query($query);
        }
        
        $db->query("UPDATE ".$db->tables["uploaded_pics"]." 
          SET is_default = '1'
          WHERE id_in_record = '1' 
            AND record_id = '".$id."' AND record_type = '".$posted["record_type"]."' ");    
    
  }
  return;
}

// used in new version
function delete_uploaded_pics($record_id, $record_type, $id = false, $delete_db = false){
  global $db;
  if(!$id){
    $query = "SELECT * FROM ".$db->tables["uploaded_pics"]." 
      WHERE record_id = '".$record_id."' AND record_type = '".$record_type."' ";
  }else{
    $query = "SELECT * FROM ".$db->tables["uploaded_pics"]." 
      WHERE record_id = '".$record_id."' AND record_type = '".$record_type."' 
      AND id = '".$id."' ";
  }
  $rows = $db->get_results($query);

  if($db->num_rows == 0){ return; }
  foreach($rows as $row){
    $path = UPLOAD."/records/".$row->id.".".$row->ext;
    if(file_exists($path)){
      @unlink($path);
    }

	if($delete_db){ 
        $db->query("DELETE FROM ".$db->tables["uploaded_pics"]." 
		WHERE id = '".$row->id."' ");
    }  

  }    
  return;
}


// used in new version
function delete_uploaded_files($record_id, $record_type, $id = false){                     
	global $db;
  if(!$id){
    $query = "SELECT * FROM ".$db->tables["uploaded_files"]." 
      WHERE record_id = '".$record_id."' AND record_type = '".$record_type."' ";
  }else{
    $query = "SELECT * FROM ".$db->tables["uploaded_files"]." 
      WHERE record_id = '".$record_id."' AND record_type = '".$record_type."' 
      AND id = '".$id."' ";
  }
  $rows = $db->get_results($query);
  if($db->num_rows == 0){ return; }
  foreach($rows as $row){
    $path = "../upload/files/".md5($row->id).".".$row->ext;
	
    if(file_exists($path)){
      @unlink($path);
    }
    $db->query("DELETE FROM ".$db->tables["uploaded_files"]." WHERE id = '".$row->id."' ");
  }
  return;
}

function delete_comment($id){
	global $db;
	$row = $db->get_row("SELECT * 
		FROM ".$db->tables['comments']." 
		WHERE id = '".$id."'
	");
	if($row){		
		if($row->record_type == 'order'){
			delete_uploaded_files($id, 'order_comment', false);
			delete_uploaded_pics($id, 'order_comment', false, true);
		}
		
		delete_uploaded_files($id, 'comment', false);
		delete_uploaded_pics($id, 'comment', false, true);
		
		$db->query("DELETE FROM ".$db->tables['comments']." 
			WHERE id = '".$id."' ");
	}
	return;	
}

/* ok */
function delete_comments($id, $where){
  global $db;
  $rows = $db->get_results("SELECT * FROM ".$db->tables["comments"]." 
        WHERE record_id = '".$id."' AND record_type = '".$where."' ");

  if($db->num_rows > 0){
    foreach($rows as $row){
		delete_comment($row->id);      
    }
  }        
  return;
}



if(!function_exists('file_not_found')){

function file_not_found($cur_script, $lang="ru")
{
  global $lang;
  $str = "<p>".GetMessage('admin', 'file_not_found')."</p>";
  $str = sprintf($str, $cur_script);
  return $str;
}

}







/* OK, new version called from admin */
if(!function_exists('_pages')){     
  function _pages($all_results, $page, $onpage, $admin=true)
  {
    global $tpl;
    if($all_results <= $onpage && $page < 1){ return; }
    foreach($_GET as $k => $v){
      if($k != "page" && !is_array($v)){  
        $link = isset($link) ? $link."&" : "?";
        $link .= $k."=".$v;
      }elseif(is_array($v)){		  
		  foreach($v as $k1 => $v1){
			  $link = isset($link) ? $link."&" : "?";
			  $link .= $k.'['.$k1.']='.$v1;
		  }
	  }                      
    }
    if($link != "") $link .= "&page=";
	$link = str_replace('&deleted=1','',$link);
	$link = str_replace('&updated=1','',$link);
    $tpl->assign("href",$link);
    $tpl->assign("npages",ceil($all_results/$onpage));
    $tpl->assign("page",$page);
    return $tpl->fetch("pages/pages.html");
  }
}




if(!function_exists('Resource_Page')){

function Resource_Page($filename)

{

  $urla = TEMPLATE_PATH.$filename;

  $urlb = TEMPLATE_PATH_DEFAULT.$filename;



  if(file_exists($urla)){

    $str = file_get_contents(urldecode($urla));

  }else{

    if(file_exists($urlb)){

      $str = file_get_contents($urlb);

    }else{

      $str = site_fatal_error($filename." not found");

    }

  }

  return $str;

}

}




/* ok */
function site_form_for_pubs_tpl($ar,&$tpl)
{
    if(!isset($ar[2])){ $ar[2] = false; }
    if(!isset($ar[3])){ $ar[3] = ' '; }
	return site_form_for_pubs($ar[0], $ar[1], $ar[2], $ar[3]);
}

// ok
function site_form_for_pubs($categ, $tab, $solo=false, $sep=' ') // site_categ or site_pubs
// $solo - только ссылки на активные разделы
{
  global $db;
  $id = isset($categ['id']) ? $categ['id'] : $categ;
  $all_sites = all_sites();
//$solo = true;
  if(empty($id)) { $id = 0;}
  if(count($all_sites) < 1) { return "<p>Not found</p>"; }
  if($tab == "site_categ"){
    $query = "SELECT id_site FROM ".$db->tables["site_categ"]."
              WHERE id_categ = '".$id."' ";
  }elseif($tab == "site_pubs"){
    $query = "SELECT id_site FROM ".$db->tables["site_publications"]."
              WHERE id_publications = '".$id."' ";
  }else{
    return "--- 304 ---";
  }
  $results = $db->get_results($query, ARRAY_N);
//$db->debug(); $exit;
  if(!is_array($results)){ $results = array(); }
  $ar_categs = array();
  foreach($results as $row){
    $ar_categs[] = $row[0];
  }
  $str = "";
  foreach($all_sites as $k => $v){
    if(in_array($v['id'], $ar_categs)){ 
      $ch = "checked";
    } else { 
      if(empty($results) && $id == 0) { 
        $ch = "checked";
      } else { 
        $ch = "";
      } 
    }

		if(!empty($solo) && !empty($ch)){
			$href = $v['default_id_categ']  == $id && $tab == "site_categ" 
				? $v['site_url'].'/'
				: $v['site_url'].'/'.$solo.URL_END;
			if(empty($categ['active']) && $tab == "site_categ" && $v['default_id_categ'] != $id){
				$href .= '?debug='.$v['id'];
			}elseif(empty($categ['active']) && $tab != "site_categ"){
				$href .= '?debug='.$v['id'];
			}
			
			if($v['default_id_categ'] == $id && $tab == "site_categ"){
				$str .= '<i class="fa fa-home"></i>';
			}
			
		  if(isset($_GET['options'])){
			$str .= ' <a href="'.$href.'" target="_blank"><i class="fa fa-external-link"></i></a> '.$sep;  
		  }else{
			if(!empty($str)){
				$str .= $sep;
			}
			$str .= ' <a href="'.$href.'" target="_blank">'.$v['site_url'].'</a> ';  
		  }
          
      }else{
        if(empty($solo)){
          $str .= "<input type=checkbox name='".$tab."[]' value='".$v['id']."' $ch>".$v['id'].": <a href='?action=settings&do=site&mode=edit&id=".$v['id']."' style='margin-right: 20px;'>".$v['site_url']."</a> ".$sep;
        }
      }
    
  }
  if(empty($str)){ $str = '-';}
  return $str;
}


/* ok */
function _sites4pub_tpl($ar,&$tpl)
{
 	return _sites4pub($ar['id']);
}

/* ok */
function pub_in_categs_tpl($ar,&$tpl)
{
 	return pub_in_categs($ar['id']);
}

/* ok */
function pub_to_products_tpl($ar,&$tpl)
{
 	return pub_to_products($ar[0],$ar[1]);
}


/* ok */
/*--------------------Language init-----------------------*/
function setLanguage($default /*Default language*/) 
{
	global $admin_vars;
	if(isset($_GET['setlang'])) {
		$lang = addslashes($_GET['setlang']);
		@setcookie('lang', $lang, time() + 2678400, '/');
		$url = isset($_SERVER['HTTP_REFERER']) 
			? $_SERVER['HTTP_REFERER'] : '/'.ADMIN_FOLDER.'/';
		header('location: ' . $url);
		exit;
	}elseif(isset($_COOKIE['lang'])) {
		$lang = addslashes($_COOKIE['lang']);
		@setcookie('lang', $lang, time() + 2678400, '/');
	}else{
		$lang = $default;
		@setcookie('lang', $lang, time() + 2678400, '/');
	}
	return $lang;
}




function load_currencies($ar){
	$ar['kurs_rur'] = 1;
	if(!empty($ar['sys_autoload_rate'])){
			global $db;
			$sql = "SELECT 
					`type_changes` as usd_rate,
					`date_insert` as usd_date, 
					(SELECT `type_changes` 
					FROM ".$db->tables['changes']." 
					WHERE `where_changed` = 'kurs_euro' 
					ORDER BY date_insert DESC 
					LIMIT 0, 1)  as euro_rate,
					(SELECT  `date_insert`  
					FROM ".$db->tables['changes']." 
					WHERE `where_changed` = 'kurs_euro' 
					ORDER BY date_insert DESC 
					LIMIT 0, 1
					) as euro_date
				FROM ".$db->tables['changes']." 
				WHERE `where_changed` = 'kurs_usd' 
				ORDER BY date_insert DESC 
				LIMIT 0, 1
			";
			$row = $db->get_row($sql, ARRAY_A);
			if($row && $db->num_rows > 0 
				&& !empty($row['usd_date'])
			){
				/* найден, проверим дату */
				/* если дата позже 13-00 след.дня
				то закажем новый курс
				*/	
				$d = array(
					'USD' => $row['usd_rate'],
					'EUR' => $row['euro_rate']
				);
				
				$ar['kurs_usd'] = $d['USD'];
				$ar['kurs_euro'] = $d['EUR'];
				$ar['kurs_date'] = $row['usd_date'];
			}
			
			
	}
		
	if(isset($ar['kurs_rur']) && !defined('SI_RATE_RUR')){
		define('SI_RATE_RUR', $ar['kurs_rur']);
	}
		
	if(isset($ar['kurs_usd']) && !defined('SI_RATE_USD')){
		define('SI_RATE_USD', $ar['kurs_usd']);
	}
		
	if(isset($ar['kurs_euro']) && !defined('SI_RATE_EURO')){
		define('SI_RATE_EURO', $ar['kurs_euro']);
	}
	
	if(isset($ar['kurs_date']) && !defined('SI_RATE_DATE')){
		define('SI_RATE_DATE', $ar['kurs_date']);
	}
	return $ar;
}

if(!function_exists('load_siteid_by_url')){
function load_siteid_by_url()
{
  $url = $_SERVER["HTTP_HOST"];
  $url_ = 'www.'.$url;
  $url = "http://".$url;
  $url_ = "http://".$url_;
  global $db;
  $query = "SELECT * FROM ".$db->tables["site_info"]."
            WHERE site_url = '".$url."' 
			OR site_url = '".$url_."' ";
  $result = $db->get_row($query, ARRAY_A);
  if(!empty($db->last_error)){ return false; }
  if($db->num_rows == 0) { return false; }

  define("SITE_ID", $result['id']);
  global $site_vars;
  $site_vars = $result;
  // $site_vars = get_object_vars($result);
  return;
}
}


// ok
if(!function_exists('load_siteid_by_default')){
  function load_siteid_by_default()
  {
	global $db;
    $query = "SELECT * FROM ".$db->tables["site_info"]." order by id ";
    $result = $db->get_row($query);	
	if(!empty($db->last_error)){ return false; }
    if($db->num_rows == 0) { return false; }
    $id = $result->id;
    define("SITE_ID", $id);
    return;
  }
}

// ok
if(!function_exists('logout')){
  function logout()
  {
    if(isset($_SESSION["BO_USERID"])){
      $_SESSION = array();
      $GLOBALS = array();
      wp_clearusercookie();
      if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
      }

      // Finally, destroy the session.
      session_destroy();
      return _redirect('./');
    }else{
      return _redirect('./');
    }
  }
}

// ok
if(!function_exists('wp_clearusercookie')){
  function wp_clearusercookie() {
    if(defined("EMAIL_COOKIE") &&
      defined("PASSWD_COOKIE") && 
      defined("EMAIL_COOKIE") && 
      defined("PASSWD_COOKIE") && 
      defined("COOKIEPATH") && 
      defined("SITECOOKIEPATH") && 
      defined("COOKIE_DOMAIN"))
    {
      setcookie("EMAIL_COOKIE", ' ', time() - 31536000, "COOKIEPATH", "COOKIE_DOMAIN");
      setcookie("PASSWD_COOKIE", ' ', time() - 31536000, "COOKIEPATH", "COOKIE_DOMAIN");
      setcookie("EMAIL_COOKIE", ' ', time() - 31536000, "SITECOOKIEPATH", "COOKIE_DOMAIN");
      setcookie("PASSWD_COOKIE", ' ', time() - 31536000, "SITECOOKIEPATH", "COOKIE_DOMAIN");
    }
    return;
  }
}

/* ok for new version 09.08.2015 */
if(!function_exists('error_404')){
  function error_404($str = ''){
    if (!headers_sent()) {
      header("HTTP/1.1 404 Not Found");
    }

	global $tpl;
	if(!empty($str)) { $tpl->assign('error', $str); }
	$tpl->display("404.html");
	exit;
  }
}



/* ok */
function upload_files_by_path($id, $type, $file){
	global $db;
	if(!empty($file['size']) || !empty($file['tmp_name']) 
		|| !empty($file['name']) || !empty($file['ext'])
	)
	
	$ext = $file['ext'];
	$allow_download = !empty($file['allow_download']) ? 1 : 0;
	$direct_link = !empty($file['direct_link']) ? 1 : 0;
		
	$id_in_record = $db->get_var("SELECT id_in_record 
		FROM ".$db->tables["uploaded_files"]." 
		WHERE `record_id` = '".$id."' AND `record_type` = '".$type."' 
		ORDER BY id_in_record desc ");
	$id_in_record = !empty($id_in_record) && $id_in_record > 0 
		? $id_in_record+1 : 1;
	
	$db->query("INSERT INTO ".$db->tables["uploaded_files"]." 
          (`id_exists`, `record_id`, `record_type`, `size`, 
		  `filename`, `title`, `ext`, `allow_download`, 
		  `direct_link`, id_in_record, time_added) 
          VALUES('0', '".$id."', 
          '".$type."', '".$file['size']."', 
          '".$db->escape($file['name'])."', '".$db->escape($file['name'])."', 
          '".$ext."', '".$allow_download."', '".$direct_link."',
          '".$id_in_record."', '".time()."') ");

        $file_id = $db->insert_id;
        $uploaddir = "../upload/files/".md5($file_id).".$ext";
        if(@!copy($file['tmp_name'],$uploaddir)){
          die("File can't be written, wrong path in $uploaddir");
        }
	return;
}

/* ok for new version, func uploads files */
function upload_files($id, $type='', $file='')
{         
	if(!empty($type) && !empty($file)){
		return upload_files_by_path($id, $type, $file);
	}
	global $db, $site, $site_vars;
	if(!empty($type)){
		$record_type = $type;
	}else{
		if(empty($_POST["record_type"])) return;
		$record_type = isset($_POST["record_type"]) ? trim($_POST["record_type"]) : "";
		if(empty($record_type)) return;
	}
	
  
  if(empty($_FILES["files"]["name"][0])) return;
	

  if(!empty($site_vars['sys_upload_ext_allowed'])){
    $allowed = explode(",", $site_vars['sys_upload_ext_allowed']);
  }

  $id_in_record = $db->get_var("SELECT id_in_record FROM ".$db->tables["uploaded_files"]." 
      WHERE `record_id` = '".$id."' AND `record_type` = '".$record_type."' 
      ORDER BY id_in_record desc ");
  $id_in_record = !empty($id_in_record) && $id_in_record > 0 ? $id_in_record+1 : 1;

  
  foreach($_FILES["files"]["name"] as $k=>$v){
    // $k - id
	$value = explode('.', $v);
    $ext = strtolower(array_pop($value));

    if(!empty($ext) && (!isset($allowed) || $allowed[0] == "*" || in_array($ext, $allowed))){
      if($_FILES["files"]["size"][$k] > 0 && is_uploaded_file($_FILES['files']['tmp_name'][$k])){
        $title = !empty($_POST["file_title"][$k]) ? trim($_POST["file_title"][$k]) : $v;
        $allow_download = !empty($_POST["allow_download"][$k]) ? intval($_POST["allow_download"][$k]) : 0;
		if($type == 'order_comment' && !empty($_POST["send_comment"]["active"])){ 
			$allow_download = 1; 
		}
        $direct_link = !empty($_POST["direct_link"][$k]) ? intval($_POST["direct_link"][$k]) : 0;
		
        $db->query("INSERT INTO ".$db->tables["uploaded_files"]." 
          (`id_exists`, `record_id`, `record_type`, `size`, `filename`, 
          `title`, `ext`, `allow_download`, `direct_link`, id_in_record, time_added) 
          VALUES('0', '".$id."', 
          '".$record_type."', '".$_FILES["files"]["size"][$k]."', 
          '".$db->escape($v)."', '".$db->escape($title)."', 
          '".$ext."', '".$allow_download."', '".$direct_link."',
          '".$id_in_record."', '".time()."') ");

        $file_id = $db->insert_id;
        $uploaddir = "../upload/files/".md5($file_id).".$ext";
        if(@!copy($_FILES["files"]["tmp_name"][$k],$uploaddir)){
          die("File can't be written, wrong path in $uploaddir");
        } 
        $id_in_record++;
      }
    }
  }
	return;
}

/* ok */
/* сохраняем характеристики у товаров и публикаций */
function update_options($id, $where){
	global $db;
	if(empty($_POST['option']) && empty($_POST['option_u'])){ 
		return; 
	}
	// phpinfo(); exit;
	$skip = '';
	if(!empty($_POST['option_u'])){
		$ar = array_keys($_POST['option_u']);
		$skip = "'".implode("','", $ar)."'";
		
		foreach($_POST['option_u'] as $k=>$v){
			$val = isset($v['value']) ? $v['value'] : '';
			
			if(is_array($val)){
				
				if(isset($val['D']) && isset($val['M']) 
							&& isset($val['Y']) 
							&& isset($val['H']) 
							&& isset($val['i']))
				{
					$val = $val['Y'].'-'.$val['M'].'-'.$val['D'].' '.$val['H'].':'.$val['i'];
				}elseif(isset($val['D']) && isset($val['M']) 
					&& isset($val['Y']))
				{
					$val = $val['Y'].'-'.$val['M'].'-'.$val['D'];
				}else{
					$val = implode("\n", $val);
				}
				//echo $val; exit;
			}
			
			$val = trim($val);
			$val2 = isset($v['value2']) ? trim($v['value2']) : '';
			$val3 = isset($v['value3']) ? trim($v['value3']) : '';
			
			$sql = "UPDATE ".$db->tables['option_values']." SET 
				`value` = '".$db->escape($val)."',
				`value2` = '".$db->escape($val2)."',
				`value3` = '".$db->escape($val3)."'
				
				WHERE id = '".$k."'
			";
			$db->query($sql);
		}
	}

	$sql = "DELETE FROM ".$db->tables['option_values']." 
			WHERE id_product = '".$id."' 
				AND `where_placed` = '".$db->escape($where)."' 
	";
	if(!empty($skip)){ $sql .= " AND `id` NOT IN (".$skip.") "; }
	$db->query($sql);

	  if(!empty($_POST['option'])){
		  $keys_ar = array();
		  foreach($_POST['option'] as $k => $v){
			  $keys = empty($keys) ? "'".$k."'" : $keys.", '".$k."'";
		  }
		  $keys_rows = $db->get_results("SELECT * FROM ".$db->tables['options']." 
			WHERE id IN (".$keys.") AND `type` = 'int' ");
			if($keys_rows && $db->num_rows > 0){
				foreach($keys_rows as $k1 => $k2){
					$keys_ar[] = $k2->id;
				}
			}
	

	
    foreach($_POST['option'] as $k => $v){
      if(is_array($v)){        
	
		if(!empty($v['city_id']) || !empty($v['region_id']) || !empty($v['country_id'])){
          // здесь найдем номер строки в переменной  $k
          $str = $db->get_var("SELECT `if_select` FROM `options` WHERE id = '".$k."' ");
          $values = str_replace("\r\n","\n",$str);
          $values = explode("\n",$values);
          $ar = $values;
          if(!isset($v['city_id'])){ $v['city_id'] = 0; }
          if(!isset($v['region_id'])){ $v['region_id'] = 0; }
          if(!isset($v['country_id'])){ $v['country_id'] = 0; }
          
          foreach($values as $v1=>$v2){
            $v2 = trim($v2);
            $first2 = mb_substr(trim($v2), 0, 2, 'utf-8');
            $first1 = mb_substr(trim($v2), 0, 1, 'utf-8');
            if($first2 == '--'){ // добавляем в массив третьего уровня
              $v2 = str_replace('--', '', $v2);
            }
            
            if($first1 == '-'){ // добавляет в массив второго уровня
		          $v2 = str_replace('-', '', $v2);
            }

            $ar[$v1+1] = trim($v2);
          }         
          
          if($v['city_id'] > 0){
			if(isset($ar[$v['country_id']])){
				$value2 = $ar[$v['country_id']];
			}
			
			if(isset($ar[$v['region_id']])){
				$value3 = $ar[$v['region_id']];
			}
            $v = isset($ar[$v['city_id']]) ? $ar[$v['city_id']] : "";
          }elseif($v['region_id'] > 0){
			if(isset($ar[$v['country_id']])){
				$value2 = $ar[$v['country_id']];
			}
			if(isset($ar[$v['region_id']])){
				$value3 = $ar[$v['region_id']];
			}
            $v = isset($ar[$v['region_id']]) ? $ar[$v['region_id']] : "";
          }elseif($v['country_id'] > 0){
			if(isset($ar[$v['country_id']])){
				$value2 = $ar[$v['country_id']];
			}
            $v = isset($ar[$v['country_id']]) ? $ar[$v['country_id']] : "";
          }else{
            $v = "";
          }
        }elseif(isset($v['old'])){
          $value2 = isset($v['old_value2']) ? $v['old_value2'] : '';
          $value3 = isset($v['old_value3']) ? trim($v['old_value3']) : "";
          $v = isset($v['old']) ? trim($v['old']) : "";
		}elseif(
			is_array($v['value']) && 
			isset($v['value']['D']) && 
			isset($v['value']['M']) && 
			isset($v['value']['Y']) && 
			isset($v['value']['H']) && 
			isset($v['value']['i']) 
		){
			$v = $v['value']['Y'].'-'.$v['value']['M'].'-'.$v['value']['D'].' '.$v['value']['H'].':'.$v['value']['i'];
		}elseif(
			is_array($v['value']) && 
			isset($v['value']['D']) && 
			isset($v['value']['M']) && 
			isset($v['value']['Y'])
		){
			$v = $v['value']['Y'].'-'.$v['value']['M'].'-'.$v['value']['D'];
        }else{
          // Просто создадим переменную разбив заданные варианты с новой строки
		  
          if(count($v) > 0 && !isset($v['value'])){
            $str = "";
            foreach($v as $k1=>$v1){
              if($k1 > 0){ $str .= "\n"; }
              $str .= $v1;
            }
            $v = $str;
		  }elseif(isset($v['value']) && is_array($v['value'])){
            $str = "";
            foreach($v['value'] as $k1=>$v1){
              if($k1 > 0){ $str .= "\n"; }
              $str .= $v1;
            }
            $v = $str;
		  }elseif(isset($v['value'])){
			  $value2 = isset($v['value2']) ? trim($v['value2']) : '';
			  $value3 = isset($v['value3']) ? trim($v['value3']) : '';
			  $v = $v['value'];
          }else{
            $v = "";
          } 		  
        }
		
		
      }

		if(!isset($value2)) { $value2 = ''; }
		if(!isset($value3)) { $value3 = ''; }
		
		$v = trim($v);
		if(in_array($k, $keys_ar)){
			$v = str_replace(',', '.', $v);
			$v = str_replace(' ', '', $v);
			$v = preg_replace('/[^0-9.\/\?\&\=\-\_\s]+/iu', '', $v);
		}
		
		$db->query("INSERT INTO ".$db->tables['option_values']."
			(`id_option`, `id_product`, `value`, `where_placed`, `value2`, `value3`)
			VALUES ('".$k."', '".$id."', 
			  '".$db->escape($v)."', '".$db->escape($where)."', '".$db->escape($value2)."', '".$db->escape($value3)."') ");
			  
    }
  }
  return;
}


/* ok */
/* сформируем массив опций для списка товаров внутри раздела */
/* id-товар, catalogs - str with catalogs ids и where - categ or catalog */
function get_options_in_list($id, $where_placed = 'product'){
	global $db;  
	$options = array();
	if($id == 0 AND intval($catalogs) == 0){ return $options; }
	
	$opts = $db->get_results("SELECT o.id, 
					og.title as group_title,
					opt.title as opt_title,
					o.value,
					o.id_option,
					o.id_product,
					o.value2,
					o.value3,
					opt.alias as opt_alias, 
					opt.type as opt_type, 
					opt.if_select as opt_if_select, 
					opt.after as opt_after
					
				FROM ".$db->tables['option_values']." o 
				LEFT JOIN ".$db->tables['options']." opt ON (o.id_option = opt.id)
				LEFT JOIN ".$db->tables['option_groups']." og ON (opt.group_id = og.id)
				WHERE o.`id_product` = '".$id."' 
				AND o.`where_placed` = '".$where_placed."' 
				AND opt.show_in_list = '1' 	
				ORDER BY og.sort, og.title, opt.sort, opt.title
			", ARRAY_A);
	//$db->debug(); exit;
	if(!$opts){ return $options; }
	if($db->num_rows > 0){
		foreach($opts as $row){
			if(isset($options[$row['id_option']])){
				$options[] = $row;
			}else{
				$options[$row['id_option']] = $row;
			}
		}
	}
	return $options;
}



/* ok */
/* сформируем массив групп и опций внутри них с данными товара */
/* id-товар, catalogs - str with catalogs ids и where - categ or catalog */
function get_options($id, $catalogs, $where="catalog"){
	global $db;
	$get_oid = !empty($_GET['add_option']) ? intval($_GET['add_option']) : 0;
	$get_gid = !empty($_GET['group_id']) ? intval($_GET['group_id']) : 0;
  
	$options = array();
	if($id == 0 AND intval($catalogs) == 0){ return $options; }
	$str_group_where = $where == "catalog" ? "product" : "pub";
	if(empty($catalogs)){ return $options; }
	
	$rows = $db->get_results("SELECT og.*, og.where_placed as `where` 
		FROM ".$db->tables['option_groups']." og 
		LEFT JOIN ".$db->tables['categ_options']." sco on (sco.id_option = og.id)
		WHERE sco.id_categ IN ($catalogs) 
			AND (og.where_placed = '".$str_group_where."' OR og.where_placed = 'all')
		GROUP BY og.id
		ORDER BY og.`sort`, og.title 
	");

	if(!$rows){ return $options; }
	if($db->num_rows > 0){
		foreach($rows as $row){
			$opts = $db->get_results("SELECT o.*, 
				spo.id as value_id, 
				spo.value as product_value, 
				spo.value2 as product_value2, 
				spo.value3 as product_value3   
				FROM ".$db->tables['options']." o 
				LEFT JOIN ".$db->tables['option_values']." spo on (spo.id_option = o.id AND spo.id_product = '".$id."') 
				WHERE o.`group_id` = '".$row->id."'
				ORDER BY o.`sort`, o.`title`
			", ARRAY_A);
			
			if(!empty($get_oid) && !empty($get_gid)){
				$row2 = $db->get_row("SELECT o.*, 
					spo.value as product_value, 
					spo.value2 as product_value2, 
					spo.value3 as product_value3   
					FROM ".$db->tables['options']." o 
					LEFT JOIN ".$db->tables['option_values']." spo on (spo.id_option = o.id AND spo.id_product = '".$id."') 
					WHERE o.`group_id` = '".$get_gid."' 
						AND o.`group_id` = '".$row->id."' 
						AND o.id = '".$get_oid."'
					ORDER BY o.`sort`, o.`title` 
					LIMIT 0,1
				", ARRAY_A);
				if($db->num_rows > 0){
					$row2['product_value'] = '';
					$row2['product_value2'] = '';
					$row2['product_value3'] = '';
					
					$opts[] = $row2;					
				}
			}
			
			$options[] = array(
				'group_id' => $row->id, 
				'title' => $row->title,
				'to_show' => $row->to_show,
				'hide_title' => $row->hide_title,
				'where_placed' => $row->where_placed,
				
				'description' => $row->description, 
				'sort' => $row->sort, 
				'opt_title' => $row->opt_title, 
				'value1' => $row->value1, 
				'value2' => $row->value2, 
				'value3' => $row->value3, 
				'options' => $opts,
			);  
		
		}		
		
	}
	
	return $options;
}


/* ok, websites in categs list */
function _sites4categ($id)
{
  global $db, $tpl;
  if(isset($id['view'])){ $view = $id['view']; }else{ $view = 'full';}
  if(isset($id['id'])){ $id = $id['id']; }else{ return;}
  
  if($view != 'simple'){ $view = 'full';}
  $query = "SELECT si.id, si.site_url, c.alias as alias, c.active  
    FROM ".$db->tables["site_categ"]." as sc,
      ".$db->tables["site_info"]." as si
    LEFT JOIN ".$db->tables["categs"]." c on (c.id = '".$id."')  
    WHERE sc.id_categ = '".$id."' AND sc.id_site = si.id ORDER BY si.id ";
	
  $rows = $db->get_results($query, ARRAY_A);
    if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }

  $tpl->assign("websites_view", $view);
  $tpl->assign("websites", $rows);
  return $tpl->fetch('info/list_websites.html');
}


/* ok, удалим опции */
function delete_options($id, $where){
  global $db;
  $query = "DELETE FROM ".$db->tables["option_values"]." 
      WHERE id_product = '".$id."' AND `where_placed` = '".$where."' ";
  $db->query($query);
  return;  	
}

/* ok, удалим связку раздела и группы опций */
function delete_opt_groups($id, $where){
  global $db;
  $query = "DELETE FROM ".$db->tables["categ_options"]." 
      WHERE id_categ = '".$id."' AND `where_placed` = '".$where."' ";
  $db->query($query);
  return;  	
}


/* ok, обновляем связку раздела и группы опций */
function update_opt_groups($id, $where){
  global $db;
  delete_opt_groups($id, $where);
  
  if(!empty($_POST["options"])){
    foreach($_POST["options"] as $v){
      $db->query("INSERT INTO ".$db->tables["categ_options"]." 
        (`id_option`, `id_categ`, `where_placed`) VALUES (
         '".intval($v)."', '".$id."', '".$db->escape($where)."'
        )");
    }
  }
  return;  	
}


/*ok, counts time to build page */
function page_loaded_time(){
	global $starttime, $db, $site;
	$mtime = explode(' ', microtime());
	$totaltime = $mtime[0] + $mtime[1] - $starttime;
	$str = ' sec. Queries: ';
	$str .= $db->num_queries-3;
	$str .= ' ('.round($db->timers[$db->num_queries]-$db->timers[1],3).' sec.).';
	$f = isset($site->vars['site_datetime_format']) 
		? $site->vars['site_datetime_format'] : 'Y-m-d H:i';
	$str .= ' Server time '.date($f);
	
	if (date_default_timezone_get()) { 
		$str .= ' (' . date_default_timezone_get() . ')';
	}

	
	return printf('%.2f', $totaltime).$str;      
}


/* ok returns form field */
function get_option_field($ar){
	global $db;
	$str = "";
	$field = !empty($ar['field']) ? $ar['field'] : 'value';
	
	$str_inc = !empty($ar['inc_product']) && !empty($ar['product_id']) 
		? '['.$ar['product_id'].']' : '';

    if($ar['type'] == "checkbox"){
		
		
		if(!empty($ar['value_id'])){
			$checked = $ar['value'] == 1 ? 'checked=checked' : ''; 
			$str = "<input type='hidden' name='option".$str_inc."[".$ar['id']."][".$field."]' value='0'>
			<input type='checkbox' name='option_u[".$ar['value_id']."][".$field."]' value='1' $checked>";			
		}else{
			$checked = $ar['value'] == 1 ? 'checked=checked' : ''; 
			$str = "<input type='hidden' name='option".$str_inc."[".$ar['id']."][".$field."]' value='0'>
			<input type='checkbox' name='option".$str_inc."[".$ar['id']."][".$field."]' value='1' $checked>";
			
		}
		
    }elseif($ar['type'] == "select"){
		
		$values = str_replace("\r\n","\n",$ar['if_select']);
		$values = explode("\n",$values);
		
		if(!in_array('',$values)){ $values[] = ''; }
		asort($values);      

		if(!empty($ar['value_id'])){
			$str = "<select name='option_u[".$ar['value_id']."][".$field."]'>";
			foreach($values as $k=>$v){
				$sel = $ar['value'] == $v ? 'selected' : '';
				$str .= "<option value='".$v."' $sel>".$v."</option>";
			}
			$str .= "</select>";
		}else{
			$str = "<select name='option".$str_inc."[".$ar['id']."][".$field."]'>";
			foreach($values as $k=>$v){
				$sel = $ar['value'] == $v ? 'selected' : '';
				$str .= "<option value='".$v."' $sel>".$v."</option>";
			}
			$str .= "</select>";
		}


      
    }elseif($ar['type'] == "categ"){
		$str = '';
		if(!empty($ar['if_select'])){
			$sql = "SELECT p.id, p.f_spec, p.active, p.alias, p.`name` 
					FROM ".$db->tables['pub_categs']." pc
					LEFT JOIN ".$db->tables['publications']." p ON (pc.id_pub = p.id)
					WHERE 
						pc.id_categ = '".intval($ar['if_select'])."' 
						AND pc.where_placed = 'pub'
					ORDER BY p.`name`, p.date_insert 			
			";
			$rows = $db->get_results($sql, ARRAY_A);
			if($db->num_rows > 0){
				
				if(!empty($ar['value_id'])){
					$str = "<select name='option_u[".$ar['value_id']."][".$field."]'>";
					$str .= "<option value=''>- выберите</option>";
					foreach($rows as $k => $v){
						$sel = $ar['value'] == $v['id'] ? 'selected' : '';
						$title = empty($v['active']) ? '*скрыто* - '.$v['name'] : $v['name'];
						$str .= "<option value='".$v['id']."' $sel>".$title."</option>";
					}
					$str .= "</select>";
				}else{
									
					$str = "<select name='option".$str_inc."[".$ar['id']."][".$field."]'>";
					$str .= "<option value=''>- выберите</option>";
					foreach($rows as $k => $v){
						$sel = $ar['value'] == $v['id'] ? 'selected' : '';
						$title = empty($v['active']) ? '*скрыто* - '.$v['name'] : $v['name'];
						$str .= "<option value='".$v['id']."' $sel>".$title."</option>";
					}
					$str .= "</select>";

				}
			}
		}		
    }elseif($ar['type'] == "products"){
		$str = '';
		if(!empty($ar['if_select'])){
			$sql = "SELECT p.id, p.f_spec, p.active, p.alias, p.`name` 
					FROM ".$db->tables['pub_categs']." pc
					LEFT JOIN ".$db->tables['products']." p ON (pc.id_pub = p.id)
					WHERE 
						pc.id_categ = '".intval($ar['if_select'])."' 
						AND pc.where_placed = 'product'
					ORDER BY p.`name`, p.date_insert 			
			";
			$rows = $db->get_results($sql, ARRAY_A);
			
			if($db->num_rows > 0){
				
				if(!empty($ar['value_id'])){
					$str = "<select name='option_u[".$ar['value_id']."][".$field."]'>";
					$str .= "<option value=''>- выберите</option>";
					foreach($rows as $k => $v){
						$sel = $ar['value'] == $v['id'] ? 'selected' : '';
						$title = empty($v['active']) ? '*скрыто* - '.$v['name'] : $v['name'];
						$str .= "<option value='".$v['id']."' $sel>".$title."</option>";
					}
					$str .= "</select>";
					
				}else{
					
					$str = "<select name='option".$str_inc."[".$ar['id']."][".$field."]'>";
					$str .= "<option value=''>- выберите</option>";
					foreach($rows as $k => $v){
						$sel = $ar['value'] == $v['id'] ? 'selected' : '';
						$title = empty($v['active']) ? '*скрыто* - '.$v['name'] : $v['name'];
						$str .= "<option value='".$v['id']."' $sel>".$title."</option>";
					}
					$str .= "</select>";
				}
				
			}
		}		
		
	}elseif($ar['type'] == "int"){
		
		if(!empty($ar['value_id'])){
			
			$str = "<input type='text' name='option_u[".$ar['value_id']."][".$field."]' style='width:100%;' value='".$ar['value']."'>";

		}else{
			
			$str = "<input type='text' name='option".$str_inc."[".$ar['id']."][".$field."]' style='width:100%;' value='".$ar['value']."'>";
			
		}
		
	  
	}elseif($ar['type'] == "date"){
		
		$values = str_replace("\r\n","\n",$ar['if_select']);
		$values = explode("\n",$values);
		
		if(!in_array('',$values)){ $values[] = ''; }
		asort($values);  
		$new_array = array_diff($values, array(''));
		
		if(empty($new_array[0])){ $new_array[0] = 1920; }
		if(empty($new_array[1])){ $new_array[1] = date('Y')+10; }
		$start = $new_array[0];
		$stop = $new_array[1];
		
		$arr = explode('-', $ar['value']);
		if(!isset($arr[0])){ $arr[0] = 0; }
		if(!isset($arr[1])){ $arr[1] = 0; }
		if(!isset($arr[2])){ $arr[2] = 0; }
				
		
		if(!empty($ar['value_id'])){
			$str = "<select name='option_u[".$ar['value_id']."][".$field."][D]'>";
			for($i=0; $i<32; $i++){
				$ch = $arr[2] == $i ? ' selected="selected"' : '';
				$str .= "<option value='".$i."' ".$ch.">".$i."</option>";
			}
			$str .= "</select>";
			
			$str .= "<select name='option_u[".$ar['value_id']."][".$field."][M]'>";
			for($i=0; $i<13; $i++){
				$ch = $arr[1] == $i ? ' selected="selected"' : '';
				$str .= "<option value='".$i."' ".$ch.">".$i."</option>";
			}
			$str .= "</select>";
			
			$str .= "<select name='option_u[".$ar['value_id']."][".$field."][Y]'>";
			for($i=$start; $i<$stop+1; $i++){
				$ch = $arr[0] == $i ? ' selected="selected"' : '';
				$str .= "<option value='".$i."' ".$ch.">".$i."</option>";
			}
			$str .= "</select>";

		}else{
			
			$str = "<input type='text' name='option".$str_inc."[".$ar['id']."][".$field."]' style='width:100%;' value='".$ar['value']."'>";


			$str = "<select name='option".$str_inc."[".$ar['id']."][".$field."][D]'>";
			for($i=0; $i<32; $i++){
				$ch = $arr[2] == $i ? ' selected="selected"' : '';
				$str .= "<option value='".$i."' ".$ch.">".$i."</option>";
			}
			$str .= "</select>";
			
			$str .= "<select name='option".$str_inc."[".$ar['id']."][".$field."][M]'>";
			for($i=0; $i<13; $i++){
				$ch = $arr[1] == $i ? ' selected="selected"' : '';
				$str .= "<option value='".$i."' ".$ch.">".$i."</option>";
			}
			$str .= "</select>";
			
			$str .= "<select name='option".$str_inc."[".$ar['id']."][".$field."][Y]'>";
			for($i=$start; $i<$stop+1; $i++){
				$ch = $arr[0] == $i ? ' selected="selected"' : '';
				$str .= "<option value='".$i."' ".$ch.">".$i."</option>";
			}
			$str .= "</select>";

			
		}
		
		
	}elseif($ar['type'] == "datetime"){
		
		$values = str_replace("\r\n","\n",$ar['if_select']);
		$values = explode("\n",$values);
		
		if(!in_array('',$values)){ $values[] = ''; }
		asort($values);  
		$new_array = array_diff($values, array(''));
		
		if(empty($new_array[0])){ $new_array[0] = 1920; }
		if(empty($new_array[1])){ $new_array[1] = date('Y')+10; }
		$start = $new_array[0];
		$stop = $new_array[1];

		$arr1 = explode(' ', $ar['value']);
		$arr = explode('-', $arr1[0]);
		if(!isset($arr[0])){ $arr[0] = 0; }
		if(!isset($arr[1])){ $arr[1] = 0; }
		if(!isset($arr[2])){ $arr[2] = 0; }
		
		if(!isset($arr1[1])){ $arr1[1] = '0:0'; }
		$arr2 = explode(':', $arr1[1]);
		if(!isset($arr2[0])){ $arr2[0] = 0; }
		if(!isset($arr2[1])){ $arr2[1] = 0; }
				
		
		if(!empty($ar['value_id'])){
			$str = "<select name='option_u[".$ar['value_id']."][".$field."][D]'>";
			for($i=0; $i<32; $i++){
				$ch = $arr[2] == $i ? ' selected="selected"' : '';
				$str .= "<option value='".$i."' ".$ch.">".$i."</option>";
			}
			$str .= "</select>";
			
			$str .= "<select name='option_u[".$ar['value_id']."][".$field."][M]'>";
			for($i=0; $i<13; $i++){
				$ch = $arr[1] == $i ? ' selected="selected"' : '';
				$str .= "<option value='".$i."' ".$ch.">".$i."</option>";
			}
			$str .= "</select>";
			
			$str .= "<select name='option_u[".$ar['value_id']."][".$field."][Y]'>";
			for($i=$start; $i<$stop+1; $i++){
				$ch = $arr[0] == $i ? ' selected="selected"' : '';
				$str .= "<option value='".$i."' ".$ch.">".$i."</option>";
			}
			$str .= "</select>";
			
			$str .= "<select name='option_u[".$ar['value_id']."][".$field."][H]'>";
			for($i=0; $i<25; $i++){
				$ch = $arr2[0] == $i ? ' selected="selected"' : '';
				$str .= "<option value='".$i."' ".$ch.">".$i."</option>";
			}
			$str .= "</select>";
			
			$str .= "<select name='option_u[".$ar['value_id']."][".$field."][i]'>";
			for($i=0; $i<61; $i++){
				$ch = $arr2[1] == $i ? ' selected="selected"' : '';
				$str .= "<option value='".$i."' ".$ch.">".$i."</option>";
			}
			$str .= "</select>";

		}else{
			
			$str = "<input type='text' name='option".$str_inc."[".$ar['id']."][".$field."]' style='width:100%;' value='".$ar['value']."'>";


			$str = "<select name='option".$str_inc."[".$ar['id']."][".$field."][D]'>";
			for($i=0; $i<32; $i++){
				$ch = $arr[2] == $i ? ' selected="selected"' : '';
				$str .= "<option value='".$i."' ".$ch.">".$i."</option>";
			}
			$str .= "</select>";
			
			$str .= "<select name='option".$str_inc."[".$ar['id']."][".$field."][M]'>";
			for($i=0; $i<13; $i++){
				$ch = $arr[1] == $i ? ' selected="selected"' : '';
				$str .= "<option value='".$i."' ".$ch.">".$i."</option>";
			}
			$str .= "</select>";
			
			$str .= "<select name='option".$str_inc."[".$ar['id']."][".$field."][Y]'>";
			for($i=$start; $i<$stop+1; $i++){
				$ch = $arr[0] == $i ? ' selected="selected"' : '';
				$str .= "<option value='".$i."' ".$ch.">".$i."</option>";
			}
			$str .= "</select>";

			$str .= "<select name='option".$str_inc."[".$ar['id']."][".$field."][H]'>";
			for($i=0; $i<25; $i++){
				$ch = $arr2[0] == $i ? ' selected="selected"' : '';
				$str .= "<option value='".$i."' ".$ch.">".$i."</option>";
			}
			$str .= "</select>";
			
			$str .= "<select name='option".$str_inc."[".$ar['id']."][".$field."][i]'>";
			for($i=0; $i<61; $i++){
				$ch = $arr2[1] == $i ? ' selected="selected"' : '';
				$str .= "<option value='".$i."' ".$ch.">".$i."</option>";
			}
			$str .= "</select>";
		}

	}elseif($ar['type'] == "connected"){
		
		global $tpl;
		if($field != 'value'){ return; }
		$values = build_connected_ar($ar['if_select']);
		$titles = explode("|", $ar['title']);
		//setcookie("simpla[option_id]", $ar['id'], time()+3600);  /* срок действия 1 час */
		$tpl->assign('option_titles', $titles);
		$tpl->assign('option_values', $values);
		$tpl->assign('option_value', $ar['value']);
		$tpl->assign('option_value2', $ar['value2']);
		$tpl->assign('option_value3', $ar['value3']);
		$tpl->assign('option_id', $ar['id']);
		$str = $tpl->fetch("js/connected/connected.html");
		
	}elseif($ar['type'] == "multicheckbox"){
		if($field != 'value'){ return; }
		$sel = str_replace("\r\n","\n",$ar['value']);
		$sel = explode("\n",$sel);
		
		$all = str_replace("\r\n","\n",$ar['if_select']);
		$all = explode("\n",$all);
		asort($all);
		
		$str = "";
		
		if(!empty($ar['value_id'])){
			foreach($all as $v){
				$v = trim($v);
				$selected = in_array($v, $sel) ? " checked='checked'" : "";
				$str .= "<input type='checkbox' name='option_u[".$ar['value_id']."][".$field."][]' value='".$v."'$selected> $v <br>
				";
			}
		}else{
			
			foreach($all as $v){
				$v = trim($v);
				$selected = in_array($v, $sel) ? " checked='checked'" : "";
				$str .= "<input type='checkbox' name='option".$str_inc."[".$ar['id']."][".$field."][]' value='".$v."'$selected> $v <br>
				";
			}
		}
		
    }else{
		
		if(!empty($ar['value_id'])){
			$str = "<textarea name='option_u[".$ar['value_id']."][".$field."]' rows='2' style='width:100%;'>".htmlspecialchars($ar['value'])."</textarea>";
		}else{			
			$str = "<textarea name='option".$str_inc."[".$ar['id']."][".$field."]' rows='2' style='width:100%;'>".htmlspecialchars($ar['value'])."</textarea>";
		}
    }
	
	return $str;
}




/* ok returns form field  ----- */
function get_option_field_DEL($ar){
	global $db;
	
	$str = "";
	$field = !empty($ar['field']) ? $ar['field'] : 'value';
	
    if($ar['type'] == "checkbox"){
		
		$checked = $ar['value'] == 1 ? 'checked=checked' : ''; 
		$str = "<input type='checkbox' name='option[".$ar['id']."][".$field."]' value='1' $checked>";
		
    }elseif($ar['type'] == "select"){
		
		$values = str_replace("\r\n","\n",$ar['if_select']);
		$values = explode("\n",$values);
		
		if(!in_array('',$values)){ $values[] = ''; }
		asort($values);      
		$str = "<select name='option[".$ar['id']."][".$field."]'>";
		foreach($values as $k=>$v){
			$sel = $ar['value'] == $v ? 'selected' : '';
			$str .= "<option value='".$v."' $sel>".$v."</option>";
		}
		$str .= "</select>";
      
    }elseif($ar['type'] == "categ"){
		$str = '';
		if(!empty($ar['if_select'])){
			$sql = "SELECT p.id, p.f_spec, p.active, p.alias, p.`name` 
					FROM ".$db->tables['pub_categs']." pc
					LEFT JOIN ".$db->tables['publications']." p ON (pc.id_pub = p.id)
					WHERE 
						pc.id_categ = '".intval($ar['if_select'])."' 
						AND pc.where_placed = 'pub'
					ORDER BY p.`name`, p.date_insert 			
			";
			$rows = $db->get_results($sql, ARRAY_A);
			if($db->num_rows > 0){
				$str = "<select name='option[".$ar['id']."][".$field."]'>";
				$str .= "<option value=''>- выберите</option>";
				foreach($rows as $k => $v){
					$sel = $ar['value'] == $v['id'] ? 'selected' : '';
					$title = empty($v['active']) ? '*скрыто* - '.$v['name'] : $v['name'];
					$str .= "<option value='".$v['id']."' $sel>".$title."</option>";
				}
				$str .= "</select>";
			}
		}		
    }elseif($ar['type'] == "products"){
		$str = '';
		if(!empty($ar['if_select'])){
			$sql = "SELECT p.id, p.f_spec, p.active, p.alias, p.`name` 
					FROM ".$db->tables['pub_categs']." pc
					LEFT JOIN ".$db->tables['products']." p ON (pc.id_pub = p.id)
					WHERE 
						pc.id_categ = '".intval($ar['if_select'])."' 
						AND pc.where_placed = 'product'
					ORDER BY p.`name`, p.date_insert 			
			";
			$rows = $db->get_results($sql, ARRAY_A);
			
			if($db->num_rows > 0){
				$str = "<select name='option[".$ar['id']."][".$field."]'>";
				$str .= "<option value=''>- выберите</option>";
				foreach($rows as $k => $v){
					$sel = $ar['value'] == $v['id'] ? 'selected' : '';
					$title = empty($v['active']) ? '*скрыто* - '.$v['name'] : $v['name'];
					$str .= "<option value='".$v['id']."' $sel>".$title."</option>";
				}
				$str .= "</select>";
			}
		}		
		
	}elseif($ar['type'] == "int"){
		
      $str = "<input type='text' name='option[".$ar['id']."][".$field."]' style='width:100%;' value='".$ar['value']."'>";
	  
    }elseif($ar['type'] == "connected"){
		
		global $tpl;
		if($field != 'value'){ return; }
		$values = build_connected_ar($ar['if_select']);
		$titles = explode("|", $ar['title']);
		//setcookie("simpla[option_id]", $ar['id'], time()+3600);  /* срок действия 1 час */
		$tpl->assign('option_titles', $titles);
		$tpl->assign('option_values', $values);
		$tpl->assign('option_value', $ar['value']);
		$tpl->assign('option_value2', $ar['value2']);
		$tpl->assign('option_value3', $ar['value3']);
		$tpl->assign('option_id', $ar['id']);
		$str = $tpl->fetch("js/connected/connected.html");
		
	}elseif($ar['type'] == "multicheckbox"){
		
		if($field != 'value'){ return; }
		$sel = str_replace("\r\n","\n",$ar['value']);
		$sel = explode("\n",$sel);
		
		$all = str_replace("\r\n","\n",$ar['if_select']);
		$all = explode("\n",$all);
		asort($all);
		
		$str = "";
		foreach($all as $v){
			$v = trim($v);
			$selected = in_array($v, $sel) ? " checked='checked'" : "";
			$str .= "<input type='checkbox' name='option[".$ar['id']."][]' value='".$v."'$selected> $v<br>";
		}
		
    }else{
		
		$str = "<textarea name='option[".$ar['id']."][".$field."]' rows='2' style='width:100%;'>".htmlspecialchars($ar['value'])."</textarea>";
    }
	
	return $str;
}

/* ok, builds array for connected select fields */
function build_connected_ar($str){
  $values = str_replace("\r\n","\n",$str);
  $values = explode("\n",$values);
  $connects = array(); // key=region value=country
  foreach($values as $k=>$v){
  	$i = $k+1;
  	$first2 = mb_substr(trim($v), 0, 2, 'utf-8');
  	$first1 = mb_substr(trim($v), 0, 1, 'utf-8');
  	if($first2 == '--'){ // добавляем в массив третьего уровня
  		$v = str_replace('--', '', $v);
  		if(isset($current_region)){
  			$ar[$current_country]['regions'][$current_region]['cities'][$i] = array('city_id' => $i, 'name' => trim($v));
  		}		
  	}elseif($first1 == '-'){ // добавляет в массив второго уровня
  		$v = str_replace('-', '', $v);
  		$ar[$current_country]['regions'][$i] = array(
  			'region_id' => $i, 
  			'name' => trim($v),
  		);
  		if(!isset($connects[$i])){ $connects[$i] = $current_country; }
  		$current_region = $i;
  	}else{ // создаем массив первого уровня
  		$ar[$i]['country_id'] = $i;
  		$ar[$i]['name'] = trim($v);
  		$current_country = $i;
  		unset($current_region);
  	}
  }
  usort($ar, 'mysort');
  return $ar;
}

/* ok */
function mysort($b,$c) { return strcmp($b['name'], $c['name']); }


/* ok, for new version */
function set_default_picture2($id,$posted)
{
	global $db;
	$default = isset($posted['default_pic']) ? $posted['default_pic'] : 0;
  if($default == 0 ) return;                                           
	$record_type = isset($posted['record_type']) ? $posted['record_type'] : "";

	$db->query("UPDATE ".$db->tables["uploaded_pics"]." 
      SET is_default = '1' 
      WHERE id_in_record = '".$default."'  
        AND record_id='$id' AND record_type='$record_type' ");

	$db->query("UPDATE ".$db->tables["uploaded_pics"]." 
      SET is_default = '0' 
      WHERE id_in_record <> '".$default."'  
        AND record_id = '".$id."' AND record_type = '".$record_type."' ");

  // Изменим порядок сортировки изображений
  $rows = $db->get_results("SELECT * FROM ".$db->tables["uploaded_pics"]." 
      WHERE record_id = '".$id."' AND record_type = '".$record_type."'       
      ORDER BY is_default DESC, id_in_record, width DESC ");
  if($db->num_rows == 0){return;}

  foreach($rows as $key=>$row){
    if($key == 0){ $i = 1; }
    if(isset($rows[$key-1]->id_in_record)){
      if($row->id_in_record != $rows[$key-1]->id_in_record){ $i = $i+1; }
    }
    $query = "UPDATE ".$db->tables["uploaded_pics"]." 
      SET id_in_record = '".$i."' WHERE id = '".$row->id."' ";
    //echo "... $query<br>";
    $db->query($query);
  }
	return;  
}



/* ok, for new version */
/* сохраняем порядок картинок */
function update_picture_positions($id,$posted)
{
    global $db;

    // Получаем из POST переменных новые позиции главных рисунков
    $risunki = array();
    $go = 0;
    foreach($posted as $key => $val)
    {
        if(strstr($key,'_position'))
        {
            $ris_id = str_replace(array('img','_','position'),array('','',''),$key);
            $risunki[$ris_id] = $val;
            if($ris_id != $val) { $go = 1;}
        }
    }

    // Проверим, если все ключи идут подряд, то выходим без изменений
    if($go == 0){ return; }

    // Получаем старые позиции по каждому рисунку
    $results = $db->get_results("SELECT record_id,id,id_in_record FROM uploaded_pics WHERE record_id={$id} AND record_type='{$posted['record_type']}' ORDER BY id_in_record",ARRAY_N);

    $ris_positions = array();
    foreach($results as $res)
    {
        if(!isset($ris_positions[$res[2]]))
        $ris_positions[$res[2]] = array();
        array_push($ris_positions[$res[2]], $res[1]);
    }

    // Формируем массив для удобного SQL запроса на UPDATE
    $uia = array();
    foreach($risunki as $ris_num => $ris_pos)
    {
        foreach($ris_positions as $old_ris_pos => $images_array)
        {
            //if(in_array($ris_num,$images_array)) $uia[$ris_pos] = $images_array;
            if($ris_num == $old_ris_pos){
                $uia[$ris_pos] = $images_array;
            }
        }
    }

    // Выполняем SQL запрос на замену позиций
    foreach($uia as $key=>$val)
    {
        $imploded_list = implode(',',$val);
        $is_default = $key == 1 ? 1 : 0;
        $sql = "UPDATE ".$db->tables["uploaded_pics"]." 
              SET 
                `id_in_record` = '".$key."',
                `is_default` = '".$is_default."' 
              WHERE record_id = '".$id."' 
                AND id IN(".$imploded_list.")  
                AND record_type = '".$posted['record_type']."' ";
        $db->query($sql);
    }
  return;
}

function update_shop_picture_positions($id,$posted)
{
    global $db;

    // Получаем из POST переменных новые позиции главных рисунков
    $risunki = array();
    foreach($posted as $key => $val)
    {
        if(strstr($key,'_position'))
        {
            $ris_id = str_replace(array('img','_','position'),array('','',''),$key);
            $risunki[$ris_id] = $val;
        }
    }


    print_r($risunki);
    print "<br><br><br>";

    // Получаем старые позиции по каждому рисунку
    $results = $db->get_results("SELECT id_product,id,id_in_product FROM shop_product_pics WHERE id_product={$id} ORDER BY id_in_product",ARRAY_N);

    $ris_positions = array();
    foreach($results as $res)
    {
        if(!isset($ris_positions[$res[2]]))
        $ris_positions[$res[2]] = array();
        array_push($ris_positions[$res[2]], $res[1]);
    }


    foreach($ris_positions as $key=>$val)
    {
        print "<li> {$key} = ".implode(',',$val);
    }
    print "<br><br>";


    // Формируем массив для удобного SQL запроса на UPDATE
    $uia = array();
    foreach($risunki as $ris_num => $ris_pos)
    {
        foreach($ris_positions as $old_ris_pos => $images_array)
        {
            if(in_array($ris_num,$images_array)) $uia[$ris_pos] = $images_array;
        }
    }

    // Выполняем SQL запрос на замену позиций
    foreach($uia as $key=>$val)
    {
        $imploded_list = implode(',',$val);
        print "<li> {$key} = ".implode(',',$val). " == ";
        $sql = "UPDATE shop_product_pics SET `id_in_product` = ".$key." WHERE id_product={$id} AND id IN(".$imploded_list.")";
        print $sql;
        $db->query($sql);
    }

}


// ok for new version
// creates new fotos from biggest
function create_new_photos($id, $posted)
{
  if(empty($posted["resize_again"])){ return; }
  if(empty($posted["record_type"])){ return; }
  global $db, $site_vars;
	foreach($posted["resize_again"] as $k=>$v){
    // Удалим все кроме самой большой картинки $v
  	$img = $db->get_row("SELECT * FROM ".$db->tables["uploaded_pics"]." 
          WHERE record_type = '".$posted["record_type"]."' 
            AND record_id = '".$id."' AND id_in_record = '".$v."' 
            ORDER by `width` DESC, `height` DESC LIMIT 1");

    $rows = $db->get_results("SELECT * FROM ".$db->tables["uploaded_pics"]." 
          WHERE record_type = '".$posted["record_type"]."' 
            AND record_id = '".$id."' AND id_in_record = '".$v."' 
            AND id != '".$img->id."' 
            ORDER by `width` DESC, `height` DESC ");
    if($db->num_rows > 0){
      foreach($rows as $row){
        delete_uploaded_pics($id, $posted["record_type"], $row->id, true);        
      }
    }

    // Нарежем новые картинки из имеющейся
    $sizes = array();
    if(!empty($site_vars['img_size1'])){ $sizes[] = $site_vars['img_size1']; }
    if(!empty($site_vars['img_size2'])){ $sizes[] = $site_vars['img_size2']; }
    if(!empty($site_vars['img_size3'])){ $sizes[] = $site_vars['img_size3']; }
    if(!empty($site_vars['img_size4'])){ $sizes[] = $site_vars['img_size4']; }
    if(!empty($site_vars['img_size5'])){ $sizes[] = $site_vars['img_size5']; }
    if(!empty($site_vars['img_size6'])){ $sizes[] = $site_vars['img_size6']; }
    if(!empty($site_vars['img_size7'])){ $sizes[] = $site_vars['img_size7']; }
    if(!empty($site_vars['img_size8'])){ $sizes[] = $site_vars['img_size8']; }
    if(!empty($site_vars['img_size9'])){ $sizes[] = $site_vars['img_size9']; }
    if(!empty($site_vars['img_size10'])){ $sizes[] = $site_vars['img_size10']; }
    $new_sizes = array();
    foreach($sizes as $v){  
      if($v['width'] > 0 || $v['height'] > 0){
        $new_sizes[] = $v;     
      }
    }
    $sizes = $new_sizes;
    if(empty($sizes)){ return; }
    $original_file = "../upload/records/".$img->id.".".$img->ext;
    $size = array($img->width,$img->height);
     
    foreach($sizes as $k=>$v){
		$diff_1 = ($img->width - $v['width']) + ($img->height - $v['height']);
		$skip = 0;
		if($img->width == $v['width'] && $v['height'] == 0){ $skip = 1; }
		if($img->height == $v['height'] && $v['width'] == 0){ $skip = 1; }
		
		if($img->width < $v['width'] && $v['height'] == 0){ $skip = 1; }
		if($img->height < $v['height'] && $v['width'] == 0){ $skip = 1; }
		
		if($img->width < $v['width'] && $img->height < $v['height']){ $skip = 1; }
		
      //if($img->width > $v['width'] && $img->height > $v['height']){
		if(($img->width > $v['width'] || $img->height > $v['height']) 
			&& ($diff_1 > 0) && $skip == 0){
				//echo $diff_1.':: '.$img->width.' - '.$v['width'].' (need); '. $img->height.' - '.$v['height'].' (need) <br>';

        // посчитаем какие размеры у файлов будут
        if($v['width'] > 0 && $v['height'] > 0){
          $width = $v['width'];
          $height = $v['height'];
		  if($img->width < $v['width']){ $width = $img->width; }
		  if($img->height < $v['height']){ $height = $img->height; }
		  //echo 'need it: '.$width.' - '.$height.' ('.$img->width.':'.$img->height.')<br>';
        }else{
          if($v['width'] > 0){
            $width = $v['width'];
            $height = $width*$img->height/$img->width;
          }else{
            $height = $v['height'];
            $width = $height*$img->width/$img->height;
          }
        }
        //echo intval($width).' - '.intval($height).'<br>';
        $db->query("INSERT INTO ".$db->tables['uploaded_pics']." (id_exists, 
                 record_id, record_type, width, height, title, ext, id_in_record, 
                 is_default) VALUES ('0', '".$id."', '".$posted["record_type"]."', 
                 '".$width."', '".$height."', '".$db->escape($img->title)."', 
                 '".$img->ext."', '".$img->id_in_record."', '".$img->is_default."')"); 
        $file_id = $db->insert_id;
        $new_file_name = "../upload/records/".$file_id.".".$img->ext;
        
        if(!empty($db->last_error)){ return db_error(basename(__FILE__).": 2673"); }
        admin_crop_image($original_file, 
                        $new_file_name, 
                        intval($width), intval($height), 
                        $size);
      }    
    }
	}
	//exit;
  return;
}



function create_new_product_photos($id, $id_in_product)

{
echo 'there 3125'; exit;
	global $db;

	$img = $db->get_row("SELECT * FROM ".$db->tables["shop_product_pics"]." WHERE id_product='$id' AND id_in_product='{$id_in_product}' ORDER by `width` DESC, `height` DESC LIMIT 1");

	$results = $db->get_results("SELECT * FROM ".$db->tables["shop_product_pics"]." WHERE id_product='$id' AND id_in_product='{$id_in_product}' AND id!='".$img->id."' ORDER by `width` DESC, `height` DESC ");

	if(is_array($results))

	foreach($results as $row) {

		unlink(UPLOAD."/products/{$row->id}.{$row->ext}");

	}

	$db->query("DELETE FROM ".$db->tables["shop_product_pics"]." WHERE id_product='$id' AND id_in_product='{$id_in_product}' AND id!='".$img->id."'");

	//include("image_resize.php");



    $pics = getimagesize(UPLOAD."/products/{$img->id}.{$img->ext}");

	$sizes = array(IMG_WIDTH_SMALL,IMG_WIDTH_BIG);

	if(defined("IMG_WIDTH_MINI")) $sizes[] = IMG_WIDTH_MINI;

	//$size = IMG_WIDTH_SMALL;

	foreach($sizes as $size){

		$width = $pics[0];

	    $height = $pics[1];

	    $ext = $pics[2];



	    if($ext == 1){ $ext = "gif"; }

	    elseif($ext == 2){ $ext = "jpg"; }

	    elseif($ext == 3){ $ext = "png"; }



		$db->query("INSERT INTO ".$db->tables["shop_product_pics"]." SET width='$size', height='$size', id_product='$id', ext='$ext', id_in_product='{$id_in_product}', is_default='".$img->is_default."'");

		$pic_id = $db->insert_id;

		upload_and_resize(UPLOAD."/products/{$img->id}.{$img->ext}",$size, UPLOAD."/products/".$pic_id.".$ext");

		//exit;

		$pic_size = getimagesize(UPLOAD."/products/".$pic_id.".$ext");

		$db->query("UPDATE ".$db->tables["shop_product_pics"]." SET `width` = ".$pic_size[0].", `height` = ".$pic_size[1]."  WHERE id = '".$pic_id."'");

	}

}



function form_edit_date($d){

  $y = _year(date("Y", strtotime($d)), "year");

  $mon = _month(date("m", strtotime($d)), "month");

  $day = _day(date("d", strtotime($d)), "day");

  $h = _hour(date("H", strtotime($d)), "hour");

  $min = _minute(date("i", strtotime($d)), "minute");

  $sec = _minute(date("s", strtotime($d)), "second");



  $str = $day." ".$mon." ".$y." время ".$h.":".$min.":".$sec;

  return $str;

}



function date_to_string(){

  $y = isset($_POST['insert_date']['y']) ? intval($_POST['insert_date']['y']) : "";

  $mon = isset($_POST['insert_date']['m']) ? intval($_POST['insert_date']['m']) : "";

  $d = isset($_POST['insert_date']['d']) ? intval($_POST['insert_date']['d']) : "";

  $h = isset($_POST['insert_date']['h']) ? intval($_POST['insert_date']['h']) : "00";

  $min = isset($_POST['insert_date']['i']) ? intval($_POST['insert_date']['i']) : "00";

  $sec = isset($_POST['insert_date']['s']) ? intval($_POST['insert_date']['s']) : "00";

  if(empty($y) || empty($mon) || empty($d) ){

    return '';

  }

  $str = $y."-".$mon."-".$d." ".$h.":".$min.":".$sec;

  return $str;

}



function getsiteurl()

{

	global $db,$site;



	$site = getsite();



	$site_url = $site->site_url;

	$site_url = str_replace("http://","",$site_url);

	$site_url = str_replace("www.","",$site_url);

	$site_url = rtrim($site_url,"/");

	$site_url = "http://".$site_url;

}



function getsite()

{

	global $db;



	$host = $_SERVER["HTTP_HOST"];

	$urls = array("http://".$host,"http://".$host."/",$host,"http://www.".$host,"http://www.".$host."/","www.".$host,);

	$urls = implode4mysql($urls);

	$sites = $db->get_results("select * from ".PREFIX."site_info where site_url in ($urls)");

	$site = $sites[0];



	return $site;

}

if(!function_exists('numformat')){
  function numformat($ar){
	return number_format($ar, 0, '.', ' ');
  }
}

function set_path(){
  global $currentlang;
  $ar = array();
  if(file_exists("tpl/$currentlang/set_path.php"))
  include("tpl/$currentlang/set_path.php");
  else include("set_path.php");
  return $ar;
}

// Функция update_multitpl() проверяет, есть ли поле multitpl в таблице
// и изменяет его значение для определнной записи
function update_multitpl($db_table, $id, $multitpl){
  global $db;
  $var = $db->get_var("SELECT multitpl FROM ".$db_table." WHERE id = '".$id."' ");
  if($var){
    $db->query("UPDATE ".$db_table." SET multitpl = '".$db->escape($multitpl)."' WHERE id = '".$id."' ");
  }
  return;
}


function show_title($ar){
  $where = isset($ar['type_record']) ? $ar['type_record'] : '';
  $where_id = isset($ar['id_record']) ? intval($ar['id_record']) : 0;

  global $db;
  if($where == 'categ'){
    return $db->get_var("SELECT `title` FROM ".$db->tables['categs']." WHERE id = '".$where_id."' ");
  }elseif($where == 'pub'){
    return $db->get_var("SELECT name FROM ".$db->tables['publications']." WHERE id = '".$where_id."' ");
  }elseif($where == 'product'){
    return $db->get_var("SELECT name FROM ".$db->tables['products']." WHERE id = '".$where_id."' ");
  }else{ return ' -'; }

}

function show_userinfo_tpl($ar){
  $userid = isset($ar['userid']) ? intval($ar['userid']) : 0;
	$var = isset($ar["assign"]) ? $ar["assign"] : "default";
  global $db, $tpl;
  $str = $db->get_row("SELECT * FROM ".$db->tables['users']." WHERE id = '".$userid."' ", ARRAY_A);
	$tpl->assign($var,$str);
  return;
}


/* ok */
function uploaded_pics_tpl($ar){
  $id = isset($ar['id']) ? intval($ar['id']) : 0;
  $where = isset($ar['where']) ? $ar['where'] : "";
	$var = isset($ar["assign"]) ? $ar["assign"] : "default";
  global $db, $tpl;
  $rows = $db->get_results("SELECT * FROM ".$db->tables['uploaded_pics']." 
      WHERE record_id = '".$id."' AND record_type = '".$where."' 
      ORDER BY is_default desc, id_in_record, width desc 
      ", ARRAY_A);
	$tpl->assign($var,$rows);
  return;
}


  // ok new version
  function admin_crop_image($filePath, $savePath, $max_width, $max_height, $size=false)
  {
	  global $site;
	$logo = array();  
    $method = "width";
    require_once 'inc/resize/AcImage.php';
	$savePath = str_replace('..', PATH, $savePath);
    $image = AcImage::createImage($filePath);

	if(!empty($site->vars['img_watermark']['img'][0]['url']) 
		&& $max_width > $site->vars['img_watermark']['value']){
		$logo = AcImage::createImage(PATH.$site->vars['img_watermark']['img'][0]['url']);
	}


	if(!empty($logo)){
		//$image->drawLogo($logo, AcImage::BOTTOM_RIGHT);
	}
    AcImage::setRewrite(true); //разрешить перезапись при конфликте имён
    
    if($max_height == 0){
     // Если 0 по высоте, то она не имеет значения и вырезаем только по ширине
          $image
            ->resizeByWidth($max_width)
			->drawLogo($logo, AcImage::BOTTOM_RIGHT)
            ->save($savePath);
          return 'ok';
    }

    if($max_width == 0){
     // Если 0 по ширине, то она не имеет значения и вырезаем только по высоте
          $image
            ->resizeByHeight($max_height)
			->drawLogo($logo, AcImage::BOTTOM_RIGHT)
            ->save($savePath);
          return 'ok';
    }


    if(!$size) { $size = GetImageSize($filePath); }
    $t_width = $size[0];
    $t_height = $size[1];
    // рассчитаем размеры изображения для ресайза
    $x_ratio = $max_width / $t_width;
    $y_ratio = $max_height / $t_height;
    
    if($t_width/$t_height < $max_width/$max_height){
      $resize_method = 'width';
    }else{
      $resize_method = 'height';
    } 
    
    if($resize_method == 'height'){
      $image
        ->resizeByHeight($max_height)
        ->cropCenter($max_width.'px', '100%')
		->drawLogo($logo, AcImage::BOTTOM_RIGHT)
        ->save($savePath);
    }else{
      $image
        ->resizeByWidth($max_width)
        ->cropCenter('100%', $max_height.'px')
		->drawLogo($logo, AcImage::BOTTOM_RIGHT)
        ->save($savePath);
    }
        
    return 'ok';              
  }
	
  // ok new version
	function add_vars_foto($id, $where, $width, $height)
	{
    global $db;
	if(empty($_FILES['foto'])){ return; }
    // Загружаем файл и вырежем в нужные размеры, если потребуется
    switch ($_FILES['foto']['type']) {
    case 'image/jpeg':
        $file_type = 'jpg';
        break;
    case 'image/png':
        $file_type = 'png';
        break;
    case 'image/gif':
        $file_type = 'gif';
        break;
    default:
       return 'wrong_filetype';
       //exit;
    }			

    $size = GetImageSize($_FILES['foto']['tmp_name']);
    if($size[0] < $width || $size[1] < $height){
      return 'too_small_size';
    }

    $if_exists = $db->get_row("SELECT * FROM ".$db->tables['uploaded_pics']." 
        WHERE record_id = '".$id."' AND record_type = '".$where."' 
        ORDER BY id_in_record desc ");
    if($db->num_rows > 0){
      $is_default = 0;
      $id_in_record = $if_exists->id_in_record+1;    
    }else{
      $is_default = 1;
      $id_in_record = 1;
    } 
    
    $db->query("INSERT INTO ".$db->tables['uploaded_pics']." (id_exists, 
          record_id, record_type, width, height, title, ext, id_in_record, 
          is_default) VALUES ('0', '".$id."', '".$where."', '".$width."', 
          '".$height."', '".$db->escape($_FILES['foto']['name'])."', 
          '".$file_type."', '".$id_in_record."', '".$is_default."')");
    $file_id = $db->insert_id;
    $new_file_name = "../upload/records/".$file_id.".".$file_type; 
    
    if(!$file_id){
      return 'database_error';      
    }
        
		if(!move_uploaded_file($_FILES['foto']['tmp_name'], $new_file_name)){
      $db->query("DELETE FROM ".$db->tables['uploaded_pics']." WHERE id = '".$file_id."' ");
      return 'not_uploaded';      
    }
    
    return admin_crop_image($new_file_name, $new_file_name, intval($width), intval($height), $size);
	}


    // ok, служебная функция упорядочивания файлов для загрузки
    function reArrayFiles(&$file_post) {  
      $file_ary = array();
      $file_count = count($file_post['name']);
      $file_keys = array_keys($file_post);
  
      for ($i=0; $i<$file_count; $i++) {
          foreach ($file_keys as $key) {
              $file_ary[$i][$key] = $file_post[$key][$i];
          }
      }
  
      return $file_ary;
    }

  // ok, upload fotos for records
  function add_new_fotos($id, $where)
	{       
    global $db, $site_vars, $admin_vars;
    if(empty($_FILES["pics"]["size"][0])){ return; }
    $sizes = array();
    if(isset($site_vars['img_size1'])){ $sizes[] = $site_vars['img_size1']; }
    if(isset($site_vars['img_size2'])){ $sizes[] = $site_vars['img_size2']; }
    if(isset($site_vars['img_size3'])){ $sizes[] = $site_vars['img_size3']; }
    if(isset($site_vars['img_size4'])){ $sizes[] = $site_vars['img_size4']; }
    if(isset($site_vars['img_size5'])){ $sizes[] = $site_vars['img_size5']; }
    if(isset($site_vars['img_size6'])){ $sizes[] = $site_vars['img_size6']; }
    if(isset($site_vars['img_size7'])){ $sizes[] = $site_vars['img_size7']; }
    if(isset($site_vars['img_size8'])){ $sizes[] = $site_vars['img_size8']; }
    if(isset($site_vars['img_size9'])){ $sizes[] = $site_vars['img_size9']; }
    if(isset($site_vars['img_size10'])){ $sizes[] = $site_vars['img_size10']; }

    $new_sizes = array();
    foreach($sizes as $v){  
      if($v['width'] > 0 || $v['height'] > 0){
        $new_sizes[] = $v;     
      }
    }
    $sizes = $new_sizes;
    if(empty($sizes)){ return; }     
      
      
    $file_ary = reArrayFiles($_FILES['pics']);
    foreach ($file_ary as $key=>$file) {
            
      switch ($file['type']) {
        case 'image/jpeg':
          $file_type = 'jpg';
          break;
        case 'image/png':
          $file_type = 'png';
          break;
        case 'image/gif':
          $file_type = 'gif';
          break;
        default:
          $file_type = 'wrong';
          break;
      }			

      if($file_type != "wrong" && $file['size'] > 0){

          $if_exists = $db->get_row("SELECT * FROM ".$db->tables['uploaded_pics']." 
                        WHERE record_id = '".$id."' AND record_type = '".$where."' 
                        ORDER BY id_in_record desc ");
          if($db->num_rows > 0){
            $is_default = 0;
            $id_in_record = $if_exists->id_in_record+1;
          }else{
            $is_default = 1;
            $id_in_record = 1;
          }

          if(!empty($_POST["pics_text"][$key])){
          	  $file['name'] = trim($_POST["pics_text"][$key]);
          }elseif(!empty($_POST["clear_pic_title"])){
			  $file['name'] = '';
		  }

          $size =@ GetImageSize($file['tmp_name']);
          if(!$size){ return; }
          
          /* Проверим, если хранить оригинал надо, то запишем */
          if($admin_vars['save_original_fotos'] == 1){
            $db->query("INSERT INTO ".$db->tables['uploaded_pics']." (id_exists, 
                        record_id, record_type, width, height, title, ext, id_in_record, 
                        is_default) VALUES ('0', '".$id."', '".$where."', '".$size[0]."', 
                        '".$size[1]."', '".$db->escape($file['name'])."', 
                        '".$file_type."', '".$id_in_record."', '".$is_default."')"); 
            $file_id = $db->insert_id;
            $new_file_name = "../upload/records/".$file_id.".".$file_type;
            if(!empty($db->last_error)){ return db_error(basename(__FILE__).": 3060"); }

            admin_crop_image($file['tmp_name'], 
                        $new_file_name, 
                        $size[0], $size[1], 
                        $size);
          
          }
          
          // пройдемся по массиву заданных размеров
          foreach($sizes as $v){
			  
			$diff_1 = ($size[0] - $v['width']) + ($size[1] - $v['height']);
			$skip = 0;
			if($size[0] == $v['width'] && $v['height'] == 0){ $skip = 1; }
			if($size[1] == $v['height'] && $v['width'] == 0){ $skip = 1; }
			
			if($size[0] < $v['width'] && $v['height'] == 0){ $skip = 1; }
			if($size[1] < $v['height'] && $v['width'] == 0){ $skip = 1; }
			
			if($size[0] < $v['width'] && $size[1] < $v['height']){ $skip = 1; }
			
			if(($size[0] > $v['width'] || $size[1] > $v['height']) 
				&& ($diff_1 > 0) && $skip == 0){
			
			/* 
            if(($size[0] > $v['width'] && $size[1] > $v['height']) || empty($admin_vars['save_original_fotos'])){
            */
              // посчитаем какие размеры у файлов будут
              if($v['width'] > 0 && $v['height'] > 0){
                $width = $v['width'];
                $height = $v['height'];
				if($size[0] < $v['width']){ $width = $size[0]; }
				if($size[1] < $v['height']){ $height = $size[1]; }
              }else{
                if($v['width'] > 0){
                  $width = $v['width'];
                  $height = $width*$size[1]/$size[0];
                }else{
                  $height = $v['height'];
                  $width = $height*$size[0]/$size[1];
                }
              }
			  $width = intval($width);
			  $height = intval($height);

					$db->query("INSERT INTO ".$db->tables['uploaded_pics']." (id_exists, 
                        record_id, record_type, width, height, title, ext, id_in_record, 
                        is_default) VALUES ('0', '".$id."', '".$where."', '".$width."', 
                        '".$height."', '".$db->escape($file['name'])."', 
                        '".$file_type."', '".$id_in_record."', '".$is_default."')"); 
                    $file_id = $db->insert_id;
                    $new_file_name = "../upload/records/".$file_id.".".$file_type;
                    if(!empty($db->last_error)){ return db_error(basename(__FILE__).": 3074"); }

                    admin_crop_image($file['tmp_name'], 
                        $new_file_name, 
                        intval($width), intval($height), 
                        $size);                      

          }
        }              
      }
    }
	
    return;
  }
  




  // ok, upload fotos for records
  function add_new_fotos_path($id, $where, $path='')
	{       
    global $db, $site_vars, $admin_vars;
    if(empty($path)){
		if(empty($_FILES["pics"]["size"][0])){
			return;
		}else{
			$path = $_FILES["pics"];
		}
	}
	
    $sizes = array();
    if(isset($site_vars['img_size1'])){ $sizes[] = $site_vars['img_size1']; }
    if(isset($site_vars['img_size2'])){ $sizes[] = $site_vars['img_size2']; }
    if(isset($site_vars['img_size3'])){ $sizes[] = $site_vars['img_size3']; }
    if(isset($site_vars['img_size4'])){ $sizes[] = $site_vars['img_size4']; }
    if(isset($site_vars['img_size5'])){ $sizes[] = $site_vars['img_size5']; }
    if(isset($site_vars['img_size6'])){ $sizes[] = $site_vars['img_size6']; }
    if(isset($site_vars['img_size7'])){ $sizes[] = $site_vars['img_size7']; }
    if(isset($site_vars['img_size8'])){ $sizes[] = $site_vars['img_size8']; }
    if(isset($site_vars['img_size9'])){ $sizes[] = $site_vars['img_size9']; }
    if(isset($site_vars['img_size10'])){ $sizes[] = $site_vars['img_size10']; }

    $new_sizes = array();
    foreach($sizes as $v){  
      if($v['width'] > 0 || $v['height'] > 0){
        $new_sizes[] = $v;     
      }
    }
    $sizes = $new_sizes;
    if(empty($sizes)){ return; } 
	$file_ary = reArrayFiles($path);
	
    foreach ($file_ary as $key=>$file) {
            
      switch ($file['type']) {
        case 'image/jpeg':
          $file_type = 'jpg';
          break;
        case 'image/png':
          $file_type = 'png';
          break;
        case 'image/gif':
          $file_type = 'gif';
          break;
        default:
          $file_type = 'wrong';
          break;
      }			

      if($file_type != "wrong" && $file['size'] > 0){

          $if_exists = $db->get_row("SELECT * 
						FROM ".$db->tables['uploaded_pics']." 
                        WHERE record_id = '".$id."' 
							AND record_type = '".$where."' 
                        ORDER BY id_in_record desc ");
          if($db->num_rows > 0){
            $is_default = 0;
            $id_in_record = $if_exists->id_in_record+1;
          }else{
            $is_default = 1;
            $id_in_record = 1;
          }

          if(!empty($_POST["pics_text"][$key])){
          	  $file['name'] = trim($_POST["pics_text"][$key]);
          }

          $size =@ GetImageSize($file['tmp_name']);
          if(!$size){ return; }
          
          /* Проверим, если хранить оригинал надо, то запишем */
          if($admin_vars['save_original_fotos'] == 1){
            $db->query("INSERT INTO ".$db->tables['uploaded_pics']." (id_exists, 
                        record_id, record_type, width, height, title, ext, id_in_record, 
                        is_default) VALUES ('0', '".$id."', '".$where."', '".$size[0]."', 
                        '".$size[1]."', '".$db->escape($file['name'])."', 
                        '".$file_type."', '".$id_in_record."', '".$is_default."')"); 
            $file_id = $db->insert_id;
            $new_file_name = "../upload/records/".$file_id.".".$file_type;
            if(!empty($db->last_error)){ return db_error(basename(__FILE__).": 3060"); }

            admin_crop_image($file['tmp_name'], 
                        $new_file_name, 
                        $size[0], $size[1], 
                        $size);
          
          }
          
          // пройдемся по массиву заданных размеров
          foreach($sizes as $v){
            if(($size[0] > $v['width'] && $size[1] > $v['height']) || empty($admin_vars['save_original_fotos'])){
            
              // посчитаем какие размеры у файлов будут
              if($v['width'] > 0 && $v['height'] > 0){
                $width = $v['width'];
                $height = $v['height'];
              }else{
                if($v['width'] > 0){
                  $width = $v['width'];
                  $height = $width*$size[1]/$size[0];
                }else{
                  $height = $v['height'];
                  $width = $height*$size[0]/$size[1];
                }
              }

                    $db->query("INSERT INTO ".$db->tables['uploaded_pics']." (id_exists, 
                        record_id, record_type, width, height, title, ext, id_in_record, 
                        is_default) VALUES ('0', '".$id."', '".$where."', '".$width."', 
                        '".$height."', '".$db->escape($file['name'])."', 
                        '".$file_type."', '".$id_in_record."', '".$is_default."')"); 
                    $file_id = $db->insert_id;
                    $new_file_name = "../upload/records/".$file_id.".".$file_type;
                    if(!empty($db->last_error)){ return db_error(basename(__FILE__).": 3012"); }

                    admin_crop_image($file['tmp_name'], 
                        $new_file_name, 
                        intval($width), intval($height), 
                        $size);                      

          }
        }              
      }
    }
    return;
  }



/* ok, filesize for well-view datas */
function well_size($data)
{
	$size = intval($data);
	if($size > 0 && $size<=1024) $data = $size.' bytes';
	else if($size == 0) $data = '0';
	else if($size<=1024*1024) $data = round($size/(1024),2).' Kb';
	else if($size<=1024*1024*1024) $data = round($size/(1024*1024),2).' Mb';
	else if($size<=1024*1024*1024*1024) $data = round($size/(1024*1024*1024),2).' Gb';
	else if($size<=1024*1024*1024*1024*1024) $data = round($size/(1024*1024*1024*1024),2).' Tb';
	else $data = round($size/(1024*1024*1024*1024*1024),2).' Pb'; // ;-) 
	return $data;
}


function _red($str, $align="left")
{
		return '<span style="color:red; font-weight:bold; text-align: '.$align.'">'.$str.'</span>';
}


/* load currency rate from cbrf */
	function load_currency_rate($vars){
		global $db;
		$d = isset($vars['sys_autoload_rate']) ? $vars['sys_autoload_rate'] : 1;
		$d = $d == 1 ? strtotime("+1 day") : time();
		$date = date("d/m/Y", $d); // формат даты
		$link = "http://www.cbr.ru/scripts/XML_daily.asp?date_req=$date"; // Ссылка на XML-файл с курсами валют
		
		@$all = simplexml_load_file($link);
		if($all === FALSE){ return; }
		$data = array();

		foreach($all->Valute as $c){
			if($c->CharCode == 'USD' || $c->CharCode == 'EUR'){
				$v = array(
					'NumCode' => $c->NumCode,
					'CharCode' => $c->CharCode,
					'Nominal' => $c->Nominal,
					'Name' => $c->Name,
					'Value' => $c->Value,
					'currency' => $c->NumCode,
					'kurs' => $c->Value
				);
				$k = str_replace(',','.',strval($c->Value));
				$data[strval($c->CharCode)] = $k;
				$str = strval($c->CharCode) == 'USD' ? 'kurs_usd' : 'kurs_euro';
				register_changes($str, $vars['id'], $k);
			}
		}
		
		return $data;
	}	



?>