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




class TagModel extends Model
{
	/**
	 * Choice id.
	 */
	var $_id = 0;

	/**
	 * Load the data.
	 */
	function _loadData()
	{
		//TODO
		return null;
	}

	function setId($id)
	{
		if(is_numeric($id))
		{
			$this->_id = $id;
			$_data = null;
		}
	}

	/**
	 * Set name
	 */
	function setName($name)
	{
		$this->_data->name = ereg_replace("[^a-zA-Z0-9_\-]", "", $name);
	}

	/**
	 * Set choice id.
	 */
	function setChoiceId($choice_id)
	{
		if(is_numeric($choice_id) == false)
			return;

		$this->_data->choice_id = $choice_id;
	}

	/**
	 * Set user id.
	 */
	function setUserId($user_id)
	{
		if(is_numeric($user_id) == false)
			return;

		$this->_data->user_id = $user_id;
	}

	/**
	 * Set the admin flag.
	 */
	function setAdminFlag($admin_flag)
	{
		if(is_numeric($admin_flag) == false)
			return;

		$this->_data->admin = $admin_flag;
	}

	/**
	 * Store a tag.
	 * Check that $this->_data->name, choice_id and user_id have been set.
	 * If a same tag already exists for the same choice_id, it does nothing.
	 */
	function store()
	{
		if($this->_data->name == "" || $this->_data->choice_id == 0 || $this->_data->user_id == 0 ||
			!is_numeric($this->_data->choice_id) || !is_numeric($this->_data->user_id) || !is_numeric($this->_data->admin))
			return false;

		$tag_id = db_result(it_query("SELECT id FROM qapoll_choice_tag WHERE name='" .
			ereg_replace("[^a-zA-Z0-9_\-]", "", $this->_data->name) . "' AND " .
			"choice_id='" . $this->_data->choice_id . "' AND admin='" . $this->_data->admin . "'"));
		if($tag_id != "")
			//Tag already present, we skip
			return true;

		$query = "INSERT INTO qapoll_choice_tag(name, choice_id, user_id, admin) VALUES ('" . 
			ereg_replace("[^a-zA-Z0-9_\-]", "", $this->_data->name) . "', '" . 
			$this->_data->choice_id . "', '" . $GLOBALS['user']->uid . "', '" . $this->_data->admin . "')";
		it_query($query);

		//Save the id of the newly inserted tag
		$this->_id = db_last_insert_id("qapoll_choice_tag", "id");

		return true;
	}

	/**
	 * Delete a tag.
	 * Needs $this->_id
	 */
	function delete()
	{
		if($this->_id == 0)
			return false;

		$query = "DELETE FROM qapoll_choice_tag WHERE id='" . $this->_id . "'";
		it_query($query);

		return true;
	}

	/**
	 * Static function : compare two tags name, after being sanitized.
	 */
	static function compareNames($name1, $name2)
	{
		return (ereg_replace("[^a-zA-Z0-9_\-]", "", $name1) === ereg_replace("[^a-zA-Z0-9_\-]", "", $name2));
	}

	/**
	 * Static function : sanitize a tag name.
	 */
	static function sanitizeTagName($tag)
	{
		return substr(ereg_replace("[^a-zA-Z0-9_\-]", "", $tag), 0, 20);
	}
}

?>
