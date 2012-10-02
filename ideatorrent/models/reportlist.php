<?php

/*
Copyright (C) 2008 Nicolas Deschildre <ndeschildre@ubuntu.com>

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




class ReportListModel extends ModelList
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
	 * report_type: Filter by the report type.
	 * item_type: Filter by the item type.
	 * itemid: Filter by the item id.
	 * states: The possible states of a link.
	 */
	var $_report_type = -1;
	var $_item_type = -1;
	var $_itemid = -1;
	var $_states = array(
		"deleted" => false,
		"processed" => false,
		"new" => true);

	/**
	 * Data filters. They are used to restrict the columns of the data returned.
	 * This can be very usefull when we only need something specific, and we
	 * don't want to waste processing time.
	 * include_minimal_data: Override all the below options by setting them to false.
	 * include_item_models: shall we include the models of the reported items?
	 */
	var $_include_minimal_data = false;
	var $_include_item_models = false;

	/**
	 * Default constructor.
	 * The Start and Number parameters indicate the position of the
	 * first row and the number of row to extract.
	 */
	function ReportListModel($page = 1, $numberRowsPerPage = -1)
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
		global $basemodule_url;

		//Get the choice list
		$query = "SELECT * " .
			"FROM qapoll_report " . 
			$this->_buildReportListQuery_where() .
			"ORDER BY votes DESC " .
			"LIMIT " . $this->_numberRowsPerPage . " OFFSET " . ($this->_page - 1)*$this->_numberRowsPerPage;

		$reports = it_query($query);

		//Store the result in a array
		$report_list = array();
		while ($report = db_fetch_object($reports))
		{
			//If asked, attach the models of the reported items
			if($this->_include_item_models)
			{
				if($report->item_type == ReportModel::$item_type["solution"])
				{
					$report->model = new ChoiceSolutionModel();
					$report->model->setDataFilter(array("include_user_vote" => 1));
					$report->model->setId($report->itemid);
					//Choice models where the solution is
					$report->choicelistmodel = new ChoiceListModel($GLOBALS['poll'], 1, 100000);
					$report->choicelistmodel->setDataFilter(array("include_minimal_data" => true));
					$report->choicelistmodel->setFilterParameters(array("choicesolutionid" => $report->itemid));
				}
				else if($report->item_type == ReportModel::$item_type["comment"])
				{
					$report->model = new ChoiceCommentModel();
					$report->model->setId($report->itemid);
					//Choice model where the comment is
					$report->choicemodel = new ChoiceModel();
					$report->choicemodel->setId($report->model->getData()->choiceid);
				}
				else if($report->item_type == ReportModel::$item_type["choice"])
				{
					$report->model = new ChoiceModel();
					$report->model->setId($report->itemid);
				}
			}

			$report_list[] = $report;

		}

		//Data to return 
		$data->items = $report_list;
		$data->page = $this->_page;
		$data->numberRowsPerPage = $this->_numberRowsPerPage;
		$data->rowCount = $this->_getRowCount();

		return $data;
	}

	function _buildReportListQuery_where()
	{
		$where = "WHERE true ";

		if($this->_states['deleted'] == false)
		{
			$where .= "AND status != -2 ";
		}
		if($this->_states['new'] == false)
		{
			$where .= "AND status != 1 ";
		}
		if($this->_states['processed'] == false)
		{
			$where .= "AND status != -3 ";
		}
		if($this->_report_type != -1)
			$where .= "AND report_type = '" . $this->_report_type . "' ";
		if($this->_item_type != -1)
			$where .= "AND item_type = '" . $this->_item_type . "' ";
		if($this->_itemid != -1)
			$where .= "AND itemid = '" . $this->_itemid . "' ";

		return $where;
	}

	function _getRowCount()
	{
		global $user;

		$count = db_result(it_query("SELECT COUNT(*) " . 

			"FROM qapoll_report " . 
			$this->_buildReportListQuery_where()));

		return $count;
	}

	/**
	 * Set the filter parameters. Giving the GET array is usually fine.
	 * It will sanitize the necessary stuff.
	 */
	function setFilterParameters($getarray)
	{
		//Save the array first.
		$this->_filter_array = $getarray;

		if($getarray['report_type'] != null && is_numeric($getarray['report_type']))
			$this->_report_type = $getarray['report_type'];
		if($getarray['item_type'] != null && is_numeric($getarray['item_type']))
			$this->_item_type = $getarray['item_type'];
		if($getarray['itemid'] != null && is_numeric($getarray['itemid']))
			$this->_itemid = $getarray['itemid'];


		//Save the states options
		if($getarray['state_new'] != null && is_numeric($getarray['state_new']))
			$this->_states['new'] = ($getarray['state_new'] != 0);
		if($getarray['state_deleted'] != null && is_numeric($getarray['state_deleted']))
			$this->_states['deleted'] = ($getarray['state_deleted'] != 0);
		if($getarray['state_processed'] != null && is_numeric($getarray['state_processed']))
			$this->_states['processed'] = ($getarray['state_processed'] != 0);
	}

	/**
	 * Set the data filter. This will be used to control the columns of data returned.
	 * Useful to reduce SQL processing time.
	 */
	function setDataFilter($filter_array)
	{
		//Save the filter array
		$this->_data_filter = $filter_array;

		//Override all the options: set all to false.
		if($filter_array['include_minimal_data'] != null && $filter_array['include_minimal_data'] == true)
		{
			$this->_include_minimal_data = true;
			$this->_include_user_approval_vote = false;
		}
		else
		{
			if($filter_array['include_item_models'] != null && is_numeric($filter_array['include_item_models']))
				$this->_include_item_models = $filter_array['include_item_models'];

		}

	}

}

?>
