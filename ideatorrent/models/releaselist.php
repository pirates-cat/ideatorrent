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




class ReleaseListModel extends ModelList
{

	/**
	 * Filter parameters.
	 * old_release: Do we include release that have been marked as "old" in the DB?
	 */
	var $_old_release = true;


	/**
	 * Default constructor.
	 */
	function ReleaseListModel()
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
		$query = "SELECT qapoll_release.id, qapoll_release.small_name, qapoll_release.long_name, qapoll_release.old_release " .
			"FROM qapoll_release " . 
			$this->_buildReleaseListQuery_where() . 
			"ORDER BY ordering ";

		$releases = it_query($query);

		//Store the result in a array
		$release_list = array();
		while ($release = db_fetch_object($releases))
		{
			$release_list[] = $release;

		}

		return $release_list;
	}

	function _buildReleaseListQuery_where()
	{
		$where = "WHERE true ";

		if($this->_old_release != true)
			$where .= "AND old_release = 'f' ";

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

		if(is_numeric($getarray['old_release']))
			$this->_old_release = $getarray['old_release'];
	}

}

?>
