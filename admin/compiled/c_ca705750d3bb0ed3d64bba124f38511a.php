<?php require_once('/home/sk-norma/new.sk-norma.ru/docs/module/tpl/src/plugins/modifier.date.php'); $this->register_modifier("date", "tpl_modifier_date");  ?>									
										
									</div><!-- END COL-9 -->
								</div><!-- END ROW -->
								<!-- END PAGE CONTENT INNER -->
							</div>
                            <!-- END PAGE CONTENT BODY -->
                        </div>
                        <!-- END CONTENT -->
                    </div>
                    <!-- END CONTAINER -->
                </div>
            </div>
            <div class="page-wrapper-row">
                <div class="page-wrapper-bottom">
                    <!-- BEGIN FOOTER -->
                    <!-- BEGIN PRE-FOOTER -->
                    <div class="page-prefooter">
                        <div class="">
                            <div class="row m-0 p-0">
								<div class="col-md-6 col-sm-6 col-xs-12 footer-block left m-0 p-0">
                                    © 2002-<?php echo $this->_run_modifier(time(), 'date', 'plugin', 1, "Y"); ?>
 «<?php echo $this->_vars['site']['name_short']; ?>
»
									<br>тел.: <?php echo $this->_vars['site']['phone']; ?>
 многоканальный
									<br><?php echo get_blocks_tpl(array('where' => "footer",'name' => "bottom_left"), $this);?>
									
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12 footer-block right  m-0 p-0">
                                    <img src="<?php echo $this->_vars['tpl']; ?>
i/logo199.png" width="199" height="34" alt="<?php echo $this->_run_modifier($this->_vars['site']['name_short'], 'escape', 'plugin', 1); ?>
" title="<?php echo $this->_run_modifier($this->_vars['site']['name_short'], 'escape', 'plugin', 1); ?>
" class="logo-default">
									<div class="col-md-12 m-0 p-0 header-text" style="color: #8f8f8f; text-shadow: 0 -1px 0 #ffffff;">
											строительная компания
									</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END PRE-FOOTER -->
                    <!-- BEGIN INNER FOOTER -->
                    <div class="page-footer pt-5 center pl-0 pr-0 ml-0 mr-0" style="color: #8f8f8f; font-size:0.9em;">
                        <?php echo get_blocks_tpl(array('where' => "footer",'name' => "links"), $this);?>                        
                    </div>
                    <div class="scroll-to-top">
                        <i class="icon-arrow-up"></i>
                    </div>
                    <!-- END INNER FOOTER -->
                    <!-- END FOOTER -->
                </div>
            </div>
        </div>


		
		
		
		
		
        
        <!--[if lt IE 9]>
<script src="<?php echo $this->_vars['tpl']; ?>
assets/global/plugins/respond.min.js"></script>
<script src="<?php echo $this->_vars['tpl']; ?>
assets/global/plugins/excanvas.min.js"></script> 
<script src="<?php echo $this->_vars['tpl']; ?>
assets/global/plugins/ie8.fix.min.js"></script> 
<![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        
        <script src="<?php echo $this->_vars['tpl']; ?>
assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
		
		
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="<?php echo $this->_vars['tpl']; ?>
assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        
		<!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="<?php echo $this->_vars['tpl']; ?>
assets/layouts/layout3/scripts/layout.js" type="text/javascript"></script>
		
		<!--<script type="text/javascript" src="<?php echo $this->_vars['tpl']; ?>
js/main.js"></script>
		<script type="text/javascript" src="<?php echo $this->_vars['tpl']; ?>
js/menu.js"></script>-->
		
        <!-- END THEME LAYOUT SCRIPTS -->
		<script src="<?php echo $this->_vars['tpl']; ?>
js/custom.js"></script>

  

    </body>

</html>