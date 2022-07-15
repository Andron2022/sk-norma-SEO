<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<script language="Javascript" type="text/javascript" src="/admin/js/editarea/edit_area/edit_area_full.js"></script>
<script language="Javascript" type="text/javascript">
  <?php echo '
    // initialisation
    editAreaLoader.init({
      id: "example_1" // id of the textarea to transform    
      ,start_highlight: true  // if start with highlight
      ,allow_resize: "both"
      ,allow_toggle: true
      ,word_wrap: true
      ,language: "en"
      ,syntax: "php" 
      ,toolbar: "search, go_to_line, fullscreen, |, undo, redo, |, select_font,|, highlight, word_wrap"
    });

    // callback functions
    function my_save(id, content){
      alert("Here is the content of the EditArea \'"+ id +"\' as received by the save callback function:\\n"+content);
    }
    
    function my_load(id){
      editAreaLoader.setValue(id, "The content is loaded from the load_callback function into EditArea");
    }
    
    function test_setSelectionRange(id){
      editAreaLoader.setSelectionRange(id, 100, 150);
    }
    
    function test_getSelectionRange(id){
      var sel =editAreaLoader.getSelectionRange(id);
      alert("start: "+sel["start"]+"\\nend: "+sel["end"]); 
    }
    
    function test_setSelectedText(id){
      text= "[REPLACED SELECTION]"; 
      editAreaLoader.setSelectedText(id, text);
    }
    
    function test_getSelectedText(id){
      alert(editAreaLoader.getSelectedText(id)); 
    }
    
    function editAreaLoaded(id){
      if(id=="example_2")
      {
        open_file1();
        open_file2();
      }
    }
    
    function open_file1()
    {
      var new_file= {id: "to\\\\ é # € to", text: "$authors= array();\\n$news= array();", syntax: \'php\', title: \'beautiful title\'};
      editAreaLoader.openFile(\'example_2\', new_file);
    }
    
    function open_file2()
    {
      var new_file= {id: "Filename", text: "<a href=\\"toto\\">\\n\\tbouh\\n</a>\\n<!-- it\'s a comment -->", syntax: \'html\'};
      editAreaLoader.openFile(\'example_2\', new_file);
    }
    
    function close_file1()
    {
      editAreaLoader.closeFile(\'example_2\', "to\\\\ é # € to");
    }
    
    function toogle_editable(id)
    {
      editAreaLoader.execCommand(id, \'set_editable\', !editAreaLoader.execCommand(id, \'is_editable\'));
    }
    '; ?>

  </script>


<h1 class="mt-0"><?php if ($this->_vars['block']['id'] > 0):  echo add_favorites_tpl(array('where' => "block",'id' => $this->_vars['block']['id']), $this);?> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "edit"), $this); else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "add"), $this); endif; ?></h1>
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("settings/elements_menu.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<table width="80%">
    <tr>
		<td>

<?php if ($this->_vars['admin_vars']['uri']['updated'] == 1): ?>
  <?php $this->assign('href', "?action=settings&do=blocks&id=".$this->_vars['block']['id']); ?>
  <blockquote><a href="<?php echo $this->_vars['href']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "updated"), $this);?></a></blockquote>
<?php elseif ($this->_vars['admin_vars']['uri']['deleted'] == 1): ?>
  <blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "deleted"), $this);?></blockquote>
<?php elseif ($this->_vars['admin_vars']['uri']['added'] == 1): ?>
  <?php $this->assign('href', "?action=settings&do=blocks&id=".$this->_vars['block']['id']); ?>
  <blockquote><a href="<?php echo $this->_vars['href']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "added"), $this);?></a></blockquote>
<?php else: ?>

  <blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "content"), $this);?>
	<?php if ($this->_vars['block']['id'] > 0): ?><br><br><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "code_to_insert_in_tpl"), $this);?>:<br> 
		<b><?php echo $this->left_delimiter; ?>
blocks where="<?php echo $this->_vars['block']['where']; ?>
" name="<?php echo $this->_vars['block']['title']; ?>
"<?php echo $this->right_delimiter; ?>
</b>
		<?php if (! empty ( $this->_vars['block']['where'] ) && $this->_vars['block']['where'] == "form"): ?>
		<br><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "code_to_insert_in_html"), $this);?>: <b>%<?php echo $this->_vars['block']['title']; ?>
%</b>
		<?php endif; ?>
	<?php endif; ?>
  </blockquote>

  
		</td>
	</tr>
</table>
  
  <form method="post">

  <table width="80%">
    <tr>
      <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "key"), $this);?></th>
      <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sites",'key3' => "value"), $this);?></th>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "active"), $this);?></td>
      <td><input type="checkbox" name="block[active]" value="1"<?php if ($this->_vars['block']['active'] == 1): ?> checked<?php endif; ?>></td>
    </tr>

    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "title"), $this);?>*</td>
      <td><input type="text" name="block[title]" style="width:100%;" maxlength="100" value="<?php echo $this->_run_modifier($this->_vars['block']['title'], 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
    </tr>

    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "title_admin"), $this);?></td>
      <td><input type="text" name="block[title_admin]" style="width:100%;" maxlength="255" value="<?php echo $this->_run_modifier($this->_vars['block']['title_admin'], 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "where"), $this);?></td>
      <td><select name="block[where]">
				<?php if (count((array)$this->_vars['block']['where_ar'])): foreach ((array)$this->_vars['block']['where_ar'] as $this->_vars['k'] => $this->_vars['v']): ?>
					<option value="<?php echo $this->_vars['k']; ?>
"<?php if ($this->_vars['block']['where'] == $this->_vars['k']): ?> selected="selected"<?php endif; ?>><?php echo $this->_vars['v']; ?>
</option>
				<?php endforeach; endif; ?>
			</select>	  
	  </td>
    </tr>
        
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "qty"), $this);?></td>
      <td><input type="text" name="block[qty]" style="width:100%;" maxlength="255" value="<?php echo $this->_run_modifier($this->_vars['block']['qty'], 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
    </tr>

    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "status",'key3' => "sort"), $this);?></td>
      <td><input type="text" name="block[sort]" style="width:100%;" maxlength="255" value="<?php echo $this->_run_modifier($this->_vars['block']['sort'], 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "type"), $this);?></td>
      <td><select name="block[type]">
				<?php if (count((array)$this->_vars['block']['type_ar'])): foreach ((array)$this->_vars['block']['type_ar'] as $this->_vars['k'] => $this->_vars['v']): ?>
					<option value="<?php echo $this->_vars['k']; ?>
"<?php if ($this->_vars['block']['type'] == $this->_vars['k']): ?> selected="selected"<?php endif; ?>><?php echo $this->_vars['v']; ?>
</option>
				<?php endforeach; endif; ?>
			</select>	  
	  </td>
    </tr>
	
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "type_id"), $this);?></td>
      <td><input type="text" name="block[type_id]" style="width:100%;" maxlength="255" value="<?php echo $this->_run_modifier($this->_vars['block']['type_id'], 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "pages"), $this);?><br><small><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "pages_comment"), $this);?></small></td>
      <td><textarea name="block[pages]" rows="4" style="width:100%;"><?php echo $this->_run_modifier($this->_run_modifier($this->_vars['block']['pages'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
</textarea></td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "skip_pages"), $this);?><br><small><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "pages_comment"), $this);?></small></td>
      <td><textarea name="block[skip_pages]" rows="4" style="width:100%;"><?php echo $this->_run_modifier($this->_run_modifier($this->_vars['block']['skip_pages'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
</textarea></td>
    </tr>

    <tr >
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "html"), $this);?></td>
      <td><textarea id="example_1" name="block[html]" style="width:100%; height:350px;"><?php echo $this->_run_modifier($this->_run_modifier($this->_vars['block']['html'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
</textarea>
	  	  
	  </td>
    </tr>
	
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "websites"), $this);?></td>
      <td>
			<?php if (count((array)$this->_vars['block']['sites_ar'])): foreach ((array)$this->_vars['block']['sites_ar'] as $this->_vars['v']): ?>
				<input type="checkbox" name="block[sites][]" value="<?php echo $this->_vars['v']['id']; ?>
"<?php if (! empty ( $this->_vars['v']['connected'] )): ?> checked="checked"<?php endif; ?>> <?php echo $this->_vars['v']['name_short']; ?>
 - <?php echo $this->_vars['v']['site_url']; ?>
<br>
			<?php endforeach; endif; ?>
	  </td>
    </tr>

    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td colspan="2" align="center"><input type="submit" name="save" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "save"), $this);?>"> 
      <?php if ($this->_vars['block']['id'] > 0): ?><input type="submit" name="del" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "delete"), $this);?>" class="small" onclick="if(confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "really"), $this);?>')) return true; else return false;"><?php endif; ?>
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