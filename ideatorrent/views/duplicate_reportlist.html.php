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



class DuplicateReportListView extends View
{



	/**
	 * Prepare and display the page.
	 */
	function display($template = "default")
	{
		global $site;

		//Call parent function: add some common stuff
		View::display($template);

		//Include CSS files
//		drupal_add_css('modules/qawebsite/qawebsite.css');
//		drupal_add_css('modules/qapoll/css/qapoll.css');

		//Include JS files
//		$this->process_and_add_js('modules/qapoll/js/qapoll_ajax.js.php');
//		if(user_access($site->getData()->adminrole))
//			drupal_add_js('modules/qapoll/js/qapoll_admin.js');

		//Create a pagination
//		$get_params = generate_GET_param_list($_GET);
//		$urlprefix = $GLOBALS['basemodule_url'] . "/process_duplicate_reports/";
//		if($this->_data != null)
//			$this->_pagination = new Pagination($this->_data->rowCount, $this->_data->page, $this->_data->numberRowsPerPage,
//				$urlprefix, (($get_params != "")?"?" . $get_params:""));

		//Execute default template
		$content .= $this->loadTemplate("duplicate_reportlist/", $template);

		return $content;
	}

}

?>
