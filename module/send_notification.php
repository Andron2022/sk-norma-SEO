<?php

    /*
    *
    *   The function sends email notification
    *   to website admin and to customer
    *   21.11.2019
    *   + skip caching
	*	+ fixes 21.11.2019
    */

    function prepare_email_altbody($body)
    {           
        if (preg_match('|<body>(.*)</body>|Uis', $body, $out)) {
            $altbody = $out[0];    
        } else {
            $altbody = $body;    
        }
        
        $altbody = strip_tags($altbody);
        $altbody = preg_replace("/(\r\n)+/i", "\r\n", $altbody);
        return nl2br($altbody);        
    }
    
    function send_notification($ar, $site){
		// stop this function
		return;
    }
    
    
    /* отправка сообщения */
    function send_notification2($subject, $body, $to_name='', $to_email='')
	{
        $from_email = SI_EMAIL;
        $from_name = SI_TITLE_SHORT;
        if(empty($to_email)){ $to_email = SI_EMAIL; }
        if(empty($to_name)){ $to_name = SI_TITLE_SHORT; }
        $altbody = prepare_email_altbody($body);    
      
        date_default_timezone_set(SI_TIMEZONE);
        require_once 'phpmailer/PHPMailerAutoload.php';
        $mail = new PHPMailer();
        $mail->CharSet = strtoupper(SI_CHARSET);

        if(defined('SI_SMTP_ON')){

            $mail->isSMTP();
            if(SI_SMTP_SECURE != "-") {
                $mail->SMTPSecure = SI_SMTP_SECURE;
            }
            
            $mail->SMTPDebug = 0;
    	    $mail->Debugoutput = 'html';
            $mail->Host = SI_SMTP_HOST;
            $mail->Port = SI_SMTP_PORT;
            $mail->SMTPAuth = SI_SMTP_AUTH == 1 ? true : false;
            $mail->Username = SI_SMTP_USERNAME;
            $mail->Password = SI_SMTP_PASSWORD;
            $mail->setFrom(SI_SMTP_USERNAME, $from_name);
            $mail->addReplyTo($from_email, $from_name);
            $mail->addAddress($to_email, $to_name);
            $mail->Subject = $subject;
            $mail->msgHTML($body);
            $mail->AltBody = $altbody;
			$_SESSION['email_sent'] = 'smtp';
        }else{
			$mail->setFrom($from_email, $from_name);
            $mail->addReplyTo($from_email, $from_name);
            $mail->addAddress($to_email, $to_name);
            $mail->Subject = $subject;
            $mail->msgHTML($body);
            $mail->AltBody = $altbody;
            $mail->IsSendmail();
            $mail->Send();
			$_SESSION['email_sent'] = 'sendmail';
            return;
        }
        
        /* если smtp не прошло, то через sendmail отправим */
        if (!$mail->send()) {
			$mail->setFrom($from_email, $from_name);
            $mail->addReplyTo($from_email, $from_name);
            $mail->addAddress($to_email, $to_name);
            $mail->Subject = $subject;
            $mail->msgHTML($body);
            $mail->IsSendmail();
            $mail->Send();
			$_SESSION['email_sent'] = 'sendmail';
        }
        return;  
    }      
	

	/* отформатируем цену и старую цену */
    function price_formatted($price, $currency, $decimals=NULL){  

		if(empty($price)){ 
			return defined('SI_PRICE_ZERO') 
				? SI_PRICE_ZERO 
				: 'Call us'; 
		}
	
		/* переведем в рубли, если задано */
		$price_ar = price_recalculate($price, $currency);
		$price = $price_ar['price'];
		$currency = $price_ar['currency'];
		
		if($decimals === NULL){			
			$decimals = SI_DECIMALS; 
		}
        $dec_point = SI_DEC_POINT; 
        $thousands_sep = SI_THOUSANDS_SEP;

        $start = $stop = '';        
        $start = $currency == 'usd' ? SI_USD : '';
		
        if(empty($start)){
            $stop = $currency == 'euro' ? SI_EURO : SI_RUB;
        }
        
        if(empty($start) && empty($stop)){ $stop = $currency; }
		
		$new_price = number_format($price, $decimals, $dec_point, $thousands_sep);
        $formatted = $start.$new_price.$stop;
        return $formatted; 
    }
  
  
	/* correct price to default currency */
	function price_recalculate($price, $currency){
		$decimals = defined('SI_DECIMALS') ? SI_DECIMALS : 2;
		if (defined('SI_CURRENCY') && SI_CURRENCY != $currency){
			if(SI_CURRENCY == 'rur'){
				if($currency == 'euro' && defined('SI_RATE_EURO')){
					$price = SI_RATE_EURO*$price;
					$currency = SI_CURRENCY;
				}elseif($currency == 'usd' && defined('SI_RATE_USD')){
					$price = SI_RATE_USD*$price;	
					$currency = SI_CURRENCY;
				}
			}
		}
		return array(
			'price' => number_format($price, $decimals, '.', ''),
			'currency' => $currency
			);
	}

	function correct_total_summ($total_summ, $delivery=0, $discount=0, $coupon=0, $nds = 0){		
        if(count($total_summ) > 1){
            $ar = array('error' => 'The cart contents different currencies'); 
        }else{
            $currency = key($total_summ);
            $summ = current($total_summ);                
			$nds_summ = 0;
			if($nds > 0){
				$nds_summ = $summ - ($summ/(1+$nds/100));	
				$nds_summ = round($nds_summ, 2);
			}
			
            $ar = array(
                'currency' => $currency,
                'summ' => $summ,
                'summ_formatted' => price_formatted($summ, $currency),
				'coupon' => $coupon,
                'coupon_formatted' => price_formatted($coupon, $currency),
                'delivery' => $delivery,
                'delivery_formatted' => price_formatted($delivery, $currency),
                'discount' => $discount,
                'discount_formatted' => price_formatted($discount, $currency),
                'total' => $summ+$delivery-$coupon,
				'total_text' => summ_text($summ+$delivery-$coupon),
                'total_formatted' => price_formatted($summ+$delivery-$coupon, $currency),
				'nds' => $nds,
				'nds_summ' => $nds_summ,
				'nds_formatted' => price_formatted($nds_summ, $currency, 2),
                'error' => ''                
            );
        }
        return $ar;                      
    }  
  
	/* email event by code or name */
	function get_email_event($event){
		global $db;
		if(!isset($db->tables['email_event'])) { 
			$db->tables['email_event'] = 'email_event'; 
		}
		if(!isset($db->tables['email_event_type'])){ 
			$db->tables['email_event_type'] = 'email_event_type'; 
		}
		if(!isset($db->tables['option_values'])){ 
			$db->tables['option_values'] = 'option_values'; 
		}
		$old = $db->cache_queries;
		$db->cache_queries = false;
		$sql = "SELECT e.*,
			(SELECT `value` 
				FROM ".$db->tables['option_values']." 
				WHERE id_option = '0' 
				AND id_product = e.id 
				AND where_placed = 'email_event_sms'
			) as sms,
			(SELECT `value` 
				FROM ".$db->tables['option_values']." 
				WHERE id_option = '0' 
				AND id_product = e.id 
				AND where_placed = 'email_event_phone'
			) as sms_phone 
			FROM ".$db->tables['email_event_type']." et, 
				".$db->tables['email_event']." e
			WHERE et.active = '1' AND 
				(et.id = '".$db->escape($event)."' OR et.event = '".$db->escape($event)."') AND et.id = e.event_type_id 
				AND (e.site = '0' OR e.site = '".SI_ID."') ";
		$rows = $db->get_results($sql, ARRAY_A);
		$db->cache_queries = $old;
		return $rows;
	}	
	
	
	/*
		Manual
		http://ru.simpla.es/ru/docs/developer/email-notifications/
	*/
	function send_email_event($event, $ar=NULL){
		global $db, $site, $tpl;
		if (!class_exists('Template_Lite')) {
			require(MODULE."/tpl/src/class.template.php");
			$tpl = new Template_Lite;
			$tpl->compile_dir = ADMIN_FOLDER."/compiled/";
			$tpl->cache_dir = ADMIN_FOLDER."/compiled";	
			$tpl->force_compile = true;
			$tpl->compile_check = true;
			$tpl->cache = true;
			$tpl->cache_lifetime = 3600;
			$tpl->config_overwrite = false;
		}

		$event_ar = get_email_event($event);
		$to = array();
		
		if(!empty($event_ar)){
			foreach($event_ar as $k => $v){
				$subject = $v['subject'];
				$body = $v['body'];
				//$tpl->assign('site', $site->vars);
				if(empty($body)){
					break 1;
				}
				$tpl->assign('ar', $ar);
				$body = $tpl->fetch_html($body);
				$subject = $tpl->fetch_html($subject);

				if(!empty($ar['test'])){
					return $body;
				}
				
				if(!empty($v['to_user'])){
					$to['email'] = isset($ar['customer']['email']) ? $ar['customer']['email'] : '';
					$to['name'] = isset($ar['customer']['name']) ? $ar['customer']['name'] : '';
					
					if(!empty($to['email'])){
						/* есть куда отправлять, отправляем */
						if(empty($to['name'])){ $to['name'] = $to['email']; }
						send_notification2($subject, $body, $to['name'], $to['email']);
					}
					
					$to['email'] = '';
					$to['name'] = '';
				}
				
				if(!empty($v['to_admin'])){
					send_notification2($subject, $body, '', '');
				}
				
				if(!empty($ar['manager']['email'])){
					if(empty($v['to_admin']) || SI_EMAIL != $ar['manager']['email']){
						send_notification2($subject, $body, $ar['manager']['email'], $ar['manager']['email']);
					}					
				}
				
				if($k == 0){
					/* проверим есть ли доп.ящики для отправки */
					if(!empty($v['to_extra'])){
						$emails_ar = explode(',', $v['to_extra']);
						if(!empty($emails_ar)){
							foreach($emails_ar as $to_email){
								send_notification2($subject, $body, 
									$to_email, $to_email);
							}
						}
					}
				}	


				/* отправка СМС */
				if(!empty($v['sms']) && !empty($v['sms_phone'])){
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
					
					$f = MODULE.'/smsc_api.php';
					if(file_exists($f)){
						include_once($f);
						$sms_message = $body;
						$sms_ar = send_sms($v['sms_phone'], $sms_message, 0);
						if(!empty($sms_ar) && count($sms_ar) > 2){
							/* отправлено! запишем в историю */
							//register_changes('email_event', $v['id'], 'sms');
						}
					}
				
				
					
				}
			}
		}else{
			$subject = $event;
			$body = '';
			$to_email = '';
			if(!empty($ar['message'])){
				$body .= '<p>'.$ar['message'].'</p>';
			}
			$body .= '<small>Not set post event '.$event.'</small>';
			
			if(!empty($ar['manager']['email'])){
				$to_email = $ar['manager']['email'];
			}
			send_notification2($subject, $body, $to_email, $to_email);
		}		

	}
	
	
	
	/* подготовим массивы данных для уведомлений */
	function prepare_order_info($id, $mes=''){
		global $db, $site;
		$old = $db->cache_queries;
		$db->cache_queries = false;
		$str2 = "SELECT o.*, 
					u.`name` as manager_name,
					u.`login` as manager_login,
					u.`email` as manager_email,
					si.`site_url`, si.`name_short` as `site`, 
					s.title as status_title, 
					d.title as delivery_title, 

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
				LEFT JOIN ".$db->tables['order_status']." s ON (o.status = s.id)
				LEFT JOIN ".$db->tables['delivery']." d ON (o.delivery_method = d.id) 
				LEFT JOIN ".$db->tables['site_info']." si ON (o.site_id = si.id) 
				LEFT JOIN ".$db->tables['discounts']." dis on (o.id = dis.order_id) 
				LEFT JOIN ".$db->tables['coupons']." c on (dis.is_coupon = c.id) 
				LEFT JOIN ".$db->tables['users']." u on (o.manager_id = u.id) 
				WHERE 
					o.id = '".$id."' 
				";
		$row2 = $db->get_row($str2);
		$db->cache_queries = $old;
		if(!$row2){ return; }
		//$db->debug(); exit;
		
		/* подготовим массив для передачи в уведомление */
		$ar = array();
		$ar['order'] = array();
		$ar['order']['site'] = $row2->site;
		$ar['order']['site_url'] = $row2->site_url;
		$ar['order']['site_id'] = $row2->site_id;
		$ar['site'] = $row2->site;
		$ar['site_url'] = $row2->site_url;
		$ar['site_id'] = $row2->site_id;
		
		$ar['order']['order_id'] = $row2->order_id;
		$ar['order']['id_formatted'] = $row2->site_id.'-'.substr(chunk_split($row2->order_id, 4, '-'), 0, -1); 
		$ar['order']['status_title'] = $row2->status_title;
		$ar['order']['phone'] = $row2->phone;
		$ar['order']['address']	= $row2->address;
		$ar['order']['memo'] = $row2->memo;
		$ar['order']['coupon'] = $row2->coupon;
							
		$ar['order']['delivery_title'] = $row2->delivery_title;
		$ar['order']['delivery_price'] = $row2->delivery_price;
		$ar['order']['payd_status']	= $row2->payd_status;
		$md5 = md5($id.$row2->created);
		$url = $row2->site_url.'/order'.URL_END.'?done='.$md5;
		$ar['order']['url']	= $url;
		$url = $row2->site_url.'/o/'.$row2->order_id;
		$ar['order']['tinyurl']	= $url;
		$url = $row2->site_url.'/'.ADMIN_FOLDER.'/?action=orders&id='.$row2->id;
		$ar['order']['adminurl'] = $url;
		$ar['order']['md5']	= $md5;
		$ar['order']['created']	= $row2->created;
		$ar['order']['created_f'] = date(SI_DATEFORMAT.' '.SI_TIMEFORMAT,strtotime($row2->created));
		$ar['order']['last_edit_f']	= date(SI_DATEFORMAT.' '.SI_TIMEFORMAT,strtotime($row2->last_edit));
				
		$ar['manager'] = array();
		$ar['manager']['login'] = $row2->manager_login;
		$ar['manager']['email'] = $row2->manager_email;
		$ar['manager']['name'] = $row2->manager_name;
		$ar['manager']['id'] = $row2->manager_id;
		
		$ar['customer'] = array();
		$ar['customer']['email'] = $row2->email;
		$ar['customer']['name'] = $row2->fio;
		
		$old = $db->cache_queries;
		$db->cache_queries = false;					
		$carts = $db->get_results("SELECT c.*, p.alias as alias  
				FROM ".$db->tables['orders_cart']." c 
				LEFT JOIN ".$db->tables['products']." p ON (c.product_id = p.id)
				WHERE c.orderid = '".$row2->id."' ");
		$db->cache_queries = $old;
		$total_summ = array();
		$ar['cart'] = array();
		$discount = 0;
							
		if($db->num_rows > 0){
			foreach($carts as $c){
				$summ = ($c->qty * $c->price);
				$cart_row = array();
				$cart_row['title'] = $c->items;
				$cart_row['title_link']	= $row2->site_url.'/'.$c->alias.URL_END;
				$cart_row['price'] = $c->price;
				$cart_row['price_formatted'] = price_formatted($c->price, $c->currency_sell);
				$cart_row['discount'] = $c->discount;
				$cart_row['currency'] = $c->currency_sell;
				$cart_row['qty'] = $c->qty;
				$cart_row['summ'] = $summ;
				$ar['cart'][] = $cart_row;
				$discount += $c->discount;

				if(!isset($total_summ[$c->currency_sell])){
					$total_summ[$c->currency_sell] = $summ;
				}else{
					$total_summ[$c->currency_sell] += $summ;
				} 
			}
		}
							
		//$ar['total_summ'] = correct_total_summ($row2->total_summ, $row2->delivery_price, $row2->dis_summ);		
		$currency = $row2->currency;
		
		$ar_total = array(
                'currency' => $currency,
                'summ' => $row2->order_summ,
                'summ_formatted' => price_formatted($row2->order_summ, $currency),
				'coupon' => $row2->dis_summ,
                'coupon_formatted' => price_formatted($row2->dis_summ, $currency),
                'delivery' => $row2->delivery_price,
                'delivery_formatted' => price_formatted($row2->delivery_price, $currency),
                'discount' => $row2->dis_summ,
                'discount_formatted' => price_formatted($row2->dis_summ, $currency),
                'total' => $row2->total_summ,
				'total_text' => summ_text($row2->total_summ),
                'total_formatted' => price_formatted($row2->total_summ, $currency),
				'nds' => 0,
				'nds_summ' => 0,
				'nds_formatted' => price_formatted(0, $currency, 2),
                'error' => ''                
            );
		$ar['total_summ'] = $ar_total;
		
		if(!empty($mes)){
			$ar['message'] = nl2br($mes);
		}		
		return $ar;
	}

	function prepare_fb_info($id, $mes=''){
		global $db;
		$old = $db->cache_queries;
		$db->cache_queries = false;
        
		$ar = $db->get_row("SELECT 
				f.`id`, f.`ticket` as id_formatted, 
				f.`name`, f.`email`, 
				f.`phone`, f.`subject`, 
				f.`message` as message_original,
				f.`sent` as created, 
				f.`hidden` as ip_address, f.`status`, 
				f.`type`, f.`site_id`, 
				f.`from_page`, f.`extras`, 
				u.`id` as manager_id,
				u.`name` as manager_name,
				u.`login` as manager_login,
				u.`email` as manager_email,
				s.name_short as site, 
				s.site_url as site_url
				
			FROM ".$db->tables['feedback']." f
			LEFT JOIN ".$db->tables['site_info']." s ON (f.site_id = s.id) 
			LEFT JOIN ".$db->tables['connections']." o ON (o.id1 = '".$id."' 
				AND o.name1 = 'fb' AND o.name2 = 'manager' ) 
			LEFT JOIN ".$db->tables['users']." u on (o.id2 = u.id) 
			WHERE f.id = '".$id."' 
		", ARRAY_A);
		
		if(!empty($mes)){
			$ar['message'] = nl2br($mes);
		}else{
			$ar['message'] = $ar['message_original'];
		}
		//$db->debug(); exit;
		$db->cache_queries = $old;
		if($db->num_rows == 0){ return $ar; }
		
		if(!empty($ar['created'])){
			$ar['created_f'] = date(SI_DATEFORMAT.' '.SI_TIMEFORMAT, strtotime($ar['created']));
		}

		if(isset($ar['name']) && isset($ar['email'])){
			$ar['customer'] = array(
				'name' => $ar['name'],
				'email' => $ar['email']
			);			
		}
		
		$ar['ticket'] = $ar['id_formatted'];
		$old = $db->cache_queries;
		$db->cache_queries = false;
		$ar['comments'] = $db->get_results("SELECT c.`id`, c.`userid`, 
				c.`comment_text` as message, c.`ddate` as created, 
				c.`ip_address`, c.`active`, c.`notify`, (SELECT `name` 
				FROM ".$db->tables['users']." WHERE id = c.userid) as username
			FROM ".$db->tables['comments']." c
			WHERE c.record_type = 'fb' AND c.record_id = '".$id."' 
				AND c.active = '1'
		", ARRAY_A);
		$db->cache_queries = $old;
		// http://www.loc/feedback/?done=8501e1f7f2715f8240bc5e6d8fe2e657
		$metka = md5($ar['id'].$ar['created']);
		$ar['url'] = $ar['site_url'].'/feedback'.URL_END.'?done='.$metka;
		
		$metka = mb_strtolower(substr($ar['ticket'],-4), 'utf-8').$ar['id'];
		$ar['tinyurl'] = $ar['site_url'].'/f'.URL_END.$metka;
		if(!empty($ar['comments'])){
			$ar['last_comment'] = array_pop($ar['comments']);
			$ar['last_comment']['created_f'] = date(SI_DATEFORMAT.' '.SI_TIMEFORMAT, strtotime($ar['last_comment']['created']));
		}
		
		$ar['manager'] = array();
		$ar['manager']['id'] = $ar['manager_id'];
		$ar['manager']['login'] = $ar['manager_login'];
		$ar['manager']['email'] = $ar['manager_email'];
		$ar['manager']['name'] = $ar['manager_name'];
		unset($ar['manager_id']);
		unset($ar['manager_login']);
		unset($ar['manager_name']);
		unset($ar['manager_email']);
		
		$ar['site_url'] = $ar['site_url'];
		$ar['site_id'] = $ar['site_id'];
		$ar['site'] = $ar['site'];
		$ar['type'] = 'fb'; // page type product/pub/categ
		return $ar;		
	}
	
	
	function prepare_comment_info($id, $mes=''){
		global $db;
		$old = $db->cache_queries;
		$db->cache_queries = false;
		$ar = $db->get_row("SELECT id, userid, 
				comment_text as message_original, 
				ip_address, unreg_email, active, 
				ext_h1 as unreg_name
				FROM ".$db->tables['comments']." 
			WHERE id = '".$id."' ", ARRAY_A);

		if(!empty($mes)){ $ar['message'] = nl2br($mes); }
		$db->cache_queries = $old;
		$ar['site_url'] = SI_URL;
		$ar['site_id'] = SI_ID;
		$ar['site'] = SI_TITLE_SHORT;
		$ar['type'] = 'comment'; // page type product/pub/categ
		return $ar;
	}
	
	
	function prepare_categ_info($id, $mes=''){
		global $db;
		$old = $db->cache_queries;
		$db->cache_queries = false;
		$ar = $db->get_row("SELECT id, title, active, alias 
				FROM ".$db->tables['categs']." 
			WHERE id = '".$id."' ", ARRAY_A); 			
		if(!empty($mes)){ $ar['message'] = nl2br($mes); }
		$db->cache_queries = $old;
		if(!empty($ar['alias'])){ $ar['url'] = SI_URL.'/'.$ar['alias'].URL_END; }
		$ar['site_url'] = SI_URL;
		$ar['site_id'] = SI_ID;
		$ar['site'] = SI_TITLE_SHORT;
		$ar['type'] = 'categ'; // page type product/pub/categ
		return $ar;
	}
	
	function prepare_pub_info($id, $mes=''){
		global $db;
		$old = $db->cache_queries;
		$db->cache_queries = false;
		$ar = $db->get_row("SELECT id, `name` as title, active, alias 
				FROM ".$db->tables['publications']." 
			WHERE id = '".$id."' ", ARRAY_A); 
		if(!empty($mes)){ $ar['message'] = nl2br($mes); }
		$db->cache_queries = $old;
		if(!empty($ar['alias'])){ $ar['url'] = SI_URL.'/'.$ar['alias'].URL_END; }
		$ar['site_url'] = SI_URL;
		$ar['site_id'] = SI_ID;
		$ar['site'] = SI_TITLE_SHORT;
		$ar['type'] = 'pub'; // page type product/pub/categ
		return $ar;
	}
	
	function prepare_product_info($id, $mes=''){
		global $db;
		$old = $db->cache_queries;
		$db->cache_queries = false;
		$ar = $db->get_row("SELECT id, `name` as title, active, alias 
				FROM ".$db->tables['products']." 
			WHERE id = '".$id."' ", ARRAY_A); 
		if(!$ar || $db->num_rows == 0){ return array(); }
		if(!empty($mes)){ $ar['message'] = nl2br($mes); }
		$db->cache_queries = $old;
		if(!empty($ar['alias'])){ $ar['url'] = SI_URL.'/'.$ar['alias'].URL_END; }
		$ar['site_url'] = SI_URL;
		$ar['site_id'] = SI_ID;
		$ar['site'] = SI_TITLE_SHORT;
		$ar['type'] = 'product'; // page type product/pub/categ
		return $ar;
	}
	
	
	function prepare_subs_info($id, $mes=''){
		/*
		$link_add = 'http://'.$site->uri['host'].'/subscription/toadd/?key='.md5($row->id.$row->email);
		$link_delete = 'http://'.$site->uri['host'].'/subscription/todelete/?key='.md5($row->id.$row->email);
		*/
	}

	
	function prepare_user_info($id, $mes=''){
		global $db;
		$old = $db->cache_queries;
		$db->cache_queries = false;
		$ar = $db->get_row("SELECT 
					u.id, u.`name`, u.login, u.email, 
					u.news, u.date_insert, 
					c.id2 as sent_time,
					md5(CONCAT(c.id,c.id1, c.id2)) as md5_key
				FROM ".$db->tables['users']." u 
				LEFT JOIN ".$db->tables['connections']." c 
						ON (u.id = c.id1 AND c.name1 = 'user' 
						AND c.name2 = 'new_password')
			WHERE u.id = '".$id."' ", ARRAY_A); 
		if(!empty($mes)){ $ar['message'] = nl2br($mes); }
		$db->cache_queries = $old;
		if(!empty($ar['email'])){ 
			$md5 = md5($ar['id'].$ar['email']);
			$ar['url']['news'] = array(
				'add' => SI_URL.'/subscription/toadd/?key='.$md5,
				'delete' => SI_URL.'/subscription/todelete/?key='.$md5
			);
			$r_date = $ar['date_insert'];
			$ar['url']['activate'] = SI_URL.'/register/?add='.md5($r_date).'&key='.md5($id);
			if(!empty($ar['md5_key']) && !empty($ar['sent_time'])){
				$ar['url']['new_password'] = SI_URL.'/forget_password/?add='.$ar['md5_key'].'&key='.$md5;
			}
			$ar['customer'] = array(
				'name' => $ar['name'],
				'email' => $ar['email']
			);			
		}
		return $ar;		
	}
	
	function summ_text($summa){
		require_once("get_summ_txt.php");		
		$str = money2str_ru($summa);
		if(function_exists('mb_ucfirst')) {
			$str = mb_ucfirst($str);
		}
		return $str;
	}
		
?>