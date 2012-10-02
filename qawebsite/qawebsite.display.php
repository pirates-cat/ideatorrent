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

function qawebsite_sitelist() {
	$query=db_query("SELECT subdomain,title FROM qawebsite_site");
	$content="<br />
				<b>Welcome to the Ubuntu QA team website.</b><br />
				This website is split into several sub-domains, please choose the one you want in the list below :<br /><br />";
	$content.="<table class=\"listing\" style=\"width:auto;\">";
	$content.="<tr class=\"trheader\"><td><b>URL</b></td><td><b>Title</b></td></tr>";
	while ($item=db_fetch_object($query)) {
		$alias=explode("|",$item->subdomain);
		$content.="<tr><td align=\"left\" valign=\"bottom\"><a href=\"http://".$alias[0]."\"><img src=\"$base_url/modules/qawebsite/images/site.png\" style=\"margin-bottom:-3px;\" alt=\"sub-domain\" /></a> <a href=\"http://".$alias[0]."\">".$alias[0]."</a></td><td align=\"left\" valign=\"bottom\">".$item->title."</td></tr>";
	}
	$content.="</table><br />";
	$content.="Authentication is shared across the different sub-domains, so you do not need to create a new account on each of those<br /><br />";
	$content.="<b>Useful QA resources :</b><br />
				<a href=\"https://launchpad.net\">Launchpad</a><br />
				<a href=\"https://wiki.ubuntu.com/Testing\">Ubuntu Testing wiki pages</a><br />
				<a href=\"http://people.ubuntu.com/~brian/graphs/\">Ubuntu bug stats</a><br />
				<a href=\"http://people.ubuntu.com/~brian/testing_graphs/\">Package stats (>100 bugs)</a><br />
				<a href=\"http://people.ubuntu.com/~brian/reports/from-teams/\">Team reported bugs</a><br />
				<a href=\"http://people.ubuntu.com/~brian/reports/gt2dups\">Bugs with more than 2 duplicates</a><br />
				<a href=\"http://people.ubuntu.com/~brian/reports/gt5subscribers\">Bugs with more than 5 subscribers</a><br />
				<a href=\"http://people.ubuntu.com/~brian/reports/gt5comments\">Bugs with more than 5 comments</a><br />
				<a href=\"http://qa.ubuntuwire.com\">UbuntuWire QA pages</a><br />";
	return $content;
}

function qawebsite_balloon($text,$item,$side) {
	# $text is the text used as link (can contain html)
	# $item is the element that actually triggers the balloon
	# $side is either right or left

	if ($side != "left" && $side != "right")
		return null;

	if ($bug->title == "") {
		$content.="
			<div class=\"balloon$side\"><div>
					$text
				</div>$item</div>";
	}
	return $content;
}

function qawebsite_bugballoon($bugnumber,$text,$side) {
	# $bugnumber is an integer value starting at 1
	# $text is the text used as link (can contain html)
	# $side is either right or left

	if ($side != "left" && $side != "right")
		return null;

	$bug=qawebsite_getbug($bugnumber);
	if ($bug === 1)
		return null;

	//If too long, split the title in several lines.
	$title = htmlentities($bug->title,ENT_QUOTES,"UTF-8");
	$splitted_title = "";
	while(strlen($title) > 45)
	{
		$spacepos = strpos($title, " ", 45);
		if($spacepos !== false)
		{
			$splitted_title .= substr($title, 0, $spacepos) . "<br />";
			$title = substr($title, $spacepos + 1);
		}
		else
			break;
	}
	$splitted_title .= $title;

	if ($bug->title == "") {
		$content.="
			<div class=\"balloon$side\"><div>
					<b>No information about this bug (#".$bugnumber.")</b><br />
					Information is update every 5 minutes.<br />
					We can only display valid public bug reports.
				</div><a class=\"blacklink\" rel=\"external\" href=\"http://launchpad.net/bugs/".$bugnumber."\">$text</a></div>";
	}
	else {
		$content.="
			<div class=\"balloon$side\"><div>";
				if ($bug->bugnumber==$bug->originalbug)
					$content.="<span><b>".$splitted_title." (#".$bug->bugnumber.")</b></span><br /><br />";
				else
					$content.="<b>".$splitted_title." (#".$bug->bugnumber.")</b><br />(master bug of duplicate <a href=\"http://launchpad.net/bugs/".$bug->originalbug."\">#".$bug->originalbug."</a>)<br /><br />";
				$content.="
					<table>
						<tr><td><b>In</b> : </td><td style=\"padding-left:2em;color:#291e1c;\">".htmlentities($bug->product,ENT_QUOTES,"UTF-8")."</td></tr>
						<tr><td><b>Status</b> : </td><td style=\"padding-left:2em;color:#291e1c;\">".htmlentities($bug->status,ENT_QUOTES,"UTF-8")."</td></tr>
						<tr><td><b>Importance</b> : </td><td style=\"padding-left:2em;color:#291e1c;\">".htmlentities($bug->importance,ENT_QUOTES,"UTF-8")."</td></tr>
						<tr><td><b>Assignee</b> : </td><td style=\"padding-left:2em;color:#291e1c;\">".htmlentities($bug->assignee,ENT_QUOTES,"UTF-8")."</td></tr>
					</table>
					<i>".$bug->commentscount." comments, ".$bug->subscriberscount." subscribers and ".$bug->duplicatescount." duplicates</i>";
					if ($bug->mentoring == 1)
						$content.="<br /><b>Mentorship is available if you want to fix this bug.</b>";
					$content.="
				</div><a class=\"blacklink\" rel=\"external\" href=\"http://launchpad.net/bugs/".$bug->bugnumber."\">$text</a></div>";
	}
	return $content;
}

function qawebsite_blueprintballoon($blueprinturl,$text,$side) {
	# $blueprinturl is a valid blueprint URL
	# $text is the text used as link (can contain html)
	# $side is either right or left

	if ($side != "left" && $side != "right")
		return null;

	$blueprint=qawebsite_getblueprint($blueprinturl);
	if ($blueprint === 1)
		return null;

	//If too long, split the title in several lines.
	$title = htmlentities($blueprint->title,ENT_QUOTES,"UTF-8");
	$splitted_title = "";
	while(strlen($title) > 45)
	{
		$spacepos = strpos($title, " ", 45);
		if($spacepos !== false)
		{
			$splitted_title .= substr($title, 0, $spacepos) . "<br />";
			$title = substr($title, $spacepos + 1);
		}
		else
			break;
	}
	$splitted_title .= $title;

	if ($blueprint->title == "") {
		$content.="
			<div class=\"balloon$side\"><div>
					<b>No information about this blueprint</b><br />
					Information is updated every 5 minutes.<br />
					Please wait till the next update.
				</div><a class=\"blacklink\" rel=\"external\" href=\"".$blueprinturl."\">$text</a></div>";
	}
	else {
		$content.="
			<div class=\"balloon$side\"><div>";
				$content.="<span><b>".$splitted_title."</b></span><br /><br />";
				$content.="
					<table>
						<tr><td><b>In</b> : </td><td style=\"padding-left:2em;color:#291e1c;\">".htmlentities($blueprint->product,ENT_QUOTES,"UTF-8")."</td></tr>
						<tr><td><b>Priority</b> : </td><td style=\"padding-left:2em;color:#291e1c;\">".htmlentities($blueprint->priority,ENT_QUOTES,"UTF-8")."</td></tr>
						<tr><td><b>Definition</b> : </td><td style=\"padding-left:2em;color:#291e1c;\">".htmlentities($blueprint->definition,ENT_QUOTES,"UTF-8")."</td></tr>
						<tr><td><b>Implementation</b> : </td><td style=\"padding-left:2em;color:#291e1c;\">".htmlentities($blueprint->implementation,ENT_QUOTES,"UTF-8")."</td></tr>
						<tr><td><b>Assignee</b> : </td><td style=\"padding-left:2em;color:#291e1c;\">".htmlentities($blueprint->assignee,ENT_QUOTES,"UTF-8")."</td></tr>
					</table>";
					if ($blueprint->mentoring == 1)
						$content.="<br /><b>Mentorship is available if you want to fix this bug.</b>";
					$content.="
				</div><a class=\"blacklink\" rel=\"external\" href=\"".$blueprinturl."\">$text</a></div>";
	}
	return $content;
}

function qawebsite_profile() {
	global $user, $base_url;
	drupal_add_css('modules/qawebsite/qawebsite.css');
	drupal_add_js('modules/qawebsite/qawebsite.js');
	$site=QAWebsiteSite::getInstance();
	if ($site == null)
		$siteid=0;
	else
		$siteid=$site->id;

	if ($_POST['qat_profileupdate'] && is_array($_POST['qat_profile'])) {
		foreach ($_POST['qat_profile'] as $name => $value) {
			if (db_result(db_query("SELECT count(id) FROM qawebsite_user_setting_info WHERE settingid='".db_escape_string($name)."' AND userid='".$user->uid."'")) == "0")
				db_query("INSERT INTO qawebsite_user_setting_info (settingid,userid,value) VALUES ('".db_escape_string($name)."','".$user->uid."','".db_escape_string($value)."')");
			else
				db_query("UPDATE qawebsite_user_setting_info SET value='".db_escape_string($value)."' WHERE userid='".$user->uid."' AND settingid='".db_escape_string($name)."'");
		}
		drupal_set_message("Your profile has been updated","notice_msg");
		drupal_set_header("Location: $base_url/qawebsite/profile");
	}
	else {
		$content="<br />";
		$query=db_query("SELECT
				qawebsite_user_setting.title,
				qawebsite_user_setting.siteid,
				qawebsite_user_setting.type,
				qawebsite_user_setting.description,
				qawebsite_user_setting.id as settingid
				FROM qawebsite_user_setting
				WHERE qawebsite_user_setting.siteid='".$siteid."' OR qawebsite_user_setting.siteid='0'
				ORDER BY qawebsite_user_setting.siteid DESC, qawebsite_user_setting.title ASC;");
		$content.="<form action=\"\" method=\"post\"><table class=\"listing\">";
		$content.="<tr>
					<td>
						<table width=\"100%\">
							<tr><td valign=\"top\" style=\"width:20em\"><b>User ID:</b></td><td>".$user->uid."</td></tr>
							<tr><td colspan=\"2\">Website admins may ask for this number.<br /><i>Global setting</i></td></tr>
						</table>
					</td>
				   </tr>";
		while ($item=db_fetch_object($query)) {
			$item->value=db_result(db_query("SELECT value FROM qawebsite_user_setting_info WHERE settingid='".$item->settingid."' AND userid='".$user->uid."'"));
			if ($item->siteid == 0)
				$visibility="Global setting";
			else
				$visibility="Site specific setting";
			$input=explode(":",$item->type);
			switch($input[0]) {
				case "input":
					$form="<input type=\"text\" name=\"qat_profile[".$item->settingid."]\" size=\"".$input[1]."\" value=\"".htmlentities($item->value,ENT_QUOTES,"UTF-8")."\" />";
				break;
				case "textarea":
					$form="<textarea name=\"qat_profile[".$item->settingid."]\" style=\"background-color:inherit\" rows=\"".$input[1]."\" cols=\"".$input[2]."\">".htmlentities($item->value,ENT_QUOTES,"UTF-8")."</textarea>";
				break;
				case "checkbox":
					if ($item->value == "1")
						$checked="checked=\"checked\"";
					else
						$checked="";
					$form="<input type=\"checkbox\" name=\"qat_profile[".$item->settingid."]\" value=\"1\" $checked />";
				break;
			}
			$content.="<tr>
						<td>
							<table width=\"100%\">
								<tr><td valign=\"top\" style=\"width:20em\"><b>".htmlentities($item->title,ENT_QUOTES,"UTF-8").":</b></td><td>&nbsp;".$form."</td></tr>
								<tr><td colspan=\"2\">".$item->description."<br /><i>".$visibility."</i></td></tr>
							</table>
						</td>
					  </tr>";
		}
		$content.="<tr>
					<td>
						<b><a href=\"/user/".$user->uid."/edit\">Change my Ubuntu QA email or password</a></b>
					</td>
				   </tr>";
		$content.="</table><div><br /><br /><input type=\"submit\" name=\"qat_profileupdate\" value=\"Save changes\" /></div></form>";
	}
	return $content;
}

function qawebsite_error($error) {
	if (!is_numeric($error))
		return drupal_not_found();

	switch ($error) {
		case "404":
			$message="
				There's no page with this address on this website.<br />
				If you got here from a link elsewhere on the website, sorry about that. We're working hard to fix those broken links.<br />
				Otherwise, check that you entered the address correctly, or complain to the maintainers of the page that kinked here.
			";
		break;
		case "403":
			$message="
				You don't seem to have the right to access this page.<br />
				Please make sure you are connected as the right user.<br />
			";
		break;
	}
	$content="
		<h1>An error occured (".$error.")</h1>
		$message<br /><br />
		If this is blocking your work, let us know on the <a href=\"http://lists.ubuntu.com/mailman/listinfo/ubuntu-qa\">Ubuntu QA Team mailing-list</a> (requires subscription).
	";
	return $content;
}
?>
