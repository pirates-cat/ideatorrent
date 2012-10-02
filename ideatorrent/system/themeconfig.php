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
 * This class takes care of the theme config functions.
 */
class QAPollThemeConfig extends Model
{
	/**
	 * The name of the theme.
	 */
	var $_name = "";

	/**
	 * The instances of the theme configs.
	 */
	static private $_instances = array();

	/**
	 * Get the unique instance of a theme.
	 */
	static function getInstance($name)
	{
		if (self::$_instances[$name] == null)
			self::$_instances[$name] = new QAPollThemeConfig($name);

		return self::$_instances[$name];
	}

	/**
	 * Default constructor.
	 */
	private function QAPollThemeConfig($name)
	{
		$this->_name = $name;
	}

	/**
	 * Load the data.
	 */
	function _loadData()
	{
		$data = array();
 
		if(function_exists("qapoll_" . $this->_name . "_get_theme_options"))
		{	
			$data = call_user_func("qapoll_" . $this->_name . "_get_theme_options");
			$keys = array_keys($data);
			for($i = 0; $i < count($data); $i++)
			{
				$data[$keys[$i]]["value"] = QAPollConfig::getInstance()->getValue("theme_" . $this->_name . "_" . $keys[$i]);
			}
		}

		return $data;
	}

	/**
	 * Get a config value
	 */
	function getValue($key)
	{
		$config = $this->getData();
		$value = $config[$key]["value"];
		if($value == "")
			$value = $config[$key]["default_value"];

		return $value;
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
	
		$options = $this->getData();
		foreach($options as $option_name => $option_values)
		{
			//QAWebsiteSite::getInstance()->setSetting is doing the db_escape_string
			$this->_data[$option_name]["value"] = $_POST[$option_name];
			if($this->_data[$option_name]["type"] == "integer" && is_numeric($this->_data[$option_name]["value"]) == false)
				$errorMessage .= "The \"" . $this->_data[$option_name]["name"] . "\" field should be numeric.<br />";
		}

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
			$options = $this->getData();
			foreach($options as $option_name => $option_values)
			{
				QAWebsiteSite::getInstance()->setSetting("theme_" . $this->_name . "_" . $option_name, $option_values["value"]); 
			}
		}

		//Now reset the data to force the reload : all the data is not loaded, and some data may be required for future use.
		$this->_data = null;

		return ($errorMessage == "");
	}

}






?>
