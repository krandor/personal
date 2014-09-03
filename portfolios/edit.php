<?php
//page to edit your profile
/*
** Needed functionality
** -edit user picture
** -edit real name
** -edit password
** -email address
** -link to add projects
** -username is not editable because that would cause problems with links breaking (ie: http://host.com/portfolios.php?u=username)
*/
session_start();
require_once('include/classes.php');

//make sure that the user is logged in else take him back to the index
if($_SESSION['logged_in'])
{
	//get the userID for the user in the session
	$userID = $_SESSION['user_id'];
	$user = $_SESSION['user'];
	$error=null;
	if((!empty($userID)) && (!empty($user)))
	{
		if(isset($_POST['submit']))
		{
			$fname=strip_tags($_POST['f_name']);
			$lname=strip_tags($_POST['l_name']);
			$passwd=trim(strip_tags($_POST['passwd']));
			$cpasswd=trim(strip_tags($_POST['cpasswd']));
			$email=strip_tags($_POST['email']);
			$bio=strip_tags($_POST['bio']);
			$img=$_FILES['img_path'];
			
			//make sure the passwords match
			if(!$passwd===$cpasswd)
			{
				$error.="\nPassword and Confirmation do not match! Please go back and make sure they're typed identically!\n<br/>";
			}
			
			//make sure the email address if valid
			if(!emailCheck($email))
			{
				$error.="\nThe email address you provided is not in the correct format.\n<br/>";
			}	
			if($error==null)
			{
				//update profile
				$xsql = new phpSQL();
				$query = "UPDATE port_user SET ";
				//check first name
				if(!$fname==$_SESSION['FIRSTNAME'])
				{
					$query.=" user_f_name='".addslashes($fname)."', ";
				}
				//check last name
				if(!$lname==$_SESSION['LASTNAME'])
				{
					$query.=" user_l_name='".addslashes($lname)."', ";
				}
				//check email address
				if(!$email==$_SESSION['EMAILADDR'])
				{
					$query.=" email_addr='".addslashes($email)."', ";
				}
				//check to make sure if the new password box is not empty, save the updated pw 
				//(no confirmation match needed as that validation has been done already)
				if(!empty($passwd))
				{
					$encpw = crypt(md5($passwd),md5($user));
					$query.=" passwd='".$encpw."', ";
				}
				//if a picture was uploaded, overwrite the existing one.
				if($img['error']==0)
				{
					$imgbasepath = 'images/users/';
					$imgpath = $imgbasepath.$userID.".".file_extension($img['name']);
					if(move_uploaded_file($img['tmp_name'],$imgpath))
					{
						//update the user record with the new image path
						$query.=" img_path='".$imgpath."', ";						
					}
				}
				//finish up the sql statement and add on the mod_dt
				$query.=" mod_dt=unix_timestamp() WHERE user_id=".$userID;
				$xsql->query($query);
				$xsql->runquery();
				//print($bio."<br/>");
				//print($_SESSION['BIO']);
				//update the bio
				if(!($bio==$_SESSION['BIO']))
				{
					$query="REPLACE INTO port_user_bio (user_id, bio) VALUES (".$userID.",'".addslashes($bio)."')";
					//print($query);
					$xsql->query($query);
					$xsql->runquery();
				}
				$text.="Update complete! Go back to the ".buildLink("index.php","Main page")." or ".buildLink("project.php?a=add","add a project");
				//session var clean up
				unset($_SESSION['FIRSTNAME']);
				unset($_SESSION['LASTNAME']);
				unset($_SESSION['EMAILADDR']);
				unset($_SESSION['IMGPATH']);
				unset($_SESSION['BIO']);
			}
		}
		else
		{
			//build the form and fill it with the users' data
			$user_arr=getUserData($userID);
			if($user_arr==null)
			{
				$error = "Error retrieving user data.";
			}
			if($error==null)
			{
				//set session vars for when the user updates his account 
				//(then check the updated values against the stored ones to determine if updates should be run)
				$_SESSION['FIRSTNAME']=$user_arr['F_NAME'];
				$_SESSION['LASTNAME']=$user_arr['L_NAME'];
				$_SESSION['EMAILADDR']=$user_arr['EMAIL_ADDR'];
				$_SESSION['IMGPATH']=$user_arr['IMG_PATH'];
				$_SESSION['BIO']=$user_arr['BIO'];
				
				//print_r($_SESSION);			
				//build the form
				$text.=startForm($type="POST",$_SERVER['PHP_SELF'],"multipart/form-data");		
					$text.="<input type='hidden' name='MAX_FILE_SIZE' value='500000' />";
					$text.=startTable();
						//first name
						$text.="<tr>";
							$text.="<td>First Name:</td>";
							$text.="<td>".buildTextbox("f_name",$_SESSION['FIRSTNAME'])."</td>";
						$text.="</tr>";
						//last name
						$text.="<tr>";
							$text.="<td>Last Name:</td>";
							$text.="<td>".buildTextbox("l_name",$_SESSION['LASTNAME'])."</td>";
						$text.="</tr>";		
						//password
						$text.="<tr>";
							$text.="<td>New Password:</td>";
							$text.="<td>".buildPasswordbox("passwd")."</td>";
						$text.="</tr>";
						//confirm password
						$text.="<tr>";
							$text.="<td>Confirm New Password:</td>";
							$text.="<td>".buildPasswordbox("cpasswd")."</td>";
						$text.="</tr>";
						//email address
						$text.="<tr>";
							$text.="<td>Email Address:</td>";
							$text.="<td>".buildTextbox("email",$_SESSION['EMAILADDR'])."</td>";
						$text.="</tr>";		
						//imagepath
						$text.="<tr>";
							$text.="<td>Personal Image: (max size: 500kb) </td>";
							$text.="<td>".buildFilebox("img_path")."</td>";
						$text.="</tr>";			
						//bio
						//email address
						$text.="<tr>";
							$text.="<td>Bio:</td>";
							$text.="<td>".buildTextarea("bio",$_SESSION['BIO'],"50","3")."</td>";
						$text.="</tr>";
					$text.=endTable();		
				$text.=endForm("Update Profile");				
				$newAuth=checkNewAuth($userID);			
				if($newAuth[0])
				{
					$text.=getNewAuth($newAuth);
					//$text.="Logged in.";
				}
			}
		}
	}
	else
	{
		//die("Something is wrong! please ".buildLink("logout.php","logout")." and back in.");
		print("Something is wrong! please ".buildLink("logout.php","logout")." and back in.");
		
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
}
else
{	
	header("Location: index.php");
}
?>