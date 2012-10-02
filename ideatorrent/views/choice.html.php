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



class ChoiceView extends View
{



	/**
	 * Prepare and display the page.
	 */
	function display($template = "default")
	{
		//Call parent function: add some common stuff
		View::display($template);

		//Add RSS feed
		$basemodulepath = $GLOBALS['basemodule_path'];
		drupal_add_feed($GLOBALS['basemodule_url'] . "/" . $basemodulepath . "/rss2",
			"RSS feed for this choice");
		

		//
		// TODO: Move that on the controller.
		//

		//If we are in a entrypoint that filter by relation id
		//Let's get and store the relation name
		if($GLOBALS['entrypoint']->getData()->filterArray['relation'] != null)
		{
			$relation = new RelationModel();
			$relation->setId($GLOBALS['entrypoint']->getData()->filterArray['relation']);
			$this->_relation_name = $relation->getData()->name;
		}

		//Let's also get the subcategory list of the current relation
		if($GLOBALS['entrypoint']->getData()->filterArray['relation'] != null || 
			($this->_data != null && $this->_data->relation_id != -1) ||
			($_POST['_choice_submitted'] != null && $_POST['choice_relation'] != -2) ||
			($this->_data == null && $_POST['_choice_submitted'] == null && $GLOBALS['gbl_relation'] != null))
		{
			$subcat_list = new RelationSubcategoryListModel();
			$relation_id = $GLOBALS['entrypoint']->getData()->filterArray['relation'];
			if($relation_id == null)
				$relation_id = $_POST['choice_relation'];
			if($relation_id == null)
				$relation_id = $this->_data->relation_id;
			if($relation_id == null && $GLOBALS['gbl_relation'] != null)
				$relation_id = $GLOBALS['gbl_relation']->getId();

			if($relation_id != -1)
			{
				$subcat_list->setFilterParameters(array("relation_id" => $relation_id));
				$this->_relation_subcategory_list = $subcat_list->getData();
			}
		}

		//Show the Choice.
		$content .= $this->loadTemplate("choice/", $template);

		return $content;
	}




}

?>
