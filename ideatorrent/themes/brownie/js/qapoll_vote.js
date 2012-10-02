
function voteDown(art_id)
{
	vote(art_id, -1);
}

function voteEqual(art_id)
{
	vote(art_id, 0);
}

function voteUp(art_id)
{
	vote(art_id, 1);
}

function vote(art_id, vote)
{
	ajax("/ajaxvote/" + art_id + "/" + vote, nullanswer, art_id, vote, null);
	answer(art_id, vote, null);
}

function nullanswer(nop, nop2, nop3)
{

}

function answer(art_id, vote, nop)
{
	var imgUp = document.getElementById('votingimageup-'+ art_id);
	var imgDown = document.getElementById('votingimagedown-'+ art_id);
	var imgEqual = document.getElementById('votingimageequal-'+ art_id);
	var linkvoteEqual = document.getElementById('linkvoteequal-'+ art_id);
	var linkvoteUp = document.getElementById('linkvoteup-'+ art_id);
	var linkvoteDown = document.getElementById('linkvotedown-'+ art_id);
	var voteupcount = document.getElementById('voteupcount-' + art_id);
	var voteequalcount = document.getElementById('voteequalcount-' + art_id);
	var votedowncount = document.getElementById('votedowncount-' + art_id);
	var upbar = document.getElementById('upbar-' + art_id);
	var equalbar = document.getElementById('equalbar-' + art_id);
	var downbar = document.getElementById('downbar-' + art_id);
	var votebar = document.getElementById('votebar-' + art_id);

	var upcount = parseInt(voteupcount.innerHTML);
	var equalcount = parseInt(voteequalcount.innerHTML);
	var downcount = parseInt(votedowncount.innerHTML);

	var oldvote = 42;
	if(imgUp.src.indexOf("images/up22.png") != -1 &&
		imgDown.src.indexOf("images/down22.png") != -1 &&
		imgEqual.src.indexOf("images/equal20.png") != -1)
		oldvote = 0;
	else if(imgUp.src.indexOf("images/up22.png") != -1)
	{
		oldvote = 1;
		upcount = upcount - 1;
	}
	else if(imgEqual.src.indexOf("images/equal20.png") != -1)
	{
		oldvote = 0;
		equalcount = equalcount - 1;
	}
	else if(imgDown.src.indexOf("images/down22.png") != -1)
	{
		oldvote = -1;
		downcount = downcount - 1;
	}

	//Notify UI that the vote was done
	var nbvotes = document.getElementById('votingnumber-'+ art_id);
	nbvotes.innerHTML = parseInt(nbvotes.innerHTML) + vote - oldvote;


	if(vote == -1)
	{
		imgUp.src=imgUp.src.substring(0, imgUp.src.lastIndexOf('/')) + "/up22-0.png";
		imgEqual.src=imgEqual.src.substring(0, imgEqual.src.lastIndexOf('/')) + "/equal20-0.png";
		imgDown.src=imgDown.src.substring(0, imgDown.src.lastIndexOf('/')) + "/down22.png";
		linkvoteUp.insertBefore(imgUp, null);
		linkvoteUp.style.display='inline';
		imgUp.title=i18n_promotethissolution;
		linkvoteEqual.insertBefore(imgEqual, null);
		linkvoteEqual.style.display='inline';
		imgEqual.title=i18n_dontcare;
		linkvoteDown.parentNode.insertBefore(imgDown, linkvoteDown);
		linkvoteDown.style.display='none';
		imgDown.title=i18n_demoted;
		downcount = downcount + 1;
	}
	else if(vote == 0)
	{
		imgUp.src=imgUp.src.substring(0, imgUp.src.lastIndexOf('/')) + "/up22-0.png";
		imgEqual.src=imgEqual.src.substring(0, imgEqual.src.lastIndexOf('/')) + "/equal20.png";
		imgDown.src=imgDown.src.substring(0, imgDown.src.lastIndexOf('/')) + "/down22-0.png";
		linkvoteUp.insertBefore(imgUp, null);
		linkvoteUp.style.display='inline';
		imgUp.title=i18n_promotethissolution;
		linkvoteEqual.parentNode.insertBefore(imgEqual, linkvoteEqual);
		linkvoteEqual.style.display='none';
		imgEqual.title=i18n_blankvotecasted;
		linkvoteDown.insertBefore(imgDown, null);
		linkvoteDown.style.display='inline';
		imgDown.title=i18n_demotethissolution;
		equalcount = equalcount + 1;
	}
	else if(vote == 1)
	{
		imgUp.src=imgUp.src.substring(0, imgUp.src.lastIndexOf('/')) + "/up22.png";
		imgEqual.src=imgEqual.src.substring(0, imgEqual.src.lastIndexOf('/')) + "/equal20-0.png";
		imgDown.src=imgDown.src.substring(0, imgDown.src.lastIndexOf('/')) + "/down22-0.png";
		linkvoteUp.parentNode.insertBefore(imgUp, linkvoteUp);
		linkvoteUp.style.display='none';
		imgUp.title=i18n_promoted;
		linkvoteEqual.insertBefore(imgEqual, null);
		linkvoteEqual.style.display='inline';
		imgEqual.title=i18n_dontcare;
		linkvoteDown.insertBefore(imgDown, null);
		linkvoteDown.style.display='inline';
		imgDown.title=i18n_demotethissolution;
		upcount = upcount + 1;
	}

	voteupcount.innerHTML = upcount;
	voteequalcount.innerHTML = equalcount;
	votedowncount.innerHTML = downcount;

	votebar.title = upcount + ' / ' + equalcount + ' / ' + downcount;

	upbar.style.width = Math.round(60*(upcount/(upcount + equalcount + downcount))) + 'px';
	equalbar.style.width = Math.round(60*(equalcount/(upcount + equalcount + downcount))) + 'px';
	downbar.style.width = Math.round(60*(downcount/(upcount + equalcount + downcount))) + 'px';

}

function approvalVoteUp(art_id)
{
	ajax("/ajaxapprovalvote/" + art_id + "/1/", approvalvote_answer, art_id, null);

}

function markAsInvalidIdea(art_id)
{
	ajax("/ajaxmark_as_invalid/" + art_id + "/", disableApprovalBoxOptions, art_id, null);

}

function markAsAlreadyImplemented(art_id)
{
	ajax("/ajaxmark_as_already_implemented/" + art_id + "/", disableApprovalBoxOptions, art_id, null);

}

function disableApprovalBoxOptions(art_id)
{
	var imgUp = document.getElementById('imageapprovalvoteup-'+ art_id);
	var linkvoteUp = document.getElementById('linkapprovalvoteup-'+ art_id);
	var imgnotanidea = document.getElementById('imagenotanidea-'+ art_id);
	var linknotanidea = document.getElementById('linknotanidea-'+ art_id);
	var imgalreadyimp = document.getElementById('imagealreadyimp-'+ art_id);
	var linkalreadyimp = document.getElementById('linkalreadyimp-'+ art_id);


	imgUp.src=imgUp.src.substring(0, imgUp.src.lastIndexOf('/')) + "/up22-0.png";
	imgnotanidea.src=imgnotanidea.src.substring(0, imgnotanidea.src.lastIndexOf('/')) + "/closed-0.png";
	imgalreadyimp.src=imgalreadyimp.src.substring(0, imgalreadyimp.src.lastIndexOf('/')) + "/alreadyimplemented-0.png";

	linkvoteUp.parentNode.insertBefore(imgUp, linkvoteUp);
	linknotanidea.parentNode.insertBefore(imgnotanidea, linknotanidea);
	linkalreadyimp.parentNode.insertBefore(imgalreadyimp, linkalreadyimp);

}

function approvalvote_answer(art_id)
{
	var nbvotes = document.getElementById('approvalvotingnumber-'+ art_id);

	disableApprovalBoxOptions(art_id);

	nbvotes.innerHTML = parseInt(nbvotes.innerHTML) + 1;
}


function togglebookmark(art_id)
{
	ajax("/ajaxtogglebookmark/" + art_id + "/", togglebookmark_answer, art_id, null, null);
}


function togglebookmark_answer(art_id, nop, nop2)
{
	//Notify UI that the vote was done
	var imgBookmark = document.getElementById('bookmarkimg-'+ art_id);
	if(imgBookmark.src.indexOf('bookmark.png') != -1)
	{
		imgBookmark.src = imgBookmark.src.substring(0, imgBookmark.src.lastIndexOf('/')) + '/bookmark-0.png';
		imgBookmark.title = i18n_bookmark;
	}
	else if(imgBookmark.src.indexOf('bookmark-0.png') != -1)
	{
		imgBookmark.src = imgBookmark.src.substring(0, imgBookmark.src.lastIndexOf('/')) + '/bookmark.png';
		imgBookmark.title = i18n_unbookmark;
	}
}


