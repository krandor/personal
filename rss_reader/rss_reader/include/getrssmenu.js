var xmlHttp;
var xmlMenuHttp;
var str = "NO";

function showRSSMenu(path) { 
	
		xmlMenuHttp=GetXmlHttpObject();
		if (xmlMenuHttp==null) {
			alert ("Browser does not support HTTP Request");
			return;
		}
		//"getmenurss.php";
		var url= path + "rss_reader/include/getmenurss.php";
		url=url+"?sid="+Math.round(Math.random()*10);
	//alert(url);
		xmlMenuHttp.onreadystatechange=stateChangedMenu 
		xmlMenuHttp.open("GET",url,true);
		xmlMenuHttp.send(null);
	
}

function stateChangedMenu() { 
	if (xmlMenuHttp.readyState==4 || xmlMenuHttp.readyState=="complete")
	{
		//alert(xmlMenuHttp.responseText);
		//RSSRequest(xmlHttp.responseText);
		document.getElementById("rssOutputMenu").innerHTML=xmlMenuHttp.responseText 

	}

}

function GetXmlHttpObject()
{
	var xmlObject=null;
	try
	{
		// Firefox, Opera 8.0+, Safari
		xmlObject=new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			xmlObject=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			xmlObject=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlObject;
}

function loaded(path) 
{
	//alert(str);
	showRSSMenu(path);	
}
//window.load=loaded(path);