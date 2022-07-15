<?php ?><table width="100%">
  <tr>
	<td nowrap><a href="?action=info&do=edit_publication&id=0<?php if (! empty ( $_GET['cid'] )): ?>&cid=<?php echo $_GET['cid'];  endif; ?>"><i class="fa fa-plus"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a></td>
  
    <form method="get">
	
    <td align="right" nowrap><i class="fa fa-search" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
;"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "search_in_title"), $this);?>:</td>
    <td nowrap>
          <input type=hidden name="action" value="info" />
          <input type=hidden name="do" value="list_publications" />
          <?php if (isset ( $_GET['cid'] )): ?><input type="hidden" name="cid" value="<?php echo $this->_run_modifier($_GET['cid'], 'htmlspecialchars', 'PHP', 1); ?>
" /><?php endif; ?>
          <input type=text size=10 name="q" value="<?php if (isset ( $_GET['q'] )):  echo $this->_run_modifier($_GET['q'], 'htmlspecialchars', 'PHP', 1);  endif; ?>" />
		  <button type="submit" class="small"><i class="fa fa-search"></i></button></td>
    </form>
    <td align="right" nowrap><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "filter"), $this);?>:</td>
    <form method=get>
    <td nowrap><input type=hidden name="action" value="info" />
           <input type=hidden name="do" value="list_publications" />
           <?php if (isset ( $_GET['q'] )): ?><input type="hidden" name="q" value="<?php echo $this->_run_modifier($_GET['q'], 'htmlspecialchars', 'PHP', 1); ?>
" /><?php endif; ?>
           <?php if ($this->_run_modifier($this->_vars['categs'], 'count', 'PHP', 0) > 0): ?>
		   
				<?php echo '<select style="width: 200px; word-break: break-all;" onChange="if(this.options[this.selectedIndex].value!=\'\'){window.location=this.options[this.selectedIndex].value}else{this.options[selectedIndex=0];}">'; ?>

				
				<option value="?action=info&do=list_publications<?php if (! empty ( $_GET['q'] )): ?>&q=<?php echo $_GET['q'];  endif; ?>">- <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "all_pages"), $this);?></option>
				
				
				
				<?php if (! empty ( $this->_vars['site_vars']['_pages'] )): ?>
					<?php if (count((array)$this->_vars['site_vars']['_pages'])): foreach ((array)$this->_vars['site_vars']['_pages'] as $this->_vars['v']): ?>
						<?php if (! empty ( $this->_vars['v']['pubs'] )): ?>
						<option value="?action=info&do=list_publications&cid=<?php echo $this->_vars['v']['id'];  if (! empty ( $_GET['q'] )): ?>&q=<?php echo $_GET['q'];  endif;  if (! empty ( $_GET['options'] )): ?>&options=1<?php endif; ?>"<?php if (isset ( $_GET['cid'] ) && $_GET['cid'] == $this->_vars['v']['id']): ?> selected="selected"<?php endif; ?>><?php if ($this->_vars['v']['level'] > 1): ?>
							<?php for($for1 = 1; ((1 < $this->_vars['v']['level']) ? ($for1 < $this->_vars['v']['level']) : ($for1 > $this->_vars['v']['level'])); $for1 += ((1 < $this->_vars['v']['level']) ? 1 : -1)):  $this->assign('current', $for1); ?> - <?php endfor; ?>
						<?php endif;  echo $this->_vars['v']['title']; ?>
 (<?php echo $this->_vars['v']['pubs']; ?>
)</option>
						<?php elseif (! empty ( $this->_vars['v']['subcategs'] )): ?>
						<optgroup label="<?php if ($this->_vars['v']['level'] > 1): ?>
							<?php for($for1 = 1; ((1 < $this->_vars['v']['level']) ? ($for1 < $this->_vars['v']['level']) : ($for1 > $this->_vars['v']['level'])); $for1 += ((1 < $this->_vars['v']['level']) ? 1 : -1)):  $this->assign('current', $for1); ?> - <?php endfor; ?>
						<?php endif;  echo $this->_vars['v']['title']; ?>
"></optgroup>
						<?php endif; ?>
					<?php endforeach; endif; ?>
				<?php endif; ?>	
				</select>
		   
    		    <?php else: ?>               
    		      <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "pages_not_found"), $this);?>
    		    <?php endif; ?>
            
			<?php if (isset ( $_GET['cid'] ) && empty ( $_GET['options'] )): ?>
				<a href="?action=info&do=list_publications&cid=<?php echo $_GET['cid']; ?>
&options=1"><i class="fa fa-bars" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "edit_options"), $this);?>"></i></a>
			<?php elseif (! empty ( $_GET['options'] )): ?>
				<a href="?action=info&do=list_publications&cid=<?php echo $_GET['cid']; ?>
"><i class="fa fa-list" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "block_types",'key3' => "listPubs"), $this);?>"></i></a>
			<?php endif; ?>
	</td>
    </form>
  </tr>
</table>