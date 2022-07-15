<?php require_once('/home/sk-norma/sk-norma.ru/docs/module/tpl/src/plugins/function.img.php'); $this->register_function("img", "tpl_function_img");  require_once('/home/sk-norma/sk-norma.ru/docs/module/tpl/src/plugins/modifier.replace.php'); $this->register_modifier("replace", "tpl_modifier_replace");  require_once('/home/sk-norma/sk-norma.ru/docs/module/tpl/src/plugins/modifier.escape.php'); $this->register_modifier("escape", "tpl_modifier_escape");  ?><!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
<?php echo $this->_vars['site']['google1']; ?>

<?php echo $this->_vars['site']['metrika']; ?>

<?php echo $this->_vars['site']['Calltouch']; ?>

        <meta charset="utf-8" />
        <title><?php echo $this->_vars['page']['metatitle']; ?>
</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="<?php echo $this->_run_modifier($this->_vars['page']['description'], 'escape', 'plugin', 1); ?>
" name="description" />
        <meta content="<?php echo $this->_vars['site']['name_short']; ?>
" name="author" />
		<meta name="generator" content="Simpla.es" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
		

<script src="https://kit.fontawesome.com/6bc54e2d7b.js" crossorigin="anonymous"></script>
  
		<link rel="stylesheet" href="<?php echo $this->_vars['tpl']; ?>
css/font-awesome-animation.min.css">

        <link href="<?php echo $this->_vars['tpl']; ?>
assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $this->_vars['tpl']; ?>
assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $this->_vars['tpl']; ?>
assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
		
        <link href="<?php echo $this->_vars['tpl']; ?>
assets/global/plugins/morris/morris.css" rel="stylesheet" type="text/css" />
		
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="<?php echo $this->_vars['tpl']; ?>
assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="<?php echo $this->_vars['tpl']; ?>
assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />		
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="<?php echo $this->_vars['tpl']; ?>
assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $this->_vars['tpl']; ?>
assets/layouts/layout3/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo $this->_vars['tpl']; ?>
assets/layouts/layout3/css/themes/default.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="<?php echo $this->_vars['tpl']; ?>
css/style.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->_vars['tpl']; ?>
css/corrections.css" rel="stylesheet" type="text/css" />
		
		<link href="<?php echo $this->_vars['tpl_url']; ?>
assets/global/plugins/lightbox/css/lightbox.css" rel="stylesheet">
		
		<script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js"></script>
		
		<!-----Fancybox---->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />



    <!--slicks slider--->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->_vars['tpl']; ?>
js/fancy.js"></script>

    <!--Ловим Скролл мышью --->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>
	
	<link rel="stylesheet" type="text/css" href="<?php echo $this->_vars['tpl']; ?>
style.css">

        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="<?php echo $this->_vars['tpl']; ?>
favicon.ico" /> 
	</head>
    <!-- END HEAD -->

    <body class="page-container-bg-solid">
<?php echo $this->_vars['site']['google2']; ?>

        <div class="page-wrapper">
            <div class="page-wrapper-row">
                <div class="page-wrapper-top">
                    <!-- BEGIN HEADER -->
                    <div class="page-header">
                        <!-- BEGIN HEADER TOP -->
                        <div class="page-header-top mb-0 pb-0">
                            <div class="pb-5 pt-5">
							
								<div class="row">
									<div class="col-md-4 col-lg-3 col-xs-6 col-sm-6 left pl-0 pr-0 ml-0 mr-0">
										<div class="row pl-0 pr-0 ml-0 mr-0">
											<div class="col-md-12 left hidden-xs hidden-sm pl-0 pr-0 ml-0 mr-0">
												<a href="<?php echo $this->_vars['site']['site_url']; ?>
">
													<img src="<?php echo $this->_vars['tpl']; ?>
i/logo199.png" width="199" height="34" alt="СК НОРМА" title="СК НОРМА" class="logo-default">
												</a>
											</div>
											<div class="col-md-12 left hidden-lg hidden-md m-0 p-0 pl-15">
												<a href="<?php echo $this->_vars['site']['site_url']; ?>
">
													<img src="<?php echo $this->_vars['tpl']; ?>
i/logo199.png" alt="СК НОРМА" title="СК НОРМА" class="img-responsive">
												</a>
											</div>											
											
											<div class="col-md-12 header-text left hidden-xs hidden-sm  pl-0 pr-0 ml-0 mr-0">
												строительная&nbsp;компания
											</div>
											<div class="col-md-12 header-text-mobile left hidden-lg hidden-md m-0 p-0 pl-15">
												строительная&nbsp;компания
											</div>
										</div>		
									</div>
									
									<div class="col-lg-4 hidden-xs hidden-sm hidden-md header-text-center  pl-0 pr-0 ml-0 mr-0">
										Офисные перегородки от ведущего<br>Российского производителя
									</div>
						
									<div class="col-md-8 col-lg-5 col-xs-6 col-sm-6 right pl-0 pr-0 ml-0 mr-0">
<?php if ($this->_vars['page']['alias'] == "okna/pvkh-plastikovaia-rama" || $this->_vars['page']['alias'] == "okna/al-aliuminievaia-rama" || $this->_vars['page']['alias'] == "okna"): ?>
	<?php $this->assign('top_email', $this->_vars['site']['email_okna']); ?>			
<?php else: ?>
	<?php $this->assign('top_email', "sknorma@irline.su"); ?>
<?php endif; ?>


	<?php $this->assign('phone', $this->_vars['site']['phone']); ?>



										
										<div class="row hidden-md hidden-lg">
											<a href="tel:<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['phone'], 'strip_tags', 'PHP', 1), 'replace', 'plugin', 1, "/,-, ", ""); ?>
"><i class="fa fa-phone fa-2x mt-10 red"></i></a>
											<a href="javascript:;" class="menu-toggler mt-5" id="menu-toggler"></a>	
										</div>

										
										<div class="row hidden-lg hidden-sm hidden-xs header-phone red">
											<a href="tel:<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['phone'], 'strip_tags', 'PHP', 1), 'replace', 'plugin', 1, "/,-, ", ""); ?>
"><i class="fa fa-phone"></i> <?php echo $this->_vars['phone']; ?>
</a>
										</div>

										
										<div class="row hidden-md hidden-sm hidden-xs pl-0 pr-0 ml-0 mr-0">	
											<div class="col-md-12 m-0 p-0 right ">
												<a href="mailto:<?php echo $this->_vars['top_email']; ?>
" class="header-contact">
													<i class="fa fa-envelope"></i> <?php echo $this->_vars['top_email']; ?>

												</a>
											</div>
											<div class="col-md-12 header-phone m-0 p-0 right">
												<div class="header-phone m-0 p-0 ">
													<a href="tel:<?php echo $this->_run_modifier($this->_run_modifier($this->_vars['phone'], 'strip_tags', 'PHP', 1), 'replace', 'plugin', 1, "/,-, ", ""); ?>
"><i class="fa fa-phone"></i> <?php echo $this->_vars['phone']; ?>
</a> <span>многоканальный</span> 
												</div>		
											</div>
										</div>
									</div>
								</div>
                                <!-- BEGIN TOP NAVIGATION MENU -->                                
                            </div>
                        </div>
                        <!-- END HEADER TOP -->
						
                        <!-- BEGIN HEADER MENU -->
                        <div class="page-header-menu mt-0">
                            <div class="container center navbar-collapse">
                                <!-- BEGIN MEGA MENU -->
								<div class="hor-menu">
                                    <ul class="nav navbar-nav ">
                                       
<?php if (count((array)$this->_vars['site']['default_menu'])): foreach ((array)$this->_vars['site']['default_menu'] as $this->_vars['v']): ?>
	<?php if ($this->_vars['v']['id_parent'] == $this->_vars['site']['default_id_categ']): ?>
		<?php if (! empty ( $this->_vars['v']['child_categs'] ) && empty ( $this->_vars['v']['shop'] )): ?>
			<li class="menu-dropdown classic-menu-dropdown dropdown mr-0 pr-0">
				<a href="<?php echo $this->_vars['v']['link']; ?>
" class="hidden-xs hidden-sm hidden-md"><?php echo $this->_vars['v']['title']; ?>

					<i class="fa fa-angle-down"></i>
				</a>
				<a href="javascript:;" class="hidden-lg menu"><?php echo $this->_vars['v']['title']; ?>

					<i class="fa fa-angle-down"></i>
				</a>
			<ul class="dropdown-menu pull-left mr-0 pr-0">
								
			<?php if (count((array)$this->_vars['site']['default_menu'])): foreach ((array)$this->_vars['site']['default_menu'] as $this->_vars['v2']): ?>
				<?php if ($this->_vars['v2']['id_parent'] == $this->_vars['v']['id']): ?>
					<li><a href="<?php echo $this->_vars['v2']['link']; ?>
" class="nav-link" ><?php echo $this->_vars['v2']['title']; ?>
</a></li>
				<?php endif; ?>
			<?php endforeach; endif; ?>
			</ul>
			</li>
		<?php elseif (empty ( $this->_vars['v']['shop'] )): ?>
			<li class=""><a href="<?php echo $this->_vars['v']['link']; ?>
"><?php echo $this->_vars['v']['title']; ?>
</a></li>
		<?php endif; ?>
	<?php endif; ?>
<?php endforeach; endif; ?>


										
                                    </ul>
								</div>

                                <!-- END MEGA MENU -->
                            </div>
							
                        </div>
                        <!-- END HEADER MENU -->
						
						

						
						
						
						
						
						
						



<div class="hidden-lg hidden-md page-header-menu mt-0 p-0">
                            <div class="center">
                                <!-- BEGIN MEGA MENU -->
								<div class="hor-menu">
                                    <ul class="nav navbar-nav">
                                       
<?php if (count((array)$this->_vars['site']['default_menu'])): foreach ((array)$this->_vars['site']['default_menu'] as $this->_vars['v']): ?>
	<?php if ($this->_vars['v']['id_parent'] == $this->_vars['site']['default_id_categ']): ?>		
		<?php if (! empty ( $this->_vars['v']['child_categs'] )): ?>
			<li class="menu-dropdown classic-menu-dropdown <?php if (! empty ( $this->_vars['v']['shop'] )): ?>hidden-lg hidden-md<?php else: ?>hidden-xs hidden-sm<?php endif; ?> mr-0 pr-0">
				<a href="<?php echo $this->_vars['v']['link']; ?>
" class="hidden-xs hidden-sm hidden-md"><?php echo $this->_vars['v']['title']; ?>
55
					<i class="fa fa-angle-down"></i>
				</a>
				<a href="javascript:;" class="hidden-lg"><?php echo $this->_vars['v']['title']; ?>

					<i class="fa fa-angle-down"></i>
				</a>
			<ul class="dropdown-menu pull-left mr-0 pr-0">
								
			<?php if (count((array)$this->_vars['site']['default_menu'])): foreach ((array)$this->_vars['site']['default_menu'] as $this->_vars['v2']): ?>
				<?php if ($this->_vars['v2']['id_parent'] == $this->_vars['v']['id']): ?>
					<li><a href="<?php echo $this->_vars['v2']['link']; ?>
" class="nav-link"><?php echo $this->_vars['v2']['title']; ?>
</a></li>
				<?php endif; ?>
			<?php endforeach; endif; ?>
			</ul>
		<?php else: ?>
			<li class="hidden-xs hidden-sm"><a href="<?php echo $this->_vars['v']['link']; ?>
"><?php echo $this->_vars['v']['title']; ?>
</a>
		<?php endif; ?>
		</li>
	<?php endif; ?>
<?php endforeach; endif; ?>

										
                                    </ul>
								</div>

                                <!-- END MEGA MENU -->
                            </div>
							
                        </div>
						
						
						
						
                    </div>
                    <!-- END HEADER -->
                </div>
            </div>
            <div class="page-wrapper-row full-height">
                <div class="page-wrapper-middle">
                    <!-- BEGIN CONTAINER -->
                    <div class="page-container">
                        <!-- BEGIN CONTENT -->
                        <div class="page-content-wrapper">
							<div class="co1ntainer">
<a name="content"></a>							
								<div class="row m-0 p-0 ">
									<div class="col-md-3 m-0 p-0 relative-option pt-10">

                                        <div class="sidebar-scrolled-z">
<?php if (count((array)$this->_vars['site']['default_menu'])): foreach ((array)$this->_vars['site']['default_menu'] as $this->_vars['v']): ?>
	<?php if (! empty ( $this->_vars['v']['shop'] ) && $this->_vars['v']['level'] == 1): ?>
	<ul class="page-sidebar-menu hidden-xs hidden-sm mt-0 pt-0 ml-0 pl-0">
		<li class="level1"><?php echo $this->_vars['v']['title']; ?>
</li>
		<?php if (count((array)$this->_vars['site']['default_menu'])): foreach ((array)$this->_vars['site']['default_menu'] as $this->_vars['v2']): ?>
			<?php if ($this->_vars['v2']['id_parent'] == $this->_vars['v']['id']): ?>
				<li class="level2 <?php if (! empty ( $this->_vars['page']['alias'] ) && $this->_vars['page']['alias'] == $this->_vars['v2']['alias']): ?>active<?php endif; ?>"><a href="<?php echo $this->_vars['v2']['link']; ?>
"><?php echo $this->_vars['v2']['title']; ?>
</a></li>
				
				<?php if (( ! empty ( $this->_vars['page']['alias'] ) && $this->_vars['page']['alias'] == $this->_vars['v2']['alias'] ) || ( ! empty ( $this->_vars['page']['id_parent'] ) && $this->_vars['page']['id_parent'] == $this->_vars['v2']['id'] ) || ( ! empty ( $this->_vars['page']['id_parent2'] ) && $this->_vars['page']['id_parent2'] == $this->_vars['v2']['id'] )): ?>
				<?php if (count((array)$this->_vars['site']['default_menu'])): foreach ((array)$this->_vars['site']['default_menu'] as $this->_vars['v3']): ?>
					<?php if ($this->_vars['v3']['id_parent'] == $this->_vars['v2']['id']): ?>
				
						<li class="level3 <?php if ($this->_vars['page']['alias'] == $this->_vars['v3']['alias']):  endif; ?>" <?php if ($this->_vars['page']['alias'] == $this->_vars['v3']['alias']): ?>style="margin-left: 15px; background:#cccccc;"<?php else: ?>style="margin-left: 15px;"<?php endif; ?>><a href="<?php echo $this->_vars['v3']['link']; ?>
" class="" <?php if ($this->_vars['page']['alias'] == $this->_vars['v3']['alias'] || ! empty ( $this->_vars['v3']['child_categs'] )): ?>style="font-weight:bold;"<?php endif; ?>><?php echo $this->_vars['v3']['title']; ?>
</a></li>
						
						<?php if (! empty ( $this->_vars['v3']['child_categs'] ) && ( ( ! empty ( $this->_vars['page']['id_parent'] ) && $this->_vars['page']['id_parent'] == $this->_vars['v3']['id'] ) || ( ! empty ( $this->_vars['page']['id_parent2'] ) && $this->_vars['page']['id_parent2'] == $this->_vars['v3']['id'] ) || ( ! empty ( $this->_vars['page']['id'] ) && $this->_vars['page']['id'] == $this->_vars['v3']['id'] ) )): ?>
							<?php if (count((array)$this->_vars['site']['default_menu'])): foreach ((array)$this->_vars['site']['default_menu'] as $this->_vars['v4']): ?>
								<?php if ($this->_vars['v4']['id_parent'] == $this->_vars['v3']['id']): ?>
					<li class="level3 <?php if ($this->_vars['page']['alias'] == $this->_vars['v4']['alias']):  endif; ?>" <?php if ($this->_vars['page']['alias'] == $this->_vars['v4']['alias']): ?>style="margin-left: 25px; background:#cccccc;"<?php else: ?>style="margin-left: 25px;"<?php endif; ?>><a href="<?php echo $this->_vars['v4']['link']; ?>
" class="" <?php if ($this->_vars['page']['alias'] == $this->_vars['v4']['alias']):  endif; ?>><?php echo $this->_vars['v4']['title']; ?>
</a></li>
								<?php endif; ?>
							<?php endforeach; endif; ?>
						<?php endif; ?>
					<?php endif; ?>
				<?php endforeach; endif; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endforeach; endif; ?>
	</ul>
	<?php endif; ?>
<?php endforeach; endif; ?>	

<div style="background-color:#cccccc;">
	<?php echo $this->_vars['site']['sidebar']; ?>

	<?php if (! empty ( $_GET['id'] ) && $_GET['id'] == 1223): ?>
	<p>Текст для проверки выравнивания столбика, при разном разрешении экрана.</p>
	<p>Текст для проверки выравнивания столбика, при разном разрешении экрана.</p>
	<p>Текст для проверки выравнивания столбика, при разном разрешении экрана.</p>
	<p>Текст для проверки выравнивания столбика, при разном разрешении экрана.</p>
	<p>Текст для проверки выравнивания столбика, при разном разрешении экрана.</p>
	<?php endif; ?>
</div>



                                    </div>
									</div><!-- END COL-3 -->
									<div class="col-md-9 m-0 p-0 pt-10">









<?php if (! empty ( $this->_vars['page']['list_photos'] ) && ! empty ( $_GET['id'] )): ?>
<?php $this->assign('gid', $_GET['id']); ?>
<?php else: ?>
<?php $this->assign('gid', ""); ?>
<?php endif; ?>

<?php if (empty ( $this->_vars['site']['img_mainslider_height']['value'] )): ?>
	<?php $this->assign('height', 410); ?>
<?php else: ?>
	<?php $this->assign('height', $this->_vars['site']['img_mainslider_height']['value']); ?>
<?php endif; ?>

<?php if (! empty ( $this->_vars['page']['list_photos'] )): ?>				
<div id="carousel-custom" class="carousel slide <?php if (isset ( $this->_vars['page']['blocks']['sys']['photos']['qty'] ) && $this->_vars['page']['blocks']['sys']['photos']['qty'] > 1): ?>pl-<?php echo $this->_vars['page']['blocks']['sys']['photos']['qty']; ?>
0 pr-<?php echo $this->_vars['page']['blocks']['sys']['photos']['qty']; ?>
0<?php endif; ?>" data-ride="carousel"  <?php if ($this->_vars['page']['blocks']['sys']['photos']['extra'] > 900): ?>data-interval="<?php echo $this->_vars['page']['blocks']['sys']['photos']['extra']; ?>
"<?php else: ?>data-interval="false"<?php endif; ?>>
  <div class="carousel-outer"> 
    <!-- Wrapper for slides -->
    <div class="carousel-inner">
	<?php if (count((array)$this->_vars['page']['list_photos'])): foreach ((array)$this->_vars['page']['list_photos'] as $this->_vars['k'] => $this->_vars['v']): ?>
		<?php if (! empty ( $this->_vars['v']['11']['url'] )): ?>
			
			<?php echo tpl_function_img(array('url' => $this->_vars['v']['11']['path'],'width' => 878,'height' => $this->_vars['height'],'assign' => "img"), $this);?>
			
			<div class="item <?php if ($this->_vars['k'] == 1 && empty ( $this->_vars['gid'] )): ?>active<?php elseif ($this->_vars['gid'] == $this->_vars['k']): ?>active<?php endif; ?>"> <img src="<?php echo $this->_vars['img']['src']; ?>
" alt="" /> 
		
			<?php if (! empty ( $this->_vars['page']['breadcrumbs'] )): ?>
				<div class="head-img-button-catalog">
				<?php if (empty ( $this->_vars['gid'] ) || ( $this->_vars['gid'] == $this->_vars['k'] && empty ( $this->_vars['v']['11']['ext_link'] ) )): ?>
				<a href="/upload/catalog_irline/Irline_Catalog_2011[1_2].pdf" target="_blank">СКАЧАТЬ&nbsp;КАТАЛОГ</a>
				<?php elseif (! empty ( $this->_vars['gid'] ) && $this->_vars['gid'] == $this->_vars['k'] && ! empty ( $this->_vars['v']['11']['ext_link'] )): ?>
				
				<?php else: ?>
				<a href="/upload/catalog_irline/Irline_Catalog_2011[1_2].pdf" target="_blank">СКАЧАТЬ&nbsp;КАТАЛОГ</a>
				<?php endif; ?>
				</div>
			<?php else: ?><div class="head-img-button"><?php echo $this->_run_modifier($this->_vars['v']['11']['title'], 'escape', 'plugin', 1); ?>
</div>
			<?php endif; ?>
			
			<?php if (! empty ( $this->_vars['gid'] ) && ( $this->_vars['gid'] == $this->_vars['k'] && ! empty ( $this->_vars['v']['11']['title'] ) && ! empty ( $this->_vars['v']['11']['ext_desc'] ) )): ?>
			<div class="note note-dark m-0<?php if (empty ( $this->_vars['v']['11']['ext_link'] )): ?> pb-50<?php endif; ?>">
				<h1><?php echo $this->_vars['v']['11']['title']; ?>
</h1>
				<?php if (! empty ( $this->_vars['v']['11']['ext_h1'] )): ?><div class="text-center p-10">
				<p class="btn btn-circle red"><?php echo $this->_vars['v']['11']['ext_h1']; ?>
</p>
				</div><?php endif; ?>
				<?php echo $this->_vars['v']['11']['ext_desc']; ?>

			</div>
			<div style="clear:both;"></div>
			<?php endif; ?>			
			
			</div>
			

	
		<?php endif; ?>
	<?php endforeach; endif; ?>
	</div>

    <?php if ($this->_run_modifier($this->_vars['page']['list_photos'], 'count', 'PHP', 0) > 1): ?>
    <!-- Controls --> 
    <a class="left carousel-control" href="#carousel-custom" data-slide="prev"> <i class="fa fa-chevron-left fa-2x faa-tada animated"></i> </a> 
    <a class="right carousel-control" href="#carousel-custom" data-slide="next"> <i class="fa fa-chevron-right fa-2x faa-tada animated"></i> </a> 
	<?php endif; ?>
	</div>

	
	
</div>
<?php endif; ?>							

