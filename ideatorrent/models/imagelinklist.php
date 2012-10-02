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




class ImageLinkListModel extends ModelList
{

	/**
	 * EntryPointModel related to this list.
	 */
	var $_entryPointModel = null;

	/**
	 * Optional. Choice id: can be usefull to build the image links.
	 */
	var $_choiceId = 0;

	/**
	 * Default constructor.
	 * Need a entryPointModel to know for which entrypoint we want the image links
	 */
	function ImageLinkListModel($entryPointModel, $choiceId = 0)
	{
		$this->_entryPointModel = $entryPointModel;
		$this->_choiceId = $choiceId;
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
		$query = "SELECT qapoll_image_link.id, qapoll_image_link.img_url, qapoll_image_link.img_height, qapoll_image_link.img_width, " .
			"qapoll_image_link.title, qapoll_image_link.need_update " .
			"FROM qapoll_image_link " . 
			"WHERE qapoll_image_link.status = 0 AND " .
			"qapoll_image_link.entry_point_id = '" . $this->_entryPointModel->getData()->id . "' ";

		$image_links = it_query($query);

		//Store the result in a array
		$image_link_list = array();
		while ($image_link = db_fetch_object($image_links))
		{
			$image_link_list[] = $image_link;

		}

		return $image_link_list;
	}

}

?>
