<?php
/******************************************************************************************
 * Здесь создаем массив из строки
 ******************************************************************************************/

include_once("../../../variable.php");
//$id = isset($_COOKIE['simpla']['option_id']) ? intval($_COOKIE['option_id']) : 0;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$str = $db->get_var("SELECT `if_select` FROM `options` WHERE id = '".$id."' ");

$ar = array();
$str1 = "Испания
-Коста дель Соль
--Малага
--Марбелья
-Коста Брава
--Аликанте
--Бенидорм
-Канарские острова
--Тенерифе
--Ланцероте
Россия
-Московская область
--Подольск
--Химки
-Ленинградская область
--Гатчина
--Выборг";

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
		$ar[$i]['name'] = trim($v);
		$current_country = $i;
		unset($current_region);
	}
}

/*
echo "<pre>";
print_r($ar);
echo "</pre>";
exit;
*/
?>