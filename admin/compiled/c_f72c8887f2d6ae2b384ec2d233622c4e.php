<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.truncate.php'); $this->register_modifier("truncate", "tpl_modifier_truncate");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.escape.php'); $this->register_modifier("escape", "tpl_modifier_escape");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
  $this->assign('do', $this->_vars['admin_vars']['uri']['do']);  $this->assign('mode', $this->_vars['admin_vars']['uri']['mode']); ?>

<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "title"), $this);?></h1>
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("settings/elements_menu.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<table width="80%">
	<tr>
		<td>
			
<blockquote><?php if ($this->_vars['mode'] == "img"):  echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "var_help1"), $this); else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "var_help2"), $this); endif; ?></blockquote>
  
<div class="center"> 
<form method="get">
<input type="hidden" name="action" value="settings" />
<input type="hidden" name="do" value="site_vars" />
<?php if ($this->_vars['mode'] == "sys"): ?>
<input type="hidden" name="mode" value="sys" />
<?php endif;  echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "filter"), $this);?>: 
<select name="site_id">
<option value="-1"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "all_vars"), $this);?></option>
<option value="0"<?php if (isset ( $_GET['site_id'] ) && $_GET['site_id'] == "0"): ?> selected<?php endif; ?>><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "common_vars"), $this);?></option>
<?php if (count((array)$this->_vars['sites'])): foreach ((array)$this->_vars['sites'] as $this->_vars['value']): ?>
<option value="<?php echo $this->_vars['value']['id']; ?>
"<?php if (isset ( $_GET['site_id'] ) && $_GET['site_id'] == $this->_vars['value']['id']): ?> selected<?php endif; ?>><?php echo $this->_run_modifier($this->_vars['value']['site_url'], 'delhttp', 'plugin', 1); ?>
</option>
<?php endforeach; endif; ?>
</select>
 <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "text",'case' => "lower"), $this);?> <input size="20" type="text" name="q" value="<?php if (isset ( $_GET['q'] )):  echo $this->_run_modifier($_GET['q'], 'escape', 'plugin', 1);  endif; ?>" />
 <input type="submit" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "orders",'key3' => "find"), $this);?> &raquo;" class="small" />
</form>
</div>

<?php if (isset ( $_GET['deleted'] )): ?>
  <blockquote class="red"><a href="?action=settings&do=site_vars"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "deleted"), $this);?> <?php echo $_GET['deleted']; ?>
</a></blockquote>
<?php endif; ?>


		</td>
	</tr>
</table>

<?php if ($this->_run_modifier($this->_vars['list_vars'], 'count', 'PHP', 0) > 0): ?>
<form method="post">
		<table width="80%">
			<tr>
				<th width="5%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "auto"), $this);?></th>
				<th width="20%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "name"), $this);?></th>
				<?php if ($this->_vars['mode'] == "img" || ( isset ( $_GET['q'] ) && $this->_run_modifier($_GET['q'], 'truncate', 'plugin', 1, 4, "", false) == "img_" )): ?>
					<th width="10%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "width"), $this);?></th>
					<th width="10%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "height"), $this);?></th>
				<?php else: ?>
					<th width="20%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "value"), $this);?></th>
				<?php endif; ?>
				<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "website"), $this);?></th>
				<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "desc"), $this);?></th>
				<?php if ($this->_vars['mode'] != "sys" && $this->_vars['mode'] != "img"): ?><th width="5%"><i class="fa fa-trash" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "delete"), $this);?>"></i> <input type="checkbox" onclick="CheckAll(this,'del[]')"/></th><?php endif; ?>
			</tr>
	
  	<?php if (count((array)$this->_vars['list_vars'])): foreach ((array)$this->_vars['list_vars'] as $this->_vars['value']): ?>
      <tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
        <td class="center"><?php if ($this->_vars['value']['autoload'] == "1"): ?><i class="fa fa-check"></i><?php else: ?><i class="fa fa-minus"></i><?php endif; ?></td>
  		<td nowrap><a href="?action=settings&do=site_vars&id=<?php echo $this->_vars['value']['id']; ?>
"><i class="fa fa-pencil"></i> <?php echo $this->_vars['value']['name']; ?>
</a></td>

        <?php if ($this->_vars['mode'] == "img" || ( isset ( $_GET['q'] ) && $this->_run_modifier($_GET['q'], 'truncate', 'plugin', 1, 4, "", false) == "img_" )): ?>
    			<td align="center"><?php echo $this->_vars['value']['width']; ?>
</td>
    			<td align="center"><?php echo $this->_vars['value']['height']; ?>
</td>
        <?php else: ?>
    			<td>
				<?php if ($this->_vars['value']['type'] == "checkbox"): ?>
					<?php if (empty ( $this->_vars['value']['value'] )): ?>
						<i class="fa fa-minus"></i>
					<?php else: ?>
						<i class="fa fa-check"></i>
					<?php endif; ?>
				<?php else: ?>
					<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['value']['value'], 'strip_tags', 'PHP', 1), 'truncate', 'plugin', 1, 50, "...", false); ?>

				<?php endif; ?>
				</td>
        <?php endif; ?>

  			<td>			
			<?php if ($this->_vars['value']['site_id'] == 0): ?>- <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "for_all"), $this);?>
			<?php else: ?>
				<?php if (empty ( $this->_vars['value']['website'] )): ?>
					<span style="color:red; font-weight:bold;">?</span>
				<?php else: ?>
					<?php echo $this->_run_modifier($this->_vars['value']['website'], 'delhttp', 'plugin', 1); ?>

				<?php endif; ?>
			<?php endif; ?></td>
  			<td><?php echo $this->_run_modifier($this->_run_modifier($this->_vars['value']['description'], 'strip_tags', 'PHP', 1), 'truncate', 'plugin', 1, 250, "...", false); ?>
</td>
  			<?php if ($this->_vars['mode'] != "sys" && $this->_vars['mode'] != "img"): ?><td class="center"><input name="del[]" type="checkbox" value="<?php echo $this->_vars['value']['id']; ?>
" /></td><?php endif; ?>
			</tr>
		<?php endforeach; endif; ?>
     </table>
    <?php if ($this->_vars['mode'] != "sys" && $this->_vars['mode'] != "img"): ?>
		<table width="80%">
  		<tr><td class="right"><input type="submit" name="delete" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "del_selected"), $this);?>" onclick="if(confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "really"), $this);?>')) return true; else return false;" class="small" /></td></tr></table>
    <?php endif; ?>

		</form>
            
<?php elseif (isset ( $_GET['q'] )): ?>
  <p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "you_searched"), $this);?> <b><?php echo $_GET['q']; ?>
</b>. <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "list_empty"), $this);?> <a href="?action=settings&do=site_vars&id=0&hint=<?php echo $_GET['q']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "add_var_now"), $this);?></a>.</p> 
<?php else: ?>
  <p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "list_empty"), $this);?></p>
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