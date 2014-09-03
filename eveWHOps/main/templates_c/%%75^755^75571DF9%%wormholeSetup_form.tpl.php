<?php /* Smarty version 2.6.26, created on 2010-04-02 19:59:47
         compiled from wormholeSetup_form.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'wormholeSetup_form.tpl', 17, false),)), $this); ?>
<form action="<?php echo $this->_tpl_vars['SCRIPT_NAME']; ?>
?action=submitSetup" method="post">
	<input type="hidden" name="whAttendees" value="<?php echo $this->_tpl_vars['attendees']; ?>
" />
	<table border="1">
		<?php if ($this->_tpl_vars['error'] != ""): ?>
			<tr>
				<td bgcolor="yellow" colspan="2">
					<?php if ($this->_tpl_vars['error'] == 'no_site'): ?>You must supply a site that was run.
					<?php elseif ($this->_tpl_vars['error'] == 'invalid_time'): ?> The date you entered was invalid or an unrecognized format, try something like '2010-01-20'.
					<?php elseif ($this->_tpl_vars['error'] == 'no_attendees'): ?> You must add attendees to this op in order to save it.
					<?php endif; ?>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<td>Site:</td>
			<td><input type="text" name="whSite" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['post']['whSite'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" size="40" /></td>
		</tr>
		<tr>
			<td valign="top">Date site was run:</td>
			<td><input type="text" name="whDate" value="<?php echo $this->_tpl_vars['post']['whDate']; ?>
"></td>
		</tr>
		<?php unset($this->_sections['whAttendees']);
$this->_sections['whAttendees']['name'] = 'whAttendees';
$this->_sections['whAttendees']['start'] = (int)0;
$this->_sections['whAttendees']['loop'] = is_array($_loop=$this->_tpl_vars['attendees']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['whAttendees']['step'] = ((int)1) == 0 ? 1 : (int)1;
$this->_sections['whAttendees']['show'] = true;
$this->_sections['whAttendees']['max'] = $this->_sections['whAttendees']['loop'];
if ($this->_sections['whAttendees']['start'] < 0)
    $this->_sections['whAttendees']['start'] = max($this->_sections['whAttendees']['step'] > 0 ? 0 : -1, $this->_sections['whAttendees']['loop'] + $this->_sections['whAttendees']['start']);
else
    $this->_sections['whAttendees']['start'] = min($this->_sections['whAttendees']['start'], $this->_sections['whAttendees']['step'] > 0 ? $this->_sections['whAttendees']['loop'] : $this->_sections['whAttendees']['loop']-1);
if ($this->_sections['whAttendees']['show']) {
    $this->_sections['whAttendees']['total'] = min(ceil(($this->_sections['whAttendees']['step'] > 0 ? $this->_sections['whAttendees']['loop'] - $this->_sections['whAttendees']['start'] : $this->_sections['whAttendees']['start']+1)/abs($this->_sections['whAttendees']['step'])), $this->_sections['whAttendees']['max']);
    if ($this->_sections['whAttendees']['total'] == 0)
        $this->_sections['whAttendees']['show'] = false;
} else
    $this->_sections['whAttendees']['total'] = 0;
if ($this->_sections['whAttendees']['show']):

            for ($this->_sections['whAttendees']['index'] = $this->_sections['whAttendees']['start'], $this->_sections['whAttendees']['iteration'] = 1;
                 $this->_sections['whAttendees']['iteration'] <= $this->_sections['whAttendees']['total'];
                 $this->_sections['whAttendees']['index'] += $this->_sections['whAttendees']['step'], $this->_sections['whAttendees']['iteration']++):
$this->_sections['whAttendees']['rownum'] = $this->_sections['whAttendees']['iteration'];
$this->_sections['whAttendees']['index_prev'] = $this->_sections['whAttendees']['index'] - $this->_sections['whAttendees']['step'];
$this->_sections['whAttendees']['index_next'] = $this->_sections['whAttendees']['index'] + $this->_sections['whAttendees']['step'];
$this->_sections['whAttendees']['first']      = ($this->_sections['whAttendees']['iteration'] == 1);
$this->_sections['whAttendees']['last']       = ($this->_sections['whAttendees']['iteration'] == $this->_sections['whAttendees']['total']);
?>
			<tr>
				<td>Player Name: <input type="text" name="whAttendees[<?php echo $this->_sections['whAttendees']['index']; ?>
][0]" value="<?php echo $this->_tpl_vars['post']['whAttendees'][$this->_sections['whAttendees']['index']][0]; ?>
"</td>
				<td>How long did the player participate? 
					<select name="whAttendees[<?php echo $this->_sections['whAttendees']['index']; ?>
][1]">
						<?php unset($this->_sections['pcent']);
$this->_sections['pcent']['name'] = 'pcent';
$this->_sections['pcent']['start'] = (int)10;
$this->_sections['pcent']['loop'] = is_array($_loop=110) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['pcent']['step'] = ((int)10) == 0 ? 1 : (int)10;
$this->_sections['pcent']['show'] = true;
$this->_sections['pcent']['max'] = $this->_sections['pcent']['loop'];
if ($this->_sections['pcent']['start'] < 0)
    $this->_sections['pcent']['start'] = max($this->_sections['pcent']['step'] > 0 ? 0 : -1, $this->_sections['pcent']['loop'] + $this->_sections['pcent']['start']);
else
    $this->_sections['pcent']['start'] = min($this->_sections['pcent']['start'], $this->_sections['pcent']['step'] > 0 ? $this->_sections['pcent']['loop'] : $this->_sections['pcent']['loop']-1);
if ($this->_sections['pcent']['show']) {
    $this->_sections['pcent']['total'] = min(ceil(($this->_sections['pcent']['step'] > 0 ? $this->_sections['pcent']['loop'] - $this->_sections['pcent']['start'] : $this->_sections['pcent']['start']+1)/abs($this->_sections['pcent']['step'])), $this->_sections['pcent']['max']);
    if ($this->_sections['pcent']['total'] == 0)
        $this->_sections['pcent']['show'] = false;
} else
    $this->_sections['pcent']['total'] = 0;
if ($this->_sections['pcent']['show']):

            for ($this->_sections['pcent']['index'] = $this->_sections['pcent']['start'], $this->_sections['pcent']['iteration'] = 1;
                 $this->_sections['pcent']['iteration'] <= $this->_sections['pcent']['total'];
                 $this->_sections['pcent']['index'] += $this->_sections['pcent']['step'], $this->_sections['pcent']['iteration']++):
$this->_sections['pcent']['rownum'] = $this->_sections['pcent']['iteration'];
$this->_sections['pcent']['index_prev'] = $this->_sections['pcent']['index'] - $this->_sections['pcent']['step'];
$this->_sections['pcent']['index_next'] = $this->_sections['pcent']['index'] + $this->_sections['pcent']['step'];
$this->_sections['pcent']['first']      = ($this->_sections['pcent']['iteration'] == 1);
$this->_sections['pcent']['last']       = ($this->_sections['pcent']['iteration'] == $this->_sections['pcent']['total']);
?>
							<option value="<?php echo $this->_sections['pcent']['index']; ?>
" selected=<?php if ($this->_sections['pcent']['index'] == 100): ?>1<?php else: ?>0<?php endif; ?>><?php echo $this->_sections['pcent']['index']; ?>
% of the time</option>
						<?php endfor; endif; ?>
					</select>
				</td>
			</tr>
		<?php endfor; endif; ?>
		<tr>
			<td colspan="2" align="center"><input type="submit" value="Submit"></td>
		</tr>
	</table>
</form>