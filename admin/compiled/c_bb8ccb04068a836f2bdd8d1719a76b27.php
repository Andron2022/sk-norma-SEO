<?php ?>          <?php if ($this->_vars['admin_vars']['multilang'] == 1): ?>
          <div class="langs"><i class="fa fa-language"></i> 
           <?php if (count((array)$this->_vars['langs'])): foreach ((array)$this->_vars['langs'] as $this->_vars['link'] => $this->_vars['lang']): ?>
            <?php if ($this->_vars['currentlang'] == $this->_vars['link']): ?>
                <b><span><?php echo $this->_vars['lang']; ?>
</span></b>
            <?php else: ?>
                <a href="?setlang=<?php echo $this->_vars['link']; ?>
"><?php echo $this->_vars['lang']; ?>
</a>
            <?php endif; ?>
           <?php endforeach; endif; ?>
          </div>
          <?php endif; ?>
