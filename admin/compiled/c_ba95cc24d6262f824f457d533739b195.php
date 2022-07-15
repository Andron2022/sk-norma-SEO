<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.math.php'); $this->register_function("math", "tpl_function_math");   if (! empty ( $_GET['do'] ) && $_GET['do'] == "licence"):  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "license_key"), $this);?></h1>
<?php endif; ?>

<p><i class="fa fa-key"></i> <a href="?action=settings&do=licence"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "license_key"), $this);?></a> <a href="?action=settings&do=site_vars&site_id=-1&q=sys_licence&redirect=1"><i class="fa fa-bars"></i></a>
		<?php if (! empty ( $this->_vars['site_vars']['sys_licence_time'] )): ?>
			<?php echo tpl_function_math(array('equation' => "x - y",'y' => time(),'x' => $this->_vars['site_vars']['sys_licence_time'],'assign' => "diff"), $this);?>
			<?php if ($this->_vars['site_vars']['sys_licence_time'] < time()): ?>		
				<br><small><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "support_closed"), $this);?> <?php echo $this->_run_modifier($this->_vars['site_vars']['sys_licence_time'], 'date', 'plugin', 1, "d.m.Y H:i"); ?>
</small>
				<br> <i class="fa fa-info-circle" style="color:red;"></i> <a href="http://www.simpla.es/update-licence/?l=<?php echo $this->_vars['site_vars']['sys_licence']; ?>
&r=1" target="_blank"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "buy_prolong"), $this);?></a>
			<?php elseif ($this->_vars['diff'] > 0 && $this->_vars['diff'] < 2592000): ?>
				<br><small><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "support_ends"), $this);?>  <?php echo $this->_run_modifier($this->_vars['site_vars']['sys_licence_time'], 'date', 'plugin', 1, "d.m.Y H:i"); ?>
</small>
				<br> <i class="fa fa-info-circle" style="color:red;"></i> <a href="http://www.simpla.es/update-licence/?l=<?php echo $this->_vars['site_vars']['sys_licence']; ?>
&r=1" target="_blank"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "buy_prolong_light"), $this);?></a>
			<?php else: ?>
				<br><small><i class="fa fa-check"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "support_ends"), $this);?> </small> <?php echo $this->_run_modifier($this->_vars['site_vars']['sys_licence_time'], 'date', 'plugin', 1, "d.m.Y"); ?>

			
			
			<?php endif; ?>
		<?php endif; ?></p>

		
<?php if (! empty ( $_GET['do'] ) && $_GET['do'] == "licence"): ?>		
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?> 
<?php endif; ?>