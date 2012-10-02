<?php

/*
Copyright (C) 2007-2008 Nicolas Deschildre <ndeschildre@gmail.com>

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




class RelationListModel extends ModelList
{

	/**
	 * PollModel related to this list.
	 */
	var $_pollModel = null;

	/**
	 * Filter parameters.
	 * url_name : Look for a relation having a given url_name. Should return only one entry max (implicit key).
	 */
	var $_url_name = null;

	/**
	 * Default constructor.
	 * Need a pollModel to know which relations we can list.
	 */
	function RelationListModel($pollModel)
	{
		$this->_pollModel = $pollModel;
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
		$query = "SELECT qapoll_poll_relation.id as relation_id, qapoll_poll_relation_category.id as relation_cat_id, " .
			"qapoll_poll_relation_category.name as relation_cat_name, " .
			"qapoll_poll_relation.name as relation_name, qapoll_poll_relation.url_name as url_name " .
			"FROM qapoll_poll_relation " . 
			"JOIN qapoll_poll_relation_category ON qapoll_poll_relation_category.id = qapoll_poll_relation.relation_category_id " .
			$this->_buildRelationListQuery_where() .
			"ORDER BY qapoll_poll_relation_category.ordering, qapoll_poll_relation.ordering, qapoll_poll_relation.name";

		$relations = it_query($query);

		//Store the result in a array
		$relation_list = array();
		while ($relation = db_fetch_object($relations))
		{
			$relation_list[] = $relation;

		}

		return $relation_list;
	}

	function _buildRelationListQuery_where()
	{
		$where = "WHERE qapoll_poll_relation_category.pollid = '" . $this->_pollModel->getData()->id . "' ";

		if($this->_url_name != null)
			$where .= "AND url_name = '" . $this->_url_name . "' ";

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

		if($getarray['url_name'] != null)
			$this->_url_name = db_escape_string($getarray['url_name']);
	}

}

?>
