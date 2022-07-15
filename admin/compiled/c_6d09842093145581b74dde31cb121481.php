<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.delhttp.php'); $this->register_modifier("delhttp", "tpl_modifier_delhttp");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "user_requests"), $this);?></h1>

<table width="80%"><tr>
<?php if (( $this->_run_modifier($this->_vars['feedback_list'], 'count', 'PHP', 0) > 0 || ! empty ( $_GET['site_id'] ) || ! empty ( $_GET['q'] ) ) && $this->_run_modifier($this->_vars['all_sites'], 'count', 'PHP', 0) > 1): ?>
	<td>
    
    <form method="get">
		<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "filter"), $this);?>: 
        <input type="hidden" name="action" value="feedback">
        <select name="" onchange="top.location=this.value">
            <option value="?action=feedback"> - <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "all_sites"), $this);?></option>
            <?php if (count((array)$this->_vars['all_sites'])): foreach ((array)$this->_vars['all_sites'] as $this->_vars['s']): ?>
                <option value="?action=feedback&site_id=<?php echo $this->_vars['s']['id']; ?>
"<?php if (! empty ( $_GET['site_id'] ) && $_GET['site_id'] == $this->_vars['s']['id']): ?> selected="selected"<?php endif; ?>><?php echo $this->_vars['s']['title']; ?>
 <?php echo $this->_vars['s']['url']; ?>
</option>  
            <?php endforeach; endif; ?>
        </select>
    </form>
	</td>
<?php endif; ?>

<?php if (( $this->_run_modifier($this->_vars['feedback_list'], 'count', 'PHP', 0) > 0 || ! empty ( $_GET['q'] ) )): ?>	
	<td>
    <form method="get">
		<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "prava",'key3' => "search"), $this);?> 
        <input type="hidden" name="action" value="feedback">
		<?php if (! empty ( $_GET['site_id'] )): ?>
			<input type="hidden" name="site_id" value="<?php echo $_GET['site_id']; ?>
">
		<?php endif; ?>		
        <input type="text" size="20" name="q" value="<?php if (! empty ( $_GET['q'] )):  echo $this->_run_modifier($this->_run_modifier($_GET['q'], 'trim', 'PHP', 1), 'escape', 'plugin', 1);  endif; ?>">
        <input type="submit" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "find"), $this);?>">
    </form>
	</td>
<?php endif; ?>
</tr></table>

<?php if (isset ( $_GET['deleted'] )): ?>
  <table width="80%"><tr><td><blockquote><?php echo $_GET['deleted']; ?>
 <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "deleted"), $this);?></blockquote></td></tr></table>
<?php endif;  if (isset ( $_GET['updated'] )): ?>
  <table width="80%"><tr><td><blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "updated"), $this);?></blockquote></td></tr></table>
<?php endif; ?>

<?php if ($this->_run_modifier($this->_vars['feedback_list'], 'count', 'PHP', 0) > 0): ?>

<?php if ($this->_run_modifier($this->_vars['feedback_list'], 'count', 'PHP', 0) > 10):  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/pages.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
  endif; ?>

<table width="100%"><form method=post name=form1>
  <tr>
  	<th>#</th>
  	<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "date"), $this);?></th>
  	<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "phone"), $this);?></th>
  	<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "author"), $this);?></th>
  	<th><i class="fa fa-envelope"></i></th>
  	<th><i class="fa fa-comments"></i></th>
	<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "status"), $this);?></th>
  	<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "mark"), $this);?></th>
	<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "website"), $this);?></th>
	<th><i class="fa fa-info-circle" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "sess_record"), $this);?>"></i></th>
  	<th><i class="fa fa-trash"></i> <INPUT onclick="CheckAll(this,'id[]')" type=checkbox></th>
	</tr>

	<?php if (count((array)$this->_vars['feedback_list'])): foreach ((array)$this->_vars['feedback_list'] as $this->_vars['value']): ?>
	<tr <?php echo tpl_function_cycle(array('values' => " ,class='odd'"), $this);?>>
		<td><a href="?action=feedback&id=<?php echo $this->_vars['value']['id']; ?>
"><?php if (! empty ( $this->_vars['value']['fav'] )): ?><small><i class="fa fa-star"></i></small> <?php endif; ?><small><?php echo $this->_vars['value']['ticket']; ?>
</small></a></td>
		<td><small><a href="?action=feedback&id=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_run_modifier($this->_vars['value']['sent'], 'date', 'plugin', 1, $this->_vars['site']['site_date_format']); ?>
 <?php echo $this->_run_modifier($this->_vars['value']['sent'], 'date', 'plugin', 1, $this->_vars['site']['site_time_format']); ?>
</a></small></td>
		<td><a href="?action=feedback&id=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_run_modifier($this->_vars['value']['phone'], 'stripslashes', 'PHP', 1); ?>
</a></td>
		<td><a href="?action=feedback&id=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_run_modifier($this->_vars['value']['name'], 'stripslashes', 'PHP', 1); ?>
</a></td>
		<td><a href="?action=feedback&id=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_run_modifier($this->_vars['value']['email'], 'stripslashes', 'PHP', 1); ?>
</a></td>
		<td align="center"><?php if (! empty ( $this->_vars['value']['comments'] )): ?><a href="?action=feedback&id=<?php echo $this->_vars['value']['id']; ?>
"><?php echo $this->_vars['value']['comments']; ?>
</a><?php endif; ?></td>
		<td class="center"><?php if ($this->_vars['value']['status'] == 0): ?><i class="fa fa-question-circle red" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "answer_wait"), $this);?>"></i><?php else: ?><i class="fa fa-check blue" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "answer_sent"), $this);?>"></i><?php endif; ?></td>
		<td><?php echo $this->_vars['value']['type']; ?>
</td>
		<td><?php if (empty ( $this->_vars['value']['site_url'] )): ?>-<?php else:  echo $this->_run_modifier($this->_vars['value']['site_url'], 'delhttp', 'plugin', 1);  endif; ?></td>
		<td align="center"><?php if (! empty ( $this->_vars['value']['visit_log_id'] )): ?><a href="?action=stat&id=<?php echo $this->_vars['value']['visit_log_id']; ?>
"><i class="fa fa-info"></i></a><?php endif; ?></td>
		<td align="center"><INPUT type=checkbox name=id[] value="<?php echo $this->_vars['value']['id']; ?>
"></td>
	</tr>
	<?php endforeach; endif; ?>

		<tr>
      <td colspan="11" align="right"><input type="submit" name="set_read" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "no_need_answer"), $this);?>"> <small><input type="submit" name="delete" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "delete"), $this);?>" onclick="<?php echo 'if(confirm(\'{lang key1="admin" key2="really"}\')){return true;}else{return false;}'; ?>
"></small></td>
    </tr>
	</form></table>
  
<?php else: ?>
  <table width="80%"><tr><td><blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "list_empty"), $this);?></blockquote></td></tr></table>
<?php endif; ?>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/pages.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>