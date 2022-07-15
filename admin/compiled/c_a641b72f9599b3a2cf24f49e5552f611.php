<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.truncate.php'); $this->register_modifier("truncate", "tpl_modifier_truncate");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "comments"), $this);?></h1>

<p><a href="?action=comments&id=0"><i class="fa fa-plus"></i></a> <a href="?action=comments&id=0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a></p> 


  <?php if (! empty ( $_GET['deleted'] )): ?>
    <table width="80%"><tr><td><blockquote><?php echo $_GET['deleted']; ?>
 <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "deleted"), $this);?></blockquote></td></tr></table>
  <?php endif; ?>

  <?php if (! empty ( $_GET['confirmed'] )): ?>
    <table width="80%"><tr><td><blockquote><?php echo $_GET['confirmed']; ?>
 <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "updated"), $this);?></blockquote></td></tr></table>
  <?php endif; ?>
  
  <?php if ($this->_run_modifier($this->_vars['filter_comments'], 'count', 'PHP', 0) > 1): ?>
    <form method="get">
      <input type="hidden" name="action" value="comments">
      <?php echo '
      <select onChange="if(this.options[this.selectedIndex].value!=\'\'){window.location=this.options[this.selectedIndex].value}else{this.options[selectedIndex=0];}">
      '; ?>

         <option value="?action=comments">- <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "filter"), $this);?></option>
        <?php if (count((array)$this->_vars['filter_comments'])): foreach ((array)$this->_vars['filter_comments'] as $this->_vars['filter']): ?>
          <?php if ($this->_vars['filter']['record_type'] == "pub"): ?>
            <?php $this->assign('tophref', "action=comments&record_id=".$this->_vars['filter']['pub_id']."&record_type=".$this->_vars['filter']['record_type']); ?>
            <?php if ($_SERVER['QUERY_STRING'] == $this->_vars['tophref']): ?>
              <?php $this->assign('selected', " selected"); ?>
            <?php else: ?>
              <?php $this->assign('selected', ""); ?>
            <?php endif; ?>
            <option value="?<?php echo $this->_vars['tophref']; ?>
" <?php echo $this->_vars['selected']; ?>
><?php echo $this->_vars['filter']['pub_title']; ?>
</option>
          <?php elseif ($this->_vars['filter']['record_type'] == "categ"): ?>
            <?php $this->assign('tophref', "action=comments&record_id=".$this->_vars['filter']['categ_id']."&record_type=".$this->_vars['filter']['record_type']); ?>
            <?php if ($_SERVER['QUERY_STRING'] == $this->_vars['tophref']): ?>
              <?php $this->assign('selected', " selected"); ?>
            <?php else: ?>
              <?php $this->assign('selected', ""); ?>
            <?php endif; ?>
            <option value="?<?php echo $this->_vars['tophref']; ?>
" <?php echo $this->_vars['selected']; ?>
><?php echo $this->_vars['filter']['categ_title']; ?>
</option>

          <?php elseif ($this->_vars['filter']['record_type'] == "catalog"): ?>
            <?php $this->assign('tophref', "action=comments&record_id=".$this->_vars['filter']['catalog_id']."&record_type=".$this->_vars['filter']['record_type']); ?>
            <?php if ($_SERVER['QUERY_STRING'] == $this->_vars['href']): ?>
              <?php $this->assign('selected', " selected"); ?>
            <?php else: ?>
              <?php $this->assign('selected', ""); ?>
            <?php endif; ?>
            <option value="?<?php echo $this->_vars['tophref']; ?>
" <?php echo $this->_vars['selected']; ?>
><?php echo $this->_vars['filter']['catalog_title']; ?>
</option>
          <?php elseif ($this->_vars['filter']['record_type'] == "product"): ?>
            <?php $this->assign('tophref', "action=comments&record_id=".$this->_vars['filter']['product_id']."&record_type=".$this->_vars['filter']['record_type']); ?>
            <?php if ($_SERVER['QUERY_STRING'] == $this->_vars['tophref']): ?>
              <?php $this->assign('selected', " selected"); ?>
            <?php else: ?>
              <?php $this->assign('selected', ""); ?>
            <?php endif; ?>
            <option value="?<?php echo $this->_vars['tophref']; ?>
" <?php echo $this->_vars['selected']; ?>
><?php echo $this->_vars['filter']['product_title']; ?>
</option>
          <?php elseif ($this->_vars['filter']['record_type'] == "comment"): ?>
            
          <?php else: ?>
          <?php endif; ?>
        <?php endforeach; endif; ?>
      </select> 
    </form>
  <?php endif; ?> 

  <?php if ($this->_run_modifier($this->_vars['list_comments'], 'count', 'PHP', 0) > 3): ?>
    <?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/pages.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>
  <?php endif; ?>

  
<?php if ($this->_run_modifier($this->_vars['list_comments'], 'count', 'PHP', 0) > 0): ?>
  <table width="80%">
	<form method=post name=form1>
    <tr>
      <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "status"), $this);?></th>
      <th><i class="fa fa-edit"></i></th>
      <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "date"), $this);?></th>
      <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "author"), $this);?></th>
      <th width="60%"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "text"), $this);?></th>
      <th><i class="fa fa-trash"></i> <INPUT onclick="CheckAll(this,'del[]')" type=checkbox></th>
    </tr>

    <?php if (count((array)$this->_vars['list_comments'])): foreach ((array)$this->_vars['list_comments'] as $this->_vars['value']): ?>
    <tr <?php echo tpl_function_cycle(array('values' => ",class=odd"), $this);?>>
        <td align="center"><a href="<?php echo $this->_vars['value']['rowhref']; ?>
"><?php if ($this->_vars['value']['active'] == 1): ?>
            <i class="fa fa-check" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "shown"), $this);?>"></i><?php elseif ($this->_vars['value']['active'] == 0): ?>
			<i class="fa fa-question-circle" style="color:red;" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "to_moderate"), $this);?>"></i>
			<?php else: ?><i class="fa fa-minus" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "blocked"), $this);?>"></i><?php endif; ?></a></td>
        <td align="center"><a href="<?php echo $this->_vars['value']['rowhref']; ?>
"><i class="fa fa-pencil"></i></a></td>
        <td><a href="<?php echo $this->_vars['value']['rowhref']; ?>
"><?php echo $this->_vars['value']['inserted']; ?>
</a> 
            <?php if ($this->_vars['value']['active'] == 1 && $this->_vars['value']['record_type'] != 'comment'): ?><a href="<?php echo $this->_vars['value']['rowhref']; ?>
&add=1" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add_subcomment"), $this);?>"><i class="fa fa-reply"></i></a><?php endif; ?>
        </td>
        <td><a href="<?php echo $this->_vars['value']['rowhref']; ?>
"><?php echo $this->_vars['value']['u_email'];  if (! empty ( $this->_vars['value']['u_name'] )): ?> (<?php echo $this->_vars['value']['u_name']; ?>
)<?php endif; ?></a></td>
        <td><?php if (! empty ( $this->_vars['value']['files_qty'] )): ?><i class="fa fa-paperclip" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "there_files"), $this);?>"></i> <?php endif;  if (! empty ( $this->_vars['value']['fotos_qty'] )): ?><i class="fa fa-camera" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "there_pics"), $this);?>"></i> <?php endif; ?><a href="<?php echo $this->_vars['value']['rowhref']; ?>
"><?php echo $this->_run_modifier($this->_vars['value']['comment_text'], 'truncate', 'plugin', 1, 150); ?>
</a></td>
        <td align=center><INPUT type=checkbox name=del[] value="<?php echo $this->_vars['value']['id']; ?>
"></td>
    </tr>
    <?php endforeach; endif; ?>

    <tr>
      <td colspan=3><input type="submit" name="confirm_comments" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "ok_selected"), $this);?>" /></td>
      <td colspan=3 align=right><input type="submit" class="small" name="delete" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "del_selected"), $this);?>" onclick="return confirm('<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "really"), $this);?>')" /></td></tr>
  </form></table>

<?php else: ?>
<blockquote><p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "set",'key3' => "list_empty"), $this);?></p></blockquote>
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