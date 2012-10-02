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



class AdminView extends View
{



	/**
	 * Prepare and display the page.
	 */
	function display($template = "default")
	{
		//Call parent function: add some common stuff
		View::display($template);

		//Add CSS
//		drupal_add_css('modules/qawebsite/qawebsite.css');
//		drupal_add_css('modules/qapoll/css/qapoll.css');

		//Add JS
//		$this->process_and_add_js('modules/qapoll/js/qapoll_ajax.js.php');	

		//Show the Choice.
		$content .= $this->loadNonThemedTemplate("admin/", $template);

		return $content;
	}

}

?>
