<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.lower.php'); $this->register_modifier("lower", "tpl_modifier_lower");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.html_select_date.php'); $this->register_function("html_select_date", "tpl_function_html_select_date");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0 mb-0"><?php if ($this->_vars['user']['id'] > 0):  echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "editing"), $this); else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "adding"), $this); endif; ?></h1>

<table width="80%"><tr><td>
	<blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "help"), $this);?></blockquote>

	<?php if (isset ( $_GET['updated'] )): ?>
		<blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "updated"), $this);?></blockquote>
	<?php elseif (isset ( $_GET['added'] )): ?>
		<blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "added"), $this);?></blockquote>
	<?php endif; ?>
</td></tr></table>

<table width="80%">
	<tr>
		<th width=200><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "field"), $this);?></th>
		<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "value"), $this);?></th>
	</tr>
	
	<form method="post" enctype="multipart/form-data">
	<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
		<td width=200>ID</td>
		<td><?php if (! empty ( $this->_vars['user']['id'] )):  echo $this->_vars['user']['id'];  else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "not_set"), $this); endif; ?></td>
	</tr>

	<?php if (! empty ( $this->_vars['user']['id'] )): ?>
	<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
		<td width=200><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "available_site"), $this);?></td>
		<td><?php if (! empty ( $this->_vars['user']['site_id'] )): ?>
			<a href="?action=settings&do=site&mode=edit&id=<?php echo $this->_vars['user']['site_id']; ?>
"><?php echo $this->_vars['user']['site_id']; ?>
: <?php echo $this->_run_modifier($this->_vars['user']['site_url'], 'delhttp', 'plugin', 1); ?>
</a>
		<?php else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "all"), $this); endif; ?></td>
	</tr>
	<?php endif; ?>

	<?php if (! empty ( $this->_vars['user']['id'] )): ?>
	<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
		<td width=200><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "last_login"), $this);?></td>
		<td>
        <?php if ($this->_vars['user']['last_login'] == '0000-00-00 00:00:00' || empty ( $this->_vars['user']['last_login'] )): ?>
			<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "no_logged"), $this);?>
		<?php else: ?>
			<small><?php echo $this->_vars['user']['last_login']; ?>
 (<?php echo $this->_vars['user']['last_ip']; ?>
)</small>
		<?php endif; ?> 
		 <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "reg_date"), $this);?>: <small><?php echo $this->_vars['user']['date_insert']; ?>
</small></td>
	</tr>
	<?php endif; ?>
	
	<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
		<td width=200><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "fio"), $this);?></td>
		<td><input type=text name="name" style="width:100%;" maxlength=100 value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['user']['name'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
	</tr>
          
	<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
		<td width=200><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "login"), $this);?></td>
		<td><input type=text name="login" style="width:100%;" maxlength=100 value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['user']['login'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
	</tr>
	
	<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
		<td width=200><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "password"), $this);?></td>
		<td>
			<?php if ($this->_vars['user']['id']): ?><small><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "enter_new_password"), $this);?></small><br><?php endif; ?>
          <input type=text name="passwd" style="width:100%;" maxlength=100 value="" /></td>
	</tr>

	<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
		<td width=200><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "email"), $this);?></td>
		<td><input type=text name="email" style="width:100%;" maxlength=100 value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['user']['email'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
	</tr>
	
	<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
		<td width=200><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "note"), $this);?><br><small><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "admin_only"), $this);?></small></td>
		<td><textarea name="memo" style="width:100%;" rows="4"><?php echo $this->_vars['user']['memo']; ?>
</textarea></td>
	</tr>
	
	
	<tr>
        <td colspan="2">
        <a href="javascript: ShowHide('block-1')" style="border-bottom: 1px dashed blue;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "extra"), $this);?></a>
			<div style="display: none;" id="block-1">
				<table width="100%">
					<tbody>
					
						<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
							<td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "phone"), $this);?></td>
							<td><input type=text name=phone_mobil style="width:100%;" maxlength=255 value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['user']['phone_mobil'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
						</tr>

						<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
							<td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "messenger"), $this);?></td>
							<td><input type=text name=icq style="width:100%;" maxlength=255 value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['user']['icq'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
						</tr>										
					
						<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
							<td width="200">
							<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "country"), $this);?>
							</td>
							<td>
							<input type=text name=country style="width:100%;" maxlength=255 value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['user']['country'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
" />
							</td>
						</tr>
						
						<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
							<td width="200">
							<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "city"), $this);?>
							</td>
							<td>
							<input type=text name=city style="width:100%;" maxlength=255 value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['user']['city'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
" />
							</td>
						</tr>

						<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
							<td width="200">
							<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "birth_day"), $this);?>
							</td>
							<td>
							<?php echo tpl_function_html_select_date(array('time' => $this->_vars['user']['birth_day'],'start_year' => "-70",'end_year' => "-3",'day_value_format' => "%02d",'month_format' => "%m",'field_order' => "DMY",'field_array' => "birth_day",'prefix' => "",'lang' => $this->_vars['site_vars']['lang_admin'],'year_empty' => "-",'month_empty' => "-",'day_empty' => "-"), $this);?>
							</td>
						</tr>

						<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
							<td width="200">
							<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "user_interes"), $this);?>
							</td>
							<td>
							<textarea name="user_interes" style="width:100%;" rows="2"><?php echo $this->_vars['user']['user_interes']; ?>
</textarea>
							</td>
						</tr>

						<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
							<td width="200">
							<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "user_about"), $this);?>
							</td>
							<td>
							<textarea name="user_about" style="width:100%;" rows="2"><?php echo $this->_vars['user']['user_about']; ?>
</textarea>
							</td>
						</tr>

						<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
							<td width="200">
							<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "url"), $this);?>
							</td>
							<td>
							<input type=text name=url style="width:100%;" maxlength=255 value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['user']['url'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
" />
							</td>
						</tr>

						<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
							<td width="200">
							<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "pers_hi"), $this);?>
							</td>
							<td>
							<textarea name="pers_hi" style="width:100%;" rows="2"><?php echo $this->_vars['user']['pers_hi']; ?>
</textarea>
							</td>
						</tr>

						<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
							<td width="200">
							<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "gender"), $this);?>
							</td>
							<td>							
							<input type="radio" name="gender" value="m" <?php if ($this->_run_modifier($this->_vars['user']['gender'], 'lower', 'plugin', 1) == "m"): ?>checked="checked"<?php endif; ?>> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "gender_m"), $this);?><br>
							<input type="radio" name="gender" value="f"<?php if ($this->_run_modifier($this->_vars['user']['gender'], 'lower', 'plugin', 1) == "f"): ?>checked="checked"<?php endif; ?>> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "gender_f"), $this);?><br>
							<input type="radio" name="gender" value="-"<?php if ($this->_run_modifier($this->_vars['user']['gender'], 'lower', 'plugin', 1) != "m" && $this->_run_modifier($this->_vars['user']['gender'], 'lower', 'plugin', 1) != "f"): ?>checked="checked"<?php endif; ?>> -<br>
							</td>
						</tr>

						<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
							<td width="200">
							<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "user_title"), $this);?>
							</td>
							<td>
							<input type=text name=user_title style="width:100%;" maxlength=255 value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['user']['user_title'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
" />
							</td>
						</tr>
						
						<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
							<td width="200">
							<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "site_url"), $this);?>
							</td>
							<td>
							<select name="site_id">
								<option value="0">- <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "all"), $this);?></option>
								<?php if (! empty ( $this->_vars['site']['list_sites'] )): ?>
									<?php if (count((array)$this->_vars['site']['list_sites'])): foreach ((array)$this->_vars['site']['list_sites'] as $this->_vars['si']): ?>
										<option value="<?php echo $this->_vars['si']['id']; ?>
" <?php if ($this->_vars['si']['id'] == $this->_vars['user']['site_id']): ?>selected="selected"<?php endif; ?>><?php echo $this->_vars['si']['id']; ?>
: <?php echo $this->_run_modifier($this->_vars['si']['site_url'], 'delhttp', 'plugin', 1); ?>
</option>
									<?php endforeach; endif; ?>
								<?php endif; ?>
							</select>
							</td>
						</tr>
						
						<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
							<td width="200">
							<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "ref"), $this);?> 1
							</td>
							<td>
							<?php if (empty ( $this->_vars['user']['id'] )): ?>
								<input type="text" name="ref1" style="width:100%;" maxlength="255" value="0" />
							<?php elseif (! empty ( $this->_vars['user']['ref1'] )): ?>
								<?php echo $this->_vars['user']['ref1']; ?>

							<?php else: ?>
								-
							<?php endif; ?>
							</td>
						</tr>	
						
						<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
							<td width="200">
							<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "ref"), $this);?> 2
							</td>
							<td>
							<?php if (empty ( $this->_vars['user']['id'] )): ?>
								<input type="text" name="ref2" style="width:100%;" maxlength="255" value="0" />
							<?php elseif (! empty ( $this->_vars['user']['ref2'] )): ?>
								<?php echo $this->_vars['user']['ref2']; ?>

							<?php else: ?>
								-
							<?php endif; ?>
							</td>
						</tr>						
						
					</tbody>
				</table>
			</div>
		</td>
	</tr>
	
	

	<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
		<td width=200><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "status",'key3' => "active"), $this);?></td>
        <input type="hidden" name="active" value="0">
        <td><input type="checkbox" name="active" value="1"<?php if (! empty ( $this->_vars['user']['active'] )): ?> checked="checked"<?php endif; ?>></td>
	</tr>
	
	<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
		<td width=200><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "news"), $this);?></td>
		<input type="hidden" name="news" value="0">
		<td><input type="checkbox" name="news" value="1"<?php if ($this->_vars['user']['news'] == 1): ?> checked="checked"<?php endif; ?>></td>
	</tr>    
		  
	<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
		<td width=200><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "admin"), $this);?></td>
		<input type="hidden" name="admin" value="0">
		<td><input type="checkbox" name="admin" value="1"<?php if ($this->_vars['user']['admin'] == 1): ?> checked="checked"<?php endif; ?>></td>
	</tr>    

<?php if ($this->_run_modifier($this->_vars['user']['prava'], 'count', 'PHP', 0) > 0): ?>
  <?php if (count((array)$this->_vars['user']['prava'])): foreach ((array)$this->_vars['user']['prava'] as $this->_vars['key'] => $this->_vars['value']): ?>
    <?php if ($this->_vars['key'] != "bo_userid"): ?>
      <tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
        <td width=200><?php if (isset ( $this->_vars['pravo'][$this->_vars['key']] )):  echo $this->_vars['pravo'][$this->_vars['key']];  else:  echo $this->_vars['key'];  endif; ?></td>
        <input type="hidden" name="pravo[<?php echo $this->_vars['key']; ?>
]" value="0">
        <td><input type="checkbox" name="pravo[<?php echo $this->_vars['key']; ?>
]" value="1"<?php if ($this->_vars['value'] == 1): ?> checked<?php endif; ?>></td>
      </tr>    
    <?php endif; ?>
  <?php endforeach; endif;  endif; ?>


	<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
		<td width=200><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "avatar"), $this);?></td>
		<td><?php if (! empty ( $this->_vars['user']['avatar'] )): ?>
                <p><img src="<?php echo $this->_vars['user']['avatar']; ?>
" width="50" border="0" /></p>
                <input type="checkbox" name="del_avatar" value="1"> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "delete"), $this);?><br>
            <?php endif; ?>
            <input type="file" name="add_avatar" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "choose_photo"), $this);?>">
			<ul>
				<?php if (! empty ( $this->_vars['site_vars']['img_size1'] )): ?><li><?php echo $this->_vars['site_vars']['img_size1']['width']; ?>
*<?php echo $this->_vars['site_vars']['img_size1']['height']; ?>
</li><?php endif; ?>
				<?php if (! empty ( $this->_vars['site_vars']['img_size2'] )): ?><li><?php echo $this->_vars['site_vars']['img_size2']['width']; ?>
*<?php echo $this->_vars['site_vars']['img_size2']['height']; ?>
</li><?php endif; ?>
				<?php if (! empty ( $this->_vars['site_vars']['img_size3'] )): ?><li><?php echo $this->_vars['site_vars']['img_size3']['width']; ?>
*<?php echo $this->_vars['site_vars']['img_size3']['height']; ?>
</li><?php endif; ?>
			</ul>
		</td>
	</tr>    

	<tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
		<td colspan=2 align=center>
			<input type=submit name=save value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "save"), $this);?>">
			<?php if (! empty ( $this->_vars['user']['id'] )): ?>
				<input class="small" type=submit name=del value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "delete"), $this);?>" onclick="if(confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "really"), $this);?>')) return true; else return false;">
			<?php endif; ?>
		</td>
	</tr>
</form>
</table>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>