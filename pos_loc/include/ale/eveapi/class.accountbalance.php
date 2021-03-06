<?php
/**************************************************************************
	Ale API Library for EvE AccountBalance Class
	Portions Copyright (C) 2007 Kw4h
	Portions Copyright (c) 2008 Thorsten Behrens

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

class AccountBalance
{
	function getAccountBalance($contents)
	{
		if (!empty($contents) && is_string($contents))
		{
			$output = array();
			$xml = new SimpleXMLElement($contents);
			
			// add all accounts in an array
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

// The below is legacy code and left in so as to not break code that expects 0.20 behavior
class Balance extends Api
{
	function __construct($userid, $apikey, $charid)
	{
		parent::setCredentials($userid, $apikey, $charid);
	}
	
	function getBalance($corp = false)
	{
			$xmldata = $this->getAccountBalance($corp);
			$data = AccountBalance::getAccountBalance($xmldata);
			unset ($xmldata); // manual garbage collection
			return $data;
	}
}
?>