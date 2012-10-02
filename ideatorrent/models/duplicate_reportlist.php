<?php

/*
Copyright (C) 2007 Nicolas Deschildre <ndeschildre@gmail.com>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/




class DuplicateReportListModel extends ModelList
{

	/**
	 * The current page to show.
	 */
	var $_page = 1;

	/**
	 * The number of rows to extract.
	 */
	var $_numberRowsPerPage = -1;

	/**
	 * Filter parameters.
	 * choiceid: The id of the choice which can get a new duplicate. -1 if all.
	 * duplicateid: The id of the duplicate choice. -1 if all.
	 * status: enum:[-1:all 0:active 1:accepted 2:rejected]
	 * submitterid: The user id of the report submitter. -1 if all.
	 * moderatorid: The user id of the admin having made the decision to accept/reject the dup.
	 */
	var $_choiceid = -1;
	var $_duplicateid = -1;
	var $_status = -1;
	var $_submitterid = -1;
	var $_moderatorid = -1;

	/**
	 * The original filter array.
	 */
	var $_filter_array = null;

	/**
	 * Default constructor.
	 * The Start and Number parameters indicate the position of the
	 * first row and the number of row to extract.
	 */
	function DuplicateReportListModel($page = 1, $numberRowsPerPage = -1)
	{
		$this->_page = (is_numeric($page))?$page:1;
		$this->_numberRowsPerPage = (is_numeric($numberRowsPerPage) && $numberRowsPerPage != -1)?
			$numberRowsPerPage:$GLOBALS['site']->getSetting("default_number_item_per_page");
	}

	/**
	 * Get the data.
	 */
	function _loadData()
	{
		global $user;
		global $site;

		//Get the choice list
		$query = "SELECT qapoll_choice_duplicate_report.id, qapoll_choice_duplicate_report.status, qapoll_choice_duplicate_report.choiceid, " .
			"qapoll_choice_duplicate_report.duplicateid, qapoll_choice_duplicate_report.submitterid, " .
			"qapoll_choice_duplicate_report.date, qapoll_choice_duplicate_report.moderatorid, " .
			"users.name as submittername, " .
			"qapoll_choiceorig.title as origtitle, qapoll_choiceorig.description as origdescription, " .
			"qapoll_choiceorig.totalvotes as origvotes, qapoll_choiceorig.duplicatenumber as origdupnumber, " .
			"qapoll_choiceorig.date as origdate, " .
			"qapoll_choicedup.title as duptitle, qapoll_choicedup.description as dupdescription, " .
			"qapoll_choicedup.totalvotes as dupvotes, qapoll_choicedup.duplicatenumber as dupdupnumber, " .
			"qapoll_choicedup.date as dupdate " .
			"FROM qapoll_choice_duplicate_report " . 
			"LEFT JOIN qapoll_choice AS qapoll_choiceorig ON qapoll_choice_duplicate_report.choiceid = qapoll_choiceorig.id " .
			"LEFT JOIN qapoll_choice AS qapoll_choicedup ON qapoll_choice_duplicate_report.duplicateid = qapoll_choicedup.id " .
			"LEFT JOIN users ON users.uid = qapoll_choice_duplicate_report.submitterid " .
			$this->_buildDuplicateReportListQuery_where() .
			"ORDER BY qapoll_choice_duplicate_report.date DESC " .
			"LIMIT " . $this->_numberRowsPerPage . " OFFSET " . ($this->_page - 1)*$this->_numberRowsPerPage;

		$dup_reps = it_query($query);

		//Store the result in a array
		$dup_rep_list = array();
		while ($dup_rep = db_fetch_object($dup_reps))
		{
			$dup_rep_list[] = $dup_rep;

		}

		foreach($dup_rep_list as $duprep)
		{
			$choicesolutionlist = new ChoiceSolutionListModel();
			$choicesolutionlist->setFilterParameters(array("choice_id" => $duprep->choiceid));
			$duprep->solutionsorig = $choicesolutionlist->getData();

			$choicesolutionlist = new ChoiceSolutionListModel();
			$choicesolutionlist->setFilterParameters(array("choice_id" => $duprep->duplicateid));
			$duprep->solutionsdup = $choicesolutionlist->getData();
		}

		//Mix all the data together
		$data = new stdClass();
		$data->items = $dup_rep_list;
		$data->page = $this->_page;
		$data->numberRowsPerPage = $this->_numberRowsPerPage;
		$data->rowCount = $this->_getRowCount();

		return $data;
	}

	function _buildDuplicateReportListQuery_where()
	{
		$where = array();

		//We don't show the duprep with no possibilies to merge.
		$where[] = "(qapoll_choiceorig.duplicatenumber != -1 OR qapoll_choicedup.duplicatenumber != -1) = false";


		//Add the filter conditions
		if($this->_choiceid != -1)
			$where[] = "qapoll_choice_duplicate_report.choiceid = '" . $this->_choiceid . "'";
		if($this->_duplicateid != -1)
			$where[] = "qapoll_choice_duplicate_report.duplicateid = '" . $this->_duplicateid . "'";
		if($this->_status != -1)
			$where[] = "qapoll_choice_duplicate_report.status = '" . $this->_status . "'";
		if($this->_submitterid != -1)
			$where[] = "qapoll_choice_duplicate_report.submitterid = '" . $this->_submitterid . "'";
		if($this->_moderatorid != -1)
			$where[] = "qapoll_choice_duplicate_report.moderatorid = '" . $this->_moderatorid . "'";

		if(count($where) != 0)
			return "WHERE " . implode(" AND ", $where) . " ";
		else
			return "";
	}

	/**
	 * Set the filter parameters. Giving the GET array is usually fine.
	 * It will sanitize the necessary stuff.
	 */
	function setFilterParameters($getarray)
	{
		//Save the array first.
		$this->_filter_array = $getarray;

		//Save the options
		if($getarray['choiceid'] != null && is_numeric($getarray['choiceid']))
			$this->_choiceid = $getarray['choiceid'];
		if($getarray['duplicateid'] != null && is_numeric($getarray['duplicateid']))
			$this->_duplicateid = $getarray['duplicateid'];
		if(($getarray['status'] === 0 || $getarray['status'] != null) && is_numeric($getarray['status']))
			$this->_status = $getarray['status'];
		if($getarray['submitterid'] != null && is_numeric($getarray['submitterid']))
			$this->_submitterid = $getarray['submitterid'];
		if($getarray['moderatorid'] != null && is_numeric($getarray['moderatorid']))
			$this->_moderatorid = $getarray['moderatorid'];
	}

	function _getRowCount()
	{
		$count = db_result(it_query("SELECT COUNT(*) FROM qapoll_choice_duplicate_report " .
			"LEFT JOIN qapoll_choice AS qapoll_choiceorig ON qapoll_choice_duplicate_report.choiceid = qapoll_choiceorig.id " .
			"LEFT JOIN qapoll_choice AS qapoll_choicedup ON qapoll_choice_duplicate_report.duplicateid = qapoll_choicedup.id " .
			$this->_buildDuplicateReportListQuery_where()));

		return $count;
	}

}



?>
