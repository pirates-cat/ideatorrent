function ShowHide(e) {
	tr = document.getElementsByTagName("tr");
	td = document.getElementsByTagName("td");
	for (i=0;i<tr.length;i++) {
		if (tr[i].className == e) {
			if (tr[i].style.display=='none')
				tr[i].style.display='table-row'
			else
				tr[i].style.display='none'	    
		}
	}
	for (i=0;i<td.length;i++) {
		if (td[i].className == "hidethis") {
			trcount=0
			for (j=0;j<tr.length;j++) {
				if (tr[j].style.display=='table-row')
					trcount+=1	
			}
			if (td[i].style.display=='table-cell' && trcount == 0)
				td[i].style.display='none'
			else
				td[i].style.display='table-cell'
		}
		if (td[i].className == e) {
			if (td[i].style.display=='none')
				td[i].style.display='table-cell'
			else
				td[i].style.display='none'	    
		}
	}
}

function checkAll(checkname, exby, version) {
    for (i = 0; i < checkname.length; i++) {
	if (!version || (checkname[i].value==version || checkname[i].className==version))
	    checkname[i].checked = exby.checked? true:false
    }
}

function externalLinks() {
    if (!document.getElementsByTagName) return;
    var anchors = document.getElementsByTagName("a");
    for (var i=0; i<anchors .length; i++) {
	var anchor = anchors[i];
	if (anchor.getAttribute("href") && anchor.getAttribute("rel") == "external") {
	    anchor.target = "_blank";
	    anchor.title = (anchor.title != "") ? anchor.title+" (opens in a new window)" : "";
	    anchor.className = (anchor.className != '') ? anchor.className+' external' : 'external';
	}
    }
}
window.onload = externalLinks;												    
