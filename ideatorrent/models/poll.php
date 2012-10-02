<?php

/*
Copyright (C) 2007 Nicolas Deschildre <ndeschildre@gmail.com>

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




class PollModel extends Model
{
	/**
	 * The SiteModel object related to this model.
	 */
	var $_site = null;

	/**
	 * The list of categories linked to this poll.
	 */
	var $_categories = null;

	/**
	 * The poll id.
	 */
	var $_id = 0;

	/**
	 * Default constructor. 
	 */
	function PollModel($site = null)
	{
		$_site = $site;
	}

	function setId($id)
	{
		if(is_numeric($id))
		{
			$this->_id = $id;
			$_data = null;
		}
	}

	function getId()
	{
		return $this->_id;
	}


	/**
	 * Load the data.
	 * $this->_id is required.
	 */
	function _loadData()
	{
		if($this->_id != 0)
		{
			return db_fetch_object(it_query("SELECT * FROM qapoll_poll WHERE id='" . $this->_id . "'"));
		}
		else
			return null;
	}

	/**
	 * Get the list of categories linked to this poll.
	 * Require $this->_id.
	 */
	function getCategoryList()
	{
		//Check data
		if($this->_id == 0)
			return null;

		//If already extracted, return it directly
		if($this->_categories != null)
			return $this->_categories;

		$categories = it_query("SELECT * FROM qapoll_poll_category WHERE pollid='" . $this->_id . "' ".
			"ORDER BY ordering, name");

		//Store the result in a array
		$categoryList = array();
		while ($categorydata = db_fetch_object($categories))
			$categoryList[] = $categorydata;

		//Save it for future use
		$this->_categories = $categoryList;

		return $categoryList;
	}

	/**
	 * Returns if a category id does belong to this poll.
	 */
	function ownCategoryId($cat_id)
	{
		$catlist = $this->getCategoryList();

		for($i = 0; $i < count($catlist); $i++)
		{
			if($cat_id == $catlist[$i]->id)
				return true;
		}

		return false;
	}

}

?>
