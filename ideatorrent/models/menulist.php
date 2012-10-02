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




class MenuListModel extends ModelList
{

	/**
	 * EntryPointModel related to this list.
	 */
	var $_entryPointModel = null;

	/**
	 * Default constructor.
	 * Need a entryPointModel to know for which entrypoint we want the menus
	 */
	function MenuListModel($entryPointModel)
	{
		$this->_entryPointModel = $entryPointModel;
	}

	/**
	 * Get the data.
	 */
	function _loadData()
	{
		global $user;
		global $site;
		global $basemodule_url;

		//Get the menu list
		$query = "SELECT qapoll_entry_point_menu.menu_title, qapoll_entry_point_menu_entry.name, qapoll_entry_point_menu_entry.url, " .
			"qapoll_entry_point_menu_entry.permission_needed, " . 
			"qapoll_entry_point_menu_entry.is_suffix, qapoll_entry_point_menu.user_function " .
			"FROM qapoll_entry_point_menu " . 
			"LEFT JOIN qapoll_entry_point_menu_entry ON qapoll_entry_point_menu_entry.menu_id = qapoll_entry_point_menu.id " .
			"WHERE (qapoll_entry_point_menu_entry.id IS NULL OR (" .
			"qapoll_entry_point_menu_entry.status = 0 AND qapoll_entry_point_menu.status = 0 AND " .
			"(qapoll_entry_point_menu_entry.user_id = '-1' OR qapoll_entry_point_menu_entry.user_id = '" . $user->uid . "'))) " .
			"AND '" . db_escape_string($_SERVER["REQUEST_URI"]) . "' SIMILAR TO qapoll_entry_point_menu.url_pattern " .
			"AND qapoll_entry_point_menu.entry_point_id = '" . $this->_entryPointModel->getData()->id . "' " .
			"ORDER BY qapoll_entry_point_menu.ordering, qapoll_entry_point_menu_entry.ordering, qapoll_entry_point_menu_entry.name";

		$menu_entries = it_query($query);

		//Store the result in a array
		$menu_entries_list = array();
		while ($menu_entry = db_fetch_object($menu_entries))
		{
			//Check the permissions
			if($menu_entry->permission_needed != null)
			{
				$array_perm = split(":", $menu_entry->permission_needed);
				//HACK
				//If it's the EntryPoint subsystem, gives the current entry point model to the permission function.
				if(($array_perm[0] == "EntryPoint" &&
					UserModel::currentUserHasPermission($array_perm[1], $array_perm[0], $GLOBALS['entrypoint']) == false) ||
					($array_perm[0] != "EntryPoint" && UserModel::currentUserHasPermission($array_perm[1], $array_perm[0]) == false))
					continue;
			}

			if(is_array($menu_entries_list[$menu_entry->menu_title]) == false)
			{
				//We create the menu, and if there is an user_function, we call it
				$menu_entries_list[$menu_entry->menu_title] = array();
				if($menu_entry->user_function != "")
				{
					$array_menu = call_user_func(split("::", $menu_entry->user_function));

					if(is_array($array_menu))
					{
						foreach($array_menu as $name => $menu)
							$menu_entries_list[$menu_entry->menu_title][$name] = 
								$basemodule_url . $GLOBALS['basemodule_prefilter_path'] . $menu;
					}
				}
			}
			if($menu_entry->is_suffix)
			{
				//Relative URL
				if($menu_entry->url != null && substr($menu_entry->url, 0, 1) != "/")
				{
					$url = getCurrentURL();
					if(substr($url, -1) != "/")
						$url = substr($url, 0, strrpos($url, "/") + 1);
				}
				else
				//Absolute URL
					$url = $basemodule_url . $GLOBALS['basemodule_prefilter_path'];
			}

			//We replace some keywords.
			$menu_entry->url = str_replace("%username%", $GLOBALS['user']->name, $menu_entry->url);

			//We store this menu entry
			$menu_entries_list[$menu_entry->menu_title][$menu_entry->name] = $url . $menu_entry->url;

		}

		return $menu_entries_list;
	}

	/**
	 * This function is called by the menus to dynamically get the list of categories link to show.
	 */
	static function getCategoriesMenu()
	{
		global $poll, $entrypoint;
		$menu_cats = array();

		//First check if the current entrypoint is filtering by a relation, and if yes,
		//check if there are categories tied to this relation defined.
		if($entrypoint->getData()->filterArray['relation'] != null)
		{
			$list_subcats = new RelationSubcategoryListModel();
			$list_subcats->setFilterParameters(array("relation_id" => $entrypoint->getData()->filterArray['relation']));
			$list = $list_subcats->getData();
			if(count($list) > 0)
			{
				for($i = 0; $i < count($list); $i++)
					$menu_cats[$list[$i]->name] = "/category/" . $list[$i]->id;
				return $menu_cats;
			}
		}

		//Otherwise, use the global categories
		$cats = $poll->getCategoryList();
		for($i = 0; $i < count($cats); $i++)
			$menu_cats[$cats[$i]->name] = "/category/" . $cats[$i]->id;

		return $menu_cats;
	}

}

?>
