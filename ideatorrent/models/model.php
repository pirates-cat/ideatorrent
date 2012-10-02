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




class Model
{

	/**
	 * Data array.
	 */
	var $_data = null;

	/**
	 * Stats array.
	 */
	var $_stats = null;


	/**
	 * The permissions data will be stored here.
	 */
	var $_perms_cache = null;

	/**
	 * Initialize the model with the url parameters.
	 */
	function init($page, $param1, $param2, $param3, $param4)
	{
		//Override
	}

	/**
	 * Method to get the data of the Model.
	 */
	function getData()
	{
		if($this->_data == null)
			$this->_data = $this->_loadData();

		return $this->_data;
	}

	/**
	 * Method to retrieve data. To override.
	 */
	function _loadData()
	{
		//Override.
		return null;
	}

	/**
	 * Method to get the stats of the Model.
	 * WARNING: usually heavy in SQL processing time.
	 */
	function getStats()
	{
		if($this->_stats == null)
			$this->_stats = $this->_loadstats();

		return $this->_stats;
	}

	/**
	 * Method to retrieve stats. To override optionnally.
	 */
	function _loadStats()
	{
		//Override.
		return null;
	}

	/**
	 *
	 */
	function setData($data)
	{
		$this->_data = $data;
	}

	/**
	 * Load an object from the _POST parameters.
	 * Return true if there was enough correct data 
	 * for its compulsory fields.
	 */
	function loadFromPost()
	{
		//Override
		return false;
	}

	/**
	 * Update or create a new object in the DB.
	 * Return true if everything was OK.
	 */
	function store()
	{
		//Override
		return false;
	}

	/**
	 * This function is a callback. DO NOT CALL DIRECTLY!
	 * It is used by UserModel::_hasPermission to determine if the filtered permisssions allows
	 * the model number $model_id to have the permission $perm_name in THIS instance.
	 * If $model_id is not in this instance, it return false.
	 */
	function _callback_hasFilteredPermissions($perm_name, $model_id, $filtered_perms)
	{
		return false;
	}

	/**
	 * This function is a callback. DO NOT CALL DIRECTLY!
	 * It is used by UserModel::_hasPermission to determine if the user user_id is
	 * the owner of the $model_id object.
	 * If $model_id is not in this instance, it return false.
	 */
	function _callback_isOwner($model_id, $user_id)
	{
		return false;
	}

}

?>
