<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "block_types",'key3' => "listPubs"), $this); if (! empty ( $this->_vars['all_pubs_qty'] )): ?> (<?php echo $this->_vars['all_pubs_qty']; ?>
)<?php endif;  if (! empty ( $this->_vars['cid'] )): ?>: <?php echo $this->_vars['cid']['title']; ?>
 
<a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['cid']['id']; ?>
"><i class="fa fa-pencil"></i></a> 
<?php if (! empty ( $this->_vars['cid']['url'] )): ?> <a href="<?php echo $this->_vars['cid']['url']; ?>
" target="_blank"><i class="fa fa-external-link"></i></a><?php endif;  endif; ?></h1>

<?php if (isset ( $_GET['updated'] )): ?>
  <table width="80%"><tr><td><blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "rows_updated"), $this);?>: <?php echo $_GET['updated']; ?>
</blockquote></td></tr></table>
<?php endif; ?>

<?php if (isset ( $_GET['deleted'] )): ?>
  <table width="80%"><tr><td><blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "rows_deleted"), $this);?>: <?php echo $_GET['deleted']; ?>
</blockquote></td></tr></table>
<?php endif; ?>

<?php if (! empty ( $this->_vars['all_pubs_qty'] ) || ! empty ( $_GET['q'] ) || ! empty ( $_GET['cid'] )):  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("info/pub_filter.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
  endif; ?>

<?php if ($this->_run_modifier($this->_vars['pubs_list'], 'count', 'PHP', 0) > 3):  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/pages.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
  endif; ?>

<?php if ($this->_run_modifier($this->_vars['pubs_list'], 'count', 'PHP', 0) > 0 && ! empty ( $_GET['cid'] ) && ! empty ( $_GET['options'] )): ?>

	<?php if (empty ( $this->_vars['th_options'] )): ?>

<table width="100%"><tr><td><blockquote>	
<p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "opt_not_set"), $this);?></p>
<p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "opt_not_set2"), $this);?></p>	
</blockquote></td></tr></table>
	
	<?php else: ?>

<form method="post" name="mainform">
	<input type="hidden" name="pubs_qty" value="<?php echo $this->_vars['pubs_qty']; ?>
" />
  <table width="100%" class="bordered">
    <tr>
     <th>#</th>
     <th><i class="fa fa-edit"></i></th>
     <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "name"), $this);?></th>
	 
	 <?php if (! empty ( $this->_vars['th_options'] )): ?>
			<?php if (count((array)$this->_vars['th_options'])): foreach ((array)$this->_vars['th_options'] as $this->_vars['v']): ?>
					<?php if (! empty ( $this->_vars['v']['g_value2'] )): ?>
						<th><a href="?action=products&do=option_group&id=<?php echo $this->_vars['v']['group_id']; ?>
"><?php echo $this->_vars['v']['g_title']; ?>
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
"><?php echo $this->_vars['v']['g_title']; ?>
</a><br><a href="?action=products&do=options&id=<?php echo $this->_vars['v']['id']; ?>
" style="text-decoration: underline;"><?php echo $this->_vars['v']['title'];  if (! empty ( $this->_vars['v']['after'] )): ?> (<?php echo $this->_vars['v']['after']; ?>
)<?php endif; ?></a><br><small>(<?php echo $this->_vars['v']['type']; ?>
)</small></th>
					<?php endif; ?>
			<?php endforeach; endif; ?>
		<?php endif; ?>
		<th><i class="fa fa-external-link"></i></th>
    </tr>
	
	
	<?php if (count((array)$this->_vars['pubs_list'])): foreach ((array)$this->_vars['pubs_list'] as $this->_vars['key'] => $this->_vars['value']): ?>
        <tr <?php echo tpl_function_cycle(array('values' => " ,class=odd"), $this);?>>
          <td align=center valign="top"><a href="?action=info&do=edit_publication&id=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_vars['value']['id']; ?>
</a></td>
          <td align=center valign="top"><a href="?action=info&do=edit_publication&id=<?php echo $this->_vars['value']['id']; ?>
"><i class="fa fa-pencil"></i></a></td>
          <td valign="top"><?php echo $this->_vars['value']['name']; ?>
</td>
					
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

          <td valign="top" align="center">
			<small><?php if (! empty ( $this->_vars['value']['site_url'] )): ?>
				<a href="<?php echo $this->_vars['value']['site_url']; ?>
/<?php echo $this->_vars['value']['alias'];  echo constant('URL_END');  if (empty ( $this->_vars['value']['active'] )): ?>?debug=<?php echo $this->_vars['value']['site_id'];  endif; ?>" target="_blank"><i class="fa fa-external-link"></i></a>
			<?php else: ?>-<?php endif; ?></small>
		  
          </td>
        </tr>
      <?php endforeach; endif; ?>
	
  </table>
  
  <p style="text-align:center;"><input type="submit" name="update" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "update"), $this);?>" onclick="return confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "are_u_update"), $this);?>')" /></p>
  
  
  
</form>
<?php endif; ?>

<?php elseif ($this->_run_modifier($this->_vars['pubs_list'], 'count', 'PHP', 0) > 0): ?>
<form method="post" name="mainform">
  <table width="100%">
    <tr>
     <th>#</th>
     <th><i class="fa fa-edit"></i></th>
     <th><i class="fa fa-check" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "show_on_website"), $this);?>"></i> <input type="checkbox" value="1" onclick="CheckAll(this,'active[]')"></th>
     <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "name"), $this);?></th>
     <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "url"), $this);?></th>
     <th><i class="fa fa-external-link"></i></th>
     <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "categ"), $this);?></th>
     <th><i class="fa fa-camera" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "qty_pics"), $this);?>"></i></th>
     <th><i class="fa fa-bullhorn" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "qty_views"), $this);?>"></i></th>
     <th><i class="fa fa-comments" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "qty_comments"), $this);?>"></i></th>
     <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "date"), $this);?></th>
     <th><i class="fa fa-trash-o" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "del_selected"), $this);?>"></i><input type="checkbox" value="1" onclick="CheckAll(this,'del[]')"></th>
    </tr>
    
    <?php if (count((array)$this->_vars['pubs_list'])): foreach ((array)$this->_vars['pubs_list'] as $this->_vars['key'] => $this->_vars['value']): ?>
      <tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
        <td><a href="?action=info&do=edit_publication&id=<?php echo $this->_vars['value']['id']; ?>
" ><?php echo $this->_vars['value']['id']; ?>
</a></td>
        <td align="center" nowrap>
		<a href="?action=info&do=edit_publication&id=<?php echo $this->_vars['value']['id']; ?>
" ><?php if (! empty ( $this->_vars['value']['f_spec'] )): ?><i class="fa fa-star"></i><?php else: ?><i class="fa fa-pencil"></i><?php endif; ?></a>
		
		<?php if ($this->_run_modifier($this->_vars['value']['date_insert'], 'strtotime', 'PHP', 1) > time()): ?>
					<i class="fa fa-clock-o red" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "not_shown_yet"), $this);?>"></i>
				<?php endif; ?>
		</td>
        <td align="center"><input name="active[]" type="checkbox" value="<?php echo $this->_vars['value']['id']; ?>
" <?php if ($this->_vars['value']['active']): ?> checked="checked"<?php endif; ?> /></td>
        <td><input name="name[<?php echo $this->_vars['value']['id']; ?>
]" type="text" style="width:100%;" value="<?php echo $this->_run_modifier($this->_vars['value']['name'], 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
        <td><input name="alias[<?php echo $this->_vars['value']['id']; ?>
]" type="text" style="width:100%;" value="<?php echo $this->_run_modifier($this->_vars['value']['alias'], 'htmlspecialchars', 'PHP', 1); ?>
" /></td>
        <td align="center"><?php if (! empty ( $this->_vars['value']['site_url'] )): ?><small><a href="<?php echo $this->_vars['value']['site_url']; ?>
/<?php echo $this->_vars['value']['alias']; ?>
/<?php if (empty ( $this->_vars['value']['active'] )): ?>?debug=<?php echo $this->_vars['value']['site_id'];  endif; ?>" target="_blank"><i class="fa fa-external-link"></i></a></small><?php endif; ?>
		</td>
        <td><?php if (! empty ( $this->_vars['value']['categ1_id'] )): ?><a href="?action=info&do=list_publications&cid=<?php echo $this->_vars['value']['categ1_id']; ?>
"><i class="fa fa-chevron-down"></i></a> <?php endif; ?>
		<?php echo $this->_vars['value']['categ1_title'];  if ($this->_vars['value']['categs_qty'] > 1): ?> (<?php echo $this->_vars['value']['categs_qty']; ?>
)<?php endif; ?></td>
        <td align="center"><?php if ($this->_vars['value']['fotos'] == 0): ?><span style="color:red;">0</span><?php else:  echo $this->_vars['value']['fotos'];  endif; ?></td>
        <td align="center"><?php echo $this->_vars['value']['views']; ?>
</td>
        <td align="center"><?php echo $this->_vars['value']['comments_qty']; ?>
</td>
        <td align="center"><small><?php echo $this->_run_modifier($this->_vars['value']['date_insert'], 'date', 'plugin', 1, $this->_vars['site_vars']['site_date_format']); ?>
</small></td>
        <td align="center"><input name="del[]" type="checkbox" value="<?php echo $this->_vars['value']['id']; ?>
" /></td>
      </tr>
    <?php endforeach; endif; ?>

    <tr>
      <td colspan="13" align="right"><input type="submit" name="update" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "update"), $this);?>" onclick="return confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "are_u_sure"), $this);?>')" /></td>
    </tr>
  </table>
  
</form>
<?php else: ?>
<pre><p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "list_empty"), $this);?></p></pre>  
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