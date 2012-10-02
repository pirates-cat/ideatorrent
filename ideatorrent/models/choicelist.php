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




class ChoiceListModel extends ModelList
{

	/**
	 * PollModel related to this list.
	 */
	var $_pollModel = null;

	/**
	 * The current page to show.
	 */
	var $_page = 1;

	/**
	 * The number of rows to extract.
	 */
	var $_numberRowsPerPage = -1;

	/**
	 * Filter parameters. They are used to restrict the number of returned rows.
	 * keywords : the keywords to search
	 * keywords_skip_common_words : Shall we remove common words from the keywords, such as "and, "or", ...
	 * keywords_any : Shall all the keywords be matched (false) or not (true)
	 * ordering : the ordering of the items. Meaning should be straightforward from the names.
	 * enum:[mosthype leasthype mostvotes leastvotes new old] and others, cf code.
	 * choice_ids: A comma separated list of choice ids we want to restrict our search to.
	 * category : the items must match this category. -1 has no effect.
	 * states : the states of the items we are looking for.
	 * all_states : override the previous options by telling we want to show all statuses
	 * userid : We look for the items of this user only. -1 means all.
	 * solution_userid : We look for the items which contains at least one solution of this user. -1 means we don't care.
	 * type_[bug|idea] : The type of the items to display.
	 * [nothing|bug|spec|thread]_attached : Show items with nothing/a bug/a spec/a thread attached to.
	 * attachment_operator : The operator to use for the above filter. E.g. a bug OR/AND a spec.
	 * duplicate_choices : Specify if we should show the duplicate choice or not. -3 means we show only duplicates. 
	 * -2 means we show all. -1 means we show only non-dup and others number means we show the duplicate of the given idea number.
	 * user_voted_items: Show items that the given user id has voted.
	 * user_voted_items_vote_value: Show items that the given user id has voted, with the given vote value. Needs the previous option to be set.
	 *  -3 means we don't care, -2 means no vote cast.
	 * release: Show the items with the given release id. -2 means we don't care, -1 means no release id set, others number are the id of the release.
	 * user_commented_items: Show items that the given user id has commented.
	 * user_bookmarked_items: Show items that have been bookmarked by the given user id.
	 * relation: Show items that have the given relation id. -2 means we don't care.
	 * relation_subcategory_id: Show items that belong to the relation_subcategory_id id. -2 means we don't care.
	 * tags: Show items filtered by these tags. The tags variable contains all the tags separated by whitespaces.
	 * tags_operator: The search operator to use with multiple tags. Can be "AND" or "OR".
	 * admintags: Show items filtered by these admin tags. The tags variable contains all the tags separated by whitespaces.
	 * admintags_operator: The search operator to use with multiple admin tags. Can be "AND" or "OR".
	 * choicesolutionid: Only show ideas which is linked to this solution
	 */
	var $_keywords = null;
	var $_keywords_skip_common_words = false;
	var $_keywords_any = false;
	var $_ordering = "mostvotes";
	var $_choice_ids = null;
	var $_category = -1;
	var $_states = array(
		"deleted" => false,
		"new" => true,
		"needinfos" => true,
		"blueprint_approved" => true,		
		"workinprogress" => true,
		"done" => true,
		"already_done" => true,		
		"unapplicable" => true,
		"not_an_idea" => true,
		"awaiting_moderation" => false);
	var $_all_states = false;
	var $_userid = -1;
	var $_solution_userid = -1;
	var $_type_bug = true;
	var $_type_idea = true;
	var $_nothing_attached = true;
	var $_bug_attached = true;
	var $_spec_attached = true;
	var $_thread_attached = true;
	var $_attachment_operator = "OR";
	var $_duplicate_choices = -1;
	var $_user_voted_items = -1;
	var $_user_voted_items_vote_value = -3;
	var $_release = -2;
	var $_user_commented_items = -1;
	var $_user_bookmarked_items = -1;
	var $_relation = -2;
	var $_relation_subcategory_id = -2;
	var $_tags = null;
	var $_tags_operator = "AND";
	var $_admintags = null;
	var $_admintags_operator = "AND";
	var $_choicesolutionid = -1;


	/**
	 * The original filter array currently in use.
	 */
	var $_filter_array = null;

	/**
	 * Data filters. They are used to restrict the columns of the data returned.
	 * This can be very usefull when we only need something specific, and we
	 * don't want to waste processing time.
	 * include_minimal_data: Override all the below options by setting them to false.
	 * include_approval_vote: shall we include the user approval vote?
	 * include_user_bookmark: shall we include if the user bookmarked each idea?
	 * include_category_extra_data: shall we include more than the category id, e.g. the category name?
	 * include_relation_extra_data: shall we include more than the relation id, e.g. the relation name?
	 * include_user_extra_data : shall we include more than the user id, e.g. the user name?
	 * include_item_comment_unread_flag: Specify if we should also compute the "unread" flag: When an idea or a comment is no older than one week,
	 * and was not read by the user, set the item_comment_unread_flag col to true.
	 * include_item_edition_unread_flag: Specify if we should also compute the "unread" flag: When an was edited less than one week ago,
	 * and was not read by the user, set the include_item_edition_unread_flag col to true.
	 * include_choice_solutions: Specify if we should also fetch the choice solution (separate subquery).
	 * include_target_release : Do we include the target release name?
	 */
	var $_include_minimal_data = false;
	var $_include_user_approval_vote = true;
	var $_include_user_bookmark = true;
	var $_include_category_extra_data = true;
	var $_include_relation_extra_data = true;
	var $_include_relation_subcategory_extra_data = true;
	var $_include_user_extra_data = true;
	var $_include_item_comment_unread_flag = false;
	var $_include_item_edition_unread_flag = false;
	var $_include_choice_solutions_flag = true;
	var $_include_target_release = false;

	/**
	 * The data filter array currently in use.
	 */
	var $_data_filter = null;

	/**
	 * An array of the models used to get additional data when loading data.
	 */
	var $additional_models = array();

	/**
	 * List to common keywords to ignore if $_keywords_skip_common_words == true
	 */
	var $_common_keywords = array('and', 'or', 'because', 'for', 'against', 'the', 'a', 'an', 'brainstorm', 'of', 'into', 'all', 'in',
		'out', 'why', 'to', 'maybe', 'on', 'out', 'do', 'not', 'by', 'via', 'is', 'are', 'this', 'these', 'which', 'well', 'put', 'into',
		'during', 'does', 'inside');



	/**
	 * Default constructor.
	 * Need a pollModel to know from which poll to extract data.
	 * The Start and Number parameters indicate the position of the
	 * first row and the number of row to extract.
	 */
	function ChoiceListModel($pollModel, $page = 1, $numberRowsPerPage = -1)
	{
		$this->_pollModel = $pollModel;
		$this->_page = (is_numeric($page))?$page:1;
		$this->_numberRowsPerPage = (is_numeric($numberRowsPerPage) && $numberRowsPerPage != -1)?
			$numberRowsPerPage:QAPollConfig::getInstance()->getValue("default_number_item_per_page");

	}


	function _loadData()
	{
		global $entrypoint;

		//Get the choice list
		$query = $this->_buildChoiceListQuery();
		$choices = it_query($query);

		//Store the result in a array
		$choicelist = array();
		while ($choice = db_fetch_object($choices))
			$choicelist[] = $choice;

		//For each choice, fetch the corresponding solution list
		if($this->_include_choice_solutions_flag == true)
		{
			foreach($choicelist as $choice)
			{
				$choicesolutionlist = new ChoiceSolutionListModel();
				$choicesolutionlist->setFilterParameters(array("choice_id" => $choice->id));
				$choice->solutions = $choicesolutionlist->getData();
				//Store the model
				$this->additional_models["choicesolutionlist"][$choice->id] = $choicesolutionlist;
			}
		}

		$poll = $this->_pollModel->getData();

		//Mix the data;
		$data->poll = $poll;
		$data->items = $choicelist;
		$data->page = $this->_page;
		$data->numberRowsPerPage = $this->_numberRowsPerPage;
		$data->rowCount = $this->_getRowCount();
		//Use the entry point title as the title
		$data->title = $entrypoint->getData()->title;

		return $data;
	}

	function _buildChoiceListQuery()
	{
		global $user;

		$query = "SELECT qapoll_choice.id, qapoll_choice.title, qapoll_choice.bugid, qapoll_choice.specid, qapoll_choice.forumid, " .
			"qapoll_choice.userid as userid, qapoll_choice.date, qapoll_choice.status, qapoll_choice.description, " .
			"qapoll_choice.duplicatenumber, qapoll_choice.totalapprovalvotes, qapoll_choice.whiteboard, qapoll_choice.release_target, " . 
			"qapoll_choice.categoryid, qapoll_choice.relation_id, qapoll_choice.relation_subcategory_id, " . 
			//Get the user approval vote only if logged.
			(($user->uid != null && $this->_include_user_approval_vote == true)?"qapoll_choice_approval_vote.value as myapprovalvote, ":"") .
			//Get the user bookmark only if logged.
			(($user->uid != null && $this->_include_user_bookmark == true)?"(qapoll_choice_bookmark.date IS NOT NULL) as mybookmark, ":"") .
			//Get the category name
			(($this->_include_category_extra_data == true)?"qapoll_poll_category.name as catname, ":"") .
			//Get the relation name
			(($this->_include_relation_extra_data == true)?"qapoll_poll_relation.name as relation_name, " . 
				"qapoll_poll_relation.url_name as relation_url_name, ":"") .
			//Get the relation subcategory name
			(($this->_include_relation_subcategory_extra_data == true)?"qapoll_poll_relation_subcategory.name as relationsubcatname, ":"") .
			//Get the target release name
			(($this->_include_target_release == true)?"qapoll_release.long_name as releasename, ":"") .
			//Get the user name
			(($this->_include_user_extra_data == true)?"users.name as username, ":"") .
			"qapoll_choice.totalvotes as votes, qapoll_choice.ideavotes as ideavotes," .
			"qapoll_choice.relation_id as relation_id, " .

			//If we are ordering by search relevance, compute the search relevance
			(($this->_ordering == "search-relevance" && count($this->_keywordslist) > 0)?
				"ts_rank_cd(totalsearchable_index_col, keywordsquery, 32) as search_rank, ":"") .

			//Compute the item/comment unread flag. See the variable declaration for infos.
			//Subquery independant to a query variable: executed once only
			(($this->_include_item_comment_unread_flag == 1 && $user->uid != null)?
				"((NOW() - COALESCE(qapoll_choice.last_comment_date, qapoll_choice.date)) < interval '1 week' AND 
				qapoll_choice.id NOT IN (
					select distinct qapoll_choice.id from qapoll_choice 
					LEFT JOIN qapoll_log ON qapoll_log.choice_id = qapoll_choice.id AND
					qapoll_log.userid = '" . $user->uid . "' AND qapoll_log.type='2'
					where qapoll_log.date IS NOT NULL AND
					COALESCE(qapoll_choice.last_comment_date, qapoll_choice.date) < qapoll_log.date
					AND (NOW() - COALESCE(qapoll_choice.last_comment_date, qapoll_choice.date)) < interval '1 week'
				)) as item_comment_unread_flag, ":"") .

			//Compute the item edition unread flag. See the variable declaration for infos.
			//Subquery independant to a query variable: executed once only
			(($this->_include_item_edition_unread_flag == 1 && $user->uid != null)?
				"((NOW() - COALESCE(qapoll_choice.last_edit_date, qapoll_choice.date)) < interval '1 week' AND 
				qapoll_choice.id NOT IN (
					select distinct qapoll_choice.id from qapoll_choice 
					LEFT JOIN qapoll_log ON qapoll_log.choice_id = qapoll_choice.id AND
					qapoll_log.userid = '" . $user->uid . "' AND qapoll_log.type='2'
					where qapoll_log.date IS NOT NULL AND
					COALESCE(qapoll_choice.last_edit_date, qapoll_choice.date) < qapoll_log.date
					AND (NOW() - COALESCE(qapoll_choice.last_edit_date, qapoll_choice.date)) < interval '1 week'
				)) as item_edition_unread_flag, ":"") .

			"qapoll_choice.commentscount, qapoll_choice.last_status_change, qapoll_choice.last_comment_date, " .
			"qapoll_choice.last_edit_date " . 
			"FROM " . 

			//Compute once the tsquery for the text search
			((count($this->_keywordslist) > 0)?"to_tsquery('" . 
				implode((($this->_keywords_any == false)?" & ":" | "), $this->_keywordslist) . 
				"') as keywordsquery, ":"") . 
		
			//Main FROM table
			"qapoll_choice " . 


			//Include extras user infos
			(($this->_include_user_extra_data == true)?
				"LEFT JOIN users ON users.uid = qapoll_choice.userid ":"") .

			//Get the user approval vote only if logged.
			(($user->uid != null && $this->_include_user_approval_vote == true)?
				"LEFT JOIN qapoll_choice_approval_vote ON qapoll_choice_approval_vote.choiceid = qapoll_choice.id AND " .
				"qapoll_choice_approval_vote.userid = " . $user->uid . " ":"") .

			//Get the user bookmark only if logged.
			(($user->uid != null && $this->_include_user_bookmark == true)?
				"LEFT JOIN qapoll_choice_bookmark ON qapoll_choice_bookmark.choiceid = qapoll_choice.id AND " .
				"qapoll_choice_bookmark.userid = " . $user->uid . " ":"") .

			//Use the user bookmark filter
			(($this->_user_bookmarked_items != -1)?
				"JOIN qapoll_choice_bookmark as qapoll_choice_bookmark_user ON " . 
				"qapoll_choice_bookmark_user.choiceid = qapoll_choice.id AND " .
				"qapoll_choice_bookmark_user.userid = '" .	$this->_user_bookmarked_items . "' ":"") .

			//Use the Choice solution id filter
			(($this->_choicesolutionid != -1)?
				"JOIN qapoll_choice_solution_link ON " . 
				"qapoll_choice_solution_link.choiceid = qapoll_choice.id AND " . 
				"qapoll_choice_solution_link.status = 1 AND " . 
				"qapoll_choice_solution_link.choicesolutionid = '" . $this->_choicesolutionid . "' ":"") . 

			//Include extras category infos
			(($this->_include_category_extra_data == true)?
				"LEFT JOIN qapoll_poll_category ON qapoll_choice.categoryid = qapoll_poll_category.id ":"") .

			//Include extras relation infos
			(($this->_include_relation_extra_data == true)?
				"LEFT JOIN qapoll_poll_relation ON qapoll_choice.relation_id = qapoll_poll_relation.id ":"") .

			//Include extra relation subcategory infos
			(($this->_include_relation_subcategory_extra_data == true)?
				"LEFT JOIN qapoll_poll_relation_subcategory ON " .
					"qapoll_choice.relation_subcategory_id = qapoll_poll_relation_subcategory.id ":"") .

			//Include extra relation subcategory infos
			(($this->_include_target_release == true)?
				"LEFT JOIN qapoll_release ON " .
					"qapoll_choice.release_target = qapoll_release.id ":"") .

			$this->_buildChoiceListQuery_where() . 
			$this->_buildChoiceListQuery_orderby() .
			"LIMIT " . $this->_numberRowsPerPage . " OFFSET " . ($this->_page - 1)*$this->_numberRowsPerPage;

		return $query;
	}

	function _buildChoiceListQuery_where()
	{
		global $user;

		$where = "WHERE qapoll_choice.pollid='" . $this->_pollModel->getData()->id . "' ";

		//Use the keywords
		//GENERIC SQL search method. Commented in favor of the much more efficient full text search of posgresql
		/**
		$keywordlist = array_filter(explode(" ", $this->_keywords), create_function('$var', 'return ($var != null);'));
		
		$where .= "AND (";
		$keyword_cond_inserted = false;
		for($i=0; $i < count($keywordlist); $i++)
		{
			//Remove common words if asked
			if($this->_keywords_skip_common_words && in_array(strtolower($keywordlist[$i]), $this->_common_keywords))
				continue;

			//Use the AND or OR logic according to what was asked with $this->_keywords_any
			if($keyword_cond_inserted == true)
				$where .= ($this->_keywords_any == false)?"AND ":"OR ";

			$keyword_cond_inserted = true;
			//WARNING: Drupal replace %b, %d and others by arguments. Put %% to get a %. Cf code of it_query.
			$where .= "(( qapoll_choice.title ILIKE '%%" . $keywordlist[$i] . "%%') OR " .
				 "(qapoll_choice.description ILIKE '%%" . $keywordlist[$i] . "%%')) ";
		}
		if($keyword_cond_inserted == false)
			$where .= "true";
		$where .= ") ";*/

		//Use the keywords, using the postgresql feature
		if(count($this->_keywordslist) > 0)
		{
			$where .= "AND totalsearchable_index_col @@ keywordsquery ";
		}


		//Filter by tags.
		if($this->_tags != null)
		{
			//Explode the tags, sanitize them
			$taglist = array_filter(explode(" ", $this->_tags), create_function('$var', 'return ($var != null);'));
			$taglist = array_map(split("::", "TagModel::sanitizeTagName"), $taglist);

			if($this->_tags_operator == "OR" || $this->_tags_operator == "or")
			{
				$list_tag_queries = array();

				foreach($taglist as $tag)
				{
					$list_tag_queries[] = "LOWER(qapoll_choice_tag.name) = LOWER('" . $tag . "') ";
				}

				$where .= "AND qapoll_choice.id in " . 
					"(SELECT qapoll_choice_tag.choice_id from qapoll_choice_tag " .
					"WHERE qapoll_choice_tag.admin = '0' AND (" . implode("OR ", $list_tag_queries) . ")) ";
			}
			//AND operator
			else
			{
				$list_tag_queries = array();

				foreach($taglist as $tag)
				{
					$list_tag_queries[] = "qapoll_choice.id in " . 
						"(SELECT qapoll_choice_tag.choice_id from qapoll_choice_tag WHERE " .
						"qapoll_choice_tag.admin = '0' AND LOWER(qapoll_choice_tag.name) = LOWER('" . $tag . "')) ";
				}

				$where .= "AND " . implode("AND ", $list_tag_queries);
			}
		}

		//Filter by admin tags.
		if($this->_admintags != null)
		{
			//Explode the tags, sanitize them
			$taglist = array_filter(explode(" ", $this->_admintags), create_function('$var', 'return ($var != null);'));
			$taglist = array_map(split("::", "TagModel::sanitizeTagName"), $taglist);

			if($this->_admintags_operator == "OR" || $this->_admintags_operator == "or")
			{
				$list_tag_queries = array();

				foreach($taglist as $tag)
				{
					$list_tag_queries[] = "LOWER(qapoll_choice_tag.name) = LOWER('" . $tag . "') ";
				}

				$where .= "AND qapoll_choice.id in " . 
					"(SELECT qapoll_choice_tag.choice_id from qapoll_choice_tag " .
					"WHERE qapoll_choice_tag.admin = '1' AND (" . implode("OR ", $list_tag_queries) . ")) ";
			}
			//AND operator
			else
			{
				$list_tag_queries = array();

				foreach($taglist as $tag)
				{
					$list_tag_queries[] = "qapoll_choice.id in " . 
						"(SELECT qapoll_choice_tag.choice_id from qapoll_choice_tag WHERE " .
						"qapoll_choice_tag.admin = '1' AND LOWER(qapoll_choice_tag.name) = LOWER('" . $tag . "')) ";
				}

				$where .= "AND " . implode("AND ", $list_tag_queries);
			}
		}


		//Use the category filter
		if($this->_category != -1)
		{
			$where .= "AND qapoll_choice.categoryid = '" . $this->_category . "' ";
		}

		//Use the user filter
		if($this->_userid != -1)
		{
			$where .= "AND qapoll_choice.userid = '" . $this->_userid . "' ";
		}

		//Use the solution user filter
		if($this->_solution_userid != -1)
		{
			$where .= "AND qapoll_choice.id IN (" .
				"SELECT qapoll_choice_solution_link.choiceid " .
				"FROM qapoll_choice_solution " .
				"JOIN qapoll_choice_solution_link ON qapoll_choice_solution.id = qapoll_choice_solution_link.choicesolutionid " .
				"WHERE qapoll_choice_solution.userid = '" . $this->_solution_userid . "' " .
				"AND qapoll_choice_solution.status != -2 " .
				"AND qapoll_choice_solution_link.status != -2 " .
				") ";
		}

		//Use the state filter.
		if($this->_states['deleted'] == false && $this->_all_states == false)
		{
			$where .= "AND qapoll_choice.status != '-2' ";
		}
		if($this->_states['new'] == false && $this->_all_states == false)
		{
			$where .= "AND qapoll_choice.status != 0 AND qapoll_choice.status != '-1' ";
		}
		if($this->_states['needinfos'] == false && $this->_all_states == false)
		{
			$where .= "AND qapoll_choice.status != 1 ";
		}
		if($this->_states['blueprint_approved'] == false && $this->_all_states == false)
		{
			$where .= "AND qapoll_choice.status != 6 ";
		}	
		if($this->_states['workinprogress'] == false && $this->_all_states == false)
		{
			$where .= "AND qapoll_choice.status != 2 ";
		}
		if($this->_states['done'] == false && $this->_all_states == false)
		{
			$where .= "AND qapoll_choice.status != 3 ";
		}
		if($this->_states['already_done'] == false && $this->_all_states == false)
		{
			$where .= "AND qapoll_choice.status != 5 ";
		}
		if($this->_states['unapplicable'] == false && $this->_all_states == false)
		{
			$where .= "AND qapoll_choice.status != 4 ";
		}
		if($this->_states['not_an_idea'] == false && $this->_all_states == false)
		{
			$where .= "AND qapoll_choice.status != 7 ";
		}
		if($this->_states['awaiting_moderation'] == false && $this->_all_states == false)
		{
			$where .= "AND qapoll_choice.status != 8 ";
		}

		//Use the choice_ids filter
		if($this->_choice_ids != null)
		{
			$where .= "AND qapoll_choice.id IN (" . $this->_choice_ids . ") ";
		}

		//Use the type filter (bugs/ideas)
		if($this->_type_bug == false)
		{
			$where .= "AND qapoll_choice.choicetype != 0 ";
		}
		if($this->_type_idea == false)
		{
			$where .= "AND qapoll_choice.choicetype != 1 ";
		}

		//Use the "attached" filter.
		$attachments = array();
		if($this->_bug_attached == true)
		{
			$attachments[] = " qapoll_choice.bugid IS NOT NULL ";
		}
		if($this->_spec_attached == true)
		{
			$attachments[] = " qapoll_choice.specid IS NOT NULL ";
		}
		if($this->_thread_attached == true)
		{
			$attachments[] = " qapoll_choice.forumid IS NOT NULL ";
		}
		if(count($attachments) > 0 || $this->_nothing_attached == true)
			$where .= "AND ( ";
		if(count($attachments) > 0)
			$where .= "( " . implode($this->_attachment_operator, $attachments) . ") ";
		if($this->_nothing_attached == true)
		{
			if(count($attachments) > 0)
				$where .= " OR ";
			$where .= "(qapoll_choice.bugid IS NULL AND qapoll_choice.specid IS NULL AND qapoll_choice.forumid IS NULL ) "; 
		}
		if(count($attachments) > 0 || $this->_nothing_attached == true)
			$where .= ") ";
		if(count($attachments) == 0 && $this->_nothing_attached == false)
			$where .= "AND true=false ";

		//Use the duplicate filter
		if($this->_duplicate_choices > -2)
			$where .= "AND qapoll_choice.duplicatenumber = '" . $this->_duplicate_choices . "' ";
		if($this->_duplicate_choices == -3)
			$where .= "AND qapoll_choice.duplicatenumber != '-1' ";

		//When using the mosthype-*, only allow a given age for the items
		if($this->_ordering == "mosthype-day" || $this->_ordering == "mosthype-week" ||
			$this->_ordering == "mosthype-month" || $this->_ordering == "mosthype-6-months")
		{

			if($this->_ordering == "mosthype-day")
				$where .= "AND (NOW() - qapoll_choice.date) < interval '1 day' ";
			if($this->_ordering == "mosthype-week")
				$where .= "AND (NOW() - qapoll_choice.date) < interval '1 week' ";
			if($this->_ordering == "mosthype-month")
				$where .= "AND (NOW() - qapoll_choice.date) < interval '1 month' ";
			if($this->_ordering == "mosthype-6-months")
				$where .= "AND (NOW() - qapoll_choice.date) < interval '6 month' ";
		}

		//Use the promoted/demoted/voted filter
		if($this->_user_voted_items != -1 && $this->_user_voted_items_vote_value != -3)
		{
			$where .= "AND qapoll_choice.id " . 
				(($this->_user_voted_items_vote_value == -2)?
				"NOT ":"") .
				"IN (" .
				"SELECT qapoll_choice_solution_link.choiceid " .
				"FROM qapoll_choice_solution_link " .
				"JOIN qapoll_vote as qapoll_vote_user ON qapoll_vote_user.choicesolutionid = qapoll_choice_solution_link.choicesolutionid AND " .
				"qapoll_vote_user.userid = " . $this->_user_voted_items . " " .
				(($this->_user_voted_items_vote_value != -2)?
				"AND qapoll_vote_user.value = " . 	$this->_user_voted_items_vote_value:"") .
				") ";
		}


		//Use the release filter
		if($this->_release != -2)
			$where .= "AND qapoll_choice.release_target = '" . $this->_release . "' ";

		//Use the relation filter
		if($this->_relation != -2)
			$where .= "AND qapoll_choice.relation_id = '" . $this->_relation . "' ";

		//Use the relation filter
		if($this->_relation_subcategory_id != -2)
			$where .= "AND qapoll_choice.relation_subcategory_id = '" . $this->_relation_subcategory_id . "' ";

		//use the user commented filter
		if($this->_user_commented_items != -1)
			$where .= "AND qapoll_choice.id IN (select qapoll_choice.id from qapoll_choice JOIN qapoll_choice_comment ON " . 					"qapoll_choice_comment.choiceid = qapoll_choice.id AND qapoll_choice_comment.status = 0 AND " .
				"qapoll_choice_comment.userid = '" . $this->_user_commented_items . "') ";

		return $where;
	}

	function _buildChoiceListQuery_orderby()
	{

		switch($this->_ordering)
		{
			case "mosthype-day":
				$orderby = "totalvotes/EXTRACT(EPOCH FROM 
						(
						CASE 
						WHEN (NOW() - qapoll_choice.date) < interval '1 hour' THEN
						interval '1 hour'
						ELSE
						NOW() - qapoll_choice.date
						END
						)
						) DESC ";
			break;

			//To prevent young ideas to appear always on top, give an minimum age of one day to each item.
			case "mosthype-week":
				$orderby = "totalvotes/EXTRACT(EPOCH FROM 
						(
						CASE 
						WHEN (NOW() - qapoll_choice.date) < interval '1 day' THEN
						interval '1 day'
						ELSE
						NOW() - qapoll_choice.date
						END
						)
						) DESC ";
			break;

			//To prevent young ideas to appear always on top, give an minimum age of one week to each item.
			case "mosthype-month":
				$orderby = "totalvotes/EXTRACT(EPOCH FROM 
						(
						CASE 
						WHEN (NOW() - qapoll_choice.date) < interval '1 week' THEN
						interval '1 week'
						ELSE
						NOW() - qapoll_choice.date
						END
						)
						) DESC ";
			break;

			//To prevent young ideas to appear always on top, give an minimum age of one week to each item.
			case "mosthype-6-months":
				$orderby = "totalvotes/EXTRACT(EPOCH FROM 
						(
						CASE 
						WHEN (NOW() - qapoll_choice.date) < interval '1 week' THEN
						interval '1 week'
						ELSE
						NOW() - qapoll_choice.date
						END
						)
						) DESC ";

			case "mosthype":
				$orderby = "totalvotes DESC ";
			break;

			case "leasthype":
				$orderby = "votes ASC ";
			break;

			case "mostvotes":
				$orderby = "votes DESC ";
			break;

			case "leastvotes":
				$orderby = "votes ASC ";
			break;

			case "new":
				$orderby = "date DESC ";
			break;

			case "old":
				$orderby = "date ASC ";
			break;

			case "random":
				$orderby = "random() ";
			break;

			case "newuservotes":
				if($this->_user_voted_items != -1)
					$orderby = "qapoll_vote_user.date DESC ";
				else
					$orderby = "votes DESC";
			break;

			case "newstatuschange":
				$orderby = "COALESCE(last_status_change, date '1999-1-1') DESC, votes DESC ";
			break;

			case "newcomments":
				$orderby = "GREATEST(last_comment_date, qapoll_choice.date) DESC ";
			break;

			case "latest-activity":
				$orderby = "GREATEST(last_edit_date, last_comment_date, qapoll_choice.date) DESC ";
			break;

			case "search-relevance":
				if(count($this->_keywordslist) > 0)
					$orderby = "search_rank DESC ";
				else
					$orderby = "votes DESC ";
			break;

			default:
				$orderby = "votes DESC ";
			break;
		}

		return ($orderby != null)?"ORDER BY " . $orderby:"";
	}

	function _getRowCount()
	{
		global $user;

		$count = db_result(it_query("SELECT COUNT(*) " . 

			"FROM " . 

			//Compute once the tsquery for the text search
			((count($this->_keywordslist) > 0)?"to_tsquery('" . 
				implode((($this->_keywords_any == false)?" & ":" | "), $this->_keywordslist) . 
				"') as keywordsquery, ":"") . 
		
			//Main FROM table
			"qapoll_choice " . 

			//Use the user bookmark filter
			(($this->_user_bookmarked_items != -1)?
				"JOIN qapoll_choice_bookmark as qapoll_choice_bookmark_user ON " . 
				"qapoll_choice_bookmark_user.choiceid = qapoll_choice.id AND " .
				"qapoll_choice_bookmark_user.userid = '" .	$this->_user_bookmarked_items . "' ":"") .

			//Use the Choice solution id filter
			(($this->_choicesolutionid != -1)?
				"JOIN qapoll_choice_solution_link ON " . 
				"qapoll_choice_solution_link.choiceid = qapoll_choice.id AND " . 
				"qapoll_choice_solution_link.status = 1 AND " . 
				"qapoll_choice_solution_link.choicesolutionid = '" . $this->_choicesolutionid . "' ":"") . 

			$this->_buildChoiceListQuery_where()));

		return $count;
	}

	/**
	 * Set the filter parameters, that will be used to filter data. Giving the GET array is usually fine.
	 * It will sanitize the necessary stuff.
	 */
	function setFilterParameters($getarray)
	{
		//Save the array first.
		$this->_filter_array = $getarray;

		if($getarray['keywords'] != null)
		{
			//For the moment, simple solution, we get rid of the quotes.
			//We also get rid of special characters interpreted by the to_tsquery function (&, |, !)
			$this->_keywords = htmlentities(str_replace(array("\"", "'", "\n", "\r", "&", "|", "!"), "", 
				$getarray['keywords']), ENT_QUOTES, "UTF-8");

			//Make an array of the search words.
			$this->_keywordslist = array_filter(explode(" ", $this->_keywords), create_function('$var', 'return ($var != null);'));
		}
		if($getarray['keywords_skip_common_words'] != null && is_numeric($getarray['keywords_skip_common_words']))
			$this->_keywords_skip_common_words = $getarray['keywords_skip_common_words'];
		if($getarray['keywords_any'] != null && is_numeric($getarray['keywords_any']))
			$this->_keywords_any = $getarray['keywords_any'];

		if($getarray['choice_ids'] != null)
		{
			//Check that all the given ids are indeed numeric.
			$array_ids = explode(", ", $getarray['choice_ids']);
			$is_all_numeric = true;
			foreach($array_ids as $id)
			{
				if(is_numeric($id) == false)
					$is_all_numeric = false;
			}
			if($is_all_numeric)
				$this->_choice_ids = $getarray['choice_ids'];
		}
			

		if($getarray['tags'] != null)
			$this->_tags = $getarray['tags'];
		if($getarray['tags_operator'] != null)
			$this->_tags_operator = $getarray['tags_operator'];
		if($getarray['admintags'] != null)
			$this->_admintags = $getarray['admintags'];
		if($getarray['admintags_operator'] != null)
			$this->_admintags_operator = $getarray['admintags_operator'];


		if($getarray['ordering'] != null)
			$this->_ordering = $getarray['ordering'];

		if($getarray['category'] != null && is_numeric($getarray['category']))
			$this->_category = $getarray['category'];

		//Save the states options
		if($getarray['state_new'] != null && is_numeric($getarray['state_new']))
			$this->_states['new'] = ($getarray['state_new'] != 0);
		if($getarray['state_needinfos'] != null && is_numeric($getarray['state_needinfos']))
			$this->_states['needinfos'] = ($getarray['state_needinfos'] != 0);
		if($getarray['state_blueprint_approved'] != null && is_numeric($getarray['state_blueprint_approved']))
			$this->_states['blueprint_approved'] = ($getarray['state_blueprint_approved'] != 0);		
		if($getarray['state_workinprogress'] != null && is_numeric($getarray['state_workinprogress']))
			$this->_states['workinprogress'] = ($getarray['state_workinprogress'] != 0);
		if($getarray['state_done'] != null && is_numeric($getarray['state_done']))
			$this->_states['done'] = ($getarray['state_done'] != 0);
		if($getarray['state_already_done'] != null && is_numeric($getarray['state_already_done']))
			$this->_states['already_done'] = ($getarray['state_already_done'] != 0);
		if($getarray['state_unapplicable'] != null && is_numeric($getarray['state_unapplicable']))
			$this->_states['unapplicable'] = ($getarray['state_unapplicable'] != 0);
		if($getarray['state_not_an_idea'] != null && is_numeric($getarray['state_not_an_idea']))
			$this->_states['not_an_idea'] = ($getarray['state_not_an_idea'] != 0);
		if($getarray['state_deleted'] != null && is_numeric($getarray['state_deleted']))
			$this->_states['deleted'] = ($getarray['state_deleted'] != 0);
		if($getarray['state_awaiting_moderation'] != null && is_numeric($getarray['state_awaiting_moderation']))
			$this->_states['awaiting_moderation'] = ($getarray['state_awaiting_moderation'] != 0);

		if($getarray['all_states'] != null && is_numeric($getarray['all_states']))
			$this->_all_states = ($getarray['all_states'] != 0);


		if($getarray['user'] != null && is_numeric($getarray['user']))
			$this->_userid = $getarray['user'];
		if($getarray['solution_userid'] != null && is_numeric($getarray['solution_userid']))
			$this->_solution_userid = $getarray['solution_userid'];


		if($getarray['type_bug'] != null && is_numeric($getarray['type_bug']))
			$this->_type_bug = ($getarray['type_bug'] != 0);
		if($getarray['type_idea'] != null && is_numeric($getarray['type_idea']))
			$this->_type_idea = ($getarray['type_idea'] != 0);

		if($getarray['nothing_attached'] != null && is_numeric($getarray['nothing_attached']))
			$this->_nothing_attached = ($getarray['nothing_attached'] != 0);
		if($getarray['bug_attached'] != null && is_numeric($getarray['bug_attached']))
			$this->_bug_attached = ($getarray['bug_attached'] != 0);
		if($getarray['spec_attached'] != null && is_numeric($getarray['spec_attached']))
			$this->_spec_attached = ($getarray['spec_attached'] != 0);
		if($getarray['thread_attached'] != null && is_numeric($getarray['thread_attached']))
			$this->_thread_attached = ($getarray['thread_attached'] != 0);
		if($getarray['attachment_operator'] != null && is_numeric($getarray['attachment_operator']))
			$this->_attachment_operator = ($getarray['attachment_operator'] != 0)?"AND":"OR";

		if($getarray['duplicate_items'] != null && is_numeric($getarray['duplicate_items']))
			$this->_duplicate_choices = $getarray['duplicate_items'];	

		if($getarray['user_voted_items'] != null && is_numeric($getarray['user_voted_items']))
			$this->_user_voted_items = $getarray['user_voted_items'];	
		if($getarray['user_voted_items_vote_value'] != null && is_numeric($getarray['user_voted_items_vote_value']))
			$this->_user_voted_items_vote_value = $getarray['user_voted_items_vote_value'];	

		if($getarray['release'] != null && is_numeric($getarray['release']))
			$this->_release = $getarray['release'];

		if($getarray['relation'] != null && is_numeric($getarray['relation']))
			$this->_relation = $getarray['relation'];
		if($getarray['relation_subcategory_id'] != null && is_numeric($getarray['relation_subcategory_id']))
			$this->_relation_subcategory_id = $getarray['relation_subcategory_id'];

		if($getarray['user_commented_items'] != null && is_numeric($getarray['user_commented_items']))
			$this->_user_commented_items = $getarray['user_commented_items'];

		if($getarray['user_bookmarked_items'] != null && is_numeric($getarray['user_bookmarked_items']))
			$this->_user_bookmarked_items = $getarray['user_bookmarked_items'];

		if($getarray['choicesolutionid'] != null && is_numeric($getarray['choicesolutionid']))
			$this->_choicesolutionid = $getarray['choicesolutionid'];

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
			$this->_include_user_bookmark = false;
			$this->_include_category_extra_data = false;
			$this->_include_relation_extra_data = false;
			$this->_include_relation_subcategory_extra_data = false;
			$this->_include_user_extra_data = false;
			$this->_include_item_comment_unread_flag = false;
			$this->_include_item_edition_unread_flag = false;
			$this->_include_choice_solutions_flag = false;
			$this->_include_target_release = false;
		}
		else
		{
			if($filter_array['include_user_approval_vote'] != null && is_numeric($filter_array['include_user_approval_vote']))
				$this->_include_user_approval_vote = $filter_array['include_user_approval_vote'];
			if($filter_array['include_user_bookmark'] != null && is_numeric($filter_array['include_user_bookmark']))
				$this->_include_user_bookmark = $filter_array['include_user_bookmark'];
			if($filter_array['include_category_extra_data'] != null && is_numeric($filter_array['include_category_extra_data']))
				$this->_include_category_extra_data = $filter_array['include_category_extra_data'];
			if($filter_array['include_relation_extra_data'] != null && is_numeric($filter_array['include_relation_extra_data']))
				$this->_include_relation_extra_data = $filter_array['include_relation_extra_data'];
			if($filter_array['include_relation_subcategory_extra_data'] != null && 
				is_numeric($filter_array['include_relation_subcategory_extra_data']))
				$this->_include_relation_subcategory_extra_data = $filter_array['include_relation_subcategory_extra_data'];
			if($filter_array['include_user_extra_data'] != null && is_numeric($filter_array['include_user_extra_data']))
				$this->_include_user_extra_data = $filter_array['include_user_extra_data'];
			if($filter_array['include_item_comment_unread_flag'] != null && is_numeric($filter_array['include_item_comment_unread_flag']))
				$this->_include_item_comment_unread_flag = $filter_array['include_item_comment_unread_flag'];
			if($filter_array['include_item_edition_unread_flag'] != null && is_numeric($filter_array['include_item_edition_unread_flag']))
				$this->_include_item_edition_unread_flag = $filter_array['include_item_edition_unread_flag'];
			if($filter_array['include_choice_solutions_flag'] != null && is_numeric($filter_array['include_choice_solutions_flag']))
				$this->_include_choice_solutions_flag = $filter_array['include_choice_solutions_flag'];
			if($filter_array['include_target_release'] != null && is_numeric($filter_array['include_target_release']))
				$this->_include_target_release = $filter_array['include_target_release'];

		}

	}

	/**
	 * Delete the fetched entries.
	 */
	function deleteEntries()
	{
		$entries_id = array();
		$entries = $this->getData();

		foreach($entries->items as $entry)
		{
			$choice = new ChoiceModel();
			$choice->setId($entry->id);
			$choice->delete();
		}

		it_query($query);
	}

	/**
	 * This function is a callback. DO NOT CALL DIRECTLY!
	 * It is used by UserModel::_hasPermission to determine if the filtered permisssions allows
	 * the model number $model_id to have the permission $perm_name in THIS instance.
	 * If $model_id is not in this instance, it return false.
	 */
	function _callback_hasFilteredPermissions($perm_name, $model_id, $filtered_perms)
	{
		//If there is no filter, the model is probably not initialized yet. => Design error.
		//Return false, and 
		if($this->_filter_array == null)
		{
			drupal_set_message("Probable design error while getting user permissions. " . 
				"Please see ChoiceListModel::_callback_hasFilteredPermissions", 'notice_msg');
			return false;
		}

		//If there is no filtered perms, just skip and return false
		if($filtered_perms == null)
			return false;

		//Check if the perm data was already fetched.
		if($this->_perms_cache == null)
		{
			//Get the data, fetch the ids
			$this->_perms_cache = array(); 
			$data = $this->getData();
			$ids = array();
			foreach($data->items as $item)
			{
				$ids[] = $item->id;
			}

			//Create a new ChoiceListModel, set the ids, set the filters of the perms
			//and see which ones match the filters
			$ids_param = implode(", ", $ids);
			foreach($filtered_perms as $filter => $perm)
			{
				$choicelist = new ChoiceListModel($this->_pollModel);
				$choicelist->setFilterParameters(generate_GET_array($filter . 
					"&ordering=mostvotes&choice_ids=" . $ids_param));
				$choicelist->setDataFilter(array("include_minimal_data" => true));
				$filteredlist = $choicelist->getData(); 

				foreach($filteredlist->items as $filtereditem)
				{
					if(isset($this->_perms_cache[$filtereditem->id]) == false)
						$this->_perms_cache[$filtereditem->id] = array();
					$this->_perms_cache[$filtereditem->id] = array_merge($this->_perms_cache[$filtereditem->id], $perm);
				}

			}


		}
		
		if(isset($this->_perms_cache[$model_id]))
			return $this->_perms_cache[$model_id]["$perm_name"];
		else
			return false;
	}

	/**
	 * This function is a callback. DO NOT CALL DIRECTLY!
	 * It is used by UserModel::_hasPermission to determine if the user user_id is
	 * the owner of the $model_id object.
	 * If $model_id is not in this instance, it return false.
	 */
	function _callback_isOwner($model_id, $user_id)
	{
		//If there is no filter, the model is probably not initialized yet. => Design error.
		//Return false, and 
		if($this->_filter_array == null)
		{
			drupal_set_message("Probable design error while getting user permissions. " . 
				"Please see ChoiceListModel::_callback_isOwner", 'notice_msg');
			return false;
		}

		//Check if user_id is the owner of model_id
		$data = $this->getData();
		foreach($data->items as $item)
		{
			if($item->id == $model_id)
				return ($item->userid == $user_id);
		}

		return false;
	}

}

?>
