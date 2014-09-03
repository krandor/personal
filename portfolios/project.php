<?php
//page to add and edit projects
/*
** NEEDED FUNCTIONALITY
** -add projects
** -add primary image
** -add additional images
** -edit an existing project
*/
/*
** PROJECT DETAILS
** -name
** -category
** -tags
** -description
** -components (materials used / technologies used)
*/
session_start();
require_once('include/classes.php');
if($_SESSION['logged_in'])
{
	$userID=$_SESSION['user_id'];
	$error=null;
	if($_GET['a']=="add")//add a new project
	{
		if(isset($_POST['submit'])) //if the submit button has been pressed
		{
			$projnm=$_POST['projname'];//*required*
			$cat=$_POST['categories'];//will be the id number of the selected category *required*
			$size=$_POST['size'];//*required*
			$desc=$_POST['desc'];//*required*
			$tags=$_POST['tags'];//we'll split this up later
			$comps=$_POST['comps'];//*required*
			$img=$_FILES['img_path'];//img data array			
			
			//VALIDATIONS
			//make sure all the required fields are filled out
			if((trim($projnm)=="") || (trim($cat)=="") || (trim($size)=="") || (trim($desc)=="") || (trim($comps)==""))
			{
				$error.="Not everything was filled out! make sure all the required fields are filled out and try again. <br/>";
			}
			
			//make sure there isn't already a project with that name in the database
			$chkProj=checkProjectNm($projnm);
			if($chkProj[0])
			{
				$error.="A project with that name already exists, you can ".buildLink("project.php?p=".$chkProj[1],"check it out")." to make sure you're not trying to duplicate projects";
			}
			//END VALIDATIONS
			if($error==null)
			{
				//save that shit
				$xsql=new phpSQL();
				$query="INSERT INTO port_proj (proj_name, cat_id, size, components, `desc`, thumb_img, create_dt, create_by, mod_dt) VALUES ".
				"('".addslashes($projnm)."', ".
				$cat.", ".
				"'".addslashes($size)."', ".
				"'".addslashes($comps)."', ".
				"'".addslashes($desc)."', null, unix_timestamp(), ".$userID.", unix_timestamp())";
				$xsql->query($query);
				$xsql->runquery();
				
				$projarr = checkProjectNm($projnm);
				$projID = $projarr[1];
				
				if($img['error']==0) 
				{					
					$projdir = "images/projects/".$projID."/";
					
					$imgbasepath = createProjFolder($projdir);
					if(!$imgbasepath==false)
					{
						$imgpath = $imgbasepath.$projID.".".file_extension($img['name']);
						
						//upload the image to the server (into the user directory)										
						if(move_uploaded_file($img['tmp_name'],$imgpath))
						{
							//update the user record with the new image path
							$query="UPDATE port_proj SET thumb_img='".$imgpath."' WHERE proj_id=".$projID;
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
						$error.="An error occured in the directory creation for the project.";
					}
				}
				
				//add tags
				if(!trim($tags)=="")
				{
					$tagArr=split(",",$tags);					
					
					$query="REPLACE INTO port_proj_tags (proj_id, tag) VALUES ";
					
					for($i=0;$i<sizeof($tagArr);$i++)
					{
						if($i+1==sizeof($tagArr))
						{
							$query.="({$projID},{$tagArr[$i]})";	
						}
						else
						{
							$query.="({$projID},{$tagArr[$i]}),";
						}
					}
					$xsql->query($query);
					$xsql->runquery();
				}
				if($error==null)
				{
					//associate the current user with the new project
					userProjAutoAssoc($userID, $projID);					
					$text.="Project has been added, and you have automatically been associated with it. You can click ".buildLink("project.php?a=edit?id=".$projID,"here")." to further edit the project info.";
				}
			}
			
		}
		else
		{
			$text.="<p>Fields in italics are <span class='required'>REQUIRED</span>.</p>";	
				$text.=startForm($type="POST",$_SERVER['PHP_SELF']."?a=add","multipart/form-data");		
					$text.="<input type='hidden' name='MAX_FILE_SIZE' value='500000' />";
					$text.=startTable();			
						//project name
						$text.="<tr>";
						$text.="<td class='required'>Project Name:</td>";
						$text.="<td>".buildTextbox("projname")."</td>";
						$text.="</tr>";
						//category
						//get categories from database			
						$text.="<tr>";
						$text.="<td class='required'>Category:</td>";
						$text.="<td>".buildSelect(getCategories(),"categories")."</td>";
						$text.="</tr>";
						//size (volume, lines of code, weight, whatever)
						$text.="<tr>";
						$text.="<td class='required'>Size:</td>";
						$text.="<td>".buildTextbox("size")."</td>";
						$text.="</tr>";
						//description
						$text.="<tr>";
						$text.="<td class='required'>Description:</td>";
						$text.="<td>".buildTextarea("desc","","50","3")."</td>";
						$text.="</tr>";
						//tags (comma separated)
						$text.="<tr>";
						$text.="<td>Tags (comma separated):</td>";
						$text.="<td>".buildTextbox("tags")."</td>";
						$text.="</tr>";
						//components
						$text.="<tr>";
						$text.="<td class='required'>Components:</td>";
						$text.="<td>".buildTextbox("comps")."</td>";
						$text.="</tr>";
						//imagepath
						$text.="<tr>";
						$text.="<td>Project Image: (max size: 500kb) </td>";
						$text.="<td>".buildFilebox("img_path")."</td>";
						$text.="</tr>";			
					$text.=endTable();
				$text.=endForm("Submit");
		}
	}
	elseif($_GET['a']=="edit")	
	{
		//edit an existing project 
		//(only the original creator can edit the actual project info)
		//otherwise edit contribution for the user to the project
		if(isset($_GET['id']))
		{
			$projID = $_GET['id'];
			$projCreator=getProjCreatorID($projID);
			if(isset($_POST['submit']))
			{
				$projnm=strip_tags($_POST['projname']);//*required*
				$cat=strip_tags($_POST['categories']);//will be the id number of the selected category *required*
				$size=strip_tags($_POST['size']);//*required*
				$desc=strip_tags($_POST['desc']);//*required*
				$tags=strip_tags($_POST['tags']);//we'll split this up later
				$comps=strip_tags($_POST['comps']);//*required*
				$img=$_FILES['img_path'];//img data array					
			
				//VALIDATIONS
				//make sure all the required fields are filled out
				if((trim($projnm)=="") || (trim($cat)=="") || (trim($size)=="") || (trim($desc)=="") || (trim($comps)==""))
				{
					$error.="Not everything was filled out! make sure all the required fields are filled out and try again. <br/>";
				}
				
				if($error==null)
				{
					$xsql=new phpSQL();
					$query="UPDATE port_proj SET ";
					if(!$projnm==$_SESSION['PROJNAME'])
					{
						$query.="proj_name='".addslashes($projnm)."', ";
					}
					if(!$cat==$_SESSION['CAT'])
					{
						$query.="cat=".$cat.", ";
					}
					if(!$size==$_SESSION['SIZE'])
					{
						$query.="size='".addslashes($size)."', ";
					}
					if(!$comps==$_SESSION['COMPS'])
					{
						$query.="components='".addslashes($comps)."', ";
					}
					if(!$desc==$_SESSION['DESC'])
					{
						$query.="`desc`='".addslashes($desc)."', ";
					}
					if($img['error']==0)
					{
						$projdir = "images/projects/".$projID."/";
						
						$imgbasepath = createProjFolder($projdir);
						if(!$imgbasepath==false)
						{
							$imgpath = $imgbasepath.$projID.".".file_extension($img['name']);
							
							//upload the image to the server (into the user directory)										
							if(move_uploaded_file($img['tmp_name'],$imgpath))
							{
								$query.="thumb_img='".$imgpath."', ";
							}
						}							
					}
					$query.="mod_dt=unix_timestamp() ".
					"WHERE proj_id=".$projID;
					$xsql->query($query);
					$xsql->runquery();
					
					//add tags
					if(!trim($tags)=="")
					{
						$tagArr=split(",",$tags);					
						
						$query="REPLACE INTO port_proj_tags (proj_id, tag) VALUES ";
						
						for($i=0;$i<sizeof($tagArr);$i++)
						{
							if($i+1==sizeof($tagArr))
							{
								$query.="({$projID},'".addslashes(trim($tagArr[$i]))."')";	
							}
							else
							{
								$query.="({$projID},'".addslashes(trim($tagArr[$i]))."'),";
							}
						}
						$xsql->query($query);
						$xsql->runquery();
					}
					
					//session var clean up
					unset($_SESSION['PROJNAME']);
					unset($_SESSION['CAT']);
					unset($_SESSION['SIZE']);
					unset($_SESSION['COMPS']);
					unset($_SESSION['DESC']);
					unset($_SESSION['THUMB']);
				}
			}
			else
			{
				if(!$projCreator)
				{
					$error.="Couldn't find project creator!";				
				}
				if($error==null)
				{
					//make sure the logged in user and the creator of the project are the same
					if($projCreator==$_SESSION['user_id'])
					{
						//build the form and fill it with the users' data
						$proj_arr=getProjectData($projID);
						
						if($proj_arr==null)
						{
							$error.="Error retrieving project data";
						}						
						
						if($error==null)
						{								
							$_SESSION['PROJNAME']=$proj_arr['PROJ_NAME'];
							$_SESSION['CAT']=$proj_arr['CAT'];
							$_SESSION['SIZE']=$proj_arr['SIZE'];
							$_SESSION['COMPS']=$proj_arr['COMPS'];
							$_SESSION['DESC']=$proj_arr['DESC'];
							$_SESSION['THUMB']=$proj_arr['THUMB'];
							
							$text.=startForm($type="POST",$_SERVER['PHP_SELF']."?a=edit&id=".$projID,"multipart/form-data");		
								$text.="<input type='hidden' name='MAX_FILE_SIZE' value='500000' />";
								$text.=startTable();			
									//project name
									$text.="<tr>";
									$text.="<td>Project Name:</td>";
									$text.="<td>".buildTextbox("projname",$_SESSION['PROJNAME'])."</td>";
									$text.="</tr>";
									//category
									//get categories from database			
									$text.="<tr>";
									$text.="<td>Category:</td>";
									$text.="<td>".buildSelect(getCategories(),"categories",$_SESSION['CAT'])."</td>";
									$text.="</tr>";
									//size (volume, lines of code, weight, whatever)
									$text.="<tr>";
									$text.="<td>Size:</td>";
									$text.="<td>".buildTextarea("size", $_SESSION['SIZE'])."</td>";
									$text.="</tr>";
									//description
									$text.="<tr>";
									$text.="<td>Description:</td>";
									$text.="<td>".buildTextarea("desc", $_SESSION['DESC'],"50","3")."</td>";
									$text.="</tr>";
									//tags (comma separated)
									$text.="<tr>";
									$text.="<td>Tags (comma separated):</td>";
									$text.="<td>".getTags($projID)."</td>";
									$text.="</tr>";
									//components
									$text.="<tr>";
									$text.="<td>Components:</td>";
									$text.="<td>".buildTextarea("comps", $_SESSION['COMPS'])."</td>";
									$text.="</tr>";
									//imagepath
									$text.="<tr>";
									$text.="<td>Project Image: (max size: 500kb) </td>";
									$text.="<td>".buildFilebox("img_path")."</td>";
									$text.="</tr>";			
								$text.=endTable();
							$text.=endForm("Submit");
						}
					}
					else
					{
						$error = "Only the original creator can edit this project.";
					}
				}
			}
		}
	}
	elseif($_GET['a']=="assoc")//associate the project with someone's portfolio
	{	
		if(isset($_GET['id']))
		{
			$projID = strip_tags($_GET['id']);
			$check = checkProjectID($projID);
			if($check[0])
			{
				$xsql = new phpSQL();
				//create a record in the user_proj_assoc table
				$xsql->query("INSERT IGNORE INTO user_proj_assoc VALUES ({$userID},{$projID})");
				$xsql->runquery();
				//create a record in the user_proj_auth table with 0 for auth state
				$xsql->query("INSERT IGNORE INTO user_proj_auth VALUES ({$userID},{$projID}, unix_timestamp(),0)");
				$xsql->runquery();
				$text.="Your request to be added to this project has been logged. The project author will be notified and instructed to validate your credentials.";
			}
			else
			{
				$error.="An error occured during the association process, please try again.";
			}
		}
	}
	elseif($_GET['a']=="myproj")
	{
		//projects personally added
		$text.=startDiv("myauths","project");
			$text.=getUserProjects($userID, "AUTH");
		$text.=endDiv();
		//projects associated with
		$text.=startDiv("myassoc","project");
			$text.=getUserProjects($userID, "ASSOC");
		$text.=endDiv();
	}	
	else
	{
		//check for a p=projID in the addr bar, if not, show last 10 projects added
		/*
		** -if there is a projID, show the project info and all users part of it
		*/
		if(isset($_GET['id']))
		{
			$text.=getProject($_GET['id']);
			$text.=getProjectUsers($_GET['id']);
		}
	}
}
else
{
	//check for a p=projID in the addr bar, if not, show last 10 projects added
	/*
	** -if there is a projID, show the project info and all users part of it
	*/
	if(isset($_GET['id']))
	{
		$text.=getProject($_GET['id']);
		$text.=getProjectUsers($_GET['id']);
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