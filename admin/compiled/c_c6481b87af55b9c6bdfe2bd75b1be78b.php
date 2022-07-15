<?php  echo '
<script>

	function add_more_blocks(){
		var tbl = document.getElementById(\'upload_blocks\');
		var numpics = document.getElementById(\'num_pics_field\').value;		
		var tr = tbl.insertRow(-1);		
		tr.className = "odd"+numpics;		
		var td1 = tr.insertCell(-1);
		var td2 = tr.insertCell(-1);
		var td3 = tr.insertCell(-1);	

		var select0 = document.getElementById(\'default_type\');
		default_type = select0.innerHTML;
		default_type = default_type.replace("block[gruppa][0]", "block[gruppa]["+numpics+"]");
		
		  //td1.innerHTML = input; 
		  td1.innerHTML = default_type+\'<br>Порядок <small>сортировки</small><br><input type="text" name="block[sort][\'+numpics+\']" /><br>Показывать <small>(active)</small><br><input type="checkbox" name="block[active][\'+numpics+\']" value="1" /><br>Кол-во в ряд <small>(qty)</small><br><select name="block[qty][\'+numpics+\']"><option value="0">0</option><option value="1" selected="selected">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="6">6</option></select><br>\'; 
		  td1.style.verticalAlign = "top";

          td2.innerHTML = \'ID: \'+numpics+\'<br>Заголовок <small>(title)</small><br><textarea name="block[title][\'+numpics+\']" rows="2" style="width:100%;"></textarea><br>Доп.поле (extra)<br><input type="text" style="width:100%;" name="block[extra][\'+numpics+\']" value=""><br><div id="addfrm\'+numpics+\'"></div><div id="bntAddPhoto\'+numpics+\'"><input type="submit" onclick="newInputAdd(\'+numpics+\');" value="+"></div><br>\';
		  td2.style.verticalAlign = "top";

          td3.innerHTML = \'Описание <small>(text)</small><br><textarea name="block[text][\'+numpics+\']" rows="10" style="width:100%;"></textarea>\';
		  td3.style.verticalAlign = "top";
		  
		  numpics++;		  
		  document.getElementById(\'num_pics_field\').value = numpics;
	}
	

	function btnDelete(id,id_block){		
		var count=($("#"+id).parent().children(\'div\').length);		
		
		if(count < 11){
			var newAdd = \'<input type="submit" onclick="newInputAdd(\'+id_block+\')" value="+">\';
			document.getElementById(\'bntAddPhoto\'+id_block).innerHTML = newAdd;
		}
		
		var div = document.getElementById(id);
		div.parentNode.removeChild(div);
	}
	
	function newInputAdd(id) {
		//var count = parseInt(document.getElementById("count").value);	
		var count=($("#addfrm"+id).children(\'div\').length);

		var newId = count;  
		//document.getElementById(\'count\').value = count+1;
		var firstform = document.getElementById(\'addfrm\'+id);

		var div = document.createElement(\'div\');
		div.setAttribute("id", "b"+id+"f_"+newId);
		div.innerHTML = \'<span style="color:red">\'+parseInt(newId+1)+\'. </span><input type="submit" onclick="btnDelete(\\\'b\'+id+\'f_\'+newId+\'\\\', \\\'\'+id+\'\\\')" value="x"> <input type="file" name="block[file][\'+id+\'][\'+newId+\']" style="width:120px; font-size:10px;"><br>\';
		document.getElementById(\'addfrm\'+id).appendChild(div);
	  if(newId > 8){
		var newAdd = \'\';
	  }else{
		var newAdd = \'<input type="submit" onclick="newInputAdd(\'+id+\')" value="+">\';
	  }
	  document.getElementById(\'bntAddPhoto\'+id).innerHTML = newAdd;
	}

	function btnDeleteInBlock(id,id_block){		
		var count=($("#"+id).parent().children(\'div\').length);		
		
		if(count < 11){
			var newAdd = \'<input type="submit" onclick="newInputAddInBlock(\'+id_block+\')" value="+">\';
			document.getElementById(\'bntAddPhotoInBlock\'+id_block).innerHTML = newAdd;
		}
		
		var div = document.getElementById(id);
		div.parentNode.removeChild(div);
	}

	function newInputAddInBlock(id) {
		//var count = parseInt(document.getElementById("count").value);	
		var count=($("#addfrminblock"+id).children(\'div\').length);

		var newId = count;  
		//document.getElementById(\'count\').value = count+1;
		var firstform = document.getElementById(\'addfrminblock\'+id);

		var div = document.createElement(\'div\');
		div.setAttribute("id", "bu"+id+"f_"+newId);
		div.innerHTML = \'<span style="color:red">\'+parseInt(newId+1)+\'. </span><input type="submit" onclick="btnDeleteInBlock(\\\'bu\'+id+\'f_\'+newId+\'\\\', \\\'\'+id+\'\\\')" value="x"> <input type="file" name="block[add_file][\'+id+\'][\'+newId+\']" style="width:120px; font-size:10px;"><br>\';
		document.getElementById(\'addfrminblock\'+id).appendChild(div);
	  if(newId > 8){
		var newAdd = \'\';
	  }else{
		var newAdd = \'<input type="submit" onclick="newInputAddInBlock(\'+id+\')" value="+">\';
	  }
	  document.getElementById(\'bntAddPhotoInBlock\'+id).innerHTML = newAdd;
	}
	
</script>'; ?>


<?php if (empty ( $this->_vars['record_type'] )):  $this->assign('record_type', "product");  endif; ?>
<input type="hidden" name="record_type" value="<?php echo $this->_vars['record_type']; ?>
" />

	<blockquote>
	<h3><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "manual"), $this);?></h3>
	<p><i class="fa fa-sort"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "status",'key3' => "sort"), $this);?>: <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "sort_help"), $this);?></p>
	<p><i class="fa fa-trash"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "del_help"), $this);?>. <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "block_help"), $this);?></p>
	</blockquote>


	<?php if (! empty ( $this->_vars['blocks'] )): ?>
		<?php if (count((array)$this->_vars['blocks'])): foreach ((array)$this->_vars['blocks'] as $this->_vars['b']): ?>
		
			<h4 align="center"><?php if (! empty ( $this->_vars['b']['title'] )):  echo $this->_vars['b']['title'];  else: ?>Блок<?php endif; ?></h4>
				<?php if (! empty ( $this->_vars['b']['title_admin'] )): ?><p align="center"><?php echo $this->_run_modifier($this->_vars['b']['title_admin'], 'nl2br', 'PHP', 1); ?>
</p><?php endif; ?>
			</h4>
			<table width="100%">
				<tr class="odd">
					<td width="30"><input type="text" name="block_update[sort][<?php echo $this->_vars['b']['id']; ?>
]" value="<?php echo $this->_run_modifier($this->_vars['b']['sort'], 'escape', 'plugin', 1); ?>
" 	size="3" style="border: 2px solid #db2020;  color:#db2020; text-align:center;" /></td>
					<td width="200"><i class="fa fa-sort"></i> <small>Порядок сортировки</small></td>
					<td width="200">
					<?php if (empty ( $this->_vars['b']['where_placed'] ) || $this->_vars['b']['where_placed'] == "manual"): ?>Без группы<?php else: ?><b><?php echo $this->_vars['b']['where_placed']; ?>
</b><?php endif;  if (isset ( $this->_vars['b']['qty'] )): ?> <span style="color:red; font-weight: bold; float:right;"><?php echo $this->_vars['b']['qty']; ?>
 в ряд</span><?php endif; ?>
					</td>
					<td align="center">
						<small>
						ID-<?php echo $this->_vars['b']['id']; ?>
 <a href="javascript: ShowHide('block-<?php echo $this->_vars['b']['id']; ?>
')" style="border-bottom: 1px dashed blue;">Показать/Скрыть</a></small>
					</td>
					<td width="200">
						<input type="checkbox" name="block_update[active][<?php echo $this->_vars['b']['id']; ?>
]" value="1" <?php if (! empty ( $this->_vars['b']['active'] )): ?>checked="checked" <?php endif; ?>> <small>Показывается</small>
					</td>
					<td width="100" align="right"> 
						<?php if (! empty ( $this->_vars['b']['title'] )): ?><i class="fa fa-text-width" title="Заголовок"></i> <?php endif; ?>
						<?php if (! empty ( $this->_vars['b']['html'] )): ?><i class="fa fa-align-left" title="Текст"></i> <?php endif; ?>
						<?php if (! empty ( $this->_vars['b']['list_photos'] )): ?> <i class="fa fa-camera" title="Элементы"></i> <?php echo $this->_run_modifier($this->_vars['b']['list_photos'], 'count', 'PHP', 0); ?>
 <?php endif; ?>
					</td>
					
				</tr>
			</table>
		
			<div style="display: none;" id="block-<?php echo $this->_vars['b']['id']; ?>
"> 	

			<table border=0  width="100%">
				<tr class="odd">
					<td width="200" align="right">
						Комментарий<br><small>(виден только в админке)</small>
					</td>
					<td>
						<textarea name="block_update[title_admin][<?php echo $this->_vars['b']['id']; ?>
]" rows="2" style="width:100%;"><?php echo $this->_vars['b']['title_admin']; ?>
</textarea>
					</td>
				</tr>
				<tr class="odd">
					<td width="200" align="right">
						<b>Тип блока</b> <small>(gruppa)</small>
					</td>
					<td>
					
						<table width="100%">
							<tr>
								<td width="30%">

									<select name="block_update[gruppa][<?php echo $this->_vars['b']['id']; ?>
]" style="width:200px;">
										<option value="manual"<?php if ($this->_vars['b']['where_placed'] == "manual"): ?> selected="selected"<?php endif; ?>>-</option>
										
										<optgroup label="Служебные">
											<option value="comments"<?php if ($this->_vars['b']['where_placed'] == "comments"): ?> selected="selected"<?php endif; ?>>комменты</option>
											<option value="pubs"<?php if ($this->_vars['b']['where_placed'] == "pubs"): ?> selected="selected"<?php endif; ?>>публикации</option>
											<option value="categs"<?php if ($this->_vars['b']['where_placed'] == "categs"): ?> selected="selected"<?php endif; ?>>разделы</option>
											<option value="products"<?php if ($this->_vars['b']['where_placed'] == "products"): ?> selected="selected"<?php endif; ?>>товары</option>
											<option value="photos"<?php if ($this->_vars['b']['where_placed'] == "photos"): ?> selected="selected"<?php endif; ?>>фото</option>
										</optgroup>					
										
										<?php if (! empty ( $this->_vars['site_vars']['spec_blocks']['types'] )): ?>
											<optgroup label="Типы блоков">
											<?php if (count((array)$this->_vars['site_vars']['spec_blocks']['types'])): foreach ((array)$this->_vars['site_vars']['spec_blocks']['types'] as $this->_vars['k'] => $this->_vars['v']): ?>
												<option value="<?php echo $this->_vars['v']['title']; ?>
"<?php if ($this->_vars['b']['where_placed'] == $this->_vars['v']['title']): ?> selected="selected"<?php endif; ?>><?php echo $this->_vars['v']['title_admin']; ?>
</option>
											<?php endforeach; endif; ?>
											</optgroup>
										<?php endif; ?>

										<?php if (! empty ( $this->_vars['site_vars']['spec_blocks']['templates'] )): ?>
											<optgroup label="Шаблоны">
											<?php if (count((array)$this->_vars['site_vars']['spec_blocks']['templates'])): foreach ((array)$this->_vars['site_vars']['spec_blocks']['templates'] as $this->_vars['k'] => $this->_vars['v']): ?>
												<option value="<?php echo $this->_vars['v']['title']; ?>
"<?php if ($this->_vars['b']['where_placed'] == $this->_vars['v']['title']): ?> selected="selected"<?php endif; ?>><?php echo $this->_vars['v']['title']; ?>
</option>
											<?php endforeach; endif; ?>
											</optgroup>
										<?php endif; ?>

									</select>



									
								</td>
								
								<td width="40%">Кол-во в ряд 
									<select name="block_update[qty][<?php echo $this->_vars['b']['id']; ?>
]">
										<option value="0"<?php if (isset ( $this->_vars['b']['qty'] ) && $this->_vars['b']['qty'] == "0"): ?> selected="selected"<?php endif; ?>>0</option>
										<option value="1"<?php if (isset ( $this->_vars['b']['qty'] ) && $this->_vars['b']['qty'] == "1"): ?> selected="selected"<?php endif; ?>>1</option>
										<option value="2"<?php if (isset ( $this->_vars['b']['qty'] ) && $this->_vars['b']['qty'] == "2"): ?> selected="selected"<?php endif; ?>>2</option>
										<option value="3"<?php if (isset ( $this->_vars['b']['qty'] ) && $this->_vars['b']['qty'] == "3"): ?> selected="selected"<?php endif; ?>>3</option>
										<option value="4"<?php if (isset ( $this->_vars['b']['qty'] ) && $this->_vars['b']['qty'] == "4"): ?> selected="selected"<?php endif; ?>>4</option>
										<option value="6"<?php if (isset ( $this->_vars['b']['qty'] ) && $this->_vars['b']['qty'] == "6"): ?> selected="selected"<?php endif; ?>>6</option>
										<option value="12"<?php if (isset ( $this->_vars['b']['qty'] ) && $this->_vars['b']['qty'] == "12"): ?> selected="selected"<?php endif; ?>>12</option>
									</select>
								</td>
								<td>Удалить блок <input type="checkbox" name="delete_block[]" value="<?php echo $this->_vars['b']['id']; ?>
"> </td>
							</tr>
						</table>
						
					</td>
				</tr>


				<tr class="odd">
					<td width="200" align="right">
						Заголовок
						<br><small>(title)</small>
					</td>
					<td>
						<textarea name="block_update[title][<?php echo $this->_vars['b']['id']; ?>
]" rows="2" style="width:100%;"><?php echo $this->_vars['b']['title']; ?>
</textarea>
					</td>
				</tr>

				<tr class="odd">
					<td width="200" align="right">
						Текст
						<br><small>(text)</small>
					</td>
					<td>
						<textarea name="block_update[text][<?php echo $this->_vars['b']['id']; ?>
]" rows="6" style="width:100%;"><?php echo $this->_run_modifier($this->_vars['b']['html'], 'escape', 'plugin', 1); ?>
</textarea>
						<?php if (! isset ( $_GET['noeditor'] )): ?>
						  <script type="text/javascript">
							var editor = CKEDITOR.replace( 'block_update[text][<?php echo $this->_vars['b']['id']; ?>
]');
							editor.config.toolbar = 'Basic2';
							editor.config.autoParagraph = false;
							editor.config.enterMode = CKEDITOR.ENTER_BR;
							editor.config.shiftEnterMode = CKEDITOR.ENTER_P;
							CKFinder.setupCKEditor( editor, '/<?php echo constant('ADMIN_FOLDER'); ?>
/ckfinder//');
						  </script>
						<?php endif; ?>
					</td>
				</tr>

				<tr class="odd">
					<td width="200" align="right">
						(extra)<br><small>Дополнительное поле</small>
					</td>
					<td>
						<textarea name="block_update[extra][<?php echo $this->_vars['b']['id']; ?>
]" rows="3" style="width:100%;"><?php echo $this->_vars['b']['pages']; ?>
</textarea>
					</td>
				</tr>
				<tr class="odd">
					<td width="200" align="right">
						(comment)<br><small>Примечание</small>
					</td>
					<td>
						<textarea name="block_update[comment][<?php echo $this->_vars['b']['id']; ?>
]" rows="3" style="width:100%;"><?php echo $this->_vars['b']['skip_pages']; ?>
</textarea>
					</td>
				</tr>
				
				<tr class="odd">
					<td width="200" align="right">
						Элементы
					</td>
					<td>
						<?php if (! empty ( $this->_vars['b']['list_photos'] )): ?>
							<table class="" width="100%" cellpadding="3" cellspacing="1">
								
								<?php if (count((array)$this->_vars['b']['list_photos'])): foreach ((array)$this->_vars['b']['list_photos'] as $this->_vars['fotokey'] => $this->_vars['foto']): ?>
									<tr>
										<th>#</th>
										<th>Элемент</th>
										<th>Заголовок<br><small>title</small></th>
										<th>Доп.поле<br><small>ext_h1</small></th>
										<th>Примечание<br><small>ext_link</small></th>
										<th>Порядок<br><small>сортировки</small></th>
										<th><i class="fa fa-trash"></i></th>
									</tr>
								
									<tr>
										<td rowspan="2"><?php echo $this->_vars['foto']['1']['id_in_record']; ?>
</td>
										<td rowspan="2" align="center">
											<p style="text-align:center;"><img src="<?php echo $this->_vars['foto']['1']['url']; ?>
" style="max-width:100px; height:auto;"></p>

										<?php if (! empty ( $this->_vars['foto'] )): ?>
											<p><i class="fa fa-external-link"></i> 
											<?php if (count((array)$this->_vars['foto'])): foreach ((array)$this->_vars['foto'] as $this->_vars['k1'] => $this->_vars['f1']): ?>
												<a href="<?php echo $this->_vars['f1']['url']; ?>
" onclick="ImgWin('<?php echo $this->_vars['f1']['url']; ?>
','<?php echo $this->_vars['f1']['width']; ?>
x<?php echo $this->_vars['f1']['height']; ?>
','<?php echo $this->_vars['f1']['width']; ?>
','<?php echo $this->_vars['f1']['height']; ?>
'); return false;" target="_blank"><?php echo $this->_vars['k1']; ?>
</a> 
											<?php endforeach; endif; ?>
											</p>
										<?php endif; ?>
										</td>
										<td>
											<textarea style="width:100%;" name="b_photo[<?php echo $this->_vars['b']['id']; ?>
][<?php echo $this->_vars['foto']['1']['id_in_record']; ?>
][title]" rows="2"><?php echo $this->_vars['foto']['1']['title']; ?>
</textarea>
										</td>
										<td><textarea style="width:100%;" name="b_photo[<?php echo $this->_vars['b']['id']; ?>
][<?php echo $this->_vars['foto']['1']['id_in_record']; ?>
][ext_h1]" rows="2"><?php echo $this->_vars['foto']['1']['ext_h1']; ?>
</textarea></td>
										
										<td><textarea style="width:100%;" name="b_photo[<?php echo $this->_vars['b']['id']; ?>
][<?php echo $this->_vars['foto']['1']['id_in_record']; ?>
][ext_link]" rows="2"><?php echo $this->_vars['foto']['1']['ext_link']; ?>
</textarea></td>
										<td align="center">
											<input type="hidden" name="b_photo[<?php echo $this->_vars['b']['id']; ?>
][<?php echo $this->_vars['foto']['1']['id_in_record']; ?>
][old_in_record]" value="<?php echo $this->_vars['foto']['1']['id_in_record']; ?>
" />
										
											<input type="text" size="4" name="b_photo[<?php echo $this->_vars['b']['id']; ?>
][<?php echo $this->_vars['foto']['1']['id_in_record']; ?>
][id_in_record]" value="<?php echo $this->_vars['foto']['1']['id_in_record']; ?>
" style="background:#cccccc; text-align:center;" />
										</td>
										<td><input type="checkbox" name="del_block_foto[<?php echo $this->_vars['b']['id']; ?>
][]" value="<?php echo $this->_vars['foto']['1']['id_in_record']; ?>
"></td>
									</tr>
									
									<tr>
										<td colspan="5">
											<p><b>Описание</b> <small>(ext_desc)</small></p>
											<textarea style="width:100%;" name="b_photo[<?php echo $this->_vars['b']['id']; ?>
][<?php echo $this->_vars['foto']['1']['id_in_record']; ?>
][ext_desc]" rows="3"><?php echo $this->_vars['foto']['1']['ext_desc']; ?>
</textarea>
										
						<?php if (! isset ( $_GET['noeditor'] )): ?>
						  <script type="text/javascript">
							var editor = CKEDITOR.replace( 'b_photo[<?php echo $this->_vars['b']['id']; ?>
][<?php echo $this->_vars['foto']['1']['id_in_record']; ?>
][ext_desc]');
							editor.config.toolbar = 'Basic2';
							editor.config.autoParagraph = false;
							editor.config.enterMode = CKEDITOR.ENTER_BR;
							editor.config.shiftEnterMode = CKEDITOR.ENTER_P;
							CKFinder.setupCKEditor( editor, '/<?php echo constant('ADMIN_FOLDER'); ?>
/ckfinder/');
						  </script>
						<?php endif; ?>
										
										</td>
									</tr>
									
									
								<?php endforeach; endif; ?>								
							</table>						
						<?php else: ?>
							нет
						<?php endif; ?>
					</td>
				</tr>				
				
				<tr class="odd">
					<td align="right">Добавить фото</td>
					<td>
						<div id="addfrminblock<?php echo $this->_vars['b']['id']; ?>
"><br>
						</div>
						  <div id="bntAddPhotoInBlock<?php echo $this->_vars['b']['id']; ?>
"><input type="submit" onclick="newInputAddInBlock(<?php echo $this->_vars['b']['id']; ?>
);" value="+"></div>
						  
					</td>		
				</tr>
			</table>
				
			</div>
		<?php endforeach; endif; ?>
	<?php endif; ?>
	
	<h4><a href="javascript: ShowHide('block-add')" style="border-bottom: 1px dashed blue;">Добавить блок</a></h4>
	
	<div style="display: none;" id="block-add"> 

		<table border=0 id="upload_blocks" width="100%">
			<tr class="odd">
				<td valign="top" width="100">

					<div id="default_type">
					<i class="fa fa-cog"></i> <a href="?action=settings&do=blocks"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "blocks",'key3' => "type"), $this);?></a><br>(btype)<br>
					
					<select name="block[gruppa][0]" style="width:100%;">
						<option value="manual">-</option>
						
						<optgroup label="Служебные">
							<option value="comments">комменты</option>
							<option value="pubs">публикации</option>
							<option value="categs">разделы</option>
							<option value="products">товары</option>
							<option value="photos">фото</option>
						</optgroup>					
						
						<?php if (! empty ( $this->_vars['site_vars']['spec_blocks']['types'] )): ?>
							<optgroup label="Типы блоков">
							<?php if (count((array)$this->_vars['site_vars']['spec_blocks']['types'])): foreach ((array)$this->_vars['site_vars']['spec_blocks']['types'] as $this->_vars['k'] => $this->_vars['v']): ?>
								<option value="<?php echo $this->_vars['v']['title']; ?>
"><?php echo $this->_vars['v']['title_admin']; ?>
</option>
							<?php endforeach; endif; ?>
							</optgroup>
						<?php endif; ?>

						<?php if (! empty ( $this->_vars['site_vars']['spec_blocks']['templates'] )): ?>
							<optgroup label="Шаблоны">
							<?php if (count((array)$this->_vars['site_vars']['spec_blocks']['templates'])): foreach ((array)$this->_vars['site_vars']['spec_blocks']['templates'] as $this->_vars['k'] => $this->_vars['v']): ?>
								<option value="<?php echo $this->_vars['v']['title']; ?>
"><?php echo $this->_vars['v']['title']; ?>
</option>
							<?php endforeach; endif; ?>
							</optgroup>
						<?php endif; ?>

					</select>
					<div></div>
					</div><br>

					<i class="fa fa-sort"></i> Порядок<br><small>сортировки</small><br>
					<input type="text" name="block[sort][0]" /><br>
					
					<i class="fa fa-eye"></i> Показывать<br><small>(active)</small><br>
					<input type="checkbox" name="block[active][0]" value="1" /><br>
					
					
					Кол-во в ряд <small>(qty)</small><br>
					<select name="block[qty][0]">
						<option value="0">0</option>
						<option value="1" selected="selected">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="6">6</option>
						<option value="12">12</option>
					</select>
				</td>	


				<td valign="top" width="200">
					Заголовок <small>(title)</small><br>
					<textarea name="block[title][0]" rows="2" style="width:100%;"></textarea><br>
					Доп.поле <small>(extra)</small><br>
					<input type="text" style="width:100%;" name="block[extra][0]" value=""><br>
					
					Фото: <div id="addfrm0"></div>
					<div id="bntAddPhoto0"><input type="submit" onclick="newInputAdd(0);" value="+"></div><br>


				</td>
				<td valign="top">
					Описание <small>(text)</small><br>
					<textarea name="block[text][0]" rows="8" style="width:100%;"></textarea>
					<?php if (! isset ( $_GET['noeditor'] )): ?>
						  <script type="text/javascript">
							var editor = CKEDITOR.replace( 'block[text][0]');
							editor.config.toolbar = 'Basic2';
							CKFinder.setupCKEditor( editor, '/adminpro/ckfinder/');
						  </script>
					<?php endif; ?>
				</td>				
			</tr>					
		</table>


<table border="0" cellpadding="3">
	<tr>
		<td><a href="javascript:add_more_blocks();">Добавить</a></td>
	</tr>
</table>
<input type="hidden" name="numpics" id="num_pics_field" value="1" />
</div>

