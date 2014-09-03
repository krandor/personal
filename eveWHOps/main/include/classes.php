<?php
require_once 'config.php';
require_once 'phpSQL.php';
require_once SMARTY_DIR.'Smarty.class.php';
/*
project specific classes below
*/
class wormholeOpTracker
{	
    // smarty template object
    var $tpl = null;
    // error messages
    var $error = null;
	//number of attendees
	var $attendees = 0;
	
	function __construct()
	{
		$this->sql =& new phpSQL;
		$this->tpl =& new wormholeOpTracker_Smarty;
	}
	
	//form to enter in the op info
	function displayOpForm($formvars = array()) 
	{
		// assign the form vars
        $this->tpl->assign('post',$formvars);
        // assign error message
        $this->tpl->assign('error', $this->error);
        $this->tpl->display('wormholeOp_form.tpl');
	}
	
	//form to setup the wormhole op
	function displaySetupForm($attendees,$formvars = array())
	{		
		$this->tpl->assign('post',$formvars);
		$this->tpl->assign('error',$this->error);
		$this->tpl->assign('attendees', $attendees);
		$this->tpl->display('wormholeSetup_form.tpl');
	}
	
	//display op summary report
	function displayOpSummary($data = array())
	{
		$this->tpl->assign('data',$data);
		$this->tpl->assign('error',$this->error);
		$this->tpl->display('wormholeOpSummary.tpl');
	}
	
	function validateSetupFormData(&$formvars)
	{
		$formvars['whSite'] = trim($formvars['whSite']);
		$formvars['whDate'] = trim($formvars['whDate']);
		//$formvars['whDate'] = strtotime($formvars['whDate']);		
	}
	
	function validateOpFormData(&$formvars)
	{
		$formvars['whAttendees'] = trim($formvars['whAttendees']);				
	}
	
	function isValidSetupForm(&$formvars)
	{
		$this->error = null;
		//check site name
		if(strlen($formvars['whSite'])==0)
		{
			$this->error = 'no_site';
			return false;
		}
		//check date
		if(strtotime($formvars['whDate'])===false)
		{			
			$this->error = 'invalid_time';
			return false;
		}
		$formvars['whDate']=date('Y-m-d',strtotime($formvars['whDate']));
		
		//check if there is at least one player
		if(count($formvars['whAttendees'])<1)
		{
			$this->error = 'no_attendees';
			return false;
		}
		return true;
	}
	
	function isValidOpForm($formvars)
	{
		$this->error = null;
		//check for attendees
		if(strlen($formvars['whAttendees'])==0)
		{
			$this->error = 'no_attendees';
			return false;
		}
		
		$pattern = '/^\d+/';
		
		if(!preg_match($pattern,$formvars['whAttendees']))
		{
			$this->error = 'no_attendees';
			return false;
		}
		$this->attendees = $formvars['whAttendees'];
		return true;
	}
	
	function addOp($formvars)
	{
		$whOp = new whOperation($formvars['whSite'],$formvars['whDate'],$formvars['whAttendees']);
		$whID = $whOp->save();
		
		return $whID;
	}
	
	function getOp($whID="")
	{
		if($whID!="")
		{
			$xsql = new phpSQL();
			$xsql->query("SELECT wo.siteName, wo.opDate, woa.playerName, woa.attendancePercentage FROM wormholeOp wo INNER JOIN wormholeOpAttendees woa on wo.opID=woa.opID WHERE wo.opID=".$whID);
			$res = $xsql->runquery();
			
			if(mysql_num_rows($res)>0)
			{
				while($row=mysql_fetch_row($res))
				{
					$data[] = array($row[0],$row[1],$row[2],$row[3]); 
				}
				return $data;
			}
		}
		return array();
		
	}
	
	function displayOpReport()
	{
	/*SELECT woa.playerName as `Player`, count(wo.opID) as `Site Player Ran`, AVG(woa.attendancePercentage) as `Average Site Attendance`,
	(SELECT count(*) FROM wormholeOp) as `Total Sites Run`,
	(SUM(woa.attendancePercentage)/(SELECT count(*) FROM wormholeOp)) as `Total Attendance Percentage`
	FROM wormholeOp wo
	INNER JOIN `wormholeOpAttendees` woa ON wo.opID=woa.opID
	WHERE wo.opDate between '2010-02-01' and '2010-04-03'
	GROUP BY woa.playerName*/
	}
}


class wormholeOpTracker_Smarty extends Smarty
{
	function __construct()
	{
		$this->template_dir = WHOPTRACKER_DIR . 'templates';
        $this->compile_dir = WHOPTRACKER_DIR . 'templates_c';
        $this->config_dir = WHOPTRACKER_DIR . 'configs';
        $this->cache_dir = WHOPTRACKER_DIR . 'cache';
	}
}

class whOperation
{
	private $whDate = null; //time stamp of when the op took place
	private $whSite = null; //what site was run
	private $whAttendees = null; //array of players (whAttendee) that atteneded the event
	
	function __construct($site,$date,$attendees)
	{
		$this->whDate = $date;
		$this->whSite = $site;
		$this->whAttendees = $attendees;
	}
	
	function getWhOpID($site,$date,$insrtDateTime)
	{
		$opIDGetStr = "SELECT opID FROM wormholeOp WHERE siteName = '".$site."' and opDate = '".$date."' and insrtDateTime = '".$insrtDateTime."'";
		$xsql = new phpSQL();
		$xsql->query($opIDGetStr);
		$res = $xsql->runquery();
		if(mysql_num_rows($res)>0)
		{
			$row = mysql_fetch_row($res);
			return $row[0];
		}
		return null;		
	}
	
	function save()
	{	
		if($this->whDate==null || $this->whSite==null || $this->whAttendees==null)
		{
			return null;
		}
		$insrtDateTime = date('Y-m-d H:i:s');
		//validate data (check date is in correct format)
		//TODO: validation
		
		//make database calls
		$xsql = new phpSQL();
		//set up queries
		$opInsertStr = "INSERT INTO wormholeOp (siteName, opDate, insrtDateTime) VALUES ".
					   "('".addslashes($this->whSite)."','".addslashes($this->whDate)."','".addslashes($insrtDateTime)."')";
		
		$attendeeInsertStr = "INSERT INTO wormholeOpAttendees (opID, playerName, attendancePercentage, insrtDateTime) VALUES ";
		
		$xsql->query($opInsertStr);
		$xsql->runquery(); //save the op to the db
		
		$opID = $this->getWhOpID(addslashes($this->whSite),addslashes($this->whDate),addslashes($insrtDateTime));
		
		foreach($this->whAttendees as $attendee)
		{
			$xsql->query($attendeeInsertStr."(".$opID.", '".addslashes($attendee[0])."','".addslashes($attendee[1])."','".addslashes($insrtDateTime)."')");
			$xsql->runquery();
		}
		return $opID;
	}
	
}

class whAttendee
{
	private $attendeeName = null;
	private $attendancePercentage = null;
	
	function __construct($name, $percentage)
	{
		$this->attendeeName =  $name;
		$this->attendancePercentage = $percentage;
	}
	
	function getName()
	{
		return $this->attendeeName;
	}
	
	function getAttendance()
	{
		return $this->attendancePercentage;
	}
}
?>