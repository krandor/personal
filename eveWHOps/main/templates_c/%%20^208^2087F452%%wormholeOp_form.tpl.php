<?php /* Smarty version 2.6.26, created on 2010-04-03 18:39:56
         compiled from wormholeOp_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'wormholeOp_form.tpl', 14, false),)), $this); ?>
<form action="<?php echo $this->_tpl_vars['SCRIPT_NAME']; ?>
?action=submitAttendees" method="post">
	<table border="1">
		<?php if ($this->_tpl_vars['error'] != ""): ?>
			<tr>
				<td bgcolor="yellow" colspan="2">
					<?php if ($this->_tpl_vars['error'] == 'no_attendees'): ?> You must add attendees to this op in order to save it.
					<?php endif; ?>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<td>Number of People that attended:</td>
			<td><input type="text" name="whAttendees" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['post']['whAttendees'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="40" /></td>
		</tr>		
		<tr>
			<td colspan="2" align="center"><input type="submit" value="Submit"></td>
		</tr>
	</table>
</form>