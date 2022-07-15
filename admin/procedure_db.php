<?php
/*
  ok
  last updated 13.04.2018
  coupons updated
  orders.status updated to smallint
  29.12.16: UPD counter, blocks, visit_log, users_prava
  23.01.17: install mode
  13.04.2018: view tables schemes and records
  30.11.2018: added button remove statistics
  20.06.2019: corrected new_order notification
*/

if(!defined('SIMPLA_ADMIN')){ die(); }
  global $admin_vars;
  $id = $admin_vars['uri']['id'];
  $do = $admin_vars['uri']['do'];
  $table = array();
  $table['name'] = empty($_GET['table']) 
	? '' : $_GET['table'];
  $table['records'] = empty($_GET['records']) 
	? 0 : 1;

  $str_content = '';
  if(MODE != 'install'){
	  if($do == "clear_db_counter"){
		$str_content = clear_db_counter();
	  }elseif($do == "clearcache"){
		$str_content = clear_cache();
	  }elseif($do == "delstat"){
		$str_content = delete_stat();
	  }elseif($do == "clear_db"){
		$str_content = clear_db();
	  }elseif($do == "optimize"){
		$str_content = optimize_tables();
	  }elseif($do == "update"){
		$str_content = update_tables();
	  }elseif($do == "add_db"){
		$str_content = add_db();
	  }elseif($do == "add_emails"){
		$str_content = add_email_events();
	  }elseif($do == "get_dump"){
		$str_content = get_dump();
	  }elseif($do == "view_db"){
		$str_content = view_db_table($table);
	  }else{
		$str_content = get_db_info();
	  }
  }


  function clear_db(){
	global $db;
	$table = !empty($_GET['table']) ? trim($_GET['table']) : '';
	if(!empty($table)){
		$sql = "DELETE FROM ".$db->escape($table)." ";
		$db->query($sql);
	}
		
    header("Location: ?action=db&updated=1");
    exit;
  }
  
  function add_db($key='',$tbl=''){
	  global $db;
	  
	  $db_queries = array(
	  
		'agents' => "(
					  `id` int(18) NOT NULL AUTO_INCREMENT,
					  `module` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
					  `sort` int(18) NOT NULL DEFAULT '100',
					  `name` text CHARACTER SET utf8,
					  `active` tinyint(1) NOT NULL DEFAULT '1',
					  `last_exec` datetime DEFAULT NULL,
					  `next_exec` datetime NOT NULL,
					  `date_check` datetime DEFAULT NULL,
					  `agent_interval` int(18) DEFAULT '86400',
					  `is_period` tinyint(1) DEFAULT '1',
					  `user_id` int(18) DEFAULT NULL,
					  `running` tinyint(1) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`),
					  KEY `next_exec` (`next_exec`),
					  KEY `sort` (`sort`),
					  KEY `active` (`active`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;",

		'blocks' => "(
						`id` int(12) NOT NULL AUTO_INCREMENT,
						`active` tinyint(2) NOT NULL DEFAULT '0',
						`where_placed` varchar(50) NOT NULL DEFAULT 'manual' COMMENT 'header left right footer main manual form',
						`title` varchar(250) NOT NULL,
						`title_admin` varchar(250) NOT NULL,
						`qty` tinyint(4) NOT NULL DEFAULT '0',
						`type` varchar(50) NOT NULL COMMENT 'visitedProducts visitedPubs starredPubs  specProducts lastProducts lastPubs',
						`type_id` int(12) NOT NULL DEFAULT '0',
						`pages` text NOT NULL,
						`skip_pages` text NOT NULL,
						`html` mediumtext NOT NULL,
						`sort` tinyint(4) NOT NULL DEFAULT '99',
						PRIMARY KEY (`id`),
						UNIQUE KEY `id` (`id`),
						KEY `active` (`active`),
						KEY `where_placed` (`where_placed`),
						KEY `type` (`type`),
						KEY `type_id` (`type_id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=21;",

		'categs' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `title` text NOT NULL,
					  `id_parent` int(11) NOT NULL DEFAULT '0',
					  `meta_description` mediumtext NOT NULL,
					  `meta_keywords` mediumtext NOT NULL,
					  `meta_title` mediumtext NOT NULL,
					  `alias` varchar(255) NOT NULL DEFAULT '',
					  `memo` mediumtext NOT NULL,
					  `active` enum('0','1','2') NOT NULL DEFAULT '0',
					  `sort` int(11) NOT NULL DEFAULT '0',
					  `sortby` varchar(30) NOT NULL,
					  `date_insert` datetime DEFAULT NULL,
					  `date_update` datetime DEFAULT NULL,
					  `user_id` int(11) DEFAULT '0',
					  `multitpl` varchar(100) NOT NULL,
					  `icon` varchar(100) NOT NULL,
					  `f_spec` tinyint(1) NOT NULL DEFAULT '0',
					  `in_last` tinyint(1) NOT NULL DEFAULT '0',
					  `shop` tinyint(1) NOT NULL DEFAULT '0',
					  `show_filter` tinyint(1) NOT NULL DEFAULT '0',
					  `memo_short` mediumtext NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `id_parent` (`id_parent`),
					  KEY `alias` (`alias`),
					  KEY `shop` (`shop`),
					  KEY `sort` (`sort`),
					  KEY `sortby` (`sortby`),
					  KEY `active` (`active`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ; ",
		
		'categ_options' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `id_option` int(11) NOT NULL DEFAULT '0',
					  `id_categ` int(11) NOT NULL DEFAULT '0',
					  `where_placed` varchar(50) NOT NULL DEFAULT 'catalog',
					  PRIMARY KEY (`id`),
					  KEY `id_categ` (`id_categ`),
					  KEY `id_categ_2` (`id_categ`,`where_placed`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100 ; ",
					
		'changes' => "(
						`id` int(12) NOT NULL AUTO_INCREMENT, 
						`where_changed` varchar(50) DEFAULT NULL, 
						`where_id` int(12) NOT NULL, 
						`who_changed` int(12) DEFAULT NULL, 
						`type_changes` varchar(20) DEFAULT NULL COMMENT 'add update or delete',
						`date_insert` datetime DEFAULT NULL, 
						`comment` text, 
						PRIMARY KEY (`id`) 
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=100 ; ",
		
		'comments' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `record_type` varchar(50) NOT NULL DEFAULT '',
					  `record_id` int(11) NOT NULL DEFAULT '0',
					  `userid` int(11) NOT NULL DEFAULT '0',
					  `comment_text` mediumtext NOT NULL,
					  `ddate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `ip_address` varchar(50) NOT NULL DEFAULT '',
					  `unreg_email` varchar(100) NOT NULL DEFAULT '',
					  `active` set('-1','0','1') DEFAULT '1',
					  `ext_h1` text,
					  `ext_desc` text,
					  `notify` tinyint(1) DEFAULT '0',
					  UNIQUE KEY `id` (`id`),
					  KEY `record_type` (`record_type`,`record_id`),
					  KEY `active` (`active`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;",

		'connections' => "(
					  `id` int(12) NOT NULL AUTO_INCREMENT,
					  `id1` int(12) NOT NULL,
					  `name1` varchar(50) NOT NULL COMMENT 'categ pub, product option or site',
					  `id2` int(12) NOT NULL,
					  `name2` varchar(50) NOT NULL COMMENT 'categ pub  product option or site',
					  PRIMARY KEY (`id`),
					  KEY `id1` (`id1`,`name1`),
					  KEY `id2` (`id2`,`name2`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=199 ; ",

		'counter' => "(
						`id` bigint(20) NOT NULL AUTO_INCREMENT,
						`for_page` varchar(10) NOT NULL,
						`id_page` int(14) NOT NULL,
						`ip` varchar(16) NOT NULL,
						`time` datetime NOT NULL,
						`hits` bigint(20) NOT NULL,
						`sess` varchar(128) NOT NULL,
						UNIQUE KEY `id_2` (`id`),
						KEY `id` (`id`,`time`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=100; ",
					
		'coupons' => "(
					  `id` int(12) NOT NULL AUTO_INCREMENT,
					  `title` varchar(50) NOT NULL,
					  `date_start` datetime DEFAULT NULL,
					  `date_stop` datetime DEFAULT NULL,
					  `id_product` int(12) DEFAULT '0',
					  `id_categ` int(12) DEFAULT '0',
					  `content` text,
					  `discount_summ` decimal(9,2) NOT NULL,
					  `discount_procent` tinyint(1) DEFAULT '0',
					  `partner_summ` tinyint(3) DEFAULT '0',
					  `for_userid` int(12) DEFAULT '0',
					  `active` tinyint(1) DEFAULT '1',
					  `onetime` tinyint(1) DEFAULT '0',
					  `site_id` tinyint(4) DEFAULT '0',
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `title` (`title`),
					  KEY `for_userid` (`for_userid`),
					  KEY `active` (`active`),
					  KEY `site_id` (`site_id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100 ;", 
		
		'delivery' => "(
					  `id` int(12) NOT NULL AUTO_INCREMENT,
					  `site` smallint(4) NOT NULL DEFAULT '0',
					  `title` varchar(250) NOT NULL,
					  `description` text NOT NULL,
					  `price` float NOT NULL DEFAULT '0',
					  `currency` varchar(5) NOT NULL DEFAULT 'rur',
					  `noprice` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Если 1 то цена рассчитывается дополнительно после оформления заказа',
					  `sort` smallint(4) NOT NULL DEFAULT '0',
					  UNIQUE KEY `id` (`id`),
					  KEY `site` (`site`),
					  KEY `sort` (`sort`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;",

		'delivery2pay' => "(
					  `id` int(12) NOT NULL AUTO_INCREMENT,
					  `delivery` smallint(6) NOT NULL,
					  `payment` smallint(6) NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `delivery` (`delivery`),
					  KEY `payment` (`payment`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;",
					
		'discounts' => "(
						`id` int(12) NOT NULL AUTO_INCREMENT, 
						`is_coupon` int(12) DEFAULT '0', 
						`order_id` int(12) NOT NULL, 
						`discount_summ` decimal(9,2) NOT NULL, 
						`currency` varchar(10) NOT NULL, 
						PRIMARY KEY (`id`), 
						KEY `is_coupon` (`is_coupon`), 
						KEY `order_id` (`order_id`) 
						) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ; ", 

		'email_event' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `event_type_id` int(11) NOT NULL,
					  `site` tinyint(3) NOT NULL DEFAULT '0',
					  `title` varchar(250) NOT NULL,
					  `content` text NOT NULL,
					  `to_user` tinyint(1) NOT NULL DEFAULT '0',
					  `to_admin` tinyint(1) NOT NULL DEFAULT '0',
					  `to_extra` text NOT NULL,
					  `subject` varchar(250) NOT NULL,
					  `body` text NOT NULL,
					  `template` tinyint(3) NOT NULL DEFAULT '0',
					  `is_html` tinyint(1) NOT NULL DEFAULT '1',
					  `active` tinyint(1) NOT NULL DEFAULT '1',
					  `date_added` datetime DEFAULT NULL,
					  `date_updated` datetime DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  KEY `event_type_id` (`event_type_id`),
					  KEY `event_type_id_2` (`event_type_id`,`site`),
					  KEY `active` (`active`),
					  KEY `site` (`site`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;",
		
		'email_event_type' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `event` varchar(150) NOT NULL,
					  `active` tinyint(1) NOT NULL DEFAULT '0',
					  `title` varchar(250) NOT NULL,
					  `content` text NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `active` (`active`),
					  KEY `event` (`event`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;",

		'fav' => "(
					  `id` int(12) NOT NULL AUTO_INCREMENT,
					  `where_placed` varchar(100) NOT NULL DEFAULT 'categ' COMMENT 'Типа записи - categ pub product site var order fb block',
					  `where_id` int(9) NOT NULL COMMENT 'ID типа записи',
					  `user_id` int(9) NOT NULL COMMENT 'ID юзера',
					  `sort` tinyint(4) NOT NULL DEFAULT '0',
					  `title` varchar(255) NOT NULL,
					  `comment` text NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `user_id` (`user_id`),
					  KEY `where_placed` (`where_placed`),
					  KEY `where_id` (`where_id`),
					  KEY `sort` (`sort`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ; ",

		'feedback' => "(
					  `id` int(9) NOT NULL AUTO_INCREMENT,
					  `ticket` varchar(100) NOT NULL DEFAULT '',
					  `name` varchar(250) DEFAULT NULL,
					  `email` varchar(250) DEFAULT NULL,
					  `phone` varchar(250) NOT NULL,
					  `subject` varchar(250) DEFAULT NULL,
					  `message` mediumtext NOT NULL,
					  `sent` datetime DEFAULT NULL,
					  `hidden` varchar(250) NOT NULL DEFAULT '',
					  `status` tinyint(1) DEFAULT '0',
					  `type` varchar(100) NOT NULL,
					  `site_id` tinyint(4) NOT NULL,
					  `from_page` varchar(250) NOT NULL,
					  `extras` text NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `site_id` (`site_id`),
					  KEY `email` (`email`),
					  KEY `status` (`status`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;",

		'go_out' => "(
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `out_url` varchar(250) NOT NULL,
					  `ip` varchar(16) NOT NULL,
					  `time` datetime NOT NULL,
					  `ref_page` varchar(255) NOT NULL,
					  KEY `id` (`id`,`time`),
					  KEY `out_url` (`out_url`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;	",

		'options' => "(			
						`id` int(11) NOT NULL AUTO_INCREMENT, 
						`title` varchar(255) NOT NULL DEFAULT '', 
						`alias` varchar(255) NOT NULL, 
						`sort` int(5) NOT NULL DEFAULT '0', 
						`group_id` int(11) NOT NULL DEFAULT '0', 
						`type` varchar(20) NOT NULL DEFAULT 'val' COMMENT 'int val checkbox select multicheckbox connected categ', 
						`if_select` mediumtext NOT NULL, 
						`show_in_filter` tinyint(1) NOT NULL, 
						`filter_type` varchar(20) NOT NULL, 
						`show_in_list` tinyint(1) NOT NULL, 
						`filter_description` mediumtext NOT NULL, 
						`after` varchar(100) NOT NULL, 
						`icon` varchar(250) NOT NULL, 
						UNIQUE KEY `id` (`id`), 
						KEY `group_id` (`group_id`), 
						KEY `sort` (`sort`), 
						KEY `show_in_list` (`show_in_list`), 
						KEY `alias` (`alias`) 
						) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ; ",
					
		'option_groups' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `to_show` varchar(20) NOT NULL DEFAULT 'all',
					  `title` varchar(255) NOT NULL DEFAULT '',
					  `hide_title` tinyint(1) NOT NULL DEFAULT '0',
					  `sort` int(5) NOT NULL DEFAULT '0',
					  `description` text NOT NULL,
					  `where_placed` varchar(50) NOT NULL DEFAULT 'all',
					  `opt_title` varchar(250) NOT NULL,
					  `value1` varchar(250) NOT NULL,
					  `value2` varchar(250) NOT NULL,
					  `value3` varchar(250) NOT NULL,
					  UNIQUE KEY `id` (`id`),
					  KEY `where_placed` (`where_placed`),
					  KEY `to_show` (`to_show`),
					  KEY `sort` (`sort`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ; ",

		'option_values' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `id_option` int(11) NOT NULL DEFAULT '0',
					  `id_product` int(11) NOT NULL DEFAULT '0',
					  `value` mediumtext NOT NULL,
					  `where_placed` varchar(50) NOT NULL DEFAULT 'product',
					  `value2` text NOT NULL,
					  `value3` text NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `id_goods` (`id_product`),
					  KEY `id_product` (`id_product`,`where_placed`),
					  KEY `id_option` (`id_option`,`id_product`,`where_placed`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=372 ; ",

		'orders' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `order_id` int(12) NOT NULL DEFAULT '0',
					  `payment_method` varchar(255) NOT NULL DEFAULT '',
					  `delivery_method` varchar(255) NOT NULL DEFAULT '',
					  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `ur_type` varchar(100) NOT NULL DEFAULT '',
					  `fio` varchar(255) NOT NULL DEFAULT '',
					  `name_company` varchar(255) NOT NULL DEFAULT '',
					  `email` varchar(255) NOT NULL DEFAULT '',
					  `city` varchar(255) NOT NULL DEFAULT '',
					  `metro` varchar(255) NOT NULL DEFAULT '',
					  `phone` varchar(255) NOT NULL DEFAULT '',
					  `address` text NOT NULL,
					  `address_memo` text NOT NULL,
					  `memo` text NOT NULL,
					  `status` smallint(6) NOT NULL DEFAULT '0',
					  `last_edit` datetime DEFAULT '0000-00-00 00:00:00',
					  `manager_id` int(5) DEFAULT '0',
					  `region` varchar(50) DEFAULT NULL,
					  `delivery_price` double DEFAULT '0',
					  `delivery_index` int(11) DEFAULT '0',
					  `payd_status` tinyint(1) DEFAULT '0',
					  `site_id` tinyint(4) NOT NULL DEFAULT '0',
					  UNIQUE KEY `id` (`id`),
					  KEY `order_id` (`order_id`),
					  KEY `email` (`email`),
					  KEY `status` (`status`),
					  KEY `payd_status` (`payd_status`),
					  KEY `site_id` (`site_id`),
					  KEY `created` (`created`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ",

		'orders_cart' => "(
					  `ID` int(11) NOT NULL AUTO_INCREMENT,
					  `orderid` mediumint(9) DEFAULT NULL,
					  `gtdid` mediumint(12) DEFAULT NULL,
					  `serialnumber` varchar(50) NOT NULL DEFAULT '',
					  `items` varchar(255) DEFAULT NULL,
					  `qty` mediumint(9) DEFAULT NULL,
					  `price` decimal(12,2) DEFAULT NULL,
					  `pricebuy` decimal(9,2) DEFAULT NULL,
					  `pricerate` decimal(4,2) NOT NULL DEFAULT '0.00',
					  `buyrate` decimal(4,2) NOT NULL DEFAULT '0.00',
					  `manager` mediumint(8) unsigned NOT NULL DEFAULT '0',
					  `currency_buy` varchar(20) NOT NULL DEFAULT '0',
					  `currency_sell` varchar(20) NOT NULL DEFAULT '0',
					  `product_id` mediumint(9) DEFAULT NULL,
					  `discount` double NOT NULL DEFAULT '0',
					  `memo` varchar(255) NOT NULL DEFAULT '',
					  `original_price` decimal(12,0) NOT NULL DEFAULT '0' COMMENT 'Цена товара при заказе на сайте',
					  `original_rate` decimal(12,0) NOT NULL DEFAULT '0' COMMENT 'Курс при заказе на сайте',
					  PRIMARY KEY (`ID`),
					  KEY `orderid` (`orderid`),
					  KEY `manager` (`manager`),
					  KEY `product_id` (`product_id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;	",

		'order_payments' => "(
					  `id` int(12) NOT NULL AUTO_INCREMENT,
					  `site` smallint(4) NOT NULL DEFAULT '0',
					  `title` varchar(250) NOT NULL,
					  `description` text NOT NULL,
					  `price_min` float NOT NULL DEFAULT '0',
					  `price_max` float NOT NULL DEFAULT '0',
					  `currency` varchar(5) NOT NULL DEFAULT 'rur',
					  `sort` smallint(4) NOT NULL DEFAULT '0',
					  `active` tinyint(1) NOT NULL DEFAULT '0',
					  `what_todo` varchar(50) NOT NULL COMMENT 'Обработчик для связи с платежными системами',
					  `encoding` varchar(50) NOT NULL DEFAULT 'utf-8',
					  `new_window` tinyint(1) NOT NULL DEFAULT '0',
					  UNIQUE KEY `id` (`id`),
					  KEY `site` (`site`),
					  KEY `sort` (`sort`),
					  KEY `active` (`active`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;",

		'order_status' => "(
					  `id` int(12) NOT NULL AUTO_INCREMENT,
					  `site` smallint(4) NOT NULL DEFAULT '0',
					  `title` varchar(250) NOT NULL,
					  `description` text NOT NULL,
					  `sort` smallint(4) NOT NULL DEFAULT '0',
					  `title_client` varchar(100) NOT NULL,
					  `active` tinyint(1) NOT NULL DEFAULT '1',
					  `show_client` tinyint(1) NOT NULL DEFAULT '1',
					  `edit` tinyint(1) NOT NULL DEFAULT '1',
					  `alias` varchar(50) DEFAULT NULL,
					  UNIQUE KEY `id` (`id`),
					  KEY `site` (`site`),
					  KEY `sort` (`sort`),
					  KEY `active` (`active`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;",
					
					
		'org' => "(
					  `id` int(12) NOT NULL AUTO_INCREMENT,
					  `active` tinyint(1) DEFAULT '0',
					  `own` tinyint(1) DEFAULT '0',
					  `title` varchar(250) NOT NULL,
					  `postal_code` varchar(20) DEFAULT NULL,
					  `country` varchar(250) DEFAULT NULL,
					  `city` varchar(250) DEFAULT NULL,
					  `address` varchar(250) DEFAULT NULL,
					  `inn` varchar(20) DEFAULT NULL,
					  `kpp` varchar(20) DEFAULT NULL,
					  `bik` varchar(20) DEFAULT NULL,
					  `r_account` varchar(50) DEFAULT NULL,
					  `k_account` varchar(50) DEFAULT NULL,
					  `bank` varchar(250) DEFAULT NULL,
					  `phone` varchar(250) DEFAULT NULL,
					  `post_address` varchar(250) DEFAULT NULL,
					  `fio_dir` varchar(250) DEFAULT NULL,
					  `fio_buh` varchar(250) DEFAULT NULL,
					  `fio_dir_kratko` varchar(250) DEFAULT NULL,
					  `fio_buh_kratko` varchar(250) DEFAULT NULL,
					  `ogrn` varchar(20) DEFAULT NULL,
					  `website` varchar(250) DEFAULT NULL,
					  `email` varchar(250) DEFAULT NULL,
					  `data_reg` date DEFAULT NULL,
					  `is_default` tinyint(1) DEFAULT '0',
					  `nds` tinyint(2) DEFAULT '0',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100 ;",					
				
		'partner_orders' => "(
					`id` bigint(20) NOT NULL AUTO_INCREMENT, 
					`order_id` bigint(20) NOT NULL, 
					`from_name` varchar(255) NOT NULL, 
					`ref1` int(12) DEFAULT '0', 
					`ref2` int(12) DEFAULT '0', 
					PRIMARY KEY (`id`), 
					KEY `order_id` (`order_id`) 
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;",

		'partner_stat' => "(
					`id` int(14) NOT NULL AUTO_INCREMENT,
					`time` datetime NOT NULL,
					`from_name` varchar(255) NOT NULL,
					`referer` varchar(255) NOT NULL,
					`page` varchar(255) NOT NULL,
					`page_views` int(11) NOT NULL,
					PRIMARY KEY (`id`),
					KEY `from_name` (`from_name`),
					KEY `time` (`time`),
					KEY `referer` (`referer`),
					KEY `page` (`page`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;",

		'partner_tags' => "(
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `name` varchar(255) NOT NULL,
					  `description` mediumtext NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `name` (`name`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;",
		
		'products' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `name` varchar(255) NOT NULL DEFAULT '',
					  `name_short` varchar(255) NOT NULL DEFAULT '',
					  `meta_title` text NOT NULL,
					  `meta_description` text NOT NULL,
					  `meta_keywords` text NOT NULL,
					  `memo` mediumtext NOT NULL,
					  `memo_short` mediumtext NOT NULL,
					  `price_buy` double(10,2) NOT NULL DEFAULT '0.00',
					  `price` double(10,2) NOT NULL DEFAULT '0.00',
					  `price_opt` double(10,2) NOT NULL DEFAULT '0.00',
					  `price_spec` double(10,2) NOT NULL DEFAULT '0.00',
					  `price_period` set('razovo','day','week','month','year') NOT NULL DEFAULT 'razovo',
					  `comment` varchar(255) NOT NULL DEFAULT '',
					  `active` int(1) NOT NULL DEFAULT '0',
					  `accept_orders` int(1) NOT NULL DEFAULT '0',
					  `total_qty` tinyint(4) NOT NULL DEFAULT '0',
					  `weight_deliver` double(10,2) NOT NULL DEFAULT '0.00',
					  `complectation` mediumtext NOT NULL,
					  `barcode` varchar(255) NOT NULL DEFAULT '0',
					  `id_next_model` int(11) NOT NULL DEFAULT '0',
					  `bid_ya` int(11) NOT NULL DEFAULT '0',
					  `alter_search` mediumtext NOT NULL,
					  `f_new` tinyint(1) NOT NULL DEFAULT '0',
					  `f_spec` tinyint(1) NOT NULL DEFAULT '0',
					  `present_id` int(11) NOT NULL DEFAULT '0',
					  `currency` set('rur','usd','euro') NOT NULL DEFAULT '',
					  `date_insert` datetime DEFAULT NULL,
					  `treba` int(11) NOT NULL DEFAULT '0',
					  `date_update` datetime DEFAULT NULL,
					  `user_id` int(11) DEFAULT '0',
					  `alias` varchar(255) DEFAULT NULL,
					  `views` bigint(20) NOT NULL,
					  `multitpl` varchar(100) NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `name` (`name`),
					  KEY `barcode` (`barcode`),
					  KEY `alias` (`alias`),
					  KEY `active` (`active`),
					  KEY `accept_orders` (`accept_orders`),
					  KEY `f_new` (`f_new`),
					  KEY `f_spec` (`f_spec`),
					  KEY `date_insert` (`date_insert`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;",
		
		'product_to_product' => "(
					  `id` int(12) NOT NULL AUTO_INCREMENT,
					  `product1` int(11) NOT NULL DEFAULT '0',
					  `product2` int(11) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`),
					  KEY `product1` (`product1`),
					  KEY `product2` (`product2`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;",
		
		'publications' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `name` varchar(255) NOT NULL DEFAULT '',
					  `anons` mediumtext,
					  `memo` longtext NOT NULL,
					  `ddate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `active` enum('0','1') NOT NULL DEFAULT '0',
					  `meta_title` varchar(255) NOT NULL DEFAULT '',
					  `meta_description` varchar(255) NOT NULL DEFAULT '',
					  `meta_keywords` mediumtext NOT NULL,
					  `alias` varchar(255) NOT NULL DEFAULT '',
					  `date_insert` datetime DEFAULT NULL,
					  `views` bigint(20) NOT NULL,
					  `user_id` int(11) DEFAULT '0',
					  `multitpl` varchar(250) NOT NULL,
					  `icon` varchar(100) NOT NULL,
					  `f_spec` tinyint(1) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`),
					  KEY `name` (`name`),
					  KEY `alias` (`alias`),
					  KEY `active` (`active`),
					  KEY `date_insert` (`date_insert`),
					  KEY `f_spec` (`f_spec`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;",
		
		'pub_categs' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `id_pub` int(11) NOT NULL DEFAULT '0',
					  `id_categ` int(11) NOT NULL DEFAULT '0',
					  `where_placed` varchar(50) NOT NULL DEFAULT 'pub',
					  PRIMARY KEY (`id`),
					  KEY `id_pub` (`id_pub`,`where_placed`),
					  KEY `id_categ` (`id_categ`,`where_placed`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=117 ;",
		
		'pub_to_product' => "(
					  `id` int(12) NOT NULL AUTO_INCREMENT,
					  `id_pub` int(11) NOT NULL,
					  `id_product` int(11) NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `id_pub` (`id_pub`),
					  KEY `id_product` (`id_product`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;",
					
					
					
					
		'ratings' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `record_type` varchar(50) NOT NULL DEFAULT '',
					  `record_id` int(11) NOT NULL DEFAULT '0',
					  `userid` int(11) NOT NULL DEFAULT '0',
					  `ddate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `ip_address` varchar(50) NOT NULL DEFAULT '',
					  `unreg_email` varchar(100) NOT NULL DEFAULT '',
					  `site_id` int(9) DEFAULT NULL,
					  `sess` varchar(250) NOT NULL,
					  `minus` tinyint(1) DEFAULT '0',
					  UNIQUE KEY `id` (`id`),
					  KEY `record_type` (`record_type`,`record_id`),
					  KEY `active` (`site_id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;",
					
		
		'site_categ' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `id_site` int(11) NOT NULL DEFAULT '0',
					  `id_categ` int(11) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`),
					  KEY `id_site` (`id_site`),
					  KEY `id_categ` (`id_categ`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=218 ;",
		
		'site_info' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `name_full` varchar(255) NOT NULL DEFAULT '',
					  `name_short` varchar(100) NOT NULL DEFAULT '',
					  `site_url` varchar(255) NOT NULL DEFAULT '',
					  `email_info` varchar(255) NOT NULL DEFAULT '',
					  `phone` varchar(255) DEFAULT NULL,
					  `meta_keywords` mediumtext NOT NULL,
					  `meta_description` mediumtext,
					  `meta_title` varchar(255) NOT NULL DEFAULT '',
					  `office_ip` varchar(255) DEFAULT NULL,
					  `template_path` varchar(100) DEFAULT NULL,
					  `site_active` tinyint(1) DEFAULT '0',
					  `site_charset` varchar(50) DEFAULT NULL,
					  `lang` varchar(50) DEFAULT NULL,
					  `site_time_zone` varchar(50) DEFAULT NULL,
					  `site_date_format` varchar(50) DEFAULT NULL,
					  `site_time_format` varchar(50) DEFAULT NULL,
					  `site_description` varchar(255) DEFAULT NULL,
					  `gallery` tinyint(1) DEFAULT '0',
					  `mode_basket` tinyint(1) DEFAULT '0',
					  `feedback_in_db` tinyint(1) DEFAULT '0',
					  `default_id_categ` int(9) DEFAULT '0',
					  PRIMARY KEY (`id`),
					  KEY `site_url` (`site_url`),
					  KEY `site_active` (`site_active`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;",
		
		'site_publications' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `id_site` int(11) NOT NULL DEFAULT '0',
					  `id_publications` int(11) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`),
					  KEY `id_site` (`id_site`),
					  KEY `id_publications` (`id_publications`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=109 ;",
		
		'site_vars' => "(
					  `id` bigint(20) NOT NULL AUTO_INCREMENT,
					  `site_id` bigint(20) NOT NULL DEFAULT '0',
					  `name` tinytext,
					  `value` text,
					  `description` mediumtext,
					  `type` enum('list','checkbox','text','img') NOT NULL DEFAULT 'text',
					  `autoload` tinyint(1) NOT NULL DEFAULT '1',
					  `if_enum` text,
					  `width` int(9) NOT NULL DEFAULT '0',
					  `height` int(9) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`),
					  KEY `site_id` (`site_id`),
					  KEY `autoload` (`autoload`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=199 ;",
		
		'subs_sent' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `status_sent` tinyint(1) NOT NULL DEFAULT '0',
					  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
					  `subscription_id` int(11) DEFAULT NULL,
					  `user_id` int(11) DEFAULT NULL,
					  `date_update` datetime DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  KEY `subscription_id` (`subscription_id`),
					  KEY `user_id` (`user_id`),
					  KEY `status_sent` (`status_sent`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=301 ;",
		
		'subs_subjects' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `site_id` tinyint(4) NOT NULL,
					  `code` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
					  `name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
					  `description` text CHARACTER SET utf8,
					  `sort` int(11) NOT NULL DEFAULT '100',
					  `active` tinyint(1) NOT NULL DEFAULT '1',
					  `auto` tinyint(1) NOT NULL DEFAULT '0',
					  `days_of_month` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
					  `days_of_week` varchar(15) CHARACTER SET utf8 DEFAULT NULL,
					  `times_of_day` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
					  `template` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
					  `last_exec` datetime DEFAULT NULL,
					  `visible` tinyint(1) NOT NULL DEFAULT '1',
					  `from_field` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
					  `subject_field` varchar(255) CHARACTER SET utf8 NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `site_id` (`site_id`),
					  KEY `sort` (`sort`),
					  KEY `active` (`active`),
					  KEY `visible` (`visible`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;",
		
		'subs_subscription' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `subs_subject_id` int(11) NOT NULL,
					  `timestamp_x` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
					  `status` tinyint(1) NOT NULL DEFAULT '0',
					  `version` tinyint(1) DEFAULT NULL,
					  `date_sent` datetime DEFAULT NULL,
					  `from_field` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
					  `to_field` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
					  `bcc_field` mediumtext COLLATE utf8_unicode_ci,
					  `email_filter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
					  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
					  `body_text` text COLLATE utf8_unicode_ci NOT NULL,
					  `body_html` mediumtext COLLATE utf8_unicode_ci NOT NULL,
					  `direct_send` tinyint(1) NOT NULL DEFAULT '0',
					  `charset` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
					  `error_email` mediumtext COLLATE utf8_unicode_ci,
					  `auto_send_time` datetime DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  KEY `subs_subject_id` (`subs_subject_id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;",
		
		'subs_users' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `subs_subject_id` int(12) NOT NULL,
					  `date_insert` datetime NOT NULL,
					  `date_update` datetime DEFAULT NULL,
					  `user_id` int(11) DEFAULT NULL,
					  `active` tinyint(1) NOT NULL DEFAULT '1',
					  `format` varchar(4) CHARACTER SET utf8 NOT NULL DEFAULT 'text',
					  `confirm_code` varchar(8) CHARACTER SET utf8 DEFAULT NULL,
					  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
					  `date_confirm` datetime NOT NULL,
					  PRIMARY KEY (`id`),
					  KEY `subs_subject_id` (`subs_subject_id`),
					  KEY `user_id` (`user_id`),
					  KEY `active` (`active`),
					  KEY `confirmed` (`confirmed`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;",
		
		'undo' => "(
					  `id` int(12) NOT NULL AUTO_INCREMENT,
					  `sess` varchar(255) CHARACTER SET utf8 NOT NULL,
					  `module_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
					  `undo_type` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
					  `undo_handler` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
					  `content` mediumtext CHARACTER SET utf8,
					  `user_id` int(11) DEFAULT NULL,
					  `date_inserted` datetime DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  KEY `user_id` (`user_id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;",
		
		'uploaded_files' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `id_exists` int(11) NOT NULL DEFAULT '0',
					  `record_id` int(11) NOT NULL DEFAULT '0',
					  `record_type` varchar(50) NOT NULL DEFAULT '',
					  `size` int(11) NOT NULL DEFAULT '0',
					  `filename` varchar(255) NOT NULL DEFAULT '',
					  `title` varchar(255) NOT NULL DEFAULT '',
					  `ext` varchar(255) NOT NULL DEFAULT '',
					  `allow_download` tinyint(1) NOT NULL DEFAULT '0',
					  `direct_link` tinyint(1) NOT NULL DEFAULT '0',
					  `id_in_record` tinyint(4) NOT NULL DEFAULT '0',
					  `time_added` int(12) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`),
					  KEY `record_id` (`record_id`,`record_type`),
					  KEY `id_in_record` (`id_in_record`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;",
		
		'uploaded_pics' => "(
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `id_exists` int(11) NOT NULL DEFAULT '0',
					  `record_id` int(11) NOT NULL DEFAULT '0',
					  `record_type` varchar(50) NOT NULL DEFAULT '',
					  `width` int(11) NOT NULL DEFAULT '0',
					  `height` int(11) NOT NULL DEFAULT '0',
					  `title` varchar(255) NOT NULL DEFAULT '',
					  `ext` varchar(255) NOT NULL DEFAULT '',
					  `id_in_record` int(11) NOT NULL,
					  `is_default` tinyint(1) NOT NULL,
					  `ext_h1` text,
					  `ext_desc` text,
					  `ext_link` text,
					  PRIMARY KEY (`id`),
					  KEY `record_id` (`record_id`,`record_type`),
					  KEY `id_in_record` (`id_in_record`),
					  KEY `is_default` (`is_default`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=374 ;",
		
		
		'users' => "(
						`id` int(11) NOT NULL AUTO_INCREMENT, 
						`name` varchar(255) DEFAULT NULL, 
						`login` varchar(255) DEFAULT NULL, 
						`passwd` varchar(255) DEFAULT NULL, 
						`email` varchar(255) DEFAULT '', 
						`phone_mobil` varchar(255) DEFAULT NULL, 
						`icq` varchar(255) DEFAULT NULL, 
						`memo` varchar(255) DEFAULT NULL, 
						`country` varchar(255) DEFAULT NULL, 
						`city` varchar(255) DEFAULT NULL, 
						`birth_day` date DEFAULT '0000-00-00', 
						`user_interes` varchar(255) DEFAULT NULL, 
						`user_about` varchar(255) DEFAULT NULL, 
						`url` varchar(255) DEFAULT NULL, 
						`pers_hi` varchar(255) DEFAULT NULL, 
						`news` enum('0','1') DEFAULT '1', 
						`date_insert` datetime DEFAULT '0000-00-00 00:00:00', 
						`last_login` datetime DEFAULT '0000-00-00 00:00:00', 
						`last_ip` varchar(255) DEFAULT NULL, 
						`admin` enum('0','1') DEFAULT '0', 
						`user_title` int(4) DEFAULT '1', 
						`active` tinyint(1) NOT NULL DEFAULT '0', 
						`gender` varchar(2) NOT NULL DEFAULT '-', 
						`site_id` int(9) DEFAULT '0', 
						`ref1` int(12) DEFAULT '0', 
						`ref2` int(12) DEFAULT '0', 
						PRIMARY KEY (`id`), 
						KEY `login` (`login`), 
						KEY `passwd` (`passwd`), 
						KEY `email` (`email`), 
						KEY `news` (`news`), 
						KEY `last_login` (`last_login`), 
						KEY `active` (`active`), 
						KEY `admin` (`admin`)
						) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=200 ;",
		
		'users_prava' => "(
						`bo_userid` int(5) NOT NULL DEFAULT '0',
						`orders` tinyint(1) DEFAULT '0',
						`products` tinyint(1) DEFAULT '0',
						`orders_stat` tinyint(1) DEFAULT '0',
						`settings` tinyint(1) DEFAULT '0',
						`info` tinyint(1) DEFAULT '0',
						`banner` tinyint(1) DEFAULT '0',
						`vote` tinyint(1) DEFAULT '0',
						`db` tinyint(1) DEFAULT '0',
						`comments` tinyint(1) DEFAULT '0',
						`feedback` tinyint(1) DEFAULT '0',
						`search` tinyint(1) DEFAULT '0',
						`mass` tinyint(1) DEFAULT '0',
						`fav` tinyint(1) DEFAULT '1',
						`delivery` tinyint(1) DEFAULT '0',
						`emails` tinyint(1) DEFAULT '0',
						`org` tinyint(1) DEFAULT '0',
						`coupons` tinyint(1) DEFAULT '0',
						`stat` tinyint(1) DEFAULT '0',
						PRIMARY KEY (`bo_userid`),
						UNIQUE KEY `bo_userid` (`bo_userid`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;",
		
		'visit_log' => " (
						`id` int(12) NOT NULL AUTO_INCREMENT,
						`time` datetime NOT NULL,
						`ip` varchar(16) NOT NULL,
						`sess` varchar(250) NOT NULL,
						`referer` varchar(255) NOT NULL,
						`referer_query` text NOT NULL,
						`page` varchar(255) NOT NULL COMMENT 'Входная страница',
						`partner_key` varchar(255) NOT NULL,
						`pages_visited` text NOT NULL,
						`qty_pages_visited` tinyint(5) NOT NULL,
						`site_id` tinyint(4) NOT NULL DEFAULT '0',
						PRIMARY KEY (`id`),
						KEY `time` (`time`),
						KEY `sess` (`sess`),
						KEY `ip` (`ip`),
						KEY `referer` (`referer`),
						KEY `site_id` (`site_id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8  AUTO_INCREMENT=1000 ;",
	  );
	  
		if(MODE == 'install' && !empty($tbl) 
				&& !empty($key) 
				
		){
			$db->last_error = '';			
			if(empty($db_queries[$key])){ return 'wrong SQL for '.$tbl; }
			$sql = "CREATE TABLE IF NOT EXISTS `".$db->escape($tbl)."` ".$db_queries[$key];
			$db->query($sql); 
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}
			return;
		}
	  
	  if(isset($_GET['type']) 
			&& isset($_GET['name']) 
			&& isset($db_queries[$_GET['type']]) 
			&& !empty($db_queries[$_GET['type']])
		){
		  $db->last_error = '';
		  $sql = "CREATE TABLE IF NOT EXISTS `".$db->escape($_GET['name'])."` ".$db_queries[$_GET['type']];
		  $db->query($sql); 
		  
		  if(!empty($db->last_error)){
			return db_error(
				'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
				<br>Function: '.__FUNCTION__
			); 			
		  }
		  
		  header("Location: ?action=db");
		  exit;
		  
	  }else{
		  header('Location: ?action=db');
		  exit;
	  }	  
	  
  }

  /* ok */
  function get_db_info(){
    global $tpl, $db;
    $sql = "SELECT
                table_name AS table_name,
                engine,
                data_length AS total_size,
                table_rows
            FROM 
                information_schema.tables
            WHERE
                table_schema=DATABASE();";
    $rows = $db->get_results($sql, ARRAY_A);	
	$db_not_exists = $db->tables;
    $to_summ = 0;
    if($rows && $db->num_rows > 0){
        foreach($rows as $k => $row){
            $to_summ += $row['total_size']; 
			$db_exists[] = $row['table_name']; 
			$key = array_search($row['table_name'], $db_not_exists);
			$rows[$k]['clear_table'] = 
				$key == 'users' || $key == 'users_prava' || $key == 'site_info' || $key == 'site_vars'
				? 0 : 1;
			
			if($key == TRUE){
				unset($db_not_exists[$key]);
			}
        }
    }
	
	$files = array();
	$folder_to_save = str_replace(array('\\', '/'), '', $tpl->compile_dir); 
	$dump_dir = PATH.'/'.ADMIN_FOLDER.'/'.$folder_to_save.'/backup';
	$dump_www_dir = '/'.ADMIN_FOLDER.'/'.$folder_to_save.'/backup/';
	$skip_files = array('.', '..', 'index.php', 'index.html');
	
	if(!empty($_GET['delete'])){
		$f_delete = trim($_GET['delete']);
		if(!empty($f_delete) && file_exists($dump_dir.'/'.$f_delete)){
			@unlink($dump_dir.'/'.$f_delete);
		}
	}
	
	$dh  = opendir($dump_dir);
	while (false !== ($filename = readdir($dh))) {
		if(!in_array($filename, $skip_files)){
			$files[] = $filename;
		}		
	}
	
	sort($files);
	
	$tpl->assign('dump_folder', $dump_www_dir);
	$tpl->assign('dump_files', $files);
    $tpl->assign('db_not_exists', $db_not_exists);
    $tpl->assign('summ', $to_summ);
    $tpl->assign('rows', $rows);
	
	/* last clearcache */
	$sql = "SELECT c.*, u.`name` as who_changed_name, 
			u.login as who_changed_login 
		FROM ".$db->tables['changes']." c 
		LEFT JOIN ".$db->tables['users']." u ON (c.who_changed = u.id)
		WHERE c.where_changed = 'clear_cache' 
		AND c.type_changes = 'mysql' 
		ORDER BY c.date_insert DESC 
		LIMIT 0, 1
	";
	$cc = $db->get_row($sql, ARRAY_A);
	$tpl->assign('last_clear_cache', $cc);
    return $tpl->display("db/index.html");
  }

  /* ok */
  function clear_db_counter(){
    global $tpl, $db, $site_vars, $admin_vars;
    $sql = "DELETE FROM ".$db->tables['counter']." WHERE 
		`time` < (NOW() - INTERVAL 30 DAY)
		AND for_page IN ('pub','product','categ')
	";
    $db->query($sql);
    header("Location: ?action=db&updated=1");
    exit;
  }
  
	function get_dump(){
		global $db, $tpl;
		$tables = $db->tables;
		$folder_to_save = str_replace(array('\\', '/'), '', $tpl->compile_dir); 
		$dump_dir = PATH.'/'.ADMIN_FOLDER.'/'.$folder_to_save.'/backup/';
		$dump_file = date('Ymd_Hi')."_db_dump.sql";
		
		/* Проверим папку, чтобы она была и были права на запись в нее */
		if(!is_dir($dump_dir)){
			if(!mkdir($dump_dir, 0777, true)){
				return error_not_found("The folder <b>".$dump_dir."</b> can't be created!");    
			}
		}

		/* Если файл уже есть, то удалим */
		if (file_exists($dump_dir.$dump_file)) {
			if(!@unlink($dump_dir.$dump_file)){
				return error_not_found("The file <b>".$dump_file."</b> exists and can't be modified!");    
			}
		}

	  
		if(is_array($db->tables)) {
			$fp = fopen($dump_dir.$dump_file,"w");
			if(!$fp){
				return error_not_found("The file <b>".$dump_file."</b> can't be created! The folder <b>backup</b> need to open for writing."); 
			}
			
			$text = "
-- SQL Dump
-- Simpla! version: ".date('Y-m-d H:i')." 
--
-- База дынных: `".DATABASE."`
--
-- ---------------------------------------------------
-- ---------------------------------------------------
					";
			fwrite($fp,$text);
			
			foreach($tables as $item) {
				$text = "
--
-- Структура таблицы - ".$item."
--
					";
					
					fwrite($fp,$text);
					
					$text = "";
					
					$text .= "
DROP TABLE IF EXISTS `".$item."`; ";
					$sql = "SHOW CREATE TABLE `".$item."` ";				
					$result = $db->get_row($sql, ARRAY_N);
					if($result){
						
						$text .= "\n".$result[1].";";
						fwrite($fp,$text);
						$text = "";
						$text .= "
						
--         
-- Dump BD - tables :".$item."
--

						";
						
						
						$sql2 = "SELECT * FROM `".$item."` ";
						$result2 = $db->get_results($sql2, ARRAY_A);
						if($db->num_rows > 0){
							
							$text .= "\nINSERT INTO `".$item."` VALUES";
							fwrite($fp,$text);
							
							foreach($result2 as $k=> $row){
								
								$text = "";
								if($k == 0) $text .= "(";
								else  $text .= ",(";
								
								
								foreach($row as $v){
									//$text .= "\"".$db->escape($v)."\",";
									$text .= "\"".$db->real_escape($v)."\",";
								}
								
								$text = rtrim($text,",");  
								$text .= ")";
								
								fwrite($fp,$text);
								$text = "";
								
							}
					
							$text .= ";\n";
							fwrite($fp,$text);							
							
						}
						
					}
										
			}
			
			fclose($fp);
		}

 
		header("Location: ?action=db&added=dump");
		exit;
	}
	
	
	
	function optimize_tables(){
		global $db;
		$sql = "SELECT
                table_name AS table_name,
                engine,
                data_length AS total_size,
                table_rows
            FROM 
                information_schema.tables
            WHERE
                table_schema=DATABASE();";
		$rows = $db->get_results($sql, ARRAY_A);
		if(!empty($db->last_error)){
			return db_error(
				'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
				<br>Function: '.__FUNCTION__
			); 			
		}
		if($rows && $db->num_rows > 0){
			foreach($rows as $row){
				$sql2 = empty($sql2) 
					? "OPTIMIZE TABLE `".$row['table_name']."`" 
					: $sql2.", `".$row['table_name']."`";
				
				
			}
		}
			
		if(!empty($sql2)){
			$db->query($sql2);
		}	
		
		header("Location: ?action=db&added=optimize");
		exit;
	
	
	}
  
  
	function update_tables(){
		global $db;		
		$db->last_error = '';
		$sql = "SELECT table_name, table_type, engine 
			FROM information_schema.tables 
			WHERE table_schema = '".DATABASE."'	
			ORDER BY table_name 
		";	
		$tables = $db->get_results($sql, ARRAY_A);
		if(!empty($db->last_error)){
			return db_error(
				'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
				<br>Function: '.__FUNCTION__
			); 			
		}
		$ar = array();
		foreach($tables as $k=>$v){
			$ar[$v['table_name']] = $v['table_name'];
		}
		$tables = $ar;
		
		#### 1 - blocks 
		#### where -> where_placed 
		#### сделать остальное по типу п.1 - проверим есть ли поле и если да, 
		#### то запускаем апдейт
		
		if(isset($tables[$db->tables['blocks']])){
			$sql = "SHOW columns FROM `".$db->tables['blocks']."` 
				WHERE `Field` = 'where'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			
			if(!empty($fields)){
				$sql = "ALTER TABLE  ".$db->tables['blocks']." CHANGE  `where`  `where_placed` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'manual' COMMENT  'header,left,right,footer,main,manual,form'";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}				
			}
			
			
			$sql = "SHOW INDEX FROM `".$db->tables['blocks']."` WHERE Key_name = 'id' AND Column_name = 'id' ";
			if(!$db->query($sql)){ 
				$sql = "ALTER TABLE `".$db->tables['blocks']."` ADD UNIQUE(`id`)";
			}
			
			$sql = "SHOW INDEX FROM `".$db->tables['blocks']."` WHERE Key_name = 'active' AND Column_name = 'active' ";
			if(!$db->query($sql)){ 
				$sql = "ALTER TABLE `".$db->tables['blocks']."` ADD INDEX(`active`)";
			}

			$sql = "SHOW INDEX FROM `".$db->tables['blocks']."` WHERE Key_name = 'where_placed' AND Column_name = 'where_placed' ";
			if(!$db->query($sql)){ 
				$sql = "ALTER TABLE `".$db->tables['blocks']."` ADD INDEX(`where_placed`)";
			}

			$sql = "SHOW INDEX FROM `".$db->tables['blocks']."` WHERE Key_name = 'type' AND Column_name = 'type' ";
			if(!$db->query($sql)){ 
				$sql = "ALTER TABLE `".$db->tables['blocks']."` ADD INDEX(`type`)";
			}

			$sql = "SHOW INDEX FROM `".$db->tables['blocks']."` WHERE Key_name = 'type_id' AND Column_name = 'type_id' ";
			if(!$db->query($sql)){ 
				$sql = "ALTER TABLE `".$db->tables['blocks']."` ADD INDEX(`type_id`);";
			}			
			
		}
	
		#### 2 - categ_options 
		#### where -> where_placed 
		if(isset($tables[$db->tables['categ_options']])){
			$sql = "SHOW columns FROM `".$db->tables['categ_options']."` 
				WHERE `Field` = 'where'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}
			
			if(!empty($fields)){
				$sql = "ALTER TABLE  ".$db->tables['categ_options']." CHANGE  `where`  `where_placed` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'catalog'";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}
		}
		
		#### 3 - comments 
		#### Добавить: ext_h1, ext_desc, notify 
		if(isset($tables[$db->tables['comments']])){
			$sql = "SHOW columns FROM `".$db->tables['comments']."` 
				WHERE `Field` = 'ext_h1'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}
			
			if(empty($fields)){
				$sql = "ALTER TABLE  `".$db->tables['comments']."` ADD  `ext_h1` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}

			$sql = "SHOW columns FROM `".$db->tables['comments']."` 
				WHERE `Field` = 'ext_desc'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(empty($fields)){
				$sql = "ALTER TABLE  `".$db->tables['comments']."` ADD  `ext_desc` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}			

			$sql = "SHOW columns FROM `".$db->tables['comments']."` 
				WHERE `Field` = 'notify'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(empty($fields)){
				$sql = "ALTER TABLE `".$db->tables['comments']."` ADD  `notify` TINYINT( 1 ) NULL DEFAULT  '0';";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}			
			
		}
		
		#### 4 - connections стала connected 
		#### тут ничего не делаем, название из настроек берется
		
		#### 5 - counter 
		#### for -> for_page 
		#### Добавить: id_page 
		if(isset($tables[$db->tables['counter']])){
			$sql = "SHOW columns FROM `".$db->tables['counter']."` 
				WHERE `Field` = 'for'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(!empty($fields)){
				$sql = "ALTER TABLE  `".$db->tables['counter']."` CHANGE  `for`  `for_page` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}
			
			$sql = "SHOW columns FROM `".$db->tables['counter']."` 
				WHERE `Field` = 'id_page'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(empty($fields)){
				$sql = "ALTER TABLE  `".$db->tables['counter']."` ADD  `id_page` INT( 14 ) NOT NULL AFTER  `for_page` ;";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}	


			$sql = "SHOW INDEX FROM `".$db->tables['counter']."` WHERE Non_unique = '0' AND Column_name = 'id' ";
			if(!$db->query($sql)){ 
				$sql = "ALTER TABLE `".$db->tables['counter']."` ADD UNIQUE(`id`)";
			}
			
			$sql = "ALTER TABLE `".$db->tables['counter']."` CHANGE `id` `id` BIGINT(20) NOT NULL AUTO_INCREMENT";
			$db->query($sql);
			
		}
		
		#### 6 - favorites 
		#### where -> where_placed 
		if(isset($tables[$db->tables['fav']])){
			$sql = "SHOW columns FROM `".$db->tables['fav']."` 
				WHERE `Field` = 'where'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(!empty($fields)){
				$sql = "ALTER TABLE  `".$db->tables['fav']."` CHANGE  `where`  `where_placed` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'categ' COMMENT  'Типа записи - categ, pub, product, site, var, order, fb, block';";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}
		}
		
		#### 7 - options 
		#### group -> group_id 
		if(isset($tables[$db->tables['options']])){
			$sql = "SHOW columns FROM `".$db->tables['options']."` 
				WHERE `Field` = 'group'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(!empty($fields)){
				$sql = "ALTER TABLE  `".$db->tables['options']."` CHANGE  `group`  `group_id` INT( 11 ) NOT NULL DEFAULT  '0';";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}			
			
			$sql = "SHOW columns FROM `".$db->tables['options']."` 
				WHERE `Field` = 'type'";
			$fields = $db->get_row($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if($fields['Type'] != 'varchar(20)'){
				$sql = " ALTER TABLE  `".$db->tables['options']."` CHANGE  `type`  `type` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'val' COMMENT 'int,val,checkbox,select,multicheckbox,connected,categ'; ";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}			

			$sql = "SHOW columns FROM `".$db->tables['options']."` 
				WHERE `Field` = 'filter_type'";
			$fields = $db->get_row($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if($fields['Type'] != 'varchar(20)'){
				$sql = " ALTER TABLE  `".$db->tables['options']."` CHANGE  `filter_type`  `filter_type` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ; ";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}			
			
		}
		
		
		#### 8 - option_groups 
		#### where -> where_placed 
		#### Добавить: to_show
		if(isset($tables[$db->tables['option_groups']])){
			$sql = "SHOW columns FROM `".$db->tables['option_groups']."` 
				WHERE `Field` = 'where'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(!empty($fields)){
				$sql = "ALTER TABLE `".$db->tables['option_groups']."` CHANGE  `where`  `where_placed` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'all';";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}
			
			$sql = "SHOW columns FROM `".$db->tables['option_groups']."` 
				WHERE `Field` = 'to_show'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(empty($fields)){
				$sql = "ALTER TABLE  `".$db->tables['option_groups']."` ADD  `to_show` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'all' AFTER  `id` ;";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}			
		}
		
		#### 9 - option_values 
		#### where -> where_placed
		if(isset($tables[$db->tables['option_values']])){
			$sql = "SHOW columns FROM `".$db->tables['option_values']."` 
				WHERE `Field` = 'where'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(!empty($fields)){
				$sql = "ALTER TABLE `".$db->tables['option_values']."` CHANGE  `where`  `where_placed` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'product';";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}
			
			
			$sql = "ALTER TABLE  `".$db->tables['option_values']."` 
				CHANGE  `value`  `value` VARCHAR( 512 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;";
			$db->query($sql);
			
			
			$sql = "SHOW INDEX FROM `".$db->tables['option_values']."` 
				WHERE Key_name = 'value' AND Column_name = 'value'
			";
			if(!$db->query($sql)){
				$sql = "ALTER TABLE `".$db->tables['option_values']."` ADD INDEX (  `value` ) ;";
				$db->query($sql);		
			}
			
			
		}	
		
		### 9a - order_status ADD field `alias`
		if(isset($tables[$db->tables['orders']])){
			$sql = "ALTER TABLE `".$db->tables['orders']."` CHANGE  `status`  `status` SMALLINT( 6 ) NULL DEFAULT  '0'; ";
			$db->query($sql);
			if(!empty($db->last_error)){
				return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__);
			}			
		} 
		
		### 9b - order_status ADD field `alias`
		if(isset($tables[$db->tables['order_status']])){
			$sql = "SHOW columns FROM `".$db->tables['order_status']."` 
				WHERE `Field` = 'alias'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(empty($fields)){
				$sql = "ALTER TABLE `".$db->tables['order_status']."` ADD  `alias` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}
		} 
		
		#### 10 - partner_orders 
		#### from -> from_name 
		if(isset($tables[$db->tables['partner_orders']])){
			$sql = "SHOW columns FROM `".$db->tables['partner_orders']."` 
				WHERE `Field` = 'from'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(!empty($fields)){
				$sql = "ALTER TABLE `".$db->tables['partner_orders']."` CHANGE  `from`  `from_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}
		}		

		#### ref1
		if(isset($tables[$db->tables['partner_orders']])){
			$sql = "SHOW columns FROM `".$db->tables['partner_orders']."` 
				WHERE `Field` = 'ref1'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(empty($fields)){
				$sql = " ALTER TABLE `".$db->tables['partner_orders']."` ADD  `ref1` INT( 12 ) NULL DEFAULT  '0' ;";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}
		}		
		#### ref2 
		if(isset($tables[$db->tables['partner_orders']])){
			$sql = "SHOW columns FROM `".$db->tables['partner_orders']."` 
				WHERE `Field` = 'ref2'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(empty($fields)){
				$sql = " ALTER TABLE `".$db->tables['partner_orders']."` ADD  `ref2` INT( 12 ) NULL DEFAULT  '0' ;";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}
		}		

		
		#### 11 - partner_stat 
		#### Добавить: id 
		#### from -> from_name
		if(isset($tables[$db->tables['partner_stat']])){
			$sql = "SHOW columns FROM `".$db->tables['partner_stat']."` 
				WHERE `Field` = 'from'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(!empty($fields)){
				$sql = "ALTER TABLE `".$db->tables['partner_stat']."` CHANGE  `from`  `from_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}
			
			$sql = "SHOW columns FROM `".$db->tables['partner_stat']."` 
				WHERE `Field` = 'id'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(empty($fields)){
				$sql = "ALTER TABLE `".$db->tables['partner_stat']."` ADD  `id` INT( 14 ) NOT NULL AUTO_INCREMENT FIRST , ADD PRIMARY KEY (  `id` ) ;";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}
		}		
		
		#### 12 - products 
		#### ALTER TABLE `products` CHANGE `price_period` `price_period` SET( 'razovo', 'day', 'week', 'month', 'year', 'from' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'razovo' 
		if(isset($tables[$db->tables['products']])){
			$sql = "ALTER TABLE `".$db->tables['products']."` CHANGE `price_period` `price_period` SET( 'razovo', 'day', 'week', 'month', 'year', 'from' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'razovo'";
			$db->query($sql);			
			if(!empty($db->last_error)){
				return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
			}
		}
		
		#### 12a - publications
		#### ddate -> NULL		
		if(isset($tables[$db->tables['publications']])){
			$sql = "ALTER TABLE `".$db->tables['publications']."` CHANGE  `ddate`  `ddate` DATETIME NULL;";
			$db->query($sql);
			if(!empty($db->last_error)){
				return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__);
			}		
		}
		
		#### 13 - pub_categs 
		#### where -> where_placed 
		if(isset($tables[$db->tables['pub_categs']])){
			$sql = "SHOW columns FROM `".$db->tables['pub_categs']."` 
				WHERE `Field` = 'where'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(!empty($fields)){
				$sql = "ALTER TABLE `".$db->tables['pub_categs']."` CHANGE  `where`  `where_placed` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'pub';";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}
		}
	
		#### 14 - uploaded_pics 
		#### Добавить: ext_h1, ext_desc, ext_link 
		if(isset($tables[$db->tables['uploaded_pics']])){
			$sql = "SHOW columns FROM `".$db->tables['uploaded_pics']."` 
				WHERE `Field` = 'ext_h1'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(empty($fields)){
				$sql = "ALTER TABLE  `".$db->tables['uploaded_pics']."` ADD  `ext_h1` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}

			$sql = "SHOW columns FROM `".$db->tables['uploaded_pics']."` 
				WHERE `Field` = 'ext_desc'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(empty($fields)){
				$sql = "ALTER TABLE  `".$db->tables['uploaded_pics']."` ADD  `ext_desc` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}

			$sql = "SHOW columns FROM `".$db->tables['uploaded_pics']."` 
				WHERE `Field` = 'ext_link'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(empty($fields)){
				$sql = "ALTER TABLE  `".$db->tables['uploaded_pics']."` ADD  `ext_link` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}
		}


		
		#### 15 - users 
		#### Добавить: gender 
		if(isset($tables[$db->tables['users']])){
			$sql = "SHOW columns FROM `".$db->tables['users']."` 
				WHERE `Field` = 'gender'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(empty($fields)){
				$sql = "ALTER TABLE  `".$db->tables['users']."` ADD  `gender` VARCHAR( 2 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '-';";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}
		}
		
		#### Добавить: site_id 
		if(isset($tables[$db->tables['users']])){
			$sql = "SHOW columns FROM `".$db->tables['users']."` 
				WHERE `Field` = 'site_id'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(empty($fields)){
				$sql = " ALTER TABLE `".$db->tables['users']."` ADD  `site_id` INT( 9 ) NULL DEFAULT  '0' ;";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}
		}
		
		#### Добавить: ref1 
		if(isset($tables[$db->tables['users']])){
			$sql = "SHOW columns FROM `".$db->tables['users']."` 
				WHERE `Field` = 'ref1'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(empty($fields)){
				$sql = " ALTER TABLE `".$db->tables['users']."` ADD  `ref1` INT( 12 ) NULL DEFAULT  '0' ; ";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}
		}
		
		#### Добавить: ref2 
		if(isset($tables[$db->tables['users']])){
			$sql = "SHOW columns FROM `".$db->tables['users']."` 
				WHERE `Field` = 'ref2'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(empty($fields)){
				$sql = " ALTER TABLE `".$db->tables['users']."` ADD  `ref2` INT( 12 ) NULL DEFAULT  '0' ;";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
				}
			}
		}
		
		#### 16 - users_prava 
		#### Тип поля изменить с enum('0', '1') на tinyint(1) 
		#### orders, products, orders_stat, settings, info, banner, vote 
		if(isset($tables[$db->tables['users_prava']])){
			$sql = "ALTER TABLE  `".$db->tables['users_prava']."` CHANGE  `orders`  `orders` TINYINT( 1 ) NULL DEFAULT  '0';";
			$db->query($sql);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(!empty($db->last_error)){
				return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
			}

			$sql = "ALTER TABLE  `".$db->tables['users_prava']."` CHANGE  `products`  `products` TINYINT( 1 ) NULL DEFAULT  '0';";
			$db->query($sql);
			if(!empty($db->last_error)){
				return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
			}

			$sql = "ALTER TABLE  `".$db->tables['users_prava']."` CHANGE  `orders_stat`  `orders_stat` TINYINT( 1 ) NULL DEFAULT  '0';";
			$db->query($sql);
			if(!empty($db->last_error)){
				return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
			}

			$sql = "ALTER TABLE  `".$db->tables['users_prava']."` CHANGE  `settings`  `settings` TINYINT( 1 ) NULL DEFAULT  '0';";
			$db->query($sql);
			if(!empty($db->last_error)){
				return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
			}

			$sql = "ALTER TABLE  `".$db->tables['users_prava']."` CHANGE  `info`  `info` TINYINT( 1 ) NULL DEFAULT  '0';";
			$db->query($sql);
			if(!empty($db->last_error)){
				return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
			}

			$sql = "ALTER TABLE  `".$db->tables['users_prava']."` CHANGE  `banner`  `banner` TINYINT( 1 ) NULL DEFAULT  '0';";
			$db->query($sql);
			if(!empty($db->last_error)){
				return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
			}
			
			$sql = "ALTER TABLE  `".$db->tables['users_prava']."` CHANGE  `vote`  `vote` TINYINT( 1 ) NULL DEFAULT  '0';";
			$db->query($sql);
			if(!empty($db->last_error)){
				return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__); 			
			}
			
			
			$sql = "SHOW columns FROM `".$db->tables['users_prava']."` 
				WHERE `Field` = 'emails'";
			$fields = $db->query($sql, ARRAY_A);
			if(!empty($db->last_error)){
				return db_error(
					'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
					<br>Function: '.__FUNCTION__
				); 			
			}

			if(empty($fields)){
				$sql = "ALTER TABLE  `".$db->tables['users_prava']."` ADD  `emails` TINYINT( 1 ) NOT NULL DEFAULT  '0';";
				$db->query($sql);
				if(!empty($db->last_error)){
					return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__);
				}
			}
			
			$ar_fields = array('org', 'coupons', 'stat', 'delivery');
			foreach($ar_fields as $f){
					
				$sql = "SHOW columns FROM `".$db->tables['users_prava']."` 
						WHERE `Field` = '".$f."'";
				$fields = $db->query($sql, ARRAY_A);
				if(!empty($db->last_error)){
					return db_error(
						'File: '.basename(__FILE__).'<br>Row: '.__LINE__.'
						<br>Function: '.__FUNCTION__
					);
				}
				
				if(empty($fields)){
					$sql = "ALTER TABLE  `".$db->tables['users_prava']."` ADD  `".$f."` TINYINT( 1 ) NOT NULL DEFAULT  '0';"; 
					$db->query($sql);
					if(!empty($db->last_error)){
						return db_error('File: '.basename(__FILE__).'<br>Row: '.__LINE__);
					}
				}				
			}
			
			
			$sql = "SHOW INDEX FROM `".$db->tables['users_prava']."` 
				WHERE Key_name = 'PRIMARY' AND Column_name = 'bo_userid'
			";
			if(!$db->query($sql)){
				$sql = "ALTER TABLE `".$db->tables['users_prava']."` ADD PRIMARY KEY(`bo_userid`)";
				$db->query($sql);		
			}
			
			$sql = "ALTER TABLE `".$db->tables['users_prava']."` CHANGE `db` `db` TINYINT(1) NULL DEFAULT '0'";
			$db->query($sql);

			$sql = "ALTER TABLE `".$db->tables['users_prava']."` CHANGE `comments` `comments` TINYINT(1) NULL DEFAULT '0'";
			$db->query($sql);
			
			$sql = "ALTER TABLE `".$db->tables['users_prava']."` CHANGE `feedback` `feedback` TINYINT(1) NULL DEFAULT '0'";
			$db->query($sql);
			
			$sql = "ALTER TABLE `".$db->tables['users_prava']."` CHANGE `search` `search` TINYINT(1) NULL DEFAULT '0'";
			$db->query($sql);
			
			$sql = "ALTER TABLE `".$db->tables['users_prava']."` CHANGE `mass` `mass` TINYINT(1) NULL DEFAULT '0'";
			$db->query($sql);
			
			$sql = "ALTER TABLE `".$db->tables['users_prava']."` CHANGE `fav` `fav` TINYINT(1) NULL DEFAULT '1'";
			$db->query($sql);
			
			$sql = "ALTER TABLE `".$db->tables['users_prava']."` CHANGE `delivery` `delivery` TINYINT(1) NULL DEFAULT '0'";
			$db->query($sql);
			
			$sql = "ALTER TABLE `".$db->tables['users_prava']."` CHANGE `emails` `emails` TINYINT(1) NULL DEFAULT '0'";
			$db->query($sql);
			
			$sql = "ALTER TABLE `".$db->tables['users_prava']."` CHANGE `org` `org` TINYINT(1) NULL DEFAULT '0'";
			$db->query($sql);
			
			$sql = "ALTER TABLE `".$db->tables['users_prava']."` CHANGE `coupons` `coupons` TINYINT(1) NULL DEFAULT '0'";
			$db->query($sql);
			
			$sql = "ALTER TABLE `".$db->tables['users_prava']."` CHANGE `stat` `stat` TINYINT(1) NULL DEFAULT '0'";
			$db->query($sql);	
			
		}
			
		
		#### 17 - visit_log 
		if(isset($tables[$db->tables['visit_log']])){
			
			$sql = "SHOW INDEX 
				FROM `".$db->tables['visit_log']."` 
				WHERE Column_name = 'sess' ";
			if(!$db->query($sql)){ 
				$sql = "ALTER TABLE `".$db->tables['visit_log']."` ADD INDEX(`sess`)";
			}
			
			$sql = "SHOW INDEX FROM `".$db->tables['visit_log']."` WHERE Column_name = 'ip' ";
			if(!$db->query($sql)){ 
				$sql = "ALTER TABLE `".$db->tables['visit_log']."` ADD INDEX(`ip`)";
			}

			$sql = "SHOW INDEX FROM `".$db->tables['visit_log']."` WHERE Column_name = 'referer' ";
			if(!$db->query($sql)){ 
				$sql = "ALTER TABLE `".$db->tables['visit_log']."` ADD INDEX(`referer`)";
			}
			
			$sql = "SHOW INDEX FROM `".$db->tables['visit_log']."` WHERE Column_name = 'site_id' ";
			if(!$db->query($sql)){ 
				$sql = "ALTER TABLE `".$db->tables['visit_log']."` ADD INDEX(`site_id`)";
			}
					
		}
	
		
		header("Location: ?action=db&added=updated");
		exit;
	}
	
	function add_email_events(){
		global $db;
		
		$ddate = date('Y-m-d H:i:s');
		$sqls = array(
			1 => "INSERT INTO `email_event_type` 
			(`id`, `event`, `active`, `title`, `content`) 
			VALUES
			(1, 'subscriber_invitation', 1, 'Подписка на новости', 'Сообщение со ссылкой для активации подписки'),
			(2, 'order_new', 1, 'Новый заказ', ''),
			(3, 'order_paid', 1, 'Заказ оплачен', ''),
			(4, 'order_done', 1, 'Заказ выполнен', ''),
			(5, 'order_remind', 1, 'Напоминание о заказе', ''),
			(6, 'order_comment', 1, 'Комментарий к заказу', ''),
			(7, 'fb_new', 1, 'Новый запрос', 'Отправка данных контактной формы'),
			(8, 'fb_comment', 1, 'Комментарий к запросу', ''),
			(9, 'subscriber_new', 1, 'Новый подписчик', ''),
			(10, 'subscriber_cancel', 1, 'Отказ от подписки', ''),
			(11, 'page_comment', 1, 'Комментарий на странице', ''),
			(12, 'user_new', 1, 'Новый пользователь', ''),
			(13, 'user_invitation', 1, 'Ссылка для активации аккаунта', ''),
			(14, 'user_password_changed', 1, 'Изменен пароль', 'Письмо пользователю, что его пароль изменен'),
			(15, 'user_lost_password', 1, 'Сброс пароля', 'Письмо со ссылкой для сброса пароля'),
			(16, 'order_new_manager', 1, 'Новый исполнитель', 'Письмо назначенному исполнителю');",
			
		2 => "INSERT INTO `email_event` 
			(`id`, `event_type_id`, `site`, `title`, `content`, `to_user`, `to_admin`, `to_extra`, `subject`, `body`, `template`, `is_html`, `active`, `date_added`, `date_updated`) VALUES 
			(1, 1, 0, 'Ссылка активации подписки', '', 1, 0, '', 'Подписка на новости', 'Для активации вашей подписки на новости сайта { \$site.name_short } нажмите на ссылку  { \$ar.url.news.add }', 0, 1, 1, '".$ddate."', '".$ddate."'),
			(2, 2, 0, 'Админу о новом  заказе', '', 0, 1, '', 'Заказ { \$ar.order.id_formatted }', '".$db->escape('<h3>Новый заказ { $ar.order.id_formatted }</h3>
			<p>Информация о заказчике:</p>
			<ul><li>Имя - { $ar.customer.name }</li>
			<li>Телефон - { $ar.order.phone }</li>
			<li>Эл.почта - { $ar.customer.email }</li>
			</ul>
			<p>Ссылка на страницу заказа:<br><a href="{ $ar.order.url }">{ $ar.order.url }</a></p>
			<h3>В заказе</h3>
			<table cellpadding="3" cellspacing="0" border="1">
			<tr><th>Наименование</th>
			<th align="center">Цена</th>
			<th align="center">Кол-во</th>
			<th align="center">Сумма</th>
			</tr>
			{ foreach from=$ar.cart value="v" }
			<tr><td><a href="{ $v.title_link }">{ $v.title }</a></td>
			<td align="center">{ $v.price }</td>
			<td align="center">{ $v.qty }</td>
			<td align="center">{ $v.summ }</td>
			</tr>
			{/foreach}
			<tr><td colspan="3" align="right">Доставка</td>
			<td align="center">{ $ar.total_summ.delivery }</td>
			</tr>
			<tr><th colspan="3" align="right">Итого</th>
			<th align="center">{ $ar.total_summ.total }</th>
			</tr></table>
			{if !empty($ar.order.memo)}<p>{$ar.order.memo}</p>{/if}
			<p><small>Данное письмо является автоматическим, отправлено роботом и не требует ответа.</small></p>')."', 0, 1, 1, '".$ddate."', '".$ddate."'),
			(3, 3, 0, 'Статус заказа Оплачен', '', 1, 0, '', 'Заказ { \$ar.order.id_formatted } оплачен', '".$db->escape('<h3>Заказ { $ar.order.id_formatted } оплачен!</h3>
			<p>Ссылка на страницу заказа:<br><a href="{ $ar.order.url }">{ $ar.order.url }</a></p>
			<p><small>Данное письмо является автоматическим, отправлено роботом и не требует ответа.</small></p>')."', 0, 1, 1, '".$ddate."', '".$ddate."'),
			(4, 4, 0, 'Письмо админу о скачанном файле', '', 0, 1, '', 'Заказ { \$ar.order.id_formatted } выполнен', '<p>Заказ { \$ar.order.id_formatted } выполнен.</p><p>Покупатель:</p><ul><li>{ \$ar.customer.name }</li><li>{ \$ar.customer.email }</li></ul>', 0, 1, 1, '".$ddate."', '".$ddate."'),
			(5, 9, 0, 'Новый подписчик', '', 0, 1, '', 'Подписка на рассылку', 'На новости сайта { \$site.name_short } оформлена подписка для адресa { \$ar.customer.email }', 0, 1, 1, '".$ddate."', '".$ddate."'),
			(6, 10, 0, 'Отказ от подписки', '', 1, 0, '', 'Подписка на новости', 'Вы подписаны на новости сайта { \$site.name_short }. Для отмены подписки нажмите на ссылку { \$ar.url.news.delete }', 0, 1, 1, '".$ddate."', '".$ddate."'),
			(7, 6, 0, 'Коммент к заказу', '', 1, 0, '', 'Комментарий по Вашему заказу', '<p>В заказе { \$ar.order.id_formatted } появился комментарий:</p><p><b>{ \$ar.message }</b></p><hr><p>Ссылка на страницу заказа: { \$ar.order.url }</p>', 0, 1, 1, '".$ddate."', '".$ddate."'),
			(8, 8, 0, 'Коммент в запросе', '', 1, 0, '', 'Комментарий на Ваш запрос', '<p>В запросе { \$ar.id_formatted } появился комментарий:</p><p><b>{ \$ar.message }</b></p><hr><p>Ссылка на страницу запроса: { \$ar.url }</p>', 0, 1, 1, '".$ddate."', '".$ddate."'),
			(9, 7, 0, 'Новый запрос', '', 0, 1, '', 'Новый запрос / { \$site.name_short }', '<p>На сайте { \$site.name_short } / { \$site.site_url } появился новый запрос</p><p><b>{ \$ar.message }</b></p>', 0, 1, 1, '".$ddate."', '".$ddate."'),
			(10, 5, 0, 'Напоминание про заказ', '', 1, 0, '', 'Ваш заказ { \$ar.order.id_formatted }', '<h3>Здравствуйте { \$ar.customer.name }</h3><p>Некоторое время назад Вы оформили заказ <b>{ \$ar.order.id_formatted }</b>. Заказ находится в ожидании оплаты.</p><p>Оплатить заказ можно на странице { \$ar.order.url }</p><p>Ваш { \$site.name_full }<br>{ \$site.site_url }</p><p><small>Сообщение отправлено роботом и не требует ответа. Если Вы получили письмо по ошибке, то просто удалите его. После аннуляции заказа Вы перестанете получать такие сообщения.</small></p>', 0, 1, 1, '".$ddate."', '".$ddate."'),
			(11, 11, 0, 'Комментарий на странице', '', 0, 1, '', 'Новый комментарий на странице', '".$db->escape('<p>На странице <a href="{ $ar.url }">{ $ar.title }</a> появился новый комментарий:</p><p><b>{ $ar.message }</b></p>')."', 0, 1, 1, '".$ddate."', '".$ddate."'),
			(12, 13, 0, 'Ссылка для активации регистрации', '', 1, 0, '', 'Вы почти зарегистрированы', '<h3>Вы успешно зарегистрировались на сайте { \$site.name_short }!</h3><p>Осталось лишь нажать на ссылку { \$ar.url.activate }</p>', 0, 1, 1, '".$ddate."', '".$ddate."'),
			(13, 12, 0, 'Новый пользователь', '', 0, 1, '', 'Новый пользователь', '<p>На сайте { \$site.name_short } зарегистрирован новый пользователь </p><ul><li>{ \$ar.login }</li><li>{ \$ar.customer.email }</li></ul>', 0, 1, 1, '".$ddate."', '".$ddate."'),
			(14, 15, 0, 'Ссылка для создания нового пароля', '', 1, 0, '', 'Сброс пароля', '".$db->escape('<p>На сайте { $site.name_short } запрошен сброс пароля.  Пароль можно изменить перейдя по ссылке ниже в течение двух дней.</p><ul><li>Нажмите <a href="{ $ar.url.new_password }">на ссылку для сброса пароля</a> и следуйте  инструкциям.</li></ul><p><small>Если письмо пришло по ошибке, то просто удалите его. Ваш пароль останется прежним.</small></p>')."', 0, 1, 1, '".$ddate."', '".$ddate."'),
			(15, 14, 0, 'Пароль изменен', '', 1, 0, '', 'Пароль изменен', '".$db->escape('<p>Ваш пароль на сайте { \$site.name_short } изменен!</p>{if !empty(\$ar.message)}<p>{ \$ar.message }</p>{/if}')."', 0, 1, 1, '".$ddate."', '".$ddate."'),
			(16, 16, 0, 'Вы назначены менеджером заказа', '', 0, 0, '', 'Вы назначены исполнителем заказа { \$ar.order.id_formatted }', '".$db->escape('<p>{ $ar.manager.name } ({ $ar.manager.login }), Вы назначены исполнителем заказа { $ar.order.id_formatted }.</p>
			<ul><li><a href="{ $ar.order.url }">Страница заказа</a></li>
			<li><a href="{ $ar.order.adminurl }">Администрирование</a></li>
			</ul>
			<hr>
			<p><a href="{ $ar.site_url }">{ $ar.site }</a></p>')."', 0, 1, 1, '".$ddate."', '".$ddate."');"
		);
		
		$rows1 = $db->get_var("SELECT COUNT(*) 
			FROM ".$db->tables['email_event_type']."
		");
		
		$rows2 = $db->get_var("SELECT COUNT(*) 
			FROM ".$db->tables['email_event']."
		");
		
		if($rows1 == 0 && $rows2 == 0){
			foreach($sqls as $sql){
				if(!$db->query($sql)){
					return $db->debug();	
				}
			}
		}else{
			return '<p class="red">Already added!</p>';
		}
		
		$url = "?action=db&updated=1";
		header("Location: ".$url);
		exit;
		
	}
	
	function view_db_table($table){
		global $db, $tpl;
		if(!empty($table['name'])){
			$sql = " DESCRIBE ".$table['name']." ";
			$table['info'] = $db->get_results($sql, ARRAY_A);
			
			$sql = "SELECT COUNT(*) FROM ".$table['name']." ";
			$table['qty'] = $db->get_var($sql);
			
			if(!empty($table['records'])){
				$onpage = ONPAGE;
				$page = empty($_GET['page']) ? 0 : intval($_GET['page']);
				$start = $page*$onpage;
				$stop = $start+$onpage;

				
				if(!empty($_POST['del']) && is_array($_POST['del'])){
					//$id = intval($_POST['id']);
					foreach($_POST['del'] as $k => $v){
						
						$sql = "DELETE FROM ".$table['name']." 
							WHERE ".$db->escape($k)." = '".$db->escape($v)."' 
						";
						$db->query($sql);
						$href = "?action=db&do=view_db&table=".$table['name']."&records=1";
						if(!empty($page)){
							$href .= "&page=".$page;
						}
						header("Location: ".$href);
						exit;
					}					
				}
				
				$sql = "SELECT * FROM ".$table['name']." 
					LIMIT ".$start.", ".$stop."
				";
				$table['rows'] = $db->get_results($sql, ARRAY_A);
				$tpl->assign("pages", _pages($table['qty'], $page, $onpage));
			}
			
		}
		
		
		$tpl->assign('table', $table);
		return $tpl->display("db/table.html");
		
		
	}

	function delete_stat(){
		global $db;
		$sql = "DELETE FROM ".$db->tables['option_values']." 
			WHERE `where_placed` = 'visit' 
		";
		$db->query($sql);
		header("Location: ?action=db&clearstat=1");
		exit;
	}
?>