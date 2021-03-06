<?php
/**************************************************************************
	Ale API Library for EvE Eve Central MarketStat Class
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

class MarketStat
{
	function getMarketStat($contents)
	{
		if (!empty($contents) && is_string($contents))
		{
	       	$output = array();
	 		$xml = new SimpleXMLElement($contents);
			$children = $xml->marketstat->children();
			foreach ($children as $child)
			{
				$index = count($output);
				$catt = $child->attributes();
				$output[$index][(string) 'typeID'] = (string) $catt['id'];
				foreach($child->children() as $sells)
				{
					$sname = $sells->getName();
					foreach($sells->children() as $key=>$value)
					{
						$output[$index][(string) $sname][(string) $key] = (string) $value;
					}
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
