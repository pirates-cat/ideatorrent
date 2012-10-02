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

/**
 * This class takes care of the choice logging functions.
 */
class ChoiceLogModel extends Model
{

	/**
	 * List of log type.
	 */
	static public $change = array(
		"title" => 1,
		"description" => 2,		
		"status" => 3,
		"category" => 4,
		"relation" => 5,
		"relationsubcat" => 6,
		"target_release" => 7,
		"duplicate" => 8,
		"tags" => 9,
		"admintags" => 10,
		"solution_linked" => 11,
		"solution_unlinked" => 12,
		"comment_added" => 13,
		"comment_deleted" => 14,
		"whiteboard" => 15,
		"moderatorapproval" => 16,
		"solution_title" => 17,
		"solution_description" => 18);

	/**
	 * Log an action on the choice
	 */
	static function log($choiceid, $changetype, $oldvalue, $newvalue)
	{
		ChoiceLogModel::userLog($choiceid, $changetype, $oldvalue, $newvalue, $GLOBALS['user']->uid);
	}

	/**
	 * Log an action on the choice
	 */
	static function userLog($choiceid, $changetype, $oldvalue, $newvalue, $userid)
	{
		if(!is_numeric($choiceid) || !is_numeric($userid) || $userid == 0 || !is_numeric($changetype))
			return false;

		//If old value == new value, skip
		if($newvalue == $oldvalue)
			return false;

		$query = "INSERT INTO qapoll_choice_log (choiceid, userid, date, change, old_value, new_value) VALUES ('" . $choiceid . 
			"', '" . $userid . "', NOW(), '" . $changetype . "', '" . db_escape_string($oldvalue) . "', '" .
			db_escape_string($newvalue) . "')";

		it_query($query);

		return true;
	}

	/**
	 * Log an action concerning a solution. This will be logged on all the choices that are linked to this solution.
	 * new/oldvalue2 can be used as an extension to $newvalue.
	 */
	static function solutionLog($solutionid, $changetype, $oldvalue, $newvalue, $oldvalue2 = null, $newvalue2 = null)
	{
		ChoiceLogModel::userSolutionLog($solutionid, $changetype, $oldvalue, $newvalue, $GLOBALS['user']->uid, $oldvalue2, $newvalue2);
	}

	/**
	 * Log an action concerning a solution. This will be logged on all the choices that are linked to this solution.
	 */
	static function userSolutionLog($solutionid, $changetype, $oldvalue, $newvalue, $userid, $oldvalue2 = null, $newvalue2 = null)
	{
		if(!is_numeric($solutionid) || !is_numeric($userid) || $userid == 0 || !is_numeric($changetype))
			return false;

		//If old value == new value, skip
		if($newvalue == $oldvalue)
			return false;

		//Get all the valid links on this solution.
		$choicesolutionlinklist = new ChoiceSolutionLinkListModel();
		$choicesolutionlinklist->setFilterParameters(array("choicesolutionid" => $solutionid));
		$links = $choicesolutionlinklist->getData();
		foreach($links as $link)
		{
			$link->id; 
			$query = "INSERT INTO qapoll_choice_log (choiceid, choicesolutionlinkid, userid, date, change, " . 
				"old_value, new_value, old_value2, new_value2) ". 
				"VALUES ('" . $link->choiceid . "', '" . $link->id .
				"', '" . $userid . "', NOW(), '" . $changetype . "', '" . db_escape_string($oldvalue) . "', '" .
				db_escape_string($newvalue) . "', '"  . db_escape_string($oldvalue2) . "', '" .
				db_escape_string($newvalue2) . "')";
			it_query($query);
		}


		return true;
	}


}






?>
