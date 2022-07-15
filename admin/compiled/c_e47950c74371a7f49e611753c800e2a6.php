<?php  if (isset ( $this->_vars['categ']['icon'] )): ?>
  <?php $this->assign('icon', $this->_vars['categ']['icon']);  elseif (isset ( $this->_vars['catalog']['icon'] )): ?>
  <?php $this->assign('icon', $this->_vars['catalog']['icon']);  elseif (isset ( $this->_vars['pub']['icon'] )): ?>
  <?php $this->assign('icon', $this->_vars['pub']['icon']);  else: ?>
  <?php $this->assign('icon', "");  endif; ?>

        <blockquote>
            <p><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "icons",'key3' => "supports"), $this);?> &lt; I &gt; .<br>
            <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "icons",'key3' => "for_example"), $this);?>, &lt;i class="fa fa-cog"&gt;&lt;/i&gt; <br>
            <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "icons",'key3' => "help"), $this);?><br>
            <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "icons",'key3' => "for_example"), $this);?>, <b>fa fa-cog fa-2x font-red</b></p>
        </blockquote>

        <table class="bordered" align="center">
		<tr class="odd">
          <td colspan="6" align="center"><b><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "icons",'key3' => "choose"), $this);?></b></td>
        </tr>
		<tr class="">
          <td colspan="6"><input type="text" name="icon_custom" value="<?php echo $this->_run_modifier($this->_vars['icon'], 'escape', 'plugin', 1); ?>
" style="width:100%;" /></td>
        </tr>
        <tr class="odd">
          <td><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "no"), $this);?> <input type="radio" name="icon" value=""<?php if ($this->_vars['icon'] == ""): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-home fa-2x"></i> <input type="radio" name="icon" value="fa fa-home"<?php if ($this->_vars['icon'] == "fa fa-home"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-cog fa-2x"></i> <input type="radio" name="icon" value="fa fa-cog"<?php if ($this->_vars['icon'] == "fa fa-cog"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-asterisk fa-2x"></i> <input type="radio" name="icon" value="fa fa-asterisk"<?php if ($this->_vars['icon'] == "fa fa-asterisk"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-comment fa-2x"></i> <input type="radio" name="icon" value="fa fa-comment"<?php if ($this->_vars['icon'] == "fa fa-comment"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-comments fa-2x"></i> <input type="radio" name="icon" value="fa fa-comments"<?php if ($this->_vars['icon'] == "fa fa-comments"): ?> checked<?php endif; ?> /></td>
        </tr>
        <tr class="odd">
          <td><i class="fa fa-camera fa-2x"></i> <input type="radio" name="icon" value="fa fa-camera"<?php if ($this->_vars['icon'] == "fa fa-camera"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-bar-chart fa-2x"></i> <input type="radio" name="icon" value="fa fa-bar-chart"<?php if ($this->_vars['icon'] == "fa fa-bar-chart"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-bell fa-2x"></i> <input type="radio" name="icon" value="fa fa-bell"<?php if ($this->_vars['icon'] == "fa fa-bell"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-bullhorn fa-2x"></i> <input type="radio" name="icon" value="fa fa-bullhorn"<?php if ($this->_vars['icon'] == "fa fa-bullhorn"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-calculator fa-2x"></i> <input type="radio" name="icon" value="fa fa-calculator"<?php if ($this->_vars['icon'] == "fa fa-calculator"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-check fa-2x"></i> <input type="radio" name="icon" value="fa fa-check"<?php if ($this->_vars['icon'] == "fa fa-check"): ?> checked<?php endif; ?> /></td>
        </tr>
        <tr class="odd">
          <td><i class="fa fa-check-square-o fa-2x"></i> <input type="radio" name="icon" value="fa fa-check-square-o"<?php if ($this->_vars['icon'] == "fa fa-check-square-o"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-book fa-2x"></i> <input type="radio" name="icon" value="fa fa-book"<?php if ($this->_vars['icon'] == "fa fa-book"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-calendar fa-2x"></i> <input type="radio" name="icon" value="fa fa-calendar"<?php if ($this->_vars['icon'] == "fa fa-calendar"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-coffee fa-2x"></i> <input type="radio" name="icon" value="fa fa-coffee"<?php if ($this->_vars['icon'] == "fa fa-coffee"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-filter fa-2x"></i> <input type="radio" name="icon" value="fa fa-filter"<?php if ($this->_vars['icon'] == "fa fa-filter"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-folder fa-2x"></i> <input type="radio" name="icon" value="fa fa-folder"<?php if ($this->_vars['icon'] == "fa fa-folder"): ?> checked<?php endif; ?> /></td>
        </tr>
        <tr class="odd">
          <td><i class="fa fa-globe fa-2x"></i> <input type="radio" name="icon" value="fa fa-globe"<?php if ($this->_vars['icon'] == "fa fa-globe"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-heart fa-2x"></i> <input type="radio" name="icon" value="fa fa-heart"<?php if ($this->_vars['icon'] == "fa fa-heart"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-envelope fa-2x"></i> <input type="radio" name="icon" value="fa fa-envelope"<?php if ($this->_vars['icon'] == "fa fa-envelope"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-edit fa-2x"></i> <input type="radio" name="icon" value="fa fa-edit"<?php if ($this->_vars['icon'] == "fa fa-edit"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-gift fa-2x"></i> <input type="radio" name="icon" value="fa fa-gift"<?php if ($this->_vars['icon'] == "fa fa-gift"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-flag fa-2x"></i> <input type="radio" name="icon" value="fa fa-flag"<?php if ($this->_vars['icon'] == "fa fa-flag"): ?> checked<?php endif; ?> /></td>
        </tr>
        <tr class="odd">
          <td><i class="fa fa-lock fa-2x"></i> <input type="radio" name="icon" value="fa fa-lock"<?php if ($this->_vars['icon'] == "fa fa-lock"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-rocket fa-2x"></i> <input type="radio" name="icon" value="fa fa-rocket"<?php if ($this->_vars['icon'] == "fa fa-rocket"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-smile-o fa-2x"></i> <input type="radio" name="icon" value="fa fa-smile-o"<?php if ($this->_vars['icon'] == "fa fa-smile-o"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-phone fa-2x"></i> <input type="radio" name="icon" value="fa fa-phone"<?php if ($this->_vars['icon'] == "fa fa-phone"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-map-marker fa-2x"></i> <input type="radio" name="icon" value="fa fa-map-marker"<?php if ($this->_vars['icon'] == "fa fa-map-marker"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-question fa-2x"></i> <input type="radio" name="icon" value="fa fa-question"<?php if ($this->_vars['icon'] == "fa fa-question"): ?> checked<?php endif; ?> /></td>
        </tr>
        <tr class="odd">
          <td><i class="fa fa-star fa-2x"></i> <input type="radio" name="icon" value="fa fa-star"<?php if ($this->_vars['icon'] == "fa fa-star"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-warning fa-2x"></i> <input type="radio" name="icon" value="fa fa-warning"<?php if ($this->_vars['icon'] == "fa fa-warning"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-user fa-2x"></i> <input type="radio" name="icon" value="fa fa-user"<?php if ($this->_vars['icon'] == "fa fa-user"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-trophy fa-2x"></i> <input type="radio" name="icon" value="fa fa-trophy"<?php if ($this->_vars['icon'] == "fa fa-trophy"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-support fa-2x"></i> <input type="radio" name="icon" value="fa fa-support"<?php if ($this->_vars['icon'] == "fa fa-support"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-hand-o-right fa-2x"></i> <input type="radio" name="icon" value="fa fa-hand-o-right"<?php if ($this->_vars['icon'] == "fa fa-hand-o-right"): ?> checked<?php endif; ?> /></td>
        </tr>

        <tr class="odd">
          <td><i class="fa fa-cogs fa-2x"></i> <input type="radio" name="icon" value="fa fa-cogs"<?php if ($this->_vars['icon'] == "fa fa-cogs"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-bars fa-2x"></i> <input type="radio" name="icon" value="fa fa-bars"<?php if ($this->_vars['icon'] == "fa fa-bars"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-desktop fa-2x"></i> <input type="radio" name="icon" value="fa fa-desktop"<?php if ($this->_vars['icon'] == "fa fa-desktop"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-bolt fa-2x"></i> <input type="radio" name="icon" value="fa fa-bolt"<?php if ($this->_vars['icon'] == "fa fa-bolt"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-info-circle fa-2x"></i> <input type="radio" name="icon" value="fa fa-info-circle"<?php if ($this->_vars['icon'] == "fa fa-info-circle"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-info fa-2x"></i> <input type="radio" name="icon" value="fa fa-info"<?php if ($this->_vars['icon'] == "fa fa-info"): ?> checked<?php endif; ?> /></td>
        </tr>

        <tr class="odd">
          <td><i class="fa fa-lightbulb-o fa-2x"></i> <input type="radio" name="icon" value="fa fa-lightbulb-o"<?php if ($this->_vars['icon'] == "fa fa-lightbulb-o"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-plus fa-2x"></i> <input type="radio" name="icon" value="fa fa-plus"<?php if ($this->_vars['icon'] == "fa fa-plus"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-plus-square fa-2x"></i> <input type="radio" name="icon" value="fa fa-plus-square"<?php if ($this->_vars['icon'] == "fa fa-plus-square"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-plus-circle fa-2x"></i> <input type="radio" name="icon" value="fa fa-plus-circle"<?php if ($this->_vars['icon'] == "fa fa-plus-circle"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-search fa-2x"></i> <input type="radio" name="icon" value="fa fa-search"<?php if ($this->_vars['icon'] == "fa fa-search"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-thumbs-up fa-2x"></i> <input type="radio" name="icon" value="fa fa-thumbs-up"<?php if ($this->_vars['icon'] == "fa fa-thumbs-up"): ?> checked<?php endif; ?> /></td>
        </tr>

        <tr class="odd">
          <td><i class="fa fa-file-text-o fa-2x"></i> <input type="radio" name="icon" value="fa fa-file-text-o"<?php if ($this->_vars['icon'] == "fa fa-file-text-o"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-list-ul fa-2x"></i> <input type="radio" name="icon" value="fa fa-list-ul"<?php if ($this->_vars['icon'] == "fa fa-list-ul"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-chevron-down fa-2x"></i> <input type="radio" name="icon" value="fa fa-chevron-down"<?php if ($this->_vars['icon'] == "fa fa-chevron-down"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-chevron-left fa-2x"></i> <input type="radio" name="icon" value="fa fa-chevron-left"<?php if ($this->_vars['icon'] == "fa fa-chevron-left"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-chevron-right fa-2x"></i> <input type="radio" name="icon" value="fa fa-chevron-right"<?php if ($this->_vars['icon'] == "fa fa-chevron-right"): ?> checked<?php endif; ?> /></td>
          <td><i class="fa fa-chevron-up fa-2x"></i> <input type="radio" name="icon" value="fa fa-chevron-up"<?php if ($this->_vars['icon'] == "fa fa-chevron-up"): ?> checked<?php endif; ?> /></td>
        </tr>

        </table>
        
