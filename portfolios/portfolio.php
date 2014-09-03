<?php
session_start();
require_once('include/classes.php');
$user = null;
$userRname = null;
//check to make sure there's a user being passed in
if(isset($_GET['u'])) {
	$user = getUserInfo($_GET['u']); //get userID and real name based on username (userID will be used for all other queries)	
	//make sure a user was returned
	if($user!=null)
	{
		$userID = $user[0];
		//update portfolio views for that user
		updateViews($userID);
		//echo $userID;
		$userRname = $user[1]." ".$user[2];		
		
		//build user bio			
		$text.=startDiv("userbioheader","bioheader");
			$text.="Bio:\n";
		$text.=endDiv();
		$text.=startDiv("userbio","userinfo");	
			$text.=getUserBio($userID);
		$text.=endDiv();
		//build user project info	
		$text.=startDiv("projinfo","projinfo");
			$text.=startDiv("projinfoheader","projheader");
				$text.="Projects:\n";
			$text.=endDiv();
			$text.=getUserProjects($userID);
		$text.=endDiv();				
	}
	else
	{
		//build html for error
		$text.="error: no user found";
	
	}	
}
else
{
	//show the top 10 users with the most views on their portfolio
	$text="<p>View the top 10 most viewed portfolios</p>";
	$text.=buildTop10Viewed();
}

//start page
if($userRname==null)
{
	$html=startPage("style", "SNS Portfolios", "include/css/");
}
else
{
	$html.=startPage("style", $userRname."'s portfolio", "include/css/");
}
	//start wrapper
	$html.=startDiv("content_wrapper","wrapper");
		//build header
		$html.=startDiv("header","header");
			//$html.="*header* (placeholder)";
		$html.=endDiv();
		//build content div
		$html.=startDiv("main_content", "content");
			//add main menu
			$html.=buildMainMenu($_SERVER['PHP_SELF']);
			//add content
			$html.=$text;
		$html.=endDiv();	
		//build footer
		$html.=startDiv("footer","footer");
			//$html.="*footer* (placeholder)";
		$html.=endDiv();
	//end wrapper
	$html.=endDiv();
//end page
$html.=endPage();

//show the page
print($html);
?>