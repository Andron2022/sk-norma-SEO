<?php  if (! isset ( $this->_vars['hint'] )):  $this->assign('hint', "");  endif;  $this->assign('do', $this->_vars['admin_vars']['uri']['do']);  $this->assign('mode', $this->_vars['admin_vars']['uri']['mode']); ?>

<ul>
	<li><?php if ($this->_vars['do'] == "site_vars" && empty ( $this->_vars['mode'] ) && ! isset ( $_GET['id'] ) && empty ( $_GET['q'] )):  echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "site_vars"), $this); else: ?><a href="?action=settings&do=site_vars"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "site_vars"), $this);?></a><?php endif; ?> | 
	<?php if ($this->_vars['mode'] == "sys" && empty ( $_GET['q'] )):  echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "sys_vars"), $this); else: ?><a href="?action=settings&do=site_vars&mode=sys"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "sys_vars"), $this);?></a><?php endif; ?> | 
	<?php if ($this->_vars['mode'] == "img"):  echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "pics"), $this); else: ?><a href="?action=settings&do=site_vars&mode=img"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "pics"), $this);?></a><?php endif; ?> | 
	<?php if ($this->_vars['do'] == "mass_vars" && empty ( $this->_vars['hint'] )):  echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "master"), $this); else: ?><a href="?action=settings&do=mass_vars"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "master"), $this);?></a><?php endif; ?>
		<?php if ($this->_vars['do'] == "mass_vars"): ?>
			<ul>
				<li><?php if ($this->_vars['hint'] != "smtp"): ?><a href="?action=settings&do=mass_vars&hint=smtp"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "smtp"), $this);?></a><?php else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "smtp"), $this); endif; ?> | 
				<?php if ($this->_vars['hint'] != "currencies"): ?><a href="?action=settings&do=mass_vars&hint=currencies"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "currencies"), $this);?></a><?php else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "currencies"), $this); endif; ?> | 
				<?php if ($this->_vars['hint'] != "images"): ?><a href="?action=settings&do=mass_vars&hint=images"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "pics"), $this);?></a><?php else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "pics"), $this); endif; ?> | 
				<?php if ($this->_vars['hint'] != "extra"): ?><a href="?action=settings&do=mass_vars&hint=extra"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "extra_lists"), $this);?></a><?php else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "extra_lists"), $this); endif; ?> | 
				<?php if ($this->_vars['hint'] != 'social'): ?><a href="?action=settings&do=mass_vars&hint=social"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "social_nets"), $this);?></a><?php else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "social_nets"), $this); endif; ?> | 
				<?php if ($this->_vars['hint'] != 'yml'): ?><a href="?action=settings&do=mass_vars&hint=yml"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "yml"), $this);?></a><?php else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "yml"), $this); endif; ?> | 
				<?php if ($this->_vars['hint'] != 'gmc'): ?><a href="?action=settings&do=mass_vars&hint=gmc"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "gmc"), $this);?></a><?php else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "gmc"), $this); endif; ?> | 
				<?php if ($this->_vars['hint'] != 'sms'): ?><a href="?action=settings&do=mass_vars&hint=sms">SMS</a><?php else: ?>SMS<?php endif; ?>
				</li>
			</ul>
		<?php endif; ?>
	</li>
</ul>