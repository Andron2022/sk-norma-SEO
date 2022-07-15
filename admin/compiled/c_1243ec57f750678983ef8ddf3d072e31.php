<?php  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "mass"), $this);?></h1>
<blockquote style="width:50%;"><p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "mass",'key3' => "here_to_add"), $this);?></p></blockquote>


<ul>
  <li><a href="?action=mass&do=categs"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "pages"), $this);?></a></li>
  <li><a href="?action=mass&do=pubs"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "publications"), $this);?></a></li>
  <li><a href="?action=mass&do=products"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "mass",'key3' => "items"), $this);?></a></li>
</ul>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>