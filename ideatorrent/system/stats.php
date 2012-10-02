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
 * This class takes care of the stats functions.
 */
class QAPollStats
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
			self::$_instance = new QAPollStats;

		return self::$_instance;
	}

	
	/**
	 * Save today's global statistics. It will check if the stats are already saved or not.
	 */
	function saveTodayGlobalStats()
	{
		//Check if the stats of the day were already saved.
		$result = db_result(it_query("SELECT id FROM qapoll_stats WHERE date_trunc('day', date) = date_trunc('day', NOW())"));
		if($result != null)
			return;

		//The differents queries.
		$nbusers = "(SELECT COUNT(*) FROM users)";
		$nbcomments = "(SELECT COUNT(*) FROM qapoll_choice_comment)";
		$nbvotes = "(SELECT COUNT(*) FROM qapoll_vote)";
		$nbvotesplus = "(SELECT COUNT(*) FROM qapoll_vote WHERE value=1)";
		$nbvotesminus = "(SELECT COUNT(*) FROM qapoll_vote WHERE value='-1')";
		$nbideas = "(SELECT COUNT(*) FROM qapoll_choice)";
		$nbideasdeleted = "(SELECT COUNT(*) FROM qapoll_choice WHERE status='-2')";
		$nbideasduplicate = "(SELECT COUNT(*) FROM qapoll_choice WHERE status!='-2' AND duplicatenumber!='-1')";
		$nbideasvalid = "(SELECT COUNT(*) FROM qapoll_choice WHERE status!='-2' AND duplicatenumber='-1')";
		$nbideasvalid_new = "(SELECT COUNT(*) FROM qapoll_choice " .
				" WHERE qapoll_choice.status!='-2' AND duplicatenumber='-1' AND (" .
				"qapoll_choice.status = 0 OR qapoll_choice.status = '-1' ))";
		$nbideasvalid_needinfos = "(SELECT COUNT(*) FROM qapoll_choice " .
				" WHERE qapoll_choice.status!='-2' AND duplicatenumber='-1' AND (" .
				"qapoll_choice.status = 1))";
		$nbideasvalid_workinprogress = "(SELECT COUNT(*) FROM qapoll_choice " .
				" WHERE qapoll_choice.status!='-2' AND duplicatenumber='-1' AND (" .
				"qapoll_choice.status = 2))";
		$nbideasvalid_done = "(SELECT COUNT(*) FROM qapoll_choice " .
				" WHERE qapoll_choice.status!='-2' AND duplicatenumber='-1' AND (" .
				"qapoll_choice.status = 3))";
		$nbideasvalid_unapplicable = "(SELECT COUNT(*) FROM qapoll_choice " .
				" WHERE qapoll_choice.status!='-2' AND duplicatenumber='-1' AND (" .
				"qapoll_choice.status = 4))";
		$nbideasvalid_already_implemented = "(SELECT COUNT(*) FROM qapoll_choice " .
				" WHERE qapoll_choice.status!='-2' AND duplicatenumber='-1' AND (" .
				"qapoll_choice.status = 5))";
		$nbideasvalid_blueprint_approved = "(SELECT COUNT(*) FROM qapoll_choice " .
				" WHERE qapoll_choice.status!='-2' AND duplicatenumber='-1' AND (" .
				"qapoll_choice.status = 6))";
		//Rather complicated, but the DB handling of the dup reports is not the best...
		$nbdupreports="(SELECT COUNT(*) - (SELECT COUNT(*)/2 FROM qapoll_choice_duplicate_report as dr1, " . 
				"qapoll_choice_duplicate_report as dr2 WHERE (dr1.choiceid = dr2.duplicateid AND " .
				"dr2.choiceid = dr1.duplicateid) = true) FROM qapoll_choice_duplicate_report)";

		#Save the stats
		$query = "INSERT INTO qapoll_stats (date, nbusers, nbcomments, nbvotes, nbvotesplus, nbvotesminus, nbideas,
			nbideasdeleted, nbideasduplicate, nbideasvalid, nbideasvalid_new, nbideasvalid_needinfos,
			nbideasvalid_workinprogress, nbideasvalid_done, nbideasvalid_unapplicable, nbideasvalid_already_implemented,
			nbideasvalid_blueprint_approved, nbdupreports)
			VALUES (NOW(), " . $nbusers . ", " . $nbcomments . ", " . $nbvotes . ", " . $nbvotesplus . ", " . 
			$nbvotesminus . ", " . $nbideas . ", " . $nbideasdeleted . ", " . $nbideasduplicate . ", " . $nbideasvalid . ", " . 
			$nbideasvalid_new . ", " . $nbideasvalid_needinfos . ", " . $nbideasvalid_workinprogress . ", " . $nbideasvalid_done . ", " .
			$nbideasvalid_unapplicable . ", " . $nbideasvalid_already_implemented . ", " . $nbideasvalid_blueprint_approved . ", " .
			$nbdupreports . " )";

		it_query($query);
	}

	/**
	 * Get the stats of the day, in the form of an object.
	 */
	function getTodayStats()
	{
		$query = "SELECT * FROM qapoll_stats " .
			"ORDER BY date DESC LIMIT 1";
		$stat_object = db_fetch_object(it_query($query));

		return $stat_object;
	}

}

?>
