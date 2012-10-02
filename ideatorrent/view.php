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

require_once "views/view.functions.php";

//Include the theme callbacks files
foreach(scandir(drupal_get_path('module', 'ideatorrent') . "/themes/") as $file)
{
	if(is_dir(drupal_get_path('module', 'ideatorrent') . "/themes/" . $file) && 
		is_file(drupal_get_path('module', 'ideatorrent') . "/themes/" . $file . "/theme.php"))
	{
		include_once "themes/" . $file . "/theme.php";		
	}
}



class View
{

	/**
	 * The models affected to the view.
	 * DEPRECATED: $this->_model && $this->_model2 should not be used in templates.
	 * Instead, use the $model array (see examples).
	 */
	var $_model = null;
	var $_model2 = null;
	var $models = array();

	/**
	 * The data provided by the models.
	 * DEPRECATED: $this->_data && $this->_data2 should not be used in templates.
	 * Instead, use the $data array (see examples).
	 */
	var $_data = null;
	var $_data2 = null;
	var $data = array();

	/**
	 * Save the used template.
	 */
	var $template = null;

	/**
	 * The options of the view are stored here.
	 */
	var $view_options = array();
	private $options = array();


	/**
	 * Display the page.
	 */
	function display($template = "default")
	{
		//Save the template
		$this->template = $template;

		//To overload... for the moment.
	}

	/**
	 * Load and execute a template. This should only be called by a subclass, or inside a template.
	 * The path can be found using the $path/$template variables, and the $prefix can be appended to it. (see code).
	 * $data is an array that contains data that you want to pass to the template. $models is an array of models you
	 * want to pass to the template.
	 * $options contain the options you want to pass to the template.
	 */
	protected function loadTemplate($path, $template, $prefix = "", $data = array(), $models = array(), $options = array())
	{
		global $user;
		global $site;

		//Call the theme callback which will include all the necessary external files (JS, CSS)
		if(function_exists("qapoll_" . QAPollConfig::getInstance()->getValue("selected_theme") . "_load_external_files"))
			call_user_func("qapoll_" . QAPollConfig::getInstance()->getValue("selected_theme") . "_load_external_files",
				$path, $template, $this->getThemePath());

		//Prepare the data for the template: mix the data passed in argument (usually non null if called within a template)
		//With the data of the View instance.
		$data = array_merge($this->data, $data);
		$models = array_merge($this->models, $models);
		$options = array_merge($this->options, $options);

		//Start capturing output into a buffer
		ob_start();

		//Select and include the template
		//include $path . "tmpl/" . $template . (($prefix != null)?"_".$prefix:"") . ".php";
		//basemodule_require("themes/" . QAPollConfig::getInstance()->getValue("selected_theme") . "/" .  $path . 
		//	$template . (($prefix != null)?"_".$prefix:"") . ".php");
		include "themes/" . QAPollConfig::getInstance()->getValue("selected_theme") . "/" .  $path . 
			$template . (($prefix != null)?"_".$prefix:"") . ".php";

		//Save the content
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * Load and execute a template in a non-theme directory. Useful for the admin pages.
	 * This should only be called by a subclass, or inside a template.
	 * The path can be found using the $path/$template variables, and the $prefix can be appended to it. (see code).
	 * $data is an array that contains data that you want to pass to the template. $models is an array of models you
	 * want to pass to the template.
	 * $options contain the options you want to pass to the template.
	 */
	protected function loadNonThemedTemplate($path, $template, $prefix = "", $data = array(), $models = array(), $options = array())
	{
		global $user;
		global $site;

		//Call the theme callback which will include all the necessary external files (JS, CSS)
		if(function_exists("qapoll_" . QAPollConfig::getInstance()->getValue("selected_theme") . "_load_external_files"))
			call_user_func("qapoll_" . QAPollConfig::getInstance()->getValue("selected_theme") . "_load_external_files",
				$path, $template, $this->getThemePath());

		//Prepare the data for the template: mix the data passed in argument (usually non null if called within a template)
		//With the data of the View instance.
		$data = array_merge($this->data, $data);
		$models = array_merge($this->models, $models);
		$options = array_merge($this->options, $options);

		//Start capturing output into a buffer
		ob_start();

		//Select and include the template
		include "views/" .  $path .
			$template . (($prefix != null)?"_".$prefix:"") . ".php";

		//Save the content
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}


	/**
	 * Attach a model to the view.
	 * A name should be given to this model so that it can be accessed within the template by calling
	 * $data[$name] for the data and $models[$name] for the models.
	 */
	function setModel(&$model, $name = "")
	{
		if($model != null)
		{
			//DEPRECATED old way to store model && data
			$this->_model = $model;
			$this->_data = $model->getData();

			//New way to store model && data
			if($name == "")
				$name = "default";
			$this->models[$name] = $model;
			$this->data[$name] = $model->getData();
		}
	}

	/**
	 * DEPRECATED
	 * Affect a second model to the view.
	 */
	function setModel2(&$model2, $name = "")
	{
		if($model2 != null)
		{
			//Save the model
			$this->_model2 = $model2;
			$this->_data2 = $model2->getData();

			//New way to store model && data
			if($name == "")
				$name = "default";
			$this->models[$name] = $model2;
			$this->data[$name] = $model2->getData();
		}
	}

	/**
	 * Affect options to the view. They will be stored in $this->view_options;
	 * To overload to check for the safety of the options.
	 */
	function setOptions($viewOptions)
	{
		$this->view_options = $viewOptions;
		$this->options = $viewOptions;
	}



	/**
	 * Get the current theme base path
	 * Return a path in the form : module/ideatorrent/theme/selected_theme/
	 */
	function getThemePath()
	{
		return drupal_get_path('module', 'ideatorrent') . "/themes/" . QAPollConfig::getInstance()->getValue("selected_theme");
	}


	/**
	 * Get a theme setting. If no stored in the DB, use the default value.
	 */
	function getThemeSetting($name)
	{
		return QAPollThemeConfig::getInstance(QAPollConfig::getInstance()->getValue("selected_theme"))->getValue($name);
	}

	/**
	 * Get the theme list.
	 */
	static function getThemeList()
	{
		$result = array();

		foreach(scandir(drupal_get_path('module', 'ideatorrent') . "/themes/") as $file)
		{
			if(is_dir(drupal_get_path('module', 'ideatorrent') . "/themes/" . $file) && 
				is_file(drupal_get_path('module', 'ideatorrent') . "/themes/" . $file . "/theme.php"))
			{
				$result[] = $file;
			}
		}

		return $result;
	}

	/**
	 * Check if the theme name is valid.
	 */
	static function isThemeNameValid($name)
	{
		$result = array();

		foreach(scandir(drupal_get_path('module', 'ideatorrent') . "/themes/") as $file)
		{
			if(is_dir(drupal_get_path('module', 'ideatorrent') . "/themes/" . $file) && 
				is_file(drupal_get_path('module', 'ideatorrent') . "/themes/" . $file . "/theme.php"))
			{
				if($file == $name)
					return true;
			}
		}

		return false;
	}
}
