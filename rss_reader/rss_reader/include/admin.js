function loaded() {
	showFeed();
}


function showFeed() {
	 
	var ajax_feed_get=GetAJAX();
	if(ajax_feed_get==null) {
		alert("Your browser is broken.");
		return;
	}
	var url = "include/getdata.php?w=g";
	ajax_feed_get.onreadystatechange=function() { 
		if(ajax_feed_get.readyState==4) {
			document.getElementById("sp_rss_reader").innerHTML=ajax_feed_get.responseText;
		}
	}
		
	ajax_feed_get.open("GET",url,true);
	ajax_feed_get.send(null); 
}

function delFeed(fid) {
		
	if(confirm("Do you want to delete this feed?")) {
		var ajax_feed_del=GetAJAX();
		if(ajax_feed_del==null) {
			alert("Your browser is broken.");
			return;
		}
	
		ajax_feed_del.onreadystatechange=function() { 
			if(ajax_feed_del.readyState==4) {
				document.getElementById("sp_rss_reader").innerHTML=ajax_feed_del.responseText;
				document.getElementById("page_updates").innerHTML="<strong>Feed Deleted.</strong>";
				showFeed();
			}
		}
	
		var url = "include/savedata.php";
		var d_parm = "whr="+fid+"&q=d&v="+fid;
	
		ajax_feed_del.open("POST",url, true);
		ajax_feed_del.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		ajax_feed_del.setRequestHeader("Content-length", d_parm.length);
		ajax_feed_del.setRequestHeader("Connection", "close");
		ajax_feed_del.send(d_parm);
	} else {
		showFeed();
	}
}
function feedEdit(fid, eid) {
	//if the record is going into edit mode.
	
	if((document.getElementById(eid).childNodes.length > 0)){
		 	
			var nodeCnt = document.getElementById(eid).childNodes.length;
			var elRow = document.getElementById(eid);
			id=eid.substring(eid.indexOf("_") + 1, eid.length);
			//fid="id_" + id;
			//make all the other fields text boxes
			for(i=0;i<nodeCnt; i++) {
				var val = elRow.childNodes[i].innerHTML;
				elRow.childNodes[i].innerHTML = "<input type='text' class='tbox' id='edt" + elRow.childNodes[i].id + "' value='" + val + "' />";
			}
			//set those nodes that need comboboxes (state list and address type)
			var st_id = "act_" + eid.substring(eid.indexOf("_") + 1, eid.length);
			//alert(st_id + " " + eid);
			var active_html = "<select class='select' id='sel_"+eid+"'><option value='1' selected='selected'>Yes</option><option value='0'>No</option></select>";
			
			document.getElementById(st_id).innerHTML = active_html;

			//remove the onclick event
			document.getElementById(eid).onclick=null;			
			//add an OK and Cancel button to the end of the row
			var cell = document.createElement('td');
			cell.setAttribute('id','clConfirm');
			cell.innerHTML = "<input type='button' class='button' value='OK' onClick='feedEditSave(\""+fid+"\", \""+eid+"\");' />" +
			"<input type='button' class='button' value='Cancel' onClick='showFeed();'/><input type='button' class='delbutton' value='DELETE' onClick='delFeed(\""+fid+"\");' />";
			//alert(cell.innerHTML);
			elRow.appendChild(cell);
		}
}

function feedEditSave(fid, eid){
	
		if(document.getElementById(eid).childNodes.length > 0) {
			//lets save some shit!
			var nodeCnt = document.getElementById(eid).childNodes.length;
			var info = [];
			var vals = "";
			
			for(i=0; i < nodeCnt -1; i++) {
					info[i] = document.getElementById(eid).childNodes[i].childNodes[0].value;
			}
			//alert(info);
			var ajax_feed_save=GetAJAX();
			if(ajax_feed_save==null) {
				alert("Your Browser Broke..");
				return;
			}
			vals = "rss_feed_id='"+info[0]+"', rss_feed_nm='"+info[1].replace("'","''")+"', rss_feed_addr='"+escape(info[2])+"', rss_feed_active='"+info[3]+"'";
			//vals = addslashes(vals);
			var url = "include/savedata.php";
			var a_parm = "whr="+info[0]+"&q=u&v="+vals;
			//alert(vals);
			ajax_feed_save.onreadystatechange=function() { 
				if(ajax_feed_save.readyState==4){ 
					if(ajax_feed_save.responseText!="") {
						alert(ajax_feed_save.responseText);
					} 
					showFeed(); 
				}
			}
			ajax_feed_save.open("POST",url, true);
			ajax_feed_save.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			ajax_feed_save.setRequestHeader("Content-length", a_parm.length);
			ajax_feed_save.setRequestHeader("Connection", "close");
			ajax_feed_save.send(a_parm);
		}
		showFeed();
}

function GetAJAX()
{
	var xmlHttp=null;
	try
	{
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}

function addslashes(str) {
	str=str.replace(/\'/g,'\\\'');
	str=str.replace(/\"/g,'\\"');
	str=str.replace(/\\/g,'\\\\');
	str=str.replace(/\0/g,'\\0');
	return str;
}
function stripslashes(str) {
	str=str.replace(/\\'/g,'\'');
	str=str.replace(/\\"/g,'"');
	str=str.replace(/\\\\/g,'\\');
	str=str.replace(/\\0/g,'\0');
	return str;
}

window.load = loaded();