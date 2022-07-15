<?php ?>                       
    <?php if (! empty ( $this->_vars['page']['list_comments'] ) && empty ( $this->_vars['uri']['params']['page'] )): ?>
        <div style="clear:both;"></div>   
        <span class="commentsNumber"><span class="number"><?php echo $this->_run_modifier($this->_vars['page']['list_comments'], 'count', 'PHP', 0); ?>
</span>  <span class="text"><?php echo GetMessageTpl(array('word' => "comment",'qty' => $this->_run_modifier($this->_vars['page']['list_comments'], 'count', 'PHP', 0)), $this);?></span></span>
        <hr>
        
        <section class="commentList">
            <ul class="commentList list-unstyled">
            
                <?php if (count((array)$this->_vars['page']['list_comments'])): foreach ((array)$this->_vars['page']['list_comments'] as $this->_vars['k'] => $this->_vars['v']): ?>
                    <a name="comm<?php echo $this->_vars['k']; ?>
"></a>
                            <li>
                                <div class="oneComment">
                                    <div class="media">
                                        <a class="pull-left" href="#comm<?php echo $this->_vars['k']; ?>
">
                                            <img class="media-object" src="<?php echo $this->_vars['tpl']; ?>
assets/images/content/agent3.jpg" alt="">                                 
                                        </a>

                                        <div class="media-body">
                                            <div class="inner-body">
                                                <h3 class="media-heading"><?php echo $this->_vars['v']['title']; ?>

                                                    <small class="date"><?php echo $this->_vars['v']['date']; ?>
 <?php echo $this->_vars['v']['time']; ?>
</small>
                                                </h3>
                                                <p><?php echo $this->_vars['v']['message']; ?>
</p>
                                                <a href="#comm<?php echo $this->_vars['k']; ?>
" class="btn btn-brick reply-button">#</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- / oneComment -->
                                
                                <?php if (! empty ( $this->_vars['v']['sub_message'] )): ?>
                                <ol class="children">
                                    <li>
                                        <div class="oneComment">
                                            <div class="media ">
                                                <a class="pull-left" href="#comm<?php echo $this->_vars['k']; ?>
">
                                                    <img class="media-object" src="<?php echo $this->_vars['tpl']; ?>
assets/images/content/agent7.jpg" alt=" ">
                                                </a>

                                                <div class="media-body">
                                                    <div class="inner-body">
                                                        <h3 class="media-heading"><?php echo $this->_vars['v']['sub_name']; ?>

                                                            <small class="date"><?php echo $this->_vars['v']['sub_date']; ?>
 <?php echo $this->_vars['v']['sub_time']; ?>
</small>
                                                        </h3>             
                                                        <p><?php echo $this->_vars['v']['sub_message']; ?>
</p>
                                                        <a href="#comm<?php echo $this->_vars['k']; ?>
" class="btn btn-brick reply-button">#</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- / oneComment -->
                                    </li>
                                </ol>
                                <?php endif; ?>                                
                            </li>
                <?php endforeach; endif; ?>
            </ul>
        </section>                           
        
    <?php endif; ?>
