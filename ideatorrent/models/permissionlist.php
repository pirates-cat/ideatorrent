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




class PermissionListModel extends Model
{

	/**
	 * Filter parameters.
	 * permission_names : List of permissions, separated by ", ": only output permissions having these names.
	 */
	var $_permission_names = null;

	/**
	 * Default constructor.
	 */
	function PermissionListModel()
	{

	}

	/**
	 * Get the data.
	 */
	function _loadData()
	{
		//Get the permissions list
		$query = "SELECT * " .
			"FROM qapoll_permission " . 
			$this->_buildTagListQuery_where() .
			"ORDER BY ordering";

		$permissions = it_query($query);

		//Store the result in a array
		$permission_list = array();
		while ($permission = db_fetch_object($permissions))
		{
			$permission_list[] = $permission;

		}

		return $permission_list;
	}

	function _buildTagListQuery_where()
	{
		$where = "WHERE true ";

		if($this->_permission_names != null)
			$where .= "AND name IN ('" . implode("', '", $this->_permission_names) . "') ";

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

		if($getarray['permission_names'] != null)
			$this->_permission_names = $permarray = explode(", ", db_escape_string($getarray['permission_names']));

	}

}

?>
