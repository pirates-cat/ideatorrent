//Show and update the dup table
$(document).ready(function() {
	var reportduplicatesearcharea = document.getElementById('reportduplicate-searcharea');
	if(reportduplicatesearcharea != null)
		reportduplicatesearcharea.style.display = 'block';
	update_dup_table('', 1, 'dup_search_string', 'submitpage2_showNextButton');
});
