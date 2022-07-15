<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.html_select_date.php'); $this->register_function("html_select_date", "tpl_function_html_select_date");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0"><?php if ($this->_vars['categ']['id'] > 0):  echo add_favorites_tpl(array('where' => "categ",'id' => $this->_vars['categ']['id']), $this);?> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "editing"), $this); else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "adding"), $this); endif; ?> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "ofcateg",'case' => "lower"), $this);?></h1>
<?php if ($this->_vars['categ']['id'] > 0): ?>
  <p><i class="fa fa-external-link"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "see_on_website"), $this);?>: <?php echo site_form_for_pubs_tpl(array('0' => $this->_vars['categ'],'1' => "site_categ",'2' => $this->_vars['categ']['alias'],'3' => " <i class='fa fa-globe'></i> "), $this);?></p>
<?php endif; ?>

  <?php if (isset ( $_GET['updated'] )): ?>
    <table width="80%"><tr><td><blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "updated"), $this);?></blockquote></td></tr></table>
  <?php elseif (isset ( $_GET['added'] )): ?>
    <table width="80%"><tr><td><blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "added"), $this);?></blockquote></td></tr></table>
  <?php endif; ?>

<?php if ($this->_run_modifier($this->_vars['categ']['date_insert'], 'strtotime', 'PHP', 1) > time()): ?>
	<table width="80%"><tr><td>
	<h4 class="red center"><i class="fa fa-clock-o"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "not_shown_yet"), $this);?></h4>
	</td></tr></table>
<?php endif; ?>
<form method="post" enctype="multipart/form-data">
  <table width="80%">
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="20%">ID: <?php if ($this->_vars['categ']['id'] > 0):  echo $this->_vars['categ']['id'];  else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "not_yet"), $this); endif; ?></td>
      <td><?php if ($this->_vars['categ']['id'] > 0): ?>
          <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "added_by"), $this);?>: <a href="?action=settings&do=add_user&id=<?php echo $this->_vars['categ']['user_id']; ?>
" title="<?php echo $this->_vars['categ']['user_login']; ?>
"><i class="fa fa-user"></i> <?php echo $this->_vars['categ']['user_name']; ?>
</a> <?php echo $this->_vars['categ']['date_insert']; ?>

               <?php if (! empty ( $this->_vars['categ']['date_update'] ) && $this->_run_modifier($this->_vars['categ']['date_update'], 'strtotime', 'PHP', 1) > 0):  echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "last_edit_by"), $this);?>: <?php echo $this->_vars['categ']['date_update'];  endif; ?>
          <?php endif; ?>
      </td>
    </tr>

    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="20%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "active"), $this);?></td>
      <td><input type="hidden" name="active" value="0" />
          <input type="checkbox" name="active" value="1" <?php if ($this->_vars['categ']['active']): ?> checked="checked"<?php endif; ?> /> 
          <input type="submit" name="save" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "save"), $this);?>" />
		  
		  <span style="float:right;">
		  <?php if (! empty ( $this->_vars['categ']['qty_pubs'] )): ?><a href="?action=info&do=list_publications&cid=<?php echo $this->_vars['categ']['id']; ?>
"><i class="fa fa-list"></i></a> <a href="?action=info&do=list_publications&cid=<?php echo $this->_vars['categ']['id']; ?>
"><?php echo $this->_vars['categ']['qty_pubs']; ?>
</a> <?php endif; ?> 
		  
		  <?php if (empty ( $this->_vars['categ']['shop'] )): ?><a href="?action=info&do=edit_publication&id=0&cid=<?php echo $this->_vars['categ']['id']; ?>
"><i class="fa fa-plus"  title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add_pub"), $this);?>"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a> <?php endif; ?> 
		  
		  <?php if (! empty ( $this->_vars['categ']['qty_products'] )): ?><a href="?action=products&do=list_products&cid=<?php echo $this->_vars['categ']['id']; ?>
"><i class="fa fa-shopping-cart" ></i></a> <a href="?action=products&do=list_products&cid=<?php echo $this->_vars['categ']['id']; ?>
"><?php echo $this->_vars['categ']['qty_products']; ?>
</a><?php endif; ?>

		  <?php if (! empty ( $this->_vars['categ']['shop'] )): ?><a href="?action=products&do=add&cid=<?php echo $this->_vars['categ']['id']; ?>
"><i class="fa fa-plus" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add_product"), $this);?>"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a><?php endif; ?> 
		  
		  </span>
      </td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "name"), $this);?></td>
      <td><input type="text" name="title" style="width: 100%;" maxlength="255" value="<?php echo $this->_run_modifier($this->_vars['categ']['title'], 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
    </tr>

   
	<tr>
      <td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "connected_page"), $this);?></td>
      <td>
	  
	  <span id='verh'>
      <?php if (isset ( $this->_vars['categ']['parent_title2'] )): ?> &raquo; <a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['categ']['parent_id2']; ?>
"><?php echo $this->_vars['categ']['parent_title2']; ?>
</a>
	  <?php endif; ?> &raquo; <?php if ($this->_vars['categ']['id_parent'] > 0): ?><a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['categ']['id_parent']; ?>
"><?php echo $this->_vars['categ']['parent_title']; ?>
</a> <a href="?action=info&do=categories&id=<?php echo $this->_vars['categ']['id_parent']; ?>
"><i class="fa fa-sitemap"></i></a><?php else: ?>
				<?php if (empty ( $this->_vars['categ']['id'] ) && ! empty ( $_GET['cid'] )): ?>
					<?php $this->assign('cid', $_GET['cid']); ?>
					<?php $this->assign('all', $this->_vars['site_vars']['_pages']); ?>
					<?php if (isset ( $this->_vars['all'][$this->_vars['cid']] )): ?>
						<?php $this->assign('cid', $this->_vars['all'][$this->_vars['cid']]); ?>
					<?php endif; ?>
				<?php endif; ?>
			<?php if (! empty ( $this->_vars['cid']['title'] )):  echo $this->_vars['cid']['title'];  else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "home"), $this); endif; ?>
	  <?php endif; ?></span>
        <p><a href="javascript: ShowHide('block-categs')" style="border-bottom: 1px dashed blue;"><i class="fa fa-edit"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "change"), $this);?></a></p> 
		<div style="display: none; max-height: 400px; overflow: auto;" id="block-categs">
			<table class="table">
			<tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
				<td width="30"><input type="radio" name="id_parental" value="0" <?php if ($this->_vars['categ']['id_parent'] == 0): ?>checked="checked"<?php endif; ?>></td>
				<td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "home"), $this);?></td>
			</tr>
			<?php if (count((array)$this->_vars['site_vars']['_pages'])): foreach ((array)$this->_vars['site_vars']['_pages'] as $this->_vars['k'] => $this->_vars['v']): ?>
				<tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>				
				<td width="30"><?php if ($this->_vars['v']['id'] != $this->_vars['categ']['id']): ?><input type="radio" name="id_parental" value="<?php echo $this->_vars['v']['id']; ?>
" <?php if ($this->_vars['categ']['id_parent'] == $this->_vars['v']['id'] || ( empty ( $this->_vars['categ']['id'] ) && ! empty ( $_GET['cid'] ) && $_GET['cid'] == $this->_vars['v']['id'] )): ?>checked="checked"<?php endif; ?>><?php endif; ?></td>
				<td>
					<span style="padding-left:<?php echo $this->_vars['v']['padding']; ?>
px;<?php if ($this->_vars['categ']['id_parent'] == $this->_vars['v']['id']): ?> color:red; font-weight:bold;<?php endif; ?>"><?php if (empty ( $this->_vars['v']['visible'] )): ?><s><?php echo $this->_vars['v']['title']; ?>
</s><?php else:  echo $this->_vars['v']['title'];  endif; ?></span>
				</td>
				</tr>				
			<?php endforeach; endif; ?>
			</table>
		</div>
      </td>
    </tr>
    
    <tr class="odd">
      <td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "page_content"), $this);?></td>
      <td><p><?php if (isset ( $_GET['noeditor'] )): ?><img src="images/word.gif" vspace="3" /> <a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['categ']['id']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "on_editor"), $this);?></a><?php else: ?><img src="images/noword.gif" vspace="3" /> <a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['categ']['id']; ?>
&noeditor=1"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "off_editor"), $this);?></a><?php endif; ?></p>
          <textarea id="memo" name="memo" rows="10" style="width: 100%;"><?php echo $this->_vars['categ']['memo']; ?>
</textarea>
          <?php if (! isset ( $_GET['noeditor'] )): ?>
          <script type="text/javascript">
            var editor = CKEDITOR.replace( 'memo' );
            CKFinder.setupCKEditor( editor, '/<?php echo constant('ADMIN_FOLDER'); ?>
/ckfinder/' ) ;
          </script>
          <?php endif; ?>
      </td>
    </tr>
    
    <tr class="<?php echo tpl_function_cycle(array('values' => ",odd",'reset' => "1"), $this);?>">
      <td colspan="2">
        <a href="javascript: ShowHide('block-meta')" style="border-bottom: 1px dashed blue;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "metatags"), $this);?></a>
        <div style="display: none;" id="block-meta">
          <table width="100%">
            <tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>">
              <td width=20%><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "metatitle"), $this);?> (title)</td>
              <td><input type="text" name="meta_title" style="width: 100%;" maxlength="255" value="<?php echo $this->_run_modifier($this->_vars['categ']['meta_title'], 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
            </tr>
            <tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>">
              <td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "metadesc"), $this);?> (description)</td>
              <td><textarea name="meta_description" style="width: 100%;" rows="4"><?php echo $this->_run_modifier($this->_vars['categ']['meta_description'], 'htmlspecialchars', 'PHP', 1); ?>
</textarea></td>
            </tr>
            <tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>">
              <td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "metakeyw"), $this);?> (keywords)</td>
              <td><textarea name="meta_keywords" style="width: 100%;" rows="4"><?php echo $this->_run_modifier($this->_vars['categ']['meta_keywords'], 'htmlspecialchars', 'PHP', 1); ?>
</textarea></td>
            </tr>
          </table>
        </div>
      </td>
    </tr>
  
  <tr class="<?php echo tpl_function_cycle(array('values' => ",odd",'reset' => "1"), $this);?>">
	<td colspan="2">
  <a href="javascript: ShowHide('block-extra')" style="border-bottom: 1px dashed blue;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "extras"), $this);?></a>
  <div style="display: none;" id="block-extra">
  <table width="100%" cellpadding="3" cellspacing="1">

  <tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>">
	<td width="20%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "synonim"), $this);?> <br><small>(www.site.com/<span style="color:red;">###</span>/)</small></td><td>
          <input type="text" name="alias" style="width: 100%;" maxlength="255" value="<?php echo $this->_run_modifier($this->_vars['categ']['alias'], 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
  </tr>

  <tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>">
  <td width="20%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "status",'key3' => "sort"), $this);?></td><td>
          <input type="text" name="sort" size="5" maxlength="5" value="<?php echo $this->_run_modifier($this->_vars['categ']['sort'], 'htmlspecialchars', 'PHP', 1); ?>
" /></td></tr>

  <tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>"><td width="20%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "website"), $this);?></td><td>
  <?php echo site_form_for_pubs_tpl(array('0' => $this->_vars['categ'],'1' => "site_categ"), $this);?>
  </td></tr>

  <tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>">
      <td width="20%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "mark_star"), $this);?></td>
      <td><input type="checkbox" name="f_spec" value="1"<?php if ($this->_vars['categ']['f_spec'] == 1): ?> checked="checked"<?php endif; ?> ></td>
  </tr>

  <tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>">
      <td width="20%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "mark_last"), $this);?></td>
      <td><input type="checkbox" name="in_last" value="1"<?php if ($this->_vars['categ']['in_last'] == 1): ?> checked="checked"<?php endif; ?> ></td>
  </tr>

  <tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>">
      <td width="20%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "products"), $this);?></td>
      <td><input type="checkbox" name="shop" value="1"<?php if ($this->_vars['categ']['shop'] == 1): ?> checked="checked"<?php endif; ?> > <i class="fa fa-shopping-cart"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "if_catalog"), $this);?></td>
  </tr>

  <tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>">
      <td width="20%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "show_filter"), $this);?></td>
      <td><input type="checkbox" name="show_filter" value="1"<?php if ($this->_vars['categ']['show_filter'] == 1): ?> checked="checked"<?php endif; ?> ></td>
  </tr>

  <tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>">
      <td width="20%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "short_desc"), $this);?></td>
      <td><textarea name="memo_short" style="width:100%;" rows="3"><?php echo $this->_vars['categ']['memo_short']; ?>
</textarea></td>
  </tr>

  <tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>">
    <td width="20%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "tpl"), $this);?></td>
    <td><?php if (isset ( $this->_vars['categ']['tpls'] )): ?>
              <select name="multitpl">
              <?php if (count((array)$this->_vars['categ']['tpls'])): foreach ((array)$this->_vars['categ']['tpls'] as $this->_vars['value']): ?>
                <option value="<?php echo $this->_vars['value']; ?>
"<?php if ($this->_vars['value'] == $this->_vars['categ']['multitpl']): ?> selected<?php endif; ?>><?php echo $this->_vars['value']; ?>
</option>
              <?php endforeach; endif; ?>
              </select> 
			  <a href="?action=settings&do=site_vars&site_id=-1&q=sys_tpl_pages&redirect=1"><i class="fa fa-pencil"></i></a>
            <?php else: ?>
              <input type="text" name="multitpl" style="width:100%;" maxlength="255" value="<?php if ($this->_vars['categ']['multitpl'] == ""): ?>product.html<?php else:  echo $this->_run_modifier($this->_vars['categ']['multitpl'], 'htmlspecialchars', 'PHP', 1);  endif; ?>" />
            <?php endif; ?></td>
  </tr>

  <tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>">
    <td width="20%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "change_add_date"), $this);?></td>
    <td>
    <?php echo tpl_function_html_select_date(array('time' => $this->_vars['categ']['date_insert'],'start_year' => "-20",'end_year' => "+5",'day_value_format' => "%02d",'month_format' => "%m",'field_order' => "DMY",'field_array' => "insert_date",'prefix' => "",'lang' => $this->_vars['site_vars']['lang_admin']), $this);?>	
    </td>
  </tr>
  <tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>">
    <td width="20%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sort_by"), $this);?>: </td>
    <td><select name="sortby">
      <option value="date_insert desc"<?php if ($this->_vars['categ']['sortby'] == "date_insert desc"): ?> selected<?php endif; ?>><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sort_date_desc"), $this);?></option>
      <option value="date_insert"<?php if ($this->_vars['categ']['sortby'] == "date_insert"): ?> selected<?php endif; ?>><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sort_date"), $this);?></option>
      <option value="name"<?php if ($this->_vars['categ']['sortby'] == "name"): ?> selected<?php endif; ?>><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sort_title"), $this);?></option>
      <option value="views desc"<?php if ($this->_vars['categ']['sortby'] == "views desc"): ?> selected<?php endif; ?>><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sort_pop"), $this);?></option>
      <option value="monthviews desc"<?php if ($this->_vars['categ']['sortby'] == "monthviews desc"): ?> selected<?php endif; ?>><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sort_pop30"), $this);?></option>
	  <option value="price"<?php if ($this->_vars['categ']['sortby'] == "price"): ?> selected<?php endif; ?>><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sort_price"), $this);?></option>
	  <option value="pricedesc"<?php if ($this->_vars['categ']['sortby'] == "pricedesc"): ?> selected<?php endif; ?>><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sort_price_desc"), $this);?></option>
    </select>
  </td></tr>
  <?php if ($this->_vars['categ']['id']): ?><tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>"><td></td><td><p><a href="./?action=comments&id=0&record_id=<?php echo $this->_vars['categ']['id']; ?>
&type=categ"><i class="fa fa-plus-circle"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "add_comment"), $this);?></a><?php if (! empty ( $this->_vars['categ']['comments_qty'] )): ?><br><a href="?action=comments&record_id=<?php echo $this->_vars['categ']['id']; ?>
&record_type=categ"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "added_comments"), $this);?> (<?php echo $this->_vars['categ']['comments_qty']; ?>
)</a><?php endif; ?></p></td></tr><?php endif; ?>
  
  <tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>">
    <td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "icon_desc"), $this);?></td>
	<td><a href="javascript: ShowHide('icons')" style="border-bottom: 1px dashed blue;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "show"), $this);?></a>
			<div style="display: none;" id="icons">
				<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/icons.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
			</div>
		</td>
  </tr>
  </table>
  </div>
  </td></tr>

  <tr class="<?php echo tpl_function_cycle(array('values' => ",odd",'reset' => "1"), $this);?>">
    <td colspan="2">
  <a href="javascript: ShowHide('block-opts')" style="border-bottom: 1px dashed blue;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "options"), $this);?></a>
  <div style="display: none;" id="block-opts">
  <p><?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/option_groups.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?></p>
  </div>
    </td>
  </tr>

  
  	<tr class="<?php echo tpl_function_cycle(array('values' => ",odd",'reset' => "1"), $this);?>">
      <td colspan="2">

  		  <a href="javascript: ShowHide('block-s')" style="border-bottom: 1px dashed blue;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "blocks"), $this);?></a>

  		  <div <?php if (empty ( $this->_vars['blocks'] )): ?>style="display: none;"<?php endif; ?> id="block-s">
		  <?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/add_block.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
		  
		  <?php if (! empty ( $this->_vars['categ']['id'] )): ?>
			<h4><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "blocks_overall"), $this);?></h4>
		  <?php if (! empty ( $this->_vars['categ']['blocks'] )): ?>
		  <table width="100%">
			<tr>
				<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "spec_title"), $this);?></th>
				<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "layout"), $this);?></th>
				<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "alias"), $this);?></th>
				<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "type"), $this);?></th>
				<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "qty"), $this);?></th>
			</tr>
		  <?php if (count((array)$this->_vars['categ']['blocks'])): foreach ((array)$this->_vars['categ']['blocks'] as $this->_vars['k'] => $this->_vars['b']): ?>
			<?php if (count((array)$this->_vars['b'])): foreach ((array)$this->_vars['b'] as $this->_vars['v']): ?>
			<tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>">
				<td><a href="?action=settings&do=blocks&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['title_admin']; ?>
</a></td>
				<td><a href="?action=settings&do=blocks&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['where']; ?>
</a></td>
				<td><a href="?action=settings&do=blocks&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['title']; ?>
</a></td>
				<td><a href="?action=settings&do=blocks&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['type']; ?>
</a></td>
				<td class="center"><?php echo $this->_vars['v']['qty']; ?>
</td>
			</tr>
			<?php endforeach; endif; ?>
		  <?php endforeach; endif; ?>
		  </table>
		  <?php endif; ?>
		  
			<ul><li><a href="?action=settings&do=blocks"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "all_blocks"), $this);?></a></li></ul>
			<?php endif; ?>
			
  		  </div>
  		</td>
    </tr>  
  
  
  <tr class="<?php echo tpl_function_cycle(array('values' => ",odd",'reset' => "1"), $this);?>">
    <td colspan="2">
  <a href="javascript: ShowHide('block-photos')" style="border-bottom: 1px dashed blue;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "upload_fotos"), $this);?></a>
  <div style="display: none;" id="block-photos">
  <p><?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/upload_fotos_form.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?></p>
  </div>
    </td>
  </tr>
  
  <tr class="<?php echo tpl_function_cycle(array('values' => ",odd",'reset' => "1"), $this);?>">
	<td colspan="2">
  <a href="javascript: ShowHide('block-files')" style="border-bottom: 1px dashed blue;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "upload_files"), $this);?></a>
  <div style="display: none;" id="block-files">
  <p><?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/upload_files_form.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?></p>    
  </div>
  </td></tr>

    <?php if ($this->_vars['categ']['fotos_qty']): ?>
    <tr>
      <td colspan="2"><?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/uploaded_fotos.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?></td>
    </tr>                                
    <?php endif; ?>

    <?php if ($this->_vars['categ']['files_qty']): ?>
    <tr>
      <td colspan="2"><?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/uploaded_files.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?></td>
    </tr>                                
    <?php endif; ?>
	
	<tr class="">
		<td colspan=2>
			<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("settings/options.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
		</td>
	</tr>

    <?php if ($this->_vars['categ']['id'] > 0): ?>
    <tr>
      <td colspan="2" align="right"><a href="?action=info&do=copy&id=<?php echo $this->_vars['categ']['id']; ?>
&where=categ" onclick="if(confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "copy_help"), $this);?>')) return true; else return false;"><i class="fa fa-copy"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "copy_page"), $this);?></a></td>
    </tr>                                
    <?php endif; ?>
    
    <tr class="<?php echo tpl_function_cycle(array('values' => ",odd",'reset' => "1"), $this);?>">
      <td colspan="2" align=center>
        <input type="submit" name="save" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "save"), $this);?>" />
        <?php if ($this->_vars['categ']['id'] > 0): ?>
          <small><input type=submit name=del value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "delete_all"), $this);?>" onclick="if(confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "really"), $this);?>')) return true; else return false;"></small>
          <?php endif; ?>
      </td>
    </tr>
        
  </table>
</form>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>