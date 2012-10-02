function showSecondPart(id)
{
	var secondpart = document.getElementById('hidden-choice-solution-' + id);
	if(secondpart != null)
		secondpart.style.display = 'block';
	var link = document.getElementById('hidden-choice-solution-link-' + id);
	if(link != null)
		link.style.display = 'none';
}

function link_solution_to(dup_id, art_id, sol_id, other_art_id)
{
 	ajax("/ajaxlink_solution_to/" + sol_id + "/" + other_art_id, link_solution_to_2,
		dup_id, art_id, sol_id);

}

function link_solution_to_2(dup_id, art_id, sol_id)
{
	var sol = document.getElementById('choice-solution-' + dup_id + '-' + art_id + '-' + sol_id);
	var destrow = document.getElementById('dupreprow3-' + dup_id);
	var adminlinks = document.getElementById('adminlinks-' + dup_id + '-' + sol_id);

	if(sol != null && destrow != null && adminlinks != null)
	{
		var clone = sol.cloneNode(true);
		//Bad hack :s
		//Hide the adminlinks of the cloned solution
		clone.childNodes[5].style.display = 'none';
		destrow.appendChild(clone);
		adminlinks.style.display = 'none';
	}
}

function mark_solution_as_dup(dup_id, art_id, sol_id, other_sol_id)
{
	ajax("/ajaxmark_solution_as_dup/" + sol_id + "/" + other_sol_id, mark_solution_as_dup_2,
		dup_id, art_id, sol_id);
}

function mark_solution_as_dup_2(dup_id, art_id, sol_id)
{
	var sol = document.getElementById('choice-solution-' + dup_id + '-' + art_id + '-' + sol_id);
	if(sol != null)
		sol.style.display = 'none';
}

function discard_duplicate_report(duprep_id)
{
	hide_duprep(duprep_id);
	ajax("/ajaxdiscard_duplicate_report/" + duprep_id, hide_duprep,
		duprep_id, null, null);
}

function mark_as_duplicate_orig(duprep_id)
{
	hide_duprep(duprep_id);
	ajax("/ajaxmark_as_duplicate_orig/" + duprep_id, hide_duprep,
		duprep_id, null, null);
}

function mark_as_duplicate_dup(duprep_id)
{
	hide_duprep(duprep_id);
	ajax("/ajaxmark_as_duplicate_dup/" + duprep_id, hide_duprep,
		duprep_id, null, null);
}

function hide_duprep(duprep_id)
{
	var duprep = document.getElementById('dupreprow1-' + duprep_id);
	if(duprep != null)
		duprep.style.display = 'none';
	duprep = document.getElementById('dupreprow2-' + duprep_id);
	if(duprep != null)
		duprep.style.display = 'none';
	duprep = document.getElementById('dupreprow3-' + duprep_id);
	if(duprep != null)
		duprep.style.display = 'none';
}
