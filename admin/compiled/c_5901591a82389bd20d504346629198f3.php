<?php ?>      <table width="100%" cellpadding="3" cellspacing="1">

<?php if (! empty ( $this->_vars['categ']['option_groups_filter'] )): ?>
		<tr class="odd">
		  <td width="200" valign="top"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "in_filter"), $this);?>:</td>
		  <td>
			<table style="margin:25px; width: 600px;">
				<tr>
						<th style="width:200px;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "option"), $this);?></th>
						<th style="width:120px;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "synonim"), $this);?></th>
						<th><i class="fa fa-sort"></i></th>
						<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "type"), $this);?></th>
						<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "type_in_filter"), $this);?></th>
				</tr>
					
			<?php if (count((array)$this->_vars['categ']['option_groups_filter'])): foreach ((array)$this->_vars['categ']['option_groups_filter'] as $this->_vars['o']): ?>

					<tr>
						<td><a href="?action=products&do=options&id=<?php echo $this->_vars['o']['id']; ?>
"><?php echo $this->_vars['o']['title']; ?>
</a></td>
						<td><a href="?action=products&do=options&id=<?php echo $this->_vars['o']['id']; ?>
"><?php echo $this->_vars['o']['alias']; ?>
</a></td>
						<td align="center"><?php echo $this->_vars['o']['sort']; ?>
</td>
						<td><?php echo $this->_vars['o']['type']; ?>
</td>
						<td><?php echo $this->_vars['o']['filter_type']; ?>
</td>
					</tr>
			<?php endforeach; endif; ?>
			</table>
		  </td>
		</tr>
<?php endif; ?>

<?php if (! empty ( $this->_vars['categ']['option_groups_list'] )): ?>
		<tr class="odd">
		  <td width="200" valign="top"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "in_list"), $this);?>:</td>
		  <td>
			<table style="margin:25px; width: 600px;">
				<tr>
						<th style="width:200px;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "option"), $this);?></th>
						<th style="width:120px;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "synonim"), $this);?></th>
						<th><i class="fa fa-sort"></i></th>
						<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "type"), $this);?></th>
				</tr>
            <?php if (count((array)$this->_vars['categ']['option_groups_list'])): foreach ((array)$this->_vars['categ']['option_groups_list'] as $this->_vars['o']): ?>
					<tr>
						<td><a href="?action=products&do=options&id=<?php echo $this->_vars['o']['id']; ?>
"><?php echo $this->_vars['o']['title']; ?>
</a></td>
						<td><a href="?action=products&do=options&id=<?php echo $this->_vars['o']['id']; ?>
"><?php echo $this->_vars['o']['alias']; ?>
</a></td>
						<td align="center"><?php echo $this->_vars['o']['sort']; ?>
</td>
						<td><?php echo $this->_vars['o']['type']; ?>
</td>
					</tr>
			<?php endforeach; endif; ?>
		  </table>
		  </td>
		</tr>
<?php endif; ?>

		<tr <?php echo tpl_function_cycle(array('values' => "class=odd, "), $this);?>>
		  <td width="200" valign="top"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "opt_groups"), $this);?>:</td>
          <td><?php if ($this->_run_modifier($this->_vars['categ']['option_groups'], 'count', 'PHP', 0) > 0): ?>
            <?php if (count((array)$this->_vars['categ']['option_groups'])): foreach ((array)$this->_vars['categ']['option_groups'] as $this->_vars['opt']): ?>
              <input type="checkbox" name="options[]" value="<?php echo $this->_vars['opt']['id']; ?>
"<?php if ($this->_vars['opt']['selected'] == $this->_vars['opt']['id']): ?> checked="checked"<?php endif; ?>> <a href="?action=products&do=option_group&id=<?php echo $this->_vars['opt']['id']; ?>
"><?php echo $this->_vars['opt']['title']; ?>
</a><br><?php if (! empty ( $this->_vars['opt']['description'] )): ?><i><?php echo $this->_run_modifier($this->_vars['opt']['description'], 'nl2br', 'PHP', 1); ?>
</i><br><?php endif; ?>
			  <?php if (! empty ( $this->_vars['opt']['options'] ) && $this->_vars['opt']['selected'] == $this->_vars['opt']['id']): ?>
				<table style="margin:25px; width: 600px;">
					<tr>
						<th style="width:200px;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "sidebar",'key3' => "option"), $this);?></th>
						<th style="width:120px;"><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "synonim"), $this);?></th>
						<th><i class="fa fa-sort"></i></th>
						<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "type"), $this);?></th>
						<th><i class="fa fa-bars" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "in_list"), $this);?>"></i></th>
						<th><i class="fa fa-filter" title="<?php echo GetMessageTpl(array('key1' => "admin",'key2' => "in_filter"), $this);?>"></i></th>
						<th><?php echo GetMessageTpl(array('key1' => "admin",'key2' => "products",'key3' => "type_in_filter"), $this);?></th>
					</tr>
				<?php if (count((array)$this->_vars['opt']['options'])): foreach ((array)$this->_vars['opt']['options'] as $this->_vars['k'] => $this->_vars['o']): ?>
					<tr>
						<td><a href="?action=products&do=options&id=<?php echo $this->_vars['o']['id']; ?>
"><?php echo $this->_vars['o']['title']; ?>
</a></td>
						<td><a href="?action=products&do=options&id=<?php echo $this->_vars['o']['id']; ?>
"><?php echo $this->_vars['o']['alias']; ?>
</a></td>
						<td align="center"><?php echo $this->_vars['o']['sort']; ?>
</td>
						<td><?php echo $this->_vars['o']['type']; ?>
</td>
						<td align="center"><?php if (! empty ( $this->_vars['o']['show_in_list'] )): ?><i class="fa fa-check"></i><?php else: ?>-<?php endif; ?></td>
						<td align="center"><?php if (! empty ( $this->_vars['o']['show_in_filter'] )): ?><i class="fa fa-check"></i><?php else: ?>-<?php endif; ?></td>
						<td><?php echo $this->_vars['o']['filter_type']; ?>
</td>
					</tr>
				<?php endforeach; endif; ?>
				</table>
			  <?php endif; ?>
            <?php endforeach; endif; ?>
          <?php else: ?>
            <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "no_yet_optgroups"), $this);?> <a href="?action=products&do=add_option_group"><i class="fa fa-plus-circle"></i> <?php echo GetMessageTpl(array('key1' => "admin",'key2' => "create_now"), $this);?></a>
          <?php endif; ?>
          </td>
        </tr>
      </table>