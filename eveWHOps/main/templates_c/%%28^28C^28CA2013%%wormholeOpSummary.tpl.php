<?php /* Smarty version 2.6.26, created on 2010-04-02 21:19:42
         compiled from wormholeOpSummary.tpl */ ?>
<table border="1">
	<?php if ($this->_tpl_vars['error'] != ""): ?>
		<tr>
			<td bgcolor="yellow" colspan="2">
				<?php if ($this->_tpl_vars['error'] == 'no_attendees'): ?> You must add attendees to this op in order to save it.
				<?php endif; ?>
			</td>
		</tr>
	<?php else: ?>
		<thead>
		<tr><th colspan=2>Wormhole Op Summary</th></tr>
		</thead>
		<tbody>
		<tr>
			<td>
				Wormhole Site:
			</td>
			<td>
				<?php echo $this->_tpl_vars['data'][0][0]; ?>

			</td>
		</tr>
		<tr>
			<td>
				Wormhole Date:
			</td>
			<td>
				<?php echo $this->_tpl_vars['data'][0][1]; ?>

			</td>
		</tr>
		</tbody>
	</table>
	<br />
	<table border="1">
		<thead>
			<tr><th>Character</th><th>Attendance %</th></tr>
		</thead>
		<tbody>
			<?php $_from = $this->_tpl_vars['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['player']):
?>
			<tr>
				<td>
					<?php echo $this->_tpl_vars['player'][2]; ?>

				</td>
				<td>
					<?php echo $this->_tpl_vars['player'][3]; ?>

				</td>
			</tr>				
			<?php endforeach; endif; unset($_from); ?>
		</tbody>
	<?php endif; ?>	
</table>