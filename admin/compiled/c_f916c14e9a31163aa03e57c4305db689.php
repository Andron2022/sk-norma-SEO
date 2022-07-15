<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.default.php'); $this->register_modifier("default", "tpl_modifier_default");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.truncate.php'); $this->register_modifier("truncate", "tpl_modifier_truncate");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.count_characters.php'); $this->register_modifier("count_characters", "tpl_modifier_count_characters");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.replace.php'); $this->register_modifier("replace", "tpl_modifier_replace");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0 mb-0"><?php if ($this->_vars['site']['id'] > 0):  echo add_favorites_tpl(array('where' => "site",'id' => $this->_vars['site']['id']), $this);?> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "edit"), $this); else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "add"), $this); endif; ?></h1>

  
<?php if ($this->_vars['admin_vars']['uri']['updated'] == 1): ?>
  <?php $this->assign('href', "?action=settings&do=site&mode=edit&id=".$this->_vars['id']); ?>
	<table width="80%">
		<tr>
			<td><blockquote><a href="<?php echo $this->_vars['href']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "updated"), $this);?></a></blockquote></td>
		</tr>
	</table>
<?php elseif ($this->_vars['admin_vars']['uri']['deleted'] == 1): ?>
	<table width="80%">
		<tr>
			<td><blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "deleted"), $this);?></blockquote></td>
		</tr>
	</table>
<?php elseif ($this->_vars['admin_vars']['uri']['added'] == 1): ?>
  <?php $this->assign('href', "?action=settings&do=site&mode=edit&id=".$this->_vars['id']); ?>
	<table width="80%">
		<tr>
			<td><blockquote><a href="<?php echo $this->_vars['href']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "added"), $this);?></a></blockquote></td>
		</tr>
	</table>
<?php else: ?>

	<table width="80%">
		<tr>
			<td><blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "content"), $this);?></blockquote></td>
		</tr>
	</table>

  <form method="post">
  <table width="80%">
    <tr>
      <th width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "key"), $this);?></th>
      <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "value"), $this);?></th>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "active"), $this);?></td>
      <td><input type="checkbox" name="site_active" value="1"<?php if ($this->_vars['site']['site_active'] == 1): ?> checked<?php endif; ?>></td>
    </tr>

    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "default_page"), $this);?></td>
      <td><select name="default_id_categ" style="width: 200px; word-break: break-all;">
      <option value="0">- <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "default"), $this);?></option>
	  
	  <?php if (! empty ( $this->_vars['site_vars']['_pages'] )): ?>
	  <?php if (count((array)$this->_vars['site_vars']['_pages'])): foreach ((array)$this->_vars['site_vars']['_pages'] as $this->_vars['v']): ?>
						
						<option value="<?php echo $this->_vars['v']['id']; ?>
"<?php if ($this->_vars['site']['default_id_categ'] == $this->_vars['v']['id']): ?> selected="selected"<?php endif; ?>><?php if ($this->_vars['v']['level'] > 1): ?>
							<?php for($for1 = 1; ((1 < $this->_vars['v']['level']) ? ($for1 < $this->_vars['v']['level']) : ($for1 > $this->_vars['v']['level'])); $for1 += ((1 < $this->_vars['v']['level']) ? 1 : -1)):  $this->assign('current', $for1); ?> - <?php endfor; ?>
						<?php endif;  echo $this->_vars['v']['title']; ?>
</option>
						
		<?php endforeach; endif; ?>
	  <?php endif; ?>
	  
	  
      
      </select></td>
    </tr>

    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "full_name"), $this);?></td>
      <td><input type="text" name="name_full" style="width:100%;" maxlength="255" value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['site']['name_full'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
    </tr>

    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "short_name"), $this);?></td>
      <td><input type="text" name="name_short" style="width:100%;" maxlength="100" value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['site']['name_short'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "description"), $this);?></td>
      <td><input type="text" name="site_description" style="width:100%;" maxlength="255" value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['site']['site_description'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "url"), $this);?></td>
      <td><input type="text" name="site_url" style="width:100%;" maxlength="255" value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['site']['site_url'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "email"), $this);?></td>
      <td><input type="text" name="email_info" style="width:100%;" maxlength="255" value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['site']['email_info'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "phone"), $this);?></td>
      <td><input type="text" name="phone" style="width:100%;" maxlength="255" value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['site']['phone'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
    </tr>
            
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "meta_title"), $this);?></td>
      <td><textarea name="meta_title" rows="2" style="width:100%;"><?php echo $this->_run_modifier($this->_run_modifier($this->_vars['site']['meta_title'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
</textarea></td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "meta_description"), $this);?></td>
      <td><textarea name="meta_description" rows="4" style="width:100%;"><?php echo $this->_run_modifier($this->_run_modifier($this->_vars['site']['meta_description'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
</textarea></td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "keywords"), $this);?></td>
      <td><textarea name="meta_keywords" rows="4" style="width:100%;"><?php echo $this->_run_modifier($this->_run_modifier($this->_vars['site']['meta_keywords'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
</textarea></td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200" valign="top"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "tpl"), $this);?></td>
      <td>
          <table width="100%" cellpadding="3">
            <tr>
              <td width="50%" valign="top">
                <select name="template_path">
                <?php if (count((array)$this->_vars['tpls'])): foreach ((array)$this->_vars['tpls'] as $this->_vars['folder']): ?>
                  <option value="/tpl/<?php echo $this->_vars['folder']; ?>
/"<?php if ($this->_run_modifier($this->_vars['site']['template_path'], 'replace', 'plugin', 1, "/tpl/,/") == $this->_vars['folder']): ?> selected<?php endif; ?>><?php echo $this->_vars['folder']; ?>
</option>
                <?php endforeach; endif; ?>
                </select>
              </td>
              <td valign="top">
              <?php if ($this->_vars['site']['id'] > 0): ?>
                <ul>
                  <?php if ($this->_run_modifier($this->_vars['site']['site_url'], 'count_characters', 'plugin', 1, true) > 7 && $this->_run_modifier($this->_vars['site']['site_url'], 'truncate', 'plugin', 1, 7, "", true) == "http://"): ?>
                    <?php $this->assign('url', $this->_vars['site']['site_url']); ?>
                  <?php else: ?>
                    <?php $this->assign('url', '..'); ?>
                  <?php endif; ?>

                <?php if (count((array)$this->_vars['tpls'])): foreach ((array)$this->_vars['tpls'] as $this->_vars['folder']): ?>
                  <li><a href="<?php echo $this->_vars['url']; ?>
/?set_tpl=<?php echo $this->_vars['folder']; ?>
" target="_blank"><i class="fa fa-external-link"></i> <?php echo $this->_vars['folder']; ?>
</a></li>
                <?php endforeach; endif; ?>
                </ul>
              <?php endif; ?>
              </td>
            </tr>
          </table>        
      </td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "fb_in_db"), $this);?></td>
      <td><input type="checkbox" name="feedback_in_db" value="1"<?php if ($this->_vars['site']['feedback_in_db'] == 1): ?> checked<?php endif; ?>></td>
    </tr>

    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "gallery"), $this);?></td>
      <td><input type="checkbox" name="gallery" value="1"<?php if ($this->_vars['site']['gallery'] > 0): ?> checked<?php endif; ?>></td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "basket"), $this);?></td>
      <td><input type="checkbox" name="mode_basket" value="1"<?php if ($this->_vars['site']['mode_basket'] == 1): ?> checked<?php endif; ?>></td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "codepage"), $this);?></td>
      <td><input type="text" name="site_charset" style="width:100%;" maxlength="255" value="<?php echo $this->_run_modifier($this->_run_modifier($this->_run_modifier($this->_vars['site']['site_charset'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1), 'default', 'plugin', 1, "utf-8"); ?>
" readonly /></td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "lang"), $this);?></td>
      <td>
        <select name="lang">
          <?php if (count((array)$this->_vars['admin_vars']['sitelangs'])): foreach ((array)$this->_vars['admin_vars']['sitelangs'] as $this->_vars['key'] => $this->_vars['value']): ?>
            <option value="<?php echo $this->_vars['key']; ?>
"<?php if ($this->_vars['site']['lang'] == $this->_vars['key']): ?> selected<?php endif; ?>><?php echo $this->_vars['value']; ?>
</option>
          <?php endforeach; endif; ?>    
        </select>
      </td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "time_zone"), $this);?></td>
      <td>
        <select name="site_time_zone">
          <?php if (count((array)$this->_vars['admin_vars']['timezone'])): foreach ((array)$this->_vars['admin_vars']['timezone'] as $this->_vars['key'] => $this->_vars['value']): ?>
            <option value="<?php echo $this->_vars['key']; ?>
"<?php if ($this->_vars['site']['site_time_zone'] == $this->_vars['key']): ?> selected<?php endif; ?>><?php echo $this->_vars['key']; ?>
 <?php echo $this->_vars['value']; ?>
</option>
          <?php endforeach; endif; ?>    
        </select>      
      </td>
    </tr>
        
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "date_format"), $this);?></td>
      <td>
        <select name="site_date_format">
          <?php if (count((array)$this->_vars['admin_vars']['dateformat'])): foreach ((array)$this->_vars['admin_vars']['dateformat'] as $this->_vars['key'] => $this->_vars['value']): ?>
            <option value="<?php echo $this->_vars['key']; ?>
"<?php if ($this->_vars['site']['site_date_format'] == $this->_vars['key']): ?> selected<?php endif; ?>><?php echo $this->_vars['value']; ?>
</option>
          <?php endforeach; endif; ?>    
        </select>      
      </td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "time_format"), $this);?></td>
      <td>
        <select name="site_time_format">
          <?php if (count((array)$this->_vars['admin_vars']['timeformat'])): foreach ((array)$this->_vars['admin_vars']['timeformat'] as $this->_vars['key'] => $this->_vars['value']): ?>
            <option value="<?php echo $this->_vars['key']; ?>
"<?php if ($this->_vars['site']['site_time_format'] == $this->_vars['key']): ?> selected<?php endif; ?>><?php echo $this->_vars['value']; ?>
</option>
          <?php endforeach; endif; ?>
        </select>
      </td>
    </tr>
	
	<?php if (! empty ( $this->_vars['orgs'] )): ?>
	<tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "orgs"), $this);?></td>
      <td>
        <?php if (count((array)$this->_vars['orgs'])): foreach ((array)$this->_vars['orgs'] as $this->_vars['k'] => $this->_vars['v']): ?>
			<input type="checkbox" name="orgs[]" value="<?php echo $this->_vars['v']['id']; ?>
"<?php if (! empty ( $this->_vars['v']['connected'] )): ?> checked="checked"<?php endif; ?>> <a href="?action=org&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['title']; ?>
</a><?php if (! empty ( $this->_vars['v']['inn'] )): ?> <?php echo $this->_vars['v']['inn'];  endif; ?><br>
        <?php endforeach; endif; ?>
      </td>
    </tr>
	<?php endif; ?>

    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "ip"), $this);?><br><small>(<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "ip_help"), $this);?>)</small></td>
      <td><input type="text" name="office_ip" style="width:100%;" maxlength="255" value="<?php echo $this->_vars['site']['office_ip']; ?>
" /></td>
    </tr>

    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td colspan="2" align="center"><input type="hidden" name="site_id" value="<?php echo $this->_vars['id']; ?>
"> <input type="submit" name="save" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "save"), $this);?>"> 
      <?php if (! empty ( $this->_vars['id'] )): ?><input type="submit" name="del" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "delete"), $this);?>" class="small"  onclick="if(confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "really"), $this);?>')) return true; else return false;"><?php endif; ?>
      </td>
    </tr>
  </table>
  </form>
 
<?php endif; ?> 
 
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>