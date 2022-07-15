<?php
return;
class User {
	var $id            = 0;
	var $vars          = array();
	var $login         = '';
	var $email         = '';
	var $lang         = array();


    //
    function __construct(){
        global $site;
        $this->check_user();
        $ar = array(
            'id' => $this->id,
            'vars' => $this->vars,
            'login' => $this->login,
            'email' => $this->email
        );
        $site->user = $ar;
    }
    
    //
    function check_user(){
        $id = isset($_SESSION["U_ID"]) ? intval($_SESSION["U_ID"]) : 0;
        $login = isset($_SESSION["U_LOGIN"]) ? $_SESSION["U_LOGIN"] : '';
        $pass = isset($_SESSION["U_PASS"]) ? $_SESSION["U_PASS"] : '';
        $email = isset($_SESSION["U_EMAIL"]) ? $_SESSION["U_EMAIL"] : '';

        if($id == 0){
            $id = isset($_COOKIE["U_ID"]) ? intval($_COOKIE["U_ID"]) : 0;
            $login = isset($_COOKIE["U_LOGIN"]) ? $_COOKIE["U_LOGIN"] : '';
            $pass = isset($_COOKIE["U_PASS"]) ? $_COOKIE["U_PASS"] : '';
            $email = isset($_COOKIE["U_EMAIL"]) ? $_COOKIE["U_EMAIL"] : '';
        }

        if($id == 0){ return; }
        
        global $db;
        $row = $db->get_row("SELECT u.*, bo_prava.settings, bo_prava.orders 
            FROM ".$db->tables['boffice_user']." u
            LEFT JOIN bo_prava ON bo_userid = '".$id."'
            WHERE u.id = '".$id."' AND md5(u.login) = '".try_addslashes($login)."'
            AND md5(md5(u.passwd)) = '".try_addslashes($pass)."'
            AND md5(u.email) = '".try_addslashes($email)."' ", ARRAY_A);
        if(!$row || $db->num_rows == 0){ return; }
        $this->id = $row['id'];
        $this->login = stripslashes($row['login']);
        $this->email = stripslashes($row['email']);
        $this->vars = $row;
        return;
    }

	
	function login($site){
		global $db, $tpl;
		$site->page['title'] = 'Авторизация';
		$site->page['page'] = 'blank.html';
		$site->page['content'] = $tpl->fetch('user/login.html');

		return $site->page;
		$ar = array('page'=>'blank.html', ''=>'5151511');
		if(isset($_SERVER["HTTP_REFERER"])){
			if(substr($_SERVER["HTTP_REFERER"], -11) == "/logout/ok/"){
				$site->page["referer"] = "/profile/";      
			}else{
				if(!isset($_POST["referer"])){
					$site->page["referer"] = $_SERVER["HTTP_REFERER"];
				}else{
					$site->page["referer"] = $_POST["referer"];
				}      
			}       
		}
    if(
      (isset($_POST['login']) ||
      isset($_POST['email'])) &&
      isset($_POST['passwd'])
    ){
      $login = isset($_POST['login']) ? trim($_POST['login']) : "";
      $email = isset($_POST['email']) ? trim($_POST['email']) : "";
      $passwd = isset($_POST['passwd']) ? trim($_POST['passwd']) : "";
      if(empty($login)){
        $and = " email = '".try_addslashes($email)."' ";
      }elseif(empty($email)){
        $and = " login = '".try_addslashes($login)."' ";
      }elseif(empty($email) && empty($login)){
        $site->page['error'] = $lang->txt('login_error');
        return;
      }else{
        $and = " email = '".try_addslashes($email)."'
          AND login = '".try_addslashes($login)."'
        ";
      }
      if(empty($passwd)) {
        $site->page['error'] = $lang->txt('login_error');
        return;
      }
      $passwd = encode_str($passwd);
      $query = "SELECT * FROM ".$db->tables['boffice_user']." WHERE
        $and AND passwd = '".$passwd."'
      ";
      $row = $db->get_row($query);

      if(!$row || $db->num_rows == 0){
        $site->page['error'] = $lang->txt('login_error');
        return;
      }

      $_SESSION["U_ID"] = $row->id;
      $_SESSION["U_LOGIN"] = md5($row->login);
      $_SESSION["U_PASS"] = md5(md5($row->passwd));
      $_SESSION["U_EMAIL"] = md5($row->email);

      if(isset($_POST['remember'])){
        go_setcookie("U_ID", $row->id, 0);
        go_setcookie("U_LOGIN", md5($row->login), 0);
        go_setcookie("U_PASS", md5(md5($row->passwd)), 0);
        go_setcookie("U_EMAIL", md5($row->email), 0);
      }

      $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
      $db->query("UPDATE ".$db->tables['users']."
        SET last_login = '".date('Y-m-d H:i:s')."',
        last_ip = '".try_addslashes($ip)."'
        WHERE id = '".$row->id."'
      ");
      if(isset($_POST["referer"]) && !empty($_POST["referer"]))
      {
      	return _redirect($_POST["referer"]);
      }
      $url = MODE_REWRITE ?
        _link(array('profile', stripslashes($row->login))) :
        _link(array('profile', $row->id));
      return _redirect($url);
    }else{
      return;
    }
  }

  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  function all_users(){
    global $site, $db, $lang;
    $site->page['metatitle'] = $lang->txt('list_users');
    $site->page['content'] = "";
    $list_users = array();
    return $list_users;
    // Выключаем возмжность просмотра списка пользователей и их профили
    $query = "SELECT * FROM ".$db->tables['boffice_user']." order by date_insert ";
    $rows = $db->get_results($query);
    if($db->num_rows == 0){ return $list_users; }

  	// PAGE LIMITS
  	$page = isset($site->uri['page']) ? $site->uri['page'] : 0;
    $limit_Start = $page*ONPAGE; // for pages generation
    $limit = "limit $limit_Start, ".ONPAGE;
    $all_results = $db->num_rows;
    $query = $query." ".$limit;

    if(MODE_REWRITE && isset($site->uri['slug'])){
      $ar = array('profiles', $site->uri['slug']);
    }elseif(!MODE_REWRITE && isset($site->uri['id'])){
      $ar = array('profiles', $site->uri['id']);
    }else{
      $ar = array('profiles', 'list');
    }

    $strPages = _pages_ar($all_results, $page, ONPAGE, $ar);


    $rows = $db->get_results($query);

    if($db->num_rows == 0){
      return array();
    }


    foreach($rows as $row){
      $link = MODE_REWRITE
        ? array('profile', stripslashes($row->login))
        : array('profile', $row->id);
      $last_login = $row->last_login == 0 ? $lang->txt('n_a') : $site->ddate($row->last_login);
      $list_users[] = array(
        'title' => stripslashes($row->login),
        'id' => $row->id,
        'name' => stripslashes($row->name),
        'email' => stripslashes($row->email),
        'phone_mobil' => stripslashes($row->phone_mobil),
        'icq' => stripslashes($row->icq),
        'country' => stripslashes($row->country),
        'city' => stripslashes($row->city),
        'user_interes' => stripslashes($row->user_interes),
        'user_about' => stripslashes($row->user_about),
        'url' => stripslashes($row->url),
        'date_insert' => $site->ddate($row->date_insert),
        'last_login' => $last_login,
        'admin' => $row->admin,
        'user_title' => $row->user_title,
        'link' => _link($link)
      );
    }
    $site->page['list_users'] = $list_users;
    return;
  }

  function _user(){
    global $site, $lang;
    $site->page['urllogin'] = _link(array('login'));
    $site->page['urlreg'] = _link(array('register'));

    $do = $site->uri['action'];
    if($do == 'register'){
      if($this->id > 0){
        $url = MODE_REWRITE ?
          _link(array('profile', $this->login)) :
          _link(array('profile', $this->id));
        return _redirect($url);
      }
      $site->page['title'] = $lang->txt('register_user');
      $site->page['content'] = "";
      return $this->register();
    }elseif($do == 'login'){
      $site->page['title'] = $lang->txt('login_user');
      $site->page['content'] = "";
      return $this->_login();
    }elseif($do == 'logout'){
      $site->page['title'] = $lang->txt('user_logout');
      $site->page['content'] = "";
      return $this->_logout();
    }elseif($do == 'forget_password'){
      $site->page['title'] = $lang->txt('forgot_password');
      $site->page['content'] = "";
      return $this->forgot_password();
    }elseif($do == 'messages'){
      $site->page['title'] = $lang->txt('messages');
      $site->page['content'] = "";
    }
    return;
  }

  function forgot_password(){
    global $db, $site, $lang;

    if(isset($_GET['key']) && isset($_GET['var'])){
     $pwd1 = isset($_POST['pwd1']) ? trim($_POST['pwd1']) : "";
     $pwd2 = isset($_POST['pwd2']) ? trim($_POST['pwd2']) : "";
     if($pwd1 != $pwd2){
        $site->page['error'] = 'Введите одинаковый пароль 2 раза';
     }elseif(empty($pwd1)){
        $site->page['error'] = 'Пароль не может быть пустым';
     }elseif(strlen($pwd1) < 4 || strlen($pwd1) > 20){
        $site->page['error'] = 'Допускается длина пароля от 4 до 20 символов';
     }else{
        $new_password = encode_str($pwd1);
        // ссылка активна в течение 2-х суток! == 172800

        $db->query("UPDATE ".$db->tables['boffice_user']." 
          SET passwd = '".$new_password."' 
          WHERE md5(date_insert) = '".$_GET['key']."' 
          AND md5(id) = '".$_GET['var']."'
          AND ((UNIX_TIMESTAMP()-UNIX_TIMESTAMP(last_login)) < 172800)
          ");
        $url = array('forget_password','done');
        return _redirect(_link($url));      
     }

      return;
    }

    $login = isset($_POST["login"]) ? trim($_POST["login"]) : '';
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
    if(isset($_POST["login"]) || isset($_POST["email"])){
      $str = check_captcha();
      if(empty($email) && empty($login)){
        $site->page['error'] = $lang->txt('error_empty_fields');
      }elseif(empty($email) && !isset($_POST["login"])){
        $site->page['error'] = $lang->txt('error_empty_email');
      }elseif(empty($login) && !isset($_POST["email"])){
        $site->page['error'] = $lang->txt('error_empty_login');
      }elseif(!empty($str)){
        $site->page['error'] = $str;
      }else{
        if(empty($email)){
          $where = " login = '".try_addslashes($login)."' ";
        }else{
          $where = " email = '".try_addslashes($email)."' ";
        }


      // Find password and send it
        $query = "SELECT * FROM ".$db->tables['boffice_user']."
            WHERE $where ";
        $row = $db->get_row($query);

        if(!$row || $db->num_rows == 0){
          $site->page['content'] = $lang->txt('usernotfound');
          return;
        }else{
          $email = stripslashes($row->email);
          $login = stripslashes($row->login);
          if(empty($login)){
            $login = stripslashes($row->email);
          }

          if(empty($email)){
            $site->page['content'] = $lang->txt('usernotfound');
            return;
          }

          $str = Resource_Page('emails/password_reminder.html');
          $str = str_replace('[LOGIN]', $login, $str);
          $str = str_replace('[EMAIL]', $email, $str);
          $str = str_replace('[PASSWORD]', '', $str);
          $str = str_replace('[SITE_SHORT]', $site->vars['name_short'], $str);
          $str = str_replace('[SITE_URL]', $site->vars['site_url'], $str);
          $str = str_replace('[PWD_LINK]', $site->vars['site_url']."/forget_password/?key=".md5($row->date_insert)."&var=".md5($row->id), $str);

          $db->query("UPDATE ".$db->tables['boffice_user']." 
            SET last_login = '".date("Y-m-d H:i:s")."' 
            WHERE email = '".$email."' ");

          $site->send_notification2($site->vars['name_short'], $site->vars['email_info'],
            $lang->txt('forgot_password'), $str, $email, $login);

          $url = array('forget_password','sent');
          return _redirect(_link($url));
        }

      }
    }

    if(in_array('sent', $site->uri)){
      $site->page['content'] = $lang->txt('password_sent');
    }

    if(in_array('done', $site->uri)){
      $site->page['content'] = "Новый пароль успешно создан! <a href=\"/login/\">Авторизация</a>";
    }
    return;
  }


  function _logout(){
    if(isset($_SESSION)){
      foreach($_SESSION as $k => $v){
        unset($_SESSION[$k]);
      }
    }
    global $site, $lang;
    if(!isset($site->uri['slug'])){
      $ar = array('logout', 'ok');
      return _redirect(_link($ar));
    }
    $site->page['content'] = $lang->txt('logout_ok');

  }

  function _edit_user(){
    global $db, $lang, $site, $tpl;
    $site->page['page'] = 'pages/user_edit.html';
    $site->page['metatitle'] = $lang->txt('userinfo')." ".stripslashes($this->login);
    $site->page['keywords'] = stripslashes($this->login).", ".$site->vars['meta_keywords'];
    $site->page['description'] = $lang->txt('userinfo')." ".stripslashes($this->login)." ".$site->vars['meta_description'];
    $site->page['title'] = $lang->txt('userinfo')." ".stripslashes($this->login);
    $site->page['user_title'] = $this->vars['user_title'];

    $years = array();
    $years['0000'] = $lang->txt('year');
    for($i=1920;$i<2000;$i++){
      $years[$i] = $i;
    }

    $days = array();
    $days['00'] = $lang->txt('day');
    for($i=1;$i<32;$i++){
      $j = strlen($i) == 1 ? '0'.$i : $i;
      $days[$j] = $i;
    }

    $months = array();
    $months['00'] = $lang->txt('month');
    for($i=1;$i<13;$i++){
      $j = strlen($i) == 1 ? '0'.$i : $i;
      $months[$j] = $lang->txt('month_'.$j);
    }

    $bd = explode('-', $this->vars['birth_day']);
    $tpl->assign('years', $years);
    $tpl->assign('months', $months);
    $tpl->assign('days', $days);
    $tpl->assign('users_year', $bd[0]);
    $tpl->assign('users_month', $bd[1]);
    $tpl->assign('users_day', $bd[2]);
    $tpl->assign('user_pers_hi', $this->vars['pers_hi']);

    $ar = array();
    if(isset($site->vars['user_statuses'])){
      $str_all = nl2br($site->vars['user_statuses']);
      $ar_all = explode("<br />",$str_all);
      foreach($ar_all as $k => $v){
        $ar[$v] = trim($v);
      } 
    }else{
      $ar = array("Клиент" => "Клиент", "Дилер" => "Дилер");
    }    
    $tpl->assign('all_pers_hi', $ar);
    
    

    if(isset($_POST['update'])){

      $name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
      $phone_mobil = isset($_POST["phone_mobil"]) ? trim($_POST["phone_mobil"]) : "";
      $sex = isset($_POST["sex"]) ? trim($_POST["sex"]) : "";
      $icq = isset($_POST["icq"]) ? trim($_POST["icq"]) : "";
      $country = isset($_POST["country"]) ? trim($_POST["country"]) : "";
      $city = isset($_POST["city"]) ? trim($_POST["city"]) : "";
      $users_year = isset($_POST["users_year"]) ? trim($_POST["users_year"]) : "0000";
      $users_month = isset($_POST["users_month"]) ? trim($_POST["users_month"]) : "00";
      $users_day = isset($_POST["users_day"]) ? trim($_POST["users_day"]) : "00";
      $birth_date = $users_year.'-'.$users_month.'-'.$users_day;
      $user_interes = isset($_POST["user_interes"]) ? trim($_POST["user_interes"]) : "";
      $user_about = isset($_POST["user_about"]) ? trim($_POST["user_about"]) : "";
      $url = isset($_POST["url"]) ? trim($_POST["url"]) : "";
      $url = str_replace('http://', '', $url);
      $pers_hi = isset($_POST["pers_hi"]) ? trim($_POST["pers_hi"]) : "";
      $news = isset($_POST["news"]) ? 1 :	0;

      $db->query("UPDATE ".$db->tables['boffice_user']." SET
        `name` = '".try_addslashes($name)."',
        `phone_mobil` = '".try_addslashes($phone_mobil)."',
        `icq` = '".try_addslashes($icq)."',
        `country` = '".try_addslashes($country)."',
        `city` = '".try_addslashes($city)."',
        `birth_day` = '".try_addslashes($birth_date)."',
        `user_interes` = '".try_addslashes($user_interes)."',
        `user_about` = '".try_addslashes($user_about)."',
        `url` = '".try_addslashes($url)."',
        `pers_hi` = '".try_addslashes($pers_hi)."',
        `news` = '".try_addslashes($news)."'
        WHERE id = '".$this->id."'
      ");
      $ar = MODE_REWRITE
        ? array('profile', $this->login)
        : array('profile', $this->id);
      return _redirect(_link($ar));
    }
    
    if(isset($_POST["newpwd"])){
      $oldpwd = isset($_POST["oldpwd"]) ? trim($_POST["oldpwd"]) : "";
      $pwd1 = isset($_POST["pwd1"]) ? trim($_POST["pwd1"]) : "";
      $pwd2 = isset($_POST["pwd2"]) ? trim($_POST["pwd2"]) : "";

      if(!empty($oldpwd) && !empty($pwd1) && $pwd1 == $pwd2 && strlen($pwd1) > 3 && strlen($pwd1) < 21){
        // Проверим старый пароль правильный ли
        $oldpwd = encode_str($oldpwd);
        $check = $db->get_var("SELECT id FROM ".$db->tables['boffice_user']." 
          WHERE login = '".$this->vars['login']."' AND passwd = '".$oldpwd."'  ");
        if($check && $db->num_rows == 1){
          $pwd1 = encode_str($pwd1);
          $db->query("UPDATE ".$db->tables['boffice_user']." SET
              `passwd` = '".try_addslashes($pwd1)."'
            WHERE id = '".$this->id."'
              AND login = '".$this->vars['login']."'
              AND passwd = '".$oldpwd."'
          ");
          //$db->debug();
          $_SESSION["U_PASS"] = md5(md5($pwd1));
          $ar = array('profile', $this->login, 'newpwd', 'saved');
          return _redirect(_link($ar));
        }
        
      }

    }
    
    return;


    $this->vars = array(
      'login' => stripslashes($row->login),
      'email' => stripslashes($row->email),
      'date_insert' => stripslashes($row->date_insert),
      'last_login' => stripslashes($row->last_login),
      'user_title' => stripslashes($row->user_title),

      'name' => stripslashes($row->name),
      'phone_mobil' => stripslashes($row->phone_mobil),
      'icq' => stripslashes($row->icq),
      'country' => stripslashes($row->country),
      'city' => stripslashes($row->city),
      'birth_day' => stripslashes($row->birth_day),
      'user_interes' => stripslashes($row->user_interes),
      'user_about' => stripslashes($row->user_about),
      'url' => stripslashes($row->url),
      'pers_hi' => stripslashes($row->pers_hi),
      'news' => stripslashes($row->news),
      'sex' => stripslashes($row->sex)

    );


  }

  function userinfo($login){
    global $db, $site, $lang;
    $login = urldecode($login);
    $login = trim($login);
    $own_profile = false;

    if($login == '' && $this->id == 0){
      return error_403();
    }

    if(isset($site->uri['slug'])){
      if($site->uri['slug'] == "messages"){
        return $this->messages();
      }elseif($site->uri['slug'] == "licences"){
        return $this->licences();
      }elseif($site->uri['slug'] == "orders"){
        return $this->orders();
      }elseif($site->uri['slug'] == "invoices"){
        return $this->invoices();
      }elseif($site->uri['slug'] == "partner"){
        return $this->partner();
      }elseif($site->uri['slug'] == "fb"){
        return $this->users_fb();
      }elseif($site->uri['slug'] == "man_projects"){
        return $this->man_projects();
      }elseif($site->uri['slug'] == "man_users"){
        return $this->man_users();
      }elseif($site->uri['slug'] == "man_deals"){
        return $this->man_deals();
      }elseif($site->uri['slug'] == "man_invoices"){
        return $this->man_invoices();
      }elseif($site->uri['slug'] == "man_orders"){
        return $this->man_orders();
      }elseif($site->uri['slug'] == "subscription"){
      	return $this->subscription();
      }elseif($site->uri['slug'] == $this->login){
        $own_profile = true;
        if(isset($site->uri['edit'])){
          return $this->_edit_user();
        }
      }
    }

    if($login == ''){
      $link = _link(array('register'));
      return _redirect($link);
    }
    $query = MODE_REWRITE ?
      "SELECT * FROM ".$db->tables['boffice_user']." WHERE login = '".try_addslashes($login)."' " :
      "SELECT * FROM ".$db->tables['boffice_user']." WHERE id = '".try_addslashes($login)."' ";
    $row = $db->get_row($query);

    if(!$row){ return $site->error_404(); }

    $site->page['page'] = 'user.html';
    $site->page['metatitle'] = $lang->txt('userinfo')." ".stripslashes($login);
    $site->page['keywords'] = stripslashes($login).", ".$site->vars['meta_keywords'];
    $site->page['description'] = $lang->txt('userinfo')." ".stripslashes($login)." ".$site->vars['meta_description'];

    $site->page['title'] = $lang->txt('userinfo')." ".stripslashes($login);

    if($own_profile || (isset($this->vars['admin']) && $this->vars['admin'] == "1")){
      $href = _link(array('profile', stripslashes($login), 'edit'));
      //$site->page['own'] = "<a href=\"$href\">".$lang->txt('edit_user')."</a>";
      if($own_profile){ 
        $site->page['own'] = "1"; 
      }else{
        $site->page['own'] = "0"; 
      }
    }else{
      return error_403();
    }

    
    $site->page['name'] = stripslashes($row->name) == '' ? $lang->txt('n_a') : stripslashes($row->name) ;

    $site->page['icq'] = stripslashes($row->icq) == '' ? $lang->txt('n_a') : stripslashes($row->icq) ;
    $site->page['phone_mobil'] = stripslashes($row->phone_mobil) == '' ? $lang->txt('n_a') : stripslashes($row->phone_mobil) ;
    $site->page['email'] = stripslashes($row->email) == '' ? $lang->txt('n_a') : stripslashes($row->email) ;
    $site->page['country'] = stripslashes($row->country) == '' ? $lang->txt('n_a') : stripslashes($row->country) ;
    $site->page['city'] = stripslashes($row->city) == '' ? $lang->txt('n_a') : stripslashes($row->city) ;
    $site->page['birth_day'] = stripslashes($row->birth_day) == 0 ? $lang->txt('n_a') : $site->ddate($row->birth_day) ;
    $site->page['user_interes'] = stripslashes($row->user_interes) == '' ? $lang->txt('n_a') : stripslashes($row->user_interes) ;
    $site->page['user_about'] = stripslashes($row->user_about) == '' ? $lang->txt('n_a') : stripslashes($row->user_about) ;
    $site->page['url'] = stripslashes($row->url) == '' ? $lang->txt('n_a') : stripslashes($row->url) ;
    $site->page['pers_hi'] = stripslashes($row->pers_hi) == '' ? $lang->txt('n_a') : stripslashes($row->pers_hi) ;
    $site->page['date_insert'] = stripslashes($row->date_insert) == 0 ? $lang->txt('n_a') : $site->ddate($row->date_insert) ;
    $site->page['last_login'] = stripslashes($row->last_login) == 0 ? $lang->txt('n_a') : $site->ddate($row->last_login) ;
    $site->page['admin'] = stripslashes($row->admin) == 0 ? 0 : stripslashes($row->admin) ;
    $site->page['user_title'] = stripslashes($row->user_title) == '' ? $lang->txt('n_a') : stripslashes($row->user_title) ;
    $site->page['sex'] = $lang->txt('n_a');

    return;
  }
  
  function man_users(){
    if($this->vars['settings'] == 0){ return error_403(); }
    global $db, $site, $tpl;
    $db2 = $this->db_n1seo();
    $users_n1 = $db2->get_results("SELECT id, userLogin, userEmail, userStatus FROM Users WHERE userType = '1' order by userLogin ", ARRAY_A);
    //$db2->debug();    
    $users_media = $db->get_results("SELECT u.id, u.login, u.email, p.orders, p.settings FROM boffice_user u, bo_prava p WHERE u.id = p.bo_userid AND u.admin = '1' order by u.login ", ARRAY_A);
    //$db->debug();    
    $site->page['title'] = "Менеджеры";
    $tpl->assign('users_n1', $users_n1);                 
    $tpl->assign('users_media', $users_media);                 
    $site->page['content'] = $tpl->fetch("pages/list_managers.html");
    
    return;
  }

  function users_fb(){
    global $db, $lang, $site;
    $site->page['title'] = $lang->txt('fb_requests');
    $site->page['metatitle'] = $lang->txt('fb_requests');
    $site->page['content'] = '';
    $rows = $db->get_results("SELECT * FROM ".$db->tables['feedback']." WHERE f_email = '".$this->email."' order by f_date ");
    if($db->num_rows == 0){ $site->page['content'] = $lang->txt('fb_list_empty'); return; }
    $site->page['fb_list'] = array();
    foreach($rows as $row){
      $link = array('feedback', 'answer', 'ticket', stripslashes($row->f_ticket_number));
      $site->page['fb_list'][] = array(
        'ticket' => stripslashes($row->f_ticket_number),
        'status' => stripslashes($row->f_status),
        'name' => stripslashes($row->f_name),
        'email' => stripslashes($row->f_email),
        'ddate' => $site->ddate($row->f_date),
        'ttime' => $site->ttime($row->f_date),
        'link' => _link($link)
      );
    }
    return;
  }

  function licences(){
    if($this->id == 0){ return error_403(); }
    global $lang, $site, $db, $tpl;
    
    // $this->id  user_id
    $id = isset($_GET['id']) ? intval($_GET['id']) : false;
    $site->page['title'] = "Лицензии";
    $site->page['metatitle'] = "Лицензии";
    $site->page['content'] = 'Оформленные лицензии';

    
    if($id === false){
      // Выводим список текущих лицензий
      $site->page['title'] = "Список лицензий ".$this->login;     
      $site->page['metatitle'] = "Список лицензий ".$this->login;
      $list_licences = $db->get_results("SELECT * FROM `licences` WHERE user_id = '".$this->id."' ORDER BY `lic_time` desc ", ARRAY_A);
      $tpl->assign('list_licences', $list_licences);                 
      $site->page['content'] = $tpl->fetch("licences/licences_list.html");
    }elseif($id == 0){
      // Выводим форму заказа новой лицензии
      $site->page['title'] = "Купить лицензию";
      $site->page['metatitle'] = "Купить лицензию";
      
      if(isset($_POST['licence']) && isset($_POST['nextstep'])){
      
        $lic_url = isset($_POST['lic_url']) ? trim($_POST['lic_url']) : '';
        if(!empty($lic_url) && $lic_url != 'http://'){
          $url_bez_http = str_replace('http://','',$lic_url);
          $point_qty = substr_count($url_bez_http, '.');
          if($point_qty == 2){
            $url_bez_www = str_replace('www.','',$url_bez_http); 
          }elseif($point_qty == 1){
            $url_bez_www = $url_bez_http;          
          }else{
            $lic_url = false;  
          }
          
///

          if(!$lic_url){
            $tpl->assign('error', "Не корректное значения адреса сайта!");
          }else{
            // Все ОК, записываем все в базу
          
            // Создадим новый заказ и новую запись о лицензии, свяжем их и переадресуем на страницу лицензии
            $id_product =  intval($_POST['licence']);
            if($id_product > 0){
              // Сделаем запись о заказе и корзине
              $product_info = $db->get_row("SELECT * FROM `shop_products` 
                WHERE id = '".$id_product."' AND price > '0' 
                AND active = '1' AND accept_orders = '1' ");
              if($product_info){
                $price = $product_info->price_spec > 0 ? $product_info->price_spec : $product_info->price; 
                $order_name = "Лицензия для ".$url_bez_www;
                $ddate = date("Y-m-d H:i:s");
                $query = "INSERT INTO `orders` (news, payment_method, delivery_method, 
                  created, ur_type, name1, name2, name3, email, city, metro, phone, address, 
                  address_memo) VALUES('0', '0', '', '".$ddate."', '', 
                  '".$db->escape($order_name)."', '', '', 
                  '".$this->vars['email']."', '', '', '".$this->vars['phone_mobil']."', '', '') ";
                $db->query($query);
                $order_id = $db->insert_id;
                // добавим в корзину
                $query = "INSERT INTO `orders_cart` (orderid, serialnumber, 
                    items, qty, price, pricerate, buyrate, manager, 
                    currency_buy, currency_sell, product_id, discount, memo) 
                    VALUES('".$order_id."', '', '".$db->escape($order_name)."', '1', 
                    '".$price."', '1.00', '0.00', 
                    '0', '0', '".$product_info->currency."',
                    '".$id_product."', '0', '') "; 
                $db->query($query);

                // Добавим в таблицу лицензий 
                $lic_time = time();
                $lic_key = encode_str($lic_time);               

                $query = "INSERT INTO `licences` (`lic_time`, `lic_key`, 
                  `title`, `user_id`, `order_id`, `stop_date`, 
                  `payd_status`, `lic_urls`, `lic_ip`, `active`) 
                    VALUES('".$lic_time."', '".$lic_key."', '".$db->escape($url_bez_www)."', 
                    '".$this->id."', '".$order_id."', '0', 
                    '0', '".$db->escape($lic_url)."', '', '0') "; 
                $db->query($query);
                $lic_id = $db->insert_id;
                header("Location: /profile/licences/?id=".$lic_id);
                exit;               
              }                                   
            }
          }      

///
        }
      }
      
      $query = "
SELECT p.* 
FROM `shop_product_options` as spo, `shop_products` as p
WHERE  
spo.`id_option` =  '3' 
AND LOWER(spo.`value` ) =  'лицензия'
AND spo.`id_product` = p.id
AND p.price > '0'
AND p.active = '1'
AND p.accept_orders = '1'
order by p.price asc      
      ";
      $list_licences = $db->get_results($query, ARRAY_A); 
      $tpl->assign('list_licences', $list_licences);      
      $site->page['content'] = $tpl->fetch("licences/licences_new.html");

    }else{
      // Выводим инфо о лицензии пользователя
      $site->page['title'] = "Лицензия ".$id;
      $site->page['metatitle'] = "Лицензия ".$id;
      $site->page['licence_id'] = $id;
      $licence_info = $db->get_row("SELECT * FROM `licences` WHERE user_id = '".$this->id."' AND id = '".$id."'  ", ARRAY_A);
      $tpl->assign('licence_info', $licence_info);                 

      $site->page['content'] = $tpl->fetch("licences/licences_info.html");

    }
     

    return;
  }

  function partner(){
    if($this->id == 0){ return error_403(); }
    global $lang, $site, $db;
    $site->page['title'] = "Партнерская программа";
    $site->page['metatitle'] = "Партнерская программа";
    $site->page['content'] = 'Партнерская программа';
    $site->page['order_id'] = 0;
    if(isset($site->uri['id'])){
    }else{
    }
    return;
  }


  function invoices(){
    if($this->id == 0){ return error_403(); }
    global $lang, $site, $db;
    $site->page['title'] = "Счета на оплату";
    $site->page['metatitle'] = "Счета на оплату";
    $site->page['content'] = 'Счета на оплату';
    $site->page['order_id'] = 0; 
    
    $db2 = $this->db_n1seo();
    $rows = $db2->get_results("SELECT id, projectID as pid FROM ProjectsData 
      WHERE projectEmail = '".$this->vars['email']."' ");      
    if(!$rows || $db2->num_rows == 0){
      $site->page['content'] = '<p>Список счетов пуст!</p>'; 
      return;
    }
    
    $ar = array();
    foreach($rows as $row){
      $bills = $db2->get_results("SELECT * FROM Billings 
      WHERE projectID = '".$row->pid."' order by id desc ", ARRAY_A);
      if($bills && $db2->num_rows > 0){
        foreach($bills as $bill){
          $ar[] = $bill;
        }                       
      }    
    }
    
    $site->page['list_invoices'] = $ar;
    if(isset($site->uri['id'])){
    }else{
    }
    return;
  }


  function orders(){
    if($this->id == 0){ return error_403(); }
    global $lang, $site, $db;
    $site->page['title'] = $lang->txt('list_orders');
    $site->page['metatitle'] = $lang->txt('list_orders');
    $site->page['content'] = '';
    $site->page['order_id'] = 0;
    if(isset($site->uri['id'])){
      $id = intval($site->uri['id']);
      $this->order_info($id);
    }else{
      $site->page['list_orders'] = $this->list_orders();
    }
    return;
  }

  function order_info($id){
    global $db, $site, $lang;
    $row = $db->get_row("SELECT * FROM ".$db->tables['orders']."
      WHERE email = '".$this->email."' AND id = '".$id."'
      ");
    if($db->num_rows == 0){ return array(); }
    $site->page['order_id'] = $id;
    $site->page['payment_method'] = $site->payment_title(stripslashes($row->payment_method));
    $site->page['ddate'] = $site->ddate($row->created);
    $site->page['ttime'] = $site->ttime($row->created);
    $fullname = stripslashes($row->name1);
    //if(!empty($row->name2)){ $fullname .= " ".stripslashes($row->name2); }
    //if(!empty($row->name3)){ $fullname .= " ".stripslashes($row->name3); }
    $site->page['title'] = $lang->txt('basket_title')." - ".$id;
    $site->page['name'] = $fullname;
    $site->page['name2'] = stripslashes($row->name2);
    $site->page['ur_type'] = empty($row->ur_type) ? 0 : $row->ur_type;
    $site->page['email'] = stripslashes($row->email);
    $site->page['city'] = stripslashes($row->city);
    $site->page['payd_status'] = $row->payd_status;
    $site->page['phone'] = stripslashes($row->phone);
    $site->page['address'] = stripslashes($row->address);
    $site->page['address_memo'] = stripslashes($row->address_memo);
    $site->page['status'] = $this->status($row->status);
    $site->page['order_status'] = $row->status;
    $site->page['region'] = $site->region_title(stripslashes($row->region));
    $site->page['delivery_price'] = stripslashes($row->delivery_price);
    $site->page['delivery_index'] = stripslashes($row->delivery_index);
    $site->page['list_products'] = $this->list_products($id);
    return;
  }

  function list_products($id){
    global $db, $site;
    $rows = $db->get_results("SELECT * FROM ".$db->tables['orders_cart']."
      WHERE orderid = '".$id."'
    ");
    if($db->num_rows == 0){ return array(); }
    $ar = array();
    $ii = 1;
    //$order_summ = 0;
    foreach($rows as $row){
      $alias = find_alias($db->tables['shop_products'], $row->product_id);
      $link_ar = MODE_REWRITE
        ? array('product', $alias)
        : array('product', $row->product_id);
      $link = _link($link_ar);
      if(!$alias){ $link = "#deleted"; }
      //$order_summ = (stripslashes($row->price)*stripslashes($row->pricerate))+$order_summ;
      $ar[] = array(
        'number' => $ii,
        'title' => stripslashes($row->items),
        'qty' => stripslashes($row->qty),
        'price' => $row->price,        
        'rate' => stripslashes($row->pricerate),
        'currency' => $site->show_currency($row->currency_sell),
        'discount' => stripslashes($row->discount),
        'link' => $link
      );
      $ii++;
    }
    $order_summ = $db->get_var("SELECT SUM(price) FROM `orders_cart` WHERE orderid = '".$id."' ");
    //$order_summ = number_format($order_summ, 2, '.', ' ');
    $site->page['order_summ'] = $order_summ;
    return $ar;

  }

  function list_orders(){
    global $db, $site;
    $rows = $db->get_results("SELECT * FROM ".$db->tables['orders']."
      WHERE email = '".$this->email."' ORDER BY created
      ");
    if($db->num_rows == 0){ return array(); }
    $ar = array();
    foreach($rows as $row){
      $order_summ = $db->get_var("SELECT SUM(price) FROM `orders_cart` WHERE orderid = '".$row->id."' ");
      $link = _link(array('profile', 'orders', 'id', $row->id));
      $ar[] = array(
        'link' => $link,
        'title' => $row->id,
        'ddate' => $site->ddate($row->created),
        'ttime' => $site->ttime($row->created),
        'status' => $this->status($row->status),
        'status_id' => $row->status,
        'ur_type' => $row->ur_type,
        'order_summ' => $order_summ
      );
    }
    return $ar;
  }

  function status($id){
    global $lang;
    if($id == -10){
      return $lang->txt('status_wrong');
    }elseif($id == -1){
      if(defined('CONFIRMORDER')){
        return $lang->txt('status_notconfirmed');
      }else{
        return $lang->txt('status_cancel');
      }
    }elseif($id == 0){
      return $lang->txt('status_new');
    }elseif($id == 1){
      return $lang->txt('status_in_progress');
    }elseif($id == 10){
      return $lang->txt('status_done');
    }else{
      return $lang->txt('status_unknown');
    }
  }

  function messages(){
    if($this->id == 0){ return error_403(); }
    global $lang, $site;
    $site->page['title'] = $lang->txt('list_messages');
    $site->page['metatitle'] = $lang->txt('list_messages');
    $site->page['content'] = '';
  }

  function subscription()
  {
  	global $db, $lang, $site;
  	if($this->id == 0){ return error_403(); }
  	$site->page["title"] = $lang->txt('subscription_title');
  	$site->page['page'] = 'subscription.html';

  	$subscr = get_param("subscr",null);
  	if($subscr)
  	{
  		$categs = get_param("categs",array());
  		$categs_list = implode(",",$categs);
  		$db->query("delete from subscriptions where user_id='{$this->id}'");
  		foreach($categs as $categ)
  		{
  			$db->query("insert into subscriptions set user_id='{$this->id}', categ_id='$categ'");
  		}
  		$url = _link(array("profile","subscription"));
  		return _redirect($url);
  	}

  	$rows = $db->get_results("select c.*,s.categ_id as cat_id from shop_categs c left join subscriptions s on (s.categ_id = c.id and s.user_id='{$this->id}')", ARRAY_A);
  	$categs = array();
  	if($rows){
    	foreach($rows as $row)
    	{
  	 	array_push($categs,$row);
    	}
    }
  	$site->page['sections'] = $categs;
  	return;
  }

  function register(){
    global $site, $lang, $db;

    if(isset($_SERVER["HTTP_REFERER"]))
    {
    	if(!isset($_POST["referer"])) $site->page["referer"] = $_SERVER["HTTP_REFERER"];
    	else $site->page["referer"] = $_POST["referer"];
    }
    if(!isset($_POST["login"]) || !isset($_POST["email"])){
      return;
    }

    if(isset($site->uri['slug'])){
      if($site->uri['slug'] == 'done'){
        $site->page['done'] = 'done';
        return;
      }elseif($site->uri['slug'] == 'sent'){
        $site->page['done'] = 'sent';
        return;
      }
    }

  	$login = isset($_POST["login"]) ? trim($_POST["login"]) : "";
  	$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $passwd1 = isset($_POST["passwd1"]) ? trim($_POST["passwd1"]) : "";
    $passwd2 = isset($_POST["passwd2"]) ? trim($_POST["passwd2"]) : "";

    if(empty($login)){
      $site->page['error'] = $lang->txt('empty_login');
      return;
    }

    if(empty($email)){
      $site->page['error'] = $lang->txt('register_incorrect_email');
      return;
    }

    if(!checkmail($email)){
      $site->page['error'] = $lang->txt('register_incorrect_email');
      return;
    }

    if(!checkmail($email)){
      $site->page['error'] = $lang->txt('register_incorrect_email');
      return;
    }

		if(strlen($passwd1) < 4 || strlen($passwd1) > 20){
			$site->page['error'] = $lang->txt('register_password_incorrect');
      return;
		}

		if($passwd1 != $passwd2){
      $site->page['error'] = $lang->txt('register_password1_password2_error');
      return;
    }

    if(!$this->available_login($login)){
      $site->page['error'] = $lang->txt('register_login_in_use');
      return;
    }

    if(!$this->available_email($email)){
      $site->page['error'] = $lang->txt('register_email_in_use');
      return;
    }

    $str = check_captcha();
    if(!empty($str)){
      $site->page['error'] = $str;
      return;
    }

    if($this->email_in_orders($email)){
      // заказ с этим мылом есть, автоматом назначаем пароль и высылаем инфо
      // на мыло, чтобы не перехватил кто-нить чужие заказы
      $passwd_auto = random_word(6);
      $passwd = encode_str($passwd_auto);
      $db->query("INSERT INTO ".$db->tables['boffice_user']."
        (`login`, `passwd`, `email`, `news`, `date_insert`, `admin`)
        VALUES ( '".try_addslashes($login)."', '".$passwd."',
        '".try_addslashes($email)."', '1', '".date('Y-m-d H:i:s')."',
        '0' )  ");

      $str = Resource_Page('emails/register_auto.html');
      $str = str_replace('[LOGIN]', $login, $str);
      $str = str_replace('[SITE_URL]', $this->vars['site_url'], $str);
      $str = str_replace('[PASSWORD]', $passwd_auto, $str);
      $subject = $lang->txt('register_subject_admin');
      $body = $site->vars['site_url']."<br>";
      $body .= $lang->txt('register_subject_admin')." - ".$login." (".$email.")<br>";
      
      // To admin
      /* переделать уведомления
      $site->send_notification($login, $email, $subject, $body, $site->vars['email_info'], $site->vars['name_short']);
      $subject = $lang->txt('register_subject_customer');
      
      $site->send_notification2($site->vars['name_short'], $site->vars['email_info'], $subject, $str, $email, $login);
      */
      $url = _link(array('register', 'sent'));
      return _redirect($url);
    }

    $passwd = encode_str($passwd1);
    $db->query("INSERT INTO ".$db->tables['boffice_user']."
        (`login`, `passwd`, `email`, `news`, `date_insert`, `admin`)
        VALUES ( '".try_addslashes($login)."', '".$passwd."',
        '".try_addslashes($email)."', '1', '".date('Y-m-d H:i:s')."',
        '0')  ");
    $passwd1 = stripslashes($passwd1);
    $login = stripslashes($login);
    $email = stripslashes($email);
    $str = Resource_Page('emails/registered.html');
    $str = str_replace('[LOGIN]', $login, $str);
    $str = str_replace('[SITE_URL]', $site->vars['site_url'], $str);
    $str = str_replace('[PASSWORD]', $passwd1, $str);
    $subject = $lang->txt('register_subject_admin');
    $body = $site->vars['site_url']."<br>";
    $body .= $lang->txt('register_subject_admin')." - ".$login." (".$email.")<br>";
    // To admin
    /* переделать уведомления
    $site->send_notification($login, $email, $subject, $body, $site->vars['email_info'], $site->vars['name_short']);
    // To user
    $subject = $lang->txt('register_subject_customer');
    $site->send_notification2($site->vars['name_short'], $site->vars['email_info'], $subject, $str, $email, $login);
    */
    session_start();
 	  $_SESSION = array();
		$_SESSION["U_ID"] = $db->insert_id;
		$_SESSION["U_LOGIN"] = md5($login);
		$_SESSION["U_PASS"] = md5(md5($passwd1));

	if(isset($_POST["referer"]) && !empty($_POST["referer"]))
      {
      	return _redirect($_POST["referer"]);
      }
    $url = _link(array('register', 'done'));
    return _redirect($url);
  }

  function available_login($login){
    global $db;
    if($login == "orders"){ return false; }
    if($login == "partner"){ return false; }
    if($login == "licences"){ return false; }
    if($login == "fb"){ return false; }
    if($login == "invoices"){ return false; }
    if($login == "man_projects"){ return false; }
    if($login == "projects"){ return false; }
    if($login == "deals"){ return false; }
    if($login == "man_deals"){ return false; }
    if($login == "man_invoices"){ return false; }
    if($login == "man_orders"){ return false; }
    if($login == "messages"){ return false; }
    $var = $db->get_var("SELECT id FROM ".$db->tables['boffice_user']."
        WHERE login = '".try_addslashes($login)."' ");
    if($db->num_rows == 0){ return true; }
    return false;
  }

  function available_email($email){
    global $db;
    $var = $db->get_var("SELECT id FROM ".$db->tables['boffice_user']."
        WHERE email = '".try_addslashes($email)."' ");
    if($db->num_rows == 0){ return true; }
    return false;
  }

  function email_in_orders($email){
    global $db;
    $var = $db->get_var("SELECT id FROM ".$db->tables['orders']."
        WHERE email = '".try_addslashes($email)."' ");
    if($db->num_rows == 0){ return false; }
    return true;
  }

  function get_project_status_name($id){
    $ar = array(
      "0" => 'Добавлен',
      "1" => 'На модерации',
      "2" => 'В работе',
      "3" => 'Отменен',
      "4" => 'Архив'      
    );

    if(isset($ar[$id])){
      return $ar[$id];
    }
    return $ar["0"];  
  }

  function man_projects(){
/*
СТАТУСЫ ПРОЕКТА
 			0 : 'добавлен';
			1 : 'ждет модерации';
      2 : 'в работе';
      3 : 'заявка на отмену'
      4 : 'отменен';
*/
    if(!isset($this->vars['admin']) || $this->vars['admin'] == 0 || $this->id == 0){ return error_403(); }
    global $lang, $site, $db;
    $site->page['title'] = "Проекты менеджера";
    $site->page['metatitle'] = "Проекты менеджера";
    $site->page['content'] = 'Проекты менеджера';
    $site->page['order_id'] = 0;
    if(isset($site->uri['id'])){
      if($site->uri['id'] > 0){
        $db2 = $this->db_n1seo();
        $site->page['user'] = $this->vars;
        $project = $db2->get_row("SELECT 
            Projects.*, 
            ProjectsData.projectContacts,
            ProjectsData.projectPhone, 
            ProjectsData.projectEmail,
            ProjectsData.projectTypeContract,
            ProjectsData.projectTypePay,
            ProjectsData.projectBudget,
            ProjectsData.projectTypeJob,
            ProjectsData.projectDescription,
            Users.userLogin
            
          FROM Projects 
          LEFT JOIN `ProjectsData` on Projects.id = ProjectsData.projectID
          LEFT JOIN `ProjectsUsers` on Projects.id = ProjectsUsers.projectID
          LEFT JOIN `Users` on Users.id = Projects.projectUserAdd
          WHERE (Projects.projectUserAdd = '".$this->vars['n1seoID']."' 
          OR ProjectsUsers.userID = '".$this->vars['n1seoID']."')
          AND Projects.id = '".$site->uri['id']."' group by Projects.id", ARRAY_A);

        if(!$project){ return error_403();}

        if(isset($_POST['project_message']) && isset($_POST['add_message'])){
          $message = isset($_POST['project_message']) ? trim($_POST['project_message']) : "";
          $mes_time = isset($_POST['time']) ? trim($_POST['time']) : "0";
          $mes_visible = isset($_POST['visible']) ? "1" : "0";
          $db2->query("INSERT INTO Messages (userFrom, projectID, 
            messageDate, messageText, messageType, messageVisible, 
            reportID, countTime) VALUES('".$this->vars['n1seoID']."', 
            '".$site->uri['id']."', '".date("Y-m-d H:i:s")."', 
            '".$db2->escape($message)."', '0', '".$mes_visible."', 
            '0', '".$db->escape($mes_time)."')");
          header("Location: /profile/man_projects/?id=".$site->uri['id']."&newmessage=1");
          exit;          
        }

        if(isset($_GET['edit'])){
          if(isset($_POST["update_project"])){

            $user_in = isset($_POST['users_in']) ? $_POST['users_in'] : array();
            if(is_array($user_in) && count($user_in) > 0){
              $db2->query("DELETE FROM ProjectsUsers WHERE projectID = '".$site->uri['id']."' ");
              foreach($user_in as $v){
                $db2->query("INSERT INTO ProjectsUsers (projectID, userID) 
                VALUES ('".$site->uri['id']."', '".$v."') ");
              }
            }                                      

            $projectName = isset($_POST["projectName"]) ? trim($_POST["projectName"]) : "";
            $projectURL = isset($_POST["projectURL"]) ? trim($_POST["projectURL"]) : "";
            $projectType = isset($_POST["projectType"]) ? trim($_POST["projectType"]) : "";
            $projectContacts = isset($_POST["projectContacts"]) ? trim($_POST["projectContacts"]) : "";
            $projectPhone = isset($_POST["projectPhone"]) ? trim($_POST["projectPhone"]) : "";
            $projectEmail = isset($_POST["projectEmail"]) ? trim($_POST["projectEmail"]) : "";
            $typecontract = isset($_POST["typecontract"]) ? trim($_POST["typecontract"]) : "";
            $projectBudget = isset($_POST["projectBudget"]) ? trim($_POST["projectBudget"]) : "";
            $projectDescription = isset($_POST["projectDescription"]) ? trim($_POST["projectDescription"]) : "";
            $projectStatus = isset($_POST["projectStatus"]) ? trim($_POST["projectStatus"]) : "0";

            $yandex = isset($_POST["yandex"]) ? trim($_POST["yandex"]) : "0";
            $google = isset($_POST["google"]) ? trim($_POST["google"]) : "0";
            $mailru = isset($_POST["mail"]) ? trim($_POST["mail"]) : "0";
            $rambler = isset($_POST["rambler"]) ? trim($_POST["rambler"]) : "0";
            $projectTypePay = isset($_POST["projectTypePay"]) ? trim($_POST["projectTypePay"]) : "0";

            $db2->query("UPDATE Projects SET 
                projectName = '".$db2->escape($projectName)."', 
                projectURL = '".$db2->escape($projectURL)."', 
                projectDateLastEdit = '".date("Y-m-d H:i:s")."', 
                projectUserLastEdit = '".$this->vars['n1seoID']."', 
                projectYandex = '".$db->escape($yandex)."', 
                projectGoogle = '".$db->escape($google)."', 
                projectRambler = '".$db->escape($rambler)."', 
                projectMail = '".$db->escape($mailru)."', 
                projectType = '".$db->escape($projectType)."',
                projectStatus = '".$db->escape($projectStatus)."'  

                WHERE id = '".$site->uri['id']."'
            ");
            
            $db2->query("UPDATE ProjectsData SET 
              projectContacts = '".$db2->escape($projectContacts)."', 
              projectPhone = '".$db2->escape($projectPhone)."', 
              projectEmail = '".$db2->escape($projectEmail)."', 
              projectTypeContract = '".$db2->escape($typecontract)."', 
              projectTypePay = '".$db2->escape($projectTypePay)."', 
              projectBudget = '".$db2->escape($projectBudget)."', 
              projectDescription = '".$db2->escape($projectDescription)."'
              
              WHERE projectID = '".$site->uri['id']."'
            ");

            header("Location: /profile/man_projects/?id=".$site->uri['id']."&updated=1");
            exit;

          }
        
          if(isset($_POST["delete_project"])){
            // удалим проект
            $db2->query("DELETE FROM Projects WHERE id = '".$site->uri['id']."' ");
            // удалим данные проекта
            $db2->query("DELETE FROM ProjectsData WHERE projectID = '".$site->uri['id']."' ");
            // удалим договора
            $db2->query("DELETE FROM Dogovors WHERE projectID = '".$site->uri['id']."' ");
            // удалим счета
            $db2->query("DELETE FROM Billings WHERE projectID = '".$site->uri['id']."' ");
            // удалим сообщения
            $db2->query("DELETE FROM Messages WHERE projectID = '".$site->uri['id']."' ");
            // удалим запросы            
            $rows = $db2->get_results("SELECT * FROM ProjectsReports WHERE projectID = '".$site->uri['id']."' ");
            if($rows && $db2->num_rows >0){
              foreach($rows as $row){
                $db2->query("DELETE FROM ProjectsReportsQuerys WHERE reportID = '".$row->id."' ");
              }
            }            
            $db2->query("DELETE FROM ProjectsReports WHERE projectID = '".$site->uri['id']."' ");
            
            $rows = $db2->get_results("SELECT * FROM ProjectsQuerys WHERE projectID = '".$site->uri['id']."' ");
            if($rows && $db2->num_rows >0){
              foreach($rows as $row){
                $db2->query("DELETE FROM NpcHistory WHERE queryID = '".$row->id."' ");
                $db2->query("DELETE FROM QueryTop10 WHERE queryID = '".$row->id."' ");
              }
            }            
            $db2->query("DELETE FROM ProjectsQuerys WHERE projectID = '".$site->uri['id']."' ");
            
            $db2->query("DELETE FROM ProjectsPositions WHERE projectID = '".$site->uri['id']."' ");
            $db2->query("DELETE FROM ProjectsHist WHERE projectID = '".$site->uri['id']."' ");
            // удалим отчеты 
            $db2->query("DELETE FROM QueryFinance WHERE projectID = '".$site->uri['id']."' ");
            $db2->query("DELETE FROM ProjectsUsers WHERE projectID = '".$site->uri['id']."' ");
            // удалим статистику по запросам
            $db2->query("DELETE FROM ProjectsStats WHERE projectID = '".$site->uri['id']."' ");

            header("Location: /profile/man_projects/?deleted=1");
            exit;
          }
        
          $users_in = $db2->get_results("SELECT id, userLogin FROM Users WHERE userType = '1' AND userStatus = '1' order by userLogin", ARRAY_A);
          $users_already = $db2->get_results("SELECT * FROM ProjectsUsers WHERE projectID = '".$site->uri['id']."' ");
          $ar = array();
          if($users_already){
            foreach($users_already as $k=>$v){
              $ar[$v->userID] = $v->userID;
            }
          }
          global $tpl;
          $tpl->assign('users_in', $users_in);                 
          $tpl->assign('users_already', $ar);                 
          $tpl->assign('project', $project);                 
          $tpl->assign('user', $this->vars);                 
          $site->page['title'] = 'Редактирование проекта';
          $site->page['content'] = $tpl->fetch("projects/project_edit.html");
          return;        
        }

        $users_in = $db2->get_results("SELECT ProjectsUsers.userID, 
            Users.userLogin, Users.userType 
            FROM ProjectsUsers
            LEFT JOIN Users ON ProjectsUsers.userID = Users.id 
            
            WHERE projectID = '".$site->uri['id']."' ", ARRAY_A);
        
        $project_dogs = $db2->get_results("SELECT * FROM Dogovors 
          WHERE projectID = '".$site->uri['id']."' order by id desc", ARRAY_A);
        $dogs_qty = $db2->num_rows;
        if($dogs_qty > ONPAGE){
          $project_dogs = $db2->get_results("SELECT * FROM Dogovors 
            WHERE projectID = '".$site->uri['id']."' order by id desc limit 0, ".ONPAGE." ", ARRAY_A);      
        }

        $project_invoices = $db2->get_results("SELECT * FROM Billings 
          WHERE projectID = '".$site->uri['id']."' order by id desc ", ARRAY_A);
        $inv_qty = $db2->num_rows;
        
        if($inv_qty > ONPAGE){
          $project_invoices = $db2->get_results("SELECT * FROM Billings 
            WHERE projectID = '".$site->uri['id']."' order by id desc limit 0, ".ONPAGE." ", ARRAY_A);
        }

        $project_messages = $db2->get_results("SELECT * FROM Messages 
          WHERE projectID = '".$site->uri['id']."' order by id desc limit 0,20 ", ARRAY_A);


        global $tpl;
        $project['status_txt'] = $this->get_project_status_name($project['projectStatus']);
        $site->page['users_in'] = $users_in; 
        $site->page['dogs_qty'] = $dogs_qty; 
        $site->page['inv_qty'] = $inv_qty; 
        $site->page['title'] = $project["projectName"]; 
        if($project["projectUserAdd"] != $this->vars['n1seoID']){
          $site->page['title'] = $project["projectName"]." (гостевой)";         
        }
        $tpl->assign('project', $project);                 
        $tpl->assign('project_dogs', $project_dogs);                 
        $tpl->assign('project_invoices', $project_invoices);                 
        $tpl->assign('project_messages', $project_messages);                 
        $tpl->assign('page', $site->page);                 
        $site->page['content'] = $tpl->fetch("projects/project_info.html");
        
      }else{
        if(isset($_POST['projectName']) && !empty($_POST['projectName']) && isset($_POST['add_project'])){
          $projectName = trim($_POST['projectName']);
          if(!empty($projectName)){
            $db2 = $this->db_n1seo();
            $projectURL = isset($_POST["projectURL"]) ? trim($_POST["projectURL"]) : "";
            $projectType = isset($_POST["projectType"]) ? trim($_POST["projectType"]) : "";
            $projectContacts = isset($_POST["projectContacts"]) ? trim($_POST["projectContacts"]) : "";
            $projectPhone = isset($_POST["projectPhone"]) ? trim($_POST["projectPhone"]) : "";
            $projectEmail = isset($_POST["projectEmail"]) ? trim($_POST["projectEmail"]) : "";
            $typecontract = isset($_POST["typecontract"]) ? trim($_POST["typecontract"]) : "";
            $projectBudget = isset($_POST["projectBudget"]) ? trim($_POST["projectBudget"]) : "";
            $projectDescription = isset($_POST["projectDescription"]) ? trim($_POST["projectDescription"]) : "";

            $yandex = isset($_POST["yandex"]) ? trim($_POST["yandex"]) : "0";
            $google = isset($_POST["google"]) ? trim($_POST["google"]) : "0";
            $mailru = isset($_POST["mail"]) ? trim($_POST["mail"]) : "0";
            $rambler = isset($_POST["rambler"]) ? trim($_POST["rambler"]) : "0";
            $projectTypePay = isset($_POST["projectTypePay"]) ? trim($_POST["projectTypePay"]) : "0";

            $db2->query("INSERT INTO Projects (projectName, projectURL, 
                projectDateAdd, projectDateLastEdit, projectUserAdd, 
                projectUserLastEdit, projectStatus, projectYandex, 
                projectGoogle, projectRambler, projectMail, projectType, 
                projectWhois, projectAutoReport) 
                VALUES ('".$db2->escape($projectName)."', 
                '".$db2->escape($projectURL)."',
                '".date("Y-m-d H:i:s")."', 
                '0000-00-00 00:00:00', 
                '".$this->vars['n1seoID']."', '0', 
                '0', '".$db->escape($yandex)."', 
                '".$db->escape($google)."', '".$db->escape($rambler)."',
                '".$db->escape($mailru)."', 
                '".$db->escape($projectType)."', '', '0') 
            ");
            $project_id = $db2->insert_id;
            
            $db2->query("INSERT INTO ProjectsData (projectID, 
              projectContacts, projectPhone, projectEmail, 
              projectTypeContract, projectTypePay, projectBudget, 
              projectTypeJob, projectDescription) 
              VALUES ('".$project_id."', 
              '".$db2->escape($projectContacts)."', 
              '".$db2->escape($projectPhone)."', 
              '".$db2->escape($projectEmail)."', 
              '".$db2->escape($typecontract)."', 
              '".$db2->escape($projectTypePay)."', 
              '".$db2->escape($projectBudget)."', 
              '0', '".$db2->escape($projectDescription)."') ");

            header("Location: /profile/man_projects/?id=".$project_id."&newproject=1");
            exit;
          }
        }
        global $tpl;
        $site->page['title'] = 'Добавление проекта';
        $site->page['content'] = $tpl->fetch("projects/project_add.html");
      }
    }else{
      $db2 = $this->db_n1seo();
      if(isset($site->uri['mode']) && $site->uri['mode'] == "guest"){
        $site->page['title'] = "Гостевые проекты";
        $projects = $db2->get_results("SELECT p.*, Users.userLogin 
          FROM Projects p, ProjectsUsers u, Users
          WHERE p.projectUserAdd <> '".$this->vars['n1seoID']."' 
          AND p.id = u.projectID 
          AND u.userID = '".$this->vars['n1seoID']."'
          AND Users.id = p.projectUserAdd  
          order by projectStatus, p.id desc ", ARRAY_A);
      }elseif(isset($site->uri['mode']) && $site->uri['mode'] == "admin" && $this->vars['settings'] == 1){
        $site->page['title'] = "Администратор";
        $projects = $db2->get_results("SELECT p.*, Users.userLogin 
          FROM Projects p, ProjectsUsers u, Users
          WHERE p.projectUserAdd <> '".$this->vars['n1seoID']."' 
          AND p.id = u.projectID 
          AND u.userID <> '".$this->vars['n1seoID']."'
          AND Users.id = p.projectUserAdd
          group by p.id   
          order by p.projectStatus, p.id desc ", ARRAY_A);
      }else{
        $projects = $db2->get_results("SELECT * FROM Projects 
          WHERE projectUserAdd = '".$this->vars['n1seoID']."' 
          order by projectStatus, id desc ", ARRAY_A);
      }
    
      global $tpl;
      $tpl->assign('user', $this->vars);                 
      $tpl->assign('list_projects', $projects);                 
      $site->page['content'] = $tpl->fetch("projects/project_list.html");
    }
    return;
  }

  function man_deals(){
    if(!isset($this->vars['admin']) || $this->vars['admin'] == 0 || $this->id == 0){ return error_403(); }
    global $lang, $site, $db;
    $site->page['title'] = "Договоры менеджера";
    $site->page['metatitle'] = "Договоры менеджера";
    $site->page['content'] = 'Договоры менеджера';
    $site->page['order_id'] = 0;
    if(isset($site->uri['id'])){
      if($site->uri['id'] > 0){
        return $this->man_deals_view($site->uri['id']);
      }else{
        return $this->man_deals_add();      
      }
    }else{
      return $this->man_deals_list();
    }
    return;
  }

  function man_deals_add(){ // добавление договора
    global $tpl, $site;
    $db2 = $this->db_n1seo();
                 
      $projectID = isset($_POST["projectID"]) ? trim($_POST["projectID"]) : "0";
      $buh_number = isset($_POST["buh_number"]) ? trim($_POST["buh_number"]) : "";
      $dog_date = isset($_POST["dog_date"]) ? trim($_POST["dog_date"]) : "";
      if(empty($dog_date)){
        $dog_date = date("Y-m-d H:i:s");
      }else{
        $dog_date = date("Y-m-d H:i:s", strtotime($dog_date));
      }
      $dog_subject = isset($_POST["dog_subject"]) ? trim($_POST["dog_subject"]) : "";
      $dog_link = isset($_POST["dog_link"]) ? trim($_POST["dog_link"]) : "";
      $company_name = isset($_POST["company_name"]) ? trim($_POST["company_name"]) : "";
      $company_phone = isset($_POST["company_phone"]) ? trim($_POST["company_phone"]) : "";
      $company_inn = isset($_POST["company_inn"]) ? trim($_POST["company_inn"]) : "";
      $company_kpp = isset($_POST["company_kpp"]) ? trim($_POST["company_kpp"]) : "";
      $company_address = isset($_POST["company_address"]) ? trim($_POST["company_address"]) : "";
      $company_info = isset($_POST["company_info"]) ? trim($_POST["company_info"]) : "";
    
    if(isset($_POST['add'])){
      $db2->query("INSERT INTO Dogovors 
        (projectID, whoCreate, dateCreate, buh_number, dog_date, 
        dog_subject, dog_link, company_name, company_phone, 
        company_inn, company_kpp, company_address, company_info)
        VALUES (
        '".$db2->escape($projectID)."',
        '".$this->vars['login']."',
        '".date("Y-m-d H:i:s")."',
        '".$db2->escape($buh_number)."',
        '".$db2->escape($dog_date)."',
        '".$db2->escape($dog_subject)."',
        '".$db2->escape($dog_link)."',
        '".$db2->escape($company_name)."',
        '".$db2->escape($company_phone)."',
        '".$db2->escape($company_inn)."',
        '".$db2->escape($company_kpp)."',
        '".$db2->escape($company_address)."',
        '".$db2->escape($company_info)."'
        ) ");
      $id = $db2->insert_id;
      //$db2->debug();
      //exit;
      if(empty($dog_date)){
        $db2->query("UPDATE Dogovors SET dog_date = '".date("Y-m-d H:i:s")."' WHERE id = '".$id."' ");
      }
        
      if(empty($buh_number)){
        $db2->query("UPDATE Dogovors SET buh_number = '".$id."/".date("y")."' WHERE id = '".$id."' ");
      }

      header("Location: /profile/man_deals/?id=".$id."&added=1");
      exit;
      
    }
    
    $projects = $db2->get_results("SELECT Projects.id, Projects.projectName 
        FROM Projects
        LEFT JOIN `ProjectsUsers` on Projects.id = ProjectsUsers.projectID
        WHERE (Projects.projectUserAdd = '".$this->vars['n1seoID']."' 
        OR ProjectsUsers.userID = '".$this->vars['n1seoID']."')  
        AND projectStatus < '3' order by projectName ", ARRAY_A);
    //$db2->debug();        

    $tpl->assign('projects', $projects);                 
    $tpl->assign('user', $this->vars);                 
    $site->page['title'] = "Добавление договора";
    $site->page['content'] = $tpl->fetch("dogovors/dog_add.html");
    
  }

  function man_deals_view($id){ // Инфо о договоре
    global $site;
    $db2 = $this->db_n1seo();
    $site->page['user'] = $this->vars;
    
    if(isset($_POST["update"])){
      $buh_number = isset($_POST["buh_number"]) ? trim($_POST["buh_number"]) : "";
      $dog_date = isset($_POST["dog_date"]) ? trim($_POST["dog_date"]) : "";
      if($dog_date == ""){
        $dog_date = date("Y-m-d H:i:s");
      }else{
        $dog_date = date("Y-m-d H:i:s", strtotime($dog_date));
      }
      $dog_subject = isset($_POST["dog_subject"]) ? trim($_POST["dog_subject"]) : "";
      $dog_link = isset($_POST["dog_link"]) ? trim($_POST["dog_link"]) : "";
      $company_name = isset($_POST["company_name"]) ? trim($_POST["company_name"]) : "";
      $company_phone = isset($_POST["company_phone"]) ? trim($_POST["company_phone"]) : "";
      $company_inn = isset($_POST["company_inn"]) ? trim($_POST["company_inn"]) : "";
      $company_kpp = isset($_POST["company_kpp"]) ? trim($_POST["company_kpp"]) : "";
      $company_address = isset($_POST["company_address"]) ? trim($_POST["company_address"]) : "";
      $company_info = isset($_POST["company_info"]) ? trim($_POST["company_info"]) : "";
      
      $db2->query("UPDATE Dogovors SET
        buh_number = '".$db2->escape($buh_number)."',
        dog_date = '".$db2->escape($dog_date)."',
        dog_subject = '".$db2->escape($dog_subject)."',
        dog_link = '".$db2->escape($dog_link)."',
        company_name = '".$db2->escape($company_name)."',
        company_phone = '".$db2->escape($company_phone)."',
        company_inn = '".$db2->escape($company_inn)."',
        company_kpp = '".$db2->escape($company_kpp)."',
        company_address = '".$db2->escape($company_address)."',
        company_info = '".$db2->escape($company_info)."'
      WHERE id = '".$id."'      
      ");
      header("Location: /profile/man_deals/?id=".$id."&updated=1");
      exit;
    }
    
    if(isset($_POST["delete"])){
      // удалим договора
      $db2->query("DELETE FROM Dogovors WHERE id = '".$id."' ");
      // удалим счета
      $db2->query("DELETE FROM Billings WHERE billDogovorID = '".$id."' ");
      header("Location: /profile/man_deals/?deleted=1");
      exit;
    }
    
    $dogs = $db2->get_row("SELECT 
            Dogovors.*, 
            Projects.projectName,
            Projects.projectURL, 
            Projects.projectType
            
          FROM Dogovors 
          LEFT JOIN `Projects` on Projects.id = Dogovors.projectID
          LEFT JOIN `ProjectsUsers` on ProjectsUsers.projectID = Projects.id
          WHERE (Projects.projectUserAdd = '".$this->vars['n1seoID']."' 
          OR ProjectsUsers.userID = '".$this->vars['n1seoID']."') 
          AND Dogovors.id = '".$id."' 
           ", ARRAY_A);

    global $tpl;
    if($dogs){
      $site->page['title'] = "Информация о договоре ".$dogs['buh_number']." от ".date("d.m.Y", strtotime($dogs['dog_date']));
      $invoices = $db2->get_results("SELECT * FROM Billings 
          WHERE billDogovorID = '".$dogs['id']."' order by id desc ", ARRAY_A);
      $tpl->assign('invoices', $invoices);                 
    }                                   

    $tpl->assign('dog_info', $dogs);                 
    $tpl->assign('user', $this->vars);                 
    
    if(isset($_GET['edit'])){
      $site->page['content'] = $tpl->fetch("dogovors/dog_edit.html");
    }else{
      $site->page['content'] = $tpl->fetch("dogovors/dog_info.html");
    }
  }
    
  function man_deals_list(){ // Список своих и гостевых договоров
    global $site;
    $site->page['title'] = "Договоры менеджера";
    $db2 = $this->db_n1seo();
    $site->page['user'] = array("aa"=>"bb","cc"=>"dd");
    $str_projectid = isset($_GET['project_id']) ? " AND Dogovors.projectID = '".intval($_GET['project_id'])."' " : "";
    $query = "SELECT 
            Dogovors.*, 
            Projects.projectName,
            Projects.projectURL, 
            Projects.projectType
            
          FROM Dogovors 
          LEFT JOIN `Projects` on Projects.id = Dogovors.projectID
          LEFT JOIN `ProjectsUsers` on ProjectsUsers.projectID = Projects.id
          WHERE (Projects.projectUserAdd = '".$this->vars['n1seoID']."' 
          OR ProjectsUsers.userID = '".$this->vars['n1seoID']."') $str_projectid 
          group by Dogovors.id
          order by Dogovors.id desc ";
    $dogs = $db2->get_results($query, ARRAY_A);
           
   	// PAGE LIMITS
  	$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
    $limit_Start = $page*ONPAGE; // for pages generation
    $limit = "limit $limit_Start, ".ONPAGE;
    $all_results = $db2->num_rows;
    $query = $query." ".$limit;
    $ar = array('profile', 'man_deals');
    $strPages = _pages_ar($all_results, $page, ONPAGE, $ar);
    $dogs = $db2->get_results($query, ARRAY_A);    

    global $tpl;
    $tpl->assign('all_results', $all_results);                 
    $tpl->assign('pages', round($all_results/ONPAGE));                 
    $tpl->assign('user', $this->vars);                 
    $tpl->assign('list_dogs', $dogs);                 
    $site->page['content'] = $tpl->fetch("dogovors/dog_list.html");
  
  }

  function man_invoices(){
    if(!isset($this->vars['admin']) || $this->vars['admin'] == 0 || $this->id == 0){ return error_403(); }
    global $lang, $site, $db;
    $site->page['title'] = "Счета менеджера";
    $site->page['metatitle'] = "Счета менеджера";
    $site->page['content'] = 'Счета менеджера';
    $site->page['order_id'] = 0;
    if(isset($site->uri['id'])){
      if($site->uri['id'] > 0){
        return $this->man_invoices_view($site->uri['id']);
      }else{
        return $this->man_invoices_add();      
      }
    }else{
      return $this->man_invoices_list();
    }
    
    return;
  }

  function man_invoices_view($id){
    global $site;
    $db2 = $this->db_n1seo();
    $site->page['user'] = $this->vars;
    
    if(isset($_POST["update"])){       
      //_POST["project_id"]	8
      $billDate = isset($_POST["billDate"]) ? trim($_POST["billDate"]) : "";
      if(empty($billDate)){ $billDate = date("Y-m-d"); }
      $actdate = isset($_POST["actdate"]) ? trim($_POST["actdate"]) : "";
      if(empty($actdate)){ $actdate = "0000-00-00"; }

      $billSumm = isset($_POST["billSumm"]) ? trim($_POST["billSumm"]) : "0";
      $billSumm = str_replace(",", ".", $billSumm);
      $billSumm = floatval($billSumm);

      $billType = isset($_POST["billType"]) ? trim($_POST["billType"]) : "";
      $billData = isset($_POST["billData"]) ? trim($_POST["billData"]) : "";
      $billstatus = isset($_POST["status"]) ? trim($_POST["status"]) : "0";

      $db2->query("UPDATE Billings SET
        billDate =  '".$db2->escape($billDate)."', 
        actdate = '".$db2->escape($actdate)."', 
        billSumm = '".$db2->escape($billSumm)."', 
        billType = '".$db2->escape($billType)."', 
        billData = '".$db2->escape($billData)."', 
        status = '".$db2->escape($billstatus)."'   
      WHERE id = '".$id."'      
      ");
      header("Location: /profile/man_invoices/?id=".$id."&updated=1");
      exit;
    }
    
    if(isset($_POST["delete"])){
      $project_id = isset($_POST["project_id"]) ? $_POST["project_id"] : 0; 
      // удалим счет
      $db2->query("DELETE FROM Billings WHERE id = '".$id."' ");
      header("Location: /profile/man_projects/?id=".$project_id."&deleted=1");
      exit;
    }
    
    $invoice = $db2->get_row("SELECT 
            Billings.*, 
            Projects.projectName,
            Projects.projectURL, 
            Projects.projectType,
            Dogovors.id as dog_id,
            Dogovors.buh_number,
            Dogovors.dog_date,
            Dogovors.company_name,
            Dogovors.company_phone
            
          FROM Billings 
          LEFT JOIN `Dogovors` on Dogovors.id = Billings.billDogovorID
          LEFT JOIN `Projects` on Projects.id = Billings.projectID
          LEFT JOIN `ProjectsUsers` on ProjectsUsers.projectID = Projects.id
          WHERE (Projects.projectUserAdd = '".$this->vars['n1seoID']."' 
          OR ProjectsUsers.userID = '".$this->vars['n1seoID']."') 
          AND Billings.id = '".$id."' group by Billings.id
           ", ARRAY_A);

    global $tpl;
    if($invoice){
      $prefix = !empty($invoice['billType']) ? $invoice['billType']."-" : ""; 
      $site->page['title'] = "Счет ".$prefix.$invoice['id']." от ".date("d.m.Y", strtotime($invoice['billDate']));
    }                                   

    $tpl->assign('invoice', $invoice);                 
    $tpl->assign('user', $this->vars);                 
    
    if(isset($_GET['edit'])){
      $site->page['content'] = $tpl->fetch("invoices/inv_edit.html");
    }else{
      $site->page['content'] = $tpl->fetch("invoices/inv_info.html");
    }       
  }

  function man_invoices_add(){
    global $tpl, $site;
    $db2 = $this->db_n1seo();
                 
      $projectID = isset($_POST["project_id"]) ? trim($_POST["project_id"]) : "0";
      $billDogovorID = isset($_POST["dogovor_id"]) ? trim($_POST["dogovor_id"]) : "0";
      $billDate = isset($_POST["billDate"]) ? trim($_POST["billDate"]) : "";
      if(empty($billDate)){ $billDate = date("Y-m-d"); }
      $actdate = isset($_POST["actdate"]) ? trim($_POST["actdate"]) : "0000-00-00";
      $billSumm = isset($_POST["billSumm"]) ? trim($_POST["billSumm"]) : "0";
      $billSumm = str_replace(",", ".", $billSumm);
      $billSumm = floatval($billSumm);
      $billType = isset($_POST["billType"]) ? trim($_POST["billType"]) : "0";
      $billData = isset($_POST["billData"]) ? trim($_POST["billData"]) : "";
      $billstatus = isset($_POST["status"]) ? trim($_POST["status"]) : "0";
	  	$unique = md5(microtime().implode('', $_POST));      
    
    if(isset($_POST['add'])){
      $db2->query("INSERT INTO Billings (billDate, billType, billSumm, 
        billData, billCreat, billUnique, billDogovorID, billDogovor, 
        billDogovorDate, projectID, actdate, status) VALUES (
        '".$db2->escape($billDate)."',
        '".$db2->escape($billType)."',
        '".$db2->escape($billSumm)."',
        '".$db2->escape($billData)."',
        '".$db2->escape($this->login)."',
        '".$db2->escape($unique)."',
        '".$db2->escape($billDogovorID)."',
        '',
        '',
        '".$db2->escape($projectID)."',
        '".$db2->escape($actdate)."',
        '".$db2->escape($billstatus)."'        
        )");

      $id = $db2->insert_id;
      header("Location: /profile/man_invoices/?id=".$id."&new=1");
      exit;
    }
    $dogovor_id = isset($_GET['dogovor_id']) ? intval($_GET['dogovor_id']) : 0; 
    $project = $db2->get_row("SELECT 
          Projects.id as project_id, 
          Projects.projectName,
          Projects.projectType,
          Dogovors.id as dogovor_id,           
          Dogovors.buh_number,           
          Dogovors.dog_date,           
          Dogovors.dog_subject,           
          Dogovors.whoCreate,           
          Dogovors.company_name           
        FROM Projects
        LEFT JOIN `Dogovors` on Projects.id = Dogovors.projectID
        LEFT JOIN `ProjectsUsers` on Projects.id = ProjectsUsers.projectID
        WHERE (Projects.projectUserAdd = '".$this->vars['n1seoID']."' 
        OR ProjectsUsers.userID = '".$this->vars['n1seoID']."')  
        AND Projects.projectStatus < '3' 
        AND Dogovors.id = '".$dogovor_id."' ", ARRAY_A);

    if(!$project || $db2->num_rows == 0){
      return error_403();
    }         


    $query = "SELECT *             
          FROM Billings 
          WHERE billDogovorID = '".$dogovor_id."'
          order by id desc ";
    $invoices = $db2->get_results($query, ARRAY_A);

    // dogovor_id
    $tpl->assign('invoice', $project);                 
    $tpl->assign('list_invoices', $invoices);                 
    $tpl->assign('user', $this->vars);                 
    $site->page['title'] = "Новый счет";
    $site->page['content'] = $tpl->fetch("invoices/inv_add.html");

  }

  function man_invoices_list(){
    global $site;
    $site->page['title'] = "Выставленные счета";
    $db2 = $this->db_n1seo();
    $str_projectid = isset($_GET['project_id']) ? " AND Billings.projectID = '".intval($_GET['project_id'])."' " : "";
    $query = "SELECT 
            Billings.*, 
            Projects.projectName,
            Projects.projectURL, 
            Projects.projectType
            
          FROM Billings 
          LEFT JOIN `Projects` on Projects.id = Billings.projectID
          LEFT JOIN `ProjectsUsers` on ProjectsUsers.projectID = Billings.projectID
          WHERE (Projects.projectUserAdd = '".$this->vars['n1seoID']."' 
          OR ProjectsUsers.userID = '".$this->vars['n1seoID']."') $str_projectid 
          group by Billings.id
          order by Billings.id desc ";
    $invoices = $db2->get_results($query, ARRAY_A);
           
   	// PAGE LIMITS
  	$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
    $limit_Start = $page*ONPAGE; // for pages generation
    $limit = "limit $limit_Start, ".ONPAGE;
    $all_results = $db2->num_rows;
    $query = $query." ".$limit;
    $ar = array('profile', 'man_invoices');
    //$strPages = _pages_ar($all_results, $page, ONPAGE, $ar);
    $invoices = $db2->get_results($query, ARRAY_A);    

    global $tpl;
    $tpl->assign('all_results', $all_results);                 
    $tpl->assign('pages', round($all_results/ONPAGE));                 
    $tpl->assign('user', $this->vars);                 
    $tpl->assign('list_invoices', $invoices);                 
    $site->page['content'] = $tpl->fetch("invoices/inv_list.html");

  }
  

  function man_orders(){
    if(!isset($this->vars['admin']) || $this->vars['admin'] == 0 || $this->id == 0 || $this->vars['orders'] != 1){ return error_403(); }
    global $lang, $site, $db;
    $site->page['title'] = "Заказы менеджера";
    $site->page['metatitle'] = "Заказы менеджера";
    $site->page['content'] = 'Заказы менеджера';
    $site->page['order_id'] = 0;
    if(isset($site->uri['id'])){
    }else{
    }
    return;
  }


}
?>