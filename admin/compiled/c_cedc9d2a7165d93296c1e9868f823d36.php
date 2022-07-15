<?php require_once('/home/sk-norma/sk-norma.ru/docs/module/tpl/src/plugins/modifier.numformat.php'); $this->register_modifier("numformat", "tpl_modifier_numformat");  require_once('/home/sk-norma/sk-norma.ru/docs/module/tpl/src/plugins/function.math.php'); $this->register_function("math", "tpl_function_math");  ?>
    <?php if (! empty ( $this->_vars['uri']['params']['page'] ) && ! empty ( $this->_vars['page']['list_pubs']['list'] )): ?>
        <?php echo tpl_function_math(array('equation' => "(x + y)",'x' => 1,'y' => $this->_vars['uri']['params']['page'],'assign' => "pp"), $this);?>
        <div class="col-md-12"><h3 class="text-center">Страница <?php echo $this->_vars['pp']; ?>
</h3></div>
    <?php endif; ?>
	
    <?php if (! empty ( $this->_vars['page']['list_pubs']['list'] )): ?>   

        <?php if ($this->_vars['page']['list_pubs']['spec'] == 'connected'): ?>
            <?php if (! empty ( $this->_vars['site']['sys_ptpubs_connected'] )): ?>
                <?php echo $this->_vars['site']['sys_ptpubs_connected']; ?>

            <?php else: ?>
                <div class="title">Публикации:</div>
            <?php endif; ?>            
        <?php endif; ?>    

        <?php $this->assign('ar', $this->_vars['page']['list_pubs']['options']); ?>           
        <?php if (count((array)$this->_vars['page']['list_pubs']['list'])): foreach ((array)$this->_vars['page']['list_pubs']['list'] as $this->_vars['v']): ?>
            <?php $this->assign('id', $this->_vars['v']['id']); ?>
            <div class="col-md-6">
                
                
                <div class="post">
                    <div class="description">

						<?php if (! empty ( $this->_vars['v']['pic']['small']['url'] )): ?><a href="<?php echo $this->_vars['v']['link']; ?>
"><img width="<?php echo $this->_vars['v']['pic']['small']['width']; ?>
" class="wrap-image-left" src="<?php echo $this->_vars['v']['pic']['small']['url']; ?>
" alt="<?php echo $this->_run_modifier($this->_vars['v']['title'], 'escape', 'plugin', 1); ?>
"></a><?php endif; ?>
						<h4><?php if (! empty ( $this->_vars['v']['icon'] )): ?><i class="fa fa-<?php echo $this->_vars['v']['icon']; ?>
"></i><?php endif; ?> <a href="<?php echo $this->_vars['v']['link']; ?>
"><?php echo $this->_vars['v']['title']; ?>
</a></h4>
                        <?php if (! empty ( $this->_vars['ar'][$this->_vars['id']]['list'] )): ?>
                            <ul class="list-unstyled">
                            <?php if (count((array)$this->_vars['ar'][$this->_vars['id']]['list'])): foreach ((array)$this->_vars['ar'][$this->_vars['id']]['list'] as $this->_vars['o']): ?>

                                <?php if ($this->_vars['o']['alias'] != 'city'): ?>
									<?php if (empty ( $this->_vars['o']['value'] )):  $this->assign('value', "?"); ?>
									<?php elseif ($this->_vars['o']['type'] == "int"):  $this->assign('value', $this->_run_modifier($this->_vars['o']['value'], 'numformat', 'plugin', 1)); ?>
									<?php else:  $this->assign('value', $this->_vars['o']['value']);  endif; ?>
                                    <li><span data-toggle="tooltip" data-placement="top" title="<?php if (empty ( $this->_vars['o']['value2'] )):  echo $this->_vars['o']['title'];  else:  echo $this->_vars['o']['group_title'];  endif; ?>" data-original-title="Tooltip on top"><?php if (! empty ( $this->_vars['o']['icon'] )): ?><span data-toggle="tooltip" data-placement="top" title="<?php echo $this->_vars['o']['title']; ?>
" data-original-title="Tooltip on top"><?php echo $this->_vars['o']['icon']; ?>
 <?php if (! empty ( $this->_vars['value'] )):  echo $this->_vars['value'];  echo $this->_vars['o']['after'];  else: ?>?<?php endif; ?></span><?php else: ?>
										<?php if (empty ( $this->_vars['o']['value2'] )): ?>
										<?php echo $this->_vars['o']['title']; ?>
: <?php echo $this->_vars['value']; ?>

										<?php else: ?>
										<?php echo $this->_vars['value']; ?>
 - <?php echo $this->_vars['o']['value2']; ?>

										<?php endif; ?>
									<?php endif; ?> <?php echo $this->_vars['o']['after']; ?>
</span></li>                                    
                                <?php endif; ?>
                                                                                
                            <?php endforeach; endif; ?>
                            </ul>
                        <?php endif; ?>
                        <?php if (! empty ( $this->_vars['v']['intro'] )): ?><p align="justify"><?php echo $this->_vars['v']['intro']; ?>
</p><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php echo tpl_function_cycle(array('advance' => "2",'values' => "1,2",'assign' => "cycle"), $this);?>
            <?php if ($this->_vars['cycle'] == 2): ?><div style="clear:left;"></div><hr><?php endif; ?>
        <?php endforeach; endif; ?>
        
        <?php if ($this->_vars['cycle'] == 1): ?>
            <div style="clear:left;"></div>
        <?php endif; ?>

        <?php if ($this->_vars['page']['list_pubs']['all'] > $this->_run_modifier($this->_vars['page']['list_pubs']['list'], 'count', 'PHP', 0)): ?>
            <p style="text-align:right;"><small><?php echo $this->_run_modifier($this->_vars['page']['list_pubs']['list'], 'count', 'PHP', 0); ?>
 из <?php echo $this->_vars['page']['list_pubs']['all']; ?>
</small></p>
            <?php echo $this->_vars['page']['list_pubs']['pages']; ?>

        <?php endif; ?>
        
    <?php endif; ?>
