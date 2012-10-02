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




class EntryPointModel extends Model
{
	/**
	 * Entry_point id.
	 */
	var $_id = 0;

	/**
	 * Entry point primary key. (Even if the real one is id.)
	 */
	var $_pollid = 0;
	var $_url_name = null;

	/**
	 * Set the primary key.
	 */
	function setPrimaryKey($pollid, $urlname = "")
	{
		if(is_numeric($pollid))
		{
			$this->_pollid = $pollid;
			$this->_url_name = htmlentities($urlname, ENT_QUOTES, "UTF-8");
			$_data = null;
		}
	}

	/**
	 * Load the data.
	 */
	function _loadData()
	{
		if($this->_pollid != 0)
		{
			//Get the entrypoint from DB
			//If we have a entrypoint name, look for it
			if($this->_url_name != null)
			{
				$query = "SELECT * FROM qapoll_entry_point " .
					"WHERE pollid='" . $this->_pollid . "' AND url_name='" . $this->_url_name . "'";
				$entrypoint = db_fetch_object(it_query($query));
			}
			else
			{
				//Otherwise, by default, we take the first available entrypoint
				$query = "SELECT * FROM qapoll_entry_point " .
					"WHERE pollid='" . $this->_pollid . "' ORDER BY id LIMIT 1";
				$entrypoint = db_fetch_object(it_query($query));
			}
			


			//Make a nice array from the filter column
			if(!empty($entrypoint))
				$entrypoint->filterArray = generate_GET_array($entrypoint->filter);
			
			//Save the id.
			$this->_id = $entrypoint->id;

			return $entrypoint;
		}
		else
			return null;
	}

	/**
	 * Save the description stored in the POST query.
	 * Of course, an poll must be loaded ($this->_data->id != 0)
	 */
	function saveDescriptionInPost()
	{
		if($this->_data->id == 0)
			return false;

		$this->_data->desc = substr($_POST['entrypoint_desc'], 0, 1000);
		$query = "UPDATE qapoll_entry_point SET description = '" . db_escape_string($_POST['entrypoint_desc']) .
			"' WHERE id=" . $this->_data->id;
		it_query($query);

		return true;
	}


	/**
	 * This function is a callback. DO NOT CALL DIRECTLY!
	 * It is used by UserModel::_hasPermission to determine if the filtered permisssions allows
	 * the model number $model_id to have the permission $perm_name in THIS instance.
	 */
	function _callback_hasFilteredPermissions($perm_name, $model_id, $filtered_perms)
	{
		//If there is no id set to this idea, return false.
		if($this->_id == 0)	
			return false;

		//If there is no filtered perms, just skip and return false
		if($filtered_perms == null)
			return false;

		//Check if the perm data was already fetched.
		if($this->_perms_cache == null)
		{
			//Create a new ChoiceListModel, set the ids, set the filters of the perms
			//and see which ones match the filters
			$ids_param = $this->_id;
			foreach($filtered_perms as $filter => $perm)
			{
				$entrypointlist = new EntryPointListModel();
				$entrypointlist->setFilterParameters(generate_GET_array($filter . "&entry_point_ids_2=" . $ids_param));
				$entrypointlist->setDataFilter(array("include_minimal_data" => true));
				$filteredlist = $entrypointlist->getData();

				foreach($filteredlist as $filtereditem)
				{
					if(isset($this->_perms_cache[$filtereditem->id]) == false)
						$this->_perms_cache[$filtereditem->id] = array();
					$this->_perms_cache = array_merge($this->_perms_cache, $perm);
				}
			}


		}
		
		if(isset($this->_perms_cache))
			return $this->_perms_cache["$perm_name"];
		else
			return false;
	}

}

?>
