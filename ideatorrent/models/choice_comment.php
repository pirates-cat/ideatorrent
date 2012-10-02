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




class ChoiceCommentModel extends Model
{


	/**
	 * ChoiceComment id.
	 */
	var $_id = 0;

	/**
	 * The ChoiceModel corresponding to this object.
	 */
	var $_choiceModel = null;

	function ChoiceCommentModel($choiceModel = null)
	{
		$this->_choiceModel = $choiceModel;
	}

	function setId($id)
	{
		if(is_numeric($id))
		{
			$this->_id = $id;
			$_data = null;
		}
	}

	/**
	 * Load the data.
	 */
	function _loadData()
	{
		if($this->_id != 0)
		{
			$query = "SELECT qapoll_choice_comment.id, qapoll_choice_comment.status, " .
				"qapoll_choice_comment.date, qapoll_choice_comment.choiceid, " . 
				"qapoll_choice_comment.comment, users.name as username " .
				"FROM qapoll_choice_comment " . 
				"LEFT JOIN users ON users.uid = qapoll_choice_comment.userid " .
				"WHERE id='" . $this->_id . "'";

			return db_fetch_object(it_query($query));
		}
		else
			return null;
	}

	/**
	 * Load an object from the _POST parameters.
	 * Return true if there was enough correct data. 
	 * Needs $this->_choiceModel to know to which choice to link the new comment
	 * for its compulsory fields.
	 */
	function loadFromPost()
	{
		$errorMessage = "";

		if($this->_choiceModel == null)
			return false;

		$this->_data->choiceid = $this->_choiceModel->getData()->id;
		$this->_data->comment = substr(db_escape_string($_POST['commennt_text']), 0, 5000);

		// Form validation

		//Should not occur.
		if(!is_numeric($this->_data->choiceid)) 
			$errorMessage .= "Internal error. Please contact an administrator.<br />";

		if(trim($this->_data->comment) == "")
			 $errorMessage .= "Please enter a comment.<br />";

		//Display the errors
		if($errorMessage != "")
			$errorMessage = substr($errorMessage, 0, strlen($errorMessage) - 6);
		drupal_set_message($errorMessage, 'error_msg');


		return ($errorMessage == "");
	}

	/**
	 * Update or insert a new comment into the BD.
	 * When updating, it takes care to update only what the user is allowed to.
	 * Returns true if everything was OK.
	 */
	function store()
	{
		$errorMessage = "";
		global $user;

		//New comment
		if($this->_id == 0)
		{
			if($errorMessage == "")
			{
				it_query(
					"INSERT INTO qapoll_choice_comment (status, choiceid, userid, date, comment) " .
					"VALUES (" .
					"'0'," . 
					"'" . $this->_data->choiceid . "'," .
					"'" . $user->uid . "'," .
					"'" . date("Y-m-d H:i:s") . "'," .
					"'" . $this->_data->comment . "')");
				it_query("UPDATE qapoll_choice " .
					"SET commentscount = commentscount + 1, last_comment_date='NOW()' " .
					"WHERE id = '" . $this->_data->choiceid . "'");

				//Saving the change in the logs.
				//Also save the comment, since we want to show how the comment look like
				//at this precise moment.
				ChoiceLogModel::log($this->_data->choiceid, ChoiceLogModel::$change["comment_added"], "", $this->_data->comment);

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

	/**
	 * Delete a comment. It will in fact set its status to -1, we keep everything in BDD.
	 * Needs $this->_id.
	 */
	function delete()
	{
		if($this->_id == 0)
			return false;

		$result = $this->_setStatus(-1);

		if($result)
		{
			it_query("UPDATE qapoll_choice " .
				"SET commentscount = commentscount - 1 " .
				"WHERE id = (SELECT choiceid FROM qapoll_choice_comment WHERE id='" . $this->_id . "')");

			//Saving the change in the logs.
			//Also save the comment, since we want to show how the comment look like
			//at this precise moment.
			ChoiceLogModel::log($this->_data->choiceid, ChoiceLogModel::$change["comment_deleted"], $this->getData()->comment, "");
		}

		return $result;
	}

	/**
	 * Change the status of the comment.
	 * Needs $this->_id.
	 */
	function _setStatus($value)
	{
		if($this->_id == 0)
			return false;

		if($this->getData()->status == $value)
			return false;

		it_query("UPDATE qapoll_choice_comment SET status='$value' WHERE id='" . $this->_id . "'");

		return true;
	}

}



?>
