<?php 
session_start();
require_once('include/classes.php');
if($_SESSION['logged_in']) //make sure we're logged in
{
	$error=null;
	//make sure the project being edited is a project the user is associated with
	if(isset($_GET['id']))
	{
		$userID=$_SESSION['user_id'];
		$projID=$_GET['id'];
		
		if(checkAssoc($userID, $projID))
		{
			if(isset($_POST['submit']))
			{
				$contrib=strip_tags($_POST['contrib']);
				$start=strip_tags($_POST['start_dt']);
				$end=strip_tags($_POST['end_dt']);
				$chall=strip_tags($_POST['challs']);
				
				if(!trim($start)=="")
				{
					if(!checkaDate($start)) 
					{
						$error="Start date is not valid!";
					}
				}
				if(!trim($end)=="")
				{
					if(!checkaDate($end))
					{
						$error="End date is not valid!";
					}
				}
				if($error==null)
				{
					/*
					** contribution ID's
					** 1-Contribution
					** 2-Challenges
					** 3-Start Date
					** 4-End Date
					*/
					$xsql = new phpSQL();
					$changes=false; //to makes sure that the query is only run if there are actual changes, otherwise there will be an error
					$query="REPLACE INTO user_proj_spec (user_id, proj_id, spec_id, fdval) VALUES ";
					if(!$contrib==$_SESSION['CONTRIBUTION'])
					{
						$query.="({$userID},{$projID},1,'".addslashes($contrib)."'),";
						$changes=true;
					}
					
					if(!$chall==$_SESSION['CHALLENGES'])
					{
						$query.="({$userID},{$projID},2,'".addslashes($chall)."'),";
						$changes=true;
					}
					
					if(!$start==$_SESSION['START DATE'])
					{
						$query.="({$userID},{$projID},3,'{$start}'),";
						$changes=true;
					}
					
					if(!$end==$_SESSION['END DATE'])
					{
						$query.="({$userID},{$projID},4,'{$end}'),";
						$changes=true;
					}
					$query=substr($query,0,-1);
					$xsql->query($query);
					if($changes)
					{
						$xsql->runquery();
					}
				}
				
				//session var clean up
				unset($_SESSION['CONTRIBUTION']);
				unset($_SESSION['START DATE']);
				unset($_SESSION['END DATE']);
				unset($_SESSION['CHALLENGES']);
			}
			else
			{
				//get contribution data
				$con_arr=getContributionInfo($userID, $projID);
				
				$_SESSION['CONTRIBUTION']=$con_arr['CONTRIBUTION'];
				$_SESSION['START DATE']=$con_arr['START DATE'];
				$_SESSION['END DATE']=$con_arr['END DATE'];
				$_SESSION['CHALLENGES']=$con_arr['CHALLENGES'];
				
				$text.=getProject($projID);
				
				$text.=startForm($type="POST",$_SERVER['PHP_SELF']."?id=".$projID);
					$text.=startTable();			
						//Contribution
						$text.="<tr>";
						$text.="<td>Contribution:</td>";
						$text.="<td>".buildTextarea("contrib",$con_arr['CONTRIBUTION'])."</td>";
						$text.="</tr>";
						//Contribution startdate
						$text.="<tr>";
						$text.="<td>Start Date (yyyy-mm-dd):</td>";
						$text.="<td>".buildTextbox("start_dt",$con_arr['START DATE'])."</td>";
						$text.="</tr>";
						//Contribution enddate
						$text.="<tr>";
						$text.="<td>End Date (yyyy-mm-dd):</td>";
						$text.="<td>".buildTextbox("end_dt",$con_arr['END DATE'])."</td>";
						$text.="</tr>";
						//Challenges
						$text.="<tr>";
						$text.="<td>Challenges:</td>";
						$text.="<td>".buildTextarea("challs",$con_arr['CHALLENGES'],"50","3")."</td>";
						$text.="</tr>";								
					$text.=endTable();
				$text.=endForm("Submit");
			}
		}
	}
}
//put the page together
	$html.=startPage("style", "SNS Portfolios", "include/css/");
		//start wrapper
		$html.=startDiv("content_wrapper","wrapper");
			//build header
			$html.=startDiv("header","header");				
			$html.=endDiv();
			//build content div
			$html.=startDiv("main_content", "content");
				$html.=buildMainMenu($_SERVER['PHP_SELF']);
				//add content
				if(!$error==null)
				{
					$html.=$error;
				}
				else
				{
					$html.=$text;
				}
			$html.=endDiv();	
			//build footer
			$html.=startDiv("footer","footer");				
			$html.=endDiv();
		//end wrapper
		$html.=endDiv();
	//end page
	$html.=endPage();
	//print the page to the screen
	print($html);
?>