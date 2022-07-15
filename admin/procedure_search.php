<?php
if(!defined('SIMPLA_ADMIN')){ die(); }

global $tpl;
$str_content = "";
if(isset($_GET['q']))
{
  if(isset($_GET['where'])){
  	if(isset($_GET['p']) && is_numeric($_GET['p'])){
  		$str_content = search_query($_GET['q'],$_GET['where'],$_GET['p']);
  	}else{
  		$str_content = search_query($_GET['q'],$_GET['where']);
  	}
  }else{
    $str_content = search_query($_GET['q']);
  }
}else{
  $str_content = $tpl->display("index.html");
}

function search_products($q,$limit)
{
	global $db;
	$res_array = array();
	$query = "SELECT * FROM ".$db->tables['products']." 
      WHERE name LIKE '%".$db->escape($q)."%' 
        OR name_short LIKE '%".$db->escape($q)."%' 
        OR memo_short LIKE '%".$db->escape($q)."%' 
        OR memo LIKE '%".$db->escape($q)."%' 
        OR alter_search LIKE '%".$db->escape($q)."%' 
        OR alias LIKE '%".$db->escape($q)."%'";
   
	$res_products = $db->get_results($query, ARRAY_A);
	$num_products = $db->num_rows;
	$res_products = $db->get_results($query." ".$limit, ARRAY_A);
	
	$res_array['total'] = $num_products;
	$res_array['items'] = $res_products;
	return $res_array;
}

function search_publications($q,$limit)
{
	global $db;
	$res_array = array();
	
  $query = "SELECT * FROM ".$db->tables['publications']." 
    WHERE name LIKE '%".$db->escape($q)."%' 
    OR alias LIKE '%".$db->escape($q)."%' 
    OR anons LIKE '%".$db->escape($q)."%' 
    OR memo LIKE '%".$db->escape($q)."%'     
    "; 
	$res_publications = $db->get_results($query, ARRAY_A);
  $num_publications = $db->num_rows;
  $res_publications = $db->get_results($query." ".$limit, ARRAY_A);
	
	$res_array['total'] = $num_publications;
	$res_array['items'] = $res_publications;
	return $res_array;
}

function search_categories($q,$limit)
{
	global $db;
  	$res_array = array();
    $query = "SELECT * FROM ".$db->tables['categs']." 
      WHERE title LIKE '%".$db->escape($q)."%' 
      OR alias LIKE '%".$db->escape($q)."%' 
      OR memo LIKE '%".$db->escape($q)."%' "; 
	  $res_categories = $db->get_results($query, ARRAY_A);
    $num_categories = $db->num_rows;
    $res_categories = $db->get_results($query." ".$limit, ARRAY_A);
	
	$res_array['total'] = $num_categories;
	$res_array['items'] = $res_categories;
	return $res_array;
}



function search_query($q,$where = NULL,$page=NULL)
{
  global $db, $tpl;

	// Указываем шаблону используемый метод поиска (обобщенный или по конкретной области поиска)
  $tpl->assign("search_area",$where);

  // Инициализируем настройки поиска
  $search_perpage = ONPAGE;
  $search_frontpage = 5;
  // Если страница поиска не задана, она равна 1
  if($page == NULL ) $page = 1;
  ob_start();
  
  // Передаем шаблону строку поиска
  $tpl->assign("query", $q);

  // Если поиск обобщенный - выводим N последних записей
  // Если поиск с указанием таблицы (области поиска) и такая область поиска разрешена - выводим по M записей на страницу
  if($where !== NULL)
  {
	$search_areas = array("products","categs","publications","categories");
	if(in_array($where,$search_areas))
	{
		// Указываем шаблону используемый метод поиска (обобщенный или по конкретной области поиска)
		$tpl->assign("search_area",$where);
  
		// Номер первой записи в зависимости от страницы
		$first_record = ($page-1)*$search_perpage;
		
		$quantity_restriction = "LIMIT ".$first_record.",".$search_perpage;

		// Запросы на получение результатов поиска по каждой области поиска в отдельности
		// с учетом ограничения на количество выводимых записей
  
		switch($where)
		{
			case 'products': $res = search_products($q,$quantity_restriction); break;
			case 'publications': $res = search_publications($q,$quantity_restriction); break;
			case 'categories': $res = search_categories($q,$quantity_restriction); break;
		}
		
		// Передаем шаблону общее количество найденных записей и сами записи по результатам поиска в конкретной предметной области
		$tpl->assign("num_items", $res['total']);
		$tpl->assign("items", $res['items']);
		
		// Передаем в шаблон номер текущей страницы и общее количесто страниц
		$tpl->assign("current_page",$page);
		$tpl->assign("total_pages",ceil($res['total']/$search_perpage));
		
	}
  }
  else
  {
		$quantity_restriction = "LIMIT ".$search_frontpage;
		
		// €щем по всем областЯм (обобщенный поиск)
		$res_products = search_products($q,$quantity_restriction);
		$res_publications = search_publications($q,$quantity_restriction);
		$res_categories = search_categories($q,$quantity_restriction);
		
		// Передаем шаблону общее количество найденных записей по каждой области поиска
		$tpl->assign("num_products", $res_products['total']);
		$tpl->assign("num_publications", $res_publications['total']);
		$tpl->assign("num_categories", $res_categories['total']);
		
		// Передаем шаблону данные результатов поиска
		$tpl->assign("search_results_products", $res_products['items']);
		$tpl->assign("search_results_publications", $res_publications['items']);
		$tpl->assign("search_results_categories", $res_categories['items']);
		
  }
  
  $tpl->assign("search_frontpage",$search_frontpage);
  $tpl->assign("site_url",SITE_URL);

  $str = $tpl->display("pages/list_search_results.html");
  return $str;
}


?>