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




class DuplicateReportModel extends Model
{
	/**
	 * Entry point primary key.
	 */
	var $_id = 0;

	/**
	 * The choiceModel linked to this duplicate report.
	 */
	var $_choiceModel = null;

	/**
	 * Set the primary key.
	 */
	function setId($id)
	{
		if(is_numeric($id))
		{
			$this->_id = $id;
			$_data = null;
		}
	}

	/**
	 * Default constructor. Optional choiceModel can be required to
	 * link this DuplicateReportModel.
	 */
	function DuplicateReportModel($choiceModel = null)
	{
		$this->_choiceModel = $choiceModel;
	}

	/**
	 * Load the data.
	 */
	function _loadData()
	{
		if($this->_id != 0)
		{
			//Get the entrypoint from DB
			$query = "SELECT * FROM qapoll_choice_duplicate_report " .
				"WHERE id='" . $this->_id . "'";
			$imagelink = db_fetch_object(it_query($query));

			return $imagelink;
		}
		else
			return null;
	}

	/**
	 * Load an object from the _POST parameters.
	 * Return true if there was enough correct data. 
	 * Needs $this->_choiceModel to know to which choice to link the new duplicate report.
	 */
	function loadFromPost()
	{
		$errorMessage = "";

		if($this->_choiceModel == null)
			return false;
	
		$this->_data->choiceid = $this->_choiceModel->getData()->id;
		$this->_data->duplicateid = $_POST['dup_number'];

		//Wrong poll id. Should not occur.
		if(!is_numeric($this->_data->choiceid)) //This has been checked in the controller.
			$errorMessage .= "Internal error[2]. Please contact an administrator.<br />";

		//Check if bugid numeric
		if($this->_data->duplicateid == "" || is_numeric($this->_data->duplicateid) == false)
			$errorMessage .= "An idea number is required for submitting a duplicate report.<br />";

		//Display the errors
		if($errorMessage != "")
			$errorMessage = substr($errorMessage, 0, strlen($errorMessage) - 6);
		drupal_set_message($errorMessage, 'error_msg');


		return ($errorMessage == "");
	}

	/**
	 * Update or insert a new article into the BD.
	 * When updating, it takes care to update only what the user is allowed to.
	 * Returns true if everything was OK.
	 * bypass_checks: Will skip the opposite report check before inserting. Set it to true only when you know what you are doing.
	 */
	function store($bypass_opposite_report_checks = false)
	{
		$errorMessage = "";
		global $user;

		//New article
		if($this->_id == 0)
		{
			//We check first if there are no duplicate.
			$dup = db_result(it_query("SELECT id FROM qapoll_choice_duplicate_report WHERE choiceid='". $this->_data->choiceid .
				"' AND duplicateid=" . $this->_data->duplicateid));
			if($dup != "")
				$errorMessage .= "The idea number you submitted was already submitted by someone else. " .
					"You can check its status on the list below.<br />";

			//Then we check that the opposite report was not made.
			$dupstatus = db_result(it_query("SELECT status FROM qapoll_choice_duplicate_report WHERE choiceid='". $this->_data->duplicateid .
				"' AND duplicateid=" . $this->_data->choiceid));
			if($bypass_opposite_report_checks == false && $dupstatus != "")
			{
				$errorMessage .= "We have a report designating this idea as a potential duplicate of the one you submitted. ";
				if($dupstatus == 0)
					$errorMessage .= "Admins will reject or mark one of them as duplicate soon.<br />";
				else if($dupstatus == 2)
					$errorMessage .= "But the admins decided that these ideas should not be merged.<br />";
			}

			//Then we check the duplicate id submitted really exists.
			if(ChoiceModel::exists($this->_data->duplicateid) == false)
				$errorMessage .= "The idea number you submitted does not exists.<br />";
			else
			{
				//Then we check that the proposed duplicateid is not a bug (choicetype=0)
				if(ChoiceModel::isABug($this->_data->duplicateid) == true)
					$errorMessage .= "The idea number you submitted appears to be a bug number. " . 
						"Please check again your idea number.<br />";
			}

			//Finally we check that we are not reporting a duplicate of ourselves.
			if($this->_data->choiceid == $this->_data->duplicateid)
				$errorMessage .= "The idea number you submitted is the number of the current idea.<br />";

			if($errorMessage == "")
			{
				it_query(
					"INSERT INTO qapoll_choice_duplicate_report (status, choiceid, duplicateid, submitterid, date) " .
					"VALUES (" .
					"'0'," . 
					"'" . $this->_data->choiceid . "'," .
					"'" . $this->_data->duplicateid . "'," .
					"'" . $user->uid . "'," .
					"'" . date("Y-m-d H:i:s") . "')");

				//Save the id of the newly inserted dup report
				$this->_id = db_last_insert_id("qapoll_choice_duplicate_report", "id");
			}
		}

		//Display the errors
		if($errorMessage != "")
			$errorMessage = substr($errorMessage, 0, strlen($errorMessage) - 6);
		drupal_set_message($errorMessage, 'error_msg');

		return ($errorMessage == "");
	}

	/**
	 * Reject a duplicate report. None of the "choice" will be marked as duplicate,
	 * and future report submission concerning these ideas will be prevented.
	 * Needs $this->_id
	 */
	function reject()
	{
		global $user;

		if($this->_id == 0)
			return false;

		//Start a transaction.
		db_lock_table("qapoll_choice_duplicate_report");

		//Check that the status is still 0 (active). Some admins may have already moderated this one.
		$dupstatus = db_result(it_query("SELECT status FROM qapoll_choice_duplicate_report WHERE id='". $this->_id . "'"));

		if($dupstatus != 0 || $dupstatus === null)
		{
			$result = false;
		}
		else
		{
			//Inactivate the dup report.
			$result = true;
			it_query("UPDATE qapoll_choice_duplicate_report SET status='2', moderatorid='" . $user->uid . 
				"' WHERE id='" . $this->_id . "'");
		}

		//End the transaction
		db_unlock_tables();

		return $result;
	}

	/**
	 * Accept a duplicate report. The choice duplicateid will be marked as a duplicate.
	 * Needs $this->_id.
	 */
	function accept()
	{
		global $user;

		if($this->_id == 0)
			return false;

		//Start a transaction.
		db_lock_table("qapoll_choice_duplicate_report");

		//Check that the status is still 0 (active). Some admins may have already moderated this one.
		$dupstatus = db_result(it_query("SELECT status FROM qapoll_choice_duplicate_report WHERE id='". $this->_id . "'"));

		if($dupstatus != 0 || $dupstatus === null)
		{
			$result = false;
		}
		else
		{

			//Mark the idea as duplicate
			$choice = new ChoiceModel();
			$choice->setId($this->getData()->duplicateid);
			$result = $choice->mark_as_duplicate_of($this->getData()->choiceid);

			//Validate or not the dup report.
			it_query("UPDATE qapoll_choice_duplicate_report SET status='" . (($result)?"1":"2") . "', moderatorid='" . $user->uid . 
				"' WHERE id='" . $this->_id . "'");

			//Invalidate all the reports that can't be accepted.
			if($result)
				it_query("UPDATE qapoll_choice_duplicate_report SET status='2' WHERE duplicateid='" . 
					$this->getData()->duplicateid  . "' AND id != '" . $this->_id . "'");

		}

		//End the transaction
		db_unlock_tables();

		return $result;
	}

	/**
	 * Accept a duplicate report, but not in the way it was proposed: The choice choiceid will be marked as a duplicate of
	 * the choice duplicateid.
	 * It will in fact reject this report, create a new one with the choice ids switched and accept it.
	 * Needs $this->_id.
	 */
	function accept_opposite()
	{
		if($this->_id == 0)
			return false;

		//Start a transaction.
		db_lock_table("qapoll_choice_duplicate_report");

		//Check that the status is still 0 (active). Check that the new duplicate report was not yet stored.
		$dupstatus = db_result(it_query("SELECT status FROM qapoll_choice_duplicate_report WHERE id='". $this->_id . "'"));

		if($dupstatus != 0 || $dupstatus === null)
		{
			$result = false;
		}
		else
		{
			//Create the new duplicate report, store it and accept it.
			$result = true;
			$newduprep = new DuplicateReportModel();
			$newduprep->_data->choiceid = $this->getData()->duplicateid;
			$newduprep->_data->duplicateid = $this->getData()->choiceid;
			$newduprep->store(true);
			$newduprep->accept();

			//Reject the current one.
			$this->reject();
		}

		//End the transaction
		db_unlock_tables();

		return $result;
	}

}

?>
