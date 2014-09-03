<?php
require_once 'config.php';
require_once 'htmlGen.php';
require_once 'phpSQL.php';

/*
project specific classes below
*/
//$xsql = new phpSQL();
function getLatestNews($count=5)
{
	$xsql = new phpSQL();
	$xsql->query("SELECT n.news_id, n.subject, n.body, u.user_name, from_unixtime(n.create_dt) FROM port_news n INNER JOIN port_user u on n.create_by=u.user_id LIMIT ".$count);
	$res = $xsql->runquery();
	
	$html=startTable();
	while($row=mysql_fetch_row($res))
	{
		$html.="<tr><td>".$row[1]."</td><td>//".$row[3]."//</td></tr>";
		$html.="<tr><td colspan=2>".str_replace("\n","<br />",$row[2])."</td></tr>";
		$html.="<tr><td colspan=2>Posted on: ".$row[4]."</td></tr>";
	}
	$html.=endTable();	
	return $html;
}

//gets the user information for a given username
function getUserInfo($username)
{
	$xsql = new phpSQL();
	$xsql->query("SELECT user_id, user_f_name AS `F_NAME`, user_l_name AS `L_NAME` FROM port_user WHERE user_name like '".addslashes($username)."'");
	$res = $xsql->runquery();
	if(mysql_num_rows($res)>0)
	{
		return mysql_fetch_array($res); //get the first row (should only be one person per username, username is unique in the table)		
	}
	return null;
}
//gets the user information for a given userID (gets more data than plain ol' getUserInfo() as well)
function getUserData($userID)
{
	$xsql = new phpSQL();
	$xsql->query("SELECT u.user_f_name AS `F_NAME`, u.user_l_name AS `L_NAME`, u.email_addr AS `EMAIL_ADDR`, u.img_path AS `IMG_PATH`, b.bio as `BIO` ".
	"FROM port_user u ".
	"LEFT JOIN  port_user_bio b on u.user_id=b.user_id ".
	"WHERE u.user_id=".$userID);
	$res = $xsql->runquery();
	if(mysql_num_rows($res)>0)
	{
		return mysql_fetch_array($res); //get the first row (should only be one person per username, username is unique in the table)		
	}
	return null;
}

//gets the user information for a given userID (gets more data than plain ol' getUserInfo() as well)
function getProjectData($projID)
{
	$xsql = new phpSQL();
	$xsql->query("SELECT proj_name AS `PROJ_NAME`, cat_id AS `CAT`, size AS `SIZE`, components as `COMPS`, `desc` as `DESC`, thumb_img as `THUMB` ".
	"FROM port_proj WHERE proj_id=".$projID);
	$res = $xsql->runquery();
	if(mysql_num_rows($res)>0)
	{
		return mysql_fetch_array($res); //get the first row (should only be one person per username, username is unique in the table)		
	}
	return null;
}

//gets the bio of a user using the userID gained from getUserInfo()
function getUserBio($uid)
{
	$xsql = new phpSQL();	
	
	//get the users image if there is one
	$xsql->query("SELECT img_path FROM port_user WHERE user_id=".$uid);
	$res = $xsql->runquery();
	
	if(mysql_num_rows($res)>0)
	{
		$row = mysql_fetch_row($res);
		$imgpath = $row[0];
		$html.=startSpan("bio_img","bio_img").buildImage($imgpath).endSpan();;
	}
	
	//get the users' bio if there is one.
	$xsql->query("SELECT `bio` FROM `port_user_bio` WHERE `user_id`=".$uid);	
	$res = $xsql->runquery();
	
	if(mysql_num_rows($res)>0)
	{
		$row = mysql_fetch_row($res);
		$bio =  $row[0];
		$html.="<p>{$bio}</p>";
	}
	
	if(!empty($html))
	{
		return $html;
	}	
	return null;	
}
//get categories
function getCategories($search="")
{
	$xsql = new phpSQL();
	$xsql->query("SELECT cat_id, cddef FROM `lkup_category_id` ORDER BY cddef");
	$res = $xsql->runquery();
	$cats = array();
	if(!$search=="")
	{
		$cats[]=array(0,"ALL");
	}
	while($row=mysql_fetch_row($res))
	{
		$cats[]=$row;
	}
	return $cats;
}
//get all tags for a project
function getTags($pid)
{
	$xsql = new phpSQL();
	$xsql->query("SELECT tag FROM port_proj_tags WHERE proj_id=".$pid);
	$res=$xsql->runquery();
	if(mysql_num_rows($res)>0)
	{
		while($row=mysql_fetch_row($res))
		{
			$tags.=$row[0].", ";
		}
		$tags=substr($tags,0,-2);		
	}
	return buildTextarea("tags",$tags);
	
}
//gets the info for the project being requested
function getProject($pid)
{
	$query="SELECT pp.proj_name, lci.cddef, pp.thumb_img, pp.`size`, pp.components, pp.`desc`, pp.proj_id ".
	"FROM port_proj pp ".
	"LEFT JOIN lkup_category_id lci on pp.cat_id=lci.cat_id ".	
	"WHERE pp.proj_id=".$pid;
	$xsql = new phpSQL();	
	$xsql->query($query);
	$res = $xsql->runquery();
	
	while($row=mysql_fetch_row($res))
	{
		/*
		0-project name
		1-project category
		2-thumbnail path
		3-lines of code
		4-components/technology used
		5-description
		6-contribution
		7-challenges
		8-contribution timeframe
		9-project id
		*/
		$projNm = str_replace(' ','_',$row[0]);
		$html.=startDiv($projNm,"project");				
			$html.=startDiv($projNm."_thmb","thumb");
				if($row[2]!=null)
				{
					$html.=buildImage($row[2]);
				}			
			$html.=endDiv();			
			
			$html.=startDiv($projNm."_nm", "projname");
				$html.=startSpan($projNm."_nmhdr", "projnmhdr");
					$html.="Project: ";
				$html.=endSpan();
				$html.=$row[0];
			$html.=endDiv();
			
			$html.=startDiv($projNm."_cat", "category");
				$html.=startSpan($projNm."_cathdr","cathdr");
					$html.="Category: ";
				$html.=endSpan();
				$html.=$row[1];
			$html.=endDiv();
			$html.=startDiv($projNm."_loc", "loc");
				if($row[3]!=null)
				{
					$html.=startSpan($projNm."_lochdr","lochdr");
						$html.="Size: ";
					$html.=endSpan();
					$html.=$row[3];
				}
			$html.=endDiv();
			$html.=startDiv($projNm."_comp", "components");
				$html.=startSpan($projNm."_comphdr","comphdr");
					$html.="Components: ";
				$html.=endSpan();
				$html.=$row[4];
			$html.=endDiv();
			$html.=startDiv($projNm."_desc", "desc");
				$html.=startSpan($projNm."_deschdr","deschdr");
					$html.="Description: ";
				$html.=endSpan();
				$html.=$row[5];
			$html.=endDiv();			
			$html.=startDiv($projNm."_imgs", "images");
				$html.=getExtraProjImages($row[9]);
			$html.=endDiv();
			if($_SESSION['logged_in'])
			{
				//check if the logged in user is already part of the project listed
				//*get list of proj_id's the user is part of (or in the process of being authorised) and check it against the current proj
				$apprvprojs=getUserProjIDs($_SESSION['user_id'], true);
				$authprojs=getUserProjIDs($_SESSION['user_id'], false);
				
									
				if((!in_array($row[6],$apprvprojs))&&(!in_array($row[6],$authprojs)))				
				{
					$html.=startDiv($projNm."_assoc","edit");
					$html.=buildLink("project.php?a=assoc&id=".$row[6],"Add yourself to this project")." ";
					$html.=endDiv();
				}				
				if((in_array($row[6],$authprojs)))
				{
					$html.=startDiv($projNm."_assoc","edit");
					$html.="You are still waiting to be approved for this Project.";
					$html.=endDiv();
				}
				
			}
		$html.=endDiv();		
	}
	return $html;
}

//get the users for a project
function getProjectUsers($pid="")
{
	if(!empty($pid))
	{
		$xsql = new phpSQL();		
		$xsql->query("SELECT `user`.user_f_name, `user`.user_l_name, `user`.img_path, `user`.user_name ".
		"FROM user_proj_assoc `assoc` ".
		"INNER JOIN user_proj_auth `auth` on `assoc`.user_id=`auth`.user_id and `assoc`.proj_id=`auth`.proj_id and `auth`.auth_state=1 ".
		"INNER JOIN port_user `user` on `assoc`.user_id=`user`.user_id ".
		"WHERE `assoc`.proj_id=".$pid);
		$res=$xsql->runquery();
		//print($xsql->getquery());
		while($info=mysql_fetch_row($res))
		{			
			$name = $info[0]." ".$info[1];
			$uname = $info[3];
			if(!$info[2]==null)
			{
				$imgpath = $info[2];//big image			
				$sm_img = file_dir($info[2])."/".file_name($info[2])."_sm.".file_extension($info[2]);				
				if((!file_exists($sm_img)) || (filemtime($imgpath) > filemtime($sm_img)))
				{
					//print("<br/>big: ".filemtime($imgpath)." small: ".filemtime($sm_img));
					//print("resized: {$sm_img}");
					$resize = new ImgResizer($imgpath);
					$resize->resize(64,$sm_img);
					//echo "File Created: {$sm_img} <br />";
				}
			}
			
			$text.=startDiv("user_wrapper","user_wrapper");
			if(!$imgpath==null)
			{
				$text.=startSpan("userimg","userimg");
					$text.=buildLink("portfolio.php?u=".$uname,buildImage($sm_img));
				$text.=endSpan();
			}
			if(!$name==null)
			{
				$text.=startSpan("username","username");
					$text.=buildLink("portfolio.php?u=".$uname,$name);
				$text.=endSpan();
			}						
			$text.=endDiv();			
		}		
		return $text;
	}
}
//get project id's a user is part of
function getUserProjIDs($uid, $auth=true)
{
	$a=1;
	if(!$auth)
	{
		$a=0;
	}
	$xsql = new phpSQL();
	$xsql->query("SELECT `assoc`.proj_id ".
	"FROM user_proj_assoc `assoc` ".
	"INNER JOIN user_proj_auth `auth` on `assoc`.user_id=`auth`.user_id and `assoc`.proj_id=`auth`.proj_id and `auth`.auth_state={$a} ".
	"WHERE `assoc`.user_id=".$uid);
	$res = $xsql->runquery();
	$ids = array();
	while($row=mysql_fetch_row($res))
	{
		$ids[]=$row[0];
	}
	return $ids;
}
//get projects based on the tag search criteria passed in
function getProjects($s="",$c="0")
{
	$query="";
	if((!$s=="")&&(!$c=="0"))
	{
		$query="SELECT pp.proj_name, lci.cddef, pp.thumb_img, pp.`size`, pp.components, pp.`desc`, pp.proj_id ".
		"FROM port_proj pp ".
		"INNER JOIN lkup_category_id lci on pp.cat_id=lci.cat_id and lci.cat_id={$c} ".
		"INNER JOIN port_proj_tags tag on pp.proj_id=tag.proj_id and tag.tag in {$s} ".
		"GROUP BY proj_id";		
	}
	elseif(!($s==""))
	{
		$query="SELECT pp.proj_name, lci.cddef, pp.thumb_img, pp.`size`, pp.components, pp.`desc`, pp.proj_id ".
		"FROM port_proj pp ".
		"INNER JOIN lkup_category_id lci on pp.cat_id=lci.cat_id ".
		"INNER JOIN port_proj_tags tag on pp.proj_id=tag.proj_id and tag.tag in {$s} ".
		"GROUP BY proj_id";	
	}
	else
	{
		$query="SELECT pp.proj_name, lci.cddef, pp.thumb_img, pp.`size`, pp.components, pp.`desc`, pp.proj_id ".
		"FROM port_proj pp ".
		"INNER JOIN lkup_category_id lci on pp.cat_id=lci.cat_id ".		
		"GROUP BY proj_id";	
	}
	if($query=="")
	{
		return false;
	}
	$xsql = new phpSQL();
	$xsql->query($query);
	//print($query);
	$res = $xsql->runquery();
	
	while($row=mysql_fetch_row($res))
	{
		/*
		0-project name
		1-project category
		2-thumbnail path
		3-lines of code
		4-components/technology used
		5-description	
		6-project id
		*/
	
		$projNm = str_replace(' ','_',$row[0]);
		$html.=startDiv($projNm,"project");			
			
			$html.=startDiv($projNm."_thmb","thumb");
				if($row[2]!=null)
				{
					$html.=buildLink("project.php?id=".$row[6],buildImage($row[2]));
				}			
			$html.=endDiv();			
			
			$html.=startDiv($projNm."_nm", "projname");
				$html.=startSpan($projNm."_nmhdr", "projnmhdr");
					$html.="Project: ";
				$html.=endSpan();
				$html.=buildLink("project.php?id=".$row[6],$row[0]);
			$html.=endDiv();
			
			$html.=startDiv($projNm."_cat", "category");
				$html.=startSpan($projNm."_cathdr","cathdr");
					$html.="Category: ";
				$html.=endSpan();
				$html.=$row[1];
			$html.=endDiv();
			$html.=startDiv($projNm."_loc", "loc");
				if($row[3]!=null)
				{
					$html.=startSpan($projNm."_lochdr","lochdr");
						$html.="Size: ";
					$html.=endSpan();
					$html.=$row[3];
				}
			$html.=endDiv();
			$html.=startDiv($projNm."_comp", "components");
				$html.=startSpan($projNm."_comphdr","comphdr");
					$html.="Components: ";
				$html.=endSpan();
				$html.=$row[4];
			$html.=endDiv();
			$html.=startDiv($projNm."_desc", "desc");
				$html.=startSpan($projNm."_deschdr","deschdr");
					$html.="Description: ";
				$html.=endSpan();
				$html.=$row[5];
			$html.=endDiv();			
			$html.=startDiv($projNm."_imgs", "images");
				$html.=getExtraProjImages($row[6]);
			$html.=endDiv();	
			if($_SESSION['logged_in'])
			{
				//check if the logged in user is already part of the project listed
				//*get list of proj_id's the user is part of (or in the process of being authorised) and check it against the current proj
				$apprvprojs=getUserProjIDs($_SESSION['user_id'], true);
				$authprojs=getUserProjIDs($_SESSION['user_id'], false);
				
									
				if((!in_array($row[6],$apprvprojs))&&(!in_array($row[6],$authprojs)))				
				{
					$html.=startDiv($projNm."_assoc","edit");
					$html.=buildLink("project.php?a=assoc&id=".$row[6],"Add yourself to this project")." ";
					$html.=endDiv();
				}				
				if(in_array($row[6],$authprojs))
				{
					$html.=startDiv($projNm."_assoc","edit");
					$html.="You are still waiting to be approved for this Project.";
					$html.=endDiv();
				}
				
			}
		$html.=endDiv();
		
	}
	return $html;
}

//gets all the projects for the userID gained from getUserInfo()
function getUserProjects($uid, $type="ALL")
{

	if($type=="ALL")
	{
		$query="SELECT pp.proj_name, lci.cddef, pp.thumb_img, pp.`size`, pp.components, pp.`desc`, con.fdval, chg.fdval, concat('From ', cstm.fdval,' until ',cetm.fdval), upa.proj_id ".
		"FROM user_proj_assoc upa ".
		"INNER JOIN port_proj pp on upa.proj_id=pp.proj_id ".
		"INNER JOIN user_proj_auth auth on upa.user_id=auth.user_id and upa.proj_id=auth.proj_id and auth.auth_state=1 ".
		"LEFT JOIN lkup_category_id lci on pp.cat_id=lci.cat_id ".
		"LEFT JOIN user_proj_spec con on upa.user_id=con.user_id and upa.proj_id=con.proj_id and con.spec_id=1 ".
		"LEFT JOIN user_proj_spec chg on upa.user_id=chg.user_id and upa.proj_id=chg.proj_id and chg.spec_id=2 ".
		"LEFT JOIN user_proj_spec cstm on upa.user_id=cstm.user_id and upa.proj_id=cstm.proj_id and cstm.spec_id=3 ".
		"LEFT JOIN user_proj_spec cetm on upa.user_id=cetm.user_id and upa.proj_id=cetm.proj_id and cetm.spec_id=4 ".
		"WHERE upa.user_id=".$uid." GROUP BY proj_id";
	}
	elseif($type=="AUTH")
	{
		$query="SELECT pp.proj_name, lci.cddef, pp.thumb_img, pp.`size`, pp.components, pp.`desc`, con.fdval, chg.fdval, concat('From ', cstm.fdval,' until ',cetm.fdval), upa.proj_id ".
		"FROM user_proj_assoc upa ".
		"INNER JOIN port_proj pp on upa.proj_id=pp.proj_id and pp.create_by=".$uid." ".
		"INNER JOIN user_proj_auth auth on upa.user_id=auth.user_id and upa.proj_id=auth.proj_id and auth.auth_state=1 ".
		"LEFT JOIN lkup_category_id lci on pp.cat_id=lci.cat_id ".
		"LEFT JOIN user_proj_spec con on upa.user_id=con.user_id and upa.proj_id=con.proj_id and con.spec_id=1 ".
		"LEFT JOIN user_proj_spec chg on upa.user_id=chg.user_id and upa.proj_id=chg.proj_id and chg.spec_id=2 ".
		"LEFT JOIN user_proj_spec cstm on upa.user_id=cstm.user_id and upa.proj_id=cstm.proj_id and cstm.spec_id=3 ".
		"LEFT JOIN user_proj_spec cetm on upa.user_id=cetm.user_id and upa.proj_id=cetm.proj_id and cetm.spec_id=4 ".
		"WHERE upa.user_id=".$uid." GROUP BY proj_id";
	}
	elseif($type=="ASSOC")
	{
		$query="SELECT pp.proj_name, lci.cddef, pp.thumb_img, pp.`size`, pp.components, pp.`desc`, con.fdval, chg.fdval, concat('From ', cstm.fdval,' until ',cetm.fdval), upa.proj_id ".
		"FROM user_proj_assoc upa ".
		"INNER JOIN port_proj pp on upa.proj_id=pp.proj_id and not(pp.create_by=".$uid.") ".
		"INNER JOIN user_proj_auth auth on upa.user_id=auth.user_id and upa.proj_id=auth.proj_id and auth.auth_state=1 ".
		"LEFT JOIN lkup_category_id lci on pp.cat_id=lci.cat_id ".
		"LEFT JOIN user_proj_spec con on upa.user_id=con.user_id and upa.proj_id=con.proj_id and con.spec_id=1 ".
		"LEFT JOIN user_proj_spec chg on upa.user_id=chg.user_id and upa.proj_id=chg.proj_id and chg.spec_id=2 ".
		"LEFT JOIN user_proj_spec cstm on upa.user_id=cstm.user_id and upa.proj_id=cstm.proj_id and cstm.spec_id=3 ".
		"LEFT JOIN user_proj_spec cetm on upa.user_id=cetm.user_id and upa.proj_id=cetm.proj_id and cetm.spec_id=4 ".
		"WHERE upa.user_id=".$uid." GROUP BY proj_id";
	}
	//print($query);
	$xsql = new phpSQL();	
	$xsql->query($query);
	$res = $xsql->runquery();
	while($row=mysql_fetch_row($res))
	{
		/*
		0-project name
		1-project category
		2-thumbnail path
		3-lines of code
		4-components/technology used
		5-description
		6-contribution
		7-challenges
		8-contribution timeframe
		9-project id
		*/
		$projNm = str_replace(' ','_',$row[0]);
		$html.=startDiv($projNm,"project");			
			
			$html.=startDiv($projNm."_thmb","thumb");
				if($row[2]!=null)
				{
					$html.=buildLink("project.php?id=".$row[9],buildImage($row[2]));
				}			
			$html.=endDiv();			
			
			$html.=startDiv($projNm."_nm", "projname");
				$html.=startSpan($projNm."_nmhdr", "projnmhdr");
					$html.="Project: ";
				$html.=endSpan();
				$html.=buildLink("project.php?id=".$row[9],$row[0]);
			$html.=endDiv();
			
			$html.=startDiv($projNm."_cat", "category");
				$html.=startSpan($projNm."_cathdr","cathdr");
					$html.="Category: ";
				$html.=endSpan();
				$html.=$row[1];
			$html.=endDiv();
			$html.=startDiv($projNm."_loc", "loc");
				if($row[3]!=null)
				{
					$html.=startSpan($projNm."_lochdr","lochdr");
						$html.="Size: ";
					$html.=endSpan();
					$html.=$row[3];
				}
			$html.=endDiv();
			$html.=startDiv($projNm."_comp", "components");
				$html.=startSpan($projNm."_comphdr","comphdr");
					$html.="Components: ";
				$html.=endSpan();
				$html.=$row[4];
			$html.=endDiv();
			$html.=startDiv($projNm."_desc", "desc");
				$html.=startSpan($projNm."_deschdr","deschdr");
					$html.="Description: ";
				$html.=endSpan();
				$html.=$row[5];
			$html.=endDiv();
			$html.=startDiv($projNm."_cont", "contribution");
				$html.=startSpan($projNm."_contrhdr", "contrhdr");
					$html.="Contribution: ";
				$html.=endSpan();
				$html.=$row[6];
			$html.=endDiv();
			$html.=startDiv($projNm."_ctm", "timeframe");
				$html.=startSpan($projNm."_timehdr", "timehdr");
					$html.="Contribution Timeframe: "; 
				$html.=endSpan();
				$html.=$row[8];
			$html.=endDiv();
			$html.=startDiv($projNm."_chg", "challenge");
				$html.=startSpan($projNm."_chalhdr","chalhdr");
					$html.="Challenges: "; 
				$html.=endSpan();
				$html.=$row[7];
			$html.=endDiv();
			$html.=startDiv($projNm."_imgs", "images");
				$html.=getExtraProjImages($row[9]);
			$html.=endDiv();
			
			//add edit buttons
			if($type=="AUTH")
			{
				$html.=startDiv($projNm."_edit","edit");
					$html.=buildLink("project.php?a=edit&id=".$row[9],"Edit Project")." ";
					$html.=buildLink("project.php?a=delete&id=".$row[9],"Delete Project")." ";
					$html.=buildLink("contribution.php?id=".$row[9],"Edit Contribution")." ";
				$html.=endDiv();
			}
			elseif($type=="ASSOC")
			{
				$html.=startDiv($projNm."_edit","edit");					
					$html.=buildLink("contribution.php?id=".$row[9],"Edit Contribution")." ";
				$html.=endDiv();
			}
			elseif($type=="ALL")
			{
				if($_SESSION['logged_in'])
				{
					//check if the logged in user is already part of the project listed
					//*get list of proj_id's the user is part of (or in the process of being authorised) and check it against the current proj
					$apprvprojs=getUserProjIDs($_SESSION['user_id'], true);
					$authprojs=getUserProjIDs($_SESSION['user_id'], false);		
										
					if((!in_array($row[9],$apprvprojs))&&(!in_array($row[9],$authprojs)))				
					{
						$html.=startDiv($projNm."_assoc","edit");
						$html.=buildLink("project.php?a=assoc&id=".$row[9],"Add yourself to this project")." ";
						$html.=endDiv();
					}				
					if((in_array($row[9],$authprojs)))
					{
						$html.=startDiv($projNm."_assoc","edit");
						$html.="You are still waiting to be approved for this Project.";
						$html.=endDiv();
					}
				}
			}
		$html.=endDiv();
		
	}
	return $html;
}
//get contribution data for a project and user
function getContributionInfo($uid, $pid)
{
	$xsql = new phpSQL();
	$xsql->query("SELECT con.fdval as `CONTRIBUTION`, st.fdval as `START DATE`, en.fdval as `END DATE`, chal.fdval as `CHALLENGES` ".
	"FROM `user_proj_assoc` upa ".
	"LEFT JOIN user_proj_spec con on upa.user_id=con.user_id and upa.proj_id=con.proj_id and con.spec_id=1 ".
	"LEFT JOIN user_proj_spec st on upa.user_id=st.user_id and upa.proj_id=st.proj_id and st.spec_id=3 ".
	"LEFT JOIN user_proj_spec en on upa.user_id=en.user_id and upa.proj_id=en.proj_id and en.spec_id=4 ".
	"LEFT JOIN user_proj_spec chal on upa.user_id=chal.user_id and upa.proj_id=chal.proj_id and chal.spec_id=2 ".
	"WHERE upa.user_id=".$uid." and upa.proj_id=".$pid);
	$res = $xsql->runquery();
	
	if(mysql_num_rows($res)>0)
	{
		return mysql_fetch_array($res);
	}
}
//get any extra images that might have been added to a project using the project ID
function getExtraProjImages($pid)
{

}

//get the user_id of the creator of a project
function getProjCreatorID($projID)
{
	$xsql = new phpSQL();
	$xsql->query("SELECT p.create_by FROM port_proj p WHERE p.proj_id=".$projID);
	$res = $xsql->runquery();
	if(mysql_num_rows($res)>0)
	{
		$row = mysql_fetch_row($res);
		return $row[0];
	}
	return false;

}

//increments the number of views a user has gotten
function updateViews($userID)
{
	$xsql = new phpSQL();
	$xsql->query("UPDATE port_user SET views = views + 1 WHERE user_id=".$userID);
	$xsql->runquery();
}

//builds the main menu
function buildMainMenu($currentpage)
{
	//print($currentpage);
	$html=startDiv("main_menu","menu");
		$html.=buildLink("index.php","Home")." | ";
		$html.=buildLink("register.php","Register")." | ";
		$html.=buildLink("portfolio.php","Portfolios")." | ";				
		$html.=buildLink("search.php","Search")." | ";
		if($_SESSION['logged_in'])
		{
			$html.=buildLink("project.php?a=add","Add a project")." | ";			
			$html.=buildLink("edit.php","My Profile")." | ";
			$html.=buildLink("project.php?a=myproj","My Projects")." | ";			
			$html.=buildLink("logout.php","Logout")." | ";						
		}
		else
		{
			$html.=buildLink("login.php","Login")." | ";
		}
	$html.=endDiv();
	return $html;
}

//get the top ten viewed user portfolios
function buildTop10Viewed()
{
	//get the top ten viewed users
	$xsql = new phpSQL();
	$xsql->query("SELECT user_id FROM `port_user` ORDER BY views DESC LIMIT 10");
	$res = $xsql->runquery();
	
	//for each user also get what categories most of his projects are in, and what tags are used the most
	while($row = mysql_fetch_row($res))
	{
		$name = null;
		$imgpath = null;
		//get img (make small thumbnail) and name (they're in the same table)
		$xsql->query("SELECT user_f_name, user_l_name, img_path, user_name FROM port_user WHERE user_id=".$row[0]);
		$uinfo=$xsql->runquery();		
		if(mysql_num_rows($uinfo)>0)
		{	
			$info = mysql_fetch_row($uinfo);
			$name = $info[0]." ".$info[1];
			$uname = $info[3];
			if(!$info[2]==null)
			{
				$imgpath = $info[2];//big image			
				$sm_img = file_dir($info[2])."/".file_name($info[2])."_sm.".file_extension($info[2]);				
				if((!file_exists($sm_img)) || (filemtime($imgpath) > filemtime($sm_img)))
				{
					//print("<br/>big: ".filemtime($imgpath)." small: ".filemtime($sm_img));
					//print("resized: {$sm_img}");
					$resize = new ImgResizer($imgpath);
					$resize->resize(64,$sm_img);
					//echo "File Created: {$sm_img} <br />";
				}
			}
			
			
		}		
		//get categories (most used 5)
		$xsql->query("SELECT lci.cddef as `Category`, count(distinct pp.proj_id) as `Projects`".
		" FROM `user_proj_assoc` upa".
		" INNER JOIN user_proj_auth auth on upa.user_id=auth.user_id and upa.proj_id=auth.proj_id and auth.auth_state=1".
		" INNER JOIN port_proj pp on upa.proj_id=pp.proj_id".
		" INNER JOIN lkup_category_id lci on pp.cat_id=lci.cat_id".
		" WHERE upa.user_id=".$row[0].
		" GROUP BY `Category` ORDER BY `Projects` DESC LIMIT 5");
		$cats=$xsql->runquery();
		
		$categories=array();
		while($cat=mysql_fetch_row($cats))
		{
			$tmp = array($cat[0], $cat[1]);
			$categories[]=$tmp;
		}		
		//get tags (most used 5)
		$xsql->query("SELECT ppt.tag as `Tag`, count(distinct ppt.proj_id) as `Projects`".
		" FROM `user_proj_assoc` upa".
		" INNER JOIN user_proj_auth auth on upa.user_id=auth.user_id and upa.proj_id=auth.proj_id and auth.auth_state=1".
		" INNER JOIN port_proj_tags ppt on upa.proj_id=ppt.proj_id".
		" WHERE upa.user_id=".$row[0].
		" GROUP BY ppt.tag ORDER BY `Projects` DESC LIMIT 5");
		$tagz=$xsql->runquery();
		//print($xsql->get_query());
		$tags=array();
		while($tag=mysql_fetch_row($tagz))
		{
			$tmp = array($tag[0],$tag[1]);
			$tags[]=$tmp;
		}
		$text.=startDiv("user_wrapper","user_wrapper");
			if(!$imgpath==null)
			{
				$text.=startSpan("userimg","userimg");
					$text.=buildLink("portfolio.php?u=".$uname,buildImage($sm_img));
				$text.=endSpan();
			}
			if(!$name==null)
			{
				$text.=startSpan("username","username");
					$text.=buildLink("portfolio.php?u=".$uname,$name);
				$text.=endSpan();
			}			
			if(sizeof($categories)>0)
			{
				$text.=startSpan("proj_cats","cats");
				$text.="<b>Project Categories:</b> ";
				for($i=0;$i<sizeof($categories);$i++)
				{
					$tmp = $categories[$i];
					$text.=$tmp[0];
					if(!($i+1==sizeof($categories)))
					{
						$text.=", ";
					}	
				}
				$text.=endSpan();
			}
			if(sizeof($tags)>0)
			{
				$text.=startSpan("proj_tags","tags");
				$text.="<b>Project Tags:</b> ";
				for($i=0;$i<sizeof($tags);$i++)
				{
					$tmp = $tags[$i];
					$text.=$tmp[0];
					if(!($i+1==sizeof($tags)))
					{
						$text.=", ";
					}	
				}
				$text.=endSpan();
			}/**/
		$text.=endDiv();
	}	
	
	return $text;
}

function file_dir($filename)
{
	$path_info = pathinfo($filename);
	//print_r($path_info);
    return $path_info['dirname'];
}

function file_name($filename)
{
	$path_info = pathinfo($filename);
	//print_r($path_info);
    return $path_info['filename'];
}

function file_extension($filename)
{
    $path_info = pathinfo($filename);
	//print_r($path_info);
    return $path_info['extension'];
}

function createProjFolder($path)
{
	//print($path."<br/>");
	$dirs = split("/",$path);
	//print(sizeof($dirs)."<br/>");
	$mkpth="";
	for($i=0;$i<sizeof($dirs);$i++)
	{
		$mkpth.=$dirs[$i]."/";
		if(!file_exists($mkpth))
		{
			if(!mkdir($mkpth)) //try to create the directory
			{
				return false; //something broke!
			}
			//print("Dir created: {$mkpth} <br/>");
		}
		else
		{
			//print("Dir exists: {$mkpth} <br/>");
		}
	}
	return $path;
}

//check to make sure the username supplied isn't used already
function userCheck($user)
{
	$xsql = new phpSQL();
	$xsql->query("SELECT user_id FROM port_user WHERE user_name like '".addslashes($user)."'");
	$res=$xsql->runquery();
	if(mysql_num_rows($res)>0)
	{
		return true;
	}
	return false;
}
//check to make sure a project with the name supplied doesn't exist already
function checkProjectNm($projnm)
{
	$xsql = new phpSQL();
	$xsql->query("SELECT proj_id FROM port_proj WHERE proj_name like '".addslashes($projnm)."'");
	$res=$xsql->runquery();
	if(mysql_num_rows($res)>0)
	{
		$row=mysql_fetch_row($res);
		return array(true,$row[0]);
	}
	return array(false,null);
}
//check to make sure a project with the given projID exists
function checkProjectID($pid)
{
	$xsql = new phpSQL();
	$xsql->query("SELECT proj_id FROM port_proj WHERE proj_id=".$pid);
	$res=$xsql->runquery();
	if(mysql_num_rows($res)>0)
	{
		$row=mysql_fetch_row($res);
		return array(true,$row[0]);
	}
	return array(false,null);
}	
function getNewAuth($newAuth)
{	
		$content.="There are users waiting to be added to projects you authored. They are listed below:";
		//get those users that need to be authed and for what projects
		$auths=$newAuth[1];
		
		foreach($auths as $tmparr)
		{
			$userID=$tmparr[0];
			$projID=$tmparr[1];
			
			$user=getUserData($userID);
			$proj=getProjectData($projID);
			
			$name = $user[0]." ".$user[1];					
			if(!$user[3]==null)
			{
				$imgpath = $user[3];//big image			
				$sm_img = file_dir($user[3])."/".file_name($user[3])."_sm.".file_extension($user[3]);				
				if((!file_exists($sm_img)) || (filemtime($imgpath) > filemtime($sm_img)))
				{
					//print("<br/>big: ".filemtime($imgpath)." small: ".filemtime($sm_img));
					//print("resized: {$sm_img}");
					$resize = new ImgResizer($imgpath);
					$resize->resize(64,$sm_img);
					//echo "File Created: {$sm_img} <br />";
				}
			}
			
			$content.=startDiv("user_wrapper","user_wrapper");
				if(!$imgpath==null)
				{
					$content.=startSpan("userimg","userimg");
						$content.=buildLink("portfolio.php?u=".$uname,buildImage($sm_img));
					$content.=endSpan();
				}
				if(!$name==null)
				{
					$content.=startSpan("username","username");
						$content.=buildLink("portfolio.php?u=".$uname,$name);
					$content.=endSpan();
				}
				//add the project they requested
				$projNm = str_replace(' ','_',$proj[0]);
				$content.=startDiv($projNm,"project");
						$content.=startDiv($projNm."_nm", "projname");
							$content.=startSpan($projNm."_nmhdr", "projnmhdr");
								$content.="Project: ";
							$content.=endSpan();
							$content.=$proj[0];
						$content.=endDiv();
						//add select with allow/deny											
				$content.=endDiv();
			$content.=endDiv();			
		}
		return $content;
}
//check for new authorizations for any projects authored by the newly logged in user
function checkNewAuth($uid)
{
	$xsql = new phpSQL();
	$xsql->query("SELECT auth.user_id, auth.proj_id ".
	"FROM port_proj p ".
	"INNER JOIN `user_proj_assoc` assoc on p.proj_id=assoc.proj_id ".
	"INNER JOIN user_proj_auth auth on assoc.proj_id=auth.proj_id and auth.auth_state=0 ".
	"WHERE p.create_by={$uid} ".
	"GROUP BY auth.proj_id");
	$res = $xsql->runquery();
	while($row=mysql_fetch_row($res))
	{		
		$auths[] = array($row[0], $row[1]);
		$found = true;
	}
	if($found)
	{
		return array(true, $auths);
	}
	return array(false,null);
}

function checkAssoc($uid, $pid)
{
	$xsql = new phpSQL();
	$xsql->query("SELECT user_id FROM user_proj_assoc WHERE user_id=".$uid." and proj_id=".$pid);
	$res = $xsql->runquery();
	if(mysql_num_rows($res)>0)
	{
		return true;
	}
	return false;
}

function userProjAutoAssoc($uid,$pid)
{
	$xsql = new phpSQL();
	$xsql->query("INSERT IGNORE INTO user_proj_auth VALUES ({$uid},{$pid}, unix_timestamp(), 1)");
	$xsql->runquery();
	$xsql->query("INSERT IGNORE INTO user_proj_assoc VALUES ({$uid},{$pid})");
	$xsql->runquery();
}
//make sure the email address provided is a valid format
function emailCheck($addr)
{
	//using regex check to make sure the email address is in a valid format
	if (eregi("^[a-zA-Z0-9_]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$]", $email)) 
	{
	   return false;
	}
	return true;
	/* --for later testing
	//split the email address into the user and the domain
	list($username, $domain) = split("@",$email);
	
	//checks if an MX record exists for the domain provided, if that fails, check to see if port 25 is open
	if(getmxrr($domain, $MXHost)) 
	{
		return true;
	}
	else 
	{		
		if(fsockopen($Domain, 25, $errno, $errstr, 30)) 
		{
			return true; 
		}
		else 
		{
			return false; 
		}
	}
	*/

}
//check a date
function checkaDate($date)//taken from http://roshanbh.com.np/2008/05/date-format-validation-php.html
{
  //match the format of the date
  if (preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts))
  {
    //check weather the date is valid of not
	if(checkdate($parts[2],$parts[3],$parts[1]))
	  return true;
	else
	 return false;
  }
  else
    return false;
}

//check to see if the session has the user logged in or not
function checkLogin() 
{
	if($_SESSION['loggedIn']) 
	{
		return true;
	}
	else 
	{
		return false;
	}
}

function checkCreds($userID) 
{
	//checks to see if the userID stored in the session
	//is the same as the one passed in the address bar
	if($_SESSION['userID']==$userID) 
	{
		return true;
	}
	return false;
}
//taken from http://www.kavoir.com/2009/01/php-resize-image-and-store-to-file.html
class ImgResizer {
	private $originalFile = '';
	public function __construct($originalFile = '') {
		$this -> originalFile = $originalFile;
	}
	public function resize($newWidth, $targetFile) {
		if (empty($newWidth) || empty($targetFile)) {
			return false;
		}
		$src = imagecreatefromjpeg($this -> originalFile);
		list($width, $height) = getimagesize($this -> originalFile);
		$newHeight = ($height / $width) * $newWidth;
		$tmp = imagecreatetruecolor($newWidth, $newHeight);
		imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
		if (file_exists($targetFile)) {
			unlink($targetFile);
		}
		imagejpeg($tmp, $targetFile, 85); // 85 is my choice, make it between 0 – 100 for output image quality with 100 being the most luxurious
	}
}
?>