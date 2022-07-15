<?php  if (! empty ( $this->_vars['admin_vars'] )): ?>


                </td>
  </tr>
</table>

                </td>
	</tr>
	<tr>
		<td background="<?php echo $this->_vars['tpl']; ?>
images/header-bg-top.gif" height="5">&nbsp;</td>
	</tr>
	<tr>
		<td align=center valign=middle>

        <table border=0 align=center>
          <tr>
        		<td align=center valign=middle >  
        	<a href="../" target="_blank"><i class="fa fa-external-link"></i></a> <a href="../" target="_blank"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "to_website"), $this);?></a> 
            <a href="./docs/index.html" target="_blank"><i class="fa fa-book"></i></a> <a href="./docs/<?php echo $this->_vars['currentlang']; ?>
/index.html" target="_blank"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "manual"), $this);?></a></td>
            <?php if ($this->_vars['admin_vars']['bo_user']['id'] == 0): ?><td>
            <img src="<?php echo $this->_vars['tpl']; ?>
images/icon/group.png" />
            <a href="?action=remind"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "user",'key3' => "reset_password"), $this);?></a>
            </td><?php endif; ?>
        	</tr>
        </table>
        <small><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "index",'key3' => "page_generated_in"), $this);?>: <?php echo page_loaded_time(array(), $this);?></small>
    </td>
	</tr>
</table>

<?php endif; ?>

</body>
</html>