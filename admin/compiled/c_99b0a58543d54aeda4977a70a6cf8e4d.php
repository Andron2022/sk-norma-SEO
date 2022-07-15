<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.date.php'); $this->register_modifier("date", "tpl_modifier_date");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "coupons"), $this);?> <?php if (! empty ( $this->_vars['all'] )): ?>(<?php echo $this->_vars['all']; ?>
)<?php endif; ?></h1>
<p><a href="?action=coupons&id=0"><i class="fa fa-plus"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a></p>


<?php if (isset ( $_GET['deleted'] )): ?>
<table width="80%">
	<tr>
		<td><blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "deleted"), $this);?></blockquote></td>
	</tr>
</table>
<?php endif; ?>

<?php if (! empty ( $this->_vars['rows'] )): ?>
	<table width="80%">
		<tr>
			<th>#</th>
			<th>ID</th>

			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "orders",'key3' => "coupon"), $this);?></th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "orders",'key3' => "discount"), $this);?></th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "activity"), $this);?></th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "start"), $this);?></th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "stop"), $this);?></th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "oneway"), $this);?></th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "renewal"), $this);?></th>
			
		</tr>
		
		<?php if (count((array)$this->_vars['rows'])): foreach ((array)$this->_vars['rows'] as $this->_vars['v']): ?>
			<tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>">
				<td><a href="?action=coupons&id=<?php echo $this->_vars['v']['id']; ?>
"><i class="fa fa-pencil"></i></a></td>
				<td><a href="?action=coupons&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['id']; ?>
</a></td>
				<td><a href="?action=coupons&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['title']; ?>
</a></td>
				<td align="right"><?php echo $this->_vars['v']['discount_summ'];  if (! empty ( $this->_vars['v']['discount_procent'] )): ?>%<?php endif; ?></td>
				<td align="center"><?php if (! empty ( $this->_vars['v']['active_date'] )): ?><i class="fa fa-check"></i><?php else: ?>-<?php endif; ?></td>
				<td align="center"><small><?php if (empty ( $this->_vars['v']['date_start'] )): ?>-<?php else:  echo $this->_run_modifier($this->_vars['v']['date_start'], 'date', 'plugin', 1, $this->_vars['site']['site_datetime_format']);  endif; ?></small></td>
				<td align="center"><small><?php if (empty ( $this->_vars['v']['date_stop'] )): ?>-<?php else:  echo $this->_run_modifier($this->_vars['v']['date_stop'], 'date', 'plugin', 1, $this->_vars['site']['site_datetime_format']);  endif; ?></small></td>
				<td align="center"><?php if (! empty ( $this->_vars['v']['onetime'] )): ?><i class="fa fa-check"></i><?php endif; ?></td>
				<td align="center"><?php echo $this->_run_modifier($this->_vars['v']['last_updated'], 'date', 'plugin', 1, $this->_vars['site']['site_date_format']); ?>
</td>
			</tr>			
		<?php endforeach; endif; ?>
	</table>
<?php else: ?>
	<p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "list_empty"), $this);?></p>
	<p><a href="?action=coupons&id=0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a></p>
<?php endif; ?>          

<?php echo $this->_vars['pages']; ?>


<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>          