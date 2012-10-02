//Init the view
$(document).ready(function() {
	init();
});

function init()
{
	var commentarea = document.getElementById('postcomment');
	if(commentarea != null)
		commentarea.style.display = 'none';

	var postlink = document.getElementById('postcommentlink');
	if(postlink != null)
		postlink.style.display = 'inline';

	var commenttitle = document.getElementById('postcommenttitle');
	if(commenttitle != null)
		commenttitle.style.display = 'none';

	var solutionarea = document.getElementById('postsolution');
	if(solutionarea != null)
		solutionarea.style.display = 'none';

	var postsolutionlink = document.getElementById('postsolutionlink');
	if(postsolutionlink != null)
		postsolutionlink.style.display = 'inline';

	var solutiontitle = document.getElementById('postsolutiontitle');
	if(solutiontitle != null)
		solutiontitle.style.display = 'none';

	var list_status = document.getElementById('list_status');
	if(list_status != null)
		list_status.style.display = 'none';

	var status_button = document.getElementById('status_button');
	if(status_button != null)
		status_button.style.display = 'none';

	var status_string = document.getElementById('status_string');
	if(status_string != null)
	status_string.style.display = 'inline';

	var edit_status_button = document.getElementById('edit_status_button');
	if(edit_status_button != null)
		edit_status_button.style.display = 'inline';

	var list_release = document.getElementById('list_release');
	if(list_release != null)
		list_release.style.display = 'none';

	var release_button = document.getElementById('release_button');
	if(release_button != null)
		release_button.style.display = 'none';

	var release_string = document.getElementById('release_string');
	if(release_string != null)
		release_string.style.display = 'inline';

	var edit_release_button = document.getElementById('edit_release_button');
	if(edit_release_button != null)
		edit_release_button.style.display = 'inline';

	var posttags = document.getElementById('posttags');
	if(posttags != null)
		posttags.style.display = 'none';

	var tagstextbox = document.getElementById('tagstextbox');
	if(tagstextbox != null)
		tagstextbox.style.display = 'none';

	var tagtext = document.getElementById('tagtext');
	if(tagtext != null)
		tagtext.style.display = 'inline';

	var edittags = document.getElementById('edittags');
	if(edittags != null)
		edittags.style.display = 'inline';

	var postadmintags = document.getElementById('postadmintags');
	if(postadmintags != null)
		postadmintags.style.display = 'none';

	var admintagstextbox = document.getElementById('admintagstextbox');
	if(admintagstextbox != null)
		admintagstextbox.style.display = 'none';

	var admintagtext = document.getElementById('admintagtext');
	if(admintagtext != null)
		admintagtext.style.display = 'inline';

	var editadmintags = document.getElementById('editadmintags');
	if(editadmintags != null)
		editadmintags.style.display = 'inline';
}

function showTagsEdit()
{
	var tagstextbox = document.getElementById('tagstextbox');
	if(tagstextbox != null)
	{
		tagstextbox.style.display = 'inline';
		tagstextbox.focus();
	}

	var tagtext = document.getElementById('tagtext');
	if(tagtext != null)
		tagtext.style.display = 'none';

	var edittags = document.getElementById('edittags');
	if(edittags != null)
		edittags.style.display = 'none';
}

function showAdminTagsEdit()
{
	var admintagstextbox = document.getElementById('admintagstextbox');
	if(admintagstextbox != null)
	{
		admintagstextbox.style.display = 'inline';
		admintagstextbox.focus();
	}

	var admintagtext = document.getElementById('admintagtext');
	if(admintagtext != null)
		admintagtext.style.display = 'none';

	var editadmintags = document.getElementById('editadmintags');
	if(editadmintags != null)
		editadmintags.style.display = 'none';
}

function sendTags(art_id, admin, tags, basepath)
{
	var tagstextbox = document.getElementById('tagstextbox');
	var tagtext = document.getElementById('tagtext');
	var edittags = document.getElementById('edittags');
	var admintagstextbox = document.getElementById('admintagstextbox');
	var admintagtext = document.getElementById('admintagtext');
	var editadmintags = document.getElementById('editadmintags');

	if(tagstextbox == null || tagtext == null || edittags == null)
		return false;

	if(admin == 0)
	{
		ajaxdata("/ajaxsavetags/" + art_id + "/" + tags, hideTagsEdit, null, null, null);

		tagstextbox.style.display = 'none';
		if(tagstextbox.value != '')
		{
			var output = '';
			var taglist = tagstextbox.value;
			var reg = /[ ]+/

			var tags = taglist.split(reg);
			for(var i=0; i < tags.length; i++)
			{
				output = output + '<a href="' + basepath + '?tags=' + tags[i] + '">' + tags[i] + '</a> ';
			}


			tagtext.innerHTML = output;
		}
		else
			tagtext.innerHTML = '<span style="color:rgb(100,100,100)">(' + i18n_none + ')</span>';
		tagtext.style.display = 'inline';
		edittags.style.display = 'inline';
	}
	else
	{
		ajaxdata("/ajaxsaveadmintags/" + art_id + "/" + tags, hideTagsEdit, null, null, null);

		admintagstextbox.style.display = 'none';
		if(admintagstextbox.value != '')
		{
			var output = '';
			var taglist = admintagstextbox.value;
			var reg = /[ ]+/

			var tags = taglist.split(reg);
			for(var i=0; i < tags.length; i++)
			{
				output = output + '<a href="' + basepath + '?admintags=' + tags[i] + '">' + tags[i] + '</a> ';
			}


			admintagtext.innerHTML = output;
		}
		else
			admintagtext.innerHTML = '<span style="color:rgb(100,100,100)">(' + i18n_none + ')</span>';
		admintagtext.style.display = 'inline';
		editadmintags.style.display = 'inline';
	}
}

function submitTags(event, art_id, admin, basepath)
{
	var keynum;
	var tagstextbox = document.getElementById('tagstextbox');
	var tagtext = document.getElementById('tagtext');
	var edittags = document.getElementById('edittags');
	var admintagstextbox = document.getElementById('admintagstextbox');
	var admintagtext = document.getElementById('admintagtext');
	var editadmintags = document.getElementById('editadmintags');

	if(tagstextbox == null || tagtext == null || edittags == null)
		return false;

	if(window.event) // IE
		keynum = event.keyCode;
	else if(event.which) // Netscape/Firefox/Opera
		keynum = event.which;

	if(keynum == 13)
	{
		if(admin == 0)
		{
			sendTags(art_id, admin, tagstextbox.value, basepath);
		}
		else
		{
			sendTags(art_id, admin, admintagstextbox.value, basepath);
		}
	}

	var keychar = String.fromCharCode(keynum);

	charcheck = /[a-zA-Z0-9 _\-]/;
	return (keynum != 13 && (charcheck.test(keychar) || keynum < 32 || keynum == undefined));

//	charcheck = /[^!"#\$%&'\(\)\*\+,\./:;<=>\?@\[\\\]\^`\{|\}~§µ£²]/;
//	return (keynum != 13 && (charcheck.test(keychar)));

}

function hideTagsEdit(data, a, b, c, d)
{
	//Done before
}

function showHideCommentArea()
{
	var commentarea = document.getElementById('postcomment');
	if(commentarea.style.display == 'none')
		commentarea.style.display = 'block';
	else
		commentarea.style.display = 'none';
}

function showHideSolutionArea()
{
	var solutionarea = document.getElementById('postsolution');
	if(solutionarea.style.display == 'none')
		solutionarea.style.display = 'block';
	else
		solutionarea.style.display = 'none';
}

function showStatusEdit()
{
	var list_status = document.getElementById('list_status');
	if(list_status != null)
		list_status.style.display = 'inline';

	var status_string = document.getElementById('status_string');
	status_string.style.display = 'none';

	var edit_status_button = document.getElementById('edit_status_button');
	if(edit_status_button != null)
		edit_status_button.style.display = 'none';
}

function showReleaseEdit()
{
	var list_release = document.getElementById('list_release');
	if(list_release != null)
		list_release.style.display = 'inline';

	var release_string = document.getElementById('release_string');
	release_string.style.display = 'none';

	var edit_release_button = document.getElementById('edit_release_button');
	if(edit_release_button != null)
		edit_release_button.style.display = 'none';
}

function showRelationEdit()
{
	var relation_text = document.getElementById('relation_text');
	var list_relations = document.getElementById('list_relations');
	var edit_relation_button = document.getElementById('edit_relation_button');
	if(relation_text == null || list_relations == null || edit_relation_button == null)
		return;

	edit_relation_button.style.display = 'none';
	var current_relation_name = relation_text.innerHTML;
	relation_text.innerHTML = i18n_loading;
	

	ajaxdata("/ajaxdata_all_relations/", populate_relation_select, current_relation_name, null, null);

}

function trim(stringToTrim) 
{
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}


function populate_relation_select(data, current_relation_name, b, c, d)
{
	var relation_text = document.getElementById('relation_text');
	var list_relations = document.getElementById('list_relations');
	var edit_relation_button = document.getElementById('edit_relation_button');
	if(relation_text == null || list_relations == null || edit_relation_button == null)
		return;

	var data = eval('(' + data + ')');

	nbOptions = list_relations.childNodes.length;

	if(nbOptions <= 1)
	{
		var current_cat = '';
		var newoptgroup = null;
		var defaultoption = document.createElement('option');
		defaultoption.value = -1;
		defaultoption.innerHTML = i18n_nothingothers;
		list_relations.appendChild(defaultoption);
		for(i=0; i < data.items.length; i++)
		{
			if(current_cat != data.items[i].category_name)
			{
				current_cat = data.items[i].category_name;
				newoptgroup = document.createElement('optgroup');
				newoptgroup.label = data.items[i].category_name;
				list_relations.appendChild(newoptgroup);
			}

			var newoption = document.createElement('option');
			newoption.value = data.items[i].id;
			newoption.innerHTML = data.items[i].name;
			if(current_relation_name.indexOf(data.items[i].name) != -1)
				newoption.selected = true;
			newoptgroup.appendChild(newoption);
		}
	}

	relation_text.style.display = 'none';
	list_relations.style.display = 'inline';
}

function saveRelation(art_id)
{
	var list_relations = document.getElementById('list_relations');

	ajax("/ajaxsaverelation/" + art_id + "/" + list_relations.options[list_relations.selectedIndex].value, hideRelationEdit,
		list_relations.options[list_relations.selectedIndex].value, null, null);
}

function hideRelationEdit(relation_id)
{
	var relation_text = document.getElementById('relation_text');
	var list_relations = document.getElementById('list_relations');
	var edit_relation_button = document.getElementById('edit_relation_button');
	if(relation_text == null || list_relations == null || edit_relation_button == null)
		return;

	edit_relation_button.style.display = 'inline';
	relation_text.style.display = 'inline';
	list_relations.style.display = 'none';
	relation_text.innerHTML = '<a href="/">' + 
		list_relations.options[list_relations.selectedIndex].innerHTML + '</a> ';	
}

function saveStatus(art_id)
{
	var list_status = document.getElementById('list_status');

	ajax("/ajaxsavestatus/" + art_id + "/" + list_status.options[list_status.selectedIndex].value, hideStatusEdit, null, null, null);
}

function hideStatusEdit()
{
	var list_status = document.getElementById('list_status');
	if(list_status != null)
		list_status.style.display = 'none';

	var status_string = document.getElementById('status_string');
	status_string.innerHTML = list_status.options[list_status.selectedIndex].innerHTML;
	status_string.style.display = 'inline';

	var edit_status_button = document.getElementById('edit_status_button');
	if(edit_status_button != null)
		edit_status_button.style.display = 'inline';
}

function saveRelease(art_id)
{
	var list_release = document.getElementById('list_release');

	ajax("/ajaxsaverelease/" + art_id + "/" + list_release.options[list_release.selectedIndex].value, hideReleaseEdit, null, null, null);
}

function hideReleaseEdit()
{
	var list_release = document.getElementById('list_release');
	if(list_release != null)
		list_release.style.display = 'none';

	var release_string = document.getElementById('release_string');
	release_string.innerHTML = list_release.options[list_release.selectedIndex].innerHTML;
	release_string.style.display = 'inline';

	var edit_release_button = document.getElementById('edit_release_button');
	if(edit_release_button != null)
		edit_release_button.style.display = 'inline'; 
}



