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



/**
 * A choice, representing an idea, bug, concern, whatever you decided to use Brainstorm for.
 */
class ChoiceModel extends Model
{


	/**
	 * Choice id.
	 */
	var $_id = 0;

	/**
	 * The pollModel corresponding to this object.
	 */
	var $_pollModel = null;

	/**
	 * The list of available imageLink related to this choice.
	 */
	var $_imageLinkList = null;

	/**
	 * The list of duplicate report indicating that another idea is a duplicate of this one.
	 */
	var $_duplicate_reportList = null;

	/**
	 * The list of duplicates of this ideas.
	 */
	var $duplicates_items = null;

	/**
	 * An array of the models used to get additional data when loading the choice data.
	 */
	var $additional_models = array();


	/**
	 * List of idea status constants. It contains only the symbolic names and their corresponding id, not the names to display.
	 */
	static public $choice_status = array(
		"deleted" => -2,
		"new" => -1,
		//"O" was a state were the status was fetched according to the attachments. Dropped but still used by a few ideas.
		"old_new" => 0,
		"needinfos" => 1,
		"blueprint_approved" => 6,		
		"workinprogress" => 2,
		"done" => 3,
		"already_done" => 5,
		//Unapplicable => Won't implement		
		"unapplicable" => 4,
		"not_an_idea" => 7,
		"awaiting_moderation" => 8);


	/**
	 * Data filters. They are used to restrict the columns of the data returned.
	 * This can be very usefull when we only need something specific, and we
	 * don't want to waste processing time.
	 * include_minimal_data: Override all the below options by setting them to false.
	 * include_approval_vote: shall we include the user approval vote?
	 */
	var $_include_minimal_data = false;
	var $_include_user_approval_vote = true;


	function ChoiceModel($pollModel = null)
	{
		$this->_pollModel = $pollModel;
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
	 * Load a choice.
	 * $this->_id is required.
	 */
	function _loadData()
	{
		global $user, $entrypoint;

		if($this->_id != 0)
		{
			//Query first the choice itself
			//Eh, have you seen the mouse?
			$query = "SELECT qapoll_choice.id, qapoll_choice.title, qapoll_choice.bugid, qapoll_choice.specid, qapoll_choice.forumid, " .
				"qapoll_choice.userid as userid, qapoll_choice.date, qapoll_choice.status, qapoll_choice.description, qapoll_choice.pollid, " . 
				"qapoll_choice.whiteboard, qapoll_choice.categoryid, qapoll_choice.choicetype, qapoll_choice.duplicatenumber, " .
				"qapoll_choice.totalvotes as votes, qapoll_choice.last_status_change, qapoll_choice.release_target, " .
				"qapoll_choice.last_comment_date, qapoll_choice.totalapprovalvotes, qapoll_choice.relation_subcategory_id, " .

				//Get the user bookmark only if logged.
				(($user->uid != null)?"(qapoll_choice_bookmark.date IS NOT NULL) as mybookmark, ":"") .

				//Get the user approval vote only if logged.
				(($user->uid != null && $this->_include_user_approval_vote == true)?"qapoll_choice_approval_vote.value as myapprovalvote, ":"") .

				"users.name as username, " .
				"qapoll_poll_category.name as catname, " .
				"qapoll_poll_relation_subcategory.name as relationsubcatname, " .
				"qawebsite_launchpad_bug.status as bugstatus, qawebsite_launchpad_bug.title as bugtitle, " . 
				"qawebsite_launchpad_blueprint.title as spectitle, qawebsite_launchpad_blueprint.definition as specstatus, " . 
				"qawebsite_launchpad_blueprint.implementation as specdelivery, " . 
				"qapoll_choicedup.title as duptitle, " .
				"qapoll_choice.relation_id as relation_id, qapoll_poll_relation.name as relation_name, " .
				"qapoll_poll_relation.url_name as relation_url_name, " . 
				"qapoll_release.long_name as target_release_name " .
				"FROM qapoll_choice " .
				"LEFT JOIN users ON users.uid = qapoll_choice.userid " .


				//Get the user bookmark only if logged.
				(($user->uid != null)?"LEFT JOIN qapoll_choice_bookmark ON qapoll_choice_bookmark.choiceid = qapoll_choice.id AND " .
					"qapoll_choice_bookmark.userid = " . $user->uid . " ":"") .

				//Get the user approval vote only if logged.
				(($user->uid != null && $this->_include_user_approval_vote == true)?
					"LEFT JOIN qapoll_choice_approval_vote ON qapoll_choice_approval_vote.choiceid = qapoll_choice.id AND " .
					"qapoll_choice_approval_vote.userid = " . $user->uid . " ":"") .

				"LEFT JOIN qapoll_poll_relation ON qapoll_choice.relation_id = qapoll_poll_relation.id " .
				"LEFT JOIN qapoll_poll_relation_subcategory ON " .
					"qapoll_choice.relation_subcategory_id = qapoll_poll_relation_subcategory.id " .
				"LEFT JOIN qapoll_poll_category ON qapoll_choice.categoryid = qapoll_poll_category.id " .
				"LEFT JOIN qawebsite_launchpad_bug ON qapoll_choice.bugid = qawebsite_launchpad_bug.originalbug " .
				"LEFT JOIN qawebsite_launchpad_blueprint ON qapoll_choice.specid = qawebsite_launchpad_blueprint.blueprinturl " .
				"LEFT JOIN qapoll_choice AS qapoll_choicedup ON qapoll_choice.duplicatenumber = qapoll_choicedup.id " . 
				"LEFT JOIN qapoll_release ON qapoll_choice.release_target = qapoll_release.id " . 
				"WHERE qapoll_choice.id='" . $this->_id . "'";
			$choice = db_fetch_object(it_query($query));

			//Put its corresponding poll model too.
			$this->_pollModel = new PollModel();
			$this->_pollModel->setId($choice->pollid);

			//Query now the comments.
			$comments = new ChoiceCommentListModel();
			$comments->setFilterParameters(array("choiceid" => $this->_id));
			$choice->comment_items =& $comments->getData();

			//Add the solutions to this item
			$choicesolutionlist = new ChoiceSolutionListModel();
			$filter = array("choice_id" => $this->_id);
			//If we are in progress or implemented, show the selected ideas first.
			if($choice->status == ChoiceModel::$choice_status["workinprogress"] ||
				$choice->status == ChoiceModel::$choice_status["done"])
				$filter["ordering"] = "selectedfirst";
			$choicesolutionlist->setFilterParameters($filter);
			$choice->solutions =& $choicesolutionlist->getData();
			//Save the model
			$this->additional_models["choicesolutionlist"] = $choicesolutionlist;

			//Pull in the duplicates
			$duplistmodel = new ChoiceListModel($this->_pollModel, 1, 100);
			$filter = array("ordering" => "mostvotes", "duplicate_items" => $this->_id);
			$filter = array_merge($filter, $entrypoint->getData()->filterArray);
			$duplistmodel->setFilterParameters($filter);
			$duplistmodel->setDataFilter(array("include_minimal_data" => true));
			$choice->duplicates_items = $duplistmodel->getData()->items;

			//Also add the tags.
			$taglist = new TagListModel();
			$filter = array("choice_id" => $this->_id, "admin" => 0);
			$taglist->setFilterParameters($filter);
			$choice->tags = $taglist->getData();

			//And also the admin tags
			$taglist = new TagListModel();
			$filter = array("choice_id" => $this->_id, "admin" => 1);
			$taglist->setFilterParameters($filter);
			$choice->admintags = $taglist->getData();



			return $choice;

		}
		else
			return null;
	}

	/**
	 * Load an object from the _POST parameters.
	 * Return true if there was enough correct data. 
	 * Needs $this->_pollModel to know to which poll to link the new choice.
	 * for its compulsory fields.
	 * @param edit WARNING: dirty hack. When editing as a user, the title is disabled.
	 * Thus, when submitting, his field will be empty (even if there was some text on it).
	 * Handle that by specifying if we are currently editing an item or not.
	 */
	function loadFromPost($edit)
	{
		global $site;
		global $user;
				
		$errorMessage = "";

		if($this->_pollModel == null)
			return false;
	
		$this->_data->pollid = $this->_pollModel->getData()->id;
		$this->_data->title = substr(db_escape_string($_POST['choice_title']), 0, 80);
		$this->_data->description = substr(db_escape_string($_POST['choice_description']), 0, 5000);
		$this->_data->spec = db_escape_string(($_POST['choice_type'] == 0)?$_POST['choice_spec']:$_POST['choice_spec2']);
		$this->_data->threadid = ($_POST['choice_type'] == 0)?"":$_POST['choice_threadid'];
		$this->_data->category = ($_POST['choice_category'] != null)?$_POST['choice_category']:-1;
		$this->_data->relation_id = ($_POST['choice_relation'] != null)?$_POST['choice_relation']:-1;
		$this->_data->relation_subcategory_id = ($_POST['choice_relation_subcategory'] != null)?$_POST['choice_relation_subcategory']:-1;
		$this->_data->type = $_POST['choice_type'];
		$this->_data->rawtags = $_POST['choice_tags'];
		$this->_data->whiteboard = substr(db_escape_string($_POST['choice_whiteboard']), 0, 5000);

		//Now check all entries.
		//Wrong poll id. Should not occur.
		if(!is_numeric($this->_data->pollid)) //This has been checked in the controller.
			$errorMessage .= t("Internal error[1]. Please contact an administrator.") . "<br />";
		
		//Check if title not empty
		if($this->_data->title == "" && ($edit == false || (user_access($site->getData()->adminrole) || user_access($site->getData()->moderatorrole))))
			$errorMessage .= t("Please enter a idea rationale title.") . "<br />";

		//Check if description not empty
		if($this->_data->description == "" && ($edit == false || (user_access($site->getData()->adminrole) || user_access($site->getData()->moderatorrole))))
			$errorMessage .= t("Please enter a idea rationale description.") . "<br />";
		
		//Data type should be 0:bug or 1:idea.
		if(!(is_numeric($this->_data->type) && $this->_data->type > -1 && $this->_data->type < 2))
			$errorMessage .= t("You must select the type : idea or bug.") . "<br />";

		//Bugid required if type = 0:bug	 
		if($this->_data->bugid == "" && $this->_data->type == 0)
			$errorMessage .= t("A bug number is required when you submit a bug.") . "<br />";
		
		//Spec url must be in a specific form
		if($this->_data->spec != "" && !ereg("^https\:\/\/blueprints.launchpad.net\/(.*)\/\+spec\/(.*)", $this->_data->spec))
			$errorMessage .= t("The URL you submitted in the blueprint field does not seem valid.") . "<br />";

		//Thread id must be numeric
		if($this->_data->threadid != "" && !is_numeric($this->_data->threadid))
		{
			//Process the thread id field. (We receive an URL, we want a thread id.)
			if(ereg("^http\:\/\/ubuntuforums\.org\/showthread\.php[?]t=([0-9]*).*", $this->_data->threadid, $res) == false)
				$errorMessage .= t("The URL you submitted as an ubuntuforums.org thread does not seem valid.") . "<br />";
			else
				$this->_data->threadid = $res[1];

		}

		//Category must be numeric and > -2, if categories are available.
		$catlist = new CategoryListModel($GLOBALS['poll']);
		if(!is_numeric($this->_data->category) || ($this->_data->category < 0 && count($catlist->getData()) > 0))
			$errorMessage .= t("Please choose a category that fits the best for your submission.") . "<br />";

		//Relation id must be numeric and > -2
		if(!is_numeric($this->_data->relation_id) || !($this->_data->relation_id > -2))
			$errorMessage .= t("Please choose the project the more closely related to your submission.") . "<br />";

		//relation_subcategory_id must be numeric and coherent to the relation id
		if($this->_data->relation_id != -1)
		{
			$relationsubcatlist = new RelationSubcategoryListModel();
			$relationsubcatlist->setFilterParameters(array("relation_id" => $this->_data->relation_id));
			$subcat_found = false;
			foreach($relationsubcatlist->getData() as $subcat)
			{
				if($subcat->id == $this->_data->relation_subcategory_id)
					$subcat_found = true;
			}
		}
		if(!is_numeric($this->_data->relation_subcategory_id) ||
			(($this->_data->relation_subcategory_id == -2 || $subcat_found == false) && $this->_data->relation_id != -1 &&
			count($relationsubcatlist->getData()) != 0))
			$errorMessage .= t("Please choose the project category the more closely related to your submission.") . "<br />";

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
	 */
	function store()
	{
		$errorMessage = "";
		global $user;

		//We check first if there are no duplicate.

		//Then the spec
		if($this->_data->spec != "")
		{
			$choiceid = db_result(it_query("SELECT id FROM qapoll_choice WHERE pollid='". $this->_data->pollid .
				"' AND specid='" . $this->_data->spec . "'"));
			if($choiceid != "" && $choiceid != $this->_id)
			{
				$choice = new ChoiceModel();
				$choice->setId($choiceid);
				$errorMessage .= t('An idea has already been submitted in the website with the blueprint URL you gave. Maybe someone has already submitted what you intended to add. <a href="!link">You should check it.</a>',
					array("!link" => $GLOBALS['basemodule_url'] . "/idea/" . $choiceid . "/")) . 
					"<br />";
			}
		}

		//Finally the thread id
		if($this->_data->threadid != "")
		{
			$choiceid = db_result(it_query("SELECT id FROM qapoll_choice WHERE pollid='". $this->_data->pollid .
				"' AND forumid=" . $this->_data->threadid));
			if($choiceid != "" && $choiceid != $this->_id)
			{
				$choice = new ChoiceModel();
				$choice->setId($choiceid);
				$errorMessage .= t('An idea has already been submitted in the website with the ubuntuforums.org thread URL you gave. Maybe someone has already submitted what you intended to add. <a href="!link">You should check it.</a>',
					array("!link" => $GLOBALS['basemodule_url'] . "/idea/" . $choiceid . "/")) .  "<br />";
			}
		}

		//New article
		if($this->_id == 0)
		{

			//We check that the user did not already submit an item within the last minute.
			if(QAPollLogger::getInstance()->isLatestItemSubmissionOlderThanOneMin($user->uid) == false)
			{
				$errorMessage .= t("Please wait one minute between submissions.") . "<br />";
			}


			if($errorMessage == "")
			{	
				$query = "INSERT INTO qapoll_choice (pollid, title, specid, forumid, userid, date, description, categoryid, " .
					"whiteboard, relation_id, relation_subcategory_id, choicetype, status) VALUES (" .
					"'" . $this->_data->pollid . "'," . 
					"'" . $this->_data->title . "'," .
					(($this->_data->spec != "")?"'".$this->_data->spec."'":"Null") . "," .
					(($this->_data->threadid != "")?"'".$this->_data->threadid."'":"Null") . "," .
					"'" . $user->uid . "'," .
					"'" . date("Y-m-d H:i:s") . "'," .
					"'" . $this->_data->description . "'," .
					"'" . $this->_data->category . "'," .
					"'" . $this->_data->whiteboard . "'," .
					"'" . $this->_data->relation_id . "'," .
					"'" . $this->_data->relation_subcategory_id . "'," .
					"'" . $this->_data->type . "'," . 
					//At submission, do items need approvals?
					"'" . ((QAPollConfig::getInstance()->getValue("choice_number_approvals_needed") > 0)?8:-1) . "')";

				it_query($query);

				//Save the id of the newly inserted choice
				$this->_id = db_last_insert_id("qapoll_choice", "id");

				//Now save the tags (separate table)
				$this->setTags($this->_data->rawtags, 0, $user->uid);

				//Log this action
				QAPollLogger::getInstance()->logItemSubmission($user->uid, $nextid);
			}
		}
		else
		{
			global $site;
			global $user;

			//We get the current values.
			$current_stored_choice = new ChoiceModel();
			$current_stored_choice->setId($this->_id);

			//We update the choice according to the user rights
			$cols = array();
			if(user_access($site->getData()->adminrole) || user_access($site->getData()->moderatorrole) ||
				UserModel::currentUserHasPermission("edit_title", "Choice", $current_stored_choice))
			{
				$cols[] = "title = '" . $this->_data->title . "'";

				//Saving the change in the logs
				ChoiceLogModel::log($this->_id, ChoiceLogModel::$change["title"], 
					$current_stored_choice->getData()->title, $this->_data->title);
			}
			if(UserModel::currentUserHasPermission("edit_dev_comments", "Choice", $current_stored_choice))
			{
				$cols[] = "whiteboard = '" . $this->_data->whiteboard . "'";

				//Saving the change in the logs
				ChoiceLogModel::log($this->_id, ChoiceLogModel::$change["whiteboard"], 
					$current_stored_choice->getData()->whiteboard, $this->_data->whiteboard);
			}
			if(user_access($site->getData()->adminrole) || user_access($site->getData()->moderatorrole) ||
				$current_stored_choice->getData()->userid == $user->uid ||
				UserModel::currentUserHasPermission("edit_description", "Choice", $current_stored_choice))
			{
				$cols[] = "description = '" . $this->_data->description . "'";

				//Saving the change in the logs
				ChoiceLogModel::log($this->_id, ChoiceLogModel::$change["description"], 
					$current_stored_choice->getData()->description, $this->_data->description);
			}
			if(UserModel::currentUserHasPermission("edit_relation", "Choice", $current_stored_choice))
			{
				$cols[] = "relation_id = '" . $this->_data->relation_id . "'";

				//Saving the change in the logs
				ChoiceLogModel::log($this->_id, ChoiceLogModel::$change["relation"], 
					$current_stored_choice->getData()->relation_id, $this->_data->relation_id);
			}
			if(UserModel::currentUserHasPermission("edit_relation_subcategory", "Choice", $current_stored_choice))
			{
				$cols[] = "relation_subcategory_id = '" . $this->_data->relation_subcategory_id . "'";

				//Saving the change in the logs
				ChoiceLogModel::log($this->_id, ChoiceLogModel::$change["relationsubcat"], 
					$current_stored_choice->getData()->relation_subcategory_id, $this->_data->relation_subcategory_id);
			}
			if(user_access($site->getData()->adminrole) || user_access($site->getData()->userrole))
			{
				//Any registered user can edit that.
				$cols[] = "specid = " . (($this->_data->spec != "")?"'".$this->_data->spec."'":"Null");
				$cols[] = "forumid = " . (($this->_data->threadid != "")?"'".$this->_data->threadid."'":"Null");
				//$cols[] = "choicetype = '" . $this->_data->type . "'";
				$cols[] = "categoryid = '" . $this->_data->category . "'";

				//Saving the change in the logs
				ChoiceLogModel::log($this->_id, ChoiceLogModel::$change["category"], 
					$current_stored_choice->getData()->categoryid, $this->_data->category);
			}
			//Store the edition date
			$cols[] = "last_edit_date = NOW()";

			if(count($cols) > 0)
				it_query("UPDATE qapoll_choice SET " . implode(", ", $cols) . " WHERE id = '" . $this->_id . "'");

			//Now save the tags (separate table)
			$this->setTags($this->_data->rawtags, 0, $user->uid);
		}

		//Display the errors
		if($errorMessage != "")
			$errorMessage = substr($errorMessage, 0, strlen($errorMessage) - 6);
		drupal_set_message($errorMessage, 'error_msg');

		//Now reset the data to force the reload : all the data is not loaded, and some data may be required for future use.
		$this->_data = null;

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
			$this->_include_user_approval_vote = false;
		}
		else
		{
			if($filter_array['include_user_approval_vote'] != null && is_numeric($filter_array['include_user_approval_vote']))
				$this->_include_user_approval_vote = $filter_array['include_user_approval_vote'];

		}

	}


	/**
	 * Delete a choice. It will in fact set its status to -2, we keep everything in BDD.
	 * Needs $this->_id.
	 */
	function delete()
	{
		return $this->setStatus(-2);
	}

	/**
	 * Undelete a choice. It will in fact set its status to -1, the default status (get status from LP).
	 * By design, no way to get back its original status if it was overriden.
	 * Needs $this->_id.
	 */
	function undelete()
	{
		return $this->setStatus(-1);
	}

	/**
	 * Add or edit an approval vote.
	 * BE SURE to have checked the permission first!
	 */
	function add_approval_vote($userid, $value)
	{
		$changes = true;

		if(is_numeric($userid) == false || $this->_id == 0 || is_numeric($value) == false)
			return false;

		$query = "SELECT value FROM qapoll_choice_approval_vote WHERE choiceid='" . $this->_id.
			"' AND userid='" . $userid . "' AND status='1'";
		$currentvote = db_result(it_query($query));

		//Saving the change in the logs
		ChoiceLogModel::log($this->_id, ChoiceLogModel::$change["moderatorapproval"], $currentvote, $value);

		if ($currentvote == null)
		{
			//Record the vote
			it_query("INSERT INTO qapoll_choice_approval_vote (choiceid, userid, date, value) VALUES ('" . $this->_id . 
				"', '" . $userid . "', NOW(), '" . $value . "')");
			//Save the vote in the total of the choice
			it_query("UPDATE qapoll_choice SET totalapprovalvotes = totalapprovalvotes + '" . $value . "' WHERE id='" . $this->_id . "'");
		}
		else if($currentvote != $value)
		{
			it_query("UPDATE qapoll_choice_approval_vote SET value='" . $value . "' WHERE choiceid='" . $this->_id .
				"' AND userid='" . $userid . "' AND status='1'");
			it_query("UPDATE qapoll_choice SET totalapprovalvotes = totalapprovalvotes + '" . ($value - $currentvote) .
				"' WHERE id='" . $this->_id . "'");
		}
		else
			$changes = false;

		return $changes;
	}

	/**
	 * This function will check the idea approval count and look if it is equal or superior to the required number of approval votes.
	 * If so, it will change its status to "New".
	 * Needs $this->_id
	 */
	function check_idea_approval_count_and_update_status()
	{
		if($this->_id == 0)
			return false;

		$query = "SELECT totalapprovalvotes FROM qapoll_choice WHERE id='" . $this->_id . "'";
		$totalvotes = db_result(it_query($query));

		if($totalvotes >= QAPollConfig::getInstance()->getValue("choice_number_approvals_needed"))
			$this->setStatus(ChoiceModel::$choice_status["new"]);
	}

	/**
	 * Change the status of the choice.
	 * Needs $this->_id.
	 * You should use the enum stored in the static array as $value!
	 */
	function setStatus($value)
	{
		if($this->_id == 0 || is_numeric($value) == false)
			return false;

		//Saving the change in the logs
		$oldvalue = db_result(it_query("SELECT status FROM qapoll_choice WHERE id='" . $this->_id . "'"));
		ChoiceLogModel::log($this->_id, ChoiceLogModel::$change["status"], $oldvalue, $value);

		it_query("UPDATE qapoll_choice SET status='$value', last_status_change='NOW()' WHERE id='" . $this->_id . "'");

		return true;
	}

	/**
	 * An user toggle its bookmark on this idea.
	 */
	function toggle_bookmark($userid)
	{
		if($this->_id == 0 || is_numeric($userid) == false || $userid == 0)
			return false;

		$query = "SELECT id FROM qapoll_choice_bookmark WHERE userid='". $userid . "' AND choiceid = '" . $this->_id . "'";
		$bookmarkid = db_result(it_query($query));

		if($bookmarkid == null)
			$ret = $this->bookmark($userid);
		else
			$ret = $this->unbookmark($userid);

		return $ret;
	}

	/**
	 * An user bookmark this idea.
	 */
	function bookmark($userid)
	{
		if($this->_id == 0 || is_numeric($userid) == false || $userid == 0)
			return false;

		$query = "SELECT id FROM qapoll_choice_bookmark WHERE userid='". $userid . "' AND choiceid = '" . $this->_id . "'";
		$bookmarkid = db_result(it_query($query));

		if($bookmarkid == null)
			it_query("INSERT INTO qapoll_choice_bookmark(userid, choiceid, date) VALUES('" . $userid . "', '" . $this->_id . "', 'NOW()')");

		return true;
	}

	/**
	 * An user unbookmark this idea.
	 */
	function unbookmark($userid)
	{
		if($this->_id == 0 || is_numeric($userid) == false || $userid == 0)
			return false;

		it_query("DELETE FROM qapoll_choice_bookmark WHERE userid='" . $userid . "' AND choiceid='" . $this->_id . "'");

		return true;
	}

	/**
	 * Set the tags of this idea.
	 * This function takes care to sanitize the tag parameter, which is a list of tags separated
	 * by one or more whitespaces.
	 * admin can be 1 (admin tags) or 0 (normal tags). This function does not check permissions.
	 * userid is the user ID of the user submitting these tags.
	 */
	function setTags($tags, $admin, $userid)
	{
		if($this->_id == 0 || is_numeric($admin) == false || $userid == 0)
			return false;

		//First query the list of tags attached to this idea.
		$currenttaglist = new TagListModel();
		$filter = array("choice_id" => $this->_id, "admin" => $admin);
		$currenttaglist->setFilterParameters($filter);
		$currenttags = $currenttaglist->getData();

		//Parse the list of new tags. We limit the number of tags to 20, with a max length of 20 chars for each tag.
		$taglist = array_filter(explode(" ", $tags), create_function('$var', 'return ($var != null);'));
		$taglist = array_slice($taglist, 0, 20);
		$taglist = array_map(create_function('$var', 'return substr($var, 0, 20);'), $taglist);


		//Create the array of tags to add or remove
		$currenttags_to_remove = array();
		$tags_to_add = array();

		//Make the comparaison
		foreach($taglist as $tag)
		{
			$found = false;
			foreach($currenttags as $currenttag)
			{
				if(TagModel::compareNames($tag, $currenttag->name))
				{
					$found = true;
					break;
				}
			}
			//$tag not found, we will add it.
			if(!$found)
				$tags_to_add[] = $tag;
		}
		foreach($currenttags as $currenttag)
		{
			$found = false;
			foreach($taglist as $tag)
			{
				if(TagModel::compareNames($tag, $currenttag->name))
				{
					$found = true;
					break;
				}
			}
			//$currenttag not found, we will remove it.
			if(!$found)
				$currenttags_to_remove[] = $currenttag->id;
		}

		//Debug info
		//print_r($tags_to_add);
		//print_r($currenttags_to_remove);

		//Add the necessary tags.
		foreach($tags_to_add as $tag_to_add)
		{
			$tag = new TagModel();
			$tag->setName($tag_to_add);
			$tag->setChoiceId($this->_id);
			$tag->setUserId($userid);
			$tag->setAdminFlag($admin);
			$tag->store();
		}

		//Remove the necessary tags.
		foreach($currenttags_to_remove as $currenttag_to_remove)
		{
			$tag = new TagModel();
			$tag->setId($currenttag_to_remove);
			$tag->delete();
		}

		if(count($currenttags_to_remove) > 0 || count($tags_to_add) > 0)
		{
			//Saving the change in the logs
			if($admin)
			{
				$curtags = "";
				foreach($currenttags as $tag)
					$curtags .= $tag->name . " ";
				ChoiceLogModel::log($this->_id, ChoiceLogModel::$change["admintags"], $curtags, implode(" ", $taglist));
			}
			else
			{
				$curtags = "";
				foreach($currenttags as $tag)
					$curtags .= $tag->name . " ";
				ChoiceLogModel::log($this->_id, ChoiceLogModel::$change["tags"], $curtags, implode(" ", $taglist));
			}
		}

		return true;
	}

	/**
	 * Mark this choices as a duplicate of another one.
	 * We are only making one level of duplicate. Thus, it is not allowed to mark this choice as
	 * a duplicate of another duplicate.
	 * skip_children_and_vote_reorganization: Used internally as it is calling itself.
	 */
	function mark_as_duplicate_of($choice_id, $skip_children_reorganization = false)
	{
		if($this->_id == 0 || is_numeric($choice_id) == false)
			return false;


		$parent = new ChoiceModel();
		$parent->setId($choice_id);
		if($choice_id != -1 && $parent->getData()->duplicatenumber != -1)
			return false;

		if($choice_id != -1 && ChoiceModel::exists($choice_id) == false)
			return false;

		//Eh, don't mark us duplicate ourselve!!
		if($this->_id == $choice_id)
			return false;

		//Set the status
		$this->_setDuplicateOf($choice_id);

		//Ok, we are already processing the children, and since we only have one level of dups,
		//the rest is not necessary
		if($skip_children_reorganization)
			return true;

		//Move this idea duplicates to the new parent duplicate
		$choicelist = new ChoiceListModel($GLOBALS['poll']);
		$choicelist->setFilterParameters(array("duplicate_items" => $this->_id));
		$choicelist->setDataFilter(array("include_minimal_data" => true));
		$entries = $choicelist->getData();

		foreach($entries->items as $entry)
		{
			$choice = new ChoiceModel();
			$choice->setId($entry->id);
			$choice->mark_as_duplicate_of($choice_id, true);
		}

		return true;
	}

	/**
	 * Set this solution duplicate number
	 */
	private function _setDuplicateOf($dup_number)
	{
		if($this->_id == 0 || !is_numeric($dup_number))
			return false;

		//Saving the change in the logs
		$oldvalue = db_result(it_query("SELECT duplicatenumber FROM qapoll_choice WHERE id='" . $this->_id . "'"));
		ChoiceLogModel::log($this->_id, ChoiceLogModel::$change["duplicate"], $oldvalue, $dup_number);

		$query = "UPDATE qapoll_choice SET duplicatenumber=" . $dup_number . " " .
			"WHERE id=" . $this->_id;
		it_query($query);
	}

	/**
	 * Change the target release of the choice.
	 * Needs $this->_id.
	 */
	function setTargetRelease($value)
	{
		if($this->_id == 0 || is_numeric($value) == false)
			return false;

		//Saving the change in the logs
		$oldvalue = db_result(it_query("SELECT release_target FROM qapoll_choice WHERE id='" . $this->_id . "'"));
		ChoiceLogModel::log($this->_id, ChoiceLogModel::$change["target_release"], $oldvalue, $value);

		it_query("UPDATE qapoll_choice SET release_target='$value' WHERE id='" . $this->_id . "'");

		return true;
	}

	/**
	 * Change the relation of the choice.
	 * Needs $this->_id.
	 */
	function setRelation($value)
	{
		if($this->_id == 0 || is_numeric($value) == false)
			return false;

		//Saving the change in the logs
		$oldvalue = db_result(it_query("SELECT relation_id FROM qapoll_choice WHERE id='" . $this->_id . "'"));
		ChoiceLogModel::log($this->_id, ChoiceLogModel::$change["relation"], $oldvalue, $value);

		it_query("UPDATE qapoll_choice SET relation_id='$value' WHERE id='" . $this->_id . "'");

		return true;
	}

	/**
	 * Get the list of available image links related to this choice.
	 */
	function getImageLinkList()
	{
		global $entrypoint;

		if($this->_imageLinkList == null)
			$this->_imageLinkList = new ImageLinkListModel($entrypoint, $this->_id);
		
		return $this->_imageLinkList->getData();
	}


	/**
	 * Get the list of duplicate report indicating that another idea is a duplicate of this one.
	 */
	function getDuplicateReportTargetingUsList()
	{
		if($this->_duplicate_reportList == null)
		{
			$this->_duplicate_reportList = new DuplicateReportListModel();
			$this->_duplicate_reportList->setFilterParameters(array("choiceid" => $this->_id));
		}
		
		return $this->_duplicate_reportList->getData();
	}

	/**
	 * Check if a given choice exists.
	 */
	static function exists($id)
	{
		$choice = db_result(it_query("SELECT id FROM qapoll_choice WHERE id='". $id . "'"));
		return ($choice != "");
	}

	/**
	 * Check if a given choice is a bug (choicetype = 0).
	 * Please check first that $id exists.
	 */
	static function isABug($id)
	{
		$choicetype = db_result(it_query("SELECT choicetype FROM qapoll_choice WHERE id='". $id . "'"));
		return (isset($choicetype) && $choicetype == 0);
	}

	/**
	 * Check if a given choice is a idea (choicetype = 1).
	 * Please check first that $id exists.
	 */
	static function isAnIdea($id)
	{
		$choicetype = db_result(it_query("SELECT choicetype FROM qapoll_choice WHERE id='". $id . "'"));
		return (isset($choicetype) && $choicetype == 1);
	}

	/**
	 * Recompute the total number of votes, taking into account the votes of the duplicates.
	 * NOTE: This is not yet recursive. Only the first level of dups will be processed.
	 * The vote of a given user on a choice and its duplicate will be only counted as one.
	 * Needs $this->_id
	 */
	function recomputeAndStoreTotalNumberOfVotes()
	{
		if($this->_id == 0)
			return;

		it_query_temporary("SELECT GREATEST(-1, LEAST(1,SUM(qapoll_vote.value))) as votes FROM qapoll_vote " .
			"WHERE qapoll_vote.choiceid = '" . $this->_id . "' OR " .
			"qapoll_vote.choiceid IN " .
			"(SELECT id from qapoll_choice WHERE duplicatenumber = '" . $this->_id . "') " .
			"GROUP BY qapoll_vote.userid", "temp_votes_users");

		it_query("UPDATE qapoll_choice ".
			"SET totalvotes = COALESCE((SELECT SUM(votes) FROM temp_votes_users), 0) " .
			"WHERE id='" . $this->_id . "'");
	}

	/**
	 * Return the choice type name
	 * e.g. "idea" if it is an idea. "item" is the generic name.
	 */
	function getChoiceTypeName()
	{
		$typename = "item"; 

		if($this->_id == 0)
			return $typename;

		if($this->getData()->choicetype == 1)
			$typename = "idea"; 
		else if($this->getData()->choicetype == 0)
			$typename = "bug";

		return $typename;
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
				$choicelist = new ChoiceListModel($GLOBALS['poll']);
				$choicelist->setFilterParameters(generate_GET_array($filter . 
					"&ordering=mostvotes&choice_ids=" . $ids_param));
				$choicelist->setDataFilter(array("include_minimal_data" => true));
				$filteredlist = $choicelist->getData();

				foreach($filteredlist->items as $filtereditem)
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


/**
 * Oh, here it is!
 * <3~
 */



?>
