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




class CategoryListModel extends ModelList
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
	function CategoryListModel($pollModel)
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
		$query = "SELECT * " .
			"FROM qapoll_poll_category " . 
			$this->_buildCategoryListQuery_where() .
			"ORDER BY ordering, name";

		$categorys = it_query($query);

		//Store the result in a array
		$category_list = array();
		while ($category = db_fetch_object($categorys))
		{
			$category_list[] = $category;

		}

		return $category_list;
	}

	function _buildCategoryListQuery_where()
	{
		$where = "WHERE pollid = '" . $this->_pollModel->getData()->id . "' ";

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
