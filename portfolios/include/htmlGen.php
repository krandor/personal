<?php
/*****************
/*HTML Generation*
/*****************/
function startPage($css,$title="",$csspath="include/") {
	//return "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\"><html><head><title>".$title."</title>".setCSS($css,$csspath)."</head><body>\n";
	return "<html><head>\n<title>".$title."</title>\n".setCSS($css,$csspath)."</head>\n<body class='body_content'>\n";
}

function setCSS($filename, $path="include/") {
	return "<link rel='stylesheet' href='".$path.$filename.".css' type='text/css' />\n";	
}

function endPage() {
	return "</body></html>\n";
}

function startDiv($name="",$class="") {
	$html = "<div";
	if($name!="") {
		$html.=" id='".$name."' name='".$name."'";
	}
	if($class!="") {
		$html.=" class='".$class."'";
	}
	$html.=">\n";
	return $html;	
}

function endDiv() {
	return "</div>\n";
}

function startSpan($name="",$class=""){
	$html="<span";
	if($name!="") {
		$html.=" id='".$name."' name='".$name."'";
	}
	if($class!="") {
		$html.=" class='".$class."'";
	}
	$html.=">\n";
	return $html;
}

function endSpan() {
	return "</span>\n";
}

function buildLink($addr,$name,$target="_parent") {
	return "<a href=\"".$addr."\" target=\"".$target."\">".$name."</a>";
}

function startForm($type="GET",$action="",$enctype="") {
	$html="<form method='".$type."' action='".$action."'";
	if($enctype!="")
	{
		$html.=" enctype='".$enctype."'";
	}
	$html.=">\n";
	return $html;
}

function endForm($btnValue="Next Step") {
 return "<input class=\"tbox\" type=\"submit\" name=\"submit\" value='".$btnValue."'/></form>\n";
}

function startTable($border=false,$headers=null) {
	if($border) {
		$html="<table border=1>";
	} else {
		$html="<table>";	
	}
	if($headers!=null) {
		for($i=0;$i<count($headers);$i++){
			if($i%2==0) {
				$clr="gry";
			} else {
				$clr="wht";
			}
			$html.="<th class='header_".$clr."'>".$headers[$i]."</th>";
		}
	}
	return $html;
}

function buildTable($arr, $style=false) {
	
	for($i=0;$i<count($arr);$i++) {
		$tmp = $arr[$i];
		$html="<tr>";
		$y=0;
		for($x=0;$x<count($tmp);$x++) {
			if($style) {
				if($y++%2==0) {
					$html.="<td class='column_gry'>".$tmp[$x]."</td>";
				} else {
					$html.="<td class='column_wht'>".$tmp[$x]."</td>";
				}		
			} else {
				$html.="<td>".$tmp[$x]."</td>";
			}
		}
		$html.="</tr>\n";
	}
	return $html;
}

function endTable() {
	return "</table>";
}

function startAlign($alignment) {
	return "<".$alignment.">";
}

function endAlign($alignment) {
	return "</".$alignment.">";
}

function buildRadioBtn($name,$text,$value,$checked=false) {
	$html=$text." <input type=\"radio\" name=\"".$name."\" value=\"".$value."\"";
	if($checked) {
		$html.=" checked";
	}
	$html.="/>\n";
	return $html;
}

function buildSelect($arr, $name, $key=NULL) {//build HTML SELECT ELEMENT
	$html = "<SELECT class=\"tbox\" NAME='".$name."'>\n";
	//$html.="<OPTION VALUE=\"\">Please Choose..</OPTION>\n";
	for($x=0; $x< count($arr); $x++) {
		$tmp = $arr[$x];
		if($tmp[0]==$key) {
			$html.="<OPTION VALUE=".$tmp[0]." SELECTED=1>".$tmp[1]."</OPTION>\n"; //<option value="volvo" SELECTED=1>Volvo</option>
		} else {
			$html.="<OPTION VALUE=".$tmp[0].">".$tmp[1]."</OPTION>\n"; //<option value="volvo">Volvo</option>
		}
	}
	$html.="</SELECT>\n";
	return $html;
}

function buildCheckbox($name,$value,$text) {
	return $text."<input class=\"tbox\" type=\"checkbox\" value=\"".$value."\" name=\"".$name."\"/>\n";
}

function buildTextbox($name, $text="",$class="tbox") {
	return "<input type='textbox' class=\"{$class}\" name='".$name."' value='".$text."'/>\n";
}

function buildFilebox($name, $text="") {
	return "<input type='file' class=\"tbox\" name='".$name."' value='".$text."'/>\n";
}

function buildPasswordbox($name, $text="") {
	return "<input type='password' class=\"tbox\" name='".$name."' value='".$text."'/>\n";
}

function buildTextarea($name, $text="", $cols="50", $rows="1") {
	
	return "<textarea class=\"tbox\" cols=".$cols." rows=".$rows." name=\"".$name."\">".$text."</textarea>\n";

}

function buildImage($addr) {
	$html="<img border=0 src=\"".$addr."\"";
	/*if(!$height=="")
	{
	
	}
	if(!$width=="")
	{
		
	}*/
	$html.="/>";
	return  $html;
}

?>