<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.lower.php'); $this->register_modifier("lower", "tpl_modifier_lower");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.truncate.php'); $this->register_modifier("truncate", "tpl_modifier_truncate");   if (! empty ( $this->_vars['site_vars']['lang'] )): ?>
<html lang="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['site_vars']['lang'], 'truncate', 'plugin', 1, 2, ""), 'lower', 'plugin', 1); ?>
">
<?php else: ?>
<html>
<?php endif; ?>
<head>
    <title>SIMPLA.es</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta content="SIMPLA.es" name="keywords">
    <meta content="SIMPLA.es" name="description">
    <meta content="global" http-equiv="distribution">
    <link href="css/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link REL="SHORTCUT ICON" href="favicon.ico">
    <link rel="StyleSheet" href="<?php echo $this->_vars['tpl']; ?>
css/style.css" type="text/css">
    
</head>
<body bgcolor="#ffffff" leftmargin="0" marginheight="0" marginwidth="0" rightmargin="0" topmargin="0">

<h1 class="center"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "auth",'key3' => "title"), $this);?></h1>

<form method="post">
	<?php $this->assign('adm_folder', "/".constant('ADMIN_FOLDER')."/?"); ?>
	<?php if (isset ( $_SERVER['REQUEST_URI'] ) && $this->_run_modifier($_SERVER['REQUEST_URI'], 'strlen', 'PHP', 1) > $this->_run_modifier($this->_vars['adm_folder'], 'strlen', 'PHP', 1)): ?>
		<input type="hidden" name="r" value="<?php echo $_SERVER['REQUEST_URI']; ?>
">
	<?php endif; ?>

<table class="table center" width="400">
	<tr>
		<td width="30%" class="right"><div class="mt-20"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "login"), $this);?></div></td>
		<td><input type="text" style="width:80%;" name="bo_login" class="mt-20 left" /></td>
	</tr>
	<tr>
		<td class="right"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "password"), $this);?></td>
		<td><input type="password" style="width:80%;" name="bo_password" class="left" /></td>
	</tr>
	<tr><td colspan="2" class="center"><input type="submit" name="bo_button" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "auth",'key3' => "authorize"), $this);?>" class="mt-10 mb-20" /></td></tr>
	</table></form>

        <table border="0" align="center">
          <tr>
        	<td align="center" valign="middle"><a href="../" target="_blank"><i class="fa fa-external-link"></i></a> <a href="../" target="_blank"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "to_website"), $this);?></a> </td>
            <?php if ($this->_vars['admin_vars']['bo_user']['id'] == 0): ?><td>
            <a href="/forget_password/"><i class="fa fa-key"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "reset_password"), $this);?></a>
            </td><?php endif; ?>
        	</tr>			
        </table>

		<?php if ($this->_vars['admin_vars']['multilang'] == 1): ?>
          <div class="" style="text-align:center;">
			<i class="fa fa-language"></i> 
           <?php if (count((array)$this->_vars['langs'])): foreach ((array)$this->_vars['langs'] as $this->_vars['link'] => $this->_vars['lang']): ?>
            <?php if ($this->_vars['currentlang'] == $this->_vars['link']): ?>
                <b><?php echo $this->_vars['lang']; ?>
</b>
            <?php else: ?>
                <a href="?setlang=<?php echo $this->_vars['link']; ?>
"><?php echo $this->_vars['lang']; ?>
</a>
            <?php endif; ?>
           <?php endforeach; endif; ?>
		   </div>
        <?php endif; ?>

        
</BODY></html>