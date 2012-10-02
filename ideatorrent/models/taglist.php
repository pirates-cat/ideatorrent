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




class TagListModel extends ModelList
{

	/**
	 * Filter parameters.
	 * choice_id : The choice id of the tags to select. -1 means we don't care.
	 * admin : The admin value of the tags to select. -1 means we don't care.
	 * name : The name of the tags to select.
	 */
	var $_choice_id = -1;
	var $_admin = -1;
	var $_name = null;

	/**
	 * Default constructor.
	 */
	function TagListModel()
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
		$query = "SELECT qapoll_choice_tag.id, qapoll_choice_tag.name, qapoll_choice_tag.choice_id, qapoll_choice_tag.admin, " .
			"qapoll_choice_tag.user_id " .
			"FROM qapoll_choice_tag " . 
			$this->_buildTagListQuery_where() .
			"ORDER BY name ";

		$tags = it_query($query);

		//Store the result in a array
		$tag_list = array();
		while ($tag = db_fetch_object($tags))
		{
			$tag_list[] = $tag;

		}

		return $tag_list;
	}

	function _buildTagListQuery_where()
	{
		$where = "WHERE true ";

		if($this->_name != null)
			$where .= "AND name = '" . $this->_name . "' ";

		if($this->_choice_id != -1)
			$where .= "AND choice_id = '" . $this->_choice_id . "' ";

		if($this->_admin != -1)
			$where .= "AND admin = '" . $this->_admin . "' ";

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

		if($getarray['name'] != null)
			$this->_name = db_escape_string($getarray['name']);

		if($getarray['choice_id'] != null && is_numeric($getarray['choice_id']))
			$this->_choice_id = $getarray['choice_id'];

		if($getarray['admin'] !== null && is_numeric($getarray['admin']))
			$this->_admin = $getarray['admin'];
	}

}

?>
