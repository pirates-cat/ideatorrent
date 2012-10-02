<?php

/*
Copyright (C) 2008 Nicolas Deschildre <ndeschildre@ubuntu.com>

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




class ChoiceSolutionLinkModel extends Model
{

	/**
	 * The releation id.
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
			return db_fetch_object(it_query("SELECT * FROM qapoll_choice_solution_link WHERE id='" . $this->_id . "'"));
		}
		else
			return null;
	}


	/**
	 * Update or insert a new choice solution link into the BD.
	 * Returns true if everything was OK.
	 */
	function store()
	{
		$errorMessage = "";
		global $user;

		//New link
		if($this->_id == 0)
		{
			if($errorMessage == "")
			{
				//Get the solution number we gonna use.
				$query = "SELECT COALESCE(MAX(solution_number), 0) FROM qapoll_choice_solution_link " .
				"WHERE choiceid = " . $this->_data->choiceid;
				$this->_data->solution_number = db_result(it_query($query)) + 1;

				it_query(
					"INSERT INTO qapoll_choice_solution_link (choiceid, choicesolutionid, userid, solution_number) " .
					"VALUES (" . 
					$this->_data->choiceid . ", " . 
					"'" . $this->_data->choicesolutionid . "'," .
					"'" . $user->uid . "'," .
					"'" . $this->_data->solution_number . "')");

				//Save the id of the newly inserted choice solution
				$this->_id = db_last_insert_id("qapoll_choice_solution_link", "id");
			}
		}
		else
		{
			//TODO
		}

		//Display the errors
		if($errorMessage != "")
			$errorMessage = substr($errorMessage, 0, strlen($errorMessage) - 6);
		drupal_set_message($errorMessage, 'error_msg');

		return ($errorMessage == "");
	}

	function setChoiceSolutionId($choicesol)
	{
		if(is_numeric($choicesol))
			$this->_data->choicesolutionid = $choicesol;
	}

	function setChoiceId($choice)
	{
		if(is_numeric($choice))
			$this->_data->choiceid = $choice;
	}

	/**
	 * Delete this link: status = -2
	 */
	function delete()
	{
		if($this->_id == 0)
			return false;

		$query = "UPDATE qapoll_choice_solution_link SET status=-2 " .
			"WHERE id=" . $this->_id;
		it_query($query);

		return true;
	}

	/**
	 * Select or unselect a solution.
	 */
	function select($select = 1)
	{
		if($this->_id == 0 || ($select != 0 && $select != 1))
			return false;

		$query = "UPDATE qapoll_choice_solution_link SET selected=" . $select . " " .
			"WHERE id=" . $this->_id;
		it_query($query);

		return true;
	}

	/**
	 * Set the status of a link
	 */
	function setStatus($status)
	{
		//TODO: do we need to use it?
	}

}

?>
