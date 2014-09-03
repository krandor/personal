{* Smarty *}
<form action="{$SCRIPT_NAME}?action=submitSetup" method="post">
	<input type="hidden" name="whAttendees" value="{$attendees}" />
	<table border="1">
		{if $error ne ""}
			<tr>
				<td bgcolor="yellow" colspan="2">
					{if $error eq "no_site"}You must supply a site that was run.
					{elseif $error eq "invalid_time"} The date you entered was invalid or an unrecognized format, try something like '2010-01-20'.
					{elseif $error eq "no_attendees"} You must add attendees to this op in order to save it.
					{/if}
				</td>
			</tr>
		{/if}
		<tr>
			<td>Site:</td>
			<td><input type="text" name="whSite" value="{$post.whSite|escape}" size="40" /></td>
		</tr>
		<tr>
			<td valign="top">Date site was run:</td>
			<td><input type="text" name="whDate" value="{$post.whDate}"></td>
		</tr>
		{section name=whAttendees start=0 loop=$attendees step=1}
			<tr>
				<td>Player Name: <input type="text" name="whAttendees[{$smarty.section.whAttendees.index}][0]" value="{$post.whAttendees[$smarty.section.whAttendees.index][0]}"</td>
				<td>How long did the player participate? 
					<select name="whAttendees[{$smarty.section.whAttendees.index}][1]">
						{section name=pcent start=10 loop=110 step=10}
							<option value="{$smarty.section.pcent.index}" selected={if $smarty.section.pcent.index eq 100}1{else}0{/if}>{$smarty.section.pcent.index}% of the time</option>
						{/section}
					</select>
				</td>
			</tr>
		{/section}
		<tr>
			<td colspan="2" align="center"><input type="submit" value="Submit"></td>
		</tr>
	</table>
</form>