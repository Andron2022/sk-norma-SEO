<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

</td>
<td valign="top">

<h1 class="mt-0 mb-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index_page"), $this);?></h1>

<table border="0" width="80%">
	<tr><td valign="top">	
		<p><small><a href="?action=settings&do=site_vars&site_id=-1&q=sys_admin_intro&redirect=1"><i class="fa fa-edit"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index_page_text"), $this);?></a></small></p>
<?php if (! empty ( $this->_vars['site_vars']['sys_admin_intro'] )): ?>
	<table class="bordered" width="100%">
        <tr><td class="p-20"><?php echo $this->_vars['site_vars']['sys_admin_intro']; ?>
</td></tr>
    </table>
	
<?php endif; ?>
	</td></tr>
	
	<?php if (! empty ( $this->_vars['fav'] )): ?>
	<tr><td valign="top">	
		<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/fav.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
	</td></tr>	
	<?php endif; ?>

	<tr><td valign="top">
   
<?php if ($this->_vars['admin_vars']['bo_user']['prava']['settings'] == 1): ?>
<p class="center"><a href="?action=settings"><i class="fa fa-wrench" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i> <b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "settings"), $this);?></b></a>: 

    <a href="?action=settings&do=site"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "websites"), $this);?> (<?php echo $this->_vars['site_qty']; ?>
)</a>  
    <a href="?action=settings&do=site_vars"><i class="fa fa-cogs" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
; padding-left:10px;"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "title"), $this);?></a> 
	<a href="?action=settings&do=users"><i class="fa fa-users" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
; padding-left:10px;"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "admins"), $this);?> (<?php echo $this->_vars['admins_qty']; ?>
)</a>
	</p>
<?php endif; ?>	



<?php if (! empty ( $this->_vars['site_vars']['_pages'] )): ?>
				<p style="text-align:center;"><b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "filter"), $this);?></b> 
				<input type="hidden" name="action" value="info" />
				<input type="hidden" name="do" value="tree_categs" />
				<select style="width: 200px; word-break: break-all;" name="" onchange="top.location=this.value">
					<option value="?"<?php if (! isset ( $_GET['id'] )): ?> selected="selected"<?php endif; ?>> - <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "all_pages"), $this);?></option>
					
					<?php if (count((array)$this->_vars['site_vars']['_pages'])): foreach ((array)$this->_vars['site_vars']['_pages'] as $this->_vars['v']): ?>
						<?php if (! empty ( $this->_vars['v']['subcategs'] )): ?>
						<option value="?id=<?php echo $this->_vars['v']['id']; ?>
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


        <?php if (! empty ( $this->_vars['AdminCategsTree'] )): ?>
          <p>
          <table width="100%">
            <tr>
              <th width="5%">ID</th>
			  <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "categs"), $this);?></th>
              <th width="5%"><i class="fa fa-check" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "show_on_website"), $this);?>"></i></th>
              <th width="5%"><i class="fa fa-edit" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "edit"), $this);?>"></i></th>
			  <th width="5%"><i class="fa fa-folder" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "pages"), $this);?>"></i></th>
              <th width="5%"><i class="fa fa-external-link" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "link_to_website"), $this);?>"></i></th>              
              <th width="10%"><i class="fa fa-copy" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "publications"), $this);?>"></i><br><?php echo $this->_vars['pubs_qty']; ?>
</th>
			  <?php if ($this->_vars['admin_vars']['shop'] == 1): ?><th width="10%"><i class="fa fa-shopping-cart" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "offers_and_objects"), $this);?>"></i><br><?php echo $this->_vars['products_qty']; ?>
</th><?php endif; ?>
            </tr>
            
            <?php if (count((array)$this->_vars['AdminCategsTree'])): foreach ((array)$this->_vars['AdminCategsTree'] as $this->_vars['row']): ?>
              <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
				<td><a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['row']['id']; ?>
"><?php echo $this->_vars['row']['id']; ?>
</a></td>
				<td><?php if ($this->_vars['row']['level'] < 0): ?>
				<i class="fa fa-exclamation-circle" style="color:red;" title="Wrong page connection"></i> 
				<?php endif; ?><span style="padding-left:<?php echo $this->_vars['row']['padding']; ?>
px;"></span><?php if ($this->_vars['row']['site_id'] > 0): ?><a href="?action=settings&do=site&mode=edit&id=<?php echo $this->_vars['row']['site_id']; ?>
"><i class="fa fa-home" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "website_homepage"), $this);?>"></i></a><?php endif; ?> <a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['row']['id']; ?>
"><?php echo $this->_vars['row']['title']; ?>
</a><?php if (! empty ( $this->_vars['row']['starred'] )): ?>*<?php endif; ?>
				<?php if ($this->_run_modifier($this->_vars['row']['date_insert'], 'strtotime', 'PHP', 1) > time()): ?>
					<i class="fa fa-clock-o red" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "not_shown_yet"), $this);?>"></i>
				<?php endif; ?>
				</td>
                <td class="center"><a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['row']['id']; ?>
"><?php if ($this->_vars['row']['visible'] == 1): ?><i class="fa fa-check"></i><?php else: ?><i class="fa fa-minus"></i><?php endif; ?></a></td>
                <td class="center"><a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['row']['id']; ?>
"><i class="fa fa-pencil"></i></a></td>
				<td class="center"><?php if (! empty ( $this->_vars['row']['subcategs'] )): ?><a href="?action=info&do=tree_categs&id=<?php echo $this->_vars['row']['id']; ?>
"><?php echo $this->_vars['row']['subcategs']; ?>
</a><?php endif; ?></td>
                <td class="center">
				<?php if (! empty ( $this->_vars['row']['site_url_qty'] ) && $this->_vars['row']['site_url_qty'] > 1): ?><span class="red">*</span><?php elseif (! empty ( $this->_vars['row']['site_url_qty'] )): ?>
					<?php if ($this->_vars['row']['site_id'] > 0): ?>
					<small><a href="<?php echo $this->_vars['row']['site_url']; ?>
" target="_blank"><i class="fa fa-external-link"></i></a></small>
					<?php elseif (! empty ( $this->_vars['row']['url'] )): ?><small><a href="<?php echo $this->_vars['row']['url']; ?>
" target="_blank"><i class="fa fa-external-link"></i></a></small><?php endif; ?>
				<?php endif; ?></td>
                <td class="center"><?php if ($this->_vars['row']['pubs'] > 0): ?><a href="?action=info&do=list_publications&cid=<?php echo $this->_vars['row']['id']; ?>
"><?php echo $this->_vars['row']['pubs']; ?>
</a><?php endif; ?> <a href="?action=info&do=edit_publication&id=0&cid=<?php echo $this->_vars['row']['id']; ?>
"><i class="fa fa-plus" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i></a></td>
				<?php if ($this->_vars['admin_vars']['shop'] == 1): ?><td class="center"><?php if ($this->_vars['row']['products'] > 0): ?><a href="?action=products&do=list_products&cid=<?php echo $this->_vars['row']['id']; ?>
"><?php echo $this->_vars['row']['products']; ?>
</a> <a href="?action=products&do=add&cid=<?php echo $this->_vars['row']['id']; ?>
"><i class="fa fa-plus" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i></a><?php elseif ($this->_vars['row']['shop'] == 1): ?><a href="?action=products&do=add&cid=<?php echo $this->_vars['row']['id']; ?>
"><i class="fa fa-plus" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i></a><?php else:  endif; ?></td><?php endif; ?>
              </tr>
            <?php endforeach; endif; ?>
          </table>
          </p>
          <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "total_pages"), $this);?>: <?php echo $this->_run_modifier($this->_vars['AdminCategsTree'], 'count', 'PHP', 0); ?>
<br>
          * - <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "starred_pages"), $this);?>
        <?php else: ?>
          <a href="?action=info&do=edit_categ&id=0"><i class="fa fa-plus-circle"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "add_categ"), $this);?></a>
        <?php endif; ?>

    </td></tr>
</table>
    
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>