<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.filesize.php'); $this->register_modifier("filesize", "tpl_modifier_filesize");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.counter.php'); $this->register_function("counter", "tpl_function_counter");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.date.php'); $this->register_modifier("date", "tpl_modifier_date");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>


<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "database"), $this);?></h1>
<ul>
	<li><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "mysql_caching"), $this);?>: <a href="?action=settings&do=site_vars&site_id=-1&q=sys_mysql_cache&redirect=1"><?php if (isset ( $this->_vars['site_vars']['sys_mysql_cache'] )): ?>
	<?php if ($this->_vars['site_vars']['sys_mysql_cache'] == 1):  echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "on"), $this); else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "off"), $this); endif; ?>
	<?php else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "not_set"), $this); endif; ?></a> [<a href="?action=db&do=clearcache"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "clear_cache"), $this);?></a>] 
	[<a href="?action=db&do=delstat"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "delete_stat",'case' => "lower"), $this);?></a>]
	
	<?php if (! empty ( $this->_vars['last_clear_cache'] )): ?>
	<br> - <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "last_clear"), $this);?>: <?php echo $this->_run_modifier($this->_vars['last_clear_cache']['date_insert'], 'date', 'plugin', 1, "d.m.Y H:i"); ?>
 [<a href="?action=settings&do=add_user&id=<?php echo $this->_vars['last_clear_cache']['who_changed']; ?>
"><?php if (! empty ( $this->_vars['last_clear_cache']['who_changed_name'] )):  echo $this->_vars['last_clear_cache']['who_changed_name'];  else:  echo $this->_vars['last_clear_cache']['who_changed_login'];  endif; ?></a>]
	<?php endif; ?>
	</li>
	<li><a href="?action=db&do=get_dump" onclick="if(confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "confirm"), $this);?>')) return true; else return false;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "create_db_copy"), $this);?></a></li>
</ul>

<table width="80%">
	<tr>
		<td>
			
<?php if (! empty ( $_GET['clearcached'] )): ?>
    <blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "cache_cleared"), $this);?></blockquote>
<?php endif; ?>

<?php if (! empty ( $_GET['clearstat'] )): ?>
    <blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "stat_cleared"), $this);?></blockquote>
<?php endif; ?>

<?php if (! empty ( $_GET['updated'] )): ?>
    <blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "info_updated"), $this);?></blockquote>
<?php endif; ?>

<?php if (! empty ( $_GET['added'] ) && $_GET['added'] == "dump"): ?>
    <blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "db_copy_done"), $this);?></blockquote>
<?php endif; ?>

<?php if (! empty ( $_GET['added'] ) && $_GET['added'] == "optimize"): ?>
    <blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "optimize_db_done"), $this);?></blockquote>
<?php endif; ?>


<?php if (! empty ( $_GET['added'] ) && $_GET['added'] == "updated"): ?>
    <blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "db_update_done"), $this);?></blockquote>
<?php endif; ?>


<?php if (! empty ( $this->_vars['dump_files'] ) && ! empty ( $this->_vars['dump_folder'] )): ?>
    <blockquote><h4><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "saved_db_copies"), $this);?></h4>
	<ul>
	<?php if (count((array)$this->_vars['dump_files'])): foreach ((array)$this->_vars['dump_files'] as $this->_vars['f']): ?>
		<li><a href="<?php echo $this->_vars['dump_folder'];  echo $this->_vars['f']; ?>
" target="_blank"><?php echo $this->_vars['f']; ?>
</a> / <small><a href="?action=db&delete=<?php echo $this->_vars['f']; ?>
" onclick="if(confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "really"), $this);?>')) return true; else return false;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "delete"), $this);?></a></small></li>
	<?php endforeach; endif; ?>
	</ul>
	</blockquote>
<?php endif; ?>

<?php if (! empty ( $this->_vars['db_not_exists'] )): ?>
	<h3><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "no_tables"), $this);?>:</h3>
	<ul>
	<?php if (count((array)$this->_vars['db_not_exists'])): foreach ((array)$this->_vars['db_not_exists'] as $this->_vars['k'] => $this->_vars['v']): ?>
		<li><?php echo $this->_vars['k']; ?>
: <?php echo $this->_vars['v']; ?>
 <a href="?action=db&do=add_db&type=<?php echo $this->_vars['k']; ?>
&name=<?php echo $this->_vars['v']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a></li>
	<?php endforeach; endif; ?>
	</ul>
<?php endif; ?>


		</td>
	</tr>
</table>

<?php if ($this->_run_modifier($this->_vars['rows'], 'count', 'PHP', 0) > 0): ?>
	<table width="80%">
		<tr>
			<th>#</th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "table"), $this);?></th>
			<th>Engine</th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "size"), $this);?></th>
			<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "qty_records"), $this);?></th>
		</tr>
        
		<?php if (count((array)$this->_vars['rows'])): foreach ((array)$this->_vars['rows'] as $this->_vars['v']): ?>
        <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
			<td><?php echo tpl_function_counter(array(), $this);?></td>
			<td><a href="?action=db&do=view_db&table=<?php echo $this->_vars['v']['table_name']; ?>
"><?php echo $this->_vars['v']['table_name']; ?>
</a>              
            <?php if ($this->_vars['v']['table_name'] == "counter" && $this->_vars['v']['table_rows'] > 0): ?>
              <br><small><a href="?action=db&do=clear_db_counter"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "delete_30_days"), $this);?></a></small>
            <?php endif; ?></td>
			<td><?php echo $this->_vars['v']['engine']; ?>
</td>
			<td align="right"<?php if ($this->_vars['v']['total_size'] > 1024000): ?> style="color:red; font-weight:bold;"<?php endif; ?>><?php echo $this->_run_modifier($this->_vars['v']['total_size'], 'filesize', 'plugin', 1); ?>
</td>
			<td align="right">
			<?php if ($this->_vars['v']['table_rows'] > 0): ?>
			<a href="?action=db&do=view_db&table=<?php echo $this->_vars['v']['table_name']; ?>
&records=1"><?php echo $this->_vars['v']['table_rows']; ?>
</a>
			<?php else:  echo $this->_vars['v']['table_rows'];  endif; ?>
			<?php if ($this->_vars['v']['table_rows'] > 0 && ! empty ( $this->_vars['v']['clear_table'] )): ?> <a href="?action=db&do=clear_db&table=<?php echo $this->_vars['v']['table_name']; ?>
" onclick="if(confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "really"), $this);?>')) return true; else return false;"><i class="fa fa-trash" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "clear"), $this);?>"></i></a>
			<?php elseif ($this->_vars['v']['table_rows'] == 0 && $this->_vars['v']['table_name'] == "email_event"): ?> <a href="?action=db&do=add_emails"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add",'case' => "lower"), $this);?></a>
			<?php endif; ?></td>
		</tr>
		<?php endforeach; endif; ?>

        <tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
			<td align="right" colspan="3"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "total"), $this);?>:</td>
			<td align="right"><b><?php echo $this->_run_modifier($this->_vars['summ'], 'filesize', 'plugin', 1); ?>
</b></td>
			<td colspan="2"></td>
        </tr>
    </table>    
<?php endif; ?>

<table width="80%">
	<tr>
		<td>
			<ul>
				<li><a href="?action=db&do=optimize"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "optimize_tables"), $this);?></a></li>
				<li><a href="?action=db&do=update" onclick="if(confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "really_update"), $this);?> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "warning_text"), $this);?>\n*****************\n <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "cant_getback"), $this);?>')) return true; else return false;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "update_db"), $this);?></a> - <span style="color:red;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "warning_text"), $this);?> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "cant_getback"), $this);?></span></li>
			</ul>
		</td>
	</tr>
</table>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>