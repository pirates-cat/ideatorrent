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




class ChoiceLogListModel extends ModelList
{


	/**
	 * Filter parameters.
	 * choice_id : Filter by choice id
	 * user_id : Filter by user id
	 */
	var $_choice_id = -1;
	var $_user_id = -1;

	/**
	 * Default constructor.
	 */
	function ChoiceLogListModel()
	{
	}

	/**
	 * Get the data.
	 */
	function _loadData()
	{
		global $user;
		global $site;
		global $basemodule_url;

		//Get the choice list
		$query = "SELECT qapoll_choice_log.*, " .
			"users.name as username " .
			"FROM qapoll_choice_log " . 
			"LEFT JOIN users ON users.uid = qapoll_choice_log.userid " .
			$this->_buildChoiceLogListQuery_where() .
			"ORDER BY qapoll_choice_log.date DESC";

		$choicelogs = it_query($query);

		//Store the result in a array
		//Depending of the type, attach a model to it
		$choicelog_list = array();
		while ($choicelog = db_fetch_object($choicelogs))
		{
			if($choicelog->change == ChoiceLogModel::$change["title"] ||
				$choicelog->change == ChoiceLogModel::$change["description"] ||
				$choicelog->change == ChoiceLogModel::$change["comment_added"] ||
				$choicelog->change == ChoiceLogModel::$change["comment_deleted"])
			{
			}
			else if($choicelog->change == ChoiceLogModel::$change["status"])
			{
				$choicelog->old_status_name = getStatusString($choicelog->old_value);
				$choicelog->new_status_name = getStatusString($choicelog->new_value);
			}
			else if($choicelog->change == ChoiceLogModel::$change["category"])
			{
				if($choicelog->old_value != -1)
				{
					$choicelog->old_category_model = new CategoryModel();
					$choicelog->old_category_model->setId($choicelog->old_value);
				}
				if($choicelog->new_value != -1)
				{
					$choicelog->new_category_model = new CategoryModel();
					$choicelog->new_category_model->setId($choicelog->new_value);
				}
			}
			else if($choicelog->change == ChoiceLogModel::$change["relation"])
			{
				if($choicelog->old_value != -1)
				{
					$choicelog->old_relation_model = new RelationModel();
					$choicelog->old_relation_model->setId($choicelog->old_value);
				}
				if($choicelog->new_value != -1)
				{
					$choicelog->new_relation_model = new RelationModel();
					$choicelog->new_relation_model->setId($choicelog->new_value);
				}
			}
			else if($choicelog->change == ChoiceLogModel::$change["relationsubcat"])
			{
				if($choicelog->old_value != -1)
				{
					$choicelog->old_relationsubcat_model = new RelationSubcategoryModel();
					$choicelog->old_relationsubcat_model->setId($choicelog->old_value);
				}
				if($choicelog->new_value != -1)
				{
					$choicelog->new_relationsubcat_model = new RelationSubcategoryModel();
					$choicelog->new_relationsubcat_model->setId($choicelog->new_value);
				}
			}
			else if($choicelog->change == ChoiceLogModel::$change["target_release"])
			{
				if($choicelog->old_value != -1)
				{
					$choicelog->old_release_model = new ReleaseModel();
					$choicelog->old_release_model->setId($choicelog->old_value);
				}
				if($choicelog->new_value != -1)
				{
					$choicelog->new_release_model = new ReleaseModel();
					$choicelog->new_release_model->setId($choicelog->new_value);
				}
			}
			else if($choicelog->change == ChoiceLogModel::$change["duplicate"])
			{
				if($choicelog->old_value != -1)
				{
					$choicelog->old_duplicate_model = new ChoiceModel();
					$choicelog->old_duplicate_model->setId($choicelog->old_value);
				}
				if($choicelog->new_value != -1)
				{
					$choicelog->new_duplicate_model = new ChoiceModel();
					$choicelog->new_duplicate_model->setId($choicelog->new_value);
				}
			}
			else if($choicelog->change == ChoiceLogModel::$change["tags"])
			{

			}
			else if($choicelog->change == ChoiceLogModel::$change["admintags"])
			{

			}
			else if($choicelog->change == ChoiceLogModel::$change["solution_linked"])
			{
				$choicelog->solutionlink_model = new ChoiceSolutionLinkModel();
				$choicelog->solutionlink_model->setId($choicelog->choicesolutionlinkid);
				$choicelog->choice_model = new ChoiceModel();
				$choicelog->choice_model->setId($choicelog->new_value);
			}
			else if($choicelog->change == ChoiceLogModel::$change["solution_unlinked"])
			{
				$choicelog->solutionlink_model = new ChoiceSolutionLinkModel();
				$choicelog->solutionlink_model->setId($choicelog->choicesolutionlinkid);
				$choicelog->choice_model = new ChoiceModel();
				$choicelog->choice_model->setId($choicelog->old_value);
			}
			else if($choicelog->change == ChoiceLogModel::$change["solution_title"])
			{
				$choicelog->solutionlink_model = new ChoiceSolutionLinkModel();
				$choicelog->solutionlink_model->setId($choicelog->choicesolutionlinkid);
			}
			else if($choicelog->change == ChoiceLogModel::$change["solution_description"])
			{
				$choicelog->solutionlink_model = new ChoiceSolutionLinkModel();
				$choicelog->solutionlink_model->setId($choicelog->choicesolutionlinkid);
			}

			$choicelog_list[] = $choicelog;

		}

		return $choicelog_list;
	}

	function _buildChoiceLogListQuery_where()
	{
		$where = "WHERE true ";

		if($this->_choice_id != -1)
			$where .= "AND choiceid = '" . $this->_choice_id . "' ";
		if($this->_user_id != -1)
			$where .= "AND userid = '" . $this->_user_id . "' ";

		return $where;
	}

	/**
	 * Set the filter parameters. Giving the GET array is usually fine.
	 * It will sanitize the necessary stuff.
	 */
	function setFilterParameters($getarray)
	{
		//Save the array first.
		$this->_filter_array = $getarray;

		if($getarray['choice_id'] != null && is_numeric($getarray['choice_id']))
			$this->_choice_id = db_escape_string($getarray['choice_id']);
		if($getarray['user_id'] != null && is_numeric($getarray['user_id']))
			$this->_user_id = db_escape_string($getarray['user_id']);
	}

}

?>
