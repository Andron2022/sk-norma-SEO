<?php
	if (!class_exists('Site')) { return; }

    /***********************
    *
    * registers order 
    * 21.11.2019
	*
    ***********************/ 


/* updates:
16.09.2016 - connections with org and order
04.05.2018 - updated correct_basket
09.06.2019 - order.list_payments for delivery corrected
14.10.2019 - added partners ?welcome=partner_login
20.11.2019 - updated order_by_done()
21.11.2019 - added set_order_paid
 
/order/pay/success/
/order/pay/fail/
/order/pay/done/
/order/pay/
квитанция сбербанка
/order/pay/sberbank/?order=5557985&summa=1100-00
/order/pay/cancel/
*/
    
    class Order extends Site {

        function __construct()
        {
        }
                
        static public function get_order($site)
        {
			global $tpl, $db, $user;
            $arr = array();
            $arr['skip_sidebar'] = '1';
            if(!empty($_POST['order']) && !empty($_POST['cart'])){
                return register_order($site);    
            }
			
			/* if NO GET - check for uri with o/ */
			if(empty($_GET['done']) && !empty($site->uri['alias']) 
				&& strlen($site->uri['alias']) > 2 
				&& substr($site->uri['alias'], 0, 2) == 'o/'
			){
				$done1 = str_replace('o/', '', $site->uri['alias']);
			}
			
            $arr['page'] = 'pages/order.html';
            $arr['title'] = $site->GetMessage('order', 'title');
            $arr['metatitle'] = $site->GetMessage('order', 'metatitle');
			
			$sql = "SELECT * FROM ".$db->tables['delivery']." WHERE `site` = '0' OR `site` = '".$site->id."' ORDER BY `sort`, `title` ";
			$delivery = $db->get_results($sql, ARRAY_A);
			$delivery_list = array();
			if($db->num_rows > 0){
				foreach($delivery as $k => $d){
					$delivery_list[$k] = $d;
					$delivery_list[$k]['price_formatted'] = $site->price_formatted($d['price'], $d['currency']);
				}
			}
            $arr['delivery_list'] = $delivery_list;
									
			if(!empty($delivery_list)){
				foreach($delivery_list as $d){
					$old = $db->cache_queries;
					$db->cache_queries = false;
					$sql = "SELECT p.*, dp.delivery 
						FROM ".$db->tables['order_payments']." p, 
							".$db->tables['delivery2pay']." dp  
						WHERE p.`active` = '1' AND (p.`site` = '0' OR p.`site` = '".$site->id."') 
						AND p.id = dp.payment AND dp.delivery = '".$d['id']."' 
						GROUP BY p.id 
						ORDER BY p.`sort`, p.`title` ";
					$payments = $db->get_results($sql, ARRAY_A);
					$db->cache_queries = $old;
					$arr['list_payments'][$d['id']] = $payments;	
				}
			}
			
			/*
			$sql = "SELECT * FROM ".$db->tables['order_payments']." 
				WHERE `active` = '1' AND (`site` = '0' OR `site` = '".$site->id."') 
				ORDER BY `sort`, `title` ";
			$payments = $db->get_results($sql, ARRAY_A);
            $arr['list_payments'] = $payments;			
            */
			
            if(!empty($_GET['done']) || !empty($done1)){ 	
                $done = !empty($_GET['done']) ? trim($_GET['done']) : $done1;
				$site->stop_db_cache();
                $sql = "SELECT o.*, s.site_url, s.`name_short` as `site`, 
						d.noprice as delivery_noprice,
						d.title as delivery_title, 
						os.title_client as status_title, 
						os.show_client, 
						orgs.id2 as orgs, 
						
						IF(
							(SELECT COUNT(DISTINCT currency_sell) 
								FROM ".$db->tables['orders_cart']." 
								WHERE orderid = o.id
							) > '1', 
							'rur', (SELECT currency_sell 
								FROM ".$db->tables['orders_cart']." 
								WHERE orderid = o.id 
								LIMIT 0,1)
						) as currency, 
						IF(dis.discount_summ > 0, dis.discount_summ, 0) as dis_summ, 
						c.title as coupon, 
						
						(SELECT SUM(qty*price*pricerate) - SUM(qty*discount)
							FROM ".$db->tables['orders_cart']." 
							WHERE orderid = o.id
						) as order_summ, 
						
						(SELECT SUM(qty*price*pricerate) 
								 - SUM(qty*discount) 
								 + delivery_price - dis_summ 
							FROM ".$db->tables['orders_cart']." 
							WHERE orderid = o.id
						) as total_summ
						
					FROM ".$db->tables['orders']." o 
                    LEFT JOIN ".$db->tables['site_info']." s ON (o.site_id = s.id)                      
                    LEFT JOIN ".$db->tables['delivery']." d ON (d.id = o.delivery_method)                      
                    LEFT JOIN ".$db->tables['order_status']." os ON (os.id = o.status)     
					LEFT JOIN ".$db->tables['discounts']." dis on (o.id = dis.order_id) 
					LEFT JOIN ".$db->tables['coupons']." c on (dis.is_coupon = c.id) 
					LEFT JOIN ".$db->tables['connections']." orgs ON (o.id = orgs.id1 AND orgs.name1 = 'order' AND orgs.name2 = 'org')
	
                    WHERE  
				";

				if(!empty($done1)){
					$sql .= " o.order_id = '".$db->escape($done)."'  
					";
				}else{
					$sql .= " MD5(concat(o.id,o.created)) = '".$db->escape($done)."' 
					";
				}
				
				$sql .= "
                        AND (show_client = '1' OR `status` = '0') 
				";
						
				$row = $db->get_row($sql, ARRAY_A);
                if(!empty($db->last_error)){ return $site->db_error(basename(__FILE__).": 52"); }
				$site->start_db_cache();
				
                if($db->num_rows == 1){
                
                    /* если больше суток, то нужна метка ?debug чтобы увидеть заказ */
                    /*                         
                    $when = strtotime($row['created']);
                    $diff = time()-$when;

                    if($diff > (60*60*24) && !isset($site->uri['params']['debug'])){
                        // метка времени устарела
                        $site->header_status = 404;
                        return $site->error_page(404);
                    }  
                    */       
										
					$row['md5'] = md5($row['id'].$row['created']);                
                    $arr['order'] = $row;
					$site->stop_db_cache();
                    $cart = $db->get_results("SELECT c.*, p.alias 
                        FROM ".$db->tables['orders_cart']." c  
                        LEFT JOIN ".$db->tables['products']." p ON (c.product_id = p.id) 
                        WHERE c.orderid = '".$row['id']."' ORDER BY c.items ");
					$site->start_db_cache();						

                    $ar = $total_summ = array();
					$total_discount = 0;
                    if($db->num_rows > 0){
                        foreach($cart as $v){
							
							$files = array();
							if($row['payd_status'] == 1){
								$site->stop_db_cache();
								$files = list_files('product', $v->product_id, $site);
								$site->start_db_cache();
							}
							
                            $summ_discount = $v->qty * $v->discount; 
							$total_discount += $summ_discount;							
                            $summ = ($v->qty * $v->price)-$summ_discount;
                    
                            $ar[] = array(
                                'files' => $files,
                                'product_id' => $v->product_id,
                                'title' => $v->items,
                                'title_link' => $row['site_url'].'/'.$v->alias.URL_END,
                                'price' => $v->price,
                                'discount' => $v->discount,
                                'discount_formatted' => $site->price_formatted($v->discount,$v->currency_sell),
                                'currency' => $v->currency_sell,
                                'price_formatted' => $site->price_formatted($v->price, $v->currency_sell),
                                'qty' => $v->qty,
                                'summ' => $summ,
                                'summ_formatted' => $site->price_formatted($summ, $v->currency_sell)
                            );
                            
                            if(!isset($total_summ[$v->currency_sell])){
                                $total_summ[$v->currency_sell] = $summ;
                            }else{
                                $total_summ[$v->currency_sell] += $summ;
                            }   
														
                        }
                    }
                    
                    $arr['cart'] = $ar;
					//$arr['order']['id'] = $row['order_id'];
					$arr['order']['summ_formatted'] = price_formatted($row['order_summ'],$row['currency']);
                    $arr['order']['total_formatted'] = price_formatted($row['total_summ'],$row['currency']);
					$arr['order']['coupon_formatted'] = price_formatted($row['dis_summ'],$row['currency']);
					$arr['order']['delivery_formatted'] = price_formatted($row['delivery_price'],$row['currency']);
										
					//$arr['total_summ'] = $site->correct_total_summ($row['total_summ'], $row['delivery_price'], $row['dis_summ']);
					
					$str = $site->id.'-'.substr(chunk_split($row['order_id'], 4, '-'), 0, -1); 
                    //$arr['content'] = sprintf($site->GetMessage('order', 'content'), ' <b>'.$str.'</b> ');

					/* Выведем инфо о способе оплаты */
					$arr['payment'] = $db->get_row("SELECT * FROM ".$db->tables['order_payments']." 
						WHERE id = '".$row['payment_method']."' AND active = '1' ", ARRAY_A);
						
					/* добавим доступные способы оплаты */
					if(!empty($row['delivery_method'])){
						$sql = "SELECT p.* 
							FROM `".$db->tables['order_payments']."` p 
							LEFT JOIN ".$db->tables['delivery2pay']." dp ON (p.id = dp.payment) 							
							WHERE 
								dp.delivery = '".$row['delivery_method']."' 
								AND p.active = '1' 
								AND (p.site = '0' OR p.site = '".$site->id."')
							ORDER BY p.sort, p.title
							";
					}else{
						$sql = "SELECT p.* 
							FROM `".$db->tables['order_payments']."` p 	
							WHERE 
								p.active = '1' 
								AND (p.site = '0' OR p.site = '".$site->id."')
							ORDER BY p.sort, p.title
							";
					}
					
					$payments = $db->get_results($sql, ARRAY_A);
					if(!empty($payments)){
						foreach($payments as $k1 => $p1){
							$sql = "SELECT 
									id as id,
									`value` as title,
									value2 as value,
									value3 as code
								FROM ".$db->tables['option_values']." 
								WHERE id_option = '0' 
									AND `where_placed` = 'payments' 
									AND id_product = '".$p1['id']."'
							";
							$opts = $db->get_results($sql, ARRAY_A);
							if(!empty($opts)){
								foreach($opts as $op1 => $op2){
									$opts[$op2['code']] = $op2;
									unset($opts[$op1]);
								}
							}
							$payments[$k1]['options'] = $opts;
						}
						
					}
					
					$arr['order']['list_payments'] = $payments;
						
					if(!empty($row['orgs'])){						
						$arr['link_invoice'] = $site->vars['site_url'].'/order/doc/?order='.$row['md5'].'&page=invoice';
						$arr['link_sberbank'] = $site->vars['site_url'].'/order/doc/?order='.$row['md5'].'&page=sberbank';
					}
					
					$arr['content'] = $row['payd_status'] == 1 
						? sprintf($site->GetMessage('order', 'payd'), ' <b>'.$str.'</b> ')
						: sprintf($site->GetMessage('order', 'content'), ' <b>'.$str.'</b> ');
						
					if(!empty($row['delivery_title'])){
						$arr['content_dostavka'] = $site->GetMessage('order', 'delivery_title').': '.$row['delivery_title'];
					}

					if($row['status'] > 0 AND !empty($row['status_title'])){
						$arr['content_status'] = $site->GetMessage('order', 'status_title').': '.$row['status_title'];
						$arr['content'] .= '<br>'.$arr['content_status'];
					}
					
					$site->stop_db_cache();
					$arr['comments'] = $db->get_results("SELECT c.*, 
							u.name as user_name, 
							u.login as user_login, 
							f.id as file_id, 
							md5(f.id) as file_md5, 
							f.size as file_size, 
							f.filename as file_name, 
							f.title as file_title, 
							f.ext as file_ext 
						FROM ".$db->tables['comments']." c 
						LEFT JOIN ".$db->tables['users']." u ON (c.userid = u.id) 
						LEFT JOIN ".$db->tables['uploaded_files']." f on (c.id = f.record_id AND f.record_type = 'order_comment')
						WHERE c.record_type = 'order' 
						AND c.record_id = '".$row['id']."' 
						AND c.active = '1' 
						ORDER BY c.ddate
						", ARRAY_A);
					$site->start_db_cache();
					//$db->debug();
					//exit;

                }else{
                    $arr['error'] = $site->GetMessage('order', 'error');
                }
                            
            }elseif(!empty($_SESSION['basket'])){
				$arr['page'] = 'pages/order_add.html';
				//$arr['basket'] = $_SESSION['basket'];
				check_basket_correct($site); // if exists info
				$arr['basket']['list'] = $_SESSION['basket'];
				$arr['basket']['total'] = $_SESSION['basket_total'];
				//$arr['total_summ'] = $_SESSION['basket_total'];
				$arr = get_coupon_info($site, $arr);
            }else{
                $arr['error'] = $site->GetMessage('order', 'error');
            }
            return $arr;                        
        }   


		static function get_order_cancel($site){
			global $tpl, $db, $user;
			$arr = array();
			$arr['skip_sidebar'] = 'true';
			$arr['page'] = 'blank.html';
			$arr['title'] = $site->GetMessage('order', 'payment', 'title');
			$arr['metatitle'] = $site->GetMessage('order', 'payment', 'metatitle');
			$arr['content'] = $site->GetMessage('order', 'payment', 'cancel');
			
			return $arr;		
		}

		
		static function get_order_fail($site){
			global $tpl, $db, $user;
			$arr = array();
			$arr['skip_sidebar'] = 'true';
			$arr['page'] = 'blank.html';
			$arr['title'] = $site->GetMessage('order', 'payment', 'title');
			$arr['metatitle'] = $site->GetMessage('order', 'payment', 'metatitle');
			$arr['content'] = $site->GetMessage('order', 'payment', 'fail');
			
			return $arr;		
		}


		static function get_order_success($site){
			global $tpl, $db, $user;			
			$arr = array();
			$arr['skip_sidebar'] = 'true';
			$arr['page'] = 'blank.html';
			$arr['title'] = $site->GetMessage('order', 'payment', 'title');
			$arr['metatitle'] = $site->GetMessage('order', 'payment', 'metatitle');
			$arr['content'] = $site->GetMessage('order', 'payment', 'success');
			
			return $arr;		
		}


		static function get_order_payment($site){
			global $tpl, $db, $user;
			$arr = array();
			$arr['skip_sidebar'] = 'true';
			$arr['page'] = 'blank.html';
			$arr['title'] = $site->GetMessage('order', 'payment', 'title');
			$arr['metatitle'] = $site->GetMessage('order', 'payment', 'metatitle');
			$arr['content'] = $site->GetMessage('order', 'payment', 'in_progress');
			
			return $arr;		
		}


		static function get_order_sberbank($site){
			global $tpl, $db, $user;
			$arr = $site->vars;
			$arr['skip_sidebar'] = 'true';
			$arr['page'] = 'docs/sberbank.html';
			$arr['title'] = $site->GetMessage('order', 'payment', 'title');
			$arr['metatitle'] = $site->GetMessage('order', 'payment', 'metatitle');
			$arr['content'] = $site->GetMessage('order', 'payment', 'sberbank');
			$tpl->assign("page", $arr);
			if(empty($_GET['done'])){				
				echo $tpl->display('docs/sberbank_old.html');
			}else{
				$order = order_by_done($_GET['done'], $site);
				$tpl->assign("order", $order);
				echo $tpl->display('docs/sberbank.html');
			}			
			exit;
			return $arr;		
		}

		static function get_order_invoice($site){
			global $tpl, $db, $user;
			$arr = $site->vars;
			$arr['skip_sidebar'] = 'true';
			$arr['page'] = 'docs/invoice.html';
			$arr['title'] = $site->GetMessage('order', 'payment', 'title');
			$arr['metatitle'] = $site->GetMessage('order', 'payment', 'metatitle');
			$arr['content'] = $site->GetMessage('order', 'payment', 'sberbank');
			$tpl->assign("page", $arr);
			if(!empty($_GET['done'])){								
				$order = order_by_done($_GET['done'], $site);
				$tpl->assign("order", $order);
				echo $tpl->display('docs/invoice.html');
			}			
			exit;
			return $arr;		
		}


		static function get_order_doc($site){
			global $tpl, $db, $user;
			$arr = $site->vars;
			$arr['skip_sidebar'] = 'true';
			//$arr['page'] = 'docs/sberbank.html';
			//$arr['title'] = $site->GetMessage('order', 'payment', 'title');
			//$arr['metatitle'] = $site->GetMessage('order', 'payment', 'metatitle');
			//$arr['content'] = $site->GetMessage('order', 'payment', 'sberbank');
			$tpl->assign("page", $arr);
			
			if(empty($site->uri['params']['order']) 
				|| empty($site->uri['params']['page'])
			){				
				$arr['error'] = $site->GetMessage('order', 'error');
				return $arr;
			}

			//$site->uri['params']['order']
			//$site->uri['params']['page']
			/* найдем заказ и соберем инфо о нем */
			$done = trim($site->uri['params']['order']);
			$site->stop_db_cache();
			$sql = "SELECT o.*, 
						s.site_url, s.email_info, s.name_full, 
						d.noprice as delivery_noprice,
						d.title as delivery_title, 
						IFNULL(dis.discount_summ, 0) as discount_summ,
						IF(
							(SELECT COUNT(DISTINCT currency_sell) 
								FROM ".$db->tables['orders_cart']." 
								WHERE orderid = o.id
							) > '1', 
							'rur', (SELECT currency_sell 
								FROM ".$db->tables['orders_cart']." 
								WHERE orderid = o.id 
								LIMIT 0,1)
						) as order_currency, 
						
						IF(
							(SELECT COUNT(DISTINCT currency_sell) 
								FROM ".$db->tables['orders_cart']." 
								WHERE orderid = o.id
							) > '1', 
							(SELECT SUM(qty*price*pricerate-discount*qty)
								FROM ".$db->tables['orders_cart']." 
								WHERE orderid = o.id
							), (SELECT SUM(qty*price-discount*qty) 
								FROM ".$db->tables['orders_cart']." 
								WHERE orderid = o.id
							)
						) as order_summ, 
						
						IF(
							(SELECT COUNT(DISTINCT currency_sell) 
								FROM ".$db->tables['orders_cart']." 
								WHERE orderid = o.id
							) > '1', 
							(SELECT SUM(qty*price*pricerate-discount) - 
								IFNULL(dis.discount_summ,0) + o.delivery_price
								FROM ".$db->tables['orders_cart']." 
								WHERE orderid = o.id
							), (SELECT SUM(qty*price-discount) - 
								IFNULL(dis.discount_summ,0) + o.delivery_price 
								FROM ".$db->tables['orders_cart']." 
								WHERE orderid = o.id
							)
						) as order_summ_total,
				
						os.title_client as status_title, 
						os.show_client,
						op.title as payment_title 
                    FROM ".$db->tables['orders']." o 
					LEFT JOIN ".$db->tables['delivery']." d ON (d.id = o.delivery_method)
					LEFT JOIN `discounts` dis on (o.id = dis.order_id) 
                    LEFT JOIN ".$db->tables['site_info']." s ON (o.site_id = s.id)
					LEFT JOIN ".$db->tables['order_status']." os ON (os.id = o.status) 
					LEFT JOIN ".$db->tables['order_payments']." op ON (o.payment_method = op.id) 					
                    WHERE                     
                        MD5(concat(o.id,o.created)) = '".$db->escape($done)."' 
            ";
			$arr['order'] = $db->get_row($sql, ARRAY_A);
			
			$site->start_db_cache();
            if(!empty($db->last_error)){ 
				return $site->db_error(basename(__FILE__).": ".__LINE__); 
			}
			
			if(empty($arr['order']['id'])){
				$arr['error'] = $site->GetMessage('order', 'error');
				return $arr;
			}
			
			/* orgs - инфо о продавце */
			$site->stop_db_cache();
			$sql = "SELECT o.*, 
				logo.id as logo_id,
				logo.ext as logo_ext,
				dir.id as dir_id,
				dir.ext as dir_ext,
				buh.id as buh_id,
				buh.ext as buh_ext,
				stamp.id as stamp_id,
				stamp.ext as stamp_ext
				
				FROM ".$db->tables['org']." o 
				LEFT JOIN ".$db->tables['connections']." c ON (id1 = '".$arr['order']['id']."' AND `name1` = 'order' AND `name2` = 'org')
				LEFT JOIN ".$db->tables['uploaded_files']." logo ON (o.id = logo.record_id AND logo.record_type = 'org_logo')
				
				LEFT JOIN ".$db->tables['uploaded_files']." dir ON (o.id = dir.record_id AND dir.record_type = 'org_dir')
				
				LEFT JOIN ".$db->tables['uploaded_files']." buh ON (o.id = buh.record_id AND buh.record_type = 'org_buh')
				
				LEFT JOIN ".$db->tables['uploaded_files']." stamp ON (o.id = stamp.record_id AND stamp.record_type = 'org_stamp')
				
				WHERE o.id = c.id2 
			";
			$arr['order']['orgs'] = $db->get_row($sql, ARRAY_A);

			/*
			dir_id
			dir_ext
			buh_id
			buh_ext
			stamp_id
			stamp_ext
			logo_id
			logo_ext
			*/
			
			$site->start_db_cache();
			/* номер квитанции - 6 знаков */
			$str = '';
			if(strlen($arr['order']['id']) < 6){
				$diff = 6-strlen($arr['order']['id']);				
				for($i=0; $i < $diff; $i++)	$str .= '0';
			}
			$str .= $arr['order']['id'];
			$arr['order']['kvit'] = $str;
			
			
			if(isset($arr['order']['orgs']['nds'])){
				$nds = round($arr['order']['order_summ_total']*$arr['order']['orgs']['nds']/100, 2);
				$arr['order']['nds_summ'] = $nds;
				$arr['order']['nds_summ_formatted'] = $site->price_formatted($nds, $arr['order']['order_currency'], 2);
			}
			
			$arr['order']['delivery_formatted'] = $site->price_formatted($arr['order']['delivery_price'], $arr['order']['order_currency'], 2);
			
			$arr['order']['discount_formatted'] = $site->price_formatted($arr['order']['discount_summ'], $arr['order']['order_currency'], 2);
			
			$arr['order']['total_formatted'] = $site->price_formatted($arr['order']['order_summ_total'], $arr['order']['order_currency'], 2);
			
			$arr['order']['summ_formatted'] = $site->price_formatted($arr['order']['order_summ'], $arr['order']['order_currency'], 2);
			
			
			/* добавим товары в заказе */
			$site->stop_db_cache();
			$sql = "SELECT c.*, 
						IF('".$arr['order']['order_currency']."' = 'rur',
							(c.price*c.qty*c.pricerate-c.discount*c.qty), 
							(c.price*c.qty-c.discount*c.qty)
						) as price_summ, 
						p.alias 
                    FROM ".$db->tables['orders_cart']." c  
                    LEFT JOIN ".$db->tables['products']." p ON (c.product_id = p.id) 
                    WHERE c.orderid = '".$arr['order']['id']."' ORDER BY c.items ";
			$arr['order_cart'] = $db->get_results($sql, ARRAY_A);
			$site->start_db_cache();
			if(!empty($db->last_error)){ 
				return $site->db_error(basename(__FILE__).": ".__LINE__); 
			}
			
			if(empty($arr['order_cart'])){
				$arr['error'] = $site->GetMessage('order', 'error');
				return $arr;
			}
			
			/* найдем шаблон документа и соберем инфо о нем */
			$doc = trim($site->uri['params']['page']);
			global $path;
			//$site->print_r($path); exit;
			$arr['page'] = 'docs/'.$site->uri['params']['page'].'.html';
			$f = $path.$site->tpl.$arr['page'];
			if(!file_exists($f)){
				$arr['error'] = 'Requested document <b>'.$arr['page'].'</b> not found';
				return $arr;
			}
			
			/* найдем заголовок для этого файла, 
			если задан в переменной sys_docs*/
			$title = $site->vars['name_short'];
			if(!empty($site->vars['sys_docs'])){
				$str = $site->vars['sys_docs'];
				
				$docs = explode ("\n",$str);
				foreach(array_keys($docs) as $key) $docs[$key] = trim($docs[$key]);
				
				foreach($docs as $v){
					$vv = explode("=",$v);
					$f1 = isset($vv[0]) ? trim($vv[0]) : 'wrong file';
					$t1 = isset($vv[1]) ? trim($vv[1]) : $title;
					
					if($f1 == $site->uri['params']['page'].'.html'){
						$title = $t1;
					}
				}
			}
			
			
			require_once('get_summ_txt.php');
			$string = money2str_ru($arr['order']['order_summ_total']);
			$char = mb_strtoupper(substr($string,0,2), "utf-8");
			$string[0] = $char[0];
			$string[1] = $char[1];
			$arr['order']['order_summ_total_str'] = $string;
			$arr['title'] = $title;			
			
			if(empty($arr['order']['name_company'])){
				$arr['order']['name_company'] = $arr['order']['fio'];
			}
			
			$tpl->assign("page", $arr);
			//$site->print_r($arr); exit;
			echo $tpl->display($arr['page']);
			exit;
			return $arr;		
		}








		
    }

	
	function order_by_done($done, $site){
		global $db;
		$done = trim($done);		
		$done1 = str_replace('o/','',$done);
		
		if(strlen($done1) > strlen($done)){
			$sql = "SELECT o.*, s.site_url, 
				d.noprice as delivery_noprice,
				d.title as delivery_title, 
				os.alias as status_code, 
				os.title_client as status_title, 
				os.show_client, 
				
				(SELECT SUM(qty*pricerate*price) - SUM(qty*discount)
                FROM ".$db->tables['orders_cart']."               
                WHERE orderid = o.id) as summa,
				
				(SELECT (SUM(qty*pricerate*price) - SUM(qty*discount) + delivery_price - discount_summ)
                FROM ".$db->tables['orders_cart']."               
                WHERE orderid = o.id) as total_summ,
				
				(SELECT c.id2 
					FROM ".$db->tables['connections']." c 
					LEFT JOIN ".$db->tables['org']." org ON (c.id2 = org.id)
					WHERE c.id1 = o.id 
						AND c.name1 = 'order' 
						AND c.name2 = 'org' 
						AND org.is_default = '1'
						AND org.active = '1'
					GROUP BY org.id 
					ORDER BY org.title
					LIMIT 0, 1 				
				) as orgs, 
			
				IF(dis.discount_summ > 0, dis.discount_summ, 0) as discount_summ, 
				dis.currency as discount_currency
            FROM ".$db->tables['orders']." o 
            LEFT JOIN ".$db->tables['site_info']." s ON (o.site_id = s.id)                      
            LEFT JOIN ".$db->tables['delivery']." d ON (d.id = o.delivery_method)                      
            LEFT JOIN ".$db->tables['order_status']." os ON (os.id = o.status)                      
			LEFT JOIN ".$db->tables['discounts']." dis ON (o.id = dis.order_id)                      
            WHERE                     
                MD5(concat(o.id,o.created)) = '".$db->escape($done)."' 
			";
			
			if(!defined("SHOW_FULL_ORDER")){
				$sql .= "
				AND (show_client = '1' OR `status` = '0') 
				AND o.payd_status = '0' 
				";
			}
		}else{
			$sql = "SELECT o.*, s.site_url, 
				d.noprice as delivery_noprice,
				d.title as delivery_title, 
				os.alias as status_code, 
				os.title_client as status_title, 
				os.show_client, 
				
				(SELECT SUM(qty*pricerate*price) - SUM(qty*discount)
                FROM ".$db->tables['orders_cart']."               
                WHERE orderid = o.id) as summa,
				
				(SELECT (SUM(qty*pricerate*price) - SUM(qty*discount) + delivery_price - discount_summ)
                FROM ".$db->tables['orders_cart']."               
                WHERE orderid = o.id) as total_summ,
				
				(SELECT c.id2 
					FROM ".$db->tables['connections']." c 
					LEFT JOIN ".$db->tables['org']." org ON (c.id2 = org.id)
					WHERE c.id1 = o.id 
						AND c.name1 = 'order' 
						AND c.name2 = 'org' 
						AND org.is_default = '1'
						AND org.active = '1'
					GROUP BY org.id 
					ORDER BY org.title
					LIMIT 0, 1 				
				) as orgs, 
			
				IF(dis.discount_summ > 0, dis.discount_summ, 0) as discount_summ, 
				dis.currency as discount_currency
            FROM ".$db->tables['orders']." o 
            LEFT JOIN ".$db->tables['site_info']." s ON (o.site_id = s.id)                      
            LEFT JOIN ".$db->tables['delivery']." d ON (d.id = o.delivery_method)                      
            LEFT JOIN ".$db->tables['order_status']." os ON (os.id = o.status)                      
			LEFT JOIN ".$db->tables['discounts']." dis ON (o.id = dis.order_id)                      
            WHERE                     
                o.order_id = '".$db->escape($done1)."'                 
			";
			
			if(!defined("SHOW_FULL_ORDER")){
				$sql .= "
					AND (show_client = '1' OR `status` = '0') 
					AND o.payd_status = '0' 
				";
			}
		}
		
		$site->stop_db_cache();
		$row = $db->get_row($sql, ARRAY_A);
			
		//$db->debug(); exit;
		$site->start_db_cache();
		if(!$row || $db->num_rows == 0){ return array(); }
		
		if(!empty($row['orgs'])){
			$site->stop_db_cache();
			$sql = "SELECT o.*, 
					f1.id as dir_id, f1.ext as dir_ext,  
					f2.id as buh_id, f2.ext as buh_ext,  
					f3.id as stamp_id, f3.ext as stamp_ext,  
					f4.id as logo_id, f4.ext as logo_ext
				FROM ".$db->tables['org']." o 
				LEFT JOIN ".$db->tables['uploaded_files']." f1 ON (o.id = f1.record_id AND f1.record_type = 'org_dir') 
				LEFT JOIN ".$db->tables['uploaded_files']." f2 ON (o.id = f2.record_id AND f2.record_type = 'org_buh') 
				LEFT JOIN ".$db->tables['uploaded_files']." f3 ON (o.id = f3.record_id AND f3.record_type = 'org_stamp') 
				LEFT JOIN ".$db->tables['uploaded_files']." f4 ON (o.id = f4.record_id AND f4.record_type = 'org_logo') 
				WHERE o.id = '".$row['orgs']."'
			";
			$row['orgs'] = $db->get_row($sql, ARRAY_A);
			$site->start_db_cache();			
		}
		
		$site->stop_db_cache();		
        $cart = $db->get_results("SELECT c.*, p.alias 
                FROM ".$db->tables['orders_cart']." c  
                LEFT JOIN ".$db->tables['products']." p ON (c.product_id = p.id) 
                WHERE c.orderid = '".$row['id']."' 
					AND c.qty > '0' 
					AND c.pricerate > '0'
				ORDER BY c.items ");
        $ar = $total_summ = array();
		$total_discount = $row['discount_summ'];
		$site->start_db_cache();
		
		if($db->num_rows > 0){
            foreach($cart as $v){
                $summ_discount = $v->qty * $v->discount; 
				$total_discount += $summ_discount;							
                $summ = ($v->qty * $v->price *$v->pricerate)-$summ_discount;
                    
                $ar[] = array(
                    'product_id' => $v->product_id,
					'title' => $v->items,
					'title_link' => $row['site_url'].'/'.$v->alias.URL_END,
                    'price' => $v->price*$v->pricerate,
                    'discount' => $v->discount,
                    'discount_formatted' => $site->price_formatted($v->discount,$v->currency_sell),
                    'currency' => $v->currency_sell,
                    'price_formatted' => $site->price_formatted($v->price*$v->pricerate, $v->currency_sell),
                    'qty' => $v->qty,
                    'summ' => $summ,
                    'summ_formatted' => $site->price_formatted($summ, $v->currency_sell)
                );
                            
                if(!isset($total_summ[$v->currency_sell])){
                    $total_summ[$v->currency_sell] = $summ;
                }else{
                    $total_summ[$v->currency_sell] += $summ;
                }
				
            }
		}
        $row['cart'] = $ar;		
        $row['total_summ'] = $site->correct_total_summ($total_summ, $row['delivery_price'], 0, $row['discount_summ'], $row['orgs']['nds']);
		$row['page'] = 'pages/order.html';
		$row['title'] = $site->GetMessage('order', 'title');
		$row['metatitle'] = $site->GetMessage('order', 'metatitle');		
        return $row;
	}

    function register_order($site){
        global $db;
		// $site->print_r($_SESSION,1);		
        $when = isset($_POST['order']['when']) ? intval($_POST['order']['when']) : 0;
        $diff = time()-$when;

        if($diff > (60*60*24)){
            // метка времени устарела
            $site->header_status = 404;
            return $site->error_page(404);
        }

        $o_name = isset($_POST['order']['name']) ? trim($_POST['order']['name']) : '';
        $o_phone = isset($_POST['order']['phone']) ? trim($_POST['order']['phone']) : '';
        $o_email = isset($_POST['order']['email']) ? trim($_POST['order']['email']) : '';
        //$o_comment = isset($_POST['order']['comment']) ? trim($_POST['order']['comment']) : '';
		
		/* delivery method */
		$o_delivery = isset($_POST['order']['delivery']) ? intval($_POST['order']['delivery']) : '0';
		/* delivery price */
		$o_delivery_price = '0';
		if($o_delivery > 0){
			$row = $db->get_row("SELECT price, currency FROM ".$db->tables['delivery']." WHERE id = '".$o_delivery."' ");
			if($row && $db->num_rows == 1){
				$kurs = 1;
				if(isset($site->vars['sys_currency']) && $site->vars['sys_currency'] != $row->currency){
					if($row->currency == 'euro' && !empty($site->vars['kurs_euro'])){ $kurs = $site->vars['kurs_euro']; }
					if($row->currency == 'usd' && !empty($site->vars['kurs_usd'])){ $kurs = $site->vars['kurs_usd']; }
					if($row->currency == 'rur' && !empty($site->vars['kurs_rur'])){ $kurs = $site->vars['kurs_rur']; }
				}				
				$o_delivery_price = $kurs*$row->price;
			}
		}

        $ar_order = $_POST['order'];
		$o_payment = isset($ar_order['payment_method']) ? intval($ar_order['payment_method']) : 0;
        unset($ar_order['payment_method']);
        unset($ar_order['when']);
        unset($ar_order['name']);
        unset($ar_order['phone']);
        unset($ar_order['email']);
        unset($ar_order['delivery']);
        //unset($ar_order['comment']);
		
        if(!empty($ar_order)){
            $str = '<ul>';
            foreach($ar_order as $k => $v){
                $str .= "<li><b>".$k."</b>: ".$v."</li>";
            }            
            $str .= "</ul>";        
        }
        
        $created = date('Y-m-d H:i:s');

		$datetime1 = date_create('2015-01-01');
		$datetime2 = date_create(date('Y-m-d'));
		$interval = date_diff($datetime1, $datetime2);
		$days = $interval->format('%a');
		$d1 = mt_rand(1, 19);
		$d2 = mt_rand(101, 999);
		$new_order_id = $days.$d1.$d2;
		/*
			Новый номер заказа, чтобы нельзя было перебрать и вычислить номер
		*/
        
        $db->query("INSERT INTO ".$db->tables['orders']." (order_id, payment_method, 
            delivery_method, created, ur_type, fio, name_company, email, 
            city, metro, phone, address, address_memo, memo, status, 
            last_edit, manager_id, region, delivery_price, delivery_index, 
            payd_status, site_id) VALUES('".$new_order_id."', 
            '".$o_payment."', '".$o_delivery."', '".$created."', '0', '".$db->escape($o_name)."', 
            '', '".$db->escape($o_email)."', '', '', 
            '".$db->escape($o_phone)."', '', '',
            '".$db->escape($str)."', '0', 
            '0000-00-00 00:00:00', '0', '',
            '".$o_delivery_price."', '0', 
			'0', '".$site->vars['id']."') ");
            
        if(!empty($db->last_error)){ return $site->db_error(basename(__FILE__).": 113"); }
        $id = $db->insert_id;
		
		/* связь с организацией */
		$orgs = !empty($site->vars['orgs']) ? intval($site->vars['orgs']) : 0;
		$sql = "INSERT INTO ".$db->tables['connections']."
			(id1, name1, id2, name2) 
			VALUES ('".$id."', 'order', '".$orgs."', 'org')
		";
		$db->query($sql);
		
		/* запишем инфо с сессией */
		$sess_current = session_id();
		$site->stop_db_cache();
		$sql = "SELECT id FROM ".$db->tables['visit_log']." 
			WHERE `sess` = '".$db->escape($sess_current)."' AND `referer` <> 'search' ";
		$visit_id = $db->get_var($sql);
		if(!empty($visit_id)){
			$sql = "INSERT INTO ".$db->tables['connections']."
				(id1, name1, id2, name2) 
				VALUES ('".$id."', 'order', '".$visit_id."', 'visit_log')
			";
			$db->query($sql);
		}
		$site->start_db_cache();
        $ar_cart = $_POST['cart'];
        $cart_to_send = $total_summ = array();
        
        foreach($ar_cart as $k => $v){
			$pricerate = 1;
			if($v['currency'] == 'euro' && !empty($site->vars['kurs_euro'])){ 
				$pricerate = $site->vars['kurs_euro']; 
			}
			
			if($v['currency'] == 'usd' && !empty($site->vars['kurs_usd'])){ 
				$pricerate = $site->vars['kurs_usd']; 
			}
			
			if($v['currency'] == 'rur' && !empty($site->vars['kurs_rur'])){ 
				$pricerate = $site->vars['kurs_rur']; 
			}			
					
            $db->query("INSERT INTO ".$db->tables['orders_cart']." (orderid, 
                gtdid, serialnumber, items, qty, price, 
				pricebuy, pricerate, buyrate, manager, currency_buy, 
				currency_sell, product_id, 
                discount, memo, 
				original_price, 
				original_rate) VALUES('".$id."', '0', '', 
                '".$db->escape($v['item'])."', '".$db->escape($v['qty'])."', 
                '".$db->escape($v['price'])."', 
				'0.00', '".$pricerate."', '0.00', '0', '', 
                '".$db->escape($v['currency'])."', 
                '".$db->escape($v['id'])."', '', '', 
				'".$db->escape($v['price'])."', 
				'".$pricerate."'
				) ");

			if(!empty($db->last_error)){ return $site->db_error(basename(__FILE__).": 127"); }
            
            $summ = $v['qty'] * $v['price'];
            
            $cart_to_send[] = array(
                'title' => $v['item'],
                'price' => $v['price'],
                'discount' => 0,
                'discount_formatted' => '',
                'currency' => $v['currency'],
                'price_formatted' => $site->price_formatted($v['price'], $v['currency']),
                'qty' => $v['qty'],
                'summ' => $summ,
                'summ_formatted' => $site->price_formatted($summ, $v['currency'])
            );
                            
            if(!isset($total_summ[$v['currency']])){
                $total_summ[$v['currency']] = $summ;
            }else{
                $total_summ[$v['currency']] += $summ;
            } 
        }
        
		/* учтем скидку по купону, если есть */
		if(!empty($total_summ) && count($total_summ) == 1){
			register_coupon($id, $total_summ, $site);	
		}			
		
		$arr = prepare_order_info($id);
		$site->send_email_event('order_new', $arr);
		
        $url = $site->vars['site_url'].'/order'.URL_END.'?done='.md5($id.$created);
  
		if(!empty($_SESSION['basket'])){
			foreach($_SESSION['basket'] as $k => $v){
				unset($_SESSION['basket'][$k]);
			}
			unset($_SESSION['basket']);
			unset($_SESSION['basket_total']);
		}
		
		
		
		/* проверим, если от партнера, то запишем */
		if(!empty($_SESSION['welcome'])){
			$str = trim($_SESSION['welcome']);
			$ref1 = 0;
			$ref2 = 0;
			
			$partner = $db->get_row("SELECT * 
				FROM ".$db->tables['users']." 
				WHERE `login` = '".$db->escape($str)."'
			");
			
			if(!empty($partner) && $db->num_rows == 1){
				$ref1 = $partner->ref1;
				$ref2 = $partner->ref2;
				$sql = "INSERT INTO 
					".$db->tables['partner_orders']." 
					(`order_id`, `from_name`, `ref1`, `ref2`) 
					VALUES('".$id."', 
					'".$db->escape($str)."',
					'".$db->escape($ref1)."',
					'".$db->escape($ref2)."'
					)
				";
				$db->query($sql);
			}
			
		}
		
		return $site->redirect($url);
		//header("Location: ".$url);
		//exit;
    }
	
	function set_order_paid($order, $site){
		/*
		$order['id'] = $order['id'];
		$order['payment_info'] = $requestBody;
		$order['set_paid'] = $set_paid;
		$order['send_notification'] = 1;
		$order['comment'] = 'txt';
		$order['method'] = 'kassa.yandex';
		*/
		
		global $db;
		$status_paid = $db->get_var("SELECT `id` 
			FROM ".$db->tables['order_status']." 
			WHERE `alias` = 'paid' 
				AND (`site` = 0 OR `site` = '".$site->id."')
		");
		
		if(!empty($status_paid) && !empty($order['set_paid'])){
			$sql = "UPDATE ".$db->tables['orders']." 
				SET 
					`status` = '".$status_paid."',
					`payd_status` = '1'
				WHERE 
					`id` = '".$order['id']."'
			";
			$db->query($sql);
		}elseif(!empty($order['set_paid'])){
			$sql = "UPDATE ".$db->tables['orders']." 
				SET 
					`payd_status` = '1'
				WHERE 
					`id` = '".$order['id']."'
			";
			$db->query($sql);
		}
		
		/* add comment */
		$record_type = 'order';
		$record_id = $order['id'];
		$userid = 0;
		$unreg_email = empty($order['method']) 
			? 'bot' : $order['method'];
		$comment_text = $order['comment'];
		$ip_address = isset($_SERVER["REMOTE_ADDR"]) ? trim($_SERVER["REMOTE_ADDR"]) : '';
		$ddate = date('Y-m-d H:i:s');
		$ext_h1 = '';
		$ext_desc = '';
		$notify = $order['send_notification'];
		
		$sql = "INSERT INTO ".$db->tables['comments']." 
			(`record_type`, `record_id`, `userid`, 
				`unreg_email`, `comment_text`, 
				`ip_address`, `ddate`, `ext_h1`, 
				`ext_desc`, `notify`, `active`
			) VALUES (
				'".$db->escape($record_type)."',
				'".$db->escape($record_id)."',
				'".$db->escape($userid)."',
				'".$db->escape($unreg_email)."',
				'".$db->escape($comment_text)."',
				'".$db->escape($ip_address)."',
				'".$db->escape($ddate)."',
				'".$db->escape($ext_h1)."',
				'".$db->escape($ext_desc)."', 
				'".$notify."', '0'
			) 
		";
		$db->query($sql);
		$id = $db->insert_id;

		$site->register_changes('comment', $id, 'add', 
			base64_encode(serialize($order['payment_info']))
		);
		$site->register_changes('order', $order['id'],
			'set_paid', ''
		);
		
		/* nofification */
		if($notify){
			$mes = $site->lang['order']['payment']['success'];
			$ar = prepare_order_info($order['id'], $mes);
			$site->send_email_event('order_paid', $ar);
		}
		return;
	}
	
	
?>