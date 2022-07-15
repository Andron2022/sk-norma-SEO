<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.priceformat.php'); $this->register_modifier("priceformat", "tpl_modifier_priceformat");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.delhttp.php'); $this->register_modifier("delhttp", "tpl_modifier_delhttp");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
  if (! empty ( $this->_vars['admin_vars']['default_currency'] )): ?>
	<?php if ($this->_vars['admin_vars']['default_currency'] == "euro"): ?>
		<?php $this->assign('t_currency', "<i class='fa fa-euro'></i>"); ?>
	<?php elseif ($this->_vars['admin_vars']['default_currency'] == "usd"): ?>
		<?php $this->assign('t_currency', "<i class='fa fa-usd'></i>"); ?>
	<?php else: ?>
		<?php $this->assign('t_currency', "<i class='fa fa-rub'></i>"); ?>
	<?php endif;  else: ?>
	<?php $this->assign('t_currency', "");  endif; ?>
		
<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "orders"), $this);?></h1>

<?php if (! empty ( $this->_vars['orders_list'] ) || ( ! empty ( $_GET['site_id'] ) && $this->_run_modifier($this->_vars['all_sites'], 'count', 'PHP', 0) > 1 ) || ! empty ( $_GET['number'] )): ?>

    <table width="70%">
    <form method="get">
		<tr>
			<td valign="top">
				<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "filter"), $this);?>: 
				<input type="hidden" name="action" value="orders">
				<?php if (! empty ( $_GET['site_id'] )): ?>
					<input type="hidden" name="site_id" value="<?php echo $_GET['site_id']; ?>
">
				<?php endif; ?>
				<select name="" onchange="top.location=this.value">
					<option value="?action=orders"> - <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "all_sites"), $this);?></option>
					<?php if (count((array)$this->_vars['all_sites'])): foreach ((array)$this->_vars['all_sites'] as $this->_vars['s']): ?>
						<option value="?action=orders&site_id=<?php echo $this->_vars['s']['id']; ?>
"<?php if (! empty ( $_GET['site_id'] ) && $_GET['site_id'] == $this->_vars['s']['id']): ?> selected="selected"<?php endif; ?>><?php echo $this->_vars['s']['id']; ?>
: <?php echo $this->_run_modifier($this->_vars['s']['url'], 'delhttp', 'plugin', 1); ?>
</option>  
					<?php endforeach; endif; ?>
				</select>
			</td>
			<td align="right">
				<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "orders",'key3' => "field_to_search"), $this);?> 
			</td>
			
			<td valign="top">
			<input type="text" name="number" size="25" 
				style="width:100%;"
				value="<?php if (! empty ( $_GET['number'] )):  echo $_GET['number'];  endif; ?>" />
			</td>
			<td valign="top">
				<input class="small" type="submit" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "find"), $this);?>" />
			</td>
    </form>
	</table>
	
<?php endif; ?>


<?php if (isset ( $_GET['deleted'] )): ?>
  <table width="70%"><tr><td><blockquote><a href="?action=orders"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "deleted"), $this);?></a></blockquote></td></tr></table>
<?php endif;  if (isset ( $_GET['updated'] )): ?>
  <table width="70%"><tr><td><blockquote><p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "updated"), $this);?></p></blockquote></td></tr></table>
<?php endif; ?>


<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/pages.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<?php if ($this->_run_modifier($this->_vars['orders_list'], 'count', 'PHP', 0) > 0): ?>

<table border=0 cellpadding=4>
  <tr>
  	<th>#</th>
  	<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "date"), $this);?></th>
  	<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "orders",'key3' => "order_summ"), $this);?></th>
  	<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "delivery"), $this);?></th>
	<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "orders",'key3' => "discount"), $this);?></th>
  	<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "orders",'key3' => "summ"), $this);?></th>
  	<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "orders",'key3' => "delivery_method"), $this);?></th>
	<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "orders",'key3' => "buyer"), $this);?></th>
	<th><i class="fa fa-comments"></i></th>
  	
	<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "orders",'key3' => "coupon"), $this);?></th>
  	<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "status"), $this);?></th>
	</tr>

	<?php if (count((array)$this->_vars['orders_list'])): foreach ((array)$this->_vars['orders_list'] as $this->_vars['value']): ?>
		
	<tr <?php echo tpl_function_cycle(array('values' => " ,class='odd'"), $this);?>>
		<td nowrap><a href="?action=orders&id=<?php echo $this->_vars['value']['id']; ?>
"><?php if (! empty ( $this->_vars['value']['fav'] )): ?><small><i class="fa fa-star"></i></small> <?php endif;  echo $this->_vars['value']['site_id']; ?>
-<?php echo $this->_run_modifier($this->_vars['value']['order_id'], 'chunk', 'plugin', 1, 4, "-"); ?>
</a>
		<?php if ($this->_vars['value']['original_summa'] != $this->_vars['value']['summa']): ?><i class="fa fa-exclamation-triangle" style="color:red;" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "orders",'key3' => "start_price_changed"), $this);?>"></i><?php endif;  if ($this->_vars['value']['payd_status'] == "1"): ?> <i class="fa fa-truck" style="color:green;" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "orders",'key3' => "order_payd"), $this);?>"></i><?php endif; ?></td>		
		<td><small><a href="?action=orders&id=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_run_modifier($this->_vars['value']['created'], 'date', 'plugin', 1, $this->_vars['site']['site_date_format']); ?>
 <?php echo $this->_run_modifier($this->_vars['value']['created'], 'date', 'plugin', 1, $this->_vars['site']['site_time_format']); ?>
</a></small></td>
		<td align="right"><a href="?action=orders&id=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_run_modifier($this->_vars['value']['summa'], 'priceformat', 'plugin', 1, $this->_vars['value']['order_currency']); ?>
 
		</a></td>
		<td align="right"><?php if (! empty ( $this->_vars['value']['delivery_price'] )): ?><a href="?action=orders&id=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_run_modifier($this->_vars['value']['delivery_price'], 'priceformat', 'plugin', 1, $this->_vars['value']['order_currency']); ?>
</a><?php endif; ?></td> 
		<td align="right"><?php if (! empty ( $this->_vars['value']['discount_summ'] )): ?><a href="?action=orders&id=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_run_modifier($this->_vars['value']['discount_summ'], 'priceformat', 'plugin', 1).$this->_vars['value']['order_currency']; ?>
</a><?php endif; ?></td> 		
		<td align="right" nowrap><b><?php echo $this->_run_modifier($this->_vars['value']['total_summ'], 'priceformat', 'plugin', 1, $this->_vars['value']['order_currency']); ?>
</b></td>		
		<td align="right"><a href="?action=orders&id=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_vars['value']['delivery_title']; ?>
</a></td>
		<td><a href="?action=orders&id=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_vars['value']['fio']; ?>
</a></td>
		<td align="center"><?php if (! empty ( $this->_vars['value']['comments'] )): ?><a href="?action=orders&id=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_vars['value']['comments']; ?>
</a><?php endif; ?></td>
		
		<td><a href="?action=orders&id=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_vars['value']['coupon']; ?>
</a></td>
		<td><?php if ($this->_vars['value']['status'] == 0): ?><span style="color:red;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "orders",'key3' => "new"), $this);?></span>		
		<?php else: ?>
			<?php if (! empty ( $this->_vars['value']['status_title'] )):  echo $this->_vars['value']['status_title'];  else:  echo $this->_vars['value']['status'];  endif; ?>
		<?php endif; ?></td>
	</tr>
	<?php endforeach; endif; ?>

	</table>
	
<?php else: ?>
  <blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "list_empty"), $this);?></blockquote>
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