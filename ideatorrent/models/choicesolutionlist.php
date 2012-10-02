<?php

/*
Copyright (C) 2008 Nicolas Deschildre <ndeschildre@gmail.com>

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




class ChoiceSolutionListModel extends ModelList
{


	/**
	 * Filter parameters.
	 * choice_id: Let's get the choice solution related to a given choice id.
	 * choice_relation_relative_number : Let's filter by the choice relative number.
	 * duplicate_of : Let's show the solutions which are the duplicates of the given number.
	 * 	Since the dup are not shown by default, it's best to set state_duplicate to true :)
	 * userid : Filter by the author of the solution
	 * link_states: Filter by the state of the links.
	 * states: Filter by the state of the solution.
	 * ordering : The ordering to use.
	 */
	var $_choice_id = -1;
	var $_choice_relation_relative_number = -1;
	var $_duplicate_of = -2;
	var $_userid = -1;
	var $_link_states = array(
		"deleted" => false,
		"new" => true);
	var $_states = array(
		"deleted" => false,
		"duplicate" => false,
		"new" => true);
	var $_ordering = "solutionumber";

	/**
	 * Data filters. They are used to restrict the columns of the data returned.
	 * This can be very usefull when we only need something specific, and we
	 * don't want to waste processing time.
	 * include_minimal_data: Override all the below options by setting them to false.
	 * include_user_bookmark: shall we include the user vote?
	 */
	var $_include_minimal_data = false;
	var $_include_user_vote = true;


	/**
	 * Default constructor.
	 */
	function ChoiceSolutionListModel()
	{
	}

	/**
	 * Get the data.
	 */
	function _loadData()
	{

		//Get the choice solution list
		$query = "SELECT qapoll_choice_solution.*, qapoll_choice_solution_link.solution_number, " .

			//Get the user vote only if logged.
			(($GLOBALS['user']->uid != null && $this->_include_user_vote == true)?"COALESCE(qapoll_vote.value, -2) as myvote, ":"") .

			"qapoll_choice_solution_link.choiceid, qapoll_choice_solution_link.advertize, " .
			"qapoll_choice_solution_link.status, qapoll_choice_solution_link.selected, users.name as username " .
			"FROM qapoll_choice_solution " . 
			"JOIN qapoll_choice_solution_link ON qapoll_choice_solution.id = qapoll_choice_solution_link.choicesolutionid " .
			"LEFT JOIN users ON users.uid = qapoll_choice_solution.userid " .

			//Get the user vote only if logged.
			(($GLOBALS['user']->uid != null && $this->_include_user_vote == true)?
				"LEFT JOIN qapoll_vote ON qapoll_vote.choicesolutionid = qapoll_choice_solution.id AND qapoll_vote.userid = " .
				$GLOBALS['user']->uid . " ":"") .

			$this->_buildChoiceSolutionListQuery_where() .
			$this->_buildChoiceSolutionListQuery_orderby();

		$choicesolutions = it_query($query);

		//Store the result in a array
		$choicesolution_list = array();
		while ($choicesolution = db_fetch_object($choicesolutions))
		{
			$choicesolution_list[] = $choicesolution;

		}

		return $choicesolution_list;
	}

	function _buildChoiceSolutionListQuery_where()
	{
		$where = "WHERE true ";

		if($this->_states['deleted'] == false)
		{
			$where .= "AND qapoll_choice_solution.status != -2 ";
		}
		if($this->_states['new'] == false)
		{
			$where .= "AND qapoll_choice_solution.status != 1 ";
		}
		if($this->_states['duplicate'] == false)
		{
			$where .= "AND qapoll_choice_solution.status != -1 ";
		}

		if($this->_link_states['deleted'] == false)
		{
			$where .= "AND qapoll_choice_solution_link.status != -2 ";
		}
		if($this->_link_states['new'] == false)
		{
			$where .= "AND qapoll_choice_solution_link.status != 1 ";
		}

		if($this->_userid != -1)
			$where .= "AND qapoll_choice_solution.userid = '" . $this->_userid . "' ";
		if($this->_choice_id != -1)
			$where .= "AND qapoll_choice_solution_link.choiceid = '" . $this->_choice_id . "' ";
		if($this->_choice_relation_relative_number != -1)
			$where .= "AND qapoll_choice_solution_link.solution_number = '" . $this->_choice_relation_relative_number . "' ";
		if($this->_duplicate_of != -2)
			$where .= "AND qapoll_choice_solution.duplicate_choice_solution_id = '" . $this->_duplicate_of . "' ";


		return $where;
	}

	function _buildChoiceSolutionListQuery_orderby()
	{

		switch($this->_ordering)
		{
			case "solutionumber":
				$orderby = "qapoll_choice_solution_link.solution_number ";
			break;

			case "selectedfirst":
				$orderby = "qapoll_choice_solution_link.selected DESC, qapoll_choice_solution_link.solution_number ";
			break;

			default:
				$orderby = "qapoll_choice_solution_link.solution_number ";
			break;
		}

		return ($orderby != null)?"ORDER BY " . $orderby:"";
	}

	/**
	 * Set the filter parameters. Giving the GET array is usually fine.
	 * It will sanitize the necessary stuff.
	 */
	function setFilterParameters($getarray)
	{
		//Save the array first.
		$this->_filter_array = $getarray;

		if($getarray['choice_id'] != null && is_numeric($getarray['choice_id']))
			$this->_choice_id = $getarray['choice_id'];
		if($getarray['choice_relation_relative_number'] != null && is_numeric($getarray['choice_relation_relative_number']))
			$this->_choice_relation_relative_number = $getarray['choice_relation_relative_number'];
		if($getarray['duplicate_of'] != null && is_numeric($getarray['duplicate_of']))
			$this->_duplicate_of = $getarray['duplicate_of'];

		if($getarray['userid'] != null && is_numeric($getarray['userid']))
			$this->_userid = $getarray['userid'];


		//Save the states options
		if($getarray['state_new'] != null && is_numeric($getarray['state_new']))
			$this->_states['new'] = ($getarray['state_new'] != 0);
		if($getarray['state_deleted'] != null && is_numeric($getarray['state_deleted']))
			$this->_states['deleted'] = ($getarray['state_deleted'] != 0);
		if($getarray['state_duplicate'] != null && is_numeric($getarray['state_duplicate']))
			$this->_states['duplicate'] = ($getarray['state_duplicate'] != 0);

		//Save the link states options
		if($getarray['state_link_new'] != null && is_numeric($getarray['state_link_new']))
			$this->_link_states['new'] = ($getarray['state_link_new'] != 0);
		if($getarray['state_link_deleted'] != null && is_numeric($getarray['state_link_deleted']))
			$this->_link_states['deleted'] = ($getarray['state_link_deleted'] != 0);

		if($getarray['ordering'] != null)
			$this->_ordering = $getarray['ordering'];

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
			$this->_include_user_vote = false;
		}
		else
		{
			if($filter_array['include_user_vote'] != null && is_numeric($filter_array['include_user_vote']))
				$this->_include_user_vote = $filter_array['include_user_vote'];

		}

	}


	/**
	 * Delete the fetched entries. Set status = -1 and update comment count.
	 */
	function deleteEntries()
	{
		$entries_id = array();
		$entries = $this->getData();

		foreach($entries as $entry)
		{
			$choicesolution = new ChoiceSolutionModel();
			$choicesolution->setId($entry->id);
			$choicesolution->delete();
		}

		it_query($query);
	}



	/**
	 * This function is a callback. DO NOT CALL DIRECTLY!
	 * It is used by UserModel::_hasPermission to determine if the user user_id is
	 * the owner of the $model_id object.
	 * If $model_id is not in this instance, it return false.
	 */
	function _callback_isOwner($model_id, $user_id)
	{
		//If there is no filter, the model is probably not initialized yet. => Design error.
		//Return false, and 
		if($this->_filter_array == null)
		{
			drupal_set_message("Probable design error while getting user permissions. " . 
				"Please see ChoiceSolutionListModel::_callback_isOwner", 'notice_msg');
			return false;
		}

		//Check if user_id is the owner of model_id
		$data = $this->getData();
		foreach($data as $item)
		{
			if($item->id == $model_id)
				return ($item->userid == $user_id);
		}

		return false;
	}

}

?>
