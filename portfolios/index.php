<?php
session_start();
require_once('include/classes.php');
//page to display most popular tags, categories, and most viewed portfolios
$html=startPage("style", "SNS Portfolios", "include/css/");
	$html.=startDiv("content_wrapper","wrapper");
		//build header
		$html.=startDiv("header","header");
		$html.=endDiv();
		$html.=startDiv("main_content", "content");
			//main menu (register, login, logout (if logged in), portfolio index)
			$html.=buildMainMenu($_SERVER['PHP_SELF']);
			$html.=startDiv("index_news","news");
				$html.=getLatestNews();
			$html.=endDiv();
		$html.=endDiv();
		//build footer
		$html.=startDiv("footer","footer");
		$html.=endDiv();
	//end wrapper
	$html.=endDiv();
$html.=endPage();
//show the page
print($html);
?>