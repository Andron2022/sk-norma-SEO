<?php require_once('/home/sk-norma/new.sk-norma.ru/docs/module/tpl/src/plugins/modifier.lastkey.php'); $this->register_modifier("lastkey", "tpl_modifier_lastkey");  ?>
<?php if (! empty ( $this->_vars['page']['breadcrumbs'] )): ?>
    <ul class="page-breadcrumb breadcrumb mt-0 pt-0 pb-0 mb-5">
    <?php if (count((array)$this->_vars['page']['breadcrumbs'])): foreach ((array)$this->_vars['page']['breadcrumbs'] as $this->_vars['v']): ?>
        <?php if ($this->_run_modifier($this->_vars['v'], 'count', 'PHP', 0) > 0): ?>
                <li><a href="<?php echo $this->_vars['site']['site_url']; ?>
/" class="breadcrumb">Главная</a></li> 
				  <?php if (count((array)$this->_vars['v'])): foreach ((array)$this->_vars['v'] as $this->_vars['k'] => $this->_vars['v2']): ?>
					  <?php if (empty ( $this->_vars['uri']['params'] ) && $this->_vars['k'] == $this->_run_modifier($this->_vars['v'], 'lastkey', 'plugin', 0)): ?>
						<li class="title"><i class="fa fa-angle-right"></i> <span><?php echo $this->_vars['v2']['title']; ?>
</span></li>
					  <?php else: ?>
						  <li><i class="fa fa-angle-right"></i> <a href="<?php echo $this->_vars['v2']['link']; ?>
" class="breadcrumb"><?php echo $this->_vars['v2']['title']; ?>
</a></li>
					  <?php endif; ?>
				  <?php endforeach; endif; ?>
        <?php endif; ?>
    <?php endforeach; endif; ?>
    </ul>
<?php endif; ?>