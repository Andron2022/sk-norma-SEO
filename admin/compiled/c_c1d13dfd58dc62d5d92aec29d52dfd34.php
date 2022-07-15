<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "statuses"), $this);?></h1>

<?php if (isset ( $_GET['deleted'] )): ?>
	<table width="80%">
		<tr>
			<td><blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "deleted"), $this);?></blockquote></td>
		</tr>
	</table>
<?php endif; ?>

<?php if ($this->_run_modifier($this->_vars['site_vars']['list_sites'], 'count', 'PHP', 0) > 1): ?>
<form method="get">
	<input type="hidden" name="action" value="settings">
	<input type="hidden" name="do" value="statuses">
    <select name="" onchange="top.location=this.value">
		<option value="?action=settings&do=statuses"> - <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "all_sites"), $this);?></option>
		<?php if (count((array)$this->_vars['site_vars']['list_sites'])): foreach ((array)$this->_vars['site_vars']['list_sites'] as $this->_vars['v']): ?>
			<option value="?action=settings&do=statuses&site_id=<?php echo $this->_vars['v']['id']; ?>
"<?php if (! empty ( $_GET['site_id'] ) && $_GET['site_id'] == $this->_vars['v']['id']): ?> selected="selected"<?php endif; ?>><?php echo $this->_vars['v']['name_short']; ?>
 <?php echo $this->_vars['v']['site_url']; ?>
</option>  
		<?php endforeach; endif; ?>
	</select>
</form>
<?php endif; ?>



<?php if ($this->_run_modifier($this->_vars['list_statuses'], 'count', 'PHP', 0) > 0): ?>
	<table width="80%">
		<tr>
			<th>ID</th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "name"), $this);?></th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "status",'key3' => "title_client"), $this);?></th>
			<th><i class="fa fa-sort" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "status",'key3' => "sort"), $this);?>"></i></th>
			<th><i class="fa fa-eye" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "status",'key3' => "active"), $this);?>"></i></th>
			<th><i class="fa fa-user" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "status",'key3' => "show_client"), $this);?>"></i></th>
			<th><i class="fa fa-edit" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "edit"), $this);?>"></i></th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "website"), $this);?></th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "mass",'key3' => "code"), $this);?></th>
		</tr>
		
		<?php if (count((array)$this->_vars['list_statuses'])): foreach ((array)$this->_vars['list_statuses'] as $this->_vars['v']): ?>
			<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
				<td><a href="<?php echo $this->_vars['v']['href']; ?>
"><?php echo $this->_vars['v']['id']; ?>
</a></td>
				<td><a href="<?php echo $this->_vars['v']['href']; ?>
"><?php echo $this->_vars['v']['title']; ?>
</a></td>
				<td><a href="<?php echo $this->_vars['v']['href']; ?>
"><?php echo $this->_vars['v']['title_client']; ?>
</a></td>
				<td align="center"><a href="<?php echo $this->_vars['v']['href']; ?>
"><?php echo $this->_vars['v']['sort']; ?>
</a></td>
				<td align="center"><a href="<?php echo $this->_vars['v']['href']; ?>
"><?php if ($this->_vars['v']['active'] == 1): ?><i class="fa fa-check"></i><?php else: ?><i class="fa fa-minus"></i><?php endif; ?></a></td>
				<td align="center"><a href="<?php echo $this->_vars['v']['href']; ?>
"><?php if ($this->_vars['v']['show_client'] == 1): ?><i class="fa fa-check"></i><?php else: ?><i class="fa fa-minus"></i><?php endif; ?></a></td>
				<td align="center"><a href="<?php echo $this->_vars['v']['href']; ?>
"><?php if ($this->_vars['v']['edit'] == 1): ?><i class="fa fa-check"></i><?php else: ?><i class="fa fa-minus"></i><?php endif; ?></a></td>
				<td align="center"><a href="<?php echo $this->_vars['v']['href']; ?>
"><?php if ($this->_vars['v']['site'] > 0):  echo $this->_vars['v']['site_url'];  else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "for_all"), $this); endif; ?></a></td>
				<td><?php echo $this->_vars['v']['alias']; ?>
</td>
			</tr>
		<?php endforeach; endif; ?>
	</table>

<?php else: ?>
	<p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "list_empty"), $this);?></p>
<?php endif; ?>          

<p><a href="?action=settings&do=statuses&id=0"><i class="fa fa-plus"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a></p>


<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>          