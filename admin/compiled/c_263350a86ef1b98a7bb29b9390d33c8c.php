<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "info_manage"), $this);?></h1>

<table width="80%" border="0" cellpadding="6">
	<tr>
		<td valign="top">
			<blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "info_help"), $this);?></blockquote>
		</td>
	</tr>
	
	<tr>
		<td valign="top">
			<h3><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "structure"), $this); if (! empty ( $this->_vars['categ_info']['title'] )): ?> / <?php echo $this->_vars['categ_info']['title']; ?>
 <a href="?action=info<?php if ($this->_vars['categ_info']['id_parent'] > 0): ?>&id=<?php echo $this->_vars['categ_info']['id_parent'];  endif; ?>"><i class="fa fa-arrow-left"></i></a> <a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['categ_info']['id']; ?>
"><i class="fa fa-pencil"></i></a> <a href="?action=info&do=categories&id=<?php echo $this->_vars['categ_info']['id']; ?>
"><i class="fa fa-bars"></i></a><?php endif; ?></h3>
			<ul>
				<li><a href="?action=info&do=edit_categ&id=0<?php if (! empty ( $_GET['id'] )): ?>&cid=<?php echo $_GET['id'];  endif; ?>"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "add_categ"), $this);?> <i class="fa fa-plus-circle" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i></a></li>
			
			<?php if (! empty ( $this->_vars['categs_tree'] ) && $this->_vars['qty_categs'] > 0): ?>
				<li><a href="?action=info&do=categories"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "total_pages"), $this);?>: <?php if ($this->_run_modifier($this->_vars['categs_tree'], 'count', 'PHP', 0) < $this->_vars['qty_categs']):  echo $this->_run_modifier($this->_vars['categs_tree'], 'count', 'PHP', 0); ?>
 <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "from"), $this);?> <?php endif; ?> <?php echo $this->_vars['qty_categs']; ?>
</a></li>
			<?php endif; ?>
			</ul>
			<?php if (! empty ( $this->_vars['categs_tree'] )): ?>
			
			<?php if (! empty ( $this->_vars['site_vars']['_pages'] )): ?>
				<p><b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "filter"), $this);?></b> 
				<input type="hidden" name="action" value="info" />
				<input type="hidden" name="do" value="tree_categs" />
				<select style="width: 200px; word-break: break-all;" name="" onchange="top.location=this.value">
					<option value="?action=info"<?php if (! isset ( $_GET['id'] )): ?> selected="selected"<?php endif; ?>> - <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "all_pages"), $this);?></option>
					
					<?php if (count((array)$this->_vars['site_vars']['_pages'])): foreach ((array)$this->_vars['site_vars']['_pages'] as $this->_vars['v']): ?>
						<?php if (! empty ( $this->_vars['v']['subcategs'] )): ?>
						<option value="?action=info&do=tree_categs&id=<?php echo $this->_vars['v']['id']; ?>
"<?php if ($this->_vars['admin_vars']['uri']['id'] == $this->_vars['v']['id']): ?> selected="selected"<?php endif; ?>><?php if ($this->_vars['v']['level'] > 1): ?>
							<?php for($for1 = 1; ((1 < $this->_vars['v']['level']) ? ($for1 < $this->_vars['v']['level']) : ($for1 > $this->_vars['v']['level'])); $for1 += ((1 < $this->_vars['v']['level']) ? 1 : -1)):  $this->assign('current', $for1); ?> - <?php endfor; ?>
						<?php endif;  echo $this->_vars['v']['title']; ?>
</option>
						<?php endif; ?>
					<?php endforeach; endif; ?>
				
				</select>
				</form>     
				</p>
			<?php endif; ?>
		</td>
	</tr>

	<tr>
		<td valign="top">
        <?php if ($this->_run_modifier($this->_vars['categs_tree'], 'count', 'PHP', 0) > 0): ?>
          <table width="100%">
            <tr>
              <th width="5%">ID</th>
              <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fav",'key3' => "title"), $this);?></th>
              <th width="5%"><i class="fa fa-check" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "show_on_website"), $this);?>"></i></th>
              <th width="5%"><i class="fa fa-edit" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "edit"), $this);?>"></i></th>
              <th width="5%"><i class="fa fa-external-link" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "link_to_website"), $this);?>"></i></th>              
              <th width="10%"><i class="fa fa-copy" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "publications"), $this);?>"></i></th>
			  <th width="10%"><i class="fa fa-shopping-cart" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "offers_and_objects"), $this);?>"></i></th>
            </tr>
            
            <?php if (count((array)$this->_vars['categs_tree'])): foreach ((array)$this->_vars['categs_tree'] as $this->_vars['row']): ?>
              <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
				<td class="center"><a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['row']['id']; ?>
"><?php echo $this->_vars['row']['id']; ?>
</a></td>
                <td><?php if ($this->_vars['row']['level'] < 0): ?>
				<i class="fa fa-exclamation-circle" style="color:red;" title="Wrong page connection"></i> 
				<?php endif; ?><span style="padding-left:<?php echo $this->_vars['row']['padding']; ?>
px;"></span><?php if ($this->_vars['row']['site_id'] > 0): ?><a href="?action=settings&do=site&mode=edit&id=<?php echo $this->_vars['row']['site_id']; ?>
"><i class="fa fa-home" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "website_homepage"), $this);?>"></i></a><?php endif; ?> <a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['row']['id']; ?>
"><?php echo $this->_vars['row']['title']; ?>
</a><?php if (! empty ( $this->_vars['row']['starred'] )): ?>*<?php endif; ?>
				
				<?php if ($this->_vars['row']['subcategs'] > 0): ?> 
					<a href="?action=info&id=<?php echo $this->_vars['row']['id']; ?>
"><i class="fa fa-sitemap"></i></a>
					<a href="?action=info&do=categories&id=<?php echo $this->_vars['row']['id']; ?>
"><i class="fa fa-bars"></i></a>
				<?php endif; ?>
				
				<?php if ($this->_run_modifier($this->_vars['row']['date_insert'], 'strtotime', 'PHP', 1) > time()): ?>
					<i class="fa fa-clock-o red" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "not_shown_yet"), $this);?>"></i>
				<?php endif; ?>
				
				</td>
                <td align="center"><a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['row']['id']; ?>
"><?php if ($this->_vars['row']['visible'] == 1): ?><i class="fa fa-check"></i><?php else: ?><i class="fa fa-minus"></i><?php endif; ?></a></td>
                <td align="center"><a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['row']['id']; ?>
"><i class="fa fa-pencil"></i></a></td>
                <td align="center"><?php if (! empty ( $this->_vars['row']['site_url_qty'] ) && $this->_vars['row']['site_url_qty'] > 1): ?><span class="red">*</span><?php elseif (! empty ( $this->_vars['row']['site_url_qty'] )): ?>
					<?php if ($this->_vars['row']['site_id'] > 0): ?>
					<small><a href="<?php echo $this->_vars['row']['site_url']; ?>
" target="_blank"><i class="fa fa-external-link"></i></a></small>
					<?php elseif (! empty ( $this->_vars['row']['url'] )): ?><small><a href="<?php echo $this->_vars['row']['url']; ?>
" target="_blank"><i class="fa fa-external-link"></i></a></small><?php endif; ?>
				<?php endif; ?></td>
                				
				<td align="center"><?php if ($this->_vars['row']['pubs'] > 0): ?><a href="?action=info&do=list_publications&cid=<?php echo $this->_vars['row']['id']; ?>
"><?php echo $this->_vars['row']['pubs']; ?>
</a><?php endif; ?> <a href="?action=info&do=edit_publication&id=0&cid=<?php echo $this->_vars['row']['id']; ?>
"><i class="fa fa-plus" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i></a></td>
				<td align="center"><?php if ($this->_vars['row']['products'] > 0): ?><a href="?action=products&do=list_products&cid=<?php echo $this->_vars['row']['id']; ?>
"><?php echo $this->_vars['row']['products']; ?>
</a><?php endif; ?> <?php if ($this->_vars['row']['shop'] == 1): ?><a href="?action=products&do=add&cid=<?php echo $this->_vars['row']['id']; ?>
"><i class="fa fa-plus" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i></a><?php endif; ?></td>
                
              </tr>
            <?php endforeach; endif; ?>
          </table>
        
        <?php endif; ?>
      <?php else: ?>
        <p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "no_pages"), $this);?></p>
      <?php endif; ?>
	  
       </td>
	</tr>

	<tr>
		<td valign="top">
      <h3><?php if ($this->_vars['qty_pubs'] > 10):  echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "last_10_pubs"), $this); else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "block_types",'key3' => "lastPubs"), $this); endif; ?></h3>
	  <ul>
	  <?php if (! empty ( $this->_vars['categs_tree'] ) || $this->_vars['admin_vars']['uri']['id'] > 0): ?> 
        <li><a href="?action=info&do=edit_publication&id=0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add_pub"), $this);?> <i class="fa fa-plus-circle" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i></a></li>
      <?php endif; ?>
	  
	  <?php if (! empty ( $this->_vars['last_pubs'] ) && ! empty ( $this->_vars['qty_pubs'] )): ?>
		<li><a href="?action=info&do=list_publications"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "total_views"), $this);?>: <?php echo $this->_vars['qty_pubs']; ?>
</a></li>
	  <?php endif; ?>
	  
	  </ul>
      <?php if ($this->_run_modifier($this->_vars['last_pubs'], 'count', 'PHP', 0) > 0): ?>
       
        <table width="100%">
			<tr>
				<th width="5%">ID</th>
				<th width="10%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "date"), $this);?></th>
				<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fav",'key3' => "title"), $this);?></th>
				<th width="5%"><i class="fa fa-edit" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "edit"), $this);?>"></i></th>
				<th width="5%"><i class="fa fa-external-link" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "link_to_website"), $this);?>"></i></th>
			</tr>
          <?php if (count((array)$this->_vars['last_pubs'])): foreach ((array)$this->_vars['last_pubs'] as $this->_vars['value']): ?>
            <tr <?php echo tpl_function_cycle(array('values' => "class=odd, ",'reset' => "1"), $this);?>>
              <td class="center"><a href="?action=info&do=edit_publication&id=<?php echo $this->_vars['value']['id']; ?>
"><small><?php echo $this->_vars['value']['id']; ?>
</small></a></td>
			  <td class="center"><a href="?action=info&do=edit_publication&id=<?php echo $this->_vars['value']['id']; ?>
"><small><?php echo $this->_run_modifier($this->_vars['value']['date_insert'], 'date', 'plugin', 1, "d.m.Y"); ?>
</small></a></td>
              <td><a href="?action=info&do=edit_publication&id=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_vars['value']['name']; ?>
</a>
			  <?php if ($this->_run_modifier($this->_vars['value']['date_insert'], 'strtotime', 'PHP', 1) > time()): ?>
					<i class="fa fa-clock-o red" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "not_shown_yet"), $this);?>"></i>
				<?php endif; ?>
			  </td>
              <td class="center"><a href="?action=info&do=edit_publication&id=<?php echo $this->_vars['value']['id']; ?>
"><i class="fa fa-pencil"></i></a></td>
              <td class="center"><?php if (! empty ( $this->_vars['value']['site_url'] )): ?>			  
				<small><a href="<?php echo $this->_vars['value']['site_url']; ?>
/<?php echo $this->_vars['value']['alias']; ?>
/<?php if (empty ( $this->_vars['value']['active'] )): ?>?debug=1<?php endif; ?>" target="_blank"><i class="fa fa-external-link"></i></a></small>
			  <?php endif; ?></td>
            </tr>     
          <?php endforeach; endif; ?>
        </table>
      <?php else: ?>
        <p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "no_pubs"), $this);?></p>
      <?php endif; ?>
      
        </td>
	</tr>

	</table>
    


<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>