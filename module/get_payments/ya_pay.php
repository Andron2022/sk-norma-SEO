<?php
/*
last update 21.11.2019
new https://kassa.yandex.ru/developers
old https://kassa.yandex.ru/tech/
*/

require_once 'vendor/autoload.php';
use YandexCheckout\Model\Notification\NotificationSucceeded;
use YandexCheckout\Model\Notification\NotificationWaitingForCapture;
use YandexCheckout\Model\NotificationEventType;

$source = file_get_contents('php://input');
$requestBody = json_decode($source, true);
if(empty($requestBody)){ return; }

$ranges = [
    '185.71.76.0/27',
    '185.71.77.0/27',
    '77.75.153.0/25',
    '77.75.154.128/25',
    '2a02:5180:0:1509::/64',
    '2a02:5180:0:2655::/64',
    '2a02:5180:0:1533::/64',
    '2a02:5180:0:2669::/64'
];

function ip_in_range( $ip, $range ) {
    if ( strpos( $range, '/' ) == false ) {
        $range .= '/32';
    }
    // $range is in IP/CIDR format eg 127.0.0.1/24
    list( $range, $netmask ) = explode( '/', $range, 2 );
    $range_decimal = ip2long( $range );
    $ip_decimal = ip2long( $ip );
    $wildcard_decimal = pow( 2, ( 32 - $netmask ) ) - 1;
    $netmask_decimal = ~ $wildcard_decimal;
    return ( ( $ip_decimal & $netmask_decimal ) == ( $range_decimal & $netmask_decimal ) );
}

/*$check = false;
foreach ($ranges as $range) {
    if(ip_in_range($_SERVER['REMOTE_ADDR'], $range)) {
        $check = true;
    }
}*/
    if(isset($requestBody['notification']) && $requestBody['notification'] != '') {
        //file_put_contents('./payments/'.date("YmD_His"), print_r($payment, true));
    }
    try {
        $notification = ($requestBody['event'] === NotificationEventType::PAYMENT_SUCCEEDED)
            ? new NotificationSucceeded($requestBody)
            : new NotificationWaitingForCapture($requestBody);
    } catch (Exception $e) {
        header("Status: 500 Server Error");
    }

    $payment = $notification->getObject();
    if($payment) {
        file_put_contents(dirname(__FILE__) . '/payments/'.date("YmD_His") . ".html", print_r($requestBody, true));
		
		/* add record */
		$order_id = isset($requestBody['object']['metadata']['CustomerNumber'])
			? $requestBody['object']['metadata']['CustomerNumber']
			: '';
			
		$cnt = strlen($site->id.'-');
		if(substr($order_id, 0, $cnt) == $site->id.'-'){
			$number = substr($order_id, $cnt);
			$order_id = str_replace('-','',$number);
		}else{
			return;
		}
		
		define("SHOW_FULL_ORDER", 1);
		require_once(MODULE."/get_order.php");
		$order = order_by_done($order_id, $site);
		if(empty($order)){ return; }
		
		if($requestBody['object']['paid'] == 1 
			&& 
			$requestBody['object']['metadata']['sum'] == $order['total_summ']['total']
		){
			$set_paid = 1;	
		}else{
			$set_paid = 0;
		}
		
		$order_info = array();
		$order_info['id'] = $order['id'];
		$order_info['method'] = 'yandex.kassa';
		$order_info['payment_info'] = $requestBody;
		$order_info['set_paid'] = $set_paid;
		$order_info['send_notification'] = 1;
		$order_info['comment'] = $site->lang['order']['payment']['title'].': '.$requestBody['object']['amount']['value'].' '.$requestBody['object']['amount']['currency'].'<br>'.$requestBody['object']['payment_method']['title'];
		set_order_paid($order_info, $site);

		
    }
