<?php
/*
Не знаю надо ли во время сессии изменять время последнего входа на сайт или
изменять только после ввода логина-пароля??
Пока оставил первый вариант
*/
if(!defined('SIMPLA_ADMIN')){ die(); }
$session_id = session_id();
if(empty($session_id)) session_start();

if(!empty($_SESSION['SI_USERID']) && !isset($_SESSION['BO_USERID']) 
	&& !empty($_SESSION['SI_LOGIN']) && !isset($_SESSION['BO_LOGIN'])
){
	global $db;
	/* авторизован в ЛК, расшифруем логин и ID */
	$sql = "SELECT id, login, passwd  
		FROM ".$db->tables['users']." 
		WHERE MD5(id) = '".$_SESSION['SI_USERID']."' 
		AND MD5(login) = '".$_SESSION['SI_LOGIN']."' 
	";
	$r = $db->get_row($sql);
	
	if(!empty($r->id)){
		$_SESSION['BO_USERID'] = $r->id;
		$_SESSION['BO_LOGIN'] = $r->login;
		$_SESSION['BO_PASSWORD'] = md5($r->passwd);
		$_SESSION['BO_REGISTERED'] = 1;
	}
}
	
if(@!isset($site_vars['site_url'])){
  $site_vars['site_url'] = $_SERVER["HTTP_HOST"];
}

if(isset($_POST["bo_login"]) && isset($_POST["bo_password"]) && isset($_POST["bo_button"])){
	echo try_login();
}
        
if(@!isset($site_vars['office_ip'])){ $site_vars['office_ip'] = ''; }
if(!check_ip($site_vars['office_ip'])){
	echo "<h2>You are welcome!</h2><br/><br/>Fatal Error 0030."; die();
}

if(
	!isset($_SESSION["BO_USERID"]) 
	|| !isset($_SESSION["BO_LOGIN"]) 
	|| !isset($_SESSION["BO_PASSWORD"]) 
	|| !isset($_SESSION["BO_REGISTERED"])
){
	$try2 = try_login_by_cookies();
	if(!$try2){
		echo login_form();
		exit;
	}

	if(!login($_SESSION["BO_USERID"],$_SESSION["BO_LOGIN"],$_SESSION["BO_PASSWORD"])){
		echo '<p align="center" class="mt-20 red"><a href=./>'.GetMessage('admin','auth','auth_needed').'</a></p>';
		unset($_SESSION["BO_USERID"]);
		unset($_SESSION["BO_LOGIN"]);
		unset($_SESSION["BO_PASSWORD"]);
		exit;
	}else{
		return TRUE;
	}

}else{
	if(!login($_SESSION["BO_USERID"],$_SESSION["BO_LOGIN"],$_SESSION["BO_PASSWORD"]))
	{
		echo '<p align="center" class="mt-20 red"><a href=./>'.GetMessage('admin','auth','auth_needed').'</a></p>';
		echo non_auth_footer();
		unset($_SESSION["BO_USERID"]);
		unset($_SESSION["BO_LOGIN"]);
		unset($_SESSION["BO_PASSWORD"]);
		exit;
	}else{
		return TRUE;
	}
}

function login_form()
{
  global $tpl;
  return $tpl->fetch("auth/auth.html");
}

function login($bo_userid, $bo_login, $bo_password)
{
  global $db, $tpl, $admin_vars;
	$bo_login = htmlspecialchars($bo_login);
	$bo_password = htmlspecialchars($bo_password);
	$query = "SELECT * FROM ".$db->tables['users']." WHERE login = '".$bo_login."' AND MD5(passwd) = '".$bo_password."' AND admin = '1' AND active = '1' ";
	$row = $db->get_row($query);
	if(!$row){ echo "<p align=center><b>DB error 0090: ".mysql_error()."</b></p>"; return FALSE; }

    $username = stripslashes($row->login);
    $password = stripslashes($row->passwd);
		$_SESSION["BO_USERID"] = stripslashes($row->id);
		$_SESSION["BO_LOGIN"] = stripslashes($row->login);
		$_SESSION["BO_PASSWORD"] = md5(stripslashes($row->passwd));
		$_SESSION["BO_REGISTERED"] = 1;
		$_SESSION["BO_NAME"] = stripslashes($row->name);
		$_SESSION["BO_EMAIL"] = stripslashes($row->email);
    $prava = $db->get_row("SELECT * FROM ".$db->tables['users_prava']." WHERE bo_userid = '".$row->id."' ", ARRAY_A);
    
    $admin_vars['bo_user'] = array(
      'id' => $row->id,
      'login' => $row->login,
      'name' => stripslashes($row->name),
      'email' => stripslashes($row->email),
      'registered' => 1,
      'prava' => $prava
    );
    
		$GLOBALS["BO_USERID"] = stripslashes($row->id);
		$GLOBALS["BO_LOGIN"] = stripslashes($row->login);
		$GLOBALS["BO_PASSWORD"] = md5(stripslashes($row->passwd));
		$GLOBALS["BO_REGISTERED"] = 1;
		$GLOBALS["BO_NAME"] = stripslashes($row->name);
		$GLOBALS["BO_EMAIL"] = stripslashes($row->email);
		$remember = isset($_POST["remember"]) ? true : false;
    
    wp_setcookie($username, $password, false, '', '', $remember);
    change_last_login($row->id);
    
    /* delete more than 30 days records in database counter */
    $sql = "DELETE FROM ".$db->tables['counter']." WHERE `time` < (NOW() - INTERVAL 30 DAY)";
    $db->query($sql);
	return TRUE;
}

function try_login()
{
  global $db, $lang;
	$bo_login = trim(stripslashes($_POST["bo_login"]));
	$bo_password = trim(stripslashes($_POST["bo_password"]));
	if(strlen($bo_login) < 4 || strlen($bo_password) < 4){
		echo '<p align="center" class="mt-20 red"><b>'.GetMessage('admin','auth','error_too_short').'</b></p>'; 
		return FALSE;
	   
	}
	$bo_password = encode_str($bo_password);

	$query = "SELECT * FROM ".$db->tables['users']." WHERE login = '$bo_login' AND passwd = '".$bo_password."'  AND admin = '1' ";
	$row = $db->get_row($query);

	if(!$row){ echo '<p align="center" class="mt-20 red"><b>'.GetMessage('admin','auth','unknown_user').'</b></p>'; return FALSE; }

    $username = stripslashes($row->login);
    $password = stripslashes($row->passwd);
		$_SESSION["BO_USERID"] = stripslashes($row->id);
		$_SESSION["BO_LOGIN"] = $username;
		$_SESSION["BO_PASSWORD"] = md5($password);
		$_SESSION["BO_REGISTERED"] = 1;
		$_SESSION["BO_NAME"] = stripslashes($row->name);
		$_SESSION["BO_EMAIL"] = stripslashes($row->email);
		$GLOBALS["BO_USERID"] = stripslashes($row->id);
		$GLOBALS["BO_LOGIN"] = $username;
		$GLOBALS["BO_PASSWORD"] = md5($password);
		$GLOBALS["BO_REGISTERED"] = 1;
		$GLOBALS["BO_NAME"] = stripslashes($row->name);
		$GLOBALS["BO_EMAIL"] = stripslashes($row->email);
		$remember = isset($_POST["remember"]) ? true : false;
    wp_setcookie($username, $password, false, '', '', $remember);
    change_last_login($row->id);
	
	if(!empty($_POST['r'])){
		$r = $_POST['r'];
	}else{ $r = "/".ADMIN_FOLDER."/"; }
    header("Location: ".$r);
    exit;
}

if(!function_exists('logout')){

function logout()
{
		unset($_SESSION["BO_USERID"]);
		unset($_SESSION["BO_LOGIN"]);
		unset($_SESSION["BO_PASSWORD"]);
		unset($_SESSION["BO_REGISTERED"]);
		unset($_SESSION["BO_NAME"]);
		unset($_SESSION["BO_EMAIL"]);
		unset($GLOBALS["BO_USERID"]);
		unset($GLOBALS["BO_LOGIN"]);
		unset($GLOBALS["BO_PASSWORD"]);
		unset($GLOBALS["BO_REGISTERED"]);
		unset($GLOBALS["BO_NAME"]);
		unset($GLOBALS["BO_EMAIL"]);
    wp_clearcookie();
    ?>
<html>
<head>
    <title>Администрирование сайта</title>
    <meta http-equiv="Content-Language" content="ru-RU">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta content="Администрирование сайта" name="keywords">
    <meta content="Администрирование сайта" name="description">
    <meta content="global" http-equiv="distribution">
<link REL="SHORTCUT ICON" href="favicon.ico">
<link rel="StyleSheet" href="style.css" type="text/css">
</head>
<body bgcolor="#ffffff" leftmargin=0 marginheight=0 marginwidth=0 rightmargin=0 topmargin=0>
<h1 align=center>Выход из системы выполнен.</h1>
<center><ul>
<li><a href=./>Войти</a></li>
</ul>
</center>
<?php
    echo non_auth_footer();
?>
</body></html>
<?php

    exit;
}

}

function try_login_by_cookies() {
  if(!isset($_COOKIE["USER_COOKIE"]) || !isset($_COOKIE["PASS_COOKIE"])){ return false; }
	if ( empty($_COOKIE["USER_COOKIE"]) || empty($_COOKIE["PASS_COOKIE"]) ){
		return false;
  }
  global $db;

	$bo_login = $_COOKIE["USER_COOKIE"];
	$bo_password = $_COOKIE["PASS_COOKIE"];
	$query = "SELECT * FROM ".$db->tables['users']." WHERE login = '".try_addslashes($bo_login)."' AND MD5(MD5(passwd)) = '".try_addslashes($bo_password)."'  AND admin = '1' ";
	$row = $db->get_row($query);
	if(!$row){ echo "<p align=center><b>DB error 0223: ".mysql_error()."</b></p>"; return FALSE; }

		$_SESSION["BO_USERID"] = $row->id;
		$_SESSION["BO_LOGIN"] = stripslashes($row->login);
		$_SESSION["BO_PASSWORD"] = md5(stripslashes($row->passwd));
		$_SESSION["BO_REGISTERED"] = 1;
		$_SESSION["BO_NAME"] = stripslashes($row->name);
		$_SESSION["BO_EMAIL"] = stripslashes($row->email);
		$GLOBALS["BO_USERID"] = stripslashes($row->id);
		$GLOBALS["BO_LOGIN"] = stripslashes($row->login);
		$GLOBALS["BO_PASSWORD"] = md5(stripslashes($row->passwd));
		$GLOBALS["BO_REGISTERED"] = 1;
		$GLOBALS["BO_NAME"] = stripslashes($row->name);
		$GLOBALS["BO_EMAIL"] = stripslashes($row->email);
		$GLOBALS["MANAGER_ID"] = stripslashes($row->id);
		$GLOBALS["MANAGER_GROUP_ID"] = 0;
		$GLOBALS["MANAGER_NAME"] = stripslashes($row->name);
		wp_setcookie($bo_login, $bo_password, true, '', '', false);
  	change_last_login($row->id);
		return TRUE;
}

function wp_clearcookie() {
	setcookie(USER_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie(PASS_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie(USER_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);
	setcookie(PASS_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);
}


function wp_setcookie($username, $password, $already_md5 = false, $home = '', $siteurl = '', $remember = false) {
	if ( !$already_md5 )
		$password = md5( md5($password) ); // Double hash the password in the cookie.

  if(!defined("COOKIEPATH") OR !defined("SITECOOKIEPATH") 
      OR !defined("COOKIEHASH") OR !defined("USER_COOKIE") 
      OR !defined("PASS_COOKIE") OR !defined("COOKIE_DOMAIN"))
  {
    return;  
  }
  
	if ( empty($home) )
		$cookiepath = COOKIEPATH;
	else
		$cookiepath = preg_replace('|https?://[^/]+|i', '', $home . '/' );

	if ( empty($siteurl) ) {
		$sitecookiepath = SITECOOKIEPATH;
		$cookiehash = COOKIEHASH;
	} else {
		$sitecookiepath = preg_replace('|https?://[^/]+|i', '', $siteurl . '/' );
		$cookiehash = md5($siteurl);
	}

	if ( $remember )
		$expire = time() + 31536000;
	else
		$expire = 0;

	if(!setcookie(USER_COOKIE, $username, $expire, $sitecookiepath, COOKIE_DOMAIN)){
  }
  if(!setcookie(PASS_COOKIE, $password, $expire, $sitecookiepath, COOKIE_DOMAIN)){
  }
	if ( $cookiepath != $sitecookiepath ) {
		setcookie(USER_COOKIE, $username, $expire, $sitecookiepath, COOKIE_DOMAIN);
		setcookie(PASS_COOKIE, $password, $expire, $sitecookiepath, COOKIE_DOMAIN);
	}

}

function change_last_login($id){
  global $db;
  $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'н/д';
  $ddate = date('Y-m-d H:i:s');
  $query = "UPDATE ".$db->tables['users']." SET last_login = '".$ddate."', last_ip = '".try_addslashes($ip)."' WHERE id = '".$id."' ";
  $db->query($query);
  return;
}
?>