<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.replace.php'); $this->register_modifier("replace", "tpl_modifier_replace");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.date.php'); $this->register_modifier("date", "tpl_modifier_date");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.filesize.php'); $this->register_modifier("filesize", "tpl_modifier_filesize");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.phpinfo.php'); $this->register_function("phpinfo", "tpl_function_phpinfo");   if (! empty ( $_GET['do'] ) && $_GET['do'] == "phpinfo"): ?>
	<a href="?action=settings"><h1 class="center mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "title"), $this);?></h1></a>
	<?php echo tpl_function_phpinfo(array(), $this); else: ?>

	<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
	
	<h1 class="mt-0 mb-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "title"), $this);?></h1>
	
	<table width="80%">
		<tr>
			<td>
				<blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "help"), $this);?></blockquote>
			</td>
		</tr>
	</table>

	<table width="80%">
		<tr>
			<td width="50%" valign="top">
	
			<?php if (! empty ( $_GET['do'] ) && $_GET['do'] == "php"): ?>	
				<?php if (! empty ( $this->_vars['settings'] )): ?>
					<h3><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "php_settings"), $this);?></h3>
					<table width="100%">
					<?php if (count((array)$this->_vars['settings'])): foreach ((array)$this->_vars['settings'] as $this->_vars['k'] => $this->_vars['v']): ?>
						<?php if ($this->_vars['k'] != "mbstring"): ?>
						<tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>">
							<td><?php echo $this->_vars['k']; ?>
</td>
							<td><?php if ($this->_run_modifier($this->_vars['v'], 'is_array', 'PHP', 1)): ?>
								<ul><?php if (count((array)$this->_vars['v'])): foreach ((array)$this->_vars['v'] as $this->_vars['v1'] => $this->_vars['v2']): ?>
									<li><?php echo $this->_vars['v2']; ?>
</li>
								<?php endforeach; endif; ?></ul>
								<?php else:  echo $this->_vars['v'];  endif; ?>
							</td>
						</tr>
						<?php endif; ?>
					<?php endforeach; endif; ?>
					</table>
				<?php endif; ?>
			<?php else: ?>
	
				<h3><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "check_vars"), $this);?></h3>
				<ul>         
					<li><a href="?action=settings&do=mass_vars&hint=images"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "picsizes"), $this);?></a>: 
					
					<?php if (isset ( $this->_vars['site_vars']['img_size1'] )): ?>1 - <?php echo $this->_vars['site_vars']['img_size1']['width']; ?>
*<?php echo $this->_vars['site_vars']['img_size1']['height'];  else: ?>, 1 - <span style="color:red;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "not_set"), $this);?></span><?php endif; ?>
					<?php if (isset ( $this->_vars['site_vars']['img_size2'] )): ?>, 2 - <?php echo $this->_vars['site_vars']['img_size2']['width']; ?>
*<?php echo $this->_vars['site_vars']['img_size2']['height'];  else: ?>, 2 - <span style="color:red;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "not_set"), $this);?></span><?php endif; ?>
					<?php if (isset ( $this->_vars['site_vars']['img_size3'] )): ?>, 3 - <?php echo $this->_vars['site_vars']['img_size3']['width']; ?>
*<?php echo $this->_vars['site_vars']['img_size3']['height'];  else: ?>, 3 - <span style="color:red;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "not_set"), $this);?></span><?php endif; ?>
					
					
					<?php if (isset ( $this->_vars['site_vars']['img_size4'] )): ?>, 4 - <?php echo $this->_vars['site_vars']['img_size4']['width']; ?>
*<?php echo $this->_vars['site_vars']['img_size4']['height'];  else: ?>, 4 - <span style="color:red;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "not_set"), $this);?></span><?php endif; ?>
					
					<?php if (isset ( $this->_vars['site_vars']['img_size5'] )): ?>, 5 - <?php echo $this->_vars['site_vars']['img_size5']['width']; ?>
*<?php echo $this->_vars['site_vars']['img_size5']['height'];  else: ?>, 5 - <span style="color:red;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "not_set"), $this);?></span><?php endif; ?>
					
					</li>
					
					<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "resize_method"), $this);?>: <?php if (isset ( $this->_vars['site_vars']['sys_resize_method'] )): ?><a href="?action=settings&do=site_vars&site_id=-1&q=sys_resize_method&redirect=1"><?php echo $this->_vars['site_vars']['sys_resize_method']; ?>
</a><?php else: ?><span style="color:red;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "not_set"), $this);?></span><?php endif; ?></li>
					
					<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "file_upload"), $this);?>: <a href="?action=settings&do=site_vars&site_id=-1&q=sys_upload_ext_allowed&redirect=1"><?php if (isset ( $this->_vars['site_vars']['sys_upload_ext_allowed'] ) && $this->_vars['site_vars']['sys_upload_ext_allowed'] != "*"):  echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "allowed"), $this);?> - <?php echo $this->_vars['site_vars']['sys_upload_ext_allowed'];  else: ?><span style="color:red;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "any_format"), $this);?></span><?php endif; ?></a></li>
					<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "original_pics"), $this);?>: <a href="?action=settings&do=site_vars&site_id=-1&q=sys_save_original_fotos&redirect=1"><?php if (! empty ( $this->_vars['admin_vars']['save_original_fotos'] )):  echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "saved"), $this); else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "deleted"), $this); endif; ?></a></li>
					<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "register_visitors"), $this);?>: <a href="?action=settings&do=site_vars&site_id=-1&q=sys_count_visitors&redirect=1"><?php if (! empty ( $this->_vars['site_vars']['sys_count_visitors'] )): ?><span style="color:green; font-weight:bold;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "on"), $this);?></span><?php else: ?><span style="color:red;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "off"), $this); endif; ?></a></li>
					<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "mysql_cache"), $this);?>: <a href="?action=settings&do=site_vars&site_id=-1&q=sys_mysql_cache&redirect=1"><?php if (! empty ( $this->_vars['site_vars']['sys_mysql_cache'] ) || ! isset ( $this->_vars['site_vars']['sys_mysql_cache'] )): ?><span style="color:green; font-weight:bold;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "on"), $this);?></span><?php else: ?><span style="color:red;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "off"), $this); endif; ?></a> | <a href="?action=db&do=clearcache"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "reset"), $this);?></a></li>
					
					<li><a href="?action=settings&do=php">PHP</a> | 
					<a href="?action=settings&do=phpinfo" target="_blank">phpinfo</a> | 
					<a href="?action=settings&do=site_vars&site_id=-1&q=sys_robots_txt&redirect=1">robots.txt</a>
					</li>
					
					<?php if (! empty ( $this->_vars['smsc_balance'] )): ?>
					<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "smsc_balance"), $this);?> - <?php echo $this->_vars['smsc_balance']; ?>
</li>
					<?php endif; ?>
					
				</ul>

				<h3><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "shop"), $this);?></h3>
				<ul>         
					<li><a href="?action=settings&do=statuses"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "statuses"), $this);?></a>, 
					<a href="?action=delivery"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "delivery"), $this);?></a>, 
					<a href="?action=settings&do=payments"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "payments"), $this);?></a>
					</li>
				</ul>
			<?php endif; ?>

			</td>
			<td valign="top">
			
			<?php if (! empty ( $_GET['do'] ) && $_GET['do'] == "php"): ?>	
				<?php if (! empty ( $this->_vars['settings']['mbstring'] )): ?>
					<h3>mbstring</h3>
					<table width="100%">
					<?php if (count((array)$this->_vars['settings']['mbstring'])): foreach ((array)$this->_vars['settings']['mbstring'] as $this->_vars['k'] => $this->_vars['v']): ?>
						<tr class="<?php echo tpl_function_cycle(array('values' => ",odd"), $this);?>"><td><?php echo $this->_vars['k']; ?>
</td>
							<td><?php if ($this->_run_modifier($this->_vars['v'], 'is_array', 'PHP', 1)): ?>
								<?php if (count((array)$this->_vars['v'])): foreach ((array)$this->_vars['v'] as $this->_vars['v1'] => $this->_vars['v2']): ?>
									<?php echo $this->_vars['v1']; ?>
: <?php echo $this->_vars['v2']; ?>
<br>
								<?php endforeach; endif; ?>
							<?php else:  echo $this->_vars['v'];  endif; ?></td>
						</tr>
					<?php endforeach; endif; ?>
					</table>
				<?php endif; ?>
			<?php else: ?>
   
				<h3><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "uploaded_files"), $this);?></h3>
				<ul>
					<li><a href="?action=settings&do=fotos"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "pics"), $this);?></a>: <?php echo $this->_run_modifier($this->_vars['sizerecords']['size'], 'filesize', 'plugin', 1, 2, $this->_vars['site_vars']['lang_admin']); ?>

					<small><?php echo $this->_run_modifier($this->_vars['sizerecords']['date'], 'date', 'plugin', 1, "d.m.Y H:i"); ?>
 <?php if (! empty ( $_GET['update'] ) && $_GET['update'] == "records"): ?><span style="color:red; font-weight:bold;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "updated"), $this);?></span><?php else: ?><a href="?action=settings&update=records"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "update"), $this);?></a><?php endif; ?></small>
					</li>
					<li><a href="?action=settings&do=files"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "files"), $this);?></a>: <?php echo $this->_run_modifier($this->_vars['sizefiles']['size'], 'filesize', 'plugin', 1, 2, $this->_vars['site_vars']['lang_admin']); ?>
 
					<small><?php echo $this->_run_modifier($this->_vars['sizefiles']['date'], 'date', 'plugin', 1, "d.m.Y H:i"); ?>
 <?php if (! empty ( $_GET['update'] ) && $_GET['update'] == "files"): ?><span style="color:red; font-weight:bold;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "updated"), $this);?></span><?php else: ?><a href="?action=settings&update=files"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "update"), $this);?></a><?php endif; ?></small>
					</li>
				</ul>
				
				<p><i class="fa fa-envelope"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "email_notifications"), $this);?>: <a href="?action=settings&do=mass_vars&hint=smtp"><?php if (isset ( $this->_vars['site_vars']['sys_smtp_on'] ) && $this->_vars['site_vars']['sys_smtp_on'] == 1):  echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "smtp"), $this); else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "sendmail"), $this); endif; ?></a> &raquo; <a href="?action=settings&do=email"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "test"), $this);?></a></p>
				
				<p><i class="fa fa-shopping-cart"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "yml_link"), $this);?>: <?php if (! empty ( $this->_vars['site_vars']['yml_key'] )): ?>
				<span style="color:darkgreen; font-weight:bold;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "published"), $this);?></span><?php else: ?><span style="color:red;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "not_published"), $this); endif; ?> <a href="?action=settings&do=mass_vars&hint=yml"><i class="fa fa-edit"></i></a></p>
				
				<p><i class="fa fa-google"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "gmc"), $this);?> <a href="?action=settings&do=mass_vars&hint=gmc"><i class="fa fa-edit"></i></a></p>
				
				<?php if ($this->_run_modifier($this->_vars['check_folders'], 'count', 'PHP', 0) > 0): ?>
				  <p style="color:red;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "folders_not_opened"), $this);?></p>
					 <?php if (count((array)$this->_vars['check_folders'])): foreach ((array)$this->_vars['check_folders'] as $this->_vars['value']): ?>
					   <i class="fa fa-folder-o"></i> <?php echo $this->_vars['value']; ?>
 
					   <?php if (! empty ( $_GET['try'] ) && $_GET['try'] == $this->_vars['value']): ?>
					   <br><span style="color:red;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "bad_attempt"), $this);?></span><br>
					   <?php else: ?>[<a href="?action=settings&try=<?php echo $this->_vars['value']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "correct"), $this);?></a>]<br><?php endif; ?>
					 <?php endforeach; endif; ?>
				<?php else: ?>
					<p><i class="fa fa-check"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "folders_ok"), $this);?></p>
				<?php endif; ?>
	  
				<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("settings/licence.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
			<?php endif; ?>	

			</td>
		</tr>
	</table>
<?php endif; ?>


<?php if (empty ( $_GET['do'] )): ?>	

	<h3><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "list_sites"), $this);?></h3>
	
	<?php if ($this->_run_modifier($this->_vars['allsites'], 'count', 'PHP', 0) > 0): ?>
    <table width="80%">
      <tr>
          <th width=20>&nbsp;</th>
          <th width=10><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "number"), $this);?></th>
          <th width=25%><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "short_name"), $this);?></th>
          <th width=25%><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "url"), $this);?></th>
          <th><i class="fa fa-home" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "default_page"), $this);?>"></i></th>
          <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "email"), $this);?></th>
          <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "phone"), $this);?></th>
          <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "tpl"), $this);?></th>
          <th><span title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "yml_link"), $this);?>"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "yml"), $this);?></th>
          <th width=20><i class="fa fa-envelope" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "fb_in_db"), $this);?>"></i></th>
          <th width=20><a href="?action=settings&do=site_vars&site_id=0"><i class="fa fa-cogs"></i></a></th>
      </tr>
          
      <?php if (count((array)$this->_vars['allsites'])): foreach ((array)$this->_vars['allsites'] as $this->_vars['value']): ?>
        <?php $this->assign('href', "?action=settings&do=site&mode=edit&id=".$this->_vars['value']['id']); ?>
        <tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
          <td><a href="<?php echo $this->_vars['href']; ?>
"><?php if ($this->_vars['value']['site_active'] == 1): ?><i class="fa fa-check"></i><?php else: ?><i class="fa fa-minus"></i><?php endif; ?></a></td>
          <td><a href="<?php echo $this->_vars['href']; ?>
"><?php echo $this->_vars['value']['id']; ?>
</a></td>
          <td><a href="<?php echo $this->_vars['href']; ?>
"><i class="fa fa-pencil"></i> <?php echo $this->_run_modifier($this->_vars['value']['name_short'], 'stripslashes', 'PHP', 1); ?>
</a></td>
          <td><a href="<?php echo $this->_vars['value']['site_url']; ?>
/" target=_blank><small><i class="fa fa-external-link"></i></small> <?php echo $this->_vars['value']['site_url']; ?>
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
		<p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "list_empty"), $this);?></p>
	<?php endif;  endif; ?>

<?php if (empty ( $_GET['do'] ) || $_GET['do'] != "phpinfo"): ?>
	<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
  endif; ?>