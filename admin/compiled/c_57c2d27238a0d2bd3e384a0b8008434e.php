<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.date.php'); $this->register_modifier("date", "tpl_modifier_date");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.chunk.php'); $this->register_modifier("chunk", "tpl_modifier_chunk");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.escape.php'); $this->register_modifier("escape", "tpl_modifier_escape");  ?><table border=0 cellpadding=5 cellspacing=1 width=100%>
  <tr>
    <th><a href="?action=info"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "main_menu"), $this);?></a></th>
  </tr>
  <tr<?php if ($this->_vars['admin_vars']['uri']['do'] == "categories" || $this->_vars['admin_vars']['uri']['do'] == "add_category" || $this->_vars['admin_vars']['uri']['do'] == "edit_categ"): ?> class="odd"<?php endif; ?>>
    <td><i class="fa fa-folder-open" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i> <a href="?action=info&do=categories"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "pages"), $this);?></a> <a href="?action=info&do=edit_categ&id=0"><i class="fa fa-plus" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></a></td>
  </tr>
  
  <?php if (! empty ( $this->_vars['site_vars']['fav']['categs'] )): ?>
  <tr>
		<td>
		<?php if (count((array)$this->_vars['site_vars']['fav']['categs'])): foreach ((array)$this->_vars['site_vars']['fav']['categs'] as $this->_vars['k'] => $this->_vars['v']): ?>
			<?php if ($this->_vars['k'] > 0): ?><br><?php endif; ?><small><a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['v']['id']; ?>
" title="<?php if (! empty ( $this->_vars['v']['title3'] )):  echo $this->_run_modifier($this->_vars['v']['title3'], 'escape', 'plugin', 1); ?>
 / <?php endif;  if (! empty ( $this->_vars['v']['title2'] )):  echo $this->_run_modifier($this->_vars['v']['title2'], 'escape', 'plugin', 1); ?>
 / <?php endif;  echo $this->_run_modifier($this->_vars['v']['title'], 'escape', 'plugin', 1); ?>
"><i class="fa fa-star"></i> <?php if (! empty ( $this->_vars['v']['u_title'] )):  echo $this->_vars['v']['u_title'];  else:  echo $this->_vars['v']['title'];  endif; ?></a></small> 
			<?php if (! empty ( $this->_vars['v']['shop'] )): ?>
				 <small><a href="?action=products&do=add&cid=<?php echo $this->_vars['v']['id']; ?>
"><i class="fa fa-plus"></i></a></small>
			<?php else: ?>
				 <small><a href="?action=info&do=edit_publication&id=0&cid=<?php echo $this->_vars['v']['id']; ?>
"><i class="fa fa-plus"></i></a></small>
			<?php endif; ?>
		<?php endforeach; endif; ?>		
		</td>
  </tr>
  <?php endif; ?>
  
  
  <tr<?php if ($this->_vars['admin_vars']['uri']['do'] == "list_publications" || $this->_vars['admin_vars']['uri']['do'] == "add_publication" || $this->_vars['admin_vars']['uri']['do'] == "edit_publication"): ?> class="odd"<?php endif; ?>>
    <td><i class="fa fa-list" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i> <a href="?action=info&do=list_publications"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "publications"), $this);?></a> <a href="?action=info&do=edit_publication&id=0"><i class="fa fa-plus" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></a></td>
  </tr>

  <?php if ($this->_vars['admin_vars']['uri']['do'] == "list_publications" || ! empty ( $this->_vars['site_vars']['fav']['pubs'] )): ?>
  <tr>
    <td>
		<?php if (empty ( $this->_vars['site_vars']['fav']['pubs'] )): ?>
          <?php if (! isset ( $_GET['active'] )): ?>
            <b> - <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "all"), $this);?></b><br>
             - <a href="?action=info&do=list_publications&active=1"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "active"), $this);?></a><br>
             - <a href="?action=info&do=list_publications&active=0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "hidden"), $this);?></a>
          <?php elseif ($_GET['active'] == 1): ?>
             - <a href="?action=info&do=list_publications"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "all"), $this);?></a><br>
             <b>- <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "active"), $this);?></b><br>
             - <a href="?action=info&do=list_publications&active=0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "hidden"), $this);?></a>
          <?php else: ?>
             - <a href="?action=info&do=list_publications"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "all"), $this);?></a><br>
             - <a href="?action=info&do=list_publications&active=1"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "active"), $this);?></a><br>
             <b>- <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "hidden"), $this);?></b>
          <?php endif; ?>    
			<?php else: ?>
				<?php if (count((array)$this->_vars['site_vars']['fav']['pubs'])): foreach ((array)$this->_vars['site_vars']['fav']['pubs'] as $this->_vars['k'] => $this->_vars['v']): ?>
					<?php if ($this->_vars['k'] > 0): ?><br><?php endif; ?><small><a href="?action=info&do=edit_publication&id=<?php echo $this->_vars['v']['id']; ?>
"><i class="fa fa-star"></i> <?php echo $this->_vars['v']['title']; ?>
</a></small>
				<?php endforeach; endif; ?>				
			<?php endif; ?>
    </td>
  </tr>
  <?php endif; ?>


  <tr<?php if ($this->_vars['admin_vars']['uri']['action'] == "comments"): ?> class="odd"<?php endif; ?>>
    <td><i class="fa fa-comments" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i> <a href="?action=comments"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "comments"), $this);?></a> <?php if (! empty ( $this->_vars['site_vars']['moderate_comments'] )): ?><i class="fa fa-question-circle red" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "to_moderate"), $this);?>"></i><?php endif; ?></td>  
  </tr>

  
<?php if ($this->_vars['admin_vars']['shop'] == 1): ?>  
  <tr>
    <th><a href="?action=products"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "catalog"), $this);?></a></th>
  </tr>
  <tr<?php if ($this->_vars['admin_vars']['uri']['do'] == "list_products" || $this->_vars['admin_vars']['uri']['do'] == "add"): ?> class="odd"<?php endif; ?>>
    <td><a href="?action=products&do=list_products"><i class="fa fa-shopping-cart" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "items"), $this);?></a> <a href="?action=products&do=add"><i class="fa fa-plus" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i></a></td>
  </tr>
  <?php if (! empty ( $this->_vars['site_vars']['fav']['products'] )): ?>
  <tr>
	<td>
		<?php if (count((array)$this->_vars['site_vars']['fav']['products'])): foreach ((array)$this->_vars['site_vars']['fav']['products'] as $this->_vars['k'] => $this->_vars['v']): ?>
			<?php if ($this->_vars['k'] > 0): ?><br><?php endif; ?><small><a href="?action=products&do=edit&id=<?php echo $this->_vars['v']['id']; ?>
"><i class="fa fa-star"></i> <?php echo $this->_vars['v']['title']; ?>
</a></small>
		<?php endforeach; endif; ?>
	</td>
  </tr>
  <?php endif; ?>
  
  <tr<?php if ($this->_vars['admin_vars']['uri']['do'] == "options" || $this->_vars['admin_vars']['uri']['do'] == "add_option" || $this->_vars['admin_vars']['uri']['do'] == "option_group" || $this->_vars['admin_vars']['uri']['do'] == "add_option_group"): ?> class="odd"<?php endif; ?>>
    <td><a href="?action=products&do=options"><i class="fa fa-filter" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "options"), $this);?></a> <small><a href="?action=products&do=option_group"><i class="fa fa-bars" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "opt_groups"), $this);?>"></i></a></small></td>    
  </tr>

  <tr<?php if ($this->_vars['admin_vars']['uri']['action'] == "orders"): ?> class="odd"<?php endif; ?>>
    <td><a href="?action=orders"><b><i class="fa fa-phone-square" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i></b> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "orders"), $this);?></a> <a href="?action=orders&id=0"><i class="fa fa-plus" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></a></td>    
  </tr>
  
  <?php if (! empty ( $this->_vars['site_vars']['fav']['orders'] )): ?>
  <tr>
	<td>
		<?php if (count((array)$this->_vars['site_vars']['fav']['orders'])): foreach ((array)$this->_vars['site_vars']['fav']['orders'] as $this->_vars['k'] => $this->_vars['v']): ?>
			<?php if ($this->_vars['k'] > 0): ?><br><?php endif; ?><small><a href="?action=orders&id=<?php echo $this->_vars['v']['id']; ?>
"><i class="fa fa-star"></i> <?php echo $this->_vars['v']['site_id']; ?>
-<?php echo $this->_run_modifier($this->_vars['v']['order_id'], 'chunk', 'plugin', 1, 4, "-"); ?>
</a></small>
		<?php endforeach; endif; ?>
	</td>
  </tr>
  <?php endif; ?>
  
  
  <tr<?php if ($this->_vars['admin_vars']['uri']['action'] == "feedback"): ?> class="odd"<?php endif; ?>>
    <td><i class="fa fa-envelope" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i> <a href="?action=feedback"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "requests"), $this);?></a></td>
  </tr>

  <?php if (! empty ( $this->_vars['site_vars']['fav']['fb'] )): ?>
  <tr>
	<td>
		<?php if (count((array)$this->_vars['site_vars']['fav']['fb'])): foreach ((array)$this->_vars['site_vars']['fav']['fb'] as $this->_vars['k'] => $this->_vars['v']): ?>
			<?php if ($this->_vars['k'] > 0): ?><br><?php endif; ?><small><a href="?action=feedback&id=<?php echo $this->_vars['v']['id']; ?>
"><i class="fa fa-star"></i> <?php echo $this->_vars['v']['ticket']; ?>
</a></small>
		<?php endforeach; endif; ?>
	</td>
  </tr>
  <?php endif; ?>
  
  <tr<?php if ($this->_vars['admin_vars']['uri']['action'] == "stat"): ?> class="odd"<?php endif; ?>>
    <td><i class="fa fa-search" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i> <a href="?action=stat"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "attendance"), $this);?></a></td>
  </tr>
  
<?php endif; ?> 

	<?php if (! empty ( $this->_vars['site_vars']['sys_autoload_rate'] )): ?>
	<tr>
		<td style="padding-top:30px;">
		<table width="100%" class="table"><tr class="bg"><td class="bg center">
			<p><a href="?action=settings&do=site_vars&site_id=-1&q=sys_autoload_rate&redirect=1"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "currency_rate"), $this);?></a><br><small><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "update_auto"), $this);?></small></p>
			
			<p><?php echo $this->_vars['site_vars']['kurs_usd']; ?>
 / <i class="fa fa-usd"></i><br>
			<?php echo $this->_vars['site_vars']['kurs_euro']; ?>
 / <i class="fa fa-euro"></i></p>
			
			<small><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "last_update"), $this);?>:<br><?php echo $this->_run_modifier($this->_vars['site_vars']['kurs_date'], 'date', 'plugin', 1, "d.m.Y H:i"); ?>
<br>
			<a href="?update_rate=1"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "update"), $this);?></a>
			</small>
		</td></tr></table>
		</td>
	</tr>
  <?php endif; ?>
 
  
</table>

</td><td valign="top">