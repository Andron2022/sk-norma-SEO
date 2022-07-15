<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.filesize.php'); $this->register_modifier("filesize", "tpl_modifier_filesize");  ?>  <h4><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "uploaded_files"), $this);?> (<?php echo $this->_vars['categ']['files_qty']; ?>
)</h4>

<table width="100%" class="">
  <tbody>
    <tr>
      <th width="50"><i class="fa fa-paperclip" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "link_to_file"), $this);?>"></i></th>
      <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "name"), $this);?></th>
      <th width="80"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "size"), $this);?></th>
      <th width="80"><small><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "uploaded_date"), $this);?></small></th>
      <th width="80"><i class="fa fa-download" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "free_download"), $this);?>"></i></th>
	  <th width="80"><i class="fa fa-external-link" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "direct_link"), $this);?>"></i></th>
      <th width="80"><i class="fa fa-sort" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "status",'key3' => "sort"), $this);?>"></i></th>
      <th align="center" width="50"><i class="fa fa-trash-o" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "delete"), $this);?>"></i> <input type="checkbox" onclick="CheckAll(this,'delete_files[]'); CheckAll(this,'delete_group2[]');"></th>      
    </tr>
</table>

    <?php if (count((array)$this->_vars['categ']['uploaded_files'])): foreach ((array)$this->_vars['categ']['uploaded_files'] as $this->_vars['key'] => $this->_vars['img']): ?>

                  
        <table id="file<?php echo $this->_vars['img']['id_in_record']; ?>
" width="100%">
          <tr class="odd">
            <td align="center" width="50"><a href="/getfile/?l=<?php echo $this->_run_modifier($this->_vars['img']['id'], 'md5', 'PHP', 1); ?>
&ext=<?php echo $this->_vars['img']['ext']; ?>
&id=<?php echo $this->_vars['img']['record_id']; ?>
" target="_blank"><i class="fa fa-download"></i></a></td>
            <td>
			<textarea name="update_files_title[<?php echo $this->_vars['img']['id']; ?>
]" rows="3" style="width: 100%;"><?php echo $this->_vars['img']['title']; ?>
</textarea>
			
</td>
            <td width="80" align="center"><?php echo $this->_run_modifier($this->_vars['img']['size'], 'filesize', 'plugin', 1); ?>
</td>
            <td width="80" align="center"><?php echo $this->_run_modifier($this->_vars['img']['time_added'], 'date', 'plugin', 1, "d.m.Y"); ?>
</td>
            <td width="80" align="center"><input type="checkbox" name="update_allow_download[<?php echo $this->_vars['img']['id']; ?>
]" value="1"<?php if (! empty ( $this->_vars['img']['allow_download'] )): ?> checked="checked"<?php endif; ?> /></td>
			<td width="80" align="center"><input type="checkbox" name="update_direct_link[<?php echo $this->_vars['img']['id']; ?>
]" value="1"<?php if (! empty ( $this->_vars['img']['direct_link'] )): ?> checked="checked"<?php endif; ?> /></td>
            <td align="center" width="80" nowrap><a href="javascript:" onclick="MoveUp(this);"><i class="fa fa-chevron-up"></i></a>&nbsp;<span id="file<?php echo $this->_vars['img']['id_in_record']; ?>
_position_text"><?php echo $this->_vars['img']['id_in_record']; ?>
</span>&nbsp;<a href="javascript:" onclick="MoveDown(this);"><i class="fa fa-chevron-down"></i></a></td>
            <input type="hidden" name="file<?php echo $this->_vars['img']['id_in_record']; ?>
_position" id="file<?php echo $this->_vars['img']['id_in_record']; ?>
_position" value="<?php echo $this->_vars['img']['id_in_record']; ?>
">
            <td align="center" width="50"><input type="checkbox" name="delete_files[]" value="<?php echo $this->_vars['img']['id']; ?>
"></td>
          </tr>
        </table>
          
    <?php endforeach; endif; ?>