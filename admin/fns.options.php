<?php
if(!defined('SIMPLA_ADMIN')){ die(); }

/**********************************
***********************************
**	
**	Доп.свойства страниц
**	updated 09.06.2019
**	
**	  
***********************************
**********************************/

  
  
  /*adding extra_options */
function add_extra_options($id, $where)
{
	global $db, $site;
	
	if(!empty($_POST['extra_options']['value'])){
		foreach($_POST['extra_options']['value'] as $k => $v){
			$v1 = trim($v);
			$v2 = isset($_POST['extra_options']['value2'][$k]) 
				? trim($_POST['extra_options']['value2'][$k]) : '';
			$v3 = isset($_POST['extra_options']['value3'][$k]) 
				? trim($_POST['extra_options']['value3'][$k]) : '';
			
			// если какое-то из полей не пустое, то добавляем запись
			if(!empty($v1) || !empty($v2) || !empty($v3)){
				$sql = "
					INSERT INTO `".$db->tables['option_values']."` 
					(`id_option`, `id_product`, `value`, `where_placed`, 
					`value2`, `value3`) 
					VALUES('0', '".$id."', '".$db->escape($v1)."', 
					'".$where."', 
					'".$db->escape($v2)."', 
					'".$db->escape($v3)."') 				
				";
				$db->query($sql);
				clear_cache(0);
			}			
		}
	}
	return;
}
  
  
  
/* updating extra_options */
function update_extra_options($id, $where)
{
	global $db;
	//phpinfo(); exit;
	if(!empty($_POST['update_extra']['value'])){
		foreach($_POST['update_extra']['value'] as $k => $v){
			$v1 = trim($v);
			$v2 = isset($_POST['update_extra']['value2'][$k]) 
				? trim($_POST['update_extra']['value2'][$k]) : '';
			$v3 = isset($_POST['update_extra']['value3'][$k]) 
				? trim($_POST['update_extra']['value3'][$k]) : '';
			// если какое-то из полей не пустое, то обновим запись
			if(empty($v1) && empty($v2) && empty($v3)){
				// все пустые, удаляем
				$sql = "
					DELETE FROM `".$db->tables['option_values']."`  
					WHERE id = '".$k."' 
						AND `id_product` = '".$id."' 
						AND `where_placed` = '".$where."'
				";
				
			}else{
				
				$sql = "
					UPDATE 
						`".$db->tables['option_values']."` 
					SET 
						`value` = '".$db->escape($v1)."',
						`value2` = '".$db->escape($v2)."',
						`value3` = '".$db->escape($v3)."' 
					WHERE 
						id = '".$k."' 	
						AND `id_product` = '".$id."' 
						AND `where_placed` = '".$where."'
				";
			}		
			$db->query($sql);
		}
	}
	
	if(!empty($_POST['delete_extra_options'])){
		foreach($_POST['delete_extra_options'] as $v){
			$sql = "DELETE FROM `".$db->tables['option_values']."` 
				WHERE id = '".$v."' ";
			$db->query($sql);
		}
	}
	clear_cache(0);
	return;		  
  }
  
/* get extra_options info */
function extra_options($id, $where)
{
	global $db, $site;

	$sql = "SELECT *
			FROM `{$db->tables['option_values']}` b 
			
			WHERE `where_placed` = '".$where."' 
				AND `id_product` = '".$id."' 
				AND `id_option` = '0'
			ORDER BY `value3`, `value2` ";
			
	$blocks = $db->get_results($sql, ARRAY_A);
	//$db->debug(); exit;
	if(!empty($db->last_error)){ 
		return db_error(basename(__FILE__)." LINE: ".__LINE__); 
	} 
	return $blocks;	  
}
  
/* удалим все свойства в данном типе записе */
function delete_extra_options($id, $where){
	global $db;
	$sql = "DELETE FROM `".$db->tables['option_values']."` 
			WHERE id_product = '".$id."' 
				AND `where_placed` = '".$where."'
			";
	$db->query($sql);
	clear_cache(0);
	return;
}




?>