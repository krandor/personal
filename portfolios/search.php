<?php
/*
** - SEARCH PAGE
** - Search for projects and portfolios using project tags, titles, and categories
*/
session_start();
require_once('include/classes.php');
if(isset($_POST['submit']))
{
	$cat=strip_tags($_POST['categories']);
	$terms=preg_split('/[\s,|;]+/',strip_tags($_POST['search_Terms']));
	//print_r($terms);
	$searchcrit = "('".implode("','",$terms)."')";
	//print($searchcrit);
	$text.=getProjects($searchcrit,$cat);	
}
else
{
	$text.="<h4>Searching for Projects</h4>";
	$text.=startForm($type="POST",$_SERVER['PHP_SELF']);
		$text.=startTable();			
			//Category
			$text.="<tr>";
			$text.="<td>Categories:</td>";
			$text.="<td>".buildSelect(getCategories("search"),"categories",0)."</td>";
			$text.="</tr>";
			//Search Terms
			$text.="<tr>";
			$text.="<td>Search Terms:</td>";
			$text.="<td>".buildTextbox("search_Terms","","tboxwide")."</td>";
			$text.="</tr>";	
			//Search Explanation
			$text.="<tr>";
			$text.="<td colspan=2>Search works by searching through tags,<br />If a project doesn't have any tags attached to it, it will not be found.</td>";			
			$text.="</tr>";			
		$text.=endTable();
	$text.=endForm("Search");
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