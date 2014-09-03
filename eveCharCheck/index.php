<?php
/*
Name: EVE ONLINE Character Check
Version: 0.01
Date: 2010-02-27
Developer: Krandor delMarniol
Purpose: The purpose of this app is to allow corporation leadership to utilize an extra tool in the fight against bullshitters and spies.
Technology Used: PHP, mySQL, ALE PHP Eve API
Inputs: EVE Online User ID
	  EVE Online Limited API Key	  
Outputs: Character Information for all characters associated with the inputted User ID
*/
require_once 'include/classes.php';
//print_r($_POST);
$html=""; //html var to hold all the html that wil be displayed
$text="";
$error=null;
$submitted=false;
$javascript="";
if(isset($_POST['submit']))
{
	$submitted=true;
	$userID=$_POST['userID'];
	$APIKey=$_POST['APIKey'];
	
	if($userID!="" && $APIKey!="")
	{
		//get ALE object
		try {
				$ale = AleFactory::getEVEOnline();
				//set user credentials, third parameter $characterID is also possible;
				
				$ale->setCredentials($userID, $APIKey);
				//all errors are handled by exceptions
				//let's fetch characters first.
		
				$account = $ale->account->Characters();
				//you can traverse rowset element with attribute name="characters" as array
		
				foreach ($account->result->characters as $character) 
				{				
					//this is how you can get attributes of element
				
					$characterID = (string) $character->characterID;		
					//print_r($character);		
					//set characterID for CharacterSheet
				
					$ale->setCharacterID($characterID);
			
					$characterSheet = $ale->char->CharacterSheet();			
					//pick out all the stuff i want from the character sheet
			
					$charName = (string) $characterSheet->result->name;
					$charRace = (string) $characterSheet->result->race;
					$corporationName = (string) $characterSheet->result->corporationName;
					$walletBalance = (string) $characterSheet->result->balance;
					$implants = $characterSheet->result->attributeEnhancers;
					$attributes = $characterSheet->result->attributes;
				
		
					$skills = $characterSheet->result->__get("skills");
					$certs = $characterSheet->result->__get("certificates");
					$roles = $characterSheet->result->__get("corporationRoles");
					$rolesAtHQ = $characterSheet->result->__get("corporationRolesAtHQ");
					$rolesAtBase = $characterSheet->result->__get("corporationRolesAtBase");
					$rolesAtOther = $characterSheet->result->__get("corporationRolesAtOther");
					$titles = $characterSheet->result->__get("corporationTitles");
					
					//now start parsing and displaying the info
					//basic info
					$text.=startDiv("char_wrapper", "char_wrapper");
						$text.=startTable();
							$text.=startTableRow();
								$text.=addTableCell("<b>Name:</b> ").addTableCell($charName);
							$text.=endTableRow();
							$text.=startTableRow();
								$text.=addTableCell("<b>Race:</b> ").addTableCell($charRace);
							$text.=endTableRow();
							$text.=startTableRow();
								$text.=addTableCell("<b>Corporation:</b> ").addTableCell($corporationName);
							$text.=endTableRow();
							$text.=startTableRow();
								$text.=addTableCell("<b>Wallet Balance:</b> ").addTableCell($walletBalance);
							$text.=endTableRow();
						$text.=endTable();
						//implants
						if($implants->children())
						{
							$javascript.="animatedcollapse.addDiv('implants".$characterID."', 'fade=1')\n";
							$text.=buildLink("javascript:animatedcollapse.toggle('implants".$characterID."')","<u><b>Implants</b></u><br/>");
							$text.=startDiv("implants".$characterID,"collapsible");
								$text.=startTable(true,array("Implant Name", "Boost Amount"));
									foreach($implants->children() as $implant)
									{
										$implantName = (string) $implant->augmentatorName;
										$implantValue = (string) $implant->augmentatorValue;
										$text.=startTableRow();
											$text.=addTableCell($implantName);
											$text.=addTableCell($implantValue);
										$text.=endTableRow();
									}
								$text.=endTable();
							$text.=endDiv();
						}
						
						//attributes
						if($attributes->children())
						{
							$javascript.="animatedcollapse.addDiv('attributes".$characterID."', 'fade=1')\n";
							$text.=buildLink("javascript:animatedcollapse.toggle('attributes".$characterID."')","<u><b>Attributes</b></u><br/>");
							$text.=startDiv("attributes".$characterID,"collapsible");
								$text.=startTable(true,array("Attribute", "Amount"));
								foreach($attributes->children() as $attribute)
								{
									$attributeName = $attribute->getName();
									$attributeAmount = $attribute;
									$text.=startTableRow();
											$text.=addTableCell($attributeName);
											$text.=addTableCell($attributeAmount);
									$text.=endTableRow();							
								}
								$text.=endTable();
							$text.=endDiv();
						}
						
						//skills
						$totalSkillPoints = 0;			

						if($skills->children())
						{
							$javascript.="animatedcollapse.addDiv('skills".$characterID."', 'fade=1')\n";
							$text.=buildLink("javascript:animatedcollapse.toggle('skills".$characterID."')","<u><b>Skills</b></u><br/>");
							$text.=startDiv("skills".$characterID,"collapsible");
								$text.=startTable(true,array("Skill","Level"));
									foreach($skills->children() as $row)
									{
										$attrib = $row->attributes();
										$typeID = $attrib->typeID;
										$skillpoints = $attrib->skillpoints;
										$skilllevel = $attrib->level;
										$unpublished = $attrib->unpublished;
										$text.=startTableRow();
												$text.=addTableCell($typeID);
												$text.=addTableCell($skilllevel);
										$text.=endTableRow();						
										//print("Skill: ".$typeID." at Level ".$skilllevel."<br/>");
										$totalSkillPoints += $skillpoints;
										//print_r($attrib);			
									}
								$text.=endTable();								
							$text.=endDiv();
							$text.="<b><u>Total Skillpoints:</u></b> ".$totalSkillPoints."<br />";
						}

						if($certs->children())
						{
							$javascript.="animatedcollapse.addDiv('certs".$characterID."', 'fade=1')\n";
							$text.=buildLink("javascript:animatedcollapse.toggle('certs".$characterID."')","<u><b>Certificates</b></u><br/>");
							$text.=startDiv("certs".$characterID,"collapsible");
								$text.=startTable(true,array("Cert Category", "Cert Class","Description"));
									foreach($certs->children() as $row)
									{
										$attrib = $row->attributes();
										$certID = $attrib->certificateID;
										
										$certData = getCert($certID);
										$text.=startTableRow();
											//$text.=addTableCell($certID);
											$text.=addTableCell($certData[0]);
											$text.=addTableCell($certData[1]);
											$text.=addTableCell($certData[2]);										
										$text.=endTableRow();																
									}
								$text.=endTable();								
							$text.=endDiv();
						}
						
						if($roles->children())
						{
							$javascript.="animatedcollapse.addDiv('roles".$characterID."', 'fade=1')\n";
							$text.=buildLink("javascript:animatedcollapse.toggle('roles".$characterID."')","<u><b>General Roles</b></u><br/>");
							$text.=startDiv("roles".$characterID,"collapsible");
								$text.=startTable(true,array("Role ID","Name"));
									foreach($roles->children() as $row)
									{
										$attrib = $row->attributes();
										$roleID = $attrib->roleID;
										$roleName = $attrib->roleName;
										
										$text.=startTableRow();
												$text.=addTableCell($roleID);
												$text.=addTableCell($roleName);
										$text.=endTableRow();																
									}
								$text.=endTable();								
							$text.=endDiv();
						}
						
						if($rolesAtHQ->children())
						{
							$javascript.="animatedcollapse.addDiv('rolesAtHQ".$characterID."', 'fade=1')\n";
							$text.=buildLink("javascript:animatedcollapse.toggle('rolesAtHQ".$characterID."')","<u><b>Roles at HQ</b></u><br/>");
							$text.=startDiv("rolesAtHQ".$characterID,"collapsible");
								$text.=startTable(true,array("Role ID","Name"));
									foreach($rolesAtHQ->children() as $row)
									{
										$attrib = $row->attributes();
										$roleID = $attrib->roleID;
										$roleName = $attrib->roleName;
										
										$text.=startTableRow();
												$text.=addTableCell($roleID);
												$text.=addTableCell($roleName);
										$text.=endTableRow();																
									}
								$text.=endTable();								
							$text.=endDiv();
						}
						
						if($rolesAtBase->children())
						{
							$javascript.="animatedcollapse.addDiv('rolesAtBase".$characterID."', 'fade=1')\n";
							$text.=buildLink("javascript:animatedcollapse.toggle('rolesAtBase".$characterID."')","<u><b>Roles At Base</b></u><br/>");
							$text.=startDiv("rolesAtBase".$characterID,"collapsible");
								$text.=startTable(true,array("Role ID","Name"));
									foreach($rolesAtBase->children() as $row)
									{
										$attrib = $row->attributes();
										$roleID = $attrib->roleID;
										$roleName = $attrib->roleName;
										
										$text.=startTableRow();
												$text.=addTableCell($roleID);
												$text.=addTableCell($roleName);
										$text.=endTableRow();																
									}
								$text.=endTable();								
							$text.=endDiv();
						}
						
						if($rolesAtOther->children())
						{
							$javascript.="animatedcollapse.addDiv('rolesAtOther".$characterID."', 'fade=1')\n";
							$text.=buildLink("javascript:animatedcollapse.toggle('rolesAtOther".$characterID."')","<u><b>Roles At Other</b></u><br/>");
							$text.=startDiv("rolesAtOther".$characterID,"collapsible");
								$text.=startTable(true,array("Role ID","Name"));
									foreach($rolesAtOther->children() as $row)
									{
										$attrib = $row->attributes();
										$roleID = $attrib->roleID;
										$roleName = $attrib->roleName;
										
										$text.=startTableRow();
												$text.=addTableCell($roleID);
												$text.=addTableCell($roleName);
										$text.=endTableRow();																
									}
								$text.=endTable();								
							$text.=endDiv();
						}
						
						if($titles->children())
						{
							$javascript.="animatedcollapse.addDiv('titles".$characterID."', 'fade=1')\n";
							$text.=buildLink("javascript:animatedcollapse.toggle('titles".$characterID."')","<u><b>Titles</b></u><br/>");
							$text.=startDiv("titles".$characterID,"collapsible");
								$text.=startTable(true,array("Title ID","Name"));
									foreach($titles->children() as $row)
									{
										$attrib = $row->attributes();
										$titleID = $attrib->titleID;
										$titleName = $attrib->titleName;
										
										$text.=startTableRow();
												$text.=addTableCell($titleID);
												$text.=addTableCell($titleName);
										$text.=endTableRow();																
									}
								$text.=endTable();								
							$text.=endDiv();
						}
						
					$text.=endDiv();
					//FOR DEBUGGING
					//print("<br />");
					//print_r($skills);
					//print("<br />");
					//print("<br />");	
					//print_r($roles);
					//print_r($characterSheet);
					//print("<br />");
				}
		}
		//and finally, we should handle exceptions
		catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	else
	{
		$error = "You must specify a User ID and API Key";
	}
}
$html.=startPage("style","Eve API Check","include/css/","<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js\">\n".
"</script><script type=\"text/javascript\" src=\"include/animatedcollapse.js\"></script>\n".
"<script type=\"text/javascript\">\n".
$javascript."\n".
"animatedcollapse.init()\n".
"</script>");
	$html.=startDiv("header","header");
	$html.=endDiv();
		$html.=startDiv("content_wrapper", "wrapper");
			$html.=startDiv("content","content");
				if($submitted)
				{
					//form has been submitted, display character info or error info
					if($error!=null)
					{
						$html.=$error;
					}
					else
					{
						$html.=$text;
					}
				}
				else
				{				
					//form has not been submitted, display form to get API Key info
					$html.="Please Enter your Limited API Key information. If you don't already have your key, you can go ".buildLink("http://www.eveonline.com/api/Default.asp","here",$target="_blank")." to obtain it";
					$html.="<br />";
					$html.=startForm("POST",$PHP_SELF);
						$html.=startTable();
							//API User ID
							$html.=startTableRow();
								//cell for lable
								$html.=addTableCell("User ID:");
								//cell for user id
								$html.=addTableCell(buildTextbox("userID"));
							$html.=endTableRow();
							//Limited API Key
							$html.=startTableRow();
								//cell for lable
								$html.=addTableCell("<u>LIMITED</u> API Key: ");
								//cell for key
								$html.=addTableCell(buildTextbox("APIKey"));
							$html.=endTableRow();
						$html.=endTable();
					$html.=endForm("Submit");
				}
			$html.=endDiv();	
		$html.=endDiv();
$html.=endPage();

print($html);
?>