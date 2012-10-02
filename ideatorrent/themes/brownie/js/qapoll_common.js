//Hide the bud & ideas info fields
$(document).ready(function() {
	menufilterbox_minimize();
	disable_topnavbar_hover_menus();

});


function disable_topnavbar_hover_menus()
{
	var menu1 = document.getElementById('maindropdown-menu1');
	if(menu1 != null)
	{
		var classlist = menu1.className.split(' ');
		var selected = false;
		for (var i = 0; i < classlist.length; i++)
			if(classlist[i] == 'selected')
				selected = true;

		if(selected == false)
			menu1.className='maindropdown-1drop-disabled';
		else
			menu1.className='maindropdown-1drop-disabled selected';
	}
	var menu2 = document.getElementById('maindropdown-menu2');
	if(menu2 != null)
	{
		var classlist = menu2.className.split(' ');
		var selected = false;
		for (var i = 0; i < classlist.length; i++)
			if(classlist[i] == 'selected')
				selected = true;

		if(selected == false)
			menu2.className='maindropdown-1drop-disabled';
		else
			menu2.className='maindropdown-1drop-disabled selected';
	}
}

function activate_menu1()
{
	var menu1 = document.getElementById('maindropdown-menu1');
	if(menu1 != null)
	{
		var classlist = menu1.className.split(' ');
		var selected = false;
		for (var i = 0; i < classlist.length; i++)
			if(classlist[i] == 'selected')
				selected = true;

		if(selected == false)
			menu1.className='maindropdown-1drop';
		else
			menu1.className='maindropdown-1drop selected';
	}

	var menu1link = document.getElementById('maindropdown-1link');
	if(menu1link != null)
		menu1link.onclick=desactivate_menu1;


	return false;
}

function activate_menu2()
{
	var menu2 = document.getElementById('maindropdown-menu2');
	if(menu2 != null)
	{
		var classlist = menu2.className.split(' ');
		var selected = false;
		for (var i = 0; i < classlist.length; i++)
			if(classlist[i] == 'selected')
				selected = true;

		if(selected == false)
			menu2.className='maindropdown-1drop';
		else
			menu2.className='maindropdown-1drop selected';
	}

	var menu2link = document.getElementById('maindropdown-2link');
	if(menu2link != null)
		menu2link.onclick=desactivate_menu2;


	return false;
}


function desactivate_menu1()
{
	var menu1 = document.getElementById('maindropdown-menu1');
	if(menu1 != null)
	{
		var classlist = menu1.className.split(' ');
		var selected = false;
		for (var i = 0; i < classlist.length; i++)
			if(classlist[i] == 'selected')
				selected = true;

		if(selected == false)
			menu1.className='maindropdown-1drop-disabled';
		else
			menu1.className='maindropdown-1drop-disabled selected';
	}

	var menu1link = document.getElementById('maindropdown-1link');
	if(menu1link != null)
		menu1link.onclick=activate_menu1;

	return false;
}

function desactivate_menu2()
{
	var menu2 = document.getElementById('maindropdown-menu2');
	if(menu2 != null)
	{
		var classlist = menu2.className.split(' ');
		var selected = false;
		for (var i = 0; i < classlist.length; i++)
			if(classlist[i] == 'selected')
				selected = true;

		if(selected == false)
			menu2.className='maindropdown-1drop-disabled';
		else
			menu2.className='maindropdown-1drop-disabled selected';
	}

	var menu2link = document.getElementById('maindropdown-2link');
	if(menu2link != null)
		menu2link.onclick=activate_menu2;

	return false;
}


function menufilterbox_minimize()
{
	var keywordinput = document.getElementById('menufilterbox-keywordinput');

	var tagsinput = document.getElementById('menufilterbox-tagsinput');
	if(tagsinput != null && tagsinput.value != null && tagsinput.value != '')
	{
		if(keywordinput != null)
		{
			if(keywordinput.value == 'Search...')
				keywordinput.value = '';
			keywordinput.onblur = '';
			keywordinput.onfocus = '';
		}
		return;
	}

	var admintagsinput = document.getElementById('menufilterbox-admintagsinput');
	if(admintagsinput != null && admintagsinput.value != null && admintagsinput.value != '')
	{
		if(keywordinput != null)
		{
			if(keywordinput.value == 'Search...')
				keywordinput.value = '';
			keywordinput.onblur = '';
			keywordinput.onfocus = '';
		}
		return;
	}

	var tagsarea = document.getElementById('menufilterbox-tagsarea');
	if(tagsarea != null)
		tagsarea.style.display = 'none';
	var tagsarea = document.getElementById('menufilterbox-tagsarea2');
	if(tagsarea != null)
		tagsarea.style.display = 'none';

	var admintagsarea = document.getElementById('menufilterbox-admintagsarea');
	if(admintagsarea != null)
		admintagsarea.style.display = 'none';
	var admintagsarea = document.getElementById('menufilterbox-admintagsarea2');
	if(admintagsarea != null)
		admintagsarea.style.display = 'none';

	var filterbuttonarea = document.getElementById('menufilterbox-filterbuttonarea');
	if(filterbuttonarea != null)
		filterbuttonarea.style.display = 'none';
	
	var keywordwordarea = document.getElementById('menufilterbox-keywordwordarea');
	if(keywordwordarea != null)
		keywordwordarea.style.display = 'none';

	if(keywordinput != null)
		keywordinput.style.width = '130px';

	var okbutton = document.getElementById('menufilterbox-okbutton');
	if(okbutton != null)
		okbutton.style.display = 'inline';
	
	var advancedsearchlinkarea = document.getElementById('menufilterbox-advancedsearchlinkarea');
	if(advancedsearchlinkarea != null)
		advancedsearchlinkarea.style.display = 'block';
}

function menufilterbox_maximize()
{
	var tagsarea = document.getElementById('menufilterbox-tagsarea');
	if(tagsarea != null)
		tagsarea.style.display = 'block';
	var tagsarea = document.getElementById('menufilterbox-tagsarea2');
	if(tagsarea != null)
		tagsarea.style.display = 'block';

	var admintagsarea = document.getElementById('menufilterbox-admintagsarea');
	if(admintagsarea != null)
		admintagsarea.style.display = 'block';
	var admintagsarea = document.getElementById('menufilterbox-admintagsarea2');
	if(admintagsarea != null)
		admintagsarea.style.display = 'block';

	var filterbuttonarea = document.getElementById('menufilterbox-filterbuttonarea');
	if(filterbuttonarea != null)
		filterbuttonarea.style.display = 'block';

	var keywordwordarea = document.getElementById('menufilterbox-keywordwordarea');
	if(keywordwordarea != null)
		keywordwordarea.style.display = 'block';

	var keywordinput = document.getElementById('menufilterbox-keywordinput');
	if(keywordinput != null)
	{
		keywordinput.style.width = '100px';
		if(keywordinput.value == 'Search...')
			keywordinput.value = '';
		keywordinput.onblur = '';
		keywordinput.onfocus = '';
	}

	var okbutton = document.getElementById('menufilterbox-okbutton');
	if(okbutton != null)
		okbutton.style.display = 'none';
	
	var advancedsearchlinkarea = document.getElementById('menufilterbox-advancedsearchlinkarea');
	if(advancedsearchlinkarea != null)
		advancedsearchlinkarea.style.display = 'none'; 
}


function show_duptable()
{
	var duptable = document.getElementById('duptable');
	if(duptable != null)
		duptable.style.display = 'block';
}

function dup_table_prev_page(table_id)
{
	var duptable_id = parseInt(table_id);

	if(isNaN(duptable_id))
		duptable_id = '';

	var pagenumber = document.getElementById('duptable' + duptable_id + '-pagenumber');
	if(pagenumber != null)
		update_dup_table(duptable_id, (parseInt(pagenumber.innerHTML) - 1));
}

function dup_table_next_page(table_id)
{
	var duptable_id = parseInt(table_id);

	if(isNaN(duptable_id))
		duptable_id = '';

	var pagenumber = document.getElementById('duptable' + duptable_id + '-pagenumber');
	if(pagenumber != null)
		update_dup_table(duptable_id, (parseInt(pagenumber.innerHTML) + 1));
}

/**
 * Update the duplicate table page
 * table_id: The id of the duptable we want to interact with. DEFAULT: ''
 * page: The page number. DEFAULT: 1
 * input_id: The id of the input containing the text to search for. DEFAULT: should be given. Stored internally.
 * callback_function: The function to call after the page is shown. DEFAULT: none
 */
function update_dup_table(table_id, page, input_id, callback_function)
{
	var duptable_id = parseInt(table_id);
	var dup_search_string_input = document.getElementById(input_id);
	var pagenumber = parseInt(page);
	var dup_search_string = '';

	if(isNaN(duptable_id))
		duptable_id = '';

	if(isNaN(pagenumber))
		pagenumber = 1;

	var stored_dup_search_string = document.getElementById('duptable' + duptable_id + '-searchstring');
	var duptable_statuscell = document.getElementById('duptable' + duptable_id + '-cell-5-3');

	//Get the search string
	if(dup_search_string_input != null)
	{
		dup_search_string = dup_search_string_input.value;
		stored_dup_search_string.innerHTML = dup_search_string;
	}
	else
	{
		dup_search_string = stored_dup_search_string.innerHTML;
	}

	if(dup_search_string != '')
	{
		//Clear the entries
		for(i=1; i < 11; i++)
		{
			for(j=1; j < 7; j++)
			{
				document.getElementById('duptable' + duptable_id + '-cell-' + i + '-' + j).innerHTML = '&nbsp;';
			}
		}

		duptable_statuscell.innerHTML = i18n_updating;
		ajaxdata("/ajaxdata_similar_items/" + dup_search_string + "/" + pagenumber + "/", populate_dup_table, callback_function, duptable_id, null);
	}
}

function populate_dup_table(data, callback_function, duptable_id, c, d)
{
	var duptable_statuscell = document.getElementById('duptable' + duptable_id + '-cell-5-3');
	var duptable = document.getElementById('duptable' + duptable_id + '-all');
	var pagenumber = document.getElementById('duptable' + duptable_id + '-pagenumber');
	var duptableleftrightlinks = document.getElementById('duptable' + duptable_id + '-leftrightlinks');
	var duptableleftlink = document.getElementById('duptable' + duptable_id + '-leftlink');
	var duptablerightlink = document.getElementById('duptable' + duptable_id + '-rightlink');

	var data = eval('(' + data + ')');

	//Clear the entries
	for(i=1; i < 11; i++)
	{
		for(j=1; j < 7; j++)
		{
			document.getElementById('duptable' + duptable_id + '-cell-' + i + '-' + j).innerHTML = '&nbsp;';
		}
	}

	if(data.items.length == 0)
	{
		duptable_statuscell.innerHTML = i18n_nonefound;
		duptableleftrightlinks.style.display = 'none';
		pagenumber.innerHTML = 0;
	}
	else
	{

		for(i=0; i < data.items.length; i++)
		{
			document.getElementById('duptable' + duptable_id + '-cell-' + (i+1) + '-1').innerHTML = data.items[i].id;
			document.getElementById('duptable' + duptable_id + '-cell-' + (i+1) + '-2').innerHTML = data.items[i].title;
			document.getElementById('duptable' + duptable_id + '-cell-' + (i+1) + '-3').innerHTML = data.items[i].votes;
			document.getElementById('duptable' + duptable_id + '-cell-' + (i+1) + '-4').innerHTML = data.items[i].date;
			document.getElementById('duptable' + duptable_id + '-cell-' + (i+1) + '-5').innerHTML = data.items[i].status;
			document.getElementById('duptable' + duptable_id + '-cell-' + (i+1) + '-6').innerHTML = data.items[i].search_rank;

		}
		duptableleftrightlinks.style.display = 'inline';
		if(data.pagenumber == 1)
			duptableleftlink.style.visibility = 'hidden';
		else
			duptableleftlink.style.visibility = 'visible';

		if(data.pagenumber >= data.totalpages)
			duptablerightlink.style.visibility = 'hidden';
		else
			duptablerightlink.style.visibility = 'visible';

		pagenumber.innerHTML = data.pagenumber;
	}

	if(eval("typeof " + callback_function + " == 'function'"))
	{
		eval(callback_function + '()');
	}

}

function limitText(textArea, length) {
    if (textArea.value.length > length-1) {
        textArea.value = textArea.value.substr(0,length-1);
    }
}

