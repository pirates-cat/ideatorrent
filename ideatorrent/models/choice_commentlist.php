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




class ChoiceCommentListModel extends ModelList
{


	/**
	 * Filter parameters.
	 * choiceid : Filter by choice id.
	 * userid : Filter by user id.
	 * states : Filter by the state of the comments.
	 */
	var $_choiceid = -1;
	var $_userid = -1;
	var $_states = array(
		"deleted" => false,
		"new" => true);

	/**
	 * Default constructor.
	 */
	function RelationListModel()
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
		$query = "SELECT qapoll_choice_comment.id, qapoll_choice_comment.date, qapoll_choice_comment.comment, " .
			"qapoll_choice_comment.userid, users.name as username " .
			"FROM qapoll_choice_comment " . 
			"LEFT JOIN users ON users.uid = qapoll_choice_comment.userid " .
			$this->_buildRelationListQuery_where() .
			"ORDER BY qapoll_choice_comment.date";

		$comments = it_query($query);

		//Store the result in a array
		$comment_list = array();
		while ($comment = db_fetch_object($comments))
		{
			//In each comment, attach the corresponding User model data.
			$comment->user_data = UserModel::getInstance($comment->userid)->getData();

			$comment_list[] = $comment;
		}

		return $comment_list;
	}

	function _buildRelationListQuery_where()
	{
		$where = "WHERE true ";

		if($this->_userid != -1)
			$where .= "AND qapoll_choice_comment.userid = '" . $this->_userid . "' ";

		if($this->_choiceid != -1)
			$where .= "AND qapoll_choice_comment.choiceid = '" . $this->_choiceid . "' ";

		if($this->_states['deleted'] == false)
		{
			$where .= "AND qapoll_choice_comment.status != -1 ";
		}
		if($this->_states['new'] == false)
		{
			$where .= "AND qapoll_choice_comment.status != 0 ";
		}

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

		if($getarray['userid'] != null && is_numeric($getarray['userid']))
			$this->_userid = $getarray['userid'];
		if($getarray['choiceid'] != null && is_numeric($getarray['choiceid']))
			$this->_choiceid = $getarray['choiceid'];

		//Save the states options
		if($getarray['state_new'] != null && is_numeric($getarray['state_new']))
			$this->_states['new'] = ($getarray['state_new'] != 0);
		if($getarray['state_deleted'] != null && is_numeric($getarray['state_deleted']))
			$this->_states['deleted'] = ($getarray['state_deleted'] != 0);
	
	}

	/**
	 * Delete the fetched entries. Set status = -1 and update comment count.
	 */
	function deleteEntries()
	{
		$entries_id = array();
		$entries = $this->getData();

		foreach($entries as $entry)
		{
			$comment = new ChoiceCommentModel();
			$comment->setId($entry->id);
			$comment->delete();
		}

		it_query($query);
	}

}

?>
