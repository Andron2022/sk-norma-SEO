<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.html_select_time.php'); $this->register_function("html_select_time", "tpl_function_html_select_time");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.html_select_date.php'); $this->register_function("html_select_date", "tpl_function_html_select_date");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<h1 class="mt-0"><?php if ($this->_vars['categ']['id'] > 0):  echo add_favorites_tpl(array('where' => "product",'id' => $this->_vars['categ']['id']), $this);?> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "editing"), $this); else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "adding"), $this); endif; ?></h1>
<?php if ($this->_vars['categ']['id'] > 0): ?>
    <p><i class="fa fa-external-link"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "see_on_website"), $this);?>: 	
	<?php if (! empty ( $this->_vars['categ']['links'] )): ?>
		<?php if (count((array)$this->_vars['categ']['links'])): foreach ((array)$this->_vars['categ']['links'] as $this->_vars['k'] => $this->_vars['v']): ?>
		<?php if ($this->_vars['k'] > 0): ?> <i class="fa fa-globe"></i> <?php endif; ?><a href="<?php echo $this->_vars['v']['site_url']; ?>
/<?php echo $this->_vars['categ']['alias'];  echo constant('URL_END');  if (empty ( $this->_vars['categ']['active'] )): ?>?debug=<?php echo $this->_vars['v']['id'];  endif; ?>" target="_blank"><?php echo $this->_vars['v']['site_url']; ?>
</a>
		<?php endforeach; endif; ?>	
	<?php else: ?>-<?php endif; ?>	
	</p>
<?php endif; ?>

  <?php if (isset ( $_GET['updated'] )): ?>
    <table width="80%"><tr><td><blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "updated"), $this);?></blockquote></td></tr></table>
  <?php elseif (isset ( $_GET['added'] )): ?>
    <table width="80%"><tr><td><blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "added"), $this);?></blockquote></td></tr></table>
  <?php endif; ?>


<?php if (! empty ( $_GET['orders'] ) && ! empty ( $this->_vars['categ']['orders'] )): ?>
	<h3><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "orders_with_product"), $this);?> <a href="?action=products&do=edit&id=<?php echo $this->_vars['categ']['id']; ?>
"><?php echo $this->_vars['categ']['name']; ?>
 <i class="fa fa-pencil"></i></a></h3>
	<?php if (! empty ( $this->_vars['pages'] )):  echo $this->_vars['pages'];  endif; ?>
	<table width="80%">
		<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
			<th>ID</th>
			<th><i class="fa fa-clock-o"></i></th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "payment"), $this);?></th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "status"), $this);?></th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "website"), $this);?></th>
		</tr>
	<?php if (count((array)$this->_vars['categ']['orders'])): foreach ((array)$this->_vars['categ']['orders'] as $this->_vars['v']): ?>
		<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
			<td><a href="?action=orders&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['site_id']; ?>
-<?php echo $this->_run_modifier($this->_vars['v']['order_id'], 'chunk', 'plugin', 1, 4, "-"); ?>
</a></td>
			<td class="center"><a href="?action=orders&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_run_modifier($this->_vars['v']['created'], 'date', 'plugin', 1, $this->_vars['site_vars']['site_date_format']); ?>
</a></td>
			<td><a href="?action=orders&id=<?php echo $this->_vars['v']['id']; ?>
"><?php if (! empty ( $this->_vars['v']['payd_status'] )): ?><i class="fa fa-check"></i><?php endif; ?></a></td>
			<td><a href="?action=orders&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['status_title']; ?>
</a></td>
			<td><a href="?action=orders&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['site']; ?>
</a></td>
		</tr>
	<?php endforeach; endif; ?>
	</table>
	<?php if (! empty ( $this->_vars['pages'] )):  echo $this->_vars['pages'];  endif;  else: ?>

<form method=post enctype="multipart/form-data">
  <table width="80%">
    <?php if ($this->_vars['categ']['id'] > 0): ?><tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
      <td width="200">ID: <?php echo $this->_vars['categ']['id']; ?>
</td>
      <td><?php if ($this->_vars['categ']['date_insert'] > $this->_run_modifier(time(), 'date', 'plugin', 1, "Y-m-d H:i:s")): ?>
		<i class="fa fa-clock-o" style="color:red;" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "future_date"), $this);?>"></i> <?php endif;  echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "added_by"), $this);?>: <a href="?action=settings&do=add_user&id=<?php echo $this->_vars['categ']['user_id']; ?>
"><i class="fa fa-user"></i> <?php echo $this->_vars['categ']['user_name']; ?>
</a> <?php echo $this->_vars['categ']['date_insert_f']; ?>
 
	  <?php if (! empty ( $this->_vars['categ']['date_update_f'] )):  echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "last_edit_by"), $this);?>: <?php echo $this->_vars['categ']['date_update_f'];  endif; ?>
	  <?php if (! empty ( $this->_vars['categ']['views'] )): ?><br><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "views"), $this);?>. <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "total_views"), $this);?>: <?php echo $this->_vars['categ']['views']; ?>
 / <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "views_for_month"), $this);?>: <?php echo $this->_vars['categ']['views_month'];  endif; ?>
	  <?php if (! empty ( $this->_vars['categ']['orders_qty'] )): ?><br><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "orders"), $this);?>: <a href="?action=products&do=edit&id=<?php echo $this->_vars['categ']['id']; ?>
&orders=1"><i class="fa fa-shopping-cart"></i> <?php echo $this->_vars['categ']['orders_qty']; ?>
</a><?php endif; ?>
	  </td>               
      </td>
    </tr><?php endif; ?>
   
    <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "title"), $this);?></td>
      <td><input type=text style="width:100%;" name="name" value="<?php echo $this->_run_modifier($this->_vars['categ']['name'], 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "product_code"), $this);?> <small>(<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "or_model"), $this);?>)</small></td>
      <td><input type="text" style="width:100%;" name="barcode" value="<?php echo $this->_run_modifier($this->_vars['categ']['barcode'], 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
    </tr>

    <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "address"), $this);?></td>
      <td><input type="text" style="width:100%;" name="name_short" value="<?php echo $this->_run_modifier($this->_vars['categ']['name_short'], 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
    </tr>
	
	<tr>
      <td<?php if (empty ( $this->_vars['product_categories'] )): ?> class="red"<?php endif; ?>><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "connected_page"), $this);?></td>
      <td>
	  
	  <?php if (! empty ( $this->_vars['product_categories'] )): ?>
		<?php if (count((array)$this->_vars['product_categories'])): foreach ((array)$this->_vars['product_categories'] as $this->_vars['v']): ?>
			<?php if (! empty ( $this->_vars['v']['subtitle'] ) && ! empty ( $this->_vars['v']['subid'] )): ?>
				&raquo; <a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['v']['subid']; ?>
"><?php echo $this->_vars['v']['subtitle']; ?>
</a> 
				<a href="?action=info&do=categories&id=<?php echo $this->_vars['v']['subid']; ?>
"><i class="fa fa-sitemap"></i></a> 
			<?php endif; ?>
			
			<?php if (! empty ( $this->_vars['v']['title'] ) && ! empty ( $this->_vars['v']['id_categ'] )): ?>
				&raquo; <a href="?action=products&do=list_products&cid=<?php echo $this->_vars['v']['id_categ']; ?>
"><?php echo $this->_vars['v']['title']; ?>
</a> 
				<a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['v']['id_categ']; ?>
"><i class="fa fa-pencil"></i></a> 
				<a href="?action=products&do=list_products&cid=<?php echo $this->_vars['v']['id_categ']; ?>
"><i class="fa fa-sitemap"></i></a><br>
			<?php endif; ?>
			
		<?php endforeach; endif; ?>
	  <?php endif; ?>
	  
	  
        <p><a href="javascript: ShowHide('block-categs')" style="border-bottom: 1px dashed blue;"><i class="fa fa-edit"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "change"), $this);?></a></p> 
		<div style="display: none; max-height: 400px; overflow: auto;" id="block-categs">
			<table class="table">
			<?php if (count((array)$this->_vars['site_vars']['_pages'])): foreach ((array)$this->_vars['site_vars']['_pages'] as $this->_vars['k'] => $this->_vars['v']): ?>
				<?php $this->assign('cur_id', $this->_vars['v']['id']); ?>
				<tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>				
				<td width="30"><?php if (! empty ( $this->_vars['v']['shop'] )): ?><input type="checkbox" name="categs[]" value="<?php echo $this->_vars['v']['id']; ?>
" <?php if (isset ( $this->_vars['product_categories'][$this->_vars['cur_id']] )): ?>checked="checked"<?php endif; ?>><?php endif; ?></td>
				<td>
					
					<span style="padding-left:<?php echo $this->_vars['v']['padding']; ?>
px;<?php if (isset ( $this->_vars['product_categories'][$this->_vars['cur_id']] )): ?> color:red; font-weight:bold;<?php endif; ?>"><?php if (empty ( $this->_vars['v']['visible'] )): ?><s><?php echo $this->_vars['v']['title']; ?>
</s><?php else:  echo $this->_vars['v']['title'];  endif; ?></span>
				</td>
				</tr>				
			<?php endforeach; endif; ?>
			</table>
		</div>
      </td>
    </tr>
		
    
    <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "active"), $this);?></td>
      <td><input type="hidden" name="active" value="0" />
        <input type="checkbox" name="active" value="1" <?php if ($this->_vars['categ']['active']): ?> checked="checked"<?php endif; ?> />
        <input type="submit" name="save" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "save"), $this);?>" />
      </td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
      <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "active_to_buy"), $this);?></td>
      <td><input type="checkbox" name="accept_orders" value="1"<?php if ($this->_vars['categ']['accept_orders'] == 1): ?> checked="checked"<?php endif; ?>></td>
    </tr>
    
    <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
      <td></td>
      <td>
          <table border=0 cellpadding=2 cellspacing=1 bgcolor="<?php echo $this->_vars['admin_vars']['bglight']; ?>
">
            <tr>
              <th><a href="?action=settings&do=site_vars&site_id=-1&q=sys_currency&redirect=1"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "currency"), $this);?></a></th>
              <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "price"), $this);?></th>
              <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "price_old"), $this);?></th>
              <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "yml_rate"), $this);?></th>
            </tr>
            <tr class="odd">
              <td><select name="currency">
                <option value="euro"<?php if ($this->_vars['categ']['currency'] == "euro"): ?> selected<?php endif; ?>><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "euro"), $this);?></option>
                <option value="usd"<?php if ($this->_vars['categ']['currency'] == "usd"): ?> selected<?php endif; ?>><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "usd"), $this);?></option>
                <option value="rur"<?php if ($this->_vars['categ']['currency'] == "rur"): ?> selected<?php endif; ?>><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "rub"), $this);?></option>
              </select></td>
              <td><input type=text size="10" name="price" value="<?php echo $this->_vars['categ']['price']; ?>
" /></td>
              <td><input type=text size="10" name="price_spec" value="<?php echo $this->_vars['categ']['price_spec']; ?>
" /></td>
              <td align="center"><input type="text" size="5" name="bid_ya" value="<?php echo $this->_vars['categ']['bid_ya']; ?>
" /></td>
            </tr>
            <tr>
              <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "new"), $this);?></th>
              <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "spec"), $this);?></th>
              <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "period"), $this);?></th>
              <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "qty"), $this);?></th>
            </tr>
            <tr>
              <td align="center"><input type="checkbox" name="f_new" value="1"<?php if ($this->_vars['categ']['f_new'] == 1): ?> checked="checked"<?php endif; ?>></td>
              <td align="center"><input type="checkbox" name="f_spec" value="1"<?php if ($this->_vars['categ']['f_spec'] == 1): ?> checked="checked"<?php endif; ?>></td>
              <td><select name="price_period">
                <option value="razovo"<?php if ($this->_vars['categ']['price_period'] == "razovo"): ?> selected<?php endif; ?>>-</option>
                <option value="day"<?php if ($this->_vars['categ']['price_period'] == "day"): ?> selected<?php endif; ?>><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "day"), $this);?></option>
                <option value="week"<?php if ($this->_vars['categ']['price_period'] == "week"): ?> selected<?php endif; ?>><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "week"), $this);?></option>
                <option value="month"<?php if ($this->_vars['categ']['price_period'] == "month"): ?> selected<?php endif; ?>><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "month"), $this);?></option>
                <option value="year"<?php if ($this->_vars['categ']['price_period'] == "year"): ?> selected<?php endif; ?>><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "year"), $this);?></option>
                <option value="from"<?php if ($this->_vars['categ']['price_period'] == "from"): ?> selected<?php endif; ?>><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "from"), $this);?></option>
              </select></td>
              <td align="center"><input type="text" size="5" name="total_qty" value="<?php echo $this->_vars['categ']['total_qty']; ?>
" /></td>
            </tr>
          </table>
    </td>
  </tr>

  <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
    <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "short_desc"), $this);?></td>
    <td><textarea name=memo_short rows=4 style="width:100%;"><?php echo $this->_run_modifier($this->_run_modifier($this->_vars['categ']['memo_short'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
</textarea></td>
  </tr>

  <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
    <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "desc"), $this);?></td>
    <td><p><?php if (isset ( $_GET['noeditor'] )): ?><img src="images/word.gif" vspace="3" /> <a href="?action=products&do=edit&id=<?php echo $this->_vars['categ']['id']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "on_editor"), $this);?></a><?php else: ?><img src="images/noword.gif" vspace="3" /> <a href="?action=products&do=edit&id=<?php echo $this->_vars['categ']['id']; ?>
&noeditor=1"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "off_editor"), $this);?></a><?php endif; ?></p>
          <textarea id="memo" name="memo" rows="10" style="width: 100%;"><?php echo $this->_vars['categ']['memo']; ?>
</textarea>
          <?php if (! isset ( $_GET['noeditor'] )): ?>
          <script type="text/javascript">
            var editor = CKEDITOR.replace( 'memo');
            CKFinder.setupCKEditor( editor, '/<?php echo constant('ADMIN_FOLDER'); ?>
/ckfinder/' ) ;
          </script>
          <?php endif; ?>    
    </td>
  </tr>

  
  <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd",'reset' => "1"), $this);?>>
    <td colspan="2"><a href="javascript: ShowHide('block-meta')" style="border-bottom: 1px dashed blue;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "metatags"), $this);?></a>
      <div style="display: none;" id="block-meta">
		  <table width="100%" cellpadding="0" cellspacing="1">
		    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
          <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "metatitle"), $this);?> (title)</td>
          <td><input type="text" name="meta_title" style="width: 100%;" maxlength="255" value="<?php echo $this->_run_modifier($this->_vars['categ']['meta_title'], 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
        </tr>
        <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
          <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "metadesc"), $this);?> (description)</td>
          <td><textarea name="meta_description" style="width: 100%;" rows="4"><?php echo $this->_run_modifier($this->_vars['categ']['meta_description'], 'htmlspecialchars', 'PHP', 1); ?>
</textarea></td>
        </tr>
        <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
          <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "metakeyw"), $this);?> (keywords)</td>
          <td><textarea name="meta_keywords" style="width: 100%;" rows="4"><?php echo $this->_run_modifier($this->_vars['categ']['meta_keywords'], 'htmlspecialchars', 'PHP', 1); ?>
</textarea></td>
        </tr>
		  </table>
      </div>  
    </td>
  </tr>

  <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd",'reset' => "1"), $this);?>>
	<td colspan="2">
    <a href="javascript: ShowHide('block-extra')" style="border-bottom: 1px dashed blue;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "extras"), $this);?></a>
    <div style="display: none;" id="block-extra">
      <table width="100%" cellpadding="3" cellspacing="1">
        <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
          <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "synonim"), $this);?>  <br><small>(www.site.com/<span style="color:red;">###</span>/)</small></td>
          <td><input type=text style="width:100%;" name="alias" value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['categ']['alias'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
        </tr>
        <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
          <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "tpl"), $this);?></td>
          <td><?php if (isset ( $this->_vars['categ']['tpls'] )): ?>
            <select name="multitpl">
            <?php if (count((array)$this->_vars['categ']['tpls'])): foreach ((array)$this->_vars['categ']['tpls'] as $this->_vars['value']): ?>
              <option value="<?php echo $this->_vars['value']; ?>
"<?php if ($this->_vars['value'] == $this->_vars['categ']['multitpl']): ?> selected<?php endif; ?>><?php echo $this->_vars['value']; ?>
</option>
            <?php endforeach; endif; ?>
            </select> 
			<a href="?action=settings&do=site_vars&site_id=-1&q=sys_tpl_products&redirect=1"><i class="fa fa-pencil"></i></a>
          <?php else: ?>
            <input type="text" name="multitpl" style="width:100%;" maxlength="255" value="<?php if ($this->_vars['categ']['multitpl'] == ""): ?>product.html<?php else:  echo $this->_run_modifier($this->_vars['categ']['multitpl'], 'htmlspecialchars', 'PHP', 1);  endif; ?>" />
          <?php endif; ?>
          </td>
        </tr>

        <tr>
          <td></td>
          <td>
            <table border="0" cellpadding="2" cellspacing="1" width="500">
              <tr>
                <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "price2"), $this);?></th>
                <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "price_own"), $this);?></th>
                <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "weight"), $this);?> (1.00)</th>
              </tr>
              <tr class="odd">
                <td><input type="text" style="width:100%;" name="price_opt" value="<?php echo $this->_vars['categ']['price_opt']; ?>
" /></td>
                <td><input type="text" style="width:100%;" name="price_buy" value="<?php echo $this->_vars['categ']['price_buy']; ?>
" /></td>
                <td><input type="text" style="width:100%;" name="weight_deliver" value="<?php echo $this->_vars['categ']['weight_deliver']; ?>
" /></td>
              </tr>
              <tr>
      		      <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "abuse"), $this);?></th>
      		      <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "gift"), $this);?></th>
      		      <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "new_id"), $this);?></th>
      		    </tr>
              <tr class="odd">
      		      <td><input type="text" style="width:100%;" name="treba" value="<?php echo $this->_vars['categ']['treba']; ?>
" /></td>
      		      <td><input type="text" style="width:100%;" name="present_id" value="<?php echo $this->_vars['categ']['present_id']; ?>
" /></td>
      		      <td><input type="text" style="width:100%;" name="id_next_model" value="<?php echo $this->_vars['categ']['id_next_model']; ?>
" /></td>
              </tr>
            </table>
          </td>
        </tr>
        
        <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
          <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "product_comment"), $this);?></td>
          <td><input type="text" style="width:100%;" name="comment" value="<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['categ']['comment'], 'stripslashes', 'PHP', 1), 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
        </tr>
        
        <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
          <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "complect"), $this);?></td>
          <td><textarea name="complectation" rows="4" style="width:100%;"><?php echo $this->_run_modifier($this->_vars['categ']['complectation'], 'stripslashes', 'PHP', 1); ?>
</textarea></td>
        </tr>

        <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
          <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "alter_search"), $this);?></td>
          <td><input type="text" style="width:100%;" name="alter_search" value="<?php echo $this->_run_modifier($this->_vars['categ']['alter_search'], 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
        </tr>
      
        <?php if ($this->_vars['categ']['id'] > 0): ?><tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
          <td></td>
          <td><p><a href="?action=comments&id=0&record_id=<?php echo $this->_vars['categ']['id']; ?>
&type=product"><i class="fa fa-plus-circle"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "add_comment"), $this);?></a>
		  <?php if (! empty ( $this->_vars['categ']['comments_qty'] )): ?><br><a href="?action=comments&record_id=<?php echo $this->_vars['categ']['id']; ?>
&record_type=product"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "added_comments"), $this);?> (<?php echo $this->_vars['categ']['comments_qty']; ?>
)</a>
		  <?php endif; ?>
		  </p></td>
        </tr><?php endif; ?>

        <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
          <td width="200"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "other_pr_conn"), $this);?></td>
          <td><input type="hidden" name="id_produktov" id="id_produktov" value="<?php echo $this->_vars['connected_products_ids']; ?>
" />
          	<span id='produkti'>
          		<?php if (count((array)$this->_vars['connected_products'])): foreach ((array)$this->_vars['connected_products'] as $this->_vars['k_prod'] => $this->_vars['conn_prod']): ?>
          			&raquo; <a href="?action=products&do=edit&id=<?php echo $this->_vars['k_prod']; ?>
"><?php echo $this->_vars['conn_prod']; ?>
</a><br>
          		<?php endforeach; endif; ?>
          	</span>
            <a href="javascript:" onClick="$('#choose_product_form').dialog('open');"><i class="fa fa-edit"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "change"), $this);?></a>
        	</td>
        </tr>
        
        <div id="choose_product_form" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "other_pr_conn"), $this);?>">
          <h1 id="header"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "list_opened_products"), $this);?></h1>
          <?php if (empty ( $this->_vars['categ']['other_products'] )):  echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "toconnect_empty"), $this); else:  echo $this->_vars['categ']['other_products'];  endif; ?>
        </div>
		
		<?php if (! empty ( $this->_vars['categ']['all_pubs'] )): ?>    
      <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
        <td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "connect_pubs"), $this);?></td>
        <td><input type="hidden" name="id_pubs" id="id_pubs" value="<?php echo $this->_vars['publication_pubs_ids']; ?>
" />
                 <span id='pubs'>
		  <?php if (count((array)$this->_vars['publication_pubs'])): foreach ((array)$this->_vars['publication_pubs'] as $this->_vars['pub_pub']): ?>
			 &raquo; <a href="?action=info&do=edit_publication&id=<?php echo $this->_vars['pub_pub']['id']; ?>
"><?php echo $this->_vars['pub_pub']['title']; ?>
</a><br>
		  <?php endforeach; endif; ?>
		  </span>
		  <p><a href="javascript:" onClick="$('#choose_pub_form').dialog('open');"><i class="fa fa-edit"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "change"), $this);?></a></p>
          
		  <div id="choose_pub_form" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "connect_pubs"), $this);?>">
		  <h1 id="header_pubs"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "publications"), $this);?> (<?php echo $this->_vars['categ']['all_pubs']; ?>
)</h1>
			<?php echo product_to_pubs(array('id' => $this->_vars['categ']['id'],'field' => 'pubs'), $this);?>
		  </div>
        </td>
      </tr>
      <?php endif; ?>
		

        <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
          <td width=200><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "change_add_date"), $this);?></td>
          <td>
		  <?php echo tpl_function_html_select_date(array('time' => $this->_vars['categ']['date_insert'],'start_year' => "-20",'end_year' => "+5",'day_value_format' => "%02d",'month_format' => "%m",'field_order' => "DMY",'field_array' => "insert_date",'prefix' => "",'lang' => $this->_vars['site_vars']['lang_admin']), $this);?> <?php echo tpl_function_html_select_time(array('use_24_hours' => true,'time' => $this->_vars['categ']['date_insert'],'field_array' => "insert_date",'prefix' => ""), $this);?>
		  </td>
        </tr>
      </table>
      </div>
    </td>
  </tr>

  	<tr <?php echo tpl_function_cycle(array('values' => " ,class=odd",'reset' => "1"), $this);?>>
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
			<tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
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
	
  <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd",'reset' => "1"), $this);?>>
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
          
  <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd",'reset' => "1"), $this);?>>
    <td colspan="2">
  		  <a href="javascript: ShowHide('block-files')" style="border-bottom: 1px dashed blue;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "upload_files"), $this);?></a>
  		  <div style="display: none;" id="block-files">
  		  <p><?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/upload_files_form.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?></p>
  		  </div>
    </td>
  </tr>

  <?php if (! empty ( $this->_vars['categ']['options'] )): ?>
  <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd",'reset' => "1"), $this);?>>
    <td colspan="2">
    <a href="javascript: ShowHide('block-options')" style="border-bottom: 1px dashed blue;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "options"), $this);?></a>
      <div  id="block-options">
        <?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/options.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
      </div>
    </td>
  </tr>
	<?php endif; ?>


    <?php if ($this->_vars['categ']['fotos_qty']): ?>
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd",'reset' => "1"), $this);?>>
      <td colspan="2"><?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/uploaded_fotos.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?></td>
    </tr>                                
    <?php endif; ?>

    <?php if ($this->_vars['categ']['files_qty']): ?>
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd",'reset' => "1"), $this);?>>
      <td colspan="2"><?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/uploaded_files.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?></td>
    </tr>                                
    <?php endif; ?>


    <?php if ($this->_vars['categ']['id'] > 0): ?>
    <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd",'reset' => "1"), $this);?>>
      <td colspan="2" align="right"><a href="?action=info&do=copy&id=<?php echo $this->_vars['categ']['id']; ?>
&where=product" onclick="if(confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "copy_help"), $this);?>')) return true; else return false;"><i class="fa fa-copy"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "copy_object"), $this);?></a></td>
    </tr>                                
    <?php endif; ?>

        
  <tr>
    <td colspan="2" align="center"><input type="submit" name="save" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "save"), $this);?>" />
  <?php if ($this->_vars['categ']['id'] > 0): ?><input class="small" type="submit" name="del" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "delete_all"), $this);?>"  onclick="if(confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "really"), $this);?>')) return true; else return false;" /><?php endif; ?></td>
  </tr>
</table>
</form>
<?php endif; ?>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>