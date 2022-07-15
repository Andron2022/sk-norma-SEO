<?php
/*
ver 1.0 28.11.2019
*/

if(!defined('MODULE')){ die(); }
if(file_exists(MODULE."/class.site.php")){ require(MODULE."/class.site.php"); }
if(file_exists(MODULE."/class.user.php")){ require(MODULE."/class.user.php"); }

include(MODULE."/captcha/captcha.php");

function reserved_slugs(){
  $ar = array( 'feedback', 'contact', 'sitemap', 'map', 'vote', 'payment',
              'search', 'user', 'basket', 'order', 'orders', 'profile', 'profiles',
              'confirm', 'login', 'alias', 'register', 'logout', 'forget_password',
              'get_last_pubs','get_last_products','get_last_price0',
              'get_last_comments','get_option','brand','rss', 'rss-comments',
              'sent','getfile');
  return $ar;
}


// ok 14.05.2015
if(!function_exists("Array2Str")){
    function Array2Str($arr, $sep, $wrap='')
    {
        if (count($arr)) {
			for ($i = 0; $i < count($arr); $i++) {
				$id = key($arr);
				$item = current($arr);
				if(!empty($wrap)){ $item = $wrap.$item.$wrap; }
                $str = isset($str) ? $str.$sep.$item : $item;
				next($arr);
			}
        }
        if(!isset($str)) { return ""; }
        return $str;
    }
}


/* ok, func downloads file */
function show_file(){
    global $site, $tpl, $db, $user;    
    // ?l=621e7fb4e770b1d72208d888011cc28c&ext=docx&id=70
    $str = isset($_GET['l']) ? trim($_GET['l']) : "";
    $ext = isset($_GET['ext']) ? trim($_GET['ext']) : "";
    $id = isset($_GET['id']) ? trim($_GET['id']) : 0;
    
    if($str == "" || $ext == "" || $id == 0){
        return $site->error_404();
    }

    $row = $db->get_row("SELECT * FROM `uploaded_files` 
            WHERE MD5( id ) = '".$str."' 
            AND `ext` = '".$ext."' ");

    if($db->num_rows == 1){
        /* OK let's download file '*/
                
        $title = $row->title;
        $title = str_replace($row->ext, "", $title);
        $filename = $title.".".$row->ext;
        $filename = str_replace('..','.', $filename);
        
        $file = UPLOAD.'/files/'.$str.'.'.$ext;

        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($filename));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . $row->size);
            ob_clean();
            flush();
            readfile($file);
            exit;
        }        
    }
    return $site->error_404();
    die();
} 



function try_addslashes($str){
  global $db;
  return $db->escape($str);
}

function _basket_link($id,$qty){
  return _link(array('basket', 'add', 'product', $id, 'qty', $qty));
}

function _linkrw($ar, $qty){
  if($qty == 2){
    if(defined('SLUG') && !in_array($ar[0], reserved_slugs())){
      if(strstr($ar[1], '?') || strstr($ar[1], '.')){
        return '/'.$ar[1];
      }
      return '/'.$ar[1].'/';
    }else{
      if(strstr($ar[1], '?') || strstr($ar[1], '.')){
        return '/'.$ar[0].'/'.$ar[1];
      }
      return '/'.$ar[0].'/'.$ar[1].'/';
    }
  }elseif($qty == 1){
    return '/'.$ar[0].'/';
  }else{

    $str = '/'.$ar[0].'/'.$ar[1].'/';
    for($i = 2; $i < $qty; $i++){
      $str .= $ar[$i].'/';
    }
    return $str;
  }
}

function _link($ar, $m=true){
  $qty = count($ar);
  if($qty == 0){ return '#error'; }
  if($m){
    if(MODE_REWRITE){ return _linkrw($ar, $qty); }
  }

  if($qty == 2){
    return '/?action='.$ar[0].'&id='.$ar[1];
  }elseif($qty == 1){
    return '/?action='.$ar[0];
  }else{
    $str = '/?action='.$ar[0].'&id='.$ar[1];
    $a = true;
    for($i = 2; $i < $qty; $i++){
      if($a){ $str .= '&'.$ar[$i]; } else { $str .= '='.$ar[$i]; }
      $a = ($a) ? false : true;
    }
    return $str;
  }
}

/*DELETE? exists function _pages in /adminpro/fns.php */
function _pages_ar($all_results, $page, $onpage, $uri_ar)
{
  global $site, $lang;
  if($all_results <= $onpage){ return; }
  $qty = ceil($all_results/$onpage);
  $ar = array();
  for($i = 0; $i < $qty; $i++){
    $page_url = $uri_ar;
    if($i > 0){
      $page_url[] = 'page';
      $page_url[] = $i;
    }

    $title=$i;

    $ar_n = array(
      'link' => _link($page_url),
      'title' => "fd".$title
    );
    if($page == $i){
      $ar_n['selected'] = 'ok';
    }

    $ar[] = $ar_n;
  }

  return $ar;
}

function _feedback(){
  global $site, $path, $tpl;
  if(isset($site->vars['fb_subject'])){
    // переведем в массив значение
 		$str = str_replace("\r\n","\n",$site->vars['fb_subject']);
 		$ar = explode("\n",$str);
    $tpl->assign('fb_subject', $ar);
  }
  
  //include($path.$site->tpl."pages/feedback.html");
  if(isset($_POST["fb"])) $site->feedback();
  $tpl->assign("page",$site->url["page"]);
  $fb = array();
  $fb["from_url"] = $_SERVER["REQUEST_URI"];
  $fb["url"] = $site->url["page"];
  //if(isset($_POST["ajax"])) $fb["message"] = utf2win($_POST["message"]);
  $tpl->assign("fb",$fb);
  if(file_exists($path.$site->tpl."pages/feedback_form.html")){
    $data = $tpl->fetch("pages/feedback_form.html");
    return $data;
  }else{ return; }
}


function db_error_old($str){ // старая, удалить
	$tpl = "<p>Database error!<br/>";
	$tpl .= "Error number: ".$str;
	return $tpl;
}



function go_setcookie($Name, $Value, $MaxAge = 0, $Path = '', $Domain = '', $Secure = false, $HTTPOnly = false){
  global $site;
  $url = $site->vars['site_url'];
  $url = str_replace('http://www', '', $url);
  //$Domain = str_replace('http://', '', $url);
  $Path = '/';

  if(header('Set-Cookie: ' . $Name . '=' . $Value
                        . (empty($MaxAge) ? '' : '; Max-Age=' . $MaxAge)
                        . (empty($Path)   ? '' : '; path=' . $Path)
                        . (empty($Domain) ? '' : '; domain=' . $Domain)
                        . (!$Secure       ? '' : '; secure')
                        . (!$HTTPOnly     ? '' : '; HttpOnly'), false)){
    return true;
  }else{
    //return false;
    return true;
  }
}

function isoneofparents($child,$id,$type="categ")
{
	global $db;//echo " $child, $id <br />";
	if($child == 0) return false;
	if($type == "categ") $table = $db->tables["categories"];
	if($type == "catalog") $table = $db->tables["shop_categs"];

	$row = $db->get_row("select id,id_parent from ".$table." where id='$child'");
	if(!$row) return false;
	if($row->id_parent == $id) return true;
	return isoneofparents($row->id_parent,$id,$type);
}

function downloadfile($src,$target="")
{
	$ch = curl_init(); // create cURL handle (ch)
	if (!$ch) {
		    die("Couldn't initialize a cURL handle");
		}

		if($target != "") $fp = fopen($target,"wb");

        $ret = curl_setopt($ch, CURLOPT_URL, $src);

		//$ret = curl_setopt($ch, CURLOPT_HEADER, 1);

		$ret = curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		if($target != "")
		{
			$ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
			$ret = curl_setopt($ch, CURLOPT_FILE,$fp);
		}
		else $ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($ch, CURLOPT_COOKIEFILE, "cook.txt");
		$ret = curl_setopt($ch, CURLOPT_TIMEOUT, 30);

		$ret = curl_exec($ch);

       if($target != "") fclose($fp);
       curl_close($ch);

       /*if($ret)
       {       	$ii = getimagesize($target);
       	if(!is_array($ii))
       	{       		$fp = fopen($target,"r");
		   	$data = fread($fp,filesize($target));
		   	fclose($fp);
		   	unlink($target);
		   	if(ereg("<?php",$data)) echo "Wannabe hackers - go away";
		   	return false;
       	}       } */

        return $ret;
}

function random_string($len)
{
	$chars = "";
	$str = "";
	for($i = ord('a'); $i <= ord('z'); $i++) $chars .= chr($i);
	for($i = ord('A'); $i <= ord('Z'); $i++) $chars .= chr($i);
	for($i = ord('0'); $i <= ord('9'); $i++) $chars .= chr($i);
	//echo $str;
	for($i = 0; $i < $len; $i++)
	{
		if(function_exists("mt_rand")) $index = mt_rand(0,strlen($chars));
		else $index = rand(0,strlen($chars));
		$str .= $chars{$index};
	}
	return $str;
}

function find_default_img($size, $alt, $where='product')
{
  global $site;
  if($where == 'user'){
    return "<img src=\"".$site->vars['template_path']."img/user_".$size.".jpg\" alt=\"".$alt."\">";
  }
  $size = $size == 'big' ? $site->vars['img_width_big'] : $site->vars['img_width_small'];
  if($size == $site->vars['img_width_big']){
    $img = "<img src=\"".$site->vars['template_path'].$site->vars['default_img_big']."\" width=\"".$site->vars['img_width_big']."\" alt=\"".$alt."\">";
  }else{
    $img = "<img src=\"".$site->vars['template_path'].$site->vars['default_img_small']."\" width=\"".$site->vars['img_width_small']."\" alt=\"".$alt."\">";
  }
  return $img;
}

function find_dop_foto(){
  global $site;
  $dp = isset($site->page['dop_foto']) ? $site->page['dop_foto'] : '';
  return $dp;
}

function find_img_records($ar) //
{
  global $site, $db;
  $width = $ar['width'];
  $where = $ar['where'];
  $id = $ar['id'];
  $alt = $ar['alt'];
  $qty = isset($ar['qty']) ? $ar['qty'] : 1;
  $cl = isset($ar['class']) ? $ar['class'] : '';
  $align = isset($ar['align']) ? $ar['align'] : '';
  $hspace = isset($ar['hspace']) ? $ar['hspace'] : 0;
  $vspace = isset($ar['vspace']) ? $ar['vspace'] : 0;
  $border = isset($ar['border']) ? $ar['border'] : '';
  if($where == "category") $where = "categ";

  global $site;
  if(in_array($where, array('product','products','good','goods'))){
    return $site->product_img($id, $width, $qty, $alt, $cl,$align,$border);
  }
  else
  if($where == 'pub')
  {
  	return $site->record_img($id,$where, $width, $qty, $alt, $cl,$align);
  }

  $size = $width == 'big' ? $site->vars['img_width_big'] : $site->vars['img_width_small'];


  $dop_foto = '';
  $query = "SELECT * FROM ".$db->tables["uploaded_pics"]."
        WHERE record_id = '".$id."' AND record_type = '".$where."'
        AND (width = '".$size."' OR height = '".$size."')
        order by is_default desc, width limit $qty";
  $results = $db->get_results($query);

  if($db->num_rows == 0){ return find_default_img($width, $alt, $where); }
  if(defined("GALLERY") && GALLERY != false
      && $size == $site->vars['img_width_big'] && $db->num_rows > 1
  )
  {
  	/*{
    if(file_exists(MODULE."/gallery.php")){
      require(MODULE."/gallery.php");
      if(!isset($img)){ $img = ""; }
      return $img;
    }*/


	    if(file_exists(MODULE."/gallery1/index.php")){
	      require(MODULE."/gallery1/index.php");
	      if(!isset($img)){ $img = ""; }
	      return $img;
	    }

  }
  $img = "";
  foreach($results as $row){
    if($row->title != ''){ $alt = htmlspecialchars($row->title); }
    $width = $row->width;
    $height = $row->height;
    if($row->width > $site->vars['img_width_big'] && $width == $site->vars['img_width_big']){
      $koef = round($site->vars['img_width_big']/$row->width, 2);
      $width = round($koef*$row->width,0);
      $height = round($koef*$row->height,0);
    }

    if(!empty($cl)){ $class = "class='".$cl."'"; } else { $class = ""; }
    if(!empty($align)){ $align = "align=\"".$align."\""; } else { $align = ""; }
    if(!empty($hspace)){ $hspace = "hspace=\"".$hspace."\""; } else { $hspace = ""; }
    if(!empty($vspace)){ $vspace = "vspace=\"".$vspace."\""; } else { $vspace = ""; }
    if($border != ""){ $border = "border=\"".$border."\""; } else { $border = ""; }

    $style = "";

    if($hspace != 0){ $style .= "margin-right: ".$hspace."px;\""; }
    if($vspace != 0){ $style .= " margin-bottom: ".$vspace."px;\""; }
    if(!empty($style)){ $style = "style=\"".$style."\""; } else { $style = ""; }

    $img = "<img src=\"/upload/records/".$row->id.".".$row->ext."\"
        width=".$width." height=\"$height\" $class $align $style
        alt=\"".$alt."\" title=\"".$alt."\" $hspace $vspace $border />";
    $img = str_replace("\\", "/", $img);
    $img = str_replace("//", "/", $img);
  }
  return $img;
}

function rus2trans($str){
  $rus = array(
      'щ','Щ','ш','Ш','ё','Ё','ж','Ж','ч','Ч','э','Э',
      'ю','Ю','я','Я','а','б','в','г','д','е','з','и',
      'й','к','л','м','н','о','п','р','с','т','у','ф',
      'х','ц','ъ','ы','ь','А','Б','В','Г','Д','Е','З',
			'И','Й','К','Л','М','Н','О','П','Р','С','Т','У',
      'Ф','Х','Ц','Ъ','Ы','Ь',' ',
	  'ó', 'ñ', 'í', 'é', 'ú', 'á', 'ü'
	  );
	$trans = array('sch','SCH','sh','SH','yo','YO','zh','ZH','ch','CH','e','E','yu','YU','ya','YA','a','b','v','g',
					'd','e','z','i','j','k','l','m','n','o','p','r','s','t','u','f','h','c','','y','','A','B','V',
					'G','D','E','Z','I','J','K','L','M','N','O','P','R','S','T','U','F','H','C','','Y','','_',
					'o', 'n', 'i', 'e', 'u', 'a', 'u'
					);
	$newstr = str_replace($rus,$trans,$str);
return $newstr;
}

function random_word($len=6){
  srand((float) microtime() * 10000000);
  $ar = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'm',
    'n', 'p', 'q', 'r', 's', 't', 'y', 'x', 'w', 'z', 'A', 'B', 'C', 'D',
    'E', 'F', 'G', 'H', 'I', 'J', 'K', 'M', 'N', 'P', 'Q', 'R', 'S', 'T',
    'Y', 'X', 'W', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9');
  $rand_keys = array_rand($ar, $len);
  $str = "";
  for($i=0; $i<$len; $i++){
    $str .= $ar[$rand_keys[$i]];
  }
  return $str;
}

// доп. функция для удаления опасных сиволов
function pregtrim($str) {
   return preg_replace("/[^\x20-\xFF]/","",@strval($str));
}

function checkmail($mail) {
   $mail=trim(pregtrim($mail));
   // если пусто - выход
   if (strlen($mail)==0) return false;
   if (!preg_match("/^[a-z0-9_-]{1,20}@(([a-z0-9-]+\.)+(com|net|org|mil|".
   "edu|gov|arpa|info|biz|inc|name|[a-z]{2})|[0-9]{1,3}\.[0-9]{1,3}\.[0-".
   "9]{1,3}\.[0-9]{1,3})$/is",$mail)){
    return false;
   }
   return $mail;
}


function _redirect($url){
  if (!headers_sent($filename, $linenum)) {
    header('Location: '.$url);
    exit;
  } else {
    return "Headers already sent in $filename on line $linenum\n<br/>
         Cannot redirect, for now please click this <a
         href=\"".$url."\">link</a> instead\n";
    exit;
  }
}

function counter(){
  global $site;
  if(MODE == 'localhost'){ return '<p>*Localhost - counters are here*</p>'; }
  return Resource_Page("counter/counter.html");
}


function upload_pics_records($id)
{
  global $site;

  if(empty($site->vars['img_width_small'])){ return; }
  include("image_resize.php");
  global $db;
  $i = 0;
  if(!isset($_FILES["pics"])){ return; }
  $record_type = isset($_POST["record_type"]) ? try_addslashes($_POST["record_type"]) : "";

  foreach($_FILES["pics"]["size"] as $k => $v){
    if($v == 0){ break; }

    $txt = try_addslashes($_POST["pics_text"][$k]);
    $pics = getimagesize($_FILES["pics"]["tmp_name"][$k]);
    if(!$pics){
      $GLOBALS["uploaded"] = "Файл не загружен! Загружаемый файл не является изображением!";
      echo _red($GLOBALS["uploaded"]); break;
    }
    $width = $pics[0];
    $height = $pics[1];
    $ext = $pics[2];

    if($ext == 1){ $ext = "gif"; }
    elseif($ext == 2){ $ext = "jpg"; }
    elseif($ext == 3){ $ext = "png"; }
    else{
      $GLOBALS["uploaded"] = "Файл не загружен! Неизвестный формат изображения: $ext.";
      echo _red($GLOBALS["uploaded"]);
      break;
    }

    if($v >= $site->vars['img_width_small'] && isset($_POST["do_small"][$k])){
      if($width >= $height){
        $widthx = ($width > $site->vars['img_width_small']) ? $site->vars['img_width_small'] : $width;
        $heightx = ceil($widthx*$height/$width);
      }else{
        $heightx = ($height > $site->vars['img_width_small']) ? $site->vars['img_width_small'] : $height;
        $widthx = ceil($heightx*$width/$height);
      }
      $db->query("INSERT INTO ".$db->tables["uploaded_pics"]." (`id_exists`, `record_id`,
            `record_type`, `width`, `height`, `title`, `ext`)
            VALUES('0', '".$id."', '".$record_type."', '".$widthx."', '".$heightx."',
            '".$txt."', '".$ext."')");
      $pic_id = $db->insert_id;
      $uploaddir = UPLOAD."/records/".$pic_id.".".$ext;
      $resize = upload_and_resize($_FILES["pics"]["tmp_name"][$k], 'small', $uploaddir);
      if(!$resize){
        $db->query("DELETE FROM ".$db->tables["uploaded_pics"]." WHERE id = '".$pic_id."' ");
      }else{ $i++; }
    }

    if($v >= $site->vars['img_width_big']  && isset($_POST["do_big"][$k])){
      if($width >= $height){
        $widthx = ($width > $site->vars['img_width_big']) ? $site->vars['img_width_big'] : $width;
        $heightx = ceil($widthx*$height/$width);
      }else{
        $heightx = ($height > $site->vars['img_width_big']) ? $site->vars['img_width_big'] : $height;
        $widthx = ceil($heightx*$width/$height);
      }
      $db->query("INSERT INTO ".$db->tables["uploaded_pics"]." (`id_exists`,
            `record_id`, `record_type`, `width`, `height`, `title`, `ext`)
            VALUES('0', '".$id."', '".$record_type."', '".$widthx."', '".$heightx."',
            '".$txt."', '".$ext."')");
      $pic_id = $db->insert_id;
      $uploaddir = UPLOAD."/records/".$pic_id.".".$ext;
      $resize = upload_and_resize($_FILES["pics"]["tmp_name"][$k], 'big', $uploaddir);
      if(!$resize){
        $db->query("DELETE FROM ".$db->tables["uploaded_pics"]." WHERE id = '".$pic_id."' ");
      }else{ $i++; }
    }

    if(isset($_POST["stop_biggest"][$k])){
      break;
    }elseif($width <= $site->vars['img_width_big'] && isset($_POST["do_big"][$k])){
      break;
    }elseif($width <= $site->vars['img_width_small'] && isset($_POST["do_small"][$k])){
      break;
    }

    $db->query("INSERT INTO ".$db->tables["uploaded_pics"]." (`id_exists`, `record_id`,
           `record_type`, `width`, `height`, `title`, `ext`)
            VALUES('0', '".$id."', '".$record_type."', '".$width."', '".$height."',
            '".$txt."', '".$ext."')");

    $pic_id = $db->insert_id;
    $uploaddir = UPLOAD."/records/".$pic_id.".".$ext;
    if(move_uploaded_file($_FILES["pics"]["tmp_name"][$k], $uploaddir)){
      $i++;
    }
  }
  $GLOBALS["uploaded"] = "<p>Загружено файлов: ".$i."</p>";
  return true;
}


function delete_pics_records($ar){
  global $db;
  foreach($ar as $v){
    $path = UPLOAD."/records/".$v;
    if(file_exists($path)){
      @unlink($path);
    }
    $id_ar = explode(".",$v);
    $id = $id_ar[0];
    $query = "DELETE FROM ".$db->tables["uploaded_pics"]." WHERE id = '".$id."' ";
    $db->query($query);
  }
  return true;
}

function delete_files_records($ar){
  global $db;
  foreach($ar as $v){
    /*$path = UPLOAD."/records/".$v;
    if(file_exists($path)){
      @unlink($path);
    }
    $id_ar = explode(".",$v);
    $id = $id_ar[0];*/
    $row = $db->get_row("SELECT * FROM ".$db->tables["uploaded_files"]." WHERE id = '".$v."' ");
    if($row)
    {
       $path = UPLOAD."/file/".$row->filename;
       $test = $db->get_row("SELECT id FROM ".$db->tables["uploaded_files"]."
    WHERE filename='".$row->filename."' AND id!='".$row->id."'");
       if(!$test) @unlink($path);
    }
    $query = "DELETE FROM ".$db->tables["uploaded_files"]." WHERE id = '".$v."' ";
    $db->query($query);
  }
  return true;
}

/// ok
function send_headers(){
  if (headers_sent()) { return; }
  global $site, $user;
  if(isset($site->page['last_modified'])){
    $last_mod = date('D, d M Y H:i:s', strtotime($site->page['last_modified']));
  }else{
    $last_mod = gmdate('D, d M Y H:i:s');
  }

  $ar = array(
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    300 => 'Multiple Choices',    
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    306 => '(Unused)',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Long',
    415 => 'Unsupported Media Type',
    416 => 'Requested Range Not Satisfiable',
    417 => 'Expectation Failed',
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Timeout',
    505 => 'HTTP Version Not Supported'
  );
  
  if(!isset($ar[$site->header_status])) { $site->header_status = 200; }
  $text = $ar[$site->header_status];

  if($site->header_status != 200) header("HTTP/1.1 ".$site->header_status." $text");
  header("Status: ".$site->header_status." $text");

  if($user->id > 0 || in_array('basket',$site->uri) || in_array('order',$site->uri)){
    nocache_headers();
  }else{
  	header('Last-Modified: '.$last_mod.' '.$site->vars['site_time_zone'].' ');
  }           
}

function nocache_headers() {
	header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-cache, must-revalidate, max-age=0');
	header('Pragma: no-cache');
}

function _status($status, $field=false)
{
  global $lang;
  $ar = array(
    0 => $lang->txt('status_new'),
    1 => $lang->txt('status_in_progress'),
    10 => $lang->txt('status_done'),
    -1 => $lang->txt('status_cancel'),
    -10 => $lang->txt('status_wrong'),
  );
  if($field == false){
    if(isset($ar[$status])){
      return $ar[$status];
    } else { return $lang->txt('status_unknown'); }
  }else{
    $str = "<select name='".$field."'>";
    foreach($ar as $k=>$v){
      if($k == $status){ $sel = "selected"; } else { $sel = ""; }
      $str .= "<option value='".$k."' $sel>$v</sel>";
    }
    $str .= "</select>";
    return $str;
  }
}

function find_alias($table, $id){
  global $db;
  $var = $db->get_var("SELECT alias FROM ".$table." WHERE id = '".$id."' ");
  if($db->num_rows == 0){ return false; }
  return stripslashes($var);
}

function find_by_alias($table, $alias){
  global $db;
  $var = $db->get_var("SELECT id FROM ".$table." WHERE alias = '".$alias."' ");
  if($db->num_rows == 0){ return false; }
  return stripslashes($var);
}

function error_403(){
    global $lang, $site;
    $site->header_status = 403;
    $site->page['content'] = Resource_Page("pages/403.html");
    $site->page['page'] = 'info.html';
    //$site->page['title'] = $lang->txt('error');
    $site->page['title'] = '';
    $site->page['metatitle'] = $lang->txt('error');
    $site->page['keywords'] = $lang->txt('error');
    $site->page['description'] = $lang->txt('error');
    $site->page['year'] = date('Y');
    if(!$site->page['content']){
      $site->page['content'] = $lang->txt('error_403');
    }
    return;
}


function img_captcha($ar)
{
  $size = isset($ar["size"]) ? intval($ar["size"]) : 5;
  $ar["name"] = isset($ar["name"]) ? $ar["name"] : random_string($size);
  $ar["size"] = $size;  
  global $tpl;          
  $tpl->assign("ar",$ar);
  $tpl->display("pages/captcha.html");
  return;          
}

function check_captcha()
{
  global $lang;

  if(!isset($_POST['captcha_name']) || !isset($_POST['keystring'])){
    return $lang->txt('error_captcha');
  }
	$name = $_POST['captcha_name'];
	$captcha = new captcha($name);
	if($captcha->check($_POST['keystring']))
	{
		unset($_SESSION['captcha'][$name]);
	}
	else
	{
		unset($_SESSION['captcha'][$name]);
		return $lang->txt('error_captcha');
	}
  return;
}

function inform($string) {
    $string = trim($string);
		return htmlspecialchars(stripslashes($string));
}

function count_delivery(){
  if(function_exists('user_fill_fields')){
    $ar = user_fill_fields();
    if(!is_array($ar)){ return false; }
    if(!in_array('delivery_summ', $ar)){ return false; }
    return true;
  }
  return false;
}

if(!function_exists('Resource_Page')){
function Resource_Page($filename)
{
  global $site, $path;
  $urla = $path.$site->vars['template_path'].$filename;
  $urlb = TPL_DEFAULT.$filename;

  if(file_exists($urla)){
    $str = file_get_contents($urla);
  }else{
    if(file_exists($urlb)){
      $str = file_get_contents($urlb);
    }else{
      $str = $filename." not found";
    }
  }
  return $str;
}
}


function new_ticket() // 3 по 4 букво-цифр
{
	$letters = array("1","2","3","4","5","6","7","8","9","A","B",
                "C","D","E","F","G","H","I","J","K","L","M",
                "N","O","P","Q","R","S","T","V","U","W","X",
                "Y","Z");
	for($i=0;$i<3;$i++){
		if(isset($str))	{ $str .= "-"; } else { $str = ""; }
		for($j=0;$j<4;$j++){
			$b = array_rand($letters, 1);
			$str .= $letters[$b];
		}
	}
	if(!check_ticket($str)){ return new_ticket(); }
	return $str;
}

function check_ticket($ticket)

{
	// If current ticket found then we will build new one
	global $db;
	$query = "SELECT * FROM ".$db->tables["feedback"]."
            WHERE f_ticket_number = '".$ticket."' ";
	$result = $db->get_row($query);
	if($db->num_rows == 0){ return true; }
	else { return false; }
}

function comments_qty($ar){
  if(!isset($ar['where']) || !isset($ar['id'])){
    return 0;
  }

  global $db,$tpl;
  $var = $db->get_var("SELECT count(*) FROM ".$db->tables['comments']."
      WHERE record_type = '".try_addslashes($ar['where'])."'
      AND record_id = '".try_addslashes($ar['id'])."'
      AND active = '1' ");
  if(isset($ar['assign']))
  {
  	$tpl->assign($ar['assign'],$var);
  	return '';
  }
  return $var;
}


function vote()
{
  global $db, $site;
  if(isset($_POST["vote_id"])){
    $vote_id = intval($_POST["vote_id"]);
    $answers = isset($_POST["vote_answer"]) ? $_POST["vote_answer"] : array();
    $ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "";
    $proxy = isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : "";
    $user_agent = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "";

    if(!is_array($answers)){
      $answers = array($answers);
    }

    $i = 0;
    foreach($answers as $answer){
      $db->query("INSERT INTO ".$db->tables["vote_results"]." (
              `date_insert`, `vote_id`, `answer_id`, `ip`, `proxy`, `user_agent`)
              VALUES ('".date("Y-m-d H:i:s")."', '".$vote_id."', '".$answer."',
              '".try_addslashes($ip)."', '".try_addslashes($proxy)."',
              '".try_addslashes($user_agent)."' ) ");
      $db->query("UPDATE ".$db->tables["vote_answers"]." SET
            qty = qty+1 WHERE id = '".$answer."' AND vote_id = '".$vote_id."' ");
      $i++;
    }

    if($i == 0){
      $href = _link(array("vote", "error"));
      return _redirect($href);
      exit;
    }

    $_SESSION["voted"] = $vote_id;
    $site_url = str_replace("http://", "", $site->vars['site_url']);
    $site_url = str_replace(".", "_", $site_url);
    setcookie($site_url."_".$vote_id, $vote_id, time()+3600*24*7);
    $href = _link(array("vote", "ok"));
    return _redirect($href);
    exit;
  }

  $row = $db->get_row("SELECT * FROM ".$db->tables["vote"]." WHERE active = '1' ");
  if($db->num_rows == 0){ return; }

  $insert_date = strtotime($row->date_insert);
  $stop_date = strtotime($row->date_stop);
  $today = time();

  if($row->date_stop == 0 || $today < $stop_date){
    $vote_id = $row->id;
    $multi = $row->multi;
  }else{
    return;
  }
  $question = stripslashes($row->question);
  $voted = dont_voted($vote_id);

  if($voted == 1){
    // vote form
    $vote_rows = rows_to_vote($vote_id, $multi);
  }else{
    // cant vote, just results
    $vote_rows = vote_results($vote_id, $question);
  }
  if(!isset($site->page['voted'])){
    $site->page['voted'] = $voted;
  }

  if(!isset($site->page['vote_question'])){
    $site->page['vote_question'] = $question;
  }

  if(!isset($site->page['vote_id'])){
    $site->page['vote_id'] = $vote_id;
  }

  if(!isset($site->page['vote_rows'])){
    $site->page['vote_rows'] = $vote_rows;
  }
  return;
}

function vote_results($vote_id, $question = "")
{
  global $db;

  if($question == ""){
    $question = $db->get_var("SELECT question FROM ".$db->tables["vote"]."
      WHERE id = '".$vote_id."' ");
  }

  $results = $db->get_results("SELECT * FROM ".$db->tables["vote_answers"]."
    WHERE vote_id = '".$vote_id."' ");
  if($db->num_rows == 0){ return; }
  $all_qty = 0;
  foreach($results as $row){
    $all_qty += $row->qty;
  }

  $ar = array();
  foreach($results as $row){
    $rows_qty = $all_qty > 0 ? round($row->qty/$all_qty*100,0) : 0;
    $ar[] = array(
      'row_answer' => stripslashes($row->answer),
      'qty_persent' => $rows_qty
    );
  }
  return $ar;
}

function dont_voted($id)
{
  global $db, $site;
  $site_url = str_replace("http://", "", $site->vars['site_url']);
  $site_url = str_replace(".", "_", $site_url);
  $kuka = $site_url."_".$id;
  if(isset($_COOKIE["$kuka"]) || isset($HTTP_COOKIE_VARS["$kuka"])){ return 0; }
  if(isset($_SESSION["voted"])){
    if($_SESSION["voted"] == $id) { return 0; }
  }

	$ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "";
	$proxy = isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : "";
	$user_agent = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "";

  $var=$db->get_var("SELECT count(*) FROM ".$db->tables["vote_results"]."
                WHERE `vote_id` = '".$id."' AND
                `ip` = '".try_addslashes($ip)."' AND
                `proxy` = '".try_addslashes($proxy)."' AND
                `user_agent` = '".try_addslashes($user_agent)."' ");
  if($var > 0){ return 0; }
  return 1;
}

function rows_to_vote($vote_id, $multi)
{
  global $db;
  $results = $db->get_results("SELECT * FROM ".$db->tables["vote_answers"]."
    WHERE vote_id = '".$vote_id."' ");
  if($db->num_rows == 0){ return; }
  $ar = array();
  foreach($results as $row){
    $field = $multi == 1 ? "checkbox" : "radio";
    $ar[] = array(
      'answer' => stripslashes($row->answer),
      'field' => $field,
      'id' => $row->id
    );
  }
  return $ar;
}

function utf2win ($s)
{
    return utf8_convert($s,'w');
}

function utf8_convert($str, $type)
{
   static $conv = '';
   if (!is_array($conv))
   {
      $conv = array();
      for ($x=128; $x <= 143; $x++)
      {
         $conv['utf'][] = chr(209) . chr($x);
         $conv['win'][] = chr($x + 112);
      }
      for ($x=144; $x<= 191; $x++)
      {
         $conv['utf'][] = chr(208) . chr($x);
         $conv['win'][] = chr($x + 48);
      }
      $conv['utf'][] = chr(208) . chr(129);
      $conv['win'][] = chr(168);
      $conv['utf'][] = chr(209) . chr(145);
      $conv['win'][] = chr(184);
   }
   if ($type == 'w')
   {
      return str_replace($conv['utf'], $conv['win'], $str);
   }
   elseif ($type == 'u')
   {
      return str_replace($conv['win'], $conv['utf'], $str);
   }
   else
   {
      return $str;
   }
}

  function random_pubs($ar_val, &$tpl){

/*
{ foreach value=value from=$random_pubs }
	<p>{ $value.title }</p>
	<p>{ $value.link }</p>
	<p>{ $value.anons }</p>
	<p>{ $value.content }</p>
{ /foreach }
*/

    $qty = isset($ar_val['qty']) ? intval($ar_val['qty']) : 1;
    $id = isset($ar_val['id']) ? intval($ar_val['id']) : 0;
    $var = isset($ar_val['assign']) ? $ar_val['assign'] : "";
    if($id == 0 && $qty == 0){ return; }
    if($id == 0){
      $id_str = "";
    }else{
      $id_str = " pcat.id_categ IN (".$id.") AND ";
    }

    global $db, $site;

    $ar = array();
    $query = "
      SELECT publications.*
      FROM
        ".$db->tables['publications']."
      INNER JOIN ".$db->tables["site_publications"]." ON
      ( site_publications.id_site=".$site->id." AND
      site_publications.id_publications = publications.id),
      ".$db->tables["pub_categs"]." pcat
      WHERE {$id_str} pcat.id_pub= ".$db->tables['publications'].".id
      AND ".$db->tables["publications"].".active = '1'
      ORDER BY RAND() LIMIT $qty
    ";//echo $query;

    $rows = $db->get_results($query);
    if($db->num_rows == 0){ return ; }
    foreach($rows as $row){
      $link = (!MODE_REWRITE) ?
        _link(array('pub', $row->id)) :
        _link(array('pub', stripslashes($row->alias)));
      $title = stripslashes($row->name);

        $ar[] = array(
          'link' => $link,
          'title' => $title,
          'anons' => nl2br(stripslashes($row->anons)),
          'content' => nl2br(stripslashes($row->memo)),
          'date_insert' => $row->date_insert,
          'date_update' => $row->ddate,
          'user_id' => $row->user_id,
          'id' => $row->id
        );

    }

    $tpl->assign($var,$ar);
    //return $ar;
  }

 function get_server($name)
 {
 	return isset($_SERVER[$name]) ? $_SERVER[$name] : null;
 }

 function get_param($name,$default=null)
 {
 	$var = isset($_GET[$name]) ? $_GET[$name] : $default;
 	$var = isset($_POST[$name]) ? $_POST[$name] : $var;
 	return $var;
 }

 function get_cookie($name,$default=null)
 {
 	$var = isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
 	return $var;
 }

 function get_session($name,$default=null)
 {
 	$var = isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
 	return $var;
 }

 function set_session($name,$value)
 {
 	$_SESSION[$name] = $value;
 	return $value;
 }

 function implode_and_quote($glue,$ar,$quote)
 {
 	foreach($ar as $k => $id)
	{
		$ar[$k] = $quote.$ar[$k].$quote;
	}
	return implode($glue,$ar);
 }

 function implode4mysql($ar,$quote="'")
 {
 	return implode_and_quote(", ",$ar, $quote);
 }

 function _img($src,$title = "",$border = 0)
 {
 	$ii = getimagesize($src);
 	return "<img src=\"$src\"".$ii[3]." alt=\"$title\" title=\"$title\" border=\"$border\" />";
 }

 function getip()
 {
 	if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])) $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
 	elseif(isset($_SERVER["HTTP_X_REAL_IP"])) $ip = $_SERVER["HTTP_X_REAL_IP"];
 	else $ip = $_SERVER["REMOTE_ADDR"];
 	return $ip;
 }

 function starrating($ar)
 {
 	//require(MODULE."/ajaxstarrater/_drawrating.php");
	return;
 	global $db,$tpl;
 	$id = $ar["id"];
 	$for = isset($ar["for"]) ? $ar["for"] : "pub";
 	ob_start();

 	$ip = getip();

 	$row = $db->get_row("select * from rating where id='$id' and ip='".$ip."' and `for`='$for'");
    if($row) $tpl->assign('already_voted',1);
    else $tpl->assign('already_voted',0);
 	//showrating($id,$for);

 	$tpl->assign('rate',round(getrate($id,$for),2));
 	$tpl->assign('votes',getratecount($id,$for));
    $tpl->assign('id',$id);
    $tpl->assign('for',$for);

    echo "<div id=\"rating_$id\">";
 	$tpl->display("rating/rating.html");
 	echo "</div>\n";

 	$data = ob_get_contents();
 	ob_end_clean();
 	return $data;
 	//return rating_bar($id,$nstars);
 }

 function categ_menu_tpl($ar)
 {
 	global $db,$tpl, $site;
 	$var = isset($ar["assign"]) ? $ar["assign"] : "default";
 	$cat = isset($ar["cat"]) ? $ar["cat"] : 0;//Если параметр не указан - корневые категории
 	$qty = isset($ar["qty"]) ? $ar["qty"] : 30;
  $sortby = isset($ar["sortby"]) ? $ar["sortby"] : "c.sort, c.name_main";
  $order = isset($ar["order"]) ? $ar["order"] : "asc";
  $notid = isset($ar["notid"]) ? $ar["notid"] : "";
  $alias = isset($ar["alias"]) ? $ar["alias"] : "";

  /* Узнаем какие картинки запрошены и строим запрос */
    $def_img = '';
    $size = isset($ar["size"]) ? $ar["size"] : "small";	 
	  $width = isset($site->vars['resize_method']) ? $site->vars['resize_method'] : '';
	  if($width != 'height'){ $width = 'width'; }
    if($size == "small") {
      $def_img = $site->vars['default_img_small'];
      $size = $site->vars['img_width_small'];
      $str_img = " AND ( uploaded_pics.".$width." = '".$size."' AND uploaded_pics.is_default = '1' ) ";
	  }elseif($size == "big"){
      $def_img = $site->vars['default_img_big'];
	    $size = $site->vars['img_width_big'];
	    $str_img = " AND ( uploaded_pics.".$width." <= '".$size."' AND uploaded_pics.".$width." > '".$site->vars['img_width_small']."' AND uploaded_pics.is_default = '1' ) ";
	  }elseif($size == "mini"){
      if(isset($site->vars['img_width_mini'])){
        $size = intval($site->vars['img_width_mini']);
			  if($size > 0){
			    $str_img = " AND (uploaded_pics.".$width." = '".$size."' AND uploaded_pics.is_default = '1') ";
			  }else{ $str_img = ''; }
		  }else{ $size=0; $str_img = ''; } 
	  }else { $size = 0; $str_img = ''; }
  /* Все, сформировали данные о запрашиваемых картинках */

    if($notid != "") $notid_sql = " AND c.id not in ($notid)";
     else $notid_sql = "";

    if($alias != "" && $cat == 0) { $cat = $db->get_var("SELECT id from ".$db->tables['categories']." WHERE alias='$alias'");}

    $query = "SELECT c.id, c.name_main as title, c.name_main as name,c.icon, c.alias, c.alias as link,
              uploaded_pics.id AS img_id,
              uploaded_pics.title AS img_name,
              uploaded_pics.width AS img_width,
              uploaded_pics.height AS img_height,
              uploaded_pics.ext AS img_ext
              FROM ".$db->tables['categories']." c
              LEFT OUTER JOIN {$db->tables['uploaded_pics']}
              ON uploaded_pics.record_id = c.id
              AND uploaded_pics.record_type = 'categ'
              $str_img,
              ".$db->tables['site_categ']." sc
              WHERE c.id_parent = '$cat' AND c.active > '0' AND sc.id_categ=c.id AND sc.id_site='".$site->id."' $notid_sql
              ORDER by $sortby $order LIMIT 0, $qty  ";

    $rows = $db->get_results($query);
    $items = array();

    if(is_array($rows))
    foreach($rows as $row)
    {
    	$item = array();
    	$item['id'] = $row->id;
    	$item['title'] = stripslashes($row->title);
    	$item['link'] = (!MODE_REWRITE) ? _link(array('category', $row->id)) : _link(array('category', stripslashes($row->alias)));
    	$item['alias'] = $row->alias;
    	$item['icon'] = $row->icon;

        // Добавленные выходные значения о изображении
        if($row->img_id)
        {
            $item['img'] = '/upload/records/'.$row->img_id.'.jpg';
        }
        else
        {
            $item['img'] = '';
        }
        $item['name_pic'] = $row->img_name;
        $item['width'] = $row->img_width;
        $item['height'] = $row->img_height;
    	array_push($items,$item);
    }

 	if(isset($var)) $tpl->assign($var,$items);
 	return "";
 }

 function catalog_menu_tpl($ar)
 {
 	global $db,$tpl,$site;
 	$var = isset($ar["assign"]) ? $ar["assign"] : "default";
 	$cat = isset($ar["cat"]) ? $ar["cat"] : 0;//Если параметр не указан - корневые категории
 	$qty = isset($ar["qty"]) ? $ar["qty"] : 30; // По умолчанию - 30
  $sortby = isset($ar["sortby"]) ? $ar["sortby"] : "sc.sort, sc.name"; 
  $order = isset($ar["order"]) ? $ar["order"] : "asc";
  $notid = isset($ar["notid"]) ? $ar["notid"] : "";
  $alias = isset($ar["alias"]) ? $ar["alias"] : "";

  /* Строим данные для подключения картинок */
  $def_img = '';
  $size = isset($ar["size"]) ? $ar["size"] : "small";
	$width = isset($site->vars['resize_method']) ? $site->vars['resize_method'] : '';
	if($width != 'height'){ $width = 'width'; }
    if($size == "small") {
      $def_img = $site->vars['default_img_small'];
	    $size = $site->vars['img_width_small'];
	    $str_img = " AND (uploaded_pics.".$width." = '".$size."' AND uploaded_pics.is_default = '1') ";
	  }elseif($size == "big"){
	    $def_img = $site->vars['default_img_big'];
	    $size = $site->vars['img_width_big'];
	    $str_img = " AND (uploaded_pics.".$width." <= '".$size."' AND uploaded_pics.".$width." > '".$site->vars['img_width_small']."' AND uploaded_pics.is_default = '1') ";
	  }elseif($size == "mini"){ 
	    if(isset($site->vars['img_width_mini'])){
        $size = intval($site->vars['img_width_mini']);
        if($size > 0){
          $str_img = " AND (uploaded_pics.".$width." = '".$size."' AND uploaded_pics.is_default = '1') ";
        }else{ $str_img = ''; }
		}else{ $size=0; $str_img = ''; } 
	  }else { $size = 0; $str_img = ''; }

    if($notid != "") $notid_sql = " AND sc.id not in ($notid)";
    else $notid_sql = "";

    if($alias != "" && $cat == 0) { $cat = find_by_alias($db->tables['shop_categs'],$alias); }
    $cat_sql = "  sc.id_parent = '$cat' AND ";

    // Оптимизированный по заданию SQL запрос
    $query = "SELECT 
      sc.id, sc.name, sc.name as title, 
      sc.alias, sc.alias as link,
      uploaded_pics.id AS img_id,
      uploaded_pics.title AS img_name,
      uploaded_pics.width AS img_width,
      uploaded_pics.height AS img_height, 
      uploaded_pics.ext AS img_ext
        FROM ".$db->tables['shop_categs']." sc
        LEFT OUTER JOIN {$db->tables['uploaded_pics']}
        ON uploaded_pics.record_id = sc.id
        AND uploaded_pics.record_type = 'catalog'
        $str_img ,
                                  ".$db->tables['site_shop_categ']." ssc
                                  WHERE  {$cat_sql} sc.active > '0' AND ssc.id_categ=sc.id AND ssc.id_site='".$site->id."'
                                  $notid_sql
                                  ORDER by $sortby $order LIMIT 0, $qty  ";

     $rows = $db->get_results($query);
     $items = array();
    if(is_array($rows))
    foreach($rows as $row)
    {
    	$item = array();
    	$item['id'] = $row->id;
    	$item['title'] = stripslashes($row->title);
      $item['alias'] = stripslashes($row->alias);  
    	$item['link'] = (!MODE_REWRITE) ? _link(array('catalog', $row->id)) : _link(array('catalog', stripslashes($row->alias)));

        // Добавленные выходные значения о изображении
        if($row->img_id)
        {
            $item['img'] = '/upload/records/'.$row->img_id.'.'.$row->img_ext;
        }
        else
        {
            $item['img'] = '';
        }
        $item['name_pic'] = $row->img_name;
        $item['width'] = $row->img_width;
        $item['height'] = $row->img_height;

    	array_push($items,$item);
    }

 	if(isset($var)) $tpl->assign($var,$items);

 	return "";
 }

 function get_map_markers()
 {
    global $db,$site;
    $str = "";
    $rows = $db->get_results("SELECT `name_short` as address 
        FROM ".PREFIX."shop_products 
        WHERE `name_short` <> '' AND active = '1' AND accept_orders = '1' 
        ORDER BY f_spec desc, f_new desc, id desc
        limit 0, 10 
        ");

    if(isset($site->vars['sys_map_marker'])){
        $str .= ' {
                      address: "'.$site->vars['sys_map_marker'].'",
                      options:{
                         icon: "/upload/images/aeropuerto.png"                      
                      }
                }, '; 
    }    
    if($db->num_rows > 0){
      foreach($rows as $k=>$row){
        if($k > 0){ $str .= ", ";}
          $str .= ' {
                      address: "'.$row->address.'",
                      options:{
                         icon: "/upload/images/custom-marker.png"                      
                      }
                } ';
      }
    }                   
    return $str;
 }

 function last_pub_tpl($ar,&$tpl)
 {
 	global $db,$tpl,$site;//echo print_r($site->page,true);
 	$var = isset($ar["assign"]) ? $ar["assign"] : "default";
 	$cat = isset($ar["cat"]) ? $ar["cat"] : 0;//Если параметр не указан - корневые категории
 	$qty = isset($ar["qty"]) ? $ar["qty"] : 5;
  $sortby = isset($ar["sortby"]) ? $ar["sortby"] : "date_insert";
  $order = isset($ar["order"]) ? $ar["order"] : "desc";
  $notid = isset($ar["notid"]) ? $ar["notid"] : "";
  $alias = isset($ar["alias"]) ? $ar["alias"] : "";

  // Входной параметр size (если не задан - то по умолчанию small)
  $size = isset($ar["size"]) ? $ar["size"] : "small";
  if($size == "big") $img_size = $site->vars["img_width_big"];
  if($size == "small") $img_size = $site->vars["img_width_small"];
  if(isset($site->vars['img_width_mini']) && $size == "mini") $img_size = $site->vars['img_width_mini'];
  if($size == "no") $img_size = "no";


  if($notid != "") $notid_sql = " AND p.id not in ($notid)";
  else $notid_sql = "";

  if($alias != "")
  {
  	$alias_sql = " AND c.alias='$alias' ";
  }else $alias_sql = "";


  if($cat !=0) $cat_sql = " AND pc.id_categ = '$cat' ";
  else $cat_sql = "";

    // Оптимизированный запрос по заданию
    $query = "SELECT p.id, p.id as id_pub, p.name, p.icon, p.anons, p.memo, p.alias, p.date_insert,
              uploaded_pics.id AS img_id,
              uploaded_pics.ext AS img_ext,
              uploaded_pics.title AS img_name,
              uploaded_pics.width AS img_width,
              uploaded_pics.height AS img_height
              FROM ".$db->tables['publications']." p
              LEFT OUTER JOIN {$db->tables['uploaded_pics']}
              ON uploaded_pics.record_id = p.id
              AND uploaded_pics.record_type = 'pub'
              AND (uploaded_pics.width={$img_size} OR uploaded_pics.height={$img_size}),
              ".$db->tables['site_publications']." sp,
              ".$db->tables['pub_categs']." pc, ".$db->tables["categories"]." c
              WHERE p.id = pc.id_pub AND c.id=pc.id_categ $alias_sql $cat_sql AND p.active > '0' $notid_sql
              AND p.id=sp.id_publications AND sp.id_site='".$site->id."'
              GROUP by p.id
              ORDER by $sortby $order LIMIT 0, $qty  ";

    $rows = $db->get_results($query);
     $items = array();

    if(is_array($rows))
    foreach($rows as $row)
    {
    	if($site->page['page'] != 'pub' || $site->page['id'] != $row->id)
    	{
	    	$item = array();
	    	$item['id'] = $row->id;
	    	$item['title'] = stripslashes($row->name);
	    	$item['link'] = (!MODE_REWRITE) ? _link(array('pub', $row->id)) : _link(array('pub', stripslashes($row->alias)));
	    	$item['anons'] = $row->anons;
	    	$item['memo'] = $row->memo;
	    	$item['icon'] = $row->icon;
	    	$item['date'] = $row->date_insert;

                // Добавленные выходные значения о изображении
                if($row->img_id)
                {
                    $item['img'] = '/upload/records/'.$row->img_id.'.'.$row->img_ext;
                }
                else
                {
                    $item['img'] =  '';
                }
                $item['name_pic'] = $row->img_name;
                $item['width'] = $row->img_width;
                $item['height'] = $row->img_height;
	    	array_push($items,$item);
    	}
    }

 	if(isset($ar["assign"])) $tpl->assign($var,$items);

 	return "";
 }


/*
Функция получения описания рубрики раздела или публикации
*/
function get_info_tpl($ar,&$tpl)
{
 	global $db,$tpl,$site;
 	$var = isset($ar["assign"]) ? $ar["assign"] : "default";
 	$get_where = isset($ar["where"]) ? $ar["where"] : "category";
 	$id = isset($ar["id"]) ? $ar["id"] : 0;//Если параметр не указан - корневые категории

  $pic_table = PREFIX."uploaded_pics";

  if($get_where == 'pub' || $get_where == 'pubs'){
    $table = PREFIX."publications";
    $record_type = "pub"; 
  }elseif($get_where == 'catalog'){
    $table = PREFIX."shop_categs";
    $record_type = "catalog"; 
  }elseif($get_where == 'product'){
    $table = PREFIX."shop_products";
    $pic_table = PREFIX."shop_product_pics";
    $record_type = "product"; 
  }else{
    $table = PREFIX."categories";
    $record_type = "categ"; 
  }
  $row = $db->get_row("SELECT * FROM `".$table."` WHERE id = '".$id."' ", ARRAY_A);
  $ar = array();
  $rows = $db->get_results("SELECT * FROM `".$pic_table."` WHERE `record_id` = '".$id."' AND `record_type` = '".$record_type."' ORDER BY is_default desc, id_in_record, width desc");
  if($db->num_rows > 0){
    foreach($rows as $picrow){

      if(!isset($ar[$picrow->id_in_record])){
         $ar[$picrow->id_in_record] = array();
         if($picrow->title == 'top'){ 
           $row['img_top'] = '/upload/records/'.$picrow->id.".".$picrow->ext;
         }
         if($picrow->title == 'welcome'){ 
            $row['img_welcome'] = '/upload/records/'.$picrow->id.".".$picrow->ext;
         }

      }
      $ar[$picrow->id_in_record][] = array(
           'img' => '/upload/records/'.$picrow->id.".".$picrow->ext,
           'id' => $picrow->id,
           'width' => $picrow->width,
           'height' => $picrow->height, 
           'title' => $picrow->title, 
           'ext' => $picrow->ext 
      );
    }
  }
  $row['images'] = $ar;
  $tpl->assign($var,$row);
 	return "";
  $db->debug();
  echo "ddddf:".$id;
  exit;

}
function last_product_tpl($ar,&$tpl)
 {
 	global $db,$tpl,$site;
 	$var = isset($ar["assign"]) ? $ar["assign"] : "default";
 	$cat = isset($ar["cat"]) ? $ar["cat"] : 0;//Если параметр не указан - корневые категории
 	$qty = isset($ar["qty"]) ? $ar["qty"] : 5;
  $sortby = isset($ar["sortby"]) ? strtolower($ar["sortby"]) : "date_insert";
  $ar_sortby = array('name', 'name_short', 'price', 'barcode', 
    'bid_ya', 'date_insert', 'alias', 'views', 'rand()');
  if (!in_array($sortby, $ar_sortby)) {
    $sortby = "date_insert";
  }
  
 	$order = isset($ar["order"]) ? $ar["order"] : "desc";
 	if($order != 'desc') { $order = 'asc'; }
  $notid = isset($ar["notid"]) ? $ar["notid"] : "";
  $alias = isset($ar["alias"]) ? $ar["alias"] : "";

  // Входной параметр size (если не задан - то по умолчанию small)
  $what_size = "=";
  $size = isset($ar["size"]) ? $ar["size"] : "small";
  if($size == "big") { 
    $img_size = $site->vars["img_width_small"]; 
    $what_size = ">";
  }
  if($size == "small") { $img_size = $site->vars["img_width_small"]; } 
  if(isset($site->vars['img_width_mini']) && $size == "mini") { 
    $img_size = $site->vars['img_width_mini'];
  }

  if($size == "biggest") { 
    $img_size = $site->vars["img_width_big"]; 
    $what_size = ">";
  }

  if($size == "no") { $img_size = "no"; }
    
  if($sortby == "")
  {
    if(defined("SORT_PRODUCTS"))
		{
			$sortby = SORT_PRODUCTS;
			$order = "";
			if(ereg("_?desc$",$sortby))
			{
				$sortby = ereg_replace("_?desc$","",$sortby);
				$order = "desc";
			}
    }else{
      $sortby = "date_insert";
    }
	}

  if($notid != "") $notid_sql = " AND p.id not in ($notid)";
  else $notid_sql = "";

  if($cat !=0) $cat_sql = " AND scp.id_categ = '$cat' ";
  else $cat_sql = "";

  if($alias != "")
  {
  	$alias_sql = " AND c.alias='$alias' ";
  }else $alias_sql = "";

    if(!isset($site->vars['opt_ploschad'])){ $site->vars['opt_ploschad'] = 0; }
    if(!isset($site->vars['opt_spalni'])){ $site->vars['opt_spalni'] = 0; }
    if(!isset($site->vars['opt_vanny'])){ $site->vars['opt_vanny'] = 0; }

  // Оптимизированный запрос (теперь выбирает картинки вместе с продуктами 1 запросом)
  // Используется LEFT OUTER JOIN, поэтому даже если картинка не найдена, в соотв.полях будет просто NULL
  $width = isset($site->vars['resize_method']) ? $site->vars['resize_method'] : 'width';
  if($width != 'height') { $width = 'width'; }
  $query = "SELECT
                    p.name, p.alias, p.barcode, p.id, p.id as id_prod, p.date_insert, p.memo_short, 
                    p.price, p.price_spec, p.price_period, p.accept_orders, p.currency,
                    shop_product_pics.id AS img_id,
                    shop_product_pics.ext AS img_ext,
                    shop_product_pics.name AS img_name,
                    shop_product_pics.width AS img_width,
                    shop_product_pics.height AS img_height,
                    spo.value as ploschad,
                    spo2.value as spalni,
                    spo3.value as vanny,
                    op.title as ploschad_title,
                    op.after as ploschad_after,
                    op2.title as spalni_title,
                    op3.title as vanny_title
              FROM 
                    {$db->tables['shop_products']} p
                LEFT JOIN shop_product_options as spo on (
                    spo.id_product = p.id
                    AND spo.id_option = '".$site->vars['opt_ploschad']."')
                LEFT JOIN options as op on (
                    op.id = spo.id_option)
                LEFT JOIN shop_product_options as spo2 on (
                    spo2.id_product = p.id
                    AND spo2.id_option = '".$site->vars['opt_spalni']."')
                LEFT JOIN options as op2 on (
                    op2.id = spo2.id_option)
                LEFT JOIN shop_product_options as spo3 on (
                    spo3.id_product = p.id
                    AND spo3.id_option = '".$site->vars['opt_vanny']."')
                LEFT JOIN options as op3 on (
                    op3.id = spo3.id_option)
                LEFT OUTER JOIN {$db->tables['shop_product_pics']}
                    ON shop_product_pics.id_product = p.id
                    AND (shop_product_pics.{$width} {$what_size} '{$img_size}' ),
                    {$db->tables['shop_categ_products']} scp, 
                    {$db->tables['shop_categs']} c, 
                    {$db->tables['site_shop_categ']} sc,
                    {$db->tables['shop_product_pics']} spp
              WHERE 
                    p.id = scp.id_product 
                    and scp.id_categ=c.id
                    {$alias_sql} {$cat_sql}
                    and sc.id_site='{$site->id}' 
                    and sc.id_categ=c.id 
                    and p.active > '0' 
                    {$notid_sql}
             GROUP BY p.id 
             ORDER BY {$sortby} {$order}
             LIMIT 0, {$qty}";
     
  $rows = $db->get_results($query);//echo $query;

  $items = array();
  if(is_array($rows))
    foreach($rows as $row)
    {
    	if($site->page['page'] != 'product' || $site->page['id'] != $row->id)
    	{
	    	$item = array();
	    	$item['id'] = $row->id;
	    	$item['title'] = stripslashes($row->name);
	    	$item['link'] = (!MODE_REWRITE) ? _link(array('pub', $row->id)) : _link(array('pub', stripslashes($row->alias)));
	    	$item['alias'] = $row->alias;
	    	$item['date'] = $row->date_insert;
	    	$item['memo_short'] = $row->memo_short;
	    	$item['price'] = $row->price;
	    	$item['price_period'] = $row->price_period;
	    	$item['currency'] = $row->currency;
	    	$item['anons'] = $row->anons;
	    	$item['barcode'] = $row->barcode;

	    	$item['ploschad'] = $row->ploschad;
	    	$item['ploschad_title'] = $row->ploschad_title;
	    	$item['ploschad_after'] = $row->ploschad_after;
	    	$item['vanny'] = $row->vanny;
	    	$item['vanny_title'] = $row->vanny_title;
	    	$item['spalni'] = $row->spalni;
	    	$item['spalni_title'] = $row->spalni_title;
        $item['price_spec'] = $row->price_spec;
        $item['accept_orders'] = $row->accept_orders;
        if($img_size == "no")
        {
          $item['img'] = NULL;
        }else{
          $item['img'] = '/upload/products/'.$row->img_id.".".$row->img_ext;
        }
        $item['name_pic'] = $row->img_name;
        $item['width'] = $row->img_width;
        $item['height'] = $row->img_height;

        $item['options'] = array();
        
        
        $query = "SELECT spo.id_option, o.title, spo.value, o.after  
                    FROM shop_product_options spo 
                    left join options as o on (spo.id_option = o.id )
                    WHERE spo.id_product = '{$row->id_prod}' AND o.show_in_list = '1'
                    ORDER BY o.sort, o.title";
        $rows = $db->get_results($query, ARRAY_A);
        $ar = array();        
        if($rows && $db->num_rows > 0){
          foreach($rows as $row){
            $ar[$row['id_option']] = $row;
          }        
        }
         $item['options'] = $ar;            
        
	    	array_push($items,$item);
    	}
    }

 	$tpl->assign($var,$items);
 	return "";
 }

  function special_products_tpl($ar,&$tpl){
      global $db,$tpl,$site;
      $mode = isset($ar["mode"]) ? isset($ar["mode"]) : "spec";
      $var = isset($ar["assign"]) ? $ar["assign"] : "default";
      $cat = isset($ar["cat"]) ? $ar["cat"] : 0;//Anee ia?aiao? ia oeacai - ei?iaaua eaoaai?ee
      $qty = isset($ar["qty"]) ? $ar["qty"] : 5;
	  $sortby = isset($ar["sortby"]) ? $ar["sortby"] : "rand()";
      $order = isset($ar["order"]) ? $ar["order"] : "desc";
      $size = isset($ar["size"]) ? $ar["size"] : "small";
	 
	  $width = isset($site->vars['resize_method']) ? $site->vars['resize_method'] : '';
	  if($width != 'height'){ $width = 'width'; }
      if($size == "small") {
		$size = $site->vars['img_width_small'];
		$str_img = " spp.id_product = product.id AND spp.".$width." = '".$size."' AND spp.is_default = '1' AND ";
	  }elseif($size == "big"){
		$size = $site->vars['img_width_big'];
		$str_img = " spp.id_product = product.id AND (spp.".$width." <= '".$size."' AND spp.".$width." > '".$site->vars['img_width_small']."' AND spp.is_default = '1') AND ";
	  }elseif($size == "mini"){ 
		if(isset($site->vars['img_width_mini'])){ 
			$size = intval($site->vars['img_width_mini']);
			if($size > 0){
				$str_img = " spp.id_product = product.id AND spp.".$width." = '".$size."' AND spp.is_default = '1' AND ";
			}else{ $str_img = ''; }
		}else{ $size=0; $str_img = ''; } 
	  }else { $size = 0; $str_img = ''; }

      $where = $mode == 'new' ? 'product.f_new' : 'product.f_spec';
      $cat_sql = "";
      if($cat != 0) $cat_sql = " categ.id=$cat AND ";

     $query = "SELECT product.*, 
					spp.name as name_pic, 
					spp.id as pic_id, 
					spp.width, 
					spp.height, 
					spp.ext, 
					categ.id as categ_id, 
					categ.alias as categ_alias, 
					categ.title as categ_title
            FROM
                  ".$db->tables['shop_categs']." as categ,
                  ".$db->tables['shop_products']." as product,
                  ".$db->tables['shop_categ_products']." as scp,
                  ".$db->tables['site_shop_categ']." as ssc,
                  ".$db->tables['shop_product_pics']." as spp
				  
            WHERE
                  ssc.id_site = '".$site->vars['id']."' AND
                  ssc.id_categ = categ.id AND
                  categ.id = scp.id_categ AND
                  scp.id_product = product.id AND
                  $str_img
                  ".$cat_sql.$where." = 1 AND 
				  product.active = 1 
                  GROUP by product.id
                  ORDER by $sortby 
                  LIMIT $qty
    ";

    $rows = $db->get_results($query);
	//$db->debug();
	$where = $mode == 'new' ? 'p.f_new' : 'p.f_spec';
	$query2 = "SELECT 
					spo.id_product as id_product, 
					opt.title as title, 
					spo.value as value 
				FROM 
					shop_product_options as spo, 
					options as opt, 
					".$db->tables['shop_products']." as p 
				WHERE 
					spo.id_option=opt.id 
					AND spo.id_product = p.id
					AND $where = '1'
					AND opt.show_in_list = '1' 
				order by opt.sort
				";
    $rows2 = $db->get_results($query2);
	//$db->debug();
    $items = array();

    if(is_array($rows))
    foreach($rows as $row)
    {
         if($site->page['page'] != 'product' || $site->page['id'] != $row->id)
         {
              $item = array();
              $item['id'] = $row->id;
              $item['title'] = stripslashes($row->name);
              $item['link'] = (!MODE_REWRITE) ? _link(array('pub', $row->id)) : _link(array('pub', stripslashes($row->alias)));
              $item['date'] = $row->date_insert;
              $item['memo_short'] = $row->memo_short;
              $item['price'] = $row->price;
              $item['price_spec'] = $row->price_spec;
              $item['accept_orders'] = $row->accept_orders;
              $item['currency'] = $row->currency;
              $item['anons'] = $row->memo_short;
              $item['name_pic'] = $row->name_pic;
			  
			  foreach($rows2 as $row2){
				if($row2->id_product==$item['id']){
					if(isset($ar_opt)){
						$ar_opt[] = array(
							'title' => $row2->title,
							'value' => $row2->value
						);
					}else{
						$ar_opt = array();
						$ar_opt[] = array(
							'title' => $row2->title,
							'value' => $row2->value
						);
					}
					$item['options'] = $ar_opt;			
				}
			  }
			  unset($ar_opt);
			  
              if($row) $item['img'] = '/upload/products/'.$row->pic_id.'.'.$row->ext;
              else $item['img'] = $site->vars['template_path'].$site->vars['default_img_small'];

              array_push($items,$item);
         }
    }

      $tpl->assign($var,$items);

      return "";
 }

 function brands_tpl($ar,&$tpl)
 {
 	global $db,$site;

 	$var = isset($ar["assign"]) ? $ar["assign"] : "default";
 	$sortby = isset($ar["sortby"]) ? $ar["sortby"] : "";
 	$order = isset($ar["order"]) ? $ar["order"] : "desc";

 	$rows = $db->get_results("select id,brand as title, memo, ddate from ".PREFIX."shop_brand",ARRAY_A);

 	if(is_array($rows)) $tpl->assign($var,$rows);
 }

 function comments_tpl($ar)
 {
 	global $db,$site,$tpl,$lang;

 	$record = isset($ar["record"]) ? $ar["record"] : 0;
 	$id = isset($ar["id"]) ? $ar["id"] : $record;
 	$for = isset($ar["for"]) ? $ar["for"] : (($record != 0) ? "pub" : "");
 	$sortby = isset($ar["sortby"]) ? $ar["sortby"] : "";
 	$order = isset($ar["order"]) ? $ar["order"] : "desc";
 	$var = isset($ar["assign"]) ? $ar["assign"] : "default";
	$qty = isset($ar["qty"]) ? $ar["qty"] : (($for != "") ? 0 : 5);
	$page = $site->get_param("cpage",1);

	if($sortby == "")
	{
		if(defined("SORT_COMMENTS"))
		{
			$sortby = SORT_COMMENTS;
			$order = "";
			if(ereg("_?desc$",$sortby))
			{
				$sortby = ereg_replace("_?desc$","",$sortby);
				$order = "desc";
			}

		}
		else $sortby = "ddate";
	}

 	$where_ar = array();
 	if($record != 0) $where_ar[] = " record_id='$id' ";
 	if($for != "") $where_ar[] = " record_type='$for' ";
 	$where = implode(" and ",$where_ar);
 	if($where != "") $where = " where $where ";

 	$comments_qty = $db->get_var("select count(*) from ".$db->tables['comments']." $where");
 	$site->page['comments_qty'] = $comments_qty;
    $site->page['link'] = $lang->txt('link');
    //$tpl->assign("page",$comments_qty);


 	$query = "select * from ".$db->tables['comments']." $where order by $sortby $order ";
 	if($qty != 0) $query .= " LIMIT 0, $qty ";
 	else $query .= " LIMIT ".(($page-1)*ONPAGE).", ".ONPAGE." ";

 	$rows = $db->get_results($query);

 	$ar = array();
 	if(is_array($rows))
 	foreach($rows as $row)
 	{
        $site->admin_comments($row->id);
 		if($row->userid > 0){
        $user = $site->username($row->userid);
        $title = $user['login'];
        $usertitle = $user['user_title'];
        $ar_link = (MODE_REWRITE) ?  array('profile', $title) : array('profile', $row->user_id);
        $userlink = _link($ar_link);
        if(empty($title)){
          $userstatus = "<a href=\"".$userlink."\">".$lang->txt('user_registered')."</a>";
        }else{
          $userstatus = "<a href=\"".$userlink."\">".$title."</a>";
        }
      }else{
        $title = stripslashes($row->unreg_email);
        if($title == ''){ $title = $lang->txt('user_na'); }
        $userstatus = $lang->txt('user_guest');
        $userlink = '';
        $usertitle = '';
      }


 		$item = array(
        'id' => $row->id,
        'title' => $title,
        'comment' => stripslashes($row->comment_text),
        'userstatus' => $userstatus,
        'usertitle' => $usertitle,
        'ddate' => $site->ddate($row->ddate),
        'ttime' => $site->ttime($row->ddate),
        'userid' => $row->userid,
        'userlink' => $userlink
      );
 		if($row->record_type == "pub")
 		$query = "select id, name, alias from ".PREFIX."publications where id='".$row->record_id."'";
 		elseif($row->record_type == "product")
 		$query = "select id, name, alias from ".PREFIX."shop_products where id='".$row->record_id."'";
 		elseif($row->record_type == "catalog")
 		$query = "select id, name, alias from ".PREFIX."shop_categs where id='".$row->record_id."'";
 		elseif($row->record_type == "category")
 		$query = "select id, name_main as name, alias from ".PREFIX."categories where id='".$row->record_id."'";
 		$rec = $db->get_row($query,ARRAY_A);
 		$rec['link'] = (MODE_REWRITE) ? _link(array($rec["alias"])) : _link(array($row->record_type,$row->id));


 		if($rec) $item["record"] = $rec;//print_r($item);
 		$ar[] = $item;
 	}

 	$tpl->assign("page",$site->page);
 	$tpl->assign($var,$ar);

 	return "";
 }

function basket_tpl($ar)
{
	global $site,$tpl;

	$var = isset($ar["assign"]) ? $ar["assign"] : "default";
	$qty = isset($ar["qty"]) ? $ar["qty"] : 5;

	$ar = array();
	$ii = 1;
	$summ = 0;
	if(!isset($_COOKIE["basket"]) || !is_array($_COOKIE["basket"]))
	{$tpl->clear_assign($var);return ;}
	foreach($_COOKIE["basket"] as $k => $v)
	{
       $v=intval($v);
       if($v > 0){
         $prod = $site->product_by_id($k, $v, $ii);
         if($prod){
           $ar[] = $prod;
           $ii++;
           $summ += $prod['qty']*$prod['price'];
         }
       }
	 }

	 $basket["basket_list"] = $ar;
	 $basket["basket_qty"] = count($ar);
	 $basket["basket_summ"] = $summ;
	 $tpl->assign($var,$basket);
}

function links_list($ar)
{
	global $db;
	$links_ar = isset($ar['from']) ? $ar['from'] : array();
	$delim = isset($ar['delim']) ? $ar['delim'] : '|';

	$links = array();

	foreach($links_ar as $k => $v)
	{
		$links[] = "<a href=\"".$v['link']."\">".$v['title']."</a>";
	}
	$str = implode(" $delim ",$links);
	return $str;
}

function sort_options($ar)
{
	global $site;
	$sorts = array("name"=>"Названию","price" => "Цене","date_insert" => "Дате добавления");
    $sort_opts = array();

     foreach($sorts as $k => $v)
    {
    	$sort = $site->get_param("sort","name");
    	$price_from = $site->get_param("price_from",0);
    	$price_to = $site->get_param("price_to",0);
    	$brand = $site->get_param("brand",0);

    	$lnk = array("catalog",((MODE_REWRITE) ? $site->uri['slug'] : $site->uri['id'] ),"sort",$k);
    	if(isset($site->uri["show"]))
    	{ $lnk[] ="show"; $lnk[] = $site->uri["show"]; }
    	if($price_from != 0)
    	{ $lnk[] ="price_from"; $lnk[] = $price_from; }
    	if($price_to != 0 )
    	{ $lnk[] ="price_to"; $lnk[] = $price_to; }
    	if($brand != 0 )
    	{ $lnk[] ="brand"; $lnk[] = $brand; }

    	$link = _link($lnk);
    	if($sort == $k)
    	$links[] = $v;
    	else $links[] = "<a href=\"".$link."\">".$v."</a>";
    }
    $str = implode(" | ",$links);
	return $str;
}

function filters($ar)
{
	global $site;
	$sorts = array("all" => "Все","sale"=>"В продаже");
    $sort_opts = array();

    foreach($sorts as $k => $v)
    {
    	$show = $site->get_param("show","all");
    	$price_from = $site->get_param("price_from",0);
    	$price_to = $site->get_param("price_to",0);
    	$brand = $site->get_param("brand",0);

    	$lnk = array("catalog",((MODE_REWRITE) ? $site->uri['slug'] : $site->uri['id'] ),"show",$k);
    	if(isset($site->uri["sort"]))
    	{ $lnk[] ="sort"; $lnk[] = $site->uri["sort"]; }
    	if($price_from != 0)
    	{ $lnk[] ="price_from"; $lnk[] = $price_from; }
    	if($price_to != 0 )
    	{ $lnk[] ="price_to"; $lnk[] = $price_to; }
    	if($brand != 0 )
    	{ $lnk[] ="brand"; $lnk[] = $brand; }

    	$link = _link($lnk);
    	if($show == $k)
    	$links[] = $v;
    	else $links[] = "<a href=\"".$link."\">".$v."</a>";
    }
    $str = implode(" | ",$links);
	return $str;
}

function seo_links_tpl($ar,&$tpl)
{
	global $db,$site;
	$page = ifset($ar['page'],'main');
	$var = isset($ar["assign"]) ? $ar["assign"] : "default";

	$results = $db->get_results("select s.* from ".PREFIX."seo_links s,".PREFIX."seo_link_sites sl where s.page='$page' and sl.id_link=s.id and sl.id_site='".$site->id."' order by rand() limit 1",ARRAY_A);
	$items = array();
	if(is_array($results))
	foreach($results as $row)
	{
		//$item = array();
		$items[] = $row;
	}

	$tpl->assign($var,$items);

	return ;
}

function categs_as_ul($parent,$expand=0)
{
	global $db,$site;
	$str = "";
	if($site->page['page'] == "category") $id = $site->page['id'];

	if($site->page['page'] == "pub")
	{
		$id = $site->page['id'];
		$id = $db->get_var("SELECT c.id FROM ".$db->tables["publications"]." p, ".$db->tables["pub_categs"]." pc, ".$db->tables["categories"]." c where p.id='$id' AND pc.id_pub=p.id and c.id=pc.id_categ order by c.id limit 1");//echo $id,"<br />";
	}


	$rows = $db->get_results("SELECT c.* from ".$db->tables["categories"]." c, ".$db->tables["site_categ"]." sc where c.id_parent='$parent' and sc.id_categ=c.id and c.active='1' and sc.id_site='{$site->id}' order by c.sort, c.name_main");
	if(is_array($rows))
	{
		$str .= "<ul>\n";
		foreach($rows as $row)
		{
			$link = (MODE_REWRITE) ? _link(array("category",$row->alias)) : _link(array("category",$row->id));
			$str .= "<li><a href=\"{$link}\">{$row->name_main}</a>";
            if($id == $row->id /*|| isoneofparents($id,$row->id)*/)
            $str .= categs_as_ul($row->id);
            else if( isoneofparents($id, $row->id)) $str .= categs_as_ul($row->id);
			$str .= "</li>\n";
		}
		$str .= "</ul>\n";
	}

	return $str;
}

function catalogs_as_ul($parent,$expand=0)
{
	global $db,$site;
	$str = "";
	if($site->page['page'] == "catalog") $id = $site->page['id'];

	if($site->page['page'] == "product")
	{
		$id = $site->page['id'];
		$id = $db->get_var("SELECT c.id FROM ".$db->tables["shop_products"]." p, ".$db->tables["shop_categ_products"]." cp, ".$db->tables["shop_categs"]." c where p.id='$id' AND cp.id_product=p.id and c.id=cp.id_categ order by c.id limit 1");//echo $id,"<br />";
	}

	$rows = $db->get_results("SELECT c.* from ".$db->tables["shop_categs"]." c, ".$db->tables["site_shop_categ"]." sc where c.id_parent='$parent' and sc.id_categ=c.id and c.active='1' and sc.id_site='{$site->id}' order by c.sort, c.name");
	if(is_array($rows))
	{
		$str .= "<ul>\n";
		foreach($rows as $row)
		{
			$link = (MODE_REWRITE) ? _link(array("category",$row->alias)) : _link(array("category",$row->id));
			$str .= "<li><a href=\"{$link}\">{$row->name}</a>";
            $str .= "</li>\n";if($id == $row->id /*|| isoneofparents($id,$row->id)*/)
            $str .= catalogs_as_ul($row->id);
            else if( isoneofparents($id, $row->id,"catalog")) $str .= catalogs_as_ul($row->id);

		}
		$str .= "</ul>\n";
	}

	return $str;
}

function categ_tree_menu($ar,&$tpl)
{
	global $db,$site;
	//$start = isset($ar['root']) ? $ar['root'] : 0;
	$start = isset($ar['id']) ? $ar['id'] : 0;
	$str = "";
	return categs_as_ul($start);
}

function catalog_tree_menu($ar,&$tpl)
{
	global $db,$site;
	//$start = isset($ar['root']) ? $ar['root'] : 0;
	$start = isset($ar['id']) ? $ar['id'] : 0;
	$str = "";
	return catalogs_as_ul($start);
}

function audio_player($ar,&$tpl)
{
	if(!isset($ar["url"])) return ;
	$url = $ar["url"];
	$flashVars = "soundFile=".$url."&initialVolume=30";
	//if($ar["title"]) $flashVars .= "";

	return '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab" type="application/x-shockwave-flash" width="290" height="24" id="audioplayer"><param name="movie" value="/module/audioplayer/player.swf" /><param name="FlashVars" value="' . $flashVars . '" /><param name="quality" value="high" /><param name="menu" value="false" />
	<embed src="/module/audioplayer/player.swf" width="290" height="24" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" name="clockMovie" flashvars="' . $flashVars . '" allowscriptaccess="always"></embed>
	</object>';
	/*return '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab" type="application/x-shockwave-flash" width="290" height="24" id="audioplayer"><param name="movie" value="/module/audioplayer/mp3player.swf" /><param name="FlashVars" value="' . $flashVars . '" /><param name="quality" value="high" /><param name="menu" value="false" />
	<embed src="/module/audioplayer/mp3player.swf" width="290" height="24" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" name="clockMovie" flashvars="' . $flashVars . '" allowscriptaccess="always"></embed>
	</object>';*/
}

function video_player($ar,&$tpl)
{
	if(!isset($ar["url"])) return ;
	$url = $ar["url"];

	//$flashVars = "file=".base64_encode($url);
	$flashVars = "file=".$url;

	/*$code = '
		<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab" type="application/x-shockwave-flash" width="290" height="24" id="audioplayer"><param name="movie" value="/module/flvplayer/player.swf" /><param name="FlashVars" value="' . $flashVars . '" /><param name="quality" value="high" /><param name="menu" value="false" />
	<embed src="/module/flvplayer/player.swf" width="290" height="24" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" name="clockMovie" flashvars="' . $flashVars . '" allowscriptaccess="always"></embed>
	</object>
	';*/
	$code = '<object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="425" height="355">
		<param name="movie" value="/module/flvplayer/player.swf" />
		<param name="allowfullscreen" value="true" />
		<param name="allowscriptaccess" value="always" />
		<param name="flashvars" value="'.$flashVars.'" />
		<object type="application/x-shockwave-flash" data="player.swf" width="425" height="355">
			<param name="movie" value="/module/flvplayer/player.swf" />
			<param name="allowfullscreen" value="true" />
			<param name="allowscriptaccess" value="always" />
			<param name="flashvars" value="' . $flashVars . '" />
			<embed src="/module/flvplayer/player.swf" width="425" height="355" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" name="clockMovie" flashvars="' . $flashVars . '" allowscriptaccess="always"></embed>
		</object>
	</object>';

	return $code;
}

function photo_links(){
  global $site;
  $dp = isset($site->page['photo_links']) ? $site->page['photo_links'] : '';
  return $dp;
}
function dop_vertical(){
  global $site;
  $dp = isset($site->page['dop_vertical']) ? $site->page['dop_vertical'] : '';
  return $dp;
}
function def(){
  global $site;
  $dp = isset($site->page['def']) ? $site->page['def'] : '';
  return $dp;
}
function defpic(){
  global $site;
  $dp = isset($site->page['defpic']) ? $site->page['defpic'] : '';
  return $dp;
}
function defext(){
  global $site;
  $dp = isset($site->page['defext']) ? $site->page['defext'] : '';
  return $dp;
}

function rest_images($ar, &$tpl)
{
   	global $db,$site;
  	$id = isset($ar['id']) ? $ar['id'] : $site->page['id'];
  	$qty = isset($ar['qty']) ? $ar['qty'] : 1;
  	$width = isset($ar['width']) ? $ar['width'] : 'small';
  	if(isset($ar['size']) && !isset($ar['width'])) $width = $ar['size'];
  	$start = isset($ar['start']) ? $ar['start'] : 1;
  	$var = isset($ar["assign"]) ? $ar["assign"] : "";
    $where = isset($ar['where']) ? $ar['where'] : $site->page['record_type'];
    if($where == "category") $where = "categ";
    $size_big = $site->vars['img_width_big'];
  	$size_small = $site->vars['img_width_small'];
  	if($width == 'big') $size = $size_big;
  	elseif($width == 'small') $size = $size_small;
  	elseif(is_numeric($width)) $size = intval($width);
  	else $size = 0;

    if($where == "product") $path = "products";
    else $path = "records";

    $tables = array('pub' => PREFIX.'publications', 'categ' => PREFIX.'categories', 'catalog' => PREFIX.'shop_categs', 'product' => PREFIX.'shop_products');

    if($where == 'categ') $namefield = "name_main";
    else $namefield = "name";

    $str = "";

  	$ar = array();

  	if($where == "product") { $query = "SELECT i.*, i.name as title FROM ".$db->tables["shop_product_pics"]." i, ".$db->tables["shop_product_pics"]." i2,
        ".$db->tables["shop_product_pics"]." i3
        WHERE i.id_product = '".$id."' AND ".( ($size == 0 ) ? "(i.width > i3.width or i.height >= i3.height)" : "(i.width = ".$size." or i.height = ".$size.")")."
        AND (i2.width = '".$size_small."' or i2.height = '".$size_small."')
        AND (i3.width = '".$size_big."' or i3.height = '".$size_big."')
        AND i2.id_in_product=i.id_in_product
        AND i2.id_in_product=i3.id_in_product
        AND i.id_product = i2.id_product
        AND i3.id_product = i2.id_product
        group by i.id_in_product order by i.is_default desc,  i.id limit $start, $qty";
        $query2 = "SELECT i2.*, i2.name as title FROM ".$db->tables["shop_product_pics"]." i, ".$db->tables["shop_product_pics"]." i2,
        ".$db->tables["shop_product_pics"]." i3
        WHERE i.id_product = '".$id."' AND ".( ($size == 0 ) ? "(i.width > i3.width or i.height >= i3.height)" : "(i.width = ".$size." or i.height = ".$size.")")."
        AND (i2.width = '".$size_small."' or i2.height = '".$size_small."')
        AND (i3.width = '".$size_big."' or i3.height = '".$size_big."')
        AND i2.id_in_product=i.id_in_product
        AND i2.id_in_product=i3.id_in_product
        AND i.id_product = i2.id_product
        AND i3.id_product = i2.id_product
        group by i.id_in_product order by i.is_default desc,  i.id limit $start, $qty";}
  	else { $query = "SELECT i.* FROM ".$db->tables["uploaded_pics"]." i,
	  	".$db->tables["uploaded_pics"]." i2, ".$db->tables["uploaded_pics"]." i3
        WHERE i.record_id = '".$id."' AND i.record_type = '".$where."'
        AND (i.width = '".$size."' OR i.height = '".$size."')
        AND (i2.width >= i3.width OR i2.height >= i3.height)
        AND (i3.width = '".$size_big."' OR i3.height = '".$size_big."')
        AND i.id_in_record = i2.id_in_record
        AND i.record_id=i2.record_id
        AND i3.record_id = i2.record_id
        AND i3.id_in_record = i2.id_in_record
        AND i3.id!=i2.id
        group by i.id_in_record order by i.is_default desc, i.title desc, i.id limit $start, $qty";
        $query2 = "SELECT i2.* FROM ".$db->tables["uploaded_pics"]." i,
	  	".$db->tables["uploaded_pics"]." i2, ".$db->tables["uploaded_pics"]." i3
        WHERE i.record_id = '".$id."' AND i.record_type = '".$where."'
        AND (i.width = '".$size."' OR i.height = '".$size."')
        AND (i2.width >= i3.width OR i2.height >= i3.height)
        AND (i3.width = '".$size_big."' OR i3.height = '".$size_big."')
        AND i.id_in_record = i2.id_in_record
        AND i.record_id=i2.record_id
        AND i3.record_id = i2.record_id
        AND i3.id_in_record = i2.id_in_record
        AND i3.id!=i2.id
        group by i.id_in_record order by i.is_default desc, i.title desc, i.id limit $start, $qty";
        }
        $rows = $db->get_results($query);
        $rows2 = $db->get_results($query2);

        $i = 0;

        if(is_array($rows))
        foreach($rows as $row)
        {
	        $row2 = $rows2[$i];
	        if($row2)
	        {
		        if($where == "product")
		        $row3 = $db->get_row("SELECT i.*,  p.alias, p.id as record FROM ".$db->tables["shop_product_pics"]." i, ".$db->tables["shop_product_pics"]." i2,
		        ".$db->tables["shop_products"]." p
		        WHERE i.id_product = '".$id."' AND i.id_in_product='".$row->id_in_product."'
		        AND (i2.width = '".$size_big."' or i2.height = '".$size_big."')
                AND (i.width >= i2.width or i.height > i2.height )
		        AND p.id=i.id_product
                AND i.id_product = i2.id_product
                AND i.id_in_product = i2.id_in_product
		        order by width desc, height desc limit 1");
		        else $row3 = $db->get_row("SELECT i.*,  r.alias, r.id as record FROM ".$db->tables["uploaded_pics"]." i, ".$db->tables["uploaded_pics"]." i2,
		        ".$tables[$where]." r
		        WHERE i.record_id = '".$id."' AND i.record_type = '".$where."'
		        AND (i2.width = '".$size_big."' or i2.height = '".$size_big."')
                AND (i.width >= i2.width or i.height > i2.height )
		        AND r.id=i.record_id
                AND i.record_id = i2.record_id
                AND i.id_in_record = i2.id_in_record
		        AND i.id_in_record='".$row->id_in_record."'
		        order by width desc, height desc limit 1");

		        if($var == "")
		        {

			        $str .= "<a href=\"/upload/$path/".$row2->id.".".$row2->ext."\" rel=\"prettyPhoto[gallery_".$id."]\"><img src=\"/upload/$path/".$row->id.".".$row->ext."\" width=\"100\" height=\"70\" border=\"0\" alt=\"".$row->title."\" /></a>";
		        }
		        else
		        {
		        	$item = array();
		        	$item["id"] = $row->id;
		        	$item["link"] = $item["src"] = "/upload/$path/".$row->id.".".$row->ext;
		        	$item["img_big"] = $item["linkbiggest"] = "/upload/$path/".$row3->id.".".$row3->ext;
		        	$item["linkbig"] = "/upload/$path/".$row2->id.".".$row2->ext;
		        	$item["linkrecord"] = (MODE_REWRITE) ? _link(array($where,$row2->alias)) : _link(array($where,$row2->record));
		        	$item["alt"] = $item["title"] = $row2->title;
		        	$item["record"] = $row2->rec_title;
		        	$item["default"] = $row->is_default;

		        	$ar[] = $item;
		        }
		        $i++;
	        }
        }

        if($var =="" ) return $str;
        else $tpl->assign($var,$ar);
}

function defimage($ar,&$tpl)
{
	global $db,$site;
  	$id = isset($ar['id']) ? $ar['id'] : $site->page['id'];
  	$var = isset($ar['assign']) ? $ar['assign'] : '';
  	$where = isset($ar['where']) ? $ar['where'] : $site->page['record_type']; //echo $where;
  	$width = isset($ar['size']) ? $ar['size'] : 'big';
  	if($where == "category") $where = "categ";
  	$size_big = $site->vars['img_width_big'];
  	$size_small = $site->vars['img_width_small'];
  	if($width == 'big') $size = $size_big;
  	elseif($width == 'small') $size = $size_small;
  	elseif(is_numeric($width)) $size = intval($width);
  	else $size = 0;

  	if($where == "product") $path = "products";
    else $path = "records";

  	if($where =="product")
  	{
  		$tbl =  ($size == 0 ) ? "i2" : "i";
  		$query = "SELECT $tbl.*, $tbl.name as title FROM ".$db->tables["shop_product_pics"]." i, ".$db->tables["shop_product_pics"]." i2,
        ".$db->tables["shop_product_pics"]." i3
        WHERE i.id_product = '".$id."' AND ".( ($size == 0 ) ? "(i.width > i3.width or i.height >= i3.height)" : "(i.width = ".$size." or i.height = ".$size.")")."
        AND (i2.width = '".$size_small."' or i2.height = '".$size_small."')
        AND (i3.width = '".$size_big."' or i3.height = '".$size_big."')
        AND i2.id_in_product=i.id_in_product
        AND i2.id_in_product=i3.id_in_product
        AND i.id_product = i2.id_product
        AND i3.id_product = i2.id_product
        group by i.id_in_product order by i.is_default desc,  i.id limit 1";
      $row = $db->get_row($query);
  	if($row) $src = "/upload/products/".$row->id.".".$row->ext;
  	}
  	else
  	{
  		$tbl =  ($size == 0 ) ? "i2" : "i";
  		$query = "SELECT $tbl.*FROM ".$db->tables["uploaded_pics"]." i,
	  	".$db->tables["uploaded_pics"]." i2, ".$db->tables["uploaded_pics"]." i3
        WHERE i.record_id = '".$id."' AND i.record_type = '".$where."'
        AND ".( ($size == 0 ) ? "(i.width = ".$size_small." or i.height = ".$size_small.")" : "(i.width = ".$size." or i.height = ".$size.")")."
        AND (i2.width >= i3.width OR i2.height >= i3.height)
        AND (i3.width = '".$size_big."' OR i3.height = '".$size_big."')
        AND i.id_in_record = i2.id_in_record
        AND i.record_id=i2.record_id
        AND i3.record_id = i2.record_id
        AND i3.id_in_record = i2.id_in_record
        AND i3.id!=i2.id
        group by i.id_in_record order by i.is_default desc, i.title desc, i.id limit 1";
      $row = $db->get_row($query);//echo $query;
	  	if($row) $src = "/upload/records/".$row->id.".".$row->ext;
  	}
  	if($var != "") {
	  	$item = array();
       	$item["id"] = $row->id;
       	$item["link"] = $item["src"] = "/upload/$path/".$row->id.".".$row->ext;
       	//$item["img_big"] = $item["linkbiggest"] = "/upload/$path/".$row2->id.".".$row2->ext;
       	//$item["linkbig"] = "/upload/$path/".$row2->id.".".$row2->ext;
       	//$item["linkrecord"] = (MODE_REWRITE) ? _link(array($where,$row2->alias)) : _link(array($where,$row2->record));
       	$item["alt"] = $item["title"] = $row->title;
       	//$item["record"] = $row2->rec_title;
       	//$item["default"] = $row->is_default;
	  	$tpl->assign($var,$item);
   }
  	else return $src;
}

function random_photos($ar, &$tpl)
{
	global $site,$db;
	$where = ifset($ar['where'],'pub');
	$id = ifset($ar['id'],0);
}

function list_images_tpl($query, &$items)
{
	global $db;
	$rows = $db->get_results($query);
	$tables = array('pub' => 'publications', 'categ' => 'categories', 'catalog' => 'shop_categs', 'product' => 'shop_products');

	if($rows)
	foreach($rows as $row)
	{
		$item = array();
		if($where == "product") $item["src"] = "/upload/products/".$row->id.".".$row->ext;
		else $item["src"] = "/upload/records/".$row->id.".".$row->ext;
		$item["alt"] = $row->name;
		$record_id = ($where =='product') ? $row->id_product : $row->record_id;
		$alias = $db->get_var("Select alias from ".PREFIX.$tables[$where]." where id='".$record_id."'" );
		$item['record_id'] = $record_id;
		$item['link'] = (MODE_REWRITE) ? _link(array($where,$alias)) : _link($where,$id);

		$items[] = $item;
	}
}

function random_img($ar, &$tpl)
{
	global $site,$db;
	$where = ifset($ar['from'],'pub');
	$id = ifset($ar['id'],0);
	$qty = ifset($ar['qty'],0);
	$size = ifset($ar['size'],'small');
	$alias = ifset($ar['alias'],'');
	$var = ifset($ar['assign'],'default');
	$tables = array('pub' => 'publications', 'categ' => 'categories', 'catalog' => 'shop_categs', 'product' => 'shop_products');
	if($size == 'small') $size = $site->vars['img_width_small'];
	else if($size == 'big') $size = $site->vars['img_width_big'];

	$size_big = $site->vars['img_width_big'];

	if($where == "product")
	{
                if($alias!='') $id = $db->get_var("SELECT id FROM ".$db->tables["shop_product_pics"]." WHERE alias='$alias'");

                $query = "SELECT p.* from ".$db->tables["shop_product_pics"]." p, ".$db->tables["shop_products"]." sp WHERE (p.width='$size' OR p.height='$size')  AND p.id_product = sp.id  ";
		if($id != 0) $query .= " AND id_product = '$id' ";
	}
	else
	{
                if($alias!='') {

                $id = $db->get_var("SELECT id FROM ".$tables[$where]." WHERE alias='$alias'");
                }

                $query = "SELECT p.* from ".$db->tables["uploaded_pics"]." p, ".PREFIX.$tables[$where]." r  WHERE (p.width='$size' OR p.height='$size') AND p.record_type='$where' AND r.id=p.record_id ";
		if($id != 0) $query .= " AND record_id = '$id' ";
	}

	$query .= " ORDER by rand()";
	if($qty) $query .= " LIMIT $qty";//echo $query;
	$items = array();
	//list_images_tpl($query,$items);
	$rows = $db->get_results($query);
	$items = array();
	if($rows)
	foreach($rows as $row)
	{
		$item = array();
		if($where == "product") $item["src"] = "/upload/products/".$row->id.".".$row->ext;
		else $item["src"] = "/upload/records/".$row->id.".".$row->ext;
		$item["alt"] = $row->name;
		$record_id = ($where =='product') ? $row->id_product : $row->record_id;
		$alias = $db->get_var("Select alias from ".PREFIX.$tables[$where]." where id='".$record_id."'" );
		$item['record_id'] = $record_id;
		$item['link'] = (MODE_REWRITE) ? _link(array($where,$alias)) : _link($where,$id);

		if($where == "product")
		{
			$big_img = $db->get_row("SELECT * FROM ".$db->tables["shop_product_pics"]." WHERE id_in_product='".$row->id_in_product."' AND (width>='$size_big' OR height>='$size_big') AND id_product='".$row->id_product."' order by width desc, height desc limit 1");
			$item['big_img'] = '/upload/products/'.$big_img->id.".".$big_img->ext;
		}
		else
		{
			$big_img = $db->get_row("SELECT * FROM ".$db->tables["uploaded_pics"]." WHERE id_in_record='".$row->id_in_record."' AND (width>='$size_big' OR height>='$size_big') AND record_id='".$row->record_id."' AND record_type='$where' order by width desc, height desc limit 1");
			$item['big_img'] = '/upload/records/'.$big_img->id.".".$big_img->ext;
		}


		$items[] = $item;
	}
	$tpl->assign($var,$items);
}

function last_img($ar, &$tpl)
{
	global $site,$db;
	$where = ifset($ar['from'],'pub');
	$id = ifset($ar['id'],0);
	$qty = ifset($ar['qty'],0);
	$size = ifset($ar['size'],'small');
	$var = ifset($ar['assign'],'default');
	$tables = array('pub' => 'publications', 'categ' => 'categories', 'catalog' => 'shop_categs', 'product' => 'shop_products');
	if($size == 'small') $size = $site->vars['img_width_small'];
	else if($size == 'big') $size = $site->vars['img_width_big'];

	$size_big = $site->vars['img_width_big'];

	if($where == "product")
	{
		$query = "SELECT p.* from ".$db->tables["shop_product_pics"]." p, ".$db->tables["shop_products"]." sp WHERE (p.width='$size' OR p.height='$size')  AND p.id_product = sp.id AND sp.active='1' ";
		if($id != 0) $query .= " AND p.id_product = '$id' ";
	}
	else
	{
		$query = "SELECT p.* from ".$db->tables["uploaded_pics"]." p, ".PREFIX.$tables[$where]." r  WHERE (p.width='$size' OR p.height='$size') AND p.record_type='$where' AND r.id=p.record_id AND r.active='1' ";
		if($id != 0) $query .= " AND p.record_id = '$id' ";
	}

	$query .= " ORDER by id desc";
	if($qty) $query .= " LIMIT $qty";//echo $query;
	$items = array();
	//list_images_tpl($query,$items);
	$rows = $db->get_results($query);
	$items = array();
	foreach($rows as $row)
	{
		$item = array();
		if($where == "product") $item["src"] = "/upload/products/".$row->id.".".$row->ext;
		else $item["src"] = "/upload/records/".$row->id.".".$row->ext;
		$item["alt"] = $row->name;
		$record_id = ($where =='product') ? $row->id_product : $row->record_id;
		$alias = $db->get_var("Select alias from ".PREFIX.$tables[$where]." where id='".$record_id."'" );
		$item['record_id'] = $record_id;
		$item['link'] = (MODE_REWRITE) ? _link(array($where,$alias)) : _link($where,$id);

		if($where == "product")
		{
			$big_img = $db->get_row("SELECT * FROM ".$db->tables["shop_product_pics"]." WHERE id_in_product='".$row->id_in_product."' AND (width>='$size_big' OR height>='$size_big') AND id_product='".$row->id_product."' order by width desc, height desc limit 1");
			$item['big_img'] = '/upload/products/'.$big_img->id.".".$big_img->ext;
		}
		else
		{
			$big_img = $db->get_row("SELECT * FROM ".$db->tables["uploaded_pics"]." WHERE id_in_record='".$row->id_in_record."' AND (width>='$size_big' OR height>='$size_big') AND record_id='".$row->record_id."' order by width desc, height desc limit 1");
			$item['big_img'] = '/upload/records/'.$big_img->id.".".$big_img->ext;
		}


		$items[] = $item;
	}
	$tpl->assign($var,$items);
}

function list_OLDfiles($ar,&$tpl)
{
	global $site,$db;
	$id = ifset($ar["id"],$site->page["id"]);
	$type = ifset($ar["type"],"pub");
	$var = ifset($ar["assign"],"default");
	$results = $db->get_results("SELECT * from ".$db->tables["uploaded_files"]." WHERE record_id='$id' AND record_type='$type'",ARRAY_A);//print_r($results);
	if(!is_array($results))
	{return ;}
	$tpl->assign($var,$results);
	return;
}

function selectinput($name,$values,$sel=0,$onchange="")
{
	if(!is_array($values)) return;
	if($onchange != "") $onchange="onchange=\"$onchange\"";

	$str = "";
	$str .=  "<select name=\"$name\" $onchange>\n";

	foreach($values as $k=>$v)
	{
		$str .= "<option value=\"$k\"".(($k==$sel) ? " selected=\"selected\"" : "").">$v</option>\n";
	}
	$str .= "</select>\n";
	return $str;
}

function ifset(&$var,$default)
{
	if(isset($var)) return $var;
	return $default;
}

  function send_headers_tpl($ar,&$tpl){
   	global $site;
   	$str = "";

  	$str .= "<link rel=\"icon\" href=\"{$site->tpl}favicon.ico\" type=\"image/x-icon\" />\n";
  	$str .= "<link rel=\"shortcut icon\" href=\"{$site->tpl}favicon.ico\" type=\"image/x-icon\" />\n";

    $str .= "<script>\nvar tpl = '{$site->tpl}';\n</script>\n";
  	$str .= "<script src=\"/module/js/script.js\"></script>\n";
  	$str .= "
        <script type=\"text/javascript\" src=\"/module/js/jquery-1.4.2.min.js\"></script>
        <script type=\"text/javascript\" src=\"/module/js/jquery.jcarousel.pack.js\"></script>
        <script type=\"text/javascript\" src=\"/module/js/jquery.fancybox-1.2.1.pack.js\"></script>
        <link type=\"text/css\" href=\"/module/css/gallery_style".$site->vars['gallery'].".css\" rel=\"stylesheet\" />
    ";
  	if(defined("SCRIPT_RATING")) {
	  	$str .= "<script src=\"/module/rating/main.js\"></script>\n";
  	}
    
    if(defined("SCRIPT_COMMENT")) { define("SCRIPT_AJAX",1); $str .= "<script src=\"/module/js/comment.js\"></script>\n"; }
    if(defined("SCRIPT_AJAX")) $str .= "<script src=\"/module/js/ajax.js\"></script>\n";
    if(defined("SCRIPT_BASKET")) $str .= "<script src=\"/module/js/basket.js\"></script>\n";
    if(defined("SCRIPT_POPUP")) $str .= "<script src=\"/tpl/default/popup.js\"></script>\n";
    
    if(defined("SCRIPT_KEYBOARD")) {
      $str .= "<script src=\"/module/dropdown_menu/ie-hover.js\"></script>\n";
      $str .= "<script src=\"/module/keyboard/keyboard.js\"></script>\n";
      $str .= "<script charset=\"utf-8\" id=\"injection_graph_func\" src=\"/module/dropdown_menu/injection_graph_func.js\"></script>\n";
      $str .= "<link href=\"/module/keyboard/default.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
    }
    
    if(defined("STYLE_DROPDOWN")) $str .= "<link href=\"/module/dropdown_menu/default.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
    if(defined("RSS_HEAD")) $str .= $tpl->fetch("pages/rss_head.html");
   

    return $str;
  }


  
  /* ok */
  function encode_str($str)
  {
    if (!defined('AUTHKEY')) { define('AUTHKEY', 'simpla.es'); }
    return md5(md5($str).'-'.md5(AUTHKEY));
  }
  
  // new function for uploads images in tpl from site_vars
  function show_var_img_tpl($ar,&$tpl)
  {
    global $db, $site;
    $qty = ifset($ar["qty"], 1);
    $border = ifset($ar["border"], '0');
    $align = ifset($ar["align"], '');
    $from = ifset($ar["from"], "");
    $width = ifset($ar["width"], 0);
    $height = ifset($ar["height"], 0);
    $default = ifset($ar["default"], "");
    $alt = ifset($ar["alt"], "");
    $title = ifset($ar["alt"], "");
    $set_class = ifset($ar["class"], "");
   	$var = ifset($ar['assign'],false);

    $ar = array();
    if(!empty($from)){
      $rows = $db->get_results("SELECT i.* 
        FROM ".$db->tables['uploaded_pics']." i
        LEFT JOIN ".$db->tables['site_vars']." v on (v.id = i.record_id AND i.record_type = 'var') 
        WHERE v.`name` LIKE '".$from."' 
        ORDER BY i.is_default desc, i.id_in_record, i.title, i.width desc LIMIT 0, ".$qty." ");
      if($db->num_rows > 0){
        foreach($rows as $row){
          $new_title = empty($row->title) ? htmlspecialchars($title) : htmlspecialchars($row->title);
          $img = '/upload/records/'.$row->id.'.'.$row->ext; 
          $ar[] = array(
            'img' => $img,  
            'width' => $row->width,  
            'height' => $row->height, 
            'title' => $new_title,
            'ext' => $row->ext
          );  

          if(!$var){
          ?>
            <img border="<?php echo $border; ?>" 
              src="<?php echo $img; ?>"  
              width="<?php echo $row->width; ?>"
              height="<?php echo $row->height; ?>" 
              align="<?php echo $align; ?>" 
              alt="<?php echo $new_title; ?>" 
              title="<?php echo $new_title; ?>" 
              class="<?php echo $set_class; ?>" /> 
          <?php
          }

        }
        
        if($var){
          $tpl->assign($var,$ar);
        }
        return;
      }
    }

    if(!empty($default)){
      return "<img border='{$border}' src='{$default}' 
                width='{$width}' height='{$height}' 
                align='{$align}' alt='{$alt}' title='{$title}' 
                class='{$set_class}'>";
    }  	
  }
  
  



?>