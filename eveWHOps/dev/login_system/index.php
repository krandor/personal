<?php
//main page that binds everything together
require_once('include/classes.php');

$whOp =& new wormholeOpTracker;

// set the current action
$_action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'login'; //if an action is set, do that, otherwise go to login/register
switch($_action) 
{
    case 'addAttendees':
        // adding a whOp entry
        $whOp->displayOpForm();
        break;
    case 'submitSetup':
        // submitting a whOp entry
        $whOp->validateSetupFormData($_POST);
        if($whOp->isValidSetupForm($_POST)) {
            $whID = $whOp->addOp($_POST);
            $whOp->displayOpSummary($whOp->getOp($whID));
        } else {
            $whOp->displaySetupForm($whOp->attendees,$_POST);
        }
        break;
	case 'submitAttendees':
		$whOp->validateOpFormData($_POST);
		if($whOp->isValidOpForm($_POST)) 
		{
			//print($whOp->attendees);
			$whOp->displaySetupForm($whOp->attendees);
		}
		else
		{
			$whOp->displayOpForm($_POST);
		}
		break;
    case 'whSetup':    
        // setting up the whOp
        $whOp->displaySetupForm($whOp->attendees);        
        break; 
	case 'login':
		//log into the system
		$whOp->displayLogin();
		break;
}

?>