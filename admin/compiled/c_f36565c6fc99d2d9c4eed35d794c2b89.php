<?php ?>

<?php echo '
<script>
	function add_options(){
		var tbl = document.getElementById(\'upload_extra\');
		var numextra = document.getElementById(\'num_extra_field\').value;		
		var tr = tbl.insertRow(-1);		
		tr.className = "odd";		
		var td1 = tr.insertCell(-1);
		var td2 = tr.insertCell(-1);
		var td3 = tr.insertCell(-1);	
		var td4 = tr.insertCell(-1);	
		
		td1.innerHTML = \'<input type="text" style="width:100%;" name="extra_options[value][\'+numextra+\']" />\'; 		
		td2.innerHTML = \'<input type="text" style="width:100%;" name="extra_options[value2][\'+numextra+\']" />\';		
		td3.innerHTML = \'<input type="text" style="width:100%;" name="extra_options[value3][\'+numextra+\']" />\';
		td4.innerHTML = numextra;
		td4.style.textAlign = "center";
		numextra++;
		document.getElementById(\'num_extra_field\').value = numextra;
	}
</script>
'; ?>


	<a href="javascript: ShowHide('add_extra')" style="border-bottom: 1px dashed blue;"><?php if (! empty ( $this->_vars['extra_options'] )):  echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "options"), $this);?> (<?php echo $this->_run_modifier($this->_vars['extra_options'], 'count', 'PHP', 0); ?>
)<?php else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "adding_option"), $this); endif; ?></a>
	
	<div style="<?php if (empty ( $this->_vars['extra_options'] )): ?>display: none;<?php endif; ?>" id="add_extra"> 

		<table border=0 id="upload_extra" width="100%">
			<tr class="odd">
				<th width="30%">
					<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "name"), $this);?>
				</th>

				<th width="30%">
					<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "value"), $this);?>
				</th>
				<th>
					<small><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "title"), $this);?></small>
				</th>	
				<th>
					<i class="fa fa-trash"></i>
				</th>
			</tr>
			<?php if (! empty ( $this->_vars['extra_options'] )): ?>
				<?php if (count((array)$this->_vars['extra_options'])): foreach ((array)$this->_vars['extra_options'] as $this->_vars['v']): ?>
					<tr class="odd">
						<td valign="top" width="30%">
							<input type="text" style="width:100%;" name="update_extra[value][<?php echo $this->_vars['v']['id']; ?>
]" value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['v']['value'], 'trim', 'PHP', 1), 'escape', 'plugin', 1); ?>
" />
						</td>

						<td valign="top" width="30%">
							<input type="text" style="width:100%;" name="update_extra[value2][<?php echo $this->_vars['v']['id']; ?>
]" value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['v']['value2'], 'trim', 'PHP', 1), 'escape', 'plugin', 1); ?>
">
						</td>
						<td valign="top">
							<input type="text" style="width:100%;" name="update_extra[value3][<?php echo $this->_vars['v']['id']; ?>
]" value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['v']['value3'], 'trim', 'PHP', 1), 'escape', 'plugin', 1); ?>
">
						</td>	
						<td align="center">
							<input type="checkbox" name="delete_extra_options[]" value="<?php echo $this->_vars['v']['id']; ?>
">
						</td>	
						
					</tr>
				<?php endforeach; endif; ?>
				<tr class="odd">
					<td valign="top" width="30%">
						<input type="text" style="width:100%;" name="extra_options[value][1]" />
					</td>

					<td valign="top" width="30%">
						<input type="text" style="width:100%;" name="extra_options[value2][1]" />
					</td>
					<td valign="top">
						<input type="text" style="width:100%;" name="extra_options[value3][1]" />
					</td>	
					<td align="center">1</td>	
					
				</tr>
				<input type="hidden" name="numextra" id="num_extra_field" value="2" />
			<?php else: ?>
				<tr class="odd">
					<td valign="top" width="30%">
						<input type="text" style="width:100%;" name="extra_options[value][1]" />
					</td>

					<td valign="top" width="30%">
						<input type="text" style="width:100%;" name="extra_options[value2][1]" />
					</td>
					<td valign="top">
						<input type="text" style="width:100%;" name="extra_options[value3][1]" />
					</td>	
					<td align="center">1</td>	
					
				</tr>
				<input type="hidden" name="numextra" id="num_extra_field" value="2" />
			<?php endif; ?>
		</table>
		
		<a href="javascript:add_options();"><i class="fa fa-plus"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a>
		

</div>