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




class ReportModel extends Model
{

	/**
	 * List of report status.
	 */
	static public $status = array(
		"processed" => -3,
		"deleted" => -2,
		"new" => 1
	);

	/**
	 * List of report item type.
	 */
	static public $item_type = array(
		"choice" => 1,
		"solution" => 2,
		"comment" => 3
	);

	/**
	 * List of report type.
	 */
	static public $type = array(
		"spam" => 1,
		"offensive" => 2,
		"indev" => 3,
		"implemented" => 4,
		//Irrelevant too
		"not_an_idea" => 5
	);

	/**
	 * The report id.
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
			return db_fetch_object(it_query("SELECT * FROM qapoll_report WHERE id='" . $this->_id . "'"));
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
				it_query(
					"INSERT INTO qapoll_report(itemid, report_type, item_type, votes, date) " .
					"VALUES (" .
					$this->_data->itemid . ", " . 
					"'" . $this->_data->report_type . "'," .
					"'" . $this->_data->item_type. "'," .
					"'1'," .
					" NOW())");

				//Save the id of the newly inserted choice solution
				$this->_id = db_last_insert_id("qapoll_report", "id");
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
	 * Set the type of the report. See enum.
	 */
	function setReportType($reportype)
	{
		if(is_numeric($reportype))
			$this->_data->report_type = $reportype;
	}

	/**
	 * Set the affected item id. See enum.
	 */
	function setAffectedId($itemid)
	{
		if(is_numeric($itemid))
			$this->_data->itemid = $itemid;
	}

	/**
	 * Set the type of the item id. See enum.
	 */
	function setItemType($item_type)
	{
		if(is_numeric($item_type))
			$this->_data->item_type = $item_type;
	}

	/**
	 * Add the current user vote to this report.
	 * Return false if the user already reported it.
	 */
	function addVote()
	{
		global $user;

		if($this->_data->item_type == null || $this->_data->itemid == null ||
			$this->_data->report_type == null)
			return false;

		//Check if the user already reported this
		$query = "SELECT COUNT(id) FROM qapoll_report_vote WHERE item_type = '" . $this->_data->item_type . "' AND " .
			"itemid = '" . $this->_data->itemid . "' AND report_type = '" . $this->_data->report_type . "' AND " .
			"userid = '" . $user->uid . "'";
		$result = db_result(it_query($query));

		if($result > 0)
			return false;

		//Now let's find out if a report already exists
		$reportlist = new ReportListModel();
		$reportlist->setFilterParameters(array("itemid" => $this->_data->itemid, "item_type" => $this->_data->item_type,
			"report_type" => $this->_data->report_type));
		$list = $reportlist->getData();
		if(count($list->items) > 0)
		{
			//Report already exists. Just add one vote.
			$query = "UPDATE qapoll_report SET votes = votes + 1 WHERE id = " . $list->items[0]->id;
			it_query($query);
		}
		else
		{
			//Report does not exists. Create it.
			//$query = "INSERT INTO qapoll_report(itemid, report_type, item_type, votes, date) VALUES"
			$this->store();
		}

		//Store the user vote
		it_query(
			"INSERT INTO qapoll_report_vote(itemid, report_type, item_type, userid, date) " .
			"VALUES (" . $this->_data->itemid . ", " . 
			"'" . $this->_data->report_type . "'," .
			"'" . $this->_data->item_type. "'," .
			"'" . $user->uid . "'," .
			" NOW())");

		return true;
	}

	/**
	 * Process and accept this report. Depending of the contents, it may be deleting the entry (spam reports),
	 * marking as implemented (implemented reports), and so on
	 */
	function accept()
	{
		if($this->_id == 0)
			return false;

		if($this->getData()->report_type == ReportModel::$type['spam'])
		{
			if($this->getData()->item_type == ReportModel::$item_type['choice'])
			{
				$model = new ChoiceModel();
				$model->setId($this->getData()->itemid);
				$model->delete();
			}
			else if($this->getData()->item_type == ReportModel::$item_type['comment'])
			{
				$model = new ChoiceCommentModel();
				$model->setId($this->getData()->itemid);
				$model->delete();
			}
			else if($this->getData()->item_type == ReportModel::$item_type['solution'])
			{
				$model = new ChoiceSolutionModel();
				$model->setId($this->getData()->itemid);
				$model->delete();
			}
		}
		else if($this->getData()->report_type == ReportModel::$type['offensive'])
		{
			if($this->getData()->item_type == ReportModel::$item_type['comment'])
			{
				$model = new ChoiceCommentModel();
				$model->setId($this->getData()->itemid);
				$model->delete();
			}
		}
		else if($this->getData()->report_type == ReportModel::$type['indev'])
		{
			if($this->getData()->item_type == ReportModel::$item_type['choice'])
			{
				$model = new ChoiceModel();
				$model->setId($this->getData()->itemid);
				$model->setStatus(ChoiceModel::$choice_status["workinprogress"]);
			}
		}
		else if($this->getData()->report_type == ReportModel::$type['implemented'])
		{
			if($this->getData()->item_type == ReportModel::$item_type['choice'])
			{
				$model = new ChoiceModel();
				$model->setId($this->getData()->itemid);
				$model->setStatus(ChoiceModel::$choice_status["done"]);
			}
		}
		else if($this->getData()->report_type == ReportModel::$type['not_an_idea'])
		{
			if($this->getData()->item_type == ReportModel::$item_type['choice'])
			{
				$model = new ChoiceModel();
				$model->setId($this->getData()->itemid);
				$model->setStatus(ChoiceModel::$choice_status["not_an_idea"]);
			}
			else if($this->getData()->item_type == ReportModel::$item_type['solution'])
			{
				$model = new ChoiceSolutionModel();
				$model->setId($this->getData()->itemid);
				$model->delete();
			}
		}
	

		//Set the status to processed
		$this->_setStatus(ReportModel::$status['processed']);

		return true;
	}

	/**
	 * Process and accept this report in an alternate way.
	 */
	function accept2()
	{
		if($this->getData()->report_type == ReportModel::$type['implemented'])
		{
			if($this->getData()->item_type == ReportModel::$item_type['choice'])
			{
				$model = new ChoiceModel();
				$model->setId($this->getData()->itemid);
				$model->setStatus(ChoiceModel::$choice_status["already_done"]);
			}
		}

		//Set the status to processed
		$this->_setStatus(ReportModel::$status['processed']);

		return true;
	}

	/**
	 * Process and reject this report. Basically dropping it.
	 */
	function reject()
	{
		//Set the status to deleted
		$this->_setStatus(ReportModel::$status['deleted']);

		return true;
	}

	/**
	 * Set the status.
	 */
	function _setStatus($status)
	{
		if($this->_id == 0 || is_numeric($status) == false)
			return false;

		$query = "UPDATE qapoll_report SET status=" . $status . " " .
			"WHERE id=" . $this->_id;
		it_query($query);
	}


}

?>
