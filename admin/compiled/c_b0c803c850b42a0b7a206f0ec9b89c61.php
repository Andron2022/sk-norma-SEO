<?php  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "templates"), $this);?></h1>
<?php if (! empty ( $_GET['folder'] )): ?>
<ul>
	<li><a href="?action=settings&do=tpl"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "templates"), $this);?></a></li>
	<li><a href="?action=settings&do=tpl&folder=<?php echo $this->_vars['folder']; ?>
"><?php echo $this->_vars['folder']; ?>
</a></li>
</ul>
<?php endif; ?>
              
<p><b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "tpl_available"), $this);?></b>

<ul>

<?php if (count((array)$this->_vars['templates'])): foreach ((array)$this->_vars['templates'] as $this->_vars['key'] => $this->_vars['value']): ?>
  <?php if ($this->_vars['value']['link']): ?>
    <li><a href="<?php echo $this->_vars['value']['link']; ?>
"><?php if ($this->_vars['value']['img']): ?><i class="fa fa-folder-open"></i> <?php echo $this->_vars['value']['file'];  endif; ?></a>
<br><a href="javascript: ShowHide('block-<?php echo $this->_vars['key']; ?>
')" style="border-bottom: 1px dashed blue; margin-left:20px;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "open"), $this);?></a>
<div style="display: none;" id="block-<?php echo $this->_vars['key']; ?>
">
		<ul>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "home"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=<?php echo $this->_vars['value']['file']; ?>
&doc=index.html">index.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "header"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=<?php echo $this->_vars['value']['file']; ?>
&doc=header.html">header.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "footer"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=<?php echo $this->_vars['value']['file']; ?>
&doc=footer.html">footer.html</a></li>
			<br>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "categ"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=<?php echo $this->_vars['value']['file']; ?>
&doc=category.html">category.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "pub"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=<?php echo $this->_vars['value']['file']; ?>
&doc=pub.html">pub.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "product"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=<?php echo $this->_vars['value']['file']; ?>
&doc=product.html">product.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "feedback"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=<?php echo $this->_vars['value']['file']; ?>
&doc=feedback.html">feedback.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "blank_page"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=<?php echo $this->_vars['value']['file']; ?>
&doc=blank.html">blank.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "categ"), $this);?> 404 - <a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=404.html">404.html</a></li>
			<br>
			<li><b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "for_homepage"), $this);?></b></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "block_types",'key3' => "lastProducts"), $this);?> 
			(<a href="?action=settings&do=site_vars&mode=sys&site_id=-1&q=sys_qty_last_products&redirect=1"><?php if (isset ( $this->_vars['site_vars']['sys_qty_last_products'] )):  echo $this->_vars['site_vars']['sys_qty_last_products'];  else: ?>?<?php endif; ?></a>) - <a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=index_last_products.html">index_last_products.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "block_types",'key3' => "lastPubs"), $this);?> (<a href="?action=settings&do=site_vars&mode=sys&site_id=-1&q=sys_qty_last_pubs&redirect=1"><?php if (isset ( $this->_vars['site_vars']['sys_qty_last_pubs'] )):  echo $this->_vars['site_vars']['sys_qty_last_pubs'];  else: ?>?<?php endif; ?></a>) - <a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=index_last_pubs.html">index_last_pubs.html</a></li>
			<br>
			<li><b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "extended"), $this);?></b></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "block_types",'key3' => "lastPubs"), $this);?> <?php if (! empty ( $this->_vars['site_vars']['sys_qty_last_pubs'] )): ?>(<a href="?action=settings&do=site_vars&mode=sys&site_id=-1&q=sys_qty_last_pubs&redirect=1"><?php if (isset ( $this->_vars['site_vars']['sys_qty_last_pubs'] )):  echo $this->_vars['site_vars']['sys_qty_last_pubs'];  else: ?>?<?php endif; ?></a>) <?php endif; ?>- <a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=last_pubs.html">last_pubs.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "block_types",'key3' => "listPubs"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=include_pubs.html">include_pubs.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "sidebar_last_pubs"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=sidebar_last_pubs.html">sidebar_last_pubs.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "block_types",'key3' => "listProducts"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=include_products.html">include_products.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "important_pubs"), $this);?> <?php if (! empty ( $this->_vars['site_vars']['sys_qty_starred_pubs'] )): ?>(<?php echo $this->_vars['site_vars']['sys_qty_starred_pubs']; ?>
) <?php endif; ?>	- <?php if (! empty ( $this->_vars['site_vars']['sys_skip_starred_pubs'] )): ?><span style="color:red;font-weight:bold;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "off"), $this);?></span> <?php endif; ?><a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=include_starred_pubs.html">include_starred_pubs.html</a>
			<br>
			<u><i><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fav",'key3' => "title"), $this);?>:</i></u> <i><a href="?action=settings&do=site_vars&mode=sys&site_id=-1&q=sys_starred_pubs_title&redirect=1"><?php if (isset ( $this->_vars['site_vars']['sys_starred_pubs_title'] )):  echo $this->_vars['site_vars']['sys_starred_pubs_title'];  else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "not_set"), $this); endif; ?></a></i></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "conn_offers"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=include_connected_products.html">include_connected_products.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "comments"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=include_comments.html">include_comments.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "breadcrumbs"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=breadcrumbs.html">breadcrumbs.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "pagination"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=pages.html">pages.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "sidebar_spec"), $this);?> (<a href="?action=settings&do=site_vars&mode=sys&site_id=-1&q=sys_qty_spec_products&redirect=1"><?php if (isset ( $this->_vars['site_vars']['sys_qty_spec_products'] )):  echo $this->_vars['site_vars']['sys_qty_spec_products'];  else: ?>?<?php endif; ?></a>) - <a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=sidebar_spec_products.html">sidebar_spec_products.html</a></li>

			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "viewed_products"), $this);?> (<a href="?action=settings&do=site_vars&mode=sys&site_id=-1&q=sys_qty_visited_products&redirect=1"><?php if (isset ( $this->_vars['site_vars']['sys_qty_visited_products'] )):  echo $this->_vars['site_vars']['sys_qty_visited_products'];  else: ?>?<?php endif; ?></a>) - <?php if (! empty ( $this->_vars['site_vars']['sys_skip_visited_products'] )): ?><span style="color:red;font-weight:bold;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "off"), $this);?></span> <?php endif; ?><a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=visited_products.html">visited_products.html</a></li>
			
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "viewed_pubs"), $this);?> (<a href="?action=settings&do=site_vars&mode=sys&site_id=-1&q=sys_qty_visited_pubs&redirect=1"><?php if (isset ( $this->_vars['site_vars']['sys_qty_visited_pubs'] )):  echo $this->_vars['site_vars']['sys_qty_visited_pubs'];  else: ?>?<?php endif; ?></a>) - <?php if (! empty ( $this->_vars['site_vars']['sys_skip_visited_pubs'] )): ?><span style="color:red;font-weight:bold;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "off"), $this);?></span> <?php endif; ?><a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=visited_pubs.html">visited_pubs.html</a></li>
			
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "sitemap"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=sitemap.html">sitemap.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "search"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=search.html">search.html</a></li>
			<br>
			<li><b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "shop"), $this);?></b></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "cart"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=basket.html">basket.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "sidebar_cart"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=basket_sidebar.html">basket_sidebar.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "do_order"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=order.html">order.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "new_order"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=order_add.html">order_add.html</a></li>
			<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "new_request"), $this);?> - <a href="?action=settings&do=tpl&folder=&folder=../tpl/<?php echo $this->_vars['value']['file']; ?>
/pages&doc=feedback.html">feedback.html</a></li>
		</ul>
</div>
	</li>
  <?php else: ?>
    <li><?php if ($this->_vars['value']['img']): ?><img src='<?php echo $this->_vars['value']['img']; ?>
' border='0' /> <?php endif;  echo $this->_vars['value']['file']; ?>
</li>
  <?php endif;  endforeach; endif; ?>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>