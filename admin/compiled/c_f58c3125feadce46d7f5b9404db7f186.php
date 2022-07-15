<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "blocks"), $this);?></h1>

<table width="80%">
	<tr>
		<td>

<?php if (! empty ( $this->_vars['filter_sites'] )): ?>

<p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "filter"), $this);?>: 
<?php if (! empty ( $_GET['site'] )): ?><a href="?action=settings&do=blocks"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "all"), $this);?></a> | <?php endif; ?>

	<?php if (count((array)$this->_vars['filter_sites'])): foreach ((array)$this->_vars['filter_sites'] as $this->_vars['k'] => $this->_vars['v']): ?>
	<?php if (! empty ( $this->_vars['v']['id'] )): ?>
		<?php if ($this->_vars['k'] > 0): ?> | <?php endif; ?>
		<?php if (! empty ( $_GET['site'] ) && $_GET['site'] == $this->_vars['v']['id']): ?>
			<b><?php echo $this->_vars['v']['name_short']; ?>
</b> <small>(<?php echo $this->_vars['v']['site_url']; ?>
)</small>
		<?php else: ?>
			<a href="?action=settings&do=blocks&site=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['name_short']; ?>
 <small>(<?php echo $this->_vars['v']['site_url']; ?>
)</small></a>
		<?php endif; ?> 
	<?php endif; ?>
	<?php endforeach; endif; ?>
</p>
<?php endif; ?>

<?php if (isset ( $_GET['added'] )): ?><blockquote><a href="?action=settings&do=blocks&id=<?php echo $_GET['added']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "added"), $this);?></a></blockquote><?php endif;  if (isset ( $_GET['updated'] )): ?><blockquote><a href="?action=settings&do=blocks&id=<?php echo $_GET['updated']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "updated"), $this);?></a></blockquote><?php endif;  if (isset ( $_GET['deleted'] )): ?><blockquote class="red"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "deleted"), $this);?></blockquote><?php endif; ?>

		</td>
	</tr>
</table>

<?php if (! empty ( $this->_vars['list_blocks'] )): ?>

<?php $this->assign('place', "");  $this->assign('old', "0"); ?>



<table width="80%">
	<tr>
		<th width=20><i class="fa fa-eye"></i></th>
		<th width=10>ID</th>
		<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "title_admin"), $this);?></th>
		<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "synonim"), $this);?></th>
		<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "type"), $this);?></th>
		<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "qty"), $this);?></th>
		<th><i class="fa fa-sort"></i></th>
		<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "categs"), $this);?></th>
		<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "exception"), $this);?></th>
		<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "website"), $this);?></th>
	</tr>
          
    <?php if (count((array)$this->_vars['list_blocks'])): foreach ((array)$this->_vars['list_blocks'] as $this->_vars['v']): ?>
		<?php $this->assign('href', "?action=settings&do=blocks&id=".$this->_vars['v']['id']); ?>
		<?php if ($this->_vars['v']['where'] != $this->_vars['place']): ?>
			<tr>
				<td colspan="10"><h3><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "block_wheres",'key3' => $this->_vars['v']['where']), $this);?></h3></td>
			</tr>			
		<?php endif; ?>
		
		<?php if ($this->_vars['v']['id'] == $this->_vars['old']): ?>
		<tr <?php if (isset ( $this->_vars['cnt'] )): ?>class="<?php echo $this->_vars['cnt']; ?>
"<?php endif; ?>>
			<td colspan="9"></td>
			<td><small><?php echo $this->_vars['v']['site']; ?>
</small></td>
		</tr>
		<?php else: ?>
		<?php $this->assign('old', $this->_vars['v']['id']); ?>
		<?php if ($this->_vars['v']['where'] != $this->_vars['place']): ?>
			<?php echo tpl_function_cycle(array('values' => "odd,",'reset' => "1",'assign' => "cnt"), $this);?>
		<?php else: ?>
			<?php echo tpl_function_cycle(array('values' => "odd,",'assign' => "cnt"), $this);?>
		<?php endif; ?>
		<tr class="<?php echo $this->_vars['cnt']; ?>
">
			<td class="center"><a href="<?php echo $this->_vars['href']; ?>
"><?php if ($this->_vars['v']['active'] == 1): ?><i class="fa fa-check"></i><?php else: ?><i class="fa fa-minus"></i><?php endif; ?></a></td>
			<td class="center"><a href="<?php echo $this->_vars['href']; ?>
"><?php echo $this->_vars['v']['id']; ?>
</a></td>
			<td><a href="<?php echo $this->_vars['href']; ?>
"><i class="fa fa-pencil"></i> <?php echo $this->_vars['v']['title_admin']; ?>
</a></td>
			<td nowrap><a href="<?php echo $this->_vars['href']; ?>
"><i class="fa fa-pencil"></i> <?php echo $this->_vars['v']['title']; ?>
</a></td>
			<td><a href="<?php echo $this->_vars['href']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "block_types",'key3' => $this->_vars['v']['type']), $this);?></a></td>
			<td class="center"><a href="<?php echo $this->_vars['href']; ?>
"><?php echo $this->_vars['v']['qty']; ?>
</a></td>
			<td class="center"><a href="<?php echo $this->_vars['href']; ?>
"><?php echo $this->_vars['v']['sort']; ?>
</a></td>
			<td><?php echo $this->_run_modifier($this->_vars['v']['pages'], 'nl2br', 'PHP', 1); ?>
</td>
			<td><?php echo $this->_run_modifier($this->_vars['v']['skip_pages'], 'nl2br', 'PHP', 1); ?>
</td>
			<td><small><?php echo $this->_run_modifier($this->_vars['v']['site_url'], 'delhttp', 'plugin', 1); ?>
</small></td>
		</tr>
		
			<?php if ($this->_vars['v']['where'] != $this->_vars['place']): ?>
				<?php $this->assign('place', $this->_vars['v']['where']); ?>
			<?php endif; ?>
		<?php endif; ?>
	<?php endforeach; endif; ?>
</table>
<?php else: ?>
<p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "list_empty"), $this);?></p>
<?php endif; ?>

  <p><a href="?action=settings&do=blocks&id=0"><i class="fa fa-plus-circle"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a></p>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>