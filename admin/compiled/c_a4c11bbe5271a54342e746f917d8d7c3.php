<?php  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="center"><?php if (isset ( $this->_vars['title'] )):  echo $this->_vars['title'];  else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "attention"), $this); endif; ?></h1>
<?php echo $this->_vars['content']; ?>


<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>