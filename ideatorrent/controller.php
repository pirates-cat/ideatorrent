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

//Include the model definition
require_once "models/model.php";
require_once "models/modellist.php";

//Include systems objects
require_once "system/logger.php";
require_once "system/stats.php";
require_once "system/mailsender.php";
require_once "system/config.php";
require_once "system/themeconfig.php";

//include the models
require_once "models/choice.php";
require_once "models/choicelist.php";
require_once "models/site.php";
require_once "models/poll.php";
require_once "models/choice_comment.php";
require_once "models/choice_commentlist.php";
require_once "models/entry_point.php";
require_once "models/entry_pointlist.php";
require_once "models/menulist.php";
require_once "models/imagelinklist.php";
require_once "models/imagelink.php";
require_once "models/duplicate_report.php";
require_once "models/duplicate_reportlist.php";
require_once "models/releaselist.php";
require_once "models/release.php";
require_once "models/user.php";
require_once "models/category.php";
require_once "models/categorylist.php";
require_once "models/relation.php";
require_once "models/relationlist.php";
require_once "models/relation_subcategory.php";
require_once "models/relation_subcategorylist.php";
require_once "models/tag.php";
require_once "models/taglist.php";
require_once "models/permissionlist.php";
require_once "models/choicesolutionlist.php";
require_once "models/choicesolution.php";
require_once "models/choicesolutionlink.php";
require_once "models/choicesolutionlinklist.php";
require_once "models/report.php";
require_once "models/reportlist.php";
require_once "models/choicelog.php";
require_once "models/choiceloglist.php";

//include the views
require_once "view.php";
require_once "views/choice.html.php";
require_once "views/choice.rss2.php";
require_once "views/choicelist.html.php";
require_once "views/choicelist.rss2.php";
require_once "views/choicelist.json.php";
require_once "views/choicelist.xml.php";
require_once "views/imagelink.png.php";
require_once "views/duplicate_reportlist.html.php";
require_once "views/admin.html.php";
require_once "views/user.html.php";
require_once "views/relationlist.json.php";
require_once "views/relationsubcategorylist.json.php";
require_once "views/menublock.html.php";
require_once "views/tour.html.php";
require_once "views/reportlist.html.php";
require_once "views/faq.html.php";




class QAPollController
{
	/**
	 * The instance of the controller.
	 */
	static private $_instance = null;

	/**
	 * The target page.
	 */
	var $_page;

	/**
	 * The parameters of the page.
	 */
	var $_param1;
	var $_param2;
	var $_param3;
	var $_param4;
	var $_param5;
	var $_param6;

	/**
	 * The list of polls linked to this HTTP_HOST.
	 */
	var $_polls = null;

	/**
	 * The poll we are currently working with.
	 */ 
	var $_poll = null;

	/**
	 * The entry point currently used by this URL.
	 */
	var $_entry_point = null;

	/**
	 * The view instance being used to output the page
	 */
	var $_output_page_view = null;

	/**
	 * The display function can specify what title to use here.
	 * The title will be in the form entry_point_title - $this->_page_title
	 */
	var $_page_title = "";

	/**
	 * Get the unique instance.
	 */
	static function getInstance()
	{
		if (self::$_instance == null)
			self::$_instance = new QAPollController;

		return self::$_instance;
	}

	/**
	 * Initialize the controller by giving him the page parameters.
	 */
	function init($page, $param1, $param2, $param3, $param4, $param5, $param6)
	{
		global $site;

		$this->_page = $page;
		$this->_param1 = $param1;
		$this->_param2 = $param2;
		$this->_param3 = $param3;
		$this->_param4 = $param4;
		$this->_param5 = $param5;
		$this->_param6 = $param6;

	}

	/**
	 * Display the page.
	 * URL are in the form 
	 * http://xxx/[poll_number(optional)]/[entrypoint]/[relation](optional)]/[category](optional)/page/param1/param2/....
	 * The first part of this function will be to process the URL from the poll number to the category, getting some global
	 * informations about the page (current poll, entrypoint, relation, category)
	 * then the second part will take care of displaying the asked page.
	 */
	function display()
	{
		//The content to be displayed
		$content = null;
		//
		global $base_url;
		//The site model we are working with
		global $site;
		//The poll model we are working with
		global $poll;
		//The entrypoint model we are working with
		global $entrypoint;
		//The relation model we are working with
		global $gbl_relation;
		//The category (or relation_subcategory) model we are working with
		global $gbl_category;
		global $gbl_relationsubcat;

		//
		// Get the poll we are working with
		//

		//Get the polls related to the current HTTP_HOST.
		$site = new SiteModel();
		$polls = $site->getPollList();
		$this->_polls = $polls;

		//If no polls related to this website, show an error
		if(count($polls) == 0)
		{
			$content = t("No polls defined on this website yet.");
			return $content;
		}

		//If the poll number is in the URL, check that it is in fact related to this website.
		if(is_numeric($this->_page))
		{
			$pollListId = $this->pollid_in_pollList($this->_page, $polls);
			if($pollListId == -1)
				return drupal_access_denied();

			$poll = $polls[$pollListId];
			$this->_shiftPageArguments();
		}
		else
			//No poll number. By default, take the last one
			$poll = $polls[count($polls) - 1];
		$this->_poll = $poll;


		//
		// Explanation of the different global path variables!
		// base_url: provided by drupal, it only contains the host (e.g. http://brainstorm.ubuntu.com) even if the install
		//		is not there (could be at http://brainstorm.ubuntu.com/my_brainstorm)
		// basemodule_url: contains the base path to access the modules (e.g. http://brainstorm.ubuntu.com, 
		//		http://mydomain.com/my_install
		// basemodule_path: contains the parent path of the current page excluding the basemodule_url. E.g. if we are in 
		//		http://mydomain.com/my_install/amarok/idea/22/, the value will be amarok/idea/22
		// basemodule_prefilter_path: contains the prefilter part of the basemodule_path. E.g. if we are in 
		//		http://mydomain.com/my_install/amarok/idea/22/, the value will be /amarok
		//		This value can either be relation_name, /relation_name/relation_subcategory_name, or
		//		/category_name, or "".
		// basemodule_parentpath: contains the parent path of the current page excluding the basemodule_url + prefilter_path plus the suffixes.
		//		E.g. if we are in http://mydomain.com/my_install/amarok/idea/22/, the value will be "idea".
		//
		global $basemodule_parentpath;

		//Generate and declare our module base url. Use the entry point custom base url if defined.
		global $basemodule_url;
		$basemodule_url = $GLOBALS['base_url'] . "/ideatorrent" .
			(($this->_poll != $polls[count($polls) - 1])?"/" . $this->_poll->getData()->id . "":"");


		//
		// Get the entry point we are working with
		//

		//We now determine which entry point we are using.
		//It can be given in the path http://xxx/frontend/entry_point_name/, or else
		//we will use the first available entrypoint
		$entrypoint_name = "";
		$default_entry_point = false;
		if($this->_page == "frontend")
		{
			$this->_shiftPageArguments();
			$entrypoint_name = $this->_page;
			$this->_shiftPageArguments();
		}
		else
			//No entry point specified in the URL. We are using the default one.
			$default_entry_point = true;
		$entrypoint = new EntryPointModel();
		$entrypoint->setPrimaryKey($poll->getData()->id, $entrypoint_name);
		if($entrypoint->getData() != null)
		{
			//The entry point is valid. Store it.
			$this->_entry_point = $entrypoint;

			//Compute the base path
			if($entrypoint->getData()->custom_base_url != "")
				//Override the base url
				$basemodule_url = $entrypoint->getData()->custom_base_url;
			else
			{
				if($default_entry_point == false)
					$basemodule_url .= "/frontend/" . $entrypoint->getData()->url_name;
				//else we don't add the frontend name in the URL
			}
		}
		else
		{
			//Not entrypoint found
			return drupal_not_found();
		}



		//Declare our current path too. By default, it is the page only, but it should be modified
		//when necessary. E.g. the path of http://xx/idea/11/ is idea/11.
		global $basemodule_path;
		$basemodule_path = $this->_page;


		//
		// Handle the AJAX requests returning data.
		//

		if(strpos($this->_page, "ajaxdata") !== false)
		{
			echo $this->handleAjaxDataCalls();

			//Return null, preventing drupal to output.
			return null;
		}

		//
		// Handle the boolean AJAX requests
		//

		if(strpos($this->_page, "ajax") !== false)
		{
			if($this->handleAjaxCalls())
				echo "AJAXOK";
			else
				echo "AJAXERROR";

			//Return null, preventing drupal to output.
			return null;
		}


		//
		// If we are in the frontpage, use the default front page stored in the preferences
		//
		if($this->_page == "")
		{
			$newpath = explode("/", QAPollConfig::getInstance()->getValue("start_page"));
			$this->_page = (($newpath[0] != "")?$newpath[0]:null);
			$this->_param1 = (($newpath[1] != "")?$newpath[1]:null);
			$this->_param2 = (($newpath[2] != "")?$newpath[2]:null);
			$this->_param3 = (($newpath[3] != "")?$newpath[3]:null);
			$this->_param4 = (($newpath[4] != "")?$newpath[4]:null);
			$this->_param5 = (($newpath[5] != "")?$newpath[5]:null);
			$this->_param6 = (($newpath[6] != "")?$newpath[6]:null);
		}


		//
		// If specified in the URL, take care of the relation or/and category
		// FIXME: We could have pbs if one of the cats or relations has an url_name identical to a existing hardcoded page
		// $path_choice_filter will contains the filter for ChoiceListModels that reflects the current path.
		// e.g. if the path is b.u.c/amarok/, then $path_choice_filter = array("relation" => 2)
		//
		global $basemodule_prefilter_path;
		$basemodule_prefilter_path = "";
		$path_choice_filter = array();
		if($this->_page != "")
		{
			//Try to see if $this->_page is a relation url_name (sanitized name for a relation, stored in DB).
			$relationlistmodel = new RelationListModel($poll);
			$relationlistmodel->setFilterParameters(array("url_name" => $this->_page));
			$relationlist = $relationlistmodel->getData();
			if(count($relationlist) == 1)
			{
				//We found a relation in the URL. Storing it in a global variable, and shifting URL path.
				$gbl_relation = new RelationModel();
				$gbl_relation->setId($relationlist[0]->relation_id);
				$path_choice_filter["relation"] = $relationlist[0]->relation_id;

				$basemodule_path .= "/" . $this->_param1;
				$basemodule_prefilter_path .= "/" . $this->_page;
				$this->_shiftPageArguments();
			}

			//Now try to see if $this->_page is a category url_name, or a relation_subcategory url_name
			if($this->_page != "")
			{
				if($gbl_relation != null)
				{
					$relsubcategorylistmodel = new RelationSubcategoryListModel();
					$relsubcategorylistmodel->setFilterParameters(
						array("url_name" => $this->_page, "relation_id" => $gbl_relation->getId()));
					$relsubcategorylist = $relsubcategorylistmodel->getData();
					if(count($relsubcategorylist) == 1)
					{
						//We found a relation in the URL. Storing it in a global variable, and shifting URL path.
						$gbl_relationsubcat = new RelationSubcategoryModel();
						$gbl_relationsubcat->setId($relsubcategorylist[0]->id);
						$path_choice_filter["relation_subcategory_id"] = $relsubcategorylist[0]->id;
						$basemodule_path .= "/" . $this->_param1;
						$basemodule_prefilter_path .= "/" . $this->_page;
						$this->_shiftPageArguments();
					}
				}
				else
				{
					$categorylistmodel = new CategoryListModel($poll);
					$categorylistmodel->setFilterParameters(array("url_name" => $this->_page));
					$categorylist = $categorylistmodel->getData();
					if(count($categorylist) == 1)
					{
						//We found a relation in the URL. Storing it in a global variable, and shifting URL path.
						$gbl_category = new CategoryModel();
						$gbl_category->setId($categorylist[0]->id);
						$path_choice_filter["category"] = $categorylist[0]->id;
						$basemodule_path .= "/" . $this->_param1;
						$basemodule_prefilter_path .= "/" . $this->_page;
						$this->_shiftPageArguments();
					}
				}
			}
		}


		//
		// Explanation of the differents SESSION variables
		// $_SESSION['basemodule_prefilter_path_relation']
		// $_SESSION['basemodule_prefilter_path_relationsubcat']
		// $_SESSION['basemodule_prefilter_path_category']
		// These are the models of the parts of the basemodule_prefilter_path of the *PREVIOUS* pages. They are used so that 
		// once we choose a relation/category/relationsubcat, we use it on every page.
		// => The relation/category/relationsubcat are already given in the URL, but not for the /idea/ path.
		// To fill these session variables, we will right now fill the following variables with the 
		// relation/category/relationsubcat found in the URL:
		// $candidate_session_prefilter_path_relation
		// $candidate_session_prefilter_path_relationsubcat
		// $candidate_session_prefilter_path_category
		// Then on the big page switch, these can be altered. And finally they will be saved at the very end.
		//
		$candidate_session_prefilter_path_relation = (($gbl_relation != null)?$gbl_relation->getId():-1);
		$candidate_session_prefilter_path_relationsubcat = (($gbl_relationsubcat != null)?$gbl_relationsubcat->getId():-1);
		$candidate_session_prefilter_path_category = (($gbl_category != null)?$gbl_category->getId():-1);


		//Save the basemodule_parentpath (see doc abobe)
		$basemodule_parentpath = $this->_page;


		//
		// Handle the traditional page request
		// Create the model and view, and output the page.
		//

		//The array of "name" => Model to give to the view
		$models = array();
		//The view that will be used to display the models
		$view = null;
		//The template to use
		$template = "default";

		//DEPRECATED. Use $models instead.
		$model = null;
		$model2 = null;
		$model_name = "";
		$model2_name = "";


		switch ($this->_page) {

			//Default page and search page. List the choices.
			case "poll":
			case "":
			case "popular_ideas":
			case "search":
			case "my_bugs":
			case "ideas_in_preparation":
			case "ideas_in_development":
			case "implemented_ideas":
			case "most_popular_today":
			case "most_popular_this_week":
			case "most_popular_this_month":
			case "most_popular_6_months":
			case "random_ideas":
			case "latest_ideas":
			case "latest_comments":
			case "most_popular_ever":

				//These pages will use ChoiceList as the main model.
				//Set up empty filters and options for the ChoiceList model and view.
				$filter = array();
				$data_filter = array();
				$viewOptions = array();


				if($this->_page == "search")
				{
					//In the search page, we use the GET parameters as filter.
					$filter = $_GET;

					//If we are not admin, we don't allow to show the deleted items.
					if(user_access($site->getData()->adminrole) == false)
						unset($filter['state_deleted']);
					$template = "search_results";
					$this->_page_title = "Search results";
				}
				else if($this->_page == "ideas_in_preparation")
				{
					//Idea in preparation filter
					$filter = array("state_new" => "0", "state_needinfos" => "0", "state_workinprogress" => "0",
						"state_blueprint_approved" => "0", "state_done" => "0", "state_already_done" => "0",
						"state_unapplicable" => "0", "state_not_an_idea" => "0");

					//We select between the differents subcategories of implemented ideas.
					if(!isset($this->_param1) || isInteger($this->_param1) || $this->_param1 == "rss2" ||
						$this->_param1 == "latest_submissions" || $this->_param1 == "latest_comments")
					{
						$data_filter = array("include_item_comment_unread_flag" => 1, 
							"include_item_edition_unread_flag" => 1);
						$filter["state_awaiting_moderation"] = 1;
					}
					else if($this->_param1 == "invalid")
					{
						$filter["state_not_an_idea"] = 1;
						$basemodule_path .= "/" . $this->_param1;
						$this->_shiftPageArguments();
					}
					else if($this->_param1 == "already_implemented")
					{
						$filter["state_already_done"] = 1;
						$basemodule_path .= "/" . $this->_param1;
						$this->_shiftPageArguments();
					}
					else if($this->_param1 == "duplicates")
					{
						$filter["state_new"] = "1";
						$filter["state_needinfos"] = "1";
						$filter["state_workinprogress"] = "1";
						$filter["state_blueprint_approved"] = "1";
						$filter["state_done"] = "1";
						$filter["state_already_done"] = "1";
						$filter["state_unapplicable"] = "1";
						$filter["state_not_an_idea"] = "1";
						$filter["duplicate_items"] = -3;
						$basemodule_path .= "/" . $this->_param1;
						$this->_shiftPageArguments();
					}

					//We now select between the different orderings
					$filter["ordering"] = "latest-activity";
					if(isInteger($this->_param1) == false && $this->_param1 != "rss2")
					{
						if($this->_param1 == "latest_submissions")
						{
							$filter["ordering"] = "new";
							$basemodule_path .= "/" . $this->_param1;
						}
						else if($this->_param1 == "latest_comments")
						{
							$filter["ordering"] = "newcomments";
							$viewOptions["show_latest_comment_date"] = 1;
							$basemodule_path .= "/" . $this->_param1;
						}
						$this->_shiftPageArguments();
					}

					$this->_page_title = t('Idea sandbox');
					$template = "ideas_in_preparation";
				}
				else if($this->_page == "ideas_in_development")
				{
					//Idea in development filter
					$filter = array("state_new" => "0", "state_needinfos" => "0",
						"state_blueprint_approved" => "0", "state_done" => "0", "state_already_done" => "0",
						"state_unapplicable" => "0", "state_not_an_idea" => "0");

					//Load the release list. Will be used to display release tabs.
					$models["newreleaselist"] = new ReleaseListModel();
					$models["newreleaselist"]->setFilterParameters(array("old_release" => 0));

					//Get the id of the most recent release.
					//Give its model to the view
					$most_recent_release_id = ReleaseModel::getIdFromMostRecentRelease();
					$models["most_recent_release"] = new ReleaseModel();
					$models["most_recent_release"]->setId($most_recent_release_id);

					//We select between the differents subcategories of in dev ideas.
					if(!isset($this->_param1) || isInteger($this->_param1) || $this->_param1 == "rss2" ||
						$this->_param1 == "most_popular" || $this->_param1 == "latest_comments")
					{
						//If we have a most recent release, i.e. if we have some release in the table:
						if($most_recent_release_id != -1)
						{
							$filter["release"] = $most_recent_release_id;
							$models["release"] = new ReleaseModel();
							$models["release"]->setId($filter["release"]);
						}
						else
						{
							//If we don't have releases, just show ALL the in dev ideas
						}
					}
					else if($this->_param1 == "no_milestone")
					{
						$filter["release"] = -1;
						$basemodule_path .= "/" . $this->_param1;
						$this->_shiftPageArguments();
					}
					else
					{
						//We assume it's a release short number
						$release_id = ReleaseModel::getIdFromShortName($this->_param1);
						if($release_id == -1)
						{
							$content = drupal_not_found();
							break;
						}
						$filter["release"] = $release_id;
						$models["release"] = new ReleaseModel();
						$models["release"]->setId($filter["release"]);
						$basemodule_path .= "/" . $this->_param1;
						$this->_shiftPageArguments();
					}

					//We now select between the different orderings
					$filter["ordering"] = "newstatuschange";
					if(isInteger($this->_param1) == false && $this->_param1 != "rss2")
					{
						if($this->_param1 == "most_popular")
						{
							$filter["ordering"] = "mostvotes";
							$basemodule_path .= "/" . $this->_param1;
						}
						else if($this->_param1 == "latest_comments")
						{
							$filter["ordering"] = "newcomments";
							$viewOptions["show_latest_comment_date"] = 1;
							$basemodule_path .= "/" . $this->_param1;
						}
						$this->_shiftPageArguments();
					}

					$this->_page_title = t('Ideas in development');
					$template = "ideas_in_development_v2";
				}
				else if($this->_page == "implemented_ideas")
				{
					//Implemented ideas filter
					$filter = array("ordering" => "newstatuschange", "state_new" => "0", "state_needinfos" => "0",
						"state_blueprint_approved" => "0", "state_workinprogress" => "0", "state_already_done" => "0",
						"state_unapplicable" => "0", "state_not_an_idea" => "0");

					//Load the release list. Will be used to display release tabs.
					$models["newreleaselist"] = new ReleaseListModel();
					$models["newreleaselist"]->setFilterParameters(array("old_release" => 0));

					//Get the id of the most recent release.
					//Give its model to the view
					$most_recent_release_id = ReleaseModel::getIdFromMostRecentRelease();
					$models["most_recent_release"] = new ReleaseModel();
					$models["most_recent_release"]->setId($most_recent_release_id);

					//We select between the differents subcategories of implemented ideas.
					if(!isset($this->_param1) || isInteger($this->_param1) || $this->_param1 == "rss2" ||
						$this->_param1 == "most_popular" || $this->_param1 == "latest_comments")
					{
						//If we have a most recent release, i.e. if we have some release in the table:
						if($most_recent_release_id != -1)
						{
							$filter["release"] = $most_recent_release_id;
							$models["release"] = new ReleaseModel();
							$models["release"]->setId($filter["release"]);
						}
						else
						{
							//If we don't have releases, just show ALL the implemented ideas
						}
					}
					else if($this->_param1 == "no_milestone")
					{
						$filter["release"] = -1;
						$basemodule_path .= "/" . $this->_param1;
						$this->_shiftPageArguments();
					}
					else
					{
						//We assume it's a release short number
						$release_id = ReleaseModel::getIdFromShortName($this->_param1);
						if($release_id == -1)
						{
							$content = drupal_not_found();
							break;
						}
						$filter["release"] = $release_id;
						$models["release"] = new ReleaseModel();
						$models["release"]->setId($filter["release"]);
						$basemodule_path .= "/" . $this->_param1;
						$this->_shiftPageArguments();
					}

					//We now select between the different orderings
					$filter["ordering"] = "newstatuschange";
					if(isInteger($this->_param1) == false && $this->_param1 != "rss2")
					{
						if($this->_param1 == "most_popular")
						{
							$filter["ordering"] = "mostvotes";
							$basemodule_path .= "/" . $this->_param1;
						}
						else if($this->_param1 == "latest_comments")
						{
							$filter["ordering"] = "newcomments";
							$viewOptions["show_latest_comment_date"] = 1;
							$basemodule_path .= "/" . $this->_param1;
						}
						$this->_shiftPageArguments();
					}

					$this->_page_title = t('Implemented ideas');
					$template = "implemented_ideas_v2";
				}
				else //if page is from the popular idea area
				{
					//Popular ideas area filter
					$filter = array("state_new" => "1", "state_needinfos" => "1",
						"state_blueprint_approved" => "1", "state_workinprogress" => "0", "state_done" => "0", 
						"state_already_done" => "0",
						"state_unapplicable" => "0", "state_not_an_idea" => "0", "awaiting_moderation" => "0");

					if($this->_page == "" || $this->_page == "most_popular_this_month")
					{
						//Front page
						$filter["ordering"] = "mosthype-month";
						$template = "popular_ideas";
						$this->_page_title = t('Popular ideas');
					}
					else if($this->_page == "random_ideas")
					{
						$filter["ordering"] = "random"; 
						if(user_access($site->getData()->userrole))
						{
							$filter["user_voted_items"] = $GLOBALS['user']->uid;
							$filter["user_voted_items_vote_value"] = -2;
						}
						$template = "popular_ideas";
						$this->_page_title = t('Random ideas');
					}
					else if($this->_page == "most_popular_today")
					{
						$filter["ordering"] = "mosthype-day";
						$template = "popular_ideas";
						$this->_page_title = t('Popular ideas');
					}
					else if($this->_page == "most_popular_this_week")
					{
						$filter["ordering"] = "mosthype-week";
						$template = "popular_ideas";
						$this->_page_title = t('Popular ideas');
					}
					else if($this->_page == "most_popular_6_months")
					{
						$filter["ordering"] = "mosthype-6-months";
						$template = "popular_ideas";
						$this->_page_title = t('Popular ideas');
					}
					else if($this->_page == "latest_ideas")
					{
						$filter["ordering"] = "new";
						$template = "popular_ideas";
						$this->_page_title = t('Latest ideas');
					}
					else if($this->_page == "latest_comments")
					{
						$filter["ordering"] = "newcomments";
						$data_filter = array("include_item_comment_unread_flag" => 1);
						$template = "popular_ideas";
						$viewOptions = array("show_latest_comment_date" => "1");
						$this->_page_title = t('Latest comments');
					}
					else if($this->_page == "most_popular_ever")
					{
						$filter["ordering"] = "mostvotes";
						$template = "popular_ideas";
						$this->_page_title = t('Popular ideas');
					}
				}

				//Create the model, giving the start and number of rows as parameters.
				if($this->_param1 == null || is_numeric($this->_param1) == false)
					$model = new ChoiceListModel($poll);
				else if(is_numeric($this->_param1) && ($this->_param2 == null || is_numeric($this->_param2) == false))
					$model = new ChoiceListModel($poll, $this->_param1);
				else if(is_numeric($this->_param1) && is_numeric($this->_param2))
					$model = new ChoiceListModel($poll, $this->_param1, $this->_param2);
				$model_name = "choicelist";

				//Extract from the GET array what interest us
				$GETfilter = array();
				$GETfilter['keywords'] = $_GET['keywords'];
				$GETfilter['tags'] = $_GET['tags'];
				$GETfilter['admintags'] = $_GET['admintags'];

				//Merge now the filters
				//The priority of the filters is as follow (from lowest to highest):
				// 1. GET array. Everything is parsed, so we can safely use this.
				// 2. The page-specific filter
				// 3. The prefilter filter (e.g. filter by relation & subcat in http://hostname/relation_name/subcategory_name/....)
				// 4. The filter defined in the entrypoint.
				$filter = array_merge($GETfilter, $filter);
				$filter = array_merge($filter, $path_choice_filter);
				$filter = array_merge($filter, $this->_entry_point->getData()->filterArray);
				$model->setFilterParameters($filter);
				$model->setDataFilter($data_filter);

				if(is_numeric($this->_param1) == false && $this->_param1 == "rss2")
				{
					$view = new ChoiceListRSS2View();
					$view->setOptions($viewOptions);
					$template = "rss2";
				}
				else
				{
					$view = new ChoiceListView();
					$view->setOptions($viewOptions);
				}
			break;

			//Show the contributor page
			case "contributor":
				//Update the global path variable
				$basemodule_path .= "/" . htmlentities($this->_param1, ENT_QUOTES,"UTF-8");

				//Check if the user is valid.
				$this_user = user_load(array("name" => $this->_param1));
				if($this_user->uid == $GLOBALS['user']->uid)
					$this->_page_title = t('My dashboard');
				else
					$this->_page_title = t('Contributor !name', array("!name" => $this_user->name));
				$this->_shiftPageArguments();
				if($this_user->uid == null)
				{
					$content = drupal_not_found();
					break;
				}

				if($this->_param1 == null || is_numeric($this->_param1) == true)
				{
					//Forbid private msg.
					if(array_key_exists('_forbid_private_msg',$_POST))
					{
						//Check permission
						if(!user_access($site->getData()->userrole))
						{
							$content = drupal_access_denied();
							break;
						}
	
						qawebsite_set_user_setting("I can receive private messages", $GLOBALS['user']->uid,
							QAWebsiteSite::getInstance()->id, "ideatorrent", 0);
						drupal_set_message(t("Your new preferences are saved."), 'notice_msg');
					}
					//Allow private msg.
					else if(array_key_exists('_allow_private_msg',$_POST))
					{
						//Check permission
						if(!user_access($site->getData()->userrole))
						{
							$content = drupal_access_denied();
							break;
						}

						qawebsite_set_user_setting("I can receive private messages", $GLOBALS['user']->uid,
							QAWebsiteSite::getInstance()->id, "ideatorrent", 1);
						drupal_set_message(t("Your new preferences are saved."), 'notice_msg');
					}
					//A message is being sent
					else if (array_key_exists('_message_submitted',$_POST))
					{
						//Check permission
						if(!user_access($site->getData()->userrole))
						{
							$content = drupal_access_denied();
							break;
						}

						//Check if we have waited long enough before sending another message.
						if(QAPollLogger::getInstance()->isLatestPrivateMessageOlderThan($GLOBALS['user']->uid, 1) == false)
							drupal_set_message(t("To prevent spam by robots, there is a 1 minute minimum interval between each private message. Please wait."), 'error_msg');
						//Check if the receiver allow private messages.
						else if(qawebsite_get_user_setting("I can receive private messages", $this_user->uid,
							QAWebsiteSite::getInstance()->id, "ideatorrent") === "0")
							drupal_set_message(t("This user does not want to receive private messages."), 'error_msg');
						else
						{
							//Fill the data for the mail. Data is sanitized in the templates.
							$args['sender_name'] = $GLOBALS['user']->name;
							$args['sender_mail'] = $GLOBALS['user']->mail;
							$args['message'] = $_POST['message_text'];

							$mail_sent = QAPollMailSender::getInstance()->sendPreformattedMessage($entrypoint->_data->title .
								" <noreply@" . $_SERVER["SERVER_NAME"] . ">",
								$this_user->mail, $GLOBALS['user']->mail, "message_to_user", $args);

							if($mail_sent)
							{
								drupal_set_message(t("Your message has been sent."), 'notice_msg');
								QAPollLogger::getInstance()->logPrivateMessageSent($GLOBALS['user']->uid, $this_user->uid);
							}
							else
								drupal_set_message(t("An error occured while sending your message."), 'error_msg');
						}

					}

					$model = UserModel::getInstance($this_user->uid);
					$model_name = "user";
					$view = new UserView();
				}
				//Admin functions
				else if($this->_param1 == "delete_user_comments")
				{
					//We are deleting ALL user comments
					//Check the rights
					if(!UserModel::currentUserHasPermission("delete_user_items", "User"))
					{
						$content = drupal_access_denied();
						break;
					}

					UserModel::getInstance($this_user->uid)->deleteAllComments();

					drupal_set_message(t("All the user comments have been deleted."), 'notice_msg');
					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . 
									$GLOBALS['basemodule_path'] . "/");
					
				}
				//Admin functions
				else if($this->_param1 == "delete_user_solutions")
				{
					//We are deleting ALL user comments
					//Check the rights
					if(!UserModel::currentUserHasPermission("delete_user_items", "User"))
					{
						$content = drupal_access_denied();
						break;
					}

					UserModel::getInstance($this_user->uid)->deleteAllSolutions();

					drupal_set_message(t("All the user solutions have been deleted."), 'notice_msg');
					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . 
									$GLOBALS['basemodule_path'] . "/");
					
				}
				//Admin functions
				else if($this->_param1 == "delete_user_ideas")
				{
					//We are deleting ALL user comments
					//Check the rights
					if(!UserModel::currentUserHasPermission("delete_user_items", "User"))
					{
						$content = drupal_access_denied();
						break;
					}

					UserModel::getInstance($this_user->uid)->deleteAllChoices();

					drupal_set_message(t("All the user ideas have been deleted."), 'notice_msg');
					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . 
									$GLOBALS['basemodule_path'] . "/");
					
				}
				//log page of the user
				else if($this->_param1 == "log")
				{
					//Show the log page of the "choice".
					$models["choiceloglist"] = new ChoiceLogListModel();
					$models["choiceloglist"]->setFilterParameters(array("user_id" => $this_user->uid));
					$models["user"] = UserModel::getInstance($this_user->uid);
	
					$view = new UserView();
					$template = "userlog";
				}
				//Keeping the old URLs for RSS bookmarks compatibility
				else if($this->_param1 == "his_ideas" || $this->_param1 == "ideas_he_promoted" || 
					$this->_param1 == "ideas_he_demoted" || $this->_param1 == "ideas_he_commented" ||
					$this->_param1 == "ideas_he_bookmarked" ||
					$this->_param1 == "ideas" || $this->_param1 == "ideas_promoted" || 
					$this->_param1 == "ideas_demoted" || $this->_param1 == "ideas_commented" ||
					$this->_param1 == "ideas_bookmarked" || $this->_param1 == "solutions")
				{
					//Empty filters
					$filter = array();
					$data_filter = array();

					//Save basemodule path
					$basemodule_path .= "/" . htmlentities($this->_param1, ENT_QUOTES,"UTF-8");
					$this->_shiftPageArguments();

					if($this->_page == "his_ideas" || $this->_page == "ideas")
					{
						//Show the ideas of the user
						//If user, we show the items of this user only. If admin, we also show its deleted choices.
						$filter = array("user" => $this_user->uid);
						$filter["state_awaiting_moderation"] = 1;
						if($this_user->uid == $GLOBALS['user']->uid)
						{
							$filter["state_deleted"] = 1;
							$filter["duplicate_items"] = -2;
						}
						if(user_access($site->getData()->adminrole))
							$filter['state_deleted'] = 1;

						$template = "user_items_v2";
					}
					else if($this->_page == "solutions")
					{
						//Show the ideas of the user
						//If user, we show the items of this user only. If admin, we also show its deleted choices.
						$filter = array("solution_userid" => $this_user->uid);
						$filter["state_awaiting_moderation"] = 1;
						if($this_user->uid == $GLOBALS['user']->uid)
						{
							$filter["state_deleted"] = 1;
							$filter["duplicate_items"] = -2;
						}
						if(user_access($site->getData()->adminrole))
							$filter['state_deleted'] = 1;

						$template = "user_items_v2";
					}
					else if($this->_page == "ideas_he_promoted" || $this->_page == "ideas_promoted")
					{
						//Show the ideas the user promoted.
						$filter = array("user_voted_items" => $this_user->uid,
								"user_voted_items_vote_value" => 1);
						if($this_user->uid == $GLOBALS['user']->uid)
						{
							$filter["state_deleted"] = 1;
							$filter["duplicate_items"] = -2;
						}
						$template = "user_items_v2";
					}
					else if($this->_page == "ideas_he_demoted" || $this->_page == "ideas_demoted")
					{
						//Show the ideas the user demoted.
						$filter = array("user_voted_items" => $this_user->uid,
								"user_voted_items_vote_value" => -1);
						if($this_user->uid == $GLOBALS['user']->uid)
						{
							$filter["state_deleted"] = 1;
							$filter["duplicate_items"] = -2;
						}
						$template = "user_items_v2";
					}
					else if($this->_page == "ideas_he_commented" || $this->_page == "ideas_commented")
					{
						//Show the ideas the user commented.
						$filter = array("user_commented_items" => $this_user->uid);
						$filter["state_awaiting_moderation"] = 1;
						if($this_user->uid == $GLOBALS['user']->uid)
						{
							$filter["state_deleted"] = 1;
							$filter["duplicate_items"] = -2;
						}
						$template = "user_items_v2";
					}
					else if($this->_page == "ideas_he_bookmarked" || $this->_page == "ideas_bookmarked")
					{
						//Show the ideas the user bookmarked.
						$filter = array("user_bookmarked_items" => $this_user->uid);
						$filter["state_awaiting_moderation"] = 1;
						if($this_user->uid == $GLOBALS['user']->uid)
						{
							$filter["state_deleted"] = 1;
							$filter["duplicate_items"] = -2;
						}
						$template = "user_items_v2";
					}

					//We now select between the different orderings
					$filter["ordering"] = "newcomments";
					$viewOptions["show_latest_comment_date"] = 1;
					$data_filter = array("include_item_comment_unread_flag" => 1);
					if(isInteger($this->_param1) == false && $this->_param1 != "rss2")
					{
						if($this->_param1 == "most_popular")
						{
							$filter["ordering"] = "mostvotes";
							$viewOptions["show_latest_comment_date"] = 0;
							$basemodule_path .= "/" . $this->_param1;
						}
						else if($this->_param1 == "my_latest_votes")
						{
							$filter["ordering"] = "newuservotes";
							$viewOptions["show_latest_comment_date"] = 0;
							$basemodule_path .= "/" . $this->_param1;
						}
						$this->_shiftPageArguments();
					}

					//Create the model, giving the start and number of rows as parameters.
					if($this->_param1 == null || is_numeric($this->_param1) == false)
						$model = new ChoiceListModel($poll);
					else if(is_numeric($this->_param1) && ($this->_param2 == null || is_numeric($this->_param2) == false))
						$model = new ChoiceListModel($poll, $this->_param1);
					else if(is_numeric($this->_param1) && is_numeric($this->_param2))
						$model = new ChoiceListModel($poll, $this->_param1, $this->_param2);
					$model_name = "choicelist";

					//Set the user as the second model.
					$model2 = UserModel::getInstance($this_user->uid);
					$model2_name = "user";

					//Extract from the GET array what interest us
					$GETfilter = array();
					$GETfilter['keywords'] = $_GET['keywords'];
					$GETfilter['tags'] = $_GET['tags'];
					$GETfilter['admintags'] = $_GET['admintags'];

					//Merge now the filters
					//The priority of the filters is as follow (from lowest to highest):
					// 1. GET array. Everything is parsed, so we can safely use this.
					// 2. The page-specific filter
					// 3. The prefilter filter (e.g. filter by relation & subcat in http://hostname/relation_name/subcategory_name/....)
					// 4. The filter defined in the entrypoint.
					$filter = array_merge($GETfilter, $filter);
					$filter = array_merge($filter, $path_choice_filter);
					$filter = array_merge($filter, $this->_entry_point->getData()->filterArray);
					$model->setFilterParameters($filter);
					$model->setDataFilter($data_filter);

					if(is_numeric($this->_param1) == false && $this->_param1 == "rss2")
					{
						$view = new ChoiceListRSS2View();
						$template = "rss2";
					}
					else
					{
						$view = new ChoiceListView();
						$view->setOptions($viewOptions);
					}
				}
				else
				{
					$content = drupal_not_found();
					break;
				}

			break;

			//Get the RSS2 feed of the frontpage
			/**case "rss2":
				$model = new ChoiceListModel($poll);
				$model->setFilterParameters($this->_entry_point->getData()->filterArray);
				$view = new ChoiceListRSS2View();
				$viewOptions = array("page_title" => t("Most popular ideas today"));
				$view->setOptions($viewOptions);
				$template = "rss2";
			break;*/

			//Get the RSS2 feed of the frontpage
			case "xml":
				//Create a model with an very high number of entry per pages (we want all!)
				$model = new ChoiceListModel($poll, 1, 9999999);

				//Set up the filter. Get all ideas, even the duplicates ones.
				$filter = array("ordering" => "new", "all_states" => 1, "duplicate_items" => -2);
				$filter = array_merge($filter, $path_choice_filter); 
				$filter = array_merge($filter, $this->_entry_point->getData()->filterArray);

				$model->setFilterParameters($filter);
				$model->setDataFilter(array("include_target_release" => 1));
				$view = new ChoiceListXMLView();
				$template = "xml";
			break;

			//Shows the advanced search page
			/**case "advanced_search":
				//If the search is being made, we show the results after completion of the filter array
				if (array_key_exists('_search_submitted',$_GET))
				{
					$choicelistview = new ChoiceListView();
					$filter_array = $choicelistview->completeAdvancedSearchFilterArray($_GET);
					drupal_set_header("Location: search?" . generate_GET_param_list($filter_array));
				}
				else
				{
					//We show the advanced search page.
					$model = new ChoiceListModel($poll);
					$view = new ChoiceListView();
					$template = "advanced_search";
					$this->_page_title = "Search";
				}
			break;*/

			//Vote link when not logged : Ask for login.
			case "needlogin":
			case "need_login":
				drupal_set_message(t("You need to login first!"), 'error_msg');
				drupal_set_header("Location: " . $GLOBALS['basemodule_url']);
			break;

			//Some help pages
			//It simply include some predefined pages.
			case "tour":
				if(is_numeric($this->_param1) == false)
					$page = 1;
				else
					$page = $this->_param1;

				$view = new TourView();
				$viewOptions = array("page_number" =>  $page);
				$view->setOptions($viewOptions);
				$template = "tour";
			break;

			//Some faq pages
			//It simply include some predefined pages.
			case "faq":
				if(is_numeric($this->_param1) == false)
					$page = 1;
				else
					$page = $this->_param1;

				$view = new FaqView();
				$viewOptions = array("page_number" =>  $page);
				$view->setOptions($viewOptions);
				$template = "faq";
			break;

			//Concern an item. Show/edit, comments.
			case "item":
			case "bug":
			case "idea":
				//Check the item number: Does it exists, and if we use the "bug" or "idea" page, it is correct?
				if(is_numeric($this->_param1) == false || ChoiceModel::exists($this->_param1) == false ||
					($this->_page == "bug" && ChoiceModel::isABug($this->_param1) == false) ||
					($this->_page == "idea" && ChoiceModel::isAnIdea($this->_param1) == false)					
					)
				{
					$content = drupal_not_found();
					break;
				}


				//Create the choice model, and load the models related to the choice relation and category/subcategory
				$model = new ChoiceModel();
				$model->setId($this->_param1);
				$model_name = "choice";
				$template = "item";
				$basemodule_path .= "/" . $this->_param1;
				$this->_page_title = t('Idea #!number: "!title"',
					array("!number" => $this->_param1,
						"!title" => strip_tags_and_evil_attributes($model->getData()->title)
					));


				//
				// Ok, since we are using a path without relation/subcat/category for idea, and we want to keep the global
				// navigation bar links coherent with the previous page, we will not modify the below session variable
				// by setting them to their original value before the idea page.
				// BUT let's be coherent: if we are showing an Amarok idea, don't show a Nautilus link in the global navbar
				$candidate_session_prefilter_path_relation = $_SESSION['basemodule_prefilter_path_relation'];
				$candidate_session_prefilter_path_relationsubcat = $_SESSION['basemodule_prefilter_path_relationsubcat'];
				$candidate_session_prefilter_path_category = $_SESSION['basemodule_prefilter_path_category'];
				if($candidate_session_prefilter_path_relationsubcat != -1 && 
					$model->getData()->relation_subcategory_id != $candidate_session_prefilter_path_relationsubcat)
					$candidate_session_prefilter_path_relationsubcat = -1;
				if($candidate_session_prefilter_path_relation != -1 &&
					$model->getData()->relation_id != $candidate_session_prefilter_path_relation)
				{
					$candidate_session_prefilter_path_relation = -1;
					$candidate_session_prefilter_path_relationsubcat = -1;
					$candidate_session_prefilter_path_category = -1;
				}
				if($candidate_session_prefilter_path_category != -1 &&
					$model->getData()->categoryid != $candidate_session_prefilter_path_category)
				{
					$candidate_session_prefilter_path_category = -1;
					$candidate_session_prefilter_path_relation = -1;
					$candidate_session_prefilter_path_relationsubcat = -1;
				}


				//
				// Models for the top navbar.
				//
				if($candidate_session_prefilter_path_relation != -1)
				{
					$models["topnavbar_relation"] = new RelationModel();
					$models["topnavbar_relation"]->setId($candidate_session_prefilter_path_relation);
					if($candidate_session_prefilter_path_relationsubcat != -1)
					{
						$models["topnavbar_relation_subcat"] = new RelationSubcategoryModel();
						$models["topnavbar_relation_subcat"]->setId($candidate_session_prefilter_path_relationsubcat);
					}
				}
				if($candidate_session_prefilter_path_category != -1)
				{
					$models["topnavbar_category"] = new CategoryModel();
					$models["topnavbar_category"]->setId($candidate_session_prefilter_path_category);
				}


				//Bookmark the idea
				if($this->_param2 == "bookmark")
				{
					//Check permission
					if(!user_access($site->getData()->userrole))
					{
						$content = drupal_access_denied();
						break;
					}

					//Bookmark the idea for the registered user.
					$model->bookmark($GLOBALS['user']->uid);
					drupal_set_message(t("The idea was bookmarked."), 'notice_msg');
					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/idea/" . $this->_param1 . "/");
				}
				//Failsafe ajax vote. Reload the poll page.
				else if($this->_param2 == "vote")
				{
					//Check that the user is logged.
					if(!user_access($site->getData()->userrole))
					{
						$content = drupal_access_denied();
						break;
					}

					if($this->_param3 == null || is_numeric($this->_param3) == false || 
						$this->_param4 == null || is_numeric($this->_param4) == false)
					{
						$content = drupal_not_found();
						break;
					}

					$choicesolutionmodel = new ChoiceSolutionModel();
					$choicesolutionmodel->setId($this->_param3);

					//We check that we are voting for a choice in the current $HTTP_HOST
					if($choicesolutionmodel->vote($this->_param4))
						drupal_set_message(t("Thanks for your vote."), 'notice_msg');

					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path']);
				}
				//Unbookmark the idea
				else if($this->_param2 == "unbookmark")
				{
					//Check permission
					if(!user_access($site->getData()->userrole))
					{
						$content = drupal_access_denied();
						break;
					}

					//Bookmark the idea for the registered user.
					$model->unbookmark($GLOBALS['user']->uid);
					drupal_set_message(t("The bookmark was removed."), 'notice_msg');
					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/idea/" . $this->_param1 . "/");
				}
				//Remove the duplicate status of this idea.
				else if($this->_param2 == "unduplicate")
				{
					//Check permission
					if(!user_access($site->getData()->adminrole) && !user_access($site->getData()->moderatorrole)
						&& !user_access($site->getData()->developerrole))
					{
						$content = drupal_access_denied();
						break;
					}

					//Unduplicate the idea
					$model->mark_as_duplicate_of(-1);
					drupal_set_message(t("Duplicate link removed."), 'notice_msg');
					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/idea/" . $this->_param1 . "/");
				}
				//Delete a comment
				else if($this->_param2 == "delete_comment")
				{
					//Check permission
					if(!user_access($site->getData()->adminrole) && !user_access($site->getData()->moderatorrole))
					{
						$content = drupal_access_denied();
						break;
					}

					//Delete the comment
					$comment = new ChoiceCommentModel();
					$comment->setId($this->_param3);
					$comment->delete();
					drupal_set_message(t("Comment deleted."), 'notice_msg');
					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/idea/" . $this->_param1 . "/");
				}
				//Report a spam comment
				else if($this->_param2 == "report_spam_comment")
				{
					//Check permission
					if(!user_access($site->getData()->userrole) || !is_numeric($this->_param3))
					{
						$content = drupal_access_denied();
						break;
					}

					//Make a report
					$report = new ReportModel();
					$report->setAffectedId($this->_param3);
					$report->setItemType(ReportModel::$item_type["comment"]);
					$report->setReportType(ReportModel::$type["spam"]);

					if($report->addVote())
						drupal_set_message(t("Comment reported as spam, moderators will look at it soon."), 'notice_msg');
					else
						drupal_set_message(t("You already reported this comment as spam."), 'error_msg');

					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/idea/" . $this->_param1 . "/");
				}
				//Report a offensive comment
				else if($this->_param2 == "report_offensive_comment")
				{
					//Check permission
					if(!user_access($site->getData()->userrole) || !is_numeric($this->_param3))
					{
						$content = drupal_access_denied();
						break;
					}

					//Make a report
					$report = new ReportModel();
					$report->setAffectedId($this->_param3);
					$report->setItemType(ReportModel::$item_type["comment"]);
					$report->setReportType(ReportModel::$type["offensive"]);

					if($report->addVote())
						drupal_set_message(t("Comment reported as offensive, moderators will look at it soon."), 'notice_msg');
					else
						drupal_set_message(t("You already reported this comment as offensive."), 'error_msg');

					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/idea/" . $this->_param1 . "/");
				}
				//Report a spam idea
				else if($this->_param2 == "report_spam_idea")
				{
					//Check permission
					if(!user_access($site->getData()->userrole))
					{
						$content = drupal_access_denied();
						break;
					}

					//Make a report
					$report = new ReportModel();
					$report->setAffectedId($this->_param1);
					$report->setItemType(ReportModel::$item_type["choice"]);
					$report->setReportType(ReportModel::$type["spam"]);

					if($report->addVote())
						drupal_set_message(t("Idea reported as spam, moderators will look at it soon."), 'notice_msg');
					else
						drupal_set_message(t("You already reported this idea as spam."), 'error_msg');

					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/idea/" . $this->_param1 . "/");
				}
				//Report a non-idea idea
				else if($this->_param2 == "report_not_idea")
				{
					//Check permission
					if(!user_access($site->getData()->userrole))
					{
						$content = drupal_access_denied();
						break;
					}

					//Make a report
					$report = new ReportModel();
					$report->setAffectedId($this->_param1);
					$report->setItemType(ReportModel::$item_type["choice"]);
					$report->setReportType(ReportModel::$type["not_an_idea"]);

					if($report->addVote())
						drupal_set_message(t("Item reported as not being an idea, moderators will look at it soon."), 'notice_msg');
					else
						drupal_set_message(t("You already reported this item as not an idea."), 'error_msg');

					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/idea/" . $this->_param1 . "/");
				}
				//Report a idea in dev
				else if($this->_param2 == "report_in_dev_idea")
				{
					//Check permission
					if(!user_access($site->getData()->userrole))
					{
						$content = drupal_access_denied();
						break;
					}

					//Make a report
					$report = new ReportModel();
					$report->setAffectedId($this->_param1);
					$report->setItemType(ReportModel::$item_type["choice"]);
					$report->setReportType(ReportModel::$type["indev"]);

					if($report->addVote())
						drupal_set_message(t("Idea reported as being in development, moderators will look at it soon."), 'notice_msg');
					else
						drupal_set_message(t("You already reported this idea as being in development."), 'error_msg');

					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/idea/" . $this->_param1 . "/");
				}
				//Report a implemented idea
				else if($this->_param2 == "report_implemented_idea")
				{
					//Check permission
					if(!user_access($site->getData()->userrole))
					{
						$content = drupal_access_denied();
						break;
					}

					//Make a report
					$report = new ReportModel();
					$report->setAffectedId($this->_param1);
					$report->setItemType(ReportModel::$item_type["choice"]);
					$report->setReportType(ReportModel::$type["implemented"]);

					if($report->addVote())
						drupal_set_message(t("Idea reported as being implemented, moderators will look at it soon."), 'notice_msg');
					else
						drupal_set_message(t("You already reported this idea as being implemented."), 'error_msg');

					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/idea/" . $this->_param1 . "/");
				}
				else if($this->_param2 == "promote")
				{
					//Show the promote page of the "choice".
					$view = new ChoiceView();
					$template = "promote";
					$this->_page_title = "Promote \"" . strip_tags_and_evil_attributes($model->getData()->title) . "\"";
				}
				else if($this->_param2 == "log")
				{
					//Show the log page of the "choice".
					$models["choiceloglist"] = new ChoiceLogListModel();
					$models["choiceloglist"]->setFilterParameters(array("choice_id" => $model->getId()));
	
					$view = new ChoiceView();
					$template = "log";
					$this->_page_title = t('Log of idea #!number: "!title"',
						array("!number" => $this->_param1,
							"!title" => strip_tags_and_evil_attributes($model->getData()->title)
						));
				}
				else if($this->_param2 == "report_duplicate")
				{
					//If it is a POST request, we save the duplicate report
					if (array_key_exists('_dup_submitted',$_POST))
					{
						//Check permission
						if(!user_access($site->getData()->userrole))
						{
							drupal_set_message(t("You must be logged in to report a duplicate."), 'error_msg');
							//Show again the edit page with the error.
							$model = new ChoiceModel();
							$model->setId($this->_param1);
							$view = new ChoiceView();
							$template = "report_duplicate";
							break;
						}

						$choicemodel = new ChoiceModel();
						$choicemodel->setId($this->_param1);
						$newduprep = new DuplicateReportModel($choicemodel);

						if($newduprep->loadFromPost() && $newduprep->store())
						{
								drupal_set_message(t("Thanks for the duplicate report, we will look at it soon."), 'notice_msg');
								drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/idea/" . $this->_param1 . "/report_duplicate");
						}
						else
						{
							//Show again the edit page with the list of errors.
							$view = new ChoiceView();
							$template = "report_duplicate";
							$this->_page_title = t("Report duplicate of \"!title\"", 
								array("!title" => strip_tags_and_evil_attributes($model->getData()->title)));
						}
					}
					else
					{
						//Show the report duplicate page
						$view = new ChoiceView();
						$template = "report_duplicate";
						$this->_page_title = t("Report duplicate of \"!title\"", 
							array("!title" => strip_tags_and_evil_attributes($model->getData()->title)));
					}
				}
				else if($this->_param2 == "image")
				{
					//Show a link image.
					$view = new ImagelinkView($this->_param3);
				}
				else if($this->_param2 == "edit")
				{
					//Check permission
					//More advanced permission checking (who is allowed to edit what) is done on the store() methods
					if(!user_access($site->getData()->userrole))
					{
						$content = drupal_access_denied();
						break;
					}

					//Since we will edit this idea, we will need a few more set of data, such as the category list,...
					//Load the category list corresponding to this poll.
					$models["categorylist"] = new CategoryListModel($GLOBALS['poll']);

					//Load the category list corresponding to this poll.
					$models["relationlist"] = new RelationListModel($GLOBALS['poll']);

					//If we are in a entrypoint that filter by relation id
					//Let's get and store the relation name
/**					if($GLOBALS['entrypoint']->getData()->filterArray['relation'] != null)
					{
						$relation = new RelationModel();
						$relation->setId($GLOBALS['entrypoint']->getData()->filterArray['relation']);
						$this->_relation_name = $relation->getData()->name;
					}

					//Let's also get the subcategory list of the current relation
					if($GLOBALS['entrypoint']->getData()->filterArray['relation'] != null || 
						($this->_data != null && $this->_data->relation_id != -1) ||
						($_POST['_choice_submitted'] != null && $_POST['choice_relation'] != -2))
					{
						$subcat_list = new RelationSubcategoryListModel();
						$relation_id = $GLOBALS['entrypoint']->getData()->filterArray['relation'];
						if($relation_id == null)
							$relation_id = $_POST['choice_relation'];
						if($relation_id == null)
							$relation_id = $this->_data->relation_id;

						if($relation_id != -1)
						{
							$subcat_list->setFilterParameters(array("relation_id" => $relation_id));
							$this->_relation_subcategory_list = $subcat_list->getData();
						}
					}*/

					//If it is a POST request, we save the choice
					if (array_key_exists('_choice_submitted',$_POST))
					{
						//Save the idea and its solutions
						$newchoice = new ChoiceModel($poll);
						$newchoice->setId($this->_param1);

						$solutionlist = new ChoiceSolutionListModel();
						$solutionlist->setFilterParameters(array("choice_id" => $this->_param1));

						if($newchoice->loadFromPost(true))
						{
							$result = true;
							foreach($solutionlist->getData() as $solution)
							{
								$sol = new ChoiceSolutionModel();
								$sol->setId($solution->id);
								$result = $result && $sol->loadFromPost(true);
								$sols[] = $sol;
							}

							if($result && $newchoice->store())
							{
								foreach($sols as $sol)
								{
									$result = $result && $sol->store($newchoice);
								}
						
								if($result)
								{
									drupal_set_message(t("Thanks for the modifications."), 'notice_msg');
									drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" .
										$newchoice->getChoiceTypeName() . "/" . $this->_param1 . "/");
									break;
								}
							}
						}

						//Show again the edit page with the list of errors.
						$view = new ChoiceView();
						$template = "edit";
						$this->_page_title = "Edit \"" . strip_tags_and_evil_attributes($model->getData()->title) . "\"";
					}
					else
					{
						//Show the edit page.
						$view = new ChoiceView();
						$template = "edit";
						$this->_page_title = "Edit \"" . strip_tags_and_evil_attributes($model->getData()->title) . "\"";
					}
				}
				else if($this->_param2 == "need_login")
				{
					//Needs login. Display a message.
					drupal_set_message(t("You need to login first!"), 'error_msg');
					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/item/" . $this->_param1 . "/");
				}
				else if($this->_param2 == "rss2")
				{
					// Display RSS feed of choice and comments
					$view = new ChoiceRSS2View();
					$template ="rss2";
				}
				else if($this->_param2 == null)
				{
					//A comment was submitted. Save it.
					if (array_key_exists('_comment_submitted',$_POST))
					{
						//Check permission
						if(!user_access($site->getData()->userrole))
						{
							$content = drupal_access_denied();
							break;
						}

						//Save the comment.
						$curchoice = new ChoiceModel();
						$curchoice->setId($this->_param1);
						$newcomment = new ChoiceCommentModel($curchoice);
						if($newcomment->loadFromPost() && $newcomment->store())
						{
								drupal_set_message(t("Thanks for your comment."), 'notice_msg');
								drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . 
									$curchoice->getChoiceTypeName() . "/" . $this->_param1 . "/");
						}
						else
						{
							//Show again the idea/bug page with the list of errors to correct.
							$view = new ChoiceView();
							$this->_page_title = strip_tags_and_evil_attributes($model->getData()->title);
						}
					}
					//A solution was submitted. Save it.
					else if (array_key_exists('_solution_submitted',$_POST))
					{
						//Check permission
						if(!UserModel::currentUserHasPermission("submit_solution", "Choice", $model))
						{
							$content = drupal_access_denied();
							break;
						}

						//If the choice is in development or implemented, do not allow solution submission
						if($model->getData()->status == ChoiceModel::$choice_status["workinprogress"] ||
							$model->getData()->status == ChoiceModel::$choice_status["done"])
						{
							$content = drupal_access_denied();
							break;
						}

						//Check that there was a minimum interval between solution postings, to avoid spam
						if(QAPollLogger::getInstance()->isLatestItemSolutionSubmissionOlderThanFiveMin($GLOBALS['user']->uid) ==
							false)
						{
							drupal_set_message(t("To prevent spam, there is a 5 minutes minimum interval between each solution submission. Please wait."), 'error_msg');
							$view = new ChoiceView();
							$this->_page_title = strip_tags_and_evil_attributes($model->getData()->title);
							break;
						}

						//Save the solution.
						$solution = new ChoiceSolutionModel();
						if($solution->loadFromPost() && $solution->store())
						{
							//Add a link from this solution to this idea
							$solution->linkToChoice($this->_param1);

							//Log this (will only log if user is logged)
							QAPollLogger::getInstance()->logItemSolutionSubmission($GLOBALS['user']->uid, $solution->getId());

							drupal_set_message(t("Thanks for proposing your solution."), 'notice_msg');
							drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/idea/" . 
								$this->_param1 . "/");
						}
						else
						{
							//Show again the idea/bug page with the list of errors to correct.
							$view = new ChoiceView();
							$this->_page_title = strip_tags_and_evil_attributes($model->getData()->title);
						}
					}
					//Tags were submitted. Save it.
					else if (array_key_exists('_tags_submitted',$_POST))
					{
						//Check permission
						if(!user_access($site->getData()->userrole))
						{
							$content = drupal_access_denied();
							break;
						}

						//Save the comment.
						$curchoice = new ChoiceModel();
						$curchoice->setId($this->_param1);
						$curchoice->setTags($_POST['tags'], 0, $GLOBALS['user']->uid);

						drupal_set_message(t("New tags saved."), 'notice_msg');
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" .
							$curchoice->getChoiceTypeName() . "/" . $this->_param1 . "/");
					}
					//Tags were submitted. Save it.
					else if (array_key_exists('_admintags_submitted',$_POST))
					{
						//Check permission
						if(!user_access($site->getData()->adminrole) && !user_access($site->getData()->developerrole))
						{
							$content = drupal_access_denied();
							break;
						}

						//Save the comment.
						$curchoice = new ChoiceModel();
						$curchoice->setId($this->_param1);
						$curchoice->setTags($_POST['admintags'], 1, $GLOBALS['user']->uid);

						drupal_set_message(t("New admin tags saved."), 'notice_msg');
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" .
							$curchoice->getChoiceTypeName() . "/" . $this->_param1 . "/");
					}
					else if(array_key_exists('_status_submitted', $_POST))
					{
						//A status change was submitted. Save it.

						//Check permission
						if(!user_access($site->getData()->adminrole) && !user_access($site->getData()->developerrole) &&
							!user_access($site->getData()->moderatorrole))
						{
							$content = drupal_access_denied();
							break;
						}

						$choice->setStatus($_POST['status']);

						drupal_set_message(t("New status saved."), 'notice_msg');
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" .
							$choice->getChoiceTypeName() . "/" . $this->_param1 . "/");
					}
					else if(array_key_exists('_release_submitted', $_POST))
					{
						//A status change was submitted. Save it.

						//Check permission
						if(!user_access($site->getData()->adminrole) && !user_access($site->getData()->developerrole) &&
							!user_access($site->getData()->moderatorrole))
						{
							$content = drupal_access_denied();
							break;
						}

						$choice->setTargetRelease($_POST['release']);

						drupal_set_message(t("New target release saved."), 'notice_msg');
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" .
							$choice->getChoiceTypeName() . "/" . $this->_param1 . "/");
					}
					else
					{
						//Show an idea
						//Add the release list data set, so that we can choose another release for the idea.
						$models["releaselist"] = new ReleaseListModel();

						$view = new ChoiceView();

						//Log this (will only log if user is logged)
						QAPollLogger::getInstance()->logItemView($GLOBALS['user']->uid, $this->_param1);
					}
				}
			break;

			//Global edition path. Used when some forms can be called from differents path.
			//In this case, the path were to go back in given in _POST['destination'] (form submission)
			// or _GET['destination'] (links).
			case "edit":
				//A solution was selected
				if($this->_param1 == "select_solution" || $this->_param1 == "unselect_solution")
				{
					if(!is_numeric($this->_param2) || !is_numeric($this->_param3))
					{
						$content = drupal_access_denied();
						break;
					}

					$choice = new ChoiceModel();
					$choice->setId($this->_param2);

					//Check permissions
					if(!UserModel::currentUserHasPermission("select_solution", "Choice", $choice))
					{
						$content = drupal_access_denied();
						break;
					}

					$linklist = new ChoiceSolutionLinkListModel();
					$linklist->setFilterParameters(array("choiceid" => $this->_param2, "choicesolutionid" => $this->_param3));
					$list = $linklist->getData();

					$link = new ChoiceSolutionLinkModel();
					$link->setId($list[0]->id);
					if(count($list) == 1 && $link->select(($this->_param1 == "select_solution")?1:0))
						drupal_set_message(t("Thanks for your attention to detail."), 'notice_msg');
					else
						drupal_set_message(t("An error occured."), 'error_msg');

					if($_GET["destination"] != "")
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . $_GET['destination']);
					else
						drupal_set_header("Location: " . $GLOBALS['basemodule_url']);
				}
				//Report a spam solution
				else if($this->_param1 == "report_spam_solution")
				{
					//Check permission
					if(!user_access($site->getData()->userrole) || !is_numeric($this->_param2))
					{
						$content = drupal_access_denied();
						break;
					}

					//Make a report
					$report = new ReportModel();
					$report->setAffectedId($this->_param2);
					$report->setItemType(ReportModel::$item_type["solution"]);
					$report->setReportType(ReportModel::$type["spam"]);

					if($report->addVote())
						drupal_set_message(t("The solution was reported as spam, moderators will look at it soon."), 'notice_msg');
					else
						drupal_set_message(t("You already reported this solution as spam."), 'error_msg');

					if($_GET["destination"] != "")
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . $_GET['destination']);
					else
						drupal_set_header("Location: " . $GLOBALS['basemodule_url']);
				}
				//Report a irrelevant solution
				else if($this->_param1 == "report_irrelevant_solution")
				{
					//Check permission
					if(!user_access($site->getData()->userrole) || !is_numeric($this->_param2))
					{
						$content = drupal_access_denied();
						break;
					}

					//Make a report
					$report = new ReportModel();
					$report->setAffectedId($this->_param2);
					$report->setItemType(ReportModel::$item_type["solution"]);
					$report->setReportType(ReportModel::$type["not_an_idea"]);

					if($report->addVote())
						drupal_set_message(t("The solution was reported as irrelevant, moderators will look at it soon."), 'notice_msg');
					else
						drupal_set_message(t("You already reported this solution as irrelevant."), 'error_msg');

					if($_GET["destination"] != "")
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . $_GET['destination']);
					else
						drupal_set_header("Location: " . $GLOBALS['basemodule_url']);
				}
				//Delete a solution
				else if($this->_param1 == "delete_solution_link")
				{
					if(!is_numeric($this->_param2) || !is_numeric($this->_param3))
					{
						$content = drupal_access_denied();
						break;
					}

					$choice = new ChoiceModel();
					$choice->setId($this->_param2);

					//Check permission
					if(!UserModel::currentUserHasPermission("delete_solution", "Choice", $choice))
					{
						$content = drupal_access_denied();
						break;
					}

					//Delete the comment
					$solution = new ChoiceSolutionModel();
					$solution->setId($this->_param3);
					$solution->delete_choice_link($this->_param2);
					drupal_set_message(t("Rationale<->Solution link deleted."), 'notice_msg');
					if($_GET["destination"] != "")
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . $_GET['destination']);
					else
						drupal_set_header("Location: " . $GLOBALS['basemodule_url']);
				}
				//Delete a solution
				else if($this->_param1 == "delete_solution")
				{
					if(!is_numeric($this->_param2) || !is_numeric($this->_param3))
					{
						$content = drupal_access_denied();
						break;
					}

					$choice = new ChoiceModel();
					$choice->setId($this->_param2);
					$solution = new ChoiceSolutionModel();
					$solution->setId($this->_param3);

					//Check permission
					if(!UserModel::currentUserHasPermission("delete_solution", "Choice", $choice) || 
						$solution->linkExists($this->_param2) == false)
					{
						$content = drupal_access_denied();
						break;
					}

					//Delete the solution
					$solution->delete();
					drupal_set_message(t("Solution deleted."), 'notice_msg');
					if($_GET["destination"] != "")
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . $_GET['destination']);
					else
						drupal_set_header("Location: " . $GLOBALS['basemodule_url']);
				}
				//A solution was edited. Save it.
				else if (array_key_exists('_solution_edited',$_POST))
				{
					if(!is_numeric($_POST['solution-id']))
					{
						$content = drupal_access_denied();
						break;
					}

					//Set the solution model
					$solution = new ChoiceSolutionModel();
					$solution->setId($_POST['solution-id']);

					//Set the choice model
					$choice = new ChoiceModel();
					$choice->setId($_POST['choice-id']);

					//Not quite clean: we are comibining in one form the edition and mark as duplicate features
					if($_POST['duplicate_of'] != "")
					{
						if(!is_numeric($_POST['duplicate_of']) ||
							!UserModel::currentUserHasPermission("mark_solution_dup", "Choice", $choice)
							|| !is_numeric($_POST['choice-id']))
						{
							$content = drupal_access_denied();
							break;
						}

						//Ok, we got a relative solution number (relative to the idea)
						//Translate it to a absolute number
						$choicesollist = new ChoiceSolutionListModel();
						$choicesollist->setFilterParameters(array("choice_id" => $_POST['choice-id'],
							 "choice_relation_relative_number" => $_POST['duplicate_of']));
						$choicesollist->setDataFilter(array("include_minimal_data" => true));

						$data = $choicesollist->getData();
						if(count($data) == 1 && $solution->mark_as_duplicate_of($data[0]->id))
							drupal_set_message(t("The solution was marked as duplicate."), 'notice_msg');
						else
							drupal_set_message(t("A error occured while marking as duplicate."), 'error_msg');
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . $_POST['destination']);
					}
					else
					{
						//Check permission before!
						//if((!UserModel::currentUserHasPermission("edit_solution", "Choice", $choice) &&
						//	!UserModel::currentUserHasPermission("edit_solution", "ChoiceSolution", $solution)) ||
						//	!$solution->linkExists($choice->getId()))
						if(!UserModel::currentUserHasPermission("edit_solution", "ChoiceSolution", $solution) &&
							!UserModel::currentUserHasPermission("edit_solution", "Choice", $choice))
						{
							$content = drupal_access_denied();
							break;
						}


						if($solution->loadFromPost() && $solution->store($choice))
						{
							drupal_set_message(t("Thanks for your attention to detail."), 'notice_msg');
							drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . $_POST['destination']);
						}
						else
						{
							drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . $_POST['destination']);
						}
					}
				}
				//A choice was edited. Save it.
				else if (array_key_exists('_choice_edited',$_POST))
				{
					if(!is_numeric($_POST['choice-id']))
					{
						$content = drupal_access_denied();
						break;
					}

					//Set the choice model
					$choice = new ChoiceModel();
					$choice->setId($_POST['choice-id']);

					//Not quite clean: we are comibining in one form the edition and mark as duplicate features
					if($_POST['duplicate_of'] != "")
					{
						if(!is_numeric($_POST['duplicate_of']) ||
							!UserModel::currentUserHasPermission("mark_dup", "Choice", $choice))
						{
							$content = drupal_access_denied();
							break;
						}

						if($choice->mark_as_duplicate_of($_POST['duplicate_of']))
							drupal_set_message(t("The idea was marked as duplicate."), 'notice_msg');
						else
							drupal_set_message(t("A error occured while marking as duplicate. Either the target idea is already a dup, or it does not exist."), 'error_msg');
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . $_POST['destination']);
					}
					else
					{
						//Check permission before!
						/**if((!UserModel::currentUserHasPermission("edit_solution", "Choice", $choice) &&
							!UserModel::currentUserHasPermission("edit_solution", "ChoiceSolution", $solution)) ||
							!$solution->linkExists($choice->getId()))
						{
							$content = drupal_access_denied();
							break;
						}


						if($solution->loadFromPost() && $solution->store())
						{
							drupal_set_message("Thanks for your attention to detail.", 'notice_msg');
							drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . $_POST['destination']);
						}
						else
						{
							drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . $_POST['destination']);
						}*/
					}
				}
				else if($this->_param1 == "approve_idea")
				{
					if(is_numeric($this->_param2) == false)
					{
						$content = drupal_not_found();
						break;
					}

					$choicemodel = new ChoiceModel();
					$choicemodel->setId($this->_param2);

					//Check permission
					if(!UserModel::currentUserHasPermission("approve_idea", "Choice", $choicemodel))
					{
						$content = drupal_access_denied();
						break;
					}
				
					$choicemodel->add_approval_vote($GLOBALS['user']->uid, 1);
					$choicemodel->check_idea_approval_count_and_update_status();

					drupal_set_message(t("Idea approved."), 'notice_msg');
					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . $_GET['destination']);
					break;
				}
				else if($this->_param1 == "mark_as_invalid")
				{
					if(is_numeric($this->_param2) == false)
					{
						$content = drupal_not_found();
						break;
					}

					$choicemodel = new ChoiceModel();
					$choicemodel->setId($this->_param2);

					//Check permission
					if(!UserModel::currentUserHasPermission("status_mark_as_nonidea", 
						"Choice", $choicemodel) &&
						!UserModel::currentUserHasPermission("edit_status",
						"Choice", $choicemodel))
					{
						$content = drupal_access_denied();
						break;
					}

					$choicemodel->setStatus(ChoiceModel::$choice_status["not_an_idea"]);

					drupal_set_message(t("Idea marked as invalid."), 'notice_msg');
					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . $_GET['destination']);
				}
				else if($this->_param1 == "mark_as_already_implemented")
				{
					if(is_numeric($this->_param2) == false)
					{
						$content = drupal_not_found();
						break;
					}

					$choicemodel = new ChoiceModel();
					$choicemodel->setId($this->_param2);

					//Check permission
					if(!UserModel::currentUserHasPermission("status_mark_as_already_implemented",
						"Choice", $choicemodel) && 
						!UserModel::currentUserHasPermission("edit_status",
						"Choice", $choicemodel))
					{
						$content = drupal_access_denied();
						break;
					}

					$choicemodel->setStatus(ChoiceModel::$choice_status["already_done"]);

					drupal_set_message(t("Idea marked as already implemented."), 'notice_msg');
					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . $_GET['destination']);
					break;
				}

			break;

			//Delete an idea/bug
			case "deleteitem":
				if (!user_access($site->getData()->adminrole) && !user_access($site->getData()->moderatorrole))
				{
					$content = drupal_access_denied();
					break;
				}
				$choiceModel = new ChoiceModel();
				$choiceModel->setId($this->_param1);
				if($choiceModel->delete())
					drupal_set_message(t("The idea was successfully deleted."), 'notice_msg');
				else
					drupal_set_message(t("Internal error while deleting this idea."), 'error_msg');
				drupal_set_header("Location: " . $GLOBALS['basemodule_url']);
			break;

			//Delete an idea/bug
			case "undeleteitem":
				if (!user_access($site->getData()->adminrole) && !user_access($site->getData()->moderatorrole))
				{
					$content = drupal_access_denied();
					break;
				}
				$choiceModel = new ChoiceModel();
				$choiceModel->setId($this->_param1);
				if($choiceModel->undelete())
					drupal_set_message(t("The idea was successfully undeleted."), 'notice_msg');
				else
					drupal_set_message(t("Internal error while deleting this idea."), 'error_msg');
				drupal_set_header("Location: " . $GLOBALS['basemodule_url']);
			break;

			//Idea/bug submission
			case "submit":

				//Check that we are logged.
				if(user_access($site->getData()->userrole) == false)
				{
					$content = drupal_access_denied();
					break;
				}

				//If it is a POST request, we save the choice
				if(array_key_exists('_choice_submitted',$_POST))
				{
					//Save the bug/idea
					$newchoice = new ChoiceModel($poll);
					$newsolution = new ChoiceSolutionModel();
					if($newchoice->loadFromPost(false) && $newsolution->loadFromPost(false) && $newchoice->store() && $newsolution->store() &&
						$newsolution->linkToChoice($newchoice->getId()))
					{
						drupal_set_message(t("Thank you for posting!"), 'notice_msg');
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/idea/" . $newchoice->getId() . "/");
						break;

					}
					//Show again the submission page with the list of errors to correct.
					$model = new ChoiceModel($poll);
					//Load the category list corresponding to this poll.
					$models["categorylist"] = new CategoryListModel($GLOBALS['poll']);
					$view = new ChoiceView();
					$template = "edit";
					$this->_page_title = t("Submit your idea (3/3)");
				}
				//Non-AJAX duplicate checking
				else if(array_key_exists('_keywords_submitted',$_POST))
				{
					$duplist = new ChoiceListModel($poll, 1, 10);

					$filter = array("ordering" => "search-relevance", "keywords" => $_POST['keywords'], "keywords_skip_common_words" => 1, 
						"state_awaiting_moderation" => 1);
		
					//Merge now the filter of the entrypoint and use the filter.
					//The filter of the entrypoint has priority over search filter.
					$filter = array_merge($filter, $this->_entry_point->getData()->filterArray);
					$duplist->setFilterParameters($filter);
					$models['duplist'] = $duplist;

					//Show the submission page.
					$model = new ChoiceModel($poll);
					$view = new ChoiceView();
					$template = "submit_second_part";
					$this->_page_title = t("Submit your idea (2/3)");
				}
				else
				{
					//Show the submission page.
					$model = new ChoiceModel($poll);
					$view = new ChoiceView();

					if($this->_param1 == "3")
					{
						//Load the category list corresponding to this poll.
						$models["categorylist"] = new CategoryListModel($GLOBALS['poll']);

						$template = "edit";
						$this->_page_title = t("Submit your idea (3/3)");
					}
					else if($this->_param1 == "2")
					{
						$template = "submit_second_part";
						$this->_page_title = t("Submit your idea (2/3)");
					}
					else
					{
						$template = "submit_first_part";
						$this->_page_title = t("Submit your idea (1/3)");
					}
				}
			break;

			//Failsafe ajax vote. Reload the poll page.
			case "vote":

				//Check that the user is logged.
				if(!user_access($site->getData()->userrole))
				{
					$content = drupal_access_denied();
					break;
				}

				if($this->_param1 == null || is_numeric($this->_param1) == false || 
					$this->_param2 == null || is_numeric($this->_param2) == false)
				{
					$content = drupal_not_found();
					break;
				}

				$choicesolutionmodel = new ChoiceSolutionModel();
				$choicesolutionmodel->setId($this->_param1);

				//We check that we are voting for a choice in the current $HTTP_HOST
				if($choicesolutionmodel->vote($this->_param2))
					drupal_set_message(t("Thanks for your vote."), 'notice_msg');

				//TODO: Bad hack: The first part of the modulepath is always set :/
				//So I'm removing the four letters of the "vote" word.
				drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . substr($GLOBALS['basemodule_path'],
					0, strlen($GLOBALS['basemodule_path']) - 4));
			break;

			//Page to process the duplicate report
			case "process_spam_reports":
			case "process_offensive_reports":
			case "process_indev_reports":
			case "process_implemented_reports":
			case "process_irrelevance_reports":

				//Check the rights.
				if(!UserModel::currentUserHasPermission("process_report", ""))
				{
					$content = drupal_access_denied();
					break;
				}

				if($this->_param1 == "accept_report" || $this->_param1 == "accept_report2")
				{
					if(!is_numeric($this->_param2))
					{
						$content = drupal_not_found();
						break;
					}

					//Discard a duplicate report
					$report = new ReportModel();
					$report->setId($this->_param2);
					if($this->_param1 == "accept_report" && $report->accept() || 
						$this->_param1 == "accept_report2" && $report->accept2())
						drupal_set_message(t("Report processed."), 'notice_msg');
					else
						drupal_set_message(t("Error while processing this report. It is very likely that another admin just moderated this report before you did, or that the report was invalidated by your previous actions."), 'error_msg');

					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/");
					break;
				}
				else if($this->_param1 == "discard_report")
				{
					if(!is_numeric($this->_param2))
					{
						$content = drupal_not_found();
						break;
					}

					//Discard a duplicate report
					$report = new ReportModel();
					$report->setId($this->_param2);
					if($report->reject())
						drupal_set_message(t("Report discarded."), 'notice_msg');
					else
						drupal_set_message(t("Error while processing this report. It is very likely that another admin just moderated this report before you did, or that the report was invalidated by your previous actions."), 'error_msg');

					drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/");
					break;
				}


				//We now select between the different categories
				if($this->_page == "process_spam_reports")
				{
					$filter = array("report_type" => ReportModel::$type['spam']);
					$filter["item_type"] = ReportModel::$item_type['solution'];
				}
				else if($this->_page == "process_offensive_reports")
				{
					$filter = array("report_type" => ReportModel::$type['offensive']);
					$filter["item_type"] = ReportModel::$item_type['comment'];
				}
				else if($this->_page == "process_indev_reports")
				{
					$filter = array("report_type" => ReportModel::$type['indev']);
					$filter["item_type"] = ReportModel::$item_type['choice'];
				}
				else if($this->_page == "process_implemented_reports")
				{
					$filter = array("report_type" => ReportModel::$type['implemented']);
					$filter["item_type"] = ReportModel::$item_type['choice'];
				}
				else if($this->_page == "process_irrelevance_reports")
				{
					$filter = array("report_type" => ReportModel::$type['not_an_idea']);
					$filter["item_type"] = ReportModel::$item_type['solution'];
				}
				if(isInteger($this->_param1) == false && $this->_param1 != "rss2")
				{
					if($this->_param1 == "comments")
					{
						$filter["item_type"] = ReportModel::$item_type['comment'];
						$basemodule_path .= "/" . $this->_param1;
					}
					else if($this->_param1 == "ideas")
					{
						$filter["item_type"] = ReportModel::$item_type['choice'];
						$basemodule_path .= "/" . $this->_param1;
					}
					$this->_shiftPageArguments();
				}

				//Show the page with the list of spam report to process
				if($this->_param1 == null || is_numeric($this->_param1) == false)
					$model = new ReportListModel();
				else if(is_numeric($this->_param1) && $this->_param2 == null)
					$model = new ReportListModel($this->_param1);
				else if(is_numeric($this->_param1) && is_numeric($this->_param2))
					$model = new ReportListModel($this->_param1, $this->_param2);
				else
				{
					$content = drupal_not_found();
					break;
				}
				$model->setFilterParameters($filter);
				$model->setDataFilter(array("include_item_models" => 1));
				$model_name = "reportlist";
				$template = "process_reportlist";
				$view = new ReportListView();

			break;

			//Page to process the duplicate report, for admins only
			case "process_duplicate_reports":

				//Check the rights.
				if(!UserModel::currentUserHasPermission("process_report", ""))
				{
					$content = drupal_access_denied();
					break;
				}

				if($this->_param1 == "discard_duplicate_report")
				{
					//Discard a duplicate report
					$duprep = new DuplicateReportModel();
					$duprep->setId($this->_param2);
					if($duprep->reject())
					{
						drupal_set_message(t("Duplicate report discarded."), 'notice_msg');
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/process_duplicate_reports/");
					}
					else
					{
						drupal_set_message(t("Error while processing this report. It is very likely that another admin just moderated this report before you did, or that the report was invalidated by your previous actions."), 'error_msg');
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/process_duplicate_reports/");
					}
				}
				else if($this->_param1 == "mark_as_duplicate_dup")
				{
					//Accept a duplicate report
					$duprep = new DuplicateReportModel();
					$duprep->setId($this->_param2);
					if($duprep->accept())
					{
						drupal_set_message(t("Idea marked as duplicate."), 'notice_msg');
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/process_duplicate_reports/");
					}
					else
					{
						drupal_set_message(t("Error while processing this report. It is very likely that another admin just moderated this report before you did, or that the report was invalidated by your previous actions."), 'error_msg');
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/process_duplicate_reports/");
					}
				}
				else if($this->_param1 == "mark_as_duplicate_orig")
				{
					//Accept a duplicate report, but in the opposite way. (see accept_opposite doc)
					$duprep = new DuplicateReportModel();
					$duprep->setId($this->_param2);
					if($duprep->accept_opposite())
					{
						drupal_set_message(t("Idea marked as duplicate."), 'notice_msg');
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/process_duplicate_reports/");
					}
					else
					{
						drupal_set_message(t("Error while processing this report. It is very likely that another admin just moderated this report before you did, or that the report was invalidated by your previous actions."), 'error_msg');
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/process_duplicate_reports/");
					}
				}
				else if($this->_param1 == "link_solution_to")
				{
					if(is_numeric($this->_param2) == false || is_numeric($this->_param3) == false)
					{
						$content = drupal_not_found();
						break;
					}

					//Add a link from this solution to this idea
					$solution = new ChoiceSolutionModel();
					$solution->setId($this->_param2);
					if($solution->linkToChoice($this->_param3))
					{
						drupal_set_message(t("The solution was linked to the other ideas."), 'notice_msg');
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/process_duplicate_reports/");
					}
					else
					{
						drupal_set_message(t("An error occured."), 'error_msg');
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/process_duplicate_reports/");
					}
				}
				else if($this->_param1 == "mark_solution_as_dup")
				{
					if(is_numeric($this->_param2) == false || is_numeric($this->_param3) == false)
					{
						$content = drupal_not_found();
						break;
					}

					//Add a link from this solution to this idea
					$solution = new ChoiceSolutionModel();
					$solution->setId($this->_param2);
					if($solution->mark_as_duplicate_of($this->_param3))
					{
						drupal_set_message(t("The solution was marked as dup, the votes were merged."), 'notice_msg');
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/process_duplicate_reports/");
					}
					else
					{
						drupal_set_message(t("An error occured."), 'error_msg');
						drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/process_duplicate_reports/");
					}
				}
				else
				{
					//Show the page with the list of dup report to process
					if($this->_param1 == null || is_numeric($this->_param1) == false)
						$model = new DuplicateReportListModel();
					else if(is_numeric($this->_param1) && $this->_param2 == null)
						$model = new DuplicateReportListModel($this->_param1);
					else if(is_numeric($this->_param1) && is_numeric($this->_param2))
						$model = new DuplicateReportListModel($this->_param1, $this->_param2);
					else
					{
						$content = drupal_not_found();
						break;
					}
					$model->setFilterParameters(array("status" => 0));
					$model_name = "duplicate_report_list";
					$view = new DuplicateReportListView();
				}
			break;

			//Show the administration panel.
			case "ideatorrent_admin":
				//Check that we have the rights.
				if(!user_access($site->getData()->adminrole))
				{
					$content = drupal_access_denied();
					break;
				}

				//Choose the admin page!
				if($this->_param1 == "selected_theme_options")
				{
					//If it is a POST request, we update the entry point description
					if (array_key_exists('_config_saved',$_POST))
					{
						//Save the bug/idea
						$themeconfig = QAPollThemeConfig::getInstance(QAPollConfig::getInstance()->getValue("selected_theme"));
						if($themeconfig->loadFromPost() && $themeconfig->store())
						{
							drupal_set_message(t("Config updated!"), 'notice_msg');
							drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . 
								"ideatorrent_admin/selected_theme_options/");
							break;
						}
					}
					//Show the admin page
					$models["themeconfig"] = QAPollThemeConfig::getInstance(QAPollConfig::getInstance()->getValue("selected_theme"));
					$view = new AdminView();
					$view->setOptions(array("selected_page" => "theme_options"));
				}
				else if($this->_param1 == "categories")
				{
					$basemodule_path .= "/" . $this->_param1;
					$this->_shiftPageArguments();

					if(is_numeric($this->_param1))
					{
						if($this->_param2 == "edit")
						{
							if(array_key_exists("_category_saved",$_POST))
							{
								$cat = new CategoryModel();
								$cat->setId($this->_param1);
								if($cat->loadFromPost() && $cat->store())
								{
									drupal_set_message(t("Category successfully modified!"), 'notice_msg');
									drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . 
										"ideatorrent_admin/categories/");
									break;
								}
							}
							$models['category'] = new CategoryModel();
							$models['category']->setId($this->_param1);
							$template = "editcategory";
						}
						else if($this->_param2 == "delete")
						{
							$cat = new CategoryModel();
							$cat->setId($this->_param1);
							$cat->delete();
							drupal_set_message(t("Category successfully deleted!"), 'notice_msg');
							drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . 
								"ideatorrent_admin/categories/");
							break;
						}
					}
					else if(array_key_exists('_category_saved',$_POST))
					{
						$cat = new CategoryModel();
						if($cat->loadFromPost() && $cat->store())
						{
							drupal_set_message(t("Category successfully added!"), 'notice_msg');
							drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . 
								"ideatorrent_admin/categories/");
							break;
						}
					}
					$view = new AdminView();
					$models["categories"] = new CategoryListModel($GLOBALS['poll']);
					$view->setOptions(array("selected_page" => "categories"));
				}
				else if($this->_param1 == "relations")
				{
					$view = new AdminView();
					$basemodule_path .= "/" . $this->_param1;
					$view->setOptions(array("selected_page" => "relations"));
				}
				else if($this->_param1 == "releases")
				{
					$basemodule_path .= "/" . $this->_param1;
					$this->_shiftPageArguments();

					if(is_numeric($this->_param1))
					{
						if($this->_param2 == "edit")
						{
							if(array_key_exists("_release_saved",$_POST))
							{
								$cat = new ReleaseModel();
								$cat->setId($this->_param1);
								if($cat->loadFromPost() && $cat->store())
								{
									drupal_set_message(t("Release successfully modified!"), 'notice_msg');
									drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . 
										"ideatorrent_admin/releases/");
									break;
								}
							}
							$models['release'] = new ReleaseModel();
							$models['release']->setId($this->_param1);
							$template = "editrelease";
						}
						else if($this->_param2 == "delete")
						{
							$cat = new ReleaseModel();
							$cat->setId($this->_param1);
							$cat->delete();
							drupal_set_message(t("Release successfully deleted!"), 'notice_msg');
							drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . 
								"ideatorrent_admin/releases/");
							break;
						}
					}
					else if(array_key_exists('_release_saved',$_POST))
					{
						$cat = new ReleaseModel();
						if($cat->loadFromPost() && $cat->store())
						{
							drupal_set_message(t("Release successfully added!"), 'notice_msg');
							drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . 
								"ideatorrent_admin/releases/");
							break;
						}
					}
					$view = new AdminView();
					$models["releases"] = new ReleaseListModel($GLOBALS['poll']);
					$view->setOptions(array("selected_page" => "releases"));
				}
				else if($this->_param1 == "")
				{
					//If it is a POST request, we update the entry point description
					if (array_key_exists('_config_saved',$_POST))
					{
						//Save the bug/idea
						if(QAPollConfig::getInstance()->loadFromPost() && QAPollConfig::getInstance()->store())
						{
							drupal_set_message(t("Config updated!"), 'notice_msg');
							drupal_set_header("Location: " . $GLOBALS['basemodule_url'] . "/" . 
								"ideatorrent_admin/");
							break;
						}
					}
					//Show the admin page
					$models["config"] = QAPollConfig::getInstance();
					$view = new AdminView();
					$view->setOptions(array("selected_page" => "global_options"));
				}
			break;

			//Show the static tooltips pages stored in the static_tooltips_pages/ folder.
			case "tooltip":
				if(is_numeric($this->_param1))
				{
					include_once "themes/" . QAPollConfig::getInstance()->getValue("selected_theme") . 
							"/static_tooltips_pages/" . $this->_param1 . ".php";
				}
				//Return null, forcing drupal common output not to show.
				return null;
			break;

			default:
				$content = drupal_not_found();
			break;
		}

		// 
		// Common to every page : get the list of all the global categories / models / models subcategories relevant
		// to the current path, for the big navigation header comboboxes.
		//
		$models["relationlist"] = new RelationListModel($poll);
		if($candidate_session_prefilter_path_relation != -1)
		{
			$models["relationsubcategorylist"] = new RelationSubcategoryListModel();
			$models["relationsubcategorylist"]->setFilterParameters(array("relation_id" => $candidate_session_prefilter_path_relation));
		}
		else
			$models["categorylist"] = new CategoryListModel($poll);


		if($view != null)
		{
			//Attach the models to the view
			foreach($models as $modelname => $modelinstance)
				$view->setModel($modelinstance, $modelname);

			//DEPRECATED: Affect the models to the view
			$view->setModel($model, $model_name);
			$view->setModel2($model2, $model2_name);

			//The view output the display
			$content = $view->display($template);

			// Save the current relation/subcat/category (eventually modified) so that it will be saved for the next page.
			// At the moment, only used for the /idea page. See complete doc above in the code.
			$_SESSION['basemodule_prefilter_path_relation'] = $candidate_session_prefilter_path_relation;
			$_SESSION['basemodule_prefilter_path_relationsubcat'] = $candidate_session_prefilter_path_relationsubcat;
			$_SESSION['basemodule_prefilter_path_category'] = $candidate_session_prefilter_path_category;

		}

		//Save the view used to output the page
		$this->_output_page_view =& $view;

		//
		// DEBUG : if $ideatorrent_sql_debug = 1, show the SQL query table
		//
		if($GLOBALS['ideatorrent_sql_debug'] == 1)
			$content .= it_showqueries();

		return $content;
	}


	/**
	 * Handle the AJAX calls returning data (in JSON).
	 */
	function handleAjaxDataCalls()
	{
		global $poll;

		$content = "";
		$filter = array();

		switch ($this->_page) {

			//Find and return possible duplicates of a given title.
			case "ajaxdata_similar_items":

				$model = new ChoiceListModel($poll, ((is_numeric($this->_param2))?$this->_param2:1), 10);

				//We remove from the keywords common words such as "and", "or", ..
				$filter = array("ordering" => "search-relevance", "keywords" => $this->_param1, "keywords_skip_common_words" => 1, 
					"state_awaiting_moderation" => 1);
		
				//Merge now the filter of the entrypoint and use the filter.
				//The filter of the entrypoint has priority over search filter.
				$filter = array_merge($filter, $this->_entry_point->getData()->filterArray);
				$model->setFilterParameters($filter);

				$view = new ChoiceListJSONView();
				$template = "json";
			break;

			//Find and return all the possible relation
			case "ajaxdata_all_relations":
				$model = new RelationListModel($poll);
				$view = new RelationListJSONView();
				$template = "json";
			break;

			//Find and return all subcategories tied to a given relation id.
			case "ajaxdata_relation_subcategories":
				if(is_numeric($this->_param1) == false || $this->_param1 == -1)
					return "";

				$model = new RelationSubcategoryListModel();
				$filter = array("relation_id" => $this->_param1);
				$model->setFilterParameters($filter);
				$view = new RelationSubcategoryListJSONView();
				$template = "json";
			break;
		}

		if($view != null)
		{
			//Affect the model to the view
			$view->setModel($model);

			//The view output the display
			$content = $view->display($template);
		}

		return $content;
	}

	/**
	 * Handle the AJAX calls
	 */
	function handleAjaxCalls()
	{
		global $site;

		switch ($this->_page) {

			//Ajax vote.
			case "ajaxvote":
				if($this->_param1 == null || is_numeric($this->_param1) == false ||
					$this->_param2 == null || is_numeric($this->_param2) == false)
					return false;

				$choicesolutionmodel = new ChoiceSolutionModel();
				$choicesolutionmodel->setId($this->_param1);
	
				//We check that we are voting for a choice in the current $HTTP_HOST
				//And that the user is registered.
				if(user_access($site->getData()->userrole))
					return $choicesolutionmodel->vote($this->_param2);
				else
					return false;
			break;

			//Approval ajax vote
			case "ajaxapprovalvote":
				if($this->_param1 == null || is_numeric($this->_param1) == false)
					return false;

				$choicemodel = new ChoiceModel();
				$choicemodel->setId($this->_param1);

				//Check permission
				if(!UserModel::currentUserHasPermission("approve_idea", "Choice", $choicemodel))
					return false;
		
				$change = $choicemodel->add_approval_vote($GLOBALS['user']->uid, 1);
				$choicemodel->check_idea_approval_count_and_update_status();

				return $change;
			break;

			case "ajaxmark_as_invalid":
				if(is_numeric($this->_param1) == false)
					return false;

				$choicemodel = new ChoiceModel();
				$choicemodel->setId($this->_param1);

				//Check permission
				if(!UserModel::currentUserHasPermission("status_mark_as_nonidea", "Choice", $choicemodel) &&
					!UserModel::currentUserHasPermission("edit_status", "Choice", $choicemodel))
					return false;

				$choicemodel->setStatus(ChoiceModel::$choice_status["not_an_idea"]);

				return true;
			break;

			case "ajaxmark_as_already_implemented":
				if(is_numeric($this->_param1) == false)
					return false;

				$choicemodel = new ChoiceModel();
				$choicemodel->setId($this->_param1);

				//Check permission
				if(!UserModel::currentUserHasPermission("status_mark_as_already_implemented", "Choice", $choicemodel) && 
					!UserModel::currentUserHasPermission("edit_status", "Choice", $choicemodel))
					return false;

				$choicemodel->setStatus(ChoiceModel::$choice_status["already_done"]);

				return true;
			break;

			//Delete an idea/bug
			case "ajaxdeleteitem":
				if (!user_access($site->getData()->adminrole) && !user_access($site->getData()->moderatorrole))
					return false;
				$choiceModel = new ChoiceModel();
				$choiceModel->setId($this->_param1);
				if($choiceModel->delete())
					return true;
				else
					return false;
			break;

			//Delete an idea/bug
			case "ajaxundeleteitem":
				if (!user_access($site->getData()->adminrole) && !user_access($site->getData()->moderatorrole))
					return false;
				$choiceModel = new ChoiceModel();
				$choiceModel->setId($this->_param1);
				if($choiceModel->undelete())
					return true;
				else
					return false;
			break;

			//Update the status of a idea/bug
			case "ajaxsavestatus":
				$choice = new ChoiceModel();
				$choice->setId($this->_param1);

				//Check permission
				if(!UserModel::currentUserHasPermission("edit_status", "Choice", $choice) ||
					($choice->getData()->status == ChoiceModel::$choice_status["awaiting_moderation"] && 
					!UserModel::currentUserHasPermission("edit_status_in_awaiting_moderation", "Choice", $choice)))
					return false;

				return $choice->setStatus($this->_param2);
			break;

			//Update the release of a idea/bug
			case "ajaxsaverelease":
				$choice = new ChoiceModel();
				$choice->setId($this->_param1);

				//Check permission
				if(!UserModel::currentUserHasPermission("edit_target_release", "Choice", $choice))
					return false;

				return $choice->setTargetRelease($this->_param2);
			break;

			//Toogle bookmark of this idea
			case  "ajaxtogglebookmark":
				//Check permission
				if(!user_access($site->getData()->userrole))
					return false;

				$model = new ChoiceModel();
				$model->setId($this->_param1);
				return $model->toggle_bookmark($GLOBALS['user']->uid);
			break;

			//Save the new relation of an idea
			case "ajaxsaverelation":
				$model = new ChoiceModel();
				$model->setId($this->_param1);

				//Check permission
				if(!UserModel::currentUserHasPermission("edit_relation", "Choice", $model))
					return false;

				return $model->setRelation($this->_param2);
			break;

			//Save the new tags of an idea
			case "ajaxsavetags":
				//Check permission
				if(!user_access($site->getData()->userrole))
					return false;

				$model = new ChoiceModel();
				$model->setId($this->_param1);
				return $model->setTags($this->_param2, 0, $GLOBALS['user']->uid);
			break;

			//Save the new tags of an idea
			case "ajaxsaveadmintags":
				//Check permission
				if(!user_access($site->getData()->adminrole) && !user_access($site->getData()->developerrole))
					return false;

				$model = new ChoiceModel();
				$model->setId($this->_param1);
				return $model->setTags($this->_param2, 1, $GLOBALS['user']->uid);
			break;

			case "ajaxdiscard_duplicate_report":
				if (!user_access($site->getData()->adminrole) && !user_access($site->getData()->moderatorrole))
					return false;

				//Discard a duplicate report
				$duprep = new DuplicateReportModel();
				$duprep->setId($this->_param1);
				return $duprep->reject();
			break;

			case "ajaxmark_as_duplicate_dup":
				if (!user_access($site->getData()->adminrole) && !user_access($site->getData()->moderatorrole))
					return false;

				//Accept a duplicate report
				$duprep = new DuplicateReportModel();
				$duprep->setId($this->_param1);
				return $duprep->accept();
			break;

			case "ajaxmark_as_duplicate_orig":
				if (!user_access($site->getData()->adminrole) && !user_access($site->getData()->moderatorrole))
					return false;

				//Accept a duplicate report, but in the opposite way. (see accept_opposite doc)
				$duprep = new DuplicateReportModel();
				$duprep->setId($this->_param1);
				return $duprep->accept_opposite();
			break;

			case "ajaxlink_solution_to":
				if (!user_access($site->getData()->adminrole) && !user_access($site->getData()->moderatorrole))
					return false;

				if(is_numeric($this->_param1) == false || is_numeric($this->_param2) == false)
					return false;

				//Add a link from this solution to this idea
				$solution = new ChoiceSolutionModel();
				$solution->setId($this->_param1);
				return $solution->linkToChoice($this->_param2);
			break;

			case "ajaxmark_solution_as_dup":
				if (!user_access($site->getData()->adminrole) && !user_access($site->getData()->moderatorrole))
					return false;

				if(is_numeric($this->_param1) == false || is_numeric($this->_param2) == false)
					return false;

				//Add a link from this solution to this idea
				$solution = new ChoiceSolutionModel();
				$solution->setId($this->_param1);
				return $solution->mark_as_duplicate_of($this->_param2);
			break;

			case "ajaxaccept_report":
			case "ajaxaccept_report2":
				if(!UserModel::currentUserHasPermission("process_report", ""))
				{
					$content = drupal_access_denied();
					break;
				}

				if(!is_numeric($this->_param1))
				{
					$content = drupal_not_found();
					break;
				}

				//Discard a duplicate report
				$report = new ReportModel();
				$report->setId($this->_param1);
				return ($this->_page == "ajaxaccept_report" && $report->accept() || 
					$this->_page == "ajaxaccept_report2" && $report->accept2());

			break;

			case "ajaxdiscard_report":
				if(!UserModel::currentUserHasPermission("process_report", ""))
				{
					$content = drupal_access_denied();
					break;
				}

				if(!is_numeric($this->_param1))
				{
					$content = drupal_not_found();
					break;
				}

				//Discard a duplicate report
				$report = new ReportModel();
				$report->setId($this->_param1);
				return $report->reject();
			break;


		}

		//default.
		return false;
	}

	/**
	 * This function checks if a given poll id is in the poll array list.
	 * If true, it returns the index of the poll in the array.
	 */
	function pollid_in_pollList($id, $list)
	{
		$result = -1;

		for($i = 0; $i < count($list); $i++)
		{
			if($list[$i]->getData()->id == $id)
				$result = $i;
		}

		return $result;
	}

	/**
	 * Return the left menu according to the current page URL
	 */
	function displayLeftMenu()
	{
		return "";
	}

	/**
	 * Return the right menu according to the current page URL
	 */
	function displayRightMenu()
	{
		$output = array();

		if($this->_entry_point == null)
			return $output;

		//Get the pretty custom widgets
		$view = new MenuBlockView();
		$output["premenu_html"] = $view->display();

		//Get the menus
		$menulistmodel = new MenuListModel($this->_entry_point);
		$output["menu"] = $menulistmodel->getData();


		return $output;
	}

	/**
	 * Return the title according to the current page URL.
	 */
	function getTitle()
	{
		if($this->_entry_point != null)
		{
			$title = "";

			//If a custom title was given by the display funtion, add it here.
			if($this->_page_title != "")
				$title .= $this->_page_title . " - ";

			$title .= $this->_entry_point->getData()->title;

			return $title;
		}
		else
			return null;
	}

	/**
	 * Returns the instance of the view used to output the page.
	 */
	function getOutputPageView()
	{
		return $this->_output_page_view;
	}

	/**
	 * Do some jobs that will be regularly executed.
	 */
	function do_cron_jobs()
	{
		//Remove the old entries in the log table.
		QAPollLogger::getInstance()->removeOldEntries();

		//Save the stats of the day
		QAPollStats::getInstance()->saveTodayGlobalStats();
	}

	/**
	 * Return the list of permissions of the QAPoll module.
	 */
	function get_permissions()
	{
		$perms = array();

		//Get the list of permissions
		$permissionlist = new PermissionListModel();
		$permlist = $permissionlist->getData();
		foreach($permlist as $perm)
			$perms[] = $perm->name;

		return $perms;
	}

	/**
	 * Shift the page arguments. Delete the first one.
	 */
	function _shiftPageArguments()
	{
		$this->_page = $this->_param1;
		$this->_param1 = $this->_param2;
		$this->_param2 = $this->_param3;
		$this->_param3 = $this->_param4;
		$this->_param4 = $this->_param5;
		$this->_param5 = $this->_param6;
	}
}


?>
