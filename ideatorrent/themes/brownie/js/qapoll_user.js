$(document).ready(function() {
	sendmsghide();
});

function sendmsghide()
{
	var form = document.getElementById('contactuser');
	if(form != null)
		form.style.display='none';
	var contactusertitle = document.getElementById('contactusertitle');
	if(contactusertitle != null)
		contactusertitle.style.display='none';
	var contactuserlink = document.getElementById('contactuserlink');
	if(contactuserlink != null)
		contactuserlink.style.display='inline';
}



function showHideSendMsgArea()
{
	var form = document.getElementById('contactuser');
	if(form != null)
	{
		if(form.style.display == 'block')
			form.style.display='none';
		else
			form.style.display='block';
	}
}
