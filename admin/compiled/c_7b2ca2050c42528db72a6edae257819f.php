<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.compare.php'); $this->register_modifier("compare", "tpl_modifier_compare");  require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>


<h1 class="mt-0"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "block_types",'key3' => "options"), $this); if (! empty ( $this->_vars['list_options'] )): ?> (<?php echo $this->_run_modifier($this->_vars['list_options'], 'count', 'PHP', 0); ?>
)<?php endif; ?></h1>
<p><a href="?action=products&do=add_option<?php if (! empty ( $_GET['group'] )): ?>&gid=<?php echo $_GET['group'];  endif; ?>"><i class="fa fa-plus"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "add"), $this);?></a></p>

<table width="80%">
  <tr>
    <td colspan="9">
<blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "option_help"), $this);?></blockquote>


<?php if (isset ( $_GET['deleted'] )): ?>
  <blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "rows_deleted"), $this);?>: <?php echo $_GET['deleted']; ?>
</blockquote>
<?php endif;  if (isset ( $_GET['updated'] )): ?>
  <blockquote><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "db",'key3' => "info_updated"), $this);?></blockquote>
<?php endif; ?>


<?php if (! empty ( $this->_vars['filter']['groups'] ) || ! empty ( $this->_vars['filter']['categs'] )): ?>

      <table width="100%">
        <tr>

          <?php if ($this->_run_modifier($this->_vars['filter']['categs'], 'count', 'PHP', 0) > 0): ?>
          <form method="get">
            <td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "fb",'key3' => "filter"), $this);?>:</td>
            <td>
            <select style="width: 200px; word-break: break-all;" name="" onchange="top.location=this.value">  
              <option value="?action=products&do=options">- <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "all",'case' => "lower"), $this);?></option>
			  
			<?php if (! empty ( $this->_vars['site_vars']['_pages'] )): ?>
				<?php if (count((array)$this->_vars['site_vars']['_pages'])): foreach ((array)$this->_vars['site_vars']['_pages'] as $this->_vars['v']): ?>
					<?php $this->assign('href', "?action=products&do=options&categ=".$this->_vars['v']['id']."&where=categ&group=0"); ?>
                  <?php if (isset ( $_GET['categ'] ) && isset ( $_GET['where'] )): ?>
                    <?php if ($_GET['categ'] == $this->_vars['v']['id'] && $_GET['where'] == "categ"): ?>
                      <?php $this->assign('sel', "selected='selected'"); ?>
                    <?php else: ?>
                      <?php $this->assign('sel', ""); ?>
                    <?php endif; ?>
                  <?php else: ?>
                      <?php $this->assign('sel', ""); ?>
                  <?php endif; ?>
				
				
					<?php if (! empty ( $this->_vars['v']['options'] )): ?>
						<option value="<?php echo $this->_vars['href']; ?>
" <?php echo $this->_vars['sel']; ?>
><?php if ($this->_vars['v']['level'] > 1): ?>
							<?php for($for1 = 1; ((1 < $this->_vars['v']['level']) ? ($for1 < $this->_vars['v']['level']) : ($for1 > $this->_vars['v']['level'])); $for1 += ((1 < $this->_vars['v']['level']) ? 1 : -1)):  $this->assign('current', $for1); ?> - <?php endfor; ?>
						<?php endif;  echo $this->_vars['v']['title']; ?>
</option>
					<?php elseif (! empty ( $this->_vars['v']['subcategs'] )): ?>
						<optgroup label="<?php if ($this->_vars['v']['level'] > 1): ?>
							<?php for($for1 = 1; ((1 < $this->_vars['v']['level']) ? ($for1 < $this->_vars['v']['level']) : ($for1 > $this->_vars['v']['level'])); $for1 += ((1 < $this->_vars['v']['level']) ? 1 : -1)):  $this->assign('current', $for1); ?> - <?php endfor; ?>
						<?php endif;  echo $this->_vars['v']['title']; ?>
"></optgroup>
					<?php endif; ?>
				<?php endforeach; endif; ?>
			<?php endif; ?>			 
			  
            </select>
            </td>
          </form>
          <?php endif; ?>

          <td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "group"), $this);?>:</td>
          <form method="get">
          <input type="hidden" name="action" value="products">
          <input type="hidden" name="do" value="options">
          <?php if (isset ( $_GET['categ'] )): ?><input type="hidden" name="categ" value="<?php echo $_GET['categ']; ?>
"><?php endif; ?>
          <?php if (isset ( $_GET['where'] )): ?><input type="hidden" name="where" value="<?php echo $_GET['where']; ?>
"><?php endif; ?>
          <td nowrap>
            <?php if ($this->_run_modifier($this->_vars['filter']['groups'], 'count', 'PHP', 0) > 0): ?>
              <select name="group">
                <option value="0">- <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "all",'case' => "lower"), $this);?></option>
              <?php if (count((array)$this->_vars['filter']['groups'])): foreach ((array)$this->_vars['filter']['groups'] as $this->_vars['gr']): ?>
                <?php if (isset ( $_GET['group'] ) && $_GET['group'] == $this->_vars['gr']['id']): ?>
                    <?php $this->assign('sel', "selected='selected'"); ?>
                <?php else: ?>
                    <?php $this->assign('sel', ""); ?>
                <?php endif; ?>
                <option value="<?php echo $this->_vars['gr']['id']; ?>
" <?php echo $this->_vars['sel']; ?>
><?php echo $this->_vars['gr']['title']; ?>
 (<?php echo $this->_vars['gr']['to_show']; ?>
)</option>
              <?php endforeach; endif; ?>
              </select>
            <?php endif; ?>
          </select>   

		  <?php if (! empty ( $_GET['group'] )): ?>
			<a href="?action=products&do=option_group&id=<?php echo $_GET['group']; ?>
"><i class="fa fa-info-circle"></i></a>
		  <?php endif; ?>
		  

		  
          </td>
          <td><input class="small" type="submit" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "apply"), $this);?> »"></td>
          </form>
        </tr>
        </table>
        
<?php endif; ?>        
      </td>
    </tr>

<?php if (! empty ( $this->_vars['list_options'] )): ?>
    <tr>
      <th width="30">#</th>
      <th width="30"><i class="fa fa-edit"></i></th>
      <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "elements",'key3' => "name"), $this);?></th>
      <th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "synonim"), $this);?></th>
      <th width="100"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "type"), $this);?></th>
      <th width="80"><i class="fa fa-filter" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "used_in_filter"), $this);?>"></i></th>
      <th width="80"><i class="fa fa-bars" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "used_in_list"), $this);?>"></i></th>
      <th width="80"><i class="fa fa-sort" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "status",'key3' => "sort"), $this);?>"></i></th>
      <th width="80"><i class="fa fa-trash"></i> <INPUT onclick="CheckAll(this,'delopt[]')" type="checkbox" /></th>
    </tr>

    <form method="post" name=form1>
    <?php if (count((array)$this->_vars['list_options'])): foreach ((array)$this->_vars['list_options'] as $this->_vars['value']): ?>
      <?php $this->assign('href', "?action=products&do=options&id=".$this->_vars['value']['id']); ?>
      <?php $this->assign('href_group', "?action=products&do=option_group&id=".$this->_vars['value']['group']); ?>
      
      <tr <?php echo tpl_function_cycle(array('values' => " ,class='odd'"), $this);?>">
        <td><a href="<?php echo $this->_vars['href']; ?>
"><?php echo $this->_vars['value']['id']; ?>
</a></td>
        <td align="center"><a href="<?php echo $this->_vars['href']; ?>
"><i class="fa fa-pencil"></i></a></td>
        <td><a href="<?php echo $this->_vars['href']; ?>
"><?php echo $this->_vars['value']['title']; ?>
</a>
		<?php if (empty ( $_GET['group'] ) && ! empty ( $this->_vars['value']['group_title'] )): ?><br><small><?php echo $this->_vars['value']['group_title']; ?>
</small><?php endif; ?>
		</td>
        <td><a href="<?php echo $this->_vars['href']; ?>
"><?php echo $this->_vars['value']['alias']; ?>
</a></td>
        <td><a href="<?php echo $this->_vars['href']; ?>
"><?php echo $this->_vars['value']['type']; ?>
</a></td>
        <td align="center"><a href="<?php echo $this->_vars['href']; ?>
"><?php echo $this->_run_modifier($this->_vars['value']['show_in_filter'], 'compare', 'plugin', 1, "1", "<i class='fa fa-check'></i>", "-"); ?>
</a></td>
        <td align="center"><a href="<?php echo $this->_vars['href']; ?>
"><?php echo $this->_run_modifier($this->_vars['value']['show_in_list'], 'compare', 'plugin', 1, "1", "<i class='fa fa-check'></i>", "-"); ?>
</a></td>
        <td align="center"><input type="text" size="4" name="sort[<?php echo $this->_vars['value']['id']; ?>
]" value="<?php echo $this->_vars['value']['sort']; ?>
"></td>
        <td align="center"><input onclick="return CheckCB(this);" type="checkbox" name="delopt[]" value="<?php echo $this->_vars['value']['id']; ?>
"></td>
      </tr>
    <?php endforeach; endif; ?>
    
    <tr>
      <td colspan="9" align="right"><input type="submit" name="update" value="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "update"), $this);?>"></th>
    </tr>
    
    
    </form>
<?php endif; ?>
</table>

<?php echo $this->_vars['pages']; ?>


<?php if (empty ( $this->_vars['list_options'] )): ?>
<p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "list_empty"), $this);?></p>
<?php endif; ?>


<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>