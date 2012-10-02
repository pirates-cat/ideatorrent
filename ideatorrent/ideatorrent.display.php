<?php
/*
Copyright (C) 2007 Stephane Graber <stgraber@ubuntu.com>,
		2008 Nicolas Deschildre <ndeschildre@gmail.com>

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

require_once "controller.php";

/**
 * This function should return the module contents.
 */
function ideatorrent_main($page = "", $param1 = null, $param2 = null, $param3 = null, $param4= null, $param5 = null, $param6 = null) 
{
	//Get the controller instance
	$controller = QAPollController::getInstance();

	//Init the controller
	$controller->init($page, $param1, $param2, $param3, $param4, $param5, $param6);

	//Get the module page
	$content = $controller->display();

	return $content;
}

/**
 * This function is called by the right menu block. It returns the menu to be displayed.
 */

function ideatorrent_actionblock2()  
{
	//Get the controller instance
	$controller = QAPollController::getInstance();

	//Get the left menu
	$content = $controller->displayRightMenu();

	return $content;
}

/**
 * This function is called by the ubuntu theme. It returns the title of the current page.
 */
function ideatorrent_gettitle()
{
	//Get the controller instance
	$controller = QAPollController::getInstance();

	//Get the title
	$title = $controller->getTitle();

	return $title;
}

/**
 * This function is called by drupal cron.
 */
function ideatorrent_cron()
{
	//Get the controller instance
	$controller = QAPollController::getInstance();

	//Get the title
	$controller->do_cron_jobs();

	return true;
}

/**
 * This function is called by the ubuntu theme. It returns some HTML code to be put under the banner.
 * Here, the qapoll module return a stat string to be put on the banner (via a CSS absolute position).
 */
function ideatorrent_get_post_banner_html()
{
	$qapoll_stats = QAPollStats::getInstance()->getTodayStats();

	if($GLOBALS['gbl_relation'] != null)
		return 
			((file_exists('/modules/ideatorrent/themes/ubuntu_brainstorm/images/' . $GLOBALS['gbl_relation']->getData()->name . ".png"))?
			"<div style=\"position: absolute;top: 97px; left:32px; z-index: 10\">" . 
			"<img src=\"http://brainstorm.ubuntu.com/modules/ideatorrent/themes/ubuntu_brainstorm/images/am2.png\"></div>" . 
			"<div style=\"position: absolute;top: 87px; left:23px; z-index: 9\">" . 
			"<img src=\"http://brainstorm.ubuntu.com/modules/ideatorrent/themes/ubuntu_brainstorm/images/projecticon-bg.png\"></div>":"") . 
			"<div style=\"position: absolute;top: 95px; left:85px; z-index: 10; font-size:18px\">" . 
			$GLOBALS['gbl_relation']->getData()->name . "</div>";
	else if($qapoll_stats != null)
		return "<div style=\"position: absolute;top: 100px; left:73px; z-index: 10; font-size:10px\">The Ubuntu community has contributed " .
		($qapoll_stats->nbideasvalid) . " ideas, " . $qapoll_stats->nbcomments . " comments, " .
		$qapoll_stats->nbvotes . " votes</div>";
	else
		return "";
}

?>
