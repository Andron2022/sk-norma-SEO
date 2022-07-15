<?php
	if (!class_exists('Site')) { return; }

    /***********************
    *
    * returns array 
    * with page datas
    * $site->page['key'] = value; 
	* + IDN domain supports 24.01.2017
    *
    ***********************/ 
       
    $s = new Get;

    class Get extends Site {

        function __construct()
        {
        }
                
        static public function get_robots_txt($site)
        {
			if(!empty($site->vars['sys_robots_txt'])){
				header("Content-Type: text/plain");
				echo $site->vars['sys_robots_txt'];				
				exit;
			}
			
			/* reserved robots.txt */
			$url = isset($site->uri['idn']) ? $site->uri['idn'] : '';
			if(empty($url) && isset($site->uri['site'])){ $url = $site->uri['site']; }
			if(empty($url) && isset($_SERVER['HTTP_HOST'])){ $url = 'http://'.$_SERVER['HTTP_HOST']; }
			
			if(empty($url)){ die('Page not found'); }
			
			$h = str_replace('https://', '', $url);
			$h = str_replace('http://', '', $h);		
			
			$txt = "User-agent: *
Disallow: /".ADMIN_FOLDER."/
Disallow: /module/
Disallow: /tpl/
Disallow: /go.php
sitemap: ".$url."/sitemap.xml
Host: ".$h;
			header("Content-Type: text/plain");
			echo $txt;
			exit;
        }
        
    }

?>