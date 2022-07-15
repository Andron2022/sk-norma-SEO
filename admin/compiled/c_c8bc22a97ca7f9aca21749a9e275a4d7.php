<?php  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "stat"), $this);?></h1>      

<p>
<a href="?action=stat&do=list"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "attendance"), $this);?></a> | 
<a href="?action=stat&do=search"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "site_search"), $this);?></a>
</p>

<table width="600">
	<tr>
		<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "period"), $this);?></th>
		<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "visits"), $this);?></th>
		<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "categs"), $this);?></th>
	</tr>
	<tr>
		<td><a href="?action=stat&from=<?php echo $this->_run_modifier(time(), 'date', 'plugin', 1, "Y-m-d"); ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "today"), $this);?></a></td>
		<td align="center"><?php if (empty ( $this->_vars['stat']['today']['qty'] )): ?>-<?php else:  echo $this->_vars['stat']['today']['qty'];  endif; ?></td>
		<td align="center"><?php if (empty ( $this->_vars['stat']['today']['total'] )): ?>-<?php else:  echo $this->_vars['stat']['today']['total'];  endif; ?></td>
	</tr>
	<tr class="odd">
		<td><a href="?action=stat&from=<?php echo $this->_vars['stat']['dates']['yesterday']; ?>
&to=<?php echo $this->_vars['stat']['dates']['yesterday']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "yesterday"), $this);?></a></td>
		<td align="center"><?php if (empty ( $this->_vars['stat']['yesterday']['qty'] )): ?>-<?php else:  echo $this->_vars['stat']['yesterday']['qty'];  endif; ?></td>
		<td align="center"><?php if (empty ( $this->_vars['stat']['yesterday']['total'] )): ?>-<?php else:  echo $this->_vars['stat']['yesterday']['total'];  endif; ?></td>
	</tr>
	<tr>
		<td><a href="?action=stat&from=<?php echo $this->_vars['stat']['dates']['week']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "for_7_days"), $this);?></a></td>
		<td align="center"><?php if (empty ( $this->_vars['stat']['week']['qty'] )): ?>-<?php else:  echo $this->_vars['stat']['week']['qty'];  endif; ?></td>
		<td align="center"><?php if (empty ( $this->_vars['stat']['week']['total'] )): ?>-<?php else:  echo $this->_vars['stat']['week']['total'];  endif; ?></td>
	</tr>
	<tr class="odd">
		<td><a href="?action=stat&from=<?php echo $this->_vars['stat']['dates']['month']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "for_30_days"), $this);?></a></td>
		<td align="center"><?php if (empty ( $this->_vars['stat']['month']['qty'] )): ?>-<?php else:  echo $this->_vars['stat']['month']['qty'];  endif; ?></td>
		<td align="center"><?php if (empty ( $this->_vars['stat']['month']['total'] )): ?>-<?php else:  echo $this->_vars['stat']['month']['total'];  endif; ?></td>
	</tr>
</table>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/pages.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>