<?php
	if (!class_exists('Site')) { return; }

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
    *
    ***********************/ 

    class Tpltest extends Site {

        function __construct()
        {
        }
                
        static public function get_test($site)
        {
            $tpl_page = !empty($_GET['page']) ? $_GET['page'] : '';
            $ar = GetEmptyPageArray();
            $site->uri['page'] = $tpl_page;
            $ar['page'] = $tpl_page;
            $ar['title'] = 'TPL test';
            $ar['metatitle'] = 'TPL test';
            $ar['content'] = '';
			
			/*
			$order = array();
			$order['metatitle'] = 'TPL test';

			$order['title'] = 'Заказ';
			$order['txt_fio'] = 'ФИО';
			$order['from'] = 'От';
			$order['phone'] = 'Телефон';
			$order['from_email'] = 'Email';
			$order['url'] = 'url';
			$order['txt_details'] = 'details';
			$order['txt_name'] = 'name';
			$order['txt_qty'] = 'qty';
			$order['txt_price'] = 'price';
			$order['cart'][] = array(
				'title' => 'Наименование',
				'qty' => '2',
				'price_formatted' => '120 000'
			);
			$order['txt_total'] = 'Сто двадцать';
			$order['total_summ'] = '120 000';
			global $tpl;
			$tpl->assign('order', $order);
			*/
			
            return $ar;           
        }
        
    }

?>