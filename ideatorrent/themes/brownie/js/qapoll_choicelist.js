function showSecondPart(id)
{
	var secondpart = document.getElementById('hidden-choice-solution-' + id);
	if(secondpart != null)
		secondpart.style.display = 'block';
	var link = document.getElementById('hidden-choice-solution-link-' + id);
	if(link != null)
		link.style.display = 'none';
}


function showHideDupSearchArea(art_id)
{
	var dupsearcharea = document.getElementById('duplicate-searcharea-' + art_id);
	if(dupsearcharea.style.display == 'none')
	{
		dupsearcharea.style.display = 'block';
		update_dup_table(art_id, 1, 'dup_search_string-' + art_id);
	}
	else
		dupsearcharea.style.display = 'none';
}
