{* Smarty *}
<table border="1">
	{if $error ne ""}
		<tr>
			<td bgcolor="yellow" colspan="2">
				{if $error eq "no_attendees"} You must add attendees to this op in order to save it.
				{/if}
			</td>
		</tr>
	{else}
		<thead>
		<tr><th colspan=2>Wormhole Op Summary</th></tr>
		</thead>
		<tbody>
		<tr>
			<td>
				Wormhole Site:
			</td>
			<td>
				{$data[0][0]}
			</td>
		</tr>
		<tr>
			<td>
				Wormhole Date:
			</td>
			<td>
				{$data[0][1]}
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
			{foreach from=$data item=player}
			<tr>
				<td>
					{$player[2]}
				</td>
				<td>
					{$player[3]}
				</td>
			</tr>				
			{/foreach}
		</tbody>
	{/if}	
</table>
