function accept_report(report_id)
{
 	ajax("/ajaxaccept_report/" + report_id + "/", hide_report,
		report_id, null, null);

}

function accept_report2(report_id)
{
 	ajax("/ajaxaccept_report2/" + report_id + "/", hide_report,
		report_id, null, null);

}

function discard_report(report_id)
{
 	ajax("/ajaxdiscard_report/" + report_id + "/", hide_report,
		report_id, null, null);

}

function hide_report(report_id)
{
	var report = document.getElementById('report-' + report_id);

	if(report != null)
	{
		report.style.display = 'none';
	}
}
