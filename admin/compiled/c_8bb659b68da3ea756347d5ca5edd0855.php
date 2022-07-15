<?php ?><blockquote>
<p><b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "size_pixels"), $this);?>:</b> 
<?php if (isset ( $this->_vars['site_vars']['img_size1'] )): ?> 
  1 - <a href="?action=settings&do=site_vars&site_id=-1&q=img_size1&redirect=1"><?php echo $this->_vars['site_vars']['img_size1']['width']; ?>
*<?php echo $this->_vars['site_vars']['img_size1']['height']; ?>
</a>;
<?php endif;  if (isset ( $this->_vars['site_vars']['img_size2'] )): ?> 
   2 - <a href="?action=settings&do=site_vars&site_id=-1&q=img_size2&redirect=1"><?php echo $this->_vars['site_vars']['img_size2']['width']; ?>
*<?php echo $this->_vars['site_vars']['img_size2']['height']; ?>
</a>;<?php endif;  if (isset ( $this->_vars['site_vars']['img_size3'] )): ?> 
   3 - <a href="?action=settings&do=site_vars&site_id=-1&q=img_size3&redirect=1"><?php echo $this->_vars['site_vars']['img_size3']['width']; ?>
*<?php echo $this->_vars['site_vars']['img_size3']['height']; ?>
</a>;<?php endif; ?>
   
<?php if (isset ( $this->_vars['site_vars']['img_size4'] )): ?> 
   4 - <a href="?action=settings&do=site_vars&site_id=-1&q=img_size4&redirect=1"><?php echo $this->_vars['site_vars']['img_size4']['width']; ?>
*<?php echo $this->_vars['site_vars']['img_size4']['height']; ?>
</a>;<?php endif; ?>

<?php if (isset ( $this->_vars['site_vars']['img_size5'] )): ?> 
   5 - <a href="?action=settings&do=site_vars&site_id=-1&q=img_size5&redirect=1"><?php echo $this->_vars['site_vars']['img_size5']['width']; ?>
*<?php echo $this->_vars['site_vars']['img_size5']['height']; ?>
</a>;<?php endif; ?>

<?php if (isset ( $this->_vars['site_vars']['img_size6'] )): ?> 
   6 - <a href="?action=settings&do=site_vars&site_id=-1&q=img_size6&redirect=1"><?php echo $this->_vars['site_vars']['img_size6']['width']; ?>
*<?php echo $this->_vars['site_vars']['img_size6']['height']; ?>
</a>;<?php endif; ?>   

<br><a href="?action=settings&do=site_vars&site_id=-1&q=save_original_fotos&redirect=1"><?php if ($this->_vars['admin_vars']['save_original_fotos'] == 1):  echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "orig_saves",'case' => "first"), $this); else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "orig_willbe_deleted"), $this); endif; ?></a>;   

<?php if (isset ( $this->_vars['site_vars']['sys_resize_method'] )): ?> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "priority"), $this);?> - <a href="?action=settings&do=site_vars&site_id=-1&q=sys_resize_method&redirect=1"><?php echo $this->_vars['site_vars']['sys_resize_method']; ?>
</a>.<?php endif; ?>

<?php if (! empty ( $this->_vars['site_vars']['img_watermark']['img']['0']['url'] )): ?>
<br><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "on_img"), $this);?> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "more"), $this);?> <?php echo $this->_vars['site_vars']['img_watermark']['value']; ?>
 <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "pixels"), $this);?> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "willbe_placed"), $this);?> 
<a href="<?php echo $this->_vars['site_vars']['img_watermark']['img']['0']['url']; ?>
" target="_blank"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "photo",'case' => "lower"), $this);?></a> (<a href="?action=settings&do=site_vars&site_id=-1&q=img_watermark&redirect=1">watermark</a>)
<?php else: ?>
<br><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "for_place_img"), $this);?> (watermark) <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "need_element"), $this);?> <b><a href="?action=settings&do=site_vars&site_id=-1&q=img_watermark&redirect=1">img_watermark</a></b>
<?php endif; ?>
</p>
</blockquote>

<p><input type="checkbox" name="clear_pic_title" value="1"> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "clear_pic_title"), $this);?>
</p>
<input type="hidden" name="record_type" value="<?php echo $this->_vars['record_type']; ?>
" />
<table width="100%" border=0 id="upload_fields" bgcolor="<?php echo $this->_vars['admin_vars']['bglight']; ?>
">
  <tr>
    <th width="60%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "name"), $this);?></th>
    <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "file"), $this);?> <small style="font-weight:normal;">(<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "maximum"), $this);?>: <?php echo get_cfg_var('post_max_size'); ?>)</small></th>
  </tr>
  <tr class="odd">
    <td><input type="text" style="width: 100%;" name="pics_text[0]" /></td>
    <td><input type="file" name="pics[0]" size="10" /></td>
  </tr>
</table>

<?php if ($this->_vars['site_vars']['gallery'] > 0): ?>
<table border="0" cellpadding="3">
  <tr>
    <td><a href="javascript:add_more_photos()"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a></td>
  </tr>
</table>
<?php endif; ?>
		  
<input type="hidden" name="numpics" id="num_pics_field" value="1" />
<script>
  <?php echo '
    function add_more_photos(){
      var tbl = document.getElementById(\'upload_fields\');		  
		  var numpics = document.getElementById(\'num_pics_field\').value;
		  
		  var tr = tbl.insertRow(-1);
		  var td1 = tr.insertCell(-1);
		  var td2 = tr.insertCell(-1);
		  
		  var input = document.getElementById(\'field_to_add\').innerHTML;
		  
		  input = input.replace(/__ID__/g, numpics);  
		  td1.innerHTML = input; 
		  td2.innerHTML = \'<input type="file" name="pics[]" size="10"  onchange="add_more_photos()" />\';
  
		  numpics++;
		  
		  document.getElementById(\'num_pics_field\').value = numpics;
    }
  '; ?>

</script>
		  
<div id="field_to_add" style="display: none;">
  <input type="text" style="width: 100%;" name="pics_text[]" />
</div>