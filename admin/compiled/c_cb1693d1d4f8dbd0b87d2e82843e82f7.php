<?php require_once('/var/www/u1126524/data/www/sk-norma.ru/module/tpl/src/plugins/modifier.replace.php'); $this->register_modifier("replace", "tpl_modifier_replace");   $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("header.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>

                      <h3 class="uppercase"><?php echo $this->_vars['page']['title']; ?>
</h3>
                        <?php if (! empty ( $this->_vars['uri']['params']['sent'] )): ?>
                            <div class="alert alert-success"><i class="fa fa-check-circle-o"></i> <?php echo $this->_vars['page']['content']; ?>
</div>
                        <?php else: ?>
                            <?php if (! empty ( $this->_vars['page']['content'] )): ?><p><?php echo $this->_vars['page']['content']; ?>
<br><br></p><?php endif; ?>                        
                        <?php endif; ?>
                        
                        
                        
                        
                        <?php if (empty ( $this->_vars['uri']['params']['sent'] ) && empty ( $this->_vars['uri']['params']['done'] )): ?>
                            <form role="form" action="<?php echo $this->_vars['site']['site_url']; ?>
/feedback<?php echo constant('URL_END'); ?>
" method="post" >
                            <input type="hidden" name="fb[when]" value="<?php echo $this->_vars['site']['formkey']; ?>
">
                            <input type="hidden" name="fb[from_page]" value="<?php echo $this->_vars['uri']['site'];  echo $this->_vars['uri']['path']; ?>
">
                            <input type="hidden" name="fb[type]" value="feedback">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="info-icons">
                                        <?php if (! empty ( $this->_vars['site']['address'] )): ?><div class="table-row">
                                            <div class="table-cell">
                                                <i class="fa fa-map-marker"></i>
                                            </div>
                                            <div class="table-cell text-right">
                                                <?php echo $this->_vars['site']['address']; ?>

                                            </div>
                                        </div>
                                        <br>
                                        <?php endif; ?>
                                        
                                        <?php if (! empty ( $this->_vars['site']['phone'] )): ?>
                                        <div class="table-row">
                                            <div class="table-cell">
                                                <i class="fa fa-phone"></i>
                                            </div>
                                            <div class="table-cell text-right">
                                                <?php echo GetMessageTpl(array('key1' => "feedback",'key2' => "phone"), $this);?>: <a href="tel:<?php echo $this->_vars['site']['phone2']; ?>
"><?php echo $this->_vars['site']['phone']; ?>
</a><br>
                                                <?php if (! empty ( $this->_vars['site']['phone2'] )): ?><a href="tel:<?php echo $this->_vars['site']['phone2']; ?>
"><?php echo $this->_vars['site']['phone2']; ?>
</a><br><?php endif; ?>
                                                <?php if (! empty ( $this->_vars['site']['phone3'] )): ?><a href="tel:<?php echo $this->_vars['site']['phone3']; ?>
"><?php echo $this->_vars['site']['phone3']; ?>
</a><br><?php endif; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <br>
                                        <div class="table-row">
                                            <div class="table-cell">
                                                <i class="fa fa-envelope"></i>
                                            </div>
                                            <div class="table-cell text-right">
                                                <a href="mailto:<?php echo $this->_vars['site']['email_info']; ?>
"><?php echo $this->_vars['site']['email_info']; ?>
</a><br>
                                            </div>
                                        </div>
                                        <div class="table-row">
                                            <div class="table-cell">
                                                <i class="fa fa-globe"></i>
                                            </div>
                                            <div class="table-cell text-right">
                                                <a href="<?php echo $this->_vars['site']['site_url']; ?>
"><?php echo $this->_run_modifier($this->_vars['site']['site_url'], 'replace', 'plugin', 1, "http://"); ?>
</a><br>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <br>
                                            <ul class="socials flat">
                                                <?php if (! empty ( $this->_vars['site']['facebook'] )): ?><li>
                                                    <a href="<?php echo $this->_vars['site']['facebook']; ?>
" target="_blank" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Facebook"><i class="fa fa-facebook"></i></a>
                                                </li><?php endif; ?>
                                                <?php if (! empty ( $this->_vars['site']['twitter'] )): ?><li>
                                                    <a href="<?php echo $this->_vars['site']['twitter']; ?>
" target="_blank" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Twitter"><i class="fa fa-twitter"></i></a>
                                                </li><?php endif; ?>
                                                <?php if (! empty ( $this->_vars['site']['googleplus'] )): ?><li>
                                                    <a href="<?php echo $this->_vars['site']['googleplus']; ?>
" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Google +"><i class="fa fa-google-plus"></i></a>
                                                </li><?php endif; ?>
                                                <?php if (! empty ( $this->_vars['site']['vk'] )): ?><li>
                                                    <a href="<?php echo $this->_vars['site']['vk']; ?>
" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="VK"><i class="fa fa-vk"></i></a>
                                                </li><?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    

                                    
                                        <div class="row padding-xs-top">
                                            <div class="col-md-6 col-sm-6">
                                                <div class="form-group">
                                                    <input required="" type="email" name="fb[email]" class="form-control" placeholder="<?php echo GetMessageTpl(array('key1' => "feedback",'key2' => "email"), $this);?> *">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <div class="form-group">
                                                    <input  type="tel" name="fb[phone]" class="form-control" placeholder="<?php echo GetMessageTpl(array('key1' => "feedback",'key2' => "phone"), $this);?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-12">
                                                <div class="form-group">
                                                     <input  type="text" name="fb[name]" class="form-control" placeholder="<?php echo GetMessageTpl(array('key1' => "feedback",'key2' => "name"), $this);?> *">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-12">
                                                <div class="form-group">
                                                    <textarea required="" class="form-control" rows="4" name="fb[message]"  placeholder="<?php echo GetMessageTpl(array('key1' => "feedback",'key2' => "message_example"), $this);?>"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <input type="submit" class="btn btn-brick btn-xs large-padding  pull-right" name="go" value="<?php echo GetMessageTpl(array('key1' => "feedback",'key2' => "button"), $this);?>">
                                                
                                            </div>
                                        </div>                                    
                                </div>
                            </div>
                            </form>
                        <?php endif; ?>
                        
                        <?php if (! empty ( $this->_vars['uri']['params']['done'] ) && ! empty ( $this->_vars['page']['feedback'] )): ?>
                            <div class="post-info">
                                <hr>
                                <p><?php echo GetMessageTpl(array('key1' => "feedback",'key2' => "view",'key3' => "ticket"), $this);?> <a name="sentby"><?php echo $this->_vars['page']['feedback']['ticket']; ?>
</a> <i class="fa fa-clock-o"></i> <strong><?php echo $this->_vars['page']['feedback']['date']; ?>
</strong></span> <span class="pull-right"><span class="motive"><?php if ($this->_vars['page']['feedback']['status'] == 1): ?><a href="#" class="btn btn-success flat btn-lg"><?php echo GetMessageTpl(array('key1' => "feedback",'key2' => "view",'key3' => "answer_sent"), $this);?></a><?php else: ?><a href="#" class="btn btn-warning flat btn-lg"><?php echo GetMessageTpl(array('key1' => "feedback",'key2' => "view",'key3' => "wait_answer"), $this);?></a><?php endif; ?></span></span></p>
                                <hr>
                                <p><span><span><?php echo GetMessageTpl(array('key1' => "feedback",'key2' => "view",'key3' => "sentby"), $this);?> <a name="sentby"><?php echo $this->_vars['page']['feedback']['name']; ?>
</a>  <i class="fa fa-at"></i> <?php echo $this->_vars['page']['feedback']['email']; ?>
 
                                <?php if (! empty ( $this->_vars['page']['feedback']['phone'] )): ?><i class="fa fa-phone"></i> <?php echo $this->_vars['page']['feedback']['phone'];  endif; ?></span> <span class="pull-right"><span class="motive">-</span></span></p>
                                <hr>
                            </div>                        
                        <?php endif; ?>


<?php $_templatelite_tpl_vars = $this->_vars;
echo $this->_fetch_compile_include("footer.html", array());
$this->_vars = $_templatelite_tpl_vars;
unset($_templatelite_tpl_vars);
 ?>