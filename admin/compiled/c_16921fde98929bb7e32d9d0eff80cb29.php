<?php ?><input type="hidden" name="record_type" value="<?php echo $this->_vars['record_type']; ?>
" />
    
	<blockquote>
    <p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "extensions"), $this);?> - <a href="?action=settings&do=site_vars&site_id=-1&q=sys_upload_ext_allowed&redirect=1"><?php if (isset ( $this->_vars['site_vars']['sys_upload_ext_allowed'] ) && $this->_vars['site_vars']['sys_upload_ext_allowed'] != "*"):  echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "allowed"), $this);?> - <?php echo $this->_vars['site_vars']['sys_upload_ext_allowed'];  else: ?><span style="color:red;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "any_format"), $this);?></span><?php endif; ?></a></p>
	</blockquote>
    <table width="100%" id="upload_files" border=0 bgcolor="<?php echo $this->_vars['admin_vars']['bglight']; ?>
">
      <tr>
        <th width="60%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "name"), $this);?></th>
        <th><small style="font-weight:normal;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "may_download"), $this);?></small></th>
        <th><small style="font-weight:normal;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "direct_link"), $this);?></small></th>
        <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "file"), $this);?> <small style="font-weight:normal;">(<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "maximum"), $this);?>: <?php echo get_cfg_var('post_max_size'); ?>)</small></th>
      </tr>
      <tr>
        <td><input type="text" name="file_title[0]" style="width: 100%;" /></td>
    	  <td align="center">
          <input name="allow_download[0]" type="checkbox" value="1"  /></td>
        <td align="center">
          <input name="direct_link[0]" type="checkbox" value="1" /></td>
        <td><input type="file" name="files[0]" size="10" /></td>
      </tr>
    </table>
      
      <table border="0" cellpadding="3">
        <tr>
          <td><a href="javascript:add_more_files()"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a></td>
        </tr>
      </table>
		  
		  <input type="hidden" name="numfiles" id="num_files_field" value="1" />

		  <div id="field_to_add1" style="display: none;">
  		  <input type="text" style="width: 100%;" name="file_title[]" />
		  </div>

      <script>
      <?php echo '
          function add_more_files(){
            var tbl = document.getElementById(\'upload_files\');
            var numfiles = document.getElementById(\'num_files_field\').value;
            var tr = tbl.insertRow(-1);
            var td1 = tr.insertCell(-1);
            var td2 = tr.insertCell(-1);
            var td3 = tr.insertCell(-1);
            var td4 = tr.insertCell(-1);
            var input = document.getElementById(\'field_to_add1\').innerHTML;
            
            input = input.replace(/__ID__/g, numfiles);
  		  
        	  td1.innerHTML = input;
            td2.innerHTML = \'<input type="hidden" name="allow_download[\'+numfiles+\']" value="0" /><input type="checkbox" name="allow_download[\'+numfiles+\']" value="1" />\';
            td3.innerHTML = \'<input type="hidden" name="direct_link[\'+numfiles+\']" value="0" /><input type="checkbox" name="direct_link[\'+numfiles+\']" value="1"  />\';
            td4.innerHTML = \'<input type="file" name="files[\'+numfiles+\']" size="10"  onchange="add_more_files()" />\';
            
            td2.align = \'center\';
            td3.align = \'center\';
            
            numfiles++;
            document.getElementById(\'num_files_field\').value = numfiles;
          }
      '; ?>

      </script>
