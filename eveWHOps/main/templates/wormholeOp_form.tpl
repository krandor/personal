{* Smarty *}
<form action="{$SCRIPT_NAME}?action=submitAttendees" method="post">
	<table border="1">
		{if $error ne ""}
			<tr>
				<td bgcolor="yellow" colspan="2">
					{if $error eq "no_attendees"} You must add attendees to this op in order to save it.
					{/if}
				</td>
			</tr>
		{/if}
		<tr>
			<td>Number of People that attended:</td>
			<td><input type="text" name="whAttendees" value="{$post.whAttendees|escape}" size="40" /></td>
		</tr>		
		<tr>
			<td colspan="2" align="center"><input type="submit" value="Submit"></td>
		</tr>
	</table>
</form>