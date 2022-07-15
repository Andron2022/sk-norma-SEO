<?php
if(!defined('SIMPLA_ADMIN')){ die(); }

/**********************************
***********************************
**	
**	Функции для работы с блоками на страницах
**	updated 21.03.2019
**	
**	  
***********************************
**********************************/

  
  
  /*adding blocks */
function add_blocks($id, $where='product')
{
	global $db, $site;
	/*
	echo '<pre>';
print_r($_POST);	
print_r($_FILES);	

exit;
*/
	$sizes = array();
	if(!empty($site->vars['img_size1'])){
		$sizes[] = $site->vars['img_size1'];
	}
	
	if(!empty($site->vars['img_size2'])){
		$sizes[] = $site->vars['img_size2'];
	}

	if(!empty($site->vars['img_size3'])){
		$sizes[] = $site->vars['img_size3'];
	}

	if(!empty($site->vars['img_size4'])){
		$sizes[] = $site->vars['img_size4'];
	}

	if(!empty($site->vars['img_size5'])){
		$sizes[] = $site->vars['img_size5'];
	}

	if(!empty($site->vars['img_size6'])){
		$sizes[] = $site->vars['img_size6'];
	}	
		
	if(!empty($_POST['block']['qty'])
	){
		foreach($_POST['block']['qty'] as $k => $v){
			if(
			!empty($_POST['block']['sort'][$k]) || 
			!empty($_POST['block']['title'][$k]) || 
			!empty($_POST['block']['title_admin'][$k]) || 
			!empty($_POST['block']['text'][$k]) ||
			!empty($_FILES['block']['size']['file'][$k])
			){
					
				$where_placed = !empty($_POST['block']['gruppa'][$k]) ? $_POST['block']['gruppa'][$k] : '';
				$sort = !empty($_POST['block']['sort'][$k]) ? $_POST['block']['sort'][$k] : '';
				$active = !empty($_POST['block']['active'][$k]) ? 1 : 0;
				$qty = !empty($_POST['block']['qty'][$k]) ? intval($_POST['block']['qty'][$k]) : 1;
				$title = !empty($_POST['block']['title'][$k]) ? trim($_POST['block']['title'][$k]) : '';
				$title_admin = !empty($_POST['block']['title_admin'][$k]) ? trim($_POST['block']['title_admin'][$k]) : '';
				$extra = !empty($_POST['block']['extra'][$k]) ? trim($_POST['block']['extra'][$k]) : '';
				$comment = !empty($_POST['block']['comment'][$k]) ? trim($_POST['block']['comment'][$k]) : '';
				$text = !empty($_POST['block']['text'][$k]) ? trim($_POST['block']['text'][$k]) : '';
			
			/*
pages = extra
skip_pages = comment
			*/
				$sql = "INSERT INTO `".$db->tables['blocks']."` 
							(`active`, `where_placed`, 
							`title`, `title_admin`, 
							`qty`, `type`, 
							`type_id`, `pages`, 
							`skip_pages`, `html`, 
							`sort`) 
							VALUES ('1', '".$db->escape($where_placed)."', 
							'".$db->escape($title)."', '".$db->escape($title_admin)."', 
							'".$qty."', '".$where."', 
							'".$id."', '".$db->escape($extra)."', 
							'".$db->escape($comment)."', '".$db->escape($text)."', 
							'".$db->escape($sort)."')";
				$db->query($sql);
				$bid = $db->insert_id;
				if($bid > 0){
					/* если есть картинка, то и ее сохраним */
					if(!empty($_FILES['block']['size']['file'][$k])){
						foreach($_FILES['block']['size']['file'][$k] as $kf => $vf){
							if($vf == 0){
								/* demo pic for block element */

								$f_arr = array(
									'name' => array('element'),
									'type' => array('image/gif'),
									'tmp_name' => array(PATH.'/'.ADMIN_FOLDER.'/images/notice.gif'),
									'size' => array(156),
									'error' => array(0)
								);
							}else{

								$f_arr = array(
									'name' => array($_FILES['block']['name']['file'][$k][$kf]),
									'type' => array($_FILES['block']['type']['file'][$k][$kf]),
									'tmp_name' => array($_FILES['block']['tmp_name']['file'][$k][$kf]),
									'size' => array($_FILES['block']['size']['file'][$k][$kf]),
									'error' => array($_FILES['block']['error']['file'][$k][$kf])
								);
							}
							add_new_fotos_path($bid, 'block', $f_arr);								

						}						
						
					}
				}
			}
		}
		
	}
	return;
}
  
  
  
/* updating blocks */
function update_blocks($id, $where)
{
	global $db;
	if(!empty($_POST['block_update']['sort'])){
		foreach($_POST['block_update']['sort'] as $k => $v){
			$sort = intval($_POST['block_update']['sort'][$k]);
			$active = !empty($_POST['block_update']['active'][$k]) ? 1 : 0;
			$title_admin = isset($_POST['block_update']['title_admin'][$k]) ? trim($_POST['block_update']['title_admin'][$k]) : '';

			$gruppa = isset($_POST['block_update']['gruppa'][$k]) ? trim($_POST['block_update']['gruppa'][$k]) : 'manual';
			$qty = intval($_POST['block_update']['qty'][$k]);
			$title = isset($_POST['block_update']['title'][$k]) ? trim($_POST['block_update']['title'][$k]) : '';
			$text = isset($_POST['block_update']['text'][$k]) ? trim($_POST['block_update']['text'][$k]) : '';

			$extra = isset($_POST['block_update']['extra'][$k]) ? trim($_POST['block_update']['extra'][$k]) : '';
			$comment = isset($_POST['block_update']['comment'][$k]) ? trim($_POST['block_update']['comment'][$k]) : '';
			
			$sql = "UPDATE `".$db->tables['blocks']."` SET 
				`sort` = '".$sort."',
				`title` = '".$db->escape($title)."',
				`html` = '".$db->escape($text)."',
				`active` = '".$active."',
				`title_admin` = '".$db->escape($title_admin)."',
				`qty` = '".$db->escape($qty)."',
				`where_placed` = '".$db->escape($gruppa)."',
				`pages` = '".$db->escape($extra)."',
				`skip_pages` = '".$db->escape($comment)."'
				WHERE id = '".$k."'
			";
			$db->query($sql);

			/* если загружен файл, то добавим его */
			if(!empty($_FILES['block']['size']['add_file'][$k])){
				foreach($_FILES['block']['size']['add_file'][$k] as $kf => $vf){
					if($vf == 0){
						//demo pic for block element
						$f_arr = array(
							'name' => array('element'),
							'type' => array('image/gif'),
							'tmp_name' => array(PATH.'/'.ADMIN_FOLDER.'/images/notice.gif'),
							'size' => array(156),
							'error' => array(0)
						);
					}else{
						$f_arr = array(
							'name' => array($_FILES['block']['name']['add_file'][$k][$kf]),
							'type' => array($_FILES['block']['type']['add_file'][$k][$kf]),
							'tmp_name' => array($_FILES['block']['tmp_name']['add_file'][$k][$kf]),
							'size' => array($_FILES['block']['size']['add_file'][$k][$kf]),
							'error' => array($_FILES['block']['error']['add_file'][$k][$kf])
						);
					}
					add_new_fotos_path($k, 'block', $f_arr);
				}
			}

		}
	}
	
	/* удалим фото */
	if(!empty($_POST['del_block_foto'])){
		$rebuild_fotos = array();
		foreach($_POST['del_block_foto'] as $k => $v){
			foreach($v as $file_del){ // id_in_record
				$sql = "SELECT * FROM `{$db->tables['uploaded_pics']}` 
					WHERE `record_id` = '".$k."' 
					AND `record_type` = 'block' 
					AND `id_in_record` = '".$file_del."'
				";
				$rows = $db->get_results($sql);
				foreach($rows as $row){
					delete_uploaded_pics($k, 'block', $row->id, true);	
				}
				$rebuild_fotos[$k] = 1;				
			}
		}
	}
	
	/*Если есть обновление фото у объектов */
	if(!empty($_POST['b_photo'])){
		
		foreach($_POST['b_photo'] as $k => $v){
			foreach($v as $v1 => $v2){
if(isset($v2['id_in_record'])){
				// $k - block
				// $v1 - old - id_in_record
				// $v2['id_in_record'] - new id_in_record
				// $v2['title'] - title
				$new_id_record = ($v2['id_in_record']*1000)+$v1;
				$title = trim($v2['title']);
				$ext_h1 = trim($v2['ext_h1']);
				$ext_link = trim($v2['ext_link']);
				$ext_desc = trim($v2['ext_desc']);
				$sql = "UPDATE `{$db->tables['uploaded_pics']}` 
					SET 
						`title` = '".$db->escape($title)."',
						`ext_h1` = '".$db->escape($ext_h1)."',
						`ext_link` = '".$db->escape($ext_link)."',
						`ext_desc` = '".$db->escape($ext_desc)."'
					WHERE record_type = 'block' 
						AND record_id = '".$k."' 
						AND id_in_record = '".$v1."'
				";
				$db->query($sql);
				
				//if($v1 != $v2['id_in_record']){
					/* обновим номер */
					$sql = "SELECT * 
						FROM `{$db->tables['uploaded_pics']}` 
						WHERE record_type = 'block' 
						AND record_id = '".$k."' 
						AND id_in_record = '".$v1."'
					";
					$rows = $db->get_results($sql);
					$n = intval($v2['id_in_record'])*1000+$v1;
					
					if($db->num_rows > 0){
						$sql = "UPDATE `{$db->tables['uploaded_pics']}` 
							SET 
								id_in_record = '".$n."', 
								is_default = '0'
							WHERE 
								record_type = 'block' 
								AND record_id = '".$k."' 
								AND id_in_record = '".$v1."' 
						";
						$db->query($sql);
						$pics_changed = 1;
					}
					
				//} окончание обновления номера
				
}				
			}
			
			/* if pics_changed build new id_in_record */
			if(!empty($pics_changed) || !empty($rebuild_fotos[$k])){
				
				foreach($v as $v1 => $v2){
					$sql = "SELECT DISTINCT id_in_record 
						FROM `{$db->tables['uploaded_pics']}` 
						WHERE record_type = 'block' 
						AND record_id = '".$k."' 
						AND id_in_record > '999' 
						ORDER BY id_in_record 
					";
					$rows = $db->get_results($sql);
					$i = 1;
					if($db->num_rows > 0){
						$n = 0;
						foreach($rows as $row){
							$is_def = $i == 1 ? 1 : 0;
							$n++;
							//$n = ceil($row->id_in_record/1000);
							$sql = "UPDATE `{$db->tables['uploaded_pics']}` 
								SET 
									id_in_record = '".$n."', 
									is_default = '".$is_def."'
								WHERE 
									record_type = 'block' 
									AND record_id = '".$k."' 
									AND id_in_record = '".$row->id_in_record."' 
							";
							$db->query($sql);
							$i++;
						}
						
					}
					unset($i);
				}
			}
			
			unset($pics_changed);
		}
	}
	
	if(!empty($_POST['delete_block'])){
		foreach($_POST['delete_block'] as $v){
			delete_uploaded_pics($v, 'block', false, true);
			$sql = "DELETE FROM `".$db->tables['blocks']."` 
				WHERE id = '".$v."' ";
			$db->query($sql);
		}
	}
	return;		  
  }
  
/* get blocks info */
function get_blocks_info($id, $where)
{
	global $db, $site;

	$sql = "SELECT b.*, 
				pic1.id AS pic1_id,
				pic1.ext AS pic1_ext,
				pic1.title AS pic1_name,
				pic1.width AS pic1_width,
				pic1.height AS pic1_height, 		
				pic2.id AS pic2_id,
				pic2.ext AS pic2_ext,
				pic2.title AS pic2_name,
				pic2.width AS pic2_width,
				pic2.height AS pic2_height,
				
				(SELECT COUNT(DISTINCT id_in_record) 
					FROM `{$db->tables['uploaded_pics']}`  
					WHERE record_type = 'block' 
					AND record_id = b.id 
				) as qty_photos
				
			FROM `{$db->tables['blocks']}` b 
			
			LEFT JOIN
						(SELECT record_id,id,ext,title,width,height
							FROM(
								SELECT *
								FROM {$db->tables['uploaded_pics']} p 
								WHERE record_type = 'block' 
								ORDER by width 
							) p GROUP BY record_id
						) pic1 ON b.id = pic1.record_id 
			LEFT JOIN
						(SELECT record_id,id,ext,title,width,height
							FROM(
								SELECT *
								FROM {$db->tables['uploaded_pics']} p 
								WHERE record_type = 'block' 
								ORDER by width desc 
							) p GROUP BY record_id 
						) pic2 ON b.id = pic2.record_id 
			
			WHERE b.`type` = '".$where."' AND b.`type_id` = '".$id."' 
			ORDER BY b.`sort`, b.`title` ";
			
	$blocks = $db->get_results($sql, ARRAY_A);
	//$db->debug(); exit;
	if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }  
	if($blocks && $db->num_rows > 0){
		include_once(MODULE.'/list_photos.php');
		foreach($blocks as $k => $v){
			 $blocks[$k]['list_photos'] = list_photos('block', $v['id'], $site);
		}
	}	
	
	if(!empty($_GET['del_block_foto']) 
		&& !empty($_GET['block_id']) 
		&& isset($_GET['photo']) 
	){
		/* удалим фото для блока */
		$sql = "SELECT * FROM `{$db->tables['uploaded_pics']}` 
			WHERE record_type = 'block' 
			AND record_id = '".intval($_GET['block_id'])."' 
			AND id_in_record = '".intval($_GET['photo'])."'
		";
		$rows = $db->get_results($sql);
		
		if($rows && $db->num_rows > 0){
			foreach($rows as $row){
				delete_uploaded_pics(
					intval($_GET['block_id']), 
					'block', 
					$row->id, 
					true
				);
			}
		}

		$url = isset($_SERVER["QUERY_STRING"]) 
			? $_SERVER["QUERY_STRING"] : '';		
		if(!empty($url)){
			$url = str_replace('&del_block_foto=1', '', $url);
			$url = str_replace('&block_id='.intval($_GET['block_id']), '', $url);
			$url = str_replace('&photo='.intval($_GET['photo']), '', $url);
			$url .= '&updated=1';
			header("Location: ?".$url);
			exit;
		}		
	}
	return $blocks;	  
}
  
 
/* deleting blocks */ 
function delete_blocks($id, $where)
{
	global $db;
	$blocks = $db->get_results("SELECT * 
		FROM `".$db->tables['blocks']."` 
		WHERE `type` = '".$where."' 
			AND type_id = '".$id."' ");
	if($db->num_rows > 0){
		foreach($blocks as $b){
			delete_uploaded_pics($b->id, 'block', false, true);
			$sql = "DELETE FROM `".$db->tables['blocks']."` 
				WHERE id = '".$b->id."' ";
			$db->query($sql);
		}
	}
	
	return;	
}





?>