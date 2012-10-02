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




class RelationSubcategoryListModel extends ModelList
{

	/**
	 * Filter parameters.
	 * relation_id : The relation id of the relationsubcats to fetch. -1 means we don't care.
	 * url_name : Look for a relation having a given url_name. Should return only one entry max (implicit key).
	 */
	var $_relation_id = -1;
	var $_url_name = null;

	/**
	 * Default constructor.
	 */
	function RelationSubcategoryListModel()
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

		//Get the relation subcat list
		$query = "SELECT * " .
			"FROM qapoll_poll_relation_subcategory " . 
			$this->_buildRelationSubcategoryListQuery_where() .
			"ORDER BY ordering, name";

		$relationsubcats = it_query($query);

		//Store the result in a array
		$relationsubcat_list = array();
		while ($relationsubcat = db_fetch_object($relationsubcats))
		{
			$relationsubcat_list[] = $relationsubcat;

		}

		return $relationsubcat_list;
	}

	function _buildRelationSubcategoryListQuery_where()
	{
		$where = "WHERE true ";

		if($this->_relation_id != -1)
			$where .= "AND relationid = '" . $this->_relation_id . "' ";

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
		$this->_filter_array = $getarray;;

		if($getarray['relation_id'] != null && is_numeric($getarray['relation_id']))
			$this->_relation_id = $getarray['relation_id'];

		if($getarray['url_name'] != null)
			$this->_url_name = db_escape_string($getarray['url_name']);
	}

}

?>
