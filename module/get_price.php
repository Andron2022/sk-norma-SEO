<?php
	if (!class_exists('Site')) { return; }

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
    * no picture - todo hide such offers!!!!!
	* last update 22.01.2020
    ***********************/ 

    class Price extends Site {

        function __construct()
        {
        }
                
        static public function get_page($site)
        {
            global $db;
            			
			if(!empty($_GET['gmc'])){
                $site->page['title'] = 'Google Merchant Center';

				if(empty($site->vars['sys_gmc'])){
					return $site->error_message('Google Merchant Center disabled.');
				}

				if(empty($site->vars['sys_gmc_key'])){
					return $site->error_message('Google Merchant Center key not found.');
				}
				
				if(!isset($_GET[$site->vars['sys_gmc_key']])){
					return $site->error_message('Google Merchant Center key not found.');
				}
				
				$site->page['list_categs'] = get_categs('categs', 0, 1, 0, $site);
				$site->page['list_products'] = list_products(0, 'categ', $site, 'all', 9999);
				
				if(empty($site->page['list_products'])){
					return $site->error_message('Google Merchant Center offers not found.');
				}
				
				return Print_GMC_Price($site);
			}

            if(empty($site->vars['sys_yml'])
				&& 
				empty($site->vars['sys_price'])){
                $site->page['title'] = 'Yandex Market';
                return $site->error_message('Yandex Market disabled.');
            }
			
            if(empty($site->vars['yml_key']) 
				&& 
				empty($site->vars['sys_price'])){
                $site->page['title'] = 'Yandex Market';
                return $site->error_message('Yandex Market key not found.');
            }
            
            if(!isset($_GET[$site->vars['yml_key']]) 
				&& 
				empty($site->vars['sys_price'])){
                $site->page['title'] = 'Yandex Market';
                return $site->error_message('The price-list not found');
            }
			
			if(!empty($site->vars['sys_price']) && 
				!isset($_GET[$site->vars['yml_key']])
			){
                $site->page['title'] = 'Price';
                return Print_Shop_Price_Table($site);
            }
			
			
            
            /* ok, lets show offers */            
            
            $site->page['list_categs'] = get_categs('categs', 0, 1, 0, $site);
            $site->page['list_products'] = list_products(0, 'categ', $site, 'all', 9999);
			
            if(count($site->page['list_categs']) < 1){
                $site->page['title'] = 'Yandex Market';
                return $site->error_message('There are no shop categories');
            }

            if($site->page['list_products']['all'] < 1){
                $site->page['title'] = 'Yandex Market';
                return $site->error_message('There are no offers');
            }
            
            return Print_Shop_Price_Yandex($site);
        }
        
    }
    
    function Print_Shop_Price_Table($site)
	{
		global $db;
		
		if(!empty($site->user['id'])){
			$site->page['content'] = 'ok';
		}
		
			
		return;
	}
	
	
	
    function Print_Shop_Price_Yandex($site)
    {
	
        global $db;
        $replace_from = array('"', '&', '>', '<', '\'', '&nbsp;');
        $replace_to = array('&quot;', '&amp;', '&gt;', '&lt;', '&apos;', ' ');
        
        $company_name = !empty($site->vars['company_name']) ? $site->vars['company_name'] : $site->vars['name_short'];
        $company_name = str_replace($replace_from, $replace_to, $company_name);
        $site->vars['name_short'] = str_replace($replace_from, $replace_to, $site->vars['name_short']);
 
header("Content-disposition: filename=yml.xml");
header("Content-Type: text/xml");
header("Pragma: no-cache");
header("Expires: 0");
print '<?xml version="1.0" encoding="'.$site->vars['site_charset'].'"?>';
?>

<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="<?php echo date('Y-m-d H:i'); ?>">
    <shop>
        <name><?=$site->vars['name_short']?></name>
        <company><?=$company_name?></company>
        <url><?=$site->vars['site_url']?>/</url>
        <platform><?=$site->ver['name']?></platform>
        <version><?=$site->ver['ver']?></version>
        <agency><?=SUPPORT_AGENCY_NAME?></agency>
        <email><?=SUPPORT_AGENCY_EMAIL?></email>
        <currencies>
            <currency id="RUR" rate="1"/>
            <currency id="USD" rate="CBRF" plus="3"/>
            <currency id="EUR" rate="CBRF" plus="3"/>
        </currencies>
        <categories>
<?php
        foreach($site->page['list_categs'] as $v){
            if($v['id_parent'] > 0){
                $title = str_replace($replace_from, $replace_to, $v['title']);
				if(empty($v['parent_title'])){
					$v['id_parent'] = 0;
				}
?>
                <category id="<?=$v['id']?>" parentId="<?=$v['id_parent']?>"><?=$title?></category>
<?php
            }else{
?>
                <category id="<?=$v['id']?>"><?=$v['title']?></category>
<?php
            } 
            
        }
?>
        </categories>
        <offers>
<?php        
		$bid = isset($site->vars['sys_yml_min_bid']) ? intval($site->vars['sys_yml_min_bid']) : 0;
		$only_in_stock = isset($site->vars['sys_yml_only_in_stock']) ? intval($site->vars['sys_yml_only_in_stock']) : 0;		

        foreach($site->page['list_products']['list'] as $v){
        
			// sys_yml_min_bid
			// sys_yml_only_in_stock
		
            $currency = $v['currency'];
            if($currency == 'euro'){
                $currency = 'EUR';
            }elseif($currency == 'usd'){
                $currency = 'USD';
            }elseif($currency == 'uah'){
                $currency = 'UAH';
            }elseif($currency == 'kzt'){
                $currency = 'KZT';
            }else{
                $currency = 'RUR';
            }
            
            $picture  = !empty($v['pic'][3]['url']) ? $v['pic'][3]['url'] : '';
            $title = str_replace($replace_from, $replace_to, $v['title']);
            $intro = strip_tags($v['intro']);
            if(strlen($intro > 175)){ $intro = substr($intro, 0, 170)."..."; }
            $intro = str_replace($replace_from, $replace_to, $intro);
            $barcode = str_replace($replace_from, $replace_to, $v['barcode']);        
            $available = $v['accept_orders'] == 1 ? 'true' : 'false';
			
			//$v['id_categ'] 
			$books_categs = array();
			if(!empty($site->vars['sys_yml_book_categs'])){
				$books_categs = explode(',', $site->vars['sys_yml_book_categs']);
			}
			
			if($v['bid_ya'] >= $bid && $v['accept_orders'] >= $only_in_stock){
				
				if(in_array($v['id_categ'], $books_categs)){
					/* offer for book */
					$opts = isset($site->page['list_products']['options'][$v['id']]['list']) ? 
						$site->page['list_products']['options'][$v['id']]['list'] : array();

					$o = array();
					$o['store'] = !empty($opts['store']['value']) ? 'true' : 'false';
					$o['pickup'] = !empty($opts['pickup']['value']) ? 'true' : 'false';
					$o['delivery'] = !empty($opts['delivery']['value']) ? intval($opts['delivery']['value']) : 1;
					$o['delivery'] = $o['delivery'] == 1 ? 'true' : 'false';
					
					$o['author'] = !empty($opts['author']['value']) ? trim($opts['author']['value']) : '';
					$o['author'] = str_replace($replace_from, $replace_to, $o['author']);
					
					$o['publisher'] = !empty($opts['publisher']['value']) ? trim($opts['publisher']['value']) : '';
					$o['publisher'] = str_replace($replace_from, $replace_to, $o['publisher']);
										
					$o['series'] = !empty($opts['series']['value']) ? trim($opts['series']['value']) : '';
					$o['series'] = str_replace($replace_from, $replace_to, $o['series']);
					
					$o['year'] = !empty($opts['year']['value']) ? intval($opts['year']['value']) : '';
					
					$o['ISBN'] = !empty($opts['isbn']['value']) ? trim($opts['isbn']['value']) : '';
					$o['ISBN'] = str_replace($replace_from, $replace_to, $o['ISBN']);
					
					$o['volume'] = !empty($opts['volume']['value']) ? trim($opts['volume']['value']) : '';
					$o['volume'] = str_replace($replace_from, $replace_to, $o['volume']);
					
					$o['part'] = !empty($opts['part']['value']) ? trim($opts['part']['value']) : '';
					$o['part'] = str_replace($replace_from, $replace_to, $o['part']);
					
					$o['language'] = !empty($opts['language']['value']) ? trim($opts['language']['value']) : '';
					$o['language'] = str_replace($replace_from, $replace_to, $o['language']);
					
					$o['binding'] = !empty($opts['binding']['value']) ? trim($opts['binding']['value']) : '';
					$o['binding'] = str_replace($replace_from, $replace_to, $o['binding']);
					
					$o['page_extent'] = !empty($opts['page_extent']['value']) ? trim($opts['page_extent']['value']) : '';
					$o['page_extent'] = str_replace($replace_from, $replace_to, $o['page_extent']);
					
					$o['downloadable'] = !empty($opts['downloadable']['value']) ? 'true' : 'false';
					
					$o['age'] = !empty($opts['age']['value']) ? intval($opts['age']['value']) : '0';
					
					
?>
			<offer id="<?=$v['id']?>" type="book" available="<?=$available?>" bid="<?=$v['bid_ya']?>">
				<url><?=$v['link']?></url>
				<price><?=$v['price']?></price>
<?php if($v['price_old'] > $v['price']){ ?>
				<oldprice><?=$v['price_old']?></oldprice>
<?php } ?>
				<currencyId><?=$currency?></currencyId>
				<categoryId><?=$v['id_categ']?></categoryId>
				<picture><?=$picture?></picture>
				<store><?=$o['store']?></store>
				<pickup><?=$o['pickup']?></pickup>
				<delivery><?=$o['delivery']?></delivery>
				<name><?=$title;?></name>
<?php if(!empty($o['author'])){ ?>
				<author><?=$o['author']?></author>
<?php } ?><?php if(!empty($o['publisher'])){ ?>
				<publisher><?=$o['publisher']?></publisher>
<?php } ?><?php if(!empty($o['series'])){ ?>
				<series><?=$o['series']?></series>
<?php } ?><?php if(!empty($o['year'])){ ?>
				<year><?=$o['year']?></year>
<?php } ?><?php if(!empty($o['ISBN'])){ ?>
				<ISBN><?=$o['ISBN']?></ISBN>
<?php } ?><?php if(!empty($o['volume'])){ ?>
				<volume><?=$o['volume']?></volume>
<?php } ?><?php if(!empty($o['part'])){ ?>
				<part><?=$o['part']?></part>
<?php } ?><?php if(!empty($o['language'])){ ?>
				<language><?=$o['language']?></language>
<?php } ?><?php if(!empty($o['binding'])){ ?>
				<binding><?=$o['binding']?></binding>
<?php } ?><?php if(!empty($o['page_extent'])){ ?>
				<page_extent><?=$o['page_extent']?></page_extent>
<?php } ?><?php if(!empty($intro)){ ?>
				<description><?=$intro;?></description>
<?php }	?>
				<downloadable><?=$o['downloadable']?></downloadable>	
				<age unit="year"><?=$o['age']?></age>	
			</offer>
<?php				
				}else{
					/* offer for other item */
        
?>
            <offer id="<?=$v['id']?>" available="<?=$available?>" bid="<?=$v['bid_ya']?>">
                <url><?=$v['link']?></url>
                <price><?=$v['price']?></price>
<?php if($v['price_old'] > 0){ ?>
                <oldprice><?=$v['price_old']?></oldprice>
<?php } ?>
                <currencyId><?=$currency?></currencyId>
                <categoryId><?=$v['id_categ']?></categoryId>
                <picture><?=$picture?></picture>
                <name><?=$title;?></name>
<?php if(!empty($intro)){ ?>
                <description><?=$intro;?></description>
<?php }

    if(!empty($site->page['list_products']['options'][$v['id']]['vendor'])){
        $vendor = $site->page['list_products']['options'][$v['id']]['vendor'];
        $vendor = str_replace($replace_from, $replace_to, $vendor); ?>
                <vendor><?=$vendor;?></vendor>
<?php }
/*
 <store>false</store>
  <pickup>false</pickup>
  <delivery>true</delivery>
  <sales_notes>Необходима предоплата.</sales_notes>
  <manufacturer_warranty>true</manufacturer_warranty>
  <country_of_origin>Китай</country_of_origin>
  <barcode>0123456789379</barcode>
  <cpa>1</cpa>
  <rec>789,120</rec>
  vendor
  model
*/

	if(isset($site->page['list_products']['options'][$v['id']]['list']['store']['value'])){
		$store = empty($site->page['list_products']['options'][$v['id']]['list']['store']['value']) 
			? 'false' : 'true'; ?>
		<store><?=$store?></store>
<?	}

	if(isset($site->page['list_products']['options'][$v['id']]['list']['manufacturer_warranty']['value'])){
		$manufacturer_warranty = empty($site->page['list_products']['options'][$v['id']]['list']['manufacturer_warranty']['value']) 
			? 'false' : 'true'; ?>
		<manufacturer_warranty><?=$manufacturer_warranty?></manufacturer_warranty>
<?	}

	if(isset($site->page['list_products']['options'][$v['id']]['list']['pickup']['value'])){
		$pickup = empty($site->page['list_products']['options'][$v['id']]['list']['pickup']['value']) 
			? 'false' : 'true'; ?>
		<pickup><?=$pickup?></pickup>
<?	}


	if(isset($site->page['list_products']['options'][$v['id']]['list']['delivery']['value'])){
		$delivery = empty($site->page['list_products']['options'][$v['id']]['list']['delivery']['value']) 
			? 'false' : 'true'; ?>
		<delivery><?=$delivery?></delivery>
<?	}


	if(!empty($site->page['list_products']['options'][$v['id']]['list']['sales_notes']['value'])){		
		$sales_notes = $site->page['list_products']['options'][$v['id']]['list']['sales_notes']; 
		$o_str = str_replace($replace_from, $replace_to, $sales_notes); ?>
		<sales_notes><?=$o_str?></sales_notes>
<?	}


	if(!empty($site->page['list_products']['options'][$v['id']]['list']['country_of_origin']['value'])){
		$country_of_origin = $site->page['list_products']['options'][$v['id']]['list']['country_of_origin']['value']; 
		$o_str = str_replace($replace_from, $replace_to, $country_of_origin); ?>
		<country_of_origin><?=$o_str?></country_of_origin>
<?	}


	if(!empty($site->page['list_products']['options'][$v['id']]['list']['barcode']['value'])){
		$barcode = $site->page['list_products']['options'][$v['id']]['list']['barcode']['value']; 
		$barcode = str_replace($replace_from, $replace_to, $barcode); ?>
		<barcode><?=$barcode?></barcode>
<?	}

	if(!empty($site->page['list_products']['options'][$v['id']]['list']['cpa']['value'])){
		$cpa = $site->page['list_products']['options'][$v['id']]['list']['cpa']['value']; 
		$o_str = str_replace($replace_from, $replace_to, $cpa); ?>
		<cpa><?=$o_str?></cpa>
<?	}

	if(!empty($site->page['list_products']['options'][$v['id']]['list']['rec']['value'])){
		$rec = $site->page['list_products']['options'][$v['id']]['list']['rec']['value']; 
		$o_str = str_replace($replace_from, $replace_to, $rec); ?>
		<rec><?=$o_str?></rec>
<?	}



	if(!empty($site->page['list_products']['options'][$v['id']]['list']['model']['value'])){ ?>
				<model><?=$site->page['list_products']['options']['list'][$v['id']]['model']['value']?></model>
<?	}elseif(!empty($v['barcode'])){ ?>
                <model><?=$v['barcode']?></model>
<?  }


	$opts_reserved = array('store', 'pickup', 'delivery', 
		'sales_notes', 'manufacturer_warranty', 
		'country_of_origin', 'barcode', 
		'cpa', 'rec', 'vendor', 'model'
	);


    if(!empty($site->page['list_products']['options'][$v['id']]['list']) && count($site->page['list_products']['options'][$v['id']]['list']) > 0){
        foreach($site->page['list_products']['options'][$v['id']]['list'] as $o){
			
			if(!in_array($o['alias'], $opts_reserved)){
			
            $title = str_replace($replace_from, $replace_to, $o['title']);
            $value = str_replace($replace_from, $replace_to, $o['value']);
            $after = str_replace($replace_from, $replace_to, $o['after']);
?>
                <param name="<?=$title?>"<?php if(!empty($after)){ echo ' unit="'.$after.'"'; } ?>><?=$value?></param>
<?php            
			}
        }
    }
?>
            </offer>
<?php
				}
			}
        }
?>
        </offers>
    </shop>
</yml_catalog>
<?php
        exit;
    }

	
	/* Price for Google Merchant Center */
	function Print_GMC_Price($site)
    {
        global $db;
        $replace_from = array('"', '&', '>', '<', '\'', '&nbsp;');
        $replace_to = array('&quot;', '&amp;', '&gt;', '&lt;', '&apos;', ' ');
        
        $company_name = !empty($site->vars['company_name']) ? $site->vars['company_name'] : $site->vars['name_short'];
        $company_name = str_replace($replace_from, $replace_to, $company_name);
        $site->vars['name_short'] = str_replace($replace_from, $replace_to, $site->vars['name_short']);

		$bid = isset($site->vars['sys_gmc_min_bid']) ? intval($site->vars['sys_gmc_min_bid']) : 0;
		$only_in_stock = isset($site->vars['sys_gmc_only_in_stock']) ? intval($site->vars['sys_gmc_only_in_stock']) : 0;	

		if(!empty($site->vars['sys_gmc_title'])){ $company_name = $site->vars['sys_gmc_title']; }
		if(!empty($site->vars['sys_gmc_description'])){ 
			$company_desc = $site->vars['sys_gmc_description']; 
		}else{
			$company_desc = $site->vars['site_description']; 
		}
echo '<?xml version="1.0"?>'; 
?>

<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
	<channel>
		<title><?=$company_name?></title>
		<link><?=$site->vars['site_url']?>/</link>
		<description><?=$company_desc?></description><?php		

		$gmc = array();
		if(!empty($site->vars['sys_gmc_categs'])){
			$ar_gmc = explode(',', $site->vars['sys_gmc_categs']);
			if(!empty($ar_gmc)){
				foreach($ar_gmc as $row_gmc){
					$gmc1 = explode('-', $row_gmc);
					if(!empty($gmc1[0]) && !empty($gmc1[1])){
						$gmc[$gmc1[0]] = $gmc1[1];
					}
				}
			}			
		}
		
		foreach($site->page['list_products']['list'] as $v){
            $intro = strip_tags($v['intro']);
			if(empty($intro)){ $intro = strip_tags($v['content']); }
            if(strlen($intro > 175)){ $intro = substr($intro, 0, 1000)."..."; }
            $intro = str_replace($replace_from, $replace_to, $intro);
			$img = '';
			if(!empty($v['pic']['mini']['url'])){ $img = $v['pic']['mini']['url']; }
			if(!empty($v['pic']['small']['url'])){ $img = $v['pic']['small']['url']; }
			
			$categ = isset($site->page['list_categs'][$v['id_categ']]['title']) 
				? $site->page['list_categs'][$v['id_categ']]['title']
				: '';
			$sub_categ = $site->page['list_categs'][$v['id_categ']]['id_parent'];
			if($sub_categ > 0 && isset($site->page['list_categs'][$sub_categ]['title'])){
				$categ = $site->page['list_categs'][$sub_categ]['title'].' / '.$categ;
			}

			$sub_categ = $site->page['list_categs'][$sub_categ]['id_parent'];
			if($sub_categ > 0 && isset($site->page['list_categs'][$sub_categ]['title'])){
				$categ = $site->page['list_categs'][$sub_categ]['title'].' / '.$categ;
			}
			
			$in_stock = $v['accept_orders'] == 1 ? 'in stock' : 'preorder';
			if($v['active'] == 0){ $in_stock = 'out of stock'; }
			
			$price = $v['price'];
			if($v['currency'] == 'rur') { $price .= ' RUB'; } 
			if($v['currency'] == 'usd') { $price .= ' USD'; } 
			if($v['currency'] == 'euro') { $price .= ' EUR'; }
			
			if(!empty($site->vars['sys_gmc_min_bid']) && $site->vars['sys_gmc_min_bid'] >= $v['bid_ya'] && $v['price'] > 0){
?>		
		<item>
			<title><?=$v['title']?></title>
			<link><?=$v['link']?></link>
			<description><?=$intro?></description>
			<g:image_link><?=$img?></g:image_link>
			<g:price><?=$price?></g:price>
			<g:availability><?=$in_stock?></g:availability>
			<g:condition>new</g:condition>
			<g:id><?=$v['id']?></g:id>
<?php if(isset($gmc[$v['id_categ']])){ ?>
			<g:google_product_category><?=$gmc[$v['id_categ']]?></g:google_product_category>
<?php } ?>
			<g:product_type><?=$categ?></g:product_type>
		</item>
<?php
			}
		}
?>
	</channel>
</rss>
<?php		
		exit;
	}
	
?>