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
 * This class takes care of the config functions.
 */
class QAPollConfig extends Model
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
			self::$_instance = new QAPollConfig;

		return self::$_instance;
	}

	/**
	 * Load the data.
	 */
	function _loadData()
	{
		return $this->_configcache = QAWebsiteSite::getInstance()->getAllSettings();
	}

	/**
	 * Get a config value
	 */
	function getValue($key)
	{
		$config = $this->getData();
		return $config[$key];
	}


	/**
	 * Load an object from the _POST parameters.
	 * Return true if there was enough correct data. 
	 */
	function loadFromPost()
	{
		global $site;
		global $user;
				
		$errorMessage = "";
	
		$this->_data["selected_theme"] = db_escape_string($_POST['selected_theme']);
		$this->_data["start_page"] = db_escape_string($_POST['start_page']);
		$this->_data["default_number_item_per_page"] = $_POST['default_number_item_per_page'];
		$this->_data["choice_number_approvals_needed"] = $_POST['choice_number_approvals_needed'];

		//Now check all entries.
		if(View::isThemeNameValid($this->_data["selected_theme"]) == false)
			$errorMessage .= "The selected theme is not valid.<br />";
		if(!is_numeric($this->_data["default_number_item_per_page"]) || $this->_data["default_number_item_per_page"] < 1) 
			$errorMessage .= "The number of ideas per page should be numeric and superior to 0.<br />";
		if(!is_numeric($this->_data["choice_number_approvals_needed"]) || $this->_data["choice_number_approvals_needed"] < 0) 
			$errorMessage .= "The number of ideas per page should be numeric and superior or equal to 0.<br />";
		


		//Display the errors
		if($errorMessage != "")
			$errorMessage = substr($errorMessage, 0, strlen($errorMessage) - 6);
		drupal_set_message($errorMessage, 'error_msg');


		return ($errorMessage == "");
	}

	/**
	 * Update or insert a new article into the BD.
	 * When updating, it takes care to update only what the user is allowed to.
	 * Returns true if everything was OK.
	 */
	function store()
	{
		global $site;

		//We update the choice according to the user rights
		if(user_access($site->getData()->adminrole))
		{
			QAWebsiteSite::getInstance()->setSetting("start_page", $this->_data["start_page"]); 
			QAWebsiteSite::getInstance()->setSetting("selected_theme", $this->_data["selected_theme"]); 
			QAWebsiteSite::getInstance()->setSetting("default_number_item_per_page", $this->_data["default_number_item_per_page"]);
			QAWebsiteSite::getInstance()->setSetting("choice_number_approvals_needed", $this->_data["choice_number_approvals_needed"]);
		}

		//Now reset the data to force the reload : all the data is not loaded, and some data may be required for future use.
		$this->_data = null;

		return ($errorMessage == "");
	}

}






?>
