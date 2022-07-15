<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0 mb-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "list_users"), $this);?></h1>
	<ul>
		<li><?php if (empty ( $_GET['admin'] )): ?><a href="?action=settings&do=users&admin=1"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "admins"), $this);?></a><?php else: ?><b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "admins"), $this);?></b><?php endif; ?></li>
		<li><?php if (empty ( $_GET['news'] )): ?><a href="?action=settings&do=users&news=1"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "subscribers"), $this);?></a><?php else: ?><b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "subscribers"), $this);?></b><?php endif; ?></li>
		<li><?php if (empty ( $_GET['all'] )): ?><a href="?action=settings&do=users&all=1"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "others"), $this);?></a><?php else: ?><b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "others"), $this);?></b><?php endif; ?></li>
	</ul>

<?php if ($this->_run_modifier($this->_vars['list_users'], 'count', 'PHP', 0) > 0): ?>

<?php if (isset ( $_GET['deleted'] )): ?>
	<table width="80%">
		<tr>
			<td><blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "deleted"), $this);?></blockquote></td>
		</tr>
	</table>
<?php endif; ?>

<table width="80%">
	<tr>
		<th>ID</th>
		<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "avatar"), $this);?></th>
		<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "fio"), $this);?></th>
		<th><i class="fa fa-user" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "login"), $this);?>"></i></th>
		<th><i class="fa fa-at" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "email"), $this);?>"></i></th>
		<th><i class="fa fa-check" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "status",'key3' => "active"), $this);?>"></i></th>
		<th><i class="fa fa-folder-open" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "info"), $this);?>"></i></th>
		<th><i class="fa fa-shopping-cart" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "products"), $this);?>"></i></th>
		<th><i class="fa fa-phone-square" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "orders"), $this);?>"></i></th>
		<th><i class="fa fa-envelope" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "feedback"), $this);?>"></i></th>
		<th><i class="fa fa-tasks" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "admin"), $this);?>"></i></th>
		<th><i class="fa fa-newspaper-o" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "news"), $this);?>"></i></th>
	</tr>
 
	<?php if (count((array)$this->_vars['list_users'])): foreach ((array)$this->_vars['list_users'] as $this->_vars['value']): ?>
		<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
		<?php $this->assign('r_href', "?action=settings&do=add_user&id=".$this->_vars['value']['id']); ?>
		<td><a href="<?php echo $this->_vars['r_href']; ?>
"><?php echo $this->_vars['value']['id']; ?>
</a></td>
		<td align="center"><a href="<?php echo $this->_vars['r_href']; ?>
"><?php if (! empty ( $this->_vars['value']['avatar'] )): ?><img src="<?php echo $this->_vars['value']['avatar']; ?>
" width="50"><?php else: ?><i class="fa fa-user"></i><?php endif; ?></a></td>
		<td><a href="<?php echo $this->_vars['r_href']; ?>
"><i class="fa fa-pencil"></i> <?php echo $this->_run_modifier($this->_vars['value']['name'], 'stripslashes', 'PHP', 1); ?>
</a></td>
		<td><a href="<?php echo $this->_vars['r_href']; ?>
"><?php echo $this->_run_modifier($this->_vars['value']['login'], 'stripslashes', 'PHP', 1); ?>
</a></td>
		<td><a href="<?php echo $this->_vars['r_href']; ?>
"><?php echo $this->_run_modifier($this->_vars['value']['email'], 'stripslashes', 'PHP', 1); ?>
</a></td>
		<td align="center"><a href="<?php echo $this->_vars['r_href']; ?>
"><?php if (empty ( $this->_vars['value']['active'] )): ?><i class="fa fa-minus" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i><?php else: ?><i class="fa fa-check" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i><?php endif; ?></a></td>
		<td align=center><?php if (! empty ( $this->_vars['value']['info'] )): ?><i class="fa fa-check" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i><?php endif; ?></td>
		<td align=center><?php if (! empty ( $this->_vars['value']['products'] )): ?><i class="fa fa-check" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i><?php endif; ?></td>
		<td align=center><?php if (! empty ( $this->_vars['value']['orders'] )): ?><i class="fa fa-check" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i><?php endif; ?></td>
		<td align=center><?php if (! empty ( $this->_vars['value']['feedback'] )): ?><i class="fa fa-check" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i><?php endif; ?></td>
		<td align=center><?php if (! empty ( $this->_vars['value']['settings'] )): ?><i class="fa fa-check" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i><?php endif; ?></td>
		<td align=center><?php if (! empty ( $this->_vars['value']['news'] )): ?><i class="fa fa-check" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i><?php endif; ?></td>
	</tr>
  <?php endforeach; endif; ?>        
</table>

<?php else: ?>
	<p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "list_empty"), $this);?></p>
<?php endif; ?>          


<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/pages.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>          