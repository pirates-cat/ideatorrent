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




class ChoiceSolutionModel extends Model
{

	/**
	 * The releation id.
	 */
	var $_id = 0;


	/**
	 * List of idea solution status constants. It contains only the symbolic names and their corresponding id, not the names to display.
	 */
	static public $choice_status = array(
		"deleted" => -2,
		"duplicate" => -1,
		"new" => 1
		);

	/**
	 * Data filters. They are used to restrict the columns of the data returned.
	 * This can be very usefull when we only need something specific, and we
	 * don't want to waste processing time.
	 * include_minimal_data: Override all the below options by setting them to false.
	 * include_user_bookmark: shall we include the user vote?
	 */
	var $_include_minimal_data = false;
	var $_include_user_vote = false;



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
			$query = "SELECT qapoll_choice_solution.*, " .

			//Get the user vote only if logged.
			(($GLOBALS['user']->uid != null && $this->_include_user_vote == true)?"COALESCE(qapoll_vote.value, -2) as myvote, ":"") .

			"users.name as username " .
			"FROM qapoll_choice_solution " . 
			"LEFT JOIN users ON users.uid = qapoll_choice_solution.userid " .

			//Get the user vote only if logged.
			(($GLOBALS['user']->uid != null && $this->_include_user_vote == true)?
				"LEFT JOIN qapoll_vote ON qapoll_vote.choicesolutionid = qapoll_choice_solution.id AND qapoll_vote.userid = " .
				$GLOBALS['user']->uid . " ":"") .

			"WHERE qapoll_choice_solution.id='" . $this->_id . "'";

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
	 * @param edit WARNING: dirty hack. When editing as a user, the title is disabled.
	 * Thus, when submitting, his field will be empty (even if there was some text on it).
	 * Handle that by specifying if we are currently editing an item or not.
	 */
	function loadFromPost($edit = false)
	{
		global $site;
		$errorMessage = "";

		$this->_data->title = substr(db_escape_string(
			(($_POST['solution_title'] != null)?$_POST['solution_title']:$_POST['solution_title-' . $this->_id])), 0, 80);
		$this->_data->description = substr(db_escape_string(
			(($_POST['solution_text'] != null)?$_POST['solution_text']:$_POST['solution_text-' . $this->_id])), 0, 5000);

		// Form validation
		if(trim($this->_data->title) == "" && ($edit == false || (user_access($site->getData()->adminrole) || user_access($site->getData()->moderatorrole))))
			 $errorMessage .= t("Please enter a title for your solution.") . "<br />";
		if(trim($this->_data->description) == "" && ($edit == false || (user_access($site->getData()->adminrole) || user_access($site->getData()->moderatorrole))))
			 $errorMessage .= t("Please enter a description for your solution.") . "<br />";

		//Display the errors
		if($errorMessage != "")
			$errorMessage = substr($errorMessage, 0, strlen($errorMessage) - 6);
		drupal_set_message($errorMessage, 'error_msg');


		return ($errorMessage == "");
	}

	/**
	 * Update or insert a new choice solution into the BD. Permissions are checked outside for submission and
	 * inside for edition (for field by field permission).
	 * $choicemodel: For solution edition, need the choicemodel we are working with to check for permissions.
	 * Returns true if everything was OK.
	 */
	function store($choicemodel = null)
	{
		$errorMessage = "";
		global $user;
		global $site;

		//New comment
		if($this->_id == 0)
		{
			if($errorMessage == "")
			{
				it_query(
					"INSERT INTO qapoll_choice_solution (status, title, description, userid, date) " .
					"VALUES (" .
					"'1'," . 
					"'" . $this->_data->title . "'," .
					"'" . $this->_data->description . "'," .
					"'" . $user->uid . "'," .
					"NOW())");

				//Save the id of the newly inserted choice solution
				$this->_id = db_last_insert_id("qapoll_choice_solution", "id");
			}
		}
		else
		{
			//We get the current values.
			$current_stored_solution = new ChoiceSolutionModel();
			$current_stored_solution->setId($this->_id);

			$cols = array();

			if(UserModel::currentUserHasPermission("edit_solution", "Choice", $choicemodel) ||
				UserModel::currentUserHasPermission("edit_solution", "ChoiceSolution", $this))
			{
				$cols[] = "title = '" . $this->_data->title . "'";
				$cols[] = "description = '" . $this->_data->description . "'";

				//Saving the change in the logs
				ChoiceLogModel::solutionLog($this->_id, ChoiceLogModel::$change["solution_title"], 
					$current_stored_solution->getData()->title, $this->_data->title);
				ChoiceLogModel::solutionLog($this->_id, ChoiceLogModel::$change["solution_description"], 
					$current_stored_solution->getData()->description, $this->_data->description);
			}

			if(count($cols) > 0)
				it_query("UPDATE qapoll_choice_solution SET " . implode(", ", $cols) . " WHERE id = '" . $this->_id . "'");
		}

		//Display the errors
		if($errorMessage != "")
			$errorMessage = substr($errorMessage, 0, strlen($errorMessage) - 6);
		drupal_set_message($errorMessage, 'error_msg');

		return ($errorMessage == "");
	}

	/**
	 * Set the data filter. This will be used to control the columns of data returned.
	 * Useful to reduce SQL processing time.
	 */
	function setDataFilter($filter_array)
	{
		//Save the filter array
		$this->_data_filter = $filter_array;

		//Override all the options: set all to false.
		if($filter_array['include_minimal_data'] != null && $filter_array['include_minimal_data'] == true)
		{
			$this->_include_minimal_data = true;
			$this->_include_user_vote = false;
		}
		else
		{
			if($filter_array['include_user_vote'] != null && is_numeric($filter_array['include_user_vote']))
				$this->_include_user_vote = $filter_array['include_user_vote'];

		}

	}

	/**
	 * Returns the id of the link if a link exists between this solution and the choice given in parameter.
	 * DO NOT return a id of a deleted (but still stored) link.
	 */
	function linkExists($choiceid)
	{
		if($this->_id == 0 || !is_numeric($choiceid))
			return false;

		$choicesolutionlinklist = new ChoiceSolutionLinkListModel();
		$choicesolutionlinklist->setFilterParameters(array("choiceid" => $choiceid, "choicesolutionid" => $this->_id));

		$result = 0;
		$links = $choicesolutionlinklist->getData();
		if(count($links) != 0)
			$result = $links[0]->id; 

		return $result;
	}

	/**
	 * Link this solution to an idea
	 */
	function linkToChoice($choiceid)
	{
		if($this->_id == 0 || !is_numeric($choiceid))
			return false;

		if($this->linkExists($choiceid) != 0)
		{
			//Do nothing.
			return false;
		}
		else
		{
			$choicesollink = new ChoiceSolutionLinkModel();
			$choicesollink->setChoiceSolutionId($this->_id);
			$choicesollink->setChoiceId($choiceid);
			$choicesollink->store();

			//Saving the change in the logs.
			//Also save the solution title and solution description, separated by "<>", since we want to show how the solution look like
			//at this precise moment.
			ChoiceLogModel::solutionLog($this->_id, ChoiceLogModel::$change["solution_linked"], -1, $choiceid,
				"", str_replace("<>", "", $this->getData()->title) . "<>" . str_replace("<>", "", $this->getData()->description));
		}

		return true;
	}

	/**
	 * Delete a link between this solution and the given choice id
	 */
	function delete_choice_link($choiceid)
	{
		if($this->_id == 0 || !is_numeric($choiceid))
			return false;

		$choicesolutionlinklist = new ChoiceSolutionLinkListModel();
		$choicesolutionlinklist->setFilterParameters(array("choiceid" => $choiceid, "choicesolutionid" => $this->_id));

		$links = $choicesolutionlinklist->getData();
		if(count($links) != 0)
		{
			//Saving the change in the logs, before the change is done (so that it is on the logs of $choiceid)
			ChoiceLogModel::solutionLog($this->_id, ChoiceLogModel::$change["solution_unlinked"], $choiceid, -1,
				str_replace("<>", "", $this->getData()->title) . "<>" . str_replace("<>", "", $this->getData()->description));

			$choicesol = new ChoiceSolutionLinkModel();
			$choicesol->setId($links[0]->id);
			$choicesol->delete();
		}

		//if there is no more links, delete this orphan solution
		$choicesolutionlinklist = new ChoiceSolutionLinkListModel();
		$choicesolutionlinklist->setFilterParameters(array("choicesolutionid" => $this->_id));

		if(count($choicesolutionlinklist->getData()) == 0)
			$this->delete();
	}

	/**
	 * Remove all the links and delete this solution (status = -2).
	 */
	function delete()
	{
		if($this->_id == 0)
			return false;

		$choicesolutionlinklist = new ChoiceSolutionLinkListModel();
		$choicesolutionlinklist->setFilterParameters(array("choicesolutionid" => $this->_id));
		$links = $choicesolutionlinklist->getData();
		if(count($links) != 0)
			$choicesolutionlinklist->deleteEntries();

		$query = "UPDATE qapoll_choice_solution SET status=-2 " .
			"WHERE id=" . $this->_id;
		it_query($query);
	}

	/**
	 * Set this solution status
	 */
	function setStatus($status)
	{
		if($this->_id == 0 || !is_numeric($status))
			return false;

		$query = "UPDATE qapoll_choice_solution SET status=" . $status . " " .
			"WHERE id=" . $this->_id;
		it_query($query);
	}



	/**
	 * Get this solution status
	 */
	function getStatus()
	{
		if($this->_id == 0)
			return false;

		$query = "SELECT status FROM qapoll_choice_solution " .
			"WHERE id=" . $this->_id;
		return db_result(it_query($query));
	}

	/**
	 * Mark this choice solution as a duplicate of another one.
	 * We are only making one level of duplicate. Thus, it is not allowed to mark this solution as
	 * a duplicate of another duplicate.
	 * skip_children_and_vote_reorganization: Used internally as it is calling itself.
	 */
	function mark_as_duplicate_of($solution_id, $skip_children_and_vote_reorganization = false)
	{
		if($this->_id == 0 || is_numeric($solution_id) == false)
			return false;

		$parent = new ChoiceSolutionModel();
		$parent->setId($solution_id);
		if($parent->getStatus() == ChoiceSolutionModel::$choice_status["duplicate"])
			return false;

		//Eh, don't mark us duplicate ourselve!!
		if($this->_id == $solution_id)
			return false;

		//Set the status
		$this->setStatus(ChoiceSolutionModel::$choice_status["duplicate"]);
		$this->_setDuplicateOf($solution_id);

		//Ok, we are already processing the children, and since we only have one level of dups,
		//the rest is not necessary
		if($skip_children_and_vote_reorganization)
			return true;

		//Move this idea duplicates to the new parent duplicate
		$choicesollist = new ChoiceSolutionListModel();
		$choicesollist->setFilterParameters(array("state_duplicate" => 1,
			 "duplicate_of" => $this->_id));
		$choicesollist->setDataFilter(array("include_minimal_data" => true));
		$entries = $choicesollist->getData();
		foreach($entries as $entry)
		{
			$choicesol = new ChoiceSolutionModel();
			$choicesol->setId($entry->id);
			$choicesol->mark_as_duplicate_of($solution_id, true);
		}

		//Update of the vote counts is handled via SQL triggers

		return true;
	}

	/**
	 * Cast a vote.
	 * @return Return if the vote was accepted.
	 */
	function vote($vote)
	{
		$result = false;

		//We check and insert/update the vote
		if( $this->_id != "0" && is_numeric($vote) &&
			($vote == -1 || $vote == 0 || $vote == 1))
		{
			if($this->_voteSQL($this->_id, $vote))
				$result = true;
		}
		
		return $result;
	}

	/**
	 * Save the user vote in the database, making the SQL store only if necessary.
	 * WARNING: parameters must have been satinized, and access control must have been checked.
	 * @return Return if a SQL update/insert was needed.
	 */
	private function _voteSQL($item_id, $vote)
	{
		global $user;
		$changes = true;

		if($user->uid == null)
			return false;

		$result = it_query("SELECT value FROM qapoll_vote WHERE choicesolutionid='" . $item_id . "' AND userid='" . $user->uid . "'");
		$currentvote = db_result($result);

		if ($currentvote == null)
		{
			//Record the vote
			//Sum fields are handled via a trigger
			it_query("INSERT INTO qapoll_vote (choicesolutionid, userid, date, value) VALUES ('".$item_id.
				"', '" . $user->uid . "', NOW(), '$vote')");
		}
		else if($currentvote != $vote)
		{
			//Sum fields are handled via a trigger
			it_query("UPDATE qapoll_vote SET value='$vote' WHERE choicesolutionid='".$item_id."' AND userid='".$user->uid."'");
		}
		else
			$changes = false;

		return $changes;
	}


	/**
	 * Set this solution duplicate number
	 */
	private function _setDuplicateOf($dup_number)
	{
		if($this->_id == 0 || !is_numeric($dup_number))
			return false;

		$query = "UPDATE qapoll_choice_solution SET duplicate_choice_solution_id=" . $dup_number . " " .
			"WHERE id=" . $this->_id;
		it_query($query);
	}

	/**
	 * This function is a callback. DO NOT CALL DIRECTLY!
	 * It is used by UserModel::_hasPermission to determine if the user user_id is
	 * the owner of this object.
	 */
	function _callback_isOwner($model_id, $user_id)
	{
		//If there is no id set to this idea, return false.
		if($this->_id == 0)	
			return false;

		//Check if user_id is the owner of model_id
		$data = $this->getData();

		return ($data->userid == $user_id);
	}

}

?>
