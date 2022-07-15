<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.red.php'); $this->register_modifier("red", "tpl_modifier_red");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<?php if (! empty ( $_GET['fcopy'] ) && ! empty ( $_GET['doc'] )): ?>
	<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "copy"), $this);?> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "file",'case' => "lower"), $this);?>: <?php echo $_GET['doc']; ?>
</h1>
<?php elseif (! empty ( $_GET['doc'] )): ?><h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "file"), $this);?>: <?php echo $_GET['doc']; ?>
</h1><?php endif; ?>

<ul>
	<li><a href="?action=settings&do=tpl"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "templates"), $this);?></a></li>
	<li><a href="?action=settings&do=tpl&folder=<?php echo $this->_vars['folder']; ?>
"><?php echo $this->_vars['folder']; ?>
</a></li>
	<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "templates"), $this);?>: <a href="?action=settings&do=site_vars&site_id=-1&q=sys_tpl_pages&redirect=1"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "found_categs"), $this);?></a>, 
<a href="?action=settings&do=site_vars&site_id=-1&q=sys_tpl_products&redirect=1"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "found_offers"), $this);?></a>, <a href="?action=settings&do=site_vars&site_id=-1&q=sys_tpl_pubs&redirect=1"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "found_pubs"), $this);?></a>.</li>
</ul>

<?php if (! empty ( $_GET['added'] )): ?>
	<blockquote style="width:60%; float:left;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "added"), $this);?></blockquote>
<?php endif; ?>

<?php if ($this->_vars['messages']): ?>
  <?php echo $this->_run_modifier($this->_vars['messages'], 'red', 'plugin', 1); ?>

<?php else: ?>
  <?php if ($this->_vars['updated']): ?><blockquote style="width:60%; float:left;"><?php echo $this->_vars['updated']; ?>
</blockquote><?php endif; ?>
  <?php if (isset ( $this->_vars['templates'] ) && $this->_run_modifier($this->_vars['templates'], 'count', 'PHP', 0) > 0): ?>
  <?php if (count((array)$this->_vars['templates'])): foreach ((array)$this->_vars['templates'] as $this->_vars['value']): ?>
    <li><a href="<?php echo $this->_vars['value']['link']; ?>
"><?php echo $this->_vars['file']; ?>
</a></li>
  <?php endforeach; endif; ?>
  <?php endif; ?>

  <table width="80%">
  	<form method=post>
  	<tr>
      <th width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "file"), $this);?></th>
      <th><?php echo $this->_vars['fname']; ?>
</th>
    </tr>
	<?php if (! empty ( $_GET['fcopy'] ) && ! empty ( $_GET['doc'] )): ?>
	
		<tr bgcolor="<?php echo $this->_vars['admin_vars']['bglight']; ?>
">
			<input type="hidden" name="folder_filename" value="<?php echo $this->_vars['folder']; ?>
" />
			<td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "filename"), $this);?>
				<br><small><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "icons",'key3' => "for_example"), $this);?>, new_filename.html</small>
			</td>
			<td><input name="new_filename" style="width:100%;" value="copy_<?php echo $_GET['doc']; ?>
"></td>
		</tr>	
	<?php endif; ?>
  	<tr bgcolor="<?php echo $this->_vars['admin_vars']['bglight']; ?>
">
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "content"), $this);?></td>
      <td><textarea name="tpl" rows=20 style="width:100%;"><?php echo $this->_vars['fcontent']; ?>
</textarea></td>
    </tr>
  	<tr bgcolor="<?php echo $this->_vars['admin_vars']['bglight']; ?>
">
		<td colspan=2>
			<?php if (! empty ( $_GET['fcopy'] ) && ! empty ( $_GET['doc'] )): ?>
				<input type=submit name="update_file" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "copy"), $this);?>">
			<?php else: ?>
				<input type=submit name="update_file" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "save"), $this);?>">
			<?php endif; ?>
		</td>
    </tr>
  	</form>
  </table>

<?php endif; ?>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>