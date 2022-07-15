<?php  $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

                <section class="post">
                    <div class="row">
                        <div class="col-md-12">
                        
                            <div class="title">
                                <?php echo $this->_vars['page']['title']; ?>

                            </div>
                        </div>
                    </div>
                </section>

                
				<ul>
					<?php if (count((array)$this->_vars['page']['list_categs'])): foreach ((array)$this->_vars['page']['list_categs'] as $this->_vars['v']): ?>
						<h4 class="motive"<?php if ($this->_vars['v']['padding'] > 0): ?> style="padding-left:<?php echo $this->_vars['v']['padding']; ?>
px;"<?php endif; ?>><a href="<?php echo $this->_vars['v']['link']; ?>
"><?php echo $this->_vars['v']['title']; ?>
</a></h4>
						<?php if (! empty ( $this->_vars['v']['list_products']['list'] )): ?>
							<?php if ($this->_vars['v']['padding'] > 0): ?><div style="padding-left:<?php echo $this->_vars['v']['padding']; ?>
px;"><?php endif; ?>
							<ul class="arrowlist columns" style="padding-left:25px;">
										<?php if (count((array)$this->_vars['v']['list_products']['list'])): foreach ((array)$this->_vars['v']['list_products']['list'] as $this->_vars['p']): ?>
											<li><a href="<?php echo $this->_vars['p']['link']; ?>
"><?php echo $this->_vars['p']['title']; ?>
</a></li>
										<?php endforeach; endif; ?>
							</ul>
							<?php if ($this->_vars['v']['padding'] > 0): ?></div><?php endif; ?>
						<?php endif; ?>

						<?php if (! empty ( $this->_vars['v']['list_pubs']['list'] )): ?>
							<?php if ($this->_vars['v']['padding'] > 0): ?><div style="padding-left:<?php echo $this->_vars['v']['padding']; ?>
px;"><?php endif; ?>
							<ul class="arrowlist columns" style="padding-left:25px;">
							
										<?php if (count((array)$this->_vars['v']['list_pubs']['list'])): foreach ((array)$this->_vars['v']['list_pubs']['list'] as $this->_vars['p']): ?>
											<li><a href="<?php echo $this->_vars['p']['link']; ?>
"><?php echo $this->_vars['p']['title']; ?>
</a></li>
										<?php endforeach; endif; ?>
							</ul>
							<?php if ($this->_vars['v']['padding'] > 0): ?></div><?php endif; ?>
						<?php endif; ?>
					<?php endforeach; endif; ?>
				</ul>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>