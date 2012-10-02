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




class CategoryModel extends Model
{

	/**
	 * The category id.
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
			return db_fetch_object(it_query("SELECT * FROM qapoll_poll_category WHERE id='" . $this->_id . "'"));
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

		$this->_data->name = substr(db_escape_string($_POST['cat_name']), 0, 80);
		$this->_data->url_name = substr(db_escape_string($_POST['cat_urlname']), 0, 80);
		$this->_data->ordering = (($_POST['cat_ordering'] !== null)?$_POST['cat_ordering']:0);

		// Form validation
		if(trim($this->_data->name) == "")
			 $errorMessage .= t("Please enter a name for the category.") . "<br />";
		if(trim($this->_data->url_name) == "")
			 $errorMessage .= t("Please enter an URL name for the category.") . "<br />";
		else if(ereg("^[0-9a-zA-Z\-_]*$", $this->_data->url_name) == false)
			 $errorMessage .= t("The URL name should be composed of alphanumeric characters and '_', '-'.") . "<br />";
		if(is_numeric($this->_data->ordering) == false)
			$errorMessage .= t("The ordering value is incorrect.") . "<br />";

		//Display the errors
		if($errorMessage != "")
			$errorMessage = substr($errorMessage, 0, strlen($errorMessage) - 6);
		drupal_set_message($errorMessage, 'error_msg');


		return ($errorMessage == "");
	}


	/**
	 * Update or insert a new category in the DB.
	 * Returns true if everything was OK.
	 */
	function store()
	{
		$errorMessage = "";
		global $user;
		global $site;

		//New category
		if($this->_id == 0)
		{
			if($errorMessage == "")
			{
				it_query(
					"INSERT INTO qapoll_poll_category (pollid, name, description, ordering, url_name) " .
					"VALUES (" .
					"'" . $GLOBALS['poll']->getId() . "'," . 
					"'" . $this->_data->name . "'," .
					"'" . $this->_data->description . "'," .
					"'" . $this->_data->ordering . "'," .
					"'" . $this->_data->url_name . "')");

				//Save the id of the newly inserted choice solution
				$this->_id = db_last_insert_id("qapoll_poll_category", "id");
			}
		}
		else
		{
			$cols = array();

			if(user_access($site->getData()->adminrole))
			{
				$cols[] = "name = '" . $this->_data->name . "'";
				$cols[] = "url_name = '" . $this->_data->url_name . "'";
				$cols[] = "ordering = '" . $this->_data->ordering . "'";
			}

			if(count($cols) > 0)
				it_query("UPDATE qapoll_poll_category SET " . implode(", ", $cols) . " WHERE id = '" . $this->_id . "'");
		}

		//Display the errors
		if($errorMessage != "")
			$errorMessage = substr($errorMessage, 0, strlen($errorMessage) - 6);
		drupal_set_message($errorMessage, 'error_msg');

		return ($errorMessage == "");
	}

	/**
	 * Delete a category. Change the category of all the ideas referencing it to -1.
	 * 
	 */
	function delete()
	{
		if($this->_id == 0)
			return false;

		it_query("DELETE FROM qapoll_poll_category WHERE id=" . $this->_id);
		it_query("UPDATE qapoll_choice SET categoryid = '-1' WHERE categoryid=" . $this->_id);
	}


}

?>
