<?php
/*
Copyright (C) 2007 Stephane Graber <stgraber@ubuntu.com>

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

function qawebsite_block($op = "list", $delta = 0, $edit = array()) {
	drupal_add_css('modules/qawebsite/qawebsite.css');
	$blocks=array();
	$blocks[0]['info'] = "QA-Website module action block";
	$blocks[0]['function'] = "qawebsite_block_action";
	$blocks[1]['info'] = "QA-Website top navigation bar";
	$blocks[1]['function'] = "qawebsite_block_navigation";
	$blocks[2]['info'] = "QA-Website module action block 2";
	$blocks[2]['function'] = "qawebsite_block_action2";
	switch($op) {
		case "list":
			return $blocks;
		break;
		case "configure":
			return;
		break;
		case "save":
			return;
		break;
		case "view":
		default:
			$data=array();
			$data['content']=call_user_func($blocks[$delta]['function']);
			return $data;
	}
}

function qawebsite_block_action() {
	$site=QAWebsiteSite::getInstance();
	$module=arg(0);
	if ($site == null || ($site->title == "Ubuntu QA" && arg(0) == "qatracker") || function_exists($module."_actionblock") == false) //FIXME: We need a proper implementation of default module
		return "";

	$content="";
	foreach (call_user_func($module."_actionblock") as $title => $values) {
		$content .= "<b class=\"qawebsite_menu_round\">
			<b class=\"qawebsite_menu_round1\"></b>
			<b class=\"qawebsite_menu_round2\"></b>
			<b class=\"qawebsite_menu_round3\"></b>
			<b class=\"qawebsite_menu_round4\"><b></b></b>
			<b class=\"qawebsite_menu_round5\"><b></b></b></b>";
		$content.="<table class=\"qawebsite_menu\">
					<tr class=\"qawebsite_menuheader\"><td><b>".$title."</b></td></tr>";
		if (is_array($values)) {
			foreach ($values as $item => $url)
				if($item != "")
					$content.="<tr><td class=\"qawebsite_menu_entry\"><a href=\"".$url."\">".t($item)."</a></td></tr>";
		}
		else
			$content.="<tr><td style=\"padding:2px 5px 2px 5px;\">".$values."</td></tr>";
		$content.="</table>";
	}
	return $content;
}

function qawebsite_block_action2() {
	$site=QAWebsiteSite::getInstance();
	$module=arg(0);

	if ($site == null || function_exists($module."_actionblock2") == false)
		return "";

	$content="";
	$data = call_user_func($module."_actionblock2");

	//Include the pre-menu HTML
	$content = $data["premenu_html"];

	//Include the menu
	foreach ($data["menu"] as $title => $values) {
		$content .= "<b class=\"qawebsite_menu_round\">
			<b class=\"qawebsite_menu_round1\"></b>
			<b class=\"qawebsite_menu_round2\"></b>
			<b class=\"qawebsite_menu_round3\"></b>
			<b class=\"qawebsite_menu_round4\"><b></b></b>
			<b class=\"qawebsite_menu_round5\"><b></b></b></b>";
		$content.="<table class=\"qawebsite_menu\">
					<tr class=\"qawebsite_menuheader\"><td><b>".$title."</b></td></tr>";
		if (is_array($values)) {
			foreach ($values as $item => $url)
				if($item != "")
					$content.="<tr><td class=\"qawebsite_menu_entry\"><a href=\"".$url."\">".t($item)."</a></td></tr>";
		}
		else
			$content.="<tr><td style=\"padding:2px 5px 2px 5px;\">".$values."</td></tr>";
		$content.="</table>";
	}

	//Include the post-menu HTML
	$content .= $data["postmenu_html"];

	return $content;
}

function qawebsite_block_navigation() {
	global $user;
	$site=QAWebsiteSite::getInstance();
	if ($site != null)
		$title=$site->title;
	else
		$title="Ubuntu QA";

	//Print the site title
	$content="
		<table width=\"100%\">
			<tr>
			<td style=\"width:1px;\"><span style=\"white-space:nowrap\">";
			$content.="<b>" . $title . ":</b>";
	$content.="
			</span></td>
				<td align=\"left\">
					<table class=\"qawebsite_topbar\">
						<tr>";
	if ($site != null && $site->getModules() != null ) {
		foreach ($site->getModules("title ASC") as $module) {
			if ($module->status == 1) {
				$content.="<td>";
				$absolutepath = (substr($module->path, 0, 4) == "http");
				if((($absolutepath == true && strpos(getCurrentURL(), $module->path) === false) ||
					($absolutepath == false && strpos($_SERVER["REQUEST_URI"], $module->path) === false)) &&
					(arg(0) != $module->path))
					$content .= "<a href=\"" . (($absolutepath == true)?"":"/") . $module->path."\">";
				else
					$content .= "<b>";
 				if ($module->title)
 					$content.=$module->title;
 				else 
 					$content.=ucfirst(str_replace("qa","",$module->path));
				if((($absolutepath == true && strpos(getCurrentURL(), $module->path) === false) ||
					($absolutepath == false && strpos($_SERVER["REQUEST_URI"], $module->path) === false)) &&
					(arg(0) != $module->path))
	 				$content .= "</a>";
				else
					$content .= "</b>";
				$content.="</td>";
			}
		}
	}
	$content.="			</tr>
					</table>
				</td>
				<td align=\"right\">
					<table class=\"qawebsite_topbar\">
						<tr>";
	
							//Since we are hiding arg(O) in some case, we have to use a dirty hack instead of drupal_get_destination
							$destination = $_GET['q'];
							if($destination == arg(0))
								$destination = "";
							else
								$destination = substr($_GET['q'], strpos($_GET['q'], "/") + 1);

							if ($user->uid != 0) {
								$content.="<td><b>Welcome, " . $user->name . "!</b></td>";
								if (user_access("Administrator"))
									$content.="<td><a href=\"/admin\">Admin</a></td>";
								$content.="<td><a href=\"/qawebsite/profile\">My profile</a></td>";
								if(arg(0) == "ideatorrent")
									$content.="<td><a href=\"/logout?destination=" . $destination ."\">Log out</a></td>";
								else 
									$content.="<td><a href=\"/logout\">Log out</a></td>";
							}
							elseif (variable_get("user_register",1))
							{
								if(arg(0) == "ideatorrent")
									$content.="<td><a href=\"/user?destination=" . $destination ."\">Log in</a></td>";
								else
									$content.="<td><a href=\"/user\">Log in</a></td>";
							}
							else
								$content.="<td>&nbsp;</td>";
							$content.="
						</tr>
					</table>
				</td>
			</tr>
		</table>";
	return $content;
}

?>
