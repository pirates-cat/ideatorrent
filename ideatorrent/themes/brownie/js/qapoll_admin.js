function deleteItem(art_id)
{
	ajax("/ajaxdeleteitem/" + art_id, delete_answer, art_id, null, null);
}

function delete_answer(art_id, nop, nop2)
{
	//Notify UI that the vote was done
	var link = document.getElementById('deletelink-'+ art_id);
	link.innerHTML = '[' + i18n_undelete + ']';

	var onclickundelete = function()
	{
		undeleteItem(art_id);
		return false;
	}
	link.onclick = onclickundelete;

	document.getElementById('title-' + art_id).style.textDecoration = 'line-through';
	document.getElementById('description-' + art_id).style.textDecoration = 'line-through';

}



function undeleteItem(art_id)
{
	ajax("/ajaxundeleteitem/" + art_id, undelete_answer, art_id, null, null);
}

function undelete_answer(art_id, nop, nop2)
{
	//Notify UI that the vote was done
	var link = document.getElementById('deletelink-'+ art_id);
	link.innerHTML = '[' + i18n_delete + ']';

	var onclickdelete = function()
	{
		deleteItem(art_id);
		return false;
	}
	link.onclick = onclickdelete;

	document.getElementById('title-' + art_id).style.textDecoration = 'none';
	document.getElementById('description-' + art_id).style.textDecoration = 'none';

}

function markAsDuplicateOrig(duprep_id)
{
	ajax("/ajaxmarkasduplicateorig/" + duprep_id, duplicateprocessing_answer, duprep_id, null, null);
}

function discard(duprep_id)
{
	ajax("/ajaxduprepdiscard/" + duprep_id, duplicateprocessing_answer, duprep_id, null, null);
}

function  markAsDuplicateDup(duprep_id)
{
	ajax("/ajaxmarkasduplicatedup/" + duprep_id, duplicateprocessing_answer, duprep_id, null, null);
}

function duplicateprocessing_answer(duprep_id, nop, nop2)
{
	document.getElementById('dupreprow1-' + duprep_id).style.display = 'none';
	document.getElementById('dupreprow2-' + duprep_id).style.display = 'none';
	document.getElementById('dupreprow3-' + duprep_id).style.display = 'none';
}

function showSolutionEditingArea(art_id)
{
	soltitle = document.getElementById('solution-title-' + art_id);
	soltitleedit = document.getElementById('solution-title-edit-' + art_id);
	soldescription = document.getElementById('solution-description-' + art_id);
	soldescriptionedit = document.getElementById('solution-description-edit-' + art_id);

	if(soltitle != null)
		soltitle.style.display = 'none';
	if(soltitleedit != null)
		soltitleedit.style.display = 'inline';
	if(soldescription != null)
		soldescription.style.display = 'none';
	if(soldescriptionedit != null)
		soldescriptionedit.style.display = 'block';

}
