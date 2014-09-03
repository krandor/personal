var xmlHttp;
var RSSRequestObject;

function showRSS(str) { 
	//alert(str);
	if(str!="Please Select A Feed") {
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null) {
			alert ("Browser does not support HTTP Request");
			return;
		}
	
		var url="include/getrss.php";
		url=url+"?q="+str;
		url=url+"&sid="+Math.random();
	
		xmlHttp.onreadystatechange=stateChanged 
		xmlHttp.open("GET",url,true);
		xmlHttp.send(null);
	}
}

function stateChanged() {
	//alert(xmlHttp.readyState);
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{
		//alert(xmlHttp.responseText);
		//RSSRequest(xmlHttp.responseText);
		document.getElementById("rssOutput").innerHTML=xmlHttp.responseText 

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

function loaded() 
{
	//showRSS("1");	
}
