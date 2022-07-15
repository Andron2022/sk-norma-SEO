<?php  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
		
<?php if (! empty ( $this->_vars['page']['intro'] )): ?><div class="note note-warning"><?php echo $this->_vars['page']['intro']; ?>
</div><?php endif;  if (! empty ( $this->_vars['page']['content'] )): ?>
<div class="row"><?php echo $this->_vars['page']['content']; ?>
</div>
<?php endif;  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/blocks.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>										
										
	
<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>