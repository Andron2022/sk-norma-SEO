<?php  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "templates"), $this); if (! empty ( $this->_vars['folder'] )): ?>: <?php echo $this->_vars['folder'];  endif; ?></h1>

<i class="fa fa-folder-open"></i> <a href="?action=settings&do=tpl"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "templates"), $this);?></a> 

<?php if ($this->_run_modifier($this->_vars['newpath'], 'count', 'PHP', 0) > 1): ?>
  <?php if (count((array)$this->_vars['newpath'])): foreach ((array)$this->_vars['newpath'] as $this->_vars['key'] => $this->_vars['value']): ?>
	<?php if ($this->_vars['key'] != ".." && ! empty ( $this->_vars['key'] ) && ! empty ( $this->_vars['value'] )): ?>
		/ <a href="?action=settings&do=tpl&folder=<?php echo $this->_vars['value']; ?>
"><?php echo $this->_vars['key']; ?>
</a>
	<?php endif; ?>
  <?php endforeach; endif;  endif; ?>

<ul>
<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "templates"), $this);?>: <a href="?action=settings&do=site_vars&site_id=-1&q=sys_tpl_pages&redirect=1"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "found_categs"), $this);?></a>, 
<a href="?action=settings&do=site_vars&site_id=-1&q=sys_tpl_products&redirect=1"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "found_offers"), $this);?></a>, <a href="?action=settings&do=site_vars&site_id=-1&q=sys_tpl_pubs&redirect=1"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "found_pubs"), $this);?></a>.</li>
</ul>


<?php if ($this->_run_modifier($this->_vars['files'], 'count', 'PHP', 0) > 0): ?>

<p>
<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "files_available"), $this);?>:
<ul>

<?php if (count((array)$this->_vars['files'])): foreach ((array)$this->_vars['files'] as $this->_vars['value']):  if ($this->_vars['value']['link']): ?> 
<li><a href="<?php echo $this->_vars['value']['link']; ?>
"><?php if (! empty ( $this->_vars['value']['img'] )): ?><i class="fa fa-folder-o"></i> <?php endif;  echo $this->_vars['value']['file']; ?>
</a><?php if ($this->_vars['value']['ext_link']): ?> <a href="<?php echo $this->_vars['value']['ext_link']; ?>
" target="_blank"><i class="fa fa-external-link"></i></a><?php endif;  if (! empty ( $this->_vars['value']['ext'] ) && $this->_vars['value']['ext'] == "html"): ?> <small><a href="<?php echo $this->_vars['value']['link']; ?>
&fcopy=1" onclick="if(confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "really_copy"), $this);?>')) return true; else return false;"><i class="fa fa-plus"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "copy"), $this);?></a></small> <?php endif; ?>
</li>
<?php else: ?>
<li><?php if ($this->_vars['value']['img']): ?><img src='<?php echo $this->_vars['value']['img']; ?>
' border='0' /> <?php endif;  echo $this->_vars['value']['file']; ?>
</a><?php if ($this->_vars['value']['ext_link']): ?> <a href="<?php echo $this->_vars['value']['ext_link']; ?>
" target="_blank"><i class="fa fa-external-link"></i></a><?php endif; ?></li>
<?php endif;  endforeach; endif;  else: ?>

<p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "tpl",'key3' => "in_folder"), $this);?> <b><?php echo $this->_vars['folder']; ?>
</b> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "list_empty"), $this);?></p>
<?php endif; ?>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>