//Hide the bud & ideas info fields
$(document).ready(function() {
	show_cat_tooltip();
	bugideahide();
	show_duptable();
});


function bugideahide()
{
	var select = document.getElementById('bugideachoice');

	if(select != null)
	{
		if(select.selectedIndex == 0)
		{
			document.getElementById('buginfos').style.display = 'none';
			document.getElementById('ideainfos').style.display = 'none';
			document.getElementById('submitbutton').style.display = 'none';
		}
		else if(select.selectedIndex == 1)
		{
			document.getElementById('buginfos').style.display = 'block';
			document.getElementById('ideainfos').style.display = 'none';
			document.getElementById('submitbutton').style.display = 'inline';
		}
		else
		{
			document.getElementById('buginfos').style.display = 'none';
			document.getElementById('ideainfos').style.display = 'block';
			document.getElementById('submitbutton').style.display = 'inline';
		}
	}
}


function show_cat_tooltip()
{
	var select = document.getElementById('categories');

	var tooltip = document.getElementById('choice_category_tooltip');
	if(select != null)
		tooltip.innerHTML = select.options[select.selectedIndex].label;
}

function updateRelationSubcategories()
{
	var relations = document.getElementById('relations');
	var relations_subcategory = document.getElementById('relations_subcategory');
	var project_category_string = document.getElementById('project_category_string');
	if(relations == null)
		return;

	ajaxdata("/ajaxdata_relation_subcategories/" + relations.options[relations.selectedIndex].value + "/", populate_relation_subcategories_list, null, null, null);

	relations_subcategory.style.display = 'none';
	project_category_string.style.display = 'none';

	while(relations_subcategory.childNodes.length != 0)
		relations_subcategory.removeChild(relations_subcategory.childNodes[0]);
}

function populate_relation_subcategories_list(data, a, b, c)
{
	var relations_subcategory = document.getElementById('relations_subcategory');
	var project_category_string = document.getElementById('project_category_string');

	var data = eval('(' + data + ')');

	while(relations_subcategory.childNodes.length != 0)
		relations_subcategory.removeChild(relations_subcategory.childNodes[0]);

	if(data.items.length > 0)
	{
		var defaultoption = document.createElement('option');
		defaultoption.value = -2;
		defaultoption.selected = true;
		defaultoption.innerHTML = '(Please select)';
		relations_subcategory.appendChild(defaultoption);
		for(i=0; i < data.items.length; i++)
		{

			var newoption = document.createElement('option');
			newoption.value = data.items[i].id;
			newoption.innerHTML = data.items[i].name;
			relations_subcategory.appendChild(newoption);
		}


		relations_subcategory.style.display = 'inline';
		project_category_string.style.display = 'inline';
	}
}



function submitpage2_showNextButton()
{
	var nextstepbuttonarea = document.getElementById('submitpage2-nextstepbutton');
	if(nextstepbuttonarea != null)
		nextstepbuttonarea.disabled = false;
}

