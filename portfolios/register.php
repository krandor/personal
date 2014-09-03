<?php
session_start();
require_once('include/classes.php');

if(isset($_POST['submit'])) 
{
	$user=strip_tags($_POST['username']);
	$fname=strip_tags($_POST['f_name']);
	$lname=strip_tags($_POST['l_name']);
	$passwd=trim(strip_tags($_POST['passwd']));
	$cpasswd=trim(strip_tags($_POST['cpasswd']));
	$email=strip_tags($_POST['email']);
	$img=$_FILES['img_path'];

	//VALIDATIONS!	
	$error=null;
	//make sure all the required fields are filled out
	if((trim($_POST['username'])=="" || trim($_POST['passwd'])=="" || trim($_POST['cpasswd'])=="" || trim($_POST['email'])=="" || trim($_POST['f_name'])=="" || trim($_POST['l_name'])==""))
	{
		$error.="\nRequired Information Missing!<br />Please go back to make sure everything required is filled out.\n<br/>";
	}
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
	//make sure the username is not already used
	if(userCheck($user))
	{
		$error.="\nThe username you provided is already in use! If you already have an account, please ".buildLink("login.php","login here").".";
	}
	//END VALIDATIONS!
	
	if($error==null)
	{
		$xsql = new phpSQL();
		
		//encrypt the password
		$encpw = crypt(md5($passwd),md5($user));
		
		//save the user information
		$query="INSERT INTO port_user (user_name, passwd, email_addr, user_f_name, user_l_name, img_path, create_dt) VALUES ".
		"('".addslashes($user)."',".
		"'".$encpw."',".
		"'".addslashes($email)."',".
		"'".addslashes($fname)."',".
		"'".addslashes($lname)."',".
		"null,".
		"unix_timestamp())";			
		$xsql->query($query);
		$xsql->runquery();
		
		//if there is an image we need to update the img_path field in the user table
		if($img['error']==0) 
		{
			//find the user_id of the user that just registered
			$xsql->query("SELECT user_id FROM port_user WHERE user_name like '".addslashes($user)."'");
			$res = $xsql->runquery();
			if(mysql_num_rows($res)>0) 
			{
				$row = mysql_fetch_row($res);
				$userID=$row[0];
				$imgbasepath = 'images/users/';
				$imgpath = $imgbasepath.$userID.".".file_extension($img['name']);
				//upload the image to the server (into the user directory)										
				if(move_uploaded_file($img['tmp_name'],$imgpath))
				{
					//update the user record with the new image path
					$query="UPDATE port_user SET img_path='".$imgpath."' WHERE user_id=".$userID;
					$xsql->query($query);
					$xsql->runquery();
				}
				else
				{
					$error.="An error occured uploading the picture file";
				}
				
			}
			else
			{
				$error.= "While trying to update the user ({$user}) record with the user picture, an error occured in finding ther userID.";
			}
		}
		
		
		$text.="Congratulations! You've succeeded in creating an account, please ".
		buildLink("login.php","login")." to add projects to your account.";		
	}
}
else
{	
	$text.="<p>Fields in italics are <span class='required'>REQUIRED</span>.</p>";	
	$text.=startForm($type="POST",$_SERVER['PHP_SELF'],"multipart/form-data");		
		$text.="<input type='hidden' name='MAX_FILE_SIZE' value='500000' />";
		$text.=startTable();			
		//username
		$text.="<tr>";
		$text.="<td class='required'>Username:</td>";
		$text.="<td>".buildTextbox("username")."</td>";
		$text.="</tr>";
		//password
		$text.="<tr>";
		$text.="<td class='required'>Password:</td>";
		$text.="<td>".buildPasswordbox("passwd")."</td>";
		$text.="</tr>";
		//confirm password
		$text.="<tr>";
		$text.="<td class='required'>Confirm Password:</td>";
		$text.="<td>".buildPasswordbox("cpasswd")."</td>";
		$text.="</tr>";
		//email address
		$text.="<tr>";
		$text.="<td class='required'>Email Address:</td>";
		$text.="<td>".buildTextbox("email")."</td>";
		$text.="</tr>";
		//first name
		$text.="<tr>";
		$text.="<td class='required'>First Name:</td>";
		$text.="<td>".buildTextbox("f_name")."</td>";
		$text.="</tr>";
		//last name
		$text.="<tr>";
		$text.="<td class='required'>Last Name:</td>";
		$text.="<td>".buildTextbox("l_name")."</td>";
		$text.="</tr>";
		//imagepath
		$text.="<tr>";
		$text.="<td>Personal Image: (max size: 500kb) </td>";
		$text.="<td>".buildFilebox("img_path")."</td>";
		$text.="</tr>";			
		$text.=endTable();
	$text.=endForm("Submit");			
}

$html.=startPage("style", "User Registration", "include/css/");
		//start wrapper
		$html.=startDiv("content_wrapper","wrapper");
			$html.=startDiv("header","header");
				//$html.="*header* (placeholder)";
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
				//$html.="*footer* (placeholder)";
			$html.=endDiv();		
		$html.=endDiv();
	$html.=endPage();
print($html);
?>