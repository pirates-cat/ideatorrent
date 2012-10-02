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




class ReleaseModel extends Model
{

	/**
	 * The release id.
	 */
	var $_id = 0;

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
	 */
	function _loadData()
	{
		if($this->_id != 0)
		{
			return db_fetch_object(it_query("SELECT * FROM qapoll_release WHERE id='" . $this->_id . "'"));
		}
		else
			return null;
	}

	/**
	 * Load an object from the _POST parameters.
	 * Return true if there was enough correct data. 
	 */
	function loadFromPost()
	{
		global $site;
		$errorMessage = "";

		$this->_data->long_name = substr(db_escape_string($_POST['release_long_name']), 0, 80);
		$this->_data->small_name = substr(db_escape_string($_POST['release_small_name']), 0, 80);
		$this->_data->old_release = (isset($_POST['release_new_release']) == false);
		$this->_data->ordering = (($_POST['release_ordering'] !== null)?$_POST['release_ordering']:0);

		// Form validation
		if(trim($this->_data->long_name) == "")
			 $errorMessage .= t("Please enter a name for the release.") . "<br />";
		if(trim($this->_data->small_name) == "")
			 $errorMessage .= t("Please enter an URL name for the release.") . "<br />";
		else if(ereg("^[0-9a-zA-Z\-_\.]*$", $this->_data->small_name) == false)
			 $errorMessage .= t("The URL name should be composed of alphanumeric characters and '_', '-', '.'.") . "<br />";
		if(is_numeric($this->_data->ordering) == false)
			$errorMessage .= t("The ordering value is incorrect.") . "<br />";

		//Display the errors
		if($errorMessage != "")
			$errorMessage = substr($errorMessage, 0, strlen($errorMessage) - 6);
		drupal_set_message($errorMessage, 'error_msg');


		return ($errorMessage == "");
	}


	/**
	 * Update or insert a new release in the DB.
	 * Returns true if everything was OK.
	 */
	function store()
	{
		$errorMessage = "";
		global $user;
		global $site;

		//New release
		if($this->_id == 0)
		{
			if($errorMessage == "")
			{
				$query = 
					"INSERT INTO qapoll_release (ordering, small_name, long_name, old_release) " .
					"VALUES (" .
					"'" . $this->_data->ordering . "'," . 
					"'" . $this->_data->small_name . "'," .
					"'" . $this->_data->long_name . "'," .
					"'" . (($this->_data->old_release)?"t":"f") . "')";
				it_query($query);

				//Save the id of the newly inserted choice solution
				$this->_id = db_last_insert_id("qapoll_release", "id");
			}
		}
		else
		{
			$cols = array();

			if(user_access($site->getData()->adminrole))
			{
				$cols[] = "small_name = '" . $this->_data->small_name . "'";
				$cols[] = "long_name = '" . $this->_data->long_name . "'";
				$cols[] = "old_release = '" . (($this->_data->old_release)?"t":"f") . "'";
				$cols[] = "ordering = '" . $this->_data->ordering . "'";
			}

			if(count($cols) > 0)
				it_query("UPDATE qapoll_release SET " . implode(", ", $cols) . " WHERE id = '" . $this->_id . "'");
		}

		//Display the errors
		if($errorMessage != "")
			$errorMessage = substr($errorMessage, 0, strlen($errorMessage) - 6);
		drupal_set_message($errorMessage, 'error_msg');

		return ($errorMessage == "");
	}

	/**
	 * Delete a release. Change the release of all the ideas referencing it to -1.
	 * 
	 */
	function delete()
	{
		if($this->_id == 0)
			return false;

		it_query("DELETE FROM qapoll_release WHERE id=" . $this->_id);
		it_query("UPDATE qapoll_choice SET release_target = '-1' WHERE release_target=" . $this->_id);
	}


	/**
	 * Get the release id from its short name. -1 if no such short name.
	 */
	static function getIdFromShortName($shortName)
	{
		$id = db_result(it_query("SELECT id FROM qapoll_release WHERE small_name='". db_escape_string($shortName) . "'"));
		return (($id != null)?$id:-1);
	}

	/**
	 * Get the id of the most recent release. -1 if no release.
	 */
	static function getIdFromMostRecentRelease()
	{
		$id = db_result(it_query("SELECT id FROM qapoll_release ORDER BY ordering DESC LIMIT 1"));
		return (($id != null)?$id:-1);
	}
}

?>
