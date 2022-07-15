<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.numformat.php'); $this->register_modifier("numformat", "tpl_modifier_numformat");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0">
<?php if (! empty ( $_GET['lider'] )):  echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "best_sellers"), $this); if (! empty ( $this->_vars['products_qty'] )): ?> (<?php echo $this->_vars['products_qty']; ?>
)<?php endif; ?> <small><a href="?action=products&do=list_products"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "all",'case' => "lower"), $this);?></a></small>
<?php else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "offers_and_objects"), $this); if (! empty ( $this->_vars['products_qty'] )): ?> (<?php echo $this->_vars['products_qty']; ?>
)<?php endif;  if (! empty ( $this->_vars['cid'] )): ?>: <?php echo $this->_vars['cid']['title']; ?>
 
<a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['cid']['id']; ?>
"><i class="fa fa-pencil"></i></a> 
<?php if (! empty ( $this->_vars['cid']['url'] )): ?> <a href="<?php echo $this->_vars['cid']['url']; ?>
" target="_blank"><i class="fa fa-external-link"></i></a><?php endif; ?> 
<a href="?action=products&do=list_products&cid=<?php echo $this->_vars['cid']['id']; ?>
&lider=1"><i class="fa fa-thumbs-up" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "best_sellers"), $this);?>"></i></a>
<?php else: ?> <a href="?action=products&do=list_products&lider=1"><i class="fa fa-thumbs-up" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "best_sellers"), $this);?>"></i></a>
<?php endif;  endif; ?>
</h1>

<?php if (isset ( $_GET['updated'] )): ?>
  <blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "rows_updated"), $this);?>: <?php echo $_GET['updated']; ?>
</blockquote>
<?php endif; ?>

<?php if (isset ( $_GET['deleted'] )): ?>
  <blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "rows_deleted"), $this);?>: <?php echo $_GET['deleted']; ?>
</blockquote>
<?php endif; ?>

<table width="100%">
<tbody>
	<tr>
		<td nowrap><a href="?action=products&do=add<?php if (! empty ( $_GET['cid'] )): ?>&cid=<?php echo $_GET['cid'];  endif; ?>"><i class="fa fa-plus"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a>
		</td>
		
    <form method="get">
		<td align="right" nowrap><i class="fa fa-search" style="color:#8eaebe;"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "search_in_title"), $this);?>:</td>
		<td nowrap>
          <input type="hidden" name="action" value="products">
          <input type="hidden" name="do" value="list_products">
		  <?php if (! empty ( $_GET['cid'] )): ?>
		  <input type="hidden" name="cid" value="<?php echo $_GET['cid']; ?>
">
		  <?php endif; ?>
          <input type="text" size="10" name="q" value="<?php if (isset ( $_GET['q'] )):  echo $this->_run_modifier($_GET['q'], 'htmlspecialchars', 'PHP', 1);  endif; ?>">
          <button type="submit" class="small"><i class="fa fa-search"></i></button>
		  </td>
		<td align="right" nowrap><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "filter"), $this);?>:</td>
    </form>
    <form method="get">
    <td nowrap>
	
	
	
			<?php echo '<select style="width: 200px; word-break: break-all;" onChange="if(this.options[this.selectedIndex].value!=\'\'){window.location=this.options[this.selectedIndex].value}else{this.options[selectedIndex=0];}">'; ?>

				
				<option value="?action=products&do=list_products<?php if (! empty ( $_GET['q'] )): ?>&q=<?php echo $_GET['q'];  endif; ?>">- <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "all_pages"), $this);?></option>
				
			<?php if (! empty ( $this->_vars['site_vars']['_pages'] )): ?>
				<?php if (count((array)$this->_vars['site_vars']['_pages'])): foreach ((array)$this->_vars['site_vars']['_pages'] as $this->_vars['v']): ?>
						<?php if (! empty ( $this->_vars['v']['products'] )): ?>
						<option value="?action=products&do=list_products&cid=<?php echo $this->_vars['v']['id'];  if (! empty ( $_GET['q'] )): ?>&q=<?php echo $_GET['q'];  endif;  if (! empty ( $_GET['options'] )): ?>&options=1<?php endif; ?>"<?php if (isset ( $_GET['cid'] ) && $_GET['cid'] == $this->_vars['v']['id']): ?> selected="selected"<?php endif; ?>><?php if ($this->_vars['v']['level'] > 1): ?>
							<?php for($for1 = 1; ((1 < $this->_vars['v']['level']) ? ($for1 < $this->_vars['v']['level']) : ($for1 > $this->_vars['v']['level'])); $for1 += ((1 < $this->_vars['v']['level']) ? 1 : -1)):  $this->assign('current', $for1); ?> - <?php endfor; ?>
						<?php endif;  echo $this->_vars['v']['title']; ?>
 (<?php echo $this->_vars['v']['products']; ?>
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
	
	
			<?php if (isset ( $_GET['cid'] ) && empty ( $_GET['options'] )): ?>
				<a href="?action=products&do=list_products&cid=<?php echo $_GET['cid']; ?>
&options=1"><i class="fa fa-bars" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "edit_options"), $this);?>"></i></a>
			<?php elseif (! empty ( $_GET['options'] )): ?>
				<a href="?action=products&do=list_products&cid=<?php echo $_GET['cid']; ?>
"><i class="fa fa-shopping-cart" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "block_types",'key3' => "listProducts"), $this);?>"></i></a>
			<?php endif; ?>
    </td>
    </form>
  </tr>
</tbody>
</table>


<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/pages.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<?php if ($this->_run_modifier($this->_vars['products_list'], 'count', 'PHP', 0) > 0 && ! empty ( $_GET['cid'] ) && ! empty ( $_GET['options'] )): ?>

<form method=post name=form1>
	<input type="hidden" name="products_qty" value="<?php echo $this->_vars['products_qty']; ?>
" />
	
    <table width="100%" class="bordered">
      <tr>
        <th>#</th>
        <th><i class="fa fa-edit"></i></th>
        <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "title"), $this);?></th>
		
		<?php if (! empty ( $this->_vars['th_options'] )): ?>
			<?php if (count((array)$this->_vars['th_options'])): foreach ((array)$this->_vars['th_options'] as $this->_vars['v']): ?>
					<?php if (! empty ( $this->_vars['v']['g_value2'] )): ?>
						<th><a href="?action=products&do=option_group&id=<?php echo $this->_vars['v']['group_id']; ?>
" style="color:white;"><?php echo $this->_vars['v']['g_title']; ?>
</a><br><a href="?action=products&do=options&id=<?php echo $this->_vars['v']['id']; ?>
" style="color:white; text-decoration: underline;"><?php echo $this->_vars['v']['title'];  if (! empty ( $this->_vars['v']['after'] )): ?> (<?php echo $this->_vars['v']['after']; ?>
)<?php endif; ?></a><br><small>(<?php echo $this->_vars['v']['g_value1']; ?>
)</small><br><small>(<?php echo $this->_vars['v']['type']; ?>
)</small></th>
						<th><?php echo $this->_vars['v']['title']; ?>
<br><small>(<?php echo $this->_vars['v']['g_value2']; ?>
)</small><br><small>(<?php echo $this->_vars['v']['type']; ?>
)</small></th>
						<th><?php echo $this->_vars['v']['title']; ?>
<br><small>(<?php echo $this->_vars['v']['g_value3']; ?>
)</small><br><small>(<?php echo $this->_vars['v']['type']; ?>
)</small></th>
					<?php else: ?>
						<th><a href="?action=products&do=option_group&id=<?php echo $this->_vars['v']['group_id']; ?>
" style="color:white;"><?php echo $this->_vars['v']['g_title']; ?>
</a><br><a href="?action=products&do=options&id=<?php echo $this->_vars['v']['id']; ?>
" style="color:white; text-decoration: underline;"><?php echo $this->_vars['v']['title'];  if (! empty ( $this->_vars['v']['after'] )): ?> (<?php echo $this->_vars['v']['after']; ?>
)<?php endif; ?></a><br><small>(<?php echo $this->_vars['v']['type']; ?>
)</small></th>
					<?php endif; ?>
			<?php endforeach; endif; ?>
		<?php endif; ?>

        <th><i class="fa fa-external-link"></i></th>
      </tr>

      <?php if (count((array)$this->_vars['products_list'])): foreach ((array)$this->_vars['products_list'] as $this->_vars['key'] => $this->_vars['value']): ?>
        <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
          <td align=center valign="top"><a href="?action=products&do=edit&id=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_vars['value']['id']; ?>
</a></td>
          <td align=center valign="top"><a href="?action=products&do=edit&id=<?php echo $this->_vars['value']['id']; ?>
"><i class="fa fa-pencil"></i></a></td>
          <td valign="top"><a href="?action=products&do=edit&id=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_vars['value']['name']; ?>
</a></td>
					
			<?php if (! empty ( $this->_vars['th_options'] )): ?>
				<?php if (count((array)$this->_vars['th_options'])): foreach ((array)$this->_vars['th_options'] as $this->_vars['v']): ?>
				
					<?php $this->assign('ids', $this->_vars['value']['id']); ?>
					<?php $this->assign('vids', $this->_vars['v']['id']); ?>
					<?php $this->assign('opts', $this->_vars['u_options'][$this->_vars['ids']]); ?>
					<?php $this->assign('opt', $this->_vars['opts'][$this->_vars['vids']]); ?>
					
					<?php if (isset ( $this->_vars['opt']['id'] )): ?>
						<?php $this->assign('value_id', $this->_vars['opt']['id']); ?>
						<?php $this->assign('value1', $this->_vars['opt']['value']); ?>
						<?php $this->assign('value2', $this->_vars['opt']['value2']); ?>
						<?php $this->assign('value3', $this->_vars['opt']['value3']); ?>
					<?php else: ?>
						<?php $this->assign('value_id', 0); ?>
						<?php $this->assign('value1', ""); ?>
						<?php $this->assign('value2', ""); ?>
						<?php $this->assign('value3', ""); ?>
					<?php endif; ?>
					
				
					<?php if (! empty ( $this->_vars['v']['g_value2'] )): ?>
						<td valign="top"><?php echo get_option_field(array('id' => $this->_vars['v']['id'],'value_id' => $this->_vars['value_id'],'type' => $this->_vars['v']['type'],'if_select' => $this->_vars['v']['if_select'],'value' => $this->_vars['value1'],'product_id' => $this->_vars['value']['id'],'inc_product' => 1,'title' => $this->_vars['v']['title'],'field' => "value",'value2' => $this->_vars['value2'],'value3' => $this->_vars['value3']), $this);?>
						</td>
						<td valign="top"><?php echo get_option_field(array('id' => $this->_vars['v']['id'],'value_id' => $this->_vars['value_id'],'type' => $this->_vars['v']['type'],'if_select' => $this->_vars['v']['if_select'],'value' => $this->_vars['value2'],'product_id' => $this->_vars['value']['id'],'inc_product' => 1,'title' => $this->_vars['v']['title'],'field' => "value2"), $this);?>
						</td>
						<td valign="top"><?php echo get_option_field(array('id' => $this->_vars['v']['id'],'value_id' => $this->_vars['value_id'],'type' => $this->_vars['v']['type'],'if_select' => $this->_vars['v']['if_select'],'value' => $this->_vars['value3'],'product_id' => $this->_vars['value']['id'],'inc_product' => 1,'title' => $this->_vars['v']['title'],'field' => "value3"), $this);?></td>
					<?php else: ?>
						<td valign="top"><?php echo get_option_field(array('id' => $this->_vars['v']['id'],'value_id' => $this->_vars['value_id'],'type' => $this->_vars['v']['type'],'if_select' => $this->_vars['v']['if_select'],'value' => $this->_vars['value1'],'product_id' => $this->_vars['value']['id'],'inc_product' => 1,'title' => $this->_vars['v']['title'],'field' => "value",'value2' => "",'value3' => ""), $this);?></td>
					<?php endif; ?>
					
					
				<?php endforeach; endif; ?>
			<?php endif; ?>

          <td valign="top" class="center">
			<small><?php if (! empty ( $this->_vars['value']['site_url'] )): ?>
				<a href="<?php echo $this->_vars['value']['site_url']; ?>
/<?php echo $this->_vars['value']['alias'];  echo constant('URL_END');  if (empty ( $this->_vars['value']['active'] )): ?>?debug=<?php echo $this->_vars['value']['site_id'];  endif; ?>"><i class="fa fa-external-link"></i></a>			
			<?php else: ?>-<?php endif; ?></small>
		  
          </td>
        </tr>
      <?php endforeach; endif; ?>

      
    </table>
    
	
	<p style="text-align:center;"><input type=submit name=update value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "update"), $this);?>" onclick="if(confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "are_u_sure"), $this);?>')) return true; else return false;"></p>
	
  </form>



<?php elseif (! empty ( $this->_vars['products_list'] )): ?>


	<?php if (! empty ( $_GET['lider'] )): ?>
	
		<table width="80%">
			<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
				<th>ID</th>
				<th width="30"><i class="fa fa-pencil"></i></th>
				<th width="30%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "title"), $this);?></th>
				<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "orders",'key3' => "title"), $this);?></th>
				<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "price"), $this);?></th>
				<th><i class="fa fa-eye" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "active"), $this);?>"></i></th>
				<th><i class="fa fa-shopping-cart" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "active_to_buy"), $this);?>"></i></td>
				<th><i class="fa fa-external-link" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "link_to_website"), $this);?>"></i></th>
			</tr>
		<?php if (count((array)$this->_vars['products_list'])): foreach ((array)$this->_vars['products_list'] as $this->_vars['v']): ?>
			<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
				<td class="center"><a href="?action=products&do=edit&id=<?php echo $this->_vars['v']['id']; ?>
"><?php echo $this->_vars['v']['id']; ?>
</a></td>
				<td class="center"><a href="?action=products&do=edit&id=<?php echo $this->_vars['v']['id']; ?>
"><i class="fa fa-pencil"></i></a></td>
				<td><?php echo $this->_vars['v']['name']; ?>
</td>
				<td class="center"><a href="?action=products&do=edit&id=<?php echo $this->_vars['v']['id']; ?>
&orders=1"><?php echo $this->_vars['v']['cnt']; ?>
</a></td>
				<td class="right"><?php echo $this->_run_modifier($this->_vars['v']['price'], 'numformat', 'plugin', 1); ?>
 <?php if ($this->_vars['value']['currency'] == "usd"): ?><i class="fa fa-usd"></i><?php elseif ($this->_vars['value']['currency'] == "euro"): ?><i class="fa fa-euro"></i><?php else: ?><i class="fa fa-rub"></i><?php endif; ?></td>
				<td class="center"><?php if (! empty ( $this->_vars['v']['active'] )): ?><i class="fa fa-check"></i><?php endif; ?></td>
				<td class="center"><?php if (! empty ( $this->_vars['v']['accept_orders'] )): ?><i class="fa fa-check"></i><?php endif; ?></td>
				<td class="center">
				<?php if (! empty ( $this->_vars['v']['site_url_qty'] ) && $this->_vars['v']['site_url_qty'] > 1): ?>
					<span class="red">*</span>
				<?php else: ?>
					<small><a href="<?php echo $this->_vars['v']['site_url']; ?>
/<?php echo $this->_vars['v']['alias'];  echo constant('URL_END');  if (empty ( $this->_vars['v']['active'] )): ?>?debug=<?php echo $this->_vars['v']['site_id'];  endif; ?>" target="_blank"><i class="fa fa-external-link"></i></a></small>
				<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; endif; ?>
		</table>
		
	<?php else: ?>


  <form method=post name=form1>
  
    <table width="100%" class="">
      <tr>
        <th>#</th>
        <th><i class="fa fa-edit"></i></th>
        <th width=50><i class="fa fa-check" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "active"), $this);?>"></i> <INPUT onclick="CheckAll(this,'active[]')" type=checkbox></th>
        <th width=50><i class="fa fa-shopping-cart" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "active_to_buy"), $this);?>"></i> <INPUT onclick="CheckAll(this,'accept_orders[]')" type=checkbox></th>
        <th width="25%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "title"), $this);?></th>
        <th width="20%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "synonim"), $this);?></th>
        <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "price"), $this);?></th>
        <th><i class="fa fa-usd"></i></th>
        <th>YML</th>
        <th width=50><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "qty_short"), $this);?></th>
        <th><i class="fa fa-bullhorn" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "new"), $this);?>"></i> <INPUT onclick="CheckAll(this,'f_new[]')" type="checkbox"></th>
        <th><i class="fa fa-thumbs-up" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "special_products"), $this);?>"></i> <INPUT onclick="CheckAll(this,'f_spec[]')" type="checkbox"></th>
        <th><i class="fa fa-camera" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "qty_pics"), $this);?>"></i></th>
        <th><i class="fa fa-external-link"></i></th>
        <th width=50><i class="fa fa-trash" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "delete"), $this);?>"></i> <INPUT onclick="CheckAll(this,'del[]')" type="checkbox"></th>
      </tr>

      <?php if (count((array)$this->_vars['products_list'])): foreach ((array)$this->_vars['products_list'] as $this->_vars['key'] => $this->_vars['value']): ?>
        <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
          <td align=center><a href="?action=products&do=edit&id=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_vars['value']['id']; ?>
</a></td>
          <td align=center nowrap><a href="?action=products&do=edit&id=<?php echo $this->_vars['value']['id']; ?>
"><i class="fa fa-pencil<?php if (empty ( $this->_vars['value']['memo'] ) && empty ( $this->_vars['value']['memo_short'] )): ?> red
			<?php elseif (empty ( $this->_vars['value']['memo'] ) || empty ( $this->_vars['value']['memo_short'] )): ?> grey<?php endif; ?>"></i></a> <?php if ($this->_run_modifier($this->_vars['value']['date_insert'], 'strtotime', 'PHP', 1) > time()): ?>
					<i class="fa fa-clock-o red" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "not_shown_yet"), $this);?>"></i>
				<?php endif; ?></td>
          <td align=center><input type=checkbox name="active[]" value="<?php echo $this->_vars['value']['id']; ?>
"<?php if ($this->_vars['value']['active']): ?> checked="checked"<?php endif; ?> /></td>
          <td width=50 align=center><input type=checkbox name="accept_orders[]" value="<?php echo $this->_vars['value']['id']; ?>
"<?php if ($this->_vars['value']['accept_orders']): ?> checked="checked"<?php endif; ?> /></td>
          <td><input type=text style="width:100%;" name="goods[<?php echo $this->_vars['value']['id']; ?>
][name]" value="<?php echo $this->_run_modifier($this->_vars['value']['name'], 'htmlspecialchars', 'PHP', 1); ?>
"></td>
          <td><input type=text style="width:100%;" name="goods[<?php echo $this->_vars['value']['id']; ?>
][alias]" value="<?php echo $this->_run_modifier($this->_vars['value']['alias'], 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
          
          <td align=right><input style="text-align: right;" type="text" style="width:100%;" name="goods[<?php echo $this->_vars['value']['id']; ?>
][price]" value="<?php echo $this->_vars['value']['price']; ?>
" /></td>
          <td align=center><?php if ($this->_vars['value']['currency'] == "usd"): ?><i class="fa fa-usd"></i><?php elseif ($this->_vars['value']['currency'] == "euro"): ?><i class="fa fa-euro"></i><?php else: ?><i class="fa fa-rub"></i><?php endif; ?></td>

		  <td align=center><input style="text-align: center;" type="text" size="2" name="goods[<?php echo $this->_vars['value']['id']; ?>
][bid_ya]" value="<?php echo $this->_vars['value']['bid_ya']; ?>
" /></td>
		  
		  
		  
          <td align=center><?php echo $this->_vars['value']['total_qty']; ?>
</td>

          <td width=50 align=center><input type=checkbox name="f_new[]" value="<?php echo $this->_vars['value']['id']; ?>
"<?php if ($this->_vars['value']['f_new'] == 1): ?> checked="checked"<?php endif; ?> /></td>
          <td width=50 align=center><input type=checkbox name="f_spec[]" value="<?php echo $this->_vars['value']['id']; ?>
"<?php if ($this->_vars['value']['f_spec'] == 1): ?> checked="checked"<?php endif; ?> /></td>
          <td width=50 align=center><?php if ($this->_vars['value']['fotos'] == 0): ?><span style="color:red;">0</span><?php else:  echo $this->_vars['value']['fotos'];  endif; ?></td>
          <td align="center">
          
          <?php if (! empty ( $this->_vars['cid'] )): ?>
            
				<?php if (! empty ( $this->_vars['value']['site_url_qty'] ) && $this->_vars['value']['site_url_qty'] > 1): ?>
					<span class="red">*</span>
				<?php else: ?>
					<small><a href="<?php echo $this->_vars['value']['site_url']; ?>
/<?php echo $this->_vars['value']['alias'];  echo constant('URL_END');  if (empty ( $this->_vars['value']['active'] )): ?>?debug=<?php echo $this->_vars['value']['site_id'];  endif; ?>" target="_blank"><i class="fa fa-external-link"></i></a></small>
				<?php endif; ?>
			<?php elseif (! empty ( $this->_vars['value']['site_url'] )): ?>
			
				<?php if (! empty ( $this->_vars['value']['site_url_qty'] ) && $this->_vars['value']['site_url_qty'] > 1): ?>
					<span class="red">*</span>
				<?php else: ?>
					<small><a href="<?php echo $this->_vars['value']['site_url']; ?>
/<?php echo $this->_vars['value']['alias'];  echo constant('URL_END');  if (empty ( $this->_vars['value']['active'] )): ?>?debug=<?php echo $this->_vars['value']['site_id'];  endif; ?>" target="_blank"><i class="fa fa-external-link"></i></a></small>
				<?php endif; ?>
		
          <?php endif; ?>
          </td>
          <td width=50 align=center><input  type="checkbox" name="del[]" value="<?php echo $this->_vars['value']['id']; ?>
"></td>
        </tr>
      <?php endforeach; endif; ?>

      
    </table>
   <p><input type=submit name=update value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "update"), $this);?>" 
   onclick="if(confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "are_u_sure"), $this);?>')) return true; else return false;" /></p> 
  </form>
  <?php endif; ?>

<?php else: ?>
  <p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "list_empty"), $this);?> <a href="?action=products&do=add"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a></p>
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