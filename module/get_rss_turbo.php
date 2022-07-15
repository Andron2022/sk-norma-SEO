<?php
	if (!class_exists('Site')) { return; }

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
    *
    ***********************/ 
       
    $s = new RssTurbo;

    class RssTurbo extends Site {

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
/*	
echo '<pre>';	
print_r($site);
exit;			
*/
			if(!empty($last_pubs['list'])){
				
				if(!headers_sent()){
					header("Content-type: text/xml; charset=".$site->vars['site_charset']);
				}
				
	// Yandex Turbo	https://yandex.ru/dev/turbo/doc/rss/markup-docpage/	
	?>

	<rss xmlns:yandex="http://news.yandex.ru" xmlns:media="http://search.yahoo.com/mrss/" xmlns:turbo="http://turbo.yandex.ru" version="2.0">
    <channel>
        <title><?=$site->vars['name_full'];?></title>
        <link><?=$site->vars['site_url'];?></link>
        <description><?=$site->vars['site_description'];?></description>
		<?
		/*
        <turbo:analytics type="Yandex" id="123456"></turbo:analytics>
        <turbo:adNetwork type="Yandex" id="идентификатор блока" turbo-ad-id="first_ad_place"></turbo:adNetwork>
        <turbo:adNetwork type="AdFox" turbo-ad-id="second_ad_place">
            <![CDATA[
                <div id="идентификатор контейнера"></div>
                <script>
                    window.Ya.adfoxCode.create({
                        ownerId: 123456,
                        containerId: 'идентификатор контейнера',
                        params: {
                            pp: 'g',
                            ps: 'cmic',
                            p2: 'fqem'
                        }
                    });
                </script>
            ]]>
        </turbo:adNetwork>
		*/
		?>
		<?
		if(!empty($site->vars['default_menu'])){
		?>
		<yandex:related type="infinity">
		<?
			foreach($site->vars['default_menu'] as $v){
				if($v['level'] == 1){
					?>
					<link url="<?=$v['link_idn'];?>"><?=$v['title'];?></link>
					<?
				}
			}
			?>			
		</yandex:related>
		<?
		}
			$phone = !empty($site->vars['phone'][0]) ? $site->vars['phone'][0] : '';
			if(empty($phone) && !empty($site->vars['phone'])){
				$phone = $site->vars['phone'];
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
				?>	
					
	<item turbo="true">
		<link><?=htmlspecialchars($v['link_idn']);?></link>
		<author><?=$site->vars['name_short'];?></author>
		<?if(!empty($v['categ_title'])):?>
		<category><?=htmlspecialchars($v['categ_title']);?></category>
		<?endif;?>
		<pubDate><?date('r', strtotime($v['date_insert']));?></pubDate>
		
		<turbo:content>
                <![CDATA[
                    <header>
                        <h1><?=htmlspecialchars($v['title']);?></h1>
						<?if(!empty($img)):?>
						<figure>
                            <img src="<?=$img;?>">
                        </figure>
                        <?endif;?>
						<?/*
                        <menu>
                            <a href="http://example.com/page1.html">Пункт меню 1</a>
                            <a href="http://example.com/page2.html">Пункт меню 2</a>
                        </menu>
						*/?>
                    </header>
                    <?=$c;?>
                    <button formaction="tel:<?=$phone;?>" data-background-color="#5B97B0" data-color="white" data-primary="true">Позвонить</button>
                    <div data-block="widget-feedback" data-stick="false">
						<?if(!empty($site->vars['facebook'])):?>
						<div data-block="chat" data-type="facebook" data-url="<?=$site->vars['facebook'];?>"></div>
						<?endif;?>
						<?if(!empty($site->vars['vk'])):?>
						<div data-block="chat" data-type="vkontakte" data-url="<?=$site->vars['vk'];?>"></div>
						<?endif;?>
						<?if(!empty($site->vars['whatsapp'])):?>
						<div data-block="chat" data-type="whatsapp" data-url="<?=$site->vars['whatsapp'];?>"></div>
						<?endif;?>
						<?if(!empty($site->vars['telegram'])):?>
						<div data-block="chat" data-type="telegram" data-url="<?=$site->vars['telegram'];?>"></div>
						<?endif;?>
						<?if(!empty($site->vars['viber'])):?>
						<div data-block="chat" data-type="viber" data-url="<?=$site->vars['viber'];?>"></div>
						<?endif;?>
                    </div>
					<?if(!empty($site->vars['address'])):?>
					<p>Наш адрес: <?=$site->vars['address'];?></p>
					<?endif;?>
                ]]>
		</turbo:content>
	</item>
			<?	} ?>
				
	
</channel>
</rss>	
<?
exit;
			}
			
			
			
		
			
			
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