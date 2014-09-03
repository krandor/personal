<?php
session_start();
require_once('include/classes.php');
//print_r($_SESSION);
if($_SESSION['logged_in'])
{
	$text.="You are already logged in. If you want to log in as another user, please ".buildLink("logout.php", "logout")." first.";
}
elseif(isset($_POST['submit'])) 
{
	$user=strip_tags($_POST['username']);
	$passwd=trim(strip_tags($_POST['passwd']));
	$error=null;
	//make sure both a username and a password were provided
	if((trim($_POST['username'])=="" || trim($_POST['passwd'])==""))
	{
		$error.="\nRequired Information Missing!<br />Please go back to make sure you entered both your username and your password.\n<br/>";
	}
	
	if($error==null)
	{
		//encrypt the password
		$encpw = crypt(md5($passwd),md5($user));
		$xsql = new phpSQL();
		$query="SELECT user_id FROM port_user WHERE user_name like '".addslashes($user)."' and passwd like '".addslashes($encpw)."'";
		//print_r($query);
		$xsql->query($query);
		$res = $xsql->runquery();
		if(mysql_num_rows($res)>0)
		{
			$row = mysql_fetch_array($res);
			$_SESSION['logged_in']=true;
			$_SESSION['user_id']=$row[0];
			$_SESSION['user']=$user;
			
			$newAuth=checkNewAuth($_SESSION['user_id']);			
			if($newAuth[0])
			{
				$text.=getNewAuth($newAuth);
				//$text.="Logged in.";
			}
			else
			{
				header("Location: index.php");
			}		
		}
	}
	else
	{
		$error.="Credentials could not be verified. Please make sure that your username and password are correct. ".buildLink("forgot.php","Forgot Password?");
	}
	
}
else
{
	$text.=startForm($type="POST",$_SERVER['PHP_SELF']);				
		$text.=startTable();			
		//username
		$text.="<tr>";
		$text.="<td>Username:</td>";
		$text.="<td>".buildTextbox("username")."</td>";
		$text.="</tr>";
		//password
		$text.="<tr>";
		$text.="<td>Password:</td>";
		$text.="<td>".buildPasswordbox("passwd")."</td>";
		$text.="</tr>";			
		$text.=endTable();
	$text.=endForm("Submit");
}

$html=startPage("style", "User Login", "include/css/");
		//start wrapper
		$html.=startDiv("content_wrapper","wrapper");
			$html.=startDiv("header","header");				
			$html.=endDiv();
			$html.=startDiv("main_content", "content");
				$html.=buildMainMenu($_SERVER['PHP_SELF']);
				if(!$error==null)
				{
					$html.=$error;
				}
				else
				{
					$html.=$text;
				}
			$html.=endDiv();
			$html.=startDiv("footer","footer");				
			$html.=endDiv();		
		$html.=endDiv();
$html.=endPage();

print($html);
?>