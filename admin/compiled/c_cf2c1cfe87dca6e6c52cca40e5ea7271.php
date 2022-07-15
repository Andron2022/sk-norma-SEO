<?php  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<h1>Ошибка 404</h1>
<p>Произошла ошибка 404, запрошенная вами страница не найдена на сервере! 
Попробуйте начать с <a href="/">главной страницы</a>.</p>

<?php if (! empty ( $this->_vars['page']['error'] )): ?><pre><?php echo $this->_vars['page']['error']; ?>
</pre><?php endif;  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>