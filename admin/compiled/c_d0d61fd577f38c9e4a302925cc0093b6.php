<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.date_format.php'); $this->register_modifier("date_format", "tpl_modifier_date_format");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.math.php'); $this->register_function("math", "tpl_function_math");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0 mb-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "search"), $this);?></h1>


<?php if (! empty ( $this->_vars['search_area'] )): ?>
	<?php $this->assign('search_total', $this->_vars['num_items']);  else: ?>
	<?php echo tpl_function_math(array('equation' => "a+c+d",'a' => $this->_vars['num_products'],'c' => $this->_vars['num_publications'],'d' => $this->_vars['num_categories'],'assign' => "search_total"), $this); endif; ?>


<?php if (! empty ( $this->_vars['search_total'] )): ?>
	<table width="80%"><tr><td><blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "you_searched"), $this);?>: <b><?php echo $this->_vars['query']; ?>
</b>
	<br><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "found"), $this);?> 
	<?php if ($this->_vars['search_area']): ?>
		<?php if ($this->_vars['search_area'] == 'products'):  echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "found_offers"), $this);?>:<?php endif; ?>
		<?php if ($this->_vars['search_area'] == 'publications'):  echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "found_pubs"), $this);?>:<?php endif; ?>
		<?php if ($this->_vars['search_area'] == 'categories'):  echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "found_categs"), $this);?>:<?php endif; ?>
	<?php else: ?>
		<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "results"), $this);?>: 
	<?php endif; ?>
	<b><?php echo $this->_vars['search_total']; ?>
</b>
	</blockquote></td></tr></table>

	<h3><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "found_results"), $this);?>:</h3>

	<?php if ($this->_vars['search_area']): ?>
		<ul>
		<?php if (count((array)$this->_vars['items'])): foreach ((array)$this->_vars['items'] as $this->_vars['search_item']): ?>
			<?php if ($this->_vars['search_area'] == 'products'): ?>
				<li><a href="?action=products&do=edit&id=<?php echo $this->_vars['search_item']['id']; ?>
"><?php echo $this->_vars['search_item']['name']; ?>
 <?php echo $this->_vars['search_item']['name_short']; ?>
</a> <small><?php echo $this->_run_modifier($this->_vars['search_item']['date_insert'], 'date_format', 'plugin', 1, "%d.%m.%Y"); ?>
</small></li>
			<?php endif; ?>
			
			<?php if ($this->_vars['search_area'] == 'publications'): ?>
				<li><a href="?action=info&do=edit_publication&id=<?php echo $this->_vars['search_item']['id']; ?>
"><?php echo $this->_vars['search_item']['name']; ?>
</a> <small><?php echo $this->_run_modifier($this->_vars['search_item']['date_insert'], 'date_format', 'plugin', 1, "%d.%m.%Y"); ?>
</small></li>
			<?php endif; ?>
			
			<?php if ($this->_vars['search_area'] == 'categories'): ?>
				<li><a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['search_item']['id']; ?>
"><?php echo $this->_vars['search_item']['title']; ?>
</a> <small><?php echo $this->_run_modifier($this->_vars['search_item']['date_insert'], 'date_format', 'plugin', 1, "%d.%m.%Y"); ?>
</small></li>
			<?php endif; ?>
		<?php endforeach; endif; ?> 
		</ul>
		<?php if (! empty ( $this->_vars['total_pages'] ) && $this->_vars['total_pages'] > 1): ?>
			<b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "categs"), $this);?>:</b>
			<?php echo tpl_function_math(array('equation' => "a+1",'a' => $this->_vars['total_pages'],'assign' => "total_pages"), $this);?>
			
			<?php for($for1 = 1; ((1 < $this->_vars['total_pages']) ? ($for1 < $this->_vars['total_pages']) : ($for1 > $this->_vars['total_pages'])); $for1 += ((1 < $this->_vars['total_pages']) ? 1 : -1)):  $this->assign('i', $for1); ?>
				<?php if ($this->_vars['i'] != $this->_vars['current_page']): ?>
					<a href="?action=search&q=<?php echo $this->_vars['query']; ?>
&where=<?php echo $this->_vars['search_area']; ?>
&p=<?php echo $this->_vars['i']; ?>
"><?php echo $this->_vars['i']; ?>
</a>&nbsp;
				<?php else:  echo $this->_vars['i'];  endif; ?>
			<?php endfor; ?> 
		<br><?php endif; ?>
		
	<?php else: ?>
	
		<ul>
			<?php if ($this->_vars['num_products'] > 0): ?><li><b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "offers"), $this);?>:</b> <a href="?action=search&q=<?php echo $this->_vars['query']; ?>
&where=products"><?php echo $this->_vars['num_products']; ?>
</a></li> <?php endif; ?>
			
			<?php if ($this->_vars['num_publications'] > 0): ?><li><b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "publications"), $this);?>:</b> <a href="?action=search&q=<?php echo $this->_vars['query']; ?>
&where=publications"><?php echo $this->_vars['num_publications']; ?>
</a></li> <?php endif; ?>
			
			<?php if ($this->_vars['num_categories'] > 0): ?><li><b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "categs"), $this);?>:</b> <a href="?action=search&q=<?php echo $this->_vars['query']; ?>
&where=categories"><?php echo $this->_vars['num_categories']; ?>
</a></li> <?php endif; ?>
		</ul>
		
		<?php if ($this->_vars['num_products'] > 0): ?>
			<b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "offers_in_catalog"), $this);?></b>&nbsp;<small>(<a href="?action=search&q=<?php echo $this->_vars['query']; ?>
&where=products"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "total_views"), $this);?>: <?php echo $this->_vars['num_products']; ?>
</a>, <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "shown_last"), $this);?> <?php echo $this->_vars['search_frontpage']; ?>
)</small><br><br>
			<?php if (count((array)$this->_vars['search_results_products'])): foreach ((array)$this->_vars['search_results_products'] as $this->_vars['product']): ?>
				<li><a href="?action=products&do=edit&id=<?php echo $this->_vars['product']['id']; ?>
"><?php echo $this->_vars['product']['name']; ?>
 <?php echo $this->_vars['product']['name_short']; ?>
</a> <small><?php echo $this->_run_modifier($this->_vars['product']['date_insert'], 'date_format', 'plugin', 1, "%d.%m.%Y"); ?>
</small></li>
			<?php endforeach; endif; ?> 
			<br>
		<?php endif; ?>


		<?php if ($this->_vars['num_publications'] > 0): ?>
			<b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "result_pubs"), $this);?></b>&nbsp;<small>(<a href="?action=search&q=<?php echo $this->_vars['query']; ?>
&where=publications"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "total_views"), $this);?>: <?php echo $this->_vars['num_publications']; ?>
</a>, <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "shown_last"), $this);?> <?php echo $this->_vars['search_frontpage']; ?>
)</small><br><br>
			<?php if (count((array)$this->_vars['search_results_publications'])): foreach ((array)$this->_vars['search_results_publications'] as $this->_vars['publication']): ?>
				<li><a href="?action=info&do=edit_publication&id=<?php echo $this->_vars['publication']['id']; ?>
"><?php echo $this->_vars['publication']['name']; ?>
</a> <small><?php echo $this->_run_modifier($this->_vars['publication']['date_insert'], 'date_format', 'plugin', 1, "%d.%m.%Y"); ?>
</small></li>
			<?php endforeach; endif; ?> 
		<br><?php endif; ?>

		<?php if ($this->_vars['num_categories'] > 0): ?>
			<b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "result_categs"), $this);?></b>&nbsp;<small>(<a href="?action=search&q=<?php echo $this->_vars['query']; ?>
&where=categories"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "total_views"), $this);?>: <?php echo $this->_vars['num_categories']; ?>
</a>, <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "shown_last"), $this);?> <?php echo $this->_vars['search_frontpage']; ?>
)</small><br><br>
			<?php if (count((array)$this->_vars['search_results_categories'])): foreach ((array)$this->_vars['search_results_categories'] as $this->_vars['category']): ?>
				<li><a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['category']['id']; ?>
"><?php echo $this->_vars['category']['title']; ?>
</a> <small><?php echo $this->_run_modifier($this->_vars['category']['date_insert'], 'date_format', 'plugin', 1, "%d.%m.%Y"); ?>
</small></li>
			<?php endforeach; endif; ?> 
		<br><?php endif; ?>
	<?php endif; ?>

<?php else: ?>
	<table width="80%"><tr><td><blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "you_searched"), $this);?>: <b><?php echo $this->_vars['query']; ?>
</b><br>
	<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "nothing_found"), $this);?></td></tr></table>
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