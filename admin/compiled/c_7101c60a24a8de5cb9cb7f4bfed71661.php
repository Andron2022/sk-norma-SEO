<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.math.php'); $this->register_function("math", "tpl_function_math");  ?>  <h4><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "uploaded_pics"), $this);?> (<?php echo $this->_vars['categ']['fotos_qty']; ?>
)</h4>

<table width="100%">
  <tbody>
    <tr>
      <th width="50"><i class="fa fa-home" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "main_img"), $this);?>"></i></th>
      <th width="150"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "photo"), $this);?></th>
      <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "name"), $this);?></th>
      <th width="80"><i class="fa fa-sort" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "status",'key3' => "sort"), $this);?>"></i></th>
      <th align="center" width="50"><i class="fa fa-cut" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "do_new_foto"), $this);?>"></i> <input type="checkbox" onclick="CheckAll(this,'resize_again[]'); CheckAll(this,'resize_again_group[]');"></th>      
      <th align="center" width="50"><i class="fa fa-trash-o" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "delete"), $this);?>"></i> <input type="checkbox" onclick="CheckAll(this,'delete_pics[]'); CheckAll(this,'delete_group[]');"></th>
      
    </tr>
</table>
    <?php $this->assign('current', 0); ?>
    <?php if (count((array)$this->_vars['categ']['uploaded_fotos'])): foreach ((array)$this->_vars['categ']['uploaded_fotos'] as $this->_vars['key'] => $this->_vars['img']): ?>
      <?php if ($this->_vars['current'] != $this->_vars['img']['id_in_record']): ?>
        <?php $this->assign('current', $this->_vars['img']['id_in_record']); ?>
        <?php echo tpl_function_math(array('equation' => "( x - 1 )",'x' => $this->_vars['current'],'assign' => "previous"), $this);?>
        <?php if ($this->_vars['previous'] == 0):  $this->assign('previous', $this->_vars['categ']['fotos_qty']);  endif; ?>
        <?php echo tpl_function_math(array('equation' => "( x + 1 )",'x' => $this->_vars['current'],'assign' => "next"), $this);?>
        <?php if ($this->_vars['next'] > $this->_vars['categ']['fotos_qty']):  $this->assign('next', 1);  endif; ?>
        <?php echo tpl_function_math(array('equation' => "( x + 10 )",'x' => $this->_vars['key'],'assign' => "stop_key"), $this);?>

                  
        <table id="img<?php echo $this->_vars['img']['id_in_record']; ?>
" width="100%" class="">
          <tr class="odd">
          
          
          <td align="center" width="50"><input name="default_pic" type="radio" value="<?php echo $this->_vars['current']; ?>
"<?php if ($this->_vars['img']['is_default'] == 1): ?>checked="checked" <?php endif; ?> /></td>
          <td width="150" align="center">
            <a href="../upload/records/<?php echo $this->_vars['img']['id']; ?>
.<?php echo $this->_vars['img']['ext']; ?>
" onclick="ImgWin('../upload/records/<?php echo $this->_vars['img']['id']; ?>
.<?php echo $this->_vars['img']['ext']; ?>
','<?php echo $this->_vars['img']['id_in_record']; ?>
','<?php echo $this->_vars['img']['width']; ?>
','<?php echo $this->_vars['img']['height']; ?>
'); return false;" target="_blank"><img src="/upload/records/<?php echo $this->_vars['img']['id']; ?>
.<?php echo $this->_vars['img']['ext']; ?>
" <?php if ($this->_vars['img']['width'] > 150): ?>width="140" <?php endif; ?>border="0" /></a>
          </td>
          <td>          
            <input type="text" name="update_pics_title[<?php echo $this->_vars['img']['id_in_record']; ?>
]" style="width: 100%;" value="<?php echo $this->_run_modifier($this->_vars['img']['title'], 'htmlspecialchars', 'PHP', 1); ?>
">
			
			<a href="javascript: ShowHide('block-<?php echo $this->_vars['img']['id_in_record']; ?>
')" style="border-bottom: 1px dashed blue;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "extra"), $this);?></a>
					
						<div style="display: none;" id="block-<?php echo $this->_vars['img']['id_in_record']; ?>
">
							<table cellpadding="0" cellspacing="1" width="100%">
								<tbody>
									<tr>
										<td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fav",'key3' => "title"), $this);?> (ext_h1): </td>
										<td><textarea name="img_ext_h1[<?php echo $this->_vars['img']['id_in_record']; ?>
]" rows="3" style="width:100%;"><?php echo $this->_vars['img']['ext_h1']; ?>
</textarea></td>
									</tr>
									<tr>
										<td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "desc"), $this);?> (ext_desc): </td>
										<td><textarea name="img_ext_desc[<?php echo $this->_vars['img']['id_in_record']; ?>
]" rows="3" style="width:100%;"><?php echo $this->_vars['img']['ext_desc']; ?>
</textarea></td>
									</tr>
									<tr>
										<td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "extra_desc"), $this);?> (ext_link): </td>
										<td><textarea name="img_ext_link[<?php echo $this->_vars['img']['id_in_record']; ?>
]" rows="3" style="width:100%;"><?php echo $this->_vars['img']['ext_link']; ?>
</textarea></td>
									</tr>
								</tbody>
							</table>
						</div>
			
            <p>
            <?php for($for1 = $this->_vars['key']; (($this->_vars['key'] < $this->_vars['stop_key']) ? ($for1 < $this->_vars['stop_key']) : ($for1 > $this->_vars['stop_key'])); $for1 += (($this->_vars['key'] < $this->_vars['stop_key']) ? 1 : -1)):  $this->assign('nextcurrent', $for1); ?>
              <?php if (isset ( $this->_vars['categ']['uploaded_fotos'][$this->_vars['nextcurrent']]['width'] ) && $this->_vars['current'] == $this->_vars['categ']['uploaded_fotos'][$this->_vars['nextcurrent']]['id_in_record']): ?>
              <a href="../upload/records/<?php echo $this->_vars['categ']['uploaded_fotos'][$this->_vars['nextcurrent']]['id']; ?>
.<?php echo $this->_vars['categ']['uploaded_fotos'][$this->_vars['nextcurrent']]['ext']; ?>
" onclick="ImgWin('../upload/records/<?php echo $this->_vars['categ']['uploaded_fotos'][$this->_vars['nextcurrent']]['id']; ?>
.<?php echo $this->_vars['categ']['uploaded_fotos'][$this->_vars['nextcurrent']]['ext']; ?>
','<?php echo $this->_vars['categ']['uploaded_fotos'][$this->_vars['nextcurrent']]['id_in_record']; ?>
','<?php echo $this->_vars['categ']['uploaded_fotos'][$this->_vars['nextcurrent']]['width']; ?>
','<?php echo $this->_vars['categ']['uploaded_fotos'][$this->_vars['nextcurrent']]['height']; ?>
'); return false;" target="_blank" style="margin-right:20px; white-space: nowrap;"><small><i class="fa fa-external-link"></i>  <?php echo $this->_vars['categ']['uploaded_fotos'][$this->_vars['nextcurrent']]['width']; ?>
*<?php echo $this->_vars['categ']['uploaded_fotos'][$this->_vars['nextcurrent']]['height']; ?>
</small></a> 
              <?php endif; ?>
            <?php endfor; ?>
            </p>
          </td>
          
          <td align="center" width="80" nowrap><a href="javascript:" onclick="MoveUp(this);"><i class="fa fa-chevron-up"></i></a>&nbsp;<span id="img<?php echo $this->_vars['img']['id_in_record']; ?>
_position_text"><?php echo $this->_vars['img']['id_in_record']; ?>
</span>&nbsp;<a href="javascript:" onclick="MoveDown(this);"><i class="fa fa-chevron-down"></i></a></td>
          <input type="hidden" name="img<?php echo $this->_vars['img']['id_in_record']; ?>
_position" id="img<?php echo $this->_vars['img']['id_in_record']; ?>
_position" value="<?php echo $this->_vars['img']['id_in_record']; ?>
">
          <td align="center" width="50"><input type="checkbox" name="resize_again[]" value="<?php echo $this->_vars['img']['id_in_record']; ?>
"></td>

          <td align="center" width="50"><input type="checkbox" name="delete_pics[]" value="<?php echo $this->_vars['img']['id_in_record']; ?>
"></td>
          </tr>
        </table>
          
          
      <?php endif; ?>
    <?php endforeach; endif; ?>