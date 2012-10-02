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




class EntryPointListModel extends ModelList
{

	/**
	 * Filter parameters.
	 * entry_point_ids: A comma separated list of entry point ids we want to restrict our search to.
	 * entry_point_ids_2: A comma separated list of entry point ids we want to restrict our search to. Same than the one before. It can be used
	 * when you want to use two separate source of ids filtering.
	 */
	var $_entry_point_ids = null;
	var $_entry_point_ids_2 = null;

	/**
	 * Default constructor.
	 */
	function EntryPointListModel()
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

		//Get the tags list
		$query = "SELECT qapoll_entry_point.* " .
			"FROM qapoll_entry_point " . 
			$this->_buildTagListQuery_where() .
			"ORDER BY id";

		$entrypoints = it_query($query);

		//Store the result in a array
		$entrypoint_list = array();
		while ($entrypoint = db_fetch_object($entrypoints))
		{
			$entrypoint_list[] = $entrypoint;

		}

		return $entrypoint_list;
	}

	function _buildTagListQuery_where()
	{
		$where = "WHERE true ";

		//Use the entry_point_ids filter
		if($this->_entry_point_ids != null)
		{
			$where .= "AND qapoll_entry_point.id IN (" . $this->_entry_point_ids . ") ";
		}

		//Use the entry_point_ids_2 filter
		if($this->_entry_point_ids_2 != null)
		{
			$where .= "AND qapoll_entry_point.id IN (" . $this->_entry_point_ids_2 . ") ";
		}

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

		if($getarray['entry_point_ids'] != null)
		{
			//Check that all the given ids are indeed numeric.
			$array_ids = explode(", ", $getarray['entry_point_ids']);
			$is_all_numeric = true;
			foreach($array_ids as $id)
			{
				if(is_numeric($id) == false)
					$is_all_numeric = false;
			}
			if($is_all_numeric)
				$this->_entry_point_ids = $getarray['entry_point_ids'];
		}

		if($getarray['entry_point_ids_2'] != null)
		{
			//Check that all the given ids are indeed numeric.
			$array_ids = explode(", ", $getarray['entry_point_ids_2']);
			$is_all_numeric = true;
			foreach($array_ids as $id)
			{
				if(is_numeric($id) == false)
					$is_all_numeric = false;
			}
			if($is_all_numeric)
				$this->_entry_point_ids_2 = $getarray['entry_point_ids_2'];
		}
	}

}

?>
