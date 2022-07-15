<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.replace.php'); $this->register_modifier("replace", "tpl_modifier_replace");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0 mb-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "websites"), $this);?></h1>

<table width="80%">
	<tr>
		<td>
	
<?php if ($this->_run_modifier($this->_vars['list_sites'], 'count', 'PHP', 0) > 0): ?>
<blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "help"), $this);?></blockquote>


<?php if (isset ( $_GET['deleted'] )): ?><blockquote class="red"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "site_deleted"), $this);?></blockquote><?php endif; ?>


		</td>
	</tr>
</table>

<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "sites_work"), $this);?>: <?php echo $this->_vars['sites_working']; ?>


<table width="80%">
	<tr>
		<th width=20><i class="fa fa-eye"></i></th>
		<th width=10>ID</th>
		<th width=20%><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "short_name"), $this);?></th>
		<th><span title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "site_address"), $this);?>"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "url"), $this);?></span></th>
		<th><i class="fa fa-home" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "default_page"), $this);?>"></i></th>
		<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "email"), $this);?></th>
		<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "phone"), $this);?></th>
		<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "tpl"), $this);?></th>
		<th width=20><span title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "price_link_for"), $this);?>">YML</span></th>
		<th width=20><i class="fa fa-envelope" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "fb_in_db"), $this);?>"></i></th>
		<th width=20><a href="?action=settings&do=site_vars&site_id=0"><i class="fa fa-cogs" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "overall_vars"), $this);?>"></i></a></th>
	</tr>
          
    <?php if (count((array)$this->_vars['list_sites'])): foreach ((array)$this->_vars['list_sites'] as $this->_vars['value']): ?>
    <?php $this->assign('href', "?action=settings&do=site&mode=edit&id=".$this->_vars['value']['id']); ?>
    <tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
      <td class="center"><a href="<?php echo $this->_vars['href']; ?>
"><?php if ($this->_vars['value']['site_active'] == 1): ?><i class="fa fa-check"></i><?php else: ?><i class="fa fa-minus"></i><?php endif; ?></a></td>
      <td class="right"><a href="<?php echo $this->_vars['href']; ?>
"><?php echo $this->_vars['value']['id']; ?>
</a></td>
      <td><a href="<?php echo $this->_vars['href']; ?>
"><i class="fa fa-pencil"></i> <?php echo $this->_run_modifier($this->_vars['value']['name_short'], 'stripslashes', 'PHP', 1); ?>
</a></td>
      <td><a href="<?php echo $this->_vars['value']['site_url']; ?>
/" target="_blank"><small><i class="fa fa-external-link"></i></small></a> <a href="<?php echo $this->_vars['href']; ?>
"><?php echo $this->_vars['value']['site_url']; ?>
</a></td>
      <td align="center"><?php if ($this->_vars['value']['default_id_categ'] > 0): ?><a href="?action=info&do=edit_categ&id=<?php echo $this->_vars['value']['default_id_categ']; ?>
"><?php echo $this->_vars['value']['categ_title']; ?>
</a><?php else: ?>-<?php endif; ?></td>
      <td><?php echo $this->_run_modifier($this->_vars['value']['email_info'], 'stripslashes', 'PHP', 1); ?>
</td>
      <td><?php echo $this->_run_modifier($this->_vars['value']['phone'], 'stripslashes', 'PHP', 1); ?>
</td>
      <td><?php echo $this->_run_modifier($this->_vars['value']['template_path'], 'replace', 'plugin', 1, "/tpl/,/"); ?>
</td>
      <td align="center"><?php if (! empty ( $this->_vars['value']['yml'] )): ?><a href="<?php echo $this->_vars['value']['site_url']; ?>
/price<?php echo constant('URL_END'); ?>
?<?php echo $this->_vars['value']['yml']; ?>
" target="_blank"><i class="fa fa-shopping-cart"></i></a><?php else: ?><a href="?action=settings&do=site_vars&id=0&hint=yml_key"><i class="fa fa-plus-circle"></i></a><?php endif; ?></td>
      <td align="center"><?php if ($this->_vars['value']['feedback_in_db'] == 1): ?><i class="fa fa-check" style="color:<?php echo $this->_vars['admin_vars']['bgdark']; ?>
"></i><?php else: ?>-<?php endif; ?></td>


      <td><a href="?action=settings&do=site_vars&site_id=<?php echo $this->_vars['value']['id']; ?>
"><i class="fa fa-cog"></i></a></td>
    </tr>          
  <?php endforeach; endif; ?>
</table>

<?php else: ?>
<p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "no_sites_added"), $this);?></p>
<?php endif; ?>

<?php if ($this->_vars['admin_vars']['multisite'] == 1): ?>
  <p><a href="?action=settings&do=add_site"><i class="fa fa-plus-circle"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a></p>
<?php endif;  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>