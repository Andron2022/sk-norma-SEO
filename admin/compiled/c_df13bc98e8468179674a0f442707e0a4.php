<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.counter.php'); $this->register_function("counter", "tpl_function_counter");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.truncate.php'); $this->register_modifier("truncate", "tpl_modifier_truncate");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.lower.php'); $this->register_modifier("lower", "tpl_modifier_lower");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
<h1 class="mt-0">
<?php if (! empty ( $this->_vars['table']['records'] ) && ! empty ( $this->_vars['table']['name'] )):  echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "table_records"), $this);?> <?php echo $this->_vars['table']['name'];  if (! empty ( $this->_vars['table']['qty'] )): ?> (<?php echo $this->_vars['table']['qty']; ?>
)<?php endif;  elseif (! empty ( $this->_vars['table']['name'] )):  echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "table_info"), $this);?> <?php echo $this->_vars['table']['name']; ?>

<?php else:  echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "unknown_page"), $this); endif; ?>
</h1>

<?php if (! empty ( $this->_vars['table']['records'] ) && ! empty ( $this->_vars['table']['name'] )): ?>
	<ul><li><a href="?action=db&do=view_db&table=<?php echo $this->_vars['table']['name']; ?>
"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "table_schema"), $this);?> <?php echo $this->_vars['table']['name']; ?>
</a></li></ul>

	<?php if (! empty ( $this->_vars['table']['rows'] )): ?>
	
		<?php echo $this->_vars['pages']; ?>

	
		<div id="table-scroll" class="table-scroll">
		  <div class="table-wrap">
				<form method="POST">
				<table class="table bordered">
				<?php if (count((array)$this->_vars['table']['rows'])): foreach ((array)$this->_vars['table']['rows'] as $this->_vars['k1'] => $this->_vars['v1']): ?>
					<?php if ($this->_vars['k1'] == 0): ?>
						<tr>
						<?php if (count((array)$this->_vars['v1'])): foreach ((array)$this->_vars['v1'] as $this->_vars['k'] => $this->_vars['v']): ?>
							<?php if ($this->_run_modifier($this->_vars['k'], 'lower', 'plugin', 1) == "id"): ?>
							<th><i class="fa fa-trash"></i></th>
							<?php endif; ?>
							<th><?php echo $this->_vars['k']; ?>
</th>
						<?php endforeach; endif; ?>
						</tr>
					<?php endif; ?>
					
					<tr class="<?php echo tpl_function_cycle(array('values' => " ,odd"), $this);?>">
						<?php if (count((array)$this->_vars['v1'])): foreach ((array)$this->_vars['v1'] as $this->_vars['k'] => $this->_vars['v']): ?>
							<?php if ($this->_run_modifier($this->_vars['k'], 'lower', 'plugin', 1) == "id"): ?>
							<td><button type="submit" name="del[<?php echo $this->_vars['k']; ?>
]" value="<?php echo $this->_vars['v']; ?>
" onclick="if(confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "really_delete_row"), $this);?> (<?php echo $this->_vars['v']; ?>
)')) return true; else return false;"><i class="fa fa-trash"></i></button></td>
							<?php endif; ?>
							<td><?php echo $this->_run_modifier($this->_run_modifier($this->_vars['v'], 'strip_tags', 'PHP', 1), 'truncate', 'plugin', 1, "250", "..."); ?>
</td>
						<?php endforeach; endif; ?>
					</tr>
								
				<?php endforeach; endif; ?>
				</table>
				</form>
			</div>
		</div>

		<?php echo $this->_vars['pages']; ?>

	<?php else: ?>
		<p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "no_records_found"), $this);?></p>
	<?php endif; ?>
	
	
<?php elseif (! empty ( $this->_vars['table']['info'] )): ?>

	<?php if (! empty ( $this->_vars['table']['qty'] )): ?>
		<ul><li><a href="?action=db&do=view_db&table=<?php echo $this->_vars['table']['name']; ?>
&records=1"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "look_records"), $this);?> (<?php echo $this->_vars['table']['qty']; ?>
)</a></li></ul>
	<?php endif; ?>
	
	<table class="table bordered">
		<tr>
			<th>#</th>
			<th>Field</th>
			<th>Type</th>
			<th>Null</th>
			<th>Key</th>
			<th>Default</th>
			<th>Extra</th>	
		</tr>
	<?php if (count((array)$this->_vars['table']['info'])): foreach ((array)$this->_vars['table']['info'] as $this->_vars['v']): ?>
		<tr>
			<td><?php echo tpl_function_counter(array(), $this);?></td>
			<td><b><?php echo $this->_vars['v']['Field']; ?>
</b></td>
			<td><?php echo $this->_vars['v']['Type']; ?>
</td>
			<td><?php echo $this->_vars['v']['Null']; ?>
</td>
			<td><?php echo $this->_vars['v']['Key']; ?>
</td>
			<td><?php echo $this->_vars['v']['Default']; ?>
</td>
			<td><?php echo $this->_vars['v']['Extra']; ?>
</td>	
		</tr>
	<?php endforeach; endif; ?>
	</table>
<?php endif; ?>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>