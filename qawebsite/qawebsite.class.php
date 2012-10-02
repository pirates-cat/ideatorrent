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

class QAWebsiteSite {

	static private $_instance = null;
	var $_module = null;

	static function getInstance() {
		if (self::$_instance == null)
			self::$_instance = new QAWebsiteSite();
		if (self::$_instance->id == 0)
			self::$_instance=null;
		return self::$_instance;
	}

	/**
	 * Default constructor. If siteid == -1, load the website object related to the currently viewed website.
	 */
	public function __construct($siteid = -1) {
		if (is_numeric($siteid)) {
			//Allow custom ports
			$_SERVER['HTTP_HOST']=ereg_replace(":.*$","",$_SERVER['HTTP_HOST']);

			//Save the module name
			$this->_module = arg(0);

			if($siteid == -1)
				$object=db_fetch_object(db_query("SELECT * FROM qawebsite_site WHERE '".$_SERVER['HTTP_HOST']."' SIMILAR TO subdomain"));
			else
				$object=db_fetch_object(db_query("SELECT * FROM qawebsite_site WHERE id='".$siteid."'"));

			if ($object != Null) {
				foreach ($object as $key => $value) {
					$this->$key=$value;
				}
			}
			else
				$this->id=0;
		}
		else
			$this->id=0;
	}
	public function getSetting($name) {
		$query = "SELECT value FROM qawebsite_module_setting WHERE option='".db_escape_string($name) .
			"' AND (siteid='".$this->id."' OR siteid='-1') AND (module='".db_escape_string($this->_module)."' OR module=null)";
		return db_result(db_query($query));
		
	}

	public function getAllSettings() {
		$query = "SELECT option, value FROM qawebsite_module_setting WHERE (siteid='".$this->id."' OR siteid='-1') AND " .
			"(module='".db_escape_string($this->_module)."' OR module=null)";
		$settings = db_query($query);

		$settinglist = array();
		while ($setting = db_fetch_object($settings))
			$settinglist[$setting->option] = $setting->value;

		return $settinglist;
	}

	public function setSetting($name,$value) {
		//setSetting is only working with the current site and module
		//directly use SQL to update the global (module=null or site=null) settings

		if ($this->getSetting($name) !== false)
			db_query("UPDATE qawebsite_module_setting
				SET value='".db_escape_string($value)."'
				WHERE option='".db_escape_string($name)."' AND siteid='".$this->id."' AND module='".db_escape_string($this->_module)."'");
		else
			db_query("INSERT INTO qawebsite_module_setting (siteid, module, option, value) 
				VALUES ('".$this->id."', '".db_escape_string($this->_module)."', '".db_escape_string($name)."', '".db_escape_string($value)."')");
		return;
	}
	public function getModules($order="id ASC") {
		$modules=array();
		$order=db_escape_string($order);
		$query=db_query("SELECT * FROM qawebsite_site_module WHERE siteid='".$this->id."' AND status='1' ORDER BY ".$order);
		while ($module = db_fetch_object($query))
			array_push($modules,$module);
		return $modules;
	}
	
	/**
	 * Get the logo of the current page. If the logo is defined in qawebsite_site_module, then we use it.
	 * Otherwise we use the website-global logo.
	 */
	public function getLogo()
	{
		$query = "SELECT logo FROM qawebsite_site_module WHERE '" . db_escape_string(getCurrentURL()) . "' LIKE qawebsite_site_module.path || '%'";
		$logourl = db_result(db_query($query));

		if($logourl != "")
			return $logourl;
		else
			return $this->logo;
	}
}

?>
