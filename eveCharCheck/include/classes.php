<?php
//require_once 'config.php';
require_once 'htmlGen.php';
require_once 'phpSQL.php';
require_once 'ale/factory.php';

/*
project specific classes below
*/
function getSkill($ID)
{
	$xsql = new phpSQL();	
}

function getCert($ID)
{
	$xsql = new phpSQL();
	$xsql->query("SELECT cat.categoryName, cls.className, crt.`description` ".
	"FROM  `crtCertificates` crt ".
	"INNER JOIN crtCategories cat ON crt.categoryID = cat.categoryID ".
	"INNER JOIN crtClasses cls ON crt.classID = cls.classID ".
	"WHERE crt.certificateID =".$ID);
	$res = $xsql->runquery();
	if(mysql_num_rows($res)>0)
	{
		$row = mysql_fetch_row($res);
		return array($row[0],$row[1],$row[2]);
	}
	return null;
}

function getRole($ID)
{
	$xsql = new phpSQL();
}
?>