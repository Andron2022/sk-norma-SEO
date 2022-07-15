<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.strip.php'); $this->register_modifier("strip", "tpl_modifier_strip");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.lower.php'); $this->register_modifier("lower", "tpl_modifier_lower");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.truncate.php'); $this->register_modifier("truncate", "tpl_modifier_truncate");   if (! empty ( $this->_vars['site_vars']['lang'] )): ?>
<html lang="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['site_vars']['lang'], 'truncate', 'plugin', 1, 2, ""), 'lower', 'plugin', 1); ?>
">
<?php else: ?>
<html>
<?php endif; ?>
  <head>
    <title><?php echo $this->_vars['metatitle']; ?>
</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta content="SIMPLA.es" name="keywords">
    <meta content="SIMPLA.es" name="description">
    <meta content="global" http-equiv="distribution">
    <link href="css/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link rel="icon" href="<?php echo $this->_vars['tpl']; ?>
favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="<?php echo $this->_vars['tpl']; ?>
favicon.ico" type="image/x-icon">	
    <link rel="StyleSheet" href="<?php echo $this->_vars['tpl']; ?>
css/style.css" type="text/css">
    <link type="text/css" href="css/ui-lightness/jquery-ui-1.8.2.custom.css" rel="stylesheet" />
	
    <script>
      //Определяется переменная, которая будет доступна для 
      // всех JavaScript, подключаемых на данной странице
      var close_window = '<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "close_window"), $this);?>';
    </script>

    

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script type="text/javascript" src="js/admin.js"></script>
    <script type="text/javascript" src="../module/js/script.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.8.2.custom.min.js"></script>
    
  	<script type="text/javascript" src="ckfinder/ckeditor/ckeditor.js"></script>
  	<script type="text/javascript" src="ckfinder/ckfinder.js"></script>

</head>

<body bgcolor="#ffffff" leftmargin=0 marginheight=0 marginwidth=0 rightmargin=0 topmargin=0>

<?php if (! empty ( $this->_vars['admin_vars'] )): ?>

  <table border="0" width="100%" cellspacing="0" cellpadding="0" id="table1" class="nomargin">
    <tr>
      <td colspan=3 background="<?php echo $this->_vars['tpl']; ?>
images/header-bg-top.gif" height="5">
            <?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("../langselector.tpl", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
		</td>
	</tr>
	<tr>
		<td background="<?php echo $this->_vars['tpl']; ?>
images/header-bg-left.gif" width="206" height="57"><p align="center"><a href="./" style="font-size:24px;"><?php echo show_var_img_tpl(array('qty' => "1",'width' => "120",'height' => "33",'from' => "img_cmslogo",'default' => $this->_vars['tpl']."images/logo.png",'align' => "bottom"), $this);?></a></td>
		<td background="<?php echo $this->_vars['tpl']; ?>
images/header-bg-center.gif" width="21" height="57">&nbsp;</td>
		<td background="<?php echo $this->_vars['tpl']; ?>
images/header-bg-right.gif" height="57" valign="top"><table border=0 width="100%" align=left cellpadding=5 cellspacing=0 class="nomargin" style="margin-top:5px;">
        <tr>
          <td style="color:#ffffff;text-transform:uppercase;" width="60%">
          <a href="?action=info" class="white"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "main_menu"), $this);?></a> |
          <a href="?action=settings" class="white"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "settings"), $this);?></a>
            <span style="float:right;">
			<a href="/<?php echo constant('ADMIN_FOLDER'); ?>
/" class="white"><i class="fa fa-home" style="color:white;"></i></a>
			<?php if ($this->_vars['check_fb'] > 0): ?><a href="?action=feedback" class="white" style="padding-left:15px;"><i class="fa fa-envelope" style="color:white;"></i> <?php echo $this->_vars['check_fb']; ?>
</a><?php endif; ?>
            <?php if ($this->_vars['check_orders'] > 0): ?> <a href="?action=orders" class="white" style="padding-left:15px;"><i class="fa fa-shopping-cart" style="color:white;"></i> <?php echo $this->_vars['check_orders']; ?>
</a><?php endif; ?>
			
			<?php if (! empty ( $this->_vars['site_vars']['sys_licence_prolong'] )): ?>
				<a href="?action=settings&do=licence" class="white"><i class="fa fa-info-circle" style="color:#cc3300;" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "update_support"), $this);?>"></i></a>
			<?php endif; ?>
			
			</span></td><td align="right" style="color:#ffffff; padding-right: 10px; ">
          
		  <?php if (! empty ( $this->_vars['user']['avatar'] )): ?>
		  <img src="<?php echo $this->_vars['user']['avatar']; ?>
" style="max-height:20px;" />
		  <?php else: ?>
		  <i class="fa fa-user" ></i>
		  <?php endif; ?>
		  
          <a href="?action=settings&do=add_user&id=<?php echo $this->_vars['admin_vars']['bo_user']['id']; ?>
" class="white">
          <?php echo $this->_vars['admin_vars']['bo_user']['name']; ?>
</a> | <a href="?action=logout" class="white"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "logout"), $this);?></a> |   
          <i class="fa fa-external-link"></i> <a href="../" class="white" target="_blank"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "to_website"), $this);?></a>
        </td></tr>
      </table></td>
	</tr>
</table><table cellspacing="0" class="breadAndSeach nomargin">
	<tr>
	<td class="breadcrumbs" style="padding-left:10px;">
	  <a href="/<?php echo constant('ADMIN_FOLDER'); ?>
/"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "home"), $this);?></a>
	  <?php if (count((array)$this->_vars['path'])): foreach ((array)$this->_vars['path'] as $this->_vars['key'] => $this->_vars['value']): ?>
     &gt; <?php if ($this->_vars['key'] != ""): ?><a href="<?php echo $this->_vars['key']; ?>
"><?php endif;  echo $this->_vars['value'];  if ($this->_vars['key'] != ""): ?></a><?php endif; ?>	  
	  <?php endforeach; endif; ?>
	</td><!-- /breadcrumbs -->
	<td class="search" style="padding-right:10px;"><!--<a href="">язык</a>-->
		<form method="get" action="" onsubmit="location.href='/<?php echo constant('ADMIN_FOLDER'); ?>
/?action=search&q='+encodeURIComponent(this.q.value); return false;">
		<fieldset>
			<input type="text" value="<?php if (isset ( $this->_vars['query'] )):  echo $this->_run_modifier($this->_run_modifier($this->_vars['query'], 'strip', 'plugin', 1), 'inform', 'PHP', 1);  else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "search"), $this); endif; ?>" id="q" class="searchInput" size="15" name="q" onfocus="javascript:ClearSearchInput(this,true);" onblur="ClearSearchInput(this,false);"/>
			<input type="submit" class="searchButton" value="" />
		</fieldset>
		</form>
	</td><!-- /search -->
	</tr>
</table><!-- /breadAndSeach -->

<table width="100%" cellpadding=3 border=0>
	<tr>
		<td><!-- Main Content starts -->

<table border=0 width="100%" cellpadding=5 cellspacing=0>
  <tr>
    <td valign="top" style="width:200px;">
      <?php echo $this->_vars['sidebar']; ?>
    
	  
<?php endif; ?>