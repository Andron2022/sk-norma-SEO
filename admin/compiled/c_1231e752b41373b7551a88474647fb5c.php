<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "add_title"), $this);?></h1>
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("settings/elements_menu.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<?php if (isset ( $this->_vars['messages'] )):  echo $this->_vars['messages']; ?>

<?php elseif (isset ( $_GET['added'] )):  $this->assign('href', "?action=settings&do=edit_site_var&id=".$_GET['id']); ?>
<blockquote><a href="<?php echo $this->_vars['href']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "added"), $this);?></a></blockquote>
<?php else: ?>

<form method="post">
<table width="80%">
	<tr>
		<td colspan="2"><blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "help"), $this);?><br><br><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "example"), $this);?></blockquote></td>
	</tr>
	<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
		<td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "website"), $this);?>: </td>
		<td><select name="forsite"<?php if ($this->_run_modifier($this->_vars['sites'], 'count', 'PHP', 0) == 1): ?> disabled="disabled"<?php endif; ?>>
        <option value="0"> - <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "for_all"), $this);?></option>
        <?php if (count((array)$this->_vars['sites'])): foreach ((array)$this->_vars['sites'] as $this->_vars['value']): ?>
        <option value="<?php echo $this->_vars['value']['id']; ?>
"<?php if ($this->_run_modifier($this->_vars['sites'], 'count', 'PHP', 0) == 1): ?> selected<?php endif; ?>><?php echo $this->_run_modifier($this->_vars['value']['site_url'], 'delhttp', 'plugin', 1); ?>
</option>
        <?php endforeach; endif; ?>
		</select></td>
	</tr>
	<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
		<td><b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "name"), $this);?>:</b> <br><small>(<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "name_help"), $this);?>)</small></td>
		<td><input name="varname" type="text" size="50" value="<?php if (! empty ( $_GET['hint'] )):  echo $_GET['hint'];  endif; ?>" /></td>
	</tr>
	<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
		<td><b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "value"), $this);?>:</b> </td>
		<td><textarea name="value" rows="7" style="width:100%;"><?php if (! empty ( $this->_vars['hint']['value'] )):  echo $this->_vars['hint']['value'];  endif; ?></textarea></td>
	</tr>
	<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
		<td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "description"), $this);?>: <br><small>(<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "desc_help"), $this);?>)</small></td>
		<td><textarea name="description" rows="4" style="width:100%;"><?php if (! empty ( $this->_vars['hint']['description'] )):  echo $this->_vars['hint']['description'];  endif; ?></textarea></td>
	</tr>
    
	<tr>
		<td colspan="2"><a href="javascript: ShowHide('block-meta')" style="border-bottom: 1px dashed blue;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "extra"), $this);?></a>
		    <div style="display: none;" id="block-meta">
				<table width="100%">
					<tbody>
						<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
							<td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "type"), $this);?>: </td>
							<td><input type="radio" name="type" value="text" <?php if (( ! empty ( $this->_vars['hint']['type'] ) && $this->_vars['hint']['type'] == "text" ) || empty ( $this->_vars['hint']['type'] )): ?>checked="checked"<?php endif; ?> /> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "text"), $this);?>
							<input type="radio" name="type" value="list" <?php if (! empty ( $this->_vars['hint']['type'] ) && $this->_vars['hint']['type'] == "list"): ?>checked="checked"<?php endif; ?> 
							/> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "select"), $this);?>
							<input type="radio" name="type" value="checkbox" <?php if (! empty ( $this->_vars['hint']['type'] ) && $this->_vars['hint']['type'] == "checkbox"): ?>checked="checked"<?php endif; ?>/> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "flag"), $this);?>
							</td>
						</tr>
						<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
							<td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "autoload"), $this);?></td>
							<td colspan="3"><input type="checkbox" name="autoload" value="1" checked></td>
						</tr>
						<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
							<td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "variants"), $this);?>:<br><small>(<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "variants_comment"), $this);?>)</small></td>
							<td colspan="3"><textarea name="if_enum" rows="7" style="width:100%;"><?php if (! empty ( $this->_vars['hint']['if_enum'] )):  echo $this->_vars['hint']['if_enum'];  endif; ?></textarea></td>
						</tr>
						<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
							<td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "size1"), $this);?></td>
							<td><input type="text" name="width" size="10" maxlength="25" value="<?php if (! empty ( $this->_vars['hint']['width'] )):  echo $this->_vars['hint']['width'];  else: ?>0<?php endif; ?>"></td>
							<td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "size2"), $this);?></td>
							<td><input type="text" name="height" size="10" maxlength="25" value="<?php if (! empty ( $this->_vars['hint']['height'] )):  echo $this->_vars['hint']['height'];  else: ?>0<?php endif; ?>"></td>
						</tr>
					</tbody>
				</table>
			</div>
		</td>
	</tr>    
    
	<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
		<td colspan="2"><input type="submit" name="add" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?>" /></td>
	</tr>
</table>
</form>

<?php endif; ?>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>