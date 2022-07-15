<?php

  function encode_str($str, $authkey='')
  {
    if (!defined('AUTHKEY')) { define('AUTHKEY', 'simpla.es'); }
    if(empty($authkey)) { $authkey = AUTHKEY; }
    return md5(md5($str).'-'.md5($authkey));
  }


?>

<html>
<head>
<title><?php echo $_SERVER['HTTP_HOST']; ?> Admin PRO</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="Администрирование сайта" name="keywords" />
<meta content="Администрирование сайта" name="description" />
<meta content="global" http-equiv="distribution" />
<link REL="SHORTCUT ICON" href="favicon.ico" />
<link rel="StyleSheet" href="style.css" type="text/css" />
</head>
<body>

<h2>Создание зашифрованного пароля</h2>

<form method="POST">
<p>Ключ <input type="text" name="authkey" value="<?php if(isset($_POST['authkey'])){ echo trim($_POST['authkey']); }?>" size=30 /></p>
<p>Пароль <input type="text" name="pass" value="<?php if(isset($_POST['pass'])){ echo trim($_POST['pass']); }?>" size=30 /></p>
<p><input type="submit" name="create" value="Шифровать" /></p>
</form>


<?php

if(isset($_POST['pass']) && isset($_POST['authkey'])){
  $newpass = encode_str(trim($_POST['pass']), trim($_POST['authkey']));
  echo "<p>Зашифрованный пароль: <b>".$newpass."</b></p>";
}


?>

</body></html>