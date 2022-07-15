<?php

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
    *
    ***********************/ 
       
    $s = new Cms;

    class Cms extends Site {

        function __construct()
        {
        }
                
        static public function get_orgs($site)
        {
			global $tpl, $db, $path;
			include_once('cms_fns.php');
			$site->tpl = "/tpl/cms/";
			if(empty($site->user['id'])){
				$ar['need_auth'] = 1;
				$ar['page'] = 'pages/403.html';
				return $ar;
			}elseif(empty($site->user['prava']['org'])){
				$ar['page'] = 'pages/403.html';
				return $ar;
			}else{				
				$ar['page'] = 'pages/orgs/list.html';
			}
			
			$ar['title'] = $site->GetMessage('cms', 'orgs', 'title');
			$ar['metatitle'] = $site->GetMessage('cms', 'orgs', 'title');
			$ar['breadcrumbs'][] = array(
				'title' => $site->GetMessage('cms', 'orgs', 'title'),
				'link' => $site->vars['site_url'].'/cms/orgs/'
			);
			
			$id = isset($_GET['id']) ? intval($_GET['id']) : NULL;
			$ar['id'] = $id;
			
			if($id === NULL){
				$c = !empty($site->uri['params']['c']) ? $site->uri['params']['c'] : '';
				if($c == 'set'){
					
				}elseif($c == 'export'){
					$sql = "SELECT * FROM ".$db->tables['org']." ";
					$fields = array('id','title', 'inn');
					cms_to_csv($sql, 'org', $fields);
				}elseif($c == 'import'){
					return Cms::get_import($site, $ar);
				}
				
				$ar['settings_menu'] = Cms::get_settings_menu($site,$ar);
				return Cms::get_list($site, $ar);
			}elseif($id > 0){
				return Cms::view_org($site, $ar);
			}else{
				return Cms::add_org($site, $ar);
			}
			exit;
			
        }
		
		function get_settings_menu($site,$ar){
			/* зададим меню для настроек */
			/* настройка столбцов для списка */
			/* выгрузка всех строк в формате CSV */
			/* загрузка в формате CSV */
			$arr = array();
			$arr[] = array(
				'link' => '/cms/orgs/?c=set',
				'title' => 'Настройка столбцов'
			);
			$arr[] = array(
				'link' => '/cms/orgs/?c=export',
				'title' => 'Скачать в CSV',
				'new' => 1
			);
			$arr[] = array(
				'link' => '/cms/orgs/?c=import',
				'title' => 'Импорт из CSV'
			);
			return $arr;			
		}
		
		function get_list($site, $ar){
			if(!empty($site->uri['params']['where'])){
				$ar['title'] .= ': '.$site->GetMessage('cms', 'orgs', 'own');
				$ar['metatitle'] .= ': '.$site->GetMessage('cms', 'orgs', 'own');
				$own = 1;
			}elseif(!empty($site->uri['params']['q'])){
				$q = trim($site->uri['params']['q']);
				$ar['title'] .= ': '.$q;
				$ar['metatitle'] .= ': '.$q;
			}
			global $db;
			$sql = "SELECT * ";
			$sql_num = "SELECT COUNT(*) ";
			
			$sql_num .= " FROM ".$db->tables['org']." ";
			$sql .= " FROM ".$db->tables['org']." ";
			
			if(!empty($own)){
				$sql .= " WHERE `own` = '1' ";
				$sql_num .= " WHERE `own` = '1' ";
			}elseif(!empty($q)){
				$where_str = " WHERE 
						`title` LIKE '%".$q."%' OR 
						`postal_code` LIKE '%".$q."%' OR 
						`country` LIKE '%".$q."%' OR 
						`city` LIKE '%".$q."%' OR 
						`address` LIKE '%".$q."%' OR 
						`inn` LIKE '%".$q."%' OR 
						`bik` LIKE '%".$q."%' OR 
						`bank` LIKE '%".$q."%' OR 
						`phone` LIKE '%".$q."%' OR 
						`post_address` LIKE '%".$q."%' OR 
						`fio_dir` LIKE '%".$q."%' OR 
						`fio_buh` LIKE '%".$q."%' OR 
						`ogrn` LIKE '%".$q."%' OR 
						`website` LIKE '%".$q."%' OR 
						`email` LIKE '%".$q."%' 
				";		
				$sql .= $where_str;
				$sql_num .= $where_str;
			}		
					
			$sql .= " ORDER BY `own` desc, `is_default` desc, 
						`active` desc, `title`
			";
			
			$ar['all_results'] = $db->get_var($sql_num);
			$onpage = ONPAGE;
			$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
			if($ar['all_results'] > $onpage){
				$start = $page*$onpage;
				$sql .= " LIMIT ".$start.", ".$onpage;
			}
			
			$ar['list'] = $db->get_results($sql, ARRAY_A);			
			$link = $site->uri['requested'];
			$start_link = '?';
			if(!empty($q) && !empty($own)){
				$link .= '?q='.$q;
				$link .= '&where=own';
				$start_link = '&';
			}elseif(!empty($q)){
				$link .= '?q='.$q;
				$start_link = '&';
			}elseif(!empty($own)){
				$link .= '?where=own';
				$start_link = '&';
			}
			
			$ar['pages'] = array(
				'link' => $link,
				'start_link' => $start_link,
				'pages' => ceil($ar['all_results']/$onpage),
				'current' => $page
			);
			
			if(!empty($db->last_error)){ return $site->db_error("File ".__FILE__."<br>Line ".__LINE__); }

			return $ar;
		}
		
		function view_org($site, $ar){
			//$ar['id']
			global $db;
			$row = $db->get_row("SELECT o.*, 
			
					(SELECT COUNT(*) 
					FROM ".$db->tables['connections']."
					WHERE name1 = 'order'
					AND id2 = o.id 
					AND name2 = 'org') as qty_orders, 
					
					(SELECT COUNT(*) 
					FROM ".$db->tables['connections']."
					WHERE name1 = 'org'
					AND id1 = o.id 
					AND name2 = 'site' 
					AND o.is_default = '1') as qty_sites, 
					
					f1.id as dir_id, 
					f2.id as buh_id, 
					f3.id as logo_id, 
					f4.id as stamp_id, 
					f1.ext as dir_ext, 
					f2.ext as buh_ext, 
					f3.ext as logo_ext, 
					f4.ext as stamp_ext
					
				FROM ".$db->tables['org']." o 
				LEFT JOIN ".$db->tables['uploaded_files']." f1 on (o.id = f1.record_id AND f1.record_type = 'org_dir') 
				
				LEFT JOIN ".$db->tables['uploaded_files']." f2 on (o.id = f2.record_id AND f2.record_type = 'org_buh') 
				
				LEFT JOIN ".$db->tables['uploaded_files']." f3 on (o.id = f3.record_id AND f3.record_type = 'org_logo') 
				
				LEFT JOIN ".$db->tables['uploaded_files']." f4 on (o.id = f4.record_id AND f4.record_type = 'org_stamp')
				
				WHERE o.id = '".$ar['id']."'
			", ARRAY_A);
			if(!empty($db->last_error)){ return $site->db_error("File ".__FILE__."<br>Line ".__LINE__); }
			
			if($db->num_rows == 0){
				$str = '<a href="/cms/orgs/">'.$site->GetMessage('cms', 'orgs', 'list').'</a>';
				return $site->error_page(404, $str);				
			}
			$ar['org'] = $row;
			
			$sql = "SELECT c.*, u.login as user_login, u.name as user_name 
					FROM ".$db->tables['changes']." c
					LEFT JOIN ".$db->tables['users']." u on (c.who_changed = u.id)
					WHERE c.`where_changed` = 'org' 
						AND c.`where_id` = '".$ar['id']."' 
					ORDER BY c.date_insert DESC, c.id DESC ";
			if(empty($site->uri['params']['where'])){
				$sql .= " LIMIT 0, ".ONPAGE;
			}
			
			$ar['logs'] = $db->get_results($sql, ARRAY_A);	
			
			$ar['title'] = $row['title'];
			$ar['page'] = 'pages/orgs/view.html';
			
			if(!empty($site->uri['params']['where'])){
				$ar['title'] = $site->GetMessage('cms', 'orgs', 'logs');
				$ar['title'] .= ' '.$row['title'];
				$ar['breadcrumbs'][] = array(
					'title' => $row['title'],
					'link' => $site->vars['site_url'].'/cms/orgs/?id='.$row['id']
				);				
			}

			$ar['metatitle'] = $site->GetMessage('cms', 'orgs', 'view').': '.$row['title'];
			
			if(!empty($site->uri['params']['edit'])){

				if(empty($site->user['prava']['settings']) && !empty($row['own'])){
					$ar['error'] = $site->GetMessage('cms', 'orgs', 'edit_own_no_permitten');
				}else{
				
					$ar['title'] = $site->GetMessage('cms', 'orgs', 'edit');
					$ar['title'] .= ' '.$row['title'];
					$ar['breadcrumbs'][] = array(
						'title' => $row['title'],
						'link' => $site->vars['site_url'].'/cms/orgs/?id='.$row['id']
					);
					$ar['page'] = 'pages/orgs/edit.html';
					
					if(!empty($_POST['org'])){
						if(!empty($_POST['org']['delete'])){
							return Cms::delete_org($site, $ar);
						}else{
							return Cms::save_org($site, $ar);
						}
					}
				}
			
			}
			
			return $ar;
		}
		
		function delete_org($site, $ar){
			if(!empty($ar['org']['qty_orders']) || !empty($ar['org']['qty_sites'])){
				$ar['error'] = $site->GetMessage('cms', 'orgs', 'delete_impossible');
				return $ar;
			}
			
			global $db;
			/* удаляем организацию */
			$sql = "DELETE FROM ".$db->tables['org']." 
					WHERE id = '".$ar['id']."'
			";
			$db->query($sql);
			if(!empty($db->last_error)){ 
				return $site->db_error("File ".__FILE__."<br>Line ".__LINE__); 
			}
			
			/* удаляем связанные файлы */
			$f_types = array('dir','buh','logo','stamp');
			foreach($f_types as $v){
				$changes = array(
					'where' => 'org', 
					'id' => $ar['id'],
					'type' => 'delete_file',
					'comment' => $v.' for '.$ar['org']['title']
				);
				cms_delete_file($site, 'org_'.$v, $ar['id'], $changes);
			}
			
			/* делаем запись в БД про удаление $ar['title'] */
			$site->register_changes('org', $ar['id'], 'delete', $ar['org']['title']);
			
			$url = $site->vars['site_url'].'/cms/orgs/?deleted=1';
			return $site->redirect($url);
		}
		
		function add_org($site, $ar){
			$ar['title'] = 'Добавление организации';
			$ar['metatitle'] = 'Добавление организации';
			$ar['page'] = 'pages/orgs/edit.html';
			if(!empty($_POST['org'])){
				return Cms::save_org($site, $ar);
			}
			return $ar;
		}
		
		function save_org($site, $ar){
			global $db;			

			$o = array();
			$o['active'] = !empty($_POST['org']['active']) ? 1 : 0;
			$o['own'] = !empty($_POST['org']['own']) ? 1 : 0;
			$o['is_default'] = !empty($_POST['org']['is_default']) ? 1 : 0;
			
			$o['title'] = !empty($_POST['org']['title']) ? trim($_POST['org']['title']) : '';
			$o['phone'] = !empty($_POST['org']['phone']) ? trim($_POST['org']['phone']) : '';
			$o['website'] = !empty($_POST['org']['website']) ? trim($_POST['org']['website']) : '';
			$o['email'] = !empty($_POST['org']['email']) ? trim($_POST['org']['email']) : '';
			$o['postal_code'] = !empty($_POST['org']['postal_code']) ? trim($_POST['org']['postal_code']) : '';
			$o['country'] = !empty($_POST['org']['country']) ? trim($_POST['org']['country']) : '';
			$o['city'] = !empty($_POST['org']['city']) ? trim($_POST['org']['city']) : '';
			$o['address'] = !empty($_POST['org']['address']) ? trim($_POST['org']['address']) : '';
			$o['nds'] = !empty($_POST['org']['nds']) ? intval($_POST['org']['nds']) : 0;
			$o['inn'] = !empty($_POST['org']['inn']) ? trim($_POST['org']['inn']) : '';
			$o['kpp'] = !empty($_POST['org']['kpp']) ? trim($_POST['org']['kpp']) : '';
			$o['bik'] = !empty($_POST['org']['bik']) ? trim($_POST['org']['bik']) : '';
			$o['r_account'] = !empty($_POST['org']['r_account']) ? trim($_POST['org']['r_account']) : '';
			$o['k_account'] = !empty($_POST['org']['k_account']) ? trim($_POST['org']['k_account']) : '';
			$o['bank'] = !empty($_POST['org']['bank']) ? trim($_POST['org']['bank']) : '';
			$o['ogrn'] = !empty($_POST['org']['ogrn']) ? trim($_POST['org']['ogrn']) : '';

			$o['post_address'] = !empty($_POST['org']['post_address']) ? trim($_POST['org']['post_address']) : '';
			$o['fio_dir'] = !empty($_POST['org']['fio_dir']) ? trim($_POST['org']['fio_dir']) : '';
			$o['fio_dir_kratko'] = !empty($_POST['org']['fio_dir_kratko']) ? trim($_POST['org']['fio_dir_kratko']) : '';
			$o['fio_buh'] = !empty($_POST['org']['fio_buh']) ? trim($_POST['org']['fio_buh']) : '';
			$o['fio_buh_kratko'] = !empty($_POST['org']['fio_buh_kratko']) ? trim($_POST['org']['fio_buh_kratko']) : '';
			
			$d = !empty($_POST['Date_Day']) ? trim($_POST['Date_Day']) : '';
			$m = !empty($_POST['Date_Month']) ? trim($_POST['Date_Month']) : '';
			$y = !empty($_POST['Date_Year']) ? trim($_POST['Date_Year']) : ''; 
			
			if(empty($d) || empty($m) || empty($y)){
				$data_reg = '';
			}else{
				$data_reg = $y.'-'.$m.'-'.$d;
			}
			
			$o['data_reg'] = !empty($data_reg) ? trim($data_reg) : '0000-00-00';
			
			if(empty($ar['id'])){
				/* add */
				$sql = "INSERT INTO ".$db->tables['org']." 
						(`active`, `own`, `is_default`, 
						`title`, `phone`, `website`, 
						`email`, `postal_code`, 
						`country`, `city`, `address`, 
						`nds`, `inn`, `kpp`, `bik`, 
						`r_account`, `k_account`, 
						`bank`, `ogrn`, `post_address`, 
						`fio_dir`, `fio_dir_kratko`, 
						`fio_buh`, `fio_buh_kratko`, 
						`data_reg`) VALUES (
						'".$o['active']."', 
						'".$o['own']."', 
						'".$o['is_default']."', 
						'".$db->escape($o['title'])."', 
						'".$db->escape($o['phone'])."', 
						'".$db->escape($o['website'])."', 
						'".$db->escape($o['email'])."', 
						'".$db->escape($o['postal_code'])."', 
						'".$db->escape($o['country'])."', 
						'".$db->escape($o['city'])."', 
						'".$db->escape($o['address'])."', 
						'".$db->escape($o['nds'])."', 
						'".$db->escape($o['inn'])."', 
						'".$db->escape($o['kpp'])."', 
						'".$db->escape($o['bik'])."', 
						'".$db->escape($o['r_account'])."', 
						'".$db->escape($o['k_account'])."', 
						'".$db->escape($o['bank'])."', 
						'".$db->escape($o['ogrn'])."', 
						'".$db->escape($o['post_address'])."', 
						'".$db->escape($o['fio_dir'])."', 
						'".$db->escape($o['fio_dir_kratko'])."', 
						'".$db->escape($o['fio_buh'])."', 
						'".$db->escape($o['fio_buh_kratko'])."', 
						'".$o['data_reg']."'
						)
				";
				$db->query($sql);
				if(!empty($db->last_error)){ 
					return $site->db_error("File ".__FILE__."<br>Line ".__LINE__); 
				}
				$ar['id'] = $db->insert_id;
				$url = $site->vars['site_url'].'/cms/orgs/?id='.$ar['id'].'&added=1';
				
				/* Добавим уведомление в CHANGES*/
				$site->register_changes('org', $ar['id'], 'add');
				
			}else{
				/* update */
				$sql = "UPDATE ".$db->tables['org']." SET 
					`active` = '".$o['active']."', 
					`own` = '".$o['own']."', 
					`is_default` = '".$o['is_default']."', 
					`title` = '".$db->escape($o['title'])."', 
					`phone` = '".$db->escape($o['phone'])."', 
					`website` = '".$db->escape($o['website'])."', 
					`email` = '".$db->escape($o['email'])."', 
					`postal_code` = '".$db->escape($o['postal_code'])."', 
					`country` = '".$db->escape($o['country'])."', 
					`city` = '".$db->escape($o['city'])."', 
					`address` = '".$db->escape($o['address'])."', 
					`nds` = '".$db->escape($o['nds'])."', 
					`inn` = '".$db->escape($o['inn'])."', 
					`kpp` = '".$db->escape($o['kpp'])."', 
					`bik` = '".$db->escape($o['bik'])."', 
					`r_account` = '".$db->escape($o['r_account'])."', 
					`k_account` = '".$db->escape($o['k_account'])."', 
					`bank` = '".$db->escape($o['bank'])."', 
					`ogrn` = '".$db->escape($o['ogrn'])."', 
					`post_address` = '".$db->escape($o['post_address'])."', 
					`fio_dir` = '".$db->escape($o['fio_dir'])."', 
					`fio_dir_kratko` = '".$db->escape($o['fio_dir_kratko'])."', 
					`fio_buh` = '".$db->escape($o['fio_buh'])."', 
					`fio_buh_kratko` = '".$db->escape($o['fio_buh_kratko'])."', 
					`data_reg` = '".$o['data_reg']."' 
				WHERE id = '".$ar['id']."'
				";
				$db->query($sql);
				if(!empty($db->last_error)){ return $site->db_error("File ".__FILE__."<br>Line ".__LINE__); }
				$url = $site->vars['site_url'].'/cms/orgs/?id='.$ar['id'].'&updated=1';

				/* Добавим уведомление в CHANGES*/
				if($db->rows_affected > 0){
					$site->register_changes('org', $ar['id'], 'update');
				}				
			}
			
			/* обработаем удаление фото, если задано */
			if(!empty($_POST['delete_files'])){				
				foreach($_POST['delete_files'] as $k=>$v){
					$changes = array(
						'where' => 'org', 
						'id' => $ar['id'],
						'type' => 'delete_file',
						'comment' => $k
					);					
					cms_delete_file($site, 'org_'.$k, $ar['id'], $changes);
				}
			}
						
			/* обработаем загрузку фото */
			Cms::save_org_files($site, $ar['id']);	
				
			return $site->redirect($url);
		}
		
		
		function save_org_files($site, $id){
			$ar = array('dir', 'logo', 'buh', 'stamp');
			foreach($ar as $v){
				if(!empty($_FILES[$v])){
					$changes = array(
						'where' => 'org', 
						'id' => $id,
						'type' => 'upload_file',
						'comment' => $v,
						'allow_download' => '1',
						'direct_link' => '0' 
					);	
					cms_upload_file($site, 'org_'.$v, $id, $_FILES[$v], $changes, '1');
				}
			}
		
			return;
		}
		
		function get_import($site, $ar){
			global $db;
			$ar['page'] = 'pages/orgs/import.html';
			if(isset($_FILES['csv']) || isset($_POST['confirm'])){
				$ar = cms_from_csv($site, $ar, $db->tables['org']);
			}
			return $ar;
		}
		
        
    }

?>