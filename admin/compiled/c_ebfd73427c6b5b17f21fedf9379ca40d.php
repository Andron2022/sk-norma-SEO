<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "block_types",'key3' => "listCategs"), $this);?>: 
  <?php if (isset ( $_GET['shop'] ) && $_GET['shop'] == 0): ?>
<span style="padding-left: 10px; margin-left: 10px;"><i class="fa fa-copy" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "for_pubs"), $this);?>"></i></a>
<a style="padding-left: 5px; margin-left: 5px;" href="?action=info&do=categories&shop=1"><i class="fa fa-shopping-cart" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "for_shop"), $this);?>"></i></span>
<a style="padding-left: 5px; margin-left: 5px;" href="?action=info&do=categories"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "all"), $this);?></a>
  <?php elseif (isset ( $_GET['shop'] ) && $_GET['shop'] == 1): ?>
<a style="padding-left: 10px; margin-left: 10px;" href="?action=info&do=categories&shop=0"><i class="fa fa-copy" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "for_pubs"), $this);?>"></i></a>
<span style="padding-left: 5px; margin-left: 5px;"><i class="fa fa-shopping-cart" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "for_shop"), $this);?>"></i></span>
<a style="padding-left: 5px; margin-left: 5px;" href="?action=info&do=categories"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "all"), $this);?></a>
  <?php else: ?>
<a style="padding-left: 10px; margin-left: 10px;" href="?action=info&do=categories&shop=0"><i class="fa fa-copy" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "for_pubs"), $this);?>"></i></a>
<a style="padding-left: 5px; margin-left: 5px;" href="?action=info&do=categories&shop=1"><i class="fa fa-shopping-cart" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "for_shop"), $this);?>"></i></a>
<span style="padding-left: 5px; margin-left: 5px;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "all"), $this);?></span>
  <?php endif; ?>
</h1>

<?php if (! empty ( $this->_vars['categ_info']['title'] )): ?> 
	<h2><?php echo $this->_vars['categ_info']['title']; ?>
 <a href="?action=info&do=categories<?php if ($this->_vars['categ_info']['id_parent'] > 0): ?>&id=<?php echo $this->_vars['categ_info']['id_parent'];  endif; ?>"><i class="fa fa-arrow-left"></i></a> <a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['categ_info']['id']; ?>
"><i class="fa fa-pencil"></i></a> <a href="?action=info&id=<?php echo $this->_vars['categ_info']['id']; ?>
"><i class="fa fa-sitemap"></i></a></h2>
<?php endif; ?>


<?php if (isset ( $_GET['updated'] )): ?>
  <table width="80%"><tr><td><blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "rows_updated"), $this);?>: <?php echo $_GET['updated']; ?>
</blockquote></td></tr></table>
<?php endif; ?>

<?php if (isset ( $_GET['deleted'] )): ?>
  <table width="80%"><tr><td><blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "rows_deleted"), $this);?>: <?php echo $_GET['deleted']; ?>
</blockquote></td></tr></table>
<?php endif; ?>


<table width="80%">
	<tr>
		<td><a href="?action=info&do=edit_categ&id=0"><i class="fa fa-plus"></i></a> <a href="?action=info&do=edit_categ&id=0<?php if (! empty ( $_GET['id'] )): ?>&cid=<?php echo $_GET['id'];  endif; ?>"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a></td>
		<form method="get">
		<td>
			<?php if (! empty ( $this->_vars['site_vars']['_pages'] )): ?>
				<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "filter"), $this);?>: <?php echo '<select style="width: 200px; word-break: break-all;" onChange="if(this.options[this.selectedIndex].value!=\'\'){window.location=this.options[this.selectedIndex].value}else{this.options[selectedIndex=0];}">'; ?>

  
				<option value="?action=info&do=categories"<?php if ($this->_vars['id'] == 0 && ! isset ( $_GET['id'] )): ?> selected="selected"<?php endif; ?>>- <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "all",'case' => "lower"), $this);?></option>
				<option value="?action=info&do=categories&id=0"<?php if ($this->_vars['id'] == 0 && isset ( $_GET['id'] )): ?> selected="selected"<?php endif; ?>>- <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "home",'case' => "lower"), $this);?></option>				
				
				<?php if (count((array)$this->_vars['site_vars']['_pages'])): foreach ((array)$this->_vars['site_vars']['_pages'] as $this->_vars['v']): ?>
					<?php if (! empty ( $this->_vars['v']['subcategs'] )): ?>
					<option value="?action=info&do=categories&id=<?php echo $this->_vars['v']['id']; ?>
"<?php if ($this->_vars['id'] == $this->_vars['v']['id']): ?> selected="selected"<?php endif; ?>><?php if ($this->_vars['v']['level'] > 1): ?>
						<?php for($for1 = 1; ((1 < $this->_vars['v']['level']) ? ($for1 < $this->_vars['v']['level']) : ($for1 > $this->_vars['v']['level'])); $for1 += ((1 < $this->_vars['v']['level']) ? 1 : -1)):  $this->assign('current', $for1); ?> - <?php endfor; ?>
					<?php endif;  echo $this->_vars['v']['title']; ?>
</option>
					<?php endif; ?>
				<?php endforeach; endif; ?>
				</select>
			<?php endif; ?>
		</td>
		</form>
	</tr>
</table>


<?php if ($this->_run_modifier($this->_vars['categs_list'], 'count', 'PHP', 0) > 3):  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/pages.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
  endif; ?>


<?php if ($this->_run_modifier($this->_vars['categs_list'], 'count', 'PHP', 0) > 0): ?>
<form method=post name=form1>
<?php if (isset ( $_GET['id'] )): ?><input type="hidden" name="id" value="<?php echo $_GET['id']; ?>
"><?php endif; ?>
<table width="80%">
  <tr>
    <th width="40">#</th>
    <th><i class="fa fa-edit"></i></th>
    <th width="50"><i class="fa fa-check"></i> <INPUT onclick="CheckAll(this,'active[]')" type="checkbox" /></th>
    <th width="30%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fav",'key3' => "title"), $this);?></th>
    <th width="20%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "url"), $this);?></th>
    <th><i class="fa fa-sort"></i></th>
    <th><i class="fa fa-files-o" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "publications"), $this);?>"></i></th>
    <th><i class="fa fa-plus" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add_pub"), $this);?>"></i></th>
    <th><i class="fa fa-shopping-cart" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "offers"), $this);?>"></i></th>
    <th><i class="fa fa-plus" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add_product"), $this);?>"></i></th>
    <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "websites"), $this);?></font></b></th>
    <th width="50"><i class="fa fa-trash-o"></i> <INPUT onclick="CheckAll(this,'delcategs[]')" type="checkbox" /></th>
  </tr>
	
  <?php if (count((array)$this->_vars['categs_list'])): foreach ((array)$this->_vars['categs_list'] as $this->_vars['key'] => $this->_vars['value']): ?>
    <tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
      <td align="center"><a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_vars['value']['id']; ?>
</a></td>
      <td align="center" nowrap><a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['value']['id']; ?>
"><i class="fa fa-pencil"></i></a>
	  <?php if ($this->_vars['value']['id'] == $this->_vars['value']['default_id_categ']): ?>
	  <small><a href="?action=info&do=categories&id=<?php echo $this->_vars['value']['id']; ?>
"><i class="fa fa-home"></i></a></small>
	  <?php elseif (! empty ( $this->_vars['value']['childs'] )): ?> <small><a href="?action=info&do=categories&id=<?php echo $this->_vars['value']['id']; ?>
"><i class="fa fa-sitemap"></i></a></small><?php endif; ?>
		<?php if ($this->_run_modifier($this->_vars['value']['date_insert'], 'strtotime', 'PHP', 1) > time()): ?>
			<small><i class="fa fa-clock-o red" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "not_shown_yet"), $this);?>"></i></small>
		<?php endif; ?>
	  </td>
      <td align="center"><input name="active[]" type="checkbox" value="<?php echo $this->_vars['value']['id']; ?>
" <?php if ($this->_vars['value']['active']): ?> checked="checked"<?php endif; ?> /></td>
      <td><input name="name[<?php echo $this->_vars['value']['id']; ?>
]" type="text" style="width:100%;" value="<?php echo $this->_vars['value']['title']; ?>
" /></td>
      <td><input name="alias[<?php echo $this->_vars['value']['id']; ?>
]" type="text" style="width:100%;" value="<?php echo $this->_vars['value']['alias']; ?>
" /></td>
      <td align="center"><input name="sort[<?php echo $this->_vars['value']['id']; ?>
]" type="text" value="<?php echo $this->_vars['value']['sort']; ?>
" size="2" /></td>
      <td align="center"><?php if ($this->_vars['value']['pubs'] > 0): ?><a href="?action=info&do=list_publications&cid=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_vars['value']['pubs']; ?>
</a><?php else: ?>-<?php endif; ?></td>
      <td align="center"><a href="?action=info&do=edit_publication&id=0&cid=<?php echo $this->_vars['value']['id']; ?>
"><i class="fa fa-plus"></i></a></td>
      <td align="center"><?php if ($this->_vars['value']['products'] > 0): ?><a href="?action=products&do=list_products&cid=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_vars['value']['products']; ?>
</a><?php elseif ($this->_vars['value']['shop'] == 1): ?><i class="fa fa-check"></i><?php else: ?>-<?php endif; ?></td>
      <td align="center"><?php if ($this->_vars['value']['shop'] == 1): ?><a href="?action=products&do=add&cid=<?php echo $this->_vars['value']['id']; ?>
"><i class="fa fa-plus"></i></a><?php else: ?>-<?php endif; ?></td>
      <td align="center"><?php if (! empty ( $this->_vars['value']['site_url'] )): ?>
				<?php if ($this->_vars['value']['id'] == $this->_vars['value']['default_id_categ']): ?>
					<?php $this->assign('r_href', $this->_vars['value']['site_url']."/"); ?>
				<?php else: ?>
					<?php $this->assign('r_href', $this->_vars['value']['site_url']."/".$this->_vars['value']['alias']."/"); ?>
					
					<?php if (empty ( $this->_vars['value']['active'] )): ?>
						<?php $this->assign('r_href', $this->_vars['r_href']."?debug=".$this->_vars['value']['site_id']); ?>
					<?php endif; ?>
				<?php endif; ?>
	  
				<small><a href="<?php echo $this->_vars['r_href']; ?>
" target="_blank"><i class="fa fa-external-link"></i></a></small>
			  <?php elseif (! empty ( $this->_vars['value']['site_url_qty'] )): ?><span class="red">*</span><?php endif; ?></td>
      <td align="center"><input onclick="return CheckCB(this);" type="checkbox" name="delcategs[]" value="<?php echo $this->_vars['value']['id']; ?>
" /></td>
    </tr>
  <?php endforeach; endif; ?>

  <tr>
    <td colspan="10" align="right"><input type="submit" name="update" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "update"), $this);?>" onclick="return confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "are_u_sure"), $this);?>')" /></td>
  </tr>
</table>
</form>
<?php else: ?>

  <?php if ($this->_vars['id'] == 0): ?>
    <p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "no_pages"), $this);?><br/>
    <a href="?action=info&do=edit_categ&id=0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "add_first_page"), $this);?></a>!</p>
  <?php else: ?>
    <p><h2><?php if (! empty ( $this->_vars['categ_info']['parent_title'] )): ?><a href="?action=info&do=categories&id=<?php echo $this->_vars['categ_info']['id_parent']; ?>
"><?php echo $this->_vars['categ_info']['parent_title']; ?>
</a> / <?php endif;  echo $this->_vars['categ_info']['title']; ?>
</h2>  
    <ul>
      <li><a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['id']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "edit_categ"), $this);?></a></li>
      <?php if ($this->_vars['categ_info']['pubs'] > 0): ?><li><a href="?action=info&do=list_publications&cid=<?php echo $this->_vars['id']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "categ_pubs"), $this);?></a> (<?php echo $this->_vars['categ_info']['pubs']; ?>
)</li><?php endif; ?>
      <li><a href="?action=info&do=edit_categ&id=0&cid=<?php echo $this->_vars['id']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "add_conn_categ"), $this);?></a></li>
      <li><a href="?action=info&do=edit_publication&id=0&cid=<?php echo $this->_vars['id']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add_pub"), $this);?></a></li>
      <li><a href="../<?php echo $this->_vars['categ_info']['alias']; ?>
/" target="_blank"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "see_on_website"), $this);?> <small><i class="fa fa-external-link"></i></small></a></li>
    </ul>
    <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "subcategs_empty"), $this);?></p>
  <?php endif; ?>

<?php endif; ?>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/pages.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>