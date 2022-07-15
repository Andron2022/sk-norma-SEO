<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "orgs"), $this);?> (<?php echo $this->_vars['all']; ?>
)</h1>

<ul>
	<li><a href="?action=org&id=0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a></li>
</ul>
<?php if (isset ( $_GET['deleted'] )): ?>
	<table width="80%"><tr><td><blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "deleted"), $this);?></blockquote></td></tr></table>
<?php endif; ?>


<?php if (! empty ( $this->_vars['list'] )): ?>
	<table width="80%">
		<tr>
			<th>#</th>
			<th>ID</th>
			<th><i class="fa fa-eye" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "active"), $this);?>"></i></th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "name"), $this);?></th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "orders",'key3' => "org_inn"), $this);?></th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "own_f"), $this);?></th>			
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "phone"), $this);?></th>

			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "mass",'key3' => "code"), $this);?></th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "city"), $this);?></th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "country"), $this);?></th>
			
		</tr>
		
		<?php if (count((array)$this->_vars['list'])): foreach ((array)$this->_vars['list'] as $this->_vars['v']): ?>
			<tr bgcolor="<?php echo tpl_function_cycle(array('values' => "#ffffff,".$this->_vars['admin_vars']['bglight']), $this);?>">
				<td><a href="?action=org&id=<?php echo $this->_vars['v']['id']; ?>
"><i class="fa fa-pencil"></i></a></td>
				<td><a href="?action=org&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['id']; ?>
</a></td>
				<td align="center"><a href="?action=org&id=<?php echo $this->_vars['v']['id']; ?>
"><?php if (! empty ( $this->_vars['v']['active'] )): ?><i class="fa fa-check"></i><?php endif; ?></a></td>
				<td><a href="?action=org&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['title']; ?>
</a></td>
				<td><?php if (! empty ( $this->_vars['v']['inn'] )): ?><a href="?action=org&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['inn']; ?>
</a><?php endif; ?></td>
				<td align="center"><a href="?action=org&id=<?php echo $this->_vars['v']['id']; ?>
"><?php if (! empty ( $this->_vars['v']['own'] )): ?><i class="fa fa-check"></i><?php endif; ?></a></td>
				<td><?php echo $this->_vars['v']['phone']; ?>
</td>
				<td><?php if (! empty ( $this->_vars['v']['postal_code'] )):  echo $this->_vars['v']['postal_code'];  endif; ?></td>
				<td><?php echo $this->_vars['v']['city']; ?>
</td>
				<td><?php echo $this->_vars['v']['address']; ?>
</td>
			</tr>
		<?php endforeach; endif; ?>
	</table>

<?php else: ?>
	<p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "list_empty"), $this);?></p>
	<p><a href="?action=org&id=0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a></p>
<?php endif; ?>          

<?php echo $this->_vars['pages']; ?>


<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>          