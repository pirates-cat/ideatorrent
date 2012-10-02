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



class ChoiceListView extends View
{



	/**
	 * Prepare and display the page.
	 */
	function display($template = "default")
	{
		global $site;
		global $user;

		//Call parent function: add some common stuff
		View::display($template);


		//Add RSS feed
		// => Add relevant RSS feed only!
		if(strstr($GLOBALS['basemodule_path'], 'ideas_in_preparation'))
		{
			if(strstr($GLOBALS['basemodule_path'], 'invalid') == false && 
				strstr($GLOBALS['basemodule_path'], 'already_implemented') == false && 
				strstr($GLOBALS['basemodule_path'], 'duplicates') == false)
			drupal_add_feed($GLOBALS['basemodule_url'] . "/ideas_in_preparation/latest_submissions/rss2/",
				t('Latest submitted ideas'));
		}
		else if(strstr($GLOBALS['basemodule_path'], 'ideas_in_development'))
		{
			$dirs = explode("/", $GLOBALS['basemodule_path']);
			$rssdir = "/" . $dirs[0] . "/" . $dirs[1] . (($dirs[1] != "")?"/":"") . "rss2";
			drupal_add_feed($GLOBALS['basemodule_url'] . $rssdir,
				t('Latest in development ideas'));
		}
		else if(strstr($GLOBALS['basemodule_path'], 'implemented_ideas'))
		{
			$dirs = explode("/", $GLOBALS['basemodule_path']);
			$rssdir = "/" . $dirs[0] . "/" . $dirs[1] . (($dirs[1] != "")?"/":"") . "rss2";
			drupal_add_feed($GLOBALS['basemodule_url'] . $rssdir,
				t('Latest implemented ideas'));
		}
		else if(strstr($GLOBALS['basemodule_path'], 'contributor/'))
		{
			//None for now.
		}
		else
			drupal_add_feed($GLOBALS['basemodule_url'] . "/latest_ideas/rss2/",
				t('Latest popular ideas'));



		//Execute default template
		$content .= $this->loadTemplate("choicelist/", $template);

		return $content;
	}



	/**
	 * This function correct the $_GET array given by the advanced search page.
	 * The problem is that unchecked checkboxes are not put in the _GET array, thus
	 * the ChoiceListModel will not see our choices and use the default values.
	 * This function will put these informations in the array.
	 */
	function completeAdvancedSearchFilterArray($filters)
	{

		//Set the value of unchecked checkboxes
		if($filters['state_new'] == null)
			$filters['state_new'] = 0;
		if($filters['state_needinfos'] == null)
			$filters['state_needinfos'] = 0;
		if($filters['state_blueprint_approved'] == null)
			$filters['state_blueprint_approved'] = 0;
		if($filters['state_workinprogress'] == null)
			$filters['state_workinprogress'] = 0;
		if($filters['state_done'] == null)
			$filters['state_done'] = 0;
		if($filters['state_already_done'] == null)
			$filters['state_already_done'] = 0;
		if($filters['state_unapplicable'] == null)
			$filters['state_unapplicable'] = 0;
		if($filters['state_deleted'] == null)
			$filters['state_deleted'] = 0;
		if($filters['state_not_an_idea'] == null)
			$filters['state_not_an_idea'] = 0;
		if($filters['state_awaiting_moderation'] == null)
			$filters['state_awaiting_moderation'] = 0;


		if($filters['nothing_attached'] == null)
			$filters['nothing_attached'] = 0;
		if($filters['bug_attached'] == null)
			$filters['bug_attached'] = 0;
		if($filters['spec_attached'] == null)
			$filters['spec_attached'] = 0;
		if($filters['thread_attached'] == null)
			$filters['thread_attached'] = 0;

		if($filters['type_bug'] == null)
			$filters['type_bug'] = 0;
		if($filters['type_idea'] == null)
			$filters['type_idea'] = 0;
			

		return $filters;
	}


}

?>
