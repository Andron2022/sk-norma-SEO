<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.replace.php'); $this->register_modifier("replace", "tpl_modifier_replace");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.math.php'); $this->register_function("math", "tpl_function_math");   if (isset ( $this->_vars['npages'] ) && $this->_vars['npages'] > 0): ?><p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "pages"), $this);?>: 
  <?php echo tpl_function_math(array('equation' => "( x - y )",'x' => $this->_vars['page'],'y' => 5,'assign' => "lpage"), $this);?>
  <?php echo tpl_function_math(array('equation' => "( x + y )",'x' => $this->_vars['page'],'y' => 5,'assign' => "ppage"), $this);?>
  <?php for($for1 = 0; ((0 < $this->_vars['npages']) ? ($for1 < $this->_vars['npages']) : ($for1 > $this->_vars['npages'])); $for1 += ((0 < $this->_vars['npages']) ? 1 : -1)):  $this->assign('i', $for1); ?>
    <?php echo tpl_function_math(array('equation' => "( x + y )",'x' => $this->_vars['i'],'y' => 1,'assign' => "subpage"), $this);?>
    <?php if ($this->_vars['i'] != $this->_vars['page']): ?>
      <?php if ($this->_vars['i'] == 0): ?>
        <a href="<?php echo $this->_run_modifier($this->_vars['href'], 'replace', 'plugin', 1, "&page=", ""); ?>
"><?php echo $this->_vars['subpage']; ?>
</a>
	  <?php elseif ($this->_vars['npages'] == $this->_vars['subpage']): ?>
		<a href="<?php echo $this->_vars['href'];  echo $this->_vars['i']; ?>
"><?php echo $this->_vars['subpage']; ?>
</a>
      <?php else: ?>    
			<?php if ($this->_vars['i'] == $this->_vars['lpage']): ?>
			...
			<?php elseif ($this->_vars['i'] == $this->_vars['ppage']): ?>
			...
			<?php elseif ($this->_vars['i'] > $this->_vars['lpage'] && $this->_vars['i'] < $this->_vars['ppage']): ?>
			<a href="<?php echo $this->_vars['href'];  echo $this->_vars['i']; ?>
"><?php echo $this->_vars['subpage']; ?>
</a>			
			<?php else: ?>
			<?php endif; ?>        
      <?php endif; ?>
    <?php else: ?>
      <b><?php echo $this->_vars['subpage']; ?>
</b>
    <?php endif; ?> 
  <?php endfor; ?></p>
<?php endif; ?>