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


/**
 * Get a user setting for a given website, module.
 * TEMPORARY location.
 */
function qawebsite_get_user_setting($setting_name, $userid, $siteid, $module)
{
	$query = "SELECT value FROM qawebsite_user_setting_info " .
		"JOIN qawebsite_user_setting ON qawebsite_user_setting_info.settingid = qawebsite_user_setting.id " .
		"WHERE qawebsite_user_setting.title = '" . db_escape_string($setting_name) . "' AND qawebsite_user_setting.module = '" . 
		db_escape_string($module) . "' AND " . 
		"qawebsite_user_setting.siteid = '" . $siteid . "' AND userid='" . $userid . "'";

	return db_result(db_query($query));
}

/**
 * Set a user setting for a given website, module.
 * TEMPORARY location.
 */
function qawebsite_set_user_setting($setting_name, $userid, $siteid, $module, $value)
{
	$cur_value = qawebsite_get_user_setting($setting_name, $userid, $siteid, $module);

	if($cur_value == null)
		$query = "INSERT INTO qawebsite_user_setting_info (settingid, userid, value) VALUES " .
			"((SELECT id FROM qawebsite_user_setting where title = '" . db_escape_string($setting_name) . "' AND " .
			"qawebsite_user_setting.module = '" . db_escape_string($module) . "' AND qawebsite_user_setting.siteid = '" . $siteid . "'), " .
			"'" . $userid . "', '" . $value . "')";
	else
		$query = "UPDATE qawebsite_user_setting_info SET value = '" . $value . "' " . 
			"WHERE settingid IN (SELECT id FROM qawebsite_user_setting where title = '" . db_escape_string($setting_name) . "' AND " .
			"qawebsite_user_setting.module = '" . db_escape_string($module) . "' AND qawebsite_user_setting.siteid = '" . $siteid . "') AND " .
			"userid = '" . $userid . "'";

	db_query($query);
}

function qawebsite_getbug($bugnumber) {
	if (is_numeric($bugnumber))
		return db_fetch_object(db_query("SELECT * FROM qawebsite_launchpad_bug WHERE originalbug='".$bugnumber."'"));
	else
		return 1;
}

function qawebsite_getblueprint($blueprinturl) {
	if (ereg("^https\:\/\/blueprints.launchpad.net\/(.*)\/\+spec\/(.*)", $blueprinturl))
		return db_fetch_object(db_query("SELECT * FROM qawebsite_launchpad_blueprint WHERE blueprinturl='".db_escape_string($blueprinturl)."'"));
	else
		return 1;
}

function array_push_associative(&$arr) {
	$args = func_get_args();
	foreach ($args as $arg) {
		if (is_array($arg)) {
			foreach ($arg as $key => $value) {
				$arr[$key]=$value;
				$ret++;
			}
		}
		else {
			$arr[$arg]="";
		}
	}
	return $ret;
}

function array_is_associative ($array) {
	if (is_array($array) && ! empty($array)) {
		for ( $iterator = count($array) - 1; $iterator; $iterator-- ) {
			if ( ! array_key_exists($iterator, $array) ) { return true; }
		}
		return ! array_key_exists(0, $array);
	}
	return false;
}

/**
 * Get the current URL
 */
function getCurrentURL()
{
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on")
		$pageURL .= "s";
	$pageURL .= "://" . $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

	return $pageURL;
}


?>
