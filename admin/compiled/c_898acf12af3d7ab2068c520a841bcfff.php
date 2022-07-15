<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.delhttp.php'); $this->register_modifier("delhttp", "tpl_modifier_delhttp");  ?><table border=0 cellpadding=5 cellspacing=1 width="200">
  <tr><th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "settings"), $this);?></th></tr>
  <tr<?php if ($this->_vars['admin_vars']['uri']['do'] == "site"): ?> class="odd"<?php endif; ?>>
    <td><i class="fa fa-wrench" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
;"></i> <a href="?action=settings&do=site"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "site_settings"), $this);?></a></td>
  </tr>
  <tr<?php if ($this->_vars['admin_vars']['uri']['do'] == "site_vars" || $this->_vars['admin_vars']['uri']['do'] == "mass_vars"): ?> class="odd"<?php endif; ?>>
    <td><i class="fa fa-cogs" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
;"></i> <a href="?action=settings&do=site_vars"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "elements"), $this);?></a> <a href="?action=settings&do=site_vars&id=0"><i class="fa fa-plus" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
;"></i></a></td>
  </tr>
  
  <?php if (! empty ( $this->_vars['site_vars']['fav']['vars'] )): ?>
  <tr>
	<td>
		<?php if (count((array)$this->_vars['site_vars']['fav']['vars'])): foreach ((array)$this->_vars['site_vars']['fav']['vars'] as $this->_vars['k'] => $this->_vars['v']): ?>
			<?php if ($this->_vars['k'] > 0): ?><br><?php endif; ?><small><a href="?action=settings&do=site_vars&id=<?php echo $this->_vars['v']['id']; ?>
" title="<?php if (! empty ( $this->_vars['v']['site'] )): ?> <?php echo $this->_run_modifier($this->_vars['v']['site'], 'delhttp', 'plugin', 1);  endif; ?>"><i class="fa fa-star"></i> <?php echo $this->_vars['v']['name']; ?>
</a></small>
		<?php endforeach; endif; ?>
	</td>
  </tr>
  <?php endif; ?>
  
  <tr<?php if ($this->_vars['admin_vars']['uri']['do'] == "blocks"): ?> class="odd"<?php endif; ?>>
    <td><i class="fa fa-th-large" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
;"></i> <a href="?action=settings&do=blocks"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "blocks"), $this);?></a> <a href="?action=settings&do=blocks&id=0"><i class="fa fa-plus" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
;"></i></a></td>
  </tr> 
  
  <?php if (! empty ( $this->_vars['site_vars']['fav']['blocks'] )): ?>
  <tr>
	<td>
		<?php if (count((array)$this->_vars['site_vars']['fav']['blocks'])): foreach ((array)$this->_vars['site_vars']['fav']['blocks'] as $this->_vars['k'] => $this->_vars['v']): ?>
			<?php if ($this->_vars['k'] > 0): ?><br><?php endif; ?><a href="?action=settings&do=blocks&id=<?php echo $this->_vars['v']['id']; ?>
"><small><i class="fa fa-star"></i> <?php if (! empty ( $this->_vars['v']['title'] )):  echo $this->_vars['v']['title'];  else:  echo $this->_vars['v']['row_title'];  endif; ?></small></a>
		<?php endforeach; endif; ?>
	</td>
  </tr>
  <?php endif; ?>
  
  <tr<?php if ($this->_vars['admin_vars']['uri']['do'] == "tpl"): ?> class="odd"<?php endif; ?>>
    <td><i class="fa fa-sitemap" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
;"></i> <a href="?action=settings&do=tpl"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "tpls"), $this);?></a></td>
  </tr>
  <tr<?php if ($this->_vars['admin_vars']['uri']['do'] == "users" || $this->_vars['admin_vars']['uri']['do'] == "add_user"): ?> class="odd"<?php endif; ?>>
    <td><i class="fa fa-users" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
;"></i> <a href="?action=settings&do=users"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "users"), $this);?></a> <a href="?action=settings&do=add_user"><i class="fa fa-plus" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
;"></i></a></td>
  </tr>

  <tr<?php if ($this->_vars['admin_vars']['uri']['action'] == "delivery"): ?> class="odd"<?php endif; ?>>
    <td><i class="fa fa-truck" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
;"></i> <a href="?action=delivery"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "delivery"), $this);?></a> <a href="?action=delivery&id=0"><i class="fa fa-plus" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
;"></i></a></td>
  </tr>

  <tr<?php if ($this->_vars['admin_vars']['uri']['do'] == "payments"): ?> class="odd"<?php endif; ?>>
    <td><i class="fa fa-credit-card" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
;"></i> <a href="?action=settings&do=payments"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "pay_methods"), $this);?></a></td>
  </tr>

  <tr<?php if ($this->_vars['admin_vars']['uri']['do'] == "statuses"): ?> class="odd"<?php endif; ?>>
    <td><i class="fa fa-spinner" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
;"></i> <a href="?action=settings&do=statuses"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "order_statuses"), $this);?></a></td>
  </tr>
  
  <tr<?php if ($this->_vars['admin_vars']['uri']['action'] == "db"): ?> class="odd"<?php endif; ?>>
    <td><i class="fa fa-database" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
;"></i> <a href="?action=db"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "database"), $this);?></a></td>
  </tr>

  <tr<?php if ($this->_vars['admin_vars']['uri']['action'] == "mass"): ?> class="odd"<?php endif; ?>>
    <td><i class="fa fa-plus" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
;"></i> <a href="?action=mass"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "mass_add"), $this);?></a></td>
  </tr>

  <tr<?php if ($this->_vars['admin_vars']['uri']['action'] == "emails"): ?> class="odd"<?php endif; ?>>
    <td><i class="fa fa-envelope" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
;"></i> <a href="?action=emails"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "mail_events"), $this);?></a></td>
  </tr>

  <tr<?php if ($this->_vars['admin_vars']['uri']['action'] == "coupons"): ?> class="odd"<?php endif; ?>>
    <td><i class="fa fa-gift" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
;"></i> <a href="?action=coupons"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "coupons"), $this);?></a></td>
  </tr>

  <tr<?php if ($this->_vars['admin_vars']['uri']['action'] == "org"): ?> class="odd"<?php endif; ?>>
    <td><i class="fa fa-briefcase" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
;"></i> <a href="?action=org"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "orgs"), $this);?></a></td>
  </tr>
  

</table>

</td><td valign="top">