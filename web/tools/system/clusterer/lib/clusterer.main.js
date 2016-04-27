<script language="JavaScript">
//
//
  
function confirmDelete(id)
{
 var agree=confirm("Are you sure you want to delete the node?");
 if (agree)	return true;
  else return false;
}

function validateFormCLAdd(id,cid_name,sid_name,url_name) {
	var cid = document.forms[id][cid_name].value;
	var sid = document.forms[id][sid_name].value;
	var url = document.forms[id][url_name].value;

	if (cid == null || cid == "") {
		alert("Cluster ID must be filled in");
		return false;
	}
	if ( ! /^[0-9]+$/.test(cid)) {
		alert("Cluster ID must be a number");
		return false;
	}

	if (sid == null || sid == "") {
		alert("Server ID must be filled in");
		return false;
	}
	if ( ! /^[0-9]+$/.test(sid)) {
		alert("Server ID must be a number");
		return false;
	}

	if (url == null || url == "") {
		alert("Server URL must be filled in");
		return false;
	}
	if ( ! /^bin:[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}:[0-9]+$/.test(url)) {
		alert("Server URL must be in 'bin:IP:PORT' format");
		return false;
	}

	return true;
}

function handleHttpResponse(http) {   
		
    if (http.readyState == 4) {
        if(http.status==200) {
                  ok = true;
				  //return results;
        }
    }
		
}
       
 

function getHTTPObject() {
  	
  var request = false;
   try {
     request = new XMLHttpRequest();
   } catch (trymicrosoft) {
     try {
       request = new ActiveXObject("Msxml2.XMLHTTP");
     } catch (othermicrosoft) {
       try {
         request = new ActiveXObject("Microsoft.XMLHTTP");
       } catch (failed) {
         request = false;
       }  
     }
   }

   if (!request)
     alert("Error initializing XMLHttpRequest!");

  
  return request;

 
}


function centerMe(element) {
//pass element name to be centered on screen
	var pWidth = window.innerWidth;
	var pTop =  window.scrollTop;
	var eWidth = document.getElementById(element).style.width
	var height = document.getElementById(element).style.height
	document.getElementById(element).style.top = '250px';
	//$(element).css('top',pTop+100+'px')
	document.getElementById(element).style.left = parseInt((pWidth / 2) - 205) + 'px';
}



function closeDialog() {
	document.getElementById('overlay').style.display = 'none';
	document.getElementById('dialog').style.display = 'none';
	document.getElementById('dialog').innerHTML = '';
}

function apply_changes(){
		url = "apply_changes.php";
		
		var http = getHTTPObject();
		
		http.open("GET", url, false);
		http.onreadystatechange = handleHttpResponse(http);
		http.send(null);
		result = http.responseText;
		
		var body = document.body,
    	html = document.documentElement;

		var height = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );


		document.getElementById('overlay').style.height = height;
		document.getElementById('overlay').style.display = 'block';
		document.getElementById('dialog').innerHTML = result;
		centerMe('dialog')
		document.getElementById('overlay').onclick = function () {closeDialog();};
		document.getElementById('dialog').style.display = 'block';
		return true;
		

		document.getElementById("content").innerHTML = "whatever";
		
		
	return true;
}
</script>
