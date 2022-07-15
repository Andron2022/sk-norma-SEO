<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/breadcrumbs.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>     


<?php if (empty ( $this->_vars['page']['breadcrumbs'] )):  if (! empty ( $this->_vars['page']['active'] )): ?><h1 class="mt-0 pt-0"><?php echo $this->_vars['page']['title']; ?>
</h1><?php endif;  endif; ?>




                            <?php if (empty ( $this->_vars['uri']['params']['page'] )): ?>
                            
                                <?php if (! empty ( $this->_vars['page']['intro'] ) || ! empty ( $this->_vars['page']['list_bottomcategs'] )): ?>
                                        <?php if (! empty ( $this->_vars['page']['intro'] )): ?><div class="note note-warning"><?php echo $this->_vars['page']['intro']; ?>
</div><?php endif; ?>
										
<?php if (! empty ( $this->_vars['page']['list_bottomcategs'] ) && ! empty ( $this->_vars['site']['right_menu'] ) && $this->_vars['page']['id'] != $this->_vars['site']['right_menu']): ?>
	<div class="row mt-10">
	<?php if (count((array)$this->_vars['page']['list_bottomcategs'])): foreach ((array)$this->_vars['page']['list_bottomcategs'] as $this->_vars['v']): ?>
		<?php if ($this->_vars['v']['id_parent'] == $this->_vars['page']['id']): ?>
		<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 pl-5 pr-5 pb-10">
			<div class="row">
				<div class="col-md-12 center">
					<a class="mt-info" href="<?php echo $this->_vars['v']['link']; ?>
"><img src="<?php echo $this->_vars['v']['pic']['1']['2']['url']; ?>
" class="img-responsive" /></a>
					<a href="<?php echo $this->_vars['v']['link']; ?>
"><h5 class="m-0 p-0"><?php echo $this->_vars['v']['title']; ?>
</h5></a>
				</div>
				
			</div>			
		</div>
		<div class="<?php echo tpl_function_cycle(array('values' => " , ,clearfix"), $this);?>"></div>
		<?php endif; ?>
	<?php endforeach; endif; ?>
	</div>
<?php endif; ?>

			
                                <?php endif; ?>								
                            <?php endif; ?>
							



<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/blocks.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

<?php if (! empty ( $this->_vars['page']['content'] )): ?>
		<?php if (! empty ( $this->_vars['site']['right_menu'] ) && ( $this->_vars['page']['id'] == $this->_vars['site']['right_menu'] || $this->_vars['page']['id_parent'] == $this->_vars['site']['right_menu'] )): ?>
		<div class="content-r">
			<ul class="menu"> 
				<?php if (count((array)$this->_vars['site']['default_menu'])): foreach ((array)$this->_vars['site']['default_menu'] as $this->_vars['v']): ?>
					<?php if ($this->_vars['v']['id_parent'] == $this->_vars['site']['right_menu']): ?>
					<li><a href="<?php echo $this->_vars['v']['link']; ?>
" <?php if ($this->_vars['page']['alias'] == $this->_vars['v']['alias']): ?>style="background-color:#cccccc; font-weight: bold; display:block; "<?php endif; ?>><?php echo $this->_vars['v']['title']; ?>
</a></li>
					<?php endif; ?>
				<?php endforeach; endif; ?>
				
			</ul>
		</div>
		<?php endif; ?>
				
	<?php echo $this->_vars['page']['content']; ?>

<?php endif; ?>


<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/include_pubs.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>



<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("pages/include_comments.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

			

<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>