<?php
	if (!class_exists('Site')) { return; }

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
	* idn supports added 24.01.2017
    *
    ***********************/ 

    class Sitemap extends Site {

        function __construct()
        {
        }
                
        static public function get_results($site)
        {
			$tpl_page = 'pages/sitemap.html';
            $ar = GetEmptyPageArray();
            $site->uri['page'] = $tpl_page;
            $ar['page'] = $tpl_page;
            $ar['title'] = $site->GetMessage('sitemap','title');
            $ar['metatitle'] = $site->GetMessage('sitemap','metatitle');
            $ar['content'] = '';
                        
			$ar['list_categs'] = get_categs('categs', 0, 0, $site->vars['default_id_categ'], $site);
			
			if(empty($ar['list_categs'])){
				$ar['list_categs'] = array();
				return $ar; 
			}
			
			$ar['ids'] = array_keys($ar['list_categs']);
			$ar['id'] = 0;
			
			
			$ar['list_products'] = list_products($ar, 'product', $site, 'all', 400);
			$ar['list_pubs'] = list_pubs($ar, $site, 'all', 100);
			
			
			
			if(!empty($ar['list_products']['list'])){
				foreach($ar['list_products']['list'] as $k => $v){
					if(isset($ar['list_categs'][$v['id_categ']])){
						if(!isset($ar['list_categs'][$v['id_categ']]['list_products'])){
							$ar['list_categs'][$v['id_categ']]['list_products']['list'] = array();
						}
						$ar['list_categs'][$v['id_categ']]['list_products']['list'][] = $v;
					}
				}
			}
			
			if(!empty($ar['list_pubs']['list'])){
				foreach($ar['list_pubs']['list'] as $k => $v){
					if(isset($ar['list_categs'][$v['categ_id']])){
						if(!isset($ar['list_categs'][$v['categ_id']]['list_pubs'])){
							$ar['list_categs'][$v['categ_id']]['list_pubs']['list'] = array();
						}
						$ar['list_categs'][$v['categ_id']]['list_pubs']['list'][] = $v;
					}
				}
			}
			
			unset($ar['list_products']);
			unset($ar['list_pubs']);
			//$site->print_r($ar,1);
			/*
			foreach($ar['list_categs'] as $k => $v){
				if($v['products'] > 0){
					$ar['list_categs'][$k]['list_products'] = list_products($k, 'product', $site, 'all', 100);
				}

				if($v['pubs'] > 0){
					$array = array('id' => $k);
					$ar['list_categs'][$k]['list_pubs'] = list_pubs($array, $site, 'all', 100);
				}				
			}
			*/
            return $ar;           
        }
        
        static public function get_xml($site){
			
			if(!isset($site->vars['default_id_categ'])){ return; }
			$ar['list_categs'] = get_categs('categs', 0, 0, $site->vars['default_id_categ'], $site);
			$ar['list_products'] = list_products(0, 'product', $site, 'last', 700);
			$ar['list_pubs'] = list_pubs(0, $site, 'last', 700);

			if(!headers_sent()){
				header("Content-type: text/xml; charset=".$site->vars['site_charset']);
			}
            echo "<?xml version=\"1.0\" encoding=\"".$site->vars['site_charset']."\" ?>
			";

  	?><urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9"   url="http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

	<url>
		<loc><?php echo $site->uri['idn']; ?>/</loc>
		<lastmod><?php echo date("Y-m-d"); ?></lastmod>
		<changefreq>always</changefreq>
	</url>
  	<?php
        if(!empty($ar['list_categs'])){
    	   foreach($ar['list_categs'] as $row)
    	   {
  	?>
	<url>
		<loc><?php echo isset($row['link_idn']) ? $row['link_idn'] : $row['link'];?></loc>
		<lastmod><?php
					$ldate = $row['date_update'] != "0000-00-00 00:00:00"  ? $row['date_update'] : $row['date_insert'];
					$ldate = explode(" ", $ldate);
					$ldate = $ldate[0];
					echo $ldate; ?></lastmod>
		<changefreq>always</changefreq>
	</url>
	<?php
			}
		}
            

        if(!empty($ar['list_pubs']['list'])){
			foreach($ar['list_pubs']['list'] as $row){
?>
	<url>
		<loc><?php echo isset($row['link_idn']) ? $row['link_idn'] : $row['link']; ?></loc>
		<lastmod><?php
			$ldate = $row['date_update'] != "0000-00-00 00:00:00"  ? $row['date_update'] : $row['date_insert'];
			$ldate = explode(" ", $ldate);
			$ldate = $ldate[0];
			echo $ldate; ?></lastmod>
		<changefreq>always</changefreq>
	</url>
<?php		
			}
		}

	if(!empty($ar['list_products']['list'])){
		foreach($ar['list_products']['list'] as $row){
  		?>
  	<url>
		<loc><?php echo isset($row['link_idn']) ? $row['link_idn'] : $row['link']; ?></loc>
  		<lastmod><?php
			$ldate = $row['date_update'] != "0000-00-00 00:00:00"  ? $row['date_update'] : $row['date_insert'];
			$ldate = explode(" ", $ldate);
			$ldate = $ldate[0];
			echo $ldate; ?></lastmod>
  		<changefreq>always</changefreq>
  	</url>
  		<?php
		}
	}
  		
            
            
?>            
</urlset>
<?php
			exit;            
        }
        
    }

?>