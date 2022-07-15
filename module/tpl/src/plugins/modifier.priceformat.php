<?php
/**
 * SIMPLA! priceformat modifier plugin 
 * ver. 1.01 24.06.2019
 * Type:     modifier
 * Name:     priceformat
 * Purpose:  priceformat function to return price value
 * currency - rur/rub, usd, euro
 */

function tpl_modifier_priceformat($string, $currency='rub', 
	$fa = '', $decimals='0', $dec_point='.', $thousands_sep=' '
)
{
	if($currency != 'rub' && $currency != 'usd' && $currency != 'euro'){
		$currency = 'rub';
	}	
	
	// $fa - means to use fa icons to return
	$string = str_replace(',', '.', $string);
	settype($string, 'float');	
	$decimals = (int) $string == $string ? '0' : '2';
	$dec_point = ','; 
	$thousands_sep = ' ';
	$string = trim($string);
    $string = str_replace('&nbsp;','',$string);
    $string = str_replace(' ','',$string);	
	if(empty($string)){ return $string; }
	if(substr_count($string, '.') > 1){ return $string; }
	if (!is_numeric($string)) { return $string; }

	$string = number_format($string, $decimals, $dec_point, $thousands_sep);
	$pre = $after = '';
	
	if(empty($fa)){	
		$fa = $currency;
		if($currency == 'usd'){
			$pre = !empty($fa) ? '<i class="fa fa-usd"></i> ' : 'USD '; 
		}else{
			$after = !empty($fa) ? ' <i class="fa fa-'.$currency.'"></i>' : ' '.$currency;
		}
	}else{
		if($currency == 'usd'){
			$pre = !empty($fa) ? '<i class="'.$fa.'"></i> ' : 'USD '; 
		}else{
			$after = !empty($fa) ? ' <i class="'.$fa.'"></i>' : ' '.$currency;
		}
	}

	return $pre.$string.$after;    
}
?>