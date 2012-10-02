function ajax(url, callback, arg1, arg2, arg3)
{

        var request = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
 
        request.open("GET", "<?php echo $GLOBALS['basemodule_url'] ?>" + url, true);

 
        request.onreadystatechange = function()
	{
		if(request.readyState == 4 && (request.status == 200 || request.status == 302 || request.status == 301))
		{
			if(request.responseText.indexOf("AJAXOK") != -1)
			{
				callback(arg1, arg2, arg3);
			}
		}
        }
        request.send(null);
}

function ajaxdata(url, callback, arg1, arg2, arg3)
{

        var request = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
 
        request.open("GET", "<?php echo $GLOBALS['basemodule_url'] ?>" + url, true);

 
        request.onreadystatechange = function()
	{
		if(request.readyState == 4 && (request.status == 200 || request.status == 302 || request.status == 301))
		{
			callback(request.responseText, arg1, arg2, arg3);
		}
        }
        request.send(null);
}
