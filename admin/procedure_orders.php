<?php
/*
  ok simpla version
  last updated 20.02.2020 
  Last updates:
  - creating new order
  - connected organizations
  - order stat (visit)
  - set manager for order
  - get info about connected org
  - sms link editable by elements
*/
if(!defined('SIMPLA_ADMIN')){ die(); }

global $tpl, $admin_vars;

if(isset($_GET['id'])){
  $str_content = edit_order($admin_vars['uri']['id']);
}else{
  $str_content = list_orders();
}

/* ok */
function edit_order($id){
	global $db, $tpl, $lang, $site_vars, $site, $admin_vars;
	
	/* order->org */
	$sql = "SELECT o.id, o.title, o.active, o.is_default, o.inn, 
			c.id as connected  
		FROM ".$db->tables['org']." o 
		LEFT JOIN ".$db->tables['connections']." c ON (".$id." = c.id1 
			AND c.name1 = 'order' 
			AND id2 = o.id 
			AND name2 = 'org')
		WHERE o.`own` = '1' 
		ORDER BY o.`active` desc, o.`is_default` desc, o.`title`
	";
	$o_rows = $db->get_results($sql, ARRAY_A);	
	$tpl->assign("orgs", $o_rows);	
	
	if($id > 0){
		
	$row = $db->get_row("SELECT o.*, 
		u.`name` as manager_name,
		u.`login` as manager_login, 
		u.`login` as manager_email, 
		s.name_short as site, s.site_url, 
		d.title as d_title, d.price as d_price, d.currency as d_currency, 
		os.edit as order_edit, os.title as status_title, cn.id2 as visit_log_id, 
		IF(dis.discount_summ > 0, dis.discount_summ, 0) as dis_summ, 
		c.title as coupon, c.id as coupon_id, 
		
		(SELECT SUM(qty*price*pricerate) - SUM(qty*discount)
			FROM ".$db->tables['orders_cart']." 
			WHERE orderid = o.id
		) as order_summ, 
		
		(SELECT SUM(qty*price*pricerate) - SUM(qty*discount) + delivery_price - dis_summ
			FROM ".$db->tables['orders_cart']." 
			WHERE orderid = o.id
		) as total_summ, 
		
		(SELECT id2
			FROM ".$db->tables['connections']." 
			WHERE id1 = o.id AND name1 = 'order' 
			AND name2 = 'org'
		) as org
		
    FROM ".$db->tables['orders']." o 
    LEFT JOIN ".$db->tables['site_info']." s ON (o.site_id = s.id) 
    LEFT JOIN ".$db->tables['delivery']." d ON (o.delivery_method = d.id)      
    LEFT JOIN ".$db->tables['order_status']." os ON (os.id = o.status) 
	LEFT JOIN `".$db->tables['discounts']."` dis on (o.id = dis.order_id) 
	LEFT JOIN `".$db->tables['coupons']."` c on (dis.is_coupon = c.id) 
	LEFT JOIN ".$db->tables['connections']." cn on (o.id = cn.id1 AND cn.name1 = 'order' AND cn.name2 = 'visit_log') 
	LEFT JOIN `".$db->tables['users']."` u on (o.manager_id = u.id) 
					
    WHERE o.id = '".$id."' 
    ", ARRAY_A);
	//$db->debug(); exit;
	if(!empty($db->last_error)){ return db_error(basename(__FILE__).": ".__LINE__); }
	
	if(!$row || $db->num_rows == 0){
		return error_not_found();    
	}
	
	}else{
		$row = array(
			'site_id' => $site->vars['id'],
			'list_sites' => $site->vars['list_sites'],
			'd_price' => '0',
			'd_currency' => 'euro',
			'created' => date('Y-m-d H:i:s'),
			'last_edit' => date('Y-m-d H:i:s'),
			'site_url' => $site->vars['site_url'],
			'id' => 0,
			'delivery_price' => '0',
		);
	}

	$sql = "SELECT u.id, u.`name`, u.login, u.email
		FROM ".$db->tables['users']." u 
		LEFT JOIN ".$db->tables['users_prava']." up ON (u.id = up.bo_userid)
		WHERE u.`admin` = '1'
		AND u.`active` = '1' 
		AND up.`orders` <> '0'
	";
	$row['managers'] = $db->get_results($sql, ARRAY_A);
	
	/* if set_payd */ 
	if(!empty($row['payd_status'])){
		$set_paid_date = $db->get_row("SELECT 
				c.`date_insert`, 
				u.login as `login`, 
				u.`name` as `name`
			FROM ".$db->tables['changes']." c
			LEFT JOIN ".$db->tables['users']." u on (u.id = c.who_changed)
			WHERE 
				c.`where_changed` = 'order'
				AND c.`where_id` = '".$id."' 
				AND c.`type_changes` = 'set_paid'
				
		", ARRAY_A);
		$s = strtotime($set_paid_date['date_insert']);
		if($s > 0){
			$row["set_paid_date"] = date('d.m.Y H:i', $s);
			$row["set_paid_login"] = $set_paid_date['login'];
			$row["set_paid_name"] = $set_paid_date['name'];
		}
	}
	
	if(!empty($row['visit_log_id'])){		
		$sql = "SELECT * FROM ".$db->tables['visit_log']." WHERE id = '".$row['visit_log_id']."' ";
		$row['visit_log'] = $db->get_row($sql, ARRAY_A);
		
		if(!empty($row['visit_log']['time'])){
			$row['visit_log']['time'] = date($site->vars['site_date_format'].' '.$site->vars['site_time_format'], strtotime($row['visit_log']['time']));			
		}
		
		if(!empty($row['visit_log']['pages_visited'])){
			$row['visit_log']['pages_visited'] = explode(' | ', $row['visit_log']['pages_visited']);
		}		
	}
	
	if(!empty($_GET['del_comment']) && !empty($site->user['prava']['settings'])){
		$sql = "DELETE FROM ".$db->tables['comments']." 
				WHERE id = '".intval($_GET['del_comment'])."' 
				AND record_type = 'order' 
				AND record_id = '".$row['id']."'
		";
		$db->query($sql);
		delete_uploaded_files(intval($_GET['del_comment']), 'order_comment');
		register_changes('order', $id, 'update', 'comment deleted');
		
		/* если чужой заказ, то уведомим менеджера */
		if($admin_vars['bo_user']['id'] != $row['manager_id'] && $row['manager_id'] > 0){
			$msg = 'Пользователь '.$admin_vars['bo_user']['name'].' ('.$admin_vars['bo_user']['login'].') удалил комментарий (#'.intval($_GET['del_comment']).') в <a href="'.$row['site_url'].'/'.ADMIN_FOLDER.'/?action=orders&id='.$id.'">заказе '.$row['site_id'].'-'.$row['order_id'].'</a>, где Вы являетесь исполнителем.';
			$ar = prepare_order_info($id, $msg);
			$ar['customer'] = array();	
			send_email_event('order_comment', $ar);
		}
		
	}

	/* напоминание клиенту про оплату */
	if(isset($_POST['o']['order_remind']) && !empty($_POST['o']['id']) ){
		$msg = 'Напоминание про оплату (order_remind).';
		$ip_address = !empty($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '';  
		$ar = prepare_order_info($id, '');		
		send_email_event('order_remind', $ar);
		register_changes('order', $id, 'update', 'sent order_remind');
		
		$sql = "INSERT INTO ".$db->tables['comments']." (record_type, 
				record_id, userid, comment_text, ddate, ip_address, 
				unreg_email, active, ext_h1, ext_desc, notify) 
				VALUES('order', '".$id."', '".$site->user['id']."', 
				'".$db->escape($msg)."', '".date('Y-m-d H:i:s')."', 
				'".$ip_address."', '', '0', '', '', 
				'1')";
		$db->query($sql);
		$url = "?action=orders&id=".$id."&order_remind=1";
		header("Location: ".$url);
		exit;
	}
	
	
  
	$docs = array();
	if(!empty($site->vars['sys_docs'])){
		$str = $site->vars['sys_docs'];
		$docs1 = explode ("\n",$str);
		foreach(array_keys($docs1) as $key) $docs1[$key] = trim($docs1[$key]);
				
		foreach($docs1 as $v){
			$vv = explode("=",$v);
			
			if(isset($vv[0])){
				$t1 = isset($vv[1]) ? trim($vv[1]) : $vv[0];
				$docs[] = array(
					'file' => $vv[0], 
					'title' => $t1,
					'url' => '/order/doc/?order='.md5($row['id'].$row['created']).'&page='.str_replace('.html', '', $vv[0])
				);
			}			
		}
	}
	
	$row['docs'] = $docs;
	/* сумма прописью, если понадобится
	require_once('get_summ_txt.php');
	$string = money2str_ru($arr['order']['order_summ_total']);
	$char = mb_strtoupper(substr($string,0,2), "utf-8");
	$string[0] = $char[0];
	$string[1] = $char[1];
	$arr['order']['order_summ_total_str'] = $string;
	*/
  
  
	$p_rows = $db->get_results("SELECT * FROM ".$db->tables['order_payments']." 
		WHERE active = '1' AND (`site` = '0' OR `site` = '".$row['site_id']."')  
		ORDER BY `sort`, `title` ", ARRAY_A);
	$row['list_payments'] = $p_rows;

	$s_rows = $db->get_results("SELECT * FROM ".$db->tables['order_status']." 
		WHERE `active` = '1'  AND (`site` = '0' OR `site` = '".$row['site_id']."')
		ORDER BY `sort`, `title` ", ARRAY_A);
	$row['list_statuses'] = $s_rows;

	$d_rows = $db->get_results("SELECT * FROM ".$db->tables['delivery']." 
		WHERE site = '0' OR site = '".$row['site_id']."' 
		ORDER BY `sort`, `title` ", ARRAY_A);
	if($d_rows && $db->num_rows > 0){
		foreach($d_rows as $d1 => $d2){
			if($d2['currency'] == 'usd'){
				$d2['currency_str'] = GetMessage('currency', 'usd');
			}else{
				$d2['currency_str'] = $d2['currency'] == 'euro' 
					? GetMessage('currency', 'euro') 
					: GetMessage('currency', 'rur');				
			}
			$d_rows[$d1]['price_formatted'] = price_formatted($d2['price'], $d2['currency']);			
		}
	}
	$row['delivery_list'] = $d_rows;
	
	$row['d_price_formatted'] = price_formatted($row['d_price'], $row['d_currency']);
		
	
  /* добавляем товары, если выбраны ранее */
  if(!empty($_POST["add_items"]) && !empty($_POST["do_add_products"])){
	 
    $i = 0;
     foreach($_POST["add_items"] as $v){
        $row5 = $db->get_row("SELECT * 
					FROM ".$db->tables['products']." 
					WHERE id = '".$db->escape($v)."' ");
        if($row5){
			$price = $row5->price_spec > 0 ? $row5->price_spec : $row5->price;
            $db->query("INSERT INTO ".$db->tables['orders_cart']." 
                (`orderid`, `items`, `qty`, `price`, `currency_sell`, 
				`product_id`, `pricerate`, `original_price`, `original_rate`) 
                VALUES('".$id."', '".$db->escape($row5->name)."', '1', 
                '".$price."', '".$db->escape($row5->currency)."', 
				'".$row5->id."', '1', '".$price."', '1') 
                ");
            $i++;
			register_changes('order', $id, 'update', 'item added: '.$row5->name.' (ID:'.$row5->id.')');
        }     
     }
	 
	/* если чужой заказ, то уведомим менеджера */
	if($admin_vars['bo_user']['id'] != $row['manager_id'] && $row['manager_id'] > 0){
	 
	 	$msg = 'Пользователь '.$admin_vars['bo_user']['name'].' ('.$admin_vars['bo_user']['login'].') добавил товар в <a href="'.$row['site_url'].'/'.ADMIN_FOLDER.'/?action=orders&id='.$id.'">заказ '.$row['site_id'].'-'.$row['order_id'].'</a>, где Вы являетесь исполнителем.';
		$ar = prepare_order_info($id, $msg);
		$ar['customer'] = array();
		send_email_event('order_comment', $ar);
	}	
	 
     $url = "?action=orders&id=".$id."&added=".$i;
     header("Location: ".$url);
     exit;
  } 
  
  if(!empty($_GET['delete']) && intval($_GET['delete']) > 0){
		$d_row = $db->get_row("SELECT c.*, p.name as product_name
				FROM ".$db->tables['orders_cart']." c
				LEFT JOIN ".$db->tables['products']." p ON (c.product_id = p.id)
				WHERE c.ID = '".intval($_GET['delete'])."' ", ARRAY_A);
		$str = '';
		if(!empty($d_row['product_name'])){ $str .= $d_row['product_name'].' '; }
		$str .= '(ID:'.$d_row['product_id'].')';

        $db->query("DELETE FROM ".$db->tables['orders_cart']." WHERE ID = '".intval($_GET['delete'])."' ");
		
		register_changes('order', $id, 'update', 'item deleted: '.$str);
				
		/* если чужой заказ, то уведомим менеджера */
		if($admin_vars['bo_user']['id'] != $row['manager_id'] && $row['manager_id'] > 0){
	 
			$msg = 'Пользователь '.$admin_vars['bo_user']['name'].' ('.$admin_vars['bo_user']['login'].') удалил товар в <a href="'.$row['site_url'].'/'.ADMIN_FOLDER.'/?action=orders&id='.$id.'">заказе '.$row['site_id'].'-'.$row['order_id'].'</a>, где Вы являетесь исполнителем.';
			$msg .= '<p>Товар: '.$str.'</p>';
			$ar = prepare_order_info($id, $msg);
			$ar['customer'] = array();
			send_email_event('order_comment', $ar);
		}
		
        $url = "?action=orders&id=".$id."&deleted=1";
        header("Location: ".$url);
        exit;    
  }

  if(isset($_POST['o']['update']) && !empty($_POST['o']['id']) ){
    // сохраняем
    $o_status = !empty($_POST['o']['status']) ? intval($_POST['o']['status']) : 0;               
    $o_ur_type = !empty($_POST['o']['ur_type']) ? intval($_POST['o']['ur_type']) : 0;
    $o_payd_status = !empty($_POST['o']['payd_status']) ? intval($_POST['o']['payd_status']) : 0;    
	
	
	/* связка с организацией */
	$orgs = isset($_POST["orgs"]) ? intval($_POST["orgs"]) : 0;
	$sql = "DELETE FROM ".$db->tables['connections']." 
		WHERE id1 = '".$id."'
		AND name1 = 'order' 
		AND name2 = 'org'
	";
	$db->get_row($sql);
	$sql = "INSERT INTO ".$db->tables['connections']." 
			(id1, name1, id2, name2) 
			VALUES('".$id."', 'order', '".$orgs."', 'org')
	";
	$db->query($sql);
	
	/* проверим изменилась ли метка оплаты и статус заказа */
	/* если да, то отправим прикрепленные почтовые события */
	$old = $db->get_row("SELECT 
		o.id, o.order_id, o.created, o.status, o.delivery_price, 
		o.delivery_index, o.manager_id, 
		o.payd_status, 
			(SELECT s.alias
			FROM ".$db->tables['order_status']." s
			WHERE s.id = o.status) as event_old,
			(SELECT s.alias
			FROM ".$db->tables['order_status']." s
			WHERE s.id = '".$o_status."') as event_new 
		FROM ".$db->tables['orders']." o
		WHERE o.id = '".$id."' ");	  
	
	
    $o_fio = !empty($_POST['o']['fio']) ? trim($_POST['o']['fio']) : '';    
    $o_phone = !empty($_POST['o']['phone']) ? trim($_POST['o']['phone']) : '';    
    $o_email = !empty($_POST['o']['email']) ? trim($_POST['o']['email']) : '';
    $o_name_company = !empty($_POST['o']['name_company']) ? trim($_POST['o']['name_company']) : '';
    $o_city = !empty($_POST['o']['city']) ? trim($_POST['o']['city']) : '';
    $o_metro = !empty($_POST['o']['metro']) ? trim($_POST['o']['metro']) : '';
    $o_address = !empty($_POST['o']['address']) ? trim($_POST['o']['address']) : '';
    $o_address_memo = !empty($_POST['o']['address_memo']) ? trim($_POST['o']['address_memo']) : '';
    $o_last_edit = date('Y-m-d H:i:s');
    $o_payment_method = !empty($_POST['o']['payment_method']) ? intval($_POST['o']['payment_method']) : 0;
    $o_delivery_method = !empty($_POST['o']['delivery_method']) ? intval($_POST['o']['delivery_method']) : 0;
    $o_delivery_price = !empty($_POST['o']['delivery_price']) ? intval($_POST['o']['delivery_price']) : 0;
    $o_delivery_index = !empty($_POST['o']['delivery_index']) ? intval($_POST['o']['delivery_index']) : 0;
    $o_region = !empty($_POST['o']['region']) ? trim($_POST['o']['region']) : '';

    $sql = "UPDATE ".$db->tables['orders']." SET 
            `status` = '".$db->escape($o_status)."',
            `ur_type` = '".$db->escape($o_ur_type)."',
            `payd_status` = '".$db->escape($o_payd_status)."',
            `fio` = '".$db->escape($o_fio)."',
            `phone` = '".$db->escape($o_phone)."',
            `email` = '".$db->escape($o_email)."',
            `name_company` = '".$db->escape($o_name_company)."',
            `city` = '".$db->escape($o_city)."',
            `metro` = '".$db->escape($o_metro)."',
            `address` = '".$db->escape($o_address)."',
            `address_memo` = '".$db->escape($o_address_memo)."',
            `last_edit` = '".$db->escape($o_last_edit)."',
			`payment_method` = '".$db->escape($o_payment_method)."', 
			`region` = '".$db->escape($o_region)."',
            `delivery_index` = '".$db->escape($o_delivery_index)."',
            `delivery_price` = '".$db->escape($o_delivery_price)."',
			`delivery_method` = '".$db->escape($o_delivery_method)."'

        WHERE id = '".$id."'     
    ";
	$db->query($sql);
	if($db->rows_affected > 0){
		register_changes('order', $id, 'update');
		
		/* если чужой заказ, то уведомим менеджера */
		if($admin_vars['bo_user']['id'] != $row['manager_id'] && $row['manager_id'] > 0 && empty($_POST["send_comment"]["message"])){
	 
			$msg = 'Пользователь '.$admin_vars['bo_user']['name'].' ('.$admin_vars['bo_user']['login'].') обновил <a href="'.$row['site_url'].'/'.ADMIN_FOLDER.'/?action=orders&id='.$id.'">заказ '.$row['site_id'].'-'.$row['order_id'].'</a>, где Вы являетесь исполнителем.';
			$ar = prepare_order_info($id, $msg);
			$ar['customer'] = array();
			send_email_event('order_comment', $ar);
		}
		
	}  

    if(!empty($_POST['o']['update_link']) AND !empty($_POST['o']['created'])){
        $new_created = date('Y-m-d H:i:s', strtotime($_POST['o']['created'])+10);
        $db->query("UPDATE ".$db->tables['orders']." SET created = '".$new_created."'
            WHERE id = '".$id."' ");
		register_changes('order', $id, 'update', 'open link changed');
    }        

    if(!empty($_POST['cart'])){
		$i = 0;		
        foreach($_POST['cart'] as $k => $v){
        
            $c_items = isset($v['items']) ? trim($v['items']) : '';
            $c_qty = isset($v['qty']) ? intval($v['qty']) : '1'; 
            $c_price = isset($v['price']) ? floatval($v['price']) : '0';
            $c_pricerate = isset($v['pricerate']) ? floatval($v['pricerate']) : '1';
            $c_discount = isset($v['discount']) ? floatval($v['discount']) : '0';
            $c_currency_sell = isset($v['currency_sell']) ? trim($v['currency_sell']) : '';
            
            $sql = "UPDATE ".$db->tables['orders_cart']." SET 
                items = '".$db->escape($c_items)."',
                qty = '".$db->escape($c_qty)."', 
                price = '".$db->escape($c_price)."', 
                pricerate = '".$db->escape($c_pricerate)."', 
                discount = '".$db->escape($c_discount)."', 
                currency_sell = '".$db->escape($c_currency_sell)."' 
                WHERE ID = '".intval($k)."'
            ";
            $db->query($sql);
			if($db->rows_affected > 0){
				$i++;		
			}  
        }
		if($i > 0){
			$c1 = $i == 1 ? 'updated 1 item' : 'updated '.$i.' items';
			register_changes('order', $id, 'update', $c1);
		}
    
    }

	/* БЛОК УВЕДОМЛЕНИЙ */	
	if($old->payd_status < $o_payd_status){
		/* поставлена оплата - отправим уведомление order_paid */
		$ar = prepare_order_info($id, '');
		send_email_event('order_paid', $ar);
		
		register_changes('order', $id, 'set_paid');
	}
	
	if(!empty($old->event_new) && $old->event_new != $old->event_old){
		/* статус изменен - проверим и отправим сообщение  */
		$new_status = 'order_'.$old->event_new;
		$event = str_replace('order_order_', 'order_', $new_status);
		$ar = prepare_order_info($id, '');
		send_email_event($event, $ar);		
		register_changes('order', $id, 'status_changed');		
		
		/* если чужой заказ, то уведомим менеджера */
		if($admin_vars['bo_user']['id'] != $row['manager_id'] && $row['manager_id'] > 0 && empty($_POST["send_comment"]["message"])){
	 
			$msg = 'Пользователь '.$admin_vars['bo_user']['name'].' ('.$admin_vars['bo_user']['login'].') обновил статус <a href="'.$row['site_url'].'/'.ADMIN_FOLDER.'/?action=orders&id='.$id.'">заказа '.$row['site_id'].'-'.$row['order_id'].'</a>, где Вы являетесь исполнителем.<br>
			Новый статус - '.$event;
			$ar['customer'] = array();
			$ar['message'] = $msg;
			send_email_event('order_comment', $ar);
		}
		
		
	}
	/* КОНЕЦ БЛОКА УВЕДОМЛЕНИЙ */	
	
	
	/* если изменен менеджер, то запишем и уведомим */
	if(isset($_POST['o']['manager_id']) 
		&& intval($_POST['o']['manager_id']) != $old->manager_id
	){
		$sql = "UPDATE ".$db->tables['orders']." 
			SET manager_id = '".intval($_POST['o']['manager_id'])."'
			WHERE id = '".$id."' 
		";
		$db->query($sql);
		
		$isp1 = 'Новый исполнитель: ';
		if(intval($_POST['o']['manager_id']) == 0){
			$isp1 .= '- не выбран';
		}else{
			$row3 = $db->get_row("SELECT * 
				FROM ".$db->tables['users']." 
				WHERE id = '".intval($_POST['o']['manager_id'])."'
			");
			if(!$row3 || $db->num_rows == 0){
				$isp1 .= 'n\a';
			}else{
				$isp1 .= $row3->name.' ('.$row3->login.')';
			}
		}
		
		$ip_address = !empty($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : ''; 
		$sql = "INSERT INTO ".$db->tables['comments']." (record_type, 
				record_id, userid, comment_text, ddate, ip_address, 
				unreg_email, active, ext_h1, ext_desc, notify) 
				VALUES('order', '".$id."', '".$site->user['id']."', 
				'".$db->escape($isp1)."', '".date('Y-m-d H:i:s')."', 
				'".$ip_address."', '', '0', '', '', '0')
		";
		$db->query($sql);			
		register_changes('order', $id, 'comment', $isp1);
		
		if(intval($_POST['o']['manager_id']) > 0 
			&& !empty($row3->email)
		){
			/* уведомим менеджера */			
			$str5 = $row3->name.' ('.$row3->login.'), Вы новый исполнитель по <a href="'.$row['site_url'].'/'.ADMIN_FOLDER.'/?action=orders&id='.$id.'">заказу '.$row['site_id'].'-'.$row['order_id'].'</a>. '.$row['site'];
			$ar = prepare_order_info($id, $str5);
			//$ar['customer'] = array();
			send_email_event('order_new_manager', $ar);
		}
		
		/* уведомим бывшего исполнителя */
		if($admin_vars['bo_user']['id'] != $row['manager_id'] && $row['manager_id'] > 0){
				
			$msg = 'Пользователь '.$admin_vars['bo_user']['name'].' ('.$admin_vars['bo_user']['login'].') назначил нового исполнителя <a href="'.$row['site_url'].'/'.ADMIN_FOLDER.'/?action=orders&id='.$id.'">заказа '.$row['site_id'].'-'.$row['order_id'].'</a>, где Вы были исполнителем.';
			$ar = prepare_order_info($id, $msg);
			$ar['customer'] = array();
			send_email_event('order_comment', $ar);
		}
		
		
	}
	
	
	// Проверим есть ли коммент и запишем его, если есть
	if(!empty($_POST["send_comment"]["message"])){
		$msg = trim($_POST["send_comment"]["message"]);
		$ip_address = !empty($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '';  
		if(!empty($msg)){
			$notify = empty($_POST["send_comment"]["notify"]) ? 0 : 1;
			$active = empty($_POST["send_comment"]["active"]) ? 0 : 1;
			$sms = empty($_POST["send_comment"]["sms"]) ? 0 : 1;
		
			$sql = "INSERT INTO ".$db->tables['comments']." (record_type, 
				record_id, userid, comment_text, ddate, ip_address, 
				unreg_email, active, ext_h1, ext_desc, notify) 
				VALUES('order', '".$id."', '".$site->user['id']."', 
				'".$db->escape($msg)."', '".date('Y-m-d H:i:s')."', 
				'".$ip_address."', '', '".$active."', '', '', 
				'".$notify."')";
			$db->query($sql);
			if(!empty($db->last_error)){
				return db_error(basename(__FILE__).' LINE: '.__LINE__); 			
			}
			
			if(!empty($notify)){
				$ar = prepare_order_info($id, $msg);
				send_email_event('order_comment', $ar);
			}else{
				$ar = prepare_order_info($id, $msg);
				$ar['customer'] = array();
				send_email_event('order_comment', $ar);
			}
						
			$comment_id = $db->insert_id;
			register_changes('order', $id, 'comment', 'comment #'.$comment_id);
			upload_files($comment_id, 'order_comment');
			
			/* если отправка SMS - то обработаем через smsc.ru */
			$o_phone = trim($o_phone);
			$o_phone = preg_replace('~[^0-9]+~','',$o_phone); 			
			
			if(!empty($sms) && !empty($o_phone)){
				/* зададим логин и пароль */
				$sms1 = isset($site->vars['sys_smsc_login']) 
					? $site->vars['sys_smsc_login'] : ''; 
				$sms2 = isset($site->vars['sys_smsc_password'])
					? $site->vars['sys_smsc_password'] : '';
				if (!defined('SMSC_LOGIN')) { define('SMSC_LOGIN', $sms1); }
				if (!defined('SMSC_PASSWORD')) { define('SMSC_PASSWORD', $sms2); } 
				// пароль или MD5-хеш пароля в нижнем регистре
				if(!empty($site->vars['sys_smsc_debug'])
					&& !defined('SMSC_DEBUG')
				){
					define('SMSC_DEBUG', 1);
				}
				
				if($o_phone[0] == 8){
					$o_phone = '7'.substr($o_phone, 1);
				}
								
				$f = MODULE.'/smsc_api.php';
				if(file_exists($f)){
					include_once($f);
					//$msg = str_replace('\n','%0A',$msg);
					
					$sms_message = $msg.' 
					'.$row['site'];
					
					if(!empty($site->vars['sys_smsc_url'])){
						$sms_message .= '  
						'.$row['site_url'].'/o/'.$row['order_id'];
					}
					$sms_ar = send_sms($o_phone, $sms_message, 0);
					if(!empty($sms_ar) && count($sms_ar) > 2){
						/* отправлено! запишем в историю */
						register_changes('comment', $comment_id, 'sms');
					}
				}				
			}
			
			header("Location: ?action=orders&id=".$id."#comment".$comment_id);
			exit;
		}
	}
	
    	
        
    header("Location: ?action=orders&id=".$id."&updated=1");
    exit;
  }elseif(!empty($_POST['o']['add'])){
	  /* Добавляем заказ */
	  
		$o_status = !empty($_POST['o']['status']) ? intval($_POST['o']['status']) : 0;               
		$o_ur_type = !empty($_POST['o']['ur_type']) ? intval($_POST['o']['ur_type']) : 0;
		$o_payd_status = !empty($_POST['o']['payd_status']) ? intval($_POST['o']['payd_status']) : 0;    
		$o_fio = !empty($_POST['o']['fio']) ? trim($_POST['o']['fio']) : '';    
		$o_phone = !empty($_POST['o']['phone']) ? trim($_POST['o']['phone']) : '';    
		$o_email = !empty($_POST['o']['email']) ? trim($_POST['o']['email']) : '';
		$o_name_company = !empty($_POST['o']['name_company']) ? trim($_POST['o']['name_company']) : '';
		$o_city = !empty($_POST['o']['city']) ? trim($_POST['o']['city']) : '';
		$o_metro = !empty($_POST['o']['metro']) ? trim($_POST['o']['metro']) : '';
		$o_address = !empty($_POST['o']['address']) ? trim($_POST['o']['address']) : '';
		$o_address_memo = !empty($_POST['o']['address_memo']) ? trim($_POST['o']['address_memo']) : '';
		
		$o_last_edit = date('Y-m-d H:i:s');
		$o_payment_method = !empty($_POST['o']['payment_method']) ? intval($_POST['o']['payment_method']) : 0;
		$o_delivery_method = !empty($_POST['o']['delivery_method']) ? intval($_POST['o']['delivery_method']) : 0;
		$o_delivery_price = !empty($_POST['o']['delivery_price']) ? intval($_POST['o']['delivery_price']) : 0;
		$o_delivery_index = !empty($_POST['o']['delivery_index']) ? intval($_POST['o']['delivery_index']) : 0;
		$o_region = !empty($_POST['o']['region']) ? trim($_POST['o']['region']) : '';
		$o_site_id = !empty($_POST['o']['site_id']) ? intval($_POST['o']['site_id']) : 0;

		$datetime1 = date_create('2015-01-01');
		$datetime2 = date_create(date('Y-m-d'));
		$interval = date_diff($datetime1, $datetime2);
		$days = $interval->format('%a');
		$d1 = mt_rand(1, 19);
		$d2 = mt_rand(101, 999);
		$order_id = $days.$d1.$d2;
		
		$sql = "INSERT INTO ".$db->tables['orders']." 
			(`order_id`,
			`payment_method`, 
			`delivery_method`, 
			`created`, 
			`ur_type`, 
			`fio`, 
			`name_company`, 
			`email`, 
			`city`, 
			`metro`, 
			`phone`, 
			`address`, 
			`address_memo`, 
			`memo`,
			`status`, 			
			`region`, 
			`delivery_price`, 
			`delivery_index`, 
			`payd_status`, 
			`site_id`) 
			VALUES
			('".$order_id."', 
			'".$db->escape($o_payment_method)."', 
			'".$db->escape($o_delivery_method)."', 
			'".$o_last_edit."',
			'".$db->escape($o_ur_type)."',
            '".$db->escape($o_fio)."',
            '".$db->escape($o_name_company)."',
            '".$db->escape($o_email)."',
            '".$db->escape($o_city)."',
            '".$db->escape($o_metro)."',
            '".$db->escape($o_phone)."',
            '".$db->escape($o_address)."',
            '".$db->escape($o_address_memo)."',
			'', 			
			'".$db->escape($o_status)."', 			
			'".$db->escape($o_region)."',
            '".$db->escape($o_delivery_price)."',
            '".$db->escape($o_delivery_index)."',		
            '".$db->escape($o_payd_status)."',
			'".$o_site_id."') 
		";
		$db->query($sql);		
        if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }
		$id = $db->insert_id;
		
		register_changes('order', $id, 'add');
		
		/* связь с организацией */
		$orgs = !empty($_POST["orgs"]) ? intval($_POST["orgs"]) : 0;
		$sql = "INSERT INTO ".$db->tables['connections']." 
			(id1, name1, id2, name2) 
			VALUES ('".$id."', 'order', '".$orgs."', 'org') 
		";
		$db->query($sql);

		/* Добавим товар, если указан */
		if(!empty($_POST['cart'][0]['items'])){
			
			$c_items = trim($_POST['cart'][0]['items']);
			$c_qty = !empty($_POST['cart'][0]['qty']) ? intval($_POST['cart'][0]['qty']) : 1;
			$c_price = !empty($_POST['cart'][0]['price']) ? intval($_POST['cart'][0]['price']) : 0;
			$c_pricerate = !empty($_POST['cart'][0]['pricerate']) ? intval($_POST['cart'][0]['pricerate']) : 1;
			$c_discount = !empty($_POST['cart'][0]['discount']) ? intval($_POST['cart'][0]['discount']) : 'rur';
			$c_currency_sell = !empty($_POST['cart'][0]['currency_sell']) ? $_POST['cart'][0]['currency_sell'] : 'rur';

			$sql = "INSERT INTO ".$db->tables['orders_cart']." 
				(`orderid`, `items`, `qty`, `price`, 
				`pricerate`, `currency_sell`, 
				`product_id`, `discount`, 
				`original_price`, `original_rate`) 
				VALUES (
				'".$id."', '".$db->escape($c_items)."', 
				'".$db->escape($c_qty)."', 
				'".$db->escape($c_price)."',
				'".$db->escape($c_pricerate)."', 
				'".$db->escape($c_currency_sell)."', 
				'0', 
				'".$db->escape($c_discount)."', 
				'".$db->escape($c_price)."', 
				'".$db->escape($c_pricerate)."')
			";
			$db->query($sql);
			if(!empty($db->last_error)){ return db_error(basename(__FILE__)." LINE: ".__LINE__); }
		}
		
		header("Location: ?action=orders&id=".$id."&added=1");
		exit;  
	  
  }elseif(!empty($_POST['o']['delete']) && !empty($_POST['o']['id']) ){
	
	/* уведомим бывшего исполнителя */
	if($admin_vars['bo_user']['id'] != $row['manager_id'] && $row['manager_id'] > 0){
				
			$msg = 'Пользователь '.$admin_vars['bo_user']['name'].' ('.$admin_vars['bo_user']['login'].') удалил заказ '.$row['site_id'].'-'.$row['order_id'].' [ID: '.$id.'], где Вы были исполнителем.';
			$ar = prepare_order_info($id, $msg);
			$ar['customer'] = array();
			send_email_event('order_comment', $ar);
	}
	
	/* пройдемся по комментам и удалим их с файлами */
	$sql = "SELECT * FROM ".$db->tables['comments']." 
			WHERE record_type = 'order' AND record_id = '".$id."' ";
	$rows = $db->get_results($sql);
	
	if($rows && $db->num_rows > 0){
		foreach($rows as $row){
			delete_comments($id, 'order');
		}
	}

    /* удаляем заказ, комменты и то что в корзине */    
    $db->query("DELETE FROM ".$db->tables['orders']." WHERE id = '".$id."' ");
    $db->query("DELETE FROM ".$db->tables['orders_cart']." WHERE orderid = '".$id."' ");
	
	/* удалим привязку к избранным */
	$query = "DELETE FROM ".$db->tables["fav"]." WHERE where_id = '".$id."' AND `where_placed` = 'order' ";
	$db->query($query);
	
	$sql = "DELETE FROM ".$db->tables['connections']." 
			WHERE id1 = '".$id."'
				AND name1 = 'order', 
				AND name2 = 'org' 			 
		";
	$db->query($sql);
	register_changes('order', $id, 'delete', 'ID: '.$id.' ');
	
    header("Location: ?action=orders&deleted=1");
    exit;
  }

  $row["date"] = date($site_vars['site_date_format']." ".$site_vars['site_time_format'], strtotime($row["created"]));
  
  if(empty($row["last_edit"]) || $row["last_edit"] == '0000-00-00 00:00:00'){
	$row["last_edit"] = '';
  }else{
	$row["last_edit"] = date($site_vars['site_date_format']." ".$site_vars['site_time_format'], strtotime($row["last_edit"]));  
  }
  

  // /order/?done=1adb691a82d7b2c96c8711ba6d0b0897
  $row["open_link"] = $row['site_url'].'/order/?done='.md5($row['id'].$row['created']);
  $row["sberbank_link"] = $row['site_url'].'/order/pay/sberbank/?done='.md5($row['id'].$row['created']);
  $row["invoice_link"] = $row['site_url'].'/order/pay/invoice/?done='.md5($row['id'].$row['created']);
  $ids = array();
  $total = 0;
 
  if($id > 0){
	  $sql = "SELECT c.*, 
				(SELECT (c.qty*c.price*c.pricerate) - (qty*discount) 
					FROM ".$db->tables['orders_cart']."               
					WHERE id = c.id 
				) as summ,
				(SELECT (c.qty*c.original_price*c.original_rate) - (qty*discount)
					FROM ".$db->tables['orders_cart']."               
					WHERE id = c.id 
				) as original_summa,
				p.alias  
		FROM ".$db->tables['orders_cart']." c 
		LEFT JOIN ".$db->tables['products']." p ON (c.product_id = p.id)   
		WHERE c.orderid = '".$id."' ";
		$cart = $db->get_results($sql, ARRAY_A);
		
		  foreach($cart as $c){
			$ids[] = "'".$c['product_id']."'";
			if(!isset($check_currency)){ $check_currency = $c['currency_sell']; }
			if($check_currency != $c['currency_sell']){ $row["diff_currency"] = "1"; }
			$total += $c['summ'];    
		  }
		  $row["subtotal"] = $total;
		  $row["total"] = $total+$row['delivery_price']-$row['dis_summ'];
		
  }else{

		$cart = array();
		$cart[] = array(
		
			'ID' => 0,
			'orderid' => 0,
			'gtdid' => '',
			'serialnumber' => '',
			'items' => '',
			'qty' => 1,
			'price' => 0,
			'pricebuy' => 0,
			'pricerate' => 0,
			'buyrate' => 1,
			'manager' => 0,
			'currency_buy' => 0,
			'currency_sell' => 0,
			'product_id' => 0,
			'discount' => 0,
			'memo' => '',
			'original_price' => '',
			'original_rate' => '',
			'summ' => 0,
			'original_summa' => 0,
			'alias' => ''																		
		);
		
		  
		  $row["subtotal"] = 0;
		  $row["total"] = 0;
  
  }
  
  $comments = $db->get_results("SELECT c.*, u.login as user_login, 
		u.`name` as user_name, 
		f.id as file_id, 
		f.size as file_size, 
		f.filename as file_name, 
		f.title as file_title, 
		f.ext as file_ext,
		sms.id as sms,
		ch.comment as pay_comment
		
	FROM ".$db->tables['comments']." c
	LEFT JOIN ".$db->tables['users']." u on (c.userid = u.id)
	LEFT JOIN ".$db->tables['uploaded_files']." f on (c.id = f.record_id 
		AND f.record_type = 'order_comment')
	LEFT JOIN ".$db->tables['changes']." sms on (c.id = sms.where_id 
		AND sms.where_changed = 'comment' AND `type_changes` = 'sms')
	LEFT JOIN ".$db->tables['changes']." ch on (
		c.id = ch.where_id AND ch.where_changed = 'comment' AND ch.`type_changes` = 'add')
	WHERE c.record_type = 'order' AND c.record_id = '".$id."' 
	ORDER BY c.ddate ", ARRAY_A);
	
	/* comments*/
	if(!empty($comments)){
		foreach($comments as $k => $v){
			if(!empty($v['pay_comment'])){
				$str = unserialize(base64_decode($v['pay_comment']));
				$str = print_r($str, true);
				$comments[$k]['pay_comment'] = $str;
			}
		}
	}
	
  $tpl->assign("site", $site->vars);
  $tpl->assign("o", $row);
  $tpl->assign("cart", $cart);
  $tpl->assign("comments", $comments);

  $ids_str = implode(',', $ids);
  $str_add = !empty($ids_str) ? " AND id NOT IN (".$ids_str.") " : "";
  
  $what_add = !empty($_POST['add']) ? trim($_POST['add']) : '';
  if(!empty($what_add) && isset($_POST['add_product'])){
    $list_add = $db->get_results("SELECT 
            id, price, `name`, barcode, alias, currency  
        FROM ".$db->tables['products']." 
        WHERE 
            (id LIKE '%".$db->escape($what_add)."%' 
            OR barcode LIKE '%".$db->escape($what_add)."%' 
            OR alias LIKE '%".$db->escape($what_add)."%' 
            OR `name` LIKE '%".$db->escape($what_add)."%')
            $str_add               
        ", ARRAY_A);
    $tpl->assign("list_add", $list_add);
  }
  return $tpl->display("info/order_edit.html");
}

/* ок */
function list_orders(){
  global $db, $tpl, $site, $admin_vars;
  $deleted = 0;

    $all_sites = $db->get_results("SELECT id, name_short as title, site_url as url 
        FROM ".$db->tables['site_info']." ORDER BY name_short, id ", ARRAY_A); 
    $tpl->assign('all_sites', $all_sites);


    $str_where = !empty($_GET['site_id']) ? " WHERE o.`site_id` = '".intval($_GET['site_id'])."' " : "";
	if(!empty($_GET['number'])){
		$got_number = trim($_GET['number']);
		if(!empty($got_number)){
			if(empty($str_where)){ $str_where = " WHERE "; } else { $str_where .= " AND "; }
			$str123 = explode('-',$got_number);
			array_shift($str123);
			$got_number1 = implode('',$str123);
			$got_number2 = str_replace('-','',$got_number);
			//echo $got_number2; exit;
			$str_where .= " ( ";
			$str_where .= " o.order_id LIKE '%".$got_number2."%' ";
			$str_where .= " OR o.order_id LIKE '".$got_number2."' ";
			if(!empty($got_number1)){
				$str_where .= " OR o.order_id LIKE '%".$got_number1."%' 
				";
			}			
			$str_where .= " OR UPPER(o.fio) LIKE '%".mb_strtoupper($got_number, 'UTF-8')."%'
			";
			$str_where .= " OR UPPER(o.email) LIKE '%".mb_strtoupper($got_number, 'UTF-8')."%' 
			";
			$str_where .= " OR UPPER(o.phone) LIKE '%".mb_strtoupper($got_number, 'UTF-8')."%' 
			";
			$str_where .= " 
			) 
			";			
		}
		//echo $got_number1; exit;
	}else{
		
		if(empty($str_where)){ 
			$str_where = " WHERE "; 
		} else { 
			$str_where .= " AND "; 
		}
			
		$str_where .= " (manager_id = '0' 
			OR manager_id = '".$admin_vars['bo_user']['id']."' 
			OR (SELECT `user_id` 
					FROM ".$db->tables['fav']." 
					WHERE `where_placed` = 'order' 
					AND `where_id` = o.id 
					AND `user_id` = '".$admin_vars['bo_user']['id']."'
				) = '".$admin_vars['bo_user']['id']."'
		) 
		";
	}
	
	$query = "SELECT o.*, d.title as delivery_title, 
				s.title as status_title, s.sort as status_sort, 
				IF(dis.discount_summ > 0, dis.discount_summ, 0) as dis_summ, 
				dis.discount_summ as discount_summ, 
				c.title as coupon, 
				
				IF(
					(SELECT COUNT(DISTINCT currency_sell) 
						FROM ".$db->tables['orders_cart']." 
							WHERE orderid = o.id
					) > '1', 
				(SELECT SUM(qty*price*pricerate-discount) - 
					IFNULL(dis_summ,0) 
					FROM ".$db->tables['orders_cart']." 
					WHERE orderid = o.id
				), (SELECT SUM(qty*price-discount) - 
						IFNULL(dis_summ,0) 
						FROM ".$db->tables['orders_cart']." 
						WHERE orderid = o.id
					)
				) as summa,
				
				IF(
					(SELECT COUNT(DISTINCT currency_sell) 
						FROM ".$db->tables['orders_cart']." 
							WHERE orderid = o.id
					) > '1', 
				(SELECT SUM(qty*price*pricerate-discount) - 
					IFNULL(dis_summ,0) + delivery_price
					FROM ".$db->tables['orders_cart']." 
					WHERE orderid = o.id
				), (SELECT SUM(qty*price-discount) - 
						IFNULL(dis_summ,0) + delivery_price
						FROM ".$db->tables['orders_cart']." 
						WHERE orderid = o.id
					)
				) as total_summ,
				
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
				(SELECT SUM(qty*original_price*original_rate-discount) - 
					IFNULL(dis_summ,0) 
					FROM ".$db->tables['orders_cart']." 
					WHERE orderid = o.id
				), (SELECT SUM(qty*original_price-discount) - 
						IFNULL(dis_summ,0) 
						FROM ".$db->tables['orders_cart']." 
						WHERE orderid = o.id
					)
				) as original_summa, 
				
				IF(
				
					(SELECT `sort` 
					FROM ".$db->tables['fav']." 
					WHERE `where_placed` = 'order' 
					AND `where_id` = o.id 
					AND `user_id` = '".$admin_vars['bo_user']['id']."') 
				> '0', 1, 0) as fav, 
				
				(SELECT COUNT(*) 
					FROM ".$db->tables['comments']." 
					WHERE `record_type` = 'order' 
					AND `record_id` = o.id
				) as comments	
				
								
            FROM ".$db->tables['orders']." o 
			LEFT JOIN ".$db->tables['delivery']." d on (d.id = o.delivery_method)
			LEFT JOIN ".$db->tables['order_status']." s on (s.id = o.status) 
			LEFT JOIN ".$db->tables['discounts']." dis on (o.id = dis.order_id) 
			LEFT JOIN ".$db->tables['coupons']." c on (dis.is_coupon = c.id) 
		
			 ".$str_where." 
            ORDER BY fav DESC, status_sort, o.`created` DESC ";
	
	$rows = $db->get_results($query);
	//$db->debug(); exit; 

	if(!empty($db->last_error)){ return db_error(basename(__FILE__).": ".__LINE__); }

	// PAGE LIMITS
	if(isset($_GET["page"])){ $page = intval($_GET["page"]); } else { $page = 0; }
	$on_Page = ONPAGE;
	$limit_Start = $page*$on_Page; // for pages generation
	$limit = "limit $limit_Start, $on_Page";

	$all_results = $db->num_rows;

	$query = $query." ".$limit;

	// COUNT PAGES
    /*
	if($page > 0) { $next = $page-1; }
	if($all_results > $limit_Start+$on_Page){ $last = $page+1; }
	if($limit_Start == 0){ $p1 = 1; } else { $p1 = $limit_Start; }
	$strPages = "";
	$pages_all = ceil($all_results/$on_Page);
	if($pages_all < 2){ $strPages = ""; }
	else {
		for($i = 0; $i < $pages_all; $i++){
			$start1 = $i*$on_Page+1;
			$start2 = $i*$on_Page+$on_Page;
			if($start2 > $all_results) { $start2 = $all_results; }
			if($page == $i){ $strPages .= " [$start1-$start2] "; }
			else { $strPages .= " [<a href=?action=orders&page=$i>$start1-$start2</a>] "; }
		}
	}
    */

	$rows = $db->get_results($query, ARRAY_A);
	if($db->num_rows == 0){
  	$tpl->assign("site",$site->vars);
  	$tpl->assign("orders_list",array());
  	return $tpl->display("info/orders_list.html");
	}

  $tpl->assign("pages",_pages($all_results, $page, $on_Page,true));

	//$tpl->assign("pages", $strPages);
  	$tpl->assign("site",$site->vars);
	$tpl->assign("orders_list",$rows);
	return $tpl->display("info/orders_list.html");
}

?>