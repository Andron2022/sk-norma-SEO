<?php

	$session_id = session_id();
	//session_start();

function _header()
{
	
}

function remind_form()
{
	global $db, $tpl;
	$tpl->display("auth/remind.html");
}

return;
$action = get_param("action","");
$do = get_param("do","");

if($action == "remind")
{	if($do == "") remind_form();
	elseif($do == "remind")
	{
	  $name = get_param("name","");
		$email = get_param("email","");
		$query = "SELECT * FROM ".$db->tables['users']." WHERE login = '$name' AND email = '$email'";

		$row = $db->get_row($query);
		_header();
		if($row)
		{			$message = "Ваш пароль к панели администрирования: {$row->passwd}";
			mail($email,"Восстановление пароля",$message,"From: ".$site->vars["email_info"]);
            echo GetMessage('admin', 'user', 'password_sent');

  		}
		else
		{
		  echo GetMessage('admin', 'user', 'notfound');
		}
		echo "</body></html>";
	}
	exit;
}

?>