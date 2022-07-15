<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "emails"), $this);?></h1>

<?php if (isset ( $_GET['deleted'] )): ?>
<blockquote><p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "deleted"), $this);?></p></blockquote>
<?php endif; ?>



<table width="100%">
	<tr>
		<td width="60%" valign="top">
				
			<?php if (! empty ( $this->_vars['site_vars']['list_sites'] )): ?>
			<form method="get">
				<input type="hidden" name="action" value="settings">
				<input type="hidden" name="do" value="payments">
				<select name="" onchange="top.location=this.value">
					<option value="?action=emails"> - <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "all_sites"), $this);?></option>
					<?php if (count((array)$this->_vars['site_vars']['list_sites'])): foreach ((array)$this->_vars['site_vars']['list_sites'] as $this->_vars['v']): ?>
						<option value="?action=emails&site_id=<?php echo $this->_vars['v']['id']; ?>
"<?php if (! empty ( $_GET['site_id'] ) && $_GET['site_id'] == $this->_vars['v']['id']): ?> selected="selected"<?php endif; ?>><?php echo $this->_vars['v']['id']; ?>
: <?php echo $this->_run_modifier($this->_vars['v']['site_url'], 'delhttp', 'plugin', 1); ?>
</option>  
					<?php endforeach; endif; ?>
				</select>
			</form>
			<?php endif; ?>
			
			<?php if (! empty ( $this->_vars['emails'] )): ?>
				<table border=0 cellpadding=3 cellspacing=1 width="90%">
					<tr>
						<th><i class="fa fa-eye" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "status",'key3' => "active"), $this);?>"></i></th>
						<th>ID</th>
						<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "mark"), $this);?></th>
						<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "name"), $this);?></th>
						<th><i class="fa fa-user" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "emails",'key3' => "to_user"), $this);?>"></i></th>
						<th><i class="fa fa-envelope" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "emails",'key3' => "to_admin"), $this);?>"></i></th>
						<th><i class="fa fa-at" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "emails",'key3' => "extra_emails"), $this);?>"></i></th>
						<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "emails",'key3' => "subj"), $this);?></th>
						<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "text"), $this);?></th>
					</tr>
					
					<?php if (count((array)$this->_vars['emails'])): foreach ((array)$this->_vars['emails'] as $this->_vars['v']): ?>
					<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
						<td><?php if (! empty ( $this->_vars['v']['active'] )): ?><i class="fa fa-check"></i><?php else: ?><i class="fa fa-minus"></i><?php endif; ?></td>
						<td><a href="?action=emails&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['id']; ?>
</a></td>
						<td><a href="?action=emails&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['type_event']; ?>
</a></td>
						<td><a href="?action=emails&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['type_title']; ?>
</a></td>
						<td><?php if (! empty ( $this->_vars['v']['to_user'] )): ?><i class="fa fa-check"></i><?php endif; ?></td>
						<td><?php if (! empty ( $this->_vars['v']['to_admin'] )): ?><i class="fa fa-check"></i><?php endif; ?></td>
						<td><?php if (! empty ( $this->_vars['v']['to_extra'] )): ?><i class="fa fa-check"></i><?php endif; ?></td>
						<td><a href="?action=emails&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['subject']; ?>
</a></td>
						<td><?php if (! empty ( $this->_vars['v']['body'] )): ?><i class="fa fa-check"></i><?php else: ?><i class="fa fa-minus" style="color:red;"></i><?php endif; ?></td>
					</tr>
					
					<?php endforeach; endif; ?>
				</table>
			<?php endif; ?>
		
			<p><a href="?action=emails&id=0"><i class="fa fa-plus"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a></p>
		</td>
		<td valign="top">
			<h3><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "emails",'key3' => "event_types"), $this);?></h3>
			<?php if (! empty ( $this->_vars['email_types'] )): ?>
				<table border=0 cellpadding=3 cellspacing=1>
					<tr>
						<th><i class="fa fa-eye" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "status",'key3' => "active"), $this);?>"></i></th>
						<th>ID</th>
						<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "name"), $this);?></th>
						<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "mark"), $this);?></th>
						<th><i class="fa fa-filter" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "filter"), $this);?>"></i></th>
					</tr>
					
					<?php if (count((array)$this->_vars['email_types'])): foreach ((array)$this->_vars['email_types'] as $this->_vars['v']): ?>
						<tr bgcolor="<?php echo tpl_function_cycle(array('values' => "#ffffff,".$this->_vars['admin_vars']['bglight']), $this);?>">
							<td><a href="?action=emails&do=type&id=<?php echo $this->_vars['v']['id']; ?>
"><?php if ($this->_vars['v']['active'] == 1): ?><i class="fa fa-check"></i><?php else: ?><i class="fa fa-minus"></i><?php endif; ?></a></td>

							<td><a href="?action=emails&do=type&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['id']; ?>
</a></td>
							<td><?php if (isset ( $_GET['type'] ) && $_GET['type'] == $this->_vars['v']['id']): ?><b><?php endif; ?><a href="?action=emails&do=type&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['title']; ?>
</a><?php if (isset ( $_GET['type'] ) && $_GET['type'] == $this->_vars['v']['id']): ?></b><?php endif; ?></td>
							<td><a href="?action=emails&do=type&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['event']; ?>
</a></td>
							<td><?php if (isset ( $_GET['type'] ) && $_GET['type'] == $this->_vars['v']['id']): ?><b><?php endif; ?><a href="<?php echo $this->_vars['href_no_type']; ?>
&type=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['qty']; ?>
</a><?php if (isset ( $_GET['type'] ) && $_GET['type'] == $this->_vars['v']['id']): ?></b><?php endif; ?></td>
						</tr>
					<?php endforeach; endif; ?>
				</table>

			<?php else: ?>
				<p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "list_empty"), $this);?></p>
				
				<a href="?action=db&do=add_emails"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add_emails"), $this);?></a>
			<?php endif; ?>          


			<p><a href="?action=emails&do=type&id=0"><i class="fa fa-plus"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a></p>
		
		</td>
</table>







<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>          