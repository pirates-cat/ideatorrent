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




class SiteModel extends Model
{

	/**
	 * Load the data related to the current HTTP_HOST.
	 */
	function _loadData()
	{
		return new QAWebsiteSite();
	}

	/**
	 * Return an array of PollModel object which are related to the current HTTP_HOST.
	 */
	function getPollList()
	{
		$polls = it_query("SELECT * FROM qapoll_poll WHERE status!='0' AND siteid='" . $this->getData()->id . "' ORDER BY startdate");

		//Store the result in a array
		$pollList = array();
		while ($polldata = db_fetch_object($polls))
		{
			$poll = new PollModel($this);
			$poll->setid($polldata->id);
			$poll->setData($polldata);
			$pollList[] = $poll;
		}

		return $pollList;
	}

	/**
	 * Return a setting concerning this module.
	 */
	function getSetting($key)
	{
		return $this->getData()->getSetting($key);
	}

}

?>
