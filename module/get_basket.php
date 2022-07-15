<?php
	if (!class_exists('Site')) { return; }
    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
	*
	* last_updated 20.05.2019
	* updated adding in cart[] 
	* added func check_basket_correct
    * added one click adding list of items
	* delete list of items
    ***********************/ 

    class Basket extends Site {

        function __construct()
        {
        }
                
        static public function get_basket($site)
        {
			global $db;
			add_basket($site);
			delete_basket($site);
			update_basket($site);
            $tpl_page = 'pages/basket.html';
            $arr = GetEmptyPageArray();
            $arr['skip_sidebar'] = '1';
            $arr['page'] = $tpl_page;
            $site->uri['page'] = $tpl_page;
            $arr['title'] = $site->GetMessage('basket', 'title');
            $arr['metatitle'] = $site->GetMessage('basket', 'metatitle');
			$old = $db->cache_queries;
			$db->cache_queries = false;
			$list_basket = list_products(0, 'product', $site, 'basket', 99);
			$db->cache_queries = $old;
			$total_summ = 0;
			$total_qty = 0;
			
			if(!empty($list_basket['list'])){
				foreach($list_basket['list'] as $k => $v){
					$_SESSION['basket'][$v['id']]['info'] = $v;
					$_SESSION['basket'][$v['id']]['title'] = $v['title'];
					$_SESSION['basket'][$v['id']]['price'] = $v['price'];
					
					$_SESSION['basket'][$v['id']]['price_formatted'] = $v['price_formatted'];
					$_SESSION['basket'][$v['id']]['summ'] = $v['price']*$_SESSION['basket'][$v['id']]['qty'];
					$_SESSION['basket'][$v['id']]['summ_formatted'] = $site->price_formatted($_SESSION['basket'][$v['id']]['summ'], $v['currency']);
					$total_summ += $_SESSION['basket'][$v['id']]['summ'];
					$total_qty += $_SESSION['basket'][$v['id']]['qty'];
				}

				$currency = isset($v['currency']) ? $v['currency'] : '';
				$ar_total = array($currency => $total_summ);
				
				$_SESSION['basket_total'] = $site->correct_total_summ($ar_total);
				
				$_SESSION['basket_total']['qty'] = $total_qty;
				$arr['basket']['list'] = $_SESSION['basket'];
				$arr['basket']['total'] = $_SESSION['basket_total'];
			}
			if(isset($_POST['add_order'])){
				$url = $site->vars['site_url'].'/order/';
				return $site->redirect($url);
				//header("Location: ".$url);
				//exit;
			}
			$arr = get_coupon_info($site, $arr);
            return $arr;           
        }
        
    }
	
	function add_basket($site){
		$ids = !empty($site->uri['params']['add']) ? explode(',',$site->uri['params']['add']) : 0;
		$qtys = !empty($site->uri['params']['qty']) ? explode(',',$site->uri['params']['qty']) : 0;
		
		if(empty($ids)){ 
			if(isset($_GET['add_order'])){
				$url = $site->vars['site_url'].'/order/';
				return $site->redirect($url);
			}
			return; 
		}	
		
		global $db;
		$site->stop_db_cache();
		
		foreach($ids as $k => $id){
			
			$qty = isset($qtys[$k]) ? $qtys[$k] : 1;
			$main_id = $id;
			$sql = "SELECT p.id, p.total_qty, p2.id as id_required, p3.id as id_gift 
				FROM ".$db->tables['products']." p 
				LEFT JOIN ".$db->tables['products']." p2 on (p.treba = p2.id AND p2.active = '1' AND p2.accept_orders = '1')
				LEFT JOIN ".$db->tables['products']." p3 on (p.present_id = p3.id AND p3.active = '1')
				WHERE p.id IN ('".$id."') 
					AND p.active = '1' 
					AND p.accept_orders = '1' 
			";
			$rows = $db->get_results($sql);
			if($rows && $db->num_rows > 0){
				foreach($rows as $row){
					
					$rid = $row->id;
					$id_required = $row->id_required;
					$id_gift = $row->id_gift;
					
					if(isset($_SESSION['basket'][$rid]) && !isset($_GET['add_order'])){
						$_SESSION['basket'][$rid]['qty'] += $qty;
					}elseif(!isset($_SESSION['basket'][$rid])
						&& $rid == $main_id
					){
						$_SESSION['basket'][$rid]['qty'] = $qty;
					}elseif(!isset($_SESSION['basket'][$rid])){
						$_SESSION['basket'][$rid]['qty'] = 1;
					}
				
					if($row->total_qty > 0 && !empty($_SESSION['basket'][$rid]['qty']) && $_SESSION['basket'][$rid]['qty'] > $row->total_qty){
						$_SESSION['basket'][$rid]['qty'] = $row->total_qty;
					}

					/* если есть связанные товары, то добавим их в корзину */
					if($row->id_required > 0 && empty($_SESSION['basket'][$id_required]['qty'])){
						$_SESSION['basket'][$id_required]['qty'] = $_SESSION['basket'][$rid]['qty'];
						$_SESSION['basket'][$id_required]['required'] = '1';
					}
					
					/* если есть подарок, то добавим его в корзину */
					if($row->id_gift > 0 && empty($_SESSION['basket'][$id_gift]['qty'])){
						$_SESSION['basket'][$id_gift]['qty'] = '1';
						$_SESSION['basket'][$id_gift]['gift'] = '1';
					}					
				}
			}			
		}
		$site->start_db_cache();
		
		$url = $site->vars['site_url'].'/basket/';
		if(!empty($_SERVER["HTTP_REFERER"])){
			$url .= '?page='.$_SERVER["HTTP_REFERER"];
		}

		if(isset($_GET['add_order'])){
			$url = $site->vars['site_url'].'/order/';
		}
		return $site->redirect($url);
		
	}
	
	
	function delete_basket($site){
		if(!empty($_POST["delete"]) || !empty($_GET["delete"])){
			$deleted = !empty($_POST["delete"]) ? $_POST["delete"] : $_GET["delete"];
			foreach($deleted as $v){
				//$db->query("DELETE");
				if(!empty($_SESSION['basket'][$v]['info']['id_gift'])){
					$to = $_SESSION['basket'][$v]['info']['id_gift'];
					unset($_SESSION['basket'][$to]);					
				}
				
				if(!empty($_SESSION['basket'][$v]['info']['id_required'])){
					$to = $_SESSION['basket'][$v]['info']['id_required'];
					unset($_SESSION['basket'][$to]);					
				}
								
				unset($_SESSION['basket'][$v]);
				unset($_SESSION['basket_total']);			
				//header("Location: ".$url);
				//exit;
			}
			$url = $site->vars['site_url'].'/basket/';
			return $site->redirect($url);
		}
		return;
	}

	
	function update_basket($site){
		
		if((isset($_POST['update']) || isset($_POST['add']) || isset($_POST['add_order'])) && !empty($_POST['basket'])){
			
			if(isset($_POST["coupon"])){
				use_coupon($_POST["coupon"], $site);
			}
			
			foreach($_POST['basket'] as $k => $v){
				$qty = intval($v['qty']);
				if($qty > 0){
					$_SESSION['basket'][$k]['qty'] = $qty;					
				}else{
					unset($_SESSION['basket'][$k]);
				}
			}
			
			if(isset($_POST['add_order'])){ return; }
			
			if(isset($_GET['add_order'])){
				$url = $site->vars['site_url'].'/order/';
			}else{
				$url = isset($_POST['update']) ? $site->vars['site_url'].'/basket/' : $site->vars['site_url'].'/basket/?add=1';
			}
			return $site->redirect($url);
			exit;			
		}
		return;
	}
	
	
	/* if exists info */
	function check_basket_correct($site, $return=FALSE){
		global $db;
		$currency = isset($site->vars['sys_currency']) 
			? $site->vars['sys_currency'] : 'euro';
		$arr = isset($_SESSION['basket']) ? $_SESSION['basket'] : array();
		$ar = array();
		$total_summ = 0;
		$total_qty = 0;
		
		if(!empty($arr)){
			$products = list_products(0, 'product', $site, 'basket');
			if(!empty($products['list'])){
				foreach($products['list'] as $k => $v){
					$qty = isset($_SESSION['basket'][$v['id']]['qty']) 
						? $_SESSION['basket'][$v['id']]['qty'] : 1;
					$ar[$v['id']]['qty'] = $qty; 
					$ar[$v['id']]['info'] = $v; 
					$ar[$v['id']]['title'] = $v['title'];
					$ar[$v['id']]['price'] = $v['price'];
					$ar[$v['id']]['price_formatted'] = $v['price_formatted'];
					$ar[$v['id']]['summ'] = $qty*$v['price'];
					$ar[$v['id']]['summ_formatted'] = $site->price_formatted($ar[$v['id']]['summ'], $v['currency']);
					$total_summ += $qty*$v['price'];
					$total_qty += $qty;
					
					if(!empty($products['options'][$v['id']]['list'])){
						$ar[$v['id']]['options'] = $products['options'][$v['id']]['list']; 
					}					
				}
			}
		}
		
		if(!$return){
			$_SESSION['basket'] = $ar;
			/* add basket_total */
			$ar_total = array($currency => $total_summ);
			$_SESSION['basket_total'] = $site->correct_total_summ($ar_total);
			$_SESSION['basket_total']['qty'] = $total_qty;
			return;
		}else{
			return $ar;
		}
	}
	
	
	
	function use_coupon($str, $site){
		if(isset($_POST['coupon'])){
			$c = $_POST['coupon'];
		}elseif(!empty($_SESSION['coupon'])){
			$c = $_SESSION['coupon'];
		}else{
			unset($_SESSION['coupon']);
			return;
		}
		
		if(empty($c)){
			unset($_SESSION['coupon']);
			return;
		}
		
		global $db;
		$str = trim($str);
		if(empty($str)){
			unset($site->page['coupon']);
			unset($_SESSION['coupon']);
		}
		$str = mb_strtoupper($str, 'UTF-8');
		$site->stop_db_cache();
		$sql = "SELECT * 
					FROM `".$db->tables['coupons']."` 
					WHERE UPPER(`title`) LIKE '".$str."' 
						AND `active` = '1' 						
						AND (date_start < NOW() 
							OR date_start  IS NULL ) 
						AND (date_stop > NOW() 
							OR date_stop IS NULL ) 
						AND (site_id = '0' OR site_id = '".$site->id."')
		";		
		$row = $db->get_row($sql, ARRAY_A);
		$site->start_db_cache();
		if(!$row || $db->num_rows == 0){ 
			unset($site->page['coupon']);
			unset($_SESSION['coupon']);
			return; 
		}
		$_SESSION['coupon'] =  $row['title'];
		return ;		
	}
	
	
	function get_coupon_info($site, $arr, $basket='Y'){
		global $db;
		if(empty($_SESSION['coupon']) || empty($arr['basket']['total'])){
			return $arr;
		}
		
		$c = $_SESSION['coupon'];
		$site->stop_db_cache();
		$sql = "SELECT * 
					FROM `".$db->tables['coupons']."` 
					WHERE UPPER(`title`) LIKE '".$c."' 
						AND `active` = '1' 						
						AND (date_start < NOW() 
							OR date_start  IS NULL ) 
						AND (date_stop > NOW() 
							OR date_stop IS NULL ) 
						AND (site_id = '0' OR site_id = '".$site->id."')
		";		
		$row = $db->get_row($sql, ARRAY_A);
		$site->start_db_cache();
		if(!$row || $db->num_rows == 0){ 
			unset($site->page['coupon']);
			unset($_SESSION['coupon']);
			return; 
		}
		$arr['coupon'] = $row;
		
		if($basket == 'Y'){
			$to = $arr['basket']['total'];			
		}
		
		if(!empty($to['summ']) && !empty($row['discount_summ'])){
			$summa = !empty($row['discount_procent']) 
				? round($to['summ']*$row['discount_summ']/100, 2)
				: $to['summ']-$row['discount_summ'];
				
			if($summa <= $to['summ']){
				/* все ок, сумма больше 0, ставим скидку и меняем все суммы */
				$total_summ = array($to['currency'] => $to['total']);
				$to = correct_total_summ($total_summ, $to['delivery'], $to['discount'], $summa, $to['nds']);
				/*
				$qty = isset($_SESSION['basket_total']['qty']) ? $_SESSION['basket_total']['qty'] : 1;
				$_SESSION['basket_total'] = $to;
				$_SESSION['basket_total']['qty'] = $qty;
				*/
			}
		}
				
		if($basket == 'Y'){
			$arr['basket']['total'] = $to;
		}
		//$site->print_r($arr,1);
		return $arr;
	}
	
	function register_coupon($id, $total_summ, $site){
		global $db;
		if(empty($_SESSION['coupon'])){
			return ;
		}
		
		$currency = key($total_summ);
		$total_summ = array_shift($total_summ);
		
		$c = $_SESSION['coupon'];
		$site->stop_db_cache();
		$sql = "SELECT * 
					FROM `".$db->tables['coupons']."` 
					WHERE UPPER(`title`) LIKE '".$c."' 
						AND `active` = '1' 						
						AND (date_start < NOW() 
							OR date_start  IS NULL ) 
						AND (date_stop > NOW() 
							OR date_stop IS NULL ) 
						AND (site_id = '0' OR site_id = '".$site->id."')
		";		
		$row = $db->get_row($sql, ARRAY_A);
		$site->start_db_cache();
		if(!$row || $db->num_rows == 0){
			unset($_SESSION['coupon']);
			return; 
		}
		
		$summa = !empty($row['discount_procent']) 
				? round($total_summ*$row['discount_summ']/100, 2)
				: $total_summ-$row['discount_summ'];
				
		if($summa <= $total_summ){
			/* все ок */
			$sql = "INSERT INTO ".$db->tables['discounts']." (`is_coupon`, 
				`order_id`, `discount_summ`, `currency`) VALUES 
				('".$row['id']."', '".$id."', '".$summa."', 
				'".$db->escape($currency)."') ";
			$db->query($sql);
		}
		unset($_SESSION['coupon']);
		return; 
	}
	
?>