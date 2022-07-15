<?php
	if (!class_exists('Site')) { return; }

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
    *
    ***********************/ 
       
    $s = new Rss;

    class Rss extends Site {

        function __construct()
        {
        }
                
        static public function get_rss($site)
        {
            $tpl_page = 'blank.html';
            $ar = GetEmptyPageArray();
            $ar['page'] = $tpl_page;
            $ar['title'] = 'RSS';
            $ar['metatitle'] = 'RSS';
			$ar['content'] = $site->GetMessage('sitemap', 'empty');
			
            $site->uri['page'] = $tpl_page;
            $site->page = $ar;  
			
			$last_pubs = list_pubs(0, $site, 'last', 100);
			//$site->print_r($last_pubs,1);
			
			
			if(!empty($last_pubs['list'])){
				
				if(!headers_sent()){
					header("Content-type: text/xml; charset=".$site->vars['site_charset']);
				}
				
				

	echo '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	
	>
<channel>
<title>'.htmlspecialchars($site->vars['name_short']).' RSS</title> 
<atom:link href="'.htmlspecialchars($site->uri['idn']).'/feed/" rel="self" type="application/rss+xml" />
<link>'.htmlspecialchars($site->uri['idn']).'</link>
<description>'.htmlspecialchars($site->vars['site_description']).'</description>
<lastBuildDate>'.date('r', time()).'</lastBuildDate>
<language>'.$site->lang['current_language'].'</language>
<generator>Simpla.es 1.0</generator>
<sy:updatePeriod>hourly</sy:updatePeriod>
<sy:updateFrequency>1</sy:updateFrequency>
';


if(!empty($site->vars['img_logo_small']['img'][0]['url'])){
	echo '
<image>
	<url>'.htmlspecialchars($site->vars['img_logo_small']['img'][0]['url']).'</url>
	<title>'.htmlspecialchars($site->vars['name_short']).'</title>
	<link>'.htmlspecialchars($site->uri['idn']).'</link>
	<width>'.$site->vars['img_logo_small']['img'][0]['width'].'</width>
	<height>'.$site->vars['img_logo_small']['img'][0]['height'].'</height>
</image> 	
	';
}
	
				
				foreach($last_pubs['list'] as $v){
					$c = !empty($v['intro']) ? $v['intro'] : mb_substr($v['content'],0,500,'utf-8');

					if(!empty($v['pic'][2]['url'])){
						$img = $v['pic'][2]['url'];
					}elseif(!empty($v['pic'][1]['url'])){
						$img = $v['pic'][1]['url'];
					}
					
					if(!empty($img)){
						$c = '<img src="'.$img.'" style="max-width:300px; margin: 0 10px 10px 0; float:left;" />'.$c;
					}

					
				echo '
	<item>
		<title>'.htmlspecialchars($v['title']).'</title>
		<link>'.htmlspecialchars($v['link_idn']).'</link>
		<guid>'.htmlspecialchars($v['link_idn']).'</guid>
		<description>'.htmlspecialchars($c).'</description>
		<pubDate>'.date('r', strtotime($v['date_insert'])).'</pubDate>';
		
	if(!empty($v['categ_title'])){
				echo '
		<category><![CDATA['.htmlspecialchars($v['categ_title']).']]></category>';		
	}
	echo '
	</item>
	';		
				}
				
	echo '
</channel>
</rss>	
';
exit;
			}
			
			
			
/*
***
2. Вставить между тегами <head> и </head> следующую строку:

<LINK href="ссылка на ваш фид " rel="alternate" type="application/rss+xml" title="описание вашего фида ">
***

*/			
			
			
			return $ar;
        }
        
		
		
		static public function get_comments($site)
        {
            $tpl_page = 'blank.html';
            $ar = GetEmptyPageArray();
            $ar['page'] = $tpl_page;
            $ar['title'] = 'RSS Comments';
            $ar['metatitle'] = 'RSS Comments';
			$ar['content'] = $site->GetMessage('sitemap', 'empty');
			
            $site->uri['page'] = $tpl_page;
            $site->page = $ar;  
			
			$list = list_comments(0, '', $site, 20, 1);
			
			$str = $site->GetMessage('words','comments');
			$str .= ' '.mb_strtolower($site->GetMessage('words','of_site'), 'UTF-8');
			$str .= ' '.$site->vars['name_short'].' RSS';
			//$site->print_r($list,1);
			
			if(!empty($list)){
				
				if(!headers_sent()){
					header("Content-type: text/xml; charset=".$site->vars['site_charset']);
				}				
				
echo '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	
	>
<channel>
<title>'.htmlspecialchars($str).'</title> 
<atom:link href="'.htmlspecialchars($site->uri['idn']).'/feed/comments/" rel="self" type="application/rss+xml" />
<link>'.htmlspecialchars($site->uri['idn']).'</link>
<description>'.htmlspecialchars($site->vars['site_description']).'</description>
<lastBuildDate>'.date('r', time()).'</lastBuildDate>
<language>'.$site->lang['current_language'].'</language>
<generator>Simpla.es 1.0</generator>
';


if(!empty($site->vars['img_logo_small']['img'][0]['url'])){
	echo '
<image>
	<url>'.htmlspecialchars($site->vars['img_logo_small']['img'][0]['url']).'</url>
	<title>'.htmlspecialchars($site->vars['name_short']).'</title>
	<link>'.htmlspecialchars($site->uri['idn']).'</link>
	<width>'.$site->vars['img_logo_small']['img'][0]['width'].'</width>
	<height>'.$site->vars['img_logo_small']['img'][0]['height'].'</height>
</image> 	
	';				
				
				foreach($list as $v){
					$c = $v['title'].': '.$v['message'];

					if(!empty($v['pic']['url'])){
						$img = $v['pic']['url'];
					}
					
					if(!empty($img)){
						$c = '<img src="'.$img.'" style="max-width:300px; margin: 0 10px 10px 0; float:left;" />'.$c;
					}

					
				echo '
	<item>
		<title>'.htmlspecialchars($v['page_title']).'</title>
		<link>'.htmlspecialchars($v['page_link']).'</link>
		<guid>'.htmlspecialchars($v['page_link']).'</guid>
		<description>'.htmlspecialchars($c).'</description>
		<pubDate>'.date('r', strtotime($v['date_insert'])).'</pubDate>
	</item>
	';		
				}

	echo '
</channel>
</rss>	
';
exit;				
				
				
			}







			
			
		



			
			}
			
			return $ar;
		}
		
		
    }

?>