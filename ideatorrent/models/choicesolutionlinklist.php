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




class ChoiceSolutionLinkListModel extends ModelList
{


	/**
	 * Filter parameters.
	 * choiceid: Filter by choice id
	 * choicesolution: Filter by choicesolution id.
	 * states: The possible states of a link.
	 */
	var $_choiceid = -1;
	var $_choicesolutionid = -1;
	var $_states = array(
		"deleted" => false,
		"new" => true);

	/**
	 * Default constructor.
	 */
	function RelationListModel()
	{
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
			"FROM qapoll_choice_solution_link " . 
			$this->_buildRelationListQuery_where() .
			"ORDER BY solution_number";

		$choicesolutionlinks = it_query($query);

		//Store the result in a array
		$choicesolutionlink_list = array();
		while ($choicesolutionlink = db_fetch_object($choicesolutionlinks))
		{
			$choicesolutionlink_list[] = $choicesolutionlink;

		}

		return $choicesolutionlink_list;
	}

	function _buildRelationListQuery_where()
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
		if($this->_choiceid != -1)
			$where .= "AND choiceid = '" . $this->_choiceid . "' ";
		if($this->_choicesolutionid != -1)
			$where .= "AND choicesolutionid = '" . $this->_choicesolutionid . "' ";

		return $where;
	}

	/**
	 * Set the filter parameters. Giving the GET array is usually fine.
	 * It will sanitize the necessary stuff.
	 */
	function setFilterParameters($getarray)
	{
		//Save the array first.
		$this->_filter_array = $getarray;

		if($getarray['choiceid'] != null && is_numeric($getarray['choiceid']))
			$this->_choiceid = $getarray['choiceid'];
		if($getarray['choicesolutionid'] != null && is_numeric($getarray['choicesolutionid']))
			$this->_choicesolutionid = $getarray['choicesolutionid'];

		//Save the states options
		if($getarray['state_new'] != null && is_numeric($getarray['state_new']))
			$this->_states['new'] = ($getarray['state_new'] != 0);
		if($getarray['state_deleted'] != null && is_numeric($getarray['state_deleted']))
			$this->_states['deleted'] = ($getarray['state_deleted'] != 0);
	}

	/**
	 * Delete the fetched entries. Set status = -2.
	 */
	function deleteEntries()
	{
		$entries_id = array();
		$entries = $this->getData();

		foreach($entries as $entry)
			$entries_id[] = $entry->id;

		$query = "UPDATE qapoll_choice_solution_link SET status=-2 " .
			"WHERE id IN (" . implode(", ", $entries_id) . ")";

		it_query($query);
	}

}

?>
