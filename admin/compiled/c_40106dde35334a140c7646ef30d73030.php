<?php require_once('/home/sk-norma/new.sk-norma.ru/docs/module/tpl/src/plugins/modifier.int.php'); $this->register_modifier("int", "tpl_modifier_int");  require_once('/home/sk-norma/new.sk-norma.ru/docs/module/tpl/src/plugins/function.cycle.php'); $this->register_function("cycle", "tpl_function_cycle");   if (! empty ( $this->_vars['page']['blocks']['manual'] )): ?>
	<?php if (count((array)$this->_vars['page']['blocks']['manual'])): foreach ((array)$this->_vars['page']['blocks']['manual'] as $this->_vars['v']): ?>
		<?php if ($this->_vars['v']['gruppa'] != "photos" && $this->_vars['v']['gruppa'] != "categs"): ?>
			<?php if ($this->_vars['v']['gruppa'] == "slider_auto"): ?>
				<?php $this->assign('autoplay', "true"); ?>
			<?php else: ?>
				<?php $this->assign('autoplay', "0"); ?>
			<?php endif; ?>
			
			
			<?php if (! empty ( $this->_vars['v']['title'] )): ?>
				<h2><?php echo $this->_vars['v']['title']; ?>
</h2>
			<?php endif; ?>
			
				<?php if (! empty ( $this->_vars['v']['list_photos'] ) && ! empty ( $this->_vars['v']['html'] )): ?>
					<?php echo tpl_function_cycle(array('values' => "left,right",'assign' => "cnt"), $this);?>
					<div class="row">
						<?php if ($this->_vars['cnt'] == "right"): ?>
						<div class="col-md-8"><?php echo $this->_vars['v']['html']; ?>
</div>
						<?php endif; ?>
						<div class="col-md-4">
							<?php if ($this->_run_modifier($this->_vars['v']['pages'], 'int', 'plugin', 1) > 900 && $this->_run_modifier($this->_vars['v']['pages'], 'int', 'plugin', 1) < 10001): ?>
								<?php $this->assign('speed', $this->_run_modifier($this->_vars['v']['pages'], 'int', 'plugin', 1)); ?>
							<?php else: ?>
								<?php $this->assign('speed', "1000"); ?>
							<?php endif; ?>
							<div class="slider-solo-3 scroll-wheel" <?php if (! empty ( $this->_vars['autoplay'] )): ?>data-slick='{"autoplaySpeed": "<?php echo $this->_vars['speed']; ?>
", "autoplay": true}'<?php endif; ?>>							
						<?php if (count((array)$this->_vars['v']['list_photos'])): foreach ((array)$this->_vars['v']['list_photos'] as $this->_vars['pic']): ?>
							<?php if ($this->_vars['v']['qty'] > 5): ?>
								<?php $this->assign('img', $this->_vars['pic']['1']['url']); ?>
							<?php elseif ($this->_vars['v']['qty'] > 1): ?>
								<?php $this->assign('img', $this->_vars['pic']['2']['url']); ?>
							<?php else: ?>
								<?php $this->assign('img', $this->_vars['pic']['3']['url']); ?>
							<?php endif; ?>
						
						<div> <!---пустой блок нужен для стилей, которые присвоит слайдер --->
							<div class="slide">
								<a href="<?php echo $this->_vars['pic']['11']['url']; ?>
"  data-fancybox="gallery<?php echo $this->_vars['v']['id']; ?>
" data-caption="<?php echo $this->_vars['pic']['11']['title']; ?>
">
								<?php if ($this->_vars['v']['qty'] < 7): ?><div class="far fa-eye gallery-zoom-button white shadow"></div><?php endif; ?> <!---Вставить этот блок где нужна иконка внутри тега <a> --->
								<img class="scale-on"  src="<?php echo $this->_vars['img']; ?>
" alt="<?php echo $this->_run_modifier($this->_vars['pic']['11']['title'], 'escape', 'plugin', 1); ?>
" />
								</a>
							</div>
						</div>
						<?php endforeach; endif; ?>
							</div>
						</div>
						<?php if ($this->_vars['cnt'] == "left"): ?>
						<div class="col-md-8 pl-5"><?php echo $this->_vars['v']['html']; ?>
</div>
						<?php endif; ?>
					</div>
				<?php elseif (! empty ( $this->_vars['v']['list_photos'] )): ?>
					<?php if ($this->_run_modifier($this->_vars['v']['pages'], 'int', 'plugin', 1) > 900 && $this->_run_modifier($this->_vars['v']['pages'], 'int', 'plugin', 1) < 10001): ?>
						<?php $this->assign('speed', $this->_run_modifier($this->_vars['v']['pages'], 'int', 'plugin', 1)); ?>
					<?php else: ?>
						<?php $this->assign('speed', "1000"); ?>
					<?php endif; ?>
					<div class="slider-<?php if ($this->_vars['v']['qty'] == 2): ?>2<?php elseif ($this->_vars['v']['qty'] == 3): ?>3<?php elseif ($this->_vars['v']['qty'] == 4): ?>4-row<?php elseif ($this->_vars['v']['qty'] == 6): ?>4<?php elseif ($this->_vars['v']['qty'] == 12): ?>5<?php else: ?>solo<?php endif; ?> scroll-wheel" <?php if (! empty ( $this->_vars['autoplay'] )): ?>data-slick='{"autoplaySpeed": "<?php echo $this->_vars['speed']; ?>
", "autoplay": true, "infinite": true}'<?php endif; ?>>
						<?php if (count((array)$this->_vars['v']['list_photos'])): foreach ((array)$this->_vars['v']['list_photos'] as $this->_vars['pic']): ?>
						<?php if ($this->_vars['v']['qty'] > 5): ?>
							<?php $this->assign('img', $this->_vars['pic']['1']['url']); ?>
						<?php elseif ($this->_vars['v']['qty'] > 1): ?>
							<?php $this->assign('img', $this->_vars['pic']['2']['url']); ?>
						<?php else: ?>
							<?php $this->assign('img', $this->_vars['pic']['3']['url']); ?>
						<?php endif; ?>
							
						<div> <!---пустой блок нужен для стилей, которые присвоит слайдер --->
							<div class="slide">
								<a href="<?php echo $this->_vars['pic']['11']['url']; ?>
"  data-fancybox="gallery<?php echo $this->_vars['v']['id']; ?>
" data-caption="<?php echo $this->_vars['pic']['11']['title']; ?>
">
								<?php if ($this->_vars['v']['qty'] < 7): ?><div class="far fa-eye <?php if ($this->_vars['v']['qty'] < 3): ?>fa-2x<?php endif; ?> gallery-zoom-button white shadow"></div><?php endif; ?><!---Вставить этот блок где нужна иконка внутри тега <a> --->
								<img class="scale-on"  src="<?php echo $this->_vars['img']; ?>
" />
								</a>
							</div>
						</div>
						<?php endforeach; endif; ?>
					</div>
				<?php elseif (! empty ( $this->_vars['v']['html'] )): ?>
					<div class="row"><?php echo $this->_vars['v']['html']; ?>
</div>
				<?php else: ?>
					
				<?php endif; ?>
		<?php elseif ($this->_vars['v']['gruppa'] == "categs"): ?>	
			<?php if (! empty ( $this->_vars['v']['title'] )): ?>
				<h2><?php echo $this->_vars['v']['title']; ?>
</h2>
			<?php endif; ?>
			<?php if (count((array)$this->_vars['v']['list_photos'])): foreach ((array)$this->_vars['v']['list_photos'] as $this->_vars['cat']): ?>
				<span style="padding-left: <?php echo $this->_vars['cat']['padding']; ?>
px;">
					<?php if (! empty ( $this->_vars['cat']['visible'] )): ?>
					<a href="<?php echo $this->_vars['cat']['link']; ?>
"><?php echo $this->_vars['cat']['title']; ?>
</a>
					<?php else: ?>
					<?php echo $this->_vars['cat']['title']; ?>

					<?php endif; ?>
				</span><br>
			<?php endforeach; endif; ?>
		
		<?php endif; ?>
	<?php endforeach; endif;  endif; ?>

