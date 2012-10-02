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
 * This class takes care of the logging functions.
 * Log ids:
 * 1: Item submission
 * 2: Choice view by a registered user
 * 3: private message sent
 * 4: Solution submission
 */
class QAPollLogger
{
	/**
	 * The instance of the logger.
	 */
	static private $_instance = null;

	/**
	 * Get the unique instance.
	 */
	static function getInstance()
	{
		if (self::$_instance == null)
			self::$_instance = new QAPollLogger;

		return self::$_instance;
	}

	/**
	 * An user sent a private message.
	 */
	function logPrivateMessageSent($userid, $receiverid)
	{
		if(!is_numeric($userid)|| $userid == 0)
			return;

		it_query("INSERT INTO qapoll_log (userid, type, date, choice_id) VALUES ('" . $userid . 
			"', '3', '".date("Y-m-d H:i:s")."', '" . $receiverid . "')");
	}

	/**
	 * Return an object containing the last private message log infos, for a given user.
	 */
	function getLatestPrivateMessageLog($userid)
	{
		$query = "SELECT * FROM qapoll_log " .
			"WHERE userid='" . $userid . "' AND type='3' ORDER BY date DESC LIMIT 1";
		$log_object = db_fetch_object(it_query($query));

		return $log_object;
	}

	/**
	 * Returns true if the latest private_msg by $userid is older than $num_minute minute.
	 */
	function isLatestPrivateMessageOlderThan($userid, $num_minute)
	{
		$query = "SELECT NOW() - date > interval '" . $num_minute . " minute' FROM qapoll_log " .
			"WHERE userid='" . $userid . "' AND type='3' ORDER BY date DESC LIMIT 1";
		$log_object = db_result(it_query($query));

		return ($log_object == "t" || $log_object == "");
	} 

	/**
	 * An user viewed an bug/idea.
	 */
	function logItemView($userid, $choice_id)
	{
		if(!is_numeric($userid) || !is_numeric($choice_id) || $userid == 0)
			return;

		it_query("INSERT INTO qapoll_log (userid, type, date, choice_id) VALUES ('" . $userid . 
			"', '2', NOW(), '$choice_id')");
	}

	/**
	 * An user submitted an bug/idea.
	 */
	function logItemSubmission($userid, $choice_id)
	{
		if(!is_numeric($userid) || !is_numeric($choice_id) || $userid == 0)
			return;

		it_query("INSERT INTO qapoll_log (userid, type, date, choice_id) VALUES ('" . $userid . 
			"', '1', '".date("Y-m-d H:i:s")."', '$choice_id')");
	}

	/**
	 * Return an object containing the last item submission log infos.
	 */
	function getLatestItemSubmission($userid)
	{
		$query = "SELECT * FROM qapoll_log " .
			"WHERE userid='" . $userid . "' AND type='1' ORDER BY date DESC LIMIT 1";
		$log_object = db_fetch_object(it_query($query));

		return $log_object;
	}

	/**
	 * Temporary function, I can't seem to find a time diff function in PHP!
	 * Returns true if the latest item submission by $userid is older than 1 minute.
	 */
	function isLatestItemSubmissionOlderThanOneMin($userid)
	{
		$query = "SELECT NOW() - date > interval '1 minute' FROM qapoll_log " .
			"WHERE userid='" . $userid . "' AND type='1' ORDER BY date DESC LIMIT 1";
		$log_object = db_result(it_query($query));

		return ($log_object == "t" || $log_object == "");
	} 


	/**
	 * An user submitted an solution.
	 */
	function logItemSolutionSubmission($userid, $choicesolution_id)
	{
		if(!is_numeric($userid) || !is_numeric($choicesolution_id) || $userid == 0)
			return;

		$query = "INSERT INTO qapoll_log (userid, type, date, choice_id) VALUES ('" . $userid . 
			"', '4', '".date("Y-m-d H:i:s")."', '$choicesolution_id')";
		it_query($query);
	}

	/**
	 * Return an object containing the last item solution submission log infos.
	 */
	function getLatestItemSolutionSubmission($userid)
	{
		$query = "SELECT * FROM qapoll_log " .
			"WHERE userid='" . $userid . "' AND type='4' ORDER BY date DESC LIMIT 1";
		$log_object = db_fetch_object(it_query($query));

		return $log_object;
	}

	/**
	 * Temporary function, I can't seem to find a time diff function in PHP!
	 * Returns true if the latest item solution submission by $userid is older than 5 minute.
	 */
	function isLatestItemSolutionSubmissionOlderThanFiveMin($userid)
	{
		$query = "SELECT NOW() - date > interval '5 minute' FROM qapoll_log " .
			"WHERE userid='" . $userid . "' AND type='4' ORDER BY date DESC LIMIT 1";
		$log_object = db_result(it_query($query));

		return ($log_object == "t" || $log_object == "");
	} 


	/**
	 * Remove all the entries older than one week.
	 * Careful if you want to change the interval: the compute_item_comment_unread_flag option of ChoiceListModel
	 * needs one week old log data.
	 */
	function removeOldEntries()
	{
		$query = "DELETE FROM qapoll_log " .
			"WHERE (NOW() - date) > interval '1 week' ";
		it_query($query);
	}

}






?>
