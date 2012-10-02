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



class ImagelinkView extends View
{
	/**
	 * The id of the image link view to show.
	 */
	var $_id = 0;


	/**
	 * Default constructor.
	 */
	function ImagelinkView($id)
	{
		if($id != null && is_numeric($id) == true && $id > 0)
			$this->_id = $id;
	}

	/**
	 * Prepare and display the page.
	 */
	function display($template = "default")
	{
		global $site;

		if($this->_id == 0 || $this->_data->id == 0)
			return null;

		//Load the image link view infos.
		$imglink = new ImagelinkModel();
		$imglink->setId($this->_id);
		$this->_data->imglink = $imglink->getData();

		if($this->_data->imglink == null)
			return null;

		//Indicate the content type.
		drupal_set_header("Content-type: image/png");

		//If the image does not need an update, just read from the cache
		$img_path = dirname(__FILE__) . "/cache/img_" . $this->_data->imglink->id . "_" . $this->_data->id . ".png";
		$img_path_dated = dirname(__FILE__) . "/cache/img_" . $this->_data->imglink->id . "_" . $this->_data->id . "_" . date("Y-m-d") . ".png";
		if($this->_data->imglink->need_update == "f" && file_exists($img_path))
		{
			readfile($img_path);
		}
		else if($this->_data->imglink->need_update == "t" && file_exists($img_path_dated))
		{
			readfile($img_path_dated);
		}
		else
		{
			//Custom processing by the selected view
			$data = $this->_data;
			require drupal_get_path('module', 'ideatorrent') . "/themes/" . QAPollConfig::getInstance()->getValue("selected_theme") .
				"/imagelink/" . $this->_data->imglink->img_url;

			//Save the image for cache purpose
			if($this->_data->imglink->need_update == "f")
			{
				$file = fopen($img_path, 'w') or die("can't open file");
				fclose($file);
				ImagePNG($image, $img_path);
			}
			else if($this->_data->imglink->need_update == "t")
			{
				foreach (glob(dirname(__FILE__) . "/cache/img_" . $this->_data->imglink->id . "_" . $this->_data->id . "_*.png") as $filename) 
				   unlink($filename);
				$file = fopen($img_path_dated, 'w') or die("can't open file");
				fclose($file);
				ImagePNG($image, $img_path_dated);
			}

			//Destroy the img
			ImageDestroy($image);
				
		}

		return null;
	}

}

?>
