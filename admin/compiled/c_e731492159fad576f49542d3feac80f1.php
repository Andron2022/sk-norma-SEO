<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.escape.php'); $this->register_modifier("escape", "tpl_modifier_escape");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.truncate.php'); $this->register_modifier("truncate", "tpl_modifier_truncate");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0"><?php if (! empty ( $this->_vars['site_var']['id'] )):  echo add_favorites_tpl(array('where' => "var",'id' => $this->_vars['site_var']['id']), $this);?> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "edit_title"), $this); else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "add_title"), $this); endif; ?></h1>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("settings/elements_menu.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<table width="80%"><tr><td>

<?php if (isset ( $this->_vars['messages'] )): ?>
  <?php echo $this->_vars['messages']; ?>

<?php elseif (isset ( $_GET['added'] )): ?>
  <blockquote><a href="?action=settings&do=site_vars&id=<?php echo $this->_vars['site_var']['id']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "added"), $this);?></a></blockquote>
<?php elseif (isset ( $_GET['updated'] )): ?>
  <blockquote><a href="?action=settings&do=site_vars&id=<?php echo $this->_vars['site_var']['id']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "updated"), $this);?></a></blockquote>
  
  <?php $this->assign('readonly_test', $this->_run_modifier($this->_vars['site_var']['name'], 'truncate', 'plugin', 1, 4, "", false)); ?>
    <ul>
    <?php if ($this->_vars['readonly_test'] == "sys_" || $this->_vars['readonly_test'] == "smtp"): ?>
      <?php if ($this->_vars['site_var']['site_id'] == 0): ?>
        <li><a href="?action=settings&do=site_vars&mode=sys&site_id=0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "common_sys_vars"), $this);?></a></li>
      <?php else: ?>
        <li><a href="?action=settings&do=site_vars&mode=sys&site_id=<?php echo $this->_vars['site_var']['site_id']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "site_sys_vars"), $this);?></a></li>
      <?php endif; ?>
    <?php else: ?>
      <?php if ($this->_vars['site_var']['site_id'] == 0): ?>
        <li><a href="?action=settings&do=site_vars&site_id=0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "common_vars"), $this);?></a></li>
      <?php else: ?>
        <li><a href="?action=settings&do=site_vars&site_id=<?php echo $this->_vars['site_var']['site_id']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "site_vars"), $this);?></a></li>
      <?php endif; ?>
    <?php endif; ?>
    </ul>

<?php elseif (! $this->_vars['site_var']): ?>
  <p style="color:red;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "var_not_found"), $this);?></p>
<?php else:  endif; ?>


<?php if ($this->_vars['site_var']['name']): ?>
  <?php $this->assign('readonly_test', $this->_run_modifier($this->_vars['site_var']['name'], 'truncate', 'plugin', 1, 4, "", false)); ?>
  <?php if ($this->_vars['readonly_test'] == "sys_" || $this->_vars['readonly_test'] == "smtp" || $this->_vars['readonly_test'] == "img_"): ?>
    <?php $this->assign('readit', "yes"); ?>
  <?php else: ?>
    <?php $this->assign('readit', "yes"); ?>
  <?php endif; ?>

  
  <blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "code_to_insert_in_tpl"), $this);?>:<br><b>{$site.<?php echo $this->_vars['site_var']['name']; ?>
}</b></blockquote>

</td></tr></table>
   
   <form method="post">
    <?php if ($this->_vars['readit'] == "no"): ?>
      <input type="hidden" name="forsite" value="<?php echo $this->_vars['site_var']['site_id']; ?>
" />
      <input type="hidden" name="varname" value="<?php echo $this->_vars['site_var']['name']; ?>
" />
      <input type="hidden" name="description" value="<?php echo $this->_vars['site_var']['description']; ?>
" />
      <input type="hidden" name="type" value="<?php echo $this->_vars['site_var']['type']; ?>
" />
      <input type="hidden" name="autoload" value="<?php echo $this->_vars['site_var']['autoload']; ?>
" />
      <input type="hidden" name="if_enum" value="<?php echo $this->_vars['site_var']['if_enum']; ?>
" />
      <input type="hidden" name="width" value="<?php echo $this->_vars['site_var']['width']; ?>
" />
      <input type="hidden" name="height" value="<?php echo $this->_vars['site_var']['height']; ?>
" />
    <?php endif; ?>

<table width="80%">
	<?php if (! empty ( $this->_vars['site_var']['other_vars'] )): ?>	
	<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
		<td width="200"><b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "same_elements"), $this);?></b> <a href="?action=settings&do=site_vars&site_id=-1&q=<?php echo $this->_vars['site_var']['name']; ?>
"><i class="fa fa-search"></i></a></td>
		<td>
			<?php if (count((array)$this->_vars['site_var']['other_vars'])): foreach ((array)$this->_vars['site_var']['other_vars'] as $this->_vars['k'] => $this->_vars['v']): ?>
				<?php if ($this->_vars['k'] > 0): ?><br><?php endif; ?> <i class="fa fa-cogs"></i> <a href="?action=settings&do=site_vars&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['name'];  if (! empty ( $this->_vars['v']['site'] )): ?><small> / <?php echo $this->_run_modifier($this->_vars['v']['site_url'], 'delhttp', 'plugin', 1); ?>
</small><?php endif; ?></a></li>
			<?php endforeach; endif; ?>
		</td>
	</tr>				
	<?php endif; ?>				
		
	<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
    	<td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "website"), $this);?>: </td>
    	<td>
          <select name="forsite"<?php if ($this->_vars['readit'] == "no"): ?> disabled="disabled"<?php endif; ?>>
            <option value="0"> - <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "for_all"), $this);?></option>
            <?php if (count((array)$this->_vars['sites'])): foreach ((array)$this->_vars['sites'] as $this->_vars['value']): ?>
            <option value="<?php echo $this->_vars['value']['id']; ?>
"<?php if ($this->_vars['value']['id'] == $this->_vars['site_var']['site_id']): ?> selected<?php endif; ?>><?php echo $this->_run_modifier($this->_vars['value']['site_url'], 'delhttp', 'plugin', 1); ?>
</option>
            <?php endforeach; endif; ?>
          </select>
        </td>
	</tr>
	
	<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
		<td width="200"><b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "name"), $this);?></b>: <br>
		<small>( a-z, 0-9, _ )</small></td>
		<td><input name="varname" type="text" style="width:100%;" value="<?php echo $this->_vars['site_var']['name']; ?>
"<?php if ($this->_vars['readit'] == "no"): ?> readonly<?php endif; ?> /></td>
	</tr>
	
	<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
		<td><b><?php if ($this->_vars['site_var']['type'] != "checkbox"):  echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "value"), $this);?>: <?php else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "on"), $this);?></b>:<?php endif; ?></td>
		<td>
			<?php if ($this->_vars['site_var']['type'] == 'list'): ?>
                <select name="value">
                <?php if (count((array)$this->_vars['site_var']['values_ar'])): foreach ((array)$this->_vars['site_var']['values_ar'] as $this->_vars['value']): ?>
                <option value="<?php echo $this->_vars['value']; ?>
"<?php if ($this->_vars['value'] == $this->_vars['site_var']['value']): ?> selected="selected"<?php endif; ?>><?php echo $this->_vars['value']; ?>
</option>
                <?php endforeach; endif; ?>
                </select>
            <?php elseif ($this->_vars['site_var']['type'] == "checkbox"): ?>
                <input type="hidden" name="value" value="0" />
                <input type="checkbox" name="value" value="1" <?php if ($this->_vars['site_var']['value']): ?> checked="checked"<?php endif; ?> />                
            <?php else: ?>
                <textarea id="value" name="value" rows="7" style="width:100%;"><?php echo $this->_vars['site_var']['value']; ?>
</textarea>
                <?php if ($this->_vars['readit'] == "yes"): ?>
                  <?php if (isset ( $_GET['editor'] )): ?>
                    <p><a href="?action=settings&do=site_vars&id=<?php echo $this->_vars['site_var']['id']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "off_editor"), $this);?></a></p>
                  <?php else: ?>
                    <p><a href="?action=settings&do=site_vars&id=<?php echo $this->_vars['site_var']['id']; ?>
&editor=on"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "on_editor"), $this);?></a></p>
                  <?php endif; ?>      
                <?php endif; ?>
                  <?php if (isset ( $_GET['editor'] )): ?>
                    <script type="text/javascript">
                    	var editor = CKEDITOR.replace( 'value' );
                    	CKFinder.setupCKEditor( editor, '/ckfinder/' ) ;
                    </script>
                  <?php endif; ?>
            <?php endif; ?>
        </td>
	</tr>


	<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
		<td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "description"), $this);?>: </td>
        <td><textarea name="description" rows="5" style="width:100%;"<?php if ($this->_vars['readit'] == "no"): ?> readonly<?php endif; ?>><?php echo $this->_vars['site_var']['description']; ?>
</textarea></td>
	</tr>
    
        <?php if ($this->_vars['readit'] == "yes"): ?>
        <tr>
          <td colspan="2">
          <a href="javascript: ShowHide('block-meta')" style="border-bottom: 1px dashed blue;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "extra"), $this);?></a>
          <div style="display: none;" id="block-meta">
            <table width="100%">
              <tbody>
                <tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
                  <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "type"), $this);?>: </td>
                  <td><input type="radio" name="type" value="text"<?php if ($this->_vars['site_var']['type'] == "text"): ?> checked<?php endif; ?> /> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "text"), $this);?>
                      <input type="radio" name="type" value="list"<?php if ($this->_vars['site_var']['type'] == "list"): ?> checked<?php endif; ?> /> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "select"), $this);?>
                      <input type="radio" name="type" value="checkbox"<?php if ($this->_vars['site_var']['type'] == "checkbox"): ?> checked<?php endif; ?> /> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "flag"), $this);?>
                  </td>
                </tr>
                <tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
                  <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "autoload"), $this);?></td>
                  <td colspan="3"><input type="checkbox" name="autoload" value="1"<?php if ($this->_vars['site_var']['autoload'] == "1"): ?> checked<?php endif; ?>></td>
                </tr>
                <tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
                  <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "variants"), $this);?>:<br><small>(<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "variants_comment"), $this);?>)</small></td>
                  <td colspan="3"><textarea name="if_enum" rows="7" style="width:100%;"><?php echo $this->_vars['site_var']['if_enum']; ?>
</textarea></td>
                </tr>
                <tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
                  <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "size1"), $this);?></td>
                  <td><input type="text" name="width" size="10" maxlength="25" value="<?php echo $this->_vars['site_var']['width']; ?>
"></td>
                  <td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "size2"), $this);?></td>
                  <td><input type="text" name="height" size="10" maxlength="25" value="<?php echo $this->_vars['site_var']['height']; ?>
"></td>
                </tr>
              </tbody>
            </table>
          </div>
          </td>
        </tr>
        <?php elseif ($this->_vars['site_var']['name'] == "img_size1" || $this->_vars['site_var']['name'] == "img_size2" || $this->_vars['site_var']['name'] == "img_size3"): ?>
      		<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
            <td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "sizes"), $this);?>: <br><small>(<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "in_pixels"), $this);?>)</small></td>
            <td>
              <table width="100%" cellpadding="3" cellspacing="1">
               <tr>
                  <td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "width"), $this);?> (width)</td>
                  <td><input type="text" name="width" size="10" maxlength="25" value="<?php echo $this->_vars['site_var']['width']; ?>
"></td>
                  <td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "height"), $this);?> (height)</td>
                  <td><input type="text" name="height" size="10" maxlength="25" value="<?php echo $this->_vars['site_var']['height']; ?>
"></td>
                </tr>
              </table>
            </td>
          </tr>  
      		<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
            <td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "autoload"), $this);?></td>
            <td><?php if ($this->_vars['site_var']['autoload'] == "1"): ?><i class="fa fa-check"></i><?php else: ?><i class="fa fa-minus"></i><?php endif; ?></td>
          </tr>      
        <?php elseif ($this->_vars['readonly_test'] == "img_"): ?>
      		<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
          <td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "sizes"), $this);?>: <br><small>(<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "in_pixels"), $this);?>)</small></td>
          <td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "width"), $this);?> - <?php echo $this->_vars['site_var']['width']; ?>
; <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "height"), $this);?> - <?php echo $this->_vars['site_var']['height']; ?>
</td>
          </tr>  
      		<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
          <td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "autoload"), $this);?></td>
          <td><?php if ($this->_vars['site_var']['autoload'] == "1"): ?><i class="fa fa-check"></i><?php else: ?><i class="fa fa-minus"></i><?php endif; ?></td>
          </tr>      
        <?php else: ?>
        <?php endif; ?>    
    
    <?php echo uploaded_pics_tpl(array('id' => $this->_vars['site_var']['id'],'where' => "var",'assign' => "pics"), $this);?>

		<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
          <td><input type="submit" name="update" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "save"), $this);?>" />
          <?php if ($this->_vars['readit'] == "yes"): ?>
            <input type="submit" name="delete" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "delete"), $this);?>" onclick="if(confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "really"), $this);?>')) return true; else return false;" class="small right" /><?php endif; ?>
          </td>
		  <td align="right"><a href="?action=settings&do=site_vars&id=0&hint=<?php echo $this->_vars['site_var']['name']; ?>
&hint_id=<?php echo $this->_vars['site_var']['id']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "clone"), $this);?></a></td>
        </tr>

		</table>
		</form>


    <?php if ($this->_vars['readonly_test'] == "img_" && ( $this->_vars['site_var']['name'] != "img_size1" && $this->_vars['site_var']['name'] != "img_size2" && $this->_vars['site_var']['name'] != "img_size3" )): ?>
      <table width="80%">
        <tr>
          <td width="50%" valign="top">
            <h3><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "uploaded_pics"), $this);?></h3>
            <?php if (isset ( $_GET['fotosupdated'] )): ?>
              <blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "updated"), $this);?></blockquote>
            <?php endif; ?>

            <?php if ($this->_run_modifier($this->_vars['pics'], 'count', 'PHP', 0) > 0): ?>
              <form method="post">
              <table border=0 width="100%">
                <tr>
                  <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "photo"), $this);?></th>
                  <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "name"), $this);?></th>
                  <th><i class="fa fa-trash"></i></th>
                  <th><i class="fa fa-sort"></i></th>
                </tr>

                <?php if (count((array)$this->_vars['pics'])): foreach ((array)$this->_vars['pics'] as $this->_vars['value']): ?>
                  <tr>
                    <td><a href="/upload/records/<?php echo $this->_vars['value']['id']; ?>
.<?php echo $this->_vars['value']['ext']; ?>
" target="_blank"><?php echo $this->_vars['value']['id']; ?>
.<?php echo $this->_vars['value']['ext']; ?>
</a></td>
                    <td><input type="text" name="imgtitle[<?php echo $this->_vars['value']['id']; ?>
]" value="<?php echo $this->_run_modifier($this->_vars['value']['title'], 'escape', 'plugin', 1); ?>
"></td>
                    <td><input type="checkbox" name="imgdel[<?php echo $this->_vars['value']['id']; ?>
]" value="1"></td>
                    <td><input type="radio" name="imgdefault" value="<?php echo $this->_vars['value']['id']; ?>
"<?php if ($this->_vars['value']['is_default'] == 1): ?> checked<?php endif; ?>></td>
                  </tr>
                <?php endforeach; endif; ?>
              </table>
              <p><input type="submit" name="update_fotos" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "save"), $this);?>"></p>
              </form>
            <?php else: ?><p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "no_uploads"), $this);?></p><?php endif; ?>
          </td>
          <td valign="top">
            <a name="upload"></a>
            <?php if (isset ( $_GET['error'] )): ?>
              <?php if ($_GET['error'] == "wrong_filetype"): ?>
              <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "wrong_filetype"), $this);?>
              <?php elseif ($_GET['error'] == "ok"): ?>
              <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "pic_uploaded"), $this);?>
              <?php elseif ($_GET['error'] == "database_error"): ?>
              <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "db_error"), $this);?>
              <?php elseif ($_GET['error'] == "not_uploaded"): ?>              
              <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "folder_not_open"), $this);?>              
              <?php elseif ($_GET['error'] == "too_small_size"): ?>
              <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "need_bigger_pic"), $this);?>
              <?php else: ?>
              <?php echo $_GET['error']; ?>

              <?php endif; ?>           
            <?php endif; ?>
            <h3><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "upload_pic"), $this);?></h3>
            <form enctype="multipart/form-data" method="post">
              <input type="hidden" name="width" value="<?php echo $this->_vars['site_var']['width']; ?>
" />
              <input type="hidden" name="height" value="<?php echo $this->_vars['site_var']['height']; ?>
" />
              <p><input type="file" name="foto">
              <input type="submit" name="add_file" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "upload"), $this);?>"></p>
            </form>
          </td>
        </tr>
      </table>
    <?php endif; ?>     

<?php endif; ?>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>